<?php
// check_username.php
require_once "config.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    
    // Check if username exists
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        $exists = $stmt->rowCount() > 0;
        echo json_encode(['exists' => $exists]);
    } else {
        echo json_encode(['exists' => false, 'error' => 'Database error']);
    }
} else {
    echo json_encode(['exists' => false, 'error' => 'Invalid request']);
}