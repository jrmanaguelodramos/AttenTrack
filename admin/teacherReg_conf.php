<?php
require_once '../connectDB.php';

if (isset($_POST['Add'])) {

    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $department = $_POST['department'];
    $subjects = $_POST['subjects'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($fname) && !empty($lname) && !empty($username) && !empty($password)) {

        // check duplicate username
        $sql = "SELECT id FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            echo "Username already taken!";
            exit();
        }

        // hash password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // =========================
        // 📸 HANDLE IMAGE (BLOB)
        // =========================
        $photoData = null;

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {

            $fileTmp = $_FILES['photo']['tmp_name'];
            $fileSize = $_FILES['photo']['size'];

            // validate size (2MB)
            if ($fileSize > 2 * 1024 * 1024) {
                echo "File too large (max 2MB)";
                exit();
            }

            // validate real image
            if (!getimagesize($fileTmp)) {
                echo "Invalid image file!";
                exit();
            }

            $photoData = file_get_contents($fileTmp);
        }

        // =========================
        // 👤 INSERT USER
        // =========================
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'teacher')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed);
        $stmt->execute();

        $userID = $conn->insert_id;

        // =========================
        // 👨‍🏫 INSERT TEACHER (WITH BLOB)
        // =========================
        $sql = "INSERT INTO teachers (userID, fname, mname, lname, department, subjects, photo)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $null = NULL; // required for blob

        $stmt->bind_param("isssssb", $userID, $fname, $mname, $lname, $department, $subjects, $null);

        // send blob data (index 6 = 7th column)
        $stmt->send_long_data(6, $photoData);

        $stmt->execute();

        echo 1;
        exit();
    } else {
        echo "Empty Fields";
        exit();
    }
}
