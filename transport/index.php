
<!DOCTYPE html>
<html>
<head>
<title>Package</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../css/cargo.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	rel="stylesheet">
<link
	href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300'
	rel='stylesheet' type='text/css'>

<style>
i {
	color: #E91E63;
}

.paging a {
    text-decoration: underline;
}

a.current-page {
    text-decoration: none;
}

button,.button {
	color: #E91E63;
}

a {
	color: #E91E63;
}

th {
	background: #e36c09;
	border-right:1px solid #F88E37;
}

.undivide th {
	background: #E91E63;
}

.order-button:hover {
	color: #E91E63;
}


.wrap th {
	width: 32%;
}
</style>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
<script src="js/ajaxlib.js"></script>
<script src="js/util.js"></script>
<script src="js/packagelib.js"></script>
<script src="js/package_ui_events.js"></script>
<script>
	$(function() {
		$( "#dform,#dto" ).datepicker({
	        dateFormat: "yy-mm-dd"
		});       
	});

function exportExcel(){
	window.open('transport_excel.php','_blank');
}
var editOn = false;
function edit(crn){
	
//     console.log(crn);
	
// 		document.getElementById('addBox').style.visibility = 'hidden';
// 		document.getElementById('searchBox').style.visibility = 'hidden';
// 		editOn = !editOn;
// 		if(editOn){
// 			document.getElementById('editBox').style.visibility = 'visible';
// 			document.getElementById('e-customerName').value = document.getElementById(crn+'customer').textContent;
// 		}else{
// 			document.getElementById('editBox').style.visibility = 'hidden';
// 		}
	window.location.href = './transport-detail.php?id='+crn;
}    

var searchOn=false;

function searchBox(){
	document.getElementById('addBox').style.visibility = 'hidden';
	document.getElementById('editBox').style.visibility = 'hidden';
	searchOn = !searchOn;
	if(searchOn){
		document.getElementById('searchBox').style.visibility = 'visible';	
		
	}else{
		document.getElementById('searchBox').style.visibility = 'hidden';
	}
}

// Global variable
var offset = 20;
var page = 1;
var pack = new Package();
var packages = [];

</script>
<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';
?>

<?php 
//paging
$pageSize = 15;
$allPage = 0;
if (isset ( $_GET ['page'] )) {
	$nowPage = $_GET ['page'] - 1;
} else {
	$nowPage = 0;
}

if (isset ( $_GET ['page'] )) {
	$curPage = $_GET ['page'] ;
} else {
	$curPage = 1;
}




// delete
if (isset ( $_POST ['del'] )) {

		if ($stmt = $con->prepare ( 'delete from package where packageno="' . $_post ['del'] . '"' )) {
			//$res = $stmt->execute ();
			if (! $res) {
				echo '<script>alert("การลบข้อมูลล้มเหลว");</script>';
			} else {
				echo '<script>alert("ลบข้อมูลสำเร็จ");window.location = "./index.php";</script>';
			}
		}
}

// cancel
if (isset ( $_POST ['cancel'] ) && isset ( $_POST ['remarkp'] )) {
	$stmFlag=false;
	$statusId=$_POST ['statusid'];

		/**
		 * 1.update package.cancel_by =userid
		 * 2.update package.cancel_date=current date
		 * 3.update package.cancel_remark=remark
		 * 4.update package.statusid = 7
		 * 5.alert("ลบรายการเรียบร้อย")
		 */
		
		//echo 'UPDATE package SET cancel_by="' . $_POST ['cancel'] . '",statusid=7,cancel_date=Now(),cancel_remark="'.$_POST ['remarkp'].'" WHERE packageno="' . $_POST ['packageno'] . '"' ;
	if ($stmt = $con->prepare ( 'update package set cancel_by="' . $_post ['cancel'] . '",statusid=7,cancel_date=now(),cancel_remark="'.$_post ['remarkp'].'" where packageno="' . $_post ['packageno'] . '"' )) {
		$res = $stmt->execute ();
		if (! $res) {
			$stmFlag=false;
		} else {
			$stmFlag=true;
		}
	}
	 if($_POST ['statusid']==3){
	/**customerId
	 *   insert into customer_statement (customer_id,statement_name,statement_date,debit,packageid) values (รหัสลูกค้า,'ยกเลิกค่าขนส่ง กล่อง P16xxxxxxx',
 package.total, idกล่อง package.packageid)
	 */
		//echo 'insert into customer_statement (customer_id,statement_name,statement_date,debit,packageid) values ('.$_POST ['customerid'].',"ยกเลิกค่าขนส่ง กล่อง '.$_POST ['packageno'].'",NOW(),'.$_POST ['debit'].','.$_POST ['packageid'].')';
		if($stmt=$con->prepare('insert into customer_statement (customer_id,statement_name,statement_date,debit,packageid) values ('.$_POST ['customerid'].',"ยกเลิกค่าขนส่ง กล่อง '.$_POST ['packageno'].'",NOW(),'.$_POST ['debit'].','.$_POST ['packageid'].')')){
			$res = $stmt->execute ();
			if (! $res) {
				$stmFlag=false;
			} else {
				$stmFlag=true;
			}
		} //end statment	
	}
	
	if($stmFlag==true){
		echo '<script>alert("ลบรายการเรียบร้อย");</script>';
	}else{
		echo '<script>alert("การยกเลิกข้อมูลล้มเหลว");</script>';
	}
}
$countPackage=0;
?>
</head>

