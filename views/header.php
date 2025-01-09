<?php
// Hämta antalet stjärnor från databasen
$query = $pdo->query('SELECT stars FROM hotel_info LIMIT 1');
$stars = $query->fetchColumn();

// Se till att antalet stjärnor är ett giltigt heltal
$stars = is_numeric($stars) ? (int)$stars : 0;
?>

<header>
    <!-- Navbar -->
    <nav class="navbar" aria-label="Main navigation">

        <div class="stars-container" aria-label="Hotel Rating">
            <?php
            // Generera stjärnor baserat på $stars från databasen
            for ($i = 0; $i < $stars; $i++) {
                echo '<img src="./assets/images/star.png" alt="Star" class="stars">';
            }
            ?>
        </div>


        <!-- Logotyp och rubrik -->
        <div class="logo-container">
            <img src="./assets/images/wll-logo.webp" alt="Wubba Lubba Lodge Logo" class="logo">
            <h1 class="header">WUBBA LUBBA LODGE</h1>
        </div>

    </nav>

</header>