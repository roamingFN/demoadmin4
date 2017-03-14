<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
	date_default_timezone_set("Asia/Bangkok");
				
	include '../database.php';
	include './utility/function.php';
	$uid = $_SESSION['ID'];

	$sumQuan=0;
	$data = json_decode($_POST['data'],true);

	if (getOrderStat($con,$data['oid'])==99) {
			echo 'รายการนี้ได้ถูกยกเลิกไปแล้ว';
			return;
	}

	//update status
	$stmt = $con->prepare('UPDATE customer_order SET order_status_code=1 WHERE order_id=?');
	$stmt->bind_param('i',$data['oid']);
	$res = $stmt->execute();
	if(!$res){
		echo $con->error;
	}
	
	//update customer_order_product
	$sql = 'UPDATE customer_order_product'. 
		' SET first_unitquantity=?,unitprice=?,confirmed_product_price=?,confirmed_quantity=?,quantity=?,order_shipping_cn_cost=?,order_status=?,remark_id=?,order_product_totalprice=?'.
		' ,backshop_quantity=?,backshop_price=?,backshop_shipping_cost=?,backshop_total_price=?,unconfirmed_product_order=?,producttypeid=?,chkflg=?'.
		' WHERE order_product_id=?';
	foreach($data as $key=>$item){
		if($key!='oid' && $key!='ono' && $key!='cid' && $key!='cname' && $key!='total' && $key!='cmail' && $key!='grandTotalTh' && $key!='grandTotalCn' && $key!='tTran' && $key!='tQuan' && $key!='unote'){
			$stmt = $con->prepare($sql);
			if ($item['stt']==2) {
				$item['quan']=0;
				$item['tran']=0;
			}
			$sumQuan+=$item['quan'];
			
			$stmt->bind_param('iddiidiididddiiii',$item['quan1'],$item['cpp'],$item['cpp'],$item['quan'],$item['quan'],$item['tran'],$item['stt'],$item['comment'],$item['totalp'],$item['quan'],$item['cpp'],$item['tran'],$item['totalp'],$item['unconfirmed'],$item['type'],intval($item['chkflg']),$key);
			$res = $stmt->execute();
			if(!$res){
				echo $con->error;
			}
		}
	}

	//update customer_order
	$sql = 'UPDATE customer_order'. 
		' SET order_price=?,order_price_yuan=?,date_order_last_update=now(),order_shop_transfer=?,product_available=?,user_note=?,update_by=?,product_available=?'.
		' WHERE order_id=?';
	$stmt = $con->prepare($sql);
	$stmt->bind_param('ddddssii',$data['grandTotalTh'],$data['grandTotalCn'],$data['tTran'],$data['tQuan'],$data['unote'],$_SESSION['ID'],$sumQuan,$data['oid']);
	$res = $stmt->execute();
	if(!$res){
		echo $con->error;
	}

	//insert customer statement 
	/*$sql = 'INSERT INTO customer_statement (customer_id,order_id,statement_name,statement_date,statement_detail,debit,credit)'.
		' VALUES (?,?,?,?,?,?,?)';
	
	$desc = 'ค่าสินค้า เลขที่สั่งซื้อ '.$data['ono'];
	$detail = '';
	$debit = 0;
	$stmt = $con->prepare($sql);
	$stmt->bind_param('iisssdd',$data['cid'],$data['oid'],$desc,$dt,$detail,$debit,$data['grandTotalTh']);
	$res = $stmt->execute();
	if(!$res){
		echo $con->error;
	}*/

	//insert customer request payments
	$sql = 'INSERT INTO customer_request_payment'.
		' (order_id,customer_id,payment_request_type,payment_request_status,payment_request_amount,date_payment_created,payment_flag)'.
		' VALUES (?,?,?,?,?,?,1)';
	$dt = date('Y-m-d H:i:s');
	$typ = 1;
	$stat = 0;
	$stmt = $con->prepare($sql);
	$stmt->bind_param('iiiids',$data['oid'],$data['cid'],$typ,$stat,$data['grandTotalTh'],$dt);
	$res = $stmt->execute();
	if(!$res){
			echo $con->error;
	}
	
	$ccode = getCustomerCode($con,$data['cid']);

	sendEmail($data['ono'],$data['cmail'],$data['cname'],$data['grandTotalTh'],$data['oid'],$ccode,$data['cid'],$uid,$con);
	
	echo 'success';

	$con->close();
?>