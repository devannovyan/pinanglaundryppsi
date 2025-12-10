<?php
// Memulai sesi di setiap halaman admin
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Melindungi halaman: Cek apakah admin sudah login.
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("location: login.php");
    exit;
}

// Mengambil nama file saat ini untuk menandai menu aktif di sidebar
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard - Admin Laundry</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- ================================================================= -->
    <!-- INTERNAL CSS - Semua gaya CSS telah diperbaiki di sini -->
    <!-- ================================================================= -->
    <style>
        /* === Base Body & Font === */
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f8f9fa;
        }

        /* === Top Navigation Bar (Fixed) === */
        .sb-topnav {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1039;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
            height: 56px;
        }
        .sb-topnav .navbar-brand {
            width: 225px;
            padding-left: 1rem;
            font-weight: 600;
            color: #212529;
        }
        .sb-topnav .navbar-brand .bi-water {
            font-size: 1.5rem;
            color: #0d6efd;
            vertical-align: middle;
        }

        /* === [PERUBAHAN] Menyembunyikan Tombol Toggle Sidebar === */
        .sb-topnav #sidebarToggle {
            display: none; /* Tombol garis tiga akan hilang */
        }

        /* === Main Layout Container === */
        #layoutSidenav {
            position: relative;
        }

        /* === Sidebar Navigation (Fixed & Full Height) === */
        #layoutSidenav_nav {
            position: fixed;
            top: 56px; /* Positioned below the topnav */
            left: 0;
            bottom: 0; /* Ensures it stretches to the bottom */
            width: 225px;
            z-index: 1038;
            background: #212529;
            transition: transform 0.3s ease-in-out;
        }

        /* === Main Content Area === */
        #layoutSidenav_content {
            position: relative;
            margin-top: 56px; /* Pushed down below topnav */
            margin-left: 225px; /* Pushed right for sidebar */
            transition: margin-left 0.3s ease-in-out;
        }
        
        /* === Sidebar Toggled State (FIXED) === */
        body.sb-sidenav-toggled #layoutSidenav_nav {
            transform: translateX(-225px); /* Slides sidebar out of view */
        }

        body.sb-sidenav-toggled #layoutSidenav_content {
            margin-left: 0; /* Content area expands to full width */
        }

        /* === Sidebar Menu Styling === */
        .sb-sidenav-dark {
            color: rgba(255, 255, 255, 0.5);
        }
        .sb-sidenav-menu {
            overflow-y: auto; /* Allows scrolling if menu is long */
            height: 100%;
        }
        .sb-sidenav-menu .nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.6);
            transition: color 0.2s, background-color 0.2s;
        }
        .sb-sidenav-menu .nav .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.05);
        }
        .sb-sidenav-menu .nav .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }
        .sb-sidenav-menu .nav .nav-link .sb-nav-link-icon {
            font-size: 1.1rem;
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        .sb-sidenav-menu-heading {
            padding: 1.75rem 1rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
        }
        .sb-sidenav-footer {
            padding: 0.75rem;
            font-size: 0.8rem;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="sb-topnav navbar navbar-expand">
        <!-- Navbar Brand-->
        <span class="navbar-brand"><i class="bi bi-water me-2"></i>Admin Laundry</span>
        <!-- Sidebar Toggle Button-->
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#!"><i class="bi bi-list"></i></button>
    </nav>
    
    <!-- Layout with Sidebar -->
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav flex-column">
                        <div class="sb-sidenav-menu-heading">Utama</div>
                        <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">
                            <div class="sb-nav-link-icon"><i class="bi bi-speedometer2"></i></div>
                            Dashboard
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Manajemen Data</div>
                        <a class="nav-link <?= $current_page == 'kelola_pesanan.php' ? 'active' : '' ?>" href="kelola_pesanan.php">
                            <div class="sb-nav-link-icon"><i class="bi bi-journal-text"></i></div>
                            Kelola Pesanan
                        </a>
                        <a class="nav-link <?= $current_page == 'kelola_pelanggan.php' ? 'active' : '' ?>" href="kelola_pelanggan.php">
                            <div class="sb-nav-link-icon"><i class="bi bi-people-fill"></i></div>
                            Kelola Pelanggan
                        </a>
                        <a class="nav-link <?= $current_page == 'kelola_layanan.php' ? 'active' : '' ?>" href="kelola_layanan.php">
                            <div class="sb-nav-link-icon"><i class="bi bi-tags-fill"></i></div>
                            Kelola Layanan
                        </a>
                        <a class="nav-link <?= $current_page == 'laporan_pesanan.php' ? 'active' : '' ?>" href="laporan_pesanan.php">
                            <div class="sb-nav-link-icon"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
                            Laporan
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin_user'); ?>
                   <!-- PERUBAHAN: Tombol logout memicu modal -->
                   <a class="btn btn-danger btn-sm w-100 mt-2" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
                 
            </nav>
        </div>
        
        <div id="layoutSidenav_content">
            <main class="container-fluid p-4">
                <!-- KONTEN HALAMAN ANDA AKAN MUNCUL DI SINI. -->

    <!-- =================================================================================== -->
    <!-- TAMBAHAN: HTML untuk Modal Logout -->
    <!-- =================================================================================== -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Anda yakin ingin keluar dari sesi ini?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <a href="logout.php" class="btn btn-danger">Ya, Keluar</a>
          </div>
        </div>
      </div>
    </div>

    <!-- =============================================================================== -->
    <!-- JAVASCRIPT LOADER                                                               -->
    <!-- =============================================================================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- ================================================================= -->
    <!-- INTERNAL JAVASCRIPT - Skrip untuk sidebar ditempatkan di sini -->
    <!-- ================================================================= -->
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            // Mengambil elemen tombol untuk toggle sidebar
            const sidebarToggle = document.body.querySelector('#sidebarToggle');

            if (sidebarToggle) {
                // Listener ini akan dijalankan saat tombol sidebar diklik.
                sidebarToggle.addEventListener('click', event => {
                    // Mencegah perilaku default dari link/tombol
                    event.preventDefault();
                    
                    // Menambah atau menghapus kelas 'sb-sidenav-toggled' pada body.
                    document.body.classList.toggle('sb-sidenav-toggled');
                    
                    // Menyimpan status sidebar (terbuka/tertutup) di localStorage.
                    localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
                });
            }
        });
    </script>
    
</body>
</html>
