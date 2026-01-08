<?php
require_once 'partials/header.php';
require_once '../config/database.php';

// ===================================================================================
// REVISI: Logika Filter dikembalikan ke filter rentang tanggal
// ===================================================================================

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$sql_where = "";
$params = [];
$types = "";

if (!empty($start_date) && !empty($end_date)) {
    // Tambah satu hari ke end_date untuk membuatnya inklusif saat membandingkan
    $end_date_inclusive = date('Y-m-d', strtotime($end_date . ' +1 day'));
    $sql_where = " WHERE p.tanggal_masuk >= ? AND p.tanggal_masuk < ?";
    $params = [$start_date, $end_date_inclusive];
    $types = "ss";
}

// --- Ambil Data Pesanan Sesuai Filter ---
$query_laporan = "

-- ================= PESANAN =================
SELECT 
    p.kode_pesanan AS kode,
    p.tanggal_masuk AS tanggal,
    p.status_pesanan AS status,
    p.total_biaya AS total,
    pl.nama_pelanggan AS pelanggan,
    'Pesanan' AS sumber
FROM pesanan p
JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
$sql_where

UNION ALL

-- ================= BOOKING =================
SELECT 
    b.kode_booking AS kode,
    b.tanggal_masuk AS tanggal,
    b.status_booking AS status,
    b.total_biaya AS total,
    pl.nama_pelanggan AS pelanggan,
    'Booking' AS sumber
FROM booking b
JOIN pelanggan pl ON b.id_pelanggan = pl.id_pelanggan
" . ($sql_where ? str_replace('p.', 'b.', $sql_where) : "") . "

ORDER BY tanggal DESC
";


$stmt = $conn->prepare($query_laporan);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result_laporan = $stmt->get_result();

// --- Hitung Ringkasan ---
$total_pendapatan = 0;
$total_pesanan = $result_laporan->num_rows;
$data_laporan = [];
while($row = $result_laporan->fetch_assoc()){
    $total_pendapatan += $row['total'];
    $data_laporan[] = $row;
}
$stmt->close();
?>
<style>
    /* CSS untuk mengatur tampilan saat mencetak */
    @media print {
        body {
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .no-print, 
        .sb-topnav, 
        #layoutSidenav_nav {
            display: none !important;
        }
        #layoutSidenav_content {
            padding-left: 0 !important;
            margin-left: 0 !important;
            top: 0 !important;
        }
        main {
            padding: 0 !important;
        }
        .report-container {
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
        }
        .card-body .row.mb-4 {
            display: none; /* Sembunyikan ringkasan saat cetak */
        }
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4 no-print">Laporan</h1>
    <ol class="breadcrumb mb-4 no-print">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan</li>
    </ol>

    <!-- Report Content -->
    <div class="card shadow mb-4 report-container">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-file-earmark-bar-graph-fill me-1"></i>
                Laporan Pesanan
            </div>
            <button onclick="window.print()" class="btn btn-success btn-sm no-print"><i class="bi bi-printer"></i> Cetak Laporan</button>
        </div>
        <div class="card-body">
            <!-- =================================================================================== -->
            <!-- REVISI: Form Filter dikembalikan ke filter rentang tanggal -->
            <!-- =================================================================================== -->
            <form action="laporan_pesanan.php" method="GET" class="mb-4 p-3 border rounded bg-light no-print">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" name="start_date" id="start_date" value="<?= htmlspecialchars($start_date) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="end_date" id="end_date" value="<?= htmlspecialchars($end_date) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                    <div class="col-md-2">
                        <a href="laporan_pesanan.php" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Ringkasan -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            Total Pesanan: <strong><?= $total_pesanan ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            Total Pendapatan: <strong>Rp<?= number_format($total_pendapatan, 0, ',', '.') ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Laporan -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Status</th>
                            <th>Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_pesanan > 0): ?>
                            <?php foreach($data_laporan as $row): ?>
                                <tr>
                                    <td><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['kode']) ?></td>
                                    <td><?= htmlspecialchars($row['pelanggan']) ?></td>
                                    <td><?= htmlspecialchars($row['status']) ?></td>
                                    <td class="text-end">Rp<?= number_format($row['total'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">Tidak ada data untuk periode yang dipilih.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="4" class="text-end">Total Keseluruhan:</td>
                            <td class="text-end">Rp<?= number_format($total_pendapatan, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
