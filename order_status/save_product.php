<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	include './utility/function.php';

	$data = json_decode($_POST['data'],true);

	//update customer_order
	$sql = 'UPDATE customer_order'. 
		' SET order_status_code=?,update_by=?'.
		' WHERE order_id=?';
	if ($stmt = $con->prepare($sql)) {
		$stmt->bind_param('isi',$data['status'],$_SESSION['ID'],$data['oid']);
		$stmt->execute();
	}
	else {
		echo 'Error while updating customer_order '.$con->error;
		return;
	}
	$con->close();
	echo 'success';
?>