<?php
session_start();
require 'connectDB.php';

if (isset($_SESSION['user_id'])) {
    header("Location: teacher/index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {

        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {

                if (password_verify($password, $user['password'])) {

                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    if ($user['role'] === 'admin') {
                        header("Location: admin/dashboard.php");
                    } elseif ($user['role'] === 'teacher') {
                        header("Location: teacher/index.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit();

                } else {
                    $error = "Invalid username or password.";
                }

            } else {
                $error = "Invalid username or password.";
            }

            $stmt->close();
        } else {
            $error = "Database error. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="teacher/css/login.css">
</head>

<body>
    <nav class="topbar">
        <div class="topbar-logo">
            <img src="img/logo.png" alt="AttenTrack Logo">
            <span class="logo-text"><span class="logo-dim">Atten</span>Track</span>
        </div>
        <div class="topbar-badge">Smart Attendance System</div>
    </nav>

    <div class="main-body">

    
        <div class="left-panel">
            <div class="left-top">
                <h1 class="headline">Track.<br>Monitor.<br><em>Succeed.</em></h1>
                <p class="desc">Access attendance records, teacher dashboards, and student monitoring tools all in one place.</p>

                <div class="features">
                    <div class="feature-item">
                        <div class="feat-icon">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div>
                            <div class="feat-name">RFID Attendance</div>
                            <div class="feat-desc">Instant tracking via RFID technology</div>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feat-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="feat-name">Real-Time Reports</div>
                            <div class="feat-desc">Monitor and generate reports instantly</div>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feat-icon">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div>
                            <div class="feat-name">Secure Access</div>
                            <div class="feat-desc">Role-based login for Admin &amp; Teachers</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-row">
                <div class="stat-item">
                    <div class="stat-num">Shield</div>
                    <div class="stat-label">Intrusion Detection</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">AES</div>
                    <div class="stat-label">Encryption</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">Role</div>
                    <div class="stat-label">Secure Access</div>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="login-box">
                <h2 class="form-title">Welcome back!</h2>
                <p class="form-hint">Sign in to your account</p>

                <form method="POST" autocomplete="off">

                    <div class="input-group">
                        <label>USERNAME</label>
                        <input type="text" name="username" placeholder="Enter your username" required>
                    </div>

                    <div class="input-group">
                        <label>PASSWORD</label>
                        <div class="password-wrap">
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <span class="toggle-pass" onclick="togglePassword()">Show</span>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-label">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <button type="submit" class="submit-btn">Sign In</button>
                </form>

                <p class="contact-text">New to AttenTrack? <a href="#">Contact Admin</a></p>
            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const pass = document.getElementById("password");
            const btn = document.querySelector(".toggle-pass");
            if (pass.type === "password") {
                pass.type = "text";
                btn.textContent = "Hide";
            } else {
                pass.type = "password";
                btn.textContent = "Show";
            }
        }
    </script>

</body>
</html>