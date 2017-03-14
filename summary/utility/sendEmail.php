<?php
		session_start();
        if (!isset($_SESSION['ID'])){
            header("Location: ../login.php");
        }

        include '../../database.php';
		$subject = $_POST['email-subject'];
		$content = $_POST['email-content'];
		$content = preg_replace("/[\t]/"," ", $content);
		$content = preg_replace("/[\r\n]/","  ", $content);
		$cid     = $_POST['email-cid'];
		$oid     = $_POST['email-oid'];
		$ono     = $_POST['email-ono'];
		$opid    = $_POST['email-opid'];
		$uid     = $_SESSION['ID'];
		$emailFlag = sendEmail($subject,$content,$cid,$oid,$ono,$opid,$uid);

		if(!$emailFlag) {
				$con->close();
				echo '<script>alert("ส่งอีเมลล์ล้มเหลว");';
				echo 'window.location.href="../detail.php?oid='.$oid.'";</script>';
		}
        else {
	        	$stmt = $con->prepare('UPDATE customer_order_product SET email_no2=email_no2+1 WHERE order_product_id=?');
				$stmt->bind_param('s',$opid);
				$res = $stmt->execute();
				$stmt = $con->prepare('INSERT INTO return_email_log (order_product_id,subject,content,return_type) VALUES (?,?,?,2)');
				$stmt->bind_param('sss',$opid,$subject,$content);
				$res = $stmt->execute();
				$con->close();
	          	echo '<script>alert("ส่งอีเมลล์สำเร็จ");';
	          	echo'window.location.href="../detail.php?oid='.$oid.'";</script>';
        }

function sendEmail($subject,$content,$cid,$oid,$ono,$opid,$uid) {
		$cInfo = getCustomerInfo($cid);			
		$strTo = json_encode($cInfo[0]['customer_email']);
		$strSubject = '=?UTF-8?B?'.base64_encode($subject).'?=';
		$strHeader = "MIME-Version: 1.0\' . \r\n";
		$strHeader .= "Content-type: text/html; charset=utf-8\r\n";
		$strHeader .= "From: support@order2easy.com";
		$strMessage = "สวัสดีค่ะ คุณ ".json_encode($cInfo[0]['customer_firstname'])." ".json_encode($cInfo[0]['customer_lastname'])."<br>".
			"รายการสั่งซื้อ ".$ono."<br>".
			"รายการสินค้า ".$opid."<br>".				
			"<table width='800px'>".
				//header===============================================================================================
				"<tr>
					<td width='140px'></td>
					<td colspan='4' align='left'></td>
				</tr>".
				//detail===============================================================================================
				"<tr>
					<td colspan='5'><pre>".$subject."</pre></td>
				</tr>".
				//trailer==============================================================================================
				"<tr>
					<td>ติดต่อกลับ</td>
					<td colspan='4'></td>
				</tr>".
				"<tr>
					<td>Email</td>
					<td colspan='4'>support@order2easy.com</td>
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
			"<br>เจ้าหน้าที่ผู้ตรวจสอบรายการ: ".$uid.
			"<br>".date('d-m-Y H:i:s');

		$flgSend = @mail($strTo,$strSubject,$strMessage,$strHeader);
		return $flgSend;
}

function getCustomerInfo ($cid) {
		include '../../database.php';
		$sql = 'SELECT * FROM customer WHERE customer_id='.$cid;
		$myArray = array(); 
		$result = $con->query($sql); 
		if (!$result) {
			throw new Exception("Database Error ".$con->error);
		}
		$row = $result->num_rows; 
		while ($row = $result->fetch_assoc()) { 
				$myArray[] = $row; 
		}
		$con->close();
		return $myArray; 
}
?>