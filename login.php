<?php
session_start();
require "config/koneksi.php";
if (isset($_SESSION['user'])) { header("Location: dashboard.php"); exit; }
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data && password_verify($password, $data['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $data['id'],
            'nama' => $data['nama'],
            'username' => $data['username'],
            'role' => $data['role']
        ];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>Login Kasir</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="login-box">
<h2>Login Aplikasi Kasir</h2>
<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
<label>Username</label><input type="text" name="username" required>
<label>Password</label><input type="password" name="password" required>
<button type="submit">Login</button>
</form>
<p class="kecil"></p>
</div>
</body></html>
