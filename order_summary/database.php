<?php
	$server = 'localhost';
	$user = 'ordereas_test';
	$pass = '01234';
	$db = 'ordereas_demo';
	
	$con = new mysqli($server,$user,$pass,$db);
	if(mysqli_connect_errno()){
		echo 'Database Connection Failed : '.mysqli_connect_error();
		exit();
	}
	$con->set_charset("utf8");
?>