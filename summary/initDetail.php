<?php
		$condition = '';
		$sql = '';
		
		//condition
		if(!empty($_GET['oid'])) {
				$condition = ' WHERE o.order_id='.$_GET['oid'];
		}

    	$sql = 'SELECT o.order_id,o.order_number,o.order_rate,o.date_order_paid,o.remark,op.order_product_id,op.quantity,op.backshop_quantity,op.backshop_price,op.backshop_shipping_cost,op.backshop_total_price,op.order_status,op.received_amount,op.return_baht,op.return_baht2,op.return_status2,op.email_no2,p.product_id,p.product_img,c.customer_id'.
	      	' FROM customer_order o JOIN customer_order_product op ON o.order_id=op.order_id'.
	       	' JOIN product p ON p.product_id = op.product_id'.
	       	' JOIN customer c ON c.customer_id = o.customer_id';
?>