<?php
	include '../../database.php';
	include './function.php';
	
	if (isset($_POST['tid'])) {
		$tid = $_POST['tid'];

		//check topup
		$topup = getTopup($con,$tid);
		if (empty($topup)) {
			echo 'Topup non found';
			return;
		}
		if ($topup[0]['topup_status']!=2) {
			echo 'This topup status is not canceled'; 
			return;
		}

		//update topup
		$sql = 'UPDATE customer_request_topup SET topup_status=0 WHERE topup_id="'.$tid.'"';
		$stmt = $con->prepare($sql);
		$res = $stmt->execute();

		//1 get SID cancel
		$sid = getSIDCancel($con,$tid,$topup[0]['topup_number']);
		deleteCancelStatement($con,$sid);

		//2 insert order payment statement
		$_statement = getStatementTmp($tid);
		if (!empty($_statement)) {
			$sql = 'INSERT INTO customer_statement_tmp (statement_name,statement_date,credit,order_id,topup_id,customer_id) VALUES (?,?,?,?,0,?)';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ssdis',$_statement[0]['statement_name'],$_statement[0]['statement_date'],$_statement[0]['credit'],$_statement[0]['order_id'],$_statement[0]['customer_id']);		//***tid is mock (this should be 0)
			$stmt->execute();
		}

		//3 update statement from confirmed to waiting
		$sid = getSIDConfirmed($con,$tid,$topup[0]['topup_number']);
		$statement = 'เติมเงิน '.$topup[0]['topup_number'].' - รอตรวจสอบ';
		$sql = 'UPDATE customer_statement SET statement_name=? WHERE statement_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('si',$statement,$sid);
		$stmt->execute();

		//insert statement
		$tno = $topup[0]['topup_number'];
		$oid = $topup[0]['customer_id'];
		$amount = $topup[0]['topup_amount'];
		$statement = 'เติมเงิน '.$tno.' - ตรวจสอบแล้ว (ย้อนสถานะ)';
		$sql = 'INSERT INTO customer_statement (statement_name,statement_date,credit,topup_id,customer_id) VALUES (?,now(),?,?,?)';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('sdii',$statement,$amount,$tid,$cid);
		$stmt->execute();
		echo 'ย้อนกลับสถานะ Topup เลขที่ '.$tno.' เรียบร้อยแล้ว';
	}
	else {
		echo 'non topup id have been sent';
	}
	$con->close();
?>