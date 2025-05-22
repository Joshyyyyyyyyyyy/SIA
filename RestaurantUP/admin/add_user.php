<?php
// Include database configuration

require_once '../config/database.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    jsonResponse(false, 'Method not allowed');
}

// Get and sanitize form data
$name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$role = isset($_POST['role']) ? sanitize($_POST['role']) : '';

// Validate input
if (empty($name) || empty($email) || empty($password) || empty($role)) {
    jsonResponse(false, 'All fields are required');
}

if (!validateEmail($email)) {
    jsonResponse(false, 'Invalid email format');
}

// Ensure role is not admin
if ($role === 'admin' || !in_array($role, ['staff', 'customer'])) {
    jsonResponse(false, 'Invalid role');
}

try {
    // Check if email already exists
    if (userExistsByEmail($pdo, $email)) {
        jsonResponse(false, 'Email already exists');
    }
    
    // Hash the password
    $hashedPassword = hashPassword($password);
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    
    // Return success response
    jsonResponse(true, 'User added successfully', ['id' => $pdo->lastInsertId()]);
    
} catch(PDOException $e) {
    // Handle database errors
    jsonResponse(false, 'Database error: ' . $e->getMessage());
}
?>