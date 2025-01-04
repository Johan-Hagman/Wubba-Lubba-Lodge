<?php
session_start();
// Load environment variables
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Retrieve the API key from the environment
$apiKeyFromEnv = $_ENV['API_KEY'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiKey = htmlspecialchars($_POST['api_key']);

    // Check if the entered API key matches the one from the environment
    if ($apiKey === $apiKeyFromEnv) {
        $_SESSION['is_admin'] = true;
        header('Location: /admin/admin.php');
        exit;
    } else {
        echo 'Well, that’s just perfect! Let’s add it to the list of things that have gone horribly wrong today!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>

<body>
    <form method="POST">
        <input type="text" name="api_key" placeholder="Enter your API Key" required>
        <!-- <input type="password" name="password" placeholder="Password" required> -->
        <button type="submit">Login</button>
        <button onclick="location.href='/../index.php';">Back to startpage</button>
    </form>
</body>

</html>