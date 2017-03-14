<?php
	// session_start();
	/* if(!isset($_SESSION['ID'])){
		header("Location: ../../login.php");
	} */
				
	include '../../database.php';
	header('Content-type: application/json');
	$json = file_get_contents('php://input');
	$data = json_decode($json, true);
	
	$pno = '';
	
	$stmt = $con->prepare('insert into package (packageno, customerid, createddate, total_tracking, shippingid, shippingno, amount, statusid, adduser, adddate, sentemail) values (?, ?, ?, 0, ?, ?, 0, 0, ?, NOW(), 0)');
	$stmt->bind_param('sisiss',$pno,$data['customerid'],$data['createddate'],$data['shippingid'],$data['shippingno'],$data['adduser']);
	$res = $stmt->execute();
	if(!$res){
		echo $con->error;
	}

	echo 'success';
	
	$con->close();
?>