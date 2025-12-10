<?php
/**
 * Skrip untuk proses logout admin.
 * File ini akan menghancurkan sesi yang sedang aktif dan mengarahkan
 * pengguna kembali ke halaman login.
 */

// 1. Memulai sesi
// Ini wajib dilakukan sebelum bisa mengakses atau memanipulasi data sesi.
session_start();

// 2. Menghapus semua variabel sesi (unset all session variables)
// Mengosongkan array $_SESSION untuk memastikan tidak ada data login yang tersisa.
$_SESSION = array();

// 3. Menghancurkan sesi (destroy the session)
// Menghapus semua data yang terkait dengan sesi saat ini di server.
session_destroy();

// 4. Mengarahkan ke halaman login (redirect to login page)
// Setelah sesi dihancurkan, pengguna akan dikembalikan ke halaman login
// untuk mencegah akses tidak sah ke halaman admin.
header("location: login.php");
exit;
?>
