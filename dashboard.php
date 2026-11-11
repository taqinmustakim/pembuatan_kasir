<?php
require "includes/auth.php";
require "config/koneksi.php";
$totalProduk = $koneksi->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$totalTransaksi = $koneksi->query("SELECT COUNT(*) FROM penjualan")->fetchColumn();
$totalStok = $koneksi->query("SELECT COALESCE(SUM(Stok),0) FROM produk")->fetchColumn();
require "includes/header.php";
?>
<h3>Dashboard</h3>
<p>halaman utama kasir</p>
<?php if (isset($_GET['pesan']) && $_GET['pesan']==='akses'): ?><p class="error">Anda tidak memiliki hak akses untuk halaman tersebut.</p><?php endif; ?>
<div class="kartu">Jumlah Barang<br><b><?= $totalProduk ?></b></div>
<div class="kartu">Jumlah Transaksi<br><b><?= $totalTransaksi ?></b></div>
<div class="kartu">Total Stok<br><b><?= $totalStok ?></b></div>
<?php require "includes/footer.php"; ?>
