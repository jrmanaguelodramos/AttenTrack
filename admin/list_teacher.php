<?php include_once '../connectDB.php';
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Teachers</title>
    <link rel="stylesheet" href="css/list.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-box {
            background: #fff;
            width: 350px;
            margin: 8% auto;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .modal-box input,
        .modal-box select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .edit-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 6px;
            cursor: pointer;
        }

        .delete-btn {
            background: #d9534f;
            color: white;
            border: none;
            padding: 6px;
            cursor: pointer;
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            min-width: 260px;
            background: #28a745;
            color: white;
            padding: 14px 18px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s ease;
            z-index: 9999;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.error {
            background: #dc3545;
        }

        .teacher-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

      
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <div class="title-box">
            LIST OF TEACHERS
        </div>

        <a href="dashboard.php" class="back-btn">←</a>
    </div>

    <div class="top-controls">

        <div class="teacher-search">
            <form method="GET" action="list_teacher.php" class="search-box">

                <input type="text" name="search" id="searchInput"
                    placeholder="Search teachers..."
                    value="<?php echo htmlspecialchars($search); ?>">

                <button type="button" class="clear-btn" onclick="clearSearch()">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <button type="submit" class="search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

            </form>
        </div>


        <div class="teacher-actions">
            <a href="archiveTeachers.php" class="archive-btn">
                <i class="fa-solid fa-box-archive"></i> Archive
            </a>

            <a href="teacherReg.php" class="add-btn">
                <i class="fa-solid fa-user-plus"></i>Add Teacher
            </a>
        </div>

    </div>

    <div class="teacher-list">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fa-solid fa-chalkboard-teacher"></i> NAME</th>
                    <th><i class="fa-solid fa-building"></i> DEPARTMENT</th>
                    <th><i class="fa-solid fa-gear"></i> ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $limit = 10;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $page = max($page, 1);
                $offset = ($page - 1) * $limit;

                $searchParam = "%$search%";

                $sql = "SELECT * FROM teachers 
                    WHERE (fname LIKE ? OR lname LIKE ?) AND archive = 0
                    ORDER BY lname ASC
                    LIMIT ? OFFSET ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssii", $searchParam, $searchParam, $limit, $offset);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {

                        $teacherName = htmlspecialchars(
                            $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname']
                        );

                        echo "
                    <tr onclick=\"goToTeacher({$row['id']})\" style='cursor:pointer;'>

                    <td>
                        <div class='name'>

                            <div class='avatar'>
                                " . strtoupper(substr($row['fname'], 0, 1) . substr($row['lname'], 0, 1)) . "
                            </div>

                            <div>
                                <strong>$teacherName</strong><br>
                                <small style='color:#64748b;'>Teacher</small>
                            </div>

                        </div>
                    </td>

                    <td>
                        <span class='dept-badge'>
                            " . htmlspecialchars($row['department']) . "
                        </span>
                    </td>

                    <td onclick='event.stopPropagation();'>

                        <div class='action-buttons'>

                            <button class='edit-btn'
                                onclick=\"openEditModal(
                                    {$row['id']},
                                    '{$row['fname']}',
                                    '{$row['mname']}',
                                    '{$row['lname']}',
                                    '{$row['department']}'
                                )\">
                                <i class='fa-solid fa-pen'></i>
                            </button>

                            <button class='delete-btn'
                                onclick=\"openDeleteModal({$row['id']})\">
                                <i class='fa-solid fa-trash'></i>
                            </button>

                        </div>

                    </td>

                    </tr>
                    ";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center;'>No teachers found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="pagination">

        <?php
        $countSql = "SELECT COUNT(*) as total 
                    FROM teachers 
                    WHERE (fname LIKE ? OR lname LIKE ?) 
                    AND archive = 0";

        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param("ss", $searchParam, $searchParam);
        $countStmt->execute();

        $countResult = $countStmt->get_result();
        $totalRows = $countResult->fetch_assoc()['total'];

        $totalPages = ceil($totalRows / $limit);

        if ($totalPages > 1) {

            if ($page > 1) {
                echo '<a class="page-btn" href="?search=' . urlencode($search) . '&page=' . ($page - 1) . '">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>';
            }

            for ($i = 1; $i <= $totalPages; $i++) {

                $active = ($i == $page) ? "active" : "";

                echo '<a class="page-btn ' . $active . '" 
                        href="?search=' . urlencode($search) . '&page=' . $i . '">
                        ' . $i . '
                    </a>';
            }
            if ($page < $totalPages) {
                echo '<a class="page-btn" href="?search=' . urlencode($search) . '&page=' . ($page + 1) . '">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>';
            }
        }
        ?>

    </div>

    <div id="editModal" class="modal">
        <div class="modal-box">
            <h3>Edit Teacher</h3>

            <input type="hidden" id="edit_id">

            <input type="text" id="edit_fname" placeholder="First Name">
            <input type="text" id="edit_mname" placeholder="Middle Name">
            <input type="text" id="edit_lname" placeholder="Last Name">

            <select id="edit_department">
                <option>Computer Science</option>
                <option>Information Technology</option>
                <option>Information Systems</option>
            </select>

            <div class="actions">
                <button onclick="updateTeacher()" style="background:#003366;color:white;">Save</button>
                <button onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-box">
            <div class="icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3>Delete Teacher</h3>
            <p>Are you sure you want to delete this record?</p>
            <input type="hidden" id="delete_id">
            <div class="actions">
                <button class="cancel" onclick="closeModal()" style=" background:#e2e8f0;color:#003366;">Cancel</button>
                <button class="delete" onclick="deleteTeacher()" style=" background:#003366;color: white;">Yes, Delete!</button>
            </div>

        </div>
    </div>

    <script>
        function clearSearch() {
            const input = document.getElementById("searchInput");

            input.value = "";

            window.location.href = "list_teacher.php";
        }

        function goToTeacher(id) {
            window.location.href = "teacher_classes.php?tID=" + id;
        }

        function goToTeacher(id) {
            window.location.href = "teacher_profile.php?id=" + id;
        }

        function openEditModal(id, fname, mname, lname, dept) {
            document.getElementById("editModal").style.display = "block";

            document.getElementById("edit_id").value = id;
            document.getElementById("edit_fname").value = fname;
            document.getElementById("edit_mname").value = mname;
            document.getElementById("edit_lname").value = lname;
            document.getElementById("edit_department").value = dept;
        }

        function openDeleteModal(id) {
            document.getElementById("deleteModal").style.display = "block";
            document.getElementById("delete_id").value = id;
        }

        function closeModal() {
            document.getElementById("editModal").style.display = "none";
            document.getElementById("deleteModal").style.display = "none";
        }

        function updateTeacher() {
            fetch("teacher_update.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        id: edit_id.value,
                        fname: edit_fname.value,
                        mname: edit_mname.value,
                        lname: edit_lname.value,
                        department: edit_department.value
                    })
                })
                .then(res => res.text())
                .then(data => {
                    showToast("Teacher updated successfully!", "success");
                    setTimeout(() => location.reload(), 3000);
                });
        }

        function deleteTeacher() {

            fetch("teacher_delete.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "id=" + delete_id.value
                })
                .then(res => res.text())
                .then(data => {
                    data = data.trim();
                    if (data !== "Teacher deleted successfully!") {
                        closeModal();
                        showToast(data, "error");
                        return;
                    }
                    showToast(data, "success");

                    setTimeout(() => {
                        location.reload();
                    }, 2000);

                })
                .catch(() => {
                    showToast("Server error.", "error");
                });
        }

        function showToast(message, type = "success") {
            let toast = document.getElementById("toast");

            toast.textContent = message;
            toast.className = "toast show";

            if (type === "error") {
                toast.classList.add("error");
            }

            setTimeout(() => {
                toast.className = "toast";
            }, 3000);
        }
    </script>
    <div id="toast" class="toast"></div>
</body>

</html>