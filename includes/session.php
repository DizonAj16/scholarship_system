<?php
// Initialize the session
include '../includes/config.php';
session_start();

// Check if the user is logged in, if not then redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth/login.php");
    exit;
}

// Fetch user data from the database
if (isset($_SESSION["username"])) {
    // Prepare a select statement to fetch role and username
    $sql = "SELECT username, role FROM users WHERE username = :username";

    if ($stmt = $pdo->prepare($sql)) { // Ensure you use the correct variable for your PDO connection
        // Bind the session username parameter
        $stmt->bindParam(":username", $_SESSION["username"], PDO::PARAM_STR);
        
        // Execute the statement
        $stmt->execute();

        // Check if the user exists
        if ($stmt->rowCount() == 1) {
            // Fetch the user data
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION["username"] = $row["username"]; // Set the session username
            $_SESSION["role"] = $row["role"]; // Set the session role
        } else {
            // If the user does not exist in the database, redirect to login
            header("location: ../auth/login.php");
            exit;
        }
    }
}

// Ensure role is set in the session
if (!isset($_SESSION["role"])) {
    // Handle the case when the role is not set (e.g., redirect to login)
    header("location: ../auth/login.php");
    exit;
}


?>