<body>
	<h1>
		<a href="index.php">Transport</a>
	</h1>
	<h3>
		<a href="../index.php">&larr; Back</a>
	</h3>
	<br>
	<div class="menu">
		<i class="material-icons" onclick="exportExcel();" title="Export">insert_drive_file</i>
		<i class="material-icons" onclick="searchBox();" title="Search">find_in_page</i>
	</div>
	<div id="detail">
		<table class="detail">
			<tr>
				<th>เลขที่กล่อง</th>
				<th>ชื่อลูกค้า</th>
				<th>ID ลูกค้า</th>			
				<th>ยอดค่าขนส่ง</th>
				<th>สถานะ</th>
				<th>วันที่ชำระ</th>
				<th>วิธีส่ง</th>
				<th>จำนวนTracking</th>
				<th>จำนวนกล่อง</th>
				<th>ผู้ส่ง</th>
				<th>วันที่ส่ง</th>
				<th>หมายเหตุ</th>
				<th>Add</th>
				<th>Add Date</th>
				<th>Action</th>			
			</tr>
			<!-- detail -->
			
			<?php	
//search block

			$sql="select p.*,c.*,ps.*,wt.*,pse.* from package p,package_send pse inner join customer c on c.customer_id = p.customerid inner join package_status ps on ps.packagestatusid=p.statusid inner join website_transport wt on wt.transport_id=p.shippingid    where p.statusid>=3 or pse.packageid = p.packageid ";
			//$sql="SELECT P.*,C.*,PS.*,WT.* FROM PACKAGE P INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = P.CUSTOMERID INNER JOIN PACKAGE_STATUS PS ON PS.PACKAGESTATUSID=P.STATUSID INNER JOIN WEBSITE_TRANSPORT WT ON WT.TRANSPORT_ID=P.SHIPPINGID  WHERE P.STATUSID>=3 ";
			if(! empty ( $_GET ['searchAll'] )
				||! empty ( $_GET ['packageno'] )
				||! empty ( $_GET ['from'] )
				||! empty ( $_GET ['to'] )
				||! empty ( $_GET ['cid'] )
				||! empty ( $_GET ['customer_code'] )
				||! empty ( $_GET ['total'] )
				||! empty ( $_GET ['packagestatusid'] )){
				$sql.=" and ";

			}
			//$sql='SELECT *,P.REMARK AS package_remark,PS.REMARK AS package_status_remark FROM PACKAGE P INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = P.CUSTOMERID INNER JOIN PACKAGE_STATUS PS ON PS.PACKAGESTATUSID = P.STATUSID WHERE ';
			$sqlSearch='';
			$request='';
			if (! empty ( $_GET ['searchAll'] )) {

				/**
				 * package.packageno :เลขที่กล่อง
				 * package.createdate	:วันที่สร้างกล่อง
				 * package.customerid  --> customer.customer_firstname + ' ' + customer.customer_lastname :ชือลูกค้า
				 * customer.customer_code :ID ลูกค้า
				 * package.total :ค่าขนส่ง
				 * package.statusid :สถานะค่าขนส่ง
				 * searchAll=se&packageno=&from=&to=&cid=&customer_code=&status=0&total=&packagestatusid=1
				 * 
				 */
				$arrSearchAll=array(
					'P.packageno like "%'.$_GET['searchAll'].'%"',
					'P.createdate like "%'.$_GET['searchAll'].'%"',
					'P.createdate like "%'.$_GET['searchAll'].'%"',
					'C.customer_firstname like "%'.$_GET['searchAll'].'%"',
					'C.customer_code like "%'.$_GET['searchAll'].'%"',
					'P.total like "%'.$_GET['searchAll'].'%"',
					'PS.packagestatusid like "%'.$_GET['searchAll'].'%"',
					'P.statusid like "%'.$_GET['searchAll'].'%"'
				);
				$i=0;
				foreach($arrSearchAll as $val){
					$sqlSearch.=$val.' ';
					if($i<count($arrSearchAll)-1){
						$sqlSearch.=" OR ";
					}
					++$i;						
				}
				
				//echo $sql;
				$request.='&searchAll='.$_GET ['searchAll'];
			} //end searchAll
			
			
			
			//package.packageno
			$arrCriteria=array();
			if(!empty($_GET['packageno'])){
				//$sql.=' P.packageno like "%'.$_GET['packageno'].'%"';
				array_push($arrCriteria,' p.packageno like "%'.$_GET['packageno'].'%"');
				$request.='&packageno='.$_GET ['packageno'];
			}
			
			//package.createdate
			if(!empty($_GET['from'])){
				//$sql.=' P.createdate like "%'.$_GET['from'].'%"';
				array_push($arrCriteria,' p.createdate like "%'.$_GET['from'].'%"');
				$request.='&from='.$_GET ['from'];
			}
			
				
			//package.createdate to
			if(!empty($_GET['to'])){
				array_push($arrCriteria,' p.createdate like "%'.$_GET['to'].'%"');
				$request.='&to='.$_GET ['to'];
			}
			
			//package.customerid  --> customer.customer_firstname + ' ' + customer.customer_lastname :ชือลูกค้า
			if(!empty($_GET['cid'])){
				array_push($arrCriteria,' c.customer_firstname like "%'.$_GET['cid'].'%"');
				$request.='&cid='.$_GET ['cid'];
			}
			
			//customer.customer_code :ID ลูกค้า
			if(!empty($_GET['customer_code'])){
				array_push($arrCriteria,' c.customer_code like "%'.$_GET['customer_code'].'%"');
				$request.='&customer_code='.$_GET ['customer_code'];
			}
			
			//package.total :ค่าขนส่ง
			if(!empty($_GET['total'])){
				array_push($arrCriteria,' p.total like "%'.$_GET['total'].'%"');
				$request.='&total='.$_GET ['total'];
			}
			
			//package.statusid --> package_status.packagestatusname
			if(!empty($_GET['packagestatusid'])){
				array_push($arrCriteria,' ps.packagestatusid like "%'.$_GET['packagestatusid'].'%"');
				$request.='&packagestatusid='.$_GET ['packagestatusid'];
			}

			if(count($arrCriteria) > 0){
				$i=0;
				if(! empty ( $_GET ['searchAll'] )){
					$sqlSearch.=' or ';
				}
				foreach ($arrCriteria as $key=>$val){
					$sqlSearch.=$val.' ';
					if($i<count($arrCriteria)-1){
						$sqlSearch.=' or ';
					}
					$i++;
				}
			}
			
			
