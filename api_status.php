<?php
include 'config/db.php';
date_default_timezone_set("Asia/Jakarta");

$stmt = $pdo->query("SELECT * FROM machines");
$machines = [];

foreach ($stmt as $m) {

    $remaining = null;

    if (!empty($m['countdown_end'])) {

        $end = strtotime($m['countdown_end']);
        $now = time();
        $diff = $end - $now;

        if ($diff > 0) {
            $min = floor($diff / 60);
            $sec = $diff % 60;
            $remaining = sprintf("%02d:%02d", $min, $sec);
        } else {
            // ⏱️ WAKTU HABIS → RESET MESIN
            $pdo->prepare("
                UPDATE machines 
                SET status='Tersedia', countdown_end=NULL 
                WHERE id=?
            ")->execute([$m['id']]);

            // 🔥 UPDATE BOOKING → SELESAI
            $pdo->prepare("
                UPDATE booking
                SET status_booking='Selesai'
                WHERE machine_id=?
                AND status_booking='Digunakan'
            ")->execute([$m['id']]);

            // 🔥 UPDATE BOOKING → CANCEL
            $pdo->prepare("
                UPDATE booking
                SET status_booking='Cancel'
                WHERE machine_id=?
                AND status_booking='Booked'
            ")->execute([$m['id']]);

            $m['status'] = "Tersedia";
        }
    }

    $machines[] = [
        "id" => $m["id"],
        "name" => $m["name"],
        "status" => $m["status"],
        "remaining" => $remaining
    ];
}

echo json_encode($machines);
exit;
