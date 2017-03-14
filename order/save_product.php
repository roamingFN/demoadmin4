<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	
	$data = json_decode($_POST['data'],true);
	$stmt = $con->prepare('UPDATE customer_order SET order_status_code=?,order_price=?,transfer_price=?,process_status=? WHERE order_id=?');
	$stmt->bind_param('iddii',$data['code'],$data['total1'],$data['total2'],$data['process'],$data['oid']);
	$res = $stmt->execute();
	if(!$res){
		echo $con->error;
	}
	
	$stmt = $con->prepare('UPDATE customer_order_shipping SET order_shipping_th_date=?,order_shipping_th_ref_no=?,order_shipping_th_weight=?,order_shipping_th_cost=? WHERE order_id=?');
	$stmt->bind_param('ssddi',$data['th-date'],$data['th-ref'],$data['th-kg'],$data['th-cost'],$data['oid']);
	$res = $stmt->execute();
	if(!$res){
		echo $con->error;
	}
	
	foreach($data as $key=>$item){
		if($key!='oid' &&$key!='code' &&$key!='th-date' &&$key!='th-ref' &&$key!='th-kg' &&$key!='th-cost' &&$key!='total1' &&$key!='total2' &&$key!='process'){
			$stmt = $con->prepare('UPDATE customer_order_product SET order_shipping_cn_ref_no=?,order_status=?,confirmed_quantity=?,order_cause=?,confirmed_product_price=?,order_shipping_cn_cost=?,backshop_price=?,backshop_shipping_cost=?,order_shipping_cn_box=?,order_shipping_cn_m3_size=?,order_shipping_cn_weight=?,order_shipping_rate=? WHERE order_product_id=?');
			$stmt->bind_param('siisdddddddds',$item['ref'],$item['stt'],$item['quan'],$item['cause'],$item['cpp'],$item['cost'],$item['bp'],$item['bcost'],$item['cn-box'],$item['cn-m3'],$item['cn-kg'],$item['cn-rate'],$key);
			$res = $stmt->execute();
			if(!$res){
				echo $con->error;
			}
		}
	}
	echo 'success';
	
	$con->close();
?>