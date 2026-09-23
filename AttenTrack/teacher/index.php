<?php
session_start();
require_once '../connectDB.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$sql = "SELECT * FROM teachers WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();
$tID = $teacher['id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Classes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="container">
        <div class="sidebar">
            <div class="profile">
                <i class="fa fa-user-circle"></i>
            </div>
            <div>
                <h2><?php echo $teacher['fname'] . " " . $teacher['lname']; ?></h2>
                <p>Teacher</p>
            </div>

            <div class="menu">
                <a href="pending.html"><button class="pending">
                        <i class="fa-solid fa-book"></i> ㅤㅤPending
                        <span class="info" hidden> </span> <!-- eto yung sa notif popup -->
                    </button></a>

                <a href="../logout.php"><button class="logout">
                        <i class="fa fa-sign-out-alt"></i>ㅤㅤLog Out
                    </button></a>

            </div>
        </div>


        <div class="main">
            <h1>My CLASSES</h1>

            <div class="filter">
                <i class="fa fa-filter"></i>
                <span>CLASS</span>
            </div>

            <div class="card-container" id="card-container"></div>
            <div class="card" id="card-template" style="display: none;">
                <h2 class="class-sec"></h2>
                <p class="course-name"></p>
                <small class="class-schedule"></small>
            </div>
        </div>

    </div>

    <script>
        function generateClassCards(data) {
            const container = document.getElementById('card-container');
            container.innerHTML = "";

            const template = document.getElementById('card-template');
            const days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

            if (!data || data.length === 0) {
                container.innerHTML = "<p>No classes found.</p>";
                return;
            }

            data.forEach((classInfo) => {
                const newCard = template.cloneNode(true);
                newCard.removeAttribute('id');

                newCard.querySelector('.class-sec').innerText = classInfo.section;
                newCard.querySelector('.course-name').innerText =
                    `${classInfo.subject} (${classInfo.course_code})`;

                const dayName = days[classInfo.schedule - 1] || "N/A";

                const [hour, minute] = classInfo.time_start.split(":");
                const timeObj = new Date();
                timeObj.setHours(hour, minute, 0, 0);

                const formattedTime = timeObj.toLocaleTimeString([], {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });

                newCard.querySelector('.class-schedule').innerText =
                    `${dayName} ${formattedTime}`;

                newCard.addEventListener('click', () => {
                    window.location.href = `class.php?class=${classInfo.id}`;
                });

                newCard.style.display = 'block';
                container.appendChild(newCard);

                console.log(classInfo);
            });
        }

        // FETCH DATA FROM PHP
        window.onload = function() {
            const data = {
                tID: <?php echo $tID; ?>,
                accepted: 1
            };

            fetch('get_classes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(data => {
                    generateClassCards(data);
                });
        };
    </script>
</body>

</html>