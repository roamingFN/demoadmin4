<?php
$formcode = "manage-bank";

	include '../connect.php';
	include '../session.php';
	include '../permission.php';
	include './function.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$error = '';
$bankid = $_GET['bankid'];

	//create new topup requset
	if (!empty($_POST['edit-product-type'])) {

		//$producttypeid 		= $_POST['producttypeid'];
		$account_name 	= $_POST['account_name'];
		$account_no		= $_POST['account_no'];
		$bank_name_th	= $_POST['bank_name_th'];
		$bank_name_en	= $_POST['bank_name_en'];
		$branch	= $_POST['branch'];

		$SQL_QUERY = "update bank_payment set ";
		$FIRST_VAR  = true;

		// if(!is_null($producttypeid)){
		// 	$producttypeid 	= stripcslashes($producttypeid);
		// 	$producttypeid 	= mysql_real_escape_string($producttypeid);
		// 	if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
		// 	$SQL_QUERY .= " producttypeid = '$producttypeid' ";
		// }

		if(!is_null($account_name)){
			$account_name 	= stripcslashes($account_name);
			$account_name 	= mysql_real_escape_string($account_name);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " account_name = '$account_name' ";
		}

		if(!is_null($account_no)){
			$account_no 	= stripcslashes($account_no);
			$account_no 	= mysql_real_escape_string($account_no);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " account_no = '$account_no' ";
		}

		if(!is_null($bank_name_th)){
			$bank_name_th 	= stripcslashes($bank_name_th);
			$bank_name_th 	= mysql_real_escape_string($bank_name_th);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " bank_name_th = '$bank_name_th' ";
		}

		if(!is_null($bank_name_en)) {
			$bank_name_en 	= stripcslashes($bank_name_en);
			$bank_name_en 	= mysql_real_escape_string($bank_name_en);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " bank_name_en = '$bank_name_en' ";
		}

		if(!is_null($branch)){
			$branch 	= stripcslashes($branch);
			$branch 	= mysql_real_escape_string($branch);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " bank_branch = '$branch' ";
		}		

		$SQL_QUERY .= " where bank_id = '$bankid' ";
		$update_bank = mysql_query($SQL_QUERY);
		if ($update_bank) {
			echo '<div class="alert alert-success container" role="alert"><label>แก้ไขข้อมูลสำเร็จ</label></div>';
		}
		else{
			$error .= "<li>Error : ".mysql_error()."</li>";
			echo '<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label>'.$error.'</div>';
		}
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
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/cargo.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
	<style>
		thead{
			color:#000;
		}
		.datepicker tr{background-color: #fff;}
		.datepicker th{border-radius:0px;}
	form{
			width: 100%;
		}
	</style>
</head>
<body style="padding:10px;">
	<h1><a href="index.php">Manage Bank Payment</a></h1>
	<h3><a href="index.php">&larr; Back</a></h3><br>
		<br />
		<form class="form-horizontal" role="form" action="update.php?bankid=<?php echo $bankid ?>" 
		method="post" enctype="multipart/form-data" >
		<?php
			$bank = mysql_query("select * from bank_payment where bank_id ='$bankid'");
			$row = mysql_fetch_array($bank);
		?>
			<input type="hidden" name="bankid" value="<?php echo $row['bankid']; ?>">
			<div class="form-group">
				<label class="col-md-3 control-label">Bank Id</label>
				<div class="col-md-8">
						<input type="text" class="form-control" value="<?php echo $row['bank_id']; ?>" disabled>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Account Name</label>
				<div class="col-md-8">
						<input type="text" class="form-control" name="account_name" 
						placeholder="Account Name" value="<?php echo $row['account_name']; ?>">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Account No</label>
				<div class="col-md-8">
						<input type="text" class="form-control" name="account_no" 
						placeholder="Account No" onkeypress="return isNumber(event)" value="<?php echo $row['account_no']; ?>">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Bank Name TH</label>
				<div class="col-md-8">
						<input type="text" class="form-control" name="bank_name_th" 
						placeholder="Bank Name TH" value="<?php echo $row['bank_name_th']; ?>">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Bank Name EN</label>
				<div class="col-md-8">
						<input type="text" class="form-control" name="bank_name_en" 
						placeholder="Bank Name EN" value="<?php echo $row['bank_name_en']; ?>">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Branch</label>
				<div class="col-md-8">
						<input type="text" class="form-control" name="branch" 
						placeholder="Branch" value="<?php echo $row['bank_branch']; ?>">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8">
					<input type="submit" name="edit-product-type" class="btn btn-primary" value="บันทึก">
				</div>
			</div>

		</form>
	<br />
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
	});

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
</script>
</body>
</html>