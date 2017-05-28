<html>
<head>
        <meta charset="UTF-8">
</head>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<?php
		include './database.php';

		function isCompletedTracking($con,$optid) {
			$result = false;
			$sql = 'SELECT statusid FROM customer_order_product_tracking WHERE order_product_tracking_id=? AND masterflg=1';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('i',$optid);
			$stmt->execute();
			$stmt->bind_result($status);
			while($stmt->fetch()) {
				if ($status==1) {
					$result = true;
				}
			}
			return $result;
		}

		echo isCompletedTracking($con,1);
?>
</html>