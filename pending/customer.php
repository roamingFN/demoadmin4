<!DOCTYPE html>
<html>
	<head>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
	</head>
	<body style="background:#fff;">
	<?php
		session_start();
		if (!isset($_SESSION['ID'])){
			header("Location: ../login.php");
		}
					
		include '../database.php';
		
		echo '<table><tr><th></th></tr>';
		
		$stmt = $con->prepare('SELECT * FROM customer_address WHERE customer_id='.$_GET['cid']);
		$stmt->execute();
		$stmt->bind_result($aid,$cid,$aname,$line,$city,$country,$zipcode,$phone,$other);
		while($stmt->fetch()){
			echo '<tr><td>'.$aname.'</td></tr>'.
			'<tr><td>'.$line.'</td></tr>'.
			'<tr><td>'.$city.'</td></tr>'.
			'<tr><td>'.$country.'</td></tr>'.
			'<tr><td>'.$zipcode.'</td></tr>'.
			'<tr><td>'.$phone.'</td></tr>'.
			'<tr><td>'.$other.'</td></tr>'.
			'<tr><td></td></tr><tr><td></td></tr><tr><td></td></tr><tr><td></td></tr>';
		}
		echo '</table>';
	?>
	<script><?php 
		if(!empty($_GET['print'])){
			echo 'window.onload = function(){window.print();}';
		}
	?></script>
	</body>
</html>