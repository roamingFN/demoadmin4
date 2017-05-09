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

		function getCustomerCode($con,$cid) {
			$sql = 'SELECT customer_code FROM customer WHERE customer_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$cid);
			$stmt->bind_result($ccode);
			$stmt->execute();
			while ($stmt->fetch()) {
					$ccode = $ccode;
			}
			return $ccode;
		}

		function genCRN($con) {
			$i = 0;
			$newTT = '0000001';
			$stmt = $con->prepare ( 'SELECT return_no FROM customer_order_return_summary ORDER BY return_no DESC LIMIT 1' );
			$stmt->execute ();
			$result = $stmt->get_result ();
			while ( $row = $result->fetch_array ( MYSQLI_NUM ) ) {
				foreach ( $row as $r ) {
					$i += 1;
				}
			}
			// find year
			$year = substr ( ( string ) date ( "Y" ), 2, 2 );
			// cash has records
			if ($i) {
				$tempTT = ( string ) (( int ) substr ( $r, - 7 ) + 1);
				$len = strlen ( $tempTT );
				$yearBase = substr ( $r, 2, 2 );
				if ($year != $yearBase) {
				} else if ($len == 6) {
					$newTT = '0' . $tempTT;
				} else if ($len == 5) {
					$newTT = '00' . $tempTT;
				} else if ($len == 4) {
					$newTT = '000' . $tempTT;
				} else if ($len == 3) {
					$newTT = '0000' . $tempTT;
				} else if ($len == 2) {
					$newTT = '00000' . $tempTT;
				} else if ($len == 1) {
					$newTT = '000000' . $tempTT;
				} else {
					$newTT = $tempTT;
				}
			}
			$stmt->close();
			return 'RT' . $year . $newTT;
		}

		function getTopupID($con,$cid,$oid) {
			$tid = 0;
			$sql = 'SELECT b.topup_id FROM payment a , payment_detail b WHERE a.customer_id = b.customer_id AND a.order_id = b.order_id AND a.customer_id = '.$cid.' and a.order_id ='.$oid.' and a.payment_type = 1';
			$stmt = $con->prepare($sql);
			$stmt->bind_result($tid);
			$stmt->execute();
			while($stmt->fetch()) {
					$tid = $tid;
			}
			$stmt->close();
			return $tid;
		}

		function getuserid($con,$uid){
				$userid = '';
				$sql = 'SELECT userid from user WHERE uid=?';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('s',$uid);
				$stmt->bind_result($userid);
				$stmt->execute();
				while ($stmt->fetch()) {
					$userid = $userid;
			}
				return $userid;
		}

		function checkBal($con,$opid) {
			$bal = 0;
			$sql = 'SELECT topup_id FROM customer_order_return WHERE order_product_id='.$opid;
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($tid);
			while($stmt->fetch()) {
					$tid = $tid;
			}
			
			$sql = 'SELECT usable_amout FROM customer_request_topup WHERE topup_id='.$tid;
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($bal);
			while($stmt->fetch()) {
					$bal = $bal;
			}
			return $bal;
		}
?>