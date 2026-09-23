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
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 30px;
        }

        .title-box {
            background: #e6e6e6;
            padding: 18px 50px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 28px;
            border: 3px solid #2b2b2b;
            color: #1f3555;
            box-shadow: 0 4px 0 #1f3555;
            letter-spacing: 1px;
        }

        .back-btn {
            margin-right: 50px;
            width: 45px;
            height: 45px;
            border: 2px solid #e6e6e6;
            border-radius: 50%;
            color: #e6e6e6;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            cursor: pointer;
        }

        .form-container {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 20px;
        }

        .form-card {
            background: #e6e6e6;
            padding: 40px 50px;
            border-radius: 30px;
            width: 420px;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .form-group label {
            font-weight: 800;
            font-size: 14px;
            color: #2c3e57;
            display: block;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 15px;
            border-radius: 20px;
            border: 1px solid #333;
            background: #dcdcdc;
        }

        .register-btn {
            margin-top: 20px;
            align-self: center;
            background: #2c4566;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="header">
        <div class="title-box">STUDENT REGISTRATION</div>
        <a href="devices.php"><div class="back-btn">←</div></a>
    </div>

    <form id="studentForm">
        <div class="alert_user"></div>
        <div class="form-container">

            <div class="form-card">
                <div class="form-group">
                    <label for="first_name">FIRST NAME</label>
                    <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <label for="middle_name">MIDDLE NAME</label>
                    <input type="text" name="middle_name" id="middle_name" placeholder="Middle Name">
                </div>
                <div class="form-group">
                    <label for="last_name">LAST NAME</label>
                    <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <label for="gender">GENDER</label>
                    <select id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-card">
                <div class="form-group">
                    <label for="student_number">STUDENT NUMBER</label>
                    <input type="text" name="student_number" id="student_number" placeholder="Student Number" required>
                </div>
                <div class="form-group">
                    <label for="course">COURSE</label>
                    <input type="text" name="course" id="course" placeholder="Course" required>
                </div>
                <div class="form-group">
                    <label for="rfidinput">CARD UID</label>
                    <input type="text" name="rfidinput" id="rfidinput" onclick="loadSelectedCard()" placeholder="Tap Card Here..." readonly>
                </div>

                <input type="hidden" name="sid" id="sid">
                <button type="button" class="register-btn" id="user_add">REGISTER</button>
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
                    } else {
                        alert("No selected card found");
                        document.getElementById("rfidinput").value = "";
                        document.getElementById("sid").value = "";
                    }
                })
                .catch(error => console.error(error));
        }

        // =========================
        // AJAX for Register Button
        // =========================
        $(document).ready(function() {
            $('#user_add').on('click', function() {
                var formData = {
                    Add: 1,
                    first_name: $('#first_name').val(),
                    middle_name: $('#middle_name').val(),
                    last_name: $('#last_name').val(),
                    gender: $('#gender').val(),
                    student_number: $('#student_number').val(),
                    course: $('#course').val(),
                    rfidinput: $('#rfidinput').val(),
                    sid: $('#sid').val()
                };

                $.ajax({
                    url: 'studentReg_conf.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.trim() === "1") {
                            $('.alert_user').fadeIn(500).html('<p class="alert alert-success">Student successfully added!</p>');
                            $('#studentForm')[0].reset();
                            $('#sid').val('');
                        } else {
                            $('.alert_user').fadeIn(500).html('<p class="alert alert-danger">' + response + '</p>');
                        }
                        setTimeout(function() {
                            $('.alert_user').fadeOut(500);
                        }, 5000);
                    },
                    error: function(xhr, status, error) {
                        $('.alert_user').fadeIn(500).html('<p class="alert alert-danger">AJAX Error: ' + error + '</p>');
                    }
                });
            });
        });
    </script>
</body>

</html>