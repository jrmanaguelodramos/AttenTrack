<?php
session_start();
require_once '../connectDB.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
if (isset($_GET['class'])) {
    $class_id = $_GET['class'];
} else {
    echo "Class ID not provided.";
    exit;
}
$sql = "SELECT * FROM classes WHERE id = ? AND tID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $class_id, $_SESSION['tID']);
$stmt->execute();
$result = $stmt->get_result();
$class_info = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="stylesheet" href="css\atten_settings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="container">
        <?php include 'class_header.php'; ?>
        <div class="main">
            <h1>ATTENDANCE SETTINGS</h1>
            <div class="settings_container">
                <form id="settingsForm">
                    <label><i class="fa-solid fa-clock"></i> TIME IN DURATION (minutes)</label>
                    <input type="number" id="time-in" name="time_in" min="0" placeholder="e.g. 15" value="<?php echo isset($class_info['grace']) ? $class_info['grace'] : ''; ?>">

                    <label><i class="fa-solid fa-clock"></i> CLASS DURATION (minutes)</label>
                    <input type="number" id="time-out" name="time_out" min="1" placeholder="e.g. 90" value="<?php echo isset($class_info['class_duration']) ? $class_info['class_duration'] : ''; ?>">

                    <label>MAX ABSENTEES</label>
                    <select id="max-absentees" name="max_absentees">
                        <option value="">Select</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= ($class_info['max_absentees'] == $i) ? 'selected' : '' ?>>
                                <?= $i ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </form>
                <div class="modal-buttons">
                    <a href="class.php?class=<?php echo $class_id ?>" class="cancel">CANCEL</a>
                    <button class="save" onclick="validateForm()">SAVE</button>
                </div>

                <div id="confirmModal" class="modal">
                    <div class="modal-content">
                        <h3 style="text-align: center;">Confirm Save</h3>
                        <p>Are you sure you want to save this settings?</p>
                        <button class="confirm" onclick="submitForm()">CONFIRM</button>
                        <button class="cancel2" onclick="closeModal()">CANCEL</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            const inputs = document.querySelectorAll("input, select");
            let isValid = true;

            inputs.forEach(input => {
                if (input.value.trim() === "") {
                    isValid = false;
                    input.style.border = "2px solid red";
                } else {
                    input.style.border = "1px solid #ccc";
                }
            });

            const timeInVal = parseInt(timeIn);
            const timeOutVal = parseInt(timeOut);

            if (timeInVal < 0 || timeOutVal <= 0) {
                alert("Invalid duration values.");
                return;
            }

            if (!isValid) {
                alert("Please fill in all required fields.");
                return;
            }

            document.getElementById("confirmModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("confirmModal").style.display = "none";
        }

        function submitForm() {
            closeModal();

            const btn = document.querySelector(".confirm");
            btn.disabled = true;

            const form = document.getElementById("settingsForm");
            const formData = new FormData(form);
            formData.append("class_id", <?php echo $class_id; ?>);

            fetch("save_settings.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;

                    if (data.status === "success") {
                        alert("Settings saved successfully!");
                        location.reload(); // refresh values
                    } else {
                        alert("Failed to save settings.");
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    alert("Server error.");
                });
        }
    </script>
</body>

</html>