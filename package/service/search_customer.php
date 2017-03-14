<?php
	// session_start();
	/* if(!isset($_SESSION['ID'])){
		header("Location: ../../login.php");
	}*/
				
	include '../../database.php';
	header('Content-type: application/json');
	$json = file_get_contents('php://input');
	$data = json_decode($json, true);
	
	$sql = 'SELECT t.order_product_tracking_id, t.order_product_id, t.order_id, o.order_number, t.tracking_no, o.order_completed_status,  t.m3, t.weight, t.rate, t.total, t.statusid '.
			'FROM ordereas_db.customer_order_product_tracking t JOIN ordereas_db.customer_order o ON t.order_id = o.order_id '.
			'WHERE t.statusid = 2 ';
	$sql.=($data['order_no'] == '') ? '' : 'AND o.order_number = \''.$data['order_no'].'\' ';
	$sql.=($data['customer_id'] == '') ? '' : 'AND o.customer_id = \''.$data['customer_id'].'\' ';
	$sql.=($data['tracking_no'] == '') ? '' : 'AND o.tracking_no = \''.$data['tracking_no'].'\'';
	
	if($stmt = $con->prepare($sql)) {
		$stmt->execute();
		$stmt->store_result();
		
		$stmt->bind_result($order_product_tracking_id, $order_product_id, $order_id, $tracking_no, $m3, $weight, $rate, $total, $statusid, $order_number);
		
		while($stmt->fetch()){
			
			$result .= ($result == '') ? '' : ',';
			
			$result .= '{"order_product_tracking_id":"'.$order_product_tracking_id.'",'.
						'"order_product_id":"'.$order_product_id.'",'.
						'"order_id":"'.$order_id.'",'.
						'"order_number":"'.$order_number.'",'.
						'"tracking_no":"'.$tracking_no.'",'.
						'"order_completed_status":"'.$order_completed_status.'",'.
						'"m3":"'.$m3.'",'.
						'"weight":"'.$weight.'",'.
						'"rate":"'.$rate.'",'.
						'"total":"'.$total.'",'.
						'"statusid":"'.$statusid.'"'.
						
						'}';
		}
		
		
		$stmt->close();
		
	}
	echo '['.$result.']';
	$con->close();
?>