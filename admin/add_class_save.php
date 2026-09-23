<?php
require_once '../connectDB.php';

$tID = $_POST['tID'];
$course_code = $_POST['course_code'];
$subject = $_POST['subject'];
$section = $_POST['section'];
$schedule = $_POST['schedule'];

$time_start = $_POST['time_start'] ?? null;
$time_end = $_POST['time_end'] ?? null;

$lec_start = $_POST['lec_start'] ?? null;
$lec_end = $_POST['lec_end'] ?? null;

$lab_start = $_POST['lab_start'] ?? null;
$lab_end = $_POST['lab_end'] ?? null;

$room = $_POST['room'] ?? null;
$lab_room = $_POST['lab_room'] ?? null;

$conn->begin_transaction();

try {

    // =========================
    // DETECT MODE
    // =========================
    $isDual = !empty($lec_start) && !empty($lec_end);

    // =========================
    // DEFAULTS
    // =========================
    $class_duration = null;
    $lab_duration = null;
    $time_start_final = null;

    // =========================
    // SINGLE SESSION
    // =========================
    if (!$isDual) {

        if (empty($time_start) || empty($time_end)) {
            throw new Exception("Missing single session time");
        }

        $start = new DateTime($time_start);
        $end = new DateTime($time_end);

        if ($end <= $start) {
            throw new Exception("Invalid class time range");
        }

        $diff = $start->diff($end);
        $class_duration = ($diff->h * 60) + $diff->i;

        $time_start_final = $time_start;
    }

    // =========================
    // DUAL SESSION (LECTURE = MAIN)
    // =========================
    else {

        if (empty($lec_start) || empty($lec_end)) {
            throw new Exception("Missing lecture time");
        }

        $start = new DateTime($lec_start);
        $end = new DateTime($lec_end);

        if ($end <= $start) {
            throw new Exception("Invalid lecture time range");
        }

        $diff = $start->diff($end);
        $class_duration = ($diff->h * 60) + $diff->i;

        $time_start_final = $lec_start;
    }

    // =========================
    // LAB DURATION (OPTIONAL)
    // =========================
    if (!empty($lab_start) && !empty($lab_end)) {

        $labS = new DateTime($lab_start);
        $labE = new DateTime($lab_end);

        if ($labE <= $labS) {
            throw new Exception("Invalid lab time range");
        }

        $labDiff = $labS->diff($labE);
        $lab_duration = ($labDiff->h * 60) + $labDiff->i;
    }

    // =========================
    // INSERT CLASS
    // =========================
    $sql = "INSERT INTO classes 
        (tID, course_code, subject, section, schedule, time_start, class_duration, room, lab_start, lab_duration, lab_room)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "isssssissis",
        $tID,
        $course_code,
        $subject,
        $section,
        $schedule,
        $time_start_final,
        $class_duration,
        $room,
        $lab_start,
        $lab_duration,
        $lab_room
    );

    $stmt->execute();
    $class_id = $stmt->insert_id;

    // =========================
    // GET STUDENTS
    // =========================
    $sql = "SELECT id, card_uid FROM students WHERE section = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $section);
    $stmt->execute();
    $result = $stmt->get_result();

    // insert
    $stmt = $conn->prepare("INSERT IGNORE INTO class (classID, sID, card_uid) VALUES (?, ?, ?)");

    while ($row = $result->fetch_assoc()) {
        $stmt->bind_param("iis", $class_id, $row['id'], $row['card_uid']);
        $stmt->execute();
    }
    $conn->commit();

    header("Location: teacher_profile.php?id=$tID");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}
