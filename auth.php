<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
function harus_admin() {
    if ($_SESSION['user']['role'] !== 'administrator') {
        header("Location: dashboard.php?pesan=akses");
        exit;
    }
}
