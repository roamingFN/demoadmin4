<?php
		function getTopupUsed($con,$tid) {

				if($stmt = $con->prepare('SELECT used FROM customer_request_topup WHERE topup_id='.$tid)) {
						$stmt->execute();
						$stmt->bind_result($used);
						while($stmt->fetch()){
								$used = $used;
						}
				}
				else {
						echo ("Error while getting topup used ".$con->error);
				}
				return $used;
		}

		function getTopupStat($con,$tid) {

				if($stmt = $con->prepare('SELECT topup_status FROM customer_request_topup WHERE topup_id='.$tid)) {
						$stmt->execute();
						$stmt->bind_result($stat);
						while($stmt->fetch()){
								$stat = $stat;
						}
				}
				else {
						echo ("Error while getting topup status ".$con->error);
				}
				return $stat;
		}

		function getArrOid($con,$tid) {
				$arrOid = array();

				if($stmt = $con->prepare('SELECT order_id FROM payment_detail WHERE topup_id='.$tid)) {
						$stmt->execute();
						$stmt->bind_result($oid);
						while($stmt->fetch()){
								array_push($arrOid,$oid);
						}
				}
				else {
						echo ("Error while getting topup order id ".$con->error);
				}
				
				return $arrOid;
		}

		function getArrTid($con,$oid) {
				$arrTid = array();

				if($stmt = $con->prepare('SELECT topup_id,before_paying_amount-after_paying_amount FROM payment_detail WHERE order_id='.$oid)) {
						$stmt->execute();
						$stmt->bind_result($tid,$amount);
						while($stmt->fetch()){
								$arrTid[$tid] = $amount;
						}
				}
				else {
						echo ("Error while getting topup id ".$con->error);
				}
				return $arrTid;
		}

		function findPayment($con,$oid) {
				$found = false;
				if($stmt = $con->prepare('SELECT payment_request_id FROM customer_request_payment WHERE order_id='.$oid.' AND payment_request_status=1')) {
						$stmt->execute();
						$stmt->bind_result($id);
						while($stmt->fetch()){
								$found = true;
						}
				}
				else {
						echo "Error while getting payment ".$con->error;
				}
				return $found;
		}

		function updatePaymentStat($con,$oid) {
				$sql = 'UPDATE customer_request_payment SET payment_request_status=0 WHERE order_id=? AND payment_request_status=1';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('i',$oid);
				$res = $stmt->execute();
		}

		function updateOrderStat($con,$oid) {
				$sql = 'UPDATE customer_order SET order_status_code=1 WHERE order_id=?';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('i',$oid);
				$res = $stmt->execute();
		}

		function updateStatement($con,$oid,$tid,$amount,$tno,$cid) {
				$statement_name = 'เติมเงิน '.$tno.' - ตรวจสอบแล้ว';
				$sql = 'UPDATE customer_statement SET statement_name=?,order_id=? WHERE topup_id=?';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('sii',$statement_name,$oid,$tid);
				$stmt->execute();

				$dt = getTopupDT($con,$tid);
				$statement_name = 'เติมเงิน '.$tno.' - ยกเลิก';
				$sql = 'INSERT INTO customer_statement (statement_name,statement_date,credit,order_id,topup_id,customer_id) VALUES (?,?,?,?,?,?)';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('ssdiis',$statement_name,$dt,$amount,$oid,$tid,$cid);
				$stmt->execute();
		}

		
		function getTopupDT($con,$tid) {
			$dt = '0000-00-00 00:00:00';
			if($stmt = $con->prepare('SELECT topup_date FROM customer_request_topup WHERE topup_id='.$tid)) {
				$stmt->execute();
				$stmt->bind_result($dt);
				while($stmt->fetch()){
					$dt = $dt;
				}
			}
			else {
				echo ("Error while getting topup status ".$con->error);
			}
			return $dt;
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

		function getTopup($con,$tid) {
			$_topup = array();
			$sql = 'SELECT * FROM customer_request_topup WHERE topup_id='.$tid;
			$result = mysqli_query($con,$sql);
			while ($row = mysqli_fetch_assoc($result)) {
					$_topup[] = $row;
			}
			return $_topup;
		}

		function deleteOrderStatement($con,$oid,$tid) {
			//get statement
			$_statement = getStatementPaid($oid);
			if (empty($_statement)) {
				echo 'ไม่พบ statement การชำระของ order เลขที่ '.$oid.' กรุณาติดต่อผู้ดูแลระบบ';
				return;
			}

			//insert into tmp
			$sql = 'INSERT INTO customer_statement_tmp (statement_name,statement_date,credit,order_id,topup_id,customer_id) VALUES (?,?,?,?,?,?)';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ssdiis',$_statement[0]['statement_name'],$_statement[0]['statement_date'],$_statement[0]['credit'],$_statement[0]['order_id'],$tid,$_statement[0]['customer_id']);		//***tid is mock (this should be 0)
			$stmt->execute();

			//delete statement
			$sql = 'DELETE FROM customer_statement WHERE statement_id='.$_statement[0]['statement_id'];
			$stmt = $con->prepare($sql);
			$stmt->execute();
		}

		function getStatementPaid($oid) {
			include '../database.php';
			$_statement = array();
			$sql = 'SELECT * FROM customer_statement WHERE order_id='.$oid.' AND statement_name LIKE \'%ชำระค่าสินค้า%\'';
			$result = mysqli_query($con,$sql);
			while ($row = mysqli_fetch_assoc($result)) {
				$_statement[] = $row;
			}
			$con->close();
			return $_statement;
		}

		function getStatementTmp($tid) {
			include '../../database.php';
			$_statement = array();
			$sql = 'SELECT * FROM customer_statement_tmp WHERE topup_id='.$tid;
			$result = mysqli_query($con,$sql);
			while ($row = mysqli_fetch_assoc($result)) {
				$_statement[] = $row;
			}
			$con->close();
			return $_statement;
		}

		function getSIDCancel($con,$tid,$tno) {
			$sid=0;
			$sql = 'SELECT statement_id FROM customer_statement WHERE topup_id=? AND statement_name LIKE \'%เติมเงิน '.$tno.' - ยกเลิก%\'';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$tid);
			$stmt->bind_result($sid);
			$stmt->execute();
			while ($stmt->fetch()) {
					$sid = $sid;
			}
			return $sid;
		}

		function getSIDConfirmed($con,$tid,$tno) {
			$sid=0;
			$sql = 'SELECT statement_id FROM customer_statement WHERE topup_id=? AND statement_name LIKE \'%เติมเงิน '.$tno.' - ตรวจสอบแล้ว%\'';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$tid);
			$stmt->bind_result($sid);
			$stmt->execute();
			while ($stmt->fetch()) {
					$sid = $sid;
			}
			return $sid;
		}

		function deleteCancelStatement($con,$sid) {
			if (!empty($sid)) {
				$sql = 'DELETE FROM customer_statement WHERE statement_id='.$sid;
				$stmt = $con->prepare($sql);
				$stmt->execute();
			}
		}
?>