<?php
require __DIR__ . '/../api/database.php';

$room_id = (int)$_POST['room_id'];
$guest_name = trim($_POST['guest_name']);
$check_in_date = $_POST['check_in_date'];
$check_out_date = $_POST['check_out_date'];
$transfer_code = trim($_POST['transfer_code']);

// Validera inmatning
if (empty($guest_name) || empty($check_in_date) || empty($check_out_date) || empty($transfer_code)) {
    die('All fields are required.');
}

// Validera datum
if ($check_in_date < '2025-01-01' || $check_out_date > '2025-01-31' || $check_in_date >= $check_out_date) {
    die('Invalid date range.');
}

// Kontrollera tillgänglighet
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM bookings 
    WHERE room_id = :room_id 
      AND (check_in_date < :check_out_date AND check_out_date > :check_in_date)
");
$stmt->execute([
    ':room_id' => $room_id,
    ':check_in_date' => $check_in_date,
    ':check_out_date' => $check_out_date
]);

$isBooked = $stmt->fetchColumn() > 0;

if ($isBooked) {
    die('Room is not available.');
}

// Spara bokningen
$stmt = $pdo->prepare("
    INSERT INTO bookings (room_id, guest_name, check_in_date, check_out_date, transfer_code) 
    VALUES (:room_id, :guest_name, :check_in_date, :check_out_date, :transfer_code)
");
if ($stmt->execute([
    ':room_id' => $room_id,
    ':guest_name' => $guest_name,
    ':check_in_date' => $check_in_date,
    ':check_out_date' => $check_out_date,
    ':transfer_code' => $transfer_code
])) {
    echo 'Booking successful!';
} else {
    echo 'Booking failed.';
}
