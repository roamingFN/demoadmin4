<?php
$formcode = "manage-page";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$error = '';
$group_id = $_GET['group_id'];

	//create new topup requset
	if (!empty($_POST['edit-group'])) {

		$group_name 	= $_POST['group_name'];
		$group_url		= $_POST['group_url'];
		$group_img 		= $_POST['group_img'];
		$group_text_icon = $_POST['group_text_icon'];

		$SQL_QUERY = "update website_featured_cate set ";
		$FIRST_VAR  = true;

		if(!is_null($group_name)){
			$group_name = stripcslashes($group_name);
			$group_name = mysql_real_escape_string($group_name);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " featured_cate_name = '$group_name' ";
		}
		if(!is_null($group_url)){
			$group_url 	= stripcslashes($group_url);
			$group_url 	= mysql_real_escape_string($group_url);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " featured_cate_link = '$group_url' ";
		}
		if(!is_null($group_img)){
			$group_img 	= stripcslashes($group_img);
			$group_img 	= mysql_real_escape_string($group_img);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " featured_cate_img = '$group_img' ";
		}
		if(!is_null($group_text_icon)){
			$group_text_icon 	= stripcslashes($group_text_icon);
			$group_text_icon 	= mysql_real_escape_string($group_text_icon);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " featured_cate_text_icon = '$group_text_icon' ";
		}


		$SQL_QUERY .= " where	featured_cate_id = '$group_id' ";

		$update_item = mysql_query($SQL_QUERY);

		if ($update_item) {
			echo '<div class="alert alert-success container" role="alert"><label>แก้ไขข้อมูลสำเร็จ</div>';
		}else{
			$error .= "<li>Error : ".mysql_error()."</li>";
			echo '<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label>'.$error.'</div>';
		}
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage Page</title>
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
		<h1><a href="index.php">Manage Page</a></h1>
		<h3><a href="index.php?cat=group">&larr; Back</a></h3>
		<br />
		<form class="form-horizontal" role="form" action="update_group.php?group_id=<?php echo $group_id ?>" method="post" enctype="multipart/form-data" >
		<?php
			$select_page = mysql_query("select * from website_featured_cate where featured_cate_id ='$group_id'");
			$page = mysql_fetch_array($select_page);
		?>

			<div class="form-group">
				<label class="col-md-3 control-label">ชื่อหมวดสินค้า</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="group_name" 
            placeholder="ชื่อหมวดสินค้า" value="<?php echo $page['featured_cate_name']; ?>">
        </div>
      </div>

			<div class="form-group">
				<label class="col-md-3 control-label">Icon หมวดสินค้า</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="group_text_icon" 
            placeholder="Icon หมวดสินค้า" value="<?php echo $page['featured_cate_text_icon']; ?>">
        </div>
      </div>

			<div class="form-group">
				<label class="col-md-3 control-label">ลิงค์หมวดสินค้า</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="group_url" 
            placeholder="ลิงค์หมวดสินค้า" value="<?php echo $page['featured_cate_link']; ?>">
        </div>
      </div>

      <div class="form-group">
				<label class="col-md-3 control-label">ที่อยู่รูปหมวดสินค้า</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="group_img" 
            placeholder="ที่อยู่รูปหมวดสินค้า" value="<?php echo $page['featured_cate_img']; ?>">
        </div>
      </div>

      <div class="form-group">
      	<label class="col-md-3 control-label"></label>
        <div class="col-md-8">
        	<input type="submit" name="edit-group" class="btn btn-primary" value="บันทึก">
        </div>
      </div>

		</form>
	</div>
<br />
<script>
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