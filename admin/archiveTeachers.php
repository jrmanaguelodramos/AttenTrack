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
    FROM teachers 
    WHERE archive = 1";

    $totalResult = $conn->query($totalQuery);
    $totalRow = $totalResult->fetch_assoc();

    $totalRecords = $totalRow['total'];
    $totalPages = ceil($totalRecords / $limit);

    $sql = "SELECT * FROM teachers t
    JOIN users u ON t.userID = u.id
    WHERE t.archive = 1
    ORDER BY t.id DESC
    LIMIT $start, $limit";

    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Teachers</title>

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, sans-serif;
        }

        body{
            background:#f4f6f9;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 30px;
        }

        .title-box {
            background: #003366;
            color: white;
            font-weight: 900;
            font-size: 32px;
            padding: 25px 70px;
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .back-btn {
            margin-right: 50px;
            width: 45px;
            height: 45px;
            border: 2px solid #003366;
            border-radius: 50%;
            color: #003366;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.3s;
        }

        .back-btn:hover {
            transform: translateX(4px);
        }

        .table-wrapper{
            background:#fff;
            border-radius:14px;
            overflow:hidden;
            border:1px solid #dce1e7;
            margin-left:40px;
            margin-right:40px;
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
            letter-spacing:.5px;
        }

        td{
            padding:15px;
            text-align:center;
            border-bottom:1px solid #eef1f4;
            font-size:14px;
            color:#333;
        }

        tbody tr:hover{
            background:#f9fbfc;
        }

        .empty{
            padding:35px;
            color:#999;
            text-align:center;
        }

        .restore-btn{
            padding:8px 14px;
            border:none;
            border-radius:8px;
            background:#28a745;
            color:#fff;
            cursor:pointer;
            font-size:13px;
            font-weight:600;
            transition:.3s;
        }

        .restore-btn:hover{
            background:#218838;
        }

        .pagination{
            display:flex;
            justify-content:center;
            align-items:center;
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
            transition:.25s;
        }

        .pagination a:hover{
            background:#f1f5f9;
            transform: translateY(-2px);
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
            box-shadow:0 10px 25px rgba(0,0,0,0.15);
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
    </style>
</head>
<body>

<div class="container">

      <div class="header">
        <div class="title-box">ARCHIVE TEACHERS</div>
        <a href="list_teacher.php" class="back-btn">←</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                   <th><i class="fa-solid fa-hashtag"></i> ID</th>
                    <th><i class="fa-solid fa-id-card"></i> Teacher ID</th>
                    <th><i class="fa-solid fa-user"></i> Name</th>
                    <th><i class="fa-solid fa-user-tag"></i> Username</th>
                    <th><i class="fa-solid fa-building-columns"></i> Department</th>
                    <th><i class="fa-solid fa-gear"></i> Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result && $result->num_rows > 0): ?>

                <?php while($row = $result->fetch_assoc()): ?>

                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['userID']); ?></td>
                        <td><?= htmlspecialchars($row['fname']. ' ' . $row['lname']); ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td><?= htmlspecialchars($row['department']); ?></td>

                        <td>
                            <form class="restoreForm">
                              <form class="restoreForm">
                                <input type="hidden"
                                name="id"
                                value="<?= $row['id']; ?>">

                                <button type="submit" class="restore-btn">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    Restore
                                </button>
                            </form>
                        </td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7" class="empty">
                        No archived teachers found.
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

        <?php for($i = 1; $i <= $totalPages; $i++): ?>

            <a href="?page=<?= $i; ?>"
            class="<?= ($i == $page) ? 'active' : ''; ?>">
                <?= $i; ?>
            </a>

        <?php endfor; ?>

        <?php if($page < $totalPages): ?>
            <a href="?page=<?= $page + 1; ?>">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        <?php endif; ?>

        </div>

</div>

<div id="toast" class="toast"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>

    function showToast(message, type="success"){

        let toast = document.getElementById("toast");

        toast.textContent = message;
        toast.className = "toast show " + type;

        setTimeout(() => {
            toast.className = "toast";
        }, 3000);
    }

    $('.restoreForm').on('submit', function(e){

        e.preventDefault();

        let form = $(this);

        $.ajax({

            url:'restoreTeacher.php',
            type:'POST',
            data:form.serialize(),

            success:function(response){

                if(response.status === "success"){

                    showToast(response.message, "success");

                    setTimeout(() => {
                        location.reload();
                    }, 1200);

                }else{

                    showToast(response.message, "error");
                }
            },

            error:function(){

                showToast("Server error", "error");
            }
        });

    });
    </script>

</body>
</html>