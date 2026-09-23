<?php
require '../connectDB.php';

$sql = "SELECT DISTINCT room FROM devices";
$result = mysqli_query($conn, $sql);

$rooms = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rooms[] = $row['room'];
}

echo json_encode($rooms);