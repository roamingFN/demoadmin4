<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';
include '../configPath.php';
include './email/send_mail.php';
date_default_timezone_set ( 'Asia/Bangkok' );



if(isset($_GET['searchTracking'])){
	$tempParams=array();
	parse_str($_GET['searchTracking'],$tempParams);
	$orderProductId=array();
	$customerId='';
	$id=$_GET['id'];
	
	$sqlCriterial='';
	$flag=false;
	if(!empty($tempParams['searchOrderNumber'])||!empty($tempParams['searchTrackingNo'])){
		$flag=true;
		
	}

	$tracingSql='';	
	if(isset($_GET['trackingRemove'])){
		$tracking = $_GET ['trackingRemove'];
		if( strlen(trim($tracking))>0){
			$tracingSql = ' and copt.order_product_tracking_id not in(' . $tracking. ') ';
			
		}
		
		
	}

	if($flag){
		$sql='select copt.*,co.*,cop.*,copt.tracking_no tracking_no_copt from customer_order_product_tracking copt
inner join customer_order co on co.order_id=copt.order_id
inner join customer_order_product cop on cop.order_id= co.order_id
where copt.statusid=1
and co.customer_id='.$id.'	
' . $tracingSql . '
and copt.order_product_tracking_id not in (select order_product_tracking_id from package_detail)		
and copt.masterflg=1
and (co.order_number like "%'.$tempParams['searchOrderNumber'].'%" and copt.tracking_no like "%'.$tempParams['searchTrackingNo'].'%")
group by copt.tracking_no,copt.order_id,copt.order_product_id
order by copt.last_edit_date desc';
		
// 		$sql = 'select *,copt.tracking_no as tracking_no_copt from customer_order co
// inner join customer_order_product cop on cop.order_id=co.order_id
// inner join customer_order_product_tracking copt on copt.order_product_id=cop.order_product_id
// where co.customer_id='.$id.'
// ' . $tracingSql . '
// and copt.order_product_id not in (select order_product_id from package_detail)
		
// and copt.statusid = 1 ';
		
			//echo $sql;
		
		$data=array();
		
		if ($result = $con->query ( $sql )) {
			while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
				$data [] = $row;
		
			}
		}
		echo json_encode($data);
	}else{
		$sql='select copt.*,co.*,cop.*,copt.tracking_no tracking_no_copt from customer_order_product_tracking copt
inner join customer_order co on co.order_id=copt.order_id
inner join customer_order_product cop on cop.order_id= co.order_id
where copt.statusid=1
and co.customer_id='.$id.'
' . $tracingSql . '
and copt.order_product_tracking_id not in (select order_product_tracking_id from package_detail)
and copt.masterflg=1
and (co.order_number like "%'.$tempParams['searchOrderNumber'].'%" and copt.tracking_no like "%'.$tempParams['searchTrackingNo'].'%")
group by copt.tracking_no,copt.order_id,copt.order_product_id
order by copt.last_edit_date desc';
		$data=array();
		
		if ($result = $con->query ( $sql )) {
			while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
				$data [] = $row;
		
			}
		}
		echo json_encode($data);
	}// end if 
	
	

}


if(isset($_GET['getAddShipping'])){
	$orderNumber=$_GET['getAddShipping'];

	if(count($orderNumber)>0){
		$sql='select *
from customer_order co
inner join customer_address ca on ca.customer_id=co.customer_id
where co.order_number in ("' . implode ( '","', $orderNumber) . '")
group by ca.address_id';
		//echo $sql;
		
		$data=array();
		if ($result = $con->query ( $sql )) {
			while($row = $result->fetch_array ( MYSQL_ASSOC )){
				$data[]=$row;
			}
		}
	}
	
	if(count($data)){
		echo json_encode($data);
	}else{
		echo json_encode('n');
	}
	
}

if(isset($_GET['getHowShipping'])){
	$orderNumber=$_GET['getHowShipping'];
	
	if(count($orderNumber)>0){
		$sql='select *
from customer_order co
inner join customer_order_shipping cos on cos.order_id=co.order_id
inner join website_transport wt on wt.transport_id=cos.order_shipping_th_option				
where co.order_number in ("' . implode ( '","', $orderNumber) . '") group by wt.transport_id';
		$data=array();
		if ($result = $con->query ( $sql )) {
			while($row = $result->fetch_array ( MYSQL_ASSOC )){
				$data[]=$row;
			}
		}
	}
	
	if(count($data)){
// 		echo "<pre>";
// 		print_r($data);
// 		echo "</pre>";
		echo json_encode($data);
	}else{
		echo json_encode('n');
	}
}


