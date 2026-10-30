<?php
// File: hapus_peminjaman.php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Hapus data dari database menggunakan Prepared Statement[cite: 90]
        $sql = "DELETE FROM peminjaman WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        header("Location: ../dashboard.php");
        exit;
    } catch (PDOException $e) {
        die("Gagal menghapus data: " . $e->getMessage());
    }
}
?>