<?php
// Include database configuration

require_once '../config/database.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Get date range parameters
$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$endDate = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');

// Validate date range
if (!validateDateRange($startDate, $endDate)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date range']);
    exit;
}

try {
    // Get summary data
    $summaryQuery = "
        SELECT 
            COUNT(*) as total_reservations,
            COUNT(CASE WHEN status = 'completed' THEN 1 ELSE NULL END) as completed_count,
            COUNT(CASE WHEN status = 'cancelled' THEN 1 ELSE NULL END) as cancelled_count,
            SUM(guests) as total_guests,
            AVG(guests) as avg_guests
        FROM 
            reservations
        WHERE 
            reservation_date BETWEEN :start_date AND :end_date
    ";
    
    $stmt = $pdo->prepare($summaryQuery);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $summary = $stmt->fetch();
    
    // Calculate completion rate
    $completionRate = 0;
    if ($summary['total_reservations'] > 0) {
        $completionRate = round(($summary['completed_count'] / $summary['total_reservations']) * 100);
    }
    
    // Get status counts
    $statusQuery = "
        SELECT 
            status,
            COUNT(*) as count
        FROM 
            reservations
        WHERE 
            reservation_date BETWEEN :start_date AND :end_date
        GROUP BY 
            status
    ";
    
    $stmt = $pdo->prepare($statusQuery);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $statusData = $stmt->fetchAll();
    
    $statusCounts = [];
    foreach ($statusData as $row) {
        $statusCounts[$row['status']] = intval($row['count']);
    }
    
    // Get time slot counts
    $timeSlotQuery = "
        SELECT 
            time_slot,
            COUNT(*) as count
        FROM 
            reservations
        WHERE 
            reservation_date BETWEEN :start_date AND :end_date
        GROUP BY 
            time_slot
    ";
    
    $stmt = $pdo->prepare($timeSlotQuery);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $timeSlotData = $stmt->fetchAll();
    
    $timeSlotCounts = [];
    foreach ($timeSlotData as $row) {
        $timeSlotCounts[$row['time_slot']] = intval($row['count']);
    }
    
    // Get package counts
    $packageQuery = "
        SELECT 
            food_package,
            COUNT(*) as count
        FROM 
            reservations
        WHERE 
            reservation_date BETWEEN :start_date AND :end_date
        GROUP BY 
            food_package
    ";
    
    $stmt = $pdo->prepare($packageQuery);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $packageData = $stmt->fetchAll();
    
    $packageCounts = [];
    foreach ($packageData as $row) {
        $packageCounts[$row['food_package']] = intval($row['count']);
    }
    
    // Get reservation trend
    $trendQuery = "
        SELECT 
            reservation_date as date,
            COUNT(*) as count
        FROM 
            reservations
        WHERE 
            reservation_date BETWEEN :start_date AND :end_date
        GROUP BY 
            reservation_date
        ORDER BY 
            reservation_date
    ";
    
    $stmt = $pdo->prepare($trendQuery);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $reservationTrend = $stmt->fetchAll();
    
    // Get detailed business report
    $detailsQuery = "
        SELECT 
            reservation_date as date,
            COUNT(*) as total_reservations,
            COUNT(CASE WHEN status = 'completed' THEN 1 ELSE NULL END) as completed,
            COUNT(CASE WHEN status = 'cancelled' THEN 1 ELSE NULL END) as cancelled,
            COUNT(CASE WHEN status = 'pending' OR status = 'approved' THEN 1 ELSE NULL END) as pending,
            COUNT(CASE WHEN time_slot = 'morning' THEN 1 ELSE NULL END) as morning,
            COUNT(CASE WHEN time_slot = 'afternoon' THEN 1 ELSE NULL END) as afternoon,
            COUNT(CASE WHEN time_slot = 'evening' THEN 1 ELSE NULL END) as evening,
            SUM(guests) as total_guests
        FROM 
            reservations
        WHERE 
            reservation_date BETWEEN :start_date AND :end_date
        GROUP BY 
            reservation_date
        ORDER BY 
            reservation_date DESC
    ";
    
    $stmt = $pdo->prepare($detailsQuery);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $details = $stmt->fetchAll();
    
    // Prepare response
    $response = [
        'summary' => [
            'total_reservations' => intval($summary['total_reservations']),
            'completion_rate' => $completionRate,
            'total_guests' => intval($summary['total_guests']),
            'avg_guests' => round(floatval($summary['avg_guests']), 1)
        ],
        'status_counts' => $statusCounts,
        'time_slot_counts' => $timeSlotCounts,
        'package_counts' => $packageCounts,
        'reservation_trend' => $reservationTrend,
        'details' => $details
    ];
    
    // Send JSON response
    echo json_encode($response);
    
} catch(PDOException $e) {
    // Handle database errors
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>