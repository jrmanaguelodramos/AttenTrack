<?php
//Connect to database
require '../connectDB.php';
date_default_timezone_set('Asia/Manila');
$d = date("Y-m-d");
$t = date("h:i:s A");

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
                        $sName = $row['fname']. " ". $row['mname']. " " . $row['lname'];
                        $sID = $row['id'];
                        $section = $row['section'];
                        $student_number = $row['stud_num'];
                        //*****************************************************
                        //An existed Card has been detected for Login
                        if ($row['add_card'] == 1) {
                            $sql = "SELECT * FROM classes WHERE room=?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error_Select_class";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "s", $room);
                                mysqli_stmt_execute($result);
                                $resultl = mysqli_stmt_get_result($result);
                                if ($row = mysqli_fetch_assoc($resultl)) {
                                    $class_id = $row['id'];
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

                                                    $sql = "INSERT INTO atten_logs (`sID`, sName, cID, room, section, stud_num, card_uid, checkindate, timein, `timeout`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                                    $result = mysqli_stmt_init($conn);
                                                    if (!mysqli_stmt_prepare($result, $sql)) {
                                                        echo "SQL_Error_Select_login1";
                                                        exit();
                                                    } else {
                                                        $timeout = "00:00:00";
                                                        mysqli_stmt_bind_param($result, "isisssssss", $sID, $sName, $class_id, $room, $section, $student_number, $card_uid, $d, $t, $timeout);
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
                                                    echo "already logged in";
                                                    exit();
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    echo "Class not found!";
                                    exit();
                                }
                            }
                        } else if ($row['add_card'] == 0) {
                            echo "Not registerd!";
                            exit();
                        }
                    } else {
                        echo "Not found!";
                        exit();
                    }
                }
            }
            //================================================================//
            // device for attendance out
            else if ($device_mode == 2) {
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
                            $sql = "SELECT * FROM classes WHERE room=?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error_Select_class";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "s", $room);
                                mysqli_stmt_execute($result);
                                $resultl = mysqli_stmt_get_result($result);
                                if ($row = mysqli_fetch_assoc($resultl)) {
                                    $class_id = $row['id'];
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
                                            $sql = "INSERT INTO class (`sID`, cID, card_uid) VALUES (?, ?, ?)";
                                            $result = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($result, $sql)) {
                                                echo "SQL_Error_Select_class_card";
                                                exit();
                                            } else {
                                                mysqli_stmt_bind_param($result, "iis", $sID, $class_id, $card_uid);
                                                mysqli_stmt_execute($result);

                                                echo "enrolled";
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
                            echo "Not registerd!";
                            exit();
                        }
                    } else {
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
            }
        } else {
            echo "Invalid Device!";
            exit();
        }
    }
}
