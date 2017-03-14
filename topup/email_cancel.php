<?php
		session_start();
     	if (!isset($_SESSION['ID'])) {
        		header("Location: ../login.php");
        }
        date_default_timezone_set("Asia/Bangkok");

        include '../database.php';
        include './utility/function.php';

        function sendEmail($tno,$cmail,$cname,$total,$tid,$date,$acnum,$ccode,$cid,$uid,$con) {
        	include '../configPath.php';

        	$userid = getuserid($con,$uid);

			$strTo = $cmail;
			$strSubject = '=?UTF-8?B?'.base64_encode('รายการเติมเงินหมายเลข '.$tno.' ของท่านได้ถูกยกเลิก').'?=';
			$strHeader = "MIME-Version: 1.0\' . \r\n";
			$strHeader .= "Content-type: text/html; charset=utf-8\r\n";
			$strHeader .= "From: Order2Easy <order2easy_admin@order2easy.com>";
			$strMessage = "สวัสดีค่ะ คุณ ".$cname." (".$ccode.")<br><br>".				
			"<table width='800px'>".
				"<tr>
					<td colspan='5' align='left'>รายการเติมเงินเลขที่ <a href='".$_site_url."order_show_detail_confirmed.php?order_id=".$tid."'>".$tno."</a> ยอดเงิน ".number_format($total,2)." วันที่โอน ".$date." โดยโอนเข้าบัญชี ".$acnum."</td>
				</tr>".
				"<tr>
					<td colspan='5'>ข้อมูลที่ท่านแจ้งไม่ถูกต้อง ทางเราขอยกเลิกรายการนี้ (ถ้ารายการนี้มีการชำระเงินจะถูกยกเลิกโดยอัตโนมัติ)</td>
				</tr>".
				"<tr>
					<td colspan='5' height='20px'></td>
				</tr>".
				"<tr>
					<td colspan='5' style='color:red;'>***กรุณาระบุยอดเงิน วันที่โอน และธนาคารที่ท่านโอนเข้าให้ถูกต้อง</td>
				</tr>".
				"<tr>
					<td colspan='5' height='20px'></td>
				</tr>".
				"<tr>
					<td colspan='5'>และเพื่อความรวดเร็ว หลังจากที่ท่านได้ทำการโอนเงินค่าสินค้าแล้ว กรุณาดำเนินการตามขั้นตอนต่อไปนี้</td>
				</tr>".
				"<tr>
					<td colspan='5'>1. แจ้งการเติมเงิน โดยกด link ตามนี้ <a href='".$_site_url."topup'><button>เติมเงิน</a></td>
				</tr>".
				"<tr>
					<td colspan='5'>2. กรอกข้อมูลรายละเอียดต่างๆตามทีปรากฏบนหน้าเว็ปไซด์ จากนั้นกดปุ่มตกลง</td>
				</tr>".
				"<tr>
					<td colspan='5'>3. ไปยังหน้ารายการสั่งซื้อ เพื่อกดปุ่มชำระเงิน</td>
				</tr>".
				"<tr>
					<td colspan='5' height='20px'></td>
				</tr>".
				"<tr>
					<td colspan='5'>(หากลูกค้าไม่ชำระภายในเวลาที่กำหนด รายการสั่งซื้อนี้จะถูกยกเลิกโดยอัตโนมัติค่ะ)</td>
				</tr>".
				"<tr>
					<td>สอบถามโทร</td>
					<td colspan='4'>02-924-5023</td>
				</tr>".
				"<tr>
					<td></td>
					<td colspan='4'>02-924-5850</td>
				</tr>".
				"<tr>
					<td></td>
					<td colspan='4'>089-052-8899</td>
				</tr>".
				"<tr>
					<td>Email</td>
					<td colspan='4'>order2easy_admin@order2easy.com</td>
				</tr>".
				"<tr>
					<td>Line</td>
					<td colspan='4'>order2easy</td>
				</tr>".
				"<tr>
					<td colspan='5' height='20px'></td>
				</tr>".
			"</table>".
			"<br>order2easy".
			"<br>เจ้าหน้าที่ผู้ตรวจสอบรายการ: ".$_SESSION['ID'].
			"<br>".date('d-m-Y H:i:s');

			@mail($strTo,$strSubject,$strMessage,$strHeader);

			$subject = 'รายการเติมเงินหมายเลข '.$tno.' ของท่านได้ถูกยกเลิก';
			$stmt = $con->prepare('INSERT INTO total_message_log (topup_id,customer_id,user_id,subject,content,message_date,active_link) VALUES (?,?,?,?,?,now(),1)');
			$stmt->bind_param('iisss',$tid,$cid,$uid,$subject,$strMessage);
			$res = $stmt->execute();
		}

		$data = json_decode($_POST['data'],true);

		//update topup
        if($stmt = $con->prepare('UPDATE customer_request_topup SET emailno=emailno+1,emaildt=now() WHERE topup_id="'.$data['tid'].'"')) {
				$res = $stmt->execute();
				if(!$res) {
						echo $con->error;
				}
		}

		$ccode = getCustomerCode($con,$data['cid']);
		//send email
		sendEmail($data['tno'],$data['cmail'],$data['cname'],$data['total'],$data['tid'],$data['date'],$data['acnum'],$ccode,$data['cid'],$_SESSION['ID'],$con);
	
		echo 'success';

		$con->close();
?>