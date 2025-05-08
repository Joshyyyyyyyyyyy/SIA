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

// Get only approved reservations (you can add Checked Out if needed)
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE status = 'Approved' ORDER BY date ASC, time ASC");
$stmt->execute();
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | User Management System</title>
    <link rel="stylesheet" href="../css/staff.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <style>
 :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --success: #10b981;
            --success-hover: #059669;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9fafb;
            color: var(--gray-800);
           
        }
        
        .container {
            
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        
        .header {
            padding: 1.5rem 2rem;
            background-color: #f0fdf4;
            border-bottom: 1px solid #dcfce7;
        }
        
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #166534;
        }
        
        .table-container {
            overflow-x: auto;
            padding: 1rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        
        th {
            background-color: #f0fdf4;
            color: #166534;
            font-weight: 600;
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #dcfce7;
        }
        
        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--gray-200);
            color: var(--gray-700);
        }
        
        tr:hover {
            background-color: var(--gray-100);
        }
        
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: var(--gray-600);
            font-size: 1rem;
        }
        
        .button {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }
        
        .button-checkout {
            background-color: #0891b2;
            color: white;
        }
        
        .button-checkout:hover {
            background-color: #0e7490;
        }
        
        .button-cancel {
            background-color: var(--danger);
            color: white;
            margin-left: 0.5rem;
        }
        
        .button-cancel:hover {
            background-color: var(--danger-hover);
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #d1fae5;
            color: #065f46;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .header {
                padding: 1rem;
            }
            
            th, td {
                padding: 0.5rem;
            }
            
            .responsive-hide {
                display: none;
            }
        }
    </style>
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
                </li>
                <li class="menu-item">
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
                    <a href="tables.php">
                        <i class="fas fa-table"></i>
                        <span>Tables</span>
                        <span class="badge badge-warning">3</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="support.php">
                        <i class="fas fa-headset"></i>
                        <span>Support Tickets</span>
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
            <div class="container">
        <div class="header">
            <h2>Approved Reservations</h2>
        </div>
        
        <div class="table-container">
            <?php if (count($reservations) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th class="responsive-hide">Theme</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reservations as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['phone'] ?></td>
                            <td><?= $row['date'] ?></td>
                            <td><?= $row['time'] . " " . $row['time_period'] ?></td>
                            <td><?= $row['guests'] ?></td>
                            <td class="responsive-hide"><?= $row['theme'] ?></td>
                            <td><span class="badge"><?= $row['status'] ?></span></td>
                            <td class="actions">
                                <!-- Check Out button -->
                                <form action="update_status.php" method="post">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="status" value="Checked Out">
                                    <button type="submit" class="button button-checkout">Check Out</button>
                                </form>

                                <!-- Cancel option if needed -->
                                <form action="update_status.php" method="post">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="status" value="Cancelled">
                                    <button type="submit" class="button button-cancel">Cancel</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 1rem; display: block; color: #16a34a;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <p style="font-weight: 500;">No approved reservations at this time.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
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