<?php
require '../includes/session.php'; // Ensure this includes database connection and session setup
require '../includes/send_email.php'; // Include the email functions

try {
    // Check if the application ID is provided
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Invalid application ID.');
    }

    $applicationId = (int) $_GET['id'];
    $notify = isset($_GET['notify']) && $_GET['notify'] === 'yes';

    // Fetch the applicant's details including scholarship grant
    $query = $pdo->prepare("
        SELECT full_name, email, scholarship_grant 
        FROM scholarship_applications 
        WHERE application_id = :id
    ");
    $query->bindParam(':id', $applicationId, PDO::PARAM_INT);
    $query->execute();
    $applicant = $query->fetch(PDO::FETCH_ASSOC);

    if (!$applicant) {
        throw new Exception('Applicant not found.');
    }

    $applicantEmail = $applicant['email'];
    $applicantName = $applicant['full_name'];
    $scholarshipGrant = $applicant['scholarship_grant'];

    // Prepare the SQL query to update the application status to 'pending'
    $stmt = $pdo->prepare("
        UPDATE scholarship_applications 
        SET status = 'pending' 
        WHERE application_id = :id
    ");
    $stmt->bindValue(':id', $applicationId, PDO::PARAM_INT);
    $stmt->execute();

    // Check if any row was updated
    if ($stmt->rowCount() > 0) {
        if ($notify) {
            // Send notification email with scholarship grant
            $emailStatus = sendPendingEmail($applicantEmail, $applicantName, $scholarshipGrant);
            
            if ($emailStatus === true) {
                $_SESSION['success_message'] = "Application ID $applicationId has been marked as 'pending' and notification sent to $applicantName.";
                logActivity($pdo, $_SESSION['id'], 'Application Status Updated', "Application ID $applicationId marked as 'pending' and notified.");
            } else {
                $_SESSION['warning_message'] = "Application status updated to pending but email could not be sent: $emailStatus";
                logActivity($pdo, $_SESSION['id'], 'Application Status Updated', "Application ID $applicationId marked as 'pending' but email failed: $emailStatus");
            }
        } else {
            $_SESSION['success_message'] = "Application ID $applicationId has been marked as 'pending'.";
            logActivity($pdo, $_SESSION['id'], 'Application Status Updated', "Application ID $applicationId marked as 'pending' without notification.");
        }
    } else {
        $_SESSION['error_message'] = "No application found with ID $applicationId or it has already been processed.";
        logActivity($pdo, $_SESSION['id'], 'Error Updating Status', "No application found with ID $applicationId or it has already been processed.");
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
    logActivity($pdo, $_SESSION['id'], 'Error Updating Status', "Error: " . $e->getMessage());
}

// Redirect back to the applications page
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../views/applications.php'));
exit;
?>