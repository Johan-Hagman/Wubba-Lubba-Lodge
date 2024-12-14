<?php

declare(strict_types=1);

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
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        die("CURL error: $error");
    }

    curl_close($ch);

    // Försök att tolka JSON-svaret
    $decodedResponse = json_decode($response, true);

    if ($decodedResponse === null) {
        die("Failed to parse API response. Raw response: $response");
    }

    return $decodedResponse;
}

function consumeTransferCode(string $username, string $transferCode, int $numberOfDays): array
{
    $url = 'https://www.yrgopelago.se/centralbank/deposit';

    $data = [
        'user' => $username,
        'transferCode' => $transferCode,
        'numberOfDays' => $numberOfDays
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        die("CURL error: $error");
    }

    curl_close($ch);

    // Försök att tolka JSON-svaret
    $decodedResponse = json_decode($response, true);

    if ($decodedResponse === null) {
        die("Failed to parse API response. Raw response: $response");
    }

    return $decodedResponse;
}



// Logga transfercodes värde
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
