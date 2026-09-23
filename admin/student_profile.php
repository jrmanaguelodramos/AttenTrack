<?php
require_once '../connectDB.php';

if (!isset($_GET['id'])) {
    echo "Student ID not provided";
    exit;
}

$sID = $_GET['id'];

$sql = "SELECT * FROM students WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sID);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    echo "Student not found";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

        .site-header a{
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border:2px solid #003366;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            color: #003366;
            font-size: 18px;
            margin-right: 50px;
            transition:transform 0.3s;
        }

        .site-header a:hover{
            transform: translateX(4px);
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
            border: 1px solid #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            color: #003366;
            font-size: 18px;
            margin-right: 32px;
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
            gap: 20px;
            margin-bottom: 28px;
        }

        .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #dce8fb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            color: #3b6fd4;
            flex-shrink: 0;
        }

        .student-name {
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
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .section-head i {
            font-size: 20px;
            color: #3b6fd4;
        }

        .section-head span {
            font-size: 17px;
            font-weight: 700;
            color: #0d1f3c;
        }

        .underline {
            width: 17px;
            height: 3px;
            background: #3b6fd4;
            border-radius: 2px;
            margin-bottom: 20px;
        }

        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
            margin-bottom: 32px;
        }

        .class-card {
            background: white;
            border-radius: 14px;
            border: 0.5px solid #e2e8f0;
            border-left: 4px solid #3b6fd4;
            padding: 16px 18px;
            cursor: pointer;
            transition: 0.2s;
        }

        .class-card:hover {
            background: #f0f7ff;
            border-left-color: #003366;
        }

        .class-card.active {
            background: #dce8fb;
            border-left-color: #003366;
        }

        .class-subject {
            font-size: 14px;
            font-weight: 700;
            color: #0d1f3c;
            margin-bottom: 6px;
        }

        .class-meta {
            font-size: 12px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .log-card {
            background: white;
            border-radius: 14px;
            border: 0.5px solid #e2e8f0;
            overflow: hidden;
        }

        .log-header {
            display: grid;
            grid-template-columns: 1fr 1fr 180px;
            padding: 14px 20px;
            background: #f4f7fc;
        }

        .log-header-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #3b6fd4;
        }

        .remarks-header {
            font-size: 13px;
            font-weight: 700;
            color: #3b6fd4;
        }

        .log-row {
            display: grid;
            grid-template-columns: 1fr 1fr 180px;
            padding: 16px 20px;
            align-items: center;
            border-top: 1px solid #f1f5f9;
        }

        .log-row:hover {
            background: #f8fbff;
        }

        .date-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .log-date-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #dce8fb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .log-date-icon i {
            font-size: 16px;
            color: #3b6fd4;
        }

        .date-main {
            font-size: 14px;
            font-weight: 700;
            color: #0d1f3c;
        }

        .date-sub {
            font-size: 12px;
            color: #94a3b8;
        }

        .time-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .time-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #dce8fb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .time-icon i {
            font-size: 15px;
            color: #3b6fd4;
        }

        .time-text {
            font-size: 13px;
            color: #334155;
        }

        .time-text b {
            color: #0d1f3c;
        }

        .remarks-cell {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .badge {
            font-size: 12px;
            font-weight: 700;
            padding: 5px 16px;
            border-radius: 999px;
            color: white;
            white-space: nowrap;
        }

        .present { background: #16a34a; }
        .late    { background: #f59e0b; }
        .absent  { background: #ef4444; }

        .remarks-name {
            font-size: 12px;
            color: #64748b;
            padding-left: 2px;
        }

        .empty-state {
            padding: 32px;
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .title-box { font-size: 20px; padding: 16px 36px; }
            .log-header,
            .log-row   { grid-template-columns: 1fr; gap: 10px; }
            .class-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

    <div class="site-header">
        <div class="title-box">STUDENT PROFILE</div>
        <a href="javascript:history.back()" class="back-btn">&#8592;</a>
    </div>

    <div class="container">

        <?php
            $fname    = $student['fname']  ?? '';
            $mname    = $student['mname']  ?? '';
            $lname    = $student['lname']  ?? '';
            $initials = strtoupper(substr($fname, 0, 1) . substr($lname, 0, 1));
            $fullname = trim("$fname $mname $lname");
        ?>
        <div class="profile-box">
            <div class="avatar"><?= $initials ?></div>
            <div>
                <div class="student-name"><?= $fullname ?></div>
                <div class="meta-tags">
                    <span class="meta-tag">
                        <i class="fa-regular fa-id-badge"></i>
                        ID: <?= $student['stud_num'] ?>
                    </span>
                    <span class="meta-tag">
                        <i class="fa-solid fa-book"></i>
                        Section: <?= $student['section'] ?>
                    </span>
                    <span class="meta-tag">
                        <i class="fa-solid fa-graduation-cap"></i>
                        Program: <?= $student['course'] ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="section-head">
            <i class="fa-solid fa-chalkboard-teacher"></i>
            <span>Enrolled Classes</span>
        </div>
        <div class="underline"></div>
        <div class="class-grid" id="classList"></div>

        <div class="section-head">
            <i class="fa-regular fa-calendar-days"></i>
            <span>Attendance Logs</span>
        </div>
        <div class="underline"></div>

        <div class="log-card">
            <div class="log-header">
                <div class="log-header-cell">
                    <i class="fa-regular fa-calendar"></i> Date
                </div>
                <div class="log-header-cell">
                    <i class="fa-regular fa-clock"></i> Time
                </div>
                <div class="log-header-cell">
                    <i class="fa-solid fa-circle-info"></i> Remarks
                </div>
            </div>
            <div id="attendanceList">
                <div class="empty-state">Select a class to view attendance.</div>
            </div>
        </div>

    </div>

    <script>
    const sID = <?= $sID ?>;

    let attendanceData = [];
    let currentPage = 1;
    const rowsPerPage = 5;

    function getDayName(dateStr) {
        const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const d = new Date(dateStr);
        return isNaN(d) ? '' : days[d.getDay()];
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr);
        if (isNaN(d)) return dateStr;
        return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    }

    function loadClasses() {
        fetch("get_student_classes.php", {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sID })
        })
        .then(res => res.json())
        .then(data => {
            let html = "";

            if (!data || data.length === 0) {
                html = `<div class="empty-state" style="grid-column:1/-1;">No classes found.</div>`;
            } else {
                data.forEach(c => {
                    html += `
                    <div class="class-card" onclick="loadAttendance(${c.id}, this)">
                        <div class="class-subject">
                            <i class="fa-solid fa-book-open" style="color:#3b6fd4;margin-right:6px;"></i>
                            ${c.subject}
                        </div>
                        <div class="class-meta">
                            <i class="fa-regular fa-clock"></i>
                            ${c.schedule} &nbsp;|&nbsp; ${c.time_start}
                        </div>
                    </div>`;
                });
            }

            document.getElementById("classList").innerHTML = html;
        });
    }

    function loadAttendance(classID, element = null) {
        document.querySelectorAll('.class-card').forEach(el => el.classList.remove('active'));
        if (element) element.classList.add('active');

        fetch("get_student_attendance.php", {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sID, classID })
        })
        .then(res => res.json())
        .then(data => {
            attendanceData = data || [];
            currentPage = 1;
            renderAttendancePage();
        });
    }

    function renderAttendancePage() {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = attendanceData.slice(start, end);

        let html = "";

        if (attendanceData.length === 0) {
            html = `<div class="empty-state">No attendance records for this class.</div>`;
        } else {
            pageData.forEach(a => {
                let badgeClass = "present";

                if (a.remarks === "Late") badgeClass = "late";
                if (a.remarks === "Absent") badgeClass = "absent";

                html += `
                <div class="log-row">
                    <div class="date-cell">
                        <div class="log-date-icon">
                            <i class="fa-regular fa-calendar"></i>
                        </div>
                        <div>
                            <div class="date-main">${formatDate(a.checkindate)}</div>
                            <div class="date-sub">${getDayName(a.checkindate)}</div>
                        </div>
                    </div>

                    <div class="time-cell">
                        <div class="time-icon">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="time-text">
                            <b>Time In:</b> ${a.timein ?? 'null'} &nbsp;|&nbsp;
                            <b>Time Out:</b> ${a.timeout ?? 'null'}
                        </div>
                    </div>

                    <div class="remarks-cell">
                        <span class="badge ${badgeClass}">${a.remarks}</span>
                        <span class="remarks-name">${a.student_name ?? ''}</span>
                    </div>
                </div>`;
            });
        }

        html += renderPaginationControls();
        document.getElementById("attendanceList").innerHTML = html;
    }

    function renderPaginationControls() {
        const totalPages = Math.ceil(attendanceData.length / rowsPerPage);

        if (totalPages <= 1) return "";

        let html = `<div style="display:flex;justify-content:center;gap:6px;padding:15px;flex-wrap:wrap;">`;

        for (let i = 1; i <= totalPages; i++) {
            html += `
            <button 
                onclick="goToPage(${i})"
                style="
                    padding:6px 12px;
                    border-radius:6px;
                    border:1px solid #d0dff5;
                    cursor:pointer;
                    font-weight:600;
                    ${i === currentPage ? 'background:#3b6fd4;color:white;' : 'background:white;color:#3b6fd4;'}
                ">
                ${i}
            </button>`;
        }

        html += `</div>`;
        return html;
    }

    function goToPage(page) {
        currentPage = page;
        renderAttendancePage();
    }
    
    loadClasses();
</script>

</body>
</html>