//end search block	
			
			$package = array ();
			$packageSize=array ();
			//$sql="SELECT *,P.REMARK AS package_remark,PS.REMARK AS package_status_remark FROM PACKAGE P INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = P.CUSTOMERID INNER JOIN PACKAGE_STATUS PS ON PS.PACKAGESTATUSID = P.STATUSID";
			//LIMIT ". $nowPage * $pageSize .",".$pageSize
			
			$sql.=$sqlSearch;
			//echo $sql;
			if ($result = $con->query ( $sql )) {
				
				while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
					$packageSize [] = $row;
				}
				
				
				$countPackage=count($packageSize);	
			
				$allPage=ceil($countPackage/$pageSize);
				
				$sqlLimit=$sql.' LIMIT '. $nowPage * $pageSize .",".$pageSize ;
			
				if ($result = $con->query ($sqlLimit )) {
					while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
						$package [] = $row;
					}
				}
				// echo json_encode ( $myArray );
				//echo $sqlLimit;
// 				echo "<pre>";
// 				print_r ( $package );
// 				echo "</pre>";
				
			}
			
			//sum buttom
			$count = array ();
			if ($result = $con->query ( "select p.*,c.*,ps.*,wt.*,pse.* from package p inner join customer c on c.customer_id = p.customerid inner join package_status ps on ps.packagestatusid=p.statusid inner join website_transport wt on wt.transport_id=p.shippingid inner join package_send pse on pse.sendid = p.sendid" )) {
			
				while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
					$count [] = $row;
				}
				// echo json_encode ( $myArray );
