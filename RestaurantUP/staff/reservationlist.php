<?php
session_start();

// Check if user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'staff') {
    $_SESSION['error'] = "You don't have permission to access this page";
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

// Get customer statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$totalCustomers = $stmt->fetch()['total'];

// Get recent customers
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC LIMIT 5");
$recentCustomers = $stmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['reservation_id'])) {
    $reservation_id = intval($_POST['reservation_id']);
    
    if ($_POST['action'] === 'checkout') {
        try {
            $stmt = $pdo->prepare("
                UPDATE reservations 
                SET status = 'completed', checkout_by = ?, checkout_at = NOW() 
                WHERE id = ? AND status = 'approved' AND table_id IS NOT NULL
            ");
            $stmt->execute([$_SESSION['staff_id'], $reservation_id]);
            
            $_SESSION['message'] = 'Reservation checked out successfully!';
            $_SESSION['message_type'] = 'success';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error checking out: ' . $e->getMessage();
            $_SESSION['message_type'] = 'danger';
        }
    }
    
    header('Location: reservationlist.php');
    exit;
}

// Get filter parameters
$status = isset($_GET['status']) ? sanitize($_GET['status']) : 'approved';
$date = isset($_GET['date']) ? sanitize($_GET['date']) : date('Y-m-d');

// Get reservations based on filters
$params = [];
$sql = "SELECT * FROM reservations WHERE 1=1";

if ($status !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $status;
}

if ($date !== 'all') {
    $sql .= " AND reservation_date = ?";
    $params[] = $date;
}

$sql .= " ORDER BY reservation_date ASC, 
    CASE 
        WHEN time_slot = 'morning' THEN 1 
        WHEN time_slot = 'afternoon' THEN 2 
        WHEN time_slot = 'evening' THEN 3 
    END";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll();

// Get unique dates for filter
$stmt = $pdo->query("SELECT DISTINCT reservation_date FROM reservations ORDER BY reservation_date DESC LIMIT 30");
$dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get page title
$page_title = 'Reservation List';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | User Management System</title>
    <link rel="stylesheet" href="../css/staff.css">
       <link rel="stylesheet" href="../css/staffs.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-user-tie"></i>
                    <h2>Staff Portal</h2>
                </div>
                <button class="menu-toggle" id="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="user-profile">
                <div class="user-avatar">
                    <img src="../image/logoo.jpg" alt="Staff">
                </div>
                <div class="user-info">
                    <h3><?php echo $_SESSION['user_name']; ?></h3>
                    <p>Staff Member</p>
                </div>
            </div>
            
            <ul class="menu">
                <li class="menu-title">MAIN MENU</li>
                <li class="menu-item">
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="customers.php">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                        <span class="badge"><?php echo $totalCustomers; ?></span>
                    </a>
                </li >
                <li class="menu-item"">
                    <a href="reservation.php">
                    <i class="fas fa-list-alt"></i>
                        <span>Reservation</span>
                        <span class="badge"><?php echo $totalCustomers; ?></span>
                    </a>
                </li>
                <li class="menu-item active">
                    <a href="reservationlist.php">
                    <i class="fas fa-calendar-check"></i>
                        <span>Reservation List</span>
                        <span class="badge badge-warning">3</span>
                    </a>
                </li>
                <li class="menu-item">
                     <a href="table.php">
                        <i class="fas fa-table"></i>
                        <span>Tables</span>
                        <span class="badge badge-warning">3</span>
                    </a>
                </li>
              
                
                <li class="menu-title">ACCOUNT</li>
                <li class="menu-item">
                    <a href="profile.php">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="settings.php">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="../logout.php" class="logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="top-bar">
                <div class="page-title">
                    <h1>Staff Dashboard</h1>
                    <nav class="breadcrumb">
                        <a href="dashboard.php">Home</a> / Dashboard
                    </nav>
                </div>
                
                <div class="top-bar-actions">
                    <div class="search">
                        <input type="text" placeholder="Search...">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                    
                    <div class="notifications">
                        <button class="notification-btn">
                            <i class="far fa-bell"></i>
                            <span class="badge">5</span>
                        </button>
                        <div class="notification-dropdown">
                            <div class="notification-header">
                                <h3>Notifications</h3>
                                <a href="#">Mark all as read</a>
                            </div>
                            <div class="notification-list">
                                <a href="#" class="notification-item unread">
                                    <div class="notification-icon bg-primary">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p>New customer registered</p>
                                        <span>2 minutes ago</span>
                                    </div>
                                </a>
                                <a href="#" class="notification-item">
                                    <div class="notification-icon bg-success">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p>New support ticket received</p>
                                        <span>1 hour ago</span>
                                    </div>
                                </a>
                                <a href="#" class="notification-item">
                                    <div class="notification-icon bg-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p>System update required</p>
                                        <span>3 hours ago</span>
                                    </div>
                                </a>
                            </div>
                            <div class="notification-footer">
                                <a href="#">View all notifications</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="user-dropdown">
                        <button class="user-dropdown-btn">
                            <img src="../img/staff-avatar.png" alt="Staff">
                            <span><?php echo $_SESSION['user_name']; ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown-menu">
                            <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                            <div class="dropdown-divider"></div>
                            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </header>
            
            <div class="content">
               <main class="content">
           
            
            <div class="content-body">
                <div class="content-header">
                    <h1><?php echo $page_title; ?></h1>
                </div>
                
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                    <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                    ?>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <form action="" method="GET" class="filter-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                                        <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                        <?php foreach (STATUS as $key => $value): ?>
                                        <option value="<?php echo $key; ?>" <?php echo $status === $key ? 'selected' : ''; ?>>
                                            <?php echo $value; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <select name="date" id="date" class="form-control" onchange="this.form.submit()">
                                        <option value="all" <?php echo $date === 'all' ? 'selected' : ''; ?>>All Dates</option>
                                        <?php foreach ($dates as $d): ?>
                                        <option value="<?php echo $d; ?>" <?php echo $date === $d ? 'selected' : ''; ?>>
                                            <?php echo date('M d, Y', strtotime($d)); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <?php if (empty($reservations)): ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-check"></i>
                                <p>No reservations found with the selected filters.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Customer</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Guests</th>
                                            <th>Table</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reservations as $reservation): ?>
                                        <tr>
                                            <td><?php echo $reservation['reservation_code']; ?></td>
                                            <td>
                                                <div><?php echo $reservation['customer_name']; ?></div>
                                                <small><?php echo $reservation['phone']; ?></small>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($reservation['reservation_date'])); ?></td>
                                            <td><?php echo getTimeSlotName($reservation['time_slot']); ?></td>
                                            <td><?php echo $reservation['guests']; ?></td>
                                            <td>
                                                <?php if ($reservation['table_id']): ?>
                                                    <?php echo getTableName($reservation['table_id']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Not assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo getStatusClass($reservation['status']); ?>">
                                                    <?php echo getStatusName($reservation['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            onclick="viewDetails(<?php echo $reservation['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <?php if ($reservation['status'] === 'approved' && $reservation['table_id']): ?>
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="checkoutReservation(<?php echo $reservation['id']; ?>)">
                                                        <i class="fas fa-check-circle"></i> Checkout
                                                    </button>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($reservation['status'] === 'completed'): ?>
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                            onclick="printReceipt(<?php echo $reservation['id']; ?>)">
                                                        <i class="fas fa-print"></i> Receipt
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
            </div>
            
            <!-- View Details Modal -->
    <div class="modal" id="detailsModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Reservation Details</h3>
                    <button type="button" class="close-modal" onclick="closeModal('detailsModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body" id="detailsModalBody">
                    <div class="loading">Loading details...</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Checkout Modal -->
    <div class="modal" id="checkoutModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Checkout Reservation</h3>
                    <button type="button" class="close-modal" onclick="closeModal('checkoutModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to checkout this reservation?</p>
                    <form action="reservationlist.php" method="POST" id="checkoutForm">
                        <input type="hidden" name="reservation_id" id="checkout_reservation_id">
                        <input type="hidden" name="action" value="checkout">
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="closeModal('checkoutModal')">Cancel</button>
                            <button type="submit" class="btn btn-success">Checkout</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Receipt Modal -->
    <div class="modal" id="receiptModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Receipt</h3>
                    <button type="button" class="close-modal" onclick="closeModal('receiptModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body" id="receiptModalBody">
                    <div class="loading">Loading receipt...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('receiptModal')">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printReceiptContent()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../js/staff.js"></script>
    <script>
        function viewDetails(id) {
            const modal = document.getElementById('detailsModal');
            const modalBody = document.getElementById('detailsModalBody');
            
            modalBody.innerHTML = '<div class="loading">Loading details...</div>';
            modal.classList.add('show');
            
            // Fetch reservation details
            fetch(`get_reservation_details.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    modalBody.innerHTML = '<div class="alert alert-danger">Error loading details. Please try again.</div>';
                });
        }
        
        function checkoutReservation(id) {
            document.getElementById('checkout_reservation_id').value = id;
            document.getElementById('checkoutModal').classList.add('show');
        }
        
        function printReceipt(id) {
            const modal = document.getElementById('receiptModal');
            const modalBody = document.getElementById('receiptModalBody');
            
            modalBody.innerHTML = '<div class="loading">Loading receipt...</div>';
            modal.classList.add('show');
            
            // Fetch receipt
            fetch(`get_receipt.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    modalBody.innerHTML = '<div class="alert alert-danger">Error loading receipt. Please try again.</div>';
                });
        }
        
        function printReceiptContent() {
            const printContents = document.getElementById('receipt-printable').innerHTML;
            const originalContents = document.body.innerHTML;
            
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            
            // Reattach event listeners after printing
            setTimeout(() => {
                location.reload();
            }, 500);
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }
    </script>
        </main>
    </div>
    
    <script>
        // Toggle sidebar
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.querySelector('.dashboard').classList.toggle('sidebar-collapsed');
        });
        
        // User dropdown
        document.querySelector('.user-dropdown-btn').addEventListener('click', function() {
            document.querySelector('.user-dropdown-menu').classList.toggle('show');
        });
        
        // Notification dropdown
        document.querySelector('.notification-btn').addEventListener('click', function() {
            document.querySelector('.notification-dropdown').classList.toggle('show');
        });
        
        // Close dropdowns when clicking outside
        window.addEventListener('click', function(e) {
            if (!e.target.matches('.user-dropdown-btn') && !e.target.closest('.user-dropdown-menu')) {
                const dropdown = document.querySelector('.user-dropdown-menu');
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
            
            if (!e.target.matches('.notification-btn') && !e.target.closest('.notification-dropdown')) {
                const dropdown = document.querySelector('.notification-dropdown');
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        });
        
        // Animate stat cards on load
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate');
                }, 100 * index);
            });
        });
    </script>
</body>
</html>