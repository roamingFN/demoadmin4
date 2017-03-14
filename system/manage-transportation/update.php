<?php
$formcode = "manage-transportation";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$error = '';
$transport_id = $_GET['transport_id'];

	//create new topup requset
	if (!empty($_POST['edit-transport'])) {

		$transport_th_name 	= $_POST['transport_th_name'];
		$transport_eng_name	= $_POST['transport_eng_name'];

		$SQL_QUERY = "update website_transport set ";
		$FIRST_VAR  = true;

		if(!is_null($transport_th_name)){
			$transport_th_name 	= stripcslashes($transport_th_name);
			$transport_th_name 	= mysql_real_escape_string($transport_th_name);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " transport_th_name = '$transport_th_name' ";
		}

		if(!is_null($transport_eng_name)){
			$transport_eng_name 	= stripcslashes($transport_eng_name);
			$transport_eng_name 	= mysql_real_escape_string($transport_eng_name);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " transport_eng_name = '$transport_eng_name' ";
		}

		$SQL_QUERY .= " where transport_id = '$transport_id' ";

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
	<title>Manage Transportation</title>
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
  <h1><a href="index.php">Manage Transportation</a></h1>
  <h3><a href="index.php">&larr; Back</a></h3><br>
		<br />
		<form class="form-horizontal" role="form" action="update.php?transport_id=<?php echo $transport_id ?>" 
		method="post" enctype="multipart/form-data" >
		<?php
			$select_transport = mysql_query("select * from website_transport where transport_id ='$transport_id'");
			$row = mysql_fetch_array($select_transport);
		?>
	    
	    <div class="form-group">
        <label class="col-md-3 control-label">ชื่อบริษัทภาษาไทย </label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="transport_th_name" 
            placeholder="ชื่อบริษัทภาษาไทย" value="<?php echo $row['transport_th_name']; ?>">
        </div>
	    </div>

	    <div class="form-group">
        <label class="col-md-3 control-label">ชื่อบริษัทภาษาอังกฤษ </label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="transport_eng_name" 
            placeholder="ชื่อบริษัทภาษาอังกฤษ" value="<?php echo $row['transport_eng_name']; ?>">
        </div>
	    </div>

	    <div class="form-group">
      	<label class="col-md-3 control-label"></label>
        <div class="col-md-8">
        	<input type="submit" name="edit-transport" class="btn btn-primary" value="บันทึก">
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