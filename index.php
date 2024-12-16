<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="/assets/style.css">
  <link rel="stylesheet" href="/assets/header.css">
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

  <section>
    <!-- Kalendern -->
    <section id="calendar">
      <?php include 'api/calender.php'; ?>
    </section>

    <div class="form-container">
      <!-- Formulär för att boka ett rum -->
      <form action="/api/book_room.php" method="POST">
        <label for="room_id">Room ID:</label>
        <select id="room_id" name="room_id" required>
          <option value="1">Budget Room</option>
          <option value="2">Standard Room</option>
          <option value="3">Luxury Room</option>
        </select><br><br>

        <label for="check_in_date">Check-In Date:</label>
        <input type="date" id="check_in_date" name="check_in_date" min="2025-01-01" max="2025-01-31" required><br><br>

        <label for="check_out_date">Check-Out Date:</label>
        <input type="date" id="check_out_date" name="check_out_date" min="2025-01-01" max="2025-01-31" required><br><br>

        <label for="guest_name">Guest Name:</label>
        <input type="text" id="guest_name" name="guest_name" required><br><br>

        <label for="transfer_code">Transfer Code:</label>
        <input type="text" id="transfer_code" name="transfer_code" required><br><br>

        <button type="submit">Book Now</button>
      </form>
    </div>
  </section>

  <script src="/assets/script.js"></script>
</body>

</html>