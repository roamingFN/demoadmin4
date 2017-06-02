<?php	
		session_start();
		if(!isset($_SESSION['ID'])){
			header("Location: ../login.php");
		}
					
		include '../../database.php';
		include './function.php';

		$data = json_decode($_POST['data'],true);

		//02/06/2017 Pratchaya Ch. check last update, if there are any update before this request then refresh the page
		foreach ($data as $key => $item) {
			if($key!='oid' && $key!='oremark') {
				if (isAlreadyUpdated($con,$item['currentDateTime'],$key,$data['oid'])) {
					echo 'Tracking นี้มีการอัพเดท กรุณาโหลดหน้าจอใหม่';
					return;
				}
			}
		}
		
		//update tracking
		$sql = 'UPDATE customer_order_product_tracking 
			SET received_amount=received_amount+?,amount=amount+?,remark=?,uid=?,last_edit_date=now(),width=?,length=?,height=?,m3=?,weight=?,producttypeid=?,type=?,rate=?,statusid=?,total=?,rateweight=?,ratem3=?
			WHERE order_product_tracking_id=? AND order_id=?';
		foreach($data as $key=>$item) {
				if($key!='oid' && $key!='oremark') {
						$stmt = $con->prepare($sql);
						$stmt->bind_param('iissiddddiididddii',$item['rec'],$item['rec'],$item['remark'],$_SESSION['ID'],$item['width'],$item['length'],$item['height'],$item['m3'],$item['weight'],$item['ptype'],$item['type'],$item['rate'],$item['stat'],$item['total'],$item['rateWeight'],$item['rateM3'],$key,$data['oid']);
						$res = $stmt->execute();
						
						//update total in tracking
						updTotalInTracking($con,$key);
						if (!$res) {
								echo $con->error;
						}
				}
		}

		//update total in tracking
		$tmpFlg = false;
		foreach($data as $key=>$item) {
				if($key!='oid' && $key!='oremark') {					
						updTotalInTracking($con,$key);
						if ($item['stat']==1) $tmpFlg=true;
				}
		}

		//update current_status order_product
		// $code = 6;
		// $countAll = 0;
		// $countCom = 0;
		// foreach ($data as $key=>$item) {
		// 		if($key!='oid' && $key!='oremark') {
		// 				//count complete
		// 				$sql = 'SELECT count(order_product_tracking_id) FROM customer_order_product_tracking WHERE statusid=1 AND order_product_id=? AND order_id=?';
		// 				$stmt = $con->prepare($sql);
		// 				$stmt->bind_param('ii',$item['opid'],$data['oid']);
		// 				$res = $stmt->execute();
		// 				$stmt->bind_result($count);
		// 				while ($stmt->fetch()) {
		// 						$countCom = $count;
		// 				}

		// 				//count all
		// 				$sql = 'SELECT count(order_product_tracking_id) FROM customer_order_product_tracking WHERE order_product_id=? AND order_id=?';
		// 				$stmt = $con->prepare($sql);
		// 				$stmt->bind_param('ii',$item['opid'],$data['oid']);
		// 				$res = $stmt->execute();
		// 				$stmt->bind_result($count);
		// 				while ($stmt->fetch()) {
		// 						$countAll = $count;
		// 				}

		// 				if ($countCom==$countAll) {
		// 						$code = 7;
		// 						//update opid
		// 						$sql = 'UPDATE customer_order_product SET current_status=? WHERE order_product_id=?';
		// 						$stmt = $con->prepare($sql);
		// 						$stmt->bind_param('ii',$code,$item['opid']);
		// 						$stmt->execute();
		// 				}
		// 		}		
		// }

		//complete
		if ($tmpFlg) {
				//count complete
				$sql = 'SELECT count(order_product_tracking_id) FROM customer_order_product_tracking WHERE statusid=1 AND order_id=?';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('i',$data['oid']);
				$res = $stmt->execute();
				$stmt->bind_result($count);
				while ($stmt->fetch()) {
						$countCom = $count;
				}

				//count all
				$sql = 'SELECT count(order_product_tracking_id) FROM customer_order_product_tracking WHERE order_id=?';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('i',$data['oid']);
				$res = $stmt->execute();
				$stmt->bind_result($count);
				while ($stmt->fetch()) {
						$countAll = $count;
				}

				if ($countCom==$countAll) {
						$code = 7;
						//update opid
						$sql = 'UPDATE customer_order SET order_status_code=? WHERE order_id=?';
						$stmt = $con->prepare($sql);
						$stmt->bind_param('ii',$code,$data['oid']);
						$stmt->execute();
				}

				foreach($data as $key=>$item) {
						$total = 0;
						if($key!='oid' && $key!='oremark') {					
								$opid = $item['opid'];
								$tid = $key;

								//update current status
								$sql = 'UPDATE customer_order_product set current_status=7 WHERE order_product_id=?';
								$stmt = $con->prepare($sql);
								$stmt->bind_param('i',$opid);
								$stmt->execute();

								//update received_amount
								$total+=getAmount($con,$opid,$key,$data['oid']);
								$sql = 'UPDATE customer_order_product SET received_amount=? WHERE order_product_id=?';
								$stmt = $con->prepare($sql);
								$stmt->bind_param('di',$total,$opid);
								$stmt->execute();		
						}
				}

				$oid = $data['oid'];
				//if current status id is 7 or 98 in all product
				if (checkCurrentStatus($con,$oid)) {
					$sql = 'UPDATE customer_order SET order_status_code=7,date_order_last_update=now() WHERE order_id='.$oid;
					$stmt = $con->prepare($sql);
					$stmt->execute();
				}
		}
		
		// $sql = 'UPDATE customer_order
		// SET remark=?,order_status_code=?
		// WHERE order_id=?';
		// $stmt = $con->prepare($sql);
		// $stmt->bind_param('sii',$data['oremark'],$code,$data['oid']);
		// $res = $stmt->execute();
		// if(!$res) {
		// 		echo $con->error;
		// }

		$con->close();
		echo 'success';

?>