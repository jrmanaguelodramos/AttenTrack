<?php
require_once '../connectDB.php';
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON"]);
    exit;
}

$class_id = $input['class_id'] ?? null;
$mode     = $input['mode'] ?? null;
$type     = $input['type'] ?? null;
$room     = $input['room'] ?? null;

if (!$class_id || !$mode || !$type || !$room) {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit;
}
$_SESSION['room'] = $room;
$date = date("Y-m-d");
$t    = date("H:i:s");


// =========================
// CHECK ACTIVE SESSION
// =========================
$sql = "SELECT id FROM attendance_sessions 
        WHERE class_id=? AND status='active' AND date=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $class_id, $date);
$stmt->execute();
$result = $stmt->get_result();


// =========================
// ATTENDANCE IN
// =========================
if ($type === "ATTENDANCE IN") {

    if ($result->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Attendance already active"
        ]);
        exit;
    }

    $sql = "INSERT INTO attendance_sessions 
            (class_id, mode, room, date, started_at, status)
            VALUES (?, ?, ?, ?, ?, 'active')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $class_id, $mode, $room, $date, $t);
    $stmt->execute();

    // device ON
    $sql = "UPDATE devices SET device_mode = 1 WHERE room = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $room);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Attendance started"
    ]);
    exit;
}


// =========================
// ATTENDANCE OUT
// =========================
// close session
$sql = "UPDATE attendance_sessions 
        SET room=?, finished=1, status='inactive', ended_at=?
        WHERE class_id=? 
        AND date=? 
        AND status='active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssis", $room, $t, $class_id, $date);
$stmt->execute();

// device OFF
$sql = "UPDATE devices SET device_mode = 2 WHERE room = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $room);
$stmt->execute();

$sql = "INSERT INTO atten_logs (
            sID, sName, cID, checkindate, room, section, stud_num, remarks
        )
        SELECT 
            s.id,
            CONCAT(s.fname, ' ', IFNULL(s.mname, ''), ' ', s.lname),
            c.classID,
            ?, ?, s.section, s.stud_num, 'Absent'
        FROM class c
        INNER JOIN students s ON s.id = c.sID
        WHERE c.classID = ?
        AND NOT EXISTS (
            SELECT 1
            FROM atten_logs al
            WHERE al.sID = s.id
              AND al.cID = c.classID
              AND al.checkindate = ?
            --   AND al.remarks IN ('On_Time', 'Late')
        )";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssis", $date, $room, $class_id, $date);
$stmt->execute();
// =========================
// RESPONSE (ONLY ONCE)
// =========================
echo json_encode([
    "status" => "success",
    "message" => "Attendance closed + absentees marked"
]);
exit;
