<html>
		<head>
				<meta charset="utf-8">
		</head>

<?php
		include '../../database.php';
		include './function.php';
		session_start();
		if(!isset($_SESSION['ID'])){
			header("Location: ../login.php");
		}
		if (empty($_POST)) return;
		$sql = 'UPDATE customer_order SET order_status_code=99,cancel_date=now(),cancel_by=\''.$_SESSION['ID'].'\' WHERE order_id='.$_POST['c-oid'];
		if ($stmt = $con->prepare($sql)) {
				$res = $stmt->execute ();
				if (! $res) {
					echo '<script>alert("ยกเลิกรายการล้มเหลว\n'.$stmt->error.'");';
					echo 'window.location.href="../index.php";</script>';
				} else {
					$statement_name = 'ค่าสินค้า เลขที่สั่งซื้อ '.$_POST['cc-ono'];
					$sql = 'INSERT INTO customer_statement (statement_name,statement_date,credit,order_id) VALUES (?,?,?,?)';
					if ($stmt = $con->prepare($sql)) {
						$stmt->bind_param('ssdi',$statement_name,$_POST['cc-dt'],$_POST['cc-amount'],$_POST['c-oid']);
						$stmt->execute();
					}
					else {
						echo $con->error;
					}

					$statement_name = 'ลูกค้า ยกเลิก เลขที่สั่งซื้อ '.$_POST['cc-ono'];
					$sql = 'INSERT INTO customer_statement (statement_name,statement_date,debit,order_id) VALUES (?,now(),?,?)';
					$stmt = $con->prepare($sql);
					$stmt->bind_param('sdi',$statement_name,$_POST['cc-amount'],$_POST['c-oid']);
					$stmt->execute();

					//get cid
					$cusInfo = getCustomerInfo($con,$_POST['c-oid']);
					$ono = getOrderNumber($con,$_POST['c-oid']);

					sendEmailCanCel($con,$ono,$cusInfo[0]['customer_email'],$cusInfo[0]['customer_firstname'].' '.$cusInfo[0]['customer_lastname'],$_POST['cc-amount'],$_POST['c-oid'],$_POST['cc-dt'],$cusInfo[0]['customer_code'],$_POST['c-cid'],$_SESSION['ID']);
					echo '<script>alert("ยกเลิกรายการสำเร็จ");';
					echo 'window.location.href="../index.php";</script>';
				}
		}

		function sendEmailCanCel($con,$ono,$cmail,$cname,$total,$oid,$date,$ccode,$cid,$uid) {
				include '../../configPath.php';
				
				$userid = getuserid($con,$uid);

				$strTo = $cmail;
				$strSubject = '=?UTF-8?B?'.base64_encode('เลขที่สั่งซื้อ '.$ono.' ของท่านได้ถูกยกเลิก').'?=';
				$strHeader = "MIME-Version: 1.0\' . \r\n";
				$strHeader .= "Content-type: text/html; charset=utf-8\r\n";
				$strHeader .= "From: support@order2easy.com";
				$strMessage = "สวัสดีค่ะ คุณ ".$cname." (".$ccode.")<br><br>".				
				"<table width='800px'>".
					"<tr>
						<td width='140px'></td>
						<td colspan='4' align='left'>เลขที่สั่งซื้อ <a href='".$_site_url."login.php?returnUrl=".$_site_url."order_show_detail_confirmed.php?order_id=".$oid."'>".$ono."</a> ยอดเงิน ".number_format((float)$total,2)." วันที่สั่งซื้อ ".$date."</td>
					</tr>".
					"<tr>
						<td colspan='5'>ข้อมูลที่ท่านแจ้งไม่ถูกต้อง ทางเราขอยกเลิกรายการนี้ (ถ้ารายการนี้มีการชำระเงินจะถูกยกเลิกโดยอัตโนมัติ)</td>
					</tr>".
					"<tr>
						<td colspan='5' height='20px'></td>
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
						<td colspan='4'>cs@order2easy.com</td>
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
				$head = 'เลขที่สั่งซื้อ '.$ono.' ของท่านได้ถูกยกเลิก';
				//echo 'INSERT INTO confirm_email_log (order_id,subject,content) VALUES ('.$oid.',\''.$head.'\',\''.$strMessage.'\')';
				// $stmt = $con->prepare('INSERT INTO confirm_email_log (order_id,subject,content) VALUES (?,?,?)');
				// $stmt->bind_param('sss',$oid,$head,$strMessage);
				// $res = $stmt->execute();
				$stmt = $con->prepare('INSERT INTO total_message_log (order_id,customer_id,user_id,subject,content,message_date,active_link) VALUES (?,?,?,?,?,now(),1)');
				$stmt->bind_param('iisss',$oid,$cid,$userid,$head,$strMessage);
				$res = $stmt->execute();
			}
?>

</html>