// 				echo "<pre>";
// 				print_r ( $count );
// 				echo "</pre>";
			}	
			$puncCount = 0;
			//echo $sql;
			?>
			
			<!-- detail -->
			<?php
				$sizeOfPackage= count($package);
				if($sizeOfPackage>0){
					for($i=0;$i<$sizeOfPackage;++$i){
			?>
				<tr class="<?php  echo (($puncCount % 2 == 0)? 'punc ' : '') . ($package[$i]['statusid'] == 7 ? 'cancel ' : '') ?> ">
					<td id="<?php echo $package[$i]['packageno']; ?>"><?php echo $package[$i]['packageno']; ?></td>					
					<td id="<?php echo $package[$i]['packageno'].'customer'; ?>"><?php echo $package[$i]['customer_firstname'].' '.$package[$i]['customer_lastname']; ?></td>
					<td id="<?php echo $package[$i]['customer_code'].'customercode'; ?>"><?php echo $package[$i]['customer_code']; ?></td>
					<td id="<?php echo $package[$i]['packageno'].'total'; ?>"><?php echo number_format($package[$i]['total'],2); ?></td>
					
					<?php if($package[$i]['packagestatusid']>=3){?>
					<td id="<?php echo $package[$i]['packageno'].'packagestatusid'; ?>"><?php echo $package[$i]['packagestatusname']; ?></td>
					<?php }else{?>
					<td id="<?php echo $package[$i]['packageno'].'packagestatusid'; ?>">-</td>
					<?php }//end else?>
					
					<td id="<?php echo $package[$i]['packageno'].'paydate'; ?>"><?php echo $package[$i]['paydate']; ?></td>
					
					<td id="<?php echo $package[$i]['packageno'].'transport_th_name'; ?>"><?php echo $package[$i]['transport_th_name']; ?></td>
					
					<td id="<?php echo $package[$i]['packageno'].'total_tracking'; ?>"><?php echo $package[$i]['total_tracking']; ?></td>
										
					<td id="<?php echo $package[$i]['packageno'].'total_count'; ?>"><?php echo $package[$i]['total_count']; ?></td>
					
					
					<td id="<?php echo $package[$i]['packageno'].'send_user'; ?>"><?php echo $package[$i]['send_user']; ?></td>
										
					<td id="<?php echo $package[$i]['packageno'].'send_date'; ?>"><?php echo $package[$i]['send_date']; ?></td>
										
					<td id="<?php echo $package[$i]['packageno'].'send_remark'; ?>"><?php echo $package[$i]['send_remark']; ?></td>
					
					<td id="<?php echo $package[$i]['packageno'].'add_user'; ?>"><?php echo $package[$i]['add_user']; ?></td>
					
					<td id="<?php echo $package[$i]['packageno'].'key_date'; ?>"><?php echo $package[$i]['key_date']; ?></td>
					
				
					<td>
					<button onclick="edit('<?php echo trim($package[$i]['packageno']); ?>')">Edit</button>
					</td>
				</tr>
			
			<?php
				$puncCount++;
				}//end for
			}
			
			if($countPackage<=0){
				//new รายละเอียดจัดส่ง
				
//$sql="SELECT P.*,C.*,PS.*,WT.*,PSE.* FROM PACKAGE P INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = P.CUSTOMERID INNER JOIN PACKAGE_STATUS PS ON PS.PACKAGESTATUSID=P.STATUSID INNER JOIN WEBSITE_TRANSPORT WT ON WT.TRANSPORT_ID=P.SHIPPINGID INNER JOIN PACKAGE_SEND PSE ON PSE.SENDID = P.SENDID WHERE P.STATUSID>=3 ";
$sql="select p.*,c.*,ps.*,wt.* ,pse.send_user,DATE_FORMAT(pse.send_date,'%Y-%m-%d') send_date,pse.send_remark,pse.add_user,pse.key_date from package p inner join customer c on c.customer_id = p.customerid inner join package_status ps on ps.packagestatusid=p.statusid inner join website_transport wt on wt.transport_id=p.shippingid left join package_send pse on pse.packageid = p.packageid where p.statusid>=3 ";
if(! empty ( $_GET ['searchAll'] )
||! empty ( $_GET ['packageno'] )
||! empty ( $_GET ['from'] )
||! empty ( $_GET ['to'] )
||! empty ( $_GET ['cid'] )
||! empty ( $_GET ['customer_code'] )
||! empty ( $_GET ['total'] )
||! empty ( $_GET ['packagestatusid'] )){
	$sql.=" and ";

}
//$sql='SELECT *,P.REMARK AS package_remark,PS.REMARK AS package_status_remark FROM PACKAGE P INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = P.CUSTOMERID INNER JOIN PACKAGE_STATUS PS ON PS.PACKAGESTATUSID = P.STATUSID WHERE ';
$sqlSearch='';
$request='';
//echo $sql;
if (! empty ( $_GET ['searchAll'] )) {

	/**
	 * package.packageno :เลขที่กล่อง
	 * package.createdate	:วันที่สร้างกล่อง
	 * package.customerid  --> customer.customer_firstname + ' ' + customer.customer_lastname :ชือลูกค้า
	 * customer.customer_code :ID ลูกค้า
	 * package.total :ค่าขนส่ง
	 * package.statusid :สถานะค่าขนส่ง
	 * searchAll=se&packageno=&from=&to=&cid=&customer_code=&status=0&total=&packagestatusid=1
	 *
	 */
	$arrSearchAll=array(
			'p.packageno like "%'.$_GET['searchAll'].'%"',
			'p.createdate like "%'.$_GET['searchAll'].'%"',
			'p.createdate like "%'.$_GET['searchAll'].'%"',
			'c.customer_firstname like "%'.$_GET['searchAll'].'%"',
			'c.customer_code like "%'.$_GET['searchAll'].'%"',
			'p.total like "%'.$_GET['searchAll'].'%"',
			'ps.packagestatusid like "%'.$_GET['searchAll'].'%"',
			'p.statusid like "%'.$_GET['searchAll'].'%"'
	);
	$i=0;
	foreach($arrSearchAll as $val){
		$sqlSearch.=$val.' ';
		if($i<count($arrSearchAll)-1){
			$sqlSearch.=" OR ";
		}
		++$i;
	}

	//echo $sql;
	$request.='&searchAll='.$_GET ['searchAll'];
} //end searchAll
	
	
	
//package.packageno
$arrCriteria=array();
if(!empty($_GET['packageno'])){
	//$sql.=' P.packageno like "%'.$_GET['packageno'].'%"';
	array_push($arrCriteria,' p.packageno like "%'.$_GET['packageno'].'%"');
	$request.='&packageno='.$_GET ['packageno'];
}
	
//package.createdate
if(!empty($_GET['from'])){
	//$sql.=' P.createdate like "%'.$_GET['from'].'%"';
	array_push($arrCriteria,' pse.key_date like "%'.$_GET['from'].'%"');
	//array_push($arrCriteria,' pse.key_date between "'.$_GET['from'].' 00:00:00"');
	$request.='&from='.$_GET ['from'];
}
	

//package.createdate to
if(!empty($_GET['to'])){
	array_push($arrCriteria,' pse.key_date like "%'.$_GET['to'].'%"');
	//array_push($arrCriteria,' pse.key_date between "'.$_GET['to'].' 00:00:00"');
	$request.='&to='.$_GET ['to'];
}
	
//package.customerid  --> customer.customer_firstname + ' ' + customer.customer_lastname :ชือลูกค้า
if(!empty($_GET['cid'])){
	
	$cidArray = explode(" ", trim($_GET['cid']));
	//echo count($cidArray);
	if(count($cidArray)>1){
			
		array_push($arrCriteria,' c.customer_firstname like "%'.$cidArray[0].'%"');
		array_push($arrCriteria,' c.customer_lastname like "%'.$cidArray[1].'%"');
	}else{
		array_push($arrCriteria,' c.customer_firstname like "%'.$_GET['cid'].'%"');
	}
	//array_push($arrCriteria,' c.customer_firstname like "%'.$_GET['cid'].'%"');
	$request.='&cid='.$_GET ['cid'];
}
	
//customer.customer_code :ID ลูกค้า
if(!empty($_GET['customer_code'])){
	array_push($arrCriteria,' c.customer_code like "%'.$_GET['customer_code'].'%"');
	$request.='&customer_code='.$_GET ['customer_code'];
}
	
//package.total :ค่าขนส่ง
if(!empty($_GET['total'])){
	array_push($arrCriteria,' p.total like "%'.$_GET['total'].'%"');
	$request.='&total='.$_GET ['total'];
}
	
//package.statusid --> package_status.packagestatusname
if(!empty($_GET['packagestatusid'])){
	array_push($arrCriteria,' ps.packagestatusid like "%'.$_GET['packagestatusid'].'%"');
	$request.='&packagestatusid='.$_GET ['packagestatusid'];
}

if(count($arrCriteria) > 0){
	$i=0;
	if(! empty ( $_GET ['searchAll'] )){
		$sqlSearch.=' or ';
	}
	foreach ($arrCriteria as $key=>$val){
		$sqlSearch.=$val.' ';
		if($i<count($arrCriteria)-1){
			$sqlSearch.=' or ';
		}
		$i++;
	}
}
	
	
//end search block
	
$package = array();
$packageSize=array();
//$sql="SELECT *,P.REMARK AS package_remark,PS.REMARK AS package_status_remark FROM PACKAGE P INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = P.CUSTOMERID INNER JOIN PACKAGE_STATUS PS ON PS.PACKAGESTATUSID = P.STATUSID";
//LIMIT ". $nowPage * $pageSize .",".$pageSize
	
$sql.=$sqlSearch.' group by p.packageid order by packageid desc';

if ($result = $con->query ( $sql )) {
	
	while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
	
		$packageSize [] = $row;
	}
	
	//print_r($packageSize);

	$countPackage=count($packageSize);
	$allPage=ceil($countPackage/$pageSize);


	$sqlLimit=$sql.' LIMIT '. $nowPage * $pageSize .",".$pageSize ;
	if ($result = $con->query ($sqlLimit )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$package [] = $row;
		}
	}
	//echo $sql;
	// echo json_encode ( $myArray );
	//echo $sqlLimit;
