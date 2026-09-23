<?php
include "db.php";

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $class_id = $_POST['class_id'];
    $room = $_POST['room'];

    $today = date('Y-m-d');
    $time_now = date('H:i:s');

    $students_inside = [];

    /*
    ==========================================
    GET STUDENTS STILL INSIDE
    ==========================================
    */
    $query = "
        SELECT *
        FROM atten_logs
        WHERE cID = ?
        AND room = ?
        AND checkindate = ?
        AND card_out = 0
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iss", $class_id, $room, $today);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {

        $students_inside[] = [
            'sID' => $row['sID'],
            'name' => $row['sName'],
            'stud_num' => $row['stud_num']
        ];

        /*
        ==========================================
        FORCE TIMEOUT
        ==========================================
        */
        $update = "
            UPDATE atten_logs
            SET
                timeout = ?,
                card_out = 1,
                remarks = 'EMERGENCY_EXIT'
            WHERE id = ?
        ";

        $update_stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param(
            $update_stmt,
            "si",
            $time_now,
            $row['id']
        );

        mysqli_stmt_execute($update_stmt);

        /*
        ==========================================
        LOG EVENT
        ==========================================
        */
        $intrusion = "
            INSERT INTO intrusion_logs
            (
                sID,
                card_UID,
                room,
                event_type,
                date,
                description
            )
            VALUES
            (
                ?, ?, ?, 'EMERGENCY_EXIT', ?, ?
            )
        ";

        $description = "Forced emergency exit from room.";

        $intrusion_stmt = mysqli_prepare($conn, $intrusion);

        mysqli_stmt_bind_param(
            $intrusion_stmt,
            "issss",
            $row['sID'],
            $row['card_uid'],
            $room,
            $today,
            $description
        );

        mysqli_stmt_execute($intrusion_stmt);
    }

    /*
    ==========================================
    CLOSE ATTENDANCE SESSION
    ==========================================
    */
    $session = "
        UPDATE attendance_sessions
        SET
            finished = 1,
            status = 'closed',
            ended_at = ?
        WHERE class_id = ?
        AND room = ?
        AND date = ?
        AND finished = 0
    ";

    $session_stmt = mysqli_prepare($conn, $session);

    mysqli_stmt_bind_param(
        $session_stmt,
        "siss",
        $time_now,
        $class_id,
        $room,
        $today
    );

    mysqli_stmt_execute($session_stmt);

    /*
    ==========================================
    RESPONSE
    ==========================================
    */
    echo json_encode([
        'success' => true,
        'room' => $room,
        'class_id' => $class_id,
        'students_remaining' => count($students_inside),
        'students' => $students_inside
    ]);
}
?>
<button
    class="emergency-btn"
    onclick="emergencyExit(<?= $class_id ?>, '<?= $room ?>')">
    <i class="fa-solid fa-triangle-exclamation"></i>
    EMERGENCY EXIT
</button>
<script>
    function emergencyExit(classId, room) {

        if (!confirm(
                "Emergency Exit will force OUT all students still inside.\nContinue?"
            )) {
            return;
        }

        $.ajax({
            url: "emergency_exit.php",
            type: "POST",
            data: {
                class_id: classId,
                room: room
            },

            success: function(response) {

                const data = JSON.parse(response);

                let message =
                    "Emergency Exit Completed\n\n" +
                    "Students Forced OUT: " +
                    data.students_remaining;

                alert(message);

                console.log(data.students);

                location.reload();
            },

            error: function() {
                alert("Emergency Exit Failed");
            }
        });
    }
</script>