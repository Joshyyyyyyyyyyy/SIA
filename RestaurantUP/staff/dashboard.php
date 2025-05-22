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
                    <img src="../img/staff-avatar.png" alt="Staff">
                </div>
                <div class="user-info">
                    <h3><?php echo $_SESSION['user_name']; ?></h3>
                    <p>Staff Member</p>
                </div>
            </div>
            
            <ul class="menu">
                <li class="menu-title">MAIN MENU</li>
                <li class="menu-item active">
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
                </li>
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
                <div class="welcome-banner">
                    <div class="welcome-content">
                        <h2>Welcome back, <?php echo $_SESSION['user_name']; ?>!</h2>
                        <p>Here's what's happening with your customers today.</p>
                        <button class="btn btn-primary">View Reports <i class="fas fa-arrow-right"></i></button>
                    </div>
                    <div class="welcome-image">
                        <img src="../img/staff-welcome.png" alt="Welcome">
                    </div>
                </div>
                
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-card-info">
                                <h3><?php echo $totalCustomers; ?></h3>
                                <p>Total Customers</p>
                            </div>
                        </div>
                        <div class="stat-card-footer">
                            <span class="text-success"><i class="fas fa-arrow-up"></i> 12%</span> from last month
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-icon bg-success">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div class="stat-card-info">
                                <h3>24</h3>
                                <p>Support Tickets</p>
                            </div>
                        </div>
                        <div class="stat-card-footer">
                            <span class="text-success"><i class="fas fa-arrow-down"></i> 8%</span> from last month
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-card-info">
                                <h3>3</h3>
                                <p>Pending Tickets</p>
                            </div>
                        </div>
                        <div class="stat-card-footer">
                            <span class="text-danger"><i class="fas fa-arrow-up"></i> 2</span> from yesterday
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-card-icon bg-info">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-card-info">
                                <h3>18</h3>
                                <p>Resolved Today</p>
                            </div>
                        </div>
                        <div class="stat-card-footer">
                            <span class="text-success"><i class="fas fa-arrow-up"></i> 5</span> from yesterday
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-8">
                        <div class="card">
                            <div class="card-header">
                                <h3><i class="fas fa-users"></i> Recent Customers</h3>
                                <div class="card-actions">
                                    <button class="btn-icon" title="Refresh"><i class="fas fa-sync-alt"></i></button>
                                    <button class="btn-icon" title="Export"><i class="fas fa-download"></i></button>
                                    <button class="btn-icon" title="Settings"><i class="fas fa-ellipsis-v"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Email</th>
                                                <th>Joined</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentCustomers as $customer): ?>
                                            <tr>
                                                <td>
                                                    <div class="user-info-cell">
                                                        <div class="user-avatar">
                                                            <?php echo strtoupper(substr($customer['name'], 0, 1)); ?>
                                                        </div>
                                                        <div class="user-name"><?php echo $customer['name']; ?></div>
                                                    </div>
                                                </td>
                                                <td><?php echo $customer['email']; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                                                <td><span class="badge badge-success">Active</span></td>
                                                <td>
                                                    <div class="table-actions">
                                                        <button class="btn-action btn-view" title="View Profile"><i class="fas fa-eye"></i></button>
                                                        <button class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i></button>
                                                        <button class="btn-action btn-delete" title="Delete"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="customers.php" class="btn btn-outline-primary btn-sm">View All Customers</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h3><i class="fas fa-ticket-alt"></i> Recent Tickets</h3>
                            </div>
                            <div class="card-body">
                                <div class="ticket-list">
                                    <div class="ticket-item">
                                        <div class="ticket-priority high"></div>
                                        <div class="ticket-content">
                                            <h4>Account access issue</h4>
                                            <p>John Doe - 2 hours ago</p>
                                        </div>
                                        <div class="ticket-status">
                                            <span class="badge badge-danger">Open</span>
                                        </div>
                                    </div>
                                    
                                    <div class="ticket-item">
                                        <div class="ticket-priority medium"></div>
                                        <div class="ticket-content">
                                            <h4>Payment question</h4>
                                            <p>Jane Smith - 5 hours ago</p>
                                        </div>
                                        <div class="ticket-status">
                                            <span class="badge badge-warning">Pending</span>
                                        </div>
                                    </div>
                                    
                                    <div class="ticket-item">
                                        <div class="ticket-priority low"></div>
                                        <div class="ticket-content">
                                            <h4>Feature request</h4>
                                            <p>Bob Johnson - 1 day ago</p>
                                        </div>
                                        <div class="ticket-status">
                                            <span class="badge badge-info">In Progress</span>
                                        </div>
                                    </div>
                                    
                                    <div class="ticket-item">
                                        <div class="ticket-priority medium"></div>
                                        <div class="ticket-content">
                                            <h4>Password reset</h4>
                                            <p>Alice Brown - 1 day ago</p>
                                        </div>
                                        <div class="ticket-status">
                                            <span class="badge badge-success">Resolved</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="support.php" class="btn btn-outline-primary btn-sm">View All Tickets</a>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h3><i class="fas fa-tasks"></i> Quick Tasks</h3>
                            </div>
                            <div class="card-body">
                                <div class="task-list">
                                    <div class="task-item">
                                        <label class="task-checkbox">
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                            <span class="task-text">Review new customer accounts</span>
                                        </label>
                                    </div>
                                    
                                    <div class="task-item">
                                        <label class="task-checkbox">
                                            <input type="checkbox" checked>
                                            <span class="checkmark"></span>
                                            <span class="task-text">Respond to urgent support tickets</span>
                                        </label>
                                    </div>
                                    
                                    <div class="task-item">
                                        <label class="task-checkbox">
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                            <span class="task-text">Update customer documentation</span>
                                        </label>
                                    </div>
                                    
                                    <div class="task-item">
                                        <label class="task-checkbox">
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                            <span class="task-text">Prepare weekly report</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="add-task">
                                    <input type="text" placeholder="Add new task...">
                                    <button class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <footer class="footer">
                <div>&copy; <?php echo date('Y'); ?> User Management System - Staff Portal</div>
                <div>Designed with <i class="fas fa-heart"></i> by Your Name</div>
            </footer>
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