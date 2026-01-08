<?php
include 'config/db.php';
date_default_timezone_set("Asia/Jakarta");

$machine_id = isset($_POST['machine_id']) ? $_POST['machine_id'] : null;

if (!$machine_id) {
    echo json_encode(["success" => false, "msg" => "Machine ID kosong"]);
    exit;
}

$pdo->prepare("
    UPDATE machines
    SET status = 'Digunakan',
        countdown_end = DATE_ADD(NOW(), INTERVAL 60 MINUTE)
    WHERE id = ?
")->execute([$machine_id]);

echo json_encode(["success" => true]);
exit;
