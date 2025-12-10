<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Layanan Kios Modern</title>
    <!-- Memuat Tailwind CSS dari CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Konfigurasi Tailwind untuk font Inter dan palet warna yang sama -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'primary': '#4F46E5', // Warna ungu utama
                        'secondary': '#10B981', // Warna hijau sekunder
                        'background': '#F9FAFB', // Latar belakang abu-abu sangat muda
                    },
                    boxShadow: {
                        '3xl': '0 35px 60px -15px rgba(0, 0, 0, 0.3)',
                        'kiosk': '0 25px 50px -12px rgba(79, 70, 229, 0.25)', // Bayangan khusus untuk kartu
                    }
                }
            }
        }
    </script>
    <style>
        /* Gaya kustom untuk memastikan kartu terlihat bagus */
        .service-card {
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            border-bottom: 8px solid; /* Mengganti border-t-8 menjadi border-b-8 atau ditambahkan */
        }

        .service-card-primary {
            border-color: #4F46E5;
        }

        .service-card-secondary {
            border-color: #10B981;
        }

        .service-card:hover {
            transform: translateY(-10px) scale(1.02); /* Efek lift yang lebih menonjol */
            box-shadow: 0 40px 60px -15px rgba(0, 0, 0, 0.2); /* Bayangan yang lebih dramatis */
        }
        
        .kiosk-button {
             transition: background-color 0.3s, transform 0.1s;
             box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .kiosk-button:active {
            transform: scale(0.98);
        }

        /* Responsif untuk layar sangat lebar (kiosk) */
        @media (min-width: 1024px) {
            .kiosk-container {
                max-width: 1200px;
                padding-top: 6rem;
                padding-bottom: 6rem;
            }
        }
    </style>
</head>
<body class="bg-background font-sans min-h-screen flex items-center justify-center p-4">

    <div class="kiosk-container w-full max-w-5xl mx-auto">

        <!-- Header Utama yang Lebih Menonjol -->
        <header class="text-center mb-16">
            <div class="inline-block p-4 bg-primary text-white rounded-full mb-3 shadow-lg">
                 <!-- Ikon Selamat Datang (Bintang) -->
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.381-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
            <h1 class="text-6xl font-extrabold text-gray-900 mb-4 tracking-tight">Pilih Kebutuhan Anda</h1>
            <p class="text-2xl text-gray-500 font-light">Layanan serba mandiri, cepat, dan mudah.</p>
        </header>

        <!-- Area Pemilihan Layanan (Grid Responsif) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            <!-- Pilihan 1: Self-Ordering (Pesan Sendiri) -->
            <div id="selfOrderingCard" class="service-card service-card-primary bg-white p-10 rounded-2xl shadow-kiosk flex flex-col items-center text-center group">
                <!-- Ikon Pemesanan yang lebih besar -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-primary mb-6 group-hover:text-indigo-600 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-4xl font-bold text-gray-900 mb-3">Self-Ordering</h2>
                <p class="text-lg text-gray-500 mb-8 flex-grow">Akses menu lengkap dan lakukan pemesanan Anda secara mandiri dari awal hingga pembayaran.</p>
                <button onclick="selectService('Self-Ordering')" class="kiosk-button w-full py-4 px-8 bg-primary text-white text-xl font-bold rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-primary focus:ring-opacity-50">
                    Mulai Pesan Sekarang
                </button>
            </div>

            <!-- Pilihan 2: Self-Service (Ambil Pesanan / Cek Status) -->
            <div id="selfServiceCard" class="service-card service-card-secondary bg-white p-10 rounded-2xl shadow-kiosk flex flex-col items-center text-center group">
                <!-- Ikon Layanan yang lebih besar -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-secondary mb-6 group-hover:text-emerald-600 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-4xl font-bold text-gray-900 mb-3">Self-Service</h2>
                <p class="text-lg text-gray-500 mb-8 flex-grow">Cek status pesanan, ambil nomor antrian, atau minta bantuan terkait pesanan yang sudah ada.</p>
                <button onclick="selectService('Self-Service')" class="kiosk-button w-full py-4 px-8 bg-secondary text-white text-xl font-bold rounded-xl hover:bg-emerald-600 focus:outline-none focus:ring-4 focus:ring-secondary focus:ring-opacity-50">
                    Cek Status & Layanan
                </button>
            </div>

        </div>

        <!-- Area Pesan (Modal/Pesan Kustom) - Dipertahankan untuk penggunaan di masa mendatang -->
        <div id="messageBox" class="fixed inset-0 bg-gray-900 bg-opacity-80 backdrop-blur-sm hidden items-center justify-center p-4 z-50 transition-opacity duration-300">
            <div class="bg-white p-8 rounded-xl shadow-2xl max-w-sm w-full text-center transform scale-100 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944c2.816 0 5.518 1.05 7.618 2.944M3 12a9 9 0 0118 0M3 12h18" />
                </svg>
                <p id="messageText" class="text-xl font-semibold text-gray-800 mb-6">Pesan Anda di sini.</p>
                <button onclick="closeMessage()" class="w-full py-3 bg-primary text-white font-medium rounded-lg hover:bg-indigo-700 transition duration-150 shadow-lg">
                    Lanjutkan
                </button>
            </div>
        </div>

    </div>

    <script>
        // Fungsi untuk menangani pemilihan layanan
        function selectService(serviceType) {
            // Langsung arahkan pengguna berdasarkan jenis layanan
            if (serviceType === 'Self-Ordering') {
                window.location.href = 'selfordering.php';
            } else if (serviceType === 'Self-Service') {
                window.location.href = 'selfservice.php';
            }
            // Catatan: Logika messageBox (modal) dihilangkan
            // agar pengguna langsung diarahkan ke halaman yang dituju sesuai permintaan.
        }

        // Fungsi untuk menutup kotak pesan (masih dipertahankan, meskipun tidak dipanggil dari selectService)
        function closeMessage() {
            const messageBox = document.getElementById('messageBox');
            messageBox.classList.remove('flex');
            messageBox.classList.add('hidden');
        }

        // Pastikan kotak pesan dapat ditutup dengan klik di luar
        document.getElementById('messageBox').addEventListener('click', function(event) {
            if (event.target.id === 'messageBox') {
                closeMessage();
            }
        });
    </script>
</body>
</html>