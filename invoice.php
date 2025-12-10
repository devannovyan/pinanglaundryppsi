<?php
require_once 'config/database.php';

// Pastikan kode pesanan ada di URL
if (!isset($_GET['kode'])) {
    die('Kode pesanan tidak ditemukan.');
}

$kode_pesanan = $_GET['kode'];

// --- Ambil Data Pesanan Utama ---
$stmt_pesanan = $conn->prepare("
    SELECT 
        p.id_pesanan, p.kode_pesanan, p.tanggal_masuk, p.total_biaya, p.metode_pembayaran, p.status_pesanan,
        pl.nama_pelanggan, pl.no_telepon
    FROM pesanan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    WHERE p.kode_pesanan = ?
");
$stmt_pesanan->bind_param("s", $kode_pesanan);
$stmt_pesanan->execute();
$result_pesanan = $stmt_pesanan->get_result();
$pesanan = $result_pesanan->fetch_assoc();
$stmt_pesanan->close();

if (!$pesanan) {
    die('Invoice tidak ditemukan.');
}

// --- Ambil Rincian Item Pesanan ---
$stmt_items = $conn->prepare("
    SELECT 
        dp.tipe_layanan, dp.berat_kg, 
        l.nama_layanan, l.harga_reguler_kg, l.harga_express_kg
    FROM detail_pesanan dp
    JOIN layanan l ON dp.id_layanan = l.id_layanan
    WHERE dp.id_pesanan = ?
");
$stmt_items->bind_param("i", $pesanan['id_pesanan']);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= htmlspecialchars($pesanan['kode_pesanan']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .invoice-container { max-width: 800px; margin: auto; }
        .invoice-box {
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
            background: #fff;
        }
        .invoice-header { text-align: center; margin-bottom: 40px; }
        .invoice-header h1 { font-weight: 700; color: #0d6efd; }
        .invoice-details table { width: 100%; line-height: inherit; text-align: left; }
        .invoice-details table td { padding: 5px; vertical-align: top; }
        .invoice-body table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .invoice-body table th, .invoice-body table td { padding: 10px; border-bottom: 1px solid #eee; }
        .invoice-body table th { background: #f8f9fa; font-weight: 600; }
        .invoice-total { text-align: right; margin-top: 20px; font-size: 1.2rem; font-weight: 700; }
        
        /* Style untuk tombol aksi agar tidak ikut ter-download */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container my-5">
        <div class="invoice-box" id="invoice-to-download">
            <div class="invoice-header">
                <h1>INVOICE</h1>
                <p>Pinang Laundry</p>
            </div>

            <div class="invoice-details mb-4">
                <table>
                    <tr>
                        <td>
                            <strong>Kepada:</strong><br>
                            <?= htmlspecialchars($pesanan['nama_pelanggan']) ?><br>
                            <?= htmlspecialchars($pesanan['no_telepon']) ?>
                        </td>
                        <td style="text-align: right;">
                            <strong>Kode Pesanan:</strong> #<?= htmlspecialchars($pesanan['kode_pesanan']) ?><br>
                            <strong>Tanggal Pesan:</strong> <?= date('d M Y', strtotime($pesanan['tanggal_masuk'])) ?><br>
                            <strong>Metode Pembayaran:</strong> <?= htmlspecialchars($pesanan['metode_pembayaran']) ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="invoice-body">
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Layanan</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Berat</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = $items_result->fetch_assoc()): 
                            // Hitung subtotal di sini
                            $harga_satuan = ($item['tipe_layanan'] == 'Express') ? $item['harga_express_kg'] : $item['harga_reguler_kg'];
                            $subtotal = $item['berat_kg'] * $harga_satuan;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nama_layanan']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($item['tipe_layanan']) ?></td>
                            <td class="text-center"><?= htmlspecialchars((float)$item['berat_kg']) ?> kg</td>
                            <td class="text-end">Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <!-- PERUBAHAN: Total Biaya dipindahkan ke dalam tfoot -->
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end border-top pt-3">Total Biaya:</td>
                            <td class="text-end border-top pt-3">Rp<?= number_format($pesanan['total_biaya'], 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- PERUBAHAN: div invoice-total dihapus dari sini -->
            <hr>
            <div class="text-center text-muted">
                <p>Terima kasih telah menggunakan layanan kami!</p>
            </div>
        </div>
        
        <!-- Tombol Download -->
        <div class="text-center mt-4 no-print">
            <button id="download-btn" class="btn btn-success"><i class="bi bi-download me-2"></i>Download Invoice</button>
        </div>
    </div>

    <!-- Library untuk membuat PDF dari HTML -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
    document.getElementById('download-btn').addEventListener('click', function() {
        const invoiceElement = document.getElementById('invoice-to-download');
        const kodePesanan = "<?= htmlspecialchars($pesanan['kode_pesanan']) ?>";
        
        const options = {
            margin:       0.5,
            filename:     'Invoice-' + kodePesanan + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        // Gunakan html2pdf untuk membuat dan mengunduh PDF
        html2pdf().from(invoiceElement).set(options).save();
    });
    </script>
</body>
</html>
