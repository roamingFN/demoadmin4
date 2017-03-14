<?php
$formcode = "profile";

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
	<title>Manage Customer</title>
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
	<h1><a href="index.php">Manage Customer</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
	<?php 
		if (isAddPermitted($formcode)) {
				echo'<i class="material-icons" data-toggle="modal" data-target="#addCustomer" title="Add">add_circle</i>';
		}
	?>
		<i class="material-icons" onclick="window.print();" title="Print">print</i>
		<i class="material-icons" data-toggle="modal" data-target="#searchCustomer"  title="Search">find_in_page</i>
	</div>
	<table class="detail">
		<tr>
			<th>#</th>
			<th>รหัส</th>
			<th>ชื่อ</th>
			<th>นามสกุล</th>
			<th>เบอร์โทร</th>
			<th>อีเมล์</th>
			<th>Add User</th>
			<th>Add Time</th>
			<th>Edit User</th>
			<th>Edit Time</th>
			<th>Class</th>
			<th>การกระทำ</th>
		</tr>

<?php

	$select_str = "select * from customer c, customer_class s where c.class_id=s.class_id and c.status = 1 ";

	if ($_REQUEST['search_firstname'] != '') { $select_str .= "and customer_firstname like '%".$_REQUEST['search_firstname']."%' "; }
	if ($_REQUEST['search_lastname']!= '')   { $select_str .= "and customer_lastname like '%".$_REQUEST['search_lastname']."%' ";   }
	if ($_REQUEST['search_phone']!= '')      { $select_str .= "and customer_phone = '".$_REQUEST['search_phone']."' ";    }
	if ($_REQUEST['search_email']!= '')      { $select_str .= "and customer_email = '".$_REQUEST['search_email']."' ";    }
	if ($_REQUEST['search_class']!= '')      { $select_str .= "and c.class_id = '".$_REQUEST['search_class']."' ";    }

	$search_str = "";
	if ($_REQUEST['search_firstname'] != '') { $search_str .= "&search_firstname=".$_REQUEST['search_firstname']." "; }
	if ($_REQUEST['search_lastname']!= '')   { $search_str .= "&search_lastname=".$_REQUEST['search_lastname']." ";   }
	if ($_REQUEST['search_phone']!= '')      { $search_str .= "&search_phone=".$_REQUEST['search_phone']." ";    }
	if ($_REQUEST['search_email']!= '')      { $search_str .= "&search_email=".$_REQUEST['search_email']." ";    }
	if ($_REQUEST['search_class']!= '')      { $search_str .= "&search_class=".$_REQUEST['search_class']." ";    }


	$select_str .= " order by customer_id ";

	//###### Calculate Page ######
	$objQuery = mysql_query($select_str) or die ("Error Query [".$select_str."]");
	$Num_Rows = mysql_num_rows($objQuery);

	$Per_Page = 15; 
	
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
					<td><?php echo $row['customer_code']; ?></td>
					<td><?php echo $row['customer_firstname']; ?></td>
					<td><?php echo $row['customer_lastname']; ?></td>
					<td><?php echo $row['customer_phone']; ?></td>
					<td><?php echo $row['customer_email']; ?></td>
					<td><?php echo $row['add_user_id']; ?></td>
					<td><?php echo $row['add_datetime']; ?></td>
					<td><?php echo $row['edit_user_id']; ?></td>
					<td><?php echo $row['edit_datetime']; ?></td>
					<td><?php echo $row['class_name']; ?></td>
					<td>
					<?php
						if (isActionPermitted($formcode)) {
							echo'<a href="update.php?cusid='.$row['customer_id'].'"><button>
							<span class="glyphicon glyphicon-edit"></span> แก้ไข</button></a>
							<button onclick="deleteCustomer('.$row['customer_id'].');">
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
<div class="modal fade" id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">เพิ่มลูกค้าใหม่</h4>
			</div>
			<div class="modal-body">
				<form id="signupform" class="form-horizontal" role="form" method="post" >
					<div class="form-group">
							<label for="email" class="col-md-3 control-label">อีเมล์ </label>
							<div class="col-md-8">
									<input type="text" class="form-control" name="email" id="register-email" placeholder="อีเมล์">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-register-email" style="color:red;"></label>
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
							<label for="firstname" class="col-md-3 control-label">ชื่อ </label>
							<div class="col-md-8">
									<input type="text" class="form-control" name="firstname" id="register-firstname" placeholder="ชื่อ">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-register-firstname" style="color:red;"></label>
							</div>
					</div>
					<div class="form-group">
							<label for="lastname" class="col-md-3 control-label">นามสกุล </label>
							<div class="col-md-8">
									<input type="text" class="form-control" name="lastname" id="register-lastname" placeholder="นามสกุล">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-register-lastname" style="color:red;"></label>
							</div>
					</div>
					<div class="form-group">
							<label for="phone" class="col-md-3 control-label">โทรศัพท์ </label>
							<div class="col-md-8">
									<input type="tel" class="form-control" name="phone" id="register-phone" placeholder="โทรศัพท์" maxlength="10" onkeypress="return isPhoneNumber(event)">
							</div>
							<div class="col-md-3"></div>
							<div class="col-md-8">
								<label class="control-label" id="help-register-phone" style="color:red;"></label>
							</div>
					</div>
					<div class="form-group">
						<label for="phone" class="col-md-3 control-label">Class </label>
						<div class="col-md-8">
							<select class="form-control" name="class" id="register-class">
								<?php 
								$select_class = mysql_query("select * from customer_class");
								while ($row = mysql_fetch_array($select_class)) {
									echo "<option value=".$row['class_id'].">".$row['class_name']."</option>";
								}
								?>
							</select>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-primary" onclick="validateRegisterForm();">บันทึก</button>
				<span id="loading"></span>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="searchCustomer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">ค้นหาลูกค้า</h4>
			</div>
			<div class="modal-body">
				<form action="index.php" method="post">
					<div class="form-group">
						<label>ชื่อ</label>
						<input type="text" class="form-control" name="search_firstname" placeholder="ชื่อ">
					</div>
					<div class="form-group">
						<label>นามสกุล</label>
						<input type="text" class="form-control" name="search_lastname" placeholder="นามสกุล">
					</div>
					<div class="form-group">
						<label>เบอร์โทร</label>
						<input type="text" class="form-control" name="search_phone" placeholder="เบอร์โทร" 
						onkeypress="return isPhoneNumber(event)" >
					</div>
					<div class="form-group">
						<label>อีเมล์</label>
						<input type="text" class="form-control" name="search_email" placeholder="อีเมล์">
					</div>
					<div class="form-group">
						<label>Class</label>
						<select class="form-control" id="sel1" name="search_class">
							<option value=""> - </option>
							<?php 
							$select_class = mysql_query("select * from customer_class");
							while ($row = mysql_fetch_array($select_class)) {
								echo "<option value=".$row['class_id'].">".$row['class_name']."</option>";
							}
							?>
						</select>
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="search_customer">ค้นหา</button>
				<a href="index.php"><button class="btn btn-default">แสดงทั้งหมด</button></a>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- ### MODAL ### -->

	<script type="text/javascript">

	function isPhoneNumber(evt) {
		//Enable arrow for firefox.
		if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
				if (evt.keyCode == 8 || evt.keyCode == 46 || evt.keyCode == 37 || evt.keyCode == 39) {
					return true;
			}
		}

		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;

		//Enable dash.
		if (charCode == 45) { return true; };

		if (charCode > 31 && (charCode < 48 || charCode > 57 )) {
				return false;
		}
		return true;
	}

	function deleteCustomer(cusID){
		swal({   
			title: 'ต้องการลบข้อมูลลูกค้านี้?',   
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
								title: 'ลบข้อมูลลูกค้า',
								text: 'ข้อมูลลูกค้าถูกลบแล้ว',
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
							//alert('wait');
						}
					}

					req.open("POST", "delete.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
					req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					req.send("cusid="+cusID);
					
				} else {     
					swal('Cancelled','Your imaginary file is safe :)','error');
				} 
		});
	}

	function validateRegisterForm(){

			var flag=0;

			var email = document.getElementById('register-email');
			var password = document.getElementById('register-password').value;
			var firstname = document.getElementById('register-firstname').value;
			var lastname = document.getElementById('register-lastname').value;
			var phone = document.getElementById('register-phone').value;
			var cus_class = document.getElementById('register-class').value;
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

			if (!filter.test(email.value)) {
					document.getElementById('help-register-email').innerText = "กรุณากรอกอีเมล์ให้ถูกต้อง";
					flag = 1;
			}else{

				if (document.getElementById('help-register-email').innerText == "อีเมล์นี้ถูกใช้งานไปแล้ว!") {
					flag = 1;
				}else{   
					document.getElementById('help-register-email').innerText = "";
				}
			}

			if (password == "") {
				document.getElementById('help-register-password').innerText = "กรุณากรอกรหัสผ่าน";
				flag = 1;
			}else{
				document.getElementById('help-register-password').innerText = "";
			}

			if (firstname == "") {
				document.getElementById('help-register-firstname').innerText = "กรุณากรอกชื่อ";
				flag = 1;
			}else{
				document.getElementById('help-register-firstname').innerText = "";
			}

			if (lastname == "") {
				document.getElementById('help-register-lastname').innerText = "กรุณากรอกนามสกุล";
				flag = 1;
			}else{
				document.getElementById('help-register-lastname').innerText = "";
			}

			if (phone == "") {
				document.getElementById('help-register-phone').innerText = "กรุณากรอกเบอร์โทรศัพท์";
				flag = 1;
			}else{
				document.getElementById('help-register-phone').innerText = "";
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
							title: 'เพิ่มข้อมูลลูกค้า!',
							text: 'ข้อมูลลูกค้าใหม่ถูกเพิ่มแล้ว!',
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
				req.send("email="+email.value+"&password="+password+"&firstname="+firstname+"&lastname="+lastname+"&phone="+phone+"&cus_class="+cus_class);
			}
		}

		$(document).ready(function(){
			$("#register-email").blur(function()
						{
								$.ajax({
										type: "POST",
										data: {
												email: $('#register-email').val(),
										},
										url: "emailexists.php",
										success: function(data)
										{
												if(data === 'USER_EXISTS')
												{
														$('#help-register-email')
																.css('color', 'red')
																.html("อีเมล์นี้ถูกใช้งานไปแล้ว!");
												}
												else if(data === 'USER_AVAILABLE')
												{
													if ($('#register-email').val() == "") {
														$('#help-register-email')
																	.html("กรุณากรอกอีเมล์");
													}else{ 
															$('#help-register-email')
																	.css('color', 'green')
																	.html("ท่านสามารถใช้งานอีเมล์นี้ได้");
														}
												}
										}
								})              
						}
				)

		});

	</script>
	<script src="../js/sweetalert2.min.js"></script> 
	<link rel="stylesheet" type="text/css" href="../js/sweetalert2.css">
	</body>
</html>
<?php
mysql_close();
?>
