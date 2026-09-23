<?php
include '../dbcon.php';

// 🔥 read JSON body
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['tID'])) {
    echo json_encode(["error" => "tID missing"]);
    exit;
}

$tID = $input['tID'];
$accepted = $input['accepted'] ;

$sql = "SELECT * FROM classes WHERE tID = ? AND accepted = ? ORDER BY schedule ASC, time_start ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$tID, $accepted]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>