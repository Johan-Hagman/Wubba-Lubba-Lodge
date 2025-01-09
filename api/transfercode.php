<?php

require __DIR__ . '/../functions.php'; // Include the file containing helper functions

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $username = htmlspecialchars(trim($_POST['username'])); // Sanitize username
    $apiKey = htmlspecialchars(trim($_POST['apiKey'])); // Sanitize API key
    $amount = (float) $_POST['amount']; // Cast the amount to a float

    try {
        // Call the createTransferCode function to generate a transfer code
        $response = createTransferCode($username, $apiKey, $amount);

        if (isset($response['transferCode'])) {
            // Return the transfer code as JSON if successfully created
            echo json_encode(['transferCode' => $response['transferCode']]);
        } else {
            // Return a generic error message if no transfer code was created
            echo json_encode(['error' => 'Unable to create transfer code. Please try again.']);
        }
    } catch (Exception $e) {
        // Handle unexpected errors gracefully by returning a generic error message
        echo json_encode(['error' => 'An unexpected error occurred.']);
    }
}
