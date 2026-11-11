<?php
require "includes/auth.php";
require "config/koneksi.php";
$data=$koneksi->query("SELECT p.*, pl.NamaPelanggan, u.nama AS NamaPetugas FROM penjualan p LEFT JOIN pelanggan pl ON p.PelangganID=pl.PelangganID JOIN users u ON p.UserID=u.id ORDER BY p.PenjualanID DESC")->fetchAll(PDO::FETCH_ASSOC);
require "includes/header.php";
?>
<h3>Riwayat Penjualan</h3>
<?php if(isset($_GET['berhasil'])): ?><p class="pesan">Transaksi nomor <?= (int)$_GET['berhasil'] ?> berhasil disimpan.</p><?php endif; ?>
<table><tr><th>ID</th><th>Tanggal</th><th>pembeli</th><th>Petugas</th><th>Total</th><th>Detail</th></tr>
<?php foreach($data as $p): ?><tr><td><?= $p['PenjualanID'] ?></td><td><?= $p['TanggalPenjualan'] ?></td><td><?= htmlspecialchars($p['NamaPelanggan'] ?? 'Pelanggan umum') ?></td><td><?= htmlspecialchars($p['NamaPetugas']) ?></td><td>Rp <?= number_format($p['TotalHarga'],0,',','.') ?></td><td><a href="detail.php?id=<?= $p['PenjualanID'] ?>">Lihat</a></td></tr><?php endforeach; ?>
</table>
<?php require "includes/footer.php"; ?>
