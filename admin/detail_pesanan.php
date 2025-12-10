<?php
require_once 'partials/header.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: kelola_pesanan.php');
    exit();
}

$id_pesanan = $_GET['id'];

// PERUBAHAN: Menambahkan harga_reguler_kg dan harga_express_kg ke query
$query = "
    SELECT 
        p.id_pesanan, p.kode_pesanan, p.tanggal_masuk, p.status_pesanan, p.nomor_laci, p.metode_pembayaran, p.total_biaya,
        pl.nama_pelanggan, pl.no_telepon,
        dp.id_detail_pesanan, dp.tipe_layanan, dp.berat_kg, dp.catatan, dp.status as status_item,
        l.nama_layanan, l.id_layanan, l.harga_reguler_kg, l.harga_express_kg
    FROM pesanan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN layanan l ON dp.id_layanan = l.id_layanan
    WHERE p.id_pesanan = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);

if (empty($items)) {
    echo "Pesanan tidak ditemukan.";
    exit();
}

// Mengelompokkan item untuk tampilan admin
$grouped_items = [];
foreach ($items as $item) {
    $key = $item['id_layanan'] . '-' . $item['tipe_layanan'];
    if (!isset($grouped_items[$key])) {
        $grouped_items[$key] = [
            'nama_layanan' => $item['nama_layanan'],
            'tipe_layanan' => $item['tipe_layanan'],
            'total_berat' => 0,
            'jumlah_kantong' => 0,
            'status_item' => $item['status_item'],
            'ids_detail' => [],
            'details' => []
        ];
    }
    $grouped_items[$key]['total_berat'] += $item['berat_kg'];
    $grouped_items[$key]['jumlah_kantong']++;
    $grouped_items[$key]['ids_detail'][] = $item['id_detail_pesanan'];
    $grouped_items[$key]['details'][] = [
        'berat_kg' => $item['berat_kg'],
        'catatan' => $item['catatan']
    ];
}

