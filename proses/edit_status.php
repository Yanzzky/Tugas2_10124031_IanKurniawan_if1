<?php
// File: edit_peminjaman.php (Simpan di folder utama / root)
session_start();
require './config/koneksi.php';
require './config/kamus.php';

$lang = $_SESSION['lang'] ?? 'id';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Ambil ID dari URL
$id = $_GET['id'] ?? '';

if (empty($id)) {
    die($kamus[$lang]['edit_notfound']);
}

// Ambil data peminjaman berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM peminjaman WHERE id = :id");
$stmt->execute([':id' => $id]);
$peminjaman = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$peminjaman) {
    die($kamus[$lang]['edit_notfound']);
}

$daftar_ruangan = $pdo->query("SELECT * FROM ruangan ORDER BY nama_ruangan ASC")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
    $ruangan_id  = $_POST['ruangan_id'];
    $tanggal     = $_POST['tanggal'];
    $jam_mulai   = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $keperluan   = htmlspecialchars($_POST['keperluan']);

    try {
        $sql = "UPDATE peminjaman 
                SET ruangan_id = :ruangan_id, tanggal = :tanggal, 
                    jam_mulai = :jam_mulai, jam_selesai = :jam_selesai, 
                    keperluan = :keperluan 
                WHERE id = :id";
        
        $stmt_update = $pdo->prepare($sql);
        $stmt_update->execute([
            ':ruangan_id' => $ruangan_id,
            ':tanggal'    => $tanggal,
            ':jam_mulai'  => $jam_mulai,
            ':jam_selesai'=> $jam_selesai,
            ':keperluan'  => $keperluan,
            ':id'         => $id
        ]);

        header("Location: dashboard.php");
        exit;
    } catch (PDOException $e) {
        die("Gagal memperbarui data: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $kamus[$lang]['edit_title'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 600px; margin-top: 40px;">
        <div class="card">
            <div class="header-flex">
                <h2><?= $kamus[$lang]['edit_title'] ?></h2>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 13px;">
                        <a href="proses/ganti_bahasa.php?lang=id" style="color: #0284c7; text-decoration: none; font-weight: bold;">ID</a> |
                        <a href="proses/ganti_bahasa.php?lang=en" style="color: #0284c7; text-decoration: none; font-weight: bold;">EN</a>
                    </span>
                    <a href="dashboard.php" class="btn btn-secondary"><?= $kamus[$lang]['edit_cancel'] ?></a>
                </div>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label><?= $kamus[$lang]['edit_room'] ?></label>
                    <select name="ruangan_id" class="form-control" required>
                        <?php foreach ($daftar_ruangan as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= ($r['id'] == $peminjaman['ruangan_id']) ? 'selected' : '' ?>>
                                <?= $r['nama_ruangan'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><?= $kamus[$lang]['edit_date'] ?></label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $peminjaman['tanggal'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label><?= $kamus[$lang]['edit_start'] ?></label>
                    <input type="time" name="jam_mulai" class="form-control" value="<?= $peminjaman['jam_mulai'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label><?= $kamus[$lang]['edit_end'] ?></label>
                    <input type="time" name="jam_selesai" class="form-control" value="<?= $peminjaman['jam_selesai'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label><?= $kamus[$lang]['edit_purpose'] ?></label>
                    <textarea name="keperluan" class="form-control" rows="3" required><?= $peminjaman['keperluan'] ?></textarea>
                </div>

                <button type="submit" name="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                    <?= $kamus[$lang]['edit_submit'] ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html>