<?php
$formcode = "report-rate";

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
	<title>รานงานสรุปเรท</title>
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
	<h1><a href="index.php">รานงานสรุปเรท</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
		<i class="material-icons" onclick="window.print();" title="Print">print</i>
		<i class="material-icons" data-toggle="modal" data-target="#searchOrder"  title="Search">find_in_page</i>
	</div>
	<table class="detail">
		<tr>
			<th colspan="2">ช่วงเวลา</th>
			<th rowspan="2">Rate</th>
		</tr>
		<tr>
				<th>เริ่มต้น</th>
				<th>สิ้นสุด</th>
		</tr>
<?php

	$select_str = "select * 
		from website_rate where 1 ";

	if ($_REQUEST['search_startdate']!= '') { 
		if ($_REQUEST['search_begintime']!= ''){
			$startdate = $_REQUEST['search_startdate'];
			$startdate = str_replace('/', '-', $startdate);
			$startdate = date('m/d/Y', strtotime($startdate));
			$begintime = $_REQUEST['search_begintime'];
			$begintime = date('H:i', strtotime($begintime));
			$select_str .= "and starting_date > STR_TO_DATE('$startdate $begintime','%c/%e/%Y %T') "; 
		}else{
			$startdate = $_REQUEST['search_startdate'];
			$startdate = str_replace('/', '-', $startdate);
			$startdate = date('m/d/Y', strtotime($startdate));
			$select_str .= "and starting_date > STR_TO_DATE('$startdate','%c/%e/%Y') "; 
		}
	}
	if ($_REQUEST['search_enddate']!= '') { 
		if ($_REQUEST['search_endtime'] != '') { 
			$enddate = $_REQUEST['search_enddate'];
			$enddate = str_replace('/', '-', $enddate);
			$enddate = date('m/d/Y', strtotime($enddate));
			$endtime = $_REQUEST['search_endtime'];
			$endtime = date('H:i', strtotime($endtime));
			$select_str .= "and starting_date < STR_TO_DATE('$enddate $endtime','%c/%e/%Y %T') "; 
		}else{
			$enddate = $_REQUEST['search_enddate'];
			$enddate = str_replace('/', '-', $enddate);
			$enddate = date('m/d/Y', strtotime($enddate));
			$select_str .= "and starting_date < STR_TO_DATE('$enddate','%c/%e/%Y') "; 
		}
	}
	
	$search_str = "";
	if ($_REQUEST['search_startdate'] != '') { $search_str .= "&search_startdate=".$_REQUEST['search_startdate']; }
	if ($_REQUEST['search_begintime']!= '') { $search_str .= "&search_begintime=".$_REQUEST['search_begintime']; }
	if ($_REQUEST['search_enddate']!= '') { $search_str .= "&search_enddate=".$_REQUEST['search_enddate']; }
	if ($_REQUEST['search_endtime']!= '') { $search_str .= "&search_endtime=".$_REQUEST['search_endtime']; }

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

	$select_str .= "order by website_rate_id desc ";
	$select_str .= "limit $Page_Start , $Per_Page ";

	//echo $select_str;
	$select_rate = mysql_query($select_str);

	if (mysql_num_rows($select_rate)>0) {
		$current_row = (($Per_Page*$Page)-$Per_Page)+1;

		while ($row = mysql_fetch_array($select_rate)) {
		?>
				<tr>
					<td><?php echo date("d/m/Y H:i:s", strtotime($row['starting_date']." ".$row['begin_time'])); ?></td>
					<td>
					<?php 
					$next = mysql_query("select * from website_rate 
						where website_rate_id = (
							select min(website_rate_id) 
							from website_rate 
							where website_rate_id > ".$row['website_rate_id'].")");
					if (mysql_num_rows($next)>0) {
						$next_row = mysql_fetch_array($next);
						echo date("d/m/Y H:i:s", strtotime($next_row['starting_date']." ".$next_row['begin_time'])-1);
					}else{
						echo "ไม่มีกำหนด";
					}
					?>
					</td>
					<td><?php echo $row['rate_cny']; ?></td>
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
				<h4 class="modal-title">ค้นหา รานงานสรุปเรท</h4>
			</div>
			<div class="modal-body">
				<form action="index.php" method="post">
					<div class="form-group">
							<label for="email" class="col-md-3 control-label">วันที่เริ่มต้น </label>
							<div class="col-md-8">
								<div class="input-group input-append date">
									<input type="text" class="form-control" id="date1" name="search_startdate" placeholder="วันที่เริ่มต้น" />
									<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								</div>
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_starting_date" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label for="email" class="col-md-3 control-label">เวลาเริ่มต้น </label>
							<div class="col-md-8">
								<div class="input-group input-append date">
									<input type="text" class="form-control" id="time1" name="search_begintime" placeholder="เวลาเริ่มต้น" />
									<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								</div>
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_begin_time" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label for="email" class="col-md-3 control-label">วันที่สินสุด </label>
							<div class="col-md-8">
								<div class="input-group input-append date">
									<input type="text" class="form-control" id="date2" name="search_enddate" placeholder="วันที่สินสุด" />
									<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								</div>
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_starting_date" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label for="email" class="col-md-3 control-label">เวลาสิ้นสุด </label>
							<div class="col-md-8">
								<div class="input-group input-append date">
									<input type="text" class="form-control" id="time2" name="search_endtime" placeholder="เวลาสิ้นสุด" />
									<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								</div>
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_begin_time" style="color:red;"></label>
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