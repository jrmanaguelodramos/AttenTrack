<?php
require_once '../connectDB.php';

if (!isset($_GET['class'])) {
    die("Class not found");
}

$class_id = $_GET['class'];

/* =========================
   GET DEVICES
========================= */
$sql = "SELECT room FROM devices WHERE device_mode = 4";
$result = $conn->query($sql);

$rooms = [];

while ($row = $result->fetch_assoc()) {
    $rooms[] = $row['room'];
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Student</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            padding: 50px;
        }

        .box {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #243e63;
            margin-bottom: 20px;
        }

        select {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .student-box {
            margin-top: 20px;
            padding: 20px;
            border-radius: 15px;
            background: #f0f4ff;
            display: none;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
        }

        .confirm {
            background: #28a745;
            color: white;
        }

        .back {
            background: #243e63;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .waiting {
            color: #666;
            margin-top: 20px;
        }

        .student-card {
            margin-top: 20px;
            padding: 20px;
            border-radius: 15px;
            background: #f0f4ff;
            text-align: left;
        }

        .student-card h3 {
            margin-bottom: 10px;
            color: #243e63;
        }

        .student-card p {
            margin-bottom: 8px;
            color: #555;
        }
    </style>
    <link rel="stylesheet" href="css/class.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <?php include 'class_header.php'; ?>

    <div class="box">
        <h1>SCAN STUDENT CARD</h1>

        <select id="room">
            <?php foreach ($rooms as $r): ?>
                <option value="<?= $r ?>">
                    <?= $r ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn confirm" id="scanBtn" onclick="toggleScan()">
            Start Scanning
        </button>

        <div class="waiting" id="status">
            Waiting to start...
        </div>

        <!-- 🔥 STUDENT LIST -->
        <div id="studentList"></div>

        <button class="btn back" onclick="confirmLeave()">
            Back
        </button>

    </div>

    <script>
        let polling = null;
        let scanning = false;

        /* =========================
           TOGGLE SCAN
        ========================= */
        function toggleScan() {

            if (!scanning) {
                startScan();
            } else {
                stopScan();
            }
        }

        /* =========================
           START SCAN
        ========================= */
        function startScan() {

            const room = document.getElementById("room").value;

            fetch("set_device_mode.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        room: room,
                        mode: 3,
                        class_id: <?= $class_id ?>
                    })
                })
                .then(res => res.text())
                .then(data => {

                    scanning = true;

                    document.getElementById("status").innerHTML =
                        "Waiting for RFID scan...";

                    const btn = document.getElementById("scanBtn");

                    btn.innerHTML = "Stop Scanning";
                    btn.style.background = "#dc3545";

                    loadStudents();

                    if (polling) {
                        clearInterval(polling);
                    }

                    polling = setInterval(loadStudents, 2000);
                });
        }

        /* =========================
           STOP SCAN
        ========================= */
        function stopScan(goBack = false) {

            clearInterval(polling);

            polling = null;

            scanning = false;

            document.getElementById("status").innerHTML =
                "Scanning stopped.";

            const btn = document.getElementById("scanBtn");

            btn.innerHTML = "Start Scanning";
            btn.style.background = "#28a745";

            const room = document.getElementById("room").value;

            fetch("set_device_mode.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        room: room,
                        mode: 4,
                        class_id: <?= $class_id ?>
                    })
                })
                .then(() => {

                    if (goBack) {
                        history.back();
                    }
                });
        }
        /* =========================
           LOAD STUDENTS
        ========================= */
        function loadStudents() {

            fetch("get_scanned_student.php?class=<?= $class_id ?>")
                .then(res => res.json())
                .then(data => {

                    let html = "";

                    if (!data.students || data.students.length === 0) {

                        html = `
                <div class="waiting">
                    No scanned students yet...
                </div>
            `;

                    } else {

                        data.students.forEach(student => {

                            html += `
                    <div class="student-card">

                        <h3>
                            ${student.fname}
                            ${student.lname}
                        </h3>

                        <p>
                            Student #: ${student.stud_num}
                        </p>

                        <p>
                            Section: ${student.section}
                        </p>

                        <button 
                            class="btn confirm"
                            onclick="confirmEnroll(${student.id})">

                            Confirm Enrollment
                        </button>

                    </div>
                `;
                        });
                    }

                    document.getElementById("studentList").innerHTML = html;
                });
        }

        /* =========================
           CONFIRM ENROLLMENT
        ========================= */
        function confirmEnroll(classRowID) {

            fetch("confirm_enrollment.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        classRowID: classRowID
                    })
                })
                .then(res => res.text())
                .then(data => {

                    alert(data);

                    loadStudents();
                });
        }
        /* =========================
   WARN BEFORE LEAVING
========================= */
        window.addEventListener("beforeunload", function() {

            if (scanning) {
                stopScan();
            }
        });

        function confirmLeave() {

            if (scanning) {

                const leave = confirm(
                    "Scanning is still active. Stop scanning and leave?"
                );

                if (!leave) {
                    return;
                }

                stopScan(true);

            } else {

                history.back();
            }
        }
    </script>

</body>

</html>