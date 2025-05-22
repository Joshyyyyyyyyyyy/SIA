<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

try {
    $date = isset($_GET['date']) ? sanitize($_GET['date']) : date('Y-m-d');
    
    // Check which time slots are available
    $stmt = $pdo->prepare("
        SELECT time_slot 
        FROM reservations 
        WHERE reservation_date = ? 
        AND status IN ('pending', 'approved')
    ");
    $stmt->execute([$date]);
    
    $booked_slots = [];
    while ($row = $stmt->fetch()) {
        $booked_slots[] = $row['time_slot'];
    }
    
    // Prepare response with all time slots
    $availability = [
        'morning' => !in_array('morning', $booked_slots),
        'afternoon' => !in_array('afternoon', $booked_slots),
        'evening' => !in_array('evening', $booked_slots)
    ];
    
    echo json_encode($availability);
    
} catch (Exception $e) {
    error_log("Error checking availability: " . $e->getMessage());
    echo json_encode(['error' => 'An error occurred while checking availability']);
}