<?php
/**
 * File Konfigurasi Database
 *
 * File ini berisi pengaturan untuk koneksi ke database MySQL.
 * Ganti nilai-nilai di bawah ini sesuai dengan konfigurasi server Anda.
 */

// --- Pengaturan Database ---

// Host database, biasanya 'localhost' atau '127.0.0.1'
define('DB_HOST', 'localhost');

// Username untuk mengakses database
define('DB_USER', 'root');

// Password untuk username database (kosongkan jika tidak ada)
define('DB_PASS', '');

// Nama database yang akan digunakan
define('DB_NAME', 'laundry_db');

// --- Membuat Koneksi ke Database ---

/**
 * Membuat objek koneksi menggunakan MySQLi.
 * @var mysqli $conn
 */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// --- Memeriksa Koneksi ---

/**
 * Memeriksa apakah koneksi berhasil atau gagal.
 * Jika gagal, skrip akan berhenti dan menampilkan pesan error.
 * Ini penting untuk memastikan aplikasi tidak berjalan dengan koneksi yang rusak.
 */
if ($conn->connect_error) {
    // Menghentikan eksekusi dan menampilkan pesan error yang jelas
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

/**
 * Mengatur character set ke utf8mb4 untuk mendukung berbagai macam karakter,
 * termasuk emoji. Ini adalah praktik terbaik untuk aplikasi web modern.
 */
if (!$conn->set_charset("utf8mb4")) {
    // Menampilkan pesan jika gagal mengatur charset, namun tidak menghentikan skrip
    printf("Error loading character set utf8mb4: %s\n", $conn->error);
}

// --- Obyek koneksi $conn sekarang siap digunakan di file lain ---
?>
