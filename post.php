<?php


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/session_secure.php';
require __DIR__ . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = trim($_POST['price']       ?? '');
    $contact     = trim($_POST['contact']     ?? '');

    if ($title === '' || $price === '' || $contact === '') {
        $error = 'Title, price, and contact are required.';
    } elseif (!is_numeric($price) || (float)$price < 0) {
        $error = 'Price must be a valid positive number.';
    } else {
        $price_f = (float)$price;
        $stmt    = mysqli_prepare($conn,
            'INSERT INTO items (title, description, price, contact) VALUES (?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'ssds', $title, $description, $price_f, $contact);
        mysqli_stmt_execute($stmt);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post an Item – Marketplace</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Marketplace</a></h1>
    </header>

    <main class="form-wrap">
        <h2>Post an Item</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="post.php">
            <label for="title">Title <span class="req">*</span></label>
            <input type="text" id="title" name="title"
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                   maxlength="200" required>

            <label for="price">Price ($) <span class="req">*</span></label>
            <input type="number" id="price" name="price" min="0" step="0.01"
                   value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
                   required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"
                      maxlength="2000"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

            <label for="contact">Contact (email or phone) <span class="req">*</span></label>
            <input type="text" id="contact" name="contact"
                   value="<?= htmlspecialchars($_POST['contact'] ?? '') ?>"
                   maxlength="200" required>

            <button type="submit" class="btn">Post Item</button>
            <a href="index.php" class="cancel">Cancel</a>
        </form>
    </main>
</body>
</html>
