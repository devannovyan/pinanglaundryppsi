<?php
require_once 'partials/header.php';
require_once '../config/database.php';

// ===================================================================================
// REVISI 1: Logika untuk Filter dan Paginasi berdasarkan Pencarian
// ===================================================================================

// Ambil nilai filter dari URL
$search_keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$search_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// Pengaturan Paginasi
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Bangun query dasar
$base_query = "
    FROM pesanan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
";
$where_clauses = [];
$params = [];
$types = '';

// Tambahkan kondisi filter jika ada input
if (!empty($search_keyword)) {
    $where_clauses[] = "(pl.nama_pelanggan LIKE ? OR p.kode_pesanan LIKE ?)";
    $keyword_param = "%" . $search_keyword . "%";
    $params[] = $keyword_param;
    $params[] = $keyword_param;
    $types .= 'ss';
}

if (!empty($search_date)) {
    $where_clauses[] = "DATE(p.tanggal_masuk) = ?";
    $params[] = $search_date;
    $types .= 's';
}

// Gabungkan semua kondisi WHERE
$where_sql = '';
if (count($where_clauses) > 0) {
    $where_sql = " WHERE " . implode(' AND ', $where_clauses);
}

// Query untuk menghitung total data dengan filter
$query_total = "SELECT COUNT(*) as total " . $base_query . $where_sql;
$stmt_total = $conn->prepare($query_total);
if (!empty($params)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_rows = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
$stmt_total->close();

// Query untuk mengambil data pesanan dengan filter dan paginasi
$query_pesanan = "
    SELECT p.id_pesanan, p.kode_pesanan, pl.nama_pelanggan, p.tanggal_masuk, p.total_biaya
    " . $base_query . $where_sql . "
    ORDER BY p.tanggal_masuk DESC
    LIMIT ? OFFSET ?
";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($query_pesanan);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result_pesanan = $stmt->get_result();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Kelola Pesanan Self Ordering</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Kelola Pesanan Self Ordering</li>
    </ol>

    <!-- Notifikasi -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header">
            <i class="bi bi-receipt me-1"></i>
            Daftar Pesanan
        </div>
        <div class="card-body">
            <!-- =================================================================================== -->
            <!-- REVISI 2: Form Filter/Pencarian dengan Tombol Reset -->
            <!-- =================================================================================== -->
            <form action="kelola_pesanan.php" method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari Nama atau Kode Pesanan..." value="<?= htmlspecialchars($search_keyword) ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($search_date) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                    <div class="col-md-2">
                        <a href="kelola_pesanan.php" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Pesanan</th>
                            <th>Nama Pelanggan</th>
                            <th>Tanggal & Jam Masuk</th>
                            <th>Total Biaya</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_pesanan->num_rows > 0): ?>
                            <?php while($row = $result_pesanan->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['kode_pesanan']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                    <td><?= date('d M Y, H:i', strtotime($row['tanggal_masuk'])) ?></td>
                                    <td>Rp<?= number_format($row['total_biaya'], 0, ',', '.') ?></td>
                                    <td>
                                        <a href="detail_pesanan.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#hapusPesananModal"
                                                data-id="<?= $row['id_pesanan'] ?>"
                                                data-kode="<?= htmlspecialchars($row['kode_pesanan']) ?>"
                                                title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Data tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- =================================================================================== -->
            <!-- REVISI 3: Paginasi yang disesuaikan dengan filter -->
            <!-- =================================================================================== -->
            <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-end mt-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0">
                        <?php 
                            // Menambahkan parameter filter ke link paginasi
                            $query_string = http_build_query(array_merge($_GET, ['page' => '']));
                        ?>
                        <!-- Tombol First -->
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">First</a>
                        </li>

                        <!-- Tombol Previous -->
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <!-- Nomor Halaman -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Tombol Next -->
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>

                        <!-- Tombol Last -->
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>">Last</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal Hapus Pesanan (tidak ada perubahan) -->
<div class="modal fade" id="hapusPesananModal" tabindex="-1" aria-labelledby="hapusPesananModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hapusPesananModalLabel">Konfirmasi Hapus Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Anda yakin ingin menghapus pesanan dengan kode <strong id="kodePesananHapus"></strong>? Tindakan ini akan menghapus semua data terkait dan tidak dapat dibatalkan.</p>
      </div>
      <div class="modal-footer">
        <form action="proses_hapus_pesanan.php" method="POST" id="hapusForm">
            <input type="hidden" name="id_pesanan" id="hapus_id_pesanan">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
require_once 'partials/footer.php';
?>

<!-- JavaScript untuk Modal Hapus (tidak ada perubahan) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hapusPesananModal = document.getElementById('hapusPesananModal');
    if (hapusPesananModal) {
        hapusPesananModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const kode = button.getAttribute('data-kode');
            const idInput = hapusPesananModal.querySelector('#hapus_id_pesanan');
            const kodeSpan = hapusPesananModal.querySelector('#kodePesananHapus');
            idInput.value = id;
            kodeSpan.textContent = kode;
        });
    }
});
</script>

<?php
// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>
