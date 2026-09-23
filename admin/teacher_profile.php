<?php
require_once '../connectDB.php';

if (!isset($_GET['id'])) {
    echo "Teacher ID not provided";
    exit;
}

$tID = $_GET['id'];

$sql = "SELECT * FROM teachers WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tID);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();

if (!$teacher) {
    echo "Teacher not found";
    exit;
}

$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher['userID']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f0f4f8;
        }

        .site-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2px;
        }

       .title-box {
            background: #003366;
            color: white;
            font-weight: 900;
            font-size: 32px;
            padding: 25px 70px;
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
        }

        .back-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: white;
            border: 2px solid #003366;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            color: #003366;
            font-size: 18px;
            margin-right: 50px;
            transition: transform 0.3s;
        }

        .back-btn:hover{
            transform:translateX(4px);
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: auto;
            padding: 28px 0;
        }

        .profile-box {
            background: white;
            border-radius: 16px;
            border: 0.5px solid #e2e8f0;
            border-left: 5px solid #3b6fd4;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 28px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 1px solid black;
            background: #dce8fb;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 26px;
            font-weight: 700;
            color: #3b6fd4;
            flex-shrink: 0;
            overflow: hidden;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .teacher-name {
            font-size: 22px;
            font-weight: 700;
            color: #0d1f3c;
            margin-bottom: 10px;
        }

        .meta-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .meta-tag {
            display: flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #d0dff5;
            border-radius: 999px;
            padding: 4px 14px;
            font-size: 13px;
            color: #3b6fd4;
            background: white;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .section-head-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-head-left i {
            font-size: 20px;
            color: #3b6fd4;
        }

        .section-head-left span {
            font-size: 17px;
            font-weight: 700;
            color: #0d1f3c;
        }

        .underline {
            width: 48px;
            height: 3px;
            background: #3b6fd4;
            border-radius: 2px;
            margin-bottom: 20px;
        }


        .add-btn {
            background: #003366;
            color: white;
            padding: 9px 20px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
        }

        .add-btn:hover {
            background: #0b4d90;
        }

        .log-card {
            background: white;
            border-radius: 14px;
            border: 0.5px solid #e2e8f0;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr th {
            background: #f4f7fc;
            color: #3b6fd4;
            padding: 14px 20px;
            text-align: left;
            font-size: 13px;
            font-weight: 700;
            border-bottom: 1px solid #e8edf5;
        }

        thead tr th i {
            margin-right: 6px;
        }

        tbody tr td {
            padding: 16px 20px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover td {
            background: #f8fbff;
            cursor: pointer;
        }

        .subject-cell {
            font-weight: 700;
            color: #0d1f3c;
        }

        .code-badge {
            background: #e8edf5;
            color: #003366;
            font-size: 12px;
            font-weight: 600;
            border-radius: 999px;
            padding: 4px 12px;
            display: inline-block;
        }

        .section-badge {
            background: #e8edf5;
            color: #003366;
            font-size: 12px;
            font-weight: 600;
            border-radius: 999px;
            padding: 4px 12px;
            display: inline-block;
        }

        .schedule-badge {
            background: #dce8fb;
            color: #3b6fd4;
            font-size: 12px;
            font-weight: 600;
            border-radius: 999px;
            padding: 4px 12px;
            display: inline-block;
        }

        .empty-state {
            padding: 32px;
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .title-box { font-size: 20px; padding: 16px 36px; }
            .meta-tags { gap: 6px; }
            table { font-size: 12px; }
        }

        .pagination{
            display:flex;
            justify-content:center;
            align-items:center;
            gap:10px;
            padding:10px;
            background:white;
            border-top:1px solid #e2e8f0;
        }

        .page-btn{
            width:38px;
            height:38px;
            display:flex;
            justify-content:center;
            align-items:center;
            border-radius:10px;
            text-decoration:none;
            background:#f1f5f9;
            color:#003366;
            font-weight:700;
            transition:0.3s;
        }

        .page-btn:hover{
            background:#dce8fb;
        }

        .page-btn.active{
            background:#003366;
            color:white;
        }
    </style>
</head>

<body>
    <div class="site-header">
        <div class="title-box">TEACHER PROFILE</div>
        <a href="list_teacher.php" class="back-btn">&#8592;</a>
    </div>

    <div class="container">

        <?php
            $fname    = $teacher['fname']  ?? '';
            $mname    = $teacher['mname']  ?? '';
            $lname    = $teacher['lname']  ?? '';
            $initials = strtoupper(substr($fname, 0, 1) . substr($lname, 0, 1));
            $fullname = trim("$fname $mname $lname");
        ?>
        <div class="profile-box">
            <div class="avatar">
                <?php if (!empty($teacher['photo'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($teacher['photo']) ?>">
                <?php else: ?>
                    <?= $initials ?>
                <?php endif; ?>
            </div>
            <div>
                <div class="teacher-name"><?= $fullname ?></div>
                <div class="meta-tags">
                    <span class="meta-tag">
                        <i class="fa-solid fa-building-columns"></i>
                        <?= $teacher['department'] ?>
                    </span>
                    <span class="meta-tag">
                        <i class="fa-solid fa-user"></i>
                        <?= $user['username'] ?>
                    </span>
                    <!-- <span class="meta-tag">
                        <i class="fa-solid fa-book"></i>
                        <?= $teacher['subjects'] ?>
                    </span> -->
                </div>
            </div>
        </div>

        <div class="section-head">
            <div class="section-head-left">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Classes Handled</span>
            </div>
            <a href="add_class.php?tID=<?= $tID ?>" class="add-btn">
                <i class="fa-solid fa-plus"></i> Add Class
            </a>
        </div>
        <div class="underline"></div>

        <div class="log-card">
            <table>
                <thead>
                    <tr>
                        <th><i class="fa-solid fa-book-open"></i> Subject</th>
                        <th><i class="fa-solid fa-hashtag"></i> Course Code</th>
                        <th><i class="fa-solid fa-users"></i> Section</th>
                        <th><i class="fa-regular fa-calendar"></i> Schedule</th>
                        <th><i class="fa-regular fa-clock"></i> Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $limit = 5;

                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                    if ($page < 1) {
                        $page = 1;
                    }

                    $offset = ($page - 1) * $limit;

                    $countSql = "SELECT COUNT(*) as total FROM classes WHERE tID=?";
                    $countStmt = $conn->prepare($countSql);
                    $countStmt->bind_param("i", $tID);
                    $countStmt->execute();

                    $totalResult = $countStmt->get_result()->fetch_assoc();

                    $totalRows = $totalResult['total'];

                    $totalPages = ceil($totalRows / $limit);

                    $sql = "SELECT * FROM classes 
                            WHERE tID=? 
                            ORDER BY schedule ASC, time_start ASC
                            LIMIT ?, ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iii", $tID, $offset, $limit);
                    $stmt->execute();

                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0):

                        while ($row = $result->fetch_assoc()):

                            switch ($row['schedule']) {
                                case '1': $schedule = "Mon"; break;
                                case '2': $schedule = "Tue"; break;
                                case '3': $schedule = "Wed"; break;
                                case '4': $schedule = "Thu"; break;
                                case '5': $schedule = "Fri"; break;
                                case '6': $schedule = "Sat"; break;
                                default:  $schedule = $row['schedule'];
                            }
                    ?>
                    <tr onclick="window.location='class.php?class=<?= $row['id'] ?>&tID=<?= $tID ?>'">
                        <td class="subject-cell"><?= $row['subject'] ?></td>
                        <td><span class="code-badge"><?= $row['course_code'] ?></span></td>
                        <td><span class="section-badge"><?= $row['section'] ?></span></td>
                        <td><span class="schedule-badge"><?= $schedule ?></span></td>
                        <td><?= $row['time_start'] ?></td>
                    </tr>

                    <?php
                        endwhile;

                    else:
                    ?>

                    <tr>
                        <td colspan="5" class="empty-state">No classes found.</td>
                    </tr>

                    <?php endif; ?>
                    </tbody>
                
            </table>
            <?php if ($totalPages > 1): ?>

                <div class="pagination">

                    <?php if ($page > 1): ?>
                        <a href="?id=<?= $tID ?>&page=<?= $page - 1 ?>" class="page-btn">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                        <a href="?id=<?= $tID ?>&page=<?= $i ?>"
                        class="page-btn <?= ($i == $page) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>

                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?id=<?= $tID ?>&page=<?= $page + 1 ?>" class="page-btn">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    <?php endif; ?>

                </div>

            <?php endif; ?>
        </div>

    </div>

</body>
</html>