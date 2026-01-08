<?php
// Memulai sesi untuk mengelola data keranjang
session_start();
require_once 'config/database.php';

// Ambil semua layanan dari database
$query_layanan = "SELECT * FROM layanan";
$result_layanan = $conn->query($query_layanan);

// Ambil data keranjang dari sesi
$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : array();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Layanan Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .navbar { background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,.05); }
        .service-card { border: 1px solid #e0e0e0; border-radius: 0.75rem; transition: all 0.3s ease; background-color: #fff; }
        .service-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,.1); }
        .service-card .card-body { text-align: center; }
        .service-card .service-icon { font-size: 3rem; color: #0d6efd; }
        .cart-container { background-color: #fff; border-radius: 0.75rem; padding: 1.5rem; }

        /* PERUBAHAN: CSS untuk ikon tombol close */
        .btn-close-arrow {
            background: none !important;
            position: relative;
            font-size: 0.9rem; /* Menyesuaikan ukuran dasar agar ikon pas */
        }
        .btn-close-arrow::before {
            /* PERUBAHAN: Mengganti ikon panah kiri menjadi panah kanan */
            content: '\F285'; /* Unicode untuk ikon chevron-right Bootstrap */
            font-family: 'bootstrap-icons';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
    <!-- Navbar (REVISED) -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <span class="navbar-brand fw-bold"><i class="bi bi-water"></i> Pinang Laundry</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="layanan.php">Layanan</a></li>
                </ul>
                <button class="btn btn-primary ms-lg-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas">
                    <i class="bi bi-cart-fill me-1"></i> 
                    Keranjang (<?= count($keranjang) ?>)
                </button>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="mb-4 fw-bold">Pilih Layanan Anda</h2>
                <div class="row g-4">
                    <?php while($layanan = $result_layanan->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card service-card h-100">
                            <div class="card-body p-4 d-flex flex-column">
                                <i class="bi <?= htmlspecialchars($layanan['gambar_icon']) ?> service-icon mb-3"></i>
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($layanan['nama_layanan']) ?></h5>
                                <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars($layanan['deskripsi']) ?></p>
                                <div class="d-flex justify-content-around mt-3">
                                    <div class="text-center">
                                        <small class="text-muted">REGULER</small><br>
                                        <strong>Rp<?= number_format($layanan['harga_reguler_kg'], 0, ',', '.') ?></strong>
                                    </div>
                                    <div class="text-center">
                                        <small class="text-muted">EXPRESS</small><br>
                                        <strong>Rp<?= number_format($layanan['harga_express_kg'], 0, ',', '.') ?></strong>
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary mt-4 w-100" data-bs-toggle="modal" data-bs-target="#addItemModal" data-id="<?= $layanan['id_layanan'] ?>" data-nama="<?= htmlspecialchars($layanan['nama_layanan']) ?>">
                                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- BARU: Offcanvas untuk Keranjang Belanja -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="cartOffcanvasLabel">Keranjang Anda</h5>
            <!-- PERUBAHAN: Tombol close sekarang menggunakan class custom untuk ikon panah kanan -->
            <button type="button" class="btn-close btn-close-arrow" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Isi keranjang yang sebelumnya di sidebar, sekarang di sini -->
            <?php if (empty($keranjang)): ?>
                <p class="text-center text-muted mt-4">Keranjang masih kosong.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($keranjang as $item): ?>
                    <li class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block"><?= htmlspecialchars($item['nama_layanan']) ?></strong>
                                <small class="text-muted"><?= count($item['detail_kantong']) ?> kantong</small> 
                            </div>
                            <form action="proses_keranjang.php" method="POST">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_layanan" value="<?= $item['id_layanan'] ?>">
                                <button type="submit" class="btn-close" aria-label="Hapus"></button>
                            </form>
                        </div>
                        <?php foreach ($item['detail_kantong'] as $index => $kantong): ?>
                            <small class="d-block text-muted ms-2">&bull; Kantong <?= $index + 1 ?>: <?= $kantong['tipe'] ?></small>
                        <?php endforeach; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="d-grid mt-4">
                    <a href="checkout.php" class="btn btn-success btn-lg">Lanjut ke Checkout</a>
                </div>
                 <div class="d-grid mt-2">
                    <form action="proses_keranjang.php" method="POST" class="mb-0">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn btn-outline-danger w-100">Kosongkan Keranjang</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Tambah Item (Tidak ada perubahan di sini) -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="proses_keranjang.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemModalLabel">Tambah Layanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_layanan" id="modal-id-layanan">
                        <input type="hidden" name="action" value="add">
                        <h4 id="modal-nama-layanan" class="fw-bold text-center mb-4"></h4>
                        <div class="mb-3">
                            <label for="jumlah_kantong" class="form-label">Jumlah Kantong</label>
                            <input type="number" class="form-control" id="jumlah_kantong" name="jumlah_kantong" value="1" min="1" required>
                        </div>
                        <div id="kantong-options"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah ke Keranjang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addItemModal = document.getElementById('addItemModal');
            const jumlahKantongInput = document.getElementById('jumlah_kantong');
            const kantongOptionsDiv = document.getElementById('kantong-options');

            function generateKantongOptions(count) {
                kantongOptionsDiv.innerHTML = '';
                for (let i = 1; i <= count; i++) {
                    const div = document.createElement('div');
                    div.classList.add('mb-3', 'p-3', 'border', 'rounded');
                    div.innerHTML = `<label class="form-label fw-bold">Kantong #${i}</label><select class="form-select" name="tipe_layanan[]" required><option value="Reguler">Reguler</option><option value="Express">Express</option></select>`;
                    kantongOptionsDiv.appendChild(div);
                }
            }

            addItemModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('modal-id-layanan').value = button.getAttribute('data-id');
                document.getElementById('modal-nama-layanan').textContent = button.getAttribute('data-nama');
                jumlahKantongInput.value = 1;
                generateKantongOptions(1);
            });

            jumlahKantongInput.addEventListener('input', function() {
                const count = parseInt(this.value) || 0;
                if (count > 0) {
                    generateKantongOptions(count);
                } else {
                    kantongOptionsDiv.innerHTML = '';
                }
            });
        });
    </script>
</body>
</html>
