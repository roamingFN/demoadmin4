<?php
$formcode = "manage-rate";

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
	<title>Manage Rate</title>
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
	<h1><a href="index.php">Manage Rate</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
	<?php 
		if (isAddPermitted($formcode)) {
			echo'<i class="material-icons" data-toggle="modal" data-target="#addRate" title="Add">add_circle</i>';
		}
	?>
		
		<i class="material-icons" onclick="window.print();" title="Print">print</i>
		<i class="material-icons" data-toggle="modal" data-target="#searchRate"  title="Search">find_in_page</i>
	</div>
	<table class="detail">
		<tr>
			<th>#</th>
			<th>วันที่</th>
			<th>Rate</th>
			<th>วันที่เริ่มต้น</th>
			<th>Add User</th>
			<th>Add Time</th>
			<th>Edit User</th>
			<th>Edit Time</th>
			<th>การกระทำ</th>
		</tr>
<?php

	$select_str = "select * from website_rate where status = '1' ";


	if ($_REQUEST['search_starting_date'] != '') { 
		$starting_date = $_REQUEST['search_starting_date'];
		$starting_date = str_replace('/', '-', $starting_date);
		$starting_date = date('m/d/Y', strtotime($starting_date));
		$select_str .= "and starting_date = STR_TO_DATE('$starting_date','%c/%e/%Y %T') "; 
	}
	if ($_REQUEST['search_rate_cny']!= '') { $select_str .= "and rate_cny = '".$_REQUEST['search_rate_cny']."' "; 	}
	if ($_REQUEST['search_shipping_rate']!= '') { $select_str .= "and shipping_rate_cny = '".$_REQUEST['search_shipping_rate']."' "; 		}

	$search_str = "";
	if ($_REQUEST['search_starting_date'] != '') { $search_str .= "&search_starting_date=".$_REQUEST['search_starting_date']; }
	if ($_REQUEST['search_rate_cny']!= '') { $search_str .= "&search_rate_cny=".$_REQUEST['search_rate_cny']; }
	if ($_REQUEST['search_shipping_rate']!= '') { $search_str .= "&search_shipping_rate=".$_REQUEST['search_shipping_rate']; }


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
					<td><?php echo $current_row; ?></td>
					<td><?php echo date("d/m/Y", strtotime($row['starting_date'])); ?></td>
					<td><?php echo $row['rate_cny']; ?></td>
					<td><?php echo $row['starting_date']." ".$row['begin_time']; ?></td>
					<td><?php echo $row['add_user_id']; ?></td>
					<td><?php echo $row['add_datetime']; ?></td>
					<td><?php echo $row['edit_user_id']; ?></td>
					<td><?php echo $row['edit_datetime']; ?></td>
					<td>
					<?php
						if (isActionPermitted($formcode)) {
							echo'<a href="update.php?rate_id='.$row['website_rate_id'].'"><button >
						<span class="glyphicon glyphicon-edit"></span> แก้ไข</button></a>
						<button onclick="deleteRate('.$row['website_rate_id'].');">
						<span class="glyphicon glyphicon-remove"></span> ลบ</button>';
						}
					?>
						
					</td>
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
<div class="modal fade" id="addRate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">เพิ่ม Rate ใหม่</h4>
			</div>
			<div class="modal-body">
				<form id="signupform" class="form-horizontal" role="form" method="post" >
					<div class="form-group">
							<label for="email" class="col-md-3 control-label">วันที่ </label>
							<div class="col-md-8">
								<div class="input-group input-append date">
									<input type="text" class="form-control" id="date1" placeholder="วันที่" />
									<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								</div>
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_starting_date" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label for="email" class="col-md-3 control-label">เวลา </label>
							<div class="col-md-8">
								<div class="input-group input-append date">
									<input type="text" class="form-control" id="time1" placeholder="เวลา" />
									<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
								</div>
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_begin_time" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label for="password" class="col-md-3 control-label">Rate </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="rate_cny" placeholder="Rate">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_rate_cny" style="color:red;"></label>
							</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-primary" onclick="addRate();">บันทึก</button>
				<span id="loading"></span>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="searchRate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">ค้นหา Rate</h4>
			</div>
			<div class="modal-body">
				<form action="index.php" method="post">
					<div class="form-group">
						<label>วันที่</label>
						<div class="input-group input-append date">
							<input type="text" class="form-control" name="search_starting_date" placeholder="วันที่" id="date2" />
							<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
						</div>
					</div>
					<div class="form-group">
						<label>Rate</label>
						<input type="text" class="form-control" name="search_rate_cny" placeholder="Rate">
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="search_rate">ค้นหา</button>
				<a href="index.php"><button class="btn btn-default">แสดงทั้งหมด</button></a>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- ### MODAL ### -->

