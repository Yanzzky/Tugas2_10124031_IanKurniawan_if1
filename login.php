<?php
session_start();

require 'config/koneksi.php';
require 'config/kamus.php';

// Atur bahasa default jika belum ada
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'id';
}
$lang = $_SESSION['lang'];

// Pengolahan Form POST dalam satu file
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepared Statement PDO dengan Named Parameters
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek kecocokan data
    // encript password
    if ($user && password_verify($password, $user['password'])) {
        // Otentikasi Berhasil: Simpan Session
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // Arahkan ke halaman utama
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $kamus[$lang]['login_title'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

    <div class="login-card">
        <!-- Kolom Kiri: Banner Biru & Informasi Sistem -->
        <div class="login-left">
            <div>
                <h1 class="brand-title">Pinjam Ruangan</h1>
                <p class="brand-subtitle"><?= $kamus[$lang]['title'] ?></p>
            </div>
            
            <div class="brand-features">
                <p>✓ <?= $kamus[$lang]['feat1'] ?></p>
                <p>✓ <?= $kamus[$lang]['feat2'] ?></p>
                <p>✓ <?= $kamus[$lang]['feat3'] ?></p>
            </div>
        </div>

        <!-- Kolom Kanan: Form Login -->
        <div class="login-right">
            <div class="header-flex" style="border: none; padding-bottom: 0; margin-bottom: 10px;">
                <h3><?= $kamus[$lang]['login_title'] ?></h3>
                <div class="lang-switch">
                    <!-- Link diarahkan ke folder proses/ -->
                    <a href="proses/ganti_bahasa.php?lang=id">ID</a> | 
                    <a href="proses/ganti_bahasa.php?lang=en">EN</a>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert-error" style="margin-top: 10px;"><?= $error ?></div>
            <?php endif; ?>

            <!-- Form Method POST -->
            <form method="POST" action="" style="margin-top: 15px;">
                <div class="form-group">
                    <label><?= $kamus[$lang]['username'] ?>:</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                
                <div class="form-group">
                    <label><?= $kamus[$lang]['password'] ?>:</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; padding: 12px;">
                    <?= $kamus[$lang]['login_btn'] ?>
                </button>

                <div style="text-align: center; margin-top: 20px; font-size: 14px;">
                    <a href="reset_password.php" style="color: #0284c7; text-decoration: none; font-weight: 600;">
                        <?= $kamus[$lang]['reset_pwd'] ?>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>