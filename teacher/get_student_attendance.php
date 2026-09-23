<?php
include '../connectDB.php';
$input = json_decode(file_get_contents("php://input"), true);

$sID = $input['sID'];
$classID = $input['classID'] ?? null;

$sql = "SELECT * FROM atten_logs 
            WHERE sID = ? AND cID = ?
            ORDER BY checkindate DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $sID, $classID);

$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
