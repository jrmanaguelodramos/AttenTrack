<?php
include '../dbcon.php';

header('Content-Type: application/json');

// 🔥 read JSON body
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['tID'], $input['accepted'])) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

$tID = $input['tID'];
$accepted = $input['accepted'];

// echo json_encode([
//     "tID" => $tID,
//     "accepted" => $accepted
// ]);
// exit;
// // ✅ SQL query

$sql = "SELECT 
    cl.id,
    cl.section,
    cl.subject,
    cl.course_code,
    cl.schedule,
    cl.time_start,

    COUNT(DISTINCT CASE 
        WHEN c.status = 'active' THEN c.sID 
    END) AS student_count

FROM classes cl

LEFT JOIN `class` c 
    ON cl.id = c.classID

WHERE cl.tID = ?
AND cl.accepted = ?

GROUP BY 
    cl.id,
    cl.section,
    cl.subject,
    cl.course_code,
    cl.schedule,
    cl.time_start

ORDER BY 
    cl.schedule ASC,
    cl.time_start ASC
";

// ✅ prepare & execute
$stmt = $pdo->prepare($sql);
$stmt->execute([$tID, $accepted]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result, JSON_NUMERIC_CHECK);
