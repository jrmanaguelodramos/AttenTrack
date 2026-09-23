<?php
require_once '../connectDB.php';
if (!isset($_GET['class'])) {
    echo "Class ID not provided.";
    exit;
}
$class_id = $_GET['class'];
$sql = "SELECT s.*, c.status 
        FROM students s
        INNER JOIN class c ON c.sID = s.id
        WHERE c.classID = ?
        ORDER BY FIELD(c.status, 'Active', 'Critical', 'Dropped'),
                s.lname ASC, s.fname";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
$grouped = [];
while ($row = $result->fetch_assoc()) {
    $status = ucfirst(strtolower($row['status'] ?? 'Active'));

    if ($status == "Not enrolled") {
        continue;
    }

    $grouped[$status][] = $row;
}
unset($students);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/class_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="css/header.css"> -->
    <link rel="stylesheet" href="css/class.css">
</head>

<body>
    <div class="main_container">
        <div class="sidebar">
            <?php include 'class_header.php'; ?>
        </div>
        <div class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <a class="back-btn" href="class.php?class=<?= $class_id ?>">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <div class="title-box">
                        <span class="mini-title">Class Management</span>
                        <h2>ENROLLED STUDENTS</h2>
                    </div>
                </div>
                <a href="analytics.php?class=<?= $class_id ?>">
                    <button class="btn-analytics">
                        <i class="fa-solid fa-chart-line"></i>
                        Analytics
                    </button>
                </a>
            </div>
            <?php foreach ($grouped as $status => $students): ?>
                <div class="group-box">
                    <div class="status-header status-<?= $status ?>">
                        <?= strtoupper($status) ?> (<?= count($students) ?>)
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th><i class="fa-solid fa-user-graduate"></i> Name</th>
                                <th><i class="fa-solid fa-address-card"></i> Student ID</th>
                                <th><i class="fa-solid fa-users"></i> Section</th>
                                <th><i class="fa-solid fa-bars-progress"></i> Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $row):
                                $name = htmlspecialchars(
                                    $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname']
                                );
                            ?>
                                <tr onclick="window.location='student_profile.php?id=<?= $row['id'] ?>&class=<?= $class_id ?>'" style="cursor:pointer;">
                                    <td><strong><?= $name ?></strong></td>
                                    <td><?= htmlspecialchars($row['stud_num']) ?></td>
                                    <td><?= htmlspecialchars($row['section']) ?></td>
                                    <td onclick="event.stopPropagation()">
                                        <select class="status-dropdown"
                                            data-sid="<?= $row['id'] ?>"
                                            data-class="<?= $class_id ?>">
                                            <option value="Active" <?= $status == "Active" ? 'selected' : '' ?>>Active</option>
                                            <option value="Critical" <?= $status == "Critical" ? 'selected' : '' ?>>Critical</option>
                                            <option value="Dropped" <?= $status == "Dropped" ? 'selected' : '' ?>>Dropped</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).on('change', '.status-dropdown', function() {
            let sID = $(this).data('sid');
            let classID = $(this).data('class');
            let status = $(this).val();
            let dropdown = $(this);
            $.ajax({
                url: "update_student_status.php",
                method: "POST",
                data: {
                    sID: sID,
                    classID: classID,
                    status: status
                },
                success: function(res) {
                    if (res.trim() === "success") {
                        dropdown.css("border", "2px solid green");
                        setTimeout(() => {
                            dropdown.css("border", "");
                            location.reload();
                        }, 500);
                    } else {
                        alert("Update failed: " + res);
                    }
                }
            });
        });

        function openLogoutModal() {
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