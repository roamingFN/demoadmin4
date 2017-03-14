
<?php 

function sendMail($param,$path,$con){
$base_dir = __DIR__; // Absolute path to your installation, ex: /var/www/mywebsite
$doc_root = preg_replace ( "!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER ['SCRIPT_FILENAME'] ); // ex: /var/www
$base_url = preg_replace ( "!^${doc_root}!", '', $base_dir ); // ex: '' or '/mywebsite'
$protocol = empty ( $_SERVER ['HTTPS'] ) ? 'http' : 'https';
$port = $_SERVER ['SERVER_PORT'];
$disp_port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";
$domain = $_SERVER ['SERVER_NAME'];
$full_url = "${protocol}://${domain}${disp_port}${base_url}"; # Ex: 'http://example.com', 'https://example.com/mywebsite', etc.
 $mailmsg='<div
		style="width: 600px; background-color: #c4bd97; padding: 25px 30px; font-size: 13px;">
		<div>
			สวัสดีค่ะ คุณ <span>'.$param['customer_fullname'].'</span> (<span>'.$param['customer_code'].'</span>)
		</div>
		<div>
			<p>
				สินค้าของคุณเลขที่ <span><a href="http://www.order2easy.com/'.$path.'/login.php?returnUrl=http://www.order2easy.com/'.$path.'/package_detail.php?packageid='.$param['packageid'].'">...'.$param['packageno'].'..</a></span>
				ได้เดินทางมาถึงไทยแล้ว กรุณากดชำระค่าขนส่งผ่านส่งทางหน้าเวปไซต์</br>เพื่อทางเจ้าหน้าที่จะได้ดำเนินการจัดส่งสินค้าให้ต่อไปค่ะ
			</p>
			<p>ในกรณีที่ลูกค้ามารับสินค้าเอง กรุณานัดหมายล่วงหน้า(ในเวลาทำการ)
				เพี่อทางเจ้าหน้าที่จะได้จัดเตรียมสินค้าล่วงหน้าไว้ให้ท่านค่ะ</p>
		</div>
		<div>
			<div style="display: inline-flex;">
				สอบถามโทร
				<ul class=""
					style="margin: 5px 0; list-style: outside none none; line-height: 25px;">
					<li>02-924-5023</li>
					<li>02-924-5850</li>
					<li>089-052-8899</li>
				</ul>
			</div>
		</div>
		<div>
			<p>
				Email <span class="" sytle="margin-left: 9%;">cs@order2easy.com</span>
			</p>
		</div>
		<div style="font-size: 12px;">
			<table>
				<tbody>
					<tr>
						<th style="background-color: #938953; width: 15%;"></th>
						<th style="background-color: #938953; width: 100px; text-align: center;">ธนาคาร</th>
						<th style="background-color: #938953;width: 150px; text-align: center;">เลขที่บัญชี</th>
						<th style="background-color: #938953;width: 250px; padding-left: 10px;">ชื่อบัญชี</th>
						<th style="background-color: #938953;width: 200px; padding-left: 10px;">สาขา</th>
					</tr>
					<tr>
						<td style="width: 70px; text-align: center;"><img
							style="width: 60%;" src="'.$full_url.'/img/kbank_logo.jpg"></td>
						<td style="width: 100px; text-align: center;font-size: 13px;font-family: tahoma;">กสิกรไทย</td>
						<td style="width: 100px; text-align: center;font-size: 13px;font-family: tahoma;">007-8-68567-0</td>
						<td style="width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;">นาย ศิรพัชร
							ณรงค์วงศ์วัฒนา</td>
						<td style="width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;">โลตัส บางกะปิ</td>
					</tr>
					<tr>
						<td style="width: 70px; text-align: center;"><img
							style="width: 60%;" src="'.$full_url.'/img/scb_logo.jpg"></td>
						<td style="width: 100px; text-align: center;font-size: 13px;font-family: tahoma;">ไทยพาณิชย์</td>
						<td style="width: 100px; text-align: center;font-size: 13px;font-family: tahoma;">281-257812-8</td>
						<td style="width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;">นาย ศิรพัชร
							ณรงค์วงศ์วัฒนา</td>
						<td style="width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;">เดอะมอลล์ บางกะปิ2</td>
					</tr>
					<tr>
						<td style="width: 70px; text-align: center;"><img
							style="width: 60%;" src="'.$full_url.'/img/bk_logo.jpg"></td>
						<td style="width: 100px; text-align: center;font-size: 13px;font-family: tahoma;">กรุงเทพ</td>
						<td style="width: 100px; text-align: center;font-size: 13px;font-family: tahoma;">234-4-68315-2</td>
						<td style="width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;">นาย ศิรพัชร
							ณรงค์วงศ์วัฒนา</td>
						<td style="width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;">เดอะมอลล์ บางกะปิ</td>
					</tr>
					<tr>
						<td style="width: 70px; text-align: center;"><img
							style="width: 60%;" src="'.$full_url.'/img/krungsri_logo.jpg"></td>
						<td style="width: 100px; text-align: center;font-size: 13px;font-family: tahoma;">กรุงศรี</td>
						<td style="width: 100px; text-align: center;font-size: 13px;font-family: tahoma;">449-1-72094-0</td>
						<td style="width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;">นาย ศิรพัชร
							ณรงค์วงศ์วัฒนา</td>
						<td style="width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;">เดอะมอลล์ บางกะปิ</td>
					</tr>
					<tr>
						<td style="width: 70px; text-align: center;"><img
							style="width: 60%;" src="'.$full_url.'/img/ktb_logo.jpg"></td>
						<td style="width: 100px; text-align: center;font-size: 13px;font-family: tahoma;">กรุงไทย</td>
						<td style="width: 100px; text-align: center;font-size: 13px;font-family: tahoma;">762-0-46752-1</td>
						<td style="width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;">นาย ศิรพัชร
							ณรงค์วงศ์วัฒนา</td>
						<td style="width: 250px; padding-left: 10px;font-size: 13px;font-family: tahoma;">เดอะมอลล์ บางกะปิ</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div style="line-height: 28px; margin-top: 20px;">
			<div style="font-size: 13px;font-family: tahoma;">order2easy</div>
			<div style="font-size: 13px;font-family: tahoma;">
				เจ้าหน้าที่ผู้ตรวจสอบรายการ: <span>'.$param['admin'].'</span>
			</div>
			<div style="font-size: 13px;font-family: tahoma;">'.date('Y/m/d h:i:s').'</div>
		</div>
	</div>';
 
 //echo '<a href="http://www.order2easy.com/'.$path.'/login.php?returnUrl=http://www.order2easy.com/'.$path.'/package_detail.php?packageid='.$param['packageid'].'">...'.$param['packageno'].'..</a>';

		$sendto = $param['customer_email'];
		//$sendto = "kukeit.c@gmail.com";
		$headers = "From: support@order2easy.com\r\n";
		$headers .= "X-Mailer: PHP/" . phpversion () . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		$headers .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$subject = $param['subject'];
		$subject = "=?UTF-8?B?" . base64_encode ( $subject ) . "?=";
		//$mailmsg = 'test2';
		
		$sendmail = mail ( $sendto, $subject, $mailmsg, $headers );
		if ($sendmail) {
			$one=1;
			$zero=0;
			$stmt = $con->prepare('INSERT INTO total_message_log (order_id,customer_id,user_id,subject,content,message_date,active_link,packageid) VALUES (?,?,?,?,?,now(),?,?)');
			$stmt->bind_param('iiissii',$zero,$param['customer_id'],$param['user_id'],$param['subject'],$mailmsg,$one,$param['packageid']);
		    $stmt->execute();
			
			
			//echo "ส่งข้อความเรียบร้อยแล้ว";
			return true;
		} else {
			return false;
			//echo "ผิดพลาด, ไม่สามารถส่งข้อความผ่านเว็บไซท์ได้ โปรดใช้วิธีส่งผ่านอีเมลของท่าน";
		}
		
} //end send mail
?>