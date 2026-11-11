<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aplikasi Kasir</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="header">
    <h2>Aplikasi Kasir</h2>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['user']['nama'] ?? 'Pengguna') ?></p>
</div>
<div class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="produk.php">Data Barang</a>
    <a href="penjualan.php">Pembelian</a>
    <a href="riwayat.php">Riwayat Penjualan</a>
    <?php if (($_SESSION['user']['role'] ?? '') === 'administrator'): ?>
        <a href="users.php">Data Pengguna</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</div>
<div class="container">
