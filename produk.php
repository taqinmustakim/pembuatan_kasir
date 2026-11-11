<?php
require "includes/auth.php";
require "config/koneksi.php";
$error = "";
if (isset($_GET['hapus'])) {
    harus_admin();
    $id = (int)$_GET['hapus'];
    try {
        $stmt = $koneksi->prepare("DELETE FROM produk WHERE ProdukID=?");
        $stmt->execute([$id]);
        header("Location: produk.php?pesan=hapus"); exit;
    } catch (PDOException $e) { $error = "Barang tidak dapat dihapus karena sudah pernah masuk transaksi."; }
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $harga = (float)($_POST['harga'] ?? 0);
    $stok = (int)($_POST['stok'] ?? 0);
    if ($nama==='' || $harga<0 || $stok<0) {
        $error = "Isi data dengan benar.";
    } else {
        if ($id>0) {
            $stmt=$koneksi->prepare("UPDATE produk SET NamaProduk=?, Harga=?, Stok=? WHERE ProdukID=?");
            $stmt->execute([$nama,$harga,$stok,$id]);
        } else {
            $stmt=$koneksi->prepare("INSERT INTO produk (NamaProduk,Harga,Stok) VALUES (?,?,?)");
            $stmt->execute([$nama,$harga,$stok]);
        }
        header("Location: produk.php?pesan=simpan"); exit;
    }
}
$edit = null;
if (isset($_GET['edit'])) {
    $stmt=$koneksi->prepare("SELECT * FROM produk WHERE ProdukID=?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit=$stmt->fetch(PDO::FETCH_ASSOC);
}
$data=$koneksi->query("SELECT * FROM produk ORDER BY ProdukID DESC")->fetchAll(PDO::FETCH_ASSOC);
require "includes/header.php";
?>
<h3>Data Barang</h3>
<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<?php if (isset($_GET['pesan'])): ?><p class="pesan">Data berhasil diproses.</p><?php endif; ?>
<h4><?= $edit ? 'Edit Barang' : 'Tambah Barang' ?></h4>
<form method="post">
<input type="hidden" name="id" value="<?= $edit['ProdukID'] ?? 0 ?>">
<label>Nama Barang</label><input name="nama" required value="<?= htmlspecialchars($edit['NamaProduk'] ?? '') ?>">
<label>Harga</label><input type="number" name="harga" min="0" step="0.01" required value="<?= $edit['Harga'] ?? '' ?>">
<label>Stok</label><input type="number" name="stok" min="0" required value="<?= $edit['Stok'] ?? '' ?>">
<button type="submit">Simpan</button>
<?php if ($edit): ?> <a class="tombol" href="produk.php">Batal</a><?php endif; ?>
</form>
<table><tr><th>ID</th><th>Nama Barang</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
<?php foreach($data as $b): ?>
<tr><td><?= $b['ProdukID'] ?></td><td><?= htmlspecialchars($b['NamaProduk']) ?></td><td>Rp <?= number_format($b['Harga'],0,',','.') ?></td><td><?= $b['Stok'] ?></td>
<td><a href="produk.php?edit=<?= $b['ProdukID'] ?>">Edit</a>
<?php if ($_SESSION['user']['role']==='administrator'): ?> | <a href="produk.php?hapus=<?= $b['ProdukID'] ?>" onclick="return confirm('Hapus barang ini?')">Hapus</a><?php endif; ?></td></tr>
<?php endforeach; ?></table>
<p class="kecil"></p>
<?php require "includes/footer.php"; ?>
