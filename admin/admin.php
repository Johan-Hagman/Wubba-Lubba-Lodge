<?php
session_start();

// Kontrollera om användaren är autentiserad
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: /users/login.php'); // Skicka användaren tillbaka till login
    exit;
}

require_once __DIR__ . '/../api/database.php';
require_once __DIR__ . '/../functions.php';

// Uppdatera priser i databasen om formuläret skickas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['room_id']) && isset($_POST['price'])) {
        // Uppdatera pris för ett rum
        $roomId = $_POST['room_id'];
        $newPrice = $_POST['price'];

        $stmt = $pdo->prepare("UPDATE rooms SET price = :price WHERE id = :id");
        $stmt->execute(['price' => $newPrice, 'id' => $roomId]);

        echo "The price for the room has been updated!<br>";
    }

    if (isset($_POST['feature_id']) && isset($_POST['feature_price'])) {
        // Uppdatera pris för en feature
        $featureId = $_POST['feature_id'];
        $newFeaturePrice = $_POST['feature_price'];

        $stmt = $pdo->prepare("UPDATE features SET price = :price WHERE id = :id");
        $stmt->execute(['price' => $newFeaturePrice, 'id' => $featureId]);

        echo "The price for the feature has been updated!<br>";
    }

    if (isset($_POST['discount_percentage'])) {
        // Uppdatera rabatt
        $newDiscount = (int)$_POST['discount_percentage'];

        // Validera att rabattprocenten är mellan 0 och 100
        if ($newDiscount < 0 || $newDiscount > 100) {
            echo "<p style='color: red;'>Discount must be between 0 and 100.</p>";
        } else {
            $stmt = $pdo->prepare("UPDATE settings SET value = :value WHERE name = 'discount_percentage'");
            $stmt->execute([':value' => $newDiscount]);

            echo "<p style='color: green;'>Discount updated to $newDiscount%!</p>";
            $currentDiscount = $newDiscount; // Uppdatera lokalt för att visa det nya värdet direkt
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['star_rating'])) {
        $newRating = (int)$_POST['star_rating'];

        if ($newRating >= 1 && $newRating <= 5) {
            try {
                $stmt = $pdo->prepare("UPDATE hotel_info SET stars = :stars WHERE id = 1");
                $stmt->execute([':stars' => $newRating]);
                echo "<p style='color: green;'>Rating updated to $newRating stars!</p>";
            } catch (PDOException $e) {
                echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: red;'Rating must be between 1 and 5.</p>";
        }
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
        $bookingId = intval($_POST['booking_id']); // Retrieve and sanitize the input

        // Check if booking_id is valid
        if ($bookingId > 0) {
            // Prepare the SQL query using PDO
            $query = "DELETE FROM bookings WHERE id = :id";
            $stmt = $pdo->prepare($query);

            // Bind the parameter
            $stmt->bindParam(':id', $bookingId, PDO::PARAM_INT);

            // Execute the query and check the result
            if ($stmt->execute()) {
                echo "<p style='color: green;'>Booking with ID $bookingId was successfully deleted.</p>";
            } else {
                echo "<p style='color: red;'>Error: Could not delete the booking. Please try again.</p>";
            }
        } else {
            echo "<p style='color: red;'>Please enter a valid Booking ID.</p>";
        }
    }
}



// Hämta alla rum med deras priser
$roomsStmt = $pdo->query("SELECT id, type, price FROM rooms");
$rooms = $roomsStmt->fetchAll(PDO::FETCH_ASSOC);

// Hämta alla features med deras priser
$featuresStmt = $pdo->query("SELECT id, name, price FROM features");
$features = $featuresStmt->fetchAll(PDO::FETCH_ASSOC);

// Hämta nuvarande rabatt från databasen
$stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'discount_percentage'");
$stmt->execute();
$currentDiscount = $stmt->fetchColumn();

// Hämta nuvarande stjärnantal från databasen
$stmt = $pdo->prepare("SELECT stars FROM hotel_info WHERE id = 1");
$stmt->execute();
$currentRating = $stmt->fetchColumn(); // Standardvärde för stjärnor

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
        <h2>Update Room Price</h2>
        <?php foreach ($rooms as $room): ?>
            <form method="POST" action="admin.php">
                <h3><?php echo htmlspecialchars($room['type']); ?></h3>
                <label for="price_<?php echo $room['id']; ?>">Current Price: <?php echo number_format($room['price'], 2); ?>$</label><br>
                <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                <input type="number" step="0.01" name="price" id="price_<?php echo $room['id']; ?>" value="<?php echo $room['price']; ?>" required>
                <button type="submit">Update Price</button>
            </form>
            <hr>
        <?php endforeach; ?>

        <!-- Formulär för att uppdatera feature-priser -->
        <h2>Update Feature Price</h2>
        <?php foreach ($features as $feature): ?>
            <form method="POST" action="admin.php">
                <h3><?php echo htmlspecialchars($feature['name']); ?></h3>
                <label for="feature_price_<?php echo $feature['id']; ?>">Current Price: <?php echo number_format($feature['price'], 2); ?>$</label><br>
                <input type="hidden" name="feature_id" value="<?php echo $feature['id']; ?>">
                <input type="number" step="0.01" name="feature_price" id="feature_price_<?php echo $feature['id']; ?>" value="<?php echo $feature['price']; ?>" required>
                <button type="submit">Update Price</button>
            </form>
            <hr>
        <?php endforeach; ?>

        <!-- Formulär för att uppdatera rabatt -->
        <h2>Update Discount Percentage</h2>
        <form method="POST" action="admin.php">
            <label for="discount_percentage">Current Discount: <?php echo htmlspecialchars($currentDiscount); ?>%</label><br>
            <input type="number" id="discount_percentage" name="discount_percentage" min="0" max="100" value="<?php echo htmlspecialchars($currentDiscount); ?>" required>
            <button type="submit">Update Discount</button>
        </form>

        <h2>Update Hotel Stars</h2>
        <form action="admin.php" method="POST">
            <label for="star_rating">Select Star Rating:</label>
            <select name="star_rating" id="star_rating" required>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
            </select>
            <button type="submit">Update Stars</button>
        </form>

        <!-- Form to delete a booking -->
        <h2>Remove Booking</h2>
        <form method="POST" action="">
            <label for="booking_id">Enter Booking ID to delete:</label>
            <input type="number" id="booking_id" name="booking_id" required placeholder="Booking ID">
            <button type="submit">Delete Booking</button>
        </form>


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
        <form method="POST" action="./logout.php">
            <button type="submit">Logga ut</button>
        </form>
    </section>
</body>

</html>