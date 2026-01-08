<?php
require_once '../config/database.php';

if (isset($_POST['gunakan'])) {
    $kode_booking = $_POST['kode_booking'];

    // 1. ambil machine_id dari booking
    $stmt = $conn->prepare(
        "SELECT machine_id FROM booking WHERE kode_booking = ?"
    );
    $stmt->bind_param("s", $kode_booking);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $machine_id = $result['machine_id'];
    $stmt->close();

    // 2. update status booking
    $stmtUpdate = $conn->prepare(
        "UPDATE booking 
         SET status_booking = 'Digunakan' 
         WHERE kode_booking = ?"
    );
    $stmtUpdate->bind_param("s", $kode_booking);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    // 3. panggil api_start_use.php
    $ch = curl_init('http://localhost/pinanglaundry/api_start_use.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'machine_id' => $machine_id
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    header('Location: pesanan_self_service.php');
    exit;
}


require_once 'partials/header.php';

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$where = "WHERE 1=1";
$params = [];
$types = "";

// filter keyword
if ($keyword != '') {
    $where .= " AND (p.nama_pelanggan LIKE ? OR b.kode_booking LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $types .= "ss";
}

// filter tanggal
if ($tanggal != '') {
    $where .= " AND DATE(b.tanggal_masuk) = ?";
    $params[] = $tanggal;
    $types .= "s";
}

// ================= TOTAL =================
$sql_total = "
SELECT COUNT(*) total
FROM booking b
JOIN pelanggan p ON b.id_pelanggan = p.id_pelanggan
JOIN machines m ON b.machine_id = m.id
$where
";

$stmt_total = $conn->prepare($sql_total);
if (!$stmt_total) die($conn->error);
if ($params) $stmt_total->bind_param($types, ...$params);
$stmt_total->execute();
$total_rows = $stmt_total->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
$stmt_total->close();

// ================= DATA =================
$sql = "
SELECT 
    b.id_booking,
    b.kode_booking,
    p.nama_pelanggan,
    m.name,
    b.tanggal_masuk,
    b.total_biaya,
    b.status_booking
FROM booking b
JOIN pelanggan p ON b.id_pelanggan = p.id_pelanggan
JOIN machines m ON b.machine_id = m.id
$where
ORDER BY b.tanggal_masuk DESC
LIMIT ? OFFSET ?
";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if (!$stmt) die($conn->error);

$stmt->bind_param($types, ...$params);
$stmt->execute();
$data = $stmt->get_result();
?>

<div class="container-fluid px-4">
<h1 class="mt-4">Kelola Booking Self Service</h1>

<div class="card shadow mb-4">
<div class="card-body">

<!-- ================= FILTER ================= -->
<form method="GET" class="mb-3">
    <div class="row g-2">
        <div class="col-md-4">
            <input type="text" name="keyword" class="form-control"
                   placeholder="Nama / Kode Booking"
                   value="<?= htmlspecialchars($keyword) ?>">
        </div>
        <div class="col-md-4">
            <input type="date" name="tanggal" class="form-control"
                   value="<?= htmlspecialchars($tanggal) ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Cari</button>
        </div>
        <div class="col-md-2">
            <a href="kelola_booking.php" class="btn btn-secondary w-100">Reset</a>
        </div>
    </div>
</form>

<!-- ================= TABLE ================= -->
<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead class="table-light">
<tr>
    <th>Kode Booking</th>
    <th>Nama Pelanggan</th>
    <th>Mesin</th>
    <th>Tanggal & Jam</th>
    <th>Total Biaya</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>

<?php if ($data->num_rows > 0): ?>
<?php while ($row = $data->fetch_assoc()): ?>
<tr>
    <td><?= $row['kode_booking'] ?></td>
    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= date('d M Y H:i', strtotime($row['tanggal_masuk'])) ?></td>
    <td>Rp<?= number_format($row['total_biaya'],0,',','.') ?></td>
    <td><?= htmlspecialchars($row['status_booking']) ?></td>
    <td class="text-center">
    <?php if ($row['status_booking'] == 'Cancel'): ?>

        <button class="btn btn-sm btn-danger" disabled>
            Canceled
        </button>

    <?php elseif ($row['status_booking'] == 'Digunakan' || $row['status_booking'] == 'Selesai'): ?>

        <span class="badge bg-secondary">Sudah Digunakan</span>

    <?php else: ?>

        <form method="post" style="display:inline;">
            <input type="hidden" name="kode_booking"
                   value="<?= $row['kode_booking'] ?>">
            <button type="submit"
                    name="gunakan"
                    class="btn btn-sm btn-success"
                    onclick="return confirm('Yakin booking ini digunakan?')">
                Digunakan
            </button>
        </form>

    <?php endif; ?>
</td>

</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="7" class="text-center">Data tidak ada</td>
</tr>
<?php endif; ?>

</tbody>
</table>
</div>

<!-- ================= PAGINATION ================= -->
<?php if ($total_pages > 1): ?>
<nav>
<ul class="pagination justify-content-end">
<?php for($i=1;$i<=$total_pages;$i++): ?>
<li class="page-item <?= $page==$i?'active':'' ?>">
    <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$i])) ?>">
        <?= $i ?>
    </a>
</li>
<?php endfor; ?>
</ul>
</nav>
<?php endif; ?>

</div>
</div>
</div>

<?php
$stmt->close();
$conn->close();
require_once 'partials/footer.php';
?>
