<html>
<head>
        <meta charset="UTF-8">
</head>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<?php
		include './database.php';
		$tid = 0;

		$sql = 'SELECT topup_id FROM customer_order_return WHERE order_product_id=2491';
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($tid);
		while($stmt->fetch()) {
				$tid = $tid;
		}
		echo $tid;
		// $sql = 'SELECT usable_amout FROM customer_request_topup WHERE topup_id='.$tid;
		// $stmt = $con->prepare($sql);
		// $stmt->execute();
		// $stmt->bind_result($bal);
		// while($stmt->fetch()) {
		// 		$bal = $bal;
		// }
		//echo $bal;

?>
</html>