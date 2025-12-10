<?php
require '../includes/session.php'; // Ensure this includes database connection and session setup

try {
    // Check if the application ID is provided
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Invalid application ID.');
    }

    $applicationId = (int) $_GET['id'];

    // Prepare the SQL query to update the application status to 'rejected'
    $stmt = $pdo->prepare("
        UPDATE scholarship_applications 
        SET status = 'not qualified' 
        WHERE application_id = :id
    ");
    $stmt->bindValue(':id', $applicationId, PDO::PARAM_INT);
    $stmt->execute();

    // Check if any row was updated
    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Application ID $applicationId has been rejected.";
        logActivity($pdo, $_SESSION['id'], 'Application Status Rejected', "Application ID $applicationId marked as 'rejected'.");
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
