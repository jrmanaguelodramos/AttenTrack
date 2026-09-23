<?php
require_once '../connectDB.php';
session_start();
$room = $_SESSION['room'];
if (!isset($_SESSION['room']) || trim($_SESSION['room']) === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Room session missing"
    ]);
    exit;
}
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['class_id']) || !is_numeric($input['class_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid class ID"]);
    exit;
}

$class_id = (int)$input['class_id'];
$status = "closed";
$sql = "UPDATE attendance_sessions 
        SET status=?, ended_at=NOW(), room=''
        WHERE class_id=? AND status='active' || status='inactive'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $class_id);
$stmt->execute();

$sql = "UPDATE devices
        SET device_mode = '4'
WHERE room=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $room);
$stmt->execute();

echo json_encode(["status" => "success"]);