<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ids_detail_string = $_POST['ids_detail'];
    $status_item_baru = $_POST['status_item'];
    $id_pesanan = $_POST['id_pesanan'];

    if (empty($ids_detail_string) || empty($status_item_baru) || empty($id_pesanan)) {
        header('Location: kelola_pesanan.php');
        exit();
    }

    $conn->begin_transaction();

    try {
        // 1. Update status untuk item-item yang dipilih
        $ids_array = array_map('intval', explode(',', $ids_detail_string));
        $placeholders = implode(',', array_fill(0, count($ids_array), '?'));
        $stmt_update_items = $conn->prepare("UPDATE detail_pesanan SET status = ? WHERE id_detail_pesanan IN ($placeholders)");
        $types = 's' . str_repeat('i', count($ids_array));
        $params = array_merge([$status_item_baru], $ids_array);
        $stmt_update_items->bind_param($types, ...$params);
        $stmt_update_items->execute();
        $stmt_update_items->close();

        // 2. Ambil semua status item dari pesanan yang sama untuk menentukan status utama
        $stmt_check = $conn->prepare("SELECT status FROM detail_pesanan WHERE id_pesanan = ?");
        $stmt_check->bind_param("i", $id_pesanan);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        $semua_status_item = [];
        while ($row = $result_check->fetch_assoc()) {
            $semua_status_item[] = $row['status'];
        }
        $stmt_check->close();

        // 3. Tentukan status pesanan utama berdasarkan status semua itemnya
        $status_pesanan_utama = 'Diambil'; // Default jika semua sudah diambil
        if (in_array('Baru', $semua_status_item)) {
            $status_pesanan_utama = 'Baru';
        } elseif (in_array('Diproses', $semua_status_item)) {
            $status_pesanan_utama = 'Diproses';
        } elseif (in_array('Selesai', $semua_status_item)) {
            $status_pesanan_utama = 'Selesai';
        }

        // 4. Update status di tabel pesanan utama
        $stmt_update_main = $conn->prepare("UPDATE pesanan SET status_pesanan = ? WHERE id_pesanan = ?");
        $stmt_update_main->bind_param("si", $status_pesanan_utama, $id_pesanan);
        $stmt_update_main->execute();
        $stmt_update_main->close();

        // Jika semua berhasil, commit transaksi
        $conn->commit();

    } catch (Exception $e) {
        // Jika ada error, batalkan semua perubahan
        $conn->rollback();
        // Anda bisa menambahkan logging error di sini
    }

    // Redirect kembali ke halaman detail pesanan
    header('Location: detail_pesanan.php?id=' . $id_pesanan);
    exit();

} else {
    header('Location: kelola_pesanan.php');
    exit();
}
?>
