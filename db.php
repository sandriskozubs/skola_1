<?php

$host   = 'localhost';
$dbname = 'sanris_market';
$user   = 'root';
$pass   = '';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die('Database connection failed: ' . htmlspecialchars(mysqli_connect_error())
        . '. Have you run <a href="setup.php">setup.php</a>?');
}

mysqli_set_charset($conn, 'utf8mb4');
