<?php
include 'session.php';
header('Content-Type: application/json');

if (!isset($_GET['grant_name'])) {
    echo json_encode(['success' => false, 'message' => 'Grant name is required']);
    exit;
}

$grant_name = $_GET['grant_name'];

try {
    $stmt = $pdo->prepare("SELECT requirement_name, requirement_type FROM grant_requirements WHERE grant_name = ? ORDER BY display_order, requirement_name");
    $stmt->execute([$grant_name]);
    $requirements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'requirements' => $requirements
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}