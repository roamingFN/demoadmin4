<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';
date_default_timezone_set('Asia/Bangkok');


//http://localhost/pharse2/order2easy/package/detail.php?chkorder=340&chkorder=370&actionPackage%27+value%3D=deleteItems
if(isset($_GET['actionPackage'])){
	
	$params = array();
	parse_str($_GET['actionPackage'],$params);
	if(isset($params['chkorder'])){
			if(count($_SESSION['addOrder']['orderId'])>0 && count($params['chkorder'])>0){
				$i=0;
				foreach($_SESSION['addOrder']['orderId'] as $val1){
					$j=0;
					foreach($params['chkorder'] as $val2){
						if($val1==$val2){
							//echo $val1.'->'.$val2;
							unset($_SESSION['addOrder']['orderId'][$i]);
							
						}	
						$j++;
					}
					$i++;
				}
				//new index array
				if(count($_SESSION['addOrder']['orderId'])>0){
					$tempArray=array();
					foreach($_SESSION['addOrder']['orderId'] as $val){
						$tempArray[]=$val;
					}
					unset($_SESSION['addOrder']['orderId']);
					$_SESSION['addOrder']['orderId']=$tempArray;
				}
				
				if(count($_SESSION['addOrder']['orderId'])<=0){
					unset($_SESSION['addOrder']);
				}
				echo json_encode('Y');
				//print_r($_SESSION['addOrder']);
			}
			
			
	}
	
}

if(isset($_GET['searchOrder'])){
$criteria = '';
	if (isset ( $_GET ['params'] ) && $_GET ['params'] != 'Search') {
		$params = trim ( $_GET ['params'] );
		/**
		 * where
		 */
		$criteria .= ' AND(';
		$criteria .= ' order_number LIKE "%' . $params . '%"';
		$criteria .= ' OR customer_firstname LIKE "%' . $params . '%"';
		$criteria .= ' OR customer_lastname LIKE "%' . $params . '%"';
		$criteria .= ' OR customer_code LIKE "%' . $params . '%"';
		$criteria .= ' OR total_tracking LIKE "%' . $params . '%"';
		$criteria .= ' OR product_quantity LIKE "%' . $params . '%"';
		$criteria .= ' OR product_available LIKE "%' . $params . '%"';
		$criteria .= ' OR received_complete_date LIKE "%' . $params . '%"';
		$criteria .= ')';
	}
	$sqlOrderComplete = 'SELECT CO.*,C.* FROM CUSTOMER_ORDER CO INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = CO.CUSTOMER_ID where CO.order_status_code=7 and CO.status_add_box=0' . $criteria . ' order by C.customer_firstname';
	// echo $sqlOrderComplete;
	$ordersComplete = array ();
	if ($result = $con->query ( $sqlOrderComplete )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$ordersComplete [] = $row;
		}
		
		// }
		if(isset($_SESSION ['order_id'])){
			$i=0;
			foreach($_SESSION ['order_id'] as $order_id){
				foreach($ordersComplete as $key => $val){
					if($val==$order_id){
						unset($_SESSION ['order_id'][$i]);
					}
				}
				$i++;
			}
			
		}
		echo json_encode ( $ordersComplete, true );
	} else {
		echo "no";
	}
}


