<html>
<head>
        <meta charset="UTF-8">
        <style>
				button {
						width: 30%;
						position: absolute;
				}
				button:hover {
						position: relative;
						color: red;
				}
		</style>
</head>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<?php
		include 'database.php';
		function getOrderInfo($tid,$cid){
				include 'database.php';
				$orderInfo = new stdClass();
				$orderInfo->oid = '';
				$orderInfo->dt = '';
				$orderInfo->price = '';

				$sql = 'SELECT order_id from payment_detail WHERE topup_id=\''.$tid.'\' and customer_id='.$cid;
				$stmt = $con->prepare($sql);
				$stmt->bind_result($oid);
				$res = $stmt->execute();
				while($stmt->fetch()){
						$orderInfo->oid = $oid;
				}
				
				$sql = 'SELECT date_order_created,order_price from customer_order WHERE order_id='.$orderInfo->oid;
				$stmt = $con->prepare($sql);
				$stmt->bind_result($dt,$price);
				$stmt->execute();
				while($stmt->fetch()){
						$orderInfo->dt = $dt;
						$orderInfo->price = $price;
				}
		return $orderInfo;
		}

		function getTopupID($cid,$oid) {
			include 'database.php';
			$tid = '';
			$sql = 'SELECT b.topup_id FROM payment a,payment_detail b WHERE a.customer_id = b.customer_id AND a.order_id = b.order_id AND a.customer_id = '.$cid.' AND a.order_id ='.$oid.' and a.payment_type = 1';
			echo $sql;
			$stmt = $con->prepare($sql);
			$stmt->bind_result($tid);
			$stmt->execute();
			while($stmt->fetch()){
					$tid = $tid;
			}
			return $tid;
		}

		function findStatement($cid,$oid) {
			include 'database.php';
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

		function updateOrderStat($con,$oid) {
				$sql = 'UPDATE customer_order SET order_status_code=0 WHERE order_id=?';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('i',$oid);
				$res = $stmt->execute();
		}

		function getCustomerInfo($con,$oid) {
			$dataSet = array();
			$sql = 'SELECT * from customer c JOIN customer_order o ON c.customer_id=o.customer_id WHERE o.order_id='.$oid;
			$queryResult = $con->query($sql); 
			if (!$queryResult) {
					echo ("Error while getting customer data ".$con->error);
					return;
			} 
			while ($row = $queryResult->fetch_assoc()) {
					$dataSet[] = $row; 
			}
			return $dataSet;
		}

		function getptid($con,$oid,$opid) {
			$ptid = '';
			$sql = 'SELECT producttypeid from customer_order_product WHERE order_id=? AND order_product_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ii',$oid,$opid);
			$res = $stmt->execute();
			if (!$res) echo $stmt->error;
			$stmt->bind_result($ptid);
			while ($stmt->fetch()) {
					$ptid = $ptid;
			}

			return $ptid;
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

		function getCustomerCode($con,$cid) {
			$sql = 'SELECT customer_code FROM customer WHERE customer_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$cid);
			$stmt->bind_result($ccode);
			$res = $stmt->execute();
			while ($stmt->fetch()) {
					$ccode = $ccode;
			}
			return $ccode;
		}

		function getGet($con,$opid) {
				$stmt = $con->prepare('SELECT topup_id,return_no,total_baht FROM customer_order_return WHERE order_product_id=?');
				$stmt->bind_param('i',$opid);
				$stmt->bind_result($tid,$rno,$total);
				$res = $stmt->execute();
				while ($stmt->fetch()) {
						$tid = $tid;
						$rno = $rno;
						$total = $total;
				}

				return $tid;
		}

		function getLink($opid) {
			include 'database.php';
			$sql = 'SELECT product_img FROM product p JOIN customer_order_product op ON p.product_id=op.product_id WHERE op.order_product_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$opid);
			$stmt->bind_result($link);
			$stmt->execute();
			while ($stmt->fetch()) {
					echo $link;
					$link = $link;
			}
			$con->close();
			return $link;
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

		function findStatementID($con,$oid,$ono) {
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

		function getTrackingInfo ($con,$tid) {
				if (!isset($tid)) return;
				
				$dataSet = array();
				$sql = 'SELECT order_id, order_product_id FROM customer_order_product_tracking WHERE order_product_tracking_id='.$tid;
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

		function getRate($con,$classId,$rType,$pType) {
			$rate=0;
			$sql = 'SELECT rate_amount FROM customer_class_rate WHERE class_id=? AND rate_type=? AND product_type=?';
			
			if ($stmt = $con->prepare($sql)) {
					$stmt->bind_param('iii',$classId,$rType,$pType);
					$stmt->bind_result($rate);
					$stmt->execute();
					while ($stmt->fetch()) {
							echo $rate;
					}
			}
			else {
					echo $con->error;
			}
			return $rate;
		}

		function getMasterFlg($con,$trn,$oid) {
			$result = 0;
			$sql = 'SELECT COUNT(*) FROM customer_order_product_tracking'.
				' WHERE order_id='.$oid.' AND tracking_no=\''.$trn.'\' AND masterflg=1';
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($count);
			while($stmt->fetch()) {
					$result = $count;
			}
			echo $result;
			if ($result==0) $result=1;
			return $result;
		}

		function isDupPaymore($con,$opid) {
			$result = 0;
			$sql = 'SELECT COUNT(*) FROM customer_order_paymore'.
				' WHERE order_product_id='.$opid;
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($count);
			while($stmt->fetch()) {
					$result = $count;
			}
			if ($result==0) $result=1;
			return $result;
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

		function genWithdrawNumber($con) {
				$i = 0;
				$newTT = '0000001';
				$stmt = $con->prepare ( 'SELECT withdraw_number FROM customer_request_withdraw ORDER BY withdraw_number DESC LIMIT 1' );
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
					$yearBase = substr ( $r, 1, 2 );
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
				return 'R' . $year . $newTT;
		}

		function countTracking($tracking) {
			$result = 0;

			if (empty($tracking)) return $result;

			//return count(explode(',' , $tracking));
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

		// $result = array(
		// 		'data' => array(1,2,3,4),
		// 		'error' => 'test'
		// );

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

		function getTopup($con,$tid) {
			$_topup = array();
			$sql = 'SELECT * FROM customer_request_topup WHERE topup_id='.$tid;
			$result = mysqli_query($con,$sql);
			while ($row = mysqli_fetch_assoc($result)) {
					$_topup[] = $row;
			}
			$con->close();
			return $_topup;
		}

		function isDupProdTypeName($producttypename) {
			include './database.php';
			$result = 0;

			$sql = 'SELECT producttypeid FROM product_type'.
						' WHERE producttypename =\''.$producttypename.'\'';
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($producttypeid);
			while($stmt->fetch()) {
					$result = ++$result;
			}
			$con->close();	//beware close connection in the wrong level
			return $result;
		}

		function getPaymentDetail($con,$tid) {
			$_paymentDetail = array();
			$sql = 'SELECT * FROM payment_detail WHERE topup_id='.$tid.' AND flag_completed=1';
			$result = mysqli_query($con,$sql);
			while ($row = mysqli_fetch_assoc($result)) {
					$_paymentDetail[] = $row;
			}
			return $_paymentDetail;
		}

		function getTopupDT($con,$tid) {
			$dt = '0000-00-00 00:00:00';		
			if ($stmt = $con->prepare('SELECT topup_date FROM customer_request_topup WHERE topup_id='.$tid)) {
				$stmt->execute();
				$stmt->bind_result($dt);
				while($stmt->fetch()) {
						$dt = $dt;
				}
			}
			else {
				echo ("Error while getting topup status ".$con->error);
			}
			return $dt;
		}

		function deleteOrderStatement($con,$oid) {
			$sql = 'DELETE FROM customer_statement WHERE order_id='.$oid.' AND statement_name LIKE \'%ชำระค่าสินค้า%\'';
			$stmt = $con->prepare($sql);
			$stmt->execute();
		}

		function getSIDCancel($con,$tid,$tno) {
			$sid=0;

			$sql = 'SELECT statement_id FROM customer_statement WHERE topup_id=? AND statement_name LIKE \'%เติมเงิน '.$tno.' - ยกเลิก%\'';
			
			$stmt = $con->prepare($sql);
			echo $sid;
			$stmt->bind_param('i',$tid);
			$stmt->bind_result($sid);
			$stmt->execute();
			while ($stmt->fetch()) {
				$sid = $sid;
			}
			
			return $sid;
		}

		echo getSIDCancel($con,66,'A16082900002');
?>
</html>

<script type="text/javascript">
		// $('form').submit( function(event) {
		// 	console.log(1000);
		//     var formId = this.id,
		//         form = this;

		//     event.preventDefault();
		//     $('#ref-submit').prop('disabled', true);
		//     setTimeout( function () { 
		//         form.submit();
		//     }, 5000);
		// });

		$('#ref-submit').click(function() {
			console.log('50');
		    
		     // $('input[type="text"]').keyup(function() {
		     //    if($(this).val() != '') {
		     //       $('input[type="submit"]').prop('disabled', false);
		     //    }
		     // })
		 });
</script>