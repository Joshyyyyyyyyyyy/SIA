<?php
require_once '../config/database.php'; // This file should define $pdo

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name        = $_POST['name'];
    $email       = $_POST['email'];
    $phone       = $_POST['phone'];
    $date        = $_POST['date'];
    $time        = $_POST['time'];
    $time_period = $_POST['time-period'];
    $guests      = $_POST['guests'];
    $theme       = $_POST['theme'];
    $message     = $_POST['message'];

    $sql = "INSERT INTO reservations (name, email, phone, date, time, time_period, guests, theme, message) 
            VALUES (:name, :email, :phone, :date, :time, :time_period, :guests, :theme, :message)";

    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        ':name'        => $name,
        ':email'       => $email,
        ':phone'       => $phone,
        ':date'        => $date,
        ':time'        => $time,
        ':time_period' => $time_period,
        ':guests'      => $guests,
        ':theme'       => $theme,
        ':message'     => $message
    ]);

    if ($success) {
        echo "<script>alert('Reservation submitted successfully!'); window.location.href='thank_you.html';</script>";
    } else {
        echo "Error submitting reservation.";
    }
}
?>
