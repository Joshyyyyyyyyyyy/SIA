<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Reports</title>
    <link rel="stylesheet" href="css/reports.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Business Reports</h1>
            <div class="user-info">
                <span>Admin</span>
            </div>
        </header>
        
        <div class="report-controls">
            <div class="report-type">
                <button id="incomeReportBtn" class="active">Income Report</button>
                <button id="businessReportBtn">Business Report</button>
            </div>
            
            <div class="date-filter">
                <label for="dateRange">Date Range:</label>
                <select id="dateRange">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="thisWeek">This Week</option>
                    <option value="lastWeek">Last Week</option>
                    <option value="thisMonth" selected>This Month</option>
                    <option value="lastMonth">Last Month</option>
                    <option value="thisYear">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
                
                <div id="customDateRange" style="display: none;">
                    <input type="date" id="startDate">
                    <span>to</span>
                    <input type="date" id="endDate">
                    <button id="applyDateRange">Apply</button>
                </div>
            </div>
            
            <button id="exportReportBtn">Export Report</button>
        </div>
        
        <!-- Income Report Section -->
        <div id="incomeReport" class="report-section">
            <div class="summary-cards">
                <div class="card">
                    <h3>Total Revenue</h3>
                    <p class="amount" id="totalRevenue">$0.00</p>
                </div>
                <div class="card">
                    <h3>Average Per Reservation</h3>
                    <p class="amount" id="avgReservation">$0.00</p>
                </div>
                <div class="card">
                    <h3>Completed Reservations</h3>
                    <p class="count" id="completedCount">0</p>
                </div>
                <div class="card">
                    <h3>Cancelled Revenue</h3>
                    <p class="amount" id="cancelledRevenue">$0.00</p>
                </div>
            </div>
            
            <div class="chart-container">
                <div class="chart-card">
                    <h3>Revenue Trend</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Revenue by Package</h3>
                    <canvas id="packageChart"></canvas>
                </div>
            </div>
            
            <div class="chart-container">
                <div class="chart-card">
                    <h3>Revenue by Time Slot</h3>
                    <canvas id="timeSlotChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Top Revenue Days</h3>
                    <canvas id="topDaysChart"></canvas>
                </div>
            </div>
            
            <div class="table-container">
                <h3>Detailed Income Report</h3>
                <table id="incomeTable">
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
                            <td colspan="8" class="loading">Loading data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Business Report Section -->
        <div id="businessReport" class="report-section" style="display: none;">
            <div class="summary-cards">
                <div class="card">
                    <h3>Total Reservations</h3>
                    <p class="count" id="totalReservations">0</p>
                </div>
                <div class="card">
                    <h3>Completion Rate</h3>
                    <p class="percentage" id="completionRate">0%</p>
                </div>
                <div class="card">
                    <h3>Total Guests</h3>
                    <p class="count" id="totalGuests">0</p>
                </div>
                <div class="card">
                    <h3>Avg. Guests Per Reservation</h3>
                    <p class="count" id="avgGuests">0</p>
                </div>
            </div>
            
            <div class="chart-container">
                <div class="chart-card">
                    <h3>Reservation Status</h3>
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Reservations by Time Slot</h3>
                    <canvas id="timeSlotReservationChart"></canvas>
                </div>
            </div>
            
            <div class="chart-container">
                <div class="chart-card">
                    <h3>Popular Food Packages</h3>
                    <canvas id="foodPackageChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Reservations Trend</h3>
                    <canvas id="reservationTrendChart"></canvas>
                </div>
            </div>
            
            <div class="table-container">
                <h3>Detailed Business Report</h3>
                <table id="businessTable">
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
                            <td colspan="9" class="loading">Loading data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <style>
 /* Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f8f9fc;
    color: #333;
    line-height: 1.6;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Header Styles */
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e3e6f0;
}

header h1 {
    color: #2e3951;
    font-size: 28px;
}

.user-info {
    background-color: #4e73df;
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 500;
}

/* Report Controls */
.report-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.report-type button {
    padding: 10px 20px;
    background-color: #f0f0f0;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
}

.report-type button.active {
    background-color: #4e73df;
    color: white;
}

.date-filter {
    display: flex;
    align-items: center;
    gap: 10px;
}

.date-filter label {
    font-weight: 500;
}

