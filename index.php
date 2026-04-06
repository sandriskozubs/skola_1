<?php
session_start();
require 'db.php';
require 'session_secure.php';

$loggedIn = isset($_SESSION['user_id']);

$result = mysqli_query($conn, 'SELECT * FROM items ORDER BY created_at DESC');
$items  = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <header>
        <h1>Marketplace</h1>
        <nav class="header-nav">
            <?php if ($loggedIn): ?>
                <span class="greeting">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="post.php" class="btn">+ Post Item</a>
                <a href="logout.php" class="btn btn-outline">Log Out</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Log In</a>
                <a href="register.php" class="btn">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php if (empty($items)): ?>
            <p class="empty">No items listed yet. <a href="post.php">Be the first to post one!</a></p>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($items as $item): ?>
                    <div class="card">
                        <h2><?= htmlspecialchars($item['title']) ?></h2>
                        <p class="price">$<?= number_format((float)$item['price'], 2) ?></p>
                        <?php if ($item['description'] !== ''): ?>
                            <p class="desc"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                        <?php endif; ?>
                        <p class="contact">Contact: <strong><?= htmlspecialchars($item['contact']) ?></strong></p>
                        <span class="date"><?= htmlspecialchars($item['created_at']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
