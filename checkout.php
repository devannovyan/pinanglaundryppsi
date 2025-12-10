<?php
session_start();
require_once 'config/database.php';

// Ambil data keranjang dari sesi
$keranjang = $_SESSION['keranjang'] ?? [];

// Jika keranjang kosong, arahkan kembali ke halaman layanan
if (empty($keranjang)) {
    header('Location: layanan.php');
    exit();
}

// Ambil semua data layanan untuk perhitungan harga di sisi client (JavaScript)
$query_layanan = "SELECT id_layanan, harga_reguler_kg, harga_express_kg FROM layanan";
$result_layanan = $conn->query($query_layanan);
$daftar_harga = [];
while ($row = $result_layanan->fetch_assoc()) {
    $daftar_harga[$row['id_layanan']] = [
        'Reguler' => $row['harga_reguler_kg'],
        'Express' => $row['harga_express_kg']
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Laundry Kilat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .checkout-container { background-color: #fff; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,.08); }
        .summary-item { border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1rem; }
        .summary-item:last-child { border-bottom: none; }
        .total-section { font-size: 1.25rem; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container my-5">
        <!-- PERUBAHAN: Header dibuat responsif agar tidak tumpang tindih -->
        <div class="d-sm-flex justify-content-between align-items-center text-center mb-5">
            <!-- Tombol Kembali -->
            <a href="layanan.php" class="btn btn-outline-secondary mb-3 mb-sm-0"><i class="bi bi-arrow-left"></i> Kembali ke Layanan</a>
            
            <!-- Judul dan Deskripsi -->
            <div>
                <h1 class="fw-bold mb-0">Checkout</h1>
                <p class="text-muted mb-0">Selesaikan pesanan Anda dalam beberapa langkah mudah.</p>
            </div>
            
            <!-- Placeholder untuk menyeimbangkan layout -->
            <div class="d-none d-sm-block" style="visibility: hidden;">
                <a href="layanan.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Layanan</a>
            </div>
        </div>

        <form action="proses_checkout.php" method="POST" id="checkout-form">
            <div class="row g-5">
                <!-- Kolom Detail Pesanan & Berat -->
                <div class="col-lg-7">
                    <div class="checkout-container">
                        <h4 class="mb-4 fw-semibold">Detail Pesanan</h4>
                        <?php foreach ($keranjang as $id_layanan => $item): ?>
                            <div class="summary-item">
                                <h5 class="fw-bold"><?= htmlspecialchars($item['nama_layanan']) ?></h5>
                                <?php foreach ($item['detail_kantong'] as $index => $kantong): ?>
                                    <div class="mb-3 p-3 border rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <label class="form-label fw-bold mb-0">Kantong #<?= $index + 1 ?></label>
                                                <span class="badge bg-info-subtle text-info-emphasis rounded-pill"><?= $kantong['tipe'] ?></span>
                                            </div>
                                            <div class="subtotal-display fw-bold text-primary">Rp0</div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="berat_<?= $id_layanan ?>_<?= $index ?>" class="form-label small">Berat (kg)</label>
                                                <input type="number" step="0.1" min="0" class="form-control form-control-sm weight-input" placeholder="Contoh: 2.5" id="berat_<?= $id_layanan ?>_<?= $index ?>" name="berat[<?= $id_layanan ?>][<?= $index ?>]" data-id-layanan="<?= $id_layanan ?>" data-tipe-layanan="<?= $kantong['tipe'] ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Input Catatan (BARU) -->
                                                <label for="catatan_<?= $id_layanan ?>_<?= $index ?>" class="form-label small">Catatan (Opsional)</label>
                                                <input type="text" class="form-control form-control-sm" placeholder="Contoh: Jangan disetrika" id="catatan_<?= $id_layanan ?>_<?= $index ?>" name="catatan[<?= $id_layanan ?>][<?= $index ?>]">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Kolom Data Diri & Pembayaran -->
                <div class="col-lg-5">
                    <div class="checkout-container position-sticky" style="top: 20px;">
                        <h4 class="mb-4 fw-semibold">Data Pelanggan</h4>
                        <div class="mb-3"><label for="nama_pelanggan" class="form-label">Nama Lengkap</label><input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" required></div>
                        <div class="mb-3"><label for="no_telepon" class="form-label">Nomor Telepon (WhatsApp)</label><input type="tel" class="form-control" id="no_telepon" name="no_telepon" required></div>
                        <!-- Input Nomor Laci (BARU) -->
                        <div class="mb-4"><label for="nomor_laci" class="form-label">Alamat</label><input type="text" class="form-control" id="nomor_laci" name="nomor_laci" placeholder="Contoh: jl.pinang no.5" required></div>
                        <hr>
                        <h4 class="mb-4 fw-semibold">Ringkasan Pembayaran</h4>
                        <div class="d-flex justify-content-between mb-3 total-section"><span>Total Biaya</span><span id="grand-total">Rp0</span></div>
                        <h5 class="mb-3 fw-semibold mt-4">Metode Pembayaran</h5>
                        <div class="form-check"><input class="form-check-input payment-method" type="radio" name="metode_pembayaran" id="qris" value="QRIS" checked><label class="form-check-label" for="qris"><i class="bi bi-qr-code-scan"></i> QRIS</label></div>
                        <div class="form-check"><input class="form-check-input payment-method" type="radio" name="metode_pembayaran" id="cash" value="Cash"><label class="form-check-label" for="cash"><i class="bi bi-cash"></i> Cash</label></div>
                        <div id="qris-display" class="mt-3 text-center"><p class="mb-2">Silakan scan untuk membayar:</p><img src="images/qris.JPG" alt="[Gambar QRIS]" class="img-fluid rounded"></div>
                        <div class="d-grid mt-4"><button type="submit" class="btn btn-success btn-lg" id="submit-button"><span class="button-text"><i class="bi bi-check-circle-fill me-2"></i>Buat Pesanan</span><span class="button-spinner spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span></button></div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const daftarHarga = <?= json_encode($daftar_harga) ?>;
            const weightInputs = document.querySelectorAll('.weight-input');
            const grandTotalEl = document.getElementById('grand-total');
            const checkoutForm = document.getElementById('checkout-form');
            const submitButton = document.getElementById('submit-button');
            const paymentMethods = document.querySelectorAll('.payment-method');
            const qrisDisplay = document.getElementById('qris-display');

            function calculateTotal() {
                let grandTotal = 0;
                weightInputs.forEach(input => {
                    const weight = parseFloat(input.value) || 0;
                    const idLayanan = input.dataset.idLayanan;
                    const tipeLayanan = input.dataset.tipeLayanan;
                    const price = parseFloat(daftarHarga[idLayanan]?.[tipeLayanan] || 0);
                    const subtotal = weight * price;
                    grandTotal += subtotal;
                    const subtotalDisplay = input.closest('.border').querySelector('.subtotal-display');
                    subtotalDisplay.textContent = 'Rp' + subtotal.toLocaleString('id-ID');
                });
                grandTotalEl.textContent = 'Rp' + grandTotal.toLocaleString('id-ID');
            }

            function toggleQrisDisplay() {
                if (document.getElementById('qris').checked) {
                    qrisDisplay.style.display = 'block';
                } else {
                    qrisDisplay.style.display = 'none';
                }
            }

            weightInputs.forEach(input => { input.addEventListener('input', calculateTotal); });
            paymentMethods.forEach(method => { method.addEventListener('change', toggleQrisDisplay); });
            checkoutForm.addEventListener('submit', function(e) {
                let totalWeight = 0;
                weightInputs.forEach(input => { totalWeight += parseFloat(input.value) || 0; });
                if (totalWeight <= 0) {
                    e.preventDefault();
                    alert('Harap isi berat minimal pada satu item.');
                    return;
                }
                submitButton.disabled = true;
                submitButton.querySelector('.button-text').textContent = 'Memproses...';
                submitButton.querySelector('.button-spinner').style.display = 'inline-block';
            });

            calculateTotal();
            toggleQrisDisplay();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
