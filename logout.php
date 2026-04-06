<?php
require __DIR__ . '/session_secure.php';

session_unset();
session_destroy();
header('Location: login.php');
exit;
