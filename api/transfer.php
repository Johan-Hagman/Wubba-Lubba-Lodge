<?php
// Inkludera din functions.php
require __DIR__ . '/../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $apiKey = htmlspecialchars(trim($_POST['apiKey']));
    $amount = (float) $_POST['amount'];
    try {
        $response = createTransferCode($username, $apiKey, $amount);

        if (isset($response['transferCode'])) {
            // Skicka tillbaka transferkoden som JSON
            echo json_encode(['transferCode' => $response['transferCode']]);
        } else {
            // Skicka tillbaka ett generellt felmeddelande
            echo json_encode(['error' => 'Unable to create transfer code. Please try again.']);
        }
    } catch (Exception $e) {
        // Skicka ett generellt fel istället för detaljer
        echo json_encode(['error' => 'An unexpected error occurred.']);
    }
}
