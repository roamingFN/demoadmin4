<?php
	// session_start();
	/* if(!isset($_SESSION['ID'])){
		header("Location: ../../login.php");
	} */
				
	include '../../database.php';
	$json = file_get_contents('php://input');
	$data = json_decode($json, true);
	
	$sql = 'SELECT p.packageid, p.packageno, p.customerid, c.customer_firstname, c.customer_lastname, '
							.'DATE_FORMAT(createdate,\'%d/%m/%Y\') createdate, s.transport_id, s.transport_th_name, '
							.'p.amount, p.amount_other, p.amount_thirdparty, p.total, p.statusid, p.shippingno, p.shipping_address '
							.'FROM package p INNER JOIN customer c ON p.customerid = c.customer_id '
							.'LEFT JOIN website_transport s ON p.shippingid = s.transport_id '
							.'WHERE p.packageid = '.$data['id'];
	if($stmt = $con->prepare($sql)) {
								
		$stmt->execute();
		$stmt->store_result();
		$count = $stmt->num_rows;
		// $allPage = ceil($count/$pageSize);
		
		$stmt->bind_result($package_id,
							$package_number,
							$customerid,
							$customer_firstname,
							$customer_lastname,
							$createdate,
							$transport_id,
							$transport_th_name,							
							$amount,
							$amount_other,
							$amount_thirdparty,
							$total,
							$statusid,
							$shippingno,
							$shipping_address);
		
		while($stmt->fetch()){
			$result .= ($result == '') ? '' : ',';
			$result .= '{"packageid":"'.$package_id.'",'.
						'"packagenumber":"'.$package_number.'",'.
						'"customer":{"name":"'.$customer_firstname.' '.$customer_lastname.'"},'.
						'"createdate":"'.$createdate.'",'.						
						'"transport_id":"'.$transport_id.'",'.
						'"transport_th_name":"'.$transport_th_name.'",'.
						'"amount":"'.$amount.'",'.
						'"amount_other":"'.$amount_other.'",'.
						'"amount_thirdparty":"'.$amount_thirdparty.'",'.
						'"total":"'.$total.'",'.
						'"statusid":"'.$statusid.'",'.
						'"shippingno":"'.$shippingno.'",'.
						'"shipping_address":"'.$shipping_address.'",'.
						'"remark":"",'.
						'"items":[]'.
						'}';
		}
		$stmt->close();
	}
	$result = ($result == '') ? '{"packageid":"",'.
						'"packagenumber":"",'.
						'"customer":{"name":""},'.
						'"createdate":"",'.						
						'"transport_id":"",'.
						'"transport_th_name":"",'.
						'"amount":0.00,'.
						'"amount_other":0.00,'.
						'"amount_thirdparty":0.00,'.
						'"total":0.00,'.
						'"statusid":"",'.
						'"shippingno":"",'.
						'"shipping_address":"",'.
						'"remark":""'.
						'}' : $result;
	echo $result;
	$con->close();
?>