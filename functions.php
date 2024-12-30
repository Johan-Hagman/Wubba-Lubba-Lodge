<?php

declare(strict_types=1);

require __DIR__ . '/api/database.php';

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

function getAvailableRooms(PDO $pdo): array
{
    try {
        $stmt = $pdo->query("SELECT id, type, price FROM rooms");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching rooms: " . $e->getMessage());
    }
}

function getAvailableFeatures(PDO $pdo): array
{
    try {
        $stmt = $pdo->query("SELECT * FROM features");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching features: " . $e->getMessage());
    }
}

// Hämta rum genom funktionen
$rooms = getAvailableRooms($pdo);

// Hämta alla tillgängliga features
$features = getAvailableFeatures($pdo);


// Hämta rabattprocenten från databasen
$stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'discount_percentage'");
$stmt->execute();
$currentDiscount = (int)$stmt->fetchColumn();
