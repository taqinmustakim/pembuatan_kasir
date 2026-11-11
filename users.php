<?php
require "includes/auth.php";
harus_admin();
require "config/koneksi.php";
$pesan=""; $error="";
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nama=trim($_POST['nama'] ?? '');
    $username=trim($_POST['username'] ?? '');
    $password=$_POST['password'] ?? '';
    $role=$_POST['role'] ?? 'petugas';
    if ($nama==='' || $username==='' || strlen($password)<6 || !in_array($role,['administrator','petugas'],true)) {
        $error="Lengkapi data. Password minimal 6 karakter.";
    } else {
        try {
            $stmt=$koneksi->prepare("INSERT INTO users (nama,username,password,role) VALUES (?,?,?,?)");
            $stmt->execute([$nama,$username,password_hash($password,PASSWORD_DEFAULT),$role]);
            $pesan="Pengguna berhasil ditambahkan.";
        } catch(PDOException $e) { $error="Username sudah digunakan."; }
    }
}
$data=$koneksi->query("SELECT id,nama,username,role,dibuat_pada FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
require "includes/header.php";
?>
<h3>Data Pengguna</h3>
<?php if($pesan): ?><p class="pesan"><?= $pesan ?></p><?php endif; ?>
<?php if($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
<label>Nama</label><input name="nama" required>
<label>Username</label><input name="username" required>
<label>Password (minimal 6 karakter)</label><input type="password" name="password" required>
<label>Hak Akses</label><select name="role"><option value="petugas">Petugas</option><option value="administrator">Administrator</option></select>
<button type="submit">Tambah Pengguna</button>
</form>
<table><tr><th>Nama</th><th>Username</th><th>Role</th><th>Dibuat</th></tr>
<?php foreach($data as $u): ?><tr><td><?= htmlspecialchars($u['nama']) ?></td><td><?= htmlspecialchars($u['username']) ?></td><td><?= $u['role'] ?></td><td><?= $u['dibuat_pada'] ?></td></tr><?php endforeach; ?></table>
<?php require "includes/footer.php"; ?>
