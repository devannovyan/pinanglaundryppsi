<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Pinang Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .navbar { background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,.1); }
        
        /* PERUBAHAN: Gradasi dan style disamakan dengan index.php */
        .hero-section { 
            background: linear-gradient(90deg, rgba(10, 49, 97, 0.9) 0%, rgba(65, 131, 215, 0.7) 100%), url('https://images.unsplash.com/photo-1582735689369-389454e21317?q=80&w=1887&auto=format&fit=crop') no-repeat center center; 
            background-size: cover; 
            color: white; 
            padding: 100px 0; 
            text-align: center; 
        }

        .hero-section h1 { font-weight: 700; font-size: 3.5rem; }
        .hero-section p { font-size: 1.25rem; }
        .section-title { text-align: center; margin-bottom: 4rem; font-weight: 600; }
        .footer { background-color: #343a40; color: white; padding: 20px 0; text-align: center; }
        .feature-icon { font-size: 3rem; color: #0d6efd; }
        .map-container {
            position: relative;
            overflow: hidden;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,.07);
        }
        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        .text-justify {
            text-align: justify;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <span class="navbar-brand fw-bold"><i class="bi bi-water"></i> Pinang Laundry</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="tentang.php">Tentang</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <h1 class="display-4">Merawat Pakaian, Menjaga Kepercayaan</h1>
            <!-- PERUBAHAN: Class col-lg-8 dan mx-auto dihapus agar format sama -->
            <p class="lead">Kenali cerita, nilai, dan komitmen kami dalam memberikan layanan perawatan pakaian terbaik untuk Anda.</p>
        </div>
    </header>

    <!-- Kisah Kami Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                 <h2 class="section-title">Kisah Pinang Laundry</h2>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://s2.bukalapak.com/bukalapak-kontenz-production/content_attachments/websites/1/91352/original/Laundry.jpeg" class="img-fluid rounded shadow" alt="[Gambar interior laundry yang bersih dan terorganisir]">
                </div>
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-3" style="color: #0d6efd;">Berawal dari Kebutuhan Sekitar</h3>
                    <p class="text-muted text-justify">Pinang Laundry lahir dari sebuah gagasan sederhana di tengah hiruk pikuk kehidupan perkotaan: menyediakan solusi perawatan pakaian yang tidak hanya bersih, tetapi juga dapat diandalkan dan personal. Kami melihat banyak orang kesulitan menemukan waktu dan layanan laundry yang benar-benar memahami cara merawat berbagai jenis bahan pakaian.</p>
                    <p class="text-muted text-justify">Berdiri sejak tahun 2020 di jantung kota Depok, kami memulai perjalanan dengan satu mesin cuci dan tekad yang kuat. Kini, kami telah tumbuh bersama kepercayaan para pelanggan, terus berinovasi dengan teknologi modern dan deterjen ramah lingkungan untuk memberikan hasil terbaik bagi Anda dan keluarga.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Visi & Misi Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Visi & Misi Kami</h2>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-6">
                    <div class="p-4 bg-white rounded shadow-sm h-100">
                        <i class="bi bi-bullseye feature-icon mb-3"></i>
                        <h4 class="fw-bold">Visi</h4>
                        <p class="text-muted text-justify">Menjadi penyedia jasa laundry paling terpercaya dan inovatif di Depok dan sekitarnya, yang dikenal karena kualitas, integritas, dan kepedulian terhadap pelanggan serta lingkungan.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 bg-white rounded shadow-sm h-100">
                        <i class="bi bi-card-checklist feature-icon mb-3"></i>
                        <h4 class="fw-bold">Misi</h4>
                        <ul class="list-unstyled text-start">
                             <li class="mb-2 text-muted"><i class="bi bi-check-circle-fill text-primary me-2"></i>Memberikan hasil cucian yang bersih maksimal.</li>
                            <li class="mb-2 text-muted"><i class="bi bi-check-circle-fill text-primary me-2"></i>Menawarkan layanan yang cepat dan tepat waktu.</li>
                            <li class="mb-2 text-muted"><i class="bi bi-check-circle-fill text-primary me-2"></i>Membangun hubungan jangka panjang dengan pelanggan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Lokasi Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Temukan Kami</h2>
                <p class="text-muted col-lg-7 mx-auto">Kami siap melayani Anda. Kunjungi outlet kami yang nyaman dan bersih untuk merasakan langsung pelayanan terbaik dari Pinang Laundry.</p>
            </div>
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="map-container mb-4">
                        <!-- Ganti URL src dengan link embed Google Maps Anda -->
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.133373550608!2d106.8317333152796!3d-6.376834995387877!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ec1a99a7858b%3A0x10948952455cb997!2sJl.%20Margonda%20Raya%2C%20Depok%2C%2C%20Kec.%20Beji%2C%20Kota%20Depok%2C%20Jawa%20Barat!5e0!3m2!1sid!2sid!4v1678886543210!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="text-center bg-light p-4 rounded">
                        <h4 class="fw-bold mb-3">Pinang Laundry Depok</h4>
                        <p class="lead mb-2"><i class="bi bi-geo-alt-fill me-2 text-primary"></i>Jl. Margonda Blok Pinang No.2, Pondok Cina, Kecamatan Beji, Kota Depok, Jawa Barat 16424 Indonesia</p>
                        <p class="mb-0"><i class="bi bi-clock-fill me-2 text-primary"></i><strong>Jam Buka:</strong> Setiap Hari, 08:00 - 21:00 WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container"><p>&copy; 2025 Pinang Laundry. All Rights Reserved.</p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
