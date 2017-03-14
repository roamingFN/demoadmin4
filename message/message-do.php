<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}

include '../database.php';
date_default_timezone_set ( 'Asia/Bangkok' );

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'activeLink':activeLink($_GET['orderId']); break;
		case 'activeLinkPk':activeLinkPk($_GET['packageid']); break;
	}

}

function activeLink($param=''){
	if($param!=0 || trim($param)>0){
	   /**
	    * update total_message_log-> active_link = 1 where eid =$param
	    */
		$sql='update total_message_log set active_link =0 where order_id ='.$param;
		mysql_query($sql);
		echo json_encode(mysql_insert_id());
	}
}

function activeLinkPk($param=''){
	if($param!=0 || trim($param)>0){
		/**
		 * update total_message_log-> active_link = 1 where eid =$param
		 */
		$sql='update total_message_log set active_link =0 where packageid ='.$param;
		mysql_query($sql);
		echo json_encode(mysql_insert_id());
	}
}

//for order only
//
if(isset($_POST['frmMsgSend'])){
/*
	1. Get variable from post method (frmMsgSend:$.trim(content),id:orderId,opt:opt})
		1.1 content
		1.2 order id , packageid
		1.3 option : opt
	2.  if ( opt == 'oid') then sql for oid
		else then sql for pid
	3. insert into total message log
		3.1  order_id,order_product_id,customer_id,topup_id,user_id,subject,content,message_date,active_link
	4. get last eid and select data from  total message log table where eid
	5 . return to json encode to invoke.

Note (mysql_real_escape_string)
 */
	$userId = $_SESSION['USERID'];
	$customerId=$_POST['cus_id'];
	$message = $_POST['frmMsgSend'];
	$Id = $_POST['id'];
	$opt = $_POST['opt'];

	if ($opt == 'oid') {
		$sql='insert into total_message_log (order_id,order_product_id,customer_id,topup_id,user_id,subject,content,message_date,active_link)';
		$sql.='values('.$Id.',0,'.$customerId.',0,'.$userId.',"'.mysqli_real_escape_string($con,$message).'","'.mysqli_real_escape_string($con,$message).'",NOW(),1)';
		$result = $con->query ( $sql );
		$eid = $con->insert_id;
		$sql_eid = 'select tml.*, us.email from total_message_log tml INNER JOIN user us on tml.user_id = us.userid where eid = '.$eid;
		$resultEid = $con->query( $sql_eid );
		$totalMessageArr=array();
		while($row = mysqli_fetch_assoc($resultEid)){
			$totalMessageArr[]=$row;
		}

		if(!empty($resultEid)){
			echo json_encode($totalMessageArr);
		}else{
			echo "n";
		}
	} else { 
		$sql='insert into total_message_log (packageid,order_product_id,customer_id,topup_id,user_id,subject,content,message_date,active_link)';
		$sql.='values('.$Id.',0,'.$customerId.',0,'.$userId.',"'.mysqli_real_escape_string($con,$message).'","'.mysqli_real_escape_string($con,$message).'",NOW(),1)';
		$result = $con->query ( $sql );
		$eid = $con->insert_id;
		$sql_eid = 'select tml.*, us.email from total_message_log tml INNER JOIN user us on tml.user_id = us.userid where eid = '.$eid;
		$resultEid = $con->query( $sql_eid );
		$totalMessageArr=array();
		while($row = mysqli_fetch_assoc($resultEid)){
			$totalMessageArr[]=$row;
		}

		if(!empty($resultEid)){
			echo json_encode($totalMessageArr);
		}else{
			echo "n";
		}
	}
	


} /// end message send new

if(isset($_POST['frmMsgSend1'])){
	$customerId=$_SESSION['CX_login_id'];
	$message=$_POST['frmMsgSend'];
	$orderId=$_POST['orderId'];
	$sql='insert into total_message_log (order_id,order_product_id,customer_id,topup_id,user_id,subject,content,message_date,active_link)';
	$sql.='values('.$orderId.',0,'.$customerId.',0,"0","'.mysql_real_escape_string($message).'","'.mysql_real_escape_string($message).'",NOW(),0)';
	$sqlQuery=mysql_query($sql);
	$totalMessageLog = mysql_insert_id();
	$sql='select * from total_message_log where eid='.$totalMessageLog;
	$selectMessageLog = mysql_query($sql);
	$totalMessageArr=array();
	while($row=mysql_fetch_assoc($selectMessageLog)){
		$totalMessageArr[]=$row;
	}

	if(!empty($totalMessageLog)){
		echo json_encode($totalMessageArr);
	}else{
		echo "n";
	}
}

//for order only
if(isset($_POST['frmMsgSendPk'])){
	$customerId=$_SESSION['CX_login_id'];
	$message=$_POST['frmMsgSendPk'];
	$packageid=$_POST['packageId'];
	$sql='insert into total_message_log (packageid,order_product_id,customer_id,topup_id,user_id,subject,content,message_date,active_link)';
	$sql.='values('.$packageid.',0,'.$customerId.',0,"0","'.mysql_real_escape_string($message).'","'.mysql_real_escape_string($message).'",NOW(),0)';
	$sqlQuery=mysql_query($sql);
	$totalMessageLog = mysql_insert_id();
	$sql='select * from total_message_log where eid='.$totalMessageLog;
	$selectMessageLog = mysql_query($sql);
	$totalMessageArr=array();
	while($row=mysql_fetch_assoc($selectMessageLog)){
		$totalMessageArr[]=$row;
	}

	if(!empty($totalMessageLog)){
		echo json_encode($totalMessageArr);
	}else{
		echo "n";
	}
}

?>