 <?php require_once '../connectDB.php';
    if (isset($_GET['class'])) {
        $class_id = $_GET['class'];
        $sql = "SELECT * FROM classes WHERE id = ?";
        $result = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($result, $sql)) {
            mysqli_stmt_bind_param($result, "i", $class_id);
            mysqli_stmt_execute($result);
            $result = mysqli_stmt_get_result($result);
            $class_info = mysqli_fetch_assoc($result);
        } else {
            echo "Class not found.";
            exit;
        }
    }
    ?>
 <div class="sidebar">
     <div class="class_info">
         <div class="course-section" id="course-section"><?php echo $class_info['section']; ?></div>
         <div class="course-details">
             <p id="course-code" style="font-size: 18px;"><?php echo $class_info['subject'] . ' ' . $class_info['course_code']; ?></p>
             <p id="course-schedule"><?php switch ($class_info['schedule']) {
                                            case '1':
                                                echo "Monday";
                                                break;
                                            case '2':
                                                echo "Tuesday";
                                                break;
                                            case '3':
                                                echo "Wednesday";
                                                break;
                                            case '4':
                                                echo "Thursday";
                                                break;
                                            case '5':
                                                echo "Friday";
                                                break;
                                            case '6':
                                                echo "Saturday";
                                            default:
                                                echo "N/A";
                                                break;
                                        } ?></p>
             <p id="course-schedule">(<?php if (!empty($class_info['time_start'])) {
                                            // Convert SQL timestamp to PHP DateTime object
                                            $time = new DateTime($class_info['time_start']);
                                            echo $time->format('g:i A');
                                        } else {
                                            echo "No time available";
                                        }
                                        ?>)</p>
             <p id="teacher">Teacher: <?php $sql = "SELECT `fname`, `mname`, `lname` FROM teachers WHERE id = " . $class_info['tID'];
                                        $result = mysqli_query($conn, $sql);
                                        if ($result && mysqli_num_rows($result) > 0) {
                                            $teacher_info = mysqli_fetch_assoc($result);
                                            echo $teacher_info['fname'] . " " . $teacher_info['mname'] . " " . $teacher_info['lname'];
                                        } else {
                                            echo "Teacher not found.";
                                        }
                                        ?></p>
             <p id="students"><?php $sql = "SELECT COUNT(id) AS total_students FROM class WHERE classID = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $class_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();

                                echo $row['total_students']; ?> Students</p>
         </div>
     </div>

     <div class="menu">
         <a href="pending.html"><button class="dashboard">
                 <i class="fa-solid fa-dashboard"></i> ㅤㅤDashboard
             </button></a>

         <a href="pending.html"><button class="add">
                 <i class="fa-solid fa-user-plus"></i> ㅤㅤAdd Student
             </button></a>

         <a href="classes.html"><button class="pending">
                 <i class="fa-solid fa-book"></i> ㅤㅤManage Class
             </button></a>

         <a href="login.html"><button class="logout">
                 <i class="fa fa-sign-out-alt"></i>ㅤㅤLog Out
             </button></a>

     </div>
 </div>