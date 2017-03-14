<?php
$formcode = "report-package";

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
	<title>รายงานรายรับค่าขนส่ง</title>
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
		#date2{z-index:10000 !important;}
		#time2{z-index:10000 !important;}
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
	<h1><a href="index.php">รายงานรายรับค่าขนส่ง</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
		<i class="material-icons" onclick="window.print();" title="Print">print</i>
		<i class="material-icons" data-toggle="modal" data-target="#searchOrder"  title="Search">find_in_page</i>
	</div>
	<table class="detail">
		<tr>
			<th>วันที่ลูกค้าตัดจ่ายค่าขนส่ง</th>
			<th>ลูกค้า</th>
			<th>เลขที่กล่อง</th>
			<th>ค่าขนส่งจีน-ไทย</th>
			<th>ค่าอื่นๆ</th>
			<th>ค่าขนส่งบริษัทขนส่ง(ไทย)</th>
			<th>ยอดรวม</th>
			<th>บริษัทขนส่ง</th>
			<th>เลขที่ขนส่ง</th>
		</tr>
<?php

	$select_str = "select * 
		from package p, customer c, website_transport t 
		where p.customerid = c.customer_id 
		and p.shippingid = t.transport_id ";

	if ($_REQUEST['search_firstname']!= '') { $select_str .= "and customer_firstname like '%".$_REQUEST['search_firstname']."%' "; 	}
	if ($_REQUEST['search_lastname']!= '') { $select_str .= "and customer_lastname like '%".$_REQUEST['search_lastname']."%' "; 	}
	if ($_REQUEST['search_customer_code']!= '') { $select_str .= "and customer_code = '".$_REQUEST['search_customer_code']."' "; 	}
	if ($_REQUEST['search_package_no']!= '') { $select_str .= "and packageno = '".$_REQUEST['search_package_no']."' "; 	}
	if ($_REQUEST['search_transport_id']!= '') { $select_str .= "and transport_id = '".$_REQUEST['search_transport_id']."' "; 	}
	if ($_REQUEST['search_tracking_no']!= '') { $select_str .= "and shippingno = '".$_REQUEST['search_tracking_no']."' "; 	}
	
	$search_str = "";
	if ($_REQUEST['search_startdate'] != '') { $search_str .= "&search_startdate=".$_REQUEST['search_startdate']; }
	if ($_REQUEST['search_begintime']!= '') { $search_str .= "&search_begintime=".$_REQUEST['search_begintime']; }
	if ($_REQUEST['search_customer_code']!= '') { $search_str .= "&search_customer_code=".$_REQUEST['search_customer_code']; }
	if ($_REQUEST['search_package_no']!= '') { $search_str .= "&search_package_no=".$_REQUEST['search_package_no']; }
	if ($_REQUEST['search_transport_id']!= '') { $search_str .= "&search_transport_id=".$_REQUEST['search_transport_id']; }
	if ($_REQUEST['search_tracking_no']!= '') { $search_str .= "&search_tracking_no=".$_REQUEST['search_tracking_no']; }

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

	$select_str .= "order by paydate desc ";
	$select_str .= "limit $Page_Start , $Per_Page ";

	echo $select_str;
	$select_rate = mysql_query($select_str);

	if (mysql_num_rows($select_rate)>0) {
		$current_row = (($Per_Page*$Page)-$Per_Page)+1;
		$sum_amount = 0;
		$sum_amount_other = 0;
		$sum_amount_thirdparty = 0;
		$sum_total = 0;

		while ($row = mysql_fetch_array($select_rate)) {
			$sum_amount +=  $row['amount'];
			$sum_amount_other += $row['amount_other'];
			$sum_amount_thirdparty += $row['amount_thirdparty'];
			$sum_total += $row['total'];
		?>
				<tr>
					<td><?php echo date("d/m/Y H:i:s", strtotime($row['starting_date']." ".$row['begin_time'])); ?></td>
					<td><?php echo $row['customer_firstname']." ".$row['customer_lastname']; ?></td>
					<td><?php echo $row['packageno']; ?></td>
					<td><?php echo $row['amount']; ?></td>
					<td><?php echo $row['amount_other']; ?></td>
					<td><?php echo $row['amount_thirdparty']; ?></td>
					<td><?php echo $row['total']; ?></td>
					<td><?php echo $row['transport_th_name']; ?></td>
					<td><?php echo $row['shippingno']; ?></td>
				</tr>
		<?php
		$current_row++;
		}?>
		<tr>
			<td></td>
			<td></td>
			<td><b>รวม</b></td>
			<td><b><?php echo number_format($sum_amount,2); ?></b></td>
			<td><b><?php echo number_format($sum_amount_other,2); ?></b></td>
			<td><b><?php echo number_format($sum_amount_thirdparty,2); ?></b></td>
			<td><b><?php echo number_format($sum_total,2); ?></b></td>
			<td></td>
			<td></td>
		</tr>
		<?php
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
					<td class="normal"><b><?php echo mysql_num_rows($select_rate)."/".mysql_num_rows($objQuery); ?></b></td>
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
				<h4 class="modal-title">ค้นหา รายงานรายรับค่าขนส่ง</h4>
			</div>
			<div class="modal-body">
				<form action="index.php" method="post" class="form-horizontal">
					<div class="form-group">
							<label class="col-md-3 control-label">ลูกค้า </label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="search_firstname" placeholder="ชื่อ">
								<input type="text" class="form-control" name="search_lastname" placeholder="นามสกุล">
								<input type="text" class="form-control" name="search_customer_code" placeholder="รหัสลูกค้า">
							</div>
					</div>
					<div class="form-group">
							<label class="col-md-3 control-label">เลขที่กล่อง </label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="search_package_no" placeholder="เลขที่กล่อง">
							</div>
					</div>
					<div class="form-group">
							<label class="col-md-3 control-label">บริษัทขนส่ง </label>
							<div class="col-md-8">
								<select name="search_transport_id" class="form-control">
									<option value="">ไม่เลือก</option>
									<option value="0">มารับด้วยตนเอง</option>
									<?php
										$transport = mysql_query("select * from website_transport");
										while($row = mysql_fetch_array($transport)){
											echo "<option value=".$row['transport_id'].">".$row['transport_th_name']."</option>";
										}
									?>
								</select>
							</div>
					</div>
					<div class="form-group">
							<label class="col-md-3 control-label">เลขที่ขนส่ง</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="search_tracking_no" placeholder="เลขที่ขนส่ง">
							</div>
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
		$('#date1').datepicker({
    	timeInput:true,
    	altRedirectFocus:false,
    	dateFormat: 'dd/mm/yy',
    })
		$('#time1').timepicker({
    	timeInput:true,
    	altRedirectFocus:false,
    })
    $('#date2').datepicker({
    	timeInput:true,
    	altRedirectFocus:false,
    	dateFormat: 'dd/mm/yy',
    })
		$('#time2').timepicker({
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