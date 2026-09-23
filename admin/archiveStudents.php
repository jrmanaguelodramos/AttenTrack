<?php
require_once '../connectDB.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;

$totalQuery = "SELECT COUNT(*) as total 
FROM students 
WHERE archive = 1";

$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();

$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

$sql = "SELECT * FROM students
WHERE archive = 1
ORDER BY id DESC
LIMIT $start, $limit";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Archived Students</title>

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial,sans-serif;
        }

        body{
            background:#f4f6f9;
        }

        .header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding-bottom:30px;
        }

        .title-box{
            background:#003366;
            color:#fff;
            font-weight:900;
            font-size:32px;
            padding:25px 70px;
            border-top-right-radius:25px;
            border-bottom-right-radius:25px;
        }

        .back-btn{
            margin-right:50px;
            width:45px;
            height:45px;
            border:2px solid #003366;
            border-radius:50%;
            color:#003366;
            display:flex;
            justify-content:center;
            align-items:center;
            text-decoration:none;
            font-size:20px;
            transition:.3s;
        }

        .back-btn:hover{
            transform:translateX(4px);
        }

        .table-wrapper{
            background:#fff;
            border-radius:14px;
            overflow:hidden;
            border:1px solid #dce1e7;
            margin:0 40px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        thead{
            background:#003366;
        }

        th{
            color:#fff;
            padding:16px;
            font-size:13px;
            text-transform:uppercase;
        }

        td{
            padding:15px;
            text-align:center;
            border-bottom:1px solid #eef1f4;
            font-size:14px;
        }

        tbody tr:hover{
            background:#f9fbfc;
        }

        .restore-btn{
            padding:8px 14px;
            border:none;
            border-radius:8px;
            background:#28a745;
            color:#fff;
            cursor:pointer;
            font-weight:600;
            transition:.3s;
        }

        .restore-btn:hover{
            background:#218838;
        }

        .pagination{
            display:flex;
            justify-content:center;
            gap:10px;
            margin:25px 0;
        }

        .pagination a{
            padding:10px 15px;
            text-decoration:none;
            border-radius:8px;
            background:#fff;
            border:1px solid #dce1e7;
            color:#003366;
            font-weight:600;
        }

        .pagination a.active{
            background:#003366;
            color:#fff;
        }

        .toast{
            position:fixed;
            bottom:20px;
            right:20px;
            min-width:250px;
            padding:14px 18px;
            border-radius:10px;
            color:#fff;
            font-weight:600;
            opacity:0;
            transform:translateY(20px);
            transition:.3s;
            z-index:9999;
        }

        .toast.show{
            opacity:1;
            transform:translateY(0);
        }

        .toast.success{
            background:#28a745;
        }

        .toast.error{
            background:#dc3545;
        }

        .empty{
            padding:35px;
            text-align:center;
            color:#999;
        }

    </style>
</head>

<body>

<div class="container">

    <div class="header">

        <div class="title-box">
            ARCHIVE STUDENTS
        </div>

        <a href="list_student.php" class="back-btn">
            ←
        </a>

    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                   <th><i class="fa-solid fa-hashtag"></i> ID</th>
                    <th><i class="fa-solid fa-id-card"></i> Student Number</th>
                    <th><i class="fa-solid fa-user"></i> Name</th>
                    <th><i class="fa-solid fa-users"></i> Section</th>
                    <th><i class="fa-solid fa-gear"></i> Action</th>
                </tr>
            </thead>
            <tbody>

            <?php if($result && $result->num_rows > 0): ?>

                <?php while($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td><?= $row['id']; ?></td>

                        <td>
                            <?= htmlspecialchars($row['stud_num']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['section']); ?>
                        </td>

                        <td>

                            <form class="restoreForm">

                                <input type="hidden"
                                name="id"
                                value="<?= $row['id']; ?>">

                                <button type="submit"
                                class="restore-btn">

                                    <i class="fa-solid fa-rotate-left"></i>
                                    Restore

                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>

                    <td colspan="5" class="empty">
                        No archived students found.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

    <div class="pagination">

        <?php if($page > 1): ?>

            <a href="?page=<?= $page - 1; ?>">
                «
            </a>

        <?php endif; ?>

        <?php for($i=1; $i <= $totalPages; $i++): ?>

            <a href="?page=<?= $i; ?>"
            class="<?= ($i == $page) ? 'active' : ''; ?>">

                <?= $i; ?>

            </a>

        <?php endfor; ?>

        <?php if($page < $totalPages): ?>

            <a href="?page=<?= $page + 1; ?>">
                »
            </a>

        <?php endif; ?>

    </div>

</div>

<div id="toast" class="toast"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

function showToast(message, type="success") {

    let toast = document.getElementById("toast");

    toast.textContent = message;

    toast.classList.remove("success", "error", "show");

    toast.classList.add(type);
    toast.classList.add("show");

    setTimeout(() => {

        toast.classList.remove("show");

    }, 3000);
}

$('.restoreForm').on('submit', function(e){

    e.preventDefault();

    let form = $(this);

    $.ajax({

        url:'restoreStudent.php',

        type:'POST',

        data:form.serialize(),

        dataType:'json',

        success:function(response){

            showToast(response.message, response.status);

            if(response.status === "success"){

                setTimeout(() => {

                    location.reload();

                }, 1200);
            }
        },

      error: function(xhr, status, error){
        console.log("XHR:", xhr.responseText);
        console.log("Status:", status);
        console.log("Error:", error);

        showToast(xhr.responseText || "Server error", "error");
    }
        });

});
</script>

</body>
</html>