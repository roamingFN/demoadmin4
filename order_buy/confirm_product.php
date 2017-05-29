<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	include './function.php';

	$sumQuan=0;
	$data = json_decode($_POST['data'],true);
	//insert/update customer product tracking
	$res = trckUpdate($con,$data);
	if ($res==0) return;
	
	//update status
	$stmt = $con->prepare('UPDATE customer_order SET order_status_code=4 WHERE order_id=?');
	$stmt->bind_param('i',$data['oid']);
	$res = $stmt->execute();
	if(!$res) {
		echo $con->error;
	}

	//update data
	$sql = 'UPDATE customer_order_product'. 
		' SET backshop_price=?,backshop_quantity=?,backshop_shipping_cost=?,backshop_total_price=?,order_shipping_cn_ref_no=?,current_status=?,order_taobao=?'.
		' ,quantity=?,order_shipping_cn_cost=?,order_status=?,order_product_totalprice=?,chkflg=?,tracking_company=?,return_baht=?'.
		' WHERE order_product_id=?';
	foreach($data as $key=>$item){
		if($key!='oid' && $key!='grandTotalTh' && $key!='grandTotalCn' && $key!='totalTaobao' && $key!='totalTracking' && $key!='btTran' && $key!='btAmt' && $key!='unote'){
			$stmt = $con->prepare($sql);
			$curr = 4;
			if (empty($item['ref'])) {
					//echo $item['ref'];
					$curr = 3;
			}
			$sumQuan+=$item['quan'];
			$stmt->bind_param('diddsisididisdi',$item['cpp'],$item['quan'],$item['tran'],$item['totalp'],$item['ref'],$curr,$item['taobao'],$item['quan1'],$item['tran1'],$item['stt'],$item['totalp1'],intval($item['chkflg']),$item['company'],$item['return'],$key);
			$res = $stmt->execute();
			if (!$res) {
				echo $con->error;
			}
		}
	}

	//update customer_order
	$sql = 'UPDATE customer_order'. 
		' SET order_price=?,order_price_yuan=?,taobao=?,tracking_no=?,date_order_last_update=now(),order_price_back=?,order_shop_transfer_back=?,user_note=?,update_by=?,product_available=?,total_tracking=?'.
		' WHERE order_id=?';
	$numberTracking = countTracking($data['totalTracking']);
	$stmt = $con->prepare($sql);
	$stmt->bind_param('ddssddssiii',$data['grandTotalTh'],$data['grandTotalCn'],$data['totalTaobao'],$data['totalTracking'],$data['btAmt'],$data['btTran'],$data['unote'],$_SESSION['ID'],$sumQuan,$numberTracking,$data['oid']);
	$res = $stmt->execute();
	if(!$res) {
			echo $con->error;
	}
	
	//paymore------------------------------------------
	$refund = json_decode($_POST['refund'],true);
	foreach ($refund as $key => $item) {
			if (isDupPaymore($con,$key)==1) {
					$transport = getTransport($con,$key);
					$sql = 'INSERT into customer_order_paymore (paymore_no,paymore_date,order_product_id,first_unitquantity,quantity,loss_quantity,first_unitprice,unitprice,total_yuan,rate,total_baht,paymore_status,order_id,customer_code, pay_transport, transport) VALUES (?,now(),?,?,?,?,?,?,?,?,?,0,?,?,?,?)';
					if ($con->prepare($sql)) {
							$stmt = $con->prepare($sql);
							$pmNo = genPM();
							$diffQuan = $item['quan1']-$item['quan'];
							$totalYn = $item['totalTh']/$item['rate'];
							$stmt->bind_param('siiiidddddisdd',$pmNo,$key,$item['quan1'],$item['quan'],$diffQuan,$item['cpp1'],$item['cpp'],$totalYn,$item['rate'],$item['totalTh'],$item['oid'],$item['ccode'],$transport['pay_transport'],$transport['transport']);
							$res = $stmt->execute();
							if (!$res) {
									echo $stmt->error;
							}
					}
					else {
							echo $con->error;
					}
			}
			else {
					$transport = getTransport($con,$key);
					$sql = 'UPDATE customer_order_paymore SET paymore_date=now(),first_unitquantity=?,quantity=?,loss_quantity=?,first_unitprice=?,unitprice=?,total_yuan=?,rate=?,total_baht=?,paymore_status=0,order_id=?,customer_code=?, pay_transport=?, transport=? WHERE order_product_id=?';
					if ($con->prepare($sql)) {
							$stmt = $con->prepare($sql);
							$diffQuan = $item['quan1']-$item['quan'];
							$totalYn = $item['totalTh']/$item['rate'];
							$stmt->bind_param('iiidddddisddi',$item['quan1'],$item['quan'],$diffQuan,$item['cpp1'],$item['cpp'],$totalYn,$item['rate'],$item['totalTh'],$item['oid'],$item['ccode'],$transport['pay_transport'],$transport['transport'],$key);
							$res = $stmt->execute();
							if (!$res) {
									echo $stmt->error;
							}
					}
					else {
							echo $con->error;
					}
			}
	}

	echo 'success';
	
	$con->close();
?>