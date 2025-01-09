<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php"; // Load dependencies via Composer

function connect(): PDO
{
    // Path to the database
    $dbName = __DIR__ . '/../database/yrgopelago.db'; // Specify the location of the SQLite database
    $db = "sqlite:$dbName";

    // Try to connect to the database
    try {
        return new PDO($db, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Handle errors as exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Default fetch mode: associative arrays
        ]);
    } catch (PDOException $e) {
        // Log or display an error message if the connection fails
        die("Failed to connect to the database: " . $e->getMessage());
    }
}
