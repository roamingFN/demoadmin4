<html>
<head>
	<meta charset="utf-8"> 
</head>
<style type="text/css">
	img {
		width: 270px;
		height: 50px;
	}
	.info {
		border: 2px solid black;
    	padding: 10px; 
    	width: 250px;
	}
</style>
<?php
		session_start();
      	if (!isset($_SESSION['ID'])) {
           		header("Location: ../login.php");
        }
		include '../database.php';

		// $sql = 'SELECT address_name,line_1,city,country,zipcode,phone FROM customer_address WHERE customer_id='.$_GET['cid'];
		// $sql = "SELECT customer.customer_code, co.order_number, pt.m3, pt.weight, pt.tracking_no, pt.uid, pt.last_edit_date, wt.transport_th_name"
		// 		." FROM customer" 
		// 		." JOIN customer_order co ON customer.customer_id = co.customer_id"
		// 		." JOIN customer_order_product_tracking pt ON co.order_id = pt.order_id"
		// 		." JOIN customer_order_shipping os ON co.order_id = os.order_id"
		// 		." JOIN website_transport wt ON os.order_shipping_th_option = wt.transport_id"
		// 		." WHERE pt.tracking_no='".$_GET["tracking_no"]."' AND co.customer_id='".$_GET['cid']."'"
		// 		." GROUP BY customer.customer_id";

		$sql = "SELECT customer.customer_code, co.order_number, pt.m3, pt.weight, pt.tracking_no, pt.uid, pt.last_edit_date,os.order_shipping_th_option"
				." FROM customer" 
				." JOIN customer_order co ON customer.customer_id = co.customer_id"
				." JOIN customer_order_product_tracking pt ON co.order_id = pt.order_id"
				." JOIN customer_order_shipping os ON co.order_id = os.order_id"
				." WHERE pt.tracking_no='".$_GET["tracking_no"]."' AND co.customer_id='".$_GET['cid']."'"
				." GROUP BY customer.customer_id";

		//echo $sql;
		$stmt = $con->prepare($sql);
		$stmt->execute();

		// $stmt->bind_result($add,$line,$city,$country,$zipcode,$phone);
		$stmt->bind_result($cc, $order_no, $size, $weight, $tracking_no, $uid, $dt, $ost);
		while($stmt->fetch()) {
			if($dt=='' || $dt=='0000-00-00 00:00:00') $dt = '';
			else $dt = date_format(date_create($dt),"d/m/Y H:i:s");

			$cc = $cc;
			$order_no = $order_no;
			$size = $size;
			$weight = $weight;
			$tracking_no = $tracking_no;
			$uid = $uid;
			$ost = $ost;
		}

		$tran = getTran($con,$ost);
		if ($tran=='') {
			$tran = $ost;
		}

		echo "<div class='info'>";
		echo "<table>";
			// ."<th></th><th></th>";

		echo "<tr><td style='text-align:center'>รหัสลูกค้า</td><td> : ". $cc."</td></tr>"
			// ."<tr><td style='text-align:center'>ID ลูกค้า </td><td> : "."id ลูกค้า"."</td></tr>"
			."<tr><td style='text-align:center'>เลขที่ออร์เดอร์</td><td> : ".$order_no."</td></tr>"
			."<tr><td style='text-align:center'>น้ำหนัก</td><td> : ".$weight." Kg</td></tr>"
			."<tr><td style='text-align:center'>ขนาด</td><td> : ".$size."</td></tr>"
			."<tr><td style='text-align:center'>Tracking No.</td><td> : ".$tracking_no."</td></tr>"
			."<tr><td style='text-align:center'>ผู้ตรวจ</td><td> : ".$uid."</td></tr>"
			."<tr><td style='text-align:center'>วันที</td><td> : ".$dt."</td></tr>"
			."<tr><td style='text-align:center'>วิธีส่ง</td><td> : ".$tran."</td></tr>"
			."</table>";
		echo "</div>";
		$barcodeText = $tracking_no;
		echo "<div style='width:250;text-align:center'>";
		echo "<br><img src='../php-barcode-master/barcode.php?text=".$barcodeText."' />";
		echo "<br><h4 style='letter-spacing: 3px;'>".$barcodeText."</h4>";
		echo "</div>";

		$con->close();
		echo '<script>window.print()</script>';

		function getTran($con,$ost) {
			$tran = '';
			$sql = 'SELECT transport_th_name FROM website_transport WHERE transport_id=\''.$ost.'\'';
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($tran);
			while($stmt->fetch()) {
				$tran = $tran;
			}
			return $tran;
		}
?>
</html>