<?php
$formcode = "order-remark";

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
	<title>Manage Order Remark</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>       
	<link rel="stylesheet" type="text/css" href="../css/cargo.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
</head>
<body style="padding:10px;">
	<h1><a href="index.php">Manage Order Remark</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
	<?php 
		if (isAddPermitted($formcode)) {
			echo'<i class="material-icons" data-toggle="modal" data-target="#addRemark" title="Add">add_circle</i>';
		}
	?>
		
		<i class="material-icons" onclick="window.print();" title="Print">print</i>
		<i class="material-icons" data-toggle="modal" data-target="#searchUser"  title="Search">find_in_page</i>
	</div>
	<table class="detail">
		<tr>
			<th>#</th>
			<th>Remark ID</th>
			<th>Remark Thai</th>
			<th>Remark English</th>
			<th>Action</th>
		</tr>
<?php

	$select_str = "select * from order_remark where 1 ";

	if ($_REQUEST['remark_eng'] != '') { $select_str .= "and uid = '".$_REQUEST['remark_eng']."' "; }
	if ($_REQUEST['remark_tha'] != '') { $select_str .= "and uid = '".$_REQUEST['remark_tha']."' "; }

	$select_str .= " and remark_id != 0 ";


	$search_str = "";
	if ($_REQUEST['remark_eng'] != '') { $search_str .= "&remark_eng=".$_REQUEST['remark_eng']; }
	if ($_REQUEST['remark_tha'] != '') { $search_str .= "&remark_tha=".$_REQUEST['remark_tha']; }

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

	$select_str .= "limit $Page_Start , $Per_Page ";

	//echo $select_str;
	$select_order_remark = mysql_query($select_str);

	if (mysql_num_rows($select_order_remark)>0) {
		$current_row = (($Per_Page*$Page)-$Per_Page)+1;
		while ($row = mysql_fetch_array($select_order_remark)) {
		?>
				<tr>
					<td><?php echo $current_row; ?></td>
					<td><?php echo $row['remark_id']; ?></td>
					<td><?php echo $row['remark_tha']; ?></td>
					<td><?php echo $row['remark_eng']; ?></td>
					<td>
					<?php
						if (isActionPermitted($formcode)) {
							echo '<a href="update.php?remark_id='.$row['remark_id'].'"><button >
							<span class="glyphicon glyphicon-edit"></span> แก้ไข</button></a>
							<button onclick="deleteRemark('.$row['remark_id'].');">
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
					<td class="normal"><b><?php echo mysql_num_rows($select_order_remark)."/".mysql_num_rows($objQuery); ?></b></td>
			</tr>
		</table>
	</div>
<!-- ### MODAL ### -->
<div class="modal fade" id="addRemark" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">เพิ่มหมายเหตุออร์เดอร์ใหม่</h4>
			</div>
			<div class="modal-body">
				<form id="signupform" class="form-horizontal" role="form" method="post" >
					<div class="form-group">
							<label class="col-md-6 control-label">Remark ID</label>
							<div class="col-md-5">
									<input type="text" class="form-control" id="remark_id" >
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-remark_id" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-6 control-label">หมายเหตุออร์เดอร์ภาษาไทย</label>
							<div class="col-md-5">
									<input type="text" class="form-control" id="remark_tha" >
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-remark_tha" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-6 control-label">หมายเหตุออร์เดอร์ภาษาอังกฤษ </label>
							<div class="col-md-5">
									<input type="text" class="form-control" id="remark_eng" >
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-remark_eng" style="color:red;"></label>
							</div>
					</div>

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-primary" onclick="addRemark();">บันทึก</button>
				<span id="loading"></span>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="searchUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">ค้นหา หมายเหตุออร์เดอร์</h4>
			</div>
			<div class="modal-body">
				<form class="form-inline" action="index.php" method="post">
					<div class="form-group">
						<label class="sr-only" >Remark Thai</label>
						<input type="text" class="form-control" name="remark_tha">
					</div>
					<div class="form-group">
						<label class="sr-only" >Remark English</label>
						<input type="text" class="form-control" name="remark_eng">
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="search_user">ค้นหา</button>
					<a href="index.php"><button class="btn btn-default">แสดงทั้งหมด</button></a>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- ### MODAL ### -->

<script type="text/javascript">
function deleteRemark(remark_id){
	swal({   
		title: 'ต้องการลบข้อมูลหมายเหตุนี้?',   
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
						title: 'ลบข้อมูลเรียบร้อย',
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
			req.send("remark_id="+remark_id);
			
		} else {     
			swal('Cancelled','Your imaginary file is safe :)','error');
		} 
	});
}

function addRemark(){

		var flag=0;

		var remark_id = document.getElementById('remark_id').value;
		var remark_tha = document.getElementById('remark_tha').value;
		var remark_eng = document.getElementById('remark_eng').value;

		if (remark_id == "") {
			document.getElementById('help-remark_id').innerText = "กรุณากรอกไอดี";
			flag = 1;
		}else{
			document.getElementById('help-remark_id').innerText = "";
		}

		if (remark_tha == "") {
			document.getElementById('help-remark_tha').innerText = "กรุณากรอกหมายเหตุออร์เดอร์ภาษาไทย";
			flag = 1;
		}else{
			document.getElementById('help-remark_tha').innerText = "";
		}

		if (remark_eng == "") {
			document.getElementById('help-remark_eng').innerText = "กรุณากรอกหมายเหตุออร์เดอร์ภาษาอังกฤษ";
			flag = 1;
		}else{
			document.getElementById('help-remark_eng').innerText = "";
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

					if (req.responseText == "success") {
						swal({
							title: 'เพิ่มหมายเหตุออร์เดอร์เรียบร้อย',
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
					}else{
						alert(req.responseText);
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
			req.send("remark_id="+remark_id+"&remark_tha="+remark_tha+"&remark_eng="+remark_eng);
		}
	}


</script>
<script src="../js/sweetalert2.min.js"></script> 
<link rel="stylesheet" type="text/css" href="../js/sweetalert2.css">
</body>
</html>