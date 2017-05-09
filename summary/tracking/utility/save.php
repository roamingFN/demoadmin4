<?php	
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../../../database.php';

	$data = json_decode($_POST['data'],true);

	$sqlProduct = 'UPDATE customer_order_product 
		SET return_quantity=?, return_yuan=?
		WHERE order_product_id=?';

	$sqlTracking = 'UPDATE customer_order_product_tracking
		SET amount=backshop_amount-?,received_amount=backshop_amount-?
		WHERE order_product_tracking_id=?';

	foreach($data as $key=>$item) {
		$stmt = $con->prepare($sqlProduct);
		$stmt->bind_param('idi',$item['return_quan'],$item['return_yuan2'],$item['opid']);
		$res = $stmt->execute();

		$stmt = $con->prepare($sqlTracking);
		$stmt->bind_param('iii',$item['return_quan'],$item['return_quan'],$key);
		$res = $stmt->execute();
	}

	$con->close();
	echo 'success';
?>