.date-filter select, 
.date-filter input {
    padding: 8px 12px;
    border: 1px solid #d1d3e2;
    border-radius: 4px;
    background-color: white;
}

#customDateRange {
    display: flex;
    align-items: center;
    gap: 10px;
}

#applyDateRange {
    padding: 8px 12px;
    background-color: #4e73df;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

#exportReportBtn {
    padding: 10px 20px;
    background-color: #36b9cc;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
}

#exportReportBtn:hover {
    background-color: #2c9faf;
}

/* Summary Cards */
.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(78, 115, 223, 0.1);
    padding: 20px;
    text-align: center;
    border-left: 4px solid #4e73df;
}

.card h3 {
    color: #6e707e;
    font-size: 16px;
    margin-bottom: 10px;
    text-transform: uppercase;
    font-weight: 600;
}

.amount {
    font-size: 28px;
    font-weight: 700;
    color: #2e3951;
}

.count {
    font-size: 28px;
    font-weight: 700;
    color: #4e73df;
}

.percentage {
    font-size: 28px;
    font-weight: 700;
    color: #1cc88a;
}

/* Chart Container */
.chart-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.chart-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(78, 115, 223, 0.1);
    padding: 20px;
}

.chart-card h3 {
    color: #2e3951;
    font-size: 18px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: 600;
}

canvas {
    width: 100% !important;
    height: 300px !important;
}

/* Table Styles */
.table-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(78, 115, 223, 0.1);
    padding: 20px;
    margin-bottom: 30px;
    overflow-x: auto;
}

.table-container h3 {
    color: #2e3951;
    font-size: 18px;
    margin-bottom: 15px;
    font-weight: 600;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e3e6f0;
}

table th {
    background-color: #f8f9fc;
    font-weight: 600;
    color: #2e3951;
}

table tbody tr:hover {
    background-color: #f8f9fc;
}

.loading {
    text-align: center;
    color: #6e707e;
    padding: 20px;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .report-controls {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .chart-container {
        grid-template-columns: 1fr;
    }
    
    .date-filter {
        flex-wrap: wrap;
    }
    
    #customDateRange {
        flex-wrap: wrap;
    }
}

/* Button Hover Effects */
.report-type button:hover:not(.active) {
    background-color: #eaecf4;
}

#applyDateRange:hover {
    background-color: #3a5ccc;
}

/* Additional Color Adjustments */
.card:nth-child(1) {
    border-left-color: #4e73df;
}

.card:nth-child(2) {
    border-left-color: #1cc88a;
}

.card:nth-child(3) {
    border-left-color: #36b9cc;
}

.card:nth-child(4) {
    border-left-color: #f6c23e;
}

