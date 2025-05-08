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

$tables = $pdo->query("SELECT * FROM tables ORDER BY table_number ASC")->fetchAll();
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
            --available: #10b981;
            --available-bg: #ecfdf5;
            --available-hover: #059669;
            --occupied: #ef4444;
            --occupied-bg: #fef2f2;
            --occupied-hover: #dc2626;
            --reserved: #f59e0b;
            --reserved-bg: #fffbeb;
            --reserved-hover: #d97706;
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
        }
        
        .header {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
        }
        
        .refresh-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .refresh-button:hover {
            background-color: var(--primary-hover);
        }
        
        .legend {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-700);
        }
        
        .legend-color {
            width: 1rem;
            height: 1rem;
            border-radius: 4px;
        }
        
        .legend-available {
            background-color: var(--available);
        }
        
        .legend-occupied {
            background-color: var(--occupied);
        }
        
        .legend-reserved {
            background-color: var(--reserved);
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
        }
        
        .table-box {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            text-align: center;
            border-top: 5px solid transparent;
            transition: all 0.25s ease-in-out;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }
        
        .table-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .status-available {
            border-top-color: var(--available);
        }
        
        .status-available::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background-color: var(--available-bg);
            opacity: 0.5;
            z-index: 0;
        }
        
        .status-occupied {
            border-top-color: var(--occupied);
        }
        
        .status-occupied::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background-color: var(--occupied-bg);
            opacity: 0.5;
            z-index: 0;
        }
        
        .status-reserved {
            border-top-color: var(--reserved);
        }
        
        .status-reserved::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background-color: var(--reserved-bg);
            opacity: 0.5;
            z-index: 0;
        }
        
        .table-content {
            position: relative;
            z-index: 1;
        }
        
        .table-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-radius: 50%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .table-number {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: var(--gray-800);
        }
        
        .seats {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }
        
        .status-label {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 500;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-label-available {
            background-color: var(--available);
            color: white;
        }
        
        .status-label-occupied {
            background-color: var(--occupied);
            color: white;
        }
        
        .status-label-reserved {
            background-color: var(--reserved);
            color: white;
        }
        
        .empty-state {
            grid-column: 1 / -1;
            padding: 3rem 1rem;
            text-align: center;
            color: var(--gray-600);
            font-size: 1rem;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 1rem;
            }
            
            .table-box {
                padding: 1rem;
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
                <li class="menu-item ">
                    <a href="reservationlist.php">
                    <i class="fas fa-calendar-check"></i>
                        <span>Reservation List</span>
                        <span class="badge badge-warning">3</span>
                    </a>
                </li>
                <li class="menu-item active">
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
            <h2>Table Availability</h2>
            <button class="refresh-button" onclick="window.location.reload()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 2v6h-6"></path>
                    <path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path>
                    <path d="M3 22v-6h6"></path>
                    <path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path>
                </svg>
                Refresh
            </button>
        </div>
        
        <div class="legend">
            <div class="legend-item">
                <div class="legend-color legend-available"></div>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <div class="legend-color legend-occupied"></div>
                <span>Occupied</span>
            </div>
            <div class="legend-item">
                <div class="legend-color legend-reserved"></div>
                <span>Reserved</span>
            </div>
        </div>
        
        <div class="grid" id="table-grid">
            <?php if (count($tables) > 0): ?>
                <?php foreach ($tables as $table): ?>
                    <?php 
                    $status = strtolower($table['status']);
                    if ($status == 'reserve') $status = 'reserved'; // Normalize status name
                    ?>
                    <div class="table-box status-<?= $status ?>">
                        <div class="table-content">
                            <div class="table-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="12" x="3" y="4" rx="2" ry="2"></rect>
                                    <line x1="3" x2="21" y1="10" y2="10"></line>
                                </svg>
                            </div>
                            <div class="table-number">Table #<?= htmlspecialchars($table['table_number']) ?></div>
                            <div class="seats">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <?= htmlspecialchars($table['seats']) ?> Seats
                            </div>
                            <div class="status-label status-label-<?= $status ?>"><?= htmlspecialchars($table['status']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 1rem; display: block; color: var(--gray-400);">
                        <rect width="18" height="12" x="3" y="4" rx="2" ry="2"></rect>
                        <line x1="2" x2="22" y1="20" y2="20"></line>
                    </svg>
                    <p style="font-weight: 500;">No tables available in the system.</p>
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