if(isset($_GET['confirm'])){
	$id=$_GET['confirm'];
	$sql='update package set statusid=3 where packageid=?';
	$stmt=$con->prepare($sql);
	$stmt->bind_param('i',$id);
	$stmt->execute();
	
	
	//insert to total_message_log
	
	
	
	//send mail
	
	/**
	 * data:
	 * firstname, lastname
	 * packageno
	 * user login
	 * email customer,code
	 * [from,to, subject]
	 */
	parse_str ( $_GET ['packageProduct'], $tempParams );
	$sql='delete from package_product where packageid=?';
	$stmt=$con->prepare($sql);
	$stmt->bind_param('i',$id);
	$stmt->execute();
	
	/*เพิ่มเติม พี่สร้าง 2016/11/12 table ใหม่ package_product*/
	for($i = 0; $i < count ( $tempParams ['orderId'] ); ++ $i) {
		$sqlSavePackageProduct='insert into package_product(packageid,product_id,tracking_no) values(
						'.$id.',
						'.$tempParams['product_id'][$i].',
						"'.$tempParams['tracking_no'][$i].'")';
		//echo $sqlSavePackageProduct;
		$con->query ( $sqlSavePackageProduct );
	}
	
	
	$sql='select 
p.packageid,
p.packageno,
c.customer_id,
c.customer_firstname,
c.customer_lastname,
c.customer_code,
c.customer_email
from package p
inner join customer c on c.customer_id=p.customerid
where p.packageid=?';
	
	$stmt = $con->prepare ( $sql );
	$stmt->bind_param ( 'i', $id );
	$stmt->execute ();
	$result = $stmt->get_result ();
	
	$data = array ();
	while ( $row = $result->fetch_assoc () ) {
		// echo json_encode($row);
		$data  = $row;
	}
	
// 	echo "<pre>";
// 	print_r($data);
// 	echo "</pre>";
	
	$param=array();
	if(!empty($data)){
		$param['packageid']=$data['packageid'];
		$param['packageno']=$data['packageno'];
		$param['customer_id']=$data['customer_id'];
		$param['user_id']=$_SESSION['USERID'];
		$param['customer_fullname']=$data['customer_firstname'].' '.$data['customer_lastname'];
		$param['customer_code']=$data['customer_code'];
		$param['customer_email']=$data['customer_email'];
		$param['subject']="Order2easy Package";
		$param['admin']=$_SESSION['ID'];
	
	}
	
// 		echo "<pre>";
// 		print_r($param);
// 		echo "</pre>";
	
	
	if(sendMail($param,$_path_frontend,$con)){
		
		echo json_encode('Y');
	}else{
		echo json_encode('N');
	}
	
	
	
	
}