<script type="text/javascript">
function deleteRate(rateID){
	swal({   
		title: 'ต้องการลบข้อมูล Rate นี้?',   
		text: 'เมื่อลบข้อมูลแล้วจะไม่สามารถกู้คืนได้!',   
		type: 'warning',
		showCancelButton: true,
		closeOnConfirm: false,   
		showLoaderOnConfirm: true,
	},
	function(isConfirm) {   
		if (isConfirm) {
			var req;
			if (window.XMLHttpRequest) {
				req = new XMLHttpRequest();
			}
			else if (window.ActiveXObject) {
				req = new ActiveXObject("Microsoft.XMLHTTP"); 
			}
			else{
				alert("Browser error");
				return false;
			}
			req.onreadystatechange = function()
			{
				if (req.readyState == 4) {
					if (req.responseText == "action_not_permitted") {
						swal({
							title: 'การทำรายการล้มเหลว',
							text: 'คุณไม่ได้รับสิทธ์ในการทำรายการนี้, โปรดติดต่อเจ้าหน้าที่',
							type: 'error',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							confirmButtonText: 'ตกลง',
							closeOnConfirm: false }, 
							function() {
								location.reload();
							}
						);
						return;
					}
					//alert('deleted');
					swal({
						title: 'ลบข้อมูล Rate',
						text: 'ข้อมูล Rate ถูกลบแล้ว!',
						type: 'success',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'ตกลง',
						closeOnConfirm: false }, 
						function() {
							location.reload();
						}
					);
				}
				else
				{
					swal.disableButtons();
					//alert('wait');
				}
			}

			req.open("POST", "delete.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
			req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			req.send("rate_id="+rateID);
			
		} else {     
			swal('Cancelled','Your imaginary file is safe :)','error');
		} 
	});
}

function addRate(){

		var flag=0;

		var starting_date = document.getElementById('date1').value;
		var begin_time = document.getElementById('time1').value;
		var rate_cny = document.getElementById('rate_cny').value;

		if (starting_date == "") {
			document.getElementById('help_starting_date').innerText = "กรุณากรอกวันที่";
			flag = 1;
		}else{
			document.getElementById('help_starting_date').innerText = "";
		}

		if (starting_date == "") {
			document.getElementById('help_begin_time').innerText = "กรุณากรอกเวลา";
			flag = 1;
		}else{
			document.getElementById('help_begin_time').innerText = "";
		}


		if (rate_cny == "") {
			document.getElementById('help_rate_cny').innerText = "กรุณากรอก Rate";
			flag = 1;
		}else{
			document.getElementById('help_rate_cny').innerText = "";
		}

		if (flag != 1) {
			var req;
			if (window.XMLHttpRequest) {
				req = new XMLHttpRequest();
			}
			else if (window.ActiveXObject) {
				req = new ActiveXObject("Microsoft.XMLHTTP"); 
			}
			else{
				alert("Browser error");
				return false;
			}
			req.onreadystatechange = function()
			{
				if (req.readyState == 4) {
					if (req.responseText == "add_not_permitted") {
						swal({
							title: 'การทำรายการล้มเหลว',
							text: 'คุณไม่ได้รับสิทธ์ในการทำรายการนี้, โปรดติดต่อเจ้าหน้าที่',
							type: 'error',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							confirmButtonText: 'ตกลง',
							closeOnConfirm: false }, 
							function() {
								location.reload();
							}
						);
						return;
					}
					//alert('deleted');

					var loading = document.getElementById('loading');
					loading.innerHTML = '';

					if (req.responseText=="SUCCESS") {
						swal({
							title: 'เพิ่มข้อมูล Rate!',
							text: 'ข้อมูล Rate ใหม่ถูกเพิ่มแล้ว!',
							type: 'success',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'ตกลง',
							closeOnConfirm: false }, 
							function() {
								location.reload();
							}
						);
					}else if (true) {
						swal(
						  'เพิ่มข้อมูล Rate!',
						  'ไม่สามารถเพิ่มได้เนื่องจากวันที่ต้องมากกว่า Rate ล่าสุด',
						  'warning'
						)
					}else{
						swal(
						  'เพิ่มข้อมูล Rate!',
						  'ไม่สามารถเพิ่มได้เนื่องจากข้อมูลไม่ถูกต้อง',
						  'warning'
						)
					}
					
				}
				else
				{
					var loading = document.getElementById('loading');
					loading.innerHTML = '<img src="../images/ajax-loader.gif">';
				}
			}

			req.open("POST", "insert.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
			req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			req.send("starting_date="+starting_date+"&rate_cny="+rate_cny+"&begin_time="+begin_time);
		}
	}

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