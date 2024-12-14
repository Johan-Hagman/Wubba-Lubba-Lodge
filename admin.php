<?php
session_start();

// Kontrollera att användaren är inloggad som admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die('Access denied.');
}

require __DIR__ . '/api/database.php';

// Hämta loggar från databasen
$stmt = $pdo->query("SELECT * FROM api_logs ORDER BY created_at DESC");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>

<body>
    <h1>API Logs</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Endpoint</th>
                <th>Request Data</th>
                <th>Response Data</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['id']) ?></td>
                    <td><?= htmlspecialchars($log['endpoint']) ?></td>
                    <td>
                        <pre><?= htmlspecialchars($log['request_data']) ?></pre>
                    </td>
                    <td>
                        <pre><?= htmlspecialchars($log['response_data']) ?></pre>
                    </td>
                    <td><?= htmlspecialchars($log['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="location.href='index.html';">Back to startpage</button>
</body>

</html>