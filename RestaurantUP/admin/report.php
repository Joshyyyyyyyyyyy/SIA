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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
</head>
<style>
    /* Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.business-reports-container {
    max-width: 1200px;
    color: #fff;
    line-height: 1.6;
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Header Styles */
.business-reports-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.business-reports-header h1 {
    color: #ffffff;
    font-size: 28px;
}

.business-reports-user-info {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 500;
}

/* Report Controls */
.business-reports-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.business-reports-type button {
    padding: 10px 20px;
    background-color: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
    color: white;
}

.business-reports-type button.active {
    background-color: white;
    color: #4e73df;
}

.business-reports-date-filter {
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
}

.business-reports-date-filter label {
    font-weight: 500;
}

.business-reports-date-filter select, 
.business-reports-date-filter input {
    padding: 8px 12px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
}

.business-reports-date-filter select option {
    background-color: #4e73df;
    color: white;
}

#businessReportsCustomDateRange {
    display: flex;
    align-items: center;
    gap: 10px;
}

#businessReportsApplyDateRange {
    padding: 8px 12px;
    background-color: white;
    color: #4e73df;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
}

#businessReportsExportBtn {
    padding: 10px 20px;
    background-color: white;
    color: #4e73df;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
}

#businessReportsExportBtn:hover {
    background-color: rgba(255, 255, 255, 0.9);
}

/* Summary Cards */
.business-reports-summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.business-reports-card {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
    border-left: 4px solid white;
    backdrop-filter: blur(5px);
}

.business-reports-card h3 {
    color: rgba(255, 255, 255, 0.8);
    font-size: 16px;
    margin-bottom: 10px;
    text-transform: uppercase;
    font-weight: 600;
}

.business-reports-amount {
    font-size: 28px;
    font-weight: 700;
    color: white;
}

.business-reports-count {
    font-size: 28px;
    font-weight: 700;
    color: white;
}

.business-reports-percentage {
    font-size: 28px;
    font-weight: 700;
    color: white;
}

/* Chart Container */
.business-reports-chart-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.business-reports-chart-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.business-reports-chart-card h3 {
    color: #4e73df;
    font-size: 18px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: 600;
}

.business-reports-chart-card canvas {
    width: 100% !important;
    height: 300px !important;
}

/* Table Styles */
.business-reports-table-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 30px;
    overflow-x: auto;
}

.business-reports-table-container h3 {
    color: #4e73df;
    font-size: 18px;
    margin-bottom: 15px;
    font-weight: 600;
}

.business-reports-table-container table {
    width: 100%;
    border-collapse: collapse;
}

.business-reports-table-container table th, 
.business-reports-table-container table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e3e6f0;
    color: #333;
}

.business-reports-table-container table th {
    background-color: #f8f9fc;
    font-weight: 600;
    color: #4e73df;
}

.business-reports-table-container table tbody tr:hover {
    background-color: #f8f9fc;
}

.business-reports-loading {
    text-align: center;
    color: #6e707e;
    padding: 20px;
}

/* Loading Spinner */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    margin-right: 10px;
    border: 3px solid rgba(78, 115, 223, 0.3);
    border-radius: 50%;
    border-top-color: #4e73df;
    animation: spin 1s ease-in-out infinite;
    vertical-align: middle;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive Styles */
@media (max-width: 768px) {
    .business-reports-controls {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .business-reports-chart-container {
        grid-template-columns: 1fr;
    }
    
    .business-reports-date-filter {
        flex-wrap: wrap;
    }
    
    #businessReportsCustomDateRange {
        flex-wrap: wrap;
    }
}

/* Button Hover Effects */
.business-reports-type button:hover:not(.active) {
    background-color: rgba(255, 255, 255, 0.3);
}

#businessReportsApplyDateRange:hover {
    background-color: rgba(255, 255, 255, 0.9);
}

/* Additional Color Adjustments */
.business-reports-card:nth-child(1) {
    border-left-color: #ffffff;
}

.business-reports-card:nth-child(2) {
    border-left-color: #1cc88a;
}

.business-reports-card:nth-child(3) {
    border-left-color: #36b9cc;
}

.business-reports-card:nth-child(4) {
    border-left-color: #f6c23e;
}

/* Focus States */
.business-reports-date-filter select:focus, 
.business-reports-date-filter input:focus {
    border-color: white;
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
}

