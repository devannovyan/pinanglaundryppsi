<?php
// Memulai sesi untuk mengakses dan memanipulasi data keranjang
session_start();
require_once 'config/database.php';

// Pastikan permintaan datang dari metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: layanan.php');
    exit();
}

// Ambil aksi yang diminta (misal: 'add', 'update', 'delete')
$action = $_POST['action'] ?? '';

// --- Logika untuk Menambah Item ke Keranjang ---
if ($action === 'add') {
    // Sanitasi input dari form modal
    $id_layanan = filter_input(INPUT_POST, 'id_layanan', FILTER_SANITIZE_NUMBER_INT);
    $jumlah_kantong = filter_input(INPUT_POST, 'jumlah_kantong', FILTER_SANITIZE_NUMBER_INT);
    $tipe_layanan_arr = $_POST['tipe_layanan'] ?? [];

    // Validasi dasar
    if (!$id_layanan || !$jumlah_kantong || count($tipe_layanan_arr) != $jumlah_kantong) {
        // Jika data tidak valid, kembalikan ke halaman layanan
        $_SESSION['error_message'] = "Data tidak lengkap. Silakan coba lagi.";
        header('Location: layanan.php');
        exit();
    }

    // Ambil detail layanan dari database untuk mendapatkan nama
    $stmt = $conn->prepare("SELECT nama_layanan FROM layanan WHERE id_layanan = ?");
    $stmt->bind_param("i", $id_layanan);
    $stmt->execute();
    $result = $stmt->get_result();
    $layanan = $result->fetch_assoc();
    $stmt->close();

    if (!$layanan) {
        $_SESSION['error_message'] = "Layanan tidak ditemukan.";
        header('Location: layanan.php');
        exit();
    }

    // Siapkan detail untuk setiap kantong
    $detail_kantong = [];
    foreach ($tipe_layanan_arr as $tipe) {
        $detail_kantong[] = [
            'tipe' => htmlspecialchars($tipe),
            'berat_kg' => 0 // Berat akan diisi di halaman checkout
        ];
    }

    // Inisialisasi keranjang jika belum ada
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Masukkan atau perbarui item di keranjang
    // Menggunakan id_layanan sebagai kunci agar satu jenis layanan hanya ada satu entri di keranjang
    $_SESSION['keranjang'][$id_layanan] = [
        'id_layanan' => $id_layanan,
        'nama_layanan' => $layanan['nama_layanan'],
        'detail_kantong' => $detail_kantong
    ];
}

// --- Logika untuk Menghapus Item dari Keranjang (opsional, bisa ditambahkan nanti) ---
if ($action === 'delete') {
    $id_layanan = filter_input(INPUT_POST, 'id_layanan', FILTER_SANITIZE_NUMBER_INT);
    if ($id_layanan && isset($_SESSION['keranjang'][$id_layanan])) {
        unset($_SESSION['keranjang'][$id_layanan]);
    }
}

// --- Logika untuk Mengosongkan Keranjang (opsional) ---
if ($action === 'clear') {
    $_SESSION['keranjang'] = [];
}

// Setelah selesai memproses, kembalikan ke halaman layanan
header('Location: layanan.php');
exit();
