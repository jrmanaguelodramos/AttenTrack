<?php
require_once '../connectDB.php';
// =====================
// ➕ ADD STUDENT
// =====================
if (isset($_POST['Add'])) {

    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $student_number = $_POST['student_number'];
    $course = $_POST['course'];
    $rfidinput = $_POST['rfidinput'];
    $sid = $_POST['sid'];

    //check if there any selected user
    $sql = "SELECT add_card FROM students WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error";
        exit();
    } else {
        mysqli_stmt_bind_param($result, "i", $sid);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {

            if ($row['add_card'] == 0) {

                if (!empty($first_name)&& !empty($last_name) && !empty($gender) && !empty($course) && !empty($student_number) && !empty($rfidinput)) {
                    //check if there any user had already the Serial Number
                    $sql = "SELECT stud_num FROM students WHERE stud_num=? AND id NOT like ?";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                        echo "SQL_Error";
                        exit();
                    } else {
                        mysqli_stmt_bind_param($result, "si", $student_number, $sid);
                        mysqli_stmt_execute($result);
                        $resultl = mysqli_stmt_get_result($result);
                        if (!$row = mysqli_fetch_assoc($resultl)) {
                            //check if there any user had already the Card UID
                            $sql = "SELECT card_uid FROM students WHERE card_uid=? AND id NOT like ?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "si", $rfidinput, $sid);
                                mysqli_stmt_execute($result);
                                $resultl = mysqli_stmt_get_result($result);
                                if ($row = mysqli_fetch_assoc($resultl)) {
                                    echo "The Card UID is already taken!";
                                    exit();
                                }
                            }
                            $sql = "UPDATE students SET fname=?, mname=?, lname=?, stud_num=?, gender=?, course=?, last_update=NOW(), card_select=0, add_card=1 WHERE id=?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error_select_Fingerprint";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "ssssssi", $first_name, $middle_name, $last_name, $student_number, $gender, $course, $sid);
                                mysqli_stmt_execute($result);

                                echo 1;
                                exit();
                            }
                        } else {
                            echo "The student number is already taken!";
                            exit();
                        }
                    }
                } else {
                    echo "Empty Fields";
                    exit();
                }
            } else {
                echo "This User is already exist";
                exit();
            }
        } else {
            echo "There's no selected Card!";
            exit();
        }
    }
}
?>