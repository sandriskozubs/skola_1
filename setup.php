<?php

$host = 'localhost';
$user = 'root';
$pass = '';

$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    echo '<p style="font-family:sans-serif;color:red">Setup failed: '
         . htmlspecialchars(mysqli_connect_error()) . '</p>';
    exit;
}

mysqli_set_charset($conn, 'utf8mb4');

$queries = [
    "CREATE DATABASE IF NOT EXISTS sanris_market
     CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",

    "USE sanris_market",

    "CREATE TABLE IF NOT EXISTS items (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        title       VARCHAR(200)   NOT NULL,
        description TEXT,
        price       DECIMAL(10,2)  NOT NULL,
        contact     VARCHAR(200)   NOT NULL,
        created_at  DATETIME       DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS users (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        username      VARCHAR(60)  NOT NULL UNIQUE,
        email         VARCHAR(200) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at    DATETIME     DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($queries as $sql) {
    if (!mysqli_query($conn, $sql)) {
        echo '<p style="font-family:sans-serif;color:red">Setup failed: '
             . htmlspecialchars(mysqli_error($conn)) . '</p>';
        exit;
    }
}

echo '<p style="font-family:sans-serif">
        Setup complete! Database and tables are ready.<br><br>
        <a href="index.php">Go to the marketplace &rarr;</a>
      </p>';