$pesanan_info = $items[0];
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Pesanan #<?= $id_pesanan ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="kelola_pesanan.php">Kelola Pesanan</a></li>
        <li class="breadcrumb-item active">Detail Pesanan</li>
    </ol>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header"><i class="bi bi-person-circle me-1"></i>Info Pelanggan</div>
                <div class="card-body">
                    <p><strong>Nama:</strong> <?= htmlspecialchars($pesanan_info['nama_pelanggan']) ?></p>
                    <p class="mb-0"><strong>No. Telepon:</strong> <?= htmlspecialchars($pesanan_info['no_telepon']) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header"><i class="bi bi-receipt-cutoff me-1"></i>Info Pesanan</div>
                <div class="card-body">
                    <p><strong>Nomor Laci:</strong> <?= htmlspecialchars($pesanan_info['nomor_laci']) ?></p>
                    <p class="mb-0"><strong>Pembayaran:</strong> <?= htmlspecialchars($pesanan_info['metode_pembayaran']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-box-seam me-1"></i>
                Rincian Item Pesanan
            </div>
            <div>
                <button class="btn btn-primary btn-sm" onclick="kirimInvoice()">
                    <i class="bi bi-receipt"></i> Kirim Invoice
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Layanan</th>
                            <th>Tipe</th>
                            <th>Jml. Kantong</th>
                            <th>Total Berat</th>
                            <th>Status</th>
                            <th>Aksi Notifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grouped_items as $group): ?>
                            <tr class="align-middle">
                                <td><strong><?= htmlspecialchars($group['nama_layanan']) ?></strong></td>
                                <td>
                                    <span class="badge <?= $group['tipe_layanan'] == 'Express' ? 'bg-danger' : 'bg-primary' ?>">
                                        <?= htmlspecialchars($group['tipe_layanan']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($group['jumlah_kantong']) ?></td>
                                <!-- PERUBAHAN: Memformat total berat -->
                                <td><?= htmlspecialchars((float)$group['total_berat']) ?> kg</td>
                                <td>
                                    <form action="proses_update_status.php" method="POST">
                                        <input type="hidden" name="ids_detail" value="<?= implode(',', $group['ids_detail']) ?>">
                                        <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">
                                        <select name="status_item" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="Baru" <?= $group['status_item'] == 'Baru' ? 'selected' : '' ?>>Baru</option>
                                            <option value="Diproses" <?= $group['status_item'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                            <option value="Selesai" <?= $group['status_item'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                            <option value="Diambil" <?= $group['status_item'] == 'Diambil' ? 'selected' : '' ?>>Diambil</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <?php if ($group['status_item'] == 'Selesai'): ?>
                                        <button class="btn btn-sm btn-success" onclick="kirimNotifikasi('<?= $pesanan_info['no_telepon'] ?>', '<?= $pesanan_info['nama_pelanggan'] ?>', '<?= $pesanan_info['kode_pesanan'] ?>', '<?= htmlspecialchars($group['nama_layanan'] . ' ' . $group['tipe_layanan']) ?>', 'Selesai')">
                                            <i class="bi bi-whatsapp"></i> Notif Selesai
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="p-3 bg-light">
                                    <h6 class="mb-2 fw-bold">Detail Kantong:</h6>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($group['details'] as $index => $detail): ?>
                                            <li class="list-group-item bg-light">
                                                <!-- PERUBAHAN: Memformat berat per kantong -->
                                                <strong>Kantong #<?= $index + 1 ?>:</strong> <?= htmlspecialchars((float)$detail['berat_kg']) ?> kg
                                                <?php if (!empty($detail['catatan'])): ?>
                                                    <br><small class="text-muted"><em>Catatan: <?= htmlspecialchars($detail['catatan']) ?></em></small>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Mengirim semua item individual ke JavaScript
const allItemsData = <?= json_encode($items) ?>;
const pesananData = <?= json_encode($pesanan_info) ?>;

function kirimInvoice() {
    try {
        if (!pesananData || !allItemsData) {
            alert('Data pesanan tidak lengkap untuk membuat invoice.');
            return;
        }

        const nama = pesananData.nama_pelanggan || 'Pelanggan';
        const kodePesanan = pesananData.kode_pesanan || 'N/A';
        const telepon = pesananData.no_telepon || '';
        
        const totalBiayaAngka = pesananData.total_biaya ? parseFloat(pesananData.total_biaya) : 0;
        const totalBiaya = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalBiayaAngka);
        
        const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/admin'))}`;
        const invoiceUrl = `${baseUrl}/invoice.php?kode=${kodePesanan}`;

        let rincianLayanan = allItemsData.map((item, index) => {
            const hargaSatuan = item.tipe_layanan === 'Express' ? parseFloat(item.harga_express_kg) : parseFloat(item.harga_reguler_kg);
            const berat = parseFloat(item.berat_kg) || 0;
            const subtotal = berat * hargaSatuan;
            const subtotalFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(subtotal);

            return `${index + 1}. ${item.nama_layanan} ${item.tipe_layanan} ${berat} kg Subtotal: ${subtotalFormatted}`;
        
        }).join('\n');

        // PERUBAHAN: Format pesan invoice diperbarui
        let pesan = [
            `Halo kak ${nama}`,
            '',
            `Pesanan laundry Anda (${kodePesanan}) telah kami terima.`,
            '',
            '*RINCIAN PESANAN*',
            rincianLayanan,
            '',
            `Total Biaya: ${totalBiaya}`,
            '',
            'Untuk melihat invoice digital, silakan klik tautan di bawah ini:',
            invoiceUrl,
            '',
            'Terima kasih!',
            'PINANG LAUNDRY'
        ].join('\n');

        let no_wa = telepon.startsWith('0') ? '62' + telepon.substring(1) : telepon;
        no_wa = no_wa.replace(/[^0-9]/g, '');

        if (!no_wa) {
            alert('Nomor telepon pelanggan tidak valid.');
            return;
        }

        const url = `https://wa.me/${no_wa}?text=${encodeURIComponent(pesan)}`;
        window.open(url, '_blank');

    } catch (error) {
        console.error("Gagal membuat pesan invoice:", error);
        alert("Terjadi kesalahan saat membuat pesan invoice. Silakan periksa konsol untuk detail.");
    }
}

function kirimNotifikasi(telepon, nama, kodePesanan, namaLayananLengkap, status) {
    let pesan;
    if (status === 'Selesai') {
        // PERUBAHAN: Format pesan notifikasi selesai diperbarui
        pesan = [
            `Halo kak ${nama}, kabar baik!`,
            '',
            `Pesanan laundry Anda (ID: ${kodePesanan}) untuk layanan ${namaLayananLengkap} telah selesai dan siap diambil.`,
            '',
            'Terima kasih!',
            'PINANG LAUNDRY'
        ].join('\n');
    }

    if(pesan){
        let no_wa = telepon.startsWith('0') ? '62' + telepon.substring(1) : telepon;
        no_wa = no_wa.replace(/[^0-9]/g, '');

        const url = `https://wa.me/${no_wa}?text=${encodeURIComponent(pesan)}`;
        window.open(url, '_blank');
    }
}
</script>

<?php
require_once 'partials/footer.php';
$stmt->close();
$conn->close();
?>
