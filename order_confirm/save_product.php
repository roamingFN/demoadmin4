<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	include './utility/function.php';

	$data = json_decode($_POST['data'],true);

	if (getOrderStat($con,$data['oid'])==99) {
			echo 'รายการนี้ได้ถูกยกเลิกไปแล้ว';
			return;
	}

	$sql = 'UPDATE customer_order_product'. 
		' SET first_unitquantity=?,unitprice=?,quantity=?,order_shipping_cn_cost=?,order_status=?,remark_id=?,order_product_totalprice=?'.
		' ,backshop_quantity=?,backshop_price=?,backshop_shipping_cost=?,backshop_total_price=?,unconfirmed_product_order=?,producttypeid=?,chkflg=?'.
		' WHERE order_product_id=?';
	foreach($data as $key=>$item){
		if($key!='oid'&& $key!='code' && $key!='grandTotalTh' && $key!='grandTotalCn' && $key!='tTran' && $key!='tQuan' && $key!='unote'){
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ididiididddiiii',$item['quan1'],$item['cpp'],$item['quan'],$item['tran'],$item['stt'],$item['comment'],$item['totalp'],$item['quan'],$item['cpp'],$item['tran'],$item['totalp'],$item['unconfirmed'],$item['type'],intval($item['chkflg']),$key);
			$res = $stmt->execute();
			if(!$res){
				echo $con->error;
			}
		}
	}

	//update customer_order
	$sql = 'UPDATE customer_order'. 
		' SET order_price=?,order_price_yuan=?,order_shop_transfer=?,product_available=?,user_note=?,update_by=?'.
		' WHERE order_id=?';
	if ($stmt = $con->prepare($sql)) {
		$stmt->bind_param('ddddssi',$data['grandTotalTh'],$data['grandTotalCn'],$data['tTran'],$data['tQuan'],$data['unote'],$_SESSION['ID'],$data['oid']);
		$stmt->execute();
	}
	else {
		echo 'Error while updating customer_order '.$con->error;
		return;
	}
	
	echo 'success';
	
	$con->close();
?>