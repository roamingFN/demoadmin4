<?php
		session_start();
		if(!isset($_SESSION['ID'])){
			header("Location: ../login.php");
		}
		require_once('../database.php');
		$myArray = array(); 
		$data = json_decode($_POST['data'],true);
		$sql = 'SELECT * FROM return_email_log WHERE order_product_id='.$data['opid'].' AND return_type=1';
		$result = $con->query($sql); 
		if (!$result) {
			throw new Exception("Database Error ".$con->error);
		}
		$row = $result->num_rows; 
		while ($row = $result->fetch_assoc()) { 
				$myArray[] = $row; 
		}
		echo json_encode($myArray);
?>