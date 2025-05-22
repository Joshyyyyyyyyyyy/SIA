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
<style>

.container {
    margin: 0 auto;
}

header {
    margin-bottom: 30px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

h1 {
    color: #ffffff;
}

/* Actions Bar */
.actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}

.search-box {
    display: flex;
    align-items: center;
}

.search-box input {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px 0 0 4px;
    width: 250px;
}

.search-box button {
    border-radius: 0 4px 4px 0;
    margin-left: -1px;
}

.filter select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
}

/* Table Styles */
.table-container {
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 8px 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table th {
    font-weight: 600;
}


.text-center {
    text-align: center;
}

/* Role Badge */
.badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.badge-staff {
    background-color: #3498db;
    color: white;
}

.badge-customer {
    background-color: #2ecc71;
    color: white;
}

/* Button Styles */
.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #3498db;
    color: white;
}

.btn-primary:hover {
    background-color: #2980b9;
}

.btn-secondary {
    background-color: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background-color: #7f8c8d;
}

.btn-danger {
    background-color: #e74c3c;
    color: white;
}

.btn-danger:hover {
    background-color: #c0392b;
}

.action-btn {
    padding: 20px 30px;
    margin-right: 5px;
    font-size: 14px;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.pagination button {
    padding: 8px 12px;
    margin: 0 5px;
    border: 1px solid #ddd;
    background-color: #fff;
    cursor: pointer;
    border-radius: 4px;
}

.pagination button.active {
    background-color: #3498db;
    color: white;
    border-color: #3498db;
}

.pagination button:hover:not(.active) {
    background-color: #f5f5f5;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    color: #1a1a1a;
    background-color: #fff;
    margin: 10% auto;
    width: 500px;
    max-width: 90%;
    border-radius: 5px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    animation: modalFadeIn 0.3s;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    font-size: 20px;
    margin: 0;
}

.close {
    font-size: 24px;
    cursor: pointer;
    color: #7f8c8d;
}

.modal-body {
    padding: 20px;
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .actions {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .search-box, .filter {
        width: 100%;
        margin-top: 10px;
    }
    
    .search-box input, .filter select {
        width: 100%;
    }
    
    .modal-content {
        margin: 20% auto;
    }
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
                    <li  class="active">
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
                      <li>
                        <a href="report.php">
                         <i class="fas fa-dollar-sign"></i>
                            <span>Income Report</span>
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
              <div class="container">
        <header>
            <h1>User Management</h1>
        </header>
        
        <div class="content">
            <div class="actions">
                <button id="addUserBtn" class="btn btn-primary">Add New User</button>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search users...">
                    <button id="searchBtn" class="btn">Search</button>
                </div>
                <div class="filter">
                    <select id="roleFilter">
                        <option value="all">All Roles</option>
                        <option value="staff">Staff</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
            </div>
            
            <div class="table-container">
                <table id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <!-- User data will be loaded here -->
                        <tr>
                            <td colspan="6" class="text-center">Loading users...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div id="pagination" class="pagination">
                <!-- Pagination controls will be added here -->
            </div>
        </div>
    </div>
    
    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New User</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="staff">Staff</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit User</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="form-group">
                        <label for="editName">Name</label>
                        <input type="text" id="editName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editPassword">Password (leave blank to keep current)</label>
                        <input type="password" id="editPassword" name="password">
                    </div>
                    <div class="form-group">
                        <label for="editRole">Role</label>
                        <select id="editRole" name="role" required>
                            <option value="staff">Staff</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirm Delete</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                <input type="hidden" id="deleteUserId">
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
    
    </div>
    
    <script>

        document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let currentPage = 1;
    const usersPerPage = 10;
    let totalUsers = 0;
    let currentFilter = 'all';
    let currentSearch = '';

    // DOM Elements
    const usersTableBody = document.getElementById('usersTableBody');
    const pagination = document.getElementById('pagination');
    const addUserBtn = document.getElementById('addUserBtn');
    const searchBtn = document.getElementById('searchBtn');
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    
    // Modals
    const addUserModal = document.getElementById('addUserModal');
    const editUserModal = document.getElementById('editUserModal');
    const deleteModal = document.getElementById('deleteModal');
    
    // Forms
    const addUserForm = document.getElementById('addUserForm');
    const editUserForm = document.getElementById('editUserForm');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // Initial load
    loadUsers();

    // Event Listeners
    addUserBtn.addEventListener('click', function() {
        addUserModal.style.display = 'block';
    });

    searchBtn.addEventListener('click', function() {
        currentSearch = searchInput.value.trim();
        currentPage = 1;
        loadUsers();
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            currentSearch = searchInput.value.trim();
            currentPage = 1;
            loadUsers();
        }
    });

    roleFilter.addEventListener('change', function() {
        currentFilter = this.value;
        currentPage = 1;
        loadUsers();
    });

    // Close modals
    document.querySelectorAll('.close, .close-btn').forEach(element => {
        element.addEventListener('click', function() {
            addUserModal.style.display = 'none';
            editUserModal.style.display = 'none';
            deleteModal.style.display = 'none';
        });
    });

    // Form submissions
    addUserForm.addEventListener('submit', function(e) {
        e.preventDefault();
        addUser();
    });

    editUserForm.addEventListener('submit', function(e) {
        e.preventDefault();
        updateUser();
    });

    confirmDeleteBtn.addEventListener('click', function() {
        const userId = document.getElementById('deleteUserId').value;
        deleteUser(userId);
    });

    // Functions
    function loadUsers() {
        usersTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Loading users...</td></tr>';
        
        // Build query string
        let queryParams = `page=${currentPage}&limit=${usersPerPage}`;
        if (currentSearch) {
            queryParams += `&search=${encodeURIComponent(currentSearch)}`;
        }
        if (currentFilter !== 'all') {
            queryParams += `&role=${encodeURIComponent(currentFilter)}`;
        }
        
        fetch(`../admin/users.php?${queryParams}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                totalUsers = data.total;
                renderUsers(data.users);
                renderPagination();
            })
            .catch(error => {
                console.error('Error fetching users:', error);
                usersTableBody.innerHTML = '<tr><td colspan="6" class="text-center">Error loading users. Please try again.</td></tr>';
            });
    }

    function renderUsers(users) {
        if (users.length === 0) {
            usersTableBody.innerHTML = '<tr><td colspan="6" class="text-center">No users found</td></tr>';
            return;
        }
        
        let html = '';
        users.forEach(user => {
            const createdDate = new Date(user.created_at).toLocaleDateString();
            
            html += `
                <tr>
                    <td>${user.id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td><span class="badge badge-${user.role}">${user.role}</span></td>
                    <td>${createdDate}</td>
                    <td>
                        <button class="btn btn-primary action-btn edit-btn" data-id="${user.id}">Edit</button>
                        <button class="btn btn-danger action-btn delete-btn" data-id="${user.id}">Delete</button>
                    </td>
                </tr>
            `;
        });
        
        usersTableBody.innerHTML = html;
        
        // Add event listeners to buttons
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                openEditModal(userId);
            });
        });
        
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                openDeleteModal(userId);
            });
        });
    }

    function renderPagination() {
        const totalPages = Math.ceil(totalUsers / usersPerPage);
        
        let html = '';
        
        // Previous button
        html += `<button ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">Previous</button>`;
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, startPage + 4);
        
        for (let i = startPage; i <= endPage; i++) {
            html += `<button class="${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
        }
        
        // Next button
        html += `<button ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">Next</button>`;
        
        pagination.innerHTML = html;
    }

    function openEditModal(userId) {
        fetch(`../admin/user.php?id=${userId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(user => {
                document.getElementById('editUserId').value = user.id;
                document.getElementById('editName').value = user.name;
                document.getElementById('editEmail').value = user.email;
                document.getElementById('editPassword').value = '';
                document.getElementById('editRole').value = user.role;
                
                editUserModal.style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching user details:', error);
                alert('Error loading user details. Please try again.');
            });
    }

    function openDeleteModal(userId) {
        document.getElementById('deleteUserId').value = userId;
        deleteModal.style.display = 'block';
    }

    function addUser() {
        const formData = new FormData(addUserForm);
        
        fetch('../admin/add_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                addUserModal.style.display = 'none';
                addUserForm.reset();
                loadUsers();
                alert('User added successfully!');
            } else {
                alert(data.message || 'Error adding user');
            }
        })
        .catch(error => {
            console.error('Error adding user:', error);
            alert('Error adding user. Please try again.');
        });
    }

    function updateUser() {
        const formData = new FormData(editUserForm);
        
        fetch('../admin/update_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                editUserModal.style.display = 'none';
                loadUsers();
                alert('User updated successfully!');
            } else {
                alert(data.message || 'Error updating user');
            }
        })
        .catch(error => {
            console.error('Error updating user:', error);
            alert('Error updating user. Please try again.');
        });
    }

    function deleteUser(userId) {
        fetch(`../admin/delete_user.php?id=${userId}`, {
            method: 'DELETE'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                deleteModal.style.display = 'none';
                loadUsers();
                alert('User deleted successfully!');
            } else {
                alert(data.message || 'Error deleting user');
            }
        })
        .catch(error => {
            console.error('Error deleting user:', error);
            alert('Error deleting user. Please try again.');
        });
    }

    // Make changePage function available globally
    window.changePage = function(page) {
        currentPage = page;
        loadUsers();
    };
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