<?php

declare(strict_types=1);

require_once __DIR__ . '/api/database.php'; // Include the database connection function

$pdo = connect(); // Establish a database connection

// Function to validate a transfer code
function validateTransferCode(string $transferCode, float $totalCost): array
{
    $url = 'https://www.yrgopelago.se/centralbank/transferCode';
    $data = [
        'transferCode' => $transferCode,
        'totalcost' => $totalCost
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        die("CURL error: $error");
    }

    curl_close($ch);

    $decodedResponse = json_decode($response, true);

    if ($decodedResponse === null) {
        die("Failed to parse API response.");
    }

    return $decodedResponse;
}

// Function to consume a transfer code and deposit funds
function consumeTransferCode(string $username, string $transferCode, int $numberOfDays): array
{
    $url = 'https://www.yrgopelago.se/centralbank/deposit';

    $data = [
        'user' => $username,
        'transferCode' => $transferCode,
        'numberOfDays' => $numberOfDays,
    ];

    $postData = http_build_query($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json',
        'User-Agent: PostmanRuntime/7.28.0',
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        die("CURL error: $error");
    }

    curl_close($ch);

    $decodedResponse = json_decode($response, true);

    if ($decodedResponse === null) {
        die("Failed to parse API response.");
    }

    return $decodedResponse;
}

// Function to create a transfer code
function createTransferCode(string $username, string $apiKey, float $amount): array
{
    // Validate that the API key is a valid UUID
    if (!isValidUUID($apiKey)) {
        die("Invalid API key. Please ensure it is a UUID.");
    }

    $url = 'https://www.yrgopelago.se/centralbank/withdraw';
    $data = http_build_query([
        'user' => $username,
        'api_key' => $apiKey,
        'amount' => $amount
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json',
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        die("CURL error: $error");
    }

    curl_close($ch);

    $decodedResponse = json_decode($response, true);

    if ($decodedResponse === null) {
        die("Failed to parse API response.");
    }

    return $decodedResponse;
}

// Function to validate a UUID format
function isValidUUID(string $uuid): bool
{
    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid) === 1;
}

// Function to log API responses to the database
function logApiResponse(PDO $pdo, string $endpoint, array $requestData, array $responseData): void
{
    $stmt = $pdo->prepare("
        INSERT INTO api_logs (endpoint, request_data, response_data) 
        VALUES (:endpoint, :request_data, :response_data)
    ");
    $stmt->execute([
        ':endpoint' => $endpoint,
        ':request_data' => json_encode($requestData, JSON_PRETTY_PRINT),
        ':response_data' => json_encode($responseData, JSON_PRETTY_PRINT),
    ]);
}

// Function to fetch available rooms from the database
function getAvailableRooms(PDO $pdo): array
{
    try {
        $stmt = $pdo->query("SELECT id, type, price FROM rooms");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching rooms: " . $e->getMessage());
    }
}

// Function to fetch available features from the database
function getAvailableFeatures(PDO $pdo): array
{
    try {
        $stmt = $pdo->query("SELECT * FROM features");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching features: " . $e->getMessage());
    }
}

// Fetch rooms using the function
$rooms = getAvailableRooms($pdo);

// Fetch all available features
$features = getAvailableFeatures($pdo);

// Fetch the discount percentage from the database
$stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'discount_percentage'");
$stmt->execute();
$currentDiscount = (int)$stmt->fetchColumn();

// Fetch current star rating from the database
$stmt = $pdo->prepare("SELECT stars FROM hotel_info WHERE id = 1");
$stmt->execute();
$currentRating = $stmt->fetchColumn();
