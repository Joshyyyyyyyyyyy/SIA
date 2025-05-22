<?php
// Include database configuration
require_once '../config/database.php';
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    jsonResponse(false, 'Method not allowed');
}

// Get and sanitize form data
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$role = isset($_POST['role']) ? sanitize($_POST['role']) : '';

// Validate input
if (empty($id) || empty($name) || empty($email) || empty($role)) {
    jsonResponse(false, 'Required fields are missing');
}

if (!validateEmail($email)) {
    jsonResponse(false, 'Invalid email format');
}

// Ensure role is not admin
if ($role === 'admin' || !in_array($role, ['staff', 'customer'])) {
    jsonResponse(false, 'Invalid role');
}

try {
    // Check if user exists and is not an admin
    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch();
    
    if (!$user) {
        jsonResponse(false, 'User not found');
    }
    
    // Prevent editing admin users
    if ($user['role'] === 'admin') {
        jsonResponse(false, 'Cannot edit admin users');
    }
    
    // Check if email already exists for another user
    if (userExistsByEmail($pdo, $email, $id)) {
        jsonResponse(false, 'Email already exists for another user');
    }
    
    // Update user
    if (!empty($password)) {
        // Update with new password
        $hashedPassword = hashPassword($password);
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, password = :password, role = :role WHERE id = :id");
        $stmt->bindParam(':password', $hashedPassword);
    } else {
        // Update without changing password
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id");
    }
    
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Return success response
    jsonResponse(true, 'User updated successfully');
    
} catch(PDOException $e) {
    // Handle database errors
    jsonResponse(false, 'Database error: ' . $e->getMessage());
}
?>