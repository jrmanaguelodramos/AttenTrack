<?php
require_once '../dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/manage_users.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
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

        .alert {
            width: 80%;
            margin: 20px auto;
            padding: 15px;
            border-radius: 10px;
            font-weight: 500;
        }

        .alert-success {
            background: #e6f7ec;
            color: #1e7e34;
            border: 1px solid #b7e4c7;
        }

        .form-container {
            display: flex;
            justify-content: center;
            gap: 50px;
            padding: 20px 40px;
        }

        .form-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            width: 420px;
            border: 1px solid #eaeaea;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        .card-title {
            font-weight: 800;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 18px;
            margin-top: 24px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
        }

        .input-box {
            display: flex;
            align-items: center;
            background: #f5f7fb;
            border-radius: 10px;
            border: 1px solid #eee;
            padding: 10px 12px;
            gap: 20px;
        }

        .input-box i {
            color: #1f3555;
            font-size: 14px;
            width: 18px;
            text-align: center;
        }

        .input-box input,
        .input-box select {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            font-size: 14px;
        }

        .register-btn {
            width: 100%;
            margin-top: 20px;
            background: #003366;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .register-btn:hover {
            background: #0b4d90;
            transform: translateY(-4px);
        }

        .info {
            margin-top: 13px;
            text-align: center;
            font-size: 13px;
            color: gray;
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
    </style>
</head>

<body>

    <div class="header">
        <div class="title-box">STUDENT REGISTRATION</div>
        <a href="list_student.php" class="back-btn">←</a>
    </div>

    <form id="studentForm">
        <div class="alert_user"></div>
        <div class="form-container">

            <div class="form-card">
                <div class="card-title">PERSONAL INFORMATION</div>

                <div class="form-group">
                    <label>FIRST NAME <span style="color:red;">*</span></label>
                    <div class="input-box">
                        <i class="fa fa-user"></i>
                        <input type="text" name="first_name" placeholder="First Name">
                    </div>
                </div>

                <div class="form-group">
                    <label>MIDDLE NAME</label>
                    <div class="input-box">
                        <i class="fa fa-user"></i>
                        <input type="text" name="middle_name" placeholder="Middle Name">
                    </div>
                </div>

                <div class="form-group">
                    <label>LAST NAME <span style="color:red;">*</span></label>
                    <div class="input-box">
                        <i class="fa fa-user"></i>
                        <input type="text" name="last_name" placeholder="Last Name">
                    </div>
                </div>

                <div class="form-group">
                    <label>GENDER<span style="color:red;">*</span></label>
                    <div class="input-box">
                        <i class="fa fa-venus-mars"></i>
                        <select name="gender">
                            <option value="">Select Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="card-title">STUDENT INFORMATION</div>

                <div class="form-group">
                    <label>STUDENT NUMBER <span style="color:red;">*</span></label>
                    <div class="input-box">
                        <i class="fa fa-id-card"></i>
                        <input type="text" name="student_number" placeholder="Ex. 24-1573">
                    </div>
                </div>

                <div class="form-group">
                    <label>PROGRAM <span style="color:red;">*</span></label>
                    <div class="input-box">
                        <i class="fa fa-graduation-cap"></i>
                        <select id="program" name="course" required>
                            <option value="" disabled selected>Select Program</option>
                            <option value="Computer Science">Bachelor of Science in Computer Science</option>
                            <option value="Information Technology">Bachelor of Science in Information Technology</option>
                            <option value="Information Systems">Bachelor of Science in Information Systems</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>CARD UID <span style="color:red;">*</span></label>
                    <div class="input-box">
                        <i class="fa fa-credit-card"></i>
                        <input type="text" name="rfidinput" id="rfidinput" onclick="loadSelectedCard()" placeholder="Tap Card Here..." readonly>
                    </div>
                </div>

                <input type="hidden" name="sid" id="sid">

                <button type="button" class="register-btn" id="user_add">
                    <i class="fa-solid fa-user-plus"></i>ㅤ
                    REGISTER
                </button>
                <div class="info">
                    <label><span style="color:#1c2e6c;"><i class="fa-solid fa-info">ㅤ</i></span>All fields marked with <span style="color:red;">*</span> are required. </label>
                </div>

            </div>

        </div>
    </form>

    <script>
        function loadSelectedCard() {
            fetch("stud_reg_getUID.php")
                .then(response => response.json())
                .then(data => {
                    if (data.card_uid) {
                        document.getElementById("rfidinput").value = data.card_uid;
                        document.getElementById("sid").value = data.id || "";
                        showToast("Card loaded successfully!", "success");
                    } else {
                        showToast("No selected card found", "error");
                        document.getElementById("rfidinput").value = "";
                        document.getElementById("sid").value = "";
                    }
                })
                .catch(error => {
                    showToast("Failed to load card data", "error");
                    console.error(error);
                });
        }

        function allowOnlyLetters(selector) {
            document.querySelectorAll(selector).forEach(input => {
                input.addEventListener("input", function() {
                    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
                });
            });
        }

        $(document).ready(function() {

            allowOnlyLetters('input[name="first_name"]');
            allowOnlyLetters('input[name="middle_name"]');
            allowOnlyLetters('input[name="last_name"]');
            $('input[name="student_number"]').on('input', function() {
                this.value = this.value.replace(/[^0-9-]/g, '');
            });
            $('#user_add').on('click', function() {

                let fields = [
                    'input[name="first_name"]',
                    'input[name="last_name"]',
                    'select[name="gender"]',
                    'input[name="student_number"]',
                    'select[name="course"]',
                    '#rfidinput'
                ];

                let missing = false;

                fields.forEach(selector => {
                    let el = document.querySelector(selector);

                    if (!el || !el.value.trim()) {
                        el.parentElement.style.border = "2px solid red";
                        missing = true;
                    } else {
                        el.parentElement.style.border = "1px solid #ddd";
                    }
                });

                if (missing) {
                    showToast("Please fill up all required fields!", "error");
                    return;
                }

                let nameFields = [
                    'input[name="first_name"]',
                    'input[name="middle_name"]',
                    'input[name="last_name"]'
                ];

                for (let selector of nameFields) {
                    let val = document.querySelector(selector).value;

                    if (/\d/.test(val)) {
                        document.querySelector(selector).parentElement.style.border = "2px solid red";
                        showToast("Names cannot contain numbers!", "error");
                        return;
                    }
                }

                var formData = {
                    Add: 1,
                    first_name: $('input[name="first_name"]').val(),
                    middle_name: $('input[name="middle_name"]').val(),
                    last_name: $('input[name="last_name"]').val(),
                    gender: $('select[name="gender"]').val(),
                    student_number: $('input[name="student_number"]').val(),
                    course: $('select[name="course"]').val(),
                    rfidinput: $('#rfidinput').val(),
                    sid: $('#sid').val()
                };

                $.ajax({
                    url: 'studentReg_conf.php',
                    type: 'POST',
                    data: formData,

                    success: function(response) {
                        response = response.trim();

                        if (response === "1") {
                            showToast("Student successfully added!", "success");
                            $('#studentForm')[0].reset();
                            $('#sid').val('');
                        } else {
                            showToast(response, "error");
                        }
                    },

                    error: function(xhr, status, error) {
                        showToast("AJAX Error: " + error, "error");
                    }
                });

            });

        });

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