<?php
$formcode = "manage-transportation";

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
	<h1><a href="index.php">Manage Transportation</a></h1>
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
			<th>ลำดับ</th>
			<th>ชื่อบริษัทขนส่ง (ภาษาไทย)</th>
			<th>ชื่อบริษัทขนส่ง (ภาษาอังกฤษ)</th>
			<th></th>
		</tr>
<?php

	$select_str = "select * from website_transport where 1 ";

	if ($_REQUEST['search_transport_th_name']!= '') { $select_str .= "and transport_th_name like '%".$_REQUEST['search_transport_th_name']."%' "; }
	if ($_REQUEST['search_transport_eng_name']!= '') { $select_str .= "and transport_eng_name like '%".$_REQUEST['search_transport_eng_name']."%' "; }

	$search_str = "";
	if ($_REQUEST['search_transport_th_name']!= '') { $search_str .= "&search_transport_th_name=".$_REQUEST['search_transport_th_name']; }
	if ($_REQUEST['search_transport_eng_name']!= '') { $search_str .= "&search_transport_eng_name=".$_REQUEST['search_transport_eng_name']; }

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
	$select_transport = mysql_query($select_str);

	if (mysql_num_rows($select_transport)>0) {
		$current_row = (($Per_Page*$Page)-$Per_Page)+1;
		while ($row = mysql_fetch_array($select_transport)) {
		?>
				<tr>
					<td><?php echo $current_row; ?></td>
					<td><?php echo $row['transport_th_name']; ?></td>
					<td><?php echo $row['transport_eng_name']; ?></td>
					<td>
					<?php
						if (isActionPermitted($formcode)) {
							echo'<a href="update.php?transport_id='.$row['transport_id'].'"><button >
									<span class="glyphicon glyphicon-edit"></span> แก้ไข</button></a>
									<button onclick="deleteTransport('.$row['transport_id'].');">
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
					<td class="normal"><b><?php echo mysql_num_rows($select_transport)."/".mysql_num_rows($objQuery); ?></b></td>
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
				<h4 class="modal-title">เพิ่มบริการขนส่งใหม่</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" role="form" method="post" >
					
					<div class="form-group">
							<label class="col-md-3 control-label">ชื่อบริษัทขนส่ง (ภาษาไทย) </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="transport_th_name" placeholder="ชื่อบริษัทขนส่ง">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_transport_th_name" style="color:red;"></label>
							</div>
					</div>
					<div class="form-group">
							<label class="col-md-3 control-label">ชื่อบริษัทขนส่ง (ภาษาอังกฤษ) </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="transport_eng_name" placeholder="ชื่อบริษัทขนส่ง">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_transport_eng_name" style="color:red;"></label>
							</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-primary" onclick="addTransport();">บันทึก</button>
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
				<h4 class="modal-title">ค้นหาบริษัทขนส่ง</h4>
			</div>
			<div class="modal-body">
				<form action="index.php" method="post">
					<div class="form-group">
						<label for="exampleInputPassword3">ชื่อภาษาไทย</label>
						<input type="text" class="form-control" name="search_transport_th_name" placeholder="ชื่อภาษาไทย">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword3">ชื่อภาษาอังกฤษ</label>
						<input type="text" class="form-control" name="search_transport_eng_name" placeholder="ชื่อภาษาอังกฤษ">
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="search_transport">ค้นหา</button>
				<a href="index.php"><button class="btn btn-default">แสดงทั้งหมด</button></a>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- ### MODAL ### -->

<script type="text/javascript">
function deleteTransport(transportID){
	swal({   
		title: 'ต้องการลบข้อมูลบริษัทขนส่งนี้?',   
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
						title: 'ลบข้อมูลบริษัทขนส่ง',
						text: 'ลบข้อมูลเรียบร้อยแล้ว!',
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
			req.send("transport_id="+transportID);
			
		} else {     
			swal('Cancelled','Your imaginary file is safe :)','error');
		} 
	});
}

function addTransport(){

		var flag=0;

		var transport_th_name = document.getElementById('transport_th_name').value;
		var transport_eng_name = document.getElementById('transport_eng_name').value;

		if (transport_th_name == "") {
			document.getElementById('help_transport_th_name').innerText = "กรุณากรอก ชื่อบริษัทขนส่ง (ภาษาไทย)";
			flag = 1;
		}else{
			document.getElementById('help_transport_th_name').innerText = "";
		}

		if (transport_eng_name == "") {
			document.getElementById('help_transport_eng_name').innerText = "กรุณากรอก ชื่อบริษัทขนส่ง (ภาษาอังกฤษ)";
			flag = 1;
		}else{
			document.getElementById('help_transport_eng_name').innerText = "";
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

					swal({
						title: 'เพิ่มข้อมูลบริษัทขนส่งสินค้า!',
						text: 'เพิ่มข้อมูล บริษัทใหม่เรียบร้อยแล้ว!',
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
			req.send("transport_th_name="+transport_th_name+"&transport_eng_name="+transport_eng_name);
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