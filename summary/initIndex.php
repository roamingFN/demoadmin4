<?php
		$_cus = getCusInfo();
		$_stat = getStatInfo();
		$sql = '';
		$sqlAll = '';
		$request = '';
		$condition = ' WHERE o.order_status_code>=6';
		$searchTotal = '';
		$orderBy = ' ORDER BY o.order_number DESC';

		//session for excel
		$_SESSION['sql'] = '';
		$_SESSION['condition'] = '';
		$_SESSION['orderBy'] = '';
		
		$cases = array();
		$request = '';
		if(!empty($_GET['ono'])) {
				array_push($cases,' o.order_number LIKE \'%'.$_GET['ono'].'%\'');
				$request .= '&ono='.$_GET['ono'];
		}
		if(!empty($_GET['cid'])) {
				$cid = $_GET['cid'];
				$cid = explode('(', $cid);
				$cid = trim($cid[0]);
				array_push($cases, ' CONCAT(c.customer_firstname, " ",c.customer_lastname) LIKE \'%'.$cid.'%\'');
				$request .= '&cid='.$cid;
		}
		if(!empty($_GET['from'])) {
				array_push($cases,' o.date_order_created>="'.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2).' 00:00:00"');
            	$request .= '&from='.$_GET['from'];  
    	}
    	if(!empty($_GET['to'])) {
            	array_push($cases,' o.date_order_created<="'.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2).' 23:59:59"');
            	$request .= '&to='.$_GET['to'];
        }
        if(!empty($_GET['status'])) {
				if ($_GET['status']!="-") {
					array_push($cases,' o.order_status_code='.$_GET['status']);
				}
				$request .= '&status='.$_GET['status'];
		}

		if(sizeof($cases)>0) {
				$condition = ' WHERE'.$cases[0];
				for($i=1;$i<sizeof($cases);$i++) {
						$condition .= ' AND'.$cases[$i];
				}
		}

		//prepare SQL
		// $sql = 'SELECT c.customer_firstname, c.customer_lastname, os.total_tracking, os.total_tracking_in_package, os.total_count_confirmed, os.total_count_backshop, os.total_received, os.total_price_payment, os.total_price_backshop, os.total_price_received, os.total_return_product1, os.total_return_product2, os.total_return, os.remark, o.order_id, o.order_number, o.customer_id
		// 	,(select count(distinct(tracking_no)) from customer_order_product_tracking where order_id = o.order_id and statusid = 1 and packageid > 0) as total_tracking_con
		// FROM customer_order_summary os JOIN customer_order o ON os.order_id=o.order_id
		// JOIN customer c ON o.customer_id = c.customer_id';

		$sql = 'SELECT c.customer_firstname, c.customer_lastname, c.customer_code,o.total_tracking, o.order_id, o.order_number, o.customer_id,o.product_quantity,o.product_available,o.received_amount,(o.product_available-o.received_amount) as missing,o.order_price,o.order_price_back,o.received_price
			,(select count(distinct(tracking_no)) from customer_order_product_tracking where order_id = o.order_id and statusid = 1 and packageid > 0) as total_tracking_con
			,(select sum(return_baht) from customer_order_product where order_id = o.order_id and return_status = 2 and return_baht >0) as return1
			, (o.order_price_back - o.received_price) as return1Missing
			,(select sum(total_baht) from customer_order_return where order_id = o.order_id and return_status = 1 and return_type = 2) as returnComplete
		FROM customer_order o JOIN customer c ON o.customer_id = c.customer_id';

	  	$sqlCount = 'SELECT COUNT(*) FROM customer_order o JOIN customer c ON o.customer_id = c.customer_id';

    	$_SESSION['sql'] = $sql;
    	$_SESSION['condition'] = $condition;
    	$_SESSION['orderBy'] = $orderBy;
?>