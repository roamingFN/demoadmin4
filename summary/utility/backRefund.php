<?php
		include '../../database.php';
		
		session_start();
		if (!isset($_SESSION['ID'])){
		        header("Location: ../../login.php");
		}

		//check balance
		if (getTopUpBal($_POST['bref-opid'])<$_POST['bref-return_baht']) {
				echo '<script>alert("ไม่สามารถคืนเงินได้อเพราะยอดเงินถูกตัดจ่ายไปแล้ว\n'.$stmt->error.'");';
				echo 'window.location.href="../detail.php?oid='.$_POST['bref-oid'].'";</script>';
		}

		//update customer_order------------------------
		$sql = 'UPDATE customer_order_product SET return_status2=1 WHERE order_product_id='.$_POST['bref-opid'];
		$stmt = $con->prepare($sql);
		$res = $stmt->execute();
		if(!$res) {
				echo '<script>alert("ดึงเงินกลับล้มเหลว\n'.$stmt->error.'");';
				echo 'window.location.href="../detail.php?oid='.$_POST['bref-oid'].'";</script>';
		}

		//update customer_order_return------------------------
		$sql = 'UPDATE customer_order_return SET return_status=1,cancel_date=?,cancel_by=? WHERE order_product_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('sss',date("Y-m-d H:i:s"),$_SESSION['ID'],$_POST['bref-opid']);
		$res = $stmt->execute();
		if(!$res) {
				echo '<script>alert("ดึงเงินกลับล้มเหลว\n'.$stmt->error.'");';
				echo 'window.location.href="../detail.php?oid='.$_POST['bref-oid'].'";</script>';
		}

		//insert customer_statement--------------
		$refundSQL = 'INSERT INTO customer_statement (customer_id,statement_name,statement_date,debit,credit,order_id) VALUES (?,?,?,?,?,?)';
		$credit = $_POST['bref-return_baht'];
		$debit = 0;
		$statement_name = 'ยกเลิกคืนเงิน - เลขที่ '.$_POST['bref-opid'];
		$stmt = $con->prepare($refundSQL);
		$stmt->bind_param('ssssss',$_POST['bref-cid'],$statement_name,date("Y-m-d H:i:s"),$debit,$credit,$_POST['bref-oid']); 
		$res = $stmt->execute();
		if(!$res) {
				echo '<script>alert("ดึงเงินกลับล้มเหลว\n'.$stmt->error.'");';
				echo 'window.location.href="../detail.php?oid='.$_POST['bref-oid'].'";</script>';
		}

		//delete customer statement
		$rid = getRID($_POST['bref-opid']);
		$sql = 'DELETE FROM customer_statement WHERE return_id='.$rid;
		$stmt = $con->prepare($sql); 
		$res = $stmt->execute();
		if(!$res) {
				echo '<script>alert("ดึงเงินกลับล้มเหลว\n'.$stmt->error.'");';
				echo 'window.location.href="../detail.php?oid='.$_POST['bref-oid'].'";</script>';
		}

		//update customer------------------------
		$sql = 'UPDATE customer SET current_amount=current_amount-? WHERE customer_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('ss',$_POST['bref-return_baht'],$_POST['bref-cid']);
		$res = $stmt->execute();
		if(!$res) {
				echo '<script>alert("ดึงเงินกลับล้มเหลว\n'.$stmt->error.'");';
				echo 'window.location.href="../detail.php?oid='.$_POST['bref-oid'].'";</script>';
		}
        else {
          		echo '<script>alert("ดึงเงินกลับสำเร็จ");';
          		echo 'window.location.href="../detail.php?oid='.$_POST['bref-oid'].'";</script>';
            		
        }

function getRID($opid) {
		include '../../database.php';
		$rid = '';
		$sql = 'SELECT running FROM customer_order_return WHERE order_product_id='.$opid;
		$stmt = $con->prepare($sql);
		$stmt->bind_result($rid);
		$stmt->execute();
		while($stmt->fetch()) {
				$rid = $rid;
		}
		$con->close();
		return $rid;
}

function getTopUpBal($opid) {
		$bal = 0;
		include '../../database.php';
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
		$con->close();
		return $bal;
}
?>