<?php
session_start();
//Connect to database
require_once '../connectDB.php';
// echo $class_id;
// if (isset($_GET['id'])) {
//     $class_id = $_GET['id'];
// } else {
//     echo "Class ID not provided.";
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Class</title>
    <!-- <link rel="stylesheet" href="css/atten_setting.css"> -->
    <link rel="stylesheet" href="css/class.css">
    <link rel="stylesheet" href="css/userslog.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="js/user_log.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"
        integrity="sha1256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
        crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function() {
            var class_id = $('#class_id').val();
            $.ajax({
                url: "user_log_up.php",
                type: 'POST',
                data: {
                    class_id: class_id
                }
            }).done(function(data) {
                $('#userslog').html(data);
            });

            setInterval(function() {
                $.ajax({
                    url: "user_log_up.php",
                    type: 'POST',
                    data: {
                        class_id: class_id
                    }
                }).done(function(data) {
                    $('#userslog').html(data);
                });
            }, 2000);
        });

        function openModal() {
            document.getElementById("attendanceModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("attendanceModal").style.display = "none";
        }
    </script>
</head>

<body>

    <div class="container">
        <?php include 'class_header.php'; ?>
        <input type="hidden" id="class_id" value="<?php echo $class_id; ?>">

        <div class="main">
            <input type="text" id="searchInput" placeholder="Search by student name or ID..." onkeyup="filterTable()">
            <button>filter</button>
            <a href="atten_setting.php?class=<?php echo $class_id; ?>"><button>Attendance Settings</button></a>
            <button onclick="openModal()">Start Attendance</button>
            <div id="userslog"></div>
        </div>
    </div>

    <div id="attendanceModal" class="modal">
        <div class="modal-box">
            <h2>START ATTENDANCE</h2>

            <label>MODE</label>
            <select>
                <option>RFID SCAN</option>
                <option>MANUAL</option>
            </select>

            <select>
                <option>ATTENDANCE IN</option>
                <option>ATTENDANCE OUT</option>
            </select>

            <label>ROOM</label>
            <div class="room-row">
                <select>
                    <option>IL101</option>
                </select>
                <button class="connect-btn">CONNECT</button>
            </div>

            <p class="status">DEVICE CONNECTION STATUS : <span>CONNECTED</span></p>

            <div class="actions">
                <button class="cancel" onclick="closeModal()">Cancel</button>
                <button class="continue">Continue</button>
            </div>
        </div>
    </div>

</body>

</html>