<?php
// File: ganti_bahasa.php
session_start(); 

if (isset($_GET['lang']) && in_array($_GET['lang'], ['id', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$redirect = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
header("Location: " . $redirect);
exit;
?>