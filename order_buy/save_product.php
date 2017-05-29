<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	include './function.php';
	
	$data = json_decode($_POST['data'],true);
	
	//insert/update customer product tracking
	$res = trckUpdate($con,$data);
	if ($res==0) return;

	$sql = 'UPDATE customer_order_product'. 
		' SET backshop_price=?,backshop_quantity=?,backshop_shipping_cost=?,backshop_total_price=?,order_shipping_cn_ref_no=?,current_status=IF(current_status=6,6,IF(current_status=98,98,?)),order_taobao=?'.
		' ,quantity=?,order_shipping_cn_cost=?,order_status=?,order_product_totalprice=?,backshop_allprice_yuan=?,backshop_total_yuan=?,return_baht=?,chkflg=?,tracking_company=?'.
		' WHERE order_product_id=?';
	foreach($data as $key=>$item){
		if($key!='oid' && $key!='grandTotalTh' && $key!='grandTotalCn' && $key!='totalTaobao' && $key!='totalTracking' && $key!='btTran' && $key!='btAmt' && $key!='unote'){
			if ($stmt = $con->prepare($sql)) {
					$curr = 4;
					if (empty($item['ref'])) {
							$curr = 3;
					}
					$stmt->bind_param('diddsisididdddisi',$item['cpp'],$item['quan'],$item['tran'],$item['totalp'],$item['ref'],$curr,$item['taobao'],$item['quan1'],$item['tran1'],$item['stt'],$item['totalp1'],$item['apy'],$item['btyuan'],$item['return'],intval($item['chkflg']),$item['company'],$key);
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

	//update customer_order
	$sql = 'UPDATE customer_order 
	SET order_price=?,order_price_yuan=?,taobao=?,tracking_no=?,order_price_back=?,order_shop_transfer_back=?,user_note=?,update_by=?,total_tracking=? 
	WHERE order_id=?';
	$numberTracking = countTracking($data['totalTracking']);
	$stmt = $con->prepare($sql);
	$stmt->bind_param('ddssddssii',$data['grandTotalTh'],$data['grandTotalCn'],$data['totalTaobao'],$data['totalTracking'],$data['btAmt'],$data['btTran'],$data['unote'],$_SESSION['ID'],$numberTracking,$data['oid']);
	$res = $stmt->execute();
	if(!$res) {
			echo $con->error;
	}

	echo 'success';
	
	$con->close();
?>