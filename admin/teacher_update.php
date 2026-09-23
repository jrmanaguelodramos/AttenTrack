<?php
require_once '../connectDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? '';
    $fname = trim($_POST['fname'] ?? '');
    $mname = trim($_POST['mname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $department = trim($_POST['department'] ?? '');

    // ✅ Validation
    if (empty($id) || empty($fname) || empty($lname) || empty($department)) {
        echo "Please fill all required fields.";
        exit;
    }

    // ✅ Update query
    $sql = "UPDATE teachers 
            SET fname = ?, mname = ?, lname = ?, department = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $fname, $mname, $lname, $department, $id);

    if ($stmt->execute()) {
        echo "Teacher updated successfully!";
    } else {
        echo "Update failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>