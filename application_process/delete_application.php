<?php
require '../includes/session.php'; // Ensure session is started

// Check if an ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Set error message in session and redirect
    $_SESSION['error_message'] = 'Invalid application ID.';
    logActivity($pdo, $_SESSION['id'], 'Error deleting application', 'Invalid application ID provided.');
    header("Location: ../views/applications.php");
    exit;
}

$applicationId = (int) $_GET['id']; // Sanitize the input

try {
    // Prepare the DELETE SQL query
    $stmt = $pdo->prepare("DELETE FROM scholarship_applications WHERE application_id = :id");
    $stmt->bindParam(':id', $applicationId, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute() && $stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Application ID $applicationId has been deleted successfully.";
        logActivity($pdo, $_SESSION['id'], 'Application Deleted', "Application ID $applicationId deleted successfully.");
    } else {
        $_SESSION['error_message'] = "No application found with ID $applicationId or it has already been deleted.";
        logActivity($pdo, $_SESSION['id'], 'Error Deleting Application', "No application found with ID $applicationId or it has already been deleted.");
    }
} catch (PDOException $e) {
    // Handle database errors
    $_SESSION['error_message'] = "Error deleting application: " . $e->getMessage();
    logActivity($pdo, $_SESSION['id'], 'Error Deleting Application', "Error: " . $e->getMessage());
}

// Redirect back to the applications page
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../views/applications.php'));
exit;
?>
