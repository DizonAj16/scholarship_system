<?php
require '../includes/session.php'; // Ensure session is started and database connection is available
require '../includes/send_email.php'; // Include the send_email.php file for email functionality

try {
    // Check if the application ID is provided
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Invalid application ID.');
    }

    $applicationId = (int) $_GET['id'];  // Sanitize and assign application ID
    $notify = isset($_GET['notify']) && $_GET['notify'] === 'yes'; // Check if notification is required

    // Prepare the SQL query to update the application status
    $stmt = $pdo->prepare("
        UPDATE scholarship_applications 
        SET status = 'approved' 
        WHERE application_id = :id
    ");
    $stmt->bindValue(':id', $applicationId, PDO::PARAM_INT);
    $stmt->execute();

    // Check if any row was updated
    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Application ID $applicationId has been approved.";

        // Fetch the applicant's email and name from the database
        $query = $pdo->prepare("SELECT full_name, email FROM scholarship_applications WHERE application_id = :id");
        $query->bindParam(':id', $applicationId, PDO::PARAM_INT);
        $query->execute();

        $applicant = $query->fetch(PDO::FETCH_ASSOC);

        // If applicant is found, handle notification
        if ($applicant) {
            $applicantEmail = $applicant['email'];
            $applicantName = $applicant['full_name'];

            if ($notify) {
                // Send email notification
                $emailStatus = sendApprovalEmail($applicantEmail, $applicantName, 'approved');
                
                // Log the email notification action
                $action = "Application approved and email notification sent";
                $details = "Application ID $applicationId approved and notification sent to $applicantName ($applicantEmail).";
                logActivity($pdo, $_SESSION['id'], $action, $details);

                // Check if the email was successfully sent
                if ($emailStatus !== true) {
                    $_SESSION['error_message'] = "Email could not be sent: $emailStatus";
                }
            } else {
                // Log the approval action without email notification
                $action = "Application approved without email notification";
                $details = "Application ID $applicationId approved but no email notification sent to $applicantName ($applicantEmail).";
                logActivity($pdo, $_SESSION['id'], $action, $details);
            }
        }
    } else {
        $_SESSION['error_message'] = "No application found with ID $applicationId or it has already been processed.";
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
    // Log the exception error
    logActivity($pdo, $_SESSION['id'], "Error during application approval", "Error: " . $e->getMessage());
}

// Redirect back to the applications page
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../views/applications.php'));
exit;
?>
