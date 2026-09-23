<?php
session_start();
date_default_timezone_set('Asia/Manila');
$d = date("Y-m-d");
//Connect to database
require_once '../connectDB.php';

if (isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];
} else {
    echo "Class ID not provided.";
    exit;
}
echo $d;
$sql = "
SELECT 
    COUNT(DISTINCT sID) AS present_count,
    SUM(CASE WHEN remarks = 'Late' THEN 1 ELSE 0 END) AS late_count
FROM atten_logs
WHERE cID = ? AND DATE(checkindate) = ?";
$result = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($result, $sql)) {
    echo '<p class="error">SQL Error</p>';
} else {
    mysqli_stmt_bind_param($result, "is", $class_id, $d);
    mysqli_stmt_execute($result);
    $resultl = mysqli_stmt_get_result($result);
    if (mysqli_num_rows($resultl) > 0) {
        $stats = mysqli_fetch_assoc($resultl);
        echo "<p>Present: " . $stats['present_count'] . "</p>";
        echo "<p>Late: " . $stats['late_count'] . "</p>";
    } else {
        echo "<p>No attendance records found for today.</p>";
    }
}

// if ($_POST['select_date'] == 1) {
//     $Start_date = date("Y-m-d");
//     $_SESSION['searchQuery'] = "checkindate='" . $Start_date . "'";
// }
?>

<div class="table-responsive" style="max-height: 500px;">
    <table class="table">
        <thead class="table-primary">
            <tr>
                <th>Student name</th>
                <th>Student id</th>
                <th></th>
                <th></th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody class="table-secondary">
            <?php
            // $sql = "SELECT * FROM users_logs WHERE checkindate=? AND pic_date BETWEEN ? AND ? ORDER BY id ASC";
            $sql = "SELECT * FROM atten_logs WHERE cID = ? AND checkindate = ? ORDER BY updated_at DESC";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                echo '<p class="error">SQL Error</p>';
            } else {
                mysqli_stmt_bind_param($result, "is", $class_id, $d);
                mysqli_stmt_execute($result);
                $resultl = mysqli_stmt_get_result($result);
                if (mysqli_num_rows($resultl) > 0) {
                    while ($row = mysqli_fetch_assoc($resultl)) {
            ?>
                        <TR>
                            <TD><?php echo $row['sName']; ?></TD>
                            <TD><?php echo $row['stud_num']; ?></TD>
                            <TD>
                            </TD>
                            <td></td>
                            <TD><?php echo $row['timein']; ?></TD>
                            <TD><?php echo $row['timeout']; ?></TD>
                            <TD><?php echo $row['remarks']; ?></TD>
                        </TR>
            <?php
                    }
                } else {
                    echo '<tr><td colspan="7">No attendance records found for today.</td></tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>