if (isset ( $_GET ['saveDetail'] )) {
	$tempParams = array ();
	parse_str ( $_GET ['saveDetail'], $tempParams );
	
	$orderId = array_unique ( $tempParams ['orderId'] );
	$order_product_Id = array_unique ( $tempParams ['order_product_id'] );
	
	/*$sqlSumFirst='select  sum(cop.first_unitquantity) as _sum_first_unitquantity
	from customer_order_product cop
	where order_id in ('.implode(',', $orderId).')';*/
	
	//แก้ sql 20161119
	$sqlSumFirst='select  sum(cop.first_unitquantity) as _sum_first_unitquantity
 from customer_order_product cop
 where order_product_id in ('.implode(',', $order_product_Id).')';
	
	
	$result = $con->query($sqlSumFirst);
	$row = $result->fetch_array ( MYSQL_ASSOC );
	$totalWant=$row['_sum_first_unitquantity'];
	
	$trackingId=$tempParams['order_product_tracking_id'];
	//echo 'total_want:'.$sqlSumFirst;
	
	
	/*$sqlTotalQuantity='select sum(copt.received_amount) as _quantity
from customer_order_product_tracking copt
where order_product_tracking_id in('.implode(',', $trackingId).')';*/
	
	
	//แก้ sql 20161119
	$sqlTotalQuantity='select sum(copt.total_in_tracking) as _quantity
from customer_order_product_tracking copt
where order_product_tracking_id in('.implode(',', $trackingId).')';
	//echo 'TotalQualtity:'.$sqlTotalQuantity;
	
	$result = $con->query($sqlTotalQuantity);
	$row = $result->fetch_array ( MYSQL_ASSOC );
	$totalQuantity=$row['_quantity'];
	
	
	//check update 446,447
	if(isset($tempParams['update'])){
		$sql='delete from package_detail where packageid=?';
		$stmt=$con->prepare($sql);
		$stmt->bind_param('i',$tempParams['id']);
		$stmt->execute();
	
	
		//delete all by package
		$sql='delete  from package where packageid=?';
		$stmt=$con->prepare($sql);
		$stmt->bind_param('i',$tempParams['id']);
		$stmt->execute();
		
		//delete customer_statement
		$sql='delete from customer_statement where packageid=?';
		$stmt=$con->prepare($sql);
		$stmt->bind_param('i',$tempParams['id']);
		$stmt->execute();
		
		$sql='delete from package_product where packageid=?';
		$stmt=$con->prepare($sql);
		$stmt->bind_param('i',$tempParams['id']);
		$stmt->execute();
	}
	
// 	echo "<pre>";
// 	print_r($tempParams);
// 	echo "</pre>";
	// print_r($orderNumber);
	
	/**
	 * 1.
	 * get pckageno auto
	 * 2. insert into package and get current id from package and insert into
	 */
	$numb = "";
	
	if ($result = $con->query ( 'select packageno from package  ORDER BY packageno DESC LIMIT 1 ' )) {
		$row = $result->fetch_array ( MYSQL_ASSOC );
		// echo $row['packageno'];
		// echo '<br/>';
		if (! empty ( $row )) {
			$numb = 'P' . date ( "y" ) . str_pad ( ( int ) (substr ( $row ['packageno'], 3, strlen ( $row ['packageno'] ) )) + 1, 6, "0", STR_PAD_LEFT );
		} else {
			$numb = 'P' . date ( "y" ) . '000001';
		}
	}
	
	$totalCount=($tempParams['total_count']>0)?$tempParams['total_count']:$tempParams['total_ordernumber'];
	$total_miss=$totalWant-$totalQuantity;
	//echo $total_miss;
	
	$sqlInsert = 'insert into package(
			packageno,
			statusid,
			createdate,
			customerid,
			amount, 
			amount_rack, 
			amount_box, 
			amount_pass,
			amount_thirdparty, 
			amount_other, 
			amount_other_specify,
			total,
			total_count,
			remark,
			total_tracking,
			shipping_address,
			shippingid,
			total_m3,
			total_weight,
			total_ordernumber,
			total_want,
			total_quantity,
			total_miss
	)
	values("' . $numb . '",2,NOW(),' . $tempParams ['cutomerIdHiden'] . ',' . floatval(str_replace(",","",$tempParams ['amount'])) . ',' . floatval(str_replace(",","",$tempParams ['amount_boxchina'])) . ',' . floatval(str_replace(",","",$tempParams ['amount_boxpackage'])) . ',' . floatval(str_replace(",","",$tempParams ['amount_pass'])) . ',' . floatval(str_replace(",","",$tempParams ['amount_thirdparty'])) . ',' . floatval(str_replace(",","",$tempParams ['other_specifiy2'])) . ',"' . $tempParams ['amount_other2'] . '",' . ($tempParams ['total']+$tempParams ['amount']) . ',' . $totalCount . ',"' . $tempParams ['remark'] . '",' . $tempParams ['total_tracking'] . ',' . $tempParams ['addressid'] . ',' . $tempParams ['shipingid'] . ',' . $tempParams ['total_m3'] . ',' . $tempParams ['total_weight'] . ',' . count ( $orderId ) . ','.$totalWant.','.$totalQuantity.','.($totalWant-$totalQuantity).')';
	//echo $sqlInsert;
	
	if ($con->query ( $sqlInsert ) === TRUE) {
		
		/*
		 * 1. select current package
		 */
		
		$sqlPackageId = 'select packageid,packageno from package where packageno="' . $numb . '"';
		// P16000002
		// $sqlPackageId='select packageid,packageno from package where packageno="P16000002"';
		//echo $sqlPackageId;
		$result = $con->query ( $sqlPackageId );
		$row = $result->fetch_array ( MYSQL_ASSOC );
		$packageId = $row ['packageid'];
		$packageNo = $row ['packageno'];
		
		
		if (count ( $tempParams ['orderId'] ) > 0) {

			for($i = 0; $i < count ( $tempParams ['orderId'] ); ++ $i) {
				$sqlInsertPakcageDetail = 'insert into package_detail(
										packageid,
										packageorder,
										order_id,
										order_product_id,
										order_product_tracking_id,
										tracking_no)
										values(
											' . $packageId . ',
											' . ($i + 1) . ',
											' . $tempParams ['orderId'] [$i] . ',
											' . $tempParams ['order_product_id'] [$i] . ',
											' . $tempParams ['order_product_tracking_id'] [$i] . ',
											"' . $tempParams ['tracking_no'] [$i] . '"
										)'; 				
				$con->query ( $sqlInsertPakcageDetail );
				
				/*เพิ่มเติม พี่สร้าง 2016/11/12 table ใหม่ package_product*/
				$sqlSavePackageProduct='insert into package_product(packageid,product_id,tracking_no) values(
						'.$packageId.',
						'.$tempParams['product_id'][$i].',
						"'.$tempParams['tracking_no'][$i].'")';
				//echo $sqlSavePackageProduct;
				$con->query ( $sqlSavePackageProduct );
				//product_id

				//echo $sqlInsertPakcageDetail;
				

			/*$sql='insert into customer_statement (customer_id,statement_name,statement_date,credit,packageid)
values ('.$tempParams['cutomerIdHiden'].',"ค่าขนส่ง  กล่อง'.$numb.'",NOW(),'.($tempParams ['total']+$tempParams ['amount']).','.$packageId.')';*/
			}
			//$con->query ( $sql );
			
			
			
			
			echo json_encode(array('status'=>'Y','packageId'=>$packageId));
		}
		
