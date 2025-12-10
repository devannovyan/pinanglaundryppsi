<?php
// Memulai sesi di awal file
session_start();

// Menyertakan file koneksi database
require_once '../config/database.php';

/**
 * Memastikan skrip hanya berjalan jika ada data yang dikirim melalui metode POST.
 * Ini mencegah akses langsung ke file ini melalui URL.
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Ambil dan sanitasi input dari form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi sederhana: pastikan input tidak kosong
    if (empty($username) || empty($password)) {
        // Jika kosong, siapkan pesan error dan kembalikan ke halaman login
        $_SESSION['error_message'] = "Username dan password tidak boleh kosong.";
        header("Location: login.php");
        exit;
    }

    // 2. Siapkan query untuk mencari admin berdasarkan username
    // Menggunakan prepared statement untuk mencegah SQL Injection
    $sql = "SELECT id_admin, username, password, nama_lengkap FROM admin WHERE username = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind variabel ke prepared statement sebagai parameter
        $stmt->bind_param("s", $username);
        
        // Eksekusi statement
        if ($stmt->execute()) {
            // Simpan hasil query
            $stmt->store_result();
            
            // Cek apakah username ditemukan (jumlah baris > 0)
            if ($stmt->num_rows == 1) {
                // Bind hasil query ke variabel
                $stmt->bind_result($id_admin, $db_username, $hashed_password, $nama_lengkap);
                
                if ($stmt->fetch()) {
                    // 3. Verifikasi password
                    // Membandingkan password yang diinput dengan hash di database
                    if (password_verify($password, $hashed_password)) {
                        
                        // 4. Jika password benar, buat sesi untuk admin
                        session_regenerate_id(true); // Mencegah session fixation
                        
                        $_SESSION["admin_logged_in"] = true;
                        $_SESSION["admin_id"] = $id_admin;
                        $_SESSION["admin_username"] = $db_username;
                        $_SESSION["admin_nama"] = $nama_lengkap;
                        
                        // 5. Arahkan ke halaman dashboard admin
                        header("Location: index.php");
                        exit();
                        
                    } else {
                        // Jika password salah
                        $_SESSION['error_message'] = "Username atau password salah.";
                        header("Location: login.php");
                        exit;
                    }
                }
            } else {
                // Jika username tidak ditemukan
                $_SESSION['error_message'] = "Username atau password salah.";
                header("Location: login.php");
                exit;
            }
        } else {
            // Jika eksekusi query gagal
            $_SESSION['error_message'] = "Terjadi kesalahan. Silakan coba lagi.";
            header("Location: login.php");
            exit;
        }
        
        // Tutup statement
        $stmt->close();
    }
    
    // Tutup koneksi
    $conn->close();

} else {
    // Jika file diakses tanpa metode POST, redirect ke halaman login
    header("Location: login.php");
    exit;
}
?>
