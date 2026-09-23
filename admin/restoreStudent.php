<?php
require_once '../connectDB.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized"
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid student ID"
    ]);
    exit;
}

$id = (int)$id;

//teachers
$sql = "SELECT * FROM students WHERE id=?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Student not found"
    ]);
    exit;
}

$stmt->close();

//retore
$update = "UPDATE students SET archive=0 WHERE id=?";
$stmt = $conn->prepare($update);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    echo json_encode([
        "status" => "error",
        "message" => $stmt->error
    ]);
    exit;
}

$stmt->close();
$conn->close();

echo json_encode([
    "status" => "success",
    "message" => "Student restored successfully!"
]);
?>