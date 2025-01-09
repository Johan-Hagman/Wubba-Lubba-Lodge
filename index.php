<?php
require_once __DIR__ . '/api/database.php';
require_once __DIR__ . '/functions.php';
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <link rel="icon" href="./assets/images/wll-logo.webp" type="image/webp">
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="stylesheet" href="./assets/css/header.css">
  <link rel="stylesheet" type="text/css" href="./vendor/benhall14/php-calendar/html/css/calendar.min.css">
  <link rel="stylesheet" href="./assets/css/booking.css">
  <link rel="stylesheet" href="./assets/css/footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <title>Wubba Lubba Lodge</title>
</head>

<body>

  <!-- Header -->
  <?php
  require __DIR__ . '/views/header.php';
  ?>

  <main>
    <!-- Hero Section -->
    <div class="hero-section">
      <img src="./assets/images/rick-morty-hero4.webp" alt="Hero Background">
      <button class="cta-button" onclick="scrollToBooking()" aria-label="Scroll to booking section">Book Now!</button>
    </div>

    <!-- Calendar -->
    <section id="calendar">
      <?php include 'api/calender.php'; ?>
    </section>

    <!-- Bookingform -->
    <?php
    require __DIR__ . '/views/booking_form.php';
    ?>

  </main>

  <!-- Footer -->
  <?php
  require __DIR__ . "/views/footer.php";
  ?>


  <script src="./assets/script/transfercode.js"></script>
  <script src="./assets/script/calendar.js"></script>
  <script src="./assets/script/costCalculator.js"></script>
  <script src="./assets/script/formHandler.js"></script>
  <script src="./assets/script/scrollHandler.js"></script>



</body>

</html>