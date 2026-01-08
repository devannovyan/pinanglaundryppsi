<?php
include 'config/db.php';

$stmt = $pdo->query("SELECT * FROM machines");
$data = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // ⬇⬇ masukin di sini
    $data[] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "status" => $row['status'],
        "countdown" => strtotime($row['countdown_end']) // jadi UNIX timestamp
    ];
}

echo json_encode($data);
?>
