<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';
date_default_timezone_set('Asia/Bangkok');

if(isset($_GET['getBoxDetail'])){
	$sql='select packageid from package where packageid=?';
	$stmt=$con->prepare($sql);
	$stmt->bind_param('i',trim($_GET['getBoxDetail']));
	$stmt->execute();
	$result=$stmt->get_result();
	
	$packageid=array();
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$packageid=$row;
	}
	$stmt->close();
	
	
	$sql='select ps.*,p.* from package_send ps inner join package p on p.packageid= ps.packageid where ps.packageid=? order by ps.key_date desc';
	$stmt=$con->prepare($sql);
	$stmt->bind_param('i',$packageid['packageid']);
	$stmt->execute();
	$result=$stmt->get_result();
	
	$packageSendArr=array();
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$packageSendArr[]=$row;
	}
// 	echo "<pre>";
// 	print_r($packageSendArr);
	
// 	echo "</pre>";

	if(count($packageSendArr)>0){
	     echo json_encode($packageSendArr);
	}else{
		/*initial box*/
		echo json_encode(array('i'));
	}
	
	$stmt->close();
}


if(isset($_GET['getDetail'])){
	$sql='select p.*,co.*,pd.*,c.*,wt.*,ps.* from package p
inner join package_detail pd on pd.packageid=p.packageid
inner join customer_order co on co.order_id=pd.order_id
inner join package_status ps on ps.packagestatusid = p.statusid
inner join customer c on c.customer_id=co.customer_id
inner join website_transport wt on wt.transport_id=p.shippingid
where p.packageid = ? group by co.order_number';
	
	//$sql='SELECT P.*,C.*,PS.*,WT.*,PSE.* FROM PACKAGE P INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = P.CUSTOMERID INNER JOIN PACKAGE_STATUS PS ON PS.PACKAGESTATUSID=P.STATUSID INNER JOIN WEBSITE_TRANSPORT WT ON WT.TRANSPORT_ID=P.SHIPPINGID INNER JOIN PACKAGE_SEND PSE ON PSE.SENDID = P.SENDID WHERE P.PACKAGENO = ?';
	
	$stmt=$con->prepare($sql);
	$stmt->bind_param('i',trim($_GET['getDetail']));
	$stmt->execute();
	$result=$stmt->get_result();
	
	$data=array();
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$data[]=$row;
	}
	
	
// 	echo "<pre>";
// 	print_r($data);
// 	echo "</pre>";
echo json_encode($data);
	
	
}


if(isset($_GET['chkBoxRemain'])){
	
	$sql='select p.* from package p
where p.packageno=?';
	//and (p.total_count-p.total_count_sent) >=?
	
	$stmt=$con->prepare($sql);
	$stmt->bind_param('s',trim($_GET['id']));
	$stmt->execute();
	
	
	//result
	$result=$stmt->get_result();
	$num_of_rows = $result->num_rows;
	
	$data=array();
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$data[]=$row;
	}
	$stmt->fetch();
	$stmt->close();
	
// 	echo "<pre>";
// 	print_r($data);
// 	echo "</pre>"; 
	if($num_of_rows<=0){
		echo json_encode("n");
	}else{
		$sql='select p.*,pd.*,copt.*,p.packageid as pid from package p
inner join package_detail pd on pd.packageid=p.packageid
inner join customer_order_product_tracking copt on copt.order_product_id=pd.order_product_id
where p.packageno=?';
		$stmt=$con->prepare($sql);
		$stmt->bind_param('s',trim($_GET['id']));
		$stmt->execute();
		
		//result
		$result=$stmt->get_result();
		
		$data=array();
		while ($row = $result->fetch_assoc()) {
			//echo json_encode($row);
			$data[]=$row;
		}
		$stmt->fetch();
		$stmt->close();
		
		//check package_send by packageid
		$sqlGetPackageSend='select * from package_send where packageid=?';
		$stmt=$con->prepare($sqlGetPackageSend);
		$stmt->bind_param('i',$data[0]['pid']);
		$stmt->execute();
		
		//result
		$result=$stmt->get_result();
		
		$dataPackageSend=array();
		while ($row = $result->fetch_assoc()) {
			//echo json_encode($row);
			$dataPackageSend[]=$row;
		}
		
		$stmt->fetch();
		$stmt->close();
		//print_r($dataPackageSend);
		
		if(count($dataPackageSend)>0){
			
			//trackingno_thai 11,222
			foreach($dataPackageSend as $val){
				
			}
			$strArrTrackingNo=explode(",",$val['trackingno_thai']);
			//print_r($strArrTrackingNo);
			
			//and copt.tracking_no <> "111222";
				$sql='select p.*,pd.*,copt.*,p.packageid as pid from package p
	inner join package_detail pd on pd.packageid=p.packageid
	inner join customer_order_product_tracking copt on copt.order_product_id=pd.order_product_id
	where p.packageno=? and copt.tracking_no not in("'.implode('","',$strArrTrackingNo).'")';
				//echo $sql;
			$stmt=$con->prepare($sql);
			$stmt->bind_param('s',trim($_GET['id']));
			$stmt->execute();
			
			//result
			$result=$stmt->get_result();
			
			$data=array();
			while ($row = $result->fetch_assoc()) {
				//echo json_encode($row);
				$data[]=$row;
			}
			$stmt->fetch();
			$stmt->close();

		}
		
		$dataResponse=array();
		for($i=0;$i<intval($_GET['chkBoxRemain']);++$i){
			$dataResponse[]=$data[$i];
		}
		
		
// 			echo "<pre>";
// 			print_r($dataResponse);
// 			echo "</pre>";
		
		echo json_encode($dataResponse);
		
	}

}


