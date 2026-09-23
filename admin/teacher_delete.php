<?php
require_once '../connectDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        echo "Invalid teacher ID.";
        exit;
    }

    // // ⚠️ Optional: check if teacher has classes
    // $check = $conn->prepare("SELECT id FROM classes WHERE tID = ?");
    // $check->bind_param("i", $id);
    // $check->execute();
    // $result = $check->get_result();

    // if ($result->num_rows > 0) {
    //     echo "Cannot delete: Teacher has assigned classes.";
    //     exit;
    // }

    // ✅ Delete teacher
    $sql = "UPDATE teachers SET archive = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Teacher deleted successfully!";
    } else {
        echo "Delete failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>