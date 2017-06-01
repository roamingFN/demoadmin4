<html>
<head>
        <meta charset="UTF-8">
</head>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<?php
		include './database.php';

		$oid=688;
		//$opid=1858;
		function getLastTrackingUpdateDate($con,$oid) {
			$currentDate = new DateTime();
			$interval = 0;
			$sql = 'SELECT last_edit_date FROM customer_order_product_tracking WHERE order_id='.$oid.' ORDER BY last_edit_date DESC LIMIT 1';
			if($stmt = $con->prepare($sql)) {
					$stmt->execute();
					$stmt->bind_result($date);
					while($stmt->fetch()) {
						$date = new DateTime($date);
						$interval = $date->diff($currentDate);
						$interval = $interval->days;
					}
			}
			else {
					echo ("Error while getting last tracking update date ".$con->error);
			}
			return $interval;
		}
		
		echo getLastTrackingUpdateDate($con,$oid);
		$currentDate = new DateTime("2017-10-20");
		$date = new DateTime("2017-10-10");
		$interval = $currentDate->diff($date);
		$interval = $interval->days;
		echo '<br>'.$interval; 
		
?>
</html>