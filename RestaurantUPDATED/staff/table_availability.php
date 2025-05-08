<?php
include '../config/database.php';

$reservation_id = $_GET['reservation_id'] ?? null;
if (!$reservation_id) {
    die("Invalid reservation.");
}

// Fetch reservation details
$res = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
$res->execute([$reservation_id]);
$reservation = $res->fetch();

if (!$reservation) {
    die("Reservation not found.");
}

$guests = $reservation['guests'] ?? 0;

// Fetch available tables that can accommodate the guests
$tables = $pdo->prepare("SELECT * FROM tables WHERE seats >= ? ORDER BY seats ASC");
$tables->execute([$guests]);
$tablesList = $tables->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Selection</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --success: #10b981;
            --success-hover: #059669;
            --warning: #f59e0b;
            --warning-hover: #d97706;
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
            padding: 2rem;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        
        .header {
            padding: 1.5rem 2rem;
            background-color: #eff6ff;
            border-bottom: 1px solid #dbeafe;
        }
        
        .reservation-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        
        .reservation-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            background-color: #dbeafe;
            color: #1e40af;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e40af;
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
            background-color: #eff6ff;
            color: #1e40af;
            font-weight: 600;
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #dbeafe;
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
        
        .button-reserve {
            background-color: var(--primary);
            color: white;
        }
        
        .button-reserve:hover {
            background-color: var(--primary-hover);
        }
        
        .button-disabled {
            background-color: var(--gray-300);
            color: var(--gray-600);
            cursor: not-allowed;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-available {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-occupied {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-reserved {
            background-color: #fff7ed;
            color: #9a3412;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Table Selection</h2>
            <div class="reservation-info">
                <span class="reservation-badge">Reservation #<?= htmlspecialchars($reservation_id) ?></span>
                <span class="reservation-badge"><?= htmlspecialchars($guests) ?> Guests</span>
                <span class="reservation-badge"><?= htmlspecialchars($reservation['name']) ?></span>
            </div>
        </div>
        
        <div class="table-container">
            <?php if (count($tablesList) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Table Number</th>
                            <th>Seats</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tablesList as $table): ?>
                        <tr>
                            <td><?= htmlspecialchars($table['table_number']) ?></td>
                            <td><?= htmlspecialchars($table['seats']) ?></td>
                            <td>
                                <?php 
                                $status = strtolower($table['status']);
                                $statusClass = 'status-occupied';
                                
                                if ($status === 'available') {
                                    $statusClass = 'status-available';
                                } elseif ($status === 'reserved') {
                                    $statusClass = 'status-reserved';
                                }
                                ?>
                                <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($table['status']) ?></span>
                            </td>
                            <td>
                                <?php if (strtolower($table['status']) === 'available'): ?>
                                    <form action="reserve_table.php" method="post">
                                        <input type="hidden" name="table_id" value="<?= htmlspecialchars($table['id']) ?>">
                                        <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation_id) ?>">
                                        <button type="submit" class="button button-reserve">Reserve</button>
                                    </form>
                                <?php else: ?>
                                    <button class="button button-disabled" disabled><?= htmlspecialchars($table['status']) ?></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 1rem; display: block; color: var(--gray-400);">
                        <rect width="18" height="12" x="3" y="4" rx="2" ry="2"></rect>
                        <line x1="2" x2="22" y1="20" y2="20"></line>
                    </svg>
                    <p style="font-weight: 500;">No suitable tables available for this reservation.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>