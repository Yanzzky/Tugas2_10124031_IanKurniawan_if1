<?php
// File: dashboard.php
session_start();
require './config/koneksi.php';
require './config/kamus.php';

// Memeriksa Session untuk proteksi otentikasi
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$lang = $_SESSION['lang'] ?? 'id';

// Modul Read: Mengambil data gabungan dengan JOIN
$sql = "SELECT p.*, r.nama_ruangan, u.username 
        FROM peminjaman p 
        JOIN ruangan r ON p.ruangan_id = r.id 
        JOIN users u ON p.user_id = u.id 
        ORDER BY p.tanggal DESC";
$stmt = $pdo->query($sql);
$data_peminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $kamus[$lang]['dashboard'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            
            <!-- Header Flex -->
            <div class="header-flex">
                <div>
                    <h2><?= $kamus[$lang]['welcome'] ?></h2>
                    <p style="color: #64748b; margin-top: 4px;">User: <b><?= htmlspecialchars($_SESSION['username']) ?></b></p>
                </div>
                <div class="lang-switch">
                    <a href="./proses/ganti_bahasa.php?lang=id">ID</a> | 
                    <a href="./proses/ganti_bahasa.php?lang=en">EN</a> | 
                    <a href="./proses/logout.php" style="color: #ef4444;"><?= $kamus[$lang]['logout'] ?></a>
                </div>
            </div>

            <!-- Tombol Tambah Peminjaman -->
            <div style="margin-bottom: 20px;">
                <a href="tambah_peminjam.php" class="btn btn-primary"><?= $kamus[$lang]['add_btn'] ?></a>
            </div>

            <!-- Tabel Data Peminjaman (Tepat 8 Kolom) -->
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 14%;">Peminjam</th>
                        <th style="width: 16%;"><?= $kamus[$lang]['room'] ?></th>
                        <th style="width: 12%;"><?= $kamus[$lang]['date'] ?></th>
                        <th style="width: 14%;"><?= $kamus[$lang]['time'] ?></th>
                        <th><?= $kamus[$lang]['purpose'] ?></th>
                        <th style="width: 10%;"><?= $kamus[$lang]['proof'] ?></th>
                        <th style="width: 14%; text-align: center;"><?= $kamus[$lang]['action'] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_peminjaman)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #64748b; padding: 20px;">
                            Belum ada data peminjaman.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $no = 1; foreach ($data_peminjaman as $row): ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['nama_ruangan']) ?></td>
                        <td style="white-space: nowrap;"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td style="white-space: nowrap;"><?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?></td>
                        <td><?= htmlspecialchars($row['keperluan']) ?></td>
                        <td>
                            <?php if (!empty($row['file_syarat'])): ?>
                                <a href="uploads/<?= $row['file_syarat'] ?>" target="_blank" style="color: #0284c7; font-weight: 600;">Lihat File</a>
                            <?php else: ?>
                                <span style="color: #94a3b8;">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($row['user_id'] == $_SESSION['user_id']): ?>
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                    <a href="edit_peminjaman.php?id=<?= $row['id'] ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px; text-decoration: none;">
                                        <?= $kamus[$lang]['edit'] ?>
                                    </a>
                                    <a href="./proses/hapus_peminajm.php?id=<?= $row['id'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px; text-decoration: none; border-radius: 4px;" onclick="return confirm('Yakin ingin menghapus data ini?');">
                                        <?= $kamus[$lang]['delete'] ?>
                                    </a>
                                </div>
                            <?php else: ?>
                                <span style="color: #cbd5e1; font-size: 14px;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>
</html>