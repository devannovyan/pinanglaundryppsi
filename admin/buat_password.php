<?php
// Masukkan password baru yang Anda inginkan di sini
$password_baru = 'admin123';

// Mengenkripsi password menggunakan standar keamanan PHP modern
$hash_password = password_hash($password_baru, PASSWORD_DEFAULT);

// Menampilkan hasil enkripsi
echo "Password baru Anda adalah: " . htmlspecialchars($password_baru) . "<br><br>";
echo "Salin teks di bawah ini dan tempel ke kolom 'password' di phpMyAdmin:<br><br>";
echo "<strong>" . htmlspecialchars($hash_password) . "</strong>";
?>