// 		echo "<pre>";
// 		print_r ( $row );
// 		echo "</pre>";
	}
}

if (isset ( $_GET ['addTrackingId'] )) {
	
	$sql = 'select *,copt.tracking_no as tracking_no_copt,copt.*,coss.*,cop.*,copm.total_baht from customer_order co
inner join customer_order_product cop on cop.order_id=co.order_id
inner join customer_order_product_tracking copt on copt.order_product_id=cop.order_product_id
inner join customer_order_shipping coss on coss.order_id=co.order_id
left join customer_order_paymore copm on copm.order_id =copt.order_id and  copm.order_product_id = copt.order_product_id  			
where copt.order_product_tracking_id in (' . implode ( ',', $_GET ['addTrackingId'] ) . ')';
	
	//echo $sql;
	$data = array ();
	if ($result = $con->query ( $sql )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$data [] = $row;
		}
	}
	
// 	echo "<pre>";
// 	print_r($data);
// 	echo "</pre>";
	echo json_encode ( $data );
}

if (isset ( $_GET ['getOrderByCustomerId'] )) {
	$response = array ();
	$id=$_GET['getOrderByCustomerId'];
	
	$orderIdArray = array ();
	$sql = 'select c.customer_id,c.customer_firstname,c.customer_lastname,c.customer_code,co.customer_id,co.order_id,co.order_number,cop.order_id,cop.order_product_id,copt.*
from customer c
inner join customer_order co on co.customer_id=c.customer_id
inner join customer_order_product cop on cop.order_id=co.order_id
inner join customer_order_product_tracking copt on copt.order_product_id=cop.order_product_id
where copt.order_product_tracking_id not in (select order_product_tracking_id from package_detail)
and copt.masterflg=1	
and copt.statusid = 1 
and c.customer_id='.$id;
	
	//and copt.order_product_id not in (select order_product_id from package_detail)
	
	//echo $sql;
	
	$data = array ();
	if ($result = $con->query ( $sql )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$data [] = $row;
		}
	}
	if (count ( $data ) > 0) {
		$response ['data'] = $data;
		foreach ( $data as $val ) {
			$orderIdArray [] = $val ['order_id'];
		}
	}
	// print_r($orderIdArray);
	
	// select customer adddress
	$sqlCustomerAddress = 'select ca.* from customer_address ca
