<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';
date_default_timezone_set('Asia/Bangkok');
if (isset ( $_GET ['searchOrder'] )) {
	
	
	if(!isset( $_SESSION ['customerId'] )){
		unset($_SESSION['customerId']);
	}
	
	
	$sqlChk='select order_id,expire_status_add_box from customer_order where status_add_box=1 ';
	
	$customerChk=array();
	if ($result = $con->query ( $sqlChk )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$customerChk [] = $row;
		}
	}

	//check session_order_id is expiretime
	//print_r($_SESSION ['order_id']); //370
	$newSession=array();
	if(count($customerChk)>0){
		$index=0;
		foreach($customerChk as $val){
			//echo strtotime($val['expire_status_add_box']).'\n';
			//echo strtotime(date('Y-m-d H:i:s')).'<br/>'; //1468910932<br/>1468910932<br/>"" 1468909689
			if(strtotime(date('Y-m-d H:i:s'))> strtotime($val['expire_status_add_box'])){
				//update
				$sqlInsert = 'update customer_order set status_add_box=0 , expire_status_add_box=null where order_id=?';
				if ($stmt = $con->prepare ( $sqlInsert )) {
					$stmt->bind_param ( 's', trim ( $val['order_id'] ) );
					$stmt->execute ();
					unset($_SESSION ['session_order_id']);
					$newSession[]=$val['order_id'];
					$stmt->close ();
					//echo 'update customer_order set status_add_box=0 , expire_status_add_box=null where order_number = '.$val['order_id'];
	
				}
			}
		} //end foreach
		//new session
		if(count($newSession) > 0){
			unset($_SESSION ['order_id']);
			foreach($newSession as $val){
				$_SESSION ['order_id'][]=$val;
			}
			
		}
	}else{
		if(isset($_SESSION['customerId'])){
			unset($_SESSION['customerId']);
		}
		
	}
	
	$decode = base64_decode ( $_GET ['searchOrder'] );
	
	
	
	// check token
	// if($decode==date('Y-m-d h:i')){
	
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
					// print_r($ordersUnique);
					// $customerId=$ordersComplete[0]['customer_id'];
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
			
			foreach ( $params as $val ) {
				if ($stmt = $con->prepare ( $sql )) {
					$stmt->bind_param ( "s", trim ( $val ) );
					$stmt->execute ();
					$stmt->bind_result ( $customer_id,$orderId, $statusAddBox );
					$stmt->fetch ();
					$stmt->close ();
					
					if ($statusAddBox == 0) {
						$customerId=$customer_id;
						
						$sqlInsert = 'update customer_order set status_add_box=1 , expire_status_add_box=now() + INTERVAL 10 MINUTE where order_number=?';
						if ($stmt = $con->prepare ( $sqlInsert )) {
							$stmt->bind_param ( 's', trim ( $val ) );
							$stmt->execute ();
							$resMsg ['success'] [] = trim ( $val );
							
							$_SESSION ['order_id'] [] = $orderId;
							$stmt->close ();
						}
					} else {
						$resMsg ['error'] [] = trim ( $val );
					}
				}
			}
			
			if(count($_SESSION ['order_id'])>0){
				$_SESSION ['session_order_id']=session_id();
				$_SESSION['customerId']=$customerId;
			}
			
			if (count ( $resMsg ) > 0) {
				echo json_encode ( $resMsg );
			}
		}
	}
} // end add order get

if (isset ( $_GET ['getPackageStatus'] )) {
	$sql = 'SELECT packagestatusid,packagestatusname FROM `package_status` WHERE packagestatusid=2 ';
	// echo $sqlOrderComplete;
	$packageStatusName = array ();
	if ($result = $con->query ( $sql )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$packageStatusName [] = $row;
		}
	}
	echo json_encode ( $packageStatusName [0], true );
}

// SELECT * FROM `customer_order` WHERE order_status_code=7 and status_add_box=1
if (isset ( $_GET ['getCustomerOrderStatus'] )) {
	$sql = 'SELECT CO.*,C.*,COP.*,COPT.* FROM CUSTOMER_ORDER CO INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = CO.CUSTOMER_ID INNER JOIN CUSTOMER_ORDER_PRODUCT COP ON COP.ORDER_ID= CO.ORDER_ID INNER JOIN CUSTOMER_ORDER_PRODUCT_TRACKING COPT ON COPT.ORDER_ID= CO.ORDER_ID where CO.order_status_code=7 and CO.status_add_box=1';
	
	$customerOrder = array ();
	if ($result = $con->query ( $sql )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$customerOrder [] = $row;
		}
	}
	
	if (count ( $customerOrder ) > 0) {
		echo json_encode ( $customerOrder );
		// echo json_encode('no');
	} else {
		echo json_encode ( 'no' );
	}
}

