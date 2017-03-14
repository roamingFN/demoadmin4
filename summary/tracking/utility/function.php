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

		function 
?>