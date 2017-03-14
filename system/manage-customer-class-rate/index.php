<?php
$formcode = "manage-class-rate";

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
	<title>Manage Customer Class Rate</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/cargo.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
	<style>
		.datepicker{z-index:1151 !important;}
		.datepicker tr {background-color: #fff;}
	</style>
</head>
<body style="padding:10px;">
	<h1><a href="index.php">Manage Customer Class Rate</a></h1>
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
			<th>Class</th>
			<th>Rate Type</th>
			<th>Begincal</th>
			<th>Endcal</th>
			<th>Product Type</th>
			<th>Rate Amount</th>
			<th>ดำเนินการ</th>
		</tr>
<?php
	$class_name = array();
	$select_class = mysql_query("select * from customer_class");
	while ($row = mysql_fetch_array($select_class)) {
		$class_name[$row['class_id']] = $row['class_name'];
	}

	function convertRateType($status){
		switch ($status) {
			case '1':
				return "KG";
			case '2':
				return "Q";
			default:
				return $status;
		}
	}

	function convertProductType($status){
		switch ($status) {
			case '1':
				return "normal";
			case '2':
				return "special";
			default:
				return $status;
		}
	}

	$select_str = "select * from customer_class_rate where 1 ";

	
	if ($_REQUEST['search_class_id']!= '') { $select_str .= "and class_id = '".$_REQUEST['search_class_id']."' "; }
	if ($_REQUEST['search_begincal_start']!= '') { $select_str .= "and begincal >= '".$_REQUEST['search_begincal_start']."' "; }
	if ($_REQUEST['search_begincal_end']!= '') { $select_str .= "and begincal <= '".$_REQUEST['search_begincal_end']."' "; }
	if ($_REQUEST['search_endcal_start']!= '') { $select_str .= "and endcal >= '".$_REQUEST['search_endcal_start']."' "; }
	if ($_REQUEST['search_endcal_end']!= '') { $select_str .= "and endcal <= '".$_REQUEST['search_endcal_end']."' "; }
	if ($_REQUEST['search_rate_type']!= '') { $select_str .= "and rate_type = '".$_REQUEST['search_rate_type']."' "; }
	if ($_REQUEST['search_product_type']!= '') { $select_str .= "and product_type = '".$_REQUEST['search_product_type']."' "; }
	if ($_REQUEST['search_rate_amount_start']!= '') { $select_str .= "and rate_amount >= '".$_REQUEST['search_rate_amount_start']."' "; }
	if ($_REQUEST['search_rate_amount_end']!= '') { $select_str .= "and rate_amount <= '".$_REQUEST['search_rate_amount_end']."' "; }

	$search_str = "";
	if ($_REQUEST['search_class_id']!= '') { $search_str .= "&search_class_id=".$_REQUEST['search_class_id']; }
	if ($_REQUEST['search_begincal_start']!= '') { $search_str .= "&search_begincal_start=".$_REQUEST['search_begincal_start']; }
	if ($_REQUEST['search_begincal_end']!= '') { $search_str .= "&search_begincal_end=".$_REQUEST['search_begincal_end']; }
	if ($_REQUEST['search_endcal_start']!= '') { $search_str .= "&search_endcal_start=".$_REQUEST['search_endcal_start']; }
	if ($_REQUEST['search_endcal_end']!= '') { $search_str .= "&search_endcal_end=".$_REQUEST['search_endcal_end']; }
	if ($_REQUEST['search_rate_type']!= '') { $search_str .= "&search_rate_type=".$_REQUEST['search_rate_type']; }
	if ($_REQUEST['search_product_type']!= '') { $search_str .= "&search_product_type=".$_REQUEST['search_product_type']; }
	if ($_REQUEST['search_rate_amount_start']!= '') { $search_str .= "&search_rate_amount_start=".$_REQUEST['search_rate_amount_start']; }
	if ($_REQUEST['search_rate_amount_end']!= '') { $search_str .= "&search_rate_amount_end=".$_REQUEST['search_rate_amount_end']; }


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

	//$select_str .= "order by starting_date desc ";
	$select_str .= "limit $Page_Start , $Per_Page ";

	//echo $select_str;
	$select_rate = mysql_query($select_str);

	if (mysql_num_rows($select_rate)>0) {
		$current_row = (($Per_Page*$Page)-$Per_Page)+1;
		while ($row = mysql_fetch_array($select_rate)) {
		?>
				<tr>
					<td><?php echo $current_row; ?></td>
					<td><?php echo $class_name[$row['class_id']]; ?></td>
					<td><?php echo convertRateType($row['rate_type']); ?></td>
					<td><?php echo $row['begincal']; ?></td>
					<td><?php echo $row['endcal']; ?></td>
					<td><?php echo convertProductType($row['product_type']); ?></td>
					<td><?php echo $row['rate_amount']; ?></td>
					<td>
					<?php
						if (isActionPermitted($formcode)) {
							echo'<a href="update.php?running='.$row['running'].'"><button >
							<span class="glyphicon glyphicon-edit"></span> แก้ไข</button></a>
							<button onclick="deleteClassRate('.$row['running'].');">
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
				<h4 class="modal-title">เพิ่ม Customer Class Rate ใหม่</h4>
			</div>
			<div class="modal-body">
				<form id="signupform" class="form-horizontal" role="form" method="post" >
					
					<div class="form-group">
							<label class="col-md-3 control-label">Class</label>
							<div class="col-md-8">
								<select class="form-control" id="class_id">
									<?php 
									$select_class = mysql_query("select * from customer_class");
									while ($row = mysql_fetch_array($select_class)) {
										echo "<option value=".$row['class_id'].">".$row['class_name']."</option>";
									}
									?>
								</select>
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_class_id" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-3 control-label">Rate Type</label>
							<div class="col-md-8">
								<select class="form-control" id="rate_type">
									<option value="1">KG</option>
									<option value="2">Q</option>
								</select>
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_rate_type" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-3 control-label">Begincal</label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="begincal" onkeypress="return isNumber(event)" >
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_begincal" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-3 control-label">Endcal</label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="endcal" onkeypress="return isNumber(event)" >
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_endcal" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-3 control-label">Product Type</label>
							<div class="col-md-8">
									<select class="form-control" id="product_type">
										<option value="1">normal</option>
										<option value="2">special</option>
									</select>
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_product_type" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-3 control-label">Rate Amount</label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="rate_amount" onkeypress="return isNumber(event)" >
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_rate_amount" style="color:red;"></label>
							</div>
					</div>

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-primary" onclick="addClassRate();">บันทึก</button>
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
				<h4 class="modal-title">ค้นหา Customer Class Rate</h4>
			</div>
			<div class="modal-body">
				<form action="index.php" class="form-horizontal" method="post">
					<div class="form-group">
						<label class="col-md-3 control-label">class</label>
						<div class="col-md-8">
							<select class="form-control" name="search_class_id">
								<option value="">ไม่เลือก</option>
								<?php 
								$select_class = mysql_query("select * from customer_class");
								while ($row = mysql_fetch_array($select_class)) {
									echo "<option value=".$row['class_id'].">".$row['class_name']."</option>";
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">rate type</label>
						<div class="col-md-8">
							<select class="form-control" name="search_rate_type">
								<option value="">ไม่เลือก</option>
								<option value="1">KG</option>
								<option value="2">Q</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">begincal</label>
						<div class="col-md-8 form-inline">
							<input type="text" class="form-control" name="search_begincal_start" placeholder="ค่าเริ่มต้น" onkeypress="return isNumber(event)" >
							<input type="text" class="form-control" name="search_begincal_end" placeholder="ค่าสิ้นสุด" onkeypress="return isNumber(event)" >
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">endcal</label>
						<div class="col-md-8 form-inline">
							<input type="text" class="form-control" name="search_endcal_start" placeholder="ค่าเริ่มต้น" onkeypress="return isNumber(event)" >
							<input type="text" class="form-control" name="search_endcal_end" placeholder="ค่าสิ้นสุด" onkeypress="return isNumber(event)" >
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">product type</label>
						<div class="col-md-8">
							<select class="form-control" name="search_product_type">
								<option value="">ไม่เลือก</option>
								<option value="1">normal</option>
								<option value="2">special</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">rate amount</label>
						<div class="col-md-8 form-inline">
							<input type="text" class="form-control" name="search_rate_amount_start" placeholder="ค่าเริ่มต้น" onkeypress="return isNumber(event)" >
							<input type="text" class="form-control" name="search_rate_amount_end" placeholder="ค่าสิ้นสุด" onkeypress="return isNumber(event)" >
						</div>
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="search_class_rate">ค้นหา</button>
				<a href="index.php"><button class="btn btn-default">แสดงทั้งหมด</button></a>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- ### MODAL ### -->

<script type="text/javascript">

function isNumber(evt) {
	//Enable arrow for firefox.
	if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
	    if (evt.keyCode == 8 || evt.keyCode == 46 || evt.keyCode == 37 || evt.keyCode == 39) {
		    return true;
		}
	}

    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;

    //Enable dot.
    if (charCode == 46) { return true; };

    if (charCode > 31 && (charCode < 48 || charCode > 57 )) {
        return false;
    }
    return true;
}

function deleteClassRate(running){
	swal({   
		title: 'ต้องการลบข้อมูลนี้?',   
		text: '',   
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
					//alert('deleted');
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

					swal({
						title: 'ลบข้อมูลเรียบร้อยแล้ว',
						text: '',
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
			req.send("running="+running);
			
		} else {     
			swal('Cancelled','Your imaginary file is safe :)','error');
		} 
	});
}

function addClassRate(){

		var flag=0;

		var class_id = document.getElementById('class_id').value;
		var rate_type = document.getElementById('rate_type').value;
		var begincal = document.getElementById('begincal').value;
		var endcal = document.getElementById('endcal').value;
		var product_type = document.getElementById('product_type').value;
		var rate_amount = document.getElementById('rate_amount').value;

		if (class_id == "") {
			document.getElementById('help_class_id').innerText = "กรุณากรอก class id";
			flag = 1;
		}else{
			document.getElementById('help_class_id').innerText = "";
		}

		if (rate_type == "") {
			document.getElementById('help_rate_type').innerText = "กรุณากรอก rate type";
			flag = 1;
		}else{
			document.getElementById('help_rate_type').innerText = "";
		}

		if (begincal == "") {
			document.getElementById('help_begincal').innerText = "กรุณากรอก begincal";
			flag = 1;
		}else{
			document.getElementById('help_begincal').innerText = "";
		}

		if (endcal == "") {
			document.getElementById('help_endcal').innerText = "กรุณากรอก endcal";
			flag = 1;
		}else{
			document.getElementById('help_endcal').innerText = "";
		}

		if (product_type == "") {
			document.getElementById('help_product_type').innerText = "กรุณากรอก product type";
			flag = 1;
		}else{
			document.getElementById('help_product_type').innerText = "";
		}

		if (rate_amount == "") {
			document.getElementById('help_rate_amount').innerText = "กรุณากรอก rate amount";
			flag = 1;
		}else{
			document.getElementById('help_rate_amount').innerText = "";
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
					//alert('deleted');
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
					
					var loading = document.getElementById('loading');
					loading.innerHTML = '';

					swal({
						title: 'เพิ่มข้อมูลเรียบร้อย',
						text: '',
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
					var loading = document.getElementById('loading');
					loading.innerHTML = '<img src="../images/ajax-loader.gif">';
				}
			}

			req.open("POST", "insert.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
			req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			req.send("class_id="+class_id+"&rate_type="+rate_type+"&begincal="+begincal+"&endcal="+endcal+"&product_type="+product_type+"&rate_amount="+rate_amount);
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
});

</script>
<script src="../js/sweetalert2.min.js"></script> 
<link rel="stylesheet" type="text/css" href="../js/sweetalert2.css">
</body>
</html>