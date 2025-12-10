<?php
// Sertakan header admin dan koneksi database
require_once 'partials/header.php';
require_once '../config/database.php';

// Mulai sesi jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tentukan opsi pengurutan
$sort_option = $_GET['sort'] ?? 'id_asc'; // Default sort by ID ascending

$order_by = "id_pelanggan ASC"; // Default
switch ($sort_option) {
    case 'nama_asc':
        $order_by = "nama_pelanggan ASC";
        break;
    case 'nama_desc':
        $order_by = "nama_pelanggan DESC";
        break;
    case 'terbaru':
        $order_by = "terakhir_pesan DESC";
        break;
    case 'terlama':
        $order_by = "terakhir_pesan ASC";
        break;
}

// --- Ambil semua data pelanggan untuk ditampilkan ---
$query_pelanggan = "
    SELECT 
        id_pelanggan, 
        nama_pelanggan, 
        no_telepon, 
        terakhir_pesan
    FROM 
        pelanggan
    ORDER BY 
        $order_by
";
$result_pelanggan = $conn->query($query_pelanggan);
?>

<!-- Style untuk notifikasi toast -->
<style>
.toast-notification {
    position: fixed;
    top: 80px; /* Disesuaikan agar di bawah navbar admin */
    right: 20px;
    z-index: 1055;
    min-width: 250px;
    opacity: 1;
    transition: opacity 0.5s ease-in-out;
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
}

.toast-notification.fade-out {
    opacity: 0;
}
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Kelola Pelanggan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Kelola Pelanggan</li>
    </ol>

    <!-- Notifikasi (diubah menjadi toast) -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success toast-notification" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= $_SESSION['success_message'] ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger toast-notification" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= $_SESSION['error_message'] ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-people-fill me-1"></i>
                Daftar Semua Pelanggan
            </div>
            <!-- Formulir untuk filter -->
            <form action="kelola_pelanggan.php" method="GET" class="d-flex align-items-center">
                <label for="sort" class="form-label me-2 mb-0">Urutkan:</label>
                <select name="sort" id="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="id_asc" <?= $sort_option == 'id_asc' ? 'selected' : '' ?>>ID (Standar)</option>
                    <option value="nama_asc" <?= $sort_option == 'nama_asc' ? 'selected' : '' ?>>Nama (A-Z)</option>
                    <option value="nama_desc" <?= $sort_option == 'nama_desc' ? 'selected' : '' ?>>Nama (Z-A)</option>
                    <option value="terbaru" <?= $sort_option == 'terbaru' ? 'selected' : '' ?>>Terbaru Pesan</option>
                    <option value="terlama" <?= $sort_option == 'terlama' ? 'selected' : '' ?>>Terlama Pesan</option>
                </select>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="datatablesSimple">
                    <thead class="table-light">
                        <tr>
                            <!-- PERUBAHAN: Judul kolom -->
                            <th>ID</th>
                            <th>Nama Pelanggan</th>
                            <th>Nomor Telepon</th>
                            <th>Terakhir Pesan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_pelanggan->num_rows > 0): ?>
                            <?php while($row = $result_pelanggan->fetch_assoc()): ?>
                                <tr>
                                    <!-- PERUBAHAN: Menampilkan ID Pelanggan dari database -->
                                    <td><?= htmlspecialchars($row['id_pelanggan']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                    <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                                    <td><?= date('d M Y, H:i', strtotime($row['terakhir_pesan'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#hapusPelangganModal"
                                                data-id="<?= $row['id_pelanggan'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_pelanggan']) ?>"
                                                title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data pelanggan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- =================================================================================== -->
<!-- TAMBAHAN: HTML untuk Modal Hapus Pelanggan -->
<!-- =================================================================================== -->
<div class="modal fade" id="hapusPelangganModal" tabindex="-1" aria-labelledby="hapusPelangganModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hapusPelangganModalLabel">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Anda yakin ingin menghapus pelanggan <strong id="namaPelangganHapus"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
      </div>
      <div class="modal-footer">
        <form action="proses_hapus_pelanggan.php" method="POST" id="hapusForm">
            <input type="hidden" name="id_pelanggan" id="hapus_id_pelanggan">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Script untuk menghilangkan notifikasi & mengisi modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk menghilangkan notifikasi toast
    const autoHideAlerts = document.querySelectorAll('.toast-notification');
    autoHideAlerts.forEach(function(alert) {
        setTimeout(() => alert.classList.add('fade-out'), 4000);
        setTimeout(() => { if (alert.parentNode) { alert.parentNode.removeChild(alert); } }, 4500);
    });

    // ===============================================================================
    // TAMBAHAN: Script untuk mempopulasi data ke dalam modal hapus
    // ===============================================================================
    const hapusPelangganModal = document.getElementById('hapusPelangganModal');
    if (hapusPelangganModal) {
        hapusPelangganModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget;

            // Ekstrak informasi dari atribut data-*
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');

            // Dapatkan elemen di dalam modal
            const idInput = hapusPelangganModal.querySelector('#hapus_id_pelanggan');
            const namaSpan = hapusPelangganModal.querySelector('#namaPelangganHapus');
            
            // Update nilai-nilai di dalam modal
            idInput.value = id;
            namaSpan.textContent = nama;
        });
    }
});
</script>

<?php
// Sertakan footer admin
require_once 'partials/footer.php';
// Tutup koneksi database
$conn->close();
?>
