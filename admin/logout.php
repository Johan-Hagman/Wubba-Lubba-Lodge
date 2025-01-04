<?php
session_start();

// Förstör sessionen
unset($_SESSION['is_admin']);
session_destroy();

// Skicka användaren till startsidan
header('Location: ./index.php');
exit();
