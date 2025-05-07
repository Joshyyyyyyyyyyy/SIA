<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error'] = "You don't have permission to access this page";
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

// Get user statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$totalCustomers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'staff'");
$totalStaff = $stmt->fetch()['total'];

// Get recent users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
$recentUsers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | User Management System</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-user-shield"></i> <span>Admin Panel</span>
            </div>
            <div class="sidebar-menu">
                <div class="menu-header">MAIN NAVIGATION</div>
                <ul>
                    <li class="active">
                        <a href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> 
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="users.php">
                            <i class="fas fa-users"></i> 
                            <span>Manage Users</span>
                            <span class="badge"><?php echo $totalUsers; ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="staff.php">
                            <i class="fas fa-user-tie"></i> 
                            <span>Manage Staff</span>
                            <span class="badge"><?php echo $totalStaff; ?></span>
                        </a>
                    </li>
                    <li class="menu-header">SETTINGS</li>
                    <li>
                        <a href="settings.php">
                            <i class="fas fa-cog"></i> 
                            <span>System Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="profile.php">
                            <i class="fas fa-user-cog"></i> 
                            <span>Admin Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="../logout.php" class="logout-link">
                            <i class="fas fa-sign-out-alt"></i> 
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="main-content">
            <div class="topbar">
                <div class="toggle-menu">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <div class="user-info">
                    <div class="notifications">
                        <i class="far fa-bell"></i>
                        <span class="notification-count">3</span>
                    </div>
                    <div class="messages">
                        <i class="far fa-envelope"></i>
                        <span class="message-count">7</span>
                    </div>
                    <div class="profile-dropdown">
                        <img src="../img/admin-avatar.png" alt="Admin">
                        <span><?php echo $_SESSION['user_name']; ?></span>
                        <i class="fas fa-chevron-down"></i>
                        <div class="dropdown-menu">
                            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="content">
                <div class="page-header">
                    <h1>Admin Dashboard</h1>
                    <nav class="breadcrumb">
                        <a href="dashboard.php">Home</a> / Dashboard
                    </nav>
                </div>
                
                <div class="welcome-card">
                    <div class="welcome-text">
                        <h2>Welcome back, <?php echo $_SESSION['user_name']; ?>!</h2>
                        <p>Here's what's happening with your system today.</p>
                    </div>
                    <div class="date-time">
                        <div class="date" id="current-date">Loading date...</div>
                        <div class="time" id="current-time">Loading time...</div>
                    </div>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value"><?php echo $totalUsers; ?></div>
                            <div class="stat-title">Total Users</div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar" style="width: 75%"></div>
                        </div>
                        <div class="stat-link">
                            <a href="users.php">View Details <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    
                    <div class="stat-card success">
                        <div class="stat-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value"><?php echo $totalCustomers; ?></div>
                            <div class="stat-title">Customers</div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar" style="width: 65%"></div>
                        </div>
                        <div class="stat-link">
                            <a href="customers.php">View Details <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    
                    <div class="stat-card warning">
                        <div class="stat-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value"><?php echo $totalStaff; ?></div>
                            <div class="stat-title">Staff Members</div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar" style="width: 45%"></div>
                        </div>
                        <div class="stat-link">
                            <a href="staff.php">View Details <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    
                    <div class="stat-card danger">
                        <div class="stat-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">1</div>
                            <div class="stat-title">Admins</div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <div class="stat-link">
                            <a href="admins.php">View Details <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-8">
                        <div class="card recent-users">
                            <div class="card-header">
                                <h3><i class="fas fa-user-clock"></i> Recent Users</h3>
                                <div class="card-actions">
                                    <button class="refresh-btn"><i class="fas fa-sync-alt"></i></button>
                                    <div class="dropdown">
                                        <button class="dropdown-btn"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="dropdown-content">
                                            <a href="#"><i class="fas fa-download"></i> Export</a>
                                            <a href="#"><i class="fas fa-print"></i> Print</a>
                                            <a href="#"><i class="fas fa-file-pdf"></i> Save as PDF</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Created At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentUsers as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td>
                                                    <div class="user-info-cell">
                                                        <div class="user-avatar">
                                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                                        </div>
                                                        <div class="user-name"><?php echo $user['name']; ?></div>
                                                    </div>
                                                </td>
                                                <td><?php echo $user['email']; ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $user['role'] === 'admin' ? 'primary' : ($user['role'] === 'staff' ? 'warning' : 'success'); ?>">
                                                        <?php echo ucfirst($user['role']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="action-btn view-btn" title="View"><i class="fas fa-eye"></i></button>
                                                        <button class="action-btn edit-btn" title="Edit"><i class="fas fa-edit"></i></button>
                                                        <button class="action-btn delete-btn" title="Delete"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="users.php" class="view-all">View All Users <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-4">
                        <div class="card user-distribution">
                            <div class="card-header">
                                <h3><i class="fas fa-chart-pie"></i> User Distribution</h3>
                            </div>
                            <div class="card-body">
                                <div class="donut-chart-container">
                                    <div class="donut-chart">
                                        <div class="slice slice-1" style="--percentage: <?php echo ($totalCustomers / $totalUsers) * 100; ?>%; --color: #36b9cc;"></div>
                                        <div class="slice slice-2" style="--percentage: <?php echo ($totalStaff / $totalUsers) * 100; ?>%; --color: #f6c23e;"></div>
                                        <div class="slice slice-3" style="--percentage: <?php echo (1 / $totalUsers) * 100; ?>%; --color: #4e73df;"></div>
                                        <div class="chart-center"></div>
                                    </div>
                                </div>
                                <div class="chart-legend">
                                    <div class="legend-item">
                                        <div class="legend-color" style="background-color: #36b9cc;"></div>
                                        <div class="legend-text">Customers (<?php echo $totalCustomers; ?>)</div>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color" style="background-color: #f6c23e;"></div>
                                        <div class="legend-text">Staff (<?php echo $totalStaff; ?>)</div>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color" style="background-color: #4e73df;"></div>
                                        <div class="legend-text">Admins (1)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card quick-actions">
                            <div class="card-header">
                                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                            </div>
                            <div class="card-body">
                                <div class="action-buttons">
                                    <a href="add-user.php" class="quick-action-btn">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Add User</span>
                                    </a>
                                    <a href="add-staff.php" class="quick-action-btn">
                                        <i class="fas fa-user-tie"></i>
                                        <span>Add Staff</span>
                                    </a>
                                    <a href="reports.php" class="quick-action-btn">
                                        <i class="fas fa-chart-bar"></i>
                                        <span>Reports</span>
                                    </a>
                                    <a href="settings.php" class="quick-action-btn">
                                        <i class="fas fa-cog"></i>
                                        <span>Settings</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <footer class="footer">
                <div>&copy; <?php echo date('Y'); ?> User Management System - Admin Panel</div>
                <div>Designed with <i class="fas fa-heart"></i> by Your Name</div>
            </footer>
        </div>
    </div>
    
    <script>
        // Toggle sidebar
        document.querySelector('.toggle-menu').addEventListener('click', function() {
            document.querySelector('.dashboard').classList.toggle('sidebar-collapsed');
        });
        
        // Profile dropdown
        document.querySelector('.profile-dropdown').addEventListener('click', function() {
            this.classList.toggle('active');
        });
        
        // Update date and time
        function updateDateTime() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions);
        }
        
        updateDateTime();
        setInterval(updateDateTime, 1000);
        
        // Add animation to stat cards
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