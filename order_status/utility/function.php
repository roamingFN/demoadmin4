<?php
		function getOrderStat($con,$oid) {
				$stat='';

				if($stmt = $con->prepare('SELECT order_status_code FROM customer_order WHERE order_id='.$oid)) {
						$stmt->execute();
						$stmt->bind_result($stat);
						while($stmt->fetch()){
								$stat = $stat;
						}
				}
				else {
						echo ("Error while getting order status ".$con->error);
				}
				return $stat;
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

		function getOrderNumber($con,$oid) {
				$ono='';

				if($stmt = $con->prepare('SELECT order_number FROM customer_order WHERE order_id='.$oid)) {
						$stmt->execute();
						$stmt->bind_result($ono);
						while($stmt->fetch()){
								$ono = $ono;
						}
				}
				else {
						echo ("Error while getting order Number ".$con->error);
				}
				return $ono;
		}

		function getCustomerCode($con,$cid) {
			$sql = 'SELECT customer_code FROM customer WHERE customer_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$cid);
			$stmt->bind_result($ccode);
			$stmt->execute();
			while ($stmt->fetch()) {
					$ccode = $ccode;
			}
			return $ccode;
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

		function sendEmail($ono,$cmail,$cname,$total,$oid,$ccode,$cid,$uid,$con) {
			include '../configPath.php';
			
			$userid = getuserid($con,$uid);

			//set path
			// $DIR = $_SERVER['HTTP_HOST'];
			// $PATH = basename(__DIR__);
			// $FILE = basename(__FILE__);

			// $url = $_SERVER['SERVER_NAME'] . dirname(__FILE__);

			// $array = explode('/',$url); 
			// $count = count($array);
			// $pmain = $array[$count-2];
			
			// $base_dir = __DIR__; // Absolute path to your installation, ex: /var/www/mywebsite
			// $doc_root = preg_replace ( "!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER ['SCRIPT_FILENAME'] ); // ex: /var/www
			// $base_url = preg_replace ( "!^${doc_root}!", '', $base_dir ); // ex: '' or '/mywebsite'
			// $protocol = empty ( $_SERVER ['HTTPS'] ) ? 'http' : 'https';
			// $port = $_SERVER ['SERVER_PORT'];
			// $disp_port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";
			// $domain = $_SERVER ['SERVER_NAME'];

			//$full_url = "${protocol}://${domain}${disp_port}${base_url}";
			$full_url = "http://www.order2easy.com/".$_path_backend."/order_confirm/utility";
			// if ($pmain=='public_html') {
			// 	$actual_link = "http://".$_SERVER[HTTP_HOST]."/".$PATH."/css/images/bank/";
			// }
			// else {
			// 	$actual_link = "http://".$_SERVER[HTTP_HOST]."/".$pmain."/css/images/bank/";
			// }
			//-----------------------------------------------------------------
			$actual_link = "http://www.order2easy.com/".$_path_backend."/css/images/bank/";
			$strTo = $cmail;
			$strSubject = '=?UTF-8?B?'.base64_encode('รายการสั่งซื้อหมายเลข '.$ono.' ของท่านได้ตรวจสอบเสร็จแล้ว').'?=';
			$strHeader = "MIME-Version: 1.0\' . \r\n";
			$strHeader .= "Content-type: text/html; charset=utf-8\r\n";
			$strHeader .= "From: Order2Easy <order2easy_admin@order2easy.com>";
			$strMessage = "สวัสดีค่ะ คุณ ".$cname." (".$ccode.")<br><br>".				
			"<table width='800px'>".
				"<tr>
					<td colspan='5'>รายการสั่งซื้อ <a href='".$_site_url."login.php?returnUrl=".$_site_url."order_show_detail_confirmed.php?order_id=".$oid."'>".$ono."</a> ของท่านได้ตรวจสอบเสร็จเรียบร้อยแล้วนะคะ</td>
				</tr>".
				"<tr>
					<td colspan='5'>ยอดค่าสินค้า = ".number_format($total,2)." บาท ท่านสามารถดูรายละเอียดได้จากหน้ารายการสั่งซื้อของท่าน</td>
				</tr>".
				"<tr>
					<td colspan='5'>หากรายละเอียดรายการสั่งซื้อถูกต้อง โปรดชำระภายใน 7 วัน เพื่อทางเราจะได้ทำการจัดซื้อต่อไปค่ะ</td>
				</tr>".
				"<tr>
					<td colspan='5' height='20px'></td>
				</tr>".
				"<tr>
					<td colspan='5'>และเพื่อความรวดเร็ว หลังจากที่ท่านได้ทำการโอนเงินค่าสินค้าแล้ว กรุณาดำเนินการตามขั้นตอนต่อไปนี้</td>
				</tr>".
				"<tr>
					<td colspan='5'>1. แจ้งการเติมเงิน โดยกด link ตามนี้ <a href='".$_site_url."login.php?returnUrl=".$_site_url."topup'><button>เติมเงิน</button></a></td>
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
					<td colspan='5' height='20px'></td>
				</tr>".
				"</table>".
				"<table>".
				// "<tr width='60px'>
				// 	<td align='center'></td>
				// 	<td><b>ธนาคาร</b></td>
				// 	<td><b>เลขที่บัญชี</b></td>
				// 	<td><b>ชื่อบัญชี</b></td>
				// 	<td><b>สาขา</b></td>
				// </tr>".
				// "<tr>
				// 	<td align='center'><img width='60px' height='30px' src='".$actual_link."kbank.png'></td>
				// 	<td>กสิกร</td>
				// 	<td>007-8-68567-0</td>
				// 	<td>นาย ศิรพัชร ณรงค์วงศ์วัฒนา</td>
				// 	<td>โลตัส บางกะปิ</td>
				// </tr>".
				// "<tr>
				// 	<td align='center'><img width='60px' height='30px' src='".$actual_link."scb.png'></td>
				// 	<td>ไทยพาณิชย์</td>
				// 	<td>281-257812-8</td>
				// 	<td>นาย ศิรพัชร ณรงค์วงศ์วัฒนา</td>
				// 	<td>เดอะมอลล์ บางกะปิ 2</td>
				// </tr>".
				// "<tr>
				// 	<td align='center'><img width='60px' height='30px' src='".$actual_link."bbl.png'></td>
				// 	<td>กรุงเทพ</td>
				// 	<td>234-4-68315-2</td>
				// 	<td>นาย ศิรพัชร ณรงค์วงศ์วัฒนา</td>
				// 	<td>เดอะมอลล์ บางกะปิ</td>
				// </tr>".
				// "<tr>
				// 	<td align='center'><img width='60px' height='30px' src='".$actual_link."bay.png'></td>
				// 	<td>กรุงศรี</td>
				// 	<td>449-1-72094-0</td>
				// 	<td>นาย ศิรพัชร ณรงค์วงศ์วัฒนา</td>
				// 	<td>เดอะมอลล์ บางกะปิ</td>
				// </tr>".
				// "<tr>
				// 	<td align='center'><img width='60px' height='30px' src='".$actual_link."/ktb.png'></td>
				// 	<td>กรุงไทย</td>
				// 	<td>762-0-46752-1</td>
				// 	<td>นาย ศิรพัชร ณรงค์วงศ์วัฒนา</td>
				// 	<td>เดอะมอลล์ บางกะปิ</td>
				// </tr>".
				"<tbody>
					<tr>
						<th style='background-color: #938953;width: 15%;'></th>
						<th style='background-color: #938953;width: 100px; text-align: center;'>ธนาคาร</th>
						<th style='background-color: #938953;width: 150px; text-align: center;'>เลขที่บัญชี</th>
						<th style='background-color: #938953;width: 250px; padding-left: 10px;'>ชื่อบัญชี</th>
						<th style='background-color: #938953;width: 200px; padding-left: 10px;'>สาขา</th>
					</tr>
					<tr>
						<td style='width: 70px; text-align: center;'><img style='width: 60%;' src='".$full_url."/img/kbank_logo.jpg'></td>
						<td style='width: 100px; text-align: center;font-size: 13px;font-family: tahoma;'>กสิกรไทย</td>
						<td style='width: 100px; text-align: center;font-size: 13px;font-family: tahoma;'>007-8-68567-0</td>
						<td style='width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;'>นาย ศิรพัชร
							ณรงค์วงศ์วัฒนา</td>
						<td style='width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;'>โลตัส บางกะปิ</td>
					</tr>
					<tr>
						<td style='width: 70px; text-align: center;'><img style='width: 60%;' src='".$full_url."/img/scb_logo.jpg'></td>
						<td style='width: 100px; text-align: center;font-size: 13px;font-family: tahoma;'>ไทยพาณิชย์</td>
						<td style='width: 100px; text-align: center;font-size: 13px;font-family: tahoma;'>281-257812-8</td>
						<td style='width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;'>นาย ศิรพัชร
							ณรงค์วงศ์วัฒนา</td>
						<td style='width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;'>เดอะมอลล์ บางกะปิ2</td>
					</tr>
					<tr>
						<td style='width: 70px; text-align: center;'><img style='width: 60%;' src='".$full_url."/img/bk_logo.jpg'></td>
						<td style='width: 100px; text-align: center;font-size: 13px;font-family: tahoma;'>กรุงเทพ</td>
						<td style='width: 100px; text-align: center;font-size: 13px;font-family: tahoma;'>234-4-68315-2</td>
						<td style='width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;'>นาย ศิรพัชร
							ณรงค์วงศ์วัฒนา</td>
						<td style='width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;'>เดอะมอลล์ บางกะปิ</td>
					</tr>
					<tr>
						<td style='width: 70px; text-align: center;'><img style='width: 60%;' src='".$full_url."/img/krungsri_logo.jpg'></td>
						<td style='width: 100px; text-align: center;font-size: 13px;font-family: tahoma;'>กรุงศรี</td>
						<td style='width: 100px; text-align: center;font-size: 13px;font-family: tahoma;'>449-1-72094-0</td>
						<td style='width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;'>นาย ศิรพัชร
							ณรงค์วงศ์วัฒนา</td>
						<td style='width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;'>เดอะมอลล์ บางกะปิ</td>
					</tr>
					<tr>
						<td style='width: 70px; text-align: center;'><img style='width: 60%;' src='".$full_url."/img/ktb_logo.jpg'></td>
						<td style='width: 100px; text-align: center;font-size: 13px;font-family: tahoma;'>กรุงไทย</td>
						<td style='width: 100px; text-align: center;font-size: 13px;font-family: tahoma;'>762-0-46752-1</td>
						<td style='width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;'>นาย ศิรพัชร
							ณรงค์วงศ์วัฒนา</td>
						<td style='width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;'>เดอะมอลล์ บางกะปิ</td>
					</tr>
				</tbody>".
			"</table>".
			"<br><br>order2easy".
			"<br>เจ้าหน้าที่ผู้ตรวจสอบรายการ: ".$_SESSION['ID'].
			"<br>".date('d-m-Y H:i:s');

			$rs = mail($strTo,$strSubject,$strMessage,$strHeader);
			if ($rs==false) echo 'Cannot send an Email, Please contact Admin.';

			$subject = 'รายการสั่งซื้อหมายเลข '.$ono.' ของท่านได้ตรวจสอบเสร็จแล้ว';
			
			$sql = 'INSERT INTO total_message_log (order_id,customer_id,user_id,subject,content,message_date,active_link) VALUES (?,?,?,?,?,now(),1)';
			if ($stmt = $con->prepare($sql)) {
					$stmt->bind_param('iisss',$oid,$cid,$userid,$subject,$strMessage);
					$res = $stmt->execute();
			}
			else {
					echo $con->error;
			}
			$con->close();
		}
		
		function getData($con,$sql,$condition,$orderBy,$groupBy,$paging) {
			if(!isset($con)) return;
			if(!isset($sql)) return;
			if(!isset($condition)) $condition = '';
			if(!isset($orderBy)) $orderBy = '';
			if(!isset($groupBy)) $groupBy = '';
			if(!isset($paging)) $paging = '';
			
			//get data
			$dataSet = array();
			$queryResult = $con->query($sql.$condition.$orderBy.$groupBy.$paging); 
			if (!$queryResult) {
					echo ("Error while getting data ".$con->error);
					return;
			} 
			while ($row = $queryResult->fetch_assoc()) {
					$dataSet[] = $row; 
			}
			return $dataSet;
		}

?>