<?php
		$condition = '';
		$sql = '';
		$orderBy = ' ORDER BY pt.tracking_no ASC';
		$groupBy = ' GROUP BY op.order_product_id';

		$_pStatDesc = getPackageStatInfo($con);
		$_productInfo = getProductInfo($con);
		
		//condition
		if(!empty($_GET['oid'])) {
				$condition = ' WHERE o.order_id='.$_GET['oid'];
		}

    	$sql = 'SELECT pt.tracking_no,pt.order_product_tracking_id,pt.order_product_id,pt.width,pt.height,pt.length,pt.m3,pt.weight,pt.rate,pt.rateweight,pt.ratem3,pt.type,pt.remark as ptremark,pt.uid,pt.received_amount,pt.last_edit_date,pt.statusid as pstat,pt.producttypeid,pt.masterflg,pt.amount,pt.remark,o.order_id
    		,op.backshop_quantity,op.quantity,op.unitprice,op.order_shipping_cn_cost,op.order_product_totalprice,op.order_status,op.backshop_price,op.backshop_shipping_cost,op.backshop_total_price,op.return_baht,op.return_status,op.tracking_company,op.return_quantity,op.return_yuan,op.order_taobao
    		,o.order_number,o.remark as oremark, o.order_rate,o.date_order_paid,o.customer_id,op.quantity,o.tracking_no as oTracking,o.taobao,o.summary_return_flag
    		,pa.packageid,pa.packageno,pa.statusid,pa.adddate
    		, p.product_img, p.shop_name,p.product_url,p.product_color,p.product_size
    		,c.customer_firstname, c.customer_lastname, c.customer_code,c.class_id
	      	FROM customer_order_product_tracking pt JOIN customer_order o ON pt.order_id=o.order_id
	      	LEFT JOIN package pa ON pt.packageid=pa.packageid
	      	JOIN customer_order_product op ON op.order_product_id=pt.order_product_id
	       	JOIN product p ON p.product_id = op.product_id
	       	JOIN customer c ON c.customer_id = o.customer_id';

	    //echo $sql.$condition.$groupBy;
?>