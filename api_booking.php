<?php
include 'config/db.php';
date_default_timezone_set("Asia/Jakarta");

$machine_id = $_POST['machine_id'];
$nama       = $_POST['name'];
$phone      = $_POST['phone'];
$total      = isset($_POST['total_biaya']) ? $_POST['total_biaya'] : 0;

// cek pelanggan
$stmt = $pdo->prepare("SELECT id_pelanggan FROM pelanggan WHERE no_telepon=?");
$stmt->execute([$phone]);
$pelanggan = $stmt->fetch();

if($pelanggan){
    $id_pelanggan = $pelanggan['id_pelanggan'];
} else {
    $stmt = $pdo->prepare("INSERT INTO pelanggan (nama_pelanggan,no_telepon) VALUES (?,?)");
    $stmt->execute([$nama,$phone]);
    $id_pelanggan = $pdo->lastInsertId();
}

// insert booking
$stmt = $pdo->prepare("
    INSERT INTO booking (id_pelanggan, machine_id, total_biaya, status_booking)
    VALUES (?, ?, ?, 'Booked')
");
$stmt->execute([$id_pelanggan, $machine_id, $total]);

$id_booking = $pdo->lastInsertId();

$kode_booking = "SS-" . date("Ymd") . "-" . str_pad($id_booking,4,"0",STR_PAD_LEFT);

$pdo->prepare("UPDATE booking SET kode_booking=? WHERE id_booking=?")
    ->execute([$kode_booking, $id_booking]);

// FIX DI SINI ⬇️
$pdo->prepare("
    UPDATE machines 
    SET status='Dipesan',
        countdown_end=DATE_ADD(NOW(), INTERVAL 30 MINUTE)
    WHERE id=?
")->execute([$machine_id]);

echo json_encode([
    "success" => true,
    "qr_image" => "images/qris.jpg",
    "nominal" => 16000,
    "kode_booking" => $kode_booking
]);
exit;


