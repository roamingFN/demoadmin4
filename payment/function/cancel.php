<?php
	include '../../database.php';
	include './utility.php';

	if (isset($_POST['cid'])) {
		$cid = $_POST['cid'];

		//Validate------------------------------------------------------------------------
		//find top up id
		$tid = getTopupID($con,$cid);
		if ($tid==0) {
			echo 'ไม่พบ Topup ID จาก Cash ID : '.$cid.' กรุฯาติดต่อผู้ดูแลระบบ'; 
			return; 
		}

		//find payment detail
		$oid = 0;
		$payment = getPaymentDetail($tid);
		
		//if this topup has payment, assign order id
		if (!empty($payment)) {
			$oid = $payment[0]['order_id'];

			//check existed statement payment
			$sidPayment = getSidPayment($con,$oid);
			if ($sidPayment==0) {
				echo 'ไม่พบ Statement การชำระเงินเลขที่ : '.$oid.' กรุฯาติดต่อผู้ดูแลระบบ'; 
				return;
			}
		}
		
		//check existed statement topup
		$sidTopup = getSidTopup($con,$tid);
		if ($sidTopup==0) {
			echo 'ไม่พบ Statement การเติมเงินเลขที่ : '.$tid.' กรุฯาติดต่อผู้ดูแลระบบ'; 
			return;
		}

		//Update-----------------------------------------------------------------------
		//update topup
		$sql = 'UPDATE customer_request_topup SET topup_status=0 WHERE topup_id="'.$tid.'"';
		$stmt = $con->prepare($sql);
		$res = $stmt->execute();

		//update cash
		$sql = 'UPDATE cash SET status=0,topup_id=0 WHERE cashid="'.$cid.'"';
		$stmt = $con->prepare($sql);
		$res = $stmt->execute();

		//in case of topup for balance-------------------------------
		if (empty($payment)) {
			//find and update statement topup
			//get topup number
			$tno = getTopupNo($con,$tid);

			//update statement topup
			$statement = 'เติมเงิน '.$tno.' (รอตรวจสอบ)';
			$sql = 'UPDATE customer_statement SET statement_name=\''.$statement.'\' WHERE topup_id='.$tid.' AND statement_id='.$sidTopup;
			$stmt = $con->prepare($sql);
			$res = $stmt->execute();
		}
		//incase of topup for pay order--------------------------------
		else {
			//update order status
			$sql = 'UPDATE customer_order SET order_status_code=2 WHERE order_id='.$oid;
			$stmt = $con->prepare($sql);
			$res = $stmt->execute();

			//find and update statement topup
			//get topup number
			$tno = getTopupNo($con,$tid);
			$statement = 'เติมเงิน '.$tno.' (รอตรวจสอบ)';
			$sql = 'UPDATE customer_statement SET statement_name=\''.$statement.'\' WHERE topup_id='.$tid.' AND statement_id='.$sidTopup;
			$stmt = $con->prepare($sql);
			$res = $stmt->execute();

			//find and update statement order id
			$tno = getOrderNo($con,$oid);
			$statement = 'ชำระค่าสินค้า เลขที่สั่งซื้อ '.$tno.' (รอตรวจสอบยอดเงินที่โอนเข้า)';
			$sql = 'UPDATE customer_statement SET statement_name=\''.$statement.'\' WHERE order_id='.$oid.' AND statement_id='.$sidPayment;
			$stmt = $con->prepare($sql);
			$res = $stmt->execute();
		}
		echo 'ยกเลิก Payment เรียบร้อยแล้ว';
	}
	else {
		echo 'non cash id have been sent';
	}
	$con->close();
?>