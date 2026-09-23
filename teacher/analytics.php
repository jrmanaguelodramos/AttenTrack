    <?php
    require_once '../connectDB.php';

    if (!isset($_GET['class'])) {
        die("Class ID not provided.");
    }

    $class_id = (int) $_GET['class'];

    if ($class_id <= 0) {
        die("Invalid class ID.");
    }
    $from = $_GET['from'] ?? null;
    $to   = $_GET['to'] ?? null;

    $sql = "SELECT * FROM classes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $class = $stmt->get_result()->fetch_assoc();

    if (!$class) {
        die("Class not found.");
    }

    $sql = "SELECT COUNT(*) AS total FROM class WHERE classID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $total_students = (int)$stmt->get_result()->fetch_assoc()['total'];

    $sql = "SELECT 
    checkindate,
    SUM(CASE WHEN remarks = 'On_Time' THEN 1 ELSE 0 END) AS present,
    SUM(CASE WHEN remarks = 'Late' THEN 1 ELSE 0 END) AS late,
    SUM(CASE WHEN remarks = 'Absent' THEN 1 ELSE 0 END) AS absent
FROM atten_logs
WHERE cID = ?";

    if ($from && $to) {
        $sql .= " AND checkindate BETWEEN ? AND ?";
    }

    $sql .= " GROUP BY checkindate ORDER BY checkindate ASC";

    $stmt = $conn->prepare($sql);

    if ($from && $to) {
        $stmt->bind_param("iss", $class_id, $from, $to);
    } else {
        $stmt->bind_param("i", $class_id);
    }

    $heatmap_label = "Weekly Attendance Heatmap";

    if ($from && $to) {
        $days = (strtotime($to) - strtotime($from)) / 86400;

        if ($days > 20) {
            $heatmap_label = "Monthly Attendance Heatmap";
        } else {
            $heatmap_label = "Weekly Attendance Heatmap";
        }
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];

    $presentData = [];
    $lateData = [];
    $absentData = [];

    $total_present = 0;
    $total_late = 0;
    $total_absent = 0;

    while ($row = $result->fetch_assoc()) {

        $present = (int)$row['present'];
        $late    = (int)$row['late'];
        $absent  = (int)$row['absent'];

        $labels[] = date("M d", strtotime($row['checkindate']));

        $presentData[] = $present;
        $lateData[] = $late;
        $absentData[] = $absent;

        // ✅ FIX: accumulate totals
        $total_present += $present;
        $total_late += $late;
        $total_absent += $absent;
    }

    $sql = "SELECT 
            YEARWEEK(checkindate, 1) AS week_id,
            MIN(checkindate) AS week_start,
            SUM(CASE WHEN remarks IN ('Present','Late') THEN 1 ELSE 0 END) AS attended
        FROM atten_logs
        WHERE cID = ?";

    if ($from && $to) {
        $sql .= " AND checkindate BETWEEN ? AND ?";
    }

    $sql .= " GROUP BY week_id ORDER BY week_id ASC";

    $stmt = $conn->prepare($sql);

    if ($from && $to) {
        $stmt->bind_param("iss", $class_id, $from, $to);
    } else {
        $stmt->bind_param("i", $class_id);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    $weekly = [];

    while ($row = $res->fetch_assoc()) {

        $percent = ($row['attended'] / max(1, $total_students)) * 100;

        $weekly[] = [
            "week" => date("M d", strtotime($row['week_start'])),
            "percent" => round($percent, 1)
        ];
    }

    if (empty($labels)) {
        $labels = ["No Data"];
        $rates = [0];
    }

    $overall_rate = round(
        ($total_present + $total_late) / max(1, $total_students) * 100,
        1
    );
    ?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>Analytics</title>

        <link rel="stylesheet" href="css/class.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <style>
            .header-card {
                background: #ffffff;
                padding: 15px 20px;
                border-radius: 12px;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
                margin-bottom: 15px;
            }

            .page-header {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .back-btn {
                width: 52px;
                height: 47px;
                border-radius: 14px;
                background: #003366;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                font-size: 30px;
                transition: 0.25s ease;
            }

            .back-btn:hover {
                transform: translateY(-2px);
            }


            .title-group h2 {
                margin: 0;
                font-size: 22px;
            }

            .title-group p {
                margin: 2px 0 0;
                color: #666;
                font-size: 14px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 18px;
                margin-top: 20px;
            }

            .stat-card {
                background: #fff;
                border-radius: 14px;
                padding: 18px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
                position: relative;
                overflow: hidden;
            }

            .stat-card::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                width: 4px;
                height: 100%;
            }

            .stat-icon {
                width: 52px;
                height: 52px;
                margin-right: 20px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 22px;
                flex-shrink: 0;
            }

            .stat-info h2 {
                margin: 0;
                font-size: 30px;
                font-weight: 700;
                line-height: 1;
            }

            .stat-info h3 {
                margin: 6px 0 3px;
                font-size: 15px;
                font-weight: 600;
                color: #111827;
            }

            .present::before {
                background: #22c55e;
            }

            .present .stat-icon {
                background: #dcfce7;
                color: #22c55e;
            }

            .present h2 {
                color: #22c55e;
            }

            .late::before {
                background: #f59e0b;
            }

            .late .stat-icon {
                background: #fef3c7;
                color: #f59e0b;
            }

            .late h2 {
                color: #f59e0b;
            }

            .absent::before {
                background: #ef4444;
            }

            .absent .stat-icon {
                background: #fee2e2;
                color: #ef4444;
            }

            .absent h2 {
                color: #ef4444;
            }

            .rate::before {
                background: #2563eb;
            }

            .rate .stat-icon {
                background: #dbeafe;
                color: #2563eb;
            }

            .rate h2 {
                color: #2563eb;
            }

            @media(max-width:900px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media(max-width:550px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                }
            }

            .filter-box {
                background: #fff;
                padding: 15px;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
                margin: 15px 0;
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                align-items: center;
                justify-content: space-between;
            }

            .filter-box input[type="date"] {
                padding: 6px 10px;
                border: 1px solid #ddd;
                border-radius: 8px;
                outline: none;
            }

            .filter-box input[type="date"]:focus {
                border-color: #3b82f6;
            }

            .filter-box button,
            .filter-box a {
                padding: 7px 12px;
                border-radius: 8px;
                font-size: 14px;
                text-decoration: none;
                transition: 0.2s ease;
            }

            .filter-box button {
                background: #003366;
                color: #fff;
                border: none;
                cursor: pointer;
            }

            .filter-box button:hover {
                background: #2563eb;
            }

            .filter-box a {
                background: #f3f4f6;
                color: #111;
            }

            .filter-box a:hover {
                background: #e5e7eb;
            }

            .quick-filters {
                margin-top: 10px;
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .quick-filters a {
                background: #eef2ff;
                color: #3730a3;
                padding: 6px 10px;
                border-radius: 8px;
                text-decoration: none;
                font-size: 13px;
                transition: 0.2s;
            }

            .quick-filters a:hover {
                background: #c7d2fe;
            }

            .week-heatmap {
                display: flex;
                gap: 14px;
                flex-wrap: wrap;
                margin-top: 15px;
            }

            .week-box {
                background: #ffffff;
                border-radius: 14px;
                padding: 12px;
                width: 120px;
                text-align: center;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
                transition: 0.25s ease;
                border: 1px solid #f1f1f1;
            }

            .week-box:hover {
                transform: translateY(-4px);
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10);
            }

            .week-bar {
                height: 45px;
                width: 100%;
                border-radius: 10px;
                margin-bottom: 8px;
                transition: 0.3s ease;
            }

            .week-box small {
                display: block;
                color: #6b7280;
                font-size: 11px;
                margin-bottom: 4px;
            }

            .week-box span {
                font-weight: 700;
                font-size: 14px;
                color: #111827;
            }

            .week-box span::after {
                content: "%";
                font-weight: 400;
                font-size: 11px;
                color: #6b7280;
                margin-left: 2px;
            }

            .modal-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);

                display: none;
                justify-content: center;
                align-items: center;

                z-index: 9999;
            }

            .modal-overlay h2 {
                margin-bottom: 10px;
            }

            .modal-box {
                width: 360px;
                background: #fff;
                border-radius: 12px;
                padding: 25px;
                text-align: center;
                animation: popIn 0.2s ease;
            }

            .actions {
                display: flex;
                gap: 40px;
            }

            .actions button {
                flex: 1;
                padding: 10px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                margin-top: 30px;
            }

            .actions button:first-child {
                background: #e5e7eb;
            }

            .actions button:last-child {
                background: #ef4444;
                color: white;
            }

            @keyframes popIn {
                from {
                    transform: scale(0.9);
                    opacity: 0;
                }

                to {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            .modal-icon {
                font-size: 50px;
                color: #ef4444;
                margin-bottom: 10px;
            }

            .actions button {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .actions button i {
                font-size: 14px;
            }
        </style>
    </head>

    <body>

        <div class="main_container">
            <div class="sidebar">
                <?php include 'class_header.php'; ?>
            </div>
            <div class="main">
                <div class="analytics-container">

                    <div class="header-card">
                        <div class="page-header">
                            <a class="back-btn" href="class_dashboard.php?class=<?= $class_id ?>">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <div class="title-group">
                                <h2><?= htmlspecialchars($class['subject']) ?> Analytics</h2>
                                <p>Section: <?= htmlspecialchars($class['section']) ?></p>
                            </div>

                        </div>

                    </div>

                    <div class="filter-box">
                        <div>

                            <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">

                                <input type="hidden" name="class" value="<?= $class_id ?>">

                                <label>
                                    From:
                                    <input type="date" name="from" value="<?= $_GET['from'] ?? '' ?>">
                                </label>

                                <label>
                                    To:
                                    <input type="date" name="to" value="<?= $_GET['to'] ?? '' ?>">
                                </label>

                                <button type="submit">Filter</button>

                                <a href="?class=<?= $class_id ?>" style="background:red;color:white;">Reset</a>

                            </form>

                            <div class="quick-filters">
                                <a href="?class=<?= $class_id ?>&from=2026-05-01&to=2026-05-06">This Week</a>
                                <a href="?class=<?= $class_id ?>&from=2026-05-01&to=2026-05-31">This Month</a>
                            </div>

                        </div>
                        <a href="export_excel.php?class=<?= $class_id ?>&from=<?= $_GET['from'] ?? '' ?>&to=<?= $_GET['to'] ?? '' ?>"
                            style="margin-left:80px;background:#003366; color:#fff; padding:10px 12px; border-radius:8px; text-decoration:none;">
                            <i class="fa-solid fa-chart-bar"></i>
                            Export Excel
                        </a>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-card present">
                            <div class="stat-icon">
                                <i class="fa-regular fa-clock"></i>
                            </div>

                            <div class="stat-info">
                                <h2><?= $total_present ?></h2>
                                <h3>On Time</h3>
                            </div>
                        </div>

                        <div class="stat-card late">
                            <div class="stat-icon">
                                <i class="fa-regular fa-clock"></i>
                            </div>

                            <div class="stat-info">
                                <h2><?= $total_late ?></h2>
                                <h3>Late</h3>
                            </div>
                        </div>

                        <div class="stat-card absent">
                            <div class="stat-icon">
                                <i class="fa-regular fa-user"></i>
                            </div>

                            <div class="stat-info">
                                <h2><?= $total_absent ?></h2>
                                <h3>Absent</h3>
                            </div>
                        </div>

                        <div class="stat-card rate">
                            <div class="stat-icon">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>

                            <div class="stat-info">
                                <h2><?= $overall_rate ?>%</h2>
                                <h3>Overall Rate</h3>
                            </div>
                        </div>

                    </div>
                    <h3 style="margin-top:25px;">Attendance Trend</h3>

                    <div style="background:#fff; padding:15px; border-radius:12px;">
                        <canvas id="attendanceChart" height="100"></canvas>
                    </div>
                    <h3 style="margin-top:25px;">
                        <?= $heatmap_label ?>
                    </h3>
                    <div style="margin-top:10px; display:flex; gap:10px; font-size:12px;">
                        <span>🟩 80%+ Good</span>
                        <span>🟨 50–79% Fair</span>
                        <span>🟥 Below 50% Low</span>
                    </div>
                    <div class="week-heatmap">

                        <?php foreach ($weekly as $w): ?>

                            <?php
                            if ($w['percent'] >= 80) {
                                $color = "#16a34a";
                            } elseif ($w['percent'] >= 50) {
                                $color = "#facc15";
                            } else {
                                $color = "#ef4444";
                            }
                            ?>

                            <div class="week-box" title="<?= $w['week'] ?> - <?= $w['percent'] ?>%">
                                <div class="week-bar" style="background: <?= $color ?>"></div>
                                <small><?= $w['week'] ?></small>
                                <span><?= $w['percent'] ?>%</span>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
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

        <script>
            const labels = <?= json_encode($labels) ?>;
            const presentData = <?= json_encode($presentData) ?>;
            const lateData = <?= json_encode($lateData) ?>;
            const absentData = <?= json_encode($absentData) ?>;

            document.addEventListener("DOMContentLoaded", function() {

                const canvas = document.getElementById('attendanceChart');

                if (canvas) {
                    const ctx = canvas.getContext('2d');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Present',
                                    data: presentData,
                                    backgroundColor: '#22c55e'
                                },
                                {
                                    label: 'Late',
                                    data: lateData,
                                    backgroundColor: '#f59e0b'
                                },
                                {
                                    label: 'Absent',
                                    data: absentData,
                                    backgroundColor: '#ef4444'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } else {
                    console.error("attendanceChart canvas not found");
                }

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