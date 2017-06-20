<?php
	//print_r($_POST);

	require_once('../../../database.php');
	include './function.php';

	$count = 0;
	$oid = $_POST['oid'];
	$opid = $_POST['opid'];

	//check return_summary before add or update
	$sql = 'SELECT COUNT(order_product_id) FROM customer_order_return_summary WHERE order_product_id='.$opid;
	$stmt= $con->prepare($sql);
	$stmt->bind_result($count);
	$stmt->execute();
	while ($stmt->fetch()) {
	}
	
	//cal 
	$loss = $_POST['backshop_quantity']-$_POST['return_quantity'];
	$return_yuan = $_POST['return_quantity']*(float)$_POST['backshop_price'];
	$return_baht = $_POST['return_yuan']*$_POST['rate'];
	
	$remark = 'ร้านค้าคืนเงิน';

	if ($count==0) {
		$ccode = getCustomerCode($con,$_POST['cid']);
		$tid = getTopupID($con,$_POST['cid'],$_POST['oid']);

		//insert
		$rtno = genCRN($con);
		$sql = 'INSERT INTO customer_order_return_summary (return_date
																,return_status
																,return_type
																,return_no
																,order_product_id
																,first_unitquantity
																,quantity
																,loss_quantity
																,unitprice
																,total_yuan
																,rate
																,total_baht
																,topup_id
																,order_id
																,customer_code
																,remark)'.
			' VALUES (now(),1,1,?,?,?,?,?,?,?,?,?,?,?,?,?)';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('siiiiddddiiss', $rtno, $opid, $_POST['backshop_quantity'], $loss, $_POST['return_quantity'], $_POST['backshop_price'], $return_yuan, $_POST['rate'], $return_baht, $tid, $oid, $ccode, $remark);		
			$res = $stmt->execute();

			$sql = 'UPDATE customer_order_product
			SET return_quantity=?,return_yuan=?
			WHERE order_id=? AND order_product_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('idii', $_POST['return_quantity'], $_POST['return_yuan'], $oid, $opid);		
			$res = $stmt->execute();
			echo $con->error;
	}
	else {
		//update
		$sql = 'UPDATE customer_order_return_summary
		SET quantity=?, loss_quantity=?, total_yuan=?, total_baht=?
		WHERE return_type=1 AND order_id=? AND order_product_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('iiddii', $_POST['return_quantity'], $loss, $return_yuan, $return_baht, $oid, $opid);		
		$res = $stmt->execute();
		echo $con->error;
	
		$sql = 'UPDATE customer_order_product
			SET return_quantity=?,return_yuan=?
			WHERE order_id=? AND order_product_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('idii', $_POST['return_quantity'], $_POST['return_yuan'], $oid, $opid);		
		$res = $stmt->execute();
		echo $con->error;
	}

	echo '<script>alert("success");location.href="../../detail.php?oid='.$oid.'"</script>';