<?php
require __DIR__ . '/api/database.php';
require __DIR__ . '/functions.php';
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="/assets/style.css">
  <link rel="stylesheet" href="/assets/header.css">
  <link rel="stylesheet" type="text/css" href="/vendor/benhall14/php-calendar/html/css/calendar.min.css">
  <link rel="stylesheet" href="/assets/booking.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <title>Wubba Lubba Lodge</title>
</head>

<body>

  <!-- Navbar -->
  <div class="navbar">
    <img src="/assets/wll-logo.webp" alt="Wubba Lubba Lodge Logo" class="logo">
    <button onclick="location.href='/users/login.php';">Admin Panel</button>
    <nav>
      <h1 class="header">WUBBA LUBBA LODGE</h1>
    </nav>
  </div>

  <!-- Hero Section -->
  <div class="hero-section">
    <img src="/assets/rick-morty-hero4.webp" alt="Hero Background">
    <button class="cta-button">Book Now!</button>
  </div>


  <!-- Kalendern -->
  <section id="calendar">
    <?php include 'api/calender.php'; ?>
  </section>
  <section class="booking">
    <div class="form-container">
      <!-- Formulär för att boka ett rum och välja features -->
      <form action="/api/book_room.php" method="POST">
        <!-- Room selection -->
        <select id="room_id" name="room_id" required>
          <?php
          foreach ($rooms as $room): ?>
            <option value="<?php echo $room['id']; ?>"
              data-price="<?php echo isset($room['price']) ? $room['price'] : 0; ?>">
              <?php echo htmlspecialchars($room['type']) . " ({$room['price']}$/night)"; ?>
            </option>
          <?php endforeach; ?>

        </select><br><br>

        <!-- Check-in and Check-out dates -->
        <label for="check_in_date">Check-In Date:</label>
        <input type="date" id="check_in_date" name="check_in_date" min="2025-01-01" max="2025-01-31" required><br><br>

        <label for="check_out_date">Check-Out Date:</label>
        <input type="date" id="check_out_date" name="check_out_date" min="2025-01-01" max="2025-01-31" required><br><br>

        <!-- Guest Name -->
        <label for="guest_name">Guest Name:</label>
        <input type="text" id="guest_name" name="guest_name" required><br><br>

        <!-- Transfer Code -->
        <label for="transfer_code">Transfer Code:</label>
        <input type="text" id="transfer_code" name="transfer_code" required><br><br>

        <!-- Features selection -->
        <h2>Select Features</h2>
        <?php foreach ($features as $feature): ?>
          <div>
            <input type="checkbox" id="feature-<?php echo $feature['id']; ?>"
              name="features[]"
              value="<?php echo $feature['id']; ?>"
              data-price="<?php echo isset($feature['price']) ? $feature['price'] : 0; ?>">
            <label for="feature-<?php echo $feature['id']; ?>">
              <?php echo htmlspecialchars($feature['name']) . " (Price: {$feature['price']}$)"; ?>
            </label>
          </div>
        <?php endforeach; ?>

        <!-- Total Cost -->
        <p><strong>Total Cost:</strong> <span id="total-cost">0</span> $</p>
        <button type="submit">Book Now</button>
      </form>
    </div>
  </section>


  <script src="/assets/script.js"></script>

</body>

</html>