<?php
include '../connectDB.php';
$input = json_decode(file_get_contents("php://input"), true);

$sID = $input['sID'];
$classID = $input['classID'] ?? null;

if ($classID) {
    $sql = "SELECT * FROM atten_logs 
            WHERE sID = ? AND cID = ?
            ORDER BY checkindate DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $sID, $classID);
} else {
    $sql = "SELECT * FROM atten_logs 
            WHERE sID = ?
            ORDER BY checkindate DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sID);
}

$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_all(MYSQLI_ASSOC));