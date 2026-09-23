<?php
require_once '../connectDB.php';

$sql = "SELECT * FROM students WHERE section = 'SBIT-2B'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        $insertSql = "INSERT INTO class (`sID`, `classID`, `card_uid`, `status`)
                      VALUES (?, 4, ?, 'active')";

        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $insertSql)) {

            mysqli_stmt_bind_param(
                $stmt,
                "ss",
                $row['id'],
                $row['card_uid']
            );

            mysqli_stmt_execute($stmt);

        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
    }

} else {
    echo "No students found in SBIT-2B.";
}
?>