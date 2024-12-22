<?php
// Initialize the session
session_start();

// Include the config file for database connection and logActivity function
require_once "../includes/config.php";

// Check if a user is logged in before logging out
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $user_id = $_SESSION["id"];
    $username = $_SESSION["username"];

    // Log the logout activity
    $action = "User logged out";
    $details = "User '$username' logged out successfully.";
    logActivity($pdo, $user_id, $action, $details);

    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session.
    session_destroy();
}

// Redirect to login page
header("location: ./login.php");
exit;
?>
