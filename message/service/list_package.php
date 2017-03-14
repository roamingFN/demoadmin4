<?php
	function getorderno($con, $id) {
		if($stmt = $con->prepare('SELECT o.order_number '
								.'FROM package_detail p INNER JOIN customer_order o ON p.order_id = o.order_id '
								.'WHERE packageid = '.$id)) {
				
			$stmt->execute();
			$stmt->store_result();
			
			$stmt->bind_result($order_number);
			while($stmt->fetch()){			
				$result .= ($result == '') ? '' : ',';			
				$result .= $order_number;
			
			
			}
			$stmt->close();
			
		}
			
		return '['.$result.']';

	}

	// session_start();
	/* if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	} */
				
	include '../../database.php';
		
	$page = $_GET['page'];
	
	if($stmt = $con->prepare('SELECT p.packageid, p.packageno, p.createdate, p.total_tracking, p.shippingid, p.shippingno, p.shipping_address,'
							.'p.amount, p.amount_other, p.amount_thirdparty, p.total, p.statusid, c.customer_firstname, c.customer_lastname,'
							.'s.transport_id, s.transport_th_name '
							.'FROM package p INNER JOIN customer c ON p.customerid = c.customer_id '
							.'LEFT JOIN website_transport s ON p.shippingid = s.transport_id '
							.'ORDER BY p.packageno DESC')) {
								
		$stmt->execute();
		$stmt->store_result();
		$count = $stmt->num_rows;
		// $allPage = ceil($count/$pageSize);
		
		$stmt->bind_result($package_id,
							$package_number,
							$createdate,
							$total_tracking,
							$shippingid,
							$shippingno,
							$shipping_address,
							$amount,
							$amount_other,
							$amount_thirdparty,
							$total,
							$statusid,
							$customer_firstname,
							$customer_lastname,
							$transport_id,
							$transport_th_name);
		
		while($stmt->fetch()){
			$result .= ($result == '') ? '' : ',';
			$result .= '{"packageid":"'.$package_id.'",'.
						'"packagenumber":"'.$package_number.'",'.
						'"customer":{"name":"'.$customer_firstname.' '.$customer_lastname.'"},'.
						'"createdate":"'.$createdate.'",'.
						'"orderno":'.getorderno($con, $package_id).','.
						'"tracking":"'.$total_tracking.'",'.
						'"tracking_complete":"0",'.
						'"tracking_incomplete":"0",'.
						'"order_status":"'.$statusid.'",'.
						'"transport_amount":"'.$total.'",'.
						'"transport_type":"'.$transport_th_name.'",'.
						'"transport_address":"'.$shipping_address.'",'.
						'"remark":""'.
						'}';
		}
		$stmt->close();
	}
	echo '['.$result.']';
	$con->close();
?>