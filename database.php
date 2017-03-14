<?php
	$server = 'localhost';
	$user = 'root';
	$pass = '';
	$db = 'ordereas_db';
	
	$con = new mysqli($server,$user,$pass,$db);
	if(mysqli_connect_errno()){
		echo 'Database Connection Failed : '.mysqli_connect_error();
		exit();
	}
	$con->set_charset("utf8");
?>