<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Validate and sanitize input
    $name = sanitize($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = sanitize($_POST['phone'] ?? '');
    $date = sanitize($_POST['date'] ?? '');
    $time_slot = sanitize($_POST['time_slot'] ?? '');
    $guests = intval($_POST['guests'] ?? 0);
    $food_package = sanitize($_POST['food_package'] ?? '');
    $theme = sanitize($_POST['theme'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($date) || 
        empty($time_slot) || empty($guests) || empty($food_package)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit;
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    // Check if the date is in the future
    $reservation_date = new DateTime($date);
    $today = new DateTime();
    if ($reservation_date < $today) {
        echo json_encode(['success' => false, 'message' => 'Reservation date must be in the future']);
        exit;
    }
    
    // Check if the time slot is available
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE reservation_date = ? AND time_slot = ? AND status IN ('pending', 'approved')");
    $stmt->execute([$date, $time_slot]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'This time slot is already booked. Please select another time slot.']);
        exit;
    }
    
    // Calculate total price
    $total_price = calculateTotalPrice($food_package, $theme);
    
    // Generate reservation code
    $reservation_code = generateReservationCode();
    
    // Insert reservation into database
    $stmt = $pdo->prepare("
        INSERT INTO reservations (
            reservation_code, customer_name, email, phone, reservation_date, 
            time_slot, guests, food_package, theme, special_request, 
            total_price, status, created_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW()
        )
    ");
    
    $stmt->execute([
        $reservation_code, $name, $email, $phone, $date, 
        $time_slot, $guests, $food_package, $theme, $message, 
        $total_price
    ]);
    
    // Send confirmation email (in a production environment)
    // mail($email, "Reservation Confirmation - $reservation_code", "Thank you for your reservation...");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Reservation submitted successfully!',
        'reservation_code' => $reservation_code
    ]);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred. Please try again later.']);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}