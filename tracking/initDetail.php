<?php
		$_pStatDesc = getPackageStatInfo($con);
		$_productInfo = getProductInfo($con);
		$condition = '';
		$groupBy = ' GROUP BY pt.order_product_tracking_id';
		//$orderBy = ' ORDER BY o.order_id AND pt.tracking_no DESC';
		$orderBy = ' ORDER BY pt.order_product_tracking_id';
		$sql = '';
		
		//condition
		if(isset($_GET['ptno']) && isset($_GET['oid'])) {
				$condition = ' WHERE pt.tracking_no=\''.$_GET['ptno'].'\' AND pt.order_id='.$_GET['oid'];
		}
		else {
				header( "Location: ./index.php" );
		}

    	$sql = 'SELECT pt.tracking_no,pt.order_product_tracking_id,pt.order_product_id,pt.width,pt.height,pt.length,pt.m3,pt.weight,pt.rate,pt.rateweight,pt.ratem3,pt.type,pt.remark as ptremark,pt.uid,pt.received_amount,pt.last_edit_date,pt.statusid as pstat,pt.producttypeid,pt.masterflg,pt.amount,pt.remark,pt.total,o.order_id,o.order_number,o.remark as oremark, op.quantity,op.backshop_quantity,pt.packageid,pa.packageno,pa.statusid as pstatusid,pa.adddate, p.product_img, c.customer_firstname, c.customer_lastname, c.customer_code,c.class_id
	      	FROM customer_order_product_tracking pt JOIN customer_order o ON pt.order_id=o.order_id
	      	LEFT JOIN package pa ON pt.packageid=pa.packageid
	      	JOIN customer_order_product op ON op.order_product_id=pt.order_product_id
	       	JOIN product p ON p.product_id = op.product_id
	       	JOIN customer c ON c.customer_id = o.customer_id';

	    //echo $sql.$condition.$orderBy.$groupBy;
?>