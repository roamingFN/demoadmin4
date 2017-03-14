<?php
$formcode = "manage-class-rate";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$error = '';
$running = $_GET['running'];

	//create new topup requset
	if (!empty($_POST['edit-class-rate'])) {

		$class_id 			= $_POST['class_id'];
		$begincal 			= $_POST['begincal'];
		$endcal				  = $_POST['endcal'];
		$rate_type			= $_POST['rate_type'];
		$product_type		= $_POST['product_type'];
		$rate_amount		= $_POST['rate_amount'];

		$SQL_QUERY = "update customer_class_rate set ";
		$FIRST_VAR  = true;

		if(!is_null($class_id)){
			$class_id 	= stripcslashes($class_id);
			$class_id 	= mysql_real_escape_string($class_id);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " class_id = '$class_id' ";
		}

		if(!is_null($begincal)){
			$begincal 	= stripcslashes($begincal);
			$begincal 	= mysql_real_escape_string($begincal);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " begincal = '$begincal' ";
		}

		if(!is_null($endcal)){
			$endcal 	= stripcslashes($endcal);
			$endcal 	= mysql_real_escape_string($endcal);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " endcal = '$endcal' ";
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

		if(!is_null($rate_amount)){
			$rate_amount 	= stripcslashes($rate_amount);
			$rate_amount 	= mysql_real_escape_string($rate_amount);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " rate_amount = '$rate_amount' ";
		}

		$SQL_QUERY .= " where running = '$running' ";

		$update_rate = mysql_query($SQL_QUERY);
		if ($update_rate) {
			echo '<div class="alert alert-success container" role="alert"><label>แก้ไขข้อมูลสำเร็จ</label></div>';
		}else{
			$error .= "<li>Error : ".mysql_error()."</li>";
			echo '<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label>'.$error.'</div>';
		}
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
	<h1><a href="index.php">Manage Customer Class Rate</a></h1>
	<h3><a href="index.php">&larr; Back</a></h3><br>
		<br />
		<form class="form-horizontal" role="form" action="update.php?running=<?php echo $running ?>" 
		method="post" enctype="multipart/form-data" >
		<?php
			$select_rate = mysql_query("select * from customer_class_rate where running ='$running'");
			$row = mysql_fetch_array($select_rate);
		?>

			<div class="form-group">
				<label class="col-md-3 control-label">Class ID</label>
				<div class="col-md-8">
					<select class="form-control" name="class_id">
						<?php 
						$select_class = mysql_query("select * from customer_class");
						while ($row2 = mysql_fetch_array($select_class)) {
							$selected = "";
							if ($row2['class_id']== $row['class_id']) {
								$selected = "selected";
							}
							echo "<option value=".$row2['class_id']." ".$selected." >".$row2['class_name']."</option>";
						}
						?>
					</select>
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
				<label class="col-md-3 control-label">Begincal</label>
				<div class="col-md-8">
						<input type="text" class="form-control" name="begincal" 
						placeholder="Rate" value="<?php echo $row['begincal']; ?>">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label">Endcal</label>
				<div class="col-md-8">
						<input type="text" class="form-control" name="endcal" 
						placeholder="Rate" value="<?php echo $row['endcal']; ?>">
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
				<label class="col-md-3 control-label">Rate Amount</label>
				<div class="col-md-8">
						<input type="text" class="form-control" name="rate_amount" 
						placeholder="Rate" value="<?php echo $row['rate_amount']; ?>">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8">
					<input type="submit" name="edit-class-rate" class="btn btn-primary" value="บันทึก">
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