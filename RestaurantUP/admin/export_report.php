<?php
// Include database configuration

require_once '../config/database.php';

// Get parameters
$reportType = isset($_GET['type']) ? $_GET['type'] : 'income';
$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$endDate = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');

// Validate date range
if (!validateDateRange($startDate, $endDate)) {
    die('Invalid date range');
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $reportType . '_report_' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

try {
    if ($reportType === 'income') {
        // Write income report headers
        fputcsv($output, [
            'Date', 
            'Reservations', 
            'Guests', 
            'Basic Package', 
            'Standard Package', 
            'Premium Package', 
            'Deluxe Package', 
            'Total Revenue'
        ]);
        
        // Get income report data
        $query = "
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
                reservation_date
        ";
    } else {
        // Write business report headers
        fputcsv($output, [
            'Date', 
            'Total Reservations', 
            'Completed', 
            'Cancelled', 
            'Pending', 
            'Morning', 
            'Afternoon', 
            'Evening', 
            'Total Guests'
        ]);
        
        // Get business report data
        $query = "
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
                reservation_date
        ";
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    
    // Write data rows
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    
    // Add summary row
    if ($reportType === 'income') {
        $summaryQuery = "
            SELECT 
                'TOTAL' as date,
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
        ";
    } else {
        $summaryQuery = "
            SELECT 
                'TOTAL' as date,
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
        ";
    }
    
    $stmt = $pdo->prepare($summaryQuery);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();
    
    // Add empty row before summary
    fputcsv($output, []);
    
    // Write summary row
    $summaryRow = $stmt->fetch(PDO::FETCH_ASSOC);
    fputcsv($output, $summaryRow);
    
} catch(PDOException $e) {
    // Write error message
    fputcsv($output, ['Error: ' . $e->getMessage()]);
}

// Close the output stream
fclose($output);
?>