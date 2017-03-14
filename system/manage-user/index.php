<?php
$formcode = "manage-user";

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
	<title>Manage User</title>
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
	<h1><a href="index.php">Manage User</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
	<?php 
		if (isAddPermitted($formcode)) {
			echo'<i class="material-icons" data-toggle="modal" data-target="#addUser" title="Add">add_circle</i>';
		}
	?>
		
		<i class="material-icons" onclick="window.print();" title="Print">print</i>
		<i class="material-icons" data-toggle="modal" data-target="#searchUser"  title="Search">find_in_page</i>
	</div>
	<table class="detail">
		<tr>
			<th>#</th>
			<th>ID</th>
			<th>Email</th>
			<th>Status</th>
			<th>Add User</th>
			<th>Add Date</th>
			<th>Edit User</th>
			<th>Edit Date</th>
			<th>การกระทำ</th>
		</tr>
<?php

	$select_str = "select * from user where 1 ";


	if ($_REQUEST['search_id'] != '') { $select_str .= "and uid = '".$_REQUEST['search_id']."' "; }

	$search_str = "";
	if ($_REQUEST['search_id'] != '') { $search_str .= "&search_id=".$_REQUEST['search_id']; }

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
	$select_customer = mysql_query($select_str);

	if (mysql_num_rows($select_customer)>0) {
		$current_row = (($Per_Page*$Page)-$Per_Page)+1;
		while ($row = mysql_fetch_array($select_customer)) {
		?>
				<tr>
					<td><?php echo $current_row; ?></td>
					<td><?php echo $row['uid']; ?></td>
					<td><?php echo $row['email']; ?></td>
					<td><?php 
						if ($row['disable'] == 0) {
							echo "Active";
						}else if($row['disable'] == 1){
							echo "Inactive"; 
						}else{
							echo $row['disable']; 
						}
					?></td>
					<td><?php echo $row['adduser']; ?></td>
					<td><?php echo $row['adddate']; ?></td>
					<td><?php echo $row['edituser']; ?></td>
					<td><?php echo $row['editdate']; ?></td>
					<td>
					<?php
						if (isActionPermitted($formcode)) {
							echo'<a href="update.php?user_id='.$row['userid'].'"><button >
							<span class="glyphicon glyphicon-edit"></span> แก้ไข</button></a>
							<button onclick="deleteUser('.$row['userid'].');">
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
					<td class="normal"><b><?php echo mysql_num_rows($select_customer)."/".mysql_num_rows($objQuery); ?></b></td>
			</tr>
		</table>
	</div>
<!-- ### MODAL ### -->
<div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">เพิ่มผู้ใช้งานใหม่</h4>
			</div>
			<div class="modal-body">
				<form id="signupform" class="form-horizontal" role="form" method="post" >
					<div class="form-group">
							<label for="email" class="col-md-3 control-label">รหัสผู้ใช้งาน </label>
							<div class="col-md-8">
									<input type="text" class="form-control" name="uid" id="register-uid" placeholder="รหัสผู้ใช้งาน">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-register-uid" style="color:red;"></label>
							</div>
					</div>
					
					<div class="form-group">
							<label for="password" class="col-md-3 control-label">รหัสผ่าน </label>
							<div class="col-md-8">
									<input type="password" class="form-control" name="password" id="register-password" placeholder="รหัสผ่าน">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-register-password" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label for="email" class="col-md-3 control-label">Email </label>
							<div class="col-md-8">
									<input type="text" class="form-control" name="email" id="register-email" placeholder="Email">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label for="email" class="col-md-3 control-label">Disable </label>
							<div class="col-md-8">
									<select class="form-control" name="disable" id="register-disable">
			              <option value="no">No</option>
			              <option value="yes">Yes</option>
			            </select>
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" style="color:red;"></label>
							</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-primary" onclick="addUser();">บันทึก</button>
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
				<h4 class="modal-title">ค้นหาผู้ใช้งาน</h4>
			</div>
			<div class="modal-body">
				<form class="form-inline" action="index.php" method="post">
					<div class="form-group">
						<label class="sr-only" >ID ผู้ใช้งาน</label>
						<input type="text" class="form-control" name="search_id" placeholder="uid">
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
function deleteUser(userID){
	swal({   
		title: 'ต้องการลบข้อมูลผู้ใช้งานนี้?',   
		text: 'เมื่อลบข้อมูลผู้ใช้งานแล้วจะไม่สามารถกู้คืนได้',   
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
						title: 'ลบข้อมูลผู้ใช้',
						text: 'ข้อมูลผู้ใช้ถูกลบแล้ว!',
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
			req.send("user_id="+userID);
			
		} else {     
			swal('Cancelled','Your imaginary file is safe :)','error');
		} 
	});
}

function addUser(){

		var flag=0;

		var password = document.getElementById('register-password').value;
		var uid = document.getElementById('register-uid').value;
		var email = document.getElementById('register-email').value;
		var disable = document.getElementById('register-disable').value;

		if (password == "") {
			document.getElementById('help-register-password').innerText = "กรุณากรอกรหัสผ่าน";
			flag = 1;
		}else{
			document.getElementById('help-register-password').innerText = "";
		}

		if (uid == "") {
			document.getElementById('help-register-uid').innerText = "กรุณากรอกรหัสผู้ใช้งาน";
			flag = 1;
		}else{
			document.getElementById('help-register-uid').innerText = "";
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
					
					var loading = document.getElementById('loading');
					loading.innerHTML = '';

					swal({
						title: 'เพิ่มข้อมูลผู้ใช้งาน!',
						text: 'ข้อมูลผู้ใช้งานถูกเพิ่มแล้ว!',
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
			alert("uid="+uid+"&password="+password+"&email="+email+"&disable="+disable);
			req.open("POST", "insert.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
			req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			req.send("uid="+uid+"&password="+password+"&email="+email+"&disable="+disable);
		}
	}


</script>
<script src="../js/sweetalert2.min.js"></script> 
<link rel="stylesheet" type="text/css" href="../js/sweetalert2.css">
</body>
</html>