<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: login.php");
        exit;
    }
    
    try {
        // Prepare SQL statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // For debugging
        error_log("Login attempt: " . $email);
        
        if ($user) {
            // For the initial admin and staff accounts, check if using direct password comparison
            if (($email === 'Admin@gmail.com' && $password === 'Admin1234@') || 
                ($email === 'Staff@gmail.com' && $password === 'Staff1234@')) {
                
                // Login successful for default accounts
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else if ($user['role'] === 'staff') {
                    header("Location: staff/dashboard.php");
                }
                exit;
            }
            // For other accounts, use password_verify
            else if (password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin/dashboard.php");
                        break;
                    case 'staff':
                        header("Location: staff/dashboard.php");
                        break;
                    case 'customer':
                        header("Location: customer/dashboard.php");
                        break;
                    default:
                        header("Location: index.php");
                }
                exit;
            } else {
                $_SESSION['error'] = "Invalid email or password";
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Invalid email or password";
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>