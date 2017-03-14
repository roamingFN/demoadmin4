<?php
	// session_start();
	/* if(!isset($_SESSION['ID'])){
		header("Location: ../../login.php");
	} */
				
	include '../../database.php';
	$json = file_get_contents('php://input');
	$data = json_decode($json, true);
	
	$sql = 'SELECT p.packageid, p.packageorder, o.order_number, t.tracking_no, t.m3, t.weight, t.rate, t.total '
			.'FROM package_detail p INNER JOIN customer_order o ON p.order_id = o.order_id '
			.'INNER JOIN customer_order_product_tracking t ON p.order_product_tracking_id = t.order_product_tracking_id '
			.'WHERE p.packageid = '.$data['id'].' '
			.'ORDER BY p.packageorder';
	if($stmt = $con->prepare($sql)) {
								
		$stmt->execute();
		$stmt->store_result();
		$count = $stmt->num_rows;
		// $allPage = ceil($count/$pageSize);
		
		$stmt->bind_result($packageid,
							$packageorder,
							$order_number,
							$tracking_no,
							$m3,
							$weight,
							$rate,
							$total);
		
		while($stmt->fetch()){
			$result .= ($result == '') ? '' : ',';
			$result .= '{"packageid":"'.$package_id.'",'.
						'"packageorder":"'.$packageorder.'",'.
						'"order_number":"'.$order_number.'",'.
						'"tracking_no":"'.$tracking_no.'",'.						
						'"m3":"'.$m3.'",'.
						'"weight":"'.$weight.'",'.
						'"rate":"'.$rate.'",'.
						'"total":"'.$total.'"'.
						'}';
		}
		$stmt->close();
	}
	
	/* $result = ($result == '') ? '{"packageid":"",'.
						'"packageorder":"",'.
						'"order_number":"",'.
						'"tracking_no":"",'.						
						'"m3":"",'.
						'"weight":"",'.
						'"rate":"",'.
						'"total":""'.
						'}' : $result; */
	echo '['.$result.']';
	$con->close();
?>