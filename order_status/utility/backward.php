<html>
		<head>
				<meta charset="utf-8">
		</head>

<?php
		
		include '../../database.php';
		include './function.php';
		
		session_start();
		if (!isset($_SESSION['ID'])){
		        header("Location: ../../login.php");
		}

		if (getOrderStat($con,$_POST['oid'])==99) {
				echo '<script>alert("รายการนี้ได้ถูกยกเลิกไปแล้ว");';
				echo 'window.location.href="../product.php?order_id='.$_POST['oid'].'";</script>';
				return;
		}
		
		$sql = 'UPDATE customer_order
			SET order_status_code=0
			WHERE order_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('s',$_POST['oid']);
		$res = $stmt->execute();

		$sql = 'DELETE FROM customer_request_payment WHERE payment_flag=1 AND order_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('s',$_POST['oid']);
		$res = $stmt->execute();
		
		if(!$res) {
				echo 'Error while updating customer_order '.$con->error;
				echo '</script>window.location.href="../product.php?order_id='.$_POST['oid'].'</script>';
		}

		echo '<script>alert("ย้อนกลับสถานะสำเร็จ");';
		echo 'window.location.href="../product.php?order_id='.$_POST['oid'].'";</script>';
?>

</html>