<?php
session_start();
require 'connectDB.php';

if (isset($_SESSION['user_id'])) {
    header("Location: teacher/index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        if (password_verify($password, $row['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            switch ($row['role']) {
                case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                case 'teacher':
                    header("Location: teacher/index.php");
                    break;
                default:
                    header("Location: index.php");
            }
            exit();

        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "User not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="admin/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="left_panel">
            <img src="img/logo.png" alt="RFID Illustration">
        </div>
        
        <div class="right_panel">
            <div class="login-box">
                <h3>Welcome To</h3>
                <h1>AttenTrack</h1>
                
                <div class="avatar"></div>
                
                <form action="" method="POST">
                    
                    <div class="input-group">
                        <label>Username</label>
                        <div class="icon">
                            <i class="fa fa-user"></i>
                            <input type="text" name="username" placeholder="Enter Username" required>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label>Password</label>
                        <div class="icon">
                            <i class="fa fa-lock"></i>
                            <input type="password" name="password" placeholder="Enter Password" required>
                        </div>
                    </div>
                    
                    <button type="submit">LOG IN</button>
                    <?php if (!empty($error)): ?>
                        <p style="color:red;"><?php echo $error; ?></p>
                    <?php endif; ?>
                </form>
                <p class="note">Contact admin if you don't have access</p>
            </div>
        </div>

    </div>

</body>

</html>