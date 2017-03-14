<?php
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

		function getSumAmount($con,$oid) {
				$result = 0;
				$sql = 'SELECT SUM(amount) FROM customer_order_product_tracking WHERE order_id=?';
				if ($stmt = $con->prepare($sql)) {
						$stmt->bind_param('i',$oid);
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

		function getSumPrice($con,$oid) {
				$result = 0;
				$sql = 'SELECT SUM(amount*backshop_price) FROM customer_order_product_tracking WHERE order_id=?';
				if ($stmt = $con->prepare($sql)) {
						$stmt->bind_param('i',$oid);
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
?>