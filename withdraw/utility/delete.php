<?php
		include '../../database.php';

		if (!isset($_POST['wid'])) echo null;

		//delete
		if($stmt = $con->prepare('DELETE FROM customer_request_withdraw WHERE withdraw_request_id="'.$_POST['wid'].'"')) {
				$res = $stmt->execute();
		}
		
		echo json_encode($_POST['wid']);
?>