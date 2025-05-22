<?php
$host = 'localhost';
$db   = 'sia';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Time slots
define('TIME_SLOTS', [
    'morning' => 'Morning (7:00 AM - 12:30 PM)',
    'afternoon' => 'Afternoon (1:00 PM - 6:30 PM)',
    'evening' => 'Evening (7:00 PM - 12:30 AM)'
]);

// Food packages with prices
define('FOOD_PACKAGES', [
    'basic' => ['name' => 'Basic Package', 'price' => 3500],
    'standard' => ['name' => 'Standard Package', 'price' => 5000],
    'premium' => ['name' => 'Premium Package', 'price' => 7500],
    'deluxe' => ['name' => 'Deluxe Package', 'price' => 10000]
]);

// Theme prices
define('THEMES', [
    'birthday' => ['name' => 'Birthday', 'price' => 2500],
    'anniversary' => ['name' => 'Anniversary', 'price' => 3000],
    'corporate' => ['name' => 'Corporate', 'price' => 5000],
    'casual' => ['name' => 'Casual', 'price' => 1500],
    'wedding' => ['name' => 'Wedding', 'price' => 8000]
]);

// Tables
define('TABLES', [
    1 => 'Table 1 (4 persons)',
    2 => 'Table 2 (6 persons)',
    3 => 'Table 3 (8 persons)'
]);

// Reservation status
define('STATUS', [
    'pending' => 'Pending',
    'approved' => 'Approved',
    'cancelled' => 'Cancelled',
    'completed' => 'Completed'
]);

// Helper functions
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function generateReservationCode() {
    return 'DP' . date('Ymd') . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
}

function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

function getTimeSlotName($slot) {
    return TIME_SLOTS[$slot] ?? $slot;
}

function getFoodPackageName($package) {
    return FOOD_PACKAGES[$package]['name'] ?? $package;
}

function getFoodPackagePrice($package) {
    return FOOD_PACKAGES[$package]['price'] ?? 0;
}

function getThemeName($theme) {
    return THEMES[$theme]['name'] ?? $theme;
}

function getThemePrice($theme) {
    return THEMES[$theme]['price'] ?? 0;
}

function getTableName($table_id) {
    return TABLES[$table_id] ?? "Table $table_id";
}

function getStatusName($status) {
    return STATUS[$status] ?? $status;
}

function getStatusClass($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'approved':
            return 'success';
        case 'cancelled':
            return 'danger';
        case 'completed':
            return 'info';
        default:
            return 'secondary';
    }
}

// Calculate total price
function calculateTotalPrice($food_package, $theme) {
    $total = 0;
    
    // Add food package price
    $total += getFoodPackagePrice($food_package);
    
    // Add theme price
    $total += getThemePrice($theme);
    
    return $total;
}


function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function userExistsByEmail($pdo, $email, $excludeId = null) {
    $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
    $params = [':email' => $email];
    
    if ($excludeId !== null) {
        $sql .= " AND id != :id";
        $params[':id'] = $excludeId;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchColumn() > 0;
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function jsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function validateDateRange($start, $end) {
    $startDate = DateTime::createFromFormat('Y-m-d', $start);
    $endDate = DateTime::createFromFormat('Y-m-d', $end);
    
    if (!$startDate || !$endDate) {
        return false;
    }
    
    return $startDate <= $endDate;
}

?>