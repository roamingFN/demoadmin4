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
$page_id = $_GET['page_id'];
$cat = $_GET['cat'];

	//create new topup requset
	if (!empty($_POST['edit-item'])) {

		$item_name 		= $_POST['item_name'];
		$item_url			= $_POST['item_url'];
		$item_img 		= $_POST['item_img'];
		$item_price 	= $_POST['item_price'];

		$SQL_QUERY = "update website_featured_item set ";
		$FIRST_VAR  = true;

		if(!is_null($item_name)){
			$item_name = stripcslashes($item_name);
			$item_name = mysql_real_escape_string($item_name);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " featured_item_name = '$item_name' ";
		}
		if(!is_null($item_url)){
			$item_url 	= stripcslashes($item_url);
			$item_url 	= mysql_real_escape_string($item_url);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " featured_item_link = '$item_url' ";
		}
		if(!is_null($item_img)){
			$item_img 	= stripcslashes($item_img);
			$item_img 	= mysql_real_escape_string($item_img);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " featured_item_img = '$item_img' ";
		}
		if(!empty($item_price)){
			$item_price 	= stripcslashes($item_price);
			$item_price 	= mysql_real_escape_string($item_price);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " featured_item_price = '$item_price' ";
		}

		$SQL_QUERY .= " where	featured_item_id = '$page_id' ";

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
		<h3><a href="index.php?cat=<?php echo $cat; ?>">&larr; Back</a></h3>
		<br />
		<form class="form-horizontal" role="form" action="update.php?page_id=<?php echo $page_id.'&cat='.$cat ?>" method="post" enctype="multipart/form-data" >
		<?php
			$select_page = mysql_query("select * from website_featured_item where featured_item_id ='$page_id'");
			$page = mysql_fetch_array($select_page);
		?>

			<div class="form-group">
				<label class="col-md-3 control-label">ชื่อสินค้า</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="item_name" 
            placeholder="ชื่อสินค้า" value="<?php echo $page['featured_item_name']; ?>">
        </div>
      </div>

			<div class="form-group">
				<label class="col-md-3 control-label">ลิงค์สินค้า</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="item_url" 
            placeholder="ลิงค์สินค้า" value="<?php echo $page['featured_item_link']; ?>">
        </div>
      </div>

      <div class="form-group">
				<label class="col-md-3 control-label">ที่อยู่รูปสินค้า</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="item_img" 
            placeholder="ที่อยู่รูปสินค้า" value="<?php echo $page['featured_item_img']; ?>">
        </div>
      </div>

      <div class="form-group">
				<label class="col-md-3 control-label">ราคาสินค้า</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="item_price" 
            placeholder="ราคาสินค้า" value="<?php echo number_format($page['featured_item_price'],2); ?>">
        </div>
      </div>

      <div class="form-group">
      	<label class="col-md-3 control-label"></label>
        <div class="col-md-8">
        	<input type="submit" name="edit-item" class="btn btn-primary" value="บันทึก">
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