// 					echo "<pre>";
// 					print_r ( $package );
// 					echo "</pre>";

}

	
//sum buttom
$count = array ();
//$sql='select p.*,c.*,ps.*,wt.*,pse.* from package p inner join customer c on c.customer_id = p.customerid inner join package_status ps on ps.packagestatusid=p.statusid inner join website_transport wt on wt.transport_id=p.shippingid inner join package_send pse on pse.sendid = p.sendid';

if ($result = $con->query ( $sql )) {
		
	while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
		$count [] = $row;
	}
	// echo json_encode ( $myArray );
	// 				echo "<pre>";
	// 				print_r ( $count );
	// 				echo "</pre>";
}
$puncCount = 0;
//echo $sql;
?>
			
			<!-- detail -->
			<?php
				$sizeOfPackage= count($package);
				if($sizeOfPackage>0){
					for($i=0;$i<$sizeOfPackage;++$i){
					
			?>
				<tr class="<?php  echo (($puncCount % 2 == 0)? 'punc ' : '') . ($package[$i]['statusid'] == 7 ? 'cancel ' : '') ?> ">
					<td id="<?php echo $package[$i]['packageno']; ?>"><?php echo (!empty($package[$i]['packageno'])? $package[$i]['packageno']:'-'); ?></td>					
					<td id="<?php echo $package[$i]['packageno'].'customer'; ?>"><?php echo $package[$i]['customer_firstname'].' '.$package[$i]['customer_lastname']; ?></td>
					<td id="<?php echo $package[$i]['customer_code'].'customercode'; ?>"><?php echo $package[$i]['customer_code']; ?></td>
					<td id="<?php echo $package[$i]['packageno'].'total'; ?>"><?php echo number_format($package[$i]['total'],2); ?></td>
					
					<?php if($package[$i]['packagestatusid']>=3){?>
					<td id="<?php echo $package[$i]['packageno'].'packagestatusid'; ?>"><?php echo $package[$i]['packagestatusname']; ?></td>
					<?php }else{?>
					<td id="<?php echo $package[$i]['packageno'].'packagestatusid'; ?>">-</td>
					<?php }//end else?>
					
					<td id="<?php echo $package[$i]['packageno'].'paydate'; ?>"><?php echo $package[$i]['paydate']; ?></td>
					
					<td id="<?php echo $package[$i]['packageno'].'transport_th_name'; ?>"><?php echo $package[$i]['transport_th_name']; ?></td>
					
					<td id="<?php echo $package[$i]['packageno'].'total_tracking'; ?>"><?php echo $package[$i]['total_tracking']; ?></td>
										
					<td id="<?php echo $package[$i]['packageno'].'total_count'; ?>"><?php echo $package[$i]['total_count']; ?></td>
					
					
					<td id="<?php echo $package[$i]['packageno'].'send_user'; ?>"><?php echo (!empty($package[$i]['send_user'])?$package[$i]['send_user'] : '-'); ?></td>
					<td id="<?php echo $package[$i]['packageno'].'send_date'; ?>"><?php echo (!empty($package[$i]['send_date'])?$package[$i]['send_date']:'-'); ?></td>
					<td id="<?php echo $package[$i]['packageno'].'send_remark'; ?>"><?php echo (!empty($package[$i]['send_remark'])?$package[$i]['send_remark']:'-'); ?></td>
					<td id="<?php echo $package[$i]['packageno'].'add_user'; ?>"><?php echo (!empty($package[$i]['add_user'])?$package[$i]['add_user']:'-'); ?></td>
					<td id="<?php echo $package[$i]['packageno'].'key_date'; ?>"><?php echo (!empty($package[$i]['key_date'])?$package[$i]['key_date']:'-'); ?></td>
				
					<td>
					<button onclick="edit('<?php echo trim($package[$i]['packageid']); ?>')">Edit</button>
					</td>
				</tr>
			
			<?php
				$puncCount++;
				}//end for
			}
			}
			
			?>
		</table>
		<br />
	</div>
	
	<div class="paging">
		<?php
			echo 'หน้า&emsp;';
			for($i = 1; $i <= $allPage; $i ++) {
				if ($i == $curPage) {
					echo '<a class="current-page" href="?page=' . $i  .$request. '">' . intval ( $i ) . '</a>';
				} else {
					echo '<a href="?page=' . $i  .$request. '">' . intval ( $i ) . '</a>';
				}
			}
		?>
	</div>

	<div id="addBox" class="wrap">
		<table>
			<tr>
				<th><h2 id="title">Add</h2></th>
				<td></td>
			</tr>
			<tr>
				<th>เลขที่ กล่อง :</th>
				<td><input name="oid" required="required" /></td>
			</tr>
			<tr>
				<th>ชื่อลูกค้า :</th>
				<td><select name="cid">
						<?php
						for($i = 0; $i < sizeof ( $cus_info ); $i ++) {
							echo '<option value="' . $cus_id [$i] . '">' . $cus_info [$i] . '</option>';
						}
						?>
					</select></td>
			</tr>
			<tr>
				<th>วันที่สร้าง :</th>
				<td><input class="datetimepicker" name="datetime"
					required="required" /></td>
			</tr>
			<tr>
				<th>บริษัทขนส่ง :</th>
				<td><select name="cid">
						<?php
						for($i = 0; $i < sizeof ( $cus_info ); $i ++) {
							echo '<option value="' . $cus_id [$i] . '">' . $cus_info [$i] . '</option>';
						}
						?>
					</select></td>
			</tr>
			<tr>
				<th>Tracking ของบริษัทขนส่ง :</th>
				<td><input type="text" /></td>
			</tr>
			<tr>
				<th>สถานะ :</th>
				<td><select name="status">
						<option value="0">Draft</option>
						<option value="1">Confirmed</option>
						<option value="2">ชำระเงินแล้ว</option>
				</select></td>
			</tr>
			<input type="hidden" name="add" value="1" />
			<tr class="confirm">
				<td></td>
				<td><a onclick="add();">Cancel</a>&emsp;
					<button>Insert</button></td>
			</tr>
		</table>
	</div>

	<div id="editBox" class="wrap">
		<form method="post">
			<table>
				<tr>
					<th><h2 id="title">Edit</h2></th>
					<td></td>
				</tr>
				<tr>
					<th>เลขที่ กล่อง:</th>
					<td><input id="e-oid" name="oid" readonly /></td>
				</tr>
				<tr>
					<th>วันที่สร้าง  Order From:</th>
					<td><input class="datetimepicker" name="datetime" step="1" /></td>
				</tr>
				<tr>
					<th>To:</th>
					<td><input class="datetimepicker" name="datetime" step="1" /></td>
				</tr>
				<tr>
					<th>ชื่อลูกค้า :</th>
					<td><input id="e-customerName" name="customerName" /></td>
				</tr>
				
				<tr>
					<th>บริษัทขนส่ง :</th>
					<td><select name="cid">
						<?php
						for($i = 0; $i < sizeof ( $cus_info ); $i ++) {
							echo '<option value="' . $cus_id [$i] . '">' . $cus_info [$i] . '</option>';
						}
						?>
					</select></td>
				</tr>
				<tr>
					<th>Tracking ของบริษัทขนส่ง :</th>
					<td><input type="text" /></td>
				</tr>
				<tr>
					<th>สถานะ :</th>
					<td><select name="status">
							<option value="0">Draft</option>
							<option value="1">Confirmed</option>
							<option value="2">ชำระเงินแล้ว</option>
					</select></td>
				</tr>
				<input type="hidden" name="edit" value="1" />
				<tr class="confirm">
					<td></td>
					<td><a onclick="edit();">Cancel</a>&emsp;
						<button>Update</button></td>
				</tr>
			</table>
		</form>
	</div>
	
	<div id="searchBox" class="wrap">
		<form method="get">
			<table>
				<tr>
					<th><h2 id="title">Search</h2></th>
					<td></td>
				</tr>
				<tr>
					<th>Search All :</th>
					<td><input name="searchAll" /></td>
				</tr>
				<tr>
					<th>เลขที่ กล่อง:</th>
					<td><input name="packageno" /></td>
				</tr>
				<tr>
					<th>วันที่สร้าง Package  :</th>
					<td><input class="datetimepicker" id="dform" type="datetime-local" name="from" /></td>
				</tr>
				<tr>
					<th>To :</th>
					<td><input class="datetimepicker" id="dto" type="datetime-local" name="to" /></td>
				</tr>
				<tr>
					<th>ชื่อลูกค้า :</th>
					<td><input name="cid" list="lst"><datalist id="lst">					
										<?php
										
										if ($stmt = $con->prepare ( 'SELECT customer_id, customer_firstname, customer_lastname FROM customer ORDER BY customer_firstname, customer_lastname' )) {
											$stmt->execute ();
											$stmt->bind_result ( $customer_id, $customer_firstname, $customer_lastname );
											while ( $stmt->fetch () ) {
												echo '<option value="' . $customer_firstname . ' ' . $customer_lastname . '"/>';
											}
											$stmt->close ();
										}
										?>
										</datalist></td>
				</tr>
				<tr>
					<th>ID ลูกค้า :</th>
					<td><input name="customer_code" list="lst_ccode"><datalist id="lst_ccode">					
										<?php
										
											if ($stmt = $con->prepare ( "SELECT customer_code FROM customer where customer_code <> '' OR customer_code <> null ORDER BY customer_code" )) {
												$stmt->execute ();
												$stmt->bind_result ( $customer_code );
												while ( $stmt->fetch () ) {
													echo '<option value="' . $customer_code. '"/>';
												}
												$stmt->close ();
										}
										?>
					</datalist>
					</td>
				</tr>
				
				<tr>
					<th>ค่าขนส่ง</th>
					<td><input name="total"
						onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
				</tr>
				
				<tr>
					<th>สถานะกล่อง :</th>
					<td>
					<select name="packagestatusid">
					<?php
										
						if ($stmt = $con->prepare ( "select packagestatusid,packagestatusname from `package_status` group by packagestatusid" )) {
							$stmt->execute ();
							$stmt->bind_result ( $packagestatusid,$packagestatusname );
							echo '<option value="0">-</option>';
							while ( $stmt->fetch () ) {
								echo '<option value="' . $packagestatusid. '">'.$packagestatusname.'</option>';
							}
							$stmt->close ();
						}
					?>
					</select>
					</td>
				</tr>
				<tr class="confirm">
					<td></td>
					<td><a onclick="searchBox();">Cancel</a>&emsp;
						<button>Search</button></td>
				</tr>
			</table>
		</form>
	</div>
	
	<?php 
// 		echo '<pre>';
// 		print_r($count);
// 		echo '</pre>';

	?>
	
	<div class="results">
		<table>
			<tr>
				<td><b>จำนวนกล่องทั้งหมด</b></td>
				<td class="normal"></td>
				<td class="normal"><?php echo (count($count)>0)? number_format(count($count)) : ''; ?>&nbsp;</td>
				<td>กล่อง<br></td>
			</tr>
			<tr>
			<?php
			$totalSum=0;
			if(count($count)>0){
				$i=0;
				foreach($count as $val){
					$totalSum+=$val['total'];

				}
			}
			?>
				<td><b>จำนวนยอดทั้งหมด</b></td>
				<td class="normal"></td>
				<td class="normal"><?php echo (count($count)>0)?  number_format($totalSum,2) : ''; ?>&nbsp;</td>
				<td style="text-align: left;">THB</td>
			</tr>
			</div>
</body>
</html>

<?php 
	//$result->close ();
	$con->close ();
?>