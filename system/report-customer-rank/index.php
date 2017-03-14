<?php
$formcode = "report-customer-rank";

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
	<title>รายงานรายได้ค่าสินค้าหลังร้าน</title>
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
	<h1><a href="index.php">รายงานจัดอันดับลูกค้า</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
		<i class="material-icons" onclick="window.print();" title="Print">print</i>
		<i class="material-icons" data-toggle="modal" data-target="#searchOrder"  title="Search">find_in_page</i>
	</div>
	<table class="detail">
		<tr>
			<th>ลำดับ</th>
			<th>ลูกค้า</th>
			<th>จำนวน M3</th>
			<th>Kg.</th>
			<th>ยอดค่าขนส่ง</th>
			<th>ค่าเฉลีย</th>
		</tr>
<?php

	$select_str = "select * from customer where 1 ";

	if ($_REQUEST['search_firstname']!= '') { $select_str .= "and customer_firstname like '%".$_REQUEST['search_firstname']."%' "; 	}
	if ($_REQUEST['search_lastname']!= '') { $select_str .= "and customer_lastname like '%".$_REQUEST['search_lastname']."%' "; 	}



	$search_str = "";
	if ($_REQUEST['search_firstname'] != '') { $search_str .= "&search_firstname=".$_REQUEST['search_firstname']; }
	if ($_REQUEST['search_lastname']!= '') { $search_str .= "&search_lastname=".$_REQUEST['search_lastname']; }
	if ($_REQUEST['sort_type']!= '') { $search_str .= "&sort_type=".$_REQUEST['sort_type']; }
	if ($_REQUEST['sort_option']!= '') { $search_str .= "&sort_option=".$_REQUEST['sort_option']; }


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

	//$select_str .= "order by customer_id asc ";

	if ($_REQUEST['sort_type']!= '') { 
		$type = $_REQUEST['sort_type'];
		$sort_option = "desc";
		if ($_REQUEST['sort_option']== 'asc') { $sort_option = "asc"; }
		
		if ($type == 1) {	
			$select_str .= "order by total_m3 ".$sort_option." ";
		}else if ($type == 2) {
			$select_str .= "order by total_kg ".$sort_option." ";
		}else if ($type == 3) {
			$select_str .= "order by total_transfer_price ".$sort_option." ";
		}
	}else{
		$select_str .= "order by customer_id asc ";

	}
	$select_str .= "limit $Page_Start , $Per_Page ";

	//echo $select_str;
	$select_order = mysql_query($select_str);

	if (mysql_num_rows($select_order)>0) {
		$current_row = (($Per_Page*$Page)-$Per_Page)+1;
		$sum_total_return = 0;
		$sum_total_transport_profit = 0;
		$sum_total_profit = 0;

		while ($row = mysql_fetch_array($select_order)) {
			$total_customer_payment 	= $row['order_price']+$row['order_shop_transfer'];
			$total_taobao_payment 		= $row['order_price_back']+$row['order_shop_transfer_back'];
			$total_transport_profit 	= $row['order_shop_transfer']-$row['order_shop_transfer_back'];
			$total_profit 						= $total_customer_payment-$total_taobao_payment-$row['total_return'];

			$sum_total_return += $row['total_return'];
			$sum_total_transport_profit += $total_transport_profit;
			$sum_total_profit += $total_profit;

		?>
				<tr>
					<td><?php echo $current_row; ?></td>
					<td><?php echo $row['customer_firstname']." ".$row['customer_lastname']; ?></td>
					<td><?php echo number_format($row['total_m3'],2); ?></td>
					<td><?php echo number_format($row['total_kg'],2); ?></td>
					<td><?php echo number_format($row['total_transfer_price'],2); ?></td>
					<td><?php 
						if ($row['total_transfer_price']!=0 && $row['total_m3']!=0) {
						 	echo number_format($row['total_transfer_price']/$row['total_m3'],2);
						}else{
							echo "0.00";
						}  ?></td>
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
				<h4 class="modal-title">ค้นหา รายงานจัดอันดับลูกค้า</h4>
			</div>
			<div class="modal-body">
				<form action="index.php" method="post" class="form-horizontal"> 
					<div class="form-group">
						<label class="col-md-3 control-label">ชื่อผู้ใช้งาน</label>
		        <div class="col-md-8">
		        	<input type="text" class="form-control" name="search_firstname" placeholder="ชื่อ">
		          <input type="text" class="form-control" name="search_lastname" placeholder="นามสกุล" >
		        </div>
		      </div>
		      <div class="form-group">
						<label class="col-md-3 control-label">จัดเรียงตาม</label>
		        <div class="col-md-8">
	            <select class="form-control" name="sort_type">
	            	<option value="">ไม่เลือก</option>
	            	<option value="1">จำนวน m3</option>
	            	<option value="2">kg.</option>
	            	<option value="3">ยอดค่าขนส่ง</option>
	            </select>
	            <select class="form-control" name="sort_option">
	            	<option value="">ไม่เลือก</option>
	            	<option value="desc">มากไปน้อย</option>
	            	<option value="asc">น้อยไปมาก</option>
	            </select>
		        </div>
		      </div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="search_customer ">ค้นหา</button>
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