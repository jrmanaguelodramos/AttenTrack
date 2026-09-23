<?php include_once '../connectDB.php'; 
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/list_teacher.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
     <div class="header">
        <div class="title-box">
        LIST OF TEACHERS
        </div>

        <button class="back-btn">
        <span class="back-icon">←</span>
        </button>
    </div>

    <div class="teacher-actions">
            <button class="archive-btn">
                <i class="fa-solid fa-box-archive"></i>ㅤArchive
            </button>
            <button class="add-btn">
                <i class="fa-solid fa-plus"></i>ㅤAdd Teacher
            </button>
        </div>
    </div>

    <div class="teacher-search">
        <form method="GET" action="list_teacher.php" class="search-box">

            <input type="text" name="search" id="searchInput"
                placeholder="Search..."
                value="<?php echo htmlspecialchars($search); ?>">

            <button type="button" class="clear-btn" onclick="clearSearch()">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

        </form>
    </div>

    <div class="teacher-list">
        <?php
            $limit = 10; 
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $page = max($page, 1); 
            $offset = ($page - 1) * $limit;

            $searchParam = "%$search%";

            $sql = "SELECT * FROM teachers 
                    WHERE `fname` LIKE ? 
                    ORDER BY `lname` ASC
                    LIMIT ? OFFSET ?";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "sii",
                $searchParam,
                $limit,
                $offset
            );

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $teacherName = htmlspecialchars(
                        $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']
                    );
                    echo "
                    <div class='teacher-row'>
                        <i class='fa-solid fa-circle-question'></i> $teacherName
                    </div>";
                }
            } else {
                echo "<div class='teacher-row'>No teachers found.</div>";
            }
        ?>
    </div>

    <div class="pagination">
        <?php
            $countQuery = "SELECT COUNT(*) AS total FROM teachers
                           WHERE `fname` LIKE ?";

            $countStmt = $conn->prepare($countQuery);
            $countStmt->bind_param("s", $searchParam);
            $countStmt->execute();
            $countResult = $countStmt->get_result();

            $totalRows = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($totalRows / $limit);

            if ($totalPages > 1) {

                if ($page > 1) {
                    echo "<a href='?search=$search&page=" . ($page - 1) . "' class='page-btn'>Previous</a>";
                }

                for ($i = 1; $i <= $totalPages; $i++) {
                    $active = ($i == $page) ? "active" : "";
                    echo "<a href='?search=$search&page=$i' class='page-btn $active'>$i</a>";
                }

                if ($page < $totalPages) {
                    echo "<a href='?search=$search&page=" . ($page + 1) . "' class='page-btn'>Next</a>";
                }
            }
        ?>
    </div>

    <script>
        function clearSearch() {
            const input = document.getElementById("searchInput");

            input.value = "";

            window.location.href = "list_teacher.php";
        }
    </script>

</body>
</html>