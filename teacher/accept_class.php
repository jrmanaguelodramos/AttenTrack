<?php
require_once '../connectDB.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$class_id = $input['class_id'] ?? null;

if (!$class_id) {
    echo json_encode(["status" => "error", "message" => "Missing class ID"]);
    exit;
}

$sql = "UPDATE classes SET accepted = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
?>