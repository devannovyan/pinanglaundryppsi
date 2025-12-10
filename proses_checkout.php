<?php
session_start();
// PERUBAHAN: Memperbaiki path ke file database
require_once 'config/database.php';

// Redirect jika bukan metode POST atau keranjang kosong
if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SESSION['keranjang'])) {
    header('Location: layanan.php');
    exit();
}

// Ambil data dari form
$nama_pelanggan = $_POST['nama_pelanggan'];
$no_telepon = $_POST['no_telepon'];
$nomor_laci = $_POST['nomor_laci'];
$metode_pembayaran = $_POST['metode_pembayaran'];
$data_berat = $_POST['berat'];
$data_catatan = $_POST['catatan'];
$keranjang = $_SESSION['keranjang'];

// Mulai transaksi database
$conn->begin_transaction();

try {
    // PERUBAHAN: Generate Kode Pesanan Unik
    $tanggal_prefix = 'PNGL-' . date('Ymd') . '-';
    $query_kode = "SELECT MAX(kode_pesanan) as max_kode FROM pesanan WHERE kode_pesanan LIKE ?";
    $stmt_kode = $conn->prepare($query_kode);
    $param_kode = $tanggal_prefix . '%';
    $stmt_kode->bind_param("s", $param_kode);
    $stmt_kode->execute();
    $result_kode = $stmt_kode->get_result()->fetch_assoc();
    $max_kode = $result_kode['max_kode'];

    if ($max_kode) {
        $urutan = (int) substr($max_kode, -3);
        $urutan++;
    } else {
        $urutan = 1;
    }
    $kode_pesanan_baru = $tanggal_prefix . sprintf('%03d', $urutan);
    $stmt_kode->close();


    // 1. Cek atau buat data pelanggan
    $id_pelanggan = null;
    $stmt = $conn->prepare("SELECT id_pelanggan FROM pelanggan WHERE no_telepon = ?");
    $stmt->bind_param("s", $no_telepon);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $id_pelanggan = $row['id_pelanggan'];
        // Update nama jika berbeda dan terakhir pesan
        $stmt_update = $conn->prepare("UPDATE pelanggan SET nama_pelanggan = ?, terakhir_pesan = NOW() WHERE id_pelanggan = ?");
        $stmt_update->bind_param("si", $nama_pelanggan, $id_pelanggan);
        $stmt_update->execute();
    } else {
        // Buat pelanggan baru jika tidak ada
        $stmt_insert = $conn->prepare("INSERT INTO pelanggan (nama_pelanggan, no_telepon, terakhir_pesan) VALUES (?, ?, NOW())");
        $stmt_insert->bind_param("ss", $nama_pelanggan, $no_telepon);
        $stmt_insert->execute();
        $id_pelanggan = $conn->insert_id;
    }
    $stmt->close();

    // 2. Hitung total biaya di server untuk keamanan
    $total_biaya = 0;
    $query_harga = "SELECT id_layanan, harga_reguler_kg, harga_express_kg FROM layanan";
    $result_harga = $conn->query($query_harga);
    $daftar_harga = [];
    while ($row = $result_harga->fetch_assoc()) {
        $daftar_harga[$row['id_layanan']] = [
            'Reguler' => $row['harga_reguler_kg'],
            'Express' => $row['harga_express_kg']
        ];
    }

    foreach ($keranjang as $id_layanan => $item) {
        foreach ($item['detail_kantong'] as $index => $kantong) {
            $berat = (float)($data_berat[$id_layanan][$index] ?? 0);
            $harga_per_kg = $daftar_harga[$id_layanan][$kantong['tipe']] ?? 0;
            $total_biaya += $berat * $harga_per_kg;
        }
    }

    // 3. Masukkan data ke tabel pesanan (dengan kode pesanan baru)
    $stmt = $conn->prepare("INSERT INTO pesanan (kode_pesanan, id_pelanggan, tanggal_masuk, total_biaya, metode_pembayaran, nomor_laci, status_pesanan) VALUES (?, ?, NOW(), ?, ?, ?, 'Baru')");
    $stmt->bind_param("sidss", $kode_pesanan_baru, $id_pelanggan, $total_biaya, $metode_pembayaran, $nomor_laci);
    $stmt->execute();
    $id_pesanan_baru = $conn->insert_id;

    // 4. Masukkan setiap item ke tabel detail_pesanan
    $stmt = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_layanan, tipe_layanan, berat_kg, catatan, status) VALUES (?, ?, ?, ?, ?, 'Baru')");
    foreach ($keranjang as $id_layanan => $item) {
        foreach ($item['detail_kantong'] as $index => $kantong) {
            $berat = (float)($data_berat[$id_layanan][$index] ?? 0);
            $catatan = $data_catatan[$id_layanan][$index] ?? '';
            $tipe = $kantong['tipe'];
            
            if ($berat > 0) {
                $stmt->bind_param("iisds", $id_pesanan_baru, $id_layanan, $tipe, $berat, $catatan);
                $stmt->execute();
            }
        }
    }

    // Jika semua berhasil, commit transaksi
    $conn->commit();

    // Kosongkan keranjang dan arahkan ke halaman sukses
    unset($_SESSION['keranjang']);
    $_SESSION['pesanan_sukses'] = "Pesanan Anda dengan ID #{$id_pesanan_baru} (Kode: {$kode_pesanan_baru}) berhasil dibuat!";
    header('Location: index.php'); // Arahkan ke halaman utama atau halaman sukses
    exit();

} catch (mysqli_sql_exception $exception) {
    // Jika ada error, batalkan semua perubahan
    $conn->rollback();
    
    // Tampilkan pesan error atau redirect ke halaman error
    // Untuk development, bisa tampilkan errornya:
    echo "Terjadi kesalahan: " . $exception->getMessage();
    // Untuk production, redirect dengan pesan umum:
    // $_SESSION['pesanan_error'] = "Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi.";
    // header('Location: checkout.php');
    exit();
}
?>
