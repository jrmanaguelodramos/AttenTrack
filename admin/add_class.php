<?php
require_once '../connectDB.php';

if (!isset($_GET['tID'])) {
    echo "Teacher ID missing";
    exit;
}

$tID = $_GET['tID'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Class</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .site-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 48px;
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

        .back-btn:hover {
            transform: translateX(4px);
        }

        .form-container {
            display: flex;
            justify-content: center;
            padding: 0 40px 40px;
        }

        .form-card {
            background: white;
            padding: 40px 50px;
            border-radius: 16px;
            width: 100%;
            max-width: 980px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 0.5px solid #e2e8f0;
        }

        .form-card-title {
            font-size: 22px;
            font-weight: 900;
            color: #0d1f3c;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 30px;
        }

        .form-card-divider {
            width: 100%;
            height: 1px;
            background: #e2e8f0;
            margin-bottom: 28px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px 54px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            font-size: 13px;
            color: #0d1f3c;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-group label .required {
            color: #dc2626;
        }

        .input-wrap {
            display: flex;
            align-items: center;
            background: #f1f5f9;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 0 14px;
            gap: 10px;
            transition: border-color 0.2s;
        }

        .input-wrap:focus-within {
            border-color: #3b6fd4;
            background: #f8faff;
        }

        .input-wrap i {
            color: #1f3555;
            font-size: 16px;
            flex-shrink: 0;
        }

        .input-wrap input,
        .input-wrap select {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            padding: 12px 0;
            font-size: 14px;
            color: #0d1f3c;
            min-width: 0;
        }

        .input-wrap input::placeholder {
            color: #94a3b8;
        }

        .input-wrap select {
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            color: #64748b;
        }

        .input-wrap select.selected {
            color: #0d1f3c;
        }

        .select-wrap {
            position: relative;
        }

        .select-wrap .input-wrap {
            padding-right: 10px;
        }

        .select-wrap .chevron {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 14px;
            pointer-events: none;
        }

        .input-wrap input[type="time"]::-webkit-calendar-picker-indicator {
            opacity: 0.4;
            cursor: pointer;
        }

        .form-footer {
            margin-top: 28px;
            display: flex;
            justify-content: flex-end;
        }

        .save-btn {
            background: #003366;
            color: white;
            border: none;
            padding: 14px 48px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            letter-spacing: 0.5px;
            transition: 0.2s;
        }

        .save-btn:hover {
            background: #0b4d90;
        }

        @media (max-width: 520px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .title-box {
                font-size: 22px;
                padding: 18px 40px;
            }

            .form-card {
                padding: 28px 20px;
            }

            .form-container {
                padding: 0 16px 40px;
            }
        }

        .info {
            margin-top: 13px;
            text-align: center;
            font-size: 13px;
            color: gray;
        }

        .session-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px 54px;
        }

        .hidden {
            display: none !important;
        }
    </style>
</head>