if(isset($_GET['getCustomerOrderStatusBySession'])){
	/**
	 * check expire time
	 * 
	 */
	
	if(isset($_GET['session'])){
		
		//check duplidate sessoin
		$sessionArray=array_unique($_GET['session']);
		
		
		//print_r($sessionArray);
		
		
		
		$sql = 'SELECT CO.*,C.*,COP.*,COPT.* FROM CUSTOMER_ORDER CO INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = CO.CUSTOMER_ID INNER JOIN CUSTOMER_ORDER_PRODUCT COP ON COP.ORDER_ID= CO.ORDER_ID INNER JOIN CUSTOMER_ORDER_PRODUCT_TRACKING COPT ON COPT.ORDER_ID= CO.ORDER_ID WHERE  CO.ORDER_ID IN ';
		$criteria = '(';
		$i = 0;
		foreach ( $sessionArray as $val ) {
			$criteria .= $val;
			if ($i < count ( $_SESSION ['order_id'] ) - 1) {
				$criteria .= ',';
			}
				
			$i ++;
		}
		$criteria .= ')';
		
		
		
		$sqlChk='select order_id,expire_status_add_box from customer_order where order_id in '.$criteria.' and status_add_box=1 ';
		
		$customerChk=array();
		if ($result = $con->query ( $sqlChk )) {
			while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
				$customerChk [] = $row;
			}
		}
		
		//print_r($customerChk);
		$customerName='';
		
		
		
		if(count($customerChk)>0){
			$index=0;
			foreach($customerChk as $val){
				if(strtotime(date('Y-m-d H:i:s'))> strtotime($val['expire_status_add_box'])){
					//update 
					echo "sd";
					$sqlInsert = 'update customer_order set status_add_box=0 , expire_status_add_box=null where order_id=?';
					if ($stmt = $con->prepare ( $sqlInsert )) {
						$stmt->bind_param ( 's', trim ( $val['order_id'] ) );
						$stmt->execute ();
						unset($_SESSION ['session_order_id']);
						unset( $_SESSION ['order_id'][$index++]);
						unset($_SESSION['customerId']);
						$stmt->close ();
						//echo 'update customer_order set status_add_box=0 , expire_status_add_box=null where order_number = '.$val['order_id'];
						
					}
				}
			}

			$sql .= $criteria;
			
			
			if ($result = $con->query ( $sql )) {
				while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
					$customerName [] = $row;
					//echo 'sd';
				}
			}
		}
		
		
	}else{
		echo json_encode("n");
	}
	
	if(empty($customerName)){
		
		unset($_SESSION ['session_order_id']);
		unset( $_SESSION ['order_id']);
		unset($_SESSION['customerId']);
		//location.reload();
		//print_r($_SESSION ['order_id']);
	}
	
	
	
	echo json_encode($customerName);
}


if(isset($_GET['addOrderComplete'])){
	//select COPT.* from customer_order_product_tracking COPT INNER JOIN customer_order C ON C.order_id=COPT.order_id where  C.customer_id=35
	$criteria='';
	
	$customerId='';
	if(!empty($_GET['params'])){
		$customerId=$_GET['params'];
	}
	
	$order_id=array();
	if(isset($_SESSION['order_id'])){
		foreach($_SESSION['order_id'] as $val){
			$order_id[]=$val;
		}
	}
	
	if(count($order_id)>0){
		$criteria = '(';
		$i = 0;
		foreach ($order_id as $val ) {
			$criteria .= $val;
			if ($i < count ( $order_id ) - 1) {
				$criteria .= ',';
			}
				
			$i ++;
		}
		$criteria .= ')';
	}
	
	if(strlen(trim($customerId))>0){
		$sql = 'select COPT.tracking_no as tracking_no_copt, COPT.*,C.* from customer_order_product_tracking COPT INNER JOIN customer_order C ON C.order_id=COPT.order_id where  C.customer_id= '.$customerId.' and status_add_box <> 1 and C.order_id not in '.$criteria;
		// echo $sqlOrderComplete;
		$ordersComplete = array ();
		if ($result = $con->query ( $sql )) {
			while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
				$ordersComplete [] = $row;
			}
		}
// 		/echo count($ordersComplete);
		//$ordersComplete['size']=count($ordersComplete);
		echo json_encode($ordersComplete);
		
	}else{
		echo "No";
	}
	
	
	
}


if(isset($_GET['removeItemOrder'])){
	$resMsg=array();
	$order_id=$_GET['removeItemOrder'];
	if(count($order_id)>0 && isset($_SESSION ['order_id'])){
		$i=0;
		foreach($order_id as $val){
			
			
			/**
			 * 1. update status_add_box=0 and expire_status_add_box=null
			 * 2. check session
			 */
			
			//cleaar session
			
			$sqlUpdate= 'update customer_order set status_add_box=0 , expire_status_add_box=null where order_id=?';
			if ($stmt = $con->prepare ( $sqlUpdate )) {
				$stmt->bind_param ( 's', trim ( $val ) );
				$stmt->execute ();
				
				$resMsg ['success'] [] = trim ( $val );
				$stmt->close ();
				
				if(count($_SESSION['order_id'])>0){
					$j=0;
					foreach($_SESSION['order_id'] as $order_id){
						if($val==$order_id){
							unset($_SESSION['order_id'][$j]);
							//echo $order_id.'->'.$val;
							//print_r($_SESSION['order_id']);
						}
						$j++;
					}
				}
			}
			
			
		}
		
	}
	
	echo json_encode($resMsg);
}


if(isset($_GET['address'])){
	$customer_id=trim($_GET['address']);
	/**
	 * 1 check customer id have 1 address
	 * 2. return 1 or 2 
	 */
	
	$arrAddress=array();
	$returnJsonArray=array();
	
	$sql='select address_id, address_name,line_1,city,country,zipcode,phone from customer_address where customer_id=?';
	if ($stmt = $con->prepare ( $sql )) {
		$stmt->bind_param ( 's', trim ( $customer_id ) );
		$stmt->execute ();
		$stmt->bind_result ( $address_id,$address_name,$line_1,$city,$country,$zipcode,$phone );
		while($stmt->fetch ()){
			
			$arrAddress['address_id']=$address_id;
			$arrAddress['address_name']=$address_name;
			$arrAddress['line_1']=$line_1;
			$arrAddress['city']=$city;
			$arrAddress['country']=$country;
			$arrAddress['zipcode']=$zipcode;
			$arrAddress['phone']=$phone;
			$returnJsonArray[]=$arrAddress;
		}
	
		//$resMsg ['success'] [] = trim ( $val );
		
		$stmt->close ();
		
		//print_r($returnJsonArray);
		echo json_encode($returnJsonArray);
	
		
	}
	
}





?>