    <?php
    require_once '../dbcon.php';
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Teacher Registration</title>

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

            .header {
                width: 100%;
                margin: 0;
                padding: 0 0 30px 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .header a {
                margin-right: 90px;
                text-decoration: none;
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
                margin-right: -40px;
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

            .subjects {
                height: 130px;
                padding-top: 10px;
            }

            input[type="file"] {
                padding: 12px;
                height: auto;
            }

            #preview {
                display: none;
                margin-top: 20px;
                width: 220px;
                height: 220px;
                object-fit: cover;
                border-radius: 14px;
                border: 2px solid #e1e1e1;
            }

            .show-password {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 12px;
                color: #555;
                font-size: 13px;
            }

            .show-password input {
                width: auto;
                height: auto;
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

            .alert_user {
                width: 100%;
                text-align: center;
                margin-bottom: 20px;
            }

            @media(max-width: 1400px) {

                .form-container {
                    flex-direction: column;
                    align-items: center;
                }

                .form-card {
                    width: 90%;
                    max-width: 500px;
                    min-height: auto;
                }

                .title-box {
                    width: auto;
                    font-size: 28px;
                    padding: 22px 40px;
                }
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
                min-width: 250px;
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

            <div class="title-box">
                TEACHER REGISTRATION
            </div>

            <a href="list_teacher.php">
                <div class="back-btn">
                    ←
                </div>
            </a>

        </div>

        <form id="teacherForm">

            <div class="form-container">

                <div class="form-card">

                    <div class="card-title">
                        PERSONAL INFORMATION
                    </div>

                    <div class="form-group">
                        <label>FIRST NAME <span style="color:red;">*</span></label>
                        <div class="input-box">
                            <i class="fa fa-user"></i>
                            <input type="text" id="fname" placeholder="First Name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>MIDDLE NAME</label>
                        <div class="input-box">
                            <i class="fa fa-user"></i>
                            <input type="text" id="mname" placeholder="Middle Name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>LAST NAME <span style="color:red;">*</span></label>
                        <div class="input-box">
                            <i class="fa fa-user"></i>
                            <input type="text" id="lname" placeholder="Last Name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>DEPARTMENT <span style="color:red;">*</span></label>

                        <div class="input-box">
                            <i class="fa-solid fa-building"></i>
                            <select id="department" required>
                                <option value="" disabled selected>Select Department</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Information Systems">Information Systems</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="form-card">

                    <div class="card-title">
                        PROFILE PICTURE
                    </div>

                    <div class="form-group">

                        <label>UPLOAD PHOTO</label>
                        <div class="input-box">
                            <i class="fa-solid fa-circle-user"></i>

                            <input type="file" id="photo" accept="image/*">
                        </div>
                        <img id="preview">

                    </div>

                </div>

                <div class="form-card">

                    <div class="card-title">
                        ACCOUNT INFORMATION
                    </div>

                    <div class="form-group">

                        <label>SUBJECTS <span style="color:red;">*</span></label>
                        <div class="input-box">
                            <i class="fa-solid fa-book"></i>
                            <select id="subjects" multiple size="1" required>
                                <option value="Programming 1">Programming 1</option>
                                <option value="Programming 2">Programming 2</option>
                                <option value="Database Systems">Database Systems</option>
                                <option value="Web Development">Web Development</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>USERNAME <span style="color:red;">*</span></label>
                        <div class="input-box">
                            <i class="fa fa-user"></i>
                            <input type="text" id="username" placeholder="Username" required>
                        </div>
                    </div>

                    <div class="form-group">

                        <label>PASSWORD <span style="color:red;">*</span></label>
                        <div class="input-box">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password" placeholder="Password" required>
                        </div>
                        <div class="show-password">
                            <input type="checkbox" onclick="togglePass()">
                            <span>Show Password</span>
                        </div>
                        <p style= "color:red;">*It should include a combination of uppercase letters, lowercase letters, numbers, and special symbols.</p>
                    </div>

                    <button type="button" class="register-btn" id="teacher_add">
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
            $('#photo').on('change', function(e) {

                const file = e.target.files[0];

                if (!file) return;

                const allowed = ['image/jpeg', 'image/png', 'image/gif'];

                if (!allowed.includes(file.type)) {

                    alert("Only JPG, PNG, GIF allowed!");

                    $(this).val('');
                    $('#preview').hide();

                    return;
                }

                if (file.size > 2 * 1024 * 1024) {

                    alert("Image must be below 2MB!");

                    $(this).val('');
                    $('#preview').hide();

                    return;
                }

                const reader = new FileReader();

                reader.onload = function(e) {

                    $('#preview')
                        .attr('src', e.target.result)
                        .fadeIn();

                };

                reader.readAsDataURL(file);

            });

            function restrictNameInput(selector) {
                document.querySelectorAll(selector).forEach(input => {
                    input.addEventListener("input", function() {
                        this.value = this.value.replace(/[^a-zA-Z\s]/g, '');

                    });
                });
            }

            restrictNameInput("#fname");
            restrictNameInput("#mname");
            restrictNameInput("#lname");

            function togglePass() {

                let p = document.getElementById("password");

                p.type = (p.type === "password") ? "text" : "password";
            }

            $('#teacher_add').on('click', function() {

                let inputs = document.querySelectorAll(
                    "#teacherForm input[required], #teacherForm select[required]"
                );

                let firstEmpty = null;

                inputs.forEach(input => {
                let box = input.closest('.input-box');
                let isEmpty = false;
                if (input.id === "subjects") {
                    isEmpty = input.selectedOptions.length === 0;
                } else {
                    isEmpty = !input.value || input.value.trim() === "";
                }
                if (isEmpty) {
                    box.style.border = "2px solid red";
                    if (!firstEmpty) firstEmpty = input;
                } else {
                    box.style.border = "1px solid #eee";
                }
            });

                if (firstEmpty) {

                    showToast("Please fill out all required fields.", "error");

                    firstEmpty.focus();
                    return;
                }

                let subjects = Array.from(
                    document.getElementById("subjects").selectedOptions
                ).map(opt => opt.value);

                if (subjects.length === 0) {

                    showToast("Please select at least one subject.", "error");
                    return;
                }

                let formData = new FormData();

                formData.append("Add", 1);
                formData.append("fname", $('#fname').val());
                formData.append("mname", $('#mname').val());
                formData.append("lname", $('#lname').val());
                formData.append("department", $('#department').val());
                formData.append("subjects", subjects.join(","));
                formData.append("username", $('#username').val());
                formData.append("password", $('#password').val());

                let file = $('#photo')[0].files[0];

                if (file) {
                    formData.append("photo", file);
                }

                $('#teacher_add').prop('disabled', true);

                $.ajax({

                    url: 'teacherReg_conf.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(res) {
                        res = res.trim();
                        if (res === "1") {
                            showToast("Teacher added successfully!", "success");

                            $('#teacherForm')[0].reset();
                            $('#preview').hide();

                        } else {
                            showToast(res, "error");
                        }

                        $('#teacher_add').prop('disabled', false);
                    },
                    error: function() {

                        showToast("Server error.", "error");

                        $('#teacher_add').prop('disabled', false);
                    }

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