<?php

require_once '../config/database.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID is required']);
    exit;
}

$userId = (int)$_GET['id'];

try {
    // Get user by ID - exclude admin users
    $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = :id AND role != 'admin'");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $user = $stmt->fetch();
    
    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found or access denied']);
        exit;
    }
    
    // Send JSON response
    echo json_encode($user);
    
} catch(PDOException $e) {
    // Handle database errors
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>