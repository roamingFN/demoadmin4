<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	
	$data = json_decode($_POST['data'],true);
	//print_r($data);
	
	foreach ($data as $key => $item) {
		//update cash
		$stmt = $con->prepare('UPDATE cash SET topup_id=?,status=? WHERE cashid=?');
		$stmt->bind_param('sss',$item['new_topup_id'],$item['cash_status'],$key);
		$res = $stmt->execute();

		//update new topup.status
		$stmt = $con->prepare('UPDATE customer_request_topup SET topup_status=1 WHERE topup_id='.$item['new_topup_id']);
		$stmt->execute();

		//update old topup.status
		$stmt = $con->prepare('UPDATE customer_request_topup SET topup_status=0 WHERE topup_id='.$item['old_topup_id']);
		$stmt->execute();

		//update customer.wait_amount
		$stmt = $con->prepare('UPDATE customer SET wait_amount=wait_amount+?-? WHERE customer_id=?');
		$stmt->bind_param('ddi',$item['old_topup_amount'],$item['new_topup_amount'],$item['customer_id']);
		$stmt->execute();

		//update new customer_statement.statement_name
		$statement_name = 'เติมเงิน '.$item['topup_no'].' - ตรวจสอบแล้ว';
		$sql = 'UPDATE customer_statement SET statement_name=? WHERE topup_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('si',$statement_name,$item['new_topup_id']);
		$stmt->execute();

		//update old customer_statement.statement_name
		$statement_name = 'เติมเงิน '.$item['topup_no'].' - (รอตรวจสอบ)';
		$sql = 'UPDATE customer_statement SET statement_name=? WHERE topup_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('si',$statement_name,$item['old_topup_id']);
		$stmt->execute();

		//update new payment_detail.flag_completed
		$sql = 'UPDATE payment_detail SET flag_completed=1 WHERE topup_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('i',$item['new_topup_id']);
		$stmt->execute();

		//update old payment_detail.flag_completed
		$sql = 'UPDATE payment_detail SET flag_completed=1 WHERE topup_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('i',$item['old_topup_id']);
		$stmt->execute();

		//get order info
		$orderInfo = getOrderInfo($item['topup_id'],$item['customer_id']);

		//insert new customer_statement for order
		$statement_name = 'ค่าสินค้า เลขที่สั่งซื้อ '.$orderInfo->ono;
		$sql = 'INSERT INTO customer_statement (customer_id,order_id,statement_name,statement_date,credit) VALUES (?,?,?,?,?)';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('ssssd',$item['customer_id'],$orderInfo->oid,$statement_name,$orderInfo->dt,$orderInfo->price);
		$stmt->execute();

		//select new order_id in payment_detail from topup_id
		$order_id = array();
		$count1 = 0;
		$count2 = 0;
		$type = '';
		$sql = 'SELECT order_id from payment_detail  WHERE topup_id='.$item['new_topup_id'];
		$stmt = $con->prepare($sql);
		$stmt->bind_result($oid);
		$stmt->execute();
		while($stmt->fetch()){
				array_push($order_id, $oid);
		}

		foreach ($order_id as $key => $value) {
				//1
				$sql = 'SELECT count(*) FROM payment_detail'.
					' WHERE topup_id='.$item['topup_id'].' AND  flag_completed=1 AND order_id='.$value;
				$stmt = $con->prepare($sql);
				$stmt->bind_result($count1);
				$stmt->execute();
				while($stmt->fetch()) {
						$count1 = $count1;
				}

				//2
				$sql = 'SELECT count(*) FROM payment_detail'.
					' WHERE topup_id='.$item['topup_id'].' AND order_id='.$value;
				$stmt = $con->prepare($sql);
				$stmt->bind_result($count2);
				$stmt->execute();
				while($stmt->fetch()) {
						$count2 = $count2;
				}

				//if 1==2
				if ($count1==$count2) {
						//update customer_request_payment
						$sql = 'UPDATE customer_request_payment SET payment_request_status=2 WHERE order_id='.$value;
						$stmt = $con->prepare($sql);
						$stmt->execute();

						//get type
						$sql = 'SELECT payment_request_type FROM customer_request_payment WHERE order_id='.$value;
						$stmt = $con->prepare($sql);
						$stmt->bind_result($type);
						$stmt->execute();
						while($stmt->fetch()) {
								$type = $type;
						}

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

		//select new order_id in payment_detail from topup_id
		$old_order_id = array();
		$sql = 'SELECT order_id from payment_detail  WHERE topup_id='.$item['old_topup_id'];
		$stmt = $con->prepare($sql);
		$stmt->bind_result($oid);
		$stmt->execute();
		while($stmt->fetch()){
				array_push($old_order_id, $oid);
		}

		foreach ($old_order_id as $key => $value) {
				//update customer_request_payment
				$sql = 'UPDATE customer_request_payment SET payment_request_status=1 WHERE order_id='.$value;
				$stmt = $con->prepare($sql);
				$stmt->execute();

				//get type
				$sql = 'SELECT payment_request_type FROM customer_request_payment WHERE order_id='.$value;
				$stmt = $con->prepare($sql);
				$stmt->bind_result($type);
				$stmt->execute();
				while($stmt->fetch()) {
						$type = $type;
				}

				//if type=1
				if ($type==1) {
						$sql = 'UPDATE customer_order SET order_status_code=2,date_order_last_update=now() WHERE order_id='.$value;
						$stmt = $con->prepare($sql);
						$stmt->execute();

						$sql = 'UPDATE customer_order_product SET current_status=2, current_updatetime=now() WHERE order_id='.$value;
						$stmt = $con->prepare($sql);
						$stmt->execute();
				}
				//if type=2
				else if ($type==2) {
						$sql = 'UPDATE customer_order SET order_status_code=8,date_order_last_update=now() WHERE order_id='.$value;
						$stmt = $con->prepare($sql);
						$stmt->execute();

						$sql = 'UPDATE customer_order_product SET current_status=8, current_updatetime=now() WHERE order_id='.$value;
						$stmt = $con->prepare($sql);
						$stmt->execute();
				}
		}	//end foreach order_id
		
	}

	echo 'success';
	
	$con->close();

	function getOrderInfo($tid,$cid){
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

		$sql = 'SELECT date_order_created,order_price,order_number from customer_order WHERE order_id='.$orderInfo->oid;
		$stmt = $con->prepare($sql);
		$stmt->bind_result($dt,$price,$ono);
		$stmt->execute();
		while($stmt->fetch()){
				$orderInfo->ono = $ono;
				$orderInfo->dt = $dt;
				$orderInfo->price = $price;
		}

		return $orderInfo;
	}
?>