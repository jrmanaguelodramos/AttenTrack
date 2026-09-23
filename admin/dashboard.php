<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>

  <header class="header">
    <div class="title-box">
      <h2>Registrar Menu</h2>
    </div>

    <button type="button" class="logout-btn" onclick="openLogoutModal()">
      <span class="logout-icon">↩</span>
      <span>Log Out</span>
  </button>
  </header>

  <main class="menu">
    <a href="list_teacher.php" class="card" aria-label="Go to Teacher section">
      <span class="icon"><i class="fas fa-chalkboard-teacher"></i></span>
      <span class="label">Teacher</span>
    </a>

    <a href="list_student.php" class="card" aria-label="Go to Student section">
      <span class="icon"><i class="fas fa-user-graduate"></i></span>
      <span class="label">Student</span>
    </a>

    <a href="devices.php" class="card" aria-label="Go to Rooms and Scanner section">
      <span class="icon">
        <i class="fas fa-door-open"></i> / <i class="fas fa-qrcode"></i>
      </span>
      <span class="label">Rooms / Scanner</span>
    </a>

    <a href="intrusion_dashboard.php" class="card" aria-label="Go to Intrusion Reports">
      <span class="icon">
        <i class="fas fa-exclamation-triangle"></i>
      </span>
      <span class="label">Intrusion Logs</span>
    </a>
  </main>

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

  <script src="script.js"></script>

    <script>
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