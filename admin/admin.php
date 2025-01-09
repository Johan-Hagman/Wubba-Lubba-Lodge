<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ./login.php'); // Redirect user to login page
    exit;
}

require_once __DIR__ . '/../api/database.php';
require_once __DIR__ . '/../functions.php';

// Handle form submissions for updating prices, discounts, stars, or deleting bookings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['room_id']) && isset($_POST['price'])) {
        // Update room price
        $roomId = $_POST['room_id'];
        $newPrice = $_POST['price'];

        $stmt = $pdo->prepare("UPDATE rooms SET price = :price WHERE id = :id");
        $stmt->execute(['price' => $newPrice, 'id' => $roomId]);

        echo "The price for the room has been updated!<br>";
    }

    if (isset($_POST['feature_id']) && isset($_POST['feature_price'])) {
        // Update feature price
        $featureId = $_POST['feature_id'];
        $newFeaturePrice = $_POST['feature_price'];

        $stmt = $pdo->prepare("UPDATE features SET price = :price WHERE id = :id");
        $stmt->execute(['price' => $newFeaturePrice, 'id' => $featureId]);

        echo "The price for the feature has been updated!<br>";
    }

    if (isset($_POST['discount_percentage'])) {
        // Update discount percentage
        $newDiscount = (int)$_POST['discount_percentage'];

        // Validate that discount is between 0 and 100
        if ($newDiscount < 0 || $newDiscount > 100) {
            echo "<p style='color: red;'>Discount must be between 0 and 100.</p>";
        } else {
            $stmt = $pdo->prepare("UPDATE settings SET value = :value WHERE name = 'discount_percentage'");
            $stmt->execute([':value' => $newDiscount]);

            echo "<p style='color: green;'>Discount updated to $newDiscount%!</p>";
            $currentDiscount = $newDiscount; // Update locally for immediate display
        }
    }

    if (isset($_POST['star_rating'])) {
        // Update hotel star rating
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
            echo "<p style='color: red;'>Rating must be between 1 and 5.</p>";
        }
    }

    if (isset($_POST['booking_id'])) {
        // Delete a booking
        $bookingId = intval($_POST['booking_id']); // Retrieve and sanitize the input

        if ($bookingId > 0) {
            // Prepare SQL query to delete the booking
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

// Fetch all rooms with their prices
$roomsStmt = $pdo->query("SELECT id, type, price FROM rooms");
$rooms = $roomsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all features with their prices
$featuresStmt = $pdo->query("SELECT id, name, price FROM features");
$features = $featuresStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch bookings from the database
$bookingsStmt = $pdo->query("SELECT id, room_id, guest_name, check_in_date, check_out_date, transfer_code FROM bookings ORDER BY check_in_date DESC");
$bookings = $bookingsStmt->fetchAll(PDO::FETCH_ASSOC);
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
        <!-- Update room prices -->
        <h2>Update Room Price</h2>
        <?php foreach ($rooms as $room): ?>
            <form method="POST" action="admin.php">
                <h3><?= htmlspecialchars($room['type']); ?></h3>
                <label for="price_<?= $room['id']; ?>">Current Price: <?= number_format($room['price'], 2); ?>$</label><br>
                <input type="hidden" name="room_id" value="<?= $room['id']; ?>">
                <input type="number" step="0.01" name="price" id="price_<?= $room['id']; ?>" value="<?= $room['price']; ?>" required>
                <button type="submit">Update Price</button>
            </form>
            <hr>
        <?php endforeach; ?>

        <!-- Update feature prices -->
        <h2>Update Feature Price</h2>
        <?php foreach ($features as $feature): ?>
            <form method="POST" action="admin.php">
                <h3><?= htmlspecialchars($feature['name']); ?></h3>
                <label for="feature_price_<?= $feature['id']; ?>">Current Price: <?= number_format($feature['price'], 2); ?>$</label><br>
                <input type="hidden" name="feature_id" value="<?= $feature['id']; ?>">
                <input type="number" step="0.01" name="feature_price" id="feature_price_<?= $feature['id']; ?>" value="<?= $feature['price']; ?>" required>
                <button type="submit">Update Price</button>
            </form>
            <hr>
        <?php endforeach; ?>

        <!-- Update discount percentage -->
        <h2>Update Discount Percentage</h2>
        <form method="POST" action="admin.php">
            <label for="discount_percentage">Current Discount: <?= htmlspecialchars($currentDiscount); ?>%</label><br>
            <input type="number" id="discount_percentage" name="discount_percentage" min="0" max="100" value="<?= htmlspecialchars($currentDiscount); ?>" required>
            <button type="submit">Update Discount</button>
        </form>

        <!-- Update hotel star rating -->
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

        <!-- Delete a booking -->
        <h2>Remove Booking</h2>
        <form method="POST" action="admin.php">
            <label for="booking_id">Enter Booking ID to delete:</label>
            <input type="number" id="booking_id" name="booking_id" required placeholder="Booking ID">
            <button type="submit">Delete Booking</button>
        </form>
    </section>

    <section class="bookings">
        <!-- Display all bookings -->
        <h1>Bookings</h1>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room ID</th>
                    <th>Guest Name</th>
                    <th>Check-In Date</th>
                    <th>Check-Out Date</th>
                    <th>Transfer Code</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['id'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($booking['room_id'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($booking['guest_name'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($booking['check_in_date'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($booking['check_out_date'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($booking['transfer_code'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <button type="button" onclick="location.href='./logout.php';">Log out</button>

</body>

</html>