<?php
// File: reset_password.php
session_start();
require 'config/koneksi.php';
require 'config/kamus.php';

// Ambil bahasa dari session
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'id';
}
$lang = $_SESSION['lang'];

if (isset($_POST["reset"])) {
    $username = $_POST['username'];
    $password_baru = $_POST['new_password'];

    // Mengacak password baru
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

    try {
        $sql = "UPDATE users SET password = :password WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':password' => $password_hash,
            ':username' => $username
        ]);
        
        if ($stmt->rowCount() > 0) {
            $pesan = "<div class='alert-success' style='margin-bottom: 15px;'>Berhasil! Silakan <a href='login.php'>Login</a></div>";
        } else {
            $pesan = "<div class='alert-error' style='margin-bottom: 15px;'>Username tidak ditemukan!</div>";
        }
    } catch (PDOException $e) {
        $pesan = "<div class='alert-error'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $kamus[$lang]['reset_title'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f6f9;">

    <div class="card" style="width: 100%; max-width: 400px;">
        
        <!-- Header & Tombol Ganti Bahasa -->
        <div class="header-flex" style="padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0;">
            <h3 style="margin: 0;"><?= $kamus[$lang]['reset_title'] ?></h3>
            <div class="lang-switch">
                <a href="proses/ganti_bahasa.php?lang=id">ID</a> | 
                <a href="proses/ganti_bahasa.php?lang=en">EN</a>
            </div>
        </div>
        
        <?= $pesan ?? '' ?>

        <!-- Form Reset -->
        <form method="POST" action="">
            <div class="form-group">
                <label><?= $kamus[$lang]['reset_user'] ?>:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label><?= $kamus[$lang]['reset_pass'] ?>:</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            
            <button type="submit" name="reset" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                <?= $kamus[$lang]['reset_btn'] ?>
            </button>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="login.php" style="color: #64748b; text-decoration: none; font-size: 14px;">
                    <?= $kamus[$lang]['back_login'] ?>
                </a>
            </div>
        </form>
    </div>

</body>
</html>