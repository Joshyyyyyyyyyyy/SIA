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
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['delete'])) {
        $id = $_POST['delete'];
        $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);
        $successMessage = "Menu item deleted successfully!";
    } else {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $badge = $_POST['badge'];
        $image_url = '';

        // Handle file upload
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = time() . '_' . basename($_FILES['image_file']['name']);
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetPath)) {
                $image_url = $targetPath;
            }
        }

        if (!empty($_POST['id'])) {
            $id = $_POST['id'];

            // Use existing image if none uploaded
            if (empty($image_url)) {
                $stmt = $pdo->prepare("SELECT image_url FROM menu_items WHERE id = ?");
                $stmt->execute([$id]);
                $existing = $stmt->fetch();
                $image_url = $existing['image_url'] ?? '';
            }

            $sql = "UPDATE menu_items SET name=?, description=?, price=?, category=?, image_url=?, badge=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $category, $image_url, $badge, $id]);
            $successMessage = "Menu item updated successfully!";
        } else {
            // Insert new
            $sql = "INSERT INTO menu_items (name, description, price, category, image_url, badge) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $category, $image_url, $badge]);
            $successMessage = "Menu item added successfully!";
        }
    }
}

// Fetch all menu items
$stmt = $pdo->query("SELECT * FROM menu_items ORDER BY category");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filtering
$categories = [];
foreach ($result as $item) {
    if (!in_array($item['category'], $categories)) {
        $categories[] = $item['category'];
    }
}
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
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --danger-light: #fee2e2;
            --success: #10b981;
            --success-hover: #059669;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.5;
        }
        .topbar{
            color: white;
        }
        .container {
            margin: 0 auto;
        }
        
        .header {
            background-color: #151521;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            margin: 0;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success {
            background-color: var(--success-light);
            color: var(--success-hover);
            border-left: 4px solid var(--success);
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .card-header {
            padding: 1.25rem 1.5rem;
            background-color: #151521            ;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }
        
        .card-body {
            padding: 1.5rem;
            background-color: #151521;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-group.full-width {
            grid-column: span 2;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: white;
        }
        
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            background-color: var(--gray-50);
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 0.875rem;
            transition: border-color 0.15s ease-in-out;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .file-input-wrapper {
            position: relative;
        }
        
        .file-input-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background-color: var(--gray-100);
            border: 1px dashed var(--gray-400);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
        }
        
        .file-input-label:hover {
            background-color: var(--gray-200);
        }
        
        input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .image-preview {
            margin-top: 1rem;
            display: none;
            align-items: center;
            gap: 1rem;
        }
        
        .image-preview img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 4px;
            border: 1px solid var(--gray-300);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
        }
        
        .btn-outline:hover {
            background-color: var(--gray-100);
            border-color: var(--gray-400);
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: var(--danger-hover);
        }
        
        .btn-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .table-filter {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .table-filter select {
            width: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        
        .table th {
            background-color: var(--gray-100);
            color: var(--gray-700);
            font-weight: 600;
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid var(--gray-300);
        }
        
        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--gray-200);
            color: var(--gray-700);
            vertical-align: middle;
        }
        
        .table tr:hover {
            background-color: var(--gray-50);
        }
        
        .table .actions {
            gap: 0.5rem;
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        
        .badge-starters {
            background-color: var(--primary-light);
            color: var(--primary);
        }
        
        .badge-mains {
            background-color: var(--success-light);
            color: var(--success);
        }
        
        .badge-desserts {
            background-color: var(--warning-light);
            color: var(--warning);
        }
        
        .badge-drinks {
            background-color: #e0f2fe;
            color: #0284c7;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .modal-header {
            margin-bottom: 1rem;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
        }
        
        .modal-body {
            margin-bottom: 1.5rem;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }
        
        .item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .header {
                padding: 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .table th:nth-child(3),
            .table td:nth-child(3) {
                display: none;
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
                    <li>
                        <a href="users.php">
                            <i class="fas fa-users"></i> 
                            <span>Manage Users</span>
                            <span class="badge"><?php echo $totalUsers; ?></span>
                        </a>
                    </li>
                    <li class="active">
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
        <div class="header">
            <h1>Menu Management</h1>
        </div>
        
        <?php if (isset($successMessage)): ?>
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <?= $successMessage ?>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title" id="form-title">Add New Menu Item</h2>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="menu-form">
                    <input type="hidden" name="id" id="item-id">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Item Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Price ($)</label>
                            <input type="number" id="price" step="0.01" name="price" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                <option value="starters">Starters</option>
                                <option value="mains">Main Courses</option>
                                <option value="desserts">Desserts</option>
                                <option value="drinks">Drinks</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="badge">Badge (optional)</label>
                            <input type="text" id="badge" name="badge" placeholder="e.g., New, Popular, Spicy">
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Item Image</label>
                            <div class="file-input-wrapper">
                                <label class="file-input-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                                        <circle cx="9" cy="9" r="2"></circle>
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                                    </svg>
                                    <span id="file-label">Choose an image file</span>
                                    <input type="file" id="image_file" name="image_file" accept="image/*" onchange="previewImage(this)">
                                </label>
                            </div>
                            <div class="image-preview" id="image-preview-container">
                                <img id="image-preview" src="/placeholder.svg" alt="Preview">
                                <span id="image-name"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="resetForm()">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Save Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Menu Items</h2>
            </div>
            <div class="card-body">
                <div class="table-controls">
                    <div class="table-filter">
                        <label for="filter-category">Filter by:</label>
                        <select id="filter-category" onchange="filterItems()">
                            <option value="all">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>"><?= ucfirst(htmlspecialchars($cat)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="search-box">
                        <input type="text" id="search-input" placeholder="Search items..." onkeyup="filterItems()">
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="table" id="menu-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $row): ?>
                            <tr data-category="<?= htmlspecialchars($row['category']) ?>">
                                <td>
                                    <?php if (!empty($row['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="item-image">
                                    <?php else: ?>
                                        <div class="item-image" style="background-color: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #9ca3af;">
                                                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                                                <circle cx="9" cy="9" r="2"></circle>
                                                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars(substr($row['description'], 0, 50)) . (strlen($row['description']) > 50 ? '...' : '') ?></td>
                                <td><span class="badge badge-<?= htmlspecialchars($row['category']) ?>"><?= htmlspecialchars($row['category']) ?></span></td>
                                <td>$<?= number_format($row['price'], 2) ?></td>
                                <td class="actions">
                                    <button class="btn btn-sm btn-outline" onclick='editItem(<?= json_encode($row) ?>)'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                            <path d="m15 5 4 4"></path>
                                        </svg>
                                        Edit
                                    </button>

                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name']) ?>')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal" id="delete-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Deletion</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="delete-item-name"></strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <form method="POST" id="delete-form">
                    <input type="hidden" name="delete" id="delete-id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>

            <footer class="footer">
                <div>&copy; <?php echo date('Y'); ?> User Management System - Admin Panel</div>
                <div>Designed with <i class="fas fa-heart"></i> by Joshua Suruiz</div>
            </footer>
        </div>
    </div>
    <script>
    function editItem(item) {
        document.querySelector('#form-title').textContent = 'Edit Menu Item';
        document.querySelector('input[name="id"]').value = item.id;
        document.querySelector('input[name="name"]').value = item.name;
        document.querySelector('textarea[name="description"]').value = item.description;
        document.querySelector('input[name="price"]').value = item.price;
        document.querySelector('select[name="category"]').value = item.category;
        document.querySelector('input[name="badge"]').value = item.badge || '';
        
        // Show image preview if available
        if (item.image_url) {
            document.getElementById('image-preview').src = item.image_url;
            document.getElementById('image-name').textContent = 'Current image';
            document.getElementById('image-preview-container').style.display = 'flex';
            document.getElementById('file-label').textContent = 'Change image (optional)';
        }
        
        // Scroll to form
        document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
    }
    
    function resetForm() {
        document.querySelector('#form-title').textContent = 'Add New Menu Item';
        document.getElementById('menu-form').reset();
        document.querySelector('input[name="id"]').value = '';
        document.getElementById('image-preview-container').style.display = 'none';
        document.getElementById('file-label').textContent = 'Choose an image file';
    }
    
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-name').textContent = file.name;
                document.getElementById('image-preview-container').style.display = 'flex';
                document.getElementById('file-label').textContent = file.name;
            }
            
            reader.readAsDataURL(file);
        }
    }
    
    function confirmDelete(id, name) {
        document.getElementById('delete-id').value = id;
        document.getElementById('delete-item-name').textContent = name;
        document.getElementById('delete-modal').style.display = 'flex';
    }
    
    function closeModal() {
        document.getElementById('delete-modal').style.display = 'none';
    }
    
    function filterItems() {
        const category = document.getElementById('filter-category').value;
        const searchText = document.getElementById('search-input').value.toLowerCase();
        const rows = document.querySelectorAll('#menu-table tbody tr');
        
        rows.forEach(row => {
            const rowCategory = row.getAttribute('data-category');
            const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const description = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            
            const categoryMatch = category === 'all' || rowCategory === category;
            const textMatch = name.includes(searchText) || description.includes(searchText);
            
            row.style.display = categoryMatch && textMatch ? '' : 'none';
        });
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('delete-modal');
        if (event.target === modal) {
            closeModal();
        }
    }
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