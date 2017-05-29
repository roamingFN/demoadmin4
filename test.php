<html>
<head>
        <meta charset="UTF-8">
</head>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<?php
		include './database.php';

		$oid=688;
		//$opid=1858;
		$splited_no = array('Tr00001','Tr00003');
		for ($i=0; $i<count($splited_no); $i++) {
			$sql = 'SELECT width,length,height,m3 FROM customer_order_product_tracking WHERE order_id='.$oid.' AND tracking_no=\''.$splited_no[$i].'\'';
			$stmt = $con->prepare($sql);
			$res = $stmt->execute();
			$stmt->bind_result($width,$length,$height,$m3);
			while ($stmt->fetch()) {
				if ($width!=0 && $length!=0 && $height!=0 && $m3!=0) {
					echo $splited_no[$i].' '.'ไม่สามารถทำการลบ Tracking ได้ เนื่องจากมีการอัพเดทไปแล้ว';
				}
				else {
					echo 'gg';
				}
			}
		}
?>
</html>