<?php
		session_start();
		if(!isset($_SESSION['ID'])){
				header("Location: ../login.php");
		}

		function sendEmail($ono,$cmail,$cname,$refnd) {
			$strTo = $cmail;
			$strSubject = '=?UTF-8?B?'.base64_encode('รายการสั่งซื้อหมายเลข '.$ono.' ของท่านได้ตรวจสอบเสร็จแล้ว').'?=';
			$strHeader = "MIME-Version: 1.0\' . \r\n";
			$strHeader .= "Content-type: text/html; charset=utf-8\r\n";
			$strHeader .= "From: support@order2easy.com";
			$strMessage = "สวัสดีค่ะ คุณ ".$cname."<br><br>".				
			"&nbsp;&nbsp;&nbsp;รายการสั่งซื้อ ".$ono." ของท่านได้ตรวจสอบเสร็จเรียบร้อยแล้วนะคะ<br>".
			"<br>มีการคืนเงินจากสินค้าที่ได้รับไม่ครบ เป็นจำนวน ".$refnd." บาท".
			"<br>โปรดตรวจสอบรายละเอียดรายการคืนเงินอีกครั้ง".
			"<br>สอบถามโทร 02-924-5850".			
			"<br><br>order2easy".
			"<br>เจ้าหน้าที่ผู้ตรวจสอบรายการ: ".$_SESSION['ID'].
			"<br>".date('Y-m-d H:i:s');

			@mail($strTo,$strSubject,$strMessage,$strHeader);
		}

		include '../database.php';
		$data = json_decode($_POST['data'],true);

		//update order complete status
		$sql = 'UPDATE customer_order SET complete_status=?,remark=?,order_completed_date=now(),total_return=? WHERE order_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('isdi',$data['stat'],$data['rmk'],$data['refnd'],$data['oid']);
		$res = $stmt->execute();
		if(!$res) {
			echo $con->error;
		}

		//update current_amount, tm3 and tweight
		$stmt = $con->prepare('UPDATE customer SET current_amount=current_amount+?,total_m3=?,total_kg=? WHERE customer_id=?');
		$stmt->bind_param('dddi',$data['refnd'],$data['tm3'],$data['tweight'],$data['cid']);
		$res = $stmt->execute();
		if(!$res) {
			echo $con->error;
		}

		//insert statement
		$statement_name = 'โอนคืนลูกค้า Order เลขที่ '.$data['ono'];
		$debit = 0;
		$stmt = $con->prepare('INSERT into customer_statement (customer_id,statement_name,statement_date,debit,credit,order_id) VALUES (?,?,now(),?,?,?)');
		$stmt->bind_param('ssssi',$data['cid'],$statement_name,$debit,$data['refnd'],$data['oid']);
		$res = $stmt->execute();
		if(!$res) {
			echo $con->error;
		}

		//send email
		sendEmail($data['ono'],$data['cmail'],$data['cname'],$data['refnd']);

		echo 'success';

?>