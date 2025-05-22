<?php
require_once '../config/database.php';
session_start();

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">Invalid request</div>';
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? AND status = 'completed'");
    $stmt->execute([$id]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        echo '<div class="alert alert-danger">Receipt not available for this reservation</div>';
        exit;
    }
?>

<div id="receipt-printable" class="receipt">
    <div class="receipt-header">
        <div class="logo">
            <h2>Diwata Pares</h2>
        </div>
        <div class="receipt-title">
            <h3>RECEIPT</h3>
            <p>Receipt #: <?php echo $reservation['reservation_code']; ?></p>
            <p>Date: <?php echo date('F d, Y', strtotime($reservation['checkout_at'])); ?></p>
            <p>Time: <?php echo date('h:i A', strtotime($reservation['checkout_at'])); ?></p>
        </div>
    </div>
    
    <div class="receipt-customer">
        <h4>Customer Information</h4>
        <p>Name: <?php echo $reservation['customer_name']; ?></p>
        <p>Phone: <?php echo $reservation['phone']; ?></p>
        <p>Email: <?php echo $reservation['email']; ?></p>
    </div>
    
    <div class="receipt-details">
        <h4>Reservation Details</h4>
        <p>Date: <?php echo date('F d, Y', strtotime($reservation['reservation_date'])); ?></p>
        <p>Time Slot: <?php echo getTimeSlotName($reservation['time_slot']); ?></p>
        <p>Number of Guests: <?php echo $reservation['guests']; ?></p>
        <p>Table: <?php echo getTableName($reservation['table_id']); ?></p>
    </div>
    
    <div class="receipt-items">
        <h4>Items</h4>
        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo getFoodPackageName($reservation['food_package']); ?></td>
                    <td><?php echo formatCurrency(getFoodPackagePrice($reservation['food_package'])); ?></td>
                </tr>
                <?php if ($reservation['theme']): ?>
                <tr>
                    <td><?php echo getThemeName($reservation['theme']); ?> Theme</td>
                    <td><?php echo formatCurrency(getThemePrice($reservation['theme'])); ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th><?php echo formatCurrency($reservation['total_price']); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="receipt-footer">
        <p>Thank you for dining with us!</p>
        <p>We hope to see you again soon.</p>
        <p>For inquiries, please call (123) 999-9999 or email DiwataPares@gmail.com</p>
    </div>
</div>

<?php
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
}
?>