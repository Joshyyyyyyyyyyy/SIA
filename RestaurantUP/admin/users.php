<?php
// Include database configuration

require_once '../config/database.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Get query parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$role = isset($_GET['role']) ? sanitize($_GET['role']) : '';

// Calculate offset for pagination
$offset = ($page - 1) * $limit;

try {
    // Build the base query - exclude admin users
    $countQuery = "SELECT COUNT(*) FROM users WHERE role != 'admin'";
    $dataQuery = "SELECT id, name, email, role, created_at FROM users WHERE role != 'admin'";
    $params = [];
    
    // Add search condition if provided
    if (!empty($search)) {
        $searchCondition = " AND (name LIKE :search OR email LIKE :search)";
        $countQuery .= $searchCondition;
        $dataQuery .= $searchCondition;
        $params[':search'] = "%$search%";
    }
    
    // Add role filter if provided and not 'all'
    if (!empty($role) && $role !== 'all') {
        $roleCondition = " AND role = :role";
        $countQuery .= $roleCondition;
        $dataQuery .= $roleCondition;
        $params[':role'] = $role;
    }
    
    // Add pagination to data query
    $dataQuery .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    
    // Get total count
    $countStmt = $pdo->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalCount = $countStmt->fetchColumn();
    
    // Get paginated data
    $dataStmt = $pdo->prepare($dataQuery);
    foreach ($params as $key => $value) {
        $dataStmt->bindValue($key, $value);
    }
    $dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $dataStmt->execute();
    $users = $dataStmt->fetchAll();
    
    // Prepare response
    $response = [
        'total' => $totalCount,
        'page' => $page,
        'limit' => $limit,
        'users' => $users
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