/* Input placeholder color */
.business-reports-container ::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

/* For Webkit browsers */
.business-reports-container ::-webkit-calendar-picker-indicator {
    filter: invert(1);
}
</style>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-user-shield"></i> <span>Admin Panel</span>
            </div>
            <div class="sidebar-menu">
                <div class="menu-header">MAIN NAVIGATION</div>
                <ul>
                    <li>
                        <a href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> 
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                         <a href="usermanagements.php">
                            <i class="fas fa-users"></i> 
                            <span>Manage Users</span>
                            <span class="badge"><?php echo $totalUsers; ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="menu.php">
                            <i class="fas fa-utensils"></i> 
                            <span>Manage Menu</span>
                            <span class="badge"><?php echo $totalStaff; ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="report.php">
                         <i class="fas fa-dollar-sign"></i>
                            <span>Manage Menu</span>
                            <span class="badge"><?php echo $totalStaff; ?></span>
                        </a>
                    </li>
                    <li class="menu-header">SETTINGS</li>
                    
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
             <div class="business-reports-container">
        <header class="business-reports-header">
            <h1>Business Reports</h1>
            <div class="business-reports-user-info">
                <span>Admin</span>
            </div>
        </header>
        
        <div class="business-reports-controls">
            <div class="business-reports-type">
                <button id="businessReportsIncomeBtn" class="active">Income Report</button>
                <button id="businessReportsBusinessBtn">Business Report</button>
            </div>
            
            <div class="business-reports-date-filter">
                <label for="businessReportsDateRange">Date Range:</label>
                <select id="businessReportsDateRange">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="thisWeek">This Week</option>
                    <option value="lastWeek">Last Week</option>
                    <option value="thisMonth" selected>This Month</option>
                    <option value="lastMonth">Last Month</option>
                    <option value="thisYear">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
                
                <div id="businessReportsCustomDateRange" style="display: none;">
                    <input type="date" id="businessReportsStartDate">
                    <span>to</span>
                    <input type="date" id="businessReportsEndDate">
                    <button id="businessReportsApplyDateRange">Apply</button>
                </div>
            </div>
            
            <button id="businessReportsExportBtn">Export Report</button>
        </div>
        
        <!-- Income Report Section -->
        <div id="businessReportsIncomeReport" class="business-reports-section">
            <div class="business-reports-summary-cards">
                <div class="business-reports-card">
                    <h3>Total Revenue</h3>
                    <p class="business-reports-amount" id="businessReportsTotalRevenue">$0.00</p>
                </div>
                <div class="business-reports-card">
                    <h3>Average Per Reservation</h3>
                    <p class="business-reports-amount" id="businessReportsAvgReservation">$0.00</p>
                </div>
                <div class="business-reports-card">
                    <h3>Completed Reservations</h3>
                    <p class="business-reports-count" id="businessReportsCompletedCount">0</p>
                </div>
                <div class="business-reports-card">
                    <h3>Cancelled Revenue</h3>
                    <p class="business-reports-amount" id="businessReportsCancelledRevenue">$0.00</p>
                </div>
            </div>
            
            <div class="business-reports-chart-container">
                <div class="business-reports-chart-card">
                    <h3>Revenue Trend</h3>
                    <canvas id="businessReportsRevenueChart"></canvas>
                </div>
                <div class="business-reports-chart-card">
                    <h3>Revenue by Package</h3>
                    <canvas id="businessReportsPackageChart"></canvas>
                </div>
            </div>
            
            <div class="business-reports-chart-container">
                <div class="business-reports-chart-card">
                    <h3>Revenue by Time Slot</h3>
                    <canvas id="businessReportsTimeSlotChart"></canvas>
                </div>
                <div class="business-reports-chart-card">
                    <h3>Top Revenue Days</h3>
                    <canvas id="businessReportsTopDaysChart"></canvas>
                </div>
            </div>
            
            <div class="business-reports-table-container">
                <h3>Detailed Income Report</h3>
                <table id="businessReportsIncomeTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reservations</th>
                            <th>Guests</th>
                            <th>Basic Package</th>
                            <th>Standard Package</th>
                            <th>Premium Package</th>
                            <th>Deluxe Package</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded here -->
                        <tr>
                            <td colspan="8" class="business-reports-loading">Loading data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Business Report Section -->
        <div id="businessReportsBusinessReport" class="business-reports-section" style="display: none;">
            <div class="business-reports-summary-cards">
                <div class="business-reports-card">
                    <h3>Total Reservations</h3>
                    <p class="business-reports-count" id="businessReportsTotalReservations">0</p>
                </div>
                <div class="business-reports-card">
                    <h3>Completion Rate</h3>
                    <p class="business-reports-percentage" id="businessReportsCompletionRate">0%</p>
                </div>
                <div class="business-reports-card">
                    <h3>Total Guests</h3>
                    <p class="business-reports-count" id="businessReportsTotalGuests">0</p>
                </div>
                <div class="business-reports-card">
                    <h3>Avg. Guests Per Reservation</h3>
                    <p class="business-reports-count" id="businessReportsAvgGuests">0</p>
                </div>
            </div>
            
            <div class="business-reports-chart-container">
                <div class="business-reports-chart-card">
                    <h3>Reservation Status</h3>
                    <canvas id="businessReportsStatusChart"></canvas>
                </div>
                <div class="business-reports-chart-card">
                    <h3>Reservations by Time Slot</h3>
                    <canvas id="businessReportsTimeSlotReservationChart"></canvas>
                </div>
            </div>
            
            <div class="business-reports-chart-container">
                <div class="business-reports-chart-card">
                    <h3>Popular Food Packages</h3>
                    <canvas id="businessReportsFoodPackageChart"></canvas>
                </div>
                <div class="business-reports-chart-card">
                    <h3>Reservations Trend</h3>
                    <canvas id="businessReportsReservationTrendChart"></canvas>
                </div>
            </div>
            
            <div class="business-reports-table-container">
                <h3>Detailed Business Report</h3>
                <table id="businessReportsBusinessTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total Reservations</th>
                            <th>Completed</th>
                            <th>Cancelled</th>
                            <th>Pending</th>
                            <th>Morning</th>
                            <th>Afternoon</th>
                            <th>Evening</th>
                            <th>Total Guests</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded here -->
                        <tr>
                            <td colspan="9" class="business-reports-loading">Loading data...</td>
                        </tr>
                    </tbody>
                </table>
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
        /**
 * Business Reports Module
 * Encapsulated to avoid conflicts with other JavaScript code
 */
