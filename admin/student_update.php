<?php
require_once '../connectDB.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
$fname = trim($_POST['fname'] ?? '');
$mname = trim($_POST['mname'] ?? '');
$lname = trim($_POST['lname'] ?? '');

if(empty($id) || empty($fname) || empty($lname)){

    echo json_encode([
        "status" => "error",
        "message" => "Please fill in required fields."
    ]);

    exit;
}

$sql = "UPDATE students 
        SET fname=?, mname=?, lname=?
        WHERE id=?";

$stmt = $conn->prepare($sql);

if(!$stmt){

    echo json_encode([
        "status" => "error",
        "message" => "Database error."
    ]);

    exit;
}

$stmt->bind_param(
    "sssi",
    $fname,
    $mname,
    $lname,
    $id
);

if($stmt->execute()){

    echo json_encode([
        "status" => "success",
        "message" => "Student updated successfully!"
    ]);

}else{

    echo json_encode([
        "status" => "error",
        "message" => "Update failed."
    ]);
}

$stmt->close();
$conn->close();
?>