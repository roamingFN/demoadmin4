<?php
$formcode = "report-payment";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isViewPermitted($formcode)) {
	header('Location: ../index.php?error_code=view_not_permitted');
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>รายงานการตัดจ่าย</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/cargo.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
	<style>
		#date1{z-index:10000 !important;}
		#date2{z-index:10000 !important;}
		#time1{z-index:10000 !important;}
		.ui-datepicker-prev span {
    background-image: url(http://legacy.australianetwork.com/img/icon_arrow_left_black.png) !important;
        background-position: 0px 0px !important;
		}
		.ui-datepicker-next span {
		    background-image: url(http://legacy.australianetwork.com/img/icon_arrow_right_black.png) !important;
		        background-position: 0px 0px !important;
		}
	</style>
</head>
<body style="padding:10px;">
	<h1><a href="index.php">รายงานการตัดจ่าย</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
		<i class="material-icons" onclick="window.print();" title="Print">print</i>
		<i class="material-icons" data-toggle="modal" data-target="#searchOrder"  title="Search">find_in_page</i>
	</div>
	<table class="detail">
		<tr>
			<th>วันที่บัญชีทำการตัดจ่าย</th>
			<th>ลูกค้า</th>
			<th>เลขที่Order</th>
			<th>ยอดเงิน(บาท)</th>
			<th>เรท</th>
			<th>ยอดเงิน(หยวน)</th>
		</tr>
<?php

	$select_str = "select * 
		from customer_request_payment r, customer c, customer_order o 
		where r.order_id = o.order_id
		and r.customer_id = c.customer_id 
		and payment_request_status = '2' ";

	if ($_REQUEST['search_firstname']!= '') { $select_str .= "and customer_firstname like '%".$_REQUEST['search_firstname']."%' "; 	}
	if ($_REQUEST['search_lastname']!= '') { $select_str .= "and customer_lastname like '%".$_REQUEST['search_lastname']."%' "; 	}
	if ($_REQUEST['search_customer_code']!= '') { $select_str .= "and customer_code = '".$_REQUEST['search_customer_code']."' "; 	}
	
	if ($_REQUEST['search_complete_date_start'] != '') { 
		if ($_REQUEST['search_complete_date_end'] != '') {
			$complete_date_start = $_REQUEST['search_complete_date_start'];
			$complete_date_start = str_replace('/', '-', $complete_date_start);
			$complete_date_start = date('m/d/Y', strtotime($complete_date_start));
			$complete_date_end = $_REQUEST['search_complete_date_end'];
			$complete_date_end = str_replace('/', '-', $complete_date_end);
			$complete_date_end = date('m/d/Y', strtotime($complete_date_end));
			$select_str .= "and date_payment_paid > STR_TO_DATE('$complete_date_start 00:00:00','%c/%e/%Y %T') 
											and date_payment_paid < STR_TO_DATE('$complete_date_end 23:59:59','%c/%e/%Y %T')"; 
		}else{
			$complete_date_start = $_REQUEST['search_complete_date_start'];
			$complete_date_start = str_replace('/', '-', $complete_date_start);
			$complete_date_start = date('m/d/Y', strtotime($complete_date_start));
			$select_str .= "and date_payment_paid > STR_TO_DATE('$complete_date_start 00:00:00','%c/%e/%Y %T') "; 
		}
	}else{
		if ($_REQUEST['search_complete_date_end'] != '') {
			$complete_date_end = $_REQUEST['search_complete_date_end'];
			$complete_date_end = str_replace('/', '-', $complete_date_end);
			$complete_date_end = date('m/d/Y', strtotime($complete_date_end));
			$select_str .= "and date_payment_paid < STR_TO_DATE('$complete_date_end 23:59:59','%c/%e/%Y %T') "; 
		}
	}

	if ($_REQUEST['search_order_number']!= '') { $select_str .= "and order_number = '".$_REQUEST['search_order_number']."' "; }

	$search_str = "";
	if ($_REQUEST['search_firstname'] != '') { $search_str .= "&search_firstname=".$_REQUEST['search_firstname']; }
	if ($_REQUEST['search_lastname']!= '') { $search_str .= "&search_lastname=".$_REQUEST['search_lastname']; }
	if ($_REQUEST['search_customer_code']!= '') { $search_str .= "&search_customer_code=".$_REQUEST['search_customer_code']; }
	if ($_REQUEST['search_complete_date_start']!= '') { $search_str .= "&search_complete_date_start=".$_REQUEST['search_complete_date_start']; }
	if ($_REQUEST['search_complete_date_end']!= '') { $search_str .= "&search_complete_date_end=".$_REQUEST['search_complete_date_end']; }
	if ($_REQUEST['search_order_number']!= '') { $search_str .= "&search_order_number=".$_REQUEST['search_order_number']; }


		//###### Calculate Page ######
	$objQuery = mysql_query($select_str) or die ("Error Query [".$select_str."]");
	$Num_Rows = mysql_num_rows($objQuery);

	$Per_Page = 10; 
	
	if (isset($_GET["Page"])) {
		$Page = $_GET["Page"];
	}else{
		$Page=1;
	}

	$Prev_Page = $Page-1;
	$Next_Page = $Page+1;

	$Page_Start = (($Per_Page*$Page)-$Per_Page);
	if($Num_Rows<=$Per_Page)
	{
		$Num_Pages =1;
	}
	else if(($Num_Rows % $Per_Page)==0)
	{
		$Num_Pages =($Num_Rows/$Per_Page) ;
	}
	else
	{
		$Num_Pages =($Num_Rows/$Per_Page)+1;
		$Num_Pages = (int)$Num_Pages;
	}
	//###########################

	$select_str .= "order by order_completed_date desc ";
	$select_str .= "limit $Page_Start , $Per_Page ";

	//echo $select_str;
	$select_order = mysql_query($select_str);

	if (mysql_num_rows($select_order)>0) {
		$current_row = (($Per_Page*$Page)-$Per_Page)+1;

		while ($row = mysql_fetch_array($select_order)) {
			$price_cny = $row['payment_request_amount']/$row['order_rate'];

		?>
				<tr>
					<td><?php echo date("d/m/Y", strtotime($row['date_payment_paid'])); ?></td>
					<td><?php echo $row['customer_firstname']." ".$row['customer_lastname']; ?></td>
					<td><?php echo $row['order_number']; ?></td>
					<td><?php echo $row['payment_request_amount']; ?></td>
					<td><?php echo $row['order_rate']; ?></td>
					<td><?php echo number_format($price_cny,2); ?></td>
				</tr>
		<?php
		$current_row++;
		}
	}
?>
	</table>  
	<br>
	<div class="paging"> 
		หน้า
<?php 
	if($Prev_Page)
	{
		echo " <a href='$_SERVER[SCRIPT_NAME]?Page=$Prev_Page".$search_str."'><span class='glyphicon glyphicon-chevron-left'></span></a> ";
	}

	for($i=1; $i<=$Num_Pages; $i++){
		if($i != $Page)
		{
			echo "<a href='$_SERVER[SCRIPT_NAME]?Page=$i".$search_str."'>$i</a>";
		}
		else
		{
			echo "<b> $i </b>";
		}
	}
	if($Page!=$Num_Pages)
	{
		echo " <a href ='$_SERVER[SCRIPT_NAME]?Page=$Next_Page".$search_str."'><span class='glyphicon glyphicon-chevron-right'></span></a> ";
	}
?>

	</div>
	<div class="results">
		<table>
			<tr>
					<td><b>จำนวนรายการทั้งหมด</b></td>
					<td class="normal"><b><?php echo mysql_num_rows($select_order)."/".mysql_num_rows($objQuery); ?></b></td>
			</tr>
		</table>
	</div>

<!-- ### MODAL ### -->
<div class="modal fade" id="searchOrder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">ค้นหา รายงานการตัดจ่าย</h4>
			</div>
			<div class="modal-body">
				<form action="index.php" method="post">
					<div class="form-group">
						<label>ลูกค้า</label>
						<input type="text" class="form-control" name="search_firstname" placeholder="ชื่อ">
						<input type="text" class="form-control" name="search_lastname" placeholder="นามสกุล">
						<input type="text" class="form-control" name="search_customer_code" placeholder="รหัสลูกค้า">
					</div>
					<div class="form-group">
						<label>วันที่ทำการตัดจ่าย</label>
						<div class="input-group input-append date">
							<input type="text" class="form-control" name="search_complete_date_start" id="date1" placeholder="ตั้งแต่วันที่" />
							<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
							<input type="text" class="form-control" name="search_complete_date_end" id="date2" placeholder="ถึงวันที่" />
							<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
						</div>
					</div>
					<div class="form-group">
						<label>เลขที่ Order</label>
						<input type="text" class="form-control" name="search_order_number" placeholder="เลขที่ Order">
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="search_order">ค้นหา</button>
				<a href="index.php"><button class="btn btn-default">แสดงทั้งหมด</button></a>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- ### MODAL ### -->

<script type="text/javascript">

$(document).ready(function() {
		$('#datePicker')
				.datepicker({
						format: 'dd/mm/yyyy'
				})
				.on('changeDate', function(e) {
						// Revalidate the date field
						$('#eventForm').formValidation('revalidateField', 'customer-birthdate');
				});
		$('#datePicker2')
				.datepicker({
						format: 'dd/mm/yyyy'
				})
				.on('changeDate', function(e) {
						// Revalidate the date field
						$('#eventForm').formValidation('revalidateField', 'customer-birthdate');
				});
		$('#date1').datepicker({
    	timeInput:true,
    	altRedirectFocus:false,
    	dateFormat: 'dd/mm/yy',
    })
    $('#date2').datepicker({
    	timeInput:true,
    	altRedirectFocus:false,
    	dateFormat: 'dd/mm/yy',
    })
		$('#time1').timepicker({
    	timeInput:true,
    	altRedirectFocus:false,
    })
});

</script>
<script src="../js/sweetalert2.min.js"></script> 
<link rel="stylesheet" type="text/css" href="../js/sweetalert2.css">
<link href="../css/jquery-ui.css" rel="stylesheet"/>
<script charset="utf-8" src="../js/jquery-ui.js"></script>
<link href="../css/jquery-ui-timepicker-addon.css" rel="stylesheet"/>
<script charset="utf-8" src="../js/jquery-ui-timepicker-addon.min.js"></script>
</body>
</html>