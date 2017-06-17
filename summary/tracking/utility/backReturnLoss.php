<?php

	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
	
	include '../../../database.php';
	include './function.php';

	if (!isset($_POST['data'])) echo null;
	$result = array();
	$data = json_decode($_POST['data']);

	$oid = $_POST['oid'];

	foreach ($data as $opid => $value) {
		//check balance
		if (checkBal($con,$opid)<$value->backReturn) {
				$result['error'] = "ไม่สามารถคืนเงินได้เพราะยอดเงินถูกตัดจ่ายไปแล้ว";
				echo json_encode($result);
				return;
		}

		//update customer_order_return------------------------
		$date = date("Y-m-d H:i:s");
		$stmt = $con->prepare('UPDATE customer_order_return_summary SET return_status=2,cancel_date=?,cancel_by=? WHERE return_type=2 AND order_product_id=?');
		$stmt->bind_param('sss',$date,$_SESSION['ID'],$opid);
		$res = $stmt->execute();

		$sql = 'SELECT running,return_no,total_baht,topup_id FROM customer_order_return_summary WHERE order_product_id='.$opid;
		$stmt = $con->prepare($sql);
		$stmt->bind_result($rid,$rno,$total,$tid);
		$res = $stmt->execute();
		while ($stmt->fetch()) {
				$rid = $rid;
		 		$tid = $tid;
		 		$rno = $rno;
		 		$total = $total;
		}
		
		//update customer request topup---------
	    $stmt = $con->prepare('UPDATE customer_request_topup SET usable_amout=usable_amout-'.$total.' WHERE topup_id='.$tid);
		$res = $stmt->execute();

		//insert customer_statement--------------
		$refundSQL = 'INSERT INTO customer_statement (customer_id,statement_name,statement_date,debit,credit,order_id,topup_id,return_id) VALUES (?,?,?,?,?,?,?,?)';
		$credit = $value->backReturn;
		$debit = 0;
		$statement_name = 'ยกเลิกคืนเงิน - เลขที่ '.$rno;
		$stmt = $con->prepare($refundSQL);
		$stmt->bind_param('ssssssii',$_POST['cid'],$statement_name,$date,$debit,$credit,$_POST['oid'],$tid,$rid); 
		$res = $stmt->execute();

		//update customer_order.flag_return
		// $sql = 'UPDATE customer_order SET summary_return_flag=0 WHERE order_id=?';
	 //    $stmt = $con->prepare($sql);
	 //    $stmt->bind_param('i',$_POST['oid']);
	 //    $stmt->execute();

		//update customer_order.flag_return
		$sql = 'UPDATE customer_order_product SET loss_status=0 WHERE order_product_id=?';
	    $stmt = $con->prepare($sql);
	    $stmt->bind_param('i',$opid);
	    $stmt->execute();

		//update customer------------------------
		$stmt = $con->prepare('UPDATE customer SET current_amount=current_amount-? WHERE customer_id=?');
		$stmt->bind_param('ss',$value->backReturn,$_POST['cid']);
		$res = $stmt->execute();
	}
	
	$con->close();
	echo json_encode($result);
?>