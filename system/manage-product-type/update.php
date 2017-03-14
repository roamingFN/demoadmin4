<?php
$formcode = "manage-product-type";

	include '../connect.php';
	include '../session.php';
	include '../permission.php';
	include './function.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$error = '';
$producttypeid = $_GET['producttypeid'];

	//create new topup requset
	if (!empty($_POST['edit-product-type'])) {

		//$producttypeid 		= $_POST['producttypeid'];
		$producttypename 	= $_POST['producttypename'];
		$rate_type			= $_POST['rate_type'];
		$product_type		= $_POST['product_type'];

		$SQL_QUERY = "update product_type set ";
		$FIRST_VAR  = true;

		// if(!is_null($producttypeid)){
		// 	$producttypeid 	= stripcslashes($producttypeid);
		// 	$producttypeid 	= mysql_real_escape_string($producttypeid);
		// 	if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
		// 	$SQL_QUERY .= " producttypeid = '$producttypeid' ";
		// }

		if(!is_null($producttypename)){
			$isDupProdTypeName=0;
			if (isDupProdTypeName($producttypename)) $isDupProdTypeName=1;
			$producttypename 	= stripcslashes($producttypename);
			$producttypename 	= mysql_real_escape_string($producttypename);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " producttypename = '$producttypename' ";
		}

		if(!is_null($rate_type)){
			$rate_type 	= stripcslashes($rate_type);
			$rate_type 	= mysql_real_escape_string($rate_type);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " rate_type = '$rate_type' ";
		}

		if(!is_null($product_type)){
			$product_type 	= stripcslashes($product_type);
			$product_type 	= mysql_real_escape_string($product_type);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " product_type = '$product_type' ";
		}

		$SQL_QUERY .= " where producttypeid = '$producttypeid' ";
		$update_rate = mysql_query($SQL_QUERY);
		if ($update_rate) {
			if ($isDupProdTypeName==0) {
				echo '<div class="alert alert-success container" role="alert"><label>แก้ไขข้อมูลสำเร็จ</label></div>';
			}
			else {
				echo '<div class="alert alert-danger container" role="alert"><label>This product type name is already had in system.</label>'.$error.'</div>';
			}
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
	<title>Manage Product Type</title>
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
	<h1><a href="index.php">Manage Product Type</a></h1>
	<h3><a href="index.php">&larr; Back</a></h3><br>
		<br />
		<form class="form-horizontal" role="form" action="update.php?producttypeid=<?php echo $producttypeid ?>" 
		method="post" enctype="multipart/form-data" >
		<?php
			$product_type = mysql_query("select * from product_type where producttypeid ='$producttypeid'");
			$row = mysql_fetch_array($product_type);
		?>

			<div class="form-group">
				<label class="col-md-3 control-label">Product Type Id</label>
				<div class="col-md-8">
						<input type="text" class="form-control" value="<?php echo $row['producttypeid']; ?>" disabled>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Product Type Name</label>
				<div class="col-md-8">
						<input type="text" class="form-control" name="producttypename" 
						placeholder="Rate" value="<?php echo $row['producttypename']; ?>">
						<input type="hidden" name="producttypeid" value="<?php echo $row['producttypeid']; ?>">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Rate Type</label>
				<div class="col-md-8">
					<select class="form-control" name="rate_type">
			    	<option value="1" <?php if($row['rate_type']=="1") echo"selected"; ?> >KG</option>
			    	<option value="2" <?php if($row['rate_type']=="2") echo"selected"; ?> >Q</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Product Type</label>
				<div class="col-md-8">
					<select class="form-control" name="product_type">
				   	<option value="1" <?php if($row['product_type']=="1") echo"selected"; ?> >normal</option>
				   	<option value="2" <?php if($row['product_type']=="2") echo"selected"; ?> >special</option>
					</select>
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
</script>
</body>
</html>