if(isset($_POST['insertDeail'])){
	$flag=false;
	$packageid=$_POST['packageid'];
	$send_date=$_POST['datepickerCreate'];
	$total_send=$_POST['total_box'];
	$send_user=$_POST['send_user'];
	$trackingno_thai=$_POST['tracking_th'];
	$send_remark=$_POST['remark'];
	$add_user=$_POST['send_user'];
	
	/**
	 * 1 select send total_count,total_count_sent
	 */
	$sqlGetTotal='select packageid,total_count,total_count_sent from package where packageid=?';
	$stmt=$con->prepare($sqlGetTotal);
	$stmt->bind_param('i',$packageid);
	$stmt->execute();
	$result=$stmt->get_result();
	
	$dataTotal=array();
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$dataTotal=$row;
	}
	
// 	echo '<pre>';
// 	print_r($dataTotal);
// 	echo '</pre>';
	
// 	$currentTotalCount=$dataTotal['total_count']-$total_send;

	
	
// 	for($i=0;$i<count($send_date);++$i){
// 		if(($dataTotal['total_count_sent']+$total_send[$i])>$dataTotal['total_count']){
// 			echo $total_send[$i];
// 			$flag=false;
// 		}else{
// 			$sql='INSERT INTO package_send
// 			(packageid,
// 			 send_date,
// 			 total_send,
// 			 send_user,
// 			 key_date,
// 			 trackingno_thai,
// 			 send_remark,
// 			 add_user)
// 			 VALUES (
// 				'.$packageid.',
// 				"'.$send_date[$i].'",
// 				'.$total_send[$i].',
// 				"'.$send_user.'",
// 				 NOW(),
// 				"'.implode(',', $trackingno_thai).'",
// 				"'.$send_remark[$i].'",
// 				"'.$add_user.'")';
// 			//print_r($trackingno_thai);
				
// 			echo $sql;
// 			if($con->query($sql) === TRUE){
// 				$last_id = $con->insert_id;
// 				$sqlUpdate='update package set total_count_sent='.($dataTotal['total_count_sent']+$total_send[$i]).',sendid='.$last_id.' where packageid='.$packageid;
// 				//echo $sqlUpdate;
// 				if($con->query($sqlUpdate) === TRUE){
// 					$flag=true;
// 				}else{
// 					$flag=false;
// 				}
					
// 			}else{
// 				$flag=false;
// 			}
				
// 		}

	if(($dataTotal['total_count_sent']+$total_send[0])>$dataTotal['total_count']){
		$flag=false;
	}else{
		$sql='INSERT INTO package_send
		(packageid,
		 send_date,
		 total_send,
		 send_user,
		 key_date,
		 trackingno_thai,
		 send_remark,
		 add_user)
		 VALUES (
			'.$packageid.',
			"'.$send_date[0].'",
			'.$total_send[0].',
			"'.$send_user.'",
			 NOW(),
			"'.implode(',', $trackingno_thai).'",
			"'.$send_remark[0].'",
			"'.$_SESSION['ID'].'")';
		//print_r($trackingno_thai);

		//echo $sql;
		if($con->query($sql) === TRUE){
			$last_id = $con->insert_id;
			$sqlUpdate='update package set total_count_sent='.($dataTotal['total_count_sent']+$total_send[0]).',sendid='.$last_id.',paydate=now() where packageid='.$packageid;
			//echo $sqlUpdate;
			if($con->query($sqlUpdate) === TRUE){
				$flag=true;
			}else{
				$flag=false;
			}

		}else{
			$flag=false;
		}
	}
		
		
		

		
		
// 	}
	
	echo "<script>window.location.href = './index.php'</script>";
	
// 	if(flag){
// 		//echo json_encode(array('y'));
// 	}else{
// 		//echo json_encode(array('n'));
// 	}
	
	
}


if(isset($_GET['clearPackageSend'])){
	
	$sendid=$_GET['clearPackageSend'];
	
	$sqlGetTotalSend='select sendid,packageid,total_send from package_send where sendid=?';
	$stmt=$con->prepare($sqlGetTotalSend);
	$stmt->bind_param('i',$sendid);
	$stmt->execute();
		
	//result
	$result=$stmt->get_result();
		
	$data=array();
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$data=$row;
	}
	$stmt->fetch();
	$stmt->close();
	
	
	$sqlTotal='select total_count_sent from package where packageid=?';
	
	$stmt=$con->prepare($sqlTotal);
	$stmt->bind_param('i',$data['packageid']);
	$stmt->execute();
	
	//result
	$result=$stmt->get_result();
	
	$dataTotal=array();
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$dataTotal=$row;
	}
	$stmt->fetch();
	$stmt->close();
	
	$totalCount=$dataTotal['total_count_sent']-$data['total_send'];
	
	
	
	$sqlUpdatePackage='update package set total_count_sent = '.$totalCount.' where packageid='.$data['packageid'];
	//echo $sqlUpdatePackage;
	$con->query($sqlUpdatePackage);
	
	
	$sql='delete from package_send where sendid=?';
	$stmt=$con->prepare($sql);
	$stmt->bind_param('i',$_GET['clearPackageSend']);
	$stmt->execute();
	echo json_encode(array('Y'));
}

?>