/* Focus States */
.date-filter select:focus, 
.date-filter input:focus {
    border-color: #4e73df;
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

/* Table Pagination (if added later) */
.pagination button {
    padding: 8px 12px;
    margin: 0 5px;
    border: 1px solid #d1d3e2;
    background-color: #fff;
    cursor: pointer;
    border-radius: 4px;
}

.pagination button.active {
    background-color: #4e73df;
    color: white;
    border-color: #4e73df;
}

.pagination button:hover:not(.active) {
    background-color: #eaecf4;
}
    </style>
    <script>document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const incomeReportBtn = document.getElementById('incomeReportBtn');
    const businessReportBtn = document.getElementById('businessReportBtn');
    const incomeReport = document.getElementById('incomeReport');
    const businessReport = document.getElementById('businessReport');
    const dateRangeSelect = document.getElementById('dateRange');
    const customDateRange = document.getElementById('customDateRange');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const applyDateRangeBtn = document.getElementById('applyDateRange');
    const exportReportBtn = document.getElementById('exportReportBtn');
    
    // Set default dates for custom range
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    
    startDateInput.valueAsDate = firstDayOfMonth;
    endDateInput.valueAsDate = today;
    
    // Initialize charts
    let revenueChart, packageChart, timeSlotChart, topDaysChart;
    let statusChart, timeSlotReservationChart, foodPackageChart, reservationTrendChart;
    
    // Event Listeners
    incomeReportBtn.addEventListener('click', function() {
        incomeReportBtn.classList.add('active');
        businessReportBtn.classList.remove('active');
        incomeReport.style.display = 'block';
        businessReport.style.display = 'none';
        loadIncomeReport();
    });
    
    businessReportBtn.addEventListener('click', function() {
        businessReportBtn.classList.add('active');
        incomeReportBtn.classList.remove('active');
        businessReport.style.display = 'block';
        incomeReport.style.display = 'none';
        loadBusinessReport();
    });
    
    dateRangeSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.style.display = 'flex';
        } else {
            customDateRange.style.display = 'none';
            loadReports();
        }
    });
    
    applyDateRangeBtn.addEventListener('click', loadReports);
    
    exportReportBtn.addEventListener('click', exportReport);
    
    // Initial load
    loadIncomeReport();
    
    // Functions
    function loadReports() {
        if (incomeReport.style.display !== 'none') {
            loadIncomeReport();
        } else {
            loadBusinessReport();
        }
    }
    
    function loadIncomeReport() {
        const dateRange = getDateRange();
        
        // Fetch income report data
        fetch(`../admin/income_report.php?start=${dateRange.start}&end=${dateRange.end}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
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
                alert('Error loading income report. Please try again.');
            });
    }
    
    function loadBusinessReport() {
        const dateRange = getDateRange();
        
        // Fetch business report data
        fetch(`../admin/business_report.php?start=${dateRange.start}&end=${dateRange.end}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
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
                alert('Error loading business report. Please try again.');
            });
    }
    
    function getDateRange() {
        const selectedRange = dateRangeSelect.value;
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
                startDate = startDateInput.value;
                endDate = endDateInput.value;
                break;
            default:
                startDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                endDate = formatDate(today);
        }
        
        return { start: startDate, end: endDate };
    }
    
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    function updateIncomeSummary(summary) {
        document.getElementById('totalRevenue').textContent = formatCurrency(summary.total_revenue);
        document.getElementById('avgReservation').textContent = formatCurrency(summary.avg_per_reservation);
        document.getElementById('completedCount').textContent = summary.completed_count;
        document.getElementById('cancelledRevenue').textContent = formatCurrency(summary.cancelled_revenue);
    }
    
    function updateIncomeCharts(data) {
        // Revenue Trend Chart
        const ctx1 = document.getElementById('revenueChart').getContext('2d');
        if (revenueChart) {
            revenueChart.destroy();
        }
        
        revenueChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: data.revenue_trend.map(item => item.date),
                datasets: [{
                    label: 'Revenue',
                    data: data.revenue_trend.map(item => item.revenue),
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderColor: 'rgba(52, 152, 219, 1)',
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
        
        // Revenue by Package Chart
        const ctx2 = document.getElementById('packageChart').getContext('2d');
        if (packageChart) {
            packageChart.destroy();
        }
        
        packageChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: Object.keys(data.revenue_by_package),
                datasets: [{
                    data: Object.values(data.revenue_by_package),
                    backgroundColor: [
                        'rgba(46, 204, 113, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(155, 89, 182, 0.7)',
                        'rgba(241, 196, 15, 0.7)'
                    ],
                    borderColor: [
                        'rgba(46, 204, 113, 1)',
                        'rgba(52, 152, 219, 1)',
                        'rgba(155, 89, 182, 1)',
                        'rgba(241, 196, 15, 1)'
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
        
        // Revenue by Time Slot Chart
        const ctx3 = document.getElementById('timeSlotChart').getContext('2d');
        if (timeSlotChart) {
            timeSlotChart.destroy();
        }
        
        timeSlotChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: Object.keys(data.revenue_by_time_slot),
                datasets: [{
                    label: 'Revenue',
                    data: Object.values(data.revenue_by_time_slot),
                    backgroundColor: [
                        'rgba(241, 196, 15, 0.7)',
                        'rgba(230, 126, 34, 0.7)',
                        'rgba(52, 73, 94, 0.7)'
                    ],
                    borderColor: [
                        'rgba(241, 196, 15, 1)',
                        'rgba(230, 126, 34, 1)',
                        'rgba(52, 73, 94, 1)'
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
        
        // Top Revenue Days Chart
        const ctx4 = document.getElementById('topDaysChart').getContext('2d');
        if (topDaysChart) {
            topDaysChart.destroy();
        }
        
        topDaysChart = new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: data.top_revenue_days.map(item => item.day),
                datasets: [{
                    label: 'Revenue',
                    data: data.top_revenue_days.map(item => item.revenue),
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: 'rgba(52, 152, 219, 1)',
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
    
    function updateIncomeTable(details) {
        const tableBody = document.querySelector('#incomeTable tbody');
        
        if (details.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="8" class="loading">No data available for the selected period</td></tr>';
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
        
        tableBody.innerHTML = html;
    }
    
    function updateBusinessSummary(summary) {
        document.getElementById('totalReservations').textContent = summary.total_reservations;
        document.getElementById('completionRate').textContent = summary.completion_rate + '%';
        document.getElementById('totalGuests').textContent = summary.total_guests;
        document.getElementById('avgGuests').textContent = summary.avg_guests;
    }
    
    function updateBusinessCharts(data) {
        // Reservation Status Chart
        const ctx1 = document.getElementById('statusChart').getContext('2d');
        if (statusChart) {
            statusChart.destroy();
        }
        
        statusChart = new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: Object.keys(data.status_counts),
                datasets: [{
                    data: Object.values(data.status_counts),
                    backgroundColor: [
                        'rgba(46, 204, 113, 0.7)',  // completed
                        'rgba(231, 76, 60, 0.7)',   // cancelled
                        'rgba(52, 152, 219, 0.7)',  // approved
                        'rgba(241, 196, 15, 0.7)'   // pending
                    ],
                    borderColor: [
                        'rgba(46, 204, 113, 1)',
                        'rgba(231, 76, 60, 1)',
                        'rgba(52, 152, 219, 1)',
                        'rgba(241, 196, 15, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Time Slot Reservation Chart
        const ctx2 = document.getElementById('timeSlotReservationChart').getContext('2d');
        if (timeSlotReservationChart) {
            timeSlotReservationChart.destroy();
        }
        
        timeSlotReservationChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: Object.keys(data.time_slot_counts),
                datasets: [{
                    label: 'Reservations',
                    data: Object.values(data.time_slot_counts),
                    backgroundColor: [
                        'rgba(241, 196, 15, 0.7)',
                        'rgba(230, 126, 34, 0.7)',
                        'rgba(52, 73, 94, 0.7)'
                    ],
                    borderColor: [
                        'rgba(241, 196, 15, 1)',
                        'rgba(230, 126, 34, 1)',
                        'rgba(52, 73, 94, 1)'
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
        
        // Food Package Chart
        const ctx3 = document.getElementById('foodPackageChart').getContext('2d');
        if (foodPackageChart) {
            foodPackageChart.destroy();
        }
        
        foodPackageChart = new Chart(ctx3, {
            type: 'pie',
            data: {
                labels: Object.keys(data.package_counts),
                datasets: [{
                    data: Object.values(data.package_counts),
                    backgroundColor: [
                        'rgba(46, 204, 113, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(155, 89, 182, 0.7)',
                        'rgba(241, 196, 15, 0.7)'
                    ],
                    borderColor: [
                        'rgba(46, 204, 113, 1)',
                        'rgba(52, 152, 219, 1)',
                        'rgba(155, 89, 182, 1)',
                        'rgba(241, 196, 15, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Reservation Trend Chart
        const ctx4 = document.getElementById('reservationTrendChart').getContext('2d');
        if (reservationTrendChart) {
            reservationTrendChart.destroy();
        }
        
        reservationTrendChart = new Chart(ctx4, {
            type: 'line',
            data: {
                labels: data.reservation_trend.map(item => item.date),
                datasets: [{
                    label: 'Reservations',
                    data: data.reservation_trend.map(item => item.count),
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderColor: 'rgba(52, 152, 219, 1)',
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
    
    function updateBusinessTable(details) {
        const tableBody = document.querySelector('#businessTable tbody');
        
        if (details.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="9" class="loading">No data available for the selected period</td></tr>';
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
        
        tableBody.innerHTML = html;
    }
    
    function formatCurrency(value) {
        return '$' + parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    function exportReport() {
        const dateRange = getDateRange();
        const reportType = incomeReport.style.display !== 'none' ? 'income' : 'business';
        
        window.location.href = `../admin/export_report.php?type=${reportType}&start=${dateRange.start}&end=${dateRange.end}`;
    }
});</script>
</body>
</html>