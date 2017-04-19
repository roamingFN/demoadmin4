<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	include './utility/function.php';

	$data = json_decode($_POST['data'],true);

	//update order_product
	$sql = 'UPDATE customer_order_product 
		SET current_status=? 
		WHERE order_product_id=?';
	foreach($data as $key=>$item) {
		if($key!='oid'&& $key!='status'){
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ii',$data['status'],$key);
			$res = $stmt->execute();
			if(!$res){
				echo $con->error;
			}
		}
	}

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