<?php
$formcode = "manage-bank";

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
	<title>Manage Bank Payment</title>
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
	<h1><a href="index.php">Manage Bank Payment</a></h1>
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
			<th>#ลำดับ</th>
			<th>ID</th>
			<th>Account Name</th>
			<th>Account No.</th>
			<th>Bank Name TH</th>
			<th>Bank Name EN</th>
			<th>Branch</th>
			<th>การกระทำ</th>
		</tr>
<?php
	//query here
	$select_str = "select * from bank_payment where 1 ";

	if ($_REQUEST['search_bank_id']!= '') { $select_str .= "and bank_id = '".$_REQUEST['search_bank_id']."' "; }
	if ($_REQUEST['search_account_name']!= '') { $select_str .= "and account_name like '%".$_REQUEST['search_account_name']."%' "; }
	if ($_REQUEST['search_account_no']!= '') { $select_str .= "and account_no = '".$_REQUEST['search_account_no']."' "; }
	if ($_REQUEST['search_bank_name_th']!= '') { $select_str .= "and bank_name_th like '%".$_REQUEST['search_bank_name_th']."%' "; }
	if ($_REQUEST['search_bank_name_en']!= '') { $select_str .= "and bank_name_en like '%".$_REQUEST['search_bank_name_en']."%' "; }
	if ($_REQUEST['search_branch']!= '') { $select_str .= "and branch like '%".$_REQUEST['search_branch']."%' "; }

	$search_str = "";
	if ($_REQUEST['search_bank_id']!= '') { $search_str .= "&search_bank_id=".$_REQUEST['search_bank_id']; }
	if ($_REQUEST['search_account_name']!= '') { $search_str .= "&search_account_name=".$_REQUEST['search_account_name']; }
	if ($_REQUEST['search_account_no']!= '') { $search_str .= "&search_account_no=".$_REQUEST['search_account_no']; }
	if ($_REQUEST['search_bank_name_th']!= '') { $search_str .= "&search_bank_name_th=".$_REQUEST['search_bank_name_th']; }
	if ($_REQUEST['search_bank_name_en']!= '') { $search_str .= "&search_bank_name_en=".$_REQUEST['search_bank_name_en']; }
	if ($_REQUEST['search_branch']!= '') { $search_str .= "&search_branch=".$_REQUEST['search_branch']; }

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
	$select_bank = mysql_query($select_str);

	if (mysql_num_rows($select_bank)>0) {
		$current_row = (($Per_Page*$Page)-$Per_Page)+1;
		while ($row = mysql_fetch_array($select_bank)) {
		?>
				<tr>
					<td><?php echo $current_row; ?></td>
					<td><?php echo $row['bank_id']; ?></td>
					<td><?php echo $row['account_name']; ?></td>
					<td><?php echo $row['account_no']; ?></td>
					<td><?php echo $row['bank_name_th']; ?></td>
					<td><?php echo $row['bank_name_en']; ?></td>
					<td><?php echo $row['bank_branch']; ?></td>
					<td>
					<?php
						if (isActionPermitted($formcode)) {
							echo'<a href="update.php?bankid='.$row['bank_id'].'"><button >
						<span class="glyphicon glyphicon-edit"></span> แก้ไข</button></a>
						<button onclick="deleteBank('.$row['bank_id'].');">
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
					<td class="normal"><b><?php echo mysql_num_rows($select_bank)."/".mysql_num_rows($objQuery); ?></b></td>
			</tr>
		</table>
	</div>

<!-- ### MODAL ### -->
<!-- add box -->
<div class="modal fade" id="addRate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">เพิ่ม Bank Account ใหม่</h4>
			</div>
			<div class="modal-body">
				<form id="signupform" class="form-horizontal" role="form" method="post" >
					
					<div class="form-group">
							<label class="col-md-3 control-label">Bank ID</label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="bankid" onkeypress="return isNumber(event)" >
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_bankid" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-3 control-label">Account Name</label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="account_name">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_account_name" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-3 control-label">Account No.</label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="account_no" onkeypress="return isNumber(event)" >
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_account_no" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-3 control-label">Bank Name TH</label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="bank_name_th">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_bank_name_th" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-3 control-label">Bank Name EN</label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="bank_name_en">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_bank_name_en" style="color:red;"></label>
							</div>
					</div>

					<div class="form-group">
							<label class="col-md-3 control-label">Branch</label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="branch">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help_branch" style="color:red;"></label>
							</div>
					</div>

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-primary" onclick="addBank();">บันทึก</button>
				<span id="loading"></span>
			</div>
		</div>
	</div>
</div>

<!-- search box -->
<div class="modal fade" id="searchRate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">ค้นหา Bank Account</h4>
			</div>
			<div class="modal-body">
				<form action="index.php" class="form-horizontal" method="post">
					<div class="form-group">
						<label class="col-md-3 control-label">Bank ID</label>
						<div class="col-md-8 form-inline">
							<input type="text" class="form-control" name="search_bank_id" onkeypress="return isNumber(event)" >
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Account Name</label>
						<div class="col-md-8 form-inline">
							<input type="text" class="form-control" name="search_account_name">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Account No.</label>
						<div class="col-md-8 form-inline">
							<input type="text" class="form-control" name="search_account_no">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Bank Name TH</label>
						<div class="col-md-8 form-inline">
							<input type="text" class="form-control" name="search_bank_name_th">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Bank Name EN</label>
						<div class="col-md-8 form-inline">
							<input type="text" class="form-control" name="search_bank_name_en">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Branch</label>
						<div class="col-md-8 form-inline">
							<input type="text" class="form-control" name="search_branch">
						</div>
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default">ค้นหา</button>
				<a href="index.php"><button class="btn btn-default">แสดงทั้งหมด</button></a>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- ### END MODAL ### -->

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

function deleteBank(bankid){
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
			req.send("bankid="+bankid);
			
		} else {     
			swal('Cancelled','Your imaginary file is safe :)','error');
		} 
	});
}

