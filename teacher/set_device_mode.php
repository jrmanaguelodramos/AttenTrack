<?php
require_once '../connectDB.php';

$data = json_decode(file_get_contents("php://input"), true);

$room = $data['room'];
$mode = $data['mode'];
$class_id = $data['class_id'];

$sql = "UPDATE devices 
        SET device_mode= ?, class_id = ?
        WHERE room=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $mode, $class_id, $room);
$stmt->execute();

echo "success";
?>