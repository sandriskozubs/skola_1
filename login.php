<?php


if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require __DIR__ . '/session_secure.php';
require __DIR__ . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login']    ?? '');
    $password =      $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $error = 'All fields are required.';
    } else {
        $stmt = mysqli_prepare($conn,
            'SELECT id, username, password_hash FROM users
             WHERE username = ? OR email = ?
             LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ss', $login, $login);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username/email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Marketplace</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Marketplace</a></h1>
    </header>

    <main class="form-wrap">
        <h2>Log In</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="login.php">
            <label for="login">Username or Email <span class="req">*</span></label>
            <input type="text" id="login" name="login"
                   value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                   autocomplete="username" required>

            <label for="password">Password <span class="req">*</span></label>
            <input type="password" id="password" name="password"
                   autocomplete="current-password" required>

            <button type="submit" class="btn">Log In</button>
        </form>

        <p class="auth-switch">Don't have an account? <a href="register.php">Register</a></p>
    </main>
</body>
</html>