if (isset ( $_GET ['addOrderItems'] )) {
	/**
	 * input: addOrder, params[chkorder]
	 * 1.
	 * check add order isset
	 * 2. check param is not empty
	 * 3. validate column is not flag
	 * 3.1 if flag (have data ) show not add
	 * 3.2 not flag add flag and show success. [keep data to session array]
	 */


		$sql = 'select customer_id,order_id,status_add_box from customer_order where order_number=? ';
	    //$sql='SELECT CO.*,C.*,COP.*,COPT.* ,COPT.tracking_no tracking_no_copt FROM CUSTOMER_ORDER CO INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = CO.CUSTOMER_ID INNER JOIN CUSTOMER_ORDER_PRODUCT COP ON COP.ORDER_ID= CO.ORDER_ID INNER JOIN CUSTOMER_ORDER_PRODUCT_TRACKING COPT ON COPT.ORDER_ID= CO.ORDER_ID where CO.CUSTOMER_ID ='.$customerID.' and CO.order_status_code=7 and CO.ORDER_ID not in ('.$orderId.') GROUP BY COPT.tracking_no';
		$index = 0;
		$orderId=array();
		$customerId=array();
		$params=$_GET['params'];

		foreach ( $params as $val ) {
			if ($stmt = $con->prepare ( $sql )) {
				$stmt->bind_param ( "s", trim ( $val ) );
				$stmt->execute ();
				$stmt->bind_result ( $customer_id,$order_Id, $statusAddBox );
				$stmt->fetch ();
				$stmt->close ();
					
				if ($statusAddBox == 0) {
					$orderId[]=$order_Id;
					$customerId[]=$customer_id;
					$resMsg ['success'][]=$val;

				} else {
					$resMsg ['error'] [] = trim ( $val );
				}
			}
		}

		$temp=$_SESSION['addOrder']['orderId'];
		if(count($orderId)>0){
			foreach($orderId as $val){
				$_SESSION['addOrder']['orderId'][]=$val;
			}
				
			$temp=array_unique($_SESSION['addOrder']['orderId']);
			unset($_SESSION['addOrder']['orderId']);
			$_SESSION['addOrder']['orderId']=$temp;
		}


		echo json_encode ( $resMsg );


} // end add order get


if (isset ( $_GET ['addOrder'] )) {
	/**
	 * input: addOrder, params[chkorder]
	 * 1.
	 * check add order isset
	 * 2. check param is not empty
	 * 3. validate column is not flag
	 * 3.1 if flag (have data ) show not add
	 * 3.2 not flag add flag and show success. [keep data to session array]
	 */
	$flag=false;
	if(isset($_SESSION['addOrder'])){
		$sql = 'select customer_id,order_id,status_add_box from customer_order where order_number=? ';
		$index = 0;
		$orderId=array();
		$customerId=array();
		$params=$_GET['params'];
		
		foreach ( $params as $val ) {
			if ($stmt = $con->prepare ( $sql )) {
				$stmt->bind_param ( "s", trim ( $val ) );
				$stmt->execute ();
				$stmt->bind_result ( $customer_id,$order_Id, $statusAddBox );
				$stmt->fetch ();
				$stmt->close ();
					
				if ($statusAddBox == 0) {
					
					$orderId[]=$order_Id;
					$customerId[]=$customer_id;
					//$resMsg ['success'][]=$val;
		
				} else {
					$resMsg ['error'] [] = trim ( $val );
				}
			}
		}

		
		
		$customerInitial=$customerId[0];
		if(count($customerId)>0){
			
			foreach($customerId as $val){
				if($customerInitial!=$val){
					$resMsg ['errorCus'] [] = 'ควรเลือกลูกค้าชื่อเดียวกัน';
					$flag=true;
					echo json_encode ( $resMsg );	
					exit ;
				}
			}
		}
		if(!$flag){
			$tempArray=array();
			if(count($orderId)>0){
				foreach($orderId as $val){
					$tempArray[]=$val;
					$resMsg ['success'][]=$val;
				}
				unset($_SESSION['addOrder']);
				
				$_SESSION['addOrder']['customerId']=array($customerId[0]);
				$_SESSION['addOrder']['orderId']=$tempArray;
			}
			echo json_encode ( $resMsg );
		}

		
		
		
		
	}else{
		$params = $_GET ['params'];
		$resMsg = array ();
		$flag = false;
		$customerId='';
		
		if (count ( $params ) > 0) {
			
			// condition check group name user unique
			
			$initCustomer = $params [0];
			
			$customerId = '';
			$sql = 'SELECT customer_id FROM customer_order  WHERE order_number="' . $initCustomer . '"';
			
			if ($result = $con->query ( $sql )) {
				while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
					$ordersComplete [] = $row;
					$customerId = $ordersComplete [0] ['customer_id'];
				}
			}
			
			// echo $customerId;
			$ordersUnique = array ();
			
			foreach ( $params as $val ) {
				$sql = 'SELECT customer_id FROM customer_order  WHERE order_number="' . $val . '"';
				// echo $sql;
				if ($result = $con->query ( $sql )) {
					while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
						$ordersUnique [] = $row;
					}
				}
			}
			
			if (count ( $ordersUnique ) > 0) {
				foreach ( $ordersUnique as $val ) {
					// echo $val['customer_id'];
					
					if ($val ['customer_id'] != $customerId) {
						$resMsg ['errorCus'] [] = 'ควรเลือกลูกค้าชื่อเดียวกัน';
						echo json_encode ( $resMsg );
						$flag = true;
						exit ;
					}
				}
			}
			
			// $initCustomer user id
			
			if (! $flag) {
				// 1 select data where order_number=xxx and status_add_box=0 then( update customer_order set status_add_box=1 where where order_number=xxx) else add message to array and return to response.
				$sql = 'select customer_id,order_id,status_add_box from customer_order where order_number=? ';
				$index = 0;
				$orderId=array();
				$customerId=array();
				
				foreach ( $params as $val ) {
					if ($stmt = $con->prepare ( $sql )) {
						$stmt->bind_param ( "s", trim ( $val ) );
						$stmt->execute ();
						$stmt->bind_result ( $customer_id,$order_Id, $statusAddBox );
						$stmt->fetch ();
						$stmt->close ();
							
						if ($statusAddBox == 0) {
							$orderId[]=$order_Id;
							$customerId[]=$customer_id;
							$resMsg ['success'][]=$val;
				
						} else {
							$resMsg ['error'] [] = trim ( $val );
						}
					}
				}
				
				if (count ( $resMsg ) > 0) {
					//print_r($orderId);
					//print_r(array_unique($customerId));
					
					if(isset($_SESSION['addOrder'])){
						unset($_SESSION['addOrder']);
						$_SESSION['addOrder']['orderId']=$orderId;
						$_SESSION['addOrder']['customerId']=array_unique($customerId);
					}else{
						$_SESSION['addOrder']['orderId']=$orderId;
						$_SESSION['addOrder']['customerId']=array_unique($customerId);
					}
					
					echo json_encode ( $resMsg );
				}
			}
		}
	}
} // end add order get

