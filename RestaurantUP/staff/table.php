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



// Process table assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'], $_POST['table_id'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $table_id = intval($_POST['table_id']);
    
    try {
        $stmt = $pdo->prepare("
            UPDATE reservations 
            SET table_id = ?, table_assigned_by = ?, table_assigned_at = NOW() 
            WHERE id = ? AND status = 'approved'
        ");
        $stmt->execute([$table_id, $_SESSION['staff_id'], $reservation_id]);
        
        $_SESSION['message'] = 'Table assigned successfully!';
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        $_SESSION['message'] = 'Error assigning table: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }
    
    header('Location: table.php');
    exit;
}

// Get approved reservations without assigned tables
$stmt = $pdo->prepare("
    SELECT * FROM reservations 
    WHERE status = 'approved' AND table_id IS NULL
    ORDER BY reservation_date ASC, 
    CASE 
        WHEN time_slot = 'morning' THEN 1 
        WHEN time_slot = 'afternoon' THEN 2 
        WHEN time_slot = 'evening' THEN 3 
    END
");
$stmt->execute();
$approved_reservations = $stmt->fetchAll();

// Get page title
$page_title = 'Assign Tables';
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
                <li class="menu-item">
                    <a href="reservation.php">
                    <i class="fas fa-list-alt"></i>
                        <span>Reservation</span>
                        <span class="badge"><?php echo $totalCustomers; ?></span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="reservationlist.php">
                    <i class="fas fa-calendar-check"></i>
                        <span>Reservation List</span>
                        <span class="badge badge-warning">3</span>
                    </a>
                </li>
                <li class="menu-item active">
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
                    <div class="card-body">
                        <?php if (empty($approved_reservations)): ?>
                            <div class="empty-state">
                                <i class="fas fa-chair"></i>
                                <p>No reservations waiting for table assignment.</p>
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
                                            <th>Package</th>
                                            <th>Theme</th>
                                            <th>Assign Table</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($approved_reservations as $reservation): ?>
                                        <tr>
                                            <td><?php echo $reservation['reservation_code']; ?></td>
                                            <td>
                                                <div><?php echo $reservation['customer_name']; ?></div>
                                                <small><?php echo $reservation['phone']; ?></small>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($reservation['reservation_date'])); ?></td>
                                            <td><?php echo getTimeSlotName($reservation['time_slot']); ?></td>
                                            <td><?php echo $reservation['guests']; ?></td>
                                            <td><?php echo getFoodPackageName($reservation['food_package']); ?></td>
                                            <td>
                                                <?php if ($reservation['theme']): ?>
                                                    <?php echo getThemeName($reservation['theme']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">None</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary" 
                                                        onclick="assignTable(<?php echo $reservation['id']; ?>, <?php echo $reservation['guests']; ?>)">
                                                    <i class="fas fa-chair"></i> Assign
                                                </button>
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
    
    <!-- Assign Table Modal -->
    <div class="modal" id="assignTableModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Assign Table</h3>
                    <button type="button" class="close-modal" onclick="closeModal('assignTableModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="table.php" method="POST" id="assignTableForm">
                        <input type="hidden" name="reservation_id" id="reservation_id">
                        
                        <div class="form-group">
                            <label for="table_id">Select Table</label>
                            <select name="table_id" id="table_id" class="form-control" required>
                                <option value="" disabled selected>Choose a table</option>
                                <?php foreach (TABLES as $id => $name): ?>
                                <option value="<?php echo $id; ?>" data-capacity="<?php echo substr($name, -10, 1); ?>">
                                    <?php echo $name; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted" id="table_capacity_warning" style="display: none; color: #dc3545;">
                                <i class="fas fa-exclamation-triangle"></i> 
                                This table may not accommodate the number of guests.
                            </small>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="closeModal('assignTableModal')">Cancel</button>
                            <button type="submit" class="btn btn-primary">Assign Table</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../js/staff.js"></script>
    <script>
        function assignTable(id, guests) {
            document.getElementById('reservation_id').value = id;
            
            // Reset select
            const tableSelect = document.getElementById('table_id');
            tableSelect.selectedIndex = 0;
            
            // Show modal
            document.getElementById('assignTableModal').classList.add('show');
            
            // Add event listener for table capacity check
            tableSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const tableCapacity = parseInt(selectedOption.getAttribute('data-capacity'));
                const warning = document.getElementById('table_capacity_warning');
                
                if (tableCapacity < guests) {
                    warning.style.display = 'block';
                } else {
                    warning.style.display = 'none';
                }
            });
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