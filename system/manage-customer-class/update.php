<?php
$formcode = "manage-class";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$error = '';
$class_id = $_GET['class_id'];

	//create new topup requset
	if (!empty($_POST['edit-class'])) {

		$cid = $_POST['cid'];
		$class_name = $_POST['class_name'];

		$SQL_QUERY = "update customer_class set ";
		$FIRST_VAR  = true;

		if(!is_null($class_name)){
			$class_name 	= stripcslashes($class_name);
			$class_name 	= mysql_real_escape_string($class_name);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " class_name = '$class_name' ";
		}

		$SQL_QUERY .= " where class_id = '$cid' ";

		$update_class = mysql_query($SQL_QUERY);
		if ($update_class) {
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
	<title>Manage Customer Class</title>
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
    	thead{
			color:#000;
		}
		form{
			width: 100%;
		}
    </style>
</head>
<body>
	<div class="container">
  <h1><a href="index.php">Manage Customer Class</a></h1>
  <h3><a href="index.php">&larr; Back</a></h3><br>
		<br />
		<form class="form-horizontal" role="form" action="update.php?class_id=<?php echo $class_id ?>" 
		method="post" enctype="multipart/form-data" >
		<?php
			$select_user = mysql_query("select * from customer_class where class_id ='$class_id'");
			$row = mysql_fetch_array($select_user);
		?>

			<div class="form-group">
				<label class="col-md-3 control-label">หมายเลขคลาส</label>
        <div class="col-md-8">
            <input type="text" class="form-control" 
            placeholder="หมายเลขคลาส" value="<?php echo $row['class_id']; ?>" disabled>
        </div>
      </div>

			<div class="form-group">
				<label class="col-md-3 control-label">ชื่อคลาส</label>
        <div class="col-md-8">
            <input type="text" name="class_name" class="form-control" 
            placeholder="ชื่อคลาส" value="<?php echo $row['class_name']; ?>" >
            <input type="hidden" class="form-control" name="cid" value="<?php echo $row['class_id']; ?>" >
        </div>
      </div>

      <div class="form-group">
      	<label class="col-md-3 control-label"></label>
        <div class="col-md-8">
        	<input type="submit" name="edit-class" class="btn btn-primary" value="บันทึก">
        </div>
      </div>

		</form>
	</div>
	<br />
</body>
</html>