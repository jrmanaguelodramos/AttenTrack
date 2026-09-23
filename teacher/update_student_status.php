<?php
require_once '../connectDB.php';

if (isset($_POST['sID'], $_POST['classID'], $_POST['status'])) {

    $sID = $_POST['sID'];
    $classID = $_POST['classID'];
    $status = $_POST['status'];

    $allowed = ['Active', 'Critical', 'Dropped'];

    if (!in_array($status, $allowed)) {
        echo "Invalid status";
        exit;
    }

    $sql = "UPDATE class SET status=? WHERE sID=? AND classID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $status, $sID, $classID);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}