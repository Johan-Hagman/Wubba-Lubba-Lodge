<?php

declare(strict_types=1);

require __DIR__ . '/../api/database.php';
require __DIR__ . '/../functions.php';

$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : null;
$guest_name = htmlspecialchars(trim($_POST['guest_name'] ?? ''));
$check_in_date = htmlspecialchars(trim($_POST['check_in_date'] ?? ''));
$check_out_date = htmlspecialchars(trim($_POST['check_out_date'] ?? ''));
$transfer_code = htmlspecialchars(trim($_POST['transfer_code'] ?? ''));
$selected_features = $_POST['features'] ?? [];

if (empty($room_id) || empty($guest_name) || empty($check_in_date) || empty($check_out_date) || empty($transfer_code)) {
    die('All fields are required.');
}

// Kontrollera att datum är giltiga
if ($check_in_date < '2025-01-01' || $check_out_date > '2025-01-31' || $check_in_date >= $check_out_date) {
    die('Invalid date range.');
}

// Kontrollera rum och hämta pris per natt
$stmt = $pdo->prepare("SELECT price FROM rooms WHERE id = :room_id");
$stmt->execute([':room_id' => $room_id]);
$room_price = $stmt->fetchColumn();

if (!$room_price) {
    die('Room not found.');
}

// Beräkna antalet nätter
$check_in = new DateTime($check_in_date);
$check_out = new DateTime($check_out_date);
$numberOfNights = $check_in->diff($check_out)->days;

// Hämta rabattprocenten från databasen
$stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'discount_percentage'");
$stmt->execute();
$discountPercentage = (int)$stmt->fetchColumn();

if ($numberOfNights >= 3) {
    $discountAmount = ($numberOfNights * $room_price) * ($discountPercentage / 100);
} else {
    $discountAmount = 0;
}


// Beräkna totalkostnaden
$totalCost = ($numberOfNights * $room_price) - $discountAmount;

// Hämta alla valda features från databasen
if (!empty($selected_features)) {
    $placeholders = implode(',', array_fill(0, count($selected_features), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price FROM features WHERE id IN ($placeholders)");
    $stmt->execute($selected_features);
    $features = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lägg till kostnaden för features till totalen
    foreach ($features as $feature) {
        $totalCost += $feature['price'];
    }
} else {
    $features = []; // Om inga features är valda
}

// Validera transferkoden baserat på totalkostnaden
$validationResult = validateTransferCode($transfer_code, $totalCost);

if (!is_array($validationResult)) {
    die("Unexpected response from API. Response is not an array.");
}

if (isset($validationResult['error'])) {
    die('<div style="text-align: center; font-family: Arial, sans-serif;">
    <p style="color: red;">Invalid or insufficient transfer code.</p>
    <img src="/../assets/booking-denied.webp" alt="Error" style="width: 500px; height: auto;"/>
    <br> <br>
    <button onclick="location.href=\'/../index.php\';">Back to startpage</button>
</div>');
}

if (!isset($validationResult['status']) || $validationResult['status'] !== 'success') {
    die('<div style="text-align: center; font-family: Arial, sans-serif;">
    <p style="color: red;">Invalid or insufficient transfer code.</p>
    <img src="/../assets/booking-denied.webp" alt="Error" style="width: 500px; height: auto;"/>
    <br> <br>
     <button onclick="location.href=\'/../index.php\';">Back to startpage</button>
</div>');
}

if (isset($validationResult['totalCost']) && $validationResult['totalCost'] < $totalCost) {
    die('<div style="text-align: center; font-family: Arial, sans-serif;">
    <p style="color: red;">Transfer code does not cover the room cost.</p>
    <p>Required: $' . $totalCost . ', Available: $' . $validationResult['totalCost'] . '</p>
    <img src="/../assets/booking-denied.png" alt="Insufficient Funds" style="width: 200px; height: auto;"/>
</div>');
}

// Konsumera transferkoden och sätt in pengarna
$username = 'Johan';
$depositResult = consumeTransferCode($username, $transfer_code, intval($totalCost));

logApiResponse($pdo, '/centralbank/deposit', [
    'user' => $username,
    'transferCode' => $transfer_code,
    'totalCost' => $totalCost
], $depositResult);

if (isset($depositResult['error'])) {
    die("Deposit failed: " . $depositResult['error']);
}

if (!isset($depositResult['status']) || stripos($depositResult['status'], 'success') === false) {
    die('Failed to deposit transfer code. Error: ' . ($depositResult['status'] ?? 'Unknown error.'));
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
    // Hämta det senaste boknings-ID:t
    $bookingId = $pdo->lastInsertId();

    // Spara valda features i `booking_features`
    $stmt = $pdo->prepare("INSERT INTO booking_features (booking_id, feature_id) VALUES (?, ?)");
    foreach ($selected_features as $featureId) {
        $stmt->execute([$bookingId, $featureId]);
    }
} {
    $response = [
        "island" => "Squanche Isle",
        "hotel" => "Wubba Lubba Lodge",
        "arrival_date" => $check_in_date,
        "departure_date" => $check_out_date,
        "total_cost" => "$" . $totalCost,
        "stars" => "5",
        "features" => $features,
        "additional_info" => [
            "greeting" => "Thank you for choosing Wubba Lubba Lodge",
            "discount" => $discountAmount,
            "imageUrl" => "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExMW00amFhMnAwZnB0ZDQxcjJoeWEyZGZ3M2dzMXExMXA1M3RpbWw4ZCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/liBsVeLILcyaY/giphy.gif"
        ]
    ];

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}
