<?php
// File: tambah_peminjaman.php
session_start();
require './config/koneksi.php';
require './config/kamus.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$lang = $_SESSION['lang'] ?? 'id';
$error_list = []; 
$sukses = "";

// Ambil data master ruangan untuk dropdown
$daftar_ruangan = $pdo->query("SELECT * FROM ruangan ORDER BY nama_ruangan ASC")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["submit"])) {

    $ruangan_id  = $_POST["ruangan_id"] ?? '';
    $tanggal     = $_POST["tanggal"] ?? '';
    $jam_mulai   = $_POST["jam_mulai"] ?? '';
    $jam_selesai = $_POST["jam_selesai"] ?? '';
    $keperluan   = htmlspecialchars($_POST["keperluan"] ?? ''); 
    $user_id     = $_SESSION["user_id"];


    if (empty($ruangan_id) || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai) || empty($keperluan)) {
        $error_list[] = $kamus[$lang]['err_required'];
    }

    if (!empty($jam_mulai) && !empty($jam_selesai) && $jam_mulai >= $jam_selesai) {
        $error_list[] = $kamus[$lang]['err_time'];
    }

    if (empty($error_list)) {
        $sql_cek = "SELECT * FROM peminjaman 
                    WHERE ruangan_id = :ruangan_id AND tanggal = :tanggal 
                      AND jam_mulai < :jam_selesai AND jam_selesai > :jam_mulai";
        $stmt_cek = $pdo->prepare($sql_cek);
        $stmt_cek->execute([
            ':ruangan_id' => $ruangan_id,
            ':tanggal'    => $tanggal,
            ':jam_mulai'   => $jam_mulai,
            ':jam_selesai' => $jam_selesai
        ]);
        if (!empty($stmt_cek->fetchAll(PDO::FETCH_ASSOC))) {
            $error_list[] = $kamus[$lang]['err_conflict'];
        }
    }

    $nama_file_baru = "";
    $tujuan = "";
    $tmp_name = "";

    if (isset($_FILES["file_syarat"]) && $_FILES["file_syarat"]["error"] === 0) {
        $nama_asli = $_FILES["file_syarat"]["name"];
        $tipe_file = $_FILES["file_syarat"]["type"];
        $tmp_name  = $_FILES["file_syarat"]["tmp_name"];
        $ukuran    = $_FILES["file_syarat"]["size"];

        $tipe_diizinkan = ["image/jpeg", "image/png"];
        $ukuran_maks    = 2 * 1024 * 1024; 

        if (!in_array($tipe_file, $tipe_diizinkan)) {
            $error_list[] = $kamus[$lang]['err_filetype'];
        } elseif ($ukuran > $ukuran_maks) {
            $error_list[] = $kamus[$lang]['err_filesize'];
        } else {
            $ekstensi = pathinfo($nama_asli, PATHINFO_EXTENSION);
            $nama_file_baru = time() . "." . $ekstensi;
            $tujuan = "./uploads/" . $nama_file_baru;
        }
    } else {
        $error_list[] = $kamus[$lang]['err_file_req'];
    }

    // 4. Proses Simpan Data (Create)[cite: 70, 87]
    if (empty($error_list)) {
        if (move_uploaded_file($tmp_name, $tujuan)) {
            try {
                $sql_insert = "INSERT INTO peminjaman (user_id, ruangan_id, tanggal, jam_mulai, jam_selesai, keperluan, file_syarat) 
                               VALUES (:user_id, :ruangan_id, :tanggal, :jam_mulai, :jam_selesai, :keperluan, :file_syarat)";
                $stmt_insert = $pdo->prepare($sql_insert);
                $stmt_insert->execute([
                    ':user_id'    => $user_id,
                    ':ruangan_id' => $ruangan_id,
                    ':tanggal'    => $tanggal,
                    ':jam_mulai'  => $jam_mulai,
                    ':jam_selesai'=> $jam_selesai,
                    ':keperluan'  => $keperluan,
                    ':file_syarat'=> $nama_file_baru
                ]);
                $sukses = $kamus[$lang]['sukses_msg'];
            } catch (PDOException $e) {
                $error_list[] = $kamus[$lang]['err_db'] . $e->getMessage();
            }
        } else {
            $error_list[] = $kamus[$lang]['err_upload'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $kamus[$lang]['tambah_title'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <div class="card">
            <div class="header-flex">
                <h2><?= $kamus[$lang]['tambah_title'] ?></h2>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="color: #64748b; font-size: 13px;">
                        <a href="./proses/ganti_bahasa.php?lang=id" style="color: #0284c7; text-decoration: none; font-weight: bold;">ID</a> |
                        <a href="./proses/ganti_bahasa.php?lang=en" style="color: #0284c7; text-decoration: none; font-weight: bold;">EN</a>
                    </span>
                    <a href="dashboard.php" class="btn btn-secondary"><?= $kamus[$lang]['tambah_back'] ?></a>
                </div>
            </div>

            <?php if (!empty($sukses)): ?>
                <div class="alert-success"><?= $sukses ?></div>
            <?php endif; ?>

            <?php if (!empty($error_list)): ?>
                <div class="alert-error">
                    <ul style="margin-left: 20px;">
                        <?php foreach ($error_list as $e): ?>
                            <li><?= $e ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Form dengan method POST & enctype multipart[cite: 51, 66] -->
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label><?= $kamus[$lang]['tambah_room'] ?></label>
                    <select name="ruangan_id" class="form-control" required>
                        <option value=""><?= $kamus[$lang]['tambah_room_opt'] ?></option>
                        <?php foreach ($daftar_ruangan as $ruang): ?>
                            <option value="<?= $ruang['id'] ?>"><?= $ruang['nama_ruangan'] ?> (<?= $kamus[$lang]['tambah_cap'] ?>: <?= $ruang['kapasitas'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?= $kamus[$lang]['tambah_date'] ?></label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?= $kamus[$lang]['tambah_start'] ?></label>
                    <input type="time" name="jam_mulai" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?= $kamus[$lang]['tambah_end'] ?></label>
                    <input type="time" name="jam_selesai" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?= $kamus[$lang]['tambah_purpose'] ?></label>
                    <textarea name="keperluan" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label><?= $kamus[$lang]['tambah_file'] ?></label>
                    <input type="file" name="file_syarat" class="form-control" accept="image/png, image/jpeg" required>
                    <small style="color: #64748b;"><?= $kamus[$lang]['tambah_file_hint'] ?></small>
                </div>
                <button type="submit" name="submit" class="btn btn-primary" style="width: 100%;"><?= $kamus[$lang]['tambah_submit'] ?></button>
            </form>
        </div>
    </div>
</body>
</html>