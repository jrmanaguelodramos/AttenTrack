<!DOCTYPE html>
<html>

<head>
    <title>Intrusion Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f6f9;
            padding: 0;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 0 48px 32px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;

        }

        .title {
            background: #003366;
            color: #fff;
            padding: 16px 40px;
            border-radius: 0 0 16px 0;
            font-size: 32px;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            display: inline-block;
            margin-left: -48px;
            padding: 25px 70px;
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            margin-bottom: 20px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            color: #1a3a5c;
            background-color: #fff;
            text-decoration: none;
            border-radius: 50%;
            font-size: 20px;
            border: 2px solid #003366;
            transition: transform .3s;
        }

        .back-btn:hover {
            transform: translateX(4px);
        }

        .sub-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .filter-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-wrap label {
            font-size: 14px;
            font-weight: 600;
            color: #1a3a5c;
        }

        .filter-wrap select {
            padding: 10px 16px;
            border-radius: 50px;
            border: 1.5px solid #ccc;
            font-size: 14px;
            background: #fff;
            outline: none;
            color: #333;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .filter-wrap select:focus {
            border-color: #1a3a5c;
        }

        .table-wrapper {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e0e4ea;
            overflow: hidden;
            margin-bottom: 32px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #f4f6f9;
            border-bottom: 1px solid #e0e4ea;
        }

        th {
            padding: 14px 20px;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #1a3a5c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        td {
            padding: 14px 20px;
            text-align: center;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #f0f2f5;
            border-left: none;
            border-right: none;
            border-top: none;
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover td {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-danger {
            background: #fde8e8;
            color: #c0392b;
            border: 1.5px solid #f5c6c6;
        }

        .badge-warning {
            background: #fef3e2;
            color: #b8860b;
            border: 1.5px solid #f5dfa0;
        }

        .badge-normal {
            background: #e8f0fb;
            color: #1a3a5c;
            border: 1.5px solid #c0d0e8;
        }

        tr.danger td {
            background: #fde8e8 !important;
        }

        tr.warning td {
            background: #fef3e2 !important;
        }

        tr.danger:hover td {
            background: #fbd5d5 !important;
        }

        tr.warning:hover td {
            background: #fdecc8 !important;
        }


        .pagination-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 16px;
            margin-bottom: 32px;
        }

        .pagination-info {
            font-size: 13px;
            color: #666;
        }

        .pagination-btns {
            display: flex;
            gap: 6px;
            justify-content: center;
            align-items: center;
        }

        .page-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1.5px solid #ccc;
            background: #fff;
            color: #1a3a5c;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-btn:hover {
            background: #e8edf3;
            border-color: #1a3a5c;
        }

        .page-btn.active {
            background: #1a3a5c;
            color: #fff;
            border-color: #1a3a5c;
        }

        .page-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .per-page-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #666;
        }

        .per-page-wrap select {
            padding: 6px 12px;
            border-radius: 8px;
            border: 1.5px solid #ccc;
            font-size: 13px;
            outline: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="page-header">
            <div class="title">Intrusion Detection</div>
            <a href="dashboard.php" class="back-btn">←</a>
        </div>

        <div class="sub-bar">
            <div class="filter-wrap">
                <label><i class="fa-solid fa-filter"></i> Filter Room:</label>
                <select id="roomFilter" onchange="loadLogs()">
                    <option value="ALL">All Rooms</option>
                </select>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th><i class="fa-solid fa-hashtag"></i> ID</th>
                        <th><i class="fa-solid fa-id-card"></i> Student ID</th>
                        <th><i class="fa-solid fa-credit-card"></i> Card UID</th>
                        <th><i class="fa-solid fa-door-open"></i> Room</th>
                        <th><i class="fa-solid fa-triangle-exclamation"></i> Event</th>
                        <th><i class="fa-solid fa-circle-info"></i> Description</th>
                        <th><i class="fa-solid fa-calendar"></i> Date</th>
                        <th><i class="fa-solid fa-clock"></i> Time</th>
                    </tr>
                </thead>
                <tbody id="logTable">
                    <tr>
                        <td colspan="8" style="color:#aaa; padding:30px;">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div class="pagination-bar">
        <div></div>
        <div class="pagination-btns" id="pagination-btns"></div>
    </div>

    <script>
        let allLogs = [];
        let currentPage = 1;

        function getBadgeClass(event_type) {
            if (event_type === "UNKNOWN_CARD" || event_type === "INVALID_DEVICE") return "badge-danger";
            if (event_type === "NOT_ENROLLED" || event_type === "DUPLICATE_SCAN") return "badge-warning";
            return "badge-normal";
        }

        function getRowClass(event_type) {
            if (event_type === "UNKNOWN_CARD" || event_type === "INVALID_DEVICE") return "danger";
            if (event_type === "NOT_ENROLLED" || event_type === "DUPLICATE_SCAN") return "warning";
            return "";
        }

        function renderTable() {
            let perPage = 10;
            let totalPages = Math.ceil(allLogs.length / perPage);
            let start = (currentPage - 1) * perPage;
            let end = start + perPage;
            let pageLogs = allLogs.slice(start, end);

            let output = "";
            if (allLogs.length === 0) {
                output = '<tr><td colspan="8" style="color:#aaa; padding:30px;">No intrusion logs found.</td></tr>';
            } else {
                pageLogs.forEach(log => {
                    let rowClass = getRowClass(log.event_type);
                    let badgeClass = getBadgeClass(log.event_type);
                    output += `
                    <tr class="${rowClass}">
                        <td>${log.id}</td>
                        <td>${log.sID}</td>
                        <td>${log.card_UID}</td>
                        <td>${log.room}</td>
                        <td><span class="badge ${badgeClass}">${log.event_type}</span></td>
                        <td>${log.description}</td>
                        <td>${log.date}</td>
                        <td>${log.time}</td>
                    </tr>
                `;
                });
            }

            document.getElementById("logTable").innerHTML = output;

            let btns = "";
            btns += `<button class="page-btn" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>‹</button>`;
            for (let i = 1; i <= totalPages; i++) {
                if (totalPages > 7) {
                    if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                        btns += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
                    } else if (i === currentPage - 2 || i === currentPage + 2) {
                        btns += `<button class="page-btn" disabled>…</button>`;
                    }
                } else {
                    btns += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
                }
            }
            btns += `<button class="page-btn" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}>›</button>`;
            document.getElementById("pagination-btns").innerHTML = btns;
        }

        function changePage(page) {
            let perPage = 10;
            let totalPages = Math.ceil(allLogs.length / perPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderTable();
        }

        function loadLogs() {
            let room = document.getElementById("roomFilter").value;
            fetch('intrusion_fetch.php?room=' + room)
                .then(res => res.json())
                .then(data => {
                    allLogs = data;
                    let perPage = 10;
                    let totalPages = Math.ceil(data.length / perPage);

                    if (currentPage > totalPages) {
                        currentPage = totalPages || 1;
                    }

                    renderTable();
                })
                .catch(() => {
                    document.getElementById("logTable").innerHTML =
                        '<tr><td colspan="8" style="color:#e74c3c; padding:30px;">Failed to load logs.</td></tr>';
                });
        }

        function loadRooms() {
            fetch('get_rooms.php')
                .then(res => res.json())
                .then(data => {
                    let select = document.getElementById("roomFilter");
                    data.forEach(room => {
                        let option = document.createElement("option");
                        option.value = room;
                        option.text = room;
                        select.appendChild(option);
                    });
                });
        }

        loadRooms();
        setInterval(loadLogs, 3000);
        loadLogs();
    </script>

</body>

</html>