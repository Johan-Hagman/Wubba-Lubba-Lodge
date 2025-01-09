<?php
// Fetch the number of stars from the database
$query = $pdo->query('SELECT stars FROM hotel_info LIMIT 1');
$stars = $query->fetchColumn();

// Ensure the number of stars is a valid integer
$stars = is_numeric($stars) ? (int)$stars : 0;
?>

<header>
    <!-- Navbar -->
    <nav class="navbar" aria-label="Main navigation">

        <div class="stars-container" aria-label="Hotel Rating">
            <?php
            // Generate stars based on the $stars value from the database
            for ($i = 0; $i < $stars; $i++) {
                echo '<img src="./assets/images/star.png" alt="Star" class="stars">';
            }
            ?>
        </div>

        <!-- Logo and heading -->
        <div class="logo-container">
            <img src="./assets/images/wll-logo.webp" alt="Wubba Lubba Lodge Logo" class="logo">
            <h1 class="header">WUBBA LUBBA LODGE</h1>
        </div>

    </nav>
</header>