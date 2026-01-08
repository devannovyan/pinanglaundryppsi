<?php
// Sertakan header admin
require_once 'partials/header.php';
// Sertakan file koneksi database
require_once '../config/database.php';

// --- Query untuk Statistik Dashboard ---

// Menghitung jumlah pesanan dengan status 'Baru'
$query_pesanan_baru = "SELECT COUNT(id_pesanan) AS total FROM pesanan WHERE status_pesanan = 'Baru'";
$result_pesanan_baru = $conn->query($query_pesanan_baru);
$total_pesanan_baru = $result_pesanan_baru->fetch_assoc()['total'];

// Menghitung jumlah pesanan dengan status 'Diproses'
$query_pesanan_proses = "SELECT COUNT(id_pesanan) AS total FROM pesanan WHERE status_pesanan = 'Diproses'";
$result_pesanan_proses = $conn->query($query_pesanan_proses);
$total_pesanan_proses = $result_pesanan_proses->fetch_assoc()['total'];

// Menghitung total jumlah pelanggan
$query_pelanggan = "SELECT COUNT(id_pelanggan) AS total FROM pelanggan";
$result_pelanggan = $conn->query($query_pelanggan);
$total_pelanggan = $result_pelanggan->fetch_assoc()['total'];

// PERUBAHAN: Query pendapatan yang lebih akurat
// Menjumlahkan total_biaya dari pesanan yang statusnya 'Selesai' atau 'Diambil'
$query_pendapatan = "
    SELECT SUM(total) AS total_pendapatan
    FROM (
        SELECT total_biaya AS total
        FROM pesanan
        WHERE status_pesanan IN ('Selesai', 'Diambil')

        UNION ALL

        SELECT total_biaya AS total
        FROM booking
    ) AS gabungan
";

$result_pendapatan = $conn->query($query_pendapatan);
$row = $result_pendapatan->fetch_assoc();

$total_pendapatan = isset($row['total_pendapatan']) ? $row['total_pendapatan'] : 0;




// --- Query untuk Pesanan Terbaru ---
$query_pesanan_terbaru = "
    SELECT p.kode_pesanan, pl.nama_pelanggan, p.tanggal_masuk, p.status_pesanan, p.total_biaya
    FROM pesanan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    ORDER BY p.tanggal_masuk DESC, p.id_pesanan DESC
    LIMIT 10
";
$result_pesanan_terbaru = $conn->query($query_pesanan_terbaru);
?>

<!-- Style kustom untuk dashboard -->
<style>
    .card-dashboard {
        border-left-width: 0.25rem;
        border-radius: 0.5rem;
    }
    .card-dashboard .card-body {
        padding: 1.25rem;
    }
    .card-dashboard .card-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }
    .card-dashboard .card-text {
        font-size: 1.75rem;
        font-weight: 700;
    }
    .card-dashboard .icon-circle {
        font-size: 2.5rem;
        color: rgba(0, 0, 0, 0.15);
    }
    .border-primary { border-left-color: #4e73df !important; }
    .border-warning { border-left-color: #f6c23e !important; }
    .border-success { border-left-color: #1cc88a !important; }
    .border-info   { border-left-color: #36b9cc !important; }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Ringkasan Informasi</li>
    </ol>

    <!-- Kartu Statistik -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card card-dashboard border-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-title text-primary">Pesanan Baru</div>
                            <div class="card-text"><?= $total_pesanan_baru ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-inbox-fill icon-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card card-dashboard border-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-title text-warning">Pesanan Diproses</div>
                            <div class="card-text"><?= $total_pesanan_proses ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split icon-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card card-dashboard border-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-title text-success">Jumlah Pelanggan</div>
                            <div class="card-text"><?= $total_pelanggan ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill icon-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card card-dashboard border-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="card-title text-info">Total Pendapatan</div>
                            <div class="card-text">Rp<?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack icon-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pesanan Terbaru -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <i class="bi bi-table me-1"></i>
            10 Pesanan Terbaru
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Pesanan</th>
                            <th>Nama Pelanggan</th>
                            <th>Tanggal Masuk</th>
                            <th>Total Biaya</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_pesanan_terbaru->num_rows > 0): ?>
                            <?php while($row = $result_pesanan_terbaru->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['kode_pesanan']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                    <td><?= date('d M Y', strtotime($row['tanggal_masuk'])) ?></td>
                                    <td><?= $row['total_biaya'] ? 'Rp' . number_format($row['total_biaya'], 0, ',', '.') : 'Menunggu' ?></td>
                                    <td>
                                        <?php 
                                            $status = htmlspecialchars($row['status_pesanan']);
                                            $badge_class = 'bg-secondary';
                                            if ($status == 'Baru') $badge_class = 'bg-primary';
                                            if ($status == 'Diproses') $badge_class = 'bg-warning text-dark';
                                            if ($status == 'Selesai') $badge_class = 'bg-success';
                                            if ($status == 'Diambil') $badge_class = 'bg-dark';
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= $status ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada pesanan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Sertakan footer admin
require_once 'partials/footer.php';
// Tutup koneksi database
$conn->close();
?>
