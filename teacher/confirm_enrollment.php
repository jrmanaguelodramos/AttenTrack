<?php
require_once '../connectDB.php';

$data = json_decode(file_get_contents("php://input"), true);

$classRowID = $data['classRowID'];

$sql = "UPDATE class
        SET status='active'
        WHERE sID=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $classRowID);
$stmt->execute();

echo "Enrollment confirmed";
?>