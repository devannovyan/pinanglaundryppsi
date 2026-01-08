<?php
// PERUBAHAN: Logika pemrosesan formulir dipindahkan ke paling atas
session_start();
require_once '../config/database.php';

// --- Proses CRUD untuk Layanan ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Aksi Tambah
    if ($action == 'create') {
        $nama_mesin = htmlspecialchars(trim($_POST['name']));

        $stmt = $conn->prepare("INSERT INTO layanan (nama_layanan, deskripsi, harga_reguler_kg, harga_express_kg) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdd", $name);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Mesin baru berhasil ditambahkan.";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan Mesin.";
        }
        $stmt->close();
    }
    
    // Aksi Hapus
    if ($action == 'delete') {
        $id_mesin = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $stmt = $conn->prepare("DELETE FROM machines WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Mesin berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus mesin, mungkin terkait dengan pesanan.";
        }
        $stmt->close();
    }
    header("Location: layanan_self_service.php");
    exit();
}

// Sertakan header setelah semua logika PHP selesai
require_once 'partials/header.php';

// Ambil data layanan untuk ditampilkan
$result_layanan = $conn->query("SELECT * FROM machines ORDER BY id ASC");
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Kelola Layanan Self Service</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Kelola Layanan Self Service</li>
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
            <span><i class="bi bi-tags-fill me-2"></i>Daftar Mesin</span>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLayananModal">
                <i class="bi bi-plus-circle"></i> Tambah Mesin
            </button>
        </div>
        <div class="row">
        <?php while($row = $result_layanan->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100 position-relative">

                    <!-- TOMBOL HAPUS DI CARD -->
                    <button 
                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                        data-bs-toggle="modal"
                        data-bs-target="#hapusLayananModal"
                        data-id="<?= $row['id'] ?>"
                        data-nama="<?= htmlspecialchars($row['name']) ?>">
                        <i class="bi bi-trash"></i>
                    </button>

                    <div class="card-body text-center">
                        <i class="bi bi-washing-machine fs-1 text-primary mb-3"></i>

                        <h5 class="card-title fw-bold">
                            <?= htmlspecialchars($row['name']) ?>
                        </h5>

                        <span class="badge 
                            <?= $row['status'] == 'Tersedia' ? 'bg-success' : 
                                ($row['status'] == 'Dipesan' ? 'bg-warning text-dark' : 'bg-danger') ?>">
                            <?= $row['status'] ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
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
                    <div class="mb-3"><label class="form-label">Nomor Mesin</label><input type="text" name="nama_layanan" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- PERUBAHAN: Modal Hapus disesuaikan -->
<div class="modal fade" id="hapusLayananModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="kelola_layanan.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Mesin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_layanan" id="hapus-id">
                    <p>
                        Yakin mau hapus mesin
                        <strong id="hapus-nama"></strong>?
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php require_once 'partials/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
document.addEventListener('DOMContentLoaded', function () {
    const hapusModal = document.getElementById('hapusLayananModal');

    hapusModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        document.getElementById('hapus-id').value =
            button.getAttribute('data-id');

        document.getElementById('hapus-nama').textContent =
            button.getAttribute('data-nama');
       });
    });
});
</script>
<?php $conn->close(); ?>
