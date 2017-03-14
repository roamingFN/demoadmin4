<?php
$formcode = "order-remark";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$error = '';
$remark_id = $_GET['remark_id'];

	//create new topup requset
	if (!empty($_POST['edit-order-remark'])) {

		$remark_tha 	= $_POST['remark_tha'];
    $remark_eng   = $_POST['remark_eng'];

		$SQL_QUERY = "update order_remark set ";
		$FIRST_VAR  = true;

    if(!is_null($remark_tha)){
      $remark_tha  = stripcslashes($remark_tha);
      $remark_tha  = mysql_real_escape_string($remark_tha);
      if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
      $SQL_QUERY .= " remark_tha = '$remark_tha' ";
    }

    if(!is_null($remark_eng)){
      $remark_eng  = stripcslashes($remark_eng);
      $remark_eng  = mysql_real_escape_string($remark_eng);
      if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
      $SQL_QUERY .= " remark_eng = '$remark_eng' ";
    }

		$SQL_QUERY .= " where remark_id = '$remark_id' ";

		$add_user = mysql_query($SQL_QUERY);
		if ($add_user) {
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
	<title>Manage Order Remark</title>
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
  <h1><a href="index.php">Manage Order Remark</a></h1>
  <h3><a href="index.php">&larr; Back</a></h3><br>
		<br />
		<form class="form-horizontal" role="form" action="update.php?remark_id=<?php echo $remark_id ?>" 
		method="post" enctype="multipart/form-data" >
		<?php
			$select_remark = mysql_query("select * from order_remark where remark_id ='$remark_id'");
			$row = mysql_fetch_array($select_remark);
		?>

			<div class="form-group">
				<label class="col-md-3 control-label">Remark ID</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="remark_id"
             value="<?php echo $row['remark_id']; ?>" disabled>
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-3 control-label">หมายเหตุออร์เดอร์ภาษาไทย</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="remark_tha"
             value="<?php echo $row['remark_tha']; ?>" >
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-3 control-label">หมายเหตุออร์เดอร์ภาษาอังกฤษ</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="remark_eng"
             value="<?php echo $row['remark_eng']; ?>" >
        </div>
      </div>

      <div class="form-group">
      	<label class="col-md-3 control-label"></label>
        <div class="col-md-8">
        	<input type="submit" name="edit-order-remark" class="btn btn-primary" value="บันทึก">
        </div>
      </div>

		</form>
	</div>
	<br />
</body>
</html>