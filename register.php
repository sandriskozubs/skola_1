<?php


if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require __DIR__ . '/session_secure.php';
require __DIR__ . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  =      $_POST['password']  ?? '';
    $password2 =      $_POST['password2'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $password2) {
        $error = 'Passwords do not match.';
    } else {
        // Check for existing username / email
        $stmt = mysqli_prepare($conn,
            'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
        mysqli_stmt_execute($stmt);
        $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($existing) {
            $error = 'That username or email is already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = mysqli_prepare($conn,
                'INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)'
            );
            mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $hash);
            mysqli_stmt_execute($stmt);

            session_regenerate_id(true);
            $_SESSION['user_id']  = (int)mysqli_insert_id($conn);
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – Marketplace</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Marketplace</a></h1>
    </header>

    <main class="form-wrap">
        <h2>Create an Account</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="register.php">
            <label for="username">Username <span class="req">*</span></label>
            <input type="text" id="username" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   maxlength="60" autocomplete="username" required>

            <label for="email">Email <span class="req">*</span></label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   maxlength="200" autocomplete="email" required>

            <label for="password">Password <span class="req">*</span></label>
            <input type="password" id="password" name="password"
                   autocomplete="new-password" minlength="8" required>

            <label for="password2">Confirm Password <span class="req">*</span></label>
            <input type="password" id="password2" name="password2"
                   autocomplete="new-password" minlength="8" required>

            <button type="submit" class="btn">Register</button>
        </form>

        <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
    </main>
</body>
</html>
