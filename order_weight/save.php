<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	include './utility/function.php';
	
	function getRate($weight,$ptid,$cid) {
		include '../database.php';
		$class = getClassId($cid);
		$rate = 1;
		$re = getRateType($ptid);
		$pt = getProType($ptid);
		$sql='SELECT rate_amount FROM customer_class_rate WHERE class_id='.$class.' AND rate_type='.$re.' AND product_type='.$pt.' AND (begincal<='.$weight.' AND endcal>='.$weight.')';
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($r);
		while($stmt->fetch()) {
				$rate = $r;
		}
		return $rate;

	}

	function getClassId($cid) {
		include '../database.php';
		$class = 1;
		$sql = 'SELECT class_id FROM customer WHERE customer_id='.$cid;
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($c);
		while($stmt->fetch()) {
				$class = $c;
		}
		return $class;
	}

	function getRateType($ptid) {
		include '../database.php';
		$result = 1;
		$sql = 'SELECT rate_type FROM product_type WHERE producttypeid='.$ptid;
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($r);
		while($stmt->fetch()) {
				$result = $r;
		}
		return $result;
	}

	function getProType($ptid) {
		include '../database.php';
		$result = 1;
		$sql = 'SELECT product_type FROM product_type WHERE producttypeid='.$ptid;
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($p);
		while($stmt->fetch()) {
				$result = $p;
		}
		return $result;
	}

	$data = json_decode($_POST['data1'],true);
	$sql = 'UPDATE customer_order_product_tracking'. 
		' SET width=?,length=?,height=?,m3=?,weight=?,remark=?,total_in_tracking=?'.
		' WHERE order_product_tracking_id=? AND masterflg=1';
	foreach($data as $key=>$item) {
			$stmt = $con->prepare($sql);
			//$rate = 1;
			//$rate = getRate($item['weight'],$item['ptid'],$item['cid']);
			$trackingNo = getTrackingInfo($con,$key);
			//echo $trackingNo[0]['tracking_no'];
			$totalInTracking = getTotalInTracking($con,$trackingNo[0]['tracking_no']);
			//echo $totalInTracking;
			$stmt->bind_param('dddddsdi',$item['width'],$item['length'],$item['height'],$item['m3'],$item['weight'],$item['remark'],$totalInTracking,$key);
			$res = $stmt->execute();
			if (!$res) {
				echo $con->error;
			}
	}

	$data = json_decode($_POST['data2'],true);
	$sql = 'UPDATE customer_order_product_tracking'. 
		' SET received_amount=received_amount+?, uid=?, last_edit_date=now()'.
		' WHERE order_id=? AND order_product_id=?';
	foreach($data as $key=>$item) {
			$dataSet = getTrackingInfo($con,$key);
			$stmt = $con->prepare($sql);
			$stmt->bind_param('isii',$item['amount'], $item['userAdd'], $dataSet[0]['order_id'],$dataSet[0]['order_product_id']);
			$res = $stmt->execute();
			if (!$res) {
					echo $con->error;
			}
	}

	$sql = 'UPDATE customer_order_product_tracking'. 
		' SET amount=amount+?, backshop_amount=?, uid=?, last_edit_date=now()'.
		' WHERE order_product_tracking_id=?';
	foreach($data as $key=>$item) {
			$dataSet = getTrackingInfo($con,$key);
			$stmt = $con->prepare($sql);
			$stmt->bind_param('iisi',$item['amount'], $item['backshop_amount'], $item['userAdd'], $key);
			$res = $stmt->execute();
			if (!$res) {
					echo $con->error;
			}
	}

	//update total in tracking
	foreach($data as $key=>$item) {						
			updTotalInTracking($con,$key);
	}

	//update customer order
	$sumAmount = getSumAmount($con,$dataSet[0]['order_id']);
	$sumPrice = getSumPrice($con,$dataSet[0]['order_id']);
	$sql = 'UPDATE customer_order
		SET received_amount=?,received_price=?*order_rate
		WHERE order_id=?';
	if ($stmt = $con->prepare($sql)) {
			$stmt->bind_param('idi',$sumAmount,$sumPrice,$dataSet[0]['order_id']);
			$stmt->execute();
	}
	else {
			echo $con->error;
	}

	echo 'success';
	
	$con->close();
?>