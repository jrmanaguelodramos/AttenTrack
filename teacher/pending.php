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
    <title>Pending Classes</title>
    <style>
        /* =========================
        MODAL OVERLAY
        ========================= */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);

            display: flex;
            justify-content: center;
            align-items: center;

            z-index: 9999;
        }

        /* =========================
        MODAL BOX
        ========================= */
        .modal-box {
            width: 380px;
            background: #fff;
            border-radius: 12px;
            padding: 25px;

            text-align: center;

            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);

            animation: popIn 0.2s ease-in-out;
        }

        /* TITLE */
        .modal-box h2 {
            margin-bottom: 10px;
            font-size: 20px;
            color: #333;
        }

        /* TEXT */
        .modal-box p {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        /* =========================
        ACTION BUTTONS
        ========================= */
        .modal-box .actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .modal-box .actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;

            font-size: 14px;
            cursor: pointer;

            transition: 0.2s ease;
        }

        /* CANCEL BUTTON */
        .modal-box .actions button:first-child {
            background: #e5e7eb;
            color: #333;
        }

        .modal-box .actions button:first-child:hover {
            background: #d1d5db;
        }

        /* CONFIRM BUTTON */
        .modal-box .actions button:last-child {
            background: #3b82f6;
            color: white;
        }

        .modal-box .actions button:last-child:hover {
            background: #2563eb;
        }

        /* =========================
        ANIMATION
        ========================= */
        @keyframes popIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="container">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="profile-section">
                <div class="avatar-circle">
                    <i class="fa fa-user"></i>
                </div>

                <h2>
                    <?php echo htmlspecialchars($teacher['fname'] . " " . $teacher['lname']); ?>
                </h2>
                <p>Teacher</p>
            </div>

            <div class="menu">
                <a href="index.php">
                    <button class="pending">
                        <i class="fa-solid fa-book"></i> My Classes
                    </button>
                </a>

                <a href="../logout.php">
                    <button class="logout">
                        <i class="fa fa-sign-out-alt"></i> Log Out
                    </button>
                </a>
            </div>
        </div>

        <!-- MAIN -->
        <div class="main">

            <div class="main-header">
                <h1>Pending Classes</h1>

                <div class="header-controls">
                    <div class="search-box">
                        <i class="fa fa-search"></i>
                        <input type="text" id="search-input"
                            placeholder="Search classes..."
                            oninput="filterCards()">
                    </div>

                    <button class="btn-filter">
                        <i class="fa-solid fa-sliders"></i> Filter
                    </button>
                </div>
            </div>

            <div class="section-label">Waiting for Approval</div>

            <div class="card-container" id="card-container"></div>

            <!-- TEMPLATE -->
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
            <!-- ACCEPT MODAL -->
            <div id="acceptModal" class="modal-overlay" style="display:none;">
                <div class="modal-box">
                    <h2>Accept Class</h2>
                    <p>Do you want to accept this class?</p>

                    <div class="actions">
                        <button onclick="closeAcceptModal()">Cancel</button>
                        <button onclick="confirmAccept()">Yes, Accept</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        let allCards = [];

        function generateClassCards(data) {
            const container = document.getElementById('card-container');
            container.innerHTML = "";
            allCards = data;

            if (!data || data.length === 0) {
                container.innerHTML = '<p class="no-classes">No pending classes found.</p>';
                return;
            }

            renderCards(data);
        }

        function renderCards(data) {
            const container = document.getElementById('card-container');
            container.innerHTML = "";

            const template = document.getElementById('card-template');
            const days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

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

                newCard.querySelector('.class-schedule').textContent =
                    `${dayName} ${formattedTime}`;

                newCard.querySelector('.student-count').textContent =
                    (classInfo.student_count ?? 0) + ' students';

                newCard.style.borderLeftColor = ['#f5a623', '#3b82f6', '#10b981', '#8b5cf6', '#ef4444'][index % 5];

                newCard.addEventListener('click', () => {
                    selectedClassId = classInfo.id;
                    document.getElementById("acceptModal").style.display = "flex";
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
            fetch('get_classes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        tID: <?php echo (int)$tID; ?>,
                        accepted: 0
                    })
                })
                .then(res => res.json())
                .then(data => generateClassCards(data))
                .catch(err => {
                    console.error(err);
                    document.getElementById('card-container')
                        .innerHTML = "<p>Error loading pending classes.</p>";
                });
        };

        function closeAcceptModal() {
            document.getElementById("acceptModal").style.display = "none";
            selectedClassId = null;
        }

        function confirmAccept() {
            if (!selectedClassId) return;

            fetch("accept_class.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        class_id: selectedClassId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        window.location.href = `class.php?class=${selectedClassId}`;
                    } else {
                        alert(data.message || "Error accepting class");
                    }
                })
                .catch(() => {
                    alert("Server error");
                });
        }
    </script>

</body>

</html>