//http://localhost/pharse2/order2easy/package/package-do.php?addOrderComplete=MjAxNi0wNy0yMCAwNjozMQ%3D%3D&params=%7B%22orderId%22%3A%5B340%5D%2C%22customerId%22%3A%5B35%5D%7D
if(isset($_GET['addOrderComplete'])){
	$arrs=json_decode($_GET['params']);
	$orderId=implode($arrs->orderId,',');
	$customerID=$arrs->customerId[0];
	//print_r(json_decode($_GET['params']));
	//$sql='SELECT CO.*,C.*,COP.*,COPT.* ,COPT.tracking_no tracking_no_copt FROM CUSTOMER_ORDER CO INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = CO.CUSTOMER_ID INNER JOIN CUSTOMER_ORDER_PRODUCT COP ON COP.ORDER_ID= CO.ORDER_ID INNER JOIN CUSTOMER_ORDER_PRODUCT_TRACKING COPT ON COPT.ORDER_ID= CO.ORDER_ID where CO.CUSTOMER_ID ='.$customerID.' and CO.order_status_code=7 and CO.ORDER_ID';
	$sql='SELECT CO.*,C.*,COP.*,COPT.* ,COPT.tracking_no tracking_no_copt FROM CUSTOMER_ORDER CO INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = CO.CUSTOMER_ID INNER JOIN CUSTOMER_ORDER_PRODUCT COP ON COP.ORDER_ID= CO.ORDER_ID INNER JOIN CUSTOMER_ORDER_PRODUCT_TRACKING COPT ON COPT.ORDER_ID= CO.ORDER_ID where CO.CUSTOMER_ID ='.$customerID.' and CO.order_status_code=7 and CO.ORDER_ID not in ('.$orderId.') GROUP BY COPT.tracking_no';
	//$arrs=$_GET['params'];
	//echo $sql;
	
	if ($result = $con->query ( $sql )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$data [] = $row;
		}
	}
	
	echo json_encode($data);
}

if(isset($_GET['saveDetail'])){
	
	$tempParams=array();
	parse_str($_GET['saveDetail'],$tempParams);
	print_r($tempParams);
	//print_r($_SESSION['addOrder']);
}






?>