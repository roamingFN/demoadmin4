<?php	
	function getOrderPrice($con,$oid) {
			$_pr = '';
			if($stmt = $con->prepare('SELECT order_price FROM customer_order WHERE order_id='.$oid)) {
					$stmt->execute();
					$stmt->bind_result($pr);
					while($stmt->fetch()){
						$_pr = $pr;
					}
			}
			else {
					echo ("Error while getting order price ".$con->error);
			}
			return $_pr;
	}

	function getOrderDt($con,$oid) {
			$_dt = '';
			if($stmt = $con->prepare('SELECT date_order_created FROM customer_order WHERE order_id='.$oid)) {
					$stmt->execute();
					$stmt->bind_result($dt);
					while($stmt->fetch()){
						$_dt = $dt;
					}
			}
			else {
					echo ("Error while getting order status ".$con->error);
			}
			return $_dt;
	}

	function getOrderNo($con,$oid) {
			$_ono = '';
			if($stmt = $con->prepare('SELECT order_number FROM customer_order WHERE order_id='.$oid)) {
					$stmt->execute();
					$stmt->bind_result($ono);
					while($stmt->fetch()){
						$_ono = $ono;
					}
			}
			else {
					echo ("Error while getting order number ".$con->error);
			}
			return $_ono;
	}

	function getPackagePrice($con,$pid) {
			$_pr = '';
			if($stmt = $con->prepare('SELECT amount FROM package WHERE packageid='.$pid)) {
					$stmt->execute();
					$stmt->bind_result($pr);
					while($stmt->fetch()){
						$_pr = $pr;
					}
			}
			else {
					echo ("Error while getting package price ".$con->error);
			}
			return $_pr;
	}

	function getPackageDt($con,$pid) {
			$_dt = '';
			if($stmt = $con->prepare('SELECT createdate FROM package WHERE order_id='.$pid)) {
					$stmt->execute();
					$stmt->bind_result($dt);
					while($stmt->fetch()){
						$_dt = $dt;
					}
			}
			else {
					echo ("Error while getting order status ".$con->error);
			}
			return $_dt;
	}

	function getPackageNo($con,$oid) {
			$_ono = '';
			if($stmt = $con->prepare('SELECT packageno FROM package WHERE packageid='.$oid)) {
					$stmt->execute();
					$stmt->bind_result($ono);
					while($stmt->fetch()){
						$_ono = $ono;
					}
			}
			else {
					echo ("Error while getting order number ".$con->error);
			}
			return $_ono;
	}
		
	function findStatement($cid,$oid) {
			include '../database.php';
			$found = false;
			$sql = 'SELECT * from customer_statement WHERE customer_id=? AND order_id=? AND statement_name LIKE \'%ค่าสินค้า%\'';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ii',$cid,$oid);
			$stmt->execute();
			while ($stmt->fetch()) {
					$found = true;
			}
			$con->close();
			return $found;
	}

	function validTopup($con,$cid,$tid) {
			$result = 0;
			$sql = 'SELECT amount,cash.date,cbid FROM cash WHERE cashid=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$cid);
			$stmt->bind_result($c_amt,$c_dt,$c_bid);
			$res = $stmt->execute();
			while ($stmt->fetch()) {
					$c_amt = $c_amt;
					$c_dt = $c_dt;
					$c_bid = $c_bid;
			}

			$sql = 'SELECT topup_amount,topup_date,topup_bank FROM customer_request_topup WHERE topup_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$tid);
			$stmt->bind_result($t_amt,$t_dt,$t_bid);
			$res = $stmt->execute();
			while ($stmt->fetch()) {
					$t_amt = $t_amt;
					$t_dt = $t_dt;
					$t_bid = $t_bid;
			}

			if ($c_amt!=$t_amt) {
					$result=1; 
					return $result;
			}
			if (!preg_match('.'.$c_dt.'.',$t_dt)) {
					$result=2; 
					return $result;
			}
			if ($c_bid!=$t_bid) {
					$result=3; 
					return $result;
			}
			return $result;
	}

	function findStatementID($con,$oid,$ono) {
			$sid=0;
			$sql = 'SELECT statement_id FROM customer_statement WHERE order_id=? AND statement_name LIKE \'%ชำระค่าสินค้า เลขที่สั่งซื้อ '.$ono.' (รอตรวจสอบยอดเงินที่โอนเข้า)%\'';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$oid);
			$stmt->bind_result($sid);
			$stmt->execute();
			while ($stmt->fetch()) {
					$sid = $sid;
			}
			return $sid;
	}

	function findStatementPackage($con,$pid,$pno) {
			$sid=0;
			$sql = 'SELECT statement_id FROM customer_statement WHERE packageid=? AND statement_name LIKE \'%ค่าขนส่ง%\'';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$pid);
			$stmt->bind_result($sid);
			$stmt->execute();
			while ($stmt->fetch()) {
					$sid = $sid;
			}
			return $sid;
	}

	function checkTopup($con,$tid) {
			$result = 0;
			$sql = 'SELECT topup_id,flag_completed FROM payment_detail WHERE topup_id=? AND flag_completed=1';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$tid);
			$stmt->bind_result($tid,$flg);
			$stmt->execute();
			while ($stmt->fetch()) {
					$result = 1;
					return $result;
			}
			return $result;
	}

	function updateCustomerClass($con,$cid) {
			$sum = 0;
			$sql = 'SELECT sum(before_paying_amount - after_paying_amount) FROM payment_detail 
			WHERE customer_id=? and flag_completed=1';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$cid);
			$stmt->bind_result($sum);
			$stmt->execute();
			while ($stmt->fetch()) {
					$sum = $sum;
			}

			$classID = 0;
			if ($sum<50000) $classID=1;
			else if ($sum>=50000 && $sum<500000) $classID=2;
			else if ($sum>=500000 && $sum<2000000) $classID=3;
			else if ($sum>=2000000) $classID=2;

			$sql = 'UPDATE customer set class_id=? WHERE customer_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ii',$classID,$cid);
			$stmt->execute();
	}

	function getTopupID($con,$cid) {
		$topup = 0;
		if($stmt = $con->prepare('SELECT topup_id FROM cash WHERE cashid='.$cid)) {
				$stmt->execute();
				$stmt->bind_result($topup);
				while($stmt->fetch()) {
						$topup = $topup;
				}
		}
		else {
				echo ("Error while getting topup id ".$con->error);
		}
		return $topup;
	}

	function getTopupNo($con,$tid) {
		$tno = '';

		if($stmt = $con->prepare('SELECT topup_number FROM customer_request_topup WHERE topup_id='.$tid)) {
				$stmt->execute();
				$stmt->bind_result($tno);
				while($stmt->fetch()) {
						$tno = $tno;
				}
		}
		else {
				echo ("Error while getting topup number ".$con->error);
		}
		return $tno;
	}

	function getPaymentDetail($tid) {
		include '../../database.php';
		$_paymentDetail = array();
		$sql = 'SELECT * FROM payment_detail WHERE topup_id='.$tid.' AND flag_completed=1';
		$result = mysqli_query($con,$sql);
		while ($row = mysqli_fetch_assoc($result)) {
				$_paymentDetail[] = $row;
		}
		$con->close();
		return $_paymentDetail;
	}

	function getSidTopup($con,$tid) {
		$sid=0;

		$sql = 'SELECT statement_id FROM customer_statement WHERE topup_id=? AND statement_name LIKE \'%ตรวจสอบแล้ว%\'';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('i',$tid);
		$stmt->bind_result($sid);
		$stmt->execute();
		while ($stmt->fetch()) {
				$sid = $sid;
		}
		return $sid;
	}

	function getSidPayment($con,$oid) {
		$sid=0;

		$sql = 'SELECT statement_id FROM customer_statement WHERE order_id=? AND statement_name LIKE \'%(ตรวจสอบแล้ว)%\'';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('i',$oid);
		$stmt->bind_result($sid);
		$stmt->execute();
		while ($stmt->fetch()) {
				$sid = $sid;
		}
		return $sid;
	}
?>