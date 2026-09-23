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

    <style>
           .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-overlay h2{
        margin-bottom: 10px;
    }

    .modal-box {
        width: 360px;
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        animation: popIn 0.2s ease;
    }

    .actions {
        display: flex;
        gap: 40px;
    }

    .actions button {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 30px;
    }

    .actions button:first-child {
        background: #e5e7eb;
    }

    .actions button:last-child {
        background: #ef4444;
        color: white;
    }

    @keyframes popIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .modal-icon {
        font-size: 50px;
        color: #ef4444;
        margin-bottom: 10px;
    }

    .actions button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: transform 0.3s;
    }

    .actions button:hover{
        transform: translateY(-4px);
    }

    .actions button i {
        font-size: 14px;
    }

    button{
        margin-top: 20px;
    }

    .logout-btn:hover {
        background: rgba(231,76,60,0.15) !important;
        color: #e74c3c !important;
    }
    </style>


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
         <a href="class_dashboard.php?class=<?= $class_id ?>"><button class="dashboard">
                 <i class="fa-solid fa-dashboard"></i> ㅤㅤDashboard
             </button></a>

         <a href="add_student_class.php?class=<?= $class_id ?>"><button class="add">
                 <i class="fa-solid fa-user-plus"></i> ㅤㅤAdd Student
             </button></a>

         <a href="index.php"><button class="pending">
                 <i class="fa-solid fa-book"></i> ㅤㅤManage Class
             </button></a>

         <button class="logout" onclick="openLogoutModal(event)">
            <i class="fa fa-sign-out-alt"></i>ㅤㅤLog Out
        </button>
     </div>
 </div>


 <div id="logoutModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fa-solid fa-right-from-bracket"></i>
        </div>

        <h2>Confirm Logout</h2>
        <p style="color:black;">Are you sure you want to log out?</p>

        <div class="actions">
            <button onclick="closeLogoutModal()">Cancel</button>
            <button onclick="confirmLogout()">Logout</button>
        </div>
    </div>
</div>


<script>
    function openLogoutModal(event) {
    if (event) event.preventDefault();
    document.getElementById("logoutModal").style.display = "flex";
    }

    function closeLogoutModal() {
        document.getElementById("logoutModal").style.display = "none";
    }

    function confirmLogout() {
        window.location.href = "../logout.php";
    }
</script>