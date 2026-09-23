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
                    s.lname, s.fname";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $grouped = [];
    while ($row = $result->fetch_assoc()) {
        $status = ucfirst(strtolower($row['status'] ?? 'Active'));
        if (!isset($grouped[$status])) $grouped[$status] = [];
        $grouped[$status][] = $row;
    }

?>

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
            margin-bottom: 28px;
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
            width: 50px;
            height: 50px;
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

    .group-label {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 24px 0 10px;
        margin-left:110px;
    }

    .group-label span {
        font-size: 13px;
        font-weight: 700;
        color: #0d1f3c;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .group-count {
        font-size: 12px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 999px;
    }

    .count-active   { 
        background: #dcfce7; 
        color: #16a34a; 
    }

    .count-critical { 
        background: #fef9c3; 
        color: #b45309; 
    }

    .count-dropped  { 
        background: #fee2e2; 
        color: #dc2626; 
    }

    .count-default  { 
        background: #e8edf5; 
        color: #003366; 
    }

    .students-card {
        background: white;
        border-radius: 14px;
        border: 0.5px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 8px;
        margin-left:90px;
        margin-right:90px;
    }

    .students-card table {
        width: 100%;
        border-collapse: collapse;
    }

    .students-card thead th {
        background: #f4f7fc;
        color: #3b6fd4;
        padding: 13px 20px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        border-bottom: 1px solid #e8edf5;
    }

    .students-card thead th i {
        margin-right: 6px;
    }

    .students-card tbody td {
        padding: 14px 20px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }

    .students-card tbody tr:last-child td {
        border-bottom: none;
    }

    .students-card tbody tr:hover td {
        background: #f8fbff;
        cursor: pointer;
    }

    .name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #dce8fb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: #3b6fd4;
        flex-shrink: 0;
    }

    .student-fullname {
        font-weight: 700;
        color: #0d1f3c;
        font-size: 13px;
    }

    .student-role {
        font-size: 11px;
        color: #94a3b8;
    }

    .id-badge {
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

    .empty-state {
        padding: 28px;
        text-align: center;
        color: #94a3b8;
        font-size: 13px;
    }
    
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <div class="site-header">
            <div class="title-box">ENROLLED STUDENTS</div>
            <a href="list_teacher.php" class="back-btn">&#8592;</a>
    </div>

    <?php if (empty($grouped)): ?>
        <div class="students-card">
            <div class="empty-state">No enrolled students found.</div>
        </div>

    <?php else: ?>
        <?php foreach ($grouped as $status => $students):

        $statusLower = strtolower($status);

        $countClass = match($statusLower) {
            'active'   => 'count-active',
            'critical' => 'count-critical',
            'dropped'  => 'count-dropped',
            default    => 'count-default',
        };

    ?>

    <div class="group-label">
        <span><?= strtoupper($status) ?></span>
        <span class="group-count <?= $countClass ?>"><?= count($students) ?></span>
    </div>

    <div class="students-card">
        <table>
            <thead>
                <tr>
                    <th><i class="fa-solid fa-user"></i> Name</th>
                    <th><i class="fa-regular fa-id-badge"></i> Student ID</th>
                    <th><i class="fa-solid fa-users"></i> Section</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $row):
                    $fname    = htmlspecialchars($row['lname'] ?? '');
                    $mname    = htmlspecialchars($row['fname'] ?? '');
                    $lname    = htmlspecialchars($row['mname'] ?? '');
                    $fullname = trim("$fname, $mname $lname");
                    $initials = strtoupper(substr($fname, 0, 1) . substr($lname, 0, 1));
                ?>
                <tr onclick="window.location='student_profile.php?id=<?= $row['id'] ?>'">
                    <td>
                        <div class="name-cell">
                            <div class="student-avatar"><?= $initials ?></div>
                            <div>
                                <div class="student-fullname"><?= $fullname ?></div>
                                <div class="student-role">Student</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="id-badge"><?= htmlspecialchars($row['stud_num']) ?></span></td>
                    <td><span class="section-badge"><?= htmlspecialchars($row['section']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php endforeach; ?>
<?php endif; ?>