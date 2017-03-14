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

	function getData($con,$sql,$condition,$groupBy,$orderBy,$paging) {
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

	function getTrackingInfo ($con,$tid) {
			if (!isset($tid)) return;

			$dataSet = array();
			$sql = 'SELECT order_id, order_product_id, tracking_no FROM customer_order_product_tracking WHERE order_product_tracking_id='.$tid;
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

	function getTotalInTracking($con,$tno) {
				$result = 0;
				$sql = 'SELECT SUM(amount) FROM customer_order_product_tracking WHERE tracking_no=?';
				if ($stmt = $con->prepare($sql)) {
						$stmt->bind_param('s',$tno);
						$stmt->bind_result($sum);
						$stmt->execute();
						while ($stmt->fetch()) {
								$result = $sum;
						}		
				}
				else {
						echo $con->error;
				}
				return $result;
	}

	function updTotalInTracking($con,$tid) {
		$tot = 0;
		$sql = 'UPDATE customer_order_product_tracking'. 
		' SET total_in_tracking=?'.
		' WHERE order_product_tracking_id=? AND masterflg=1';
			$stmt = $con->prepare($sql);
			$trackingNo = getTrackingInfo($con,$tid);
			$tot = getTotalInTracking($con,$trackingNo[0]['tracking_no']);
			$stmt->bind_param('ii',$tot,$tid);
			$res = $stmt->execute();
			if (!$res) {
					echo $con->error;
			}
	}

	function getAmount($con,$opid,$tid,$oid) {
			$sum = 0;
			$sql = 'SELECT amount FROM customer_order_product_tracking WHERE order_product_id=? AND order_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ii',$opid,$oid);
			$stmt->bind_result($amount);
			$stmt->execute();
			while ($stmt->fetch()) {
					$sum+=$amount;
			}
			return $sum;
	}
?>