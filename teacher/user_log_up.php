    <?php
    session_start();
    date_default_timezone_set('Asia/Manila');
    $d = date("Y-m-d");

    require_once '../connectDB.php';

    if (isset($_POST['class_id'])) {
        $class_id = $_POST['class_id'];
    } else {
        echo "Class ID not provided.";
        exit;
    }

    $stats = ['present_count' => 0, 'late_count' => 0];

    $sql = "
    SELECT 
        SUM(CASE WHEN remarks = 'Present' THEN 1 ELSE 0 END) AS present_count,

        SUM(CASE WHEN remarks = 'Late' THEN 1 ELSE 0 END) AS late_count,

        SUM(CASE WHEN remarks = 'Absent' THEN 1 ELSE 0 END) AS absent_count

    FROM atten_logs
    WHERE cID = ? 
    AND DATE(checkindate) = ?";

    $result = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($result, $sql)) {
        echo '<p class="error">SQL Error</p>';
    } else {
        mysqli_stmt_bind_param($result, "is", $class_id, $d);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);

        if (mysqli_num_rows($resultl) > 0) {
            $stats = mysqli_fetch_assoc($resultl);
        }
    }
    ?>

    <div class="stats-container">
        <div class="stat-card single-card">
            <p><strong>Date:</strong> <?php echo $d; ?></p>
            <p><strong>Present:</strong> <?php echo $stats['present_count']; ?></p>
            <p><strong>Late:</strong> <?php echo $stats['late_count']; ?></p>
            <p><strong>Absent:</strong>
                <?php echo $stats['absent_count']; ?></p>
        </div>
    </div>

    <div class="table-responsive" style="max-height: 500px;">
        <table class="table">
            <thead class="table-primary">
                <tr>
                    <th><i class="fa-solid fa-user-graduate"></i> Student Name</th>
                    <th><i class="fa-solid fa-address-card"></i> Student ID</th>
                    <th></th>
                    <th></th>
                    <th><i class="fa-solid fa-clock"></i> Time In</th>
                    <th><i class="fa-solid fa-clock"></i> Time Out</th>
                    <th><i class="fa-solid fa-comment"></i> Remarks</th>
                </tr>
            </thead>
            <tbody class="table-secondary">
                <?php
                $sql = "SELECT * FROM atten_logs WHERE cID = ? AND DATE(checkindate) = ? ORDER BY updated_at DESC";
                $result = mysqli_stmt_init($conn);

                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo '<p class="error">SQL Error</p>';
                } else {
                    mysqli_stmt_bind_param($result, "is", $class_id, $d);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);

                    if (mysqli_num_rows($resultl) > 0) {
                        while ($row = mysqli_fetch_assoc($resultl)) {
                ?>
                            <tr class="student-row"
                                data-status="<?php echo $row['remarks']; ?>">
                                <td>
                                    <?php
                                        $fullName = $row['lname'] . ", " . $row['fname'];
                                        if (!empty($row['mname'])) {
                                            $fullName .= " " . $row['mname'];
                                        }
                                        echo htmlspecialchars($fullName);
                                    ?>
                                </td>
                                <td><?php echo $row['stud_num']; ?></td>
                                <td></td>
                                <td></td>
                                <td><?php echo $row['timein']; ?></td>
                                <td><?php echo $row['timeout']; ?></td>
                                 <td>
                                    <?php
                                        $remarks = $row['remarks'] ?? '';
                                        $badgeStyle = match(strtolower($remarks)) {
                                            'present'  => 'background:#16a34a; color:white;',
                                            'late'     => 'background:#FFC107; color:#white;',
                                            'absent'   => 'background:#ef4444; color:white;',
                                            default    => 'background:#e8edf5; color:#003366;',
                                        };
                                    ?>
                                    <span style="<?= $badgeStyle ?> font-size:13px; font-weight:600; padding:5px 16px; border-radius:999px; display:inline-block;">
                                        <?= htmlspecialchars($remarks) ?>
                                    </span>
                                </td>
                            </tr>
                <?php
                        }
                    } else {
                        echo '<tr><td colspan="7">No attendance records found for today.</td></tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>