<?php
include '../config/database.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Update reservation status in the database
    $stmt = $pdo->prepare("UPDATE reservations SET status = :status WHERE id = :id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Redirect to table_availability.php after approval
    if ($status === 'Approved') {
        header("Location: table_availability.php?reservation_id=" . $id);
        exit();
    }

    // You can add more redirection logic if needed (e.g., redirect to another page after cancellation)
    header("Location: reservations.php");
    exit();
}
?>
