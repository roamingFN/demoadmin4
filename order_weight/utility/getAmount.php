<?php
		session_start();
		if(!isset($_SESSION['ID'])){
			header("Location: ../login.php");
		}
		require_once('../../database.php');
		include './function.php';

		$myArray = array(); 
		$data = json_decode($_POST['data'],true);
		$dataSet = getTrackingInfo($con,$data['tid']);
		$sql = 'SELECT tracking_no,amount FROM customer_order_product_tracking WHERE order_id='.$dataSet[0]['order_id'].' AND order_product_id='.$dataSet[0]['order_product_id'];
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