<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Menu</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/regMenu.css">
</head>

<body>

  <header class="header">
    <div class="title-box">
      <h2>Registrar Menu</h2>
    </div>

    <button class="logout-btn" aria-label="Log out">
      <span class="logout-icon" aria-hidden="true">↩</span>
      <span>Log Out</span>
    </button>
  </header>

  <main class="menu">
    <a href="teacher.php" class="card" aria-label="Go to Teacher section">
      <span class="icon"><i class="fas fa-chalkboard-teacher"></i></span>
      <span class="label">Teacher</span>
    </a>

    <a href="student.php" class="card" aria-label="Go to Student section">
      <span class="icon"><i class="fas fa-user-graduate"></i></span>
      <span class="label">Student</span>
    </a>

    <a href="rooms.php" class="card" aria-label="Go to Rooms and Scanner section">
      <span class="icon">
        <i class="fas fa-door-open"></i>
        <i class="fas fa-qrcode"></i>
      </span>
      <span class="label">Rooms / Scanner</span>
    </a>

    <a href="reports.php" class="card" aria-label="Go to Reports section">
      <span class="icon"><i class="fas fa-chart-bar"></i></span>
      <span class="label">Reports</span>
    </a>
  </main>

  <script src="script.js"></script>
</body>

</html>