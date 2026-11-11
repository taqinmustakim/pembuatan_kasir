<?php
require "config/koneksi.php";
$cek = $koneksi->query("SELECT COUNT(*) FROM users WHERE role='administrator'")->fetchColumn();
if ($cek > 0) {
    echo "Administrator sudah dibuat. Hapus file setup_admin.php setelah selesai.";
    exit;
}
$nama = "Administrator";
$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$stmt = $koneksi->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, 'administrator')");
$stmt->execute([$nama, $username, $password]);
echo "Akun admin berhasil dibuat. Username: admin | Password: admin123. Segera hapus file setup_admin.php.";
