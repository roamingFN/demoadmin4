<?php
		$_cus = getCusInfo();
		$_stat = getStatInfo();
		$_pStatDesc = getPackageStatInfo($con);
		$sql = '';
		$sqlAll = '';
		$request = '';
		$condition = ' WHERE (o.order_status_code>=4) AND (pt.masterflg=1) AND (pt.statusid=0)';
		$searchTotal = '';
		$orderBy = ' ORDER BY pt.order_product_tracking_id';

		//get product type
		$_pType[0] = "-";
		if($stmt = $con->prepare('SELECT producttypeid,producttypename,rate_type,product_type FROM product_type')){
				$stmt->execute();
				$stmt->bind_result($ptid,$ptname,$rate,$type);
				while($stmt->fetch()){
						$_pType[$ptid] = $ptname;
				}
		}

		//condition
		$_SESSION['sql'] = '';
		$cases = array();
		$request = '';
		if(!empty($_GET['tno'])) {
				array_push($cases,' pt.tracking_no LIKE \'%'.$_GET['tno'].'%\'');
				$request .= '&tno='.$_GET['tno'];
		}
		if(!empty($_GET['ono'])) {
				array_push($cases,' o.order_number LIKE \'%'.$_GET['ono'].'%\'');
				$request .= '&ono='.$_GET['ono'];
		}
		if(!empty($_GET['cname'])) {
				$cname = $_GET['cname'];
				$cname = explode('(', $cname);
				$cname = trim($cname[0]);
				array_push($cases, ' CONCAT(c.customer_firstname, " ",c.customer_lastname) LIKE \'%'.$cname.'%\'');
				$request .= '&cname='.$cname;
		}
		if(!empty($_GET['cid'])) {
				array_push($cases,' o.customer_id=\''.$_GET['cid'].'\'');
				$request .= '&cid='.$_GET['cid'];
		}
		if(!empty($_GET['from'])) {
				$dt = $_GET['from'];
				array_push($cases,' o.date_order_created>="'.substr($dt,6,4).'-'.substr($dt,3,2).'-'.substr($dt,0,2).' 00:00:00"');
            	$request .= '&from='.$dt;  
    	}
    	if(!empty($_GET['to'])) {
    			$dt = $_GET['to'];
            	array_push($cases,' o.date_order_created<="'.substr($dt,6,4).'-'.substr($dt,3,2).'-'.substr($dt,0,2).' 23:59:59"');
            	$request .= '&to='.$dt;
        }
        if(!empty($_GET['ostatus'])) {
        		if ($_GET['ostatus']!="-") {
					array_push($cases,' o.order_status_code= '.$_GET['ostatus']);
					$request .= '&ostatus='.$_GET['ostatus'];
				}
		}
		if(!empty($_GET['pstatus'])) {
        		if ($_GET['pstatus']!="-") {
					array_push($cases,' p.statusid= '.$_GET['pstatus']);
					$request .= '&pstatus='.$_GET['pstatus'];
				}
		}
		if(!empty($_GET['tstatus'])) {
				if ($_GET['tstatus']=="-") {
						array_push($cases,' pt.statusid>=0');
				}
				else {
						array_push($cases,' pt.statusid='.$_GET['tstatus']);
				}
				$request .= '&tstatus='.$_GET['tstatus'];
		}
		if(!empty($_GET['tran'])) {
				array_push($cases,' o.customer_id='.$_GET['tran']);
				$request .= '&tran='.$_GET['tran'];
		}
		if(!empty($_GET['all'])) {
				$all = $_GET['all'];
				array_push($cases,' (pt.tracking_no LIKE \'%'.$all.'%\' or o.order_number LIKE \'%'.$all.'%\' or CONCAT(c.customer_firstname, " ",c.customer_lastname) LIKE \'%'.$all.'%\' or o.customer_id=\''.$all.'\')');
				$request .= '&all='.$_GET['all'];
		}

		if(sizeof($cases)>0) {
				$condition = ' WHERE (o.order_status_code>=4) AND (pt.masterflg=1) AND'.$cases[0];
				for($i=1;$i<sizeof($cases);$i++) {
						$condition .= ' AND'.$cases[$i];
				}
		}

		//prepare SQL
		$sql = 'SELECT pt.tracking_no, pt.order_product_tracking_id, pt.m3, pt.weight, pt.type, pt.rate, pt.ratem3,pt.rateweight,pt.statusid as tstatusid, pt.uid, pt.last_edit_date,pt.total,o.order_id, o.order_number, o.date_order_created, pt.remark, c.customer_id, c.customer_code,c.customer_firstname, c.customer_lastname,p.statusid as pstatusid
		FROM customer_order_product_tracking pt JOIN customer_order o ON pt.order_id=o.order_id 
		JOIN customer c ON o.customer_id=c.customer_id
		LEFT JOIN package p ON p.packageid=pt.packageid';

	  	$sqlCount = 'SELECT COUNT(*) FROM customer_order_product_tracking pt JOIN customer_order o ON pt.order_id=o.order_id 
		JOIN customer c ON o.customer_id=c.customer_id LEFT JOIN package p ON p.packageid=pt.packageid';

    	$_SESSION['sql'] = $sql;
    	$_SESSION['condition'] = $condition;
    	$_SESSION['orderBy'] = $orderBy;
?>