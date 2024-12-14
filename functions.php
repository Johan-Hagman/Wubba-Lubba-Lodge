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