<body>

    <div class="site-header">
        <div class="title-box">ADD CLASS</div>
        <a href="list_teacher.php" class="back-btn">&#8592;</a>
    </div>

    <div class="form-container">
        <div class="form-card">

            <div class="form-card-title">Class Information</div>
            <div class="form-card-divider"></div>

            <form method="POST" action="add_class_save.php">

                <input type="hidden" name="tID" value="<?= $tID ?>">
                <div class="form-grid">

                    <div class="form-group">
                        <label>COURSE CODE <span class="required">*</span></label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-hashtag"></i>
                            <input type="text" name="course_code" id="course_code"
                                placeholder="Course Code" list="course_codes"
                                autocomplete="off" required>
                            <datalist id="course_codes">
                                <option value="IT101">
                                <option value="IT102">
                                <option value="IT201">
                                <option value="IT202">
                                <option value="CS301">
                            </datalist>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>SUBJECT NAME <span class="required">*</span></label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-book-open"></i>
                            <input type="text" name="subject" id="subject_name"
                                placeholder="Subject Name" list="subject_names"
                                autocomplete="off" required>
                            <datalist id="subject_names">
                                <option value="Introduction to Computing">
                                <option value="Computer Programming">
                                <option value="Database Management System">
                                <option value="Web Development">
                                <option value="Data Structures">
                            </datalist>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>SECTION <span class="required">*</span></label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-users"></i>
                            <input type="text" name="section"
                                placeholder="Section" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>SCHEDULE <span class="required">*</span></label>
                        <div class="select-wrap">
                            <div class="input-wrap">
                                <i class="fa-regular fa-calendar"></i>
                                <select name="schedule" required
                                    onchange="this.classList.add('selected')">
                                    <option value="" disabled selected>Select Schedule</option>
                                    <option value="1">Monday</option>
                                    <option value="2">Tuesday</option>
                                    <option value="3">Wednesday</option>
                                    <option value="4">Thursday</option>
                                    <option value="5">Friday</option>
                                    <option value="6">Saturday</option>
                                </select>
                            </div>
                            <i class="fa-solid fa-chevron-down chevron"></i>
                        </div>
                    </div>

                    <!-- =========================
                    SINGLE SESSION
                    ========================= -->
                    <div style="grid-column: 1 / -1;">

                        <div id="singleSessionGroup" class="session-grid">

                            <div class="form-group">
                                <label>TIME RANGE *</label>

                                <div style="display:flex; gap:10px;">
                                    <div class="input-wrap" style="flex:1;">
                                        <input type="time" name="time_start" id="time_start" required>
                                    </div>

                                    <div class="input-wrap" style="flex:1;">
                                        <input type="time" name="time_end" id="time_end" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>ROOM *</label>
                                <div class="input-wrap">
                                    <i class="fa-solid fa-door-open"></i>
                                    <?php $sql = "SELECT room FROM devices WHERE building = 'lec'";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $room = [];
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        $room[] = $row['room'];
                                    }
                                    ?>
                                    <select name="room" id="single_room">
                                        <option value="" disabled selected>Select Room</option>
                                        <?php foreach ($room as $r): ?>
                                            <option value="<?= $r ?>"><?= ucfirst($r) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div id="dualSessionGroup" class="session-grid" style="display:none;">
                            <div class="form-group">
                                <label>LECTURE TIME</label>

                                <div style="display:flex; gap:10px;">
                                    <div class="input-wrap" style="flex:1;">
                                        <i class="fa-regular fa-clock"></i>
                                        <input type="time" name="lec_start" id="lec_start">
                                    </div>
                                    <div class="input-wrap" style="flex:1;">
                                        <input type="time" name="lec_end" id="lec_end">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>LECTURE ROOM</label>
                                <div class="input-wrap">
                                    <select name="room" id="lecture_room">
                                        <option value="" disabled selected>Select Lecture Room</option>
                                        <?php foreach ($room as $r): ?>
                                            <option value="<?= $r ?>"><?= ucfirst($r) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>LAB TIME</label>

                                <div style="display:flex; gap:10px;">
                                    <div class="input-wrap" style="flex:1;">
                                        <input type="time" name="lab_start" id="lab_start">
                                    </div>
                                    <div class="input-wrap" style="flex:1;">
                                        <input type="time" name="lab_end" id="lab_end">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>LAB ROOM</label>
                                <?php $sql = "SELECT room FROM devices WHERE building = 'lab'";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $room = [];
                                $result = $stmt->get_result();
                                while ($row = $result->fetch_assoc()) {
                                    $room[] = $row['room'];
                                }
                                ?>
                                <div class="input-wrap">
                                    <select name="lab_room" id="lab_room">
                                        <option value="" disabled selected>Select Lab Room</option>
                                        <?php foreach ($room as $r): ?>
                                            <option value="<?= $r ?>"><?= ucfirst($r) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="form-footer">
                    <button type="submit" class="save-btn">SAVE CLASS</button>

                </div>

                <div class="info">
                    <label><span style="color:#1c2e6c;"><i class="fa-solid fa-info">ㅤ</i></span>All fields marked with <span style="color:red;">*</span> are required. </label>
                </div>

            </form>
        </div>
    </div>
    <script>
        const subjects = {

            "IT101": {
                name: "Introduction to Computing",
                sessions: 1
            },

            "IT102": {
                name: "Computer Programming",
                sessions: 2
            },

            "IT201": {
                name: "Database Management System",
                sessions: 2
            },

            "IT202": {
                name: "Web Development",
                sessions: 2
            },

            "CS301": {
                name: "Data Structures",
                sessions: 1
            }
        };

        const courseCode =
            document.getElementById("course_code");

        const subjectName =
            document.getElementById("subject_name");

        /* =========================
           COURSE CODE
        ========================= */
        courseCode.addEventListener(
            "input",
            function() {

                this.value =
                    this.value.toUpperCase();

                const code =
                    this.value.trim();

                if (subjects[code]) {

                    subjectName.value =
                        subjects[code].name;

                    checkSessionType(
                        subjects[code].sessions
                    );
                }
            }
        );

        /* =========================
           SUBJECT NAME
        ========================= */
        subjectName.addEventListener(
            "input",
            function() {

                const subject =
                    this.value.trim().toLowerCase();

                for (const code in subjects) {

                    if (
                        subjects[code].name
                        .toLowerCase() ===
                        subject
                    ) {

                        courseCode.value = code;

                        checkSessionType(
                            subjects[code].sessions
                        );

                        break;
                    }
                }
            }
        );

        /* =========================
           SESSION TYPE
        ========================= */
        const singleGroup = document.getElementById("singleSessionGroup");
        const dualGroup = document.getElementById("dualSessionGroup");

        const singleInputs = [
            document.getElementById("time_start"),
            document.getElementById("time_end"),
            document.getElementById("single_room")
        ];

        const dualInputs = [
            document.getElementById("lecture_start"),
            document.getElementById("lecture_end"),
            document.getElementById("lecture_room"),
            document.getElementById("lab_start"),
            document.getElementById("lab_end"),
            document.getElementById("lab_room")
        ];

        function setRequired(inputs, value) {
            inputs.forEach(input => {
                if (!input) return;
                input.required = value;
                if (!value) {
                    input.value = "";
                    input.setCustomValidity(""); // clears hidden validation issues
                }
            });
        }

        function checkSessionType(sessions) {
            if (!singleGroup || !dualGroup) return;

            if (sessions === 1) {

                // show single
                singleGroup.style.display = "grid";
                dualGroup.style.display = "none";

                setRequired(singleInputs, true);
                setRequired(dualInputs, false);

            } else if (sessions === 2) {

                // show dual
                singleGroup.style.display = "none";
                dualGroup.style.display = "grid";

                setRequired(singleInputs, false);
                setRequired(dualInputs, true);

            } else {

                // fallback: hide both
                singleGroup.style.display = "none";
                dualGroup.style.display = "none";

                setRequired(singleInputs, false);
                setRequired(dualInputs, false);
            }
        }
    </script>
</body>

</html>