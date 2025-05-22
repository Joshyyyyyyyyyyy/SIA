<?php
require_once '../config/database.php';
session_start();


if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">Invalid request</div>';
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
    $stmt->execute([$id]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        echo '<div class="alert alert-danger">Reservation not found</div>';
        exit;
    }
    
    // Get staff names
    $staff_names = [];
    if ($reservation['approved_by']) {
        $stmt = $pdo->prepare("SELECT name FROM staff WHERE id = ?");
        $stmt->execute([$reservation['approved_by']]);
        $staff_names['approved_by'] = $stmt->fetchColumn();
    }
    
    if ($reservation['table_assigned_by']) {
        $stmt = $pdo->prepare("SELECT name FROM staff WHERE id = ?");
        $stmt->execute([$reservation['table_assigned_by']]);
        $staff_names['table_assigned_by'] = $stmt->fetchColumn();
    }
    
    if ($reservation['checkout_by']) {
        $stmt = $pdo->prepare("SELECT name FROM staff WHERE id = ?");
        $stmt->execute([$reservation['checkout_by']]);
        $staff_names['checkout_by'] = $stmt->fetchColumn();
    }
    
    if ($reservation['cancelled_by']) {
        $stmt = $pdo->prepare("SELECT name FROM staff WHERE id = ?");
        $stmt->execute([$reservation['cancelled_by']]);
        $staff_names['cancelled_by'] = $stmt->fetchColumn();
    }
?>

<div class="reservation-details">
    <div class="detail-section">
        <h4>Reservation Information</h4>
        <div class="detail-row">
            <div class="detail-label">Reservation Code:</div>
            <div class="detail-value"><?php echo $reservation['reservation_code']; ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Status:</div>
            <div class="detail-value">
                <span class="badge badge-<?php echo getStatusClass($reservation['status']); ?>">
                    <?php echo getStatusName($reservation['status']); ?>
                </span>
            </div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Date:</div>
            <div class="detail-value"><?php echo date('F d, Y', strtotime($reservation['reservation_date'])); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Time Slot:</div>
            <div class="detail-value"><?php echo getTimeSlotName($reservation['time_slot']); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Number of Guests:</div>
            <div class="detail-value"><?php echo $reservation['guests']; ?></div>
        </div>
        <?php if ($reservation['table_id']): ?>
        <div class="detail-row">
            <div class="detail-label">Table:</div>
            <div class="detail-value"><?php echo getTableName($reservation['table_id']); ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="detail-section">
        <h4>Customer Information</h4>
        <div class="detail-row">
            <div class="detail-label">Name:</div>
            <div class="detail-value"><?php echo $reservation['customer_name']; ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Email:</div>
            <div class="detail-value"><?php echo $reservation['email']; ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Phone:</div>
            <div class="detail-value"><?php echo $reservation['phone']; ?></div>
        </div>
    </div>
    
    <div class="detail-section">
        <h4>Package & Theme</h4>
        <div class="detail-row">
            <div class="detail-label">Food Package:</div>
            <div class="detail-value"><?php echo getFoodPackageName($reservation['food_package']); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Package Price:</div>
            <div class="detail-value"><?php echo formatCurrency(getFoodPackagePrice($reservation['food_package'])); ?></div>
        </div>
        <?php if ($reservation['theme']): ?>
        <div class="detail-row">
            <div class="detail-label">Theme:</div>
            <div class="detail-value"><?php echo getThemeName($reservation['theme']); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Theme Price:</div>
            <div class="detail-value"><?php echo formatCurrency(getThemePrice($reservation['theme'])); ?></div>
        </div>
        <?php endif; ?>
        <div class="detail-row">
            <div class="detail-label">Total Price:</div>
            <div class="detail-value"><strong><?php echo formatCurrency($reservation['total_price']); ?></strong></div>
        </div>
    </div>
    
    <?php if ($reservation['special_request']): ?>
    <div class="detail-section">
        <h4>Special Request</h4>
        <div class="detail-value special-request">
            <?php echo nl2br($reservation['special_request']); ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="detail-section">
        <h4>Reservation Timeline</h4>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div class="timeline-content">
                    <h5>Created</h5>
                    <p><?php echo date('F d, Y h:i A', strtotime($reservation['created_at'])); ?></p>
                </div>
            </div>
            
            <?php if ($reservation['status'] === 'approved' || $reservation['status'] === 'completed'): ?>
            <div class="timeline-item">
                <div class="timeline-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="timeline-content">
                    <h5>Approved</h5>
                    <p><?php echo date('F d, Y h:i A', strtotime($reservation['approved_at'])); ?></p>
                    <p>By: <?php echo $staff_names['approved_by'] ?? 'Unknown'; ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($reservation['table_id']): ?>
            <div class="timeline-item">
                <div class="timeline-icon">
                    <i class="fas fa-chair"></i>
                </div>
                <div class="timeline-content">
                    <h5>Table Assigned</h5>
                    <p><?php echo date('F d, Y h:i A', strtotime($reservation['table_assigned_at'])); ?></p>
                    <p>By: <?php echo $staff_names['table_assigned_by'] ?? 'Unknown'; ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($reservation['status'] === 'completed'): ?>
            <div class="timeline-item">
                <div class="timeline-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="timeline-content">
                    <h5>Checked Out</h5>
                    <p><?php echo date('F d, Y h:i A', strtotime($reservation['checkout_at'])); ?></p>
                    <p>By: <?php echo $staff_names['checkout_by'] ?? 'Unknown'; ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($reservation['status'] === 'cancelled'): ?>
            <div class="timeline-item">
                <div class="timeline-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="timeline-content">
                    <h5>Cancelled</h5>
                    <p><?php echo date('F d, Y h:i A', strtotime($reservation['cancelled_at'])); ?></p>
                    <p>By: <?php echo $staff_names['cancelled_by'] ?? 'Unknown'; ?></p>
                    <?php if ($reservation['cancel_reason']): ?>
                    <p>Reason: <?php echo $reservation['cancel_reason']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
}
?>