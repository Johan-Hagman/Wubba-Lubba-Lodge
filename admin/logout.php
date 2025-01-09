<?php

declare(strict_types=1);

session_start();

// Destroy session
unset($_SESSION['is_admin']);
session_destroy();

// Send user to landingpage
header('Location: ./../index.php');
exit();