where ca.customer_id='.$id;

	$customerAddress = array ();
	
	if ($result = $con->query ( $sqlCustomerAddress )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$customerAddress [] = $row;
		}
	}
	
	if (count ( $customerAddress ) > 0) {
		$response ['address'] = $customerAddress;
	}
	
	// select customer shipping
	
	$customerOrderShippingArray = array ();
	$sqlCustomerOrderShipping = 'select cop.*,wt.* from customer_order_shipping cop inner join website_transport wt on wt.transport_id=cop.order_shipping_th_option
   where cop.order_id in (' . implode ( ",", $orderIdArray ) . ')';
	if ($result = $con->query ( $sqlCustomerOrderShipping )) {
		
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$customerOrderShippingArray [] = $row;
		}
	}
	if (count ( $customerOrderShippingArray ) > 0) {
		$response ['shipping'] = $customerOrderShippingArray;
	}
	// echo "<pre>";
	// print_r($response);
	// echo "</pre>";
	
	if (count ( $data ) > 0) {
		echo json_encode ( $response );
	} else {
		echo json_encode ( 'no' );
	}
}

if (isset ( $_GET ['addOrderComplete'] )) {
	
	if(isset($_GET['id'])){
		$id = $_GET ['id'];
		
		$tracking = '';
		$tracingSql = '';
		if (isset ( $_GET ['trackingRemove'] )) {
			$tracking = $_GET ['trackingRemove'];
			$tracingSql = ' and copt.order_product_tracking_id not in(' . implode ( ',', $tracking ) . ') ';
		}
		$sql = 'select co.*,cop.*,copt.*,copt.tracking_no as tracking_no_copt,copm.total_baht from customer_order co
inner join customer_order_product cop on cop.order_id=co.order_id
inner join customer_order_product_tracking copt on copt.order_product_id=cop.order_product_id
left join customer_order_paymore copm on copm.order_id =copt.order_id and  copm.order_product_id = copt.order_product_id 
where co.customer_id=?
' . $tracingSql . '
and copt.order_product_tracking_id not in (select order_product_tracking_id from package_detail)
and copt.masterflg=1	
and copt.statusid = 1 
order by copt.last_edit_date desc';
		//and copt.order_product_id not in (select order_product_id from package_detail)
		
		//echo $sql;
		$stmt = $con->prepare ( $sql );
		$stmt->bind_param ( 'i', $id );
		$stmt->execute ();
		$result = $stmt->get_result ();
		
		$data = array ();
		while ( $row = $result->fetch_assoc () ) {
			// echo json_encode($row);
			$data [] = $row;
		}
		
		if (count ( $data ) > 0) {
			echo json_encode ( $data );
		} else {
			echo json_encode ( array (
					'n'
			) );
		}
	}else{
		echo json_encode ( array ('n'));
	}
	
	
}

if (isset ( $_GET ['searchOrder'] )) {
	$criteria = '';
	if (isset ( $_GET ['param'] ) && $_GET ['param'] != 'Search') {
		$params = trim ( $_GET ['param'] );
		/**
		 * where
		 */
		$criteria .= ' and(';
		$criteria .= ' order_number like "%' . $params . '%"';
		$criteria .= ' OR customer_firstname like "%' . $params . '%"';
		$criteria .= ' OR customer_lastname like "%' . $params . '%"';
		$criteria .= ' OR customer_code like "%' . $params . '%"';
		$criteria .= ' OR total_tracking like "%' . $params . '%"';
		$criteria .= ' OR product_quantity like "%' . $params . '%"';
		$criteria .= ' OR product_available like "%' . $params . '%"';
		$criteria .= ' OR received_complete_date like "%' . $params . '%"';
		$criteria .= ')';
	}

	$sql = 'select co.*,c.*,cop.*,copt.* from customer_order co 
inner join customer c on c.customer_id = co.customer_id 
inner join customer_order_product cop on cop.order_id=co.order_id
inner join customer_order_product_tracking copt on  copt.order_product_id=cop.order_product_id
where co.order_status_code=7
and copt.masterflg=1
	and copt.statusid = 1

and copt.order_product_tracking_id not in (select order_product_tracking_id from package_detail)
' . $criteria . '
group by co.order_id
order by c.customer_firstname';
	//and copt.order_product_id not in (select order_product_id from package_detail)
	// echo $sqlOrderComplete;
	//echo $sql;
	$ordersComplete = array ();
	if ($result = $con->query ( $sql )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$ordersComplete [] = $row;
		}
		
		// }
		if (isset ( $_SESSION ['order_id'] )) {
			$i = 0;
			foreach ( $_SESSION ['order_id'] as $order_id ) {
				foreach ( $ordersComplete as $key => $val ) {
					if ($val == $order_id) {
						unset ( $_SESSION ['order_id'] [$i] );
					}
				}
				$i ++;
			}
		}
		echo json_encode ( $ordersComplete, true );
	} else {
		echo "no";
	}
}
?>