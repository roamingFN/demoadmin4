<?php
	//echo '<html><head><meta charset="utf-8"></head></html>';
	header('Content-Type: text/html; charset=UTF-8');
	date_default_timezone_set("Asia/Bangkok");

	function updTracking ($oid,$opid,$allTrack,$tracking_curr,$count) {
			$res = insertTracking($oid,$opid,$allTrack);
			$res = delTracking($oid,$opid,$allTrack,$tracking_curr);
			$res = orderTracking($oid,$count);
			return $res;
	}

	function getptid($con,$oid,$opid) {
			$ptid = '';
			$sql = 'SELECT producttypeid from customer_order_product WHERE order_id=? AND order_product_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ii',$oid,$opid);
			$res = $stmt->execute();
			$stmt->bind_result($ptid);
			while ($stmt->fetch()) {
					$ptid = $ptid;
			}

			return $ptid;
	}

	function insertTracking ($oid,$opid,$allTrack) {
			include '../database.php';

			$sql = 'UPDATE customer_order_product SET order_shipping_cn_ref_no=? WHERE order_product_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('si',$allTrack,$opid);
			$res = $stmt->execute();
			if (!$res) {
				return $con->error;
			}

			$ptid = getptid($con,$oid,$opid);
			$sql = 'INSERT INTO customer_order_product_tracking'. 
				' (order_product_id,order_id,tracking_no,producttypeid)'.
				' VALUES (?,?,?,?)';
			$splited_trn = explode(",",$allTrack);
			for ($i=0; $i<count($splited_trn); $i++) {
					if (!empty($opid) && !empty($splited_trn[$i])) {
							if (isNotDup($splited_trn[$i],$oid,$opid)==0) {
									$stmt = $con->prepare($sql);
									$stmt->bind_param('iisi',$opid,$oid,$splited_trn[$i],$ptid);
									$res = $stmt->execute();
							}
					}
					if(!$res){
						return $con->error;
					}
			}
			return $res;
	}

	function isNotDup($trn,$oid,$opid) {
			include '../database.php';
			$result = 0;
			$sql = 'SELECT order_product_tracking_id FROM customer_order_product_tracking'.
				' WHERE order_product_id='.$opid.' AND tracking_no=\''.$trn.'\' AND order_id='.$oid;
			//echo $sql."\r\n";
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($count);
			while($stmt->fetch()) {
					$result = $count;
			}
			$con->close();
			return $result;
	}

	function trckUpdate($con,$data) {
			//insert----------------------------------------------------------------------
			$ptypeInfo = array();
			$rate = 0;
			
			//28/05/2017 Pratchaya Ch. validate tracking if it is alreadys updated then this tracking cannot be deleted
			foreach($data as $key=>$item) {
				if($key!='oid' && $key!='grandTotalTh' && $key!='grandTotalCn' && $key!='totalTaobao' && $key!='totalTracking' && $key!='btTran' && $key!='btAmt' && $key!='unote') {
						//tracking
						$tracking_no = $item['ref'];
						$splited_no = explode(",",$tracking_no);
						$tracking_curr = $item['curr_ref'];
						$splited_curr = explode(",", $tracking_curr);

						//get oid, opid
						$oid = $data['oid'];
						$opid = $key;

						if($tracking_curr==$tracking_no) {
							continue;
						}

						if ($tracking_curr=='') {
							continue;
						}

						for ($i=0; $i<count($splited_curr); $i++) {
								$del = $splited_curr[$i];
								for ($j=0; $j<count($splited_no); $j++) {
									//echo $splited_curr[$i]."-".$splited_no[$j]."\r\n";
										//if same tracking then do not delete
										if ($splited_curr[$i]==$splited_no[$j]) {
											$del = "0";
										}
								}
								
								if ($del!="0") {
									$sql = 'SELECT width,length,height,m3 FROM customer_order_product_tracking WHERE order_id='.$oid.' AND order_product_id='.$opid.' AND tracking_no=\''.$del.'\'';
									$stmt = $con->prepare($sql);
									$res = $stmt->execute();
									$stmt->bind_result($width,$length,$height,$m3);
									while ($stmt->fetch()) {
										if ($width!=0 && $length!=0 && $height!=0 && $m3!=0) {
											echo 'ไม่สามารถทำการลบ Tracking '.$del.' ได้ เนื่องจาก Tracking นี้มีการอัพเดทไปแล้ว';
											return 0;
										}
									}
								}
						}
				}
			}
	
			//add tracking
			foreach($data as $key=>$item) {
					if($key!='oid' && $key!='grandTotalTh' && $key!='grandTotalCn' && $key!='totalTaobao' && $key!='totalTracking' && $key!='btTran' && $key!='btAmt' && $key!='unote') {	
							//check tracking no
							$tracking_no = $item['ref'];
							$splited_trn = explode(",",$tracking_no);
							for ($i=0; $i<count($splited_trn); $i++) {
									if (!empty($key) && !empty($splited_trn[$i])) {
											$trckID = isNotDup($splited_trn[$i],$data['oid'],$key);
											if (empty($trckID)) {
													$masterflg = getMasterFlg($con,$splited_trn[$i],$data['oid']);
													
													$ptypeid = getptid($con,$data['oid'],$key);
													$ptypeInfo = getpTypeInfo($con,$ptypeid);
													//$cid = getCid($con,$data['oid']);
													//$classId = getClassId($con,$cid);
													//$rate = getRate($con,$classId,$ptypeInfo[0]['rate_type'],$ptypeInfo[0]['product_type']);
													$sql = 'INSERT INTO customer_order_product_tracking'. 
													' (order_product_id,order_id,tracking_no,masterflg,producttypeid,type,backshop_price)'.
													' VALUES (?,?,?,?,?,?,?)';
													if ($stmt = $con->prepare($sql)) {
															$stmt->bind_param('iisiiid',$key,$data['oid'],$splited_trn[$i],$masterflg,$ptypeid,$ptypeInfo[0]['rate_type'],$item['cpp']);
															$res = $stmt->execute();
													}
													else {
															echo $con->error;
													}
											}
											else {
													$sql = 'UPDATE customer_order_product_tracking 
													SET backshop_price=? WHERE order_product_tracking_id=?';
													if ($stmt = $con->prepare($sql)) {
															$stmt->bind_param('di',$item['cpp'],$trckID);
															$res = $stmt->execute();
													}
													else {
															echo $con->error;
													}
											}
									}
							}
					}
			}

			//del not use-----------------------------------------------------------
			foreach($data as $key=>$item) {
					if($key!='oid' && $key!='grandTotalTh' && $key!='grandTotalCn' && $key!='totalTaobao' && $key!='totalTracking' && $key!='btTran' && $key!='btAmt' && $key!='unote') {
						//split tracking no
						$tracking_no = $item['ref'];
						$splited_no = explode(",",$tracking_no);
						$tracking_curr = $item['curr_ref'];
						$splited_curr = explode(",", $tracking_curr);

						//get oid, opid
						$oid = $data['oid'];
						$opid = $key;
						
						if($tracking_curr==$tracking_no) {
							continue;
						}

						//if curr is blank quit 
						if ($tracking_curr=='') {
							continue;
						}

						//if insert blank deltete all
						if ($tracking_no=='') {
							$sql = 'DELETE FROM customer_order_product_tracking WHERE order_id='.$oid.' AND order_product_id='.$opid;
							$stmt = $con->prepare($sql);
							$res = $stmt->execute();
						}
						else {
							//del flag
							//0 - do not delete
							//other - delete by tracking_no			
							for ($i=0; $i<count($splited_curr); $i++) {
								$del = $splited_curr[$i];
								for ($j=0; $j<count($splited_no); $j++) {
									//echo $splited_curr[$i]."-".$splited_no[$j]."\r\n";
										//if same tracking then do not delete
										if ($splited_curr[$i]==$splited_no[$j]) {
											$del = "0";
										}
								}
								
								if ($del!="0") {
									$sql = 'DELETE FROM customer_order_product_tracking'.
										' WHERE tracking_no=\''.$del.'\' AND order_id='.$oid.' AND order_product_id='.$opid;
									
									if ($stmt = $con->prepare($sql)) {
										//echo $sql;
										$res = $stmt->execute();
									}
									//echo $stmt->num_rows;
								}
								if(!$res){
									echo $con->error;
								}	
							}
						}
					}
			}

			return $res;
	}

	function getMasterFlg($con,$trn,$oid) {
			$result = 0;
			$sql = 'SELECT COUNT(*) FROM customer_order_product_tracking'.
				' WHERE order_id='.$oid.' AND tracking_no=\''.$trn.'\' AND masterflg=1';
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($count);
			while($stmt->fetch()) {
					$result = $count;
			}
			if ($result==0) $result=1;
			else $result=0;
			return $result;
	}

	function delTracking($oid,$opid,$allTrack,$tracking_curr) {
			include '../database.php';
			$res = 1;
			//split tracking no
			$splited_no = explode(",",$allTrack);
			$splited_curr = explode(",", $tracking_curr);
			
			if($tracking_curr==$allTrack) {
				return 1;
			}

			//if curr is blank quit 
			if ($tracking_curr=='') {
				return 1;
			}

			//if insert blank deltete all
			if ($allTrack=='') {
				$sql = 'DELETE FROM customer_order_product_tracking WHERE order_id='.$oid.' AND order_product_id='.$opid;
				$stmt = $con->prepare($sql);
				$res = $stmt->execute();
			}
			else {
				//del flag
				//0 - do not delete
				//other - delete by tracking_no			
				for ($i=0; $i<count($splited_curr); $i++) {
					$del = $splited_curr[$i];
					for ($j=0; $j<count($splited_no); $j++) {
							if ($splited_curr[$i]==$splited_no[$j]) {
								$del = "0";
							}
							//echo $splited_curr[$i]."-".$splited_no[$j]." ".$del."<br>";
					}
					
					if ($del!="0") {
						$sql = 'DELETE FROM customer_order_product_tracking'.
							' WHERE tracking_no=\''.$del.'\' AND order_id='.$oid.' AND order_product_id='.$opid;
						
						if ($stmt = $con->prepare($sql)) {
							//echo $sql;
							$res = $stmt->execute();
						}
						//echo $stmt->num_rows;
					}
					if(!$res){
						return $con->error;
					}	
				}
			}
			return 1;
	}

	function orderTracking ($oid,$count) {
			include '../database.php';
			$res = 1;
			$result = '';
			$sql = 'SELECT tracking_no FROM customer_order_product_tracking'.
				' WHERE order_id='.$oid;
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($trn);
			while($stmt->fetch()) {
					if ($result=='') {
						$result = $trn;
					}
					else {
						$result = $result.','.$trn;
					}
			}

			$sql = 'UPDATE customer_order SET tracking_no=\''.$result.'\',total_tracking='.$count.' WHERE order_id='.$oid;
			$stmt = $con->prepare($sql);
			$res = $stmt->execute();

			if (!$res) {
				return $con->error;
			}
			return $res;
	}
	// function genarate CRN
	function genCRN($con) {
		$i = 0;
		$newTT = '0000001';
		$stmt = $con->prepare ( 'SELECT return_no FROM customer_order_return ORDER BY return_no DESC LIMIT 1' );
		$stmt->execute ();
		$result = $stmt->get_result ();
		while ( $row = $result->fetch_array ( MYSQLI_NUM ) ) {
			foreach ( $row as $r ) {
				$i += 1;
			}
		}
		// find year
		$year = substr ( ( string ) date ( "Y" ), 2, 2 );
		// cash has records
		if ($i) {
			$tempTT = ( string ) (( int ) substr ( $r, - 7 ) + 1);
			$len = strlen ( $tempTT );
			$yearBase = substr ( $r, 2, 2 );
			if ($year != $yearBase) {
			} else if ($len == 6) {
				$newTT = '0' . $tempTT;
			} else if ($len == 5) {
				$newTT = '00' . $tempTT;
			} else if ($len == 4) {
				$newTT = '000' . $tempTT;
			} else if ($len == 3) {
				$newTT = '0000' . $tempTT;
			} else if ($len == 2) {
				$newTT = '00000' . $tempTT;
			} else if ($len == 1) {
				$newTT = '000000' . $tempTT;
			} else {
				$newTT = $tempTT;
			}
		}
		$stmt->close();
		return 'RT' . $year . $newTT;
	}

	function genPM() {
		include '../database.php';
		$i = 0;
		$newTT = '0000001';
		$stmt = $con->prepare ( 'SELECT paymore_no FROM customer_order_paymore ORDER BY paymore_no DESC LIMIT 1' );
		$stmt->execute ();
		$result = $stmt->get_result();
		while ( $row = $result->fetch_array ( MYSQLI_NUM ) ) {
				foreach ( $row as $r ) {
					$i += 1;
				}
		}
		// find year
		$year = substr ( ( string ) date ( "Y" ), 2, 2 );
		// cash has records
		if ($i) {
			$tempTT = ( string ) (( int ) substr ( $r, - 7 ) + 1);
			$len = strlen ( $tempTT );
			$yearBase = substr ( $r, 2, 2 );
			if ($year != $yearBase) {
			} else if ($len == 6) {
				$newTT = '0' . $tempTT;
			} else if ($len == 5) {
				$newTT = '00' . $tempTT;
			} else if ($len == 4) {
				$newTT = '000' . $tempTT;
			} else if ($len == 3) {
				$newTT = '0000' . $tempTT;
			} else if ($len == 2) {
				$newTT = '00000' . $tempTT;
			} else if ($len == 1) {
				$newTT = '000000' . $tempTT;
			} else {
				$newTT = $tempTT;
			}
		}
		return 'PM' . $year . $newTT;
	}

	function checkBal($con,$opid) {
		$bal = 0;
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
		return $bal;
	}

	function getCustomerInfo ($cid) {
			include '../database.php';
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
			return $myArray; 
	}

	function getLink($opid) {
			include '../database.php';
			$sql = 'SELECT product_img FROM product p JOIN customer_order_product op ON p.product_id=op.product_id WHERE op.order_product_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$opid);
			$stmt->bind_result($link);
			$stmt->execute();
			while ($stmt->fetch()) {
					$link = $link;
			}
			$con->close();
			return $link;
	}

	function getMail($uid) {
			include '../database.php';
			$sql = 'SELECT email FROM user WHERE uid=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('s',$uid);
			$stmt->bind_result($mail);
			$stmt->execute();
			while ($stmt->fetch()) {
					$mail = $mail;
			}
			$con->close();
			return $mail;
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

	function sendEmail($subject,$content,$cid,$oid,$ono,$opid,$uid,$ccode,$con) {

			$userid = getuserid($con,$uid);
			$link = getLink($opid);
			$umail = getMail($uid);
			$cInfo = getCustomerInfo($cid);		
			$strTo = str_replace("\"","",json_encode($cInfo[0]['customer_email']));
			$name = str_replace("\"","",json_encode($cInfo[0]['customer_firstname']))." ".str_replace("\"","",json_encode($cInfo[0]['customer_lastname']));
			$strSubject = '=?UTF-8?B?'.base64_encode($subject).'?=';
			$strHeader = "MIME-Version: 1.0\' . \r\n";
			$strHeader .= "Content-type: text/html; charset=utf-8\r\n";
			$strHeader .= "From: Order2Easy <order2easy_admin@order2easy.com>";
			$strMessage = "สวัสดีค่ะ คุณ ".$name." (".$ccode.")<br><br>".
				//"รายการสั่งซื้อ ".$ono."<br>".
				//"<span style='vertical-align:top;'>รายการสินค้า</span> <img src='".$link."' width='60px' height='60px'><br><br>".


				//detail===============================================================================================
				"<div style='width: 600px; padding: 25px 30px; font-size: 13px;'>".
						"<div>".
							//"<tr>
								$content.
							//</tr>".
						"</div><br>".

				//trailer==============================================================================================
				"<table>".
					"<tr>
						<td>ติดต่อกลับ</td>
						<td colspan='4'></td>
					</tr>".
					"<tr>
						<td>Email</td>
						<td colspan='4'>".$umail."</td>
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
				"<br>เจ้าหน้าที่ผู้ตรวจสอบรายการ: ".$uid.
				"<br>".date('d-m-Y H:i:s').
				"</div>";

			$flgSend = @mail($strTo,$strSubject,$strMessage,$strHeader);

			$stmt = $con->prepare('INSERT INTO total_message_log (order_id,order_product_id,customer_id,user_id,subject,content,message_date,active_link) VALUES (?,?,?,?,?,?,now(),1)');
			$stmt->bind_param('iiisss',$oid,$opid,$cid,$userid,$subject,$strMessage);
			$res = $stmt->execute();
			
			return $flgSend;
	}

	function getTopupID($con,$cid,$oid) {
			$tid = '';
			$sql = 'SELECT b.topup_id FROM payment a , payment_detail b WHERE a.customer_id = b.customer_id AND a.order_id = b.order_id AND a.customer_id = '.$cid.' and a.order_id ='.$oid.' and a.payment_type = 1';
			$stmt = $con->prepare($sql);
			$stmt->bind_result($tid);
			$stmt->execute();
			while($stmt->fetch()) {
					$tid = $tid;
			}
			$stmt->close();
			return $tid;
	}

	function getRID($opid) {
			include '../database.php';
			$rid = '';
			$sql = 'SELECT running FROM customer_order_return WHERE order_product_id='.$opid;
			$stmt = $con->prepare($sql);
			$stmt->bind_result($rid);
			$stmt->execute();
			while($stmt->fetch()) {
					$rid = $rid;
			}
			return $rid;
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

	function getpTypeInfo ($con,$ptypeid) {
			if (!isset($ptypeid)) return;

			$dataSet = array();
			$sql = 'SELECT rate_type,product_type FROM product_type WHERE producttypeid='.$ptypeid;
			$queryResult = $con->query($sql); 
			if (!$queryResult) {
					echo ("Error while getting data ".$con->error);
					return;
			} 
			while ($row = $queryResult->fetch_assoc()) {
					$dataSet[] = $row; 
			}
			return $dataSet;
	}

	function getCid($con,$oid) {
			if (!isset($oid)) return;

			$cid='';
			$sql = 'SELECT customer_id FROM customer_order WHERE order_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$oid);
			$stmt->bind_result($cid);
			$stmt->execute();
			while ($stmt->fetch()) {
					$cid = $cid;
			}
			return $cid;
	}

	function getClassID($con,$cid) {
			if (!isset($cid)) return;

			$classId='';
			$sql = 'SELECT class_id FROM customer WHERE customer_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$cid);
			$stmt->bind_result($classId);
			$stmt->execute();
			while ($stmt->fetch()) {
					$classId = $classId;
			}
			return $classId;
	}

	function getRate($con,$classId,$rType,$pType) {
			$rate=0;
			$sql = 'SELECT rate_amount FROM customer_class_rate WHERE class_id=? AND rate_type=? AND product_type=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('iii',$classId,$rType,$pType);
			$stmt->bind_result($rate);
			$stmt->execute();
			while ($stmt->fetch()) {
					$rate = $rate;
			}
			return $rate;
	}

	function isDupPaymore($con,$opid) {
			$result = 0;
			$sql = 'SELECT COUNT(*) FROM customer_order_paymore'.
				' WHERE order_product_id='.$opid;
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($count);
			while($stmt->fetch()) {
					$result = $count;
			}
			if ($result==0) $result=1;
			else $result=0;
			return $result;
	}

	function countTracking($tracking) {
			$result = 0;

			if (empty($tracking)) return $result;

			return count(explode(',' , $tracking));
	}

	function getTransport($con,$opid) {
			$result = array(
					"transport" => 0,
					"pay_transport" => 0
			);
			$sql = 'SELECT order_shipping_cn_cost,backshop_shipping_cost FROM customer_order_product WHERE order_product_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$opid);
			$stmt->bind_result($pay_transport,$transport);
			$stmt->execute();
			while ($stmt->fetch()) {
					$result['transport'] = $transport;
					$result['pay_transport'] = $pay_transport;
			}
			return $result;
	}

	function getShopName($con,$opid) {
		$shopName = '';
		$sql = 'SELECT shop_name 
			FROM customer_order_product op
			JOIN product p on op.product_id=p.product_id
			WHERE op.order_product_id=?';

		$stmt = $con->prepare($sql);
		$stmt->bind_param('i',$opid);
		$stmt->bind_result($shopName);
		$stmt->execute();
		while ($stmt->fetch()) {
			$shopName = $shopName;
		}
		return $shopName;
	}

	function checkOrderStatusInShop($con,$oid,$shopName) {
		$result = 0;
		$sql = 'SELECT backshop_quantity 
			FROM customer_order_product op
			JOIN product p on op.product_id=p.product_id
			WHERE op.order_id=? AND p.shop_name=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('is',$oid,$shopName);
		$stmt->bind_result($backshop_quantity);
		$stmt->execute();
		while ($stmt->fetch()) {
			if ($backshop_quantity==0) {
				$result = 1;
			}
			else {
				$result = 0;
				return $result;
			}
		}
		return $result;
	}

?>