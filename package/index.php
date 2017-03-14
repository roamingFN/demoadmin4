<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';
// echo "<pre>";
// print_r(print_r($_SESSION));
// echo "</pre>";
		
?>
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
	color: #0070c0;
}

.paging a {
    text-decoration: underline;
}

a.current-page {
    text-decoration: none;
}

button,.button {
	color: #0070c0;
}

a {
	color: #0070c0;
}

th {
	background: #0070c0;
}

.undivide th,.detail-order-complete th{
	background: #0070c0;
}
.detail-order-complete th{
	border-right: 1px solid #00796b;
    color: #fff;
    padding: 4px;
    text-align: center;
    width: 127px !important;
}

.order-button:hover {
	color: #0070c0;
}


.wrap th {
	width: 32%;
}

#orderComplete table{
	width:60%;
}



.detail-order-complete{
 	box-shadow: none !important;
    display: block !important;
    max-height: 400px !important;
    position: relative !important;
    width: 98% !important;
    overflow-y: auto;
}

        #search input {
	background: #e4f1fb none repeat scroll 0 0;
	border: 0 none;
	color: #7F7F7F;
	float: left;
	font: 12px 'Helvetica','Lucida Sans Unicode','Lucida Grande',sans-serif;
	height: 20px;
	margin: 0;
	padding: 10px;
	transition: background 0.3s ease-in-out 0s;
	width: 300px;
}

#search button {
	background: url("images/search.png") no-repeat scroll center center #0070c0 ;
	cursor: pointer;
	height: 40px;
	text-indent: -99999em;
	transition: background 0.3s ease-in-out 0s;
	width: 40px;
	border: 2px solid #fff;
}

#search button:hover {
	background-color:#021828;
}

.searchBox{
    margin-right: 24px;
    float: right;
}

table.detail-order-complete  tr:hover{
	background: #b2dfdb none repeat scroll 0 0 !important;
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
//     $( ".datetimepicker" ).datetimepicker({
//         dateFormat: "yy-mm-dd",
//         timeFormat: "HH:mm:ss",
//         showSecond:true
// 	});
	  $( "#dform,#dto" ).datepicker({
	        dateFormat: "yy-mm-dd"
		});

   
});


