<?php
	function getCusInfo() {
			include '../database.php';

			//get customer info
			$_cus = array();
			$sqlgetCus = 'SELECT customer_id,customer_firstname,customer_lastname,customer_code FROM customer ORDER BY customer_firstname ASC';
			$result = mysqli_query($con,$sqlgetCus);
			while ($row = mysqli_fetch_assoc($result)) {
					$_cus[] = $row;
			}
			$con->close();
			return $_cus;
	}

	function getStatInfo() {
			include '../database.php';

			//get status description
			$_stat = array();
			if($stmt = $con->prepare('SELECT des FROM order_status_code')){
					$stmt->execute();
					$stmt->bind_result($des);
					while($stmt->fetch()){
						array_push($_stat,$des);
					}
			}
			else {
					echo ("Error while getting status description ".$con->error);
			}
			$con->close();
			return $_stat;
	}

	function getPackageStatInfo($con) {
			//get status description
			$_stat = array();
			if($stmt = $con->prepare('SELECT packagestatusid,packagestatusname FROM package_status')){
					$stmt->execute();
					$stmt->bind_result($pid,$pname);
					while($stmt->fetch()){
						$_stat[$pid] = $pname;
					}
			}
			else {
					echo ("Error while getting status description ".$con->error);
			}
			return $_stat;
	}

	function getData($con,$sql,$condition,$orderBy,$groupBy,$paging) {
			if(!isset($con)) return;
			if(!isset($sql)) return;
			if(!isset($condition)) $condition = '';
			if(!isset($orderBy)) $orderBy = '';
			if(!isset($groupBy)) $groupBy = '';
			if(!isset($paging)) $paging = '';
			//echo $sql.$condition.$orderBy.$groupBy.$paging;
			//get data
			$dataSet = array();
			$queryResult = $con->query($sql.$condition.$groupBy.$orderBy.$paging); 
			if (!$queryResult) {
					echo ("Error while getting data ".$con->error);
					return;
			} 
			while ($row = $queryResult->fetch_assoc()) {
					$dataSet[] = $row; 
			}
			return $dataSet;
	}

	function getNumberOfRows($con,$sql,$condition,$groupBy) {
			if(!isset($con)) return;
			if(!isset($sql)) return;
			if(!isset($condition)) $condition = '';
			if(!isset($groupBy)) $groupBy = '';

			$result = 0;
			if ( $stmt = $con->prepare($sql.$condition.$groupBy) ) {
				    $stmt->execute();
					$stmt->bind_result($count);
					while($stmt->fetch()) {
							$result = $count;
					}
			}
			else {
					echo "Error while getting number of rows ".$con->error;
			}
			return $result;
	}

	function getpTypeInfo ($con,$ptypeid) {
			if (!isset($ptypeid)) return;

			$dataSet = array();
			$sql = 'SELECT rate_type,product_type FROM product_type WHERE producttypeid='.$ptypeid;
			$queryResult = $con->query($sql); 
			if (!$queryResult) {
					echo ("Error while getting data ".$con->error);
					return;
			} 
			while ($row = $queryResult->fetch_assoc()) {
					$dataSet[] = $row; 
			}
			return $dataSet;
	}

	function arrangeData($dataSet) {
			$result = array();

			foreach ($dataSet as $key => $value) {
					if (!isset($result[$value['shop_name']])) $result[$value['shop_name']][0] = $value;
					else array_push($result[$value['shop_name']],$value);
			}
			// foreach ($dataSet as $key => $value) {
			// 		if (!isset($result[$value['shop_name']])) $result[$value['shop_name']][$value['order_product_id']] = $value;
			// 		else $result[$value['shop_name']][$value['order_product_id']] = $value;
			// }
			return $result;		
	}

	function rearrangeDataByTracking($dataSet) {
			$result = array();

			foreach ($dataSet as $key => $value) {
					if (!isset($result[$value['tracking_no']])) $result[$value['tracking_no']][0] = $value;
					else array_push($result[$value['tracking_no']],$value);
			}
			// foreach ($dataSet as $key => $value) {
			// 		if (!isset($result[$value['shop_name']])) $result[$value['shop_name']][$value['order_product_id']] = $value;
			// 		else $result[$value['shop_name']][$value['order_product_id']] = $value;
			// }
			return $result;		
	}

	function getRate ($con,$userClass,$rType,$pType) {
			$dataSet = array();
			$sql = 'SELECT * FROM customer_class_rate WHERE class_id='.$userClass.' AND rate_type='.$rType.' AND product_type='.$pType;
			$queryResult = $con->query($sql); 
			if (!$queryResult) {
					echo ("Error while getting data ".$con->error);
					return;
			} 
			while ($row = $queryResult->fetch_assoc()) {
					$dataSet[] = $row; 
			}
			return $dataSet;
	}

	function getProductInfo($con) {
			//get status description
			$_prodcut = array();
			if($stmt = $con->prepare('SELECT producttypeid,producttypename FROM product_type ORDER BY producttypename')){
					$stmt->execute();
					$stmt->bind_result($pid,$pname);
					while($stmt->fetch()){
						$_product[$pid] = $pname;
					}
			}
			else {
					echo ("Error while getting status description ".$con->error);
			}
			return $_product;
	}

	function getLastTrackingUpdateDate($con,$oid) {
		$currentDate = new DateTime();
		$interval = "-";
		$sql = 'SELECT last_edit_date FROM customer_order_product_tracking WHERE order_id='.$oid.' ORDER BY last_edit_date DESC LIMIT 1';
		if($stmt = $con->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($date);
			while($stmt->fetch()) {
				if ($date == '0000-00-00 00:00:00') return '-';
				$date = new DateTime($date);
				$interval = $date->diff($currentDate);
				$interval = $interval->days;
			}
		}
		else {
			echo ("Error while getting last tracking update date ".$con->error);
		}
		return $interval;
	}

	function getCreatedDate($date) {
		$currentDate = new DateTime();
		$interval = "-";
		if ($date == '0000-00-00 00:00:00') return '-';
		$date = new DateTime($date);
		$interval = $date->diff($currentDate);
		$interval = $interval->days;
		return $interval;
	}

	function getProductQuantity($con,$oid) {
		$quan = 0;
		$sql = 'SELECT op.backshop_quantity,op.return_quantity
		FROM customer_order o JOIN customer_order_product op ON o.order_id=op.order_id
		WHERE op.current_status<>98 AND o.order_id='.$oid;
		if($stmt = $con->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($backshop_quantity,$return_quantity);
			while($stmt->fetch()) {
				$quan += ($backshop_quantity-$return_quantity);
			}
		}
		else {
			echo ("Error while getting product quantity ".$con->error);
		}
		return $quan;
	}

	function getProductReceived($con,$oid) {
		$quan = 0;
		$sql = 'SELECT pt.received_amount
		FROM customer_order o JOIN customer_order_product_tracking pt ON o.order_id=pt.order_id
		WHERE o.order_id='.$oid;
		if($stmt = $con->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($received_amount);
			while($stmt->fetch()) {
				$quan += $received_amount;
			}
		}
		else {
			echo ("Error while getting product received ".$con->error);
		}
		return $quan;
	}
?>