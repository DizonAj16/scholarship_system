<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'scholarship_system_db');

try {
    // Attempt to connect to MySQL database using PDO
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

function logActivity($pdo, $userId, $action, $details = "") {
    // Get the IP address of the user
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // Prepare SQL query to insert a log entry
    $sql = "INSERT INTO logs (user_id, action, details) VALUES (:user_id, :action, :details)";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind the parameters
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        $stmt->bindParam(':details', $details, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            return true; // Log inserted successfully
        } else {
            return false; // Failed to insert log
        }
    } else {
        return false; // Failed to prepare statement
    }
}
?>
