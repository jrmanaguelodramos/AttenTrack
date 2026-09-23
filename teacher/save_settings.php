<?php
session_start();
require '../connectDB.php';

header('Content-Type: application/json');

// 🔒 Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

// 🔒 Validate inputs
if (
    !isset($_POST['class_id']) ||
    !isset($_POST['time_in']) ||
    !isset($_POST['time_out']) ||
    !isset($_POST['max_absentees'])
) {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit;
}

$class_id = intval($_POST['class_id']);
$time_in = intval($_POST['time_in']);
$time_out = intval($_POST['time_out']);
$max_absentees = intval($_POST['max_absentees']);

// ⚠️ Basic validation
if ($time_in < 0 || $time_out <= 0 || $max_absentees <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid values"]);
    exit;
}

// 🔒 Get teacher ID from session (secure)
if (!isset($_SESSION['tID'])) {
    $sqlT = "SELECT id FROM teachers WHERE userID = ?";
    $stmtT = $conn->prepare($sqlT);
    $stmtT->bind_param("i", $_SESSION['user_id']);
    $stmtT->execute();
    $resT = $stmtT->get_result();
    $teacher = $resT->fetch_assoc();
    $_SESSION['tID'] = $teacher['id'];
}

$tID = $_SESSION['tID'];

// 🔒 Ensure teacher owns the class
$sqlCheck = "SELECT id FROM classes WHERE id = ? AND tID = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $class_id, $tID);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

// ✅ Update class settings
$sql = "UPDATE classes 
        SET grace = ?, class_duration = ?, max_absentees = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $time_in, $time_out, $max_absentees, $class_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "DB error"]);
}
?>