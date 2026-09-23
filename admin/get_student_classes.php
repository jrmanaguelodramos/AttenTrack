<?php
include '../connectDB.php';

$input = json_decode(file_get_contents("php://input"), true);
$sID = $input['sID'];

$sql = "SELECT c.* 
        FROM classes c
        JOIN class cl ON cl.classID = c.id
        WHERE cl.sID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sID);
$stmt->execute();

$result = $stmt->get_result();

echo json_encode($result->fetch_all(MYSQLI_ASSOC));