<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pelanggan = $_POST['id_pelanggan'];

    if (empty($id_pelanggan)) {
        $_SESSION['error_message'] = "ID Pelanggan tidak valid.";
        header('Location: kelola_pelanggan.php');
        exit();
    }

    // Sebaiknya tambahkan pengecekan apakah pelanggan memiliki transaksi aktif sebelum menghapus
    // Untuk saat ini, kita langsung hapus
    
    $stmt = $conn->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
    $stmt->bind_param("i", $id_pelanggan);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Data pelanggan berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus data pelanggan. Pelanggan mungkin terkait dengan data transaksi.";
    }

    $stmt->close();
    $conn->close();
    header('Location: kelola_pelanggan.php');
    exit();

} else {
    header('Location: kelola_pelanggan.php');
    exit();
}
?>
