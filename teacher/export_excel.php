<?php
require_once '../connectDB.php';

$class_id = (int)$_GET['class'];
$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;

/* CLASS INFO */
$stmt = $conn->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();

/* ATTENDANCE QUERY (WITH STUDENT NAMES) */
$sql = "SELECT 
            a.checkindate,
            CONCAT(s.fname, ' ', s.mname, ' ', s.lname) AS name,
            a.remarks
        FROM atten_logs a
        JOIN students s ON s.id = a.sID
        WHERE a.cID = ?";

if ($from && $to) {
    $sql .= " AND a.checkindate BETWEEN ? AND ?";
}

$sql .= " ORDER BY a.checkindate ASC";

$stmt = $conn->prepare($sql);

if ($from && $to) {
    $stmt->bind_param("iss", $class_id, $from, $to);
} else {
    $stmt->bind_param("i", $class_id);
}

$stmt->execute();
$res = $stmt->get_result();

/* EXCEL HEADERS */
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=attendance_report.xls");

/* HEADER ROW */
echo "Class\tName\tDate\tStatus\n";

while ($row = $res->fetch_assoc()) {

    echo $class['subject'] . "\t";
    echo $row['name'] . "\t";
    echo $row['checkindate'] . "\t";
    echo $row['remarks'] . "\n";
}

exit;
?>