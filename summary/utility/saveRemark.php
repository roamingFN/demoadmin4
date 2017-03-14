<?php
		$oid = $_POST['oid'];
		$remark = $_POST['remark'];

		include '../../database.php';

		//get status description
		$sql = 'UPDATE customer_order SET remark=\''.$remark.'\' WHERE order_id='.$oid;
		if($stmt = $con->prepare($sql)){
				$stmt->execute();
				echo '<script>alert("บันทึกสำเร็จ");window.location.href="../detail.php?oid='.$oid.'";</script>';
		}
		else {
				echo ("Error while updating remark ".$con->error);
		}
		$con->close();
?>