const BusinessReports = (function() {
    // Private variables
    let _revenueChart = null;
    let _packageChart = null;
    let _timeSlotChart = null;
    let _topDaysChart = null;
    let _statusChart = null;
    let _timeSlotReservationChart = null;
    let _foodPackageChart = null;
    let _reservationTrendChart = null;
    
    // DOM Elements cache
    let _elements = {};
    
    // Chart configuration
    const _chartColors = {
        blue: 'rgba(78, 115, 223, 1)',
        blueTransparent: 'rgba(78, 115, 223, 0.2)',
        green: 'rgba(28, 200, 138, 0.7)',
        greenBorder: 'rgba(28, 200, 138, 1)',
        cyan: 'rgba(54, 185, 204, 0.7)',
        cyanBorder: 'rgba(54, 185, 204, 1)',
        purple: 'rgba(155, 89, 182, 0.7)',
        purpleBorder: 'rgba(155, 89, 182, 1)',
        yellow: 'rgba(246, 194, 62, 0.7)',
        yellowBorder: 'rgba(246, 194, 62, 1)',
        red: 'rgba(231, 74, 59, 0.7)',
        redBorder: 'rgba(231, 74, 59, 1)'
    };
    
    /**
     * Initialize the module
     */
    function init() {
        // Cache DOM elements
        cacheElements();
        
        // Set default dates for custom range
        setDefaultDates();
        
        // Set up event listeners
        setupEventListeners();
        
        // Initial load
        loadIncomeReport();
    }
    
    /**
     * Cache DOM elements for better performance
     */
    function cacheElements() {
        _elements = {
            incomeReportBtn: document.getElementById('businessReportsIncomeBtn'),
            businessReportBtn: document.getElementById('businessReportsBusinessBtn'),
            incomeReport: document.getElementById('businessReportsIncomeReport'),
            businessReport: document.getElementById('businessReportsBusinessReport'),
            dateRangeSelect: document.getElementById('businessReportsDateRange'),
            customDateRange: document.getElementById('businessReportsCustomDateRange'),
            startDateInput: document.getElementById('businessReportsStartDate'),
            endDateInput: document.getElementById('businessReportsEndDate'),
            applyDateRangeBtn: document.getElementById('businessReportsApplyDateRange'),
            exportReportBtn: document.getElementById('businessReportsExportBtn'),
            
            // Summary elements - Income
            totalRevenue: document.getElementById('businessReportsTotalRevenue'),
            avgReservation: document.getElementById('businessReportsAvgReservation'),
            completedCount: document.getElementById('businessReportsCompletedCount'),
            cancelledRevenue: document.getElementById('businessReportsCancelledRevenue'),
            
            // Summary elements - Business
            totalReservations: document.getElementById('businessReportsTotalReservations'),
            completionRate: document.getElementById('businessReportsCompletionRate'),
            totalGuests: document.getElementById('businessReportsTotalGuests'),
            avgGuests: document.getElementById('businessReportsAvgGuests'),
            
            // Chart canvases
            revenueChartCanvas: document.getElementById('businessReportsRevenueChart'),
            packageChartCanvas: document.getElementById('businessReportsPackageChart'),
            timeSlotChartCanvas: document.getElementById('businessReportsTimeSlotChart'),
            topDaysChartCanvas: document.getElementById('businessReportsTopDaysChart'),
            statusChartCanvas: document.getElementById('businessReportsStatusChart'),
            timeSlotReservationChartCanvas: document.getElementById('businessReportsTimeSlotReservationChart'),
            foodPackageChartCanvas: document.getElementById('businessReportsFoodPackageChart'),
            reservationTrendChartCanvas: document.getElementById('businessReportsReservationTrendChart'),
            
            // Tables
            incomeTableBody: document.querySelector('#businessReportsIncomeTable tbody'),
            businessTableBody: document.querySelector('#businessReportsBusinessTable tbody')
        };
    }
    
    /**
     * Set default dates for custom date range
     */
    function setDefaultDates() {
        const today = new Date();
        const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        
        _elements.startDateInput.valueAsDate = firstDayOfMonth;
        _elements.endDateInput.valueAsDate = today;
    }
    
    /**
     * Set up event listeners
     */
    function setupEventListeners() {
        // Tab switching
        _elements.incomeReportBtn.addEventListener('click', function() {
            _elements.incomeReportBtn.classList.add('active');
            _elements.businessReportBtn.classList.remove('active');
            _elements.incomeReport.style.display = 'block';
            _elements.businessReport.style.display = 'none';
            loadIncomeReport();
        });
        
        _elements.businessReportBtn.addEventListener('click', function() {
            _elements.businessReportBtn.classList.add('active');
            _elements.incomeReportBtn.classList.remove('active');
            _elements.businessReport.style.display = 'block';
            _elements.incomeReport.style.display = 'none';
            loadBusinessReport();
        });
        
        // Date range selection
        _elements.dateRangeSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                _elements.customDateRange.style.display = 'flex';
            } else {
                _elements.customDateRange.style.display = 'none';
                loadReports();
            }
        });
        
        _elements.applyDateRangeBtn.addEventListener('click', loadReports);
        
        // Export report
        _elements.exportReportBtn.addEventListener('click', exportReport);
    }
    
    /**
     * Load appropriate report based on current tab
     */
    function loadReports() {
        if (_elements.incomeReport.style.display !== 'none') {
            loadIncomeReport();
        } else {
            loadBusinessReport();
        }
    }
    
    /**
     * Load income report data and update UI
     */
    function loadIncomeReport() {
        const dateRange = getDateRange();
        
        // Show loading state
        updateLoadingState(_elements.incomeTableBody, true, 8);
        
        // Fetch income report data
        fetch(`../admin/income_Report.php?start=${dateRange.start}&end=${dateRange.end}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                updateIncomeSummary(data.summary);
                updateIncomeCharts(data);
                updateIncomeTable(data.details);
            })
            .catch(error => {
                console.error('Error fetching income report:', error);
                
                // Show error message in table
                _elements.incomeTableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="business-reports-loading">
                            Error loading income report: ${error.message}. Please try again.
                        </td>
                    </tr>
                `;
                
                // Reset summary values
                _elements.totalRevenue.textContent = '$0.00';
                _elements.avgReservation.textContent = '$0.00';
                _elements.completedCount.textContent = '0';
                _elements.cancelledRevenue.textContent = '$0.00';
                
                // Clear charts
                clearIncomeCharts();
            });
    }
    
    /**
     * Load business report data and update UI
     */
    function loadBusinessReport() {
        const dateRange = getDateRange();
        
        // Show loading state
        updateLoadingState(_elements.businessTableBody, true, 9);
        
        // Fetch business report data
        fetch(`../admin/business_report.php?start=${dateRange.start}&end=${dateRange.end}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                updateBusinessSummary(data.summary);
                updateBusinessCharts(data);
                updateBusinessTable(data.details);
            })
            .catch(error => {
                console.error('Error fetching business report:', error);
                
                // Show error message in table
                _elements.businessTableBody.innerHTML = `
                    <tr>
                        <td colspan="9" class="business-reports-loading">
                            Error loading business report: ${error.message}. Please try again.
                        </td>
                    </tr>
                `;
                
                // Reset summary values
                _elements.totalReservations.textContent = '0';
                _elements.completionRate.textContent = '0%';
                _elements.totalGuests.textContent = '0';
                _elements.avgGuests.textContent = '0';
                
                // Clear charts
                clearBusinessCharts();
            });
    }
    
    /**
     * Update loading state for tables
     */
    function updateLoadingState(tableBody, isLoading, colSpan) {
        if (isLoading) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${colSpan}" class="business-reports-loading">
                        <div class="loading-spinner"></div> Loading data...
                    </td>
                </tr>
            `;
        }
    }
    
    /**
     * Clear all income charts
     */
    function clearIncomeCharts() {
        if (_revenueChart) {
            _revenueChart.destroy();
            _revenueChart = null;
        }
        
        if (_packageChart) {
            _packageChart.destroy();
            _packageChart = null;
        }
        
        if (_timeSlotChart) {
            _timeSlotChart.destroy();
            _timeSlotChart = null;
        }
        
        if (_topDaysChart) {
            _topDaysChart.destroy();
            _topDaysChart = null;
        }
    }
    
    /**
     * Clear all business charts
     */
    function clearBusinessCharts() {
        if (_statusChart) {
            _statusChart.destroy();
            _statusChart = null;
        }
        
        if (_timeSlotReservationChart) {
            _timeSlotReservationChart.destroy();
            _timeSlotReservationChart = null;
        }
        
        if (_foodPackageChart) {
            _foodPackageChart.destroy();
            _foodPackageChart = null;
        }
        
        if (_reservationTrendChart) {
            _reservationTrendChart.destroy();
            _reservationTrendChart = null;
        }
    }
    
    /**
     * Get date range based on selection
     */
    function getDateRange() {
        const selectedRange = _elements.dateRangeSelect.value;
        let startDate, endDate;
        const today = new Date();
        
        switch (selectedRange) {
            case 'today':
                startDate = formatDate(today);
                endDate = formatDate(today);
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                startDate = formatDate(yesterday);
                endDate = formatDate(yesterday);
                break;
            case 'thisWeek':
                const firstDayOfWeek = new Date(today);
                firstDayOfWeek.setDate(today.getDate() - today.getDay());
                startDate = formatDate(firstDayOfWeek);
                endDate = formatDate(today);
                break;
            case 'lastWeek':
                const lastWeekEnd = new Date(today);
                lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                const lastWeekStart = new Date(lastWeekEnd);
                lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                startDate = formatDate(lastWeekStart);
                endDate = formatDate(lastWeekEnd);
                break;
            case 'thisMonth':
                startDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                endDate = formatDate(today);
                break;
            case 'lastMonth':
                startDate = formatDate(new Date(today.getFullYear(), today.getMonth() - 1, 1));
                endDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 0));
                break;
            case 'thisYear':
                startDate = formatDate(new Date(today.getFullYear(), 0, 1));
                endDate = formatDate(today);
                break;
            case 'custom':
                startDate = _elements.startDateInput.value;
                endDate = _elements.endDateInput.value;
                break;
            default:
                startDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                endDate = formatDate(today);
        }
        
        return { start: startDate, end: endDate };
    }
    
    /**
     * Format date as YYYY-MM-DD
     */
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    /**
     * Update income summary cards
     */
    function updateIncomeSummary(summary) {
        _elements.totalRevenue.textContent = formatCurrency(summary.total_revenue);
        _elements.avgReservation.textContent = formatCurrency(summary.avg_per_reservation);
        _elements.completedCount.textContent = summary.completed_count;
        _elements.cancelledRevenue.textContent = formatCurrency(summary.cancelled_revenue);
    }
    
    /**
     * Update income charts
     */
    function updateIncomeCharts(data) {
        // Revenue Trend Chart
        if (_revenueChart) {
            _revenueChart.destroy();
        }
        
        if (_elements.revenueChartCanvas) {
            const ctx1 = _elements.revenueChartCanvas.getContext('2d');
            
            _revenueChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: data.revenue_trend.map(item => item.date),
                    datasets: [{
                        label: 'Revenue',
                        data: data.revenue_trend.map(item => item.revenue),
                        backgroundColor: _chartColors.blueTransparent,
                        borderColor: _chartColors.blue,
                        borderWidth: 2,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Revenue by Package Chart
        if (_packageChart) {
            _packageChart.destroy();
        }
        
        if (_elements.packageChartCanvas) {
            const ctx2 = _elements.packageChartCanvas.getContext('2d');
            
            _packageChart = new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: Object.keys(data.revenue_by_package),
                    datasets: [{
                        data: Object.values(data.revenue_by_package),
                        backgroundColor: [
                            _chartColors.green,
                            _chartColors.blue,
                            _chartColors.purple,
                            _chartColors.yellow
                        ],
                        borderColor: [
                            _chartColors.greenBorder,
                            _chartColors.blue,
                            _chartColors.purpleBorder,
                            _chartColors.yellowBorder
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    return label + ': ' + formatCurrency(value);
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Revenue by Time Slot Chart
        if (_timeSlotChart) {
            _timeSlotChart.destroy();
        }
        
        if (_elements.timeSlotChartCanvas) {
            const ctx3 = _elements.timeSlotChartCanvas.getContext('2d');
            
            _timeSlotChart = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: Object.keys(data.revenue_by_time_slot),
                    datasets: [{
                        label: 'Revenue',
                        data: Object.values(data.revenue_by_time_slot),
                        backgroundColor: [
                            _chartColors.yellow,
                            _chartColors.cyan,
                            _chartColors.purple
                        ],
                        borderColor: [
                            _chartColors.yellowBorder,
                            _chartColors.cyanBorder,
                            _chartColors.purpleBorder
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Top Revenue Days Chart
        if (_topDaysChart) {
            _topDaysChart.destroy();
        }
        
        if (_elements.topDaysChartCanvas) {
            const ctx4 = _elements.topDaysChartCanvas.getContext('2d');
            
            _topDaysChart = new Chart(ctx4, {
                type: 'bar',
                data: {
                    labels: data.top_revenue_days.map(item => item.day),
                    datasets: [{
                        label: 'Revenue',
                        data: data.top_revenue_days.map(item => item.revenue),
                        backgroundColor: _chartColors.blueTransparent,
                        borderColor: _chartColors.blue,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    },
                    indexAxis: 'y'
                }
            });
        }
    }
    
    /**
     * Update income table
     */
    function updateIncomeTable(details) {
        if (!_elements.incomeTableBody) return;
        
        if (details.length === 0) {
            _elements.incomeTableBody.innerHTML = '<tr><td colspan="8" class="business-reports-loading">No data available for the selected period</td></tr>';
            return;
        }
        
        let html = '';
        details.forEach(row => {
            html += `
                <tr>
                    <td>${row.date}</td>
                    <td>${row.reservations}</td>
                    <td>${row.guests}</td>
                    <td>${formatCurrency(row.basic_package)}</td>
                    <td>${formatCurrency(row.standard_package)}</td>
                    <td>${formatCurrency(row.premium_package)}</td>
                    <td>${formatCurrency(row.deluxe_package)}</td>
                    <td>${formatCurrency(row.total_revenue)}</td>
                </tr>
            `;
        });
        
        _elements.incomeTableBody.innerHTML = html;
    }
    
    /**
     * Update business summary cards
     */
    function updateBusinessSummary(summary) {
        _elements.totalReservations.textContent = summary.total_reservations;
        _elements.completionRate.textContent = summary.completion_rate + '%';
        _elements.totalGuests.textContent = summary.total_guests;
        _elements.avgGuests.textContent = summary.avg_guests;
    }
    
    /**
     * Update business charts
     */
    function updateBusinessCharts(data) {
        // Reservation Status Chart
        if (_statusChart) {
            _statusChart.destroy();
        }
        
        if (_elements.statusChartCanvas) {
            const ctx1 = _elements.statusChartCanvas.getContext('2d');
            
            _statusChart = new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data.status_counts),
                    datasets: [{
                        data: Object.values(data.status_counts),
                        backgroundColor: [
                            _chartColors.green,
                            _chartColors.red,
                            _chartColors.blue,
                            _chartColors.yellow
                        ],
                        borderColor: [
                            _chartColors.greenBorder,
                            _chartColors.redBorder,
                            _chartColors.blue,
                            _chartColors.yellowBorder
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
        
        // Time Slot Reservation Chart
        if (_timeSlotReservationChart) {
            _timeSlotReservationChart.destroy();
        }
        
        if (_elements.timeSlotReservationChartCanvas) {
            const ctx2 = _elements.timeSlotReservationChartCanvas.getContext('2d');
            
            _timeSlotReservationChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: Object.keys(data.time_slot_counts),
                    datasets: [{
                        label: 'Reservations',
                        data: Object.values(data.time_slot_counts),
                        backgroundColor: [
                            _chartColors.yellow,
                            _chartColors.cyan,
                            _chartColors.purple
                        ],
                        borderColor: [
                            _chartColors.yellowBorder,
                            _chartColors.cyanBorder,
                            _chartColors.purpleBorder
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Food Package Chart
        if (_foodPackageChart) {
            _foodPackageChart.destroy();
        }
        
        if (_elements.foodPackageChartCanvas) {
            const ctx3 = _elements.foodPackageChartCanvas.getContext('2d');
            
            _foodPackageChart = new Chart(ctx3, {
                type: 'pie',
                data: {
                    labels: Object.keys(data.package_counts),
                    datasets: [{
                        data: Object.values(data.package_counts),
                        backgroundColor: [
                            _chartColors.green,
                            _chartColors.blue,
                            _chartColors.purple,
                            _chartColors.yellow
                        ],
                        borderColor: [
                            _chartColors.greenBorder,
                            _chartColors.blue,
                            _chartColors.purpleBorder,
                            _chartColors.yellowBorder
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
        
        // Reservation Trend Chart
        if (_reservationTrendChart) {
            _reservationTrendChart.destroy();
        }
        
        if (_elements.reservationTrendChartCanvas) {
            const ctx4 = _elements.reservationTrendChartCanvas.getContext('2d');
            
            _reservationTrendChart = new Chart(ctx4, {
                type: 'line',
                data: {
                    labels: data.reservation_trend.map(item => item.date),
                    datasets: [{
                        label: 'Reservations',
                        data: data.reservation_trend.map(item => item.count),
                        backgroundColor: _chartColors.blueTransparent,
                        borderColor: _chartColors.blue,
                        borderWidth: 2,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
    
    /**
     * Update business table
     */
    function updateBusinessTable(details) {
        if (!_elements.businessTableBody) return;
        
        if (details.length === 0) {
            _elements.businessTableBody.innerHTML = '<tr><td colspan="9" class="business-reports-loading">No data available for the selected period</td></tr>';
            return;
        }
        
        let html = '';
        details.forEach(row => {
            html += `
                <tr>
                    <td>${row.date}</td>
                    <td>${row.total_reservations}</td>
                    <td>${row.completed}</td>
                    <td>${row.cancelled}</td>
                    <td>${row.pending}</td>
                    <td>${row.morning}</td>
                    <td>${row.afternoon}</td>
                    <td>${row.evening}</td>
                    <td>${row.total_guests}</td>
                </tr>
            `;
        });
        
        _elements.businessTableBody.innerHTML = html;
    }
    
    /**
     * Format currency
     */
    function formatCurrency(value) {
        return '$' + parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    /**
     * Export report
     */
    function exportReport() {
        const dateRange = getDateRange();
        const reportType = _elements.incomeReport.style.display !== 'none' ? 'income' : 'business';
        
        window.location.href = `../admin/export_report.php?type=${reportType}&start=${dateRange.start}&end=${dateRange.end}`;
    }
    
    // Public API
    return {
        init: init,
        loadIncomeReport: loadIncomeReport,
        loadBusinessReport: loadBusinessReport
    };
})();

// Initialize the module when the DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    BusinessReports.init();
});
    </script>
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