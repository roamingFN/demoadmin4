<html>
<head>
        <meta charset="UTF-8">
</head>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<?php
		include './database.php';

		function getTran($con,$ost) {
			$tran = '';
			$sql = 'SELECT transport_th_name FROM website_transport WHERE transport_id='.$ost;
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($tran);
			while($stmt->fetch()) {
				$tran = $tran;
			}
			return $tran;
		}

		echo getTran($con,4);
?>
</html>