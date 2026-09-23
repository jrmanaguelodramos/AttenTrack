<?php
require_once '../connectDB.php';

header('Content-Type: application/json');

if (!isset($_GET['class'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing class"
    ]);
    exit;
}

$classID = $_GET['class'];

/* =====================================
   GET ALL NOT ENROLLED STUDENTS
===================================== */
$sql = "SELECT *
        FROM class c
        INNER JOIN students s ON s.id = c.sID
        WHERE c.classID = ?
        AND c.status = 'not enrolled'
        ORDER BY c.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $classID);
$stmt->execute();

$result = $stmt->get_result();

$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode([
    "success" => true,
    "students" => $students
]);
?>