<?php

require_once '../config/database.php';

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    jsonResponse(false, 'Method not allowed');
}

// Get user ID
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (empty($userId)) {
    jsonResponse(false, 'User ID is required');
}

try {
    // Check if user exists and is not an admin
    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch();
    
    if (!$user) {
        jsonResponse(false, 'User not found');
    }
    
    // Prevent deleting admin users
    if ($user['role'] === 'admin') {
        jsonResponse(false, 'Cannot delete admin users');
    }
    
    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    // Return success response
    jsonResponse(true, 'User deleted successfully');
    
} catch(PDOException $e) {
    // Handle database errors
    jsonResponse(false, 'Database error: ' . $e->getMessage());
}
?>