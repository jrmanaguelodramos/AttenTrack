<?php
//Connect to database
require '../connectDB.php';
date_default_timezone_set('Asia/Manila');
$d = date("Y-m-d");
$t = date("h:i:s A");

function logIntrusion($conn, $sID, $card_uid, $room, $event_type, $description)
{
    date_default_timezone_set('Asia/Manila');
    $date = date("Y-m-d");

    $sql = "INSERT INTO intrusion_logs (sID, card_UID, room, event_type, description, date)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "sissss", $sID, $card_uid, $room, $event_type, $description, $date);
        mysqli_stmt_execute($stmt);
    }
}

if (isset($_GET['card_uid']) && isset($_GET['device_token'])) {

    $card_uid = $_GET['card_uid'];
    $device_uid = $_GET['device_token'];

    $sql = "SELECT * FROM devices WHERE device_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select_device";
        exit();
    } else {
        mysqli_stmt_bind_param($result, "s", $device_uid);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {
            $device_mode = $row['device_mode'];
            $room = $row['room'];
            // ================================================================//
            // device for attendance in
            if ($device_mode == 1) {
                $sql = "SELECT * FROM students WHERE card_uid=?";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_Select_card";
                    exit();
                } else {
                    mysqli_stmt_bind_param($result, "s", $card_uid);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);
                    if ($row = mysqli_fetch_assoc($resultl)) {
                        $sName = $row['fname'] . " " . $row['mname'] . " " . $row['lname'];
                        $sID = $row['id'];
                        $section = $row['section'];
                        $student_number = $row['stud_num'];
                        //*****************************************************
                        //An existed Card has been detected for Login
                        if ($row['add_card'] == 1) {
                            $sql = "SELECT * FROM attendance_sessions WHERE room=? AND status='ACTIVE'";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error_Select_class";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "s", $room);
                                mysqli_stmt_execute($result);
                                $resultl = mysqli_stmt_get_result($result);
                                if ($row = mysqli_fetch_assoc($resultl)) {
                                    $class_id = $row['class_id'];
                                    $sql = "SELECT * FROM class WHERE `sID`=? AND classID=?";
                                    $result = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($result, $sql)) {
                                        echo "SQL_Error_Select_class_card";
                                        exit();
                                    } else {
                                        mysqli_stmt_bind_param($result, "si", $sID, $class_id);
                                        mysqli_stmt_execute($result);
                                        $resultl = mysqli_stmt_get_result($result);
                                        if (!$row = mysqli_fetch_assoc($resultl)) {
                                            logIntrusion($conn, $sID, $card_uid, $room, "NOT_ENROLLED", "Not enrolled in class");
                                            echo "Student not enrolled in this class!";
                                            exit();
                                        } else {
                                            $sql = "SELECT * FROM atten_logs WHERE card_uid=? AND checkindate=? AND card_out=0";
                                            $result = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($result, $sql)) {
                                                echo "SQL_Error_Select_logs";
                                                exit();
                                            } else {
                                                mysqli_stmt_bind_param($result, "ss", $card_uid, $d);
                                                mysqli_stmt_execute($result);
                                                $resultl = mysqli_stmt_get_result($result);
                                                //*****************************************************
                                                //Login
                                                if (!$row = mysqli_fetch_assoc($resultl)) {
                                                    // get class schedule + grace
                                                    $sql = "SELECT s.started_at, c.grace FROM attendance_sessions s JOIN classes c ON s.class_id = c.id WHERE s.class_id=? AND status='active'";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bind_param("i", $class_id);
                                                    $stmt->execute();
                                                    $class = $stmt->get_result()->fetch_assoc();

                                                    $classStart = strtotime($class['started_at']);
                                                    $currentTime = strtotime(date("H:i:s"));
                                                    $graceLimit = $classStart + ($class['grace'] * 60);

                                                    // determine status
                                                    if ($currentTime <= $graceLimit) {
                                                        $status = "ON_TIME";
                                                    } else {
                                                        $status = "LATE";
                                                    }

                                                    $sql = "INSERT INTO atten_logs (`sID`, sName, cID, room, section, stud_num, card_uid, checkindate, timein, `timeout`, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                                    $result = mysqli_stmt_init($conn);
                                                    if (!mysqli_stmt_prepare($result, $sql)) {
                                                        echo "SQL_Error_Select_login1";
                                                        exit();
                                                    } else {
                                                        $timeout = "00:00:00";
                                                        mysqli_stmt_bind_param($result, "isissssssss", $sID, $sName, $class_id, $room, $section, $student_number, $card_uid, $d, $t, $timeout, $status);
                                                        mysqli_stmt_execute($result);

                                                        echo "login";
                                                        exit();
                                                    }
                                                }
                                                //*****************************************************
                                                //Logout
                                                else {
                                                    // $sql="UPDATE users_logs SET timeout=?, card_out=1 WHERE card_uid=? AND checkindate=? AND card_out=0";
                                                    // $result = mysqli_stmt_init($conn);
                                                    // if (!mysqli_stmt_prepare($result, $sql)) {
                                                    //     echo "SQL_Error_insert_logout1";
                                                    //     exit();
                                                    // }
                                                    // else{
                                                    //     mysqli_stmt_bind_param($result, "sss", $t, $card_uid, $d);
                                                    //     mysqli_stmt_execute($result);

                                                    //     echo "logout".$Uname;
                                                    //     exit();
                                                    // }
                                                    logIntrusion($conn, $sID, $card_uid, $room, "DUPLICATE_SCAN", "Already logged in");
                                                    echo "already logged in";
                                                    exit();
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    logIntrusion($conn, 0, $card_uid, $room, "CLASS_NOT_FOUND", "Class not found");
                                    echo "Class not found!";
                                    exit();
                                }
                            }
                        } else if ($row['add_card'] == 0) {
                            logIntrusion($conn, $row['id'], $card_uid, $room, "UNREGISTERED_CARD", "Card inactive");
                            echo "Not registerd!";
                            exit();
                        }
                    } else {
                        logIntrusion($conn, 0, $card_uid, $room, "UNKNOWN_CARD", "Card not found");
                        echo "Not found!";
                        exit();
                    }
                }
            }
            //================================================================//
            // device for attendance out
            else if ($device_mode == 2) {
                $sql = "SELECT * FROM attendance_sessions WHERE room=? AND status='INACTIVE'";
                $stmt = mysqli_stmt_init($conn);

                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo "SQL_Error_Status";
                    exit();
                }

                mysqli_stmt_bind_param($stmt, "s", $room);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);

                if (!$row = mysqli_fetch_assoc($res)) {
                    echo "Attendance is CLOSED!";
                    exit();
                }
                $sql = "SELECT * FROM atten_logs WHERE card_uid=? AND checkindate=? AND card_out=0";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_Select_logs";
                    exit();
                } else {
                    mysqli_stmt_bind_param($result, "ss", $card_uid, $d);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);
                    //*****************************************************
                    //Logout
                    if ($row = mysqli_fetch_assoc($resultl)) {
                        $sql = "UPDATE atten_logs SET timeout=?, card_out=1 WHERE card_uid=? AND checkindate=? AND card_out=0";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo "SQL_Error_insert_logout1";
                            exit();
                        } else {
                            mysqli_stmt_bind_param($result, "sss", $t, $card_uid, $d);
                            mysqli_stmt_execute($result);

                            echo "logout";
                            exit();
                        }
                    } else {
                        logIntrusion($conn, 0, $card_uid, $room, "INVALID_LOGOUT", "Logout without login");
                        echo "Not logged in!";
                        exit();
                    }
                }
            }
            //================================================================//
            // device for enrolling a student on class
            else if ($device_mode == 3) {
                $sql = "SELECT * FROM students WHERE card_uid=?";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_Select_card";
                    exit();
                } else {
                    mysqli_stmt_bind_param($result, "s", $card_uid);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);
                    if ($row = mysqli_fetch_assoc($resultl)) {
                        $sID = $row['id'];
                        //*****************************************************
                        //An existed Card has been detected for Login
                        if ($row['add_card'] == 1) {
                            $sql = "SELECT * FROM devices WHERE room=?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error_Select_class";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "s", $room);
                                mysqli_stmt_execute($result);
                                $resultl = mysqli_stmt_get_result($result);
                                if ($row = mysqli_fetch_assoc($resultl)) {
                                    $class_id = $row['class_id'];
                                    $sql = "SELECT * FROM class WHERE `sID`=? AND classID=?";
                                    $result = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($result, $sql)) {
                                        echo "SQL_Error_Select_class_card";
                                        exit();
                                    } else {
                                        mysqli_stmt_bind_param($result, "ii", $sID, $class_id);
                                        mysqli_stmt_execute($result);
                                        $resultl = mysqli_stmt_get_result($result);
                                        if (!$row = mysqli_fetch_assoc($resultl)) {
                                            $sql = "INSERT INTO class (sID, classID, card_uid, status)
                                                    VALUES (?, ?, ?, 'not enrolled')";
                                            $result = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($result, $sql)) {
                                                echo "SQL_Error_Select_class_card";
                                                exit();
                                            } else {
                                                mysqli_stmt_bind_param($result, "iis", $sID, $class_id, $card_uid);
                                                mysqli_stmt_execute($result);

                                                echo "succesful";
                                                exit();
                                            }
                                        } else {
                                            echo "already enrolled";

                                            exit();
                                        }
                                    }
                                }
                            }
                        } else if ($row['add_card'] == 0) {
                            logIntrusion($conn, $row['id'], $card_uid, $room, "UNREGISTERED_CARD", "Card inactive");
                            echo "Not registerd!";
                            exit();
                        }
                    } else {
                        logIntrusion($conn, 0, $card_uid, $room, "UNKNOWN_CARD", "Card not found");
                        echo "Not found!";
                        exit();
                    }
                }
            }
            //================================================================//
            // device for enrollment
            else if ($device_mode == 0) {
                //New Card has been added
                $sql = "SELECT * FROM students WHERE card_uid=?";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_Select_card";
                    exit();
                } else {
                    mysqli_stmt_bind_param($result, "s", $card_uid);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);
                    //The Card is available
                    if ($row = mysqli_fetch_assoc($resultl)) {
                        $sql = "SELECT card_select FROM students WHERE card_select=1";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo "SQL_Error_Select";
                            exit();
                        } else {
                            mysqli_stmt_execute($result);
                            $resultl = mysqli_stmt_get_result($result);

                            if ($row = mysqli_fetch_assoc($resultl)) {
                                $sql = "UPDATE students SET card_select=0";
                                $result = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($result, $sql)) {
                                    echo "SQL_Error_insert";
                                    exit();
                                } else {
                                    mysqli_stmt_execute($result);

                                    $sql = "UPDATE students SET card_select=1 WHERE card_uid=?";
                                    $result = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($result, $sql)) {
                                        echo "SQL_Error_insert_An_available_card";
                                        exit();
                                    } else {
                                        mysqli_stmt_bind_param($result, "s", $card_uid);
                                        mysqli_stmt_execute($result);

                                        echo "available";
                                        exit();
                                    }
                                }
                            } else {
                                $sql = "UPDATE students SET card_select=1 WHERE card_uid=?";
                                $result = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($result, $sql)) {
                                    echo "SQL_Error_insert_An_available_card";
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($result, "s", $card_uid);
                                    mysqli_stmt_execute($result);

                                    echo "available";
                                    exit();
                                }
                            }
                        }
                    }
                    //The Card is new
                    else {
                        $sql = "UPDATE students SET card_select=0";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo "SQL_Error_insert";
                            exit();
                        } else {
                            mysqli_stmt_execute($result);
                            $sql = "INSERT INTO students (card_uid, card_select, last_update) VALUES (?, 1, NOW())";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error_Select_add";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "s", $card_uid);
                                mysqli_stmt_execute($result);

                                echo "succesful";
                                exit();
                            }
                        }
                    }
                }
                //===========================================//
                // idle device
            } elseif ($device_mode == 4) {
                $sql = "SELECT * FROM students WHERE card_uid=?";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_Select_card";
                    exit();
                } else {
                    mysqli_stmt_bind_param($result, "s", $card_uid);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);
                    if ($row = mysqli_fetch_assoc($resultl)) {
                        logIntrusion($conn, $row['id'], $card_uid, $room, "IDLE_DEVICE", "Device is idle");
                        echo "Device is idle!";
                        exit();
                    } else {
                        logIntrusion($conn, 0, $card_uid, $room, "UNKNOWN_CARD", "Card not found");
                        echo "Not found!";
                        exit();
                    }
                }
                // ============================================// emergency exit
            } elseif ($device_mode == 5) {

                $today = date('Y-m-d');
                $time_now = date('H:i:s');

                /*
    ====================================
    FIND ACTIVE ATTENDANCE
    ====================================
    */
                $attendance = mysqli_query($conn, "
        SELECT *
        FROM atten_logs
        WHERE card_uid = '$card_uid'
        AND checkindate = '$today'
        AND card_out = 0
        ORDER BY id DESC
        LIMIT 1
    ");

                if (mysqli_num_rows($attendance) > 0) {

                    $log = mysqli_fetch_assoc($attendance);

                    /*
        ====================================
        FORCE EXIT
        ====================================
        */
                    mysqli_query($conn, "
            UPDATE atten_logs
            SET
                timeout = '$time_now',
                card_out = 1,
                remarks = 'EMERGENCY_EXIT'
            WHERE id = '" . $log['id'] . "'
        ");

                    /*
        ====================================
        LOG EVENT
        ====================================
        */
                    mysqli_query($conn, "
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
                '" . $log['sID'] . "',
                '$card_uid',
                '$room',
                'EMERGENCY_EXIT',
                '$today',
                'Student exited through emergency gate.'
            )
        ");

                    echo "EMERGENCY_EXIT_SUCCESS";
                } else {

                    echo "NO_ACTIVE_ATTENDANCE";
                }

                exit;
            }
        } else {
            logIntrusion($conn, 0, $card_uid ?? "UNKNOWN", "UNKNOWN", "INVALID_DEVICE", "Fake device token");
            echo "Invalid Device!";
            exit();
        }
    }
}
