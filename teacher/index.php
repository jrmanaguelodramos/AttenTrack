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
            <div class="profile-section">
                <div class="avatar-circle">
                    <i class="fa fa-user"></i>
                </div>
                <h2><?php echo htmlspecialchars($teacher['fname'] . " " . $teacher['lname']); ?></h2>
                <p>Teacher</p>
            </div>

            <div class="menu">
                <a href="pending.php">
                    <button class="pending">
                        <i class="fa-solid fa-book"></i> Pending
                        <span class="info" hidden></span>
                    </button>
                </a>
               <button class="logout" onclick="openLogoutModal(event)">
                <i class="fa fa-sign-out-alt"></i> Log Out
            </button>
            </div>
        </div>

        <div class="main">

            <div class="main-header">
                <h1>My Classes</h1>
                <div class="header-controls">
                    <div class="search-box">
                        <i class="fa fa-search"></i>
                        <input type="text" id="search-input" placeholder="Search classes..." oninput="filterCards()">
                    </div>
                    <button class="btn-filter">
                        <i class="fa-solid fa-sliders"></i> Filter
                    </button>
                </div>
            </div>


            <div class="section-label">Active This Semester</div>

            <div class="card-container" id="card-container"></div>

            <div class="card" id="card-template" style="display:none;">
                <h2 class="class-sec"></h2>
                <p class="course-name"></p>
                <div class="card-footer">
                    <small class="class-schedule"></small>
                    <span class="dot"></span>
                    <span class="student-badge">
                        <i class="fa-solid fa-users" style="font-size:11px;"></i>
                        <span class="student-count"></span>
                    </span>
                </div>
            </div>

        </div>
    </div>

     <div id="logoutModal" class="modal-overlay">
    <div class="modal-box">
       <div class="modal-icon">
        <i class="fa-solid fa-right-from-bracket"></i>
      </div>
      <h2>Confirm Logout</h2>
      <p>Are you sure you want to log out?</p>

      <div class="actions">
        <button onclick="closeLogoutModal()">Cancel</button>
        <button onclick="confirmLogout()">Logout</button>
      </div>
    </div>
  </div>

    <script>
        let allCards = [];

        function generateClassCards(data) {
            const container = document.getElementById('card-container');
            container.innerHTML = "";
            allCards = data;

            const template = document.getElementById('card-template');
            const days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

            if (!data || data.length === 0) {
                container.innerHTML = '<p class="no-classes">No classes found.</p>';
                return;
            }

            renderCards(data);
        }

        function renderCards(data) {
            const container = document.getElementById('card-container');
            container.innerHTML = "";
            const template = document.getElementById('card-template');
            const days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

            if (!data || data.length === 0) {
                container.innerHTML = '<p class="no-classes">No classes found.</p>';
                return;
            }

            data.forEach((classInfo, index) => {
                const newCard = template.cloneNode(true);
                newCard.removeAttribute('id');

                newCard.querySelector('.class-sec').textContent = classInfo.section;
                newCard.querySelector('.course-name').textContent =
                    `${classInfo.subject} (${classInfo.course_code})`;

                const dayIndex = parseInt(classInfo.schedule) - 1;
                const dayName = days[dayIndex] || "N/A";

                const timeParts = (classInfo.time_start || "00:00").split(":");
                const hour = parseInt(timeParts[0]) || 0;
                const minute = parseInt(timeParts[1]) || 0;
                const timeObj = new Date();
                timeObj.setHours(hour, minute, 0, 0);
                const formattedTime = timeObj.toLocaleTimeString([], {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });

                newCard.querySelector('.class-schedule').textContent = `${dayName} ${formattedTime}`;
                newCard.querySelector('.student-count').textContent =
                    (classInfo.student_count ?? 0) + ' students';

                const colors = ['#f5a623', '#3b82f6', '#10b981', '#8b5cf6', '#ef4444'];
                newCard.style.borderLeftColor = colors[index % colors.length];

                newCard.addEventListener('click', () => {
                    window.location.href = `class.php?class=${classInfo.id}`;
                });

                newCard.style.display = 'block';
                container.appendChild(newCard);
            });
        }

        function filterCards() {
            const q = document.getElementById('search-input').value.toLowerCase();
            const filtered = allCards.filter(c =>
                (c.section || '').toLowerCase().includes(q) ||
                (c.subject || '').toLowerCase().includes(q) ||
                (c.course_code || '').toLowerCase().includes(q)
            );
            renderCards(filtered);
        }

        window.onload = function() {
            const data = {
                tID: <?php echo (int)$tID; ?>,
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
                .then(data => generateClassCards(data));
        };

        function openLogoutModal(event) {
        event.preventDefault(); 
        document.getElementById("logoutModal").style.display = "flex";
        }

        function closeLogoutModal() {
            document.getElementById("logoutModal").style.display = "none";
        }

        function confirmLogout() {
            window.location.href = "../logout.php";
        }

        window.addEventListener("click", function(e) {
            const modal = document.getElementById("logoutModal");
            if (e.target === modal) {
                closeLogoutModal();
            }
        });
    </script>

</body>

</html>