var editOn = false;
function edit(crn){
	window.location.href = './detail-edit.php?id='+crn;

	
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

var orderCompleteOn=false;
function orderComplete(){
	document.getElementById('addBox').style.visibility = 'hidden';
	document.getElementById('editBox').style.visibility = 'hidden';
	orderCompleteOn = !orderCompleteOn;
	//call data with ajax
	searchOrderCompleteJSON();
	//end call
	if(orderCompleteOn){

		var CheckBoxes=document.getElementsByClassName('chkorderComplete');
		for (var i = 0; i < CheckBoxes.length; i++) {
		    CheckBoxes[i].checked = false;        
		}
		
		document.getElementById('orderComplete').style.visibility = 'visible';	
		
	}else{
		document.getElementById('orderComplete').style.visibility = 'hidden';
	}	
}

function cancelBtn(){
	document.getElementById('orderComplete').style.visibility = 'hidden';
	document.getElementById('addBox').style.visibility = 'hidden';
	document.getElementById('editBox').style.visibility = 'hidden';
	<?php unset($_SESSION['details']);?>
	location.reload(true);
	
}

function searchOrderComplete(){

	$('.detail-order-complete > tbody').empty();
	var search=$('#searchInput').val();
	searchOrderCompleteJSON(search);
	
}


function searchOrderCompleteJSON(param){

	var html='';
	$.getJSON("./package-do.php",{ searchOrder : '1',param:param }, function (data) {
		   var i=0;
		   if(data.length==0){
			   $('#btnOrderAdd').hide();
		   }
		   $.each(data,function(k,v){
				if(v.order_status_code==='7'){
					console.log(v);
					html+='<tr class='+((i % 2 == 0) ? "punc" : "") + '>';	
					html+='<td id='+v.order_number+v.customer_firstname+'>'+v.customer_firstname+' '+v.customer_lastname+'</td>';
					html+='<td id='+v.order_number+'-'+v.customer_code+'>'+v.customer_code+'</td>';
					html+='<td>'+v.order_number+'</td>';
					html+='<td style="text-align:center">'+v.total_tracking+'</td>';
					html+='<td style="text-align:center">'+v.product_quantity+'</td>';
					html+='<td style="text-align:center">'+v.product_available+'</td>';
					html+='<td>'+v.received_complete_date+'</td>';
					
					html+='<td><input type="hidden" value="'+v.order_product_tracking_id+'" name="order_product_tracking_id[]"/><input type="hidden" value="'+v.tracking_no+'" name="tracking_no[]"/><input type="hidden" value="'+v.product_id+'" name="product_id[]"/><input type="hidden" value="'+v.order_product_id+'" name="order_product_id[]"/><input class="total_baht" type="hidden" value="'+((v.total_baht)?v.total_baht:0)+'" name="total_baht[]"/><input type="checkbox" class="chkorderComplete" name="chkorder[]" value="'+v.order_number+'"/></td>';

				
					html+='</tr>';
				}
				i++;
			});
		   	$('.detail-order-complete > tbody').empty();
			$('.detail-order-complete > tbody').append(html);
			//console.log(html);
	});
}

function btnOrderAdd(){
	$orderCheckList=$('#orderValidate').serialize();
	$paramAmp=$orderCheckList.split('&');
	$paramArray=[];
	if($paramAmp.length>0){
		for(i=0;i<$paramAmp.length;++i){
			$paramArray[i]=$paramAmp[i].split('=')[1];
		}
	}
	
	if($orderCheckList.length !=0){
	$('#orderValidate').submit();

	}else{
		alert('กรุณาเลือกรายการ');
	}
	
	
}

// Global variable
var offset = 20;
var page = 1;
var pack = new Package();
var packages = [];

</script>	


<?php 
if(isset($_SESSION['addOrder'])){
	print_r($_SESSION['addOrder']);
}

//paging
$pageSize = 20;
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


if(isset($_POST['addOrder'])){
	unset($_SESSION['order_number']);
	if(count($_POST['chkorder'])>0){
		foreach($_POST['chkorder'] as $val){
			echo $val.'<br/>';
			$_SESSION['order_number'][]=trim($val);
		}
	}
	
	print_r($_SESSION['order_number']);

	
}

// delete
if (isset ( $_POST ['del'] )) {

		if ($stmt = $con->prepare ( 'delete from package where packageno="' . $_POST ['del'] . '"' )) {
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
	if ($stmt = $con->prepare ( 'update package set cancel_by="' . $_POST ['cancel'] . '",statusid=7,cancel_date=Now(),cancel_remark="'.$_POST ['remarkp'].'" WHERE packageno="' . $_POST ['packageno'] . '"' )) {
		$res = $stmt->execute ();
		if (! $res) {
			$stmFlag=false;
		} else {
			$stmFlag=true;
		}
	}
	 if($_POST ['statusid']==3){
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
?>
</head>

<body>
	<h1>
		<a href="index.php">Package</a>
	</h1>
	<h3>
		<a href="../index.php">&larr; Back</a>
	</h3>
	<br>
	<div class="menu">
		<i class="material-icons" onclick="orderComplete()" title="orders">assignment</i>
		<i class="material-icons" onclick="on_add_button_click();" title="Add">add_circle</i>
		<i class="material-icons" onclick="searchBox();" title="Search">find_in_page</i>
	</div>
	<div id="detail">
		<table class="detail">
			<tr>
				<th>เลขที่<br>กล่อง
				</th>
				<th>วันที่สร้าง<br>กล่อง
				</th>
				<th>ชื่อลูกค้า</th>
				<th>ID ลูกค้า</th>
				<th>จำนวนTracking
				</th>
				<th>จำนวน order
				</th>
				<th>จำนวนสินค้าที่สั่ง
				</th>
				<th>จำนวนสินค้าที่<br/>ได้รับ
				</th>
				<th>จำนวนที่ขาด
				</th>
				<th>สถานะกล่อง</th>
				<th>ค่าขนส่ง(ของกล่อง)</th>
				<th>สถานะค่าขนส่ง</th>
				<th>Action</th>
				<th>หมายเหตุ</th>
			</tr>
			<!-- detail -->
			
			<?php	
//search block
			$sql="select *,p.remark as package_remark,ps.remark as package_status_remark from package p inner join customer c on c.customer_id = p.customerid inner join package_status ps on ps.packagestatusid = p.statusid ";
			if(! empty ( $_GET ['searchAll'] )
				||! empty ( $_GET ['packageno'] )
				||! empty ( $_GET ['from'] )
				||! empty ( $_GET ['to'] )
				||! empty ( $_GET ['cid'] )
				||! empty ( $_GET ['customer_code'] )
				||! empty ( $_GET ['total'] )
				||! empty ( $_GET ['packagestatusid'] )
				||! empty ( $_GET ['pstatusid'] )){
				$sql.=" WHERE ";

			}
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
				 * searchAll=se&packageno=&from=&to=&cid=&customer_code=&status=0&total=&packagestatusid=1&pstatusid=1
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
				array_push($arrCriteria,' p.packageno like "%'.$_GET['packageno'].'%"');
				$request.='&packageno='.$_GET ['packageno'];
			}
			
			//package.createdate
			if(!empty($_GET['from'])){
				array_push($arrCriteria,' p.createdate like "%'.$_GET['from'].'%"');
				$request.='&from='.$_GET ['from'];
			}
			
				
			//package.createdate to
			if(!empty($_GET['to'])){
				array_push($arrCriteria,' p.createdate like "%'.$_GET['to'].'%"');
				$request.='&to='.$_GET ['to'];
			}
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
			
			//package.statusid :สถานะค่าขนส่ง
			if(!empty($_GET['pstatusid'])){
				array_push($arrCriteria,' p.statusid like "%'.$_GET['pstatusid'].'%"');
				$request.='&pstatusid='.$_GET ['pstatusid'];
			}
			
			if(count($arrCriteria) > 0){
				$i=0;
				if(! empty ( $_GET ['searchAll'] )){
					$sqlSearch.=' OR ';
				}
				foreach ($arrCriteria as $key=>$val){
					$sqlSearch.=$val.' ';
					if($i<count($arrCriteria)-1){
						$sqlSearch.=' OR ';
					}
					$i++;
				}
			}
			
 			//echo $sqlSearch;
			
// 			echo "<pre>";
// 			print_r($_GET);
			
// 			echo "</pre>";
		
			
//end search block	
			
			$package = array ();
			$packageSize=array ();
			//$sql="SELECT *,P.REMARK AS package_remark,PS.REMARK AS package_status_remark FROM PACKAGE P INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = P.CUSTOMERID INNER JOIN PACKAGE_STATUS PS ON PS.PACKAGESTATUSID = P.STATUSID";
			//LIMIT ". $nowPage * $pageSize .",".$pageSize
			$sql.=$sqlSearch.' order by p.packageid desc';
			//echo $sql;
			$sqlLimit='';
			if ($result = $con->query ( $sql )) {
				
				while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
					$packageSize [] = $row;
				}
				
				$countPackage=count($packageSize);				
				$allPage=ceil($countPackage/$pageSize);
				
				$sqlLimit=$sql.' limit '. $nowPage * $pageSize .",".$pageSize ;
				if ($result = $con->query ($sqlLimit )) {
					while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
						$package [] = $row;
					}
				}
				// echo json_encode ( $myArray );
// 				echo "<pre>";
// 				print_r ( $package );
// 				echo "</pre>";
//echo $sqlLimit;
				
			}
			
			//sum buttom
			//echo $sql;
			//$sql="select ps.packagestatusid,sum(p.total) as amount,count(p.packageid) as count from package p inner join customer c on c.customer_id = p.customerid inner join package_status ps on ps.packagestatusid = p.statusid group by ps.packagestatusid";
			$count = array ();
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
			
			
			?>
			
			<!-- detail -->
			<?php
				$sizeOfPackage= count($package);
				if($sizeOfPackage>0){
					for($i=0;$i<$sizeOfPackage;++$i){
			?>
				<tr class="<?php  echo (($puncCount % 2 == 0)? 'punc ' : '') . ($package[$i]['statusid'] == 7 ? 'cancel ' : '') ?> ">
					<td id="<?php echo $package[$i]['packageno']; ?>"><?php echo $package[$i]['packageno']; ?></td>
					<td id="<?php echo $package[$i]['packageno'].'date'; ?>"><?php echo $package[$i]['createdate']; ?></td>
					<td id="<?php echo $package[$i]['packageno'].'customer'; ?>"><?php echo $package[$i]['customer_firstname'].' '.$package[$i]['customer_lastname']; ?></td>
					<td id="<?php echo $package[$i]['customer_code'].'customercode'; ?>"><?php echo $package[$i]['customer_code']; ?></td>
					<td style="text-align: center;" id="<?php echo $package[$i]['packageno'].'total_tracking'; ?>"><?php echo $package[$i]['total_tracking']; ?></td>
					<td style="text-align: center;" id="<?php echo $package[$i]['packageno'].'total_ordernumber'; ?>"><?php echo $package[$i]['total_ordernumber']; ?></td>
					<td style="text-align: center;" id="<?php echo $package[$i]['packageno'].'total_want'; ?>"><?php echo $package[$i]['total_want']; ?></td>
					
					<?php 
					 //12/11/2016:ในรูปด้านล่าง จำนวนสินค้าที่ได้รับ ให้ใช้ field customer_order_product_tracking.amount แทนของเดิม ครับ
					 $sqlAmount='select
									  sum(copt.amount) as sum_amount
									from customer_order_product_tracking copt
									where copt.order_product_id in (select
									  pd.order_product_id
									from package_detail pd
									where pd.packageid = ?)
									and copt.tracking_no in (select
									  pd.tracking_no
									from package_detail pd
									where pd.packageid = ?)';
					 $stmt = $con->prepare ( $sqlAmount );
					 $stmt->bind_param ( 'ii', $package[$i]['packageid'],$package[$i]['packageid'] );
					 $stmt->execute ();
					 $result = $stmt->get_result ();										 
					 $dataAmount = array ();
					 while ( $row = $result->fetch_assoc () ) {
					 	// echo json_encode($row);
					 	$dataAmount [] = $row;
// 					 	echo "<pre>";
// 					 	print_r($dataAmount);
// 					 	echo "</pre>";
					 }
					?>
					<!-- <td style="text-align: center;" id="<?php echo $package[$i]['packageno'].'total_quantity'; ?>"><?php /*echo (empty($dataAmount[0]['sum_amount'])?'-':$dataAmount[0]['sum_amount']); */?></td>  -->
				    <td style="text-align: center;" id="<?php echo $package[$i]['packageno'].'total_quantity'; ?>"><?php echo $package[$i]['total_quantity']; ?></td>
					<td style="text-align: center;" id="<?php echo $package[$i]['packageno'].'total_miss'; ?>"><?php echo $package[$i]['total_miss']; ?></td>
					<td id="<?php echo $package[$i]['packageno'].'packagestatusname'; ?>"><?php echo $package[$i]['packagestatusname']; ?></td>
					<td style="text-align: center;" id="<?php echo $package[$i]['packageno'].'total'; ?>"><?php echo number_format($package[$i]['total'],2); ?></td>
					<td id="<?php echo $package[$i]['packageno'].'statusid'; ?>">
						<?php 
							
							if($package[$i]['statusid']>=5){
								echo 'จ่ายแล้ว';
							}else{
								echo 'ยังไม่ได้จ่าย';
							}
						?>
					</td>
					<td>
					<button onclick="edit('<?php echo trim($package[$i]['packageid']); ?>')">Edit</button>
					<?php if($package[$i]['statusid'] != 7){?>
						
						<?php if($package[$i]['statusid'] ==1 || $package[$i]['statusid'] ==2 || $package[$i]['statusid'] ==3){ ?>
 							<form method="post" action="index.php?page=1" 
								onclick="input=prompt('กรุณาใส่เหตุผลที่ต้องการยกเลิก');this.ok=false; if(input!=null){document.getElementById('<?php echo 'cancel'.$package[$i]['packageno'].'remarkp'; ?>').value=input;this.ok=true;}"
								onsubmit="return this.ok;">
								<input  name="cancel" type="hidden" value="<?php echo $package[$i]['customer_code']; ?>"/>
								<input  name="customerid" type="hidden" value="<?php echo $package[$i]['customerid']; ?>"/>
								<input id="<?php echo 'cancel'.$package[$i]['packageno'].'remarkp'; ?>" name="remarkp" type="hidden"/>
								<input name="statusid" type="hidden" value="<?php echo $package[$i]['statusid']; ?>"/>
								<input name="packageno" type="hidden" value="<?php echo $package[$i]['packageno']; ?>"/>
								<input name="debit" type="hidden" value="<?php echo $package[$i]['total']; ?>"/>
								<input name="packageid" type="hidden" value="<?php echo $package[$i]['packageid']; ?>"/>
								<button>Del</button>
							</form>

						<?php } //end if statusid=1,2,3 ?>

					<?php }//end check statusid <> 7?>
					</td>
					<td id="<?php echo $package[$i]['packageno'].'package_remark'; ?>"><?php echo $package[$i]['package_remark']; ?></td>
				</tr>
			
			<?php
				$puncCount++;
				}//end for
			}//end if 
			
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
					<th>วันที่สร้าง Package :</th>
					<td><input class="datepicker" id="dform"  name="from" /></td>
				</tr>
				<tr>
					<th>To :</th>
					<td><input class="datepicker" id="dto"  name="to" /></td>
				</tr>
				<tr>
					<th>ชื่อลูกค้า :</th>
					<td><input name="cid" list="lst"><datalist id="lst">					
										<?php
										
										if ($stmt = $con->prepare ( 'select customer_id, customer_firstname, customer_lastname from customer order by customer_firstname, customer_lastname' )) {
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
										
											if ($stmt = $con->prepare ( "select customer_code from customer where customer_code <> '' OR customer_code <> null order by customer_firstname, customer_lastname " )) {
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
				
<!-- 				<tr> -->
<!-- 					<th>สถานะ :</th> -->
<!-- 					<td><select name="status"> -->
<!-- 							<option value="0">Draft</option> -->
<!-- 							<option value="1">Confirmed</option> -->
<!-- 							<option value="2">ชำระเงินแล้ว</option> -->
<!-- 					</select></td> -->
<!-- 				</tr> -->
				
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
							echo '<option value="">-</option>';
							while ( $stmt->fetch () ) {
								echo '<option value="' . $packagestatusid. '">'.$packagestatusname.'</option>';
							}
							$stmt->close ();
						}
					?>
					</select>
					</td>
				</tr>
				<tr>
					<th>สถานะค่าขนส่ง</th>
					<td><select name="pstatusid">
							<option value="">-</option>							
							<option value="0">ยังไม่จ่าย</option>
							<option value="5">จ่ายแล้ว</option>
					</select></td>
				</tr>
				<tr class="confirm">
					<td></td>
					<td><a onclick="searchBox();">Cancel</a>&emsp;
						<button>Search</button></td>
				</tr>
			</table>
		</form>
	</div>
	
		<!--  Block order complete -->
		<div id="orderComplete" class="wrap">
			<table>
				<tr>
					<th colspan="2" style="text-align: left"><h2 id="title">สรุปรายการออร์เดอร์ที่ครบ</h2></th>
				</tr>
				<tr>
					<td colspan="2">
						<div class="menu">			
						<!--  onclick="on_add_button_click();"-->			
							<i class="material-icons" id="btnOrderAdd" onclick="btnOrderAdd();"  title="Add">add_circle</i>						
						</div>
					</td>
				</tr>				
				<tr>
					<td colspan="2">
						<div class="searchBox">						
								<div  id="search" >
						              <input type="text" placeholder="Search" id="searchInput" class="search" value="" onblur="if(this.value == '') { this.value = 'Search'; }" onfocus="if(this.value == 'Search') { this.value = ''; }" name="s">
						              <button type="submit" onclick="searchOrderComplete()">Submit</button>
								</div>		
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<form id="orderValidate" method="post" action="./detail.php">
						<table class="detail-order-complete">
						  <thead> 
							<tr>
								<th>ชื่อลูกค้า</th>
								<th>ID ลูกค้า</th>
								<th>เลขที่ Order</th>
								<th>จำนวน Tracking</th>
								<th>จำนวนที่สั่ง</th>
								<th>จำนวนที่ได้รับ</th>
								<th>วันที่รับสินค้าครบ</th>
								<th>Action</th>
							</tr>
						  </thead>
						  <tbody>
						  	
						  </tbody>
						</table>
						<input type="hidden" name="orderCompleate" value="1"/>
					</form>
						</td>
					</tr>
					<tr class="confirm">
						<td></td>
						<td><a onclick="cancelBtn();">Cancel</a>&emsp;
							
						</tr>
					</table>
				

	</div>
	<!-- End block order complete -->
	
	<?php 

		if(count($count)>0){
			$amountTmp=0;
			
			$i=1;
	 		foreach($count as $val){

	 			$amountTmp+=$val['total'];
	 
	 		}
		}
	 ?>
	
	<div class="results">

		<table>
			<tr>
				<td><b>จำนวนรายการทั้งหมด</b></td>
				
<!-- 				<td class="normal"><?php /*echo (!empty($count[0]['count']))? number_format($count[0]['count']) : ''; */?>&nbsp;</td> -->
<!-- 				<td class="complete"><b>Complete :</b></td> -->
<!-- 				<td class="complete">0&nbsp;</td> -->
<!-- 				<td class="cancel"><b>Cancel :</b></td> -->
<!-- 				<td class="cancel">0&nbsp;</td> -->
<!-- 				<td><b>Total :</b></td> -->
				<td><?php echo (count($count)>0)? count($count) : ''; ?>&nbsp;</td>
				<td>Records<br></td>
			</tr>
			<tr>
				<td><b>จำนวนยอดทั้งหมด</b></td>
				
<!-- 				<td class="normal"><?php /* echo (!empty($count[0]['amount']))?  number_format($count[0]['amount'],2) : '';*/ ?>&nbsp;</td> -->
<!-- 				<td class="complete"><b>Complete :</b></td> -->
<!-- 				<td class="complete">0.00&nbsp;</td> -->
<!-- 				<td class="cancel"><b>Cancel :</b></td> -->
<!-- 				<td class="cancel">0.00&nbsp;</td> -->
<!-- 				<td><b>Total :</b></td> -->
				<td><?php echo (!empty($amountTmp))? number_format($amountTmp,2) : ''; ?>&nbsp;</td>
				<td style="text-align: left;">THB</td>
			</tr>
			</div>
</body>
</html>

<?php 
	//$result->close ();
	$con->close ();
?>