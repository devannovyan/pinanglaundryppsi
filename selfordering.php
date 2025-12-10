<?php
// Memulai sesi untuk menampilkan notifikasi pesanan sukses
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Pinang Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .navbar { background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,.1); }
        
        .hero-section { 
            background: linear-gradient(90deg, rgba(10, 49, 97, 0.9) 0%, rgba(65, 131, 215, 0.7) 100%), url('https://images.unsplash.com/photo-1593113646773-9b2f6f9b2b2b?q=80&w=2070&auto=format&fit=crop') no-repeat center center; 
            background-size: cover; 
            color: white; 
            padding: 100px 0; 
            text-align: center; 
        }

        .hero-section h1 { font-weight: 700; font-size: 3.5rem; }
        .hero-section p { font-size: 1.25rem; }
        .section-title { text-align: center; margin-bottom: 4rem; font-weight: 600; }
        .footer { background-color: #343a40; color: white; padding: 20px 0; text-align: center; }
        .service-comparison-card { border: 2px solid #e9ecef; border-radius: 1rem; padding: 2rem; height: 100%; transition: all 0.3s ease; }
        .service-comparison-card.reguler { border-top: 5px solid #0d6efd; }
        .service-comparison-card.express { border-top: 5px solid #dc3545; }
        .service-comparison-card:hover { transform: translateY(-10px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
        .feature-icon { font-size: 3rem; color: #0d6efd; }

        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
            min-width: 300px;
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
        }
        .toast-notification.fade-out {
            opacity: 0;
        }
        
        /* Style untuk slider yang bisa digeser */
        .scrolling-wrapper {
            overflow-x: auto; /* Mengizinkan scroll horizontal */
            cursor: grab; /* Menunjukkan bisa digeser */
            user-select: none; /* Mencegah seleksi teks saat menggeser */
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .scrolling-wrapper::-webkit-scrollbar {
            display: none; /* Chrome, Safari and Opera */
        }

        .scrolling-content {
            display: flex;
            width: 200%; /* Lebar dua kali lipat untuk duplikasi konten */
            animation: scroll 25s linear infinite;
        }
        .feature-item {
            flex: 0 0 25%; /* Setiap item mengambil 25% dari lebar wrapper */
            padding: 1rem;
            text-align: center;
        }
        .feature-item p {
            text-align: center;
        }

        /* Jeda animasi saat di-hover atau disentuh */
        .scrolling-wrapper:hover .scrolling-content {
            animation-play-state: paused;
        }

        @keyframes scroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%); /* Geser sejauh lebar konten asli */
            }
        }

        /* Style untuk section tutorial */
        .tutorial-step {
            text-align: center;
            display: flex;
            flex-direction: column;
            height: 100%; /* Memastikan semua step punya tinggi yang sama */
        }
        .tutorial-icon {
            font-size: 3rem;
            color: #0d6efd;
            background-color: #e7f1ff;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto; /* Icon berada di tengah */
        }
        
        /* Class untuk perataan teks */
        .text-justify {
            text-align: justify;
        }
    </style>
</head>
<body>

    <!-- Menampilkan notifikasi jika ada -->
    <?php if (isset($_SESSION['pesanan_sukses'])): ?>
        <div class="alert alert-success toast-notification" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= $_SESSION['pesanan_sukses'] ?>
        </div>
        <?php unset($_SESSION['pesanan_sukses']); ?>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <span class="navbar-brand fw-bold"><i class="bi bi-water"></i> Pinang Laundry</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="tentang.php">Tentang</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <h1 class="display-4">Bersih, Cepat, dan Profesional</h1>
            <p class="lead">Kami memberikan layanan laundry terbaik untuk pakaian Anda. Pesan sekarang dan nikmati kemudahannya!</p>
        </div>
    </header>

    <!-- Why Choose Us Section -->
   <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Mengapa Memilih Kami?</h2>
            </div>
            <div class="scrolling-wrapper" id="feature-slider">
                <div class="scrolling-content">
                    <!-- Konten Asli -->
                    <div class="feature-item">
                        <i class="bi bi-award-fill feature-icon mb-3"></i>
                        <h5 class="fw-bold">Kualitas Terjamin</h5>
                        <p class="text-muted small">Proses pencucian profesional untuk hasil terbaik.</p>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-clock-history feature-icon mb-3"></i>
                        <h5 class="fw-bold">Tepat Waktu</h5>
                        <p class="text-muted small">Pesanan Anda selesai sesuai estimasi yang dijanjikan.</p>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-shield-check feature-icon mb-3"></i>
                        <h5 class="fw-bold">Aman & Terpercaya</h5>
                        <p class="text-muted small">Kami pastikan tidak ada pakaian yang tertukar atau hilang.</p>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-emoji-smile-fill feature-icon mb-3"></i>
                        <h5 class="fw-bold">Pelayanan Ramah</h5>
                        <p class="text-muted small">Kepuasan Anda adalah prioritas utama kami.</p>
                    </div>
                    <!-- Duplikasi Konten untuk efek loop -->
                    <div class="feature-item">
                        <i class="bi bi-award-fill feature-icon mb-3"></i>
                        <h5 class="fw-bold">Kualitas Terjamin</h5>
                        <p class="text-muted small">Proses pencucian profesional untuk hasil terbaik.</p>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-clock-history feature-icon mb-3"></i>
                        <h5 class="fw-bold">Tepat Waktu</h5>
                        <p class="text-muted small">Pesanan Anda selesai sesuai estimasi yang dijanjikan.</p>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-shield-check feature-icon mb-3"></i>
                        <h5 class="fw-bold">Aman & Terpercaya</h5>
                        <p class="text-muted small">Kami pastikan tidak ada pakaian yang tertukar atau hilang.</p>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-emoji-smile-fill feature-icon mb-3"></i>
                        <h5 class="fw-bold">Pelayanan Ramah</h5>
                        <p class="text-muted small">Kepuasan Anda adalah prioritas utama kami.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tutorial Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Cara Pesan Self-Ordering</h2>
                <p class="text-muted col-lg-7 mx-auto">Ikuti 4 langkah mudah ini untuk menikmati layanan laundry kami yang cepat dan praktis.</p>
            </div>
            <div class="row g-4 justify-content-center align-items-stretch">
                <div class="col-md-3 col-6">
                    <div class="tutorial-step">
                        <div class="tutorial-icon">
                            <i class="bi bi-card-checklist"></i>
                        </div>
                        <h5 class="fw-bold">Pilih Layanan</h5>
                        <p class="text-muted small">Pilih jenis layanan yang Anda butuhkan.</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="tutorial-step">
                        <div class="tutorial-icon">
                            <i class="bi bi-basket3-fill"></i>
                        </div>
                        <h5 class="fw-bold">Input Cucian</h5>
                        <p class="text-muted small">Masukkan detail cucian Anda pada layar.</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="tutorial-step">
                        <div class="tutorial-icon">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <h5 class="fw-bold">Isi Data Diri</h5>
                        <p class="text-muted small">Lengkapi nama dan nomor telepon Anda.</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="tutorial-step">
                        <div class="tutorial-icon">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <h5 class="fw-bold">Selesai & Bayar</h5>
                        <p class="text-muted small">Lakukan pembayaran dan tunggu notifikasi.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="layanan.php" class="btn btn-primary btn-lg">Pesan Layanan Sekarang</a>
            </div>
        </div>
    </section>
    
    <!-- About Us Section -->
    <section id="tentang" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <img src="https://s2.bukalapak.com/bukalapak-kontenz-production/content_attachments/websites/1/91352/original/Laundry.jpeg" class="img-fluid rounded shadow" alt="[Gambar Mesin Cuci]">
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <h2 class="section-title text-start mb-3">Tentang Pinang Laundry</h2>
                    <!-- PERUBAHAN: Menambahkan class text-justify -->
                    <p class="text-muted text-justify">Di Pinang Laundry, kami percaya bahwa pakaian bersih adalah awal dari hari yang produktif. Misi kami adalah menyediakan layanan laundry berkualitas tinggi yang cepat, andal, dan terjangkau bagi semua kalangan. Kami menggunakan deterjen ramah lingkungan dan teknologi modern untuk memastikan setiap helai pakaian Anda kembali bersih, wangi, dan terawat sempurna.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Comparison Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Pilihan Layanan Sesuai Kebutuhan</h2>
                <p class="text-muted">Pilih layanan yang paling sesuai dengan jadwal Anda.</p>
            </div>
            <div class="row g-4 justify-content-center">
                <!-- Reguler Card -->
                <div class="col-lg-5">
                    <div class="service-comparison-card reguler">
                        <h3 class="fw-bold text-primary mb-3">Reguler</h3>
                        <p class="text-muted">Pilihan ideal untuk Anda yang tidak terburu-buru. Kualitas tetap terjaga dengan harga yang lebih ekonomis.</p>
                        <ul class="list-unstyled mt-4">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i><strong>Waktu Pengerjaan:</strong> 2-3 Hari</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i><strong>Harga:</strong> Lebih Terjangkau</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i><strong>Cocok Untuk:</strong> Pakaian sehari-hari</li>
                        </ul>
                    </div>
                </div>
                <!-- Express Card -->
                <div class="col-lg-5">
                     <div class="service-comparison-card express">
                        <h3 class="fw-bold text-danger mb-3">Express</h3>
                        <p class="text-muted">Solusi super cepat untuk kebutuhan mendesak. Pakaian siap dalam waktu 24 jam dengan kualitas yang sama.</p>
                        <ul class="list-unstyled mt-4">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i><strong>Waktu Pengerjaan:</strong> Maks 24 Jam</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i><strong>Prioritas:</strong> Pengerjaan didahulukan</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i><strong>Cocok Untuk:</strong> Acara mendadak</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="kontak">
        <div class="container"><p>&copy; 2025 Pinang Laundry. All Rights Reserved.</p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script untuk menghilangkan notifikasi secara otomatis -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const autoHideAlerts = document.querySelectorAll('.toast-notification');
        autoHideAlerts.forEach(function(alert) {
            setTimeout(function() {
                alert.classList.add('fade-out');
            }, 5000);
            setTimeout(function() {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 5500);
        });

        // JavaScript untuk slider interaktif
        const slider = document.getElementById('feature-slider');
        let isDown = false;
        let startX;
        let scrollLeft;

        const startDragging = (e) => {
            isDown = true;
            slider.style.cursor = 'grabbing';
            startX = (e.pageX || e.touches[0].pageX) - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        };

        const stopDragging = () => {
            isDown = false;
            slider.style.cursor = 'grab';
        };

        const drag = (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = (e.pageX || e.touches[0].pageX) - slider.offsetLeft;
            const walk = (x - startX) * 2; // Kecepatan geser
            slider.scrollLeft = scrollLeft - walk;
        };

        // Mouse Events
        slider.addEventListener('mousedown', startDragging);
        slider.addEventListener('mouseleave', stopDragging);
        slider.addEventListener('mouseup', stopDragging);
        slider.addEventListener('mousemove', drag);

        // Touch Events
        slider.addEventListener('touchstart', startDragging);
        slider.addEventListener('touchend', stopDragging);
        slider.addEventListener('touchmove', drag);
    });
    </script>
</body>
</html>
