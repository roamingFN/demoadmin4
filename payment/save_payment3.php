<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	include './function/utility.php';
	
	$data = json_decode($_POST['data'],true);
	//print_r($data);
	
	foreach ($data as $key => $item) {
		$flg = validTopup($con,$item['cashid'],$item['topup_id']);
		if ($flg==1) {
			echo 'ยอดเงินไม่ตรงกัน';  
			return;
		}
		else if ($flg==2) {
			echo 'วันที่โอนไม่ตรงกัน'; 
			return;
		}
		else if ($flg==3) {
			echo 'ชื่อธนาคารไม่ตรงกัน'; 
			return;
		}
		
		//update cash 
		$stmt = $con->prepare('UPDATE cash SET topup_id=?,status=?,matchtopup_by=?,matchtopup_date=now() WHERE cashid=?');
		$stmt->bind_param('ssss',$item['topup_id'],$item['cash_status'],$_SESSION['ID'],$key);
		$res = $stmt->execute();

		//update topup.status
		$stmt = $con->prepare('UPDATE customer_request_topup SET topup_status=1 WHERE topup_id='.$item['topup_id']);
		$stmt->execute();

		//update customer.wait_amount
		$stmt = $con->prepare('UPDATE customer SET wait_amount=wait_amount-? WHERE customer_id=?');
		$stmt->bind_param('di',$item['topup_amount'],$item['customer_id']);
		$stmt->execute();

		//update customer_statement.statement_name
		$statement_name = 'เติมเงิน '.$item['topup_no'].' - ตรวจสอบแล้ว';
		$sql = 'UPDATE customer_statement SET statement_name=? WHERE topup_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('si',$statement_name,$item['topup_id']);
		$stmt->execute();

		//get order info
		$orderInfo = getOrderInfo($item['topup_id'],$item['customer_id']);

		if ($orderInfo!='') {

			//==================================================================================================================
			//select order_id in payment_detail from topup_id
			$order_id = array();
			$count1 = 0;
			$count2 = 0;
			$type = '';
			$sql = 'SELECT order_id from payment_detail WHERE topup_id='.$item['topup_id'];
			$stmt = $con->prepare($sql);
			$stmt->bind_result($oid);
			$stmt->execute();
			while($stmt->fetch()) {
					array_push($order_id, $oid);
			}

			foreach ($order_id as $key => $value) {
					//update payment_detail.flag_completed
					$sql = 'UPDATE payment_detail SET flag_completed=1 WHERE topup_id=? AND order_id=?';
					//$sql = 'UPDATE payment_detail SET flag_completed=1 WHERE order_id=?';
					$stmt = $con->prepare($sql);
					$stmt->bind_param('is',$item['topup_id'],$value);
					$stmt->execute();

					$tmpoid = $value;
			}

			//update others topup_id-----------------------------------------------
		 	$arr_topup_id = array();
			$sql = 'SELECT topup_id from payment_detail WHERE order_id='.$tmpoid;
			$stmt = $con->prepare($sql);
			$stmt->bind_result($tid);
			$stmt->execute();
			while($stmt->fetch()) {
					array_push($arr_topup_id, $tid);
			}
			foreach ($arr_topup_id as $key => $topup_id) {
					//if (checkTopup($con,$topup_id)) {
						$sql = 'UPDATE payment_detail SET flag_completed=1 WHERE topup_id=?';
						$stmt = $con->prepare($sql);
						$stmt->bind_param('i',$topup_id);
						$stmt->execute();
					//}
			}

			foreach ($order_id as $key => $value) {
					//1
					$sql = 'SELECT count(topup_id) FROM payment_detail'.
						' WHERE flag_completed=1 AND order_id='.$value;
					$stmt = $con->prepare($sql);
					$stmt->bind_result($count1);	
					$stmt->execute();
					while($stmt->fetch()) {
							$count1 = $count1;
					}
					
					//2
					$sql = 'SELECT count(topup_id) FROM payment_detail'.
						' WHERE order_id='.$value;
					$stmt = $con->prepare($sql);
					$stmt->bind_result($count2);
					$stmt->execute();
					while($stmt->fetch()) {
							$count2 = $count2;
					}
					
					//if 1==2
					if ($count1==$count2) {
							//insert new customer_statement for order
							$ono = getOrderNo($con,$value);
							// $statement_name = 'ค่าสินค้า เลขที่สั่งซื้อ '.$ono;
							// $sql = 'INSERT INTO customer_statement (customer_id,order_id,statement_name,statement_date,credit) VALUES (?,?,?,?,?)';
							// $dt = getOrderDt($con,$value);
							// $dp = getOrderPrice($con,$value);
							// $stmt = $con->prepare($sql);
							// $res = $stmt->bind_param('ssssd',$item['customer_id'],$value,$statement_name,$dt,$dp);
							// $stmt->execute();
							// if (!$res) {
							// 		echo $stmt->error;
							// }
							
							//update statement
							$sid = findStatementID($con,$value,$ono);
							$statement = 'ชำระค่าสินค้า เลขที่สั่งซื้อ '.$ono.' (ตรวจสอบแล้ว)';
							$sql = 'UPDATE customer_statement set statement_name=? WHERE statement_id=?';
							$stmt = $con->prepare($sql);
							$stmt->bind_param('si',$statement,$sid);
							$res = $stmt->execute();

							//update customer_request_payment
							$sql = 'UPDATE customer_request_payment SET payment_request_status=2 WHERE order_id='.$value;
							$stmt = $con->prepare($sql);
							$stmt->execute();

							//14/01/2017	comment code to fetch type and hard code for type 1
							//get type
							// $sql = 'SELECT payment_request_type FROM customer_request_payment WHERE order_id='.$value;
							// $stmt = $con->prepare($sql);
							// $stmt->bind_result($type);
							// $stmt->execute();
							// while($stmt->fetch()) {
							// 		$type = $type;
							// }

							//type 1 - ค่าสินค้า
							//type 2 - ค่าขนส่ง
							$type=1;

							//if type=1
							if ($type==1) {
									$sql = 'UPDATE customer_order SET order_status_code=3,date_order_last_update=now() WHERE order_id='.$value;
									$stmt = $con->prepare($sql);
									$stmt->execute();

									$sql = 'UPDATE customer_order_product SET current_status=3, current_updatetime=now() WHERE order_id='.$value;
									$stmt = $con->prepare($sql);
									$stmt->execute();
							}
							//if type=2
							else if ($type==2) {
									$sql = 'UPDATE customer_order SET order_status_code=9,date_order_last_update=now() WHERE order_id='.$value;
									$stmt = $con->prepare($sql);
									$stmt->execute();

									$sql = 'UPDATE customer_order_product SET current_status=9, current_updatetime=now() WHERE order_id='.$value;
									$stmt = $con->prepare($sql);
									$stmt->execute();
							}
					}	//end if
			}	//end foreach order_id
		}
		else {		
				//for package
				$packageInfo = getPackageInfo($item['topup_id'],$item['customer_id']);
				
				if ($packageInfo!='') {

						$order_id = array();
						$count1 = 0;
						$count2 = 0;
						$type = '';
						$sql = 'SELECT package_id from payment_detail WHERE topup_id='.$item['topup_id'];
						$stmt = $con->prepare($sql);
						$stmt->bind_result($oid);
						$stmt->execute();
						while($stmt->fetch()) {
								array_push($order_id, $oid);
						}

						foreach ($order_id as $key => $value) {
								//update payment_detail.flag_completed
								$sql = 'UPDATE payment_detail SET flag_completed=1 WHERE topup_id=? AND package_id=?';
								//$sql = 'UPDATE payment_detail SET flag_completed=1 WHERE order_id=?';
								$stmt = $con->prepare($sql);
								$stmt->bind_param('is',$item['topup_id'],$value);
								$stmt->execute();

								$tmpoid = $value;
						}

						//update others topup_id-----------------------------------------------
					 	$arr_topup_id = array();
						$sql = 'SELECT topup_id from payment_detail WHERE package_id='.$tmpoid;
						$stmt = $con->prepare($sql);
						$stmt->bind_result($tid);
						$stmt->execute();
						while($stmt->fetch()) {
								array_push($arr_topup_id, $tid);
						}
						foreach ($arr_topup_id as $key => $topup_id) {
								//if (checkTopup($con,$topup_id)) {
									$sql = 'UPDATE payment_detail SET flag_completed=1 WHERE topup_id=?';
									$stmt = $con->prepare($sql);
									$stmt->bind_param('i',$topup_id);
									$stmt->execute();
								//}
						}

						foreach ($order_id as $key => $value) {
								//1
								$sql = 'SELECT count(topup_id) FROM payment_detail'.
									' WHERE flag_completed=1 AND package_id='.$value;
								$stmt = $con->prepare($sql);
								$stmt->bind_result($count1);	
								$stmt->execute();
								while($stmt->fetch()) {
										$count1 = $count1;
								}
								
								//2
								$sql = 'SELECT count(topup_id) FROM payment_detail'.
									' WHERE package_id='.$value;
								$stmt = $con->prepare($sql);
								$stmt->bind_result($count2);
								$stmt->execute();
								while($stmt->fetch()) {
										$count2 = $count2;
								}
							
								//if 1==2
								if ($count1==$count2) {
										//insert new customer_statement for package
										$pno = getPackageNo($con,$value);
										// $statement_name = 'ค่าขนส่ง เลขที่สั่งซื้อ '.$ono;
										// $sql = 'INSERT INTO customer_statement (customer_id,packageid,statement_name,statement_date,credit) VALUES (?,?,?,?,?)';
										// $dt = getPackageDt($con,$value);
										// $dp = getPackagePrice($con,$value);
										// $stmt = $con->prepare($sql);
										// $res = $stmt->bind_param('ssssd',$item['customer_id'],$value,$statement_name,$dt,$dp);
										// $stmt->execute();
										// if (!$res) {
										// 		echo $stmt->error;
										// }

										//update statement
										$sid = findStatementPackage($con,$value,$pno);
										$statement = 'ชำระค่า Package เลขที่ '.$pno.' (ตรวจสอบแล้ว)';
										$sql = 'UPDATE customer_statement set statement_name=? WHERE statement_id=?';
										$stmt = $con->prepare($sql);
										$stmt->bind_param('si',$statement,$sid);
										$res = $stmt->execute();

										//update customer_request_payment
										$sql = 'UPDATE customer_request_payment SET payment_request_status=2 WHERE package_id='.$value;
										$stmt = $con->prepare($sql);
										$stmt->execute();

										//update o.order_status_code and op.current_status
										$sql = 'UPDATE customer_order SET order_status_code=9,date_order_last_update=now() WHERE order_id='.$value;
										$stmt = $con->prepare($sql);
										$stmt->execute();

										$sql = 'UPDATE customer_order_product SET current_status=9, current_updatetime=now() WHERE order_id='.$value;
										$stmt = $con->prepare($sql);
										$stmt->execute();

										$sql = 'UPDATE customer_order_product as a inner join customer_order_paymore as b on a.order_product_id = b.order_product_id set return_status=3 WHERE b.package_id='.$value;
										$stmt = $con->prepare($sql);
										$stmt->execute();

								}	//end if
						}
				}
		}
		updateCustomerClass($con,$item['customer_id']);
		echo 'success';
	}
	
	$con->close();

	function getOrderInfo($tid,$cid) {
			include '../database.php';
			$orderInfo = new stdClass();
			$orderInfo->oid = '';
			$orderInfo->ono = '';
			$orderInfo->dt = '';
			$orderInfo->price = '';

			$sql = 'SELECT order_id from payment_detail WHERE topup_id='.$tid.' AND customer_id='.$cid;
			$stmt = $con->prepare($sql);
			$stmt->bind_result($oid);
			$stmt->execute();
			while($stmt->fetch()){
					$orderInfo->oid = $oid;
			}
			
			if ($orderInfo->oid=='') return '';
			$sql = 'SELECT date_order_created,order_price,order_number from customer_order WHERE order_id='.$orderInfo->oid;
			$stmt = $con->prepare($sql);
			$stmt->bind_result($dt,$price,$ono);
			$stmt->execute();
			while($stmt->fetch()) {
					$orderInfo->ono = $ono;
					$orderInfo->dt = $dt;
					$orderInfo->price = $price;
			}
			$con->close();
			return $orderInfo;
	}

	function getPackageInfo($tid,$cid) {
			include '../database.php';
			$packageInfo = new stdClass();
			$packageInfo->oid = '';
			$packageInfo->ono = '';
			$packageInfo->dt = '';
			$packageInfo->price = '';

			$sql = 'SELECT package_id from payment_detail WHERE topup_id='.$tid.' AND customer_id='.$cid;
			$stmt = $con->prepare($sql);
			$stmt->bind_result($oid);
			$stmt->execute();
			while($stmt->fetch()){
					$packageInfo->oid = $oid;
			}
			
			if ($packageInfo->oid=='') return '';
			$sql = 'SELECT createdate,amount,packageno from package WHERE packageid='.$packageInfo->oid;
			$stmt = $con->prepare($sql);
			$stmt->bind_result($dt,$price,$ono);
			$stmt->execute();
			while($stmt->fetch()) {
					$packageInfo->ono = $ono;
					$packageInfo->dt = $dt;
					$packageInfo->price = $price;
			}
			$con->close();
			return $packageInfo;
	}
?>