function addBank(){

		var flag=0;

		var bankid = document.getElementById('bankid').value;
		var account_name = document.getElementById('account_name').value;
		var account_no = document.getElementById('account_no').value;
		var bank_name_th = document.getElementById('bank_name_th').value;
		var bank_name_en = document.getElementById('bank_name_en').value;
		var branch = document.getElementById('branch').value;

		if (bankid == "") {
			document.getElementById('help_bankid').innerText = "กรุณากรอก bank id";
			flag = 1;
		}else{
			document.getElementById('help_bankid').innerText = "";
		}

		if (account_name == "") {
			document.getElementById('help_account_name').innerText = "กรุณากรอก account name";
			flag = 1;
		}else{
			document.getElementById('help_account_name').innerText = "";
		}

		if (account_no == "") {
			document.getElementById('help_account_no').innerText = "กรุณากรอก account no";
			flag = 1;
		}else{
			document.getElementById('help_account_no').innerText = "";
		}

		if (bank_name_th == "") {
			document.getElementById('help_bank_name_th').innerText = "กรุณากรอก bank name TH";
			flag = 1;
		}else{
			document.getElementById('help_bank_name_th').innerText = "";
		}
		if (bank_name_en == "") {
			document.getElementById('help_bank_name_en').innerText = "กรุณากรอก bank name EN";
			flag = 1;
		}else{
			document.getElementById('help_bank_name_en').innerText = "";
		}
		if (branch == "") {
			document.getElementById('help_branch').innerText = "กรุณากรอก branch";
			flag = 1;
		}else{
			document.getElementById('help_branch').innerText = "";
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

					if (req.responseText=='') {
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
					}else{
						sweetAlert(
						  'ไม่สามารถเพิ่มข้อมูลได้',
						  req.responseText,
						  'error'
						);
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
			req.send("bankid="+bankid
				+"&account_name="+account_name
				+"&account_no="+account_no
				+"&bank_name_th="+bank_name_th
				+"&bank_name_en="+bank_name_en
				+"&branch="+branch
			);
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