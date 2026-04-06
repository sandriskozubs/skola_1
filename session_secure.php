<?php

// Sesijas cookie drošības iestatījumi
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);

session_start();


// Sesijas ID atjaunošana ik pēc 5 minūtēm
if (!isset($_SESSION['last_regen'])) {
    $_SESSION['last_regen'] = time();
} elseif (time() - $_SESSION['last_regen'] > 300) { // 300s = 5 min
    session_regenerate_id(true);
    $_SESSION['last_regen'] = time();
}

// Sesijas derīguma termiņš (30 min)
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) { // 1800s = 30 min
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// IP adrese (aizsardzība pret sesijas zādzību)
if (!isset($_SESSION['ip'])) {
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
} elseif ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR']) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// User-Agent pārbaude (papildu drošība)
if (!isset($_SESSION['ua'])) {
    $_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];
} elseif ($_SESSION['ua'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
