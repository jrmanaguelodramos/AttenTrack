<?php 
session_start();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
	.dev-table-wrapper {
		background: #fff;
		border-radius: 12px;
		border: 1px solid #e0e4ea;
		overflow: hidden;
		margin-left:120px;
		margin-right:120px;
	}

	.dev-table {
		width: 100%;
		border-collapse: collapse;
	}

	.dev-table thead tr {
		background: #f4f6f9;
		border-bottom: 1px solid #e0e4ea;
		text-align:center;
	}

	.dev-table th {
		padding: 14px 20px;
		text-align: center;  
		font-size: 13px;
		font-weight: 700;
		color: #1a3a5c;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		border: none;
	}

	.dev-table td {
		padding: 14px 20px;
		font-size: 14px;
		color: #333;
		border-bottom: 1px solid #f0f2f5;
		vertical-align: middle;
		border-left: none;
		border-right: none;
		border-top: none;
		text-align: center;  
	}

	.dev-table th i {
		margin-right: 6px;
		color: #1a3a5c;
	}

	.dev-table tbody tr:last-child td {
		border-bottom: none;
	}

	.dev-table tbody tr:hover td {
		background: #f9fafb;
	}

	.uid-cell {
		display: flex;
		align-items: center;
		gap: 8px;
		justify-content: center; 
	}

	.uid-text {
		font-family: monospace;
		font-size: 14px;
		color: #555;
		background: #f4f6f9;
		padding: 3px 8px;
		border-radius: 6px;
	}

	.mode_select {
		display: flex;
		gap: 4px;
		flex-wrap: wrap;
		justify-content: center; 
	}

	.mode_select input[type="radio"] {
		display: none;
	}

	.mode_select label {
		padding: 5px 12px;
		border-radius: 50px;
		border: 1.5px solid #ccd6e0;
		background: #f4f6f9;
		color: #1a3a5c;
		font-size: 12px;
		font-weight: 600;
		cursor: pointer;
		transition: .2s;
		white-space: nowrap;
	}

	.mode_select label:hover {
		background: #dce6f0;
		transform: scale(1.1);
	}

	.mode_select input[type="radio"]:checked + label {
		background: #1a3a5c;
		color: #fff;
		border-color: #1a3a5c;
	}

	.btn-refresh {
		background: #f0a500;
		color: #fff;
		border: none;
		border-radius: 8px;
		padding: 7px 10px;
		font-size: 13px;
		cursor: pointer;
		transition:0.2s;
	}

	.btn-refresh:hover { 
		background: #d4900a; 
		transform: scale(1.1);
	}

	.btn-delete {
		background: #e74c3c;
		color: #fff;
		border: none;
		border-radius: 8px;
		padding: 7px 10px;
		font-size: 13px;
		cursor: pointer;
		transition:0.2s;
	}

	.btn-delete:hover {
		 background: #c0392b; 
		 transform: scale(1.1);
	}
</style>

<div class="dev-table-wrapper">
    <table class="dev-table">
        <thead>
            <tr>
                <th><i class="fa-solid fa-door-open"></i> Room</th>
                <th><i class="fa-solid fa-building"></i> Building</th>
                <th><i class="fa-solid fa-id-card"></i> Device UID</th>
                <th><i class="fa-solid fa-calendar"></i> Date</th>
                <th><i class="fa-solid fa-repeat"></i> Mode</th>
                <th><i class="fa-solid fa-gear"></i> Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require '../connectDB.php';
            $sql = "SELECT * FROM devices ORDER BY id DESC";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                echo '<tr><td colspan="6" style="text-align:center; color:red; padding:20px;">SQL Error</td></tr>';
            } else {
                mysqli_stmt_execute($result);
                $resultl = mysqli_stmt_get_result($result);
                echo '<form action="" method="POST" enctype="multipart/form-data">';
                while ($row = mysqli_fetch_assoc($resultl)) {
                    $radio1 = ($row["device_mode"] == 0) ? "checked" : "";
                    $radio2 = ($row["device_mode"] == 1) ? "checked" : "";
                    $radio3 = ($row["device_mode"] == 2) ? "checked" : "";

                    $de_mode = '
                    <div class="mode_select">
                        <input type="radio" id="'.$row["id"].'-1" name="'.$row["id"].'" class="mode_sel" data-id="'.$row["id"].'" value="0" '.$radio1.'/>
                        <label for="'.$row["id"].'-1">Enrollment</label>
                        <input type="radio" id="'.$row["id"].'-2" name="'.$row["id"].'" class="mode_sel" data-id="'.$row["id"].'" value="1" '.$radio2.'/>
                        <label for="'.$row["id"].'-2">Attendance In</label>
                        <input type="radio" id="'.$row["id"].'-3" name="'.$row["id"].'" class="mode_sel" data-id="'.$row["id"].'" value="2" '.$radio3.'/>
                        <label for="'.$row["id"].'-3">Attendance Out</label>
                    </div>';

                    echo '
                    <tr>
                        <td>'.$row["room"].'</td>
                        <td>'.$row["building"].'</td>
                        <td>
                            <div class="uid-cell">
                                <button type="button" class="btn-refresh dev_uid_up" data-id="'.$row["id"].'" title="Update device token">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </button>
                                <span class="uid-text">'.($row["device_uid"] ?: '—').'</span>
                            </div>
                        </td>
                        <td>'.$row["device_date"].'</td>
                        <td>'.$de_mode.'</td>
                        <td>
                            <button type="button" class="btn-delete dev_del" data-id="'.$row["id"].'" title="Delete device">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>';
                }
                echo '</form>';
            }
            ?>
        </tbody>
    </table>
</div>