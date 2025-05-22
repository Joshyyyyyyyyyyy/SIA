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
            SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as total_revenue,
            AVG(CASE WHEN status = 'completed' THEN total_price ELSE NULL END) as avg_per_reservation,
            COUNT(CASE WHEN status = 'completed' THEN 1 ELSE NULL END) as completed_count,
            SUM(CASE WHEN status = 'cancelled' THEN total_price ELSE 0 END) as cancelled_revenue
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
    
    // Get revenue trend data
    $trendQuery = "
        SELECT 
            reservation_date as date,
            SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as revenue
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
    $revenueTrend = $stmt->fetchAll();
    
    // Get revenue by package
    $packageQuery = "
        SELECT 
            food_package,
            SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as revenue
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
    
    $revenueByPackage = [];
    foreach ($packageData as $row) {
        $revenueByPackage[$row['food_package']] = floatval($row['revenue']);
    }
    
    // Get revenue by time slot
    $timeSlotQuery = "
        SELECT 
            time_slot,
            SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as revenue
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
    
    $revenueByTimeSlot = [];
    foreach ($timeSlotData as $row) {
        $revenueByTimeSlot[$row['time_slot']] = floatval($row['revenue']);
    }
    
    // Get top revenue days
    $topDaysQuery = "
        SELECT 
            DAYNAME(reservation_date) as day,
            SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as revenue
        FROM 
            reservations
        WHERE 
            reservation_date BETWEEN :start_date AND :end_date
        GROUP BY 
            DAYNAME(reservation_date)
        ORDER BY 
            revenue DESC
        LIMIT 5
    ";
    
    $stmt = $pdo->prepare($topDaysQuery);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    $topRevenueDays = $stmt->fetchAll();
    
    // Get detailed income report
    $detailsQuery = "
        SELECT 
            reservation_date as date,
            COUNT(*) as reservations,
            SUM(guests) as guests,
            SUM(CASE WHEN food_package = 'basic' AND status = 'completed' THEN total_price ELSE 0 END) as basic_package,
            SUM(CASE WHEN food_package = 'standard' AND status = 'completed' THEN total_price ELSE 0 END) as standard_package,
            SUM(CASE WHEN food_package = 'premium' AND status = 'completed' THEN total_price ELSE 0 END) as premium_package,
            SUM(CASE WHEN food_package = 'deluxe' AND status = 'completed' THEN total_price ELSE 0 END) as deluxe_package,
            SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as total_revenue
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
            'total_revenue' => floatval($summary['total_revenue']),
            'avg_per_reservation' => floatval($summary['avg_per_reservation']),
            'completed_count' => intval($summary['completed_count']),
            'cancelled_revenue' => floatval($summary['cancelled_revenue'])
        ],
        'revenue_trend' => $revenueTrend,
        'revenue_by_package' => $revenueByPackage,
        'revenue_by_time_slot' => $revenueByTimeSlot,
        'top_revenue_days' => $topRevenueDays,
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