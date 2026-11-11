<?php
require "includes/auth.php";
require "config/koneksi.php";
if (!isset($_SESSION['cart'])) $_SESSION['cart']=[];
$error=""; $pesan="";
if (isset($_GET['hapus'])) { unset($_SESSION['cart'][(int)$_GET['hapus']]); header("Location: penjualan.php"); exit; }
if (isset($_POST['tambah'])) {
    $id=(int)($_POST['produk_id'] ?? 0); $jumlah=(int)($_POST['jumlah'] ?? 0);
    $stmt=$koneksi->prepare("SELECT * FROM produk WHERE ProdukID=?"); $stmt->execute([$id]); $p=$stmt->fetch(PDO::FETCH_ASSOC);
    if (!$p || $jumlah<1) $error="Pilih barang dan jumlah yang benar.";
    elseif ($jumlah > (int)$p['Stok']) $error="Jumlah melebihi stok tersedia.";
    else {
        $lama=$_SESSION['cart'][$id]['jumlah'] ?? 0;
        if ($lama+$jumlah > (int)$p['Stok']) $error="Jumlah total melebihi stok.";
        else $_SESSION['cart'][$id]=['nama'=>$p['NamaProduk'],'harga'=>(float)$p['Harga'],'jumlah'=>$lama+$jumlah];
    }
}
if (isset($_POST['checkout']) && count($_SESSION['cart'])>0) {
    $pelanggan=(int)($_POST['pelanggan_id'] ?? 0);
    try {
        $koneksi->beginTransaction();
        $total=0;
        foreach($_SESSION['cart'] as $id=>$item) $total += $item['harga']*$item['jumlah'];
        $stmt=$koneksi->prepare("INSERT INTO penjualan (TotalHarga,PelangganID,UserID) VALUES (?,?,?)");
        $stmt->execute([$total,$pelanggan ?: null,$_SESSION['user']['id']]);
        $penjualanId=(int)$koneksi->lastInsertId();
        foreach($_SESSION['cart'] as $id=>$item) {
            $cek=$koneksi->prepare("SELECT Stok FROM produk WHERE ProdukID=? FOR UPDATE");
            $cek->execute([$id]); $stok=$cek->fetchColumn();
            if ($stok===false || $stok < $item['jumlah']) throw new Exception("Stok barang berubah atau tidak cukup.");
            $sub=$item['harga']*$item['jumlah'];
            $detail=$koneksi->prepare("INSERT INTO detail_penjualan (PenjualanID,ProdukID,JumlahProduk,Subtotal) VALUES (?,?,?,?)");
            $detail->execute([$penjualanId,$id,$item['jumlah'],$sub]);
            $kurang=$koneksi->prepare("UPDATE produk SET Stok=Stok-? WHERE ProdukID=?");
            $kurang->execute([$item['jumlah'],$id]);
        }
        $koneksi->commit();
        $_SESSION['cart']=[];
        header("Location: riwayat.php?berhasil=".$penjualanId); exit;
    } catch(Exception $e) {
        $koneksi->rollBack(); $error=$e->getMessage();
    }
}
$produk=$koneksi->query("SELECT * FROM produk WHERE Stok>0 ORDER BY NamaProduk")->fetchAll(PDO::FETCH_ASSOC);
$pelanggan=$koneksi->query("SELECT * FROM pelanggan ORDER BY NamaPelanggan")->fetchAll(PDO::FETCH_ASSOC);
$total=0; foreach($_SESSION['cart'] as $item) $total += $item['harga']*$item['jumlah'];
require "includes/header.php";
?>
<h3>Transaksi Pembelian</h3>
<?php if($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
<label>Barang</label><select name="produk_id" required><option value="">-- Pilih Barang --</option>
<?php foreach($produk as $p): ?><option value="<?= $p['ProdukID'] ?>"><?= htmlspecialchars($p['NamaProduk']) ?> - Rp <?= number_format($p['Harga'],0,',','.') ?> (Stok <?= $p['Stok'] ?>)</option><?php endforeach; ?>
</select>
<label>Jumlah</label><input type="number" name="jumlah" min="1" value="1" required>
<button name="tambah" type="submit">Tambah ke Keranjang</button>
</form>
<h4>Keranjang</h4>
<table><tr><th>Barang</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th>Aksi</th></tr>
<?php foreach($_SESSION['cart'] as $id=>$item): ?><tr><td><?= htmlspecialchars($item['nama']) ?></td><td>Rp <?= number_format($item['harga'],0,',','.') ?></td><td><?= $item['jumlah'] ?></td><td>Rp <?= number_format($item['harga']*$item['jumlah'],0,',','.') ?></td><td><a href="penjualan.php?hapus=<?= $id ?>">Hapus</a></td></tr><?php endforeach; ?>
<tr><th colspan="3">Total</th><th colspan="2">Rp <?= number_format($total,0,',','.') ?></th></tr></table>
<form method="post">
<label>Pelanggan</label><select name="pelanggan_id"><option value="">pembeli</option>
<?php foreach($pelanggan as $pl): ?><option value="<?= $pl['PelangganID'] ?>"><?= htmlspecialchars($pl['NamaPelanggan']) ?></option><?php endforeach; ?></select>
<button name="checkout" type="submit" <?= count($_SESSION['cart'])===0?'disabled':'' ?>>Simpan Transaksi</button>
</form>
<?php require "includes/footer.php"; ?>
