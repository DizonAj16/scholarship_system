<?php
require '../includes/session.php'; // Ensure session is started and database connection is available
require '../includes/send_email.php'; // Include the send_email.php file for email functionality

try {
    // Validate and sanitize input
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Invalid application ID.');
    }

    $applicationId = (int) $_GET['id'];
    $action = $_GET['action'] ?? null;

    // Check if "notify" is part of the action
    $notify = ($action === 'notify');

    // Fetch the applicant's details
    $query = $pdo->prepare("SELECT full_name, email, status FROM scholarship_applications WHERE application_id = :id");
    $query->bindParam(':id', $applicationId, PDO::PARAM_INT);
    $query->execute();

    $applicant = $query->fetch(PDO::FETCH_ASSOC);

    if (!$applicant) {
        throw new Exception('Applicant not found.');
    }

    $applicantEmail = $applicant['email'];
    $applicantName = $applicant['full_name'];


    // Update the application status to "approved"
    $updateStmt = $pdo->prepare("
        UPDATE scholarship_applications 
        SET status = 'approved', notified = :notified 
        WHERE application_id = :id
    ");
    $updateStmt->bindValue(':notified', $notify ? 'yes' : 'no', PDO::PARAM_STR);
    $updateStmt->bindParam(':id', $applicationId, PDO::PARAM_INT);
    $updateStmt->execute();

    $applicationStatus = $applicant['status'];

    if ($updateStmt->rowCount() === 0) {
        throw new Exception('Failed to update application status. It may have already been processed.');
    }

    if ($notify) {
        // Send notification email

        $emailStatus = sendApprovalEmail($applicantEmail, $applicantName, $applicationStatus);

        if ($emailStatus !== true) {
            throw new Exception("Email could not be sent: $emailStatus");
        }

        $_SESSION['success_message'] = "Application approved and notification sent to $applicantName ($applicantEmail).";
        logActivity($pdo, $_SESSION['id'], "Application approved and notified", "ID $applicationId approved and notified.");
    } else {
        $_SESSION['success_message'] = "Application approved without notifying the applicant.";
        logActivity($pdo, $_SESSION['id'], "Application approved", "ID $applicationId approved without notification.");
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
    logActivity($pdo, $_SESSION['id'], "Error", $e->getMessage());
}

// Redirect back to the applications page
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../views/applications.php'));
exit;
?>
