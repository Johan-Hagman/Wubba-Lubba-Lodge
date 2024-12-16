<?php
session_start();

// Kontrollera att användaren är inloggad som admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die('Access denied.');
}

require __DIR__ . '/api/database.php';

// Uppdatera pris i databasen om formuläret skickas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['room_id'];
    $newPrice = $_POST['price'];

    $stmt = $pdo->prepare("UPDATE rooms SET price = :price WHERE id = :id");
    $stmt->execute(['price' => $newPrice, 'id' => $roomId]);

    echo "Priset för rummet har uppdaterats!";
}

// Hämta alla rum med deras priser
$roomsStmt = $pdo->query("SELECT id, type, price FROM rooms");
$rooms = $roomsStmt->fetchAll(PDO::FETCH_ASSOC);


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

    <section class="admin">
        <h1>Admin Panel - Uppdatera Rumspriser</h1>
        <?php foreach ($rooms as $room): ?>
            <form method="POST" action="admin.php">
                <h3><?php echo htmlspecialchars($room['type']); ?></h3>
                <label for="price_<?php echo $room['id']; ?>">Nuvarande pris: <?php echo number_format($room['price'], 2); ?>$</label><br>
                <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                <input type="number" step="0.01" name="price" id="price_<?php echo $room['id']; ?>" value="<?php echo $room['price']; ?>" required>
                <button type="submit">Uppdatera Pris</button>
            </form>
            <hr>
        <?php endforeach; ?>
    </section>

    <section class="api-logs">
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
                        <td><?= htmlspecialchars($log['id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($log['endpoint'] ?? '') ?></td>
                        <td>
                            <pre><?= htmlspecialchars($log['request_data'] ?? '') ?></pre>
                        </td>
                        <td>
                            <pre><?= htmlspecialchars($log['response_data'] ?? '') ?></pre>
                        </td>
                        <td><?= htmlspecialchars($log['created_at'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="POST" action="users/logout.php">
            <button type="submit">Logga ut</button>
        </form>
    </section>
</body>

</html>