<?php
// PERUBAHAN: Logika pemrosesan formulir dipindahkan ke paling atas
session_start();
require_once '../config/database.php';

// --- Proses CRUD untuk Layanan ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Aksi Tambah
    if ($action == 'create') {
        $nama_layanan = htmlspecialchars(trim($_POST['nama_layanan']));
        $deskripsi = htmlspecialchars(trim($_POST['deskripsi']));
        $harga_reguler_kg = filter_input(INPUT_POST, 'harga_reguler_kg', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $harga_express_kg = filter_input(INPUT_POST, 'harga_express_kg', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $stmt = $conn->prepare("INSERT INTO layanan (nama_layanan, deskripsi, harga_reguler_kg, harga_express_kg) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdd", $nama_layanan, $deskripsi, $harga_reguler_kg, $harga_express_kg);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Layanan baru berhasil ditambahkan.";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan layanan.";
        }
        $stmt->close();
    }
    // Aksi Update
    if ($action == 'update') {
        $id_layanan = filter_input(INPUT_POST, 'id_layanan', FILTER_SANITIZE_NUMBER_INT);
        $nama_layanan = htmlspecialchars(trim($_POST['nama_layanan']));
        $deskripsi = htmlspecialchars(trim($_POST['deskripsi']));
        $harga_reguler_kg = filter_input(INPUT_POST, 'harga_reguler_kg', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $harga_express_kg = filter_input(INPUT_POST, 'harga_express_kg', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $stmt = $conn->prepare("UPDATE layanan SET nama_layanan = ?, deskripsi = ?, harga_reguler_kg = ?, harga_express_kg = ? WHERE id_layanan = ?");
        $stmt->bind_param("ssddi", $nama_layanan, $deskripsi, $harga_reguler_kg, $harga_express_kg, $id_layanan);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Data layanan berhasil diperbarui.";
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui data layanan.";
        }
        $stmt->close();
    }
    // Aksi Hapus
    if ($action == 'delete') {
        $id_layanan = filter_input(INPUT_POST, 'id_layanan', FILTER_SANITIZE_NUMBER_INT);
        $stmt = $conn->prepare("DELETE FROM layanan WHERE id_layanan = ?");
        $stmt->bind_param("i", $id_layanan);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Layanan berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus layanan, mungkin terkait dengan pesanan.";
        }
        $stmt->close();
    }
    header("Location: kelola_layanan.php");
    exit();
}

// Sertakan header setelah semua logika PHP selesai
require_once 'partials/header.php';

// Ambil data layanan untuk ditampilkan
$result_layanan = $conn->query("SELECT * FROM layanan ORDER BY id_layanan ASC");
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Kelola Layanan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Kelola Layanan Self Ordering</li>
    </ol>

    <!-- Notifikasi -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-tags-fill me-2"></i>Daftar Layanan</span>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLayananModal">
                <i class="bi bi-plus-circle"></i> Tambah Layanan
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Layanan</th>
                            <th>Harga Reguler</th>
                            <th>Harga Express</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result_layanan->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_layanan']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($row['deskripsi']) ?></small>
                                </td>
                                <td>Rp<?= number_format($row['harga_reguler_kg'], 0, ',', '.') ?>/kg</td>
                                <td>Rp<?= number_format($row['harga_express_kg'], 0, ',', '.') ?>/kg</td>
                                <td>
                                    <button class="btn btn-info btn-sm edit-btn" 
                                        data-bs-toggle="modal" data-bs-target="#editLayananModal"
                                        data-id="<?= $row['id_layanan'] ?>"
                                        data-nama="<?= htmlspecialchars($row['nama_layanan']) ?>"
                                        data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>"
                                        data-harga-reguler="<?= $row['harga_reguler_kg'] ?>"
                                        data-harga-express="<?= $row['harga_express_kg'] ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <!-- PERUBAHAN: Tombol Hapus memicu modal -->
                                    <button class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#hapusLayananModal"
                                        data-id="<?= $row['id_layanan'] ?>"
                                        data-nama="<?= htmlspecialchars($row['nama_layanan']) ?>">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addLayananModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="kelola_layanan.php" method="POST">
                <div class="modal-header"><h5 class="modal-title">Tambah Layanan Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3"><label class="form-label">Nama Layanan</label><input type="text" name="nama_layanan" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Harga Reguler (/kg)</label><input type="number" name="harga_reguler_kg" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Harga Express (/kg)</label><input type="number" name="harga_express_kg" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Edit -->
<div class="modal fade" id="editLayananModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="kelola_layanan.php" method="POST">
                <div class="modal-header"><h5 class="modal-title">Edit Layanan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_layanan" id="edit-id">
                    <div class="mb-3"><label class="form-label">Nama Layanan</label><input type="text" name="nama_layanan" id="edit-nama" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="deskripsi" id="edit-deskripsi" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Harga Reguler (/kg)</label><input type="number" name="harga_reguler_kg" id="edit-harga-reguler" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Harga Express (/kg)</label><input type="number" name="harga_express_kg" id="edit-harga-express" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
            </form>
        </div>
    </div>
</div>
<!-- PERUBAHAN: Modal Hapus disesuaikan -->
<div class="modal fade" id="hapusLayananModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="kelola_layanan.php" method="POST">
                <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_layanan" id="hapus-id">
                    <p>Anda yakin ingin menghapus layanan <strong id="hapus-nama"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Ya, Hapus</button></div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Script untuk modal edit
    const editModal = document.getElementById('editLayananModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit-id').value = button.getAttribute('data-id');
        document.getElementById('edit-nama').value = button.getAttribute('data-nama');
        document.getElementById('edit-deskripsi').value = button.getAttribute('data-deskripsi');
        document.getElementById('edit-harga-reguler').value = button.getAttribute('data-harga-reguler');
        document.getElementById('edit-harga-express').value = button.getAttribute('data-harga-express');
    });
    // PERUBAHAN: Script untuk modal hapus disesuaikan
    const hapusModal = document.getElementById('hapusLayananModal');
    hapusModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('hapus-id').value = button.getAttribute('data-id');
        document.getElementById('hapus-nama').textContent = button.getAttribute('data-nama');
    });
});
</script>
<?php $conn->close(); ?>
