<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	
	$data = json_decode($_POST['data'],true);
	while($item = current($data)){
		$stmt = $con->prepare('UPDATE customer_request_topup SET topup_status=?,remarkc=? WHERE topup_id=?');
		$stmt->bind_param('sss',$item['status'],$item['remarkc'],key($data));
		$res = $stmt->execute();
		if(!$res){
			echo $con->error;
		}
		next($data);
	}
	echo 'success';
	
	$con->close();
?>