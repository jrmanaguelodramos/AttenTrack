<?php
require '../connectDB.php';

$room = isset($_GET['room']) ? $_GET['room'] : 'ALL';

if ($room === 'ALL') {
    $sql = "SELECT * FROM intrusion_logs ORDER BY date DESC, time DESC LIMIT 50";
    $stmt = mysqli_prepare($conn, $sql);
} else {
    $sql = "SELECT * FROM intrusion_logs WHERE room=? ORDER BY date DESC, time DESC LIMIT 50";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $room);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);