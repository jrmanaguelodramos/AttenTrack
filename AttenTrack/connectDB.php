<?php
/* Database connection settings */
	$servername = "localhost";
    $username = "root";		//put your phpmyadmin username.(default is "root")
    $password = "";			//if your phpmyadmin has a password put it here.(default is "root")
    $dbname = "attentrack";	//put your database name here which you created for this project.
    
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	
	if ($conn->connect_error) {
        die("Database Connection failed: " . $conn->connect_error);
    }
?>