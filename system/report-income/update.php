<?php
	include '../connect.php';
	include '../session.php';

$error = '';
$rate_id = $_GET['rate_id'];

	//create new topup requset
	if (!empty($_POST['edit-user'])) {

		$starting_date 			= $_POST['starting_date'];
    $begin_time         = $_POST['begin_time'];
		$rate_cny 					= $_POST['rate_cny'];
    $user_id            = $_SESSION['USERID'];

		$SQL_QUERY = "update website_rate set edit_user_id = '$user_id', edit_datetime = NOW(), ";
		$FIRST_VAR  = true;

		if(!is_null($starting_date)){
			$starting_date 	= stripcslashes($starting_date);
			$starting_date 	= mysql_real_escape_string($starting_date);
			$starting_date = str_replace('/', '-', $starting_date);
			$starting_date = date('m/d/Y', strtotime($starting_date));
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " starting_date = STR_TO_DATE('$starting_date','%c/%e/%Y %T') ";
		}

    if(!is_null($begin_time)){
      $begin_time  = stripcslashes($begin_time);
      $begin_time  = mysql_real_escape_string($begin_time);
      if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
      $SQL_QUERY .= " begin_time = '$begin_time'";
    }

		if(!is_null($rate_cny)){
			$rate_cny 	= stripcslashes($rate_cny);
			$rate_cny 	= mysql_real_escape_string($rate_cny);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " rate_cny = '$rate_cny' ";
		}

		$SQL_QUERY .= " where website_rate_id = '$rate_id' ";

    //echo $SQL_QUERY;

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
	<title>Manage Rate</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
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
  <h1><a href="index.php">Manage Rate</a></h1>
  <h3><a href="index.php">&larr; Back</a></h3><br>
		<br />
		<form class="form-horizontal" role="form" action="update.php?rate_id=<?php echo $rate_id ?>" 
		method="post" enctype="multipart/form-data" >
		<?php
			$select_rate = mysql_query("select * from website_rate where website_rate_id ='$rate_id'");
			$row = mysql_fetch_array($select_rate);
		?>

			<div class="form-group">
        <label class="col-md-3 control-label">วันที่ </label>
        <div class="col-md-8">
        	<div class="input-group input-append date">
            <input type="text" class="form-control" name="starting_date" id="date1"
            placeholder="วันที่" value="<?php echo date("d/m/Y", strtotime($row['starting_date'])); ?>" />
            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
          </div>
        </div>
	    </div>

      <div class="form-group">
        <label class="col-md-3 control-label">เวลา </label>
        <div class="col-md-8">
          <div class="input-group input-append date">
            <input type="text" class="form-control" name="begin_time" id="time1"
            placeholder="วันที่" value="<?php echo $row['begin_time']; ?>" />
            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
          </div>
        </div>
      </div>
	    
	    <div class="form-group">
        <label class="col-md-3 control-label">Rate </label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="rate_cny" 
            placeholder="Rate" value="<?php echo $row['rate_cny']; ?>">
        </div>
	    </div>

	    <div class="form-group">
      	<label class="col-md-3 control-label"></label>
        <div class="col-md-8">
        	<input type="submit" name="edit-user" class="btn btn-primary" value="บันทึก">
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

    $('#date1').datepicker({
      timeInput:true,
      altRedirectFocus:false,
      dateFormat: 'dd/mm/yy',
    })
    $('#time1').timepicker({
      timeInput:true,
      altRedirectFocus:false,
    })
	});
</script>
<script src="../js/sweetalert2.min.js"></script> 
<link rel="stylesheet" type="text/css" href="../js/sweetalert2.css">
<link href="../css/jquery-ui.css" rel="stylesheet"/>
<script charset="utf-8" src="../js/jquery-ui.js"></script>
<link href="../css/jquery-ui-timepicker-addon.css" rel="stylesheet"/>
<script charset="utf-8" src="../js/jquery-ui-timepicker-addon.min.js"></script>
</body>
</html>