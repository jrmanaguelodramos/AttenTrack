<?php
require_once '../connectDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        echo "Invalid Student ID.";
        exit;
    }

    $sql = "UPDATE students SET archive = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Student deleted successfully!";
    } else {
        echo "Delete failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>