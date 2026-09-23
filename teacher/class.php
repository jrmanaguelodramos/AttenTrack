<?php
session_start();
require_once '../connectDB.php';
$class_id = isset($_GET['class']) ? (int) $_GET['class'] : 0;
if ($class_id <= 0) {
    exit("Invalid class ID.");
}

$sql = "SELECT * FROM classes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class_info = $stmt->get_result()->fetch_assoc();

if (isset($_GET['action']) && $_GET['action'] === 'get_rooms') {
    require_once '../connectDB.php';

    $sql = "SELECT room FROM devices WHERE device_mode = 4";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['room']}'>{$row['room']}</option>";
    }
    exit;
}
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var class_id = $('#class_id').val();

            function loadLog() {
                $.ajax({
                        url: "user_log_up.php",
                        type: 'POST',
                        data: {
                            class_id
                        }
                    })
                    .done(data => {
                        $('#userslog').html(data);
                        filterCards(); 
                    })
                    .fail(() => $('#userslog').html("<p>Error loading logs</p>"));
            }
            loadLog();
            setInterval(loadLog, 3000);
        });

        $(document).on("click", "#refresh-btn", function() {
            fetch(window.location.href + "&action=get_rooms")
                .then(res => res.text())
                .then(html => {
                    document.getElementById("room").innerHTML = html;
                    showToast("Rooms refreshed", "success");
                })
                .catch(() => {
                    showToast("Failed to refresh rooms", "error");
                });
        });

        function openModal() {
            document.getElementById("attendanceModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("attendanceModal").style.display = "none";
        }

        function openSettingsModal() {
            document.getElementById("settingsModal").style.display = "flex";
        }

        function closeSettingsModal() {
            document.getElementById("settingsModal").style.display = "none";
        }

        function openConfirmModal() {
            document.getElementById("confirmModal").style.display = "flex";
        }

        function closeConfirmModal() {
            document.getElementById("confirmModal").style.display = "none";
        }

        function validateSettings() {
            const inputs = document.querySelectorAll("#settingsForm input, #settingsForm select");
            let isValid = true;
            inputs.forEach(input => {
                if (input.value.trim() === "") {
                    isValid = false;
                    input.classList.add("input-error");
                } else {
                    input.classList.remove("input-error");
                }
            });
            if (!isValid) {
                showToast("Please fill in all required fields.", "error");
                return;
            }
            closeSettingsModal();
            openConfirmModal();
        }

        function submitSettings() {
            const btn = document.querySelector(".confirm-btn");
            btn.disabled = true;
            btn.textContent = "Saving...";

            const form = document.getElementById("settingsForm");
            const formData = new FormData(form);
            formData.append("class_id", <?php echo $class_id; ?>);

            fetch("save_settings.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    showToast("Settings saved successfully!", "success");
                    btn.disabled = false;
                    btn.textContent = "Confirm";
                    closeConfirmModal();
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.textContent = "Confirm";
                    showToast("Server error.", "error");
                });
        }

        window.onclick = function(event) {
            document.querySelectorAll(".modal-overlay").forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        }

        function startAttendance() {
            const class_id = document.getElementById("class_id").value;
            const mode = document.getElementById("mode").value;
            const type = document.getElementById("type").value;
            const room = document.getElementById("room").value;

            const startBtn = document.getElementById("startBtn");
            const stopBtn = document.getElementById("stopBtn");

            if (!mode || !type || !room) {
                showToast("Please complete all fields", "error");
                return;
            }

            startBtn.disabled = true;
            startBtn.textContent = "Starting...";

            fetch("start_attendance.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        class_id,
                        mode,
                        type,
                        room
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        showToast("Attendance started!", "success");
                        closeModal();

                        startBtn.style.display = "none";
                        stopBtn.style.display = "inline-block";
                        startBtn.textContent = "Start Attendance";
                        startBtn.disabled = false;

                    } else {
                        showToast(data.message, "error");
                        startBtn.disabled = false;
                        startBtn.textContent = "Start Attendance";
                    }
                })
                .catch(err => {
                    showToast("Error: " + err.message, "error");
                    startBtn.disabled = false;
                    startBtn.textContent = "Start Attendance";
                });
        }

        function stopAttendance() {
            const class_id = document.getElementById("class_id").value;

            const startBtn = document.getElementById("startBtn");
            const stopBtn = document.getElementById("stopBtn");

            stopBtn.disabled = true;
            stopBtn.textContent = "Stopping...";

            fetch("stop_attendance.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        class_id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        showToast("Attendance stopped!", "info");

                        stopBtn.style.display = "none";
                        startBtn.style.display = "inline-block";
                        stopBtn.textContent = "Stop Attendance";
                        stopBtn.disabled = false;
                    } else {
                        showToast(data.message || "Failed to stop", "error");
                        stopBtn.disabled = false;
                        stopBtn.textContent = "Stop Attendance";
                    }
                })
                .catch(err => {
                    showToast("Error: " + err.message, "error");
                    stopBtn.disabled = false;
                    stopBtn.textContent = "Stop Attendance";
                });
        }

        function showToast(message, type = "info") {
            const toastContainer = document.getElementById("toast");

            const toast = document.createElement("div");
            toast.className = `toast ${type}`;
            toast.textContent = message;

            toastContainer.appendChild(toast);

            // Trigger animation
            setTimeout(() => toast.classList.add("show"), 100);

            // Auto remove
            setTimeout(() => {
                toast.classList.remove("show");
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function filterCards() {
            let input = document.getElementById("search-input").value.toLowerCase();
            let status = document.getElementById("statusFilter").value;
            let rows = document.querySelectorAll(".student-row");

            rows.forEach(row => {

                let text = row.textContent.toLowerCase();
                let rowStatus = row.dataset.status;

                let matchesSearch = text.includes(input);

                let matchesStatus =
                    status === "all" ||
                    rowStatus === status;

                row.style.display =
                    matchesSearch && matchesStatus ? "" : "none";
            });
        }
    </script>
</head>

<body>

    <div class="container">
        <?php include 'class_header.php'; ?>
        <input type="hidden" id="class_id" value="<?php echo $class_id; ?>">

        <div class="main">
            <div class="top-bar">
                <div class="header-controls">
                    <div class="search-box">
                        <i class="fa fa-search"></i>
                        <input type="text" id="search-input" placeholder="Search student" oninput="filterCards()">
                    </div>
                    <select id="statusFilter" class="btn-filter" onchange="filterCards()">
                        <option value="all" disabled selected>Filter</option>
                        <option value="all">All</option>
                        <option value="On_Time">On Time</option>
                        <option value="Late">Late</option>
                        <option value="Absent">Absent</option>
                    </select>
                    <button class="btn-settings" onclick="openSettingsModal()">
                        <i class="fa-solid fa-gear"></i> Attendance Settings
                    </button>
                    <?php
                    $sql = "SELECT status 
                    FROM attendance_sessions 
                    WHERE class_id = ? 
                    ORDER BY id DESC 
                    LIMIT 1";

                    $stmt = $conn->prepare($sql);

                    if (!$stmt) {
                        die("DB error: " . $conn->error);
                    }

                    $stmt->bind_param("i", $class_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();

                    $isActive = ($row && $row['status'] === 'active');

                    $sql = "SELECT room FROM devices WHERE device_mode = 4";
                    $result = $conn->query($sql);

                    $rooms = [];

                    while ($row = $result->fetch_assoc()) {
                        $rooms[] = $row['room'];
                    }
                    ?>

                    <button class="btn-attendance" id="startBtn"
                        style="display: <?= $isActive ? 'none' : 'inline-block' ?>;"
                        onclick="openModal()">
                        <i class="fa-solid fa-play"></i> Start Attendance
                    </button>

                    <button class="btn-attendance" id="stopBtn"
                        style="display: <?= $isActive ? 'inline-block' : 'none' ?>;"
                        onclick="stopAttendance()">
                        <i class="fa-solid fa-pause"></i> Stop Attendance
                    </button>
                </div>
            </div>

            <div id="userslog"></div>
        </div>
    </div>

    <div id="attendanceModal" class="modal-overlay">
        <div class="modal-box">
            <h2>START ATTENDANCE</h2>
            <label for="mode">Mode</label>
            <select id="mode">
                <option>RFID SCAN</option>
                <option>MANUAL</option>
            </select>

            <select id="type">
                <option>ATTENDANCE IN</option>
                <option>ATTENDANCE OUT</option>
            </select>
            <label for="room">Room</label>
            <div class="room-row">
                <select id="room">
                    <?php foreach ($rooms as $r): ?>
                        <option value="<?= $r ?>">
                            <?= $r ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="refresh-btn" id="refresh-btn">Refresh</button>
            </div>
            <div class="actions">
                <button class="cancel" onclick="closeModal()">Cancel</button>
                <button class="continue" onclick="startAttendance()">Continue</button>
            </div>
        </div>
    </div>

    <div id="settingsModal" class="modal-overlay">
        <div class="settings-modal-box">
            <div class="modal-title">Attendance Settings</div>
            <div class="modal-subtitle">Configure rules for this class</div>

            <form id="settingsForm">
                <div class="field-label">
                    <i class="fa-solid fa-clock"></i> Time In Duration (minutes)
                </div>
                <input type="number" name="time_in" min="0" placeholder="e.g. 15"
                    value="<?php echo isset($class_info['grace']) ? htmlspecialchars($class_info['grace']) : ''; ?>">

                <div class="field-label">
                    <i class="fa-solid fa-hourglass-half"></i> Class Duration (minutes)
                </div>
                <input type="number" name="time_out" min="1" placeholder="e.g. 180"
                    value="<?php echo isset($class_info['class_duration']) ? htmlspecialchars($class_info['class_duration']) : ''; ?>">

                <hr class="settings-divider">

                <div class="field-label">
                    <i class="fa-solid fa-user-xmark"></i> Max Absentees
                </div>
                <select name="max_absentees">
                    <option value="">Select</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= (isset($class_info['max_absentees']) && $class_info['max_absentees'] == $i) ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </form>

            <div class="settings-modal-actions">
                <button class="btn-cancel-link" onclick="closeSettingsModal()">CANCEL</button>
                <button class="btn-save-settings" onclick="validateSettings()">SAVE</button>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="modal-overlay">
        <div class="confirm-modal-box">
            <div class="confirm-icon">
                <i class="fa-solid fa-floppy-disk"></i>
            </div>
            <h3>Confirm Save</h3>
            <p>Are you sure you want to save these attendance settings?</p>
            <div class="confirm-modal-actions">
                <button class="cancel-btn" onclick="closeConfirmModal(); openSettingsModal();">Back</button>
                <button class="confirm-btn" onclick="submitSettings()">Confirm</button>
            </div>
        </div>
    </div>

    <div id="toast"></div>
</body>

</html>