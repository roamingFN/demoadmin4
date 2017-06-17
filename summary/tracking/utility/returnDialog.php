<?php
		require('../../database.php');

		//update customer order product
		$sql = 'UPDATE customer_order_product SET return_baht2=?,return_status2=2 WHERE order_product_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('di',$_POST['ref-total_baht'],$_POST['ref-opid']);
		$res = $stmt->execute();
		if(!$res) {
				$con->close();
				echo '<script>alert("เพ่ิมข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");';
				echo 'window.location.href="../detail.php?order_id='.$_POST['ref-oid'].'";</script>';
		}

		//update customer request topup---------
        $tid = getTopupID($_POST['ref-cid'],$_POST['ref-oid']);
        $sql = 'UPDATE customer_request_topup SET usable_amout=usable_amout+'.$_POST['ref-total_baht'].' WHERE topup_id='.$tid;
        $stmt = $con->prepare($sql);
		$res = $stmt->execute();
		if(!$res) {
				$con->close();
				echo '<script>alert("เพ่ิมข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");';
				echo 'window.location.href="../detail.php?oid='.$_POST['ref-oid'].'";</script>';
		}

		//insert return-------------------------- 
		$rtno = genRTNO();
		$refundSQL = 'INSERT INTO customer_order_return (return_no, return_date, order_product_id, first_unitquantity, quantity, loss_quantity, unitprice, total_yuan, rate, total_baht, return_status, topup_id, order_id, return_type)'.
		' VALUES (\''.$rtno.'\', now(), '.$_POST['ref-opid'].', '.$_POST['ref-bsQuan'].', '.$_POST['ref-rcQuan'].', '.$_POST['ref-missing'].', '.$_POST['ref-bsPrice'].', '.($_POST['ref-total_baht']/$_POST['ref-rate']).', '.$_POST['ref-rate'].', '.$_POST['ref-total_baht'].', 1, '.$tid.', '.$_POST['ref-oid'].', 2)';
		if($stmt = $con->prepare($refundSQL)) {
			$res = $stmt->execute();
			if(!$res) {
					$con->close();
					echo '<script>alert("เพิ่มข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");';
					echo 'window.location.href="../detail.php?oid='.$_POST['ref-oid'].'";</script>';
			}
			$lastID = $stmt->insert_id;
		}

		//insert customer_statement--------------
		$refundSQL = 'INSERT INTO customer_statement (customer_id,statement_name,statement_date,debit,credit,order_id, return_id) VALUES (?,?,?,?,?,?,?)';
		$credit = 0;
		$statement_name = 'คืนเงิน - เลขที่ '.$rtno;
		$stmt = $con->prepare($refundSQL);
		$stmt->bind_param('sssssss',$_POST['ref-cid'],$statement_name,date("Y-m-d H:i:s"),$_POST['ref-total_baht'],$credit,$_POST['ref-oid'],$lastID); 
		$res = $stmt->execute();
		if(!$res) {
				$con->close();
				echo '<script>alert("เพิ่มข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");';
				echo 'window.location.href="../detail.php?oid='.$_POST['ref-oid'].'";</script>';
		}

		//update customer------------------------
		$stmt = $con->prepare('UPDATE customer SET current_amount=current_amount+? WHERE customer_id=?');
		$stmt->bind_param('ss',$_POST['ref-return_baht'],$_POST['ref-cid']);
		$res = $stmt->execute();
		if(!$res) {
				$con->close();
				echo '<script>alert("เพ่ิมข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");';
				echo 'window.location.href="../detali.php?oid='.$_POST['ref-oid'].'";</script>';
		}
        else {
        		$con->close();
	          	echo '<script>alert("เพิ่มข้อมูลการคืนเงินสำเร็จ");';
	          	echo 'window.location.href="../detail.php?oid='.$_POST['ref-oid'].'";</script>';	
        }

function genRTNO() {
		include '../../database.php';
		$i = 0;
		$newTT = '0000001';
		$stmt = $con->prepare ( 'SELECT return_no FROM customer_order_return ORDER BY return_no DESC LIMIT 1' );
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
		$con->close();
		return 'RT' . $year . $newTT;
}

function getTopupID($cid,$oid) {
		include '../../database.php';
		$tid = '';
		$sql = 'SELECT b.topup_id FROM payment a , payment_detail b WHERE a.customer_id = b.customer_id AND a.order_id = b.order_id AND a.customer_id = '.$cid.' and a.order_id ='.$oid.' and a.payment_type = 1';
		$stmt = $con->prepare($sql);
		$stmt->bind_result($tid);
		$stmt->execute();
		while($stmt->fetch()) {
				$tid = $tid;
		}
		$con->close();
		return $tid;
}
?>