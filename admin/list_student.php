<?php include_once '../connectDB.php';
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>List of Students</title>
    <link rel="stylesheet" href="css/list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<style>
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

    .student-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
    }

    .actions button:last-child{
        background:#e2e8f0;
        color:black;
    }
</style>

<body>

    <div class="header">
        <div class="title-box">
            LIST OF STUDENTS
        </div>

        <a href="dashboard.php" class="back-btn">←</a>
    </div>

    <div class="top-controls">

        <div class="teacher-search">
            <form method="GET" action="list_student.php" class="search-box">

                <input type="text"
                    name="search"
                    id="searchInput"
                    placeholder="Search students..."
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
            <a href= "archiveStudents.php" class="archive-btn">
                <i class="fa-solid fa-box-archive"></i>
                Archive
            </a>

            <a href="studentReg.php" class="add-btn">
                <i class="fa-solid fa-user-plus"></i>
                Add Student
            </a>

        </div>

    </div>

    <div class="teacher-list">

        <table class="table">

            <thead>
                <tr>
                    <th>
                        <i class="fa-solid fa-user-graduate"></i>
                        NAME
                    </th>

                    <th>
                        <i class="fa-solid fa-id-card"></i>
                        STUDENT ID
                    </th>

                    <th>
                        <i class="fa-solid fa-users"></i>
                        SECTION
                    </th>

                    <th>
                        <i class="fa-solid fa-gear"></i>
                        ACTIONS
                    </th>
                </tr>
            </thead>

            <tbody>

    <?php
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1);
    $offset = ($page - 1) * $limit;

    $searchParam = "%$search%";

    $sql = "SELECT * FROM students 
           WHERE archive = 0
            AND (
                fname LIKE ? 
                OR lname LIKE ? 
                OR stud_num LIKE ?
            )
            ORDER BY lname ASC
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii",
        $searchParam,
        $searchParam,
        $searchParam,
        $limit,
        $offset
    );

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

            $studentName = htmlspecialchars(
                $row['lname'] . ', ' .
                $row['fname'] . ' ' .
                $row['mname']
            );

            echo "

        <tr onclick=\"window.location='student_profile.php?id={$row['id']}'\">

        <td>

            <div class='name'>

                <div class='avatar'>
                    " . strtoupper(substr($row['fname'],0,1) . substr($row['lname'],0,1)) . "
                </div>
                <div>
                    <strong>$studentName</strong><br>

                    <small style='color:#64748b;'>
                        Student
                    </small>
                </div>
            </div>
        </td>

        <td>
            <span class='dept-badge'>
                " . htmlspecialchars($row['stud_num']) . "
            </span>
        </td>

        <td>
            <span class='dept-badge'>
                " . htmlspecialchars($row['section']) . "
            </span>
        </td>
       <td onclick=\"event.stopPropagation();\">
            <div class=\"student-actions\">
                <button class=\"edit-btn\"
                    onclick=\"openEditModal(
                        '{$row['id']}',
                        '" . htmlspecialchars($row['fname']) . "',
                        '" . htmlspecialchars($row['mname']) . "',
                        '" . htmlspecialchars($row['lname']) . "'
                    )\">
                    <i class=\"fa-solid fa-pen\"></i>
                </button>
                <button class=\"delete-btn\"
                    onclick=\"openDeleteModal({$row['id']})\">
                    <i class=\"fa-solid fa-trash\"></i>
                </button>
            </div>
        </td>
        </tr>
    ";
        }

    } else {

        echo "
        <tr>
            <td colspan='3' style='text-align:center;padding:40px;'>
                No students found.
            </td>
        </tr>";
    }
    ?>

            </tbody>

        </table>

    </div>

    <div class="pagination">

    <?php

    $countQuery = "SELECT COUNT(*) AS total 
                FROM students 
                WHERE fname LIKE ? 
                OR lname LIKE ? 
                OR stud_num LIKE ?";

    $countStmt = $conn->prepare($countQuery);

    $countStmt->bind_param(
        "sss",
        $searchParam,
        $searchParam,
        $searchParam
    );

    $countStmt->execute();

    $countResult = $countStmt->get_result();

    $totalRows = $countResult->fetch_assoc()['total'];

    $totalPages = ceil($totalRows / $limit);

    if ($totalPages > 1) {

        if ($page > 1) {

            echo '
            <a class="page-btn"
            href="?search=' . urlencode($search) . '&page=' . ($page - 1) . '">
                <i class="fa-solid fa-chevron-left"></i>
            </a>';
        }

        for ($i = 1; $i <= $totalPages; $i++) {

            $active = ($i == $page) ? "active" : "";

            echo '
            <a class="page-btn ' . $active . '"
            href="?search=' . urlencode($search) . '&page=' . $i . '">
                ' . $i . '
            </a>';
        }

        if ($page < $totalPages) {

            echo '
            <a class="page-btn"
            href="?search=' . urlencode($search) . '&page=' . ($page + 1) . '">
                <i class="fa-solid fa-chevron-right"></i>
            </a>';
        }
    }
    ?>

    </div>
    
        <div class="modal" id="editModal">

        <div class="modal-content">

            <h2>Edit Student</h2>

            <input type="hidden" id="edit_id">

            <input type="text" id="edit_fname" placeholder="First Name">
            <input type="text" id="edit_mname" placeholder="Middle Name">
            <input type="text" id="edit_lname" placeholder="Last Name">

            <div class="actions">

                <button onclick="saveStudentEdit()" class="save-btn">
                    Save
                </button>

                <button onclick="closeModal()" class="cancel-btn">
                    Cancel
                </button>

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
                <button class="delete" onclick="deleteStudent()" style=" background:#003366;color: white;">Yes, Delete!</button>
            </div>

        </div>
    </div>

    <div id="toast" class="toast"></div>


        <script>

        function clearSearch() {
            document.getElementById("searchInput").value = "";
            window.location.href = "list_student.php";
        }

        function openEditModal(id, fname, mname, lname){
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_fname').value = fname;
            document.getElementById('edit_mname').value = mname;
            document.getElementById('edit_lname').value = lname;

            document.getElementById('editModal').style.display = 'flex';
        }

        function closeModal(){
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('deleteModal').style.display = 'none';
        }

        function saveStudentEdit(){

            let formData = new FormData();

            formData.append("id", document.getElementById('edit_id').value);
            formData.append("fname", document.getElementById('edit_fname').value);
            formData.append("mname", document.getElementById('edit_mname').value);
            formData.append("lname", document.getElementById('edit_lname').value);

            fetch('student_update.php', {
                method:'POST',
                body:formData
            })
            .then(res => res.json())
            .then(data => {

                showToast(data.message, data.status);

                if(data.status === "success"){
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            });
        }

        function openDeleteModal(id){
            document.getElementById("deleteModal").style.display = "flex";
            document.getElementById("delete_id").value = id;
        }

        function deleteStudent(){

            let id = document.getElementById("delete_id").value;

            fetch("student_delete.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + encodeURIComponent(id)
            })
            .then(res => res.text())
            .then(data => {

                data = data.trim();

                showToast(data, "success");

                setTimeout(() => {
                    location.reload();
                }, 1500);

            })
            .catch(() => {
                showToast("Server error", "error");
            });
        }

        function showToast(message, type = "success") {
            let toast = document.getElementById("toast");

            toast.textContent = message;
            toast.className = "toast show";

            if(type === "error"){
                toast.classList.add("error");
            } else {
                toast.classList.remove("error");
            }

            setTimeout(() => {
                toast.className = "toast";
            }, 3000);
        }

        </script>
    </body>

</html>