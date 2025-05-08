<?php
include '../config/database.php';

$table_id = $_POST['table_id'];
$reservation_id = $_POST['reservation_id'];

// Step 1: Update table status
$update_table = $pdo->prepare("UPDATE tables SET status = 'Occupied' WHERE id = ?");
$update_table->execute([$table_id]);

// Step 2: Update reservation status
$update_res = $pdo->prepare("UPDATE reservations SET status = 'Approved' WHERE id = ?");
$update_res->execute([$reservation_id]);

// Optionally: Save which table was reserved
$pdo->prepare("UPDATE reservations SET table_id = ? WHERE id = ?")
    ->execute([$table_id, $reservation_id]);

header("Location: tables.php");
exit;
