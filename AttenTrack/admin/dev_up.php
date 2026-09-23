<?php 
session_start();
?>
<div class="table-responsive">          
	<table class="table">
		<thead>
	      <tr>
	        <th>Room</th>
	        <th>Building</th>
	        <th>De.UID</th>
	        <th>De.Date</th>
	        <th>De.Mode</th>
	        <th>De.Config</th>
	      </tr>
    	</thead>
    	<tbody>
			<?php  
		    	require'../connectDB.php';
		    	$sql = "SELECT * FROM devices ORDER BY id DESC";
				$result = mysqli_stmt_init($conn);
				if (!mysqli_stmt_prepare($result, $sql)) {
				    echo '<p class="error">SQL Error</p>';
				} 
				else{
				    mysqli_stmt_execute($result);
				    $resultl = mysqli_stmt_get_result($result);
				    echo '<form action="" method="POST" enctype="multipart/form-data">';
					    while ($row = mysqli_fetch_assoc($resultl)){

					      	$radio1 = ($row["device_mode"] == 0) ? "checked" : "" ;
					      	$radio2 = ($row["device_mode"] == 1) ? "checked" : "" ;
							$radio3 = ($row["device_mode"] == 2) ? "checked" : "" ;

					      	$de_mode = '<div class="mode_select">
					      	<input type="radio" id="'.$row["id"].'-1" name="'.$row["id"].'" class="mode_sel" data-id="'.$row["id"].'" value="0" '.$radio1.'/>
					                    <label for="'.$row["id"].'-1">Enrollment</label>
		                    <input type="radio" id="'.$row["id"].'-2" name="'.$row["id"].'" class="mode_sel" data-id="'.$row["id"].'" value="1" '.$radio2.'/>
					                    <label for="'.$row["id"].'-2">Attendance in</label>
							<input type="radio" id="'.$row["id"].'-3" name="'.$row["id"].'" class="mode_sel" data-id="'.$row["id"].'" value="2" '.$radio3.'/>
					                    <label for="'.$row["id"].'-3">Attendance out</label>
					                    </div>';

					    	echo '<tr>
							        <td>'.$row["room"].'</td>
							        <td>'.$row["building"].'</td>
							        <td><button type="button" class="dev_uid_up btn btn-warning" id="del_'.$row["id"].'" data-id="'.$row["id"].'" title="Update this device Token"><span class="glyphicon glyphicon-refresh"> </span></button>
							        	'.$row["device_uid"].'
							        </td>
							        <td>'.$row["device_date"].'</td>
							        <td>'.$de_mode.'</td>
							        <td>
								    	<button type="button" class="dev_del btn btn-danger" id="del_'.$row["id"].'" data-id="'.$row["id"].'" title="Delete this device"><span class="glyphicon glyphicon-trash"></span></button>
								    </td>
							      </tr>';
					    }
				    echo '</form>';
				}
		    ?>
    	</tbody>
	</table>
</div>
<!-- <button type="button" class="dev_pro_up btn btn-info" id="del_'.$row["id"].'" data-id="'.$row["id"].'"  title="Change this device Project"><span class="glyphicon glyphicon-cog"> </span></button> -->