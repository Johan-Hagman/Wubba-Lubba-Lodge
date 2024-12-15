<?php

declare(strict_types=1);

require __DIR__ . '/../api/database.php';
require __DIR__ . '/../functions.php';

$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : null;
$guest_name = htmlspecialchars(trim($_POST['guest_name'] ?? ''));
$check_in_date = htmlspecialchars(trim($_POST['check_in_date'] ?? ''));
$check_out_date = htmlspecialchars(trim($_POST['check_out_date'] ?? ''));
$transfer_code = htmlspecialchars(trim($_POST['transfer_code'] ?? ''));

if (empty($room_id) || empty($guest_name) || empty($check_in_date) || empty($check_out_date) || empty($transfer_code)) {
    die('All fields are required.');
}

// Kontrollera rum och hämta pris
$stmt = $pdo->prepare("SELECT price FROM rooms WHERE id = :room_id");
$stmt->execute([':room_id' => $room_id]);
$room_price = $stmt->fetchColumn();

if (!$room_price) {
    die('Room not found.');
}

// Validera transferkoden
$validationResult = validateTransferCode($transfer_code, $room_price);

// Kontrollera att svaret är en array
if (!is_array($validationResult)) {
    die("Unexpected response from API. Response is not an array.");
}

// Kontrollera om API:t returnerade ett fel
if (isset($validationResult['error'])) {
    die("API error: " . $validationResult['error']);
}

// Kontrollera om status är "success"
if (!isset($validationResult['status']) || $validationResult['status'] !== 'success') {
    die('Invalid or insufficient transfer code.');
}

// Kontrollera om transferkoden täcker kostnaden
if (isset($validationResult['totalCost']) && $validationResult['totalCost'] < $room_price) {
    die("Transfer code does not cover the room price. Required: $room_price, Available: " . $validationResult['totalCost']);
}

// Konsumera transferkoden och sätt in pengarna
$username = 'Johan'; // Ersätt med ditt användarnamn
$numberOfDays = (new DateTime($check_out_date))->diff(new DateTime($check_in_date))->days;

$depositResult = consumeTransferCode($username, $transfer_code, $numberOfDays);

// Logga responsen
logApiResponse($pdo, '/centralbank/deposit', [
    'user' => $username,
    'transferCode' => $transfer_code,
    'numberOfDays' => $numberOfDays
], $depositResult);

// Hantera responsen från /deposit
if (isset($depositResult['error'])) {
    die("Deposit failed: " . $depositResult['error']);
}

if (!isset($depositResult['message']) || stripos($depositResult['message'], 'success') === false) {
    die('Failed to deposit transfer code. Error: ' . ($depositResult['message'] ?? 'Unknown error.'));
}

// Kontrollera datum
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
    // Generera JSON-respons för godkänd bokning
    $response = [
        "island" => "Squanche Isle",
        "hotel" => "Wubba Lubba Lodge",
        "arrival_date" => $check_in_date,
        "departure_date" => $check_out_date,
        "total_cost" => (string)$room_price,
        "stars" => "to be announced",
        "features" => [
            [
                "name" => "tba",
                "cost" => "null"
            ]
        ],
        "addtional_info" => [
            "greeting" => "Thank you for choosing Wubba Lubba Lodge",
            "imageUrl" => "https://giphy.com/gifs/adultswim-liBsVeLILcyaY"
        ]
    ];

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
} else {
    die('Booking failed.');
}
