<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

function connect(): PDO

{
    // Sökvägen till din databas
    $dbName = __DIR__ . '/../database/yrgopelago.db';
    $db = "sqlite:$dbName";

    // Försök att ansluta till databasen
    try {
        return new PDO($db, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Hantera fel som undantag
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Standard: associativa arrayer
        ]);
    } catch (PDOException $e) {
        // Logga eller visa ett felmeddelande om anslutningen misslyckas
        die("Failed to connect to the database: " . $e->getMessage());
    }
}


// function connect(): PDO
// {
//     // Sökvägen till din databas
//     $dbName = __DIR__ . '/../database/yrgopelago.db';
//     $db = "sqlite:$dbName";

//     // Försök att ansluta till databasen
//     try {
//         $pdo = new PDO($db);
//         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Hantera fel som undantag
//         $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Standard: associativa arrayer
//     } catch (PDOException $e) {
//         // Logga eller visa ett felmeddelande om anslutningen misslyckas
//         die("Failed to connect to the database: " . $e->getMessage());
//     }

//     return $pdo;
// }
