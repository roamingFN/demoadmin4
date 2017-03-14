<?php
$formcode = "manage-website";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isViewPermitted($formcode)) {
  header('Location: ../index.php?error_code=view_not_permitted');
}

$error = '';

	//create new topup requset
	if (!empty($_POST['edit-website-config'])) {

    if (!isActionPermitted($formcode)) {
      return;
    }

		$site_url   = $_POST['site_url'];
		$site_path 	= $_POST['site_path'];
    $site_lowest_price  = $_POST['site_lowest_price'];
    $site_max_cart = $_POST['site_max_cart'];

		$SQL_QUERY = "update website_config set ";
		$FIRST_VAR  = true;

		if(isset($site_url) && $site_url!=""){
			$site_url 	= stripcslashes($site_url);
			$site_url 	= mysql_real_escape_string($site_url);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " site_url = '$site_url' ";
		}

    if(isset($site_path) && $site_path!=""){
      $site_path   = stripcslashes($site_path);
      $site_path   = mysql_real_escape_string($site_path);
      if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
      $SQL_QUERY .= " site_path = '$site_path' ";
    }

    if(isset($site_lowest_price) && $site_lowest_price!=""){
      $site_lowest_price   = stripcslashes($site_lowest_price);
      $site_lowest_price   = mysql_real_escape_string($site_lowest_price);
      if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
      $SQL_QUERY .= " site_lowest_price = '$site_lowest_price' ";
    }

    if(isset($site_max_cart) && $site_max_cart!=""){
      $site_max_cart   = stripcslashes($site_max_cart);
      $site_max_cart   = mysql_real_escape_string($site_max_cart);
      if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
      $SQL_QUERY .= " site_max_cart = '$site_max_cart' ";
    }

		$update_website = mysql_query($SQL_QUERY);

		if ($update_website) {
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
	<title>Manage Website Config</title>
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
  <h1><a href="index.php">Manage Website</a></h1>
  <h3><a href="../index.php">&larr; Back</a></h3><br>
		<br />
		<form class="form-horizontal" role="form" action="index.php" 
		method="post" enctype="multipart/form-data" >
		<?php
			$select_website_config = mysql_query("select * from website_config");
			$row = mysql_fetch_array($select_website_config);
		?>

			<div class="form-group">
				<label class="col-md-3 control-label">SITE URL</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="site_url"
              value="<?php echo $row['SITE_URL']; ?>" >
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-3 control-label">SITE PATH</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="site_path"
              value="<?php echo $row['SITE_PATH']; ?>" >
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-3 control-label">ราคาขั้นต่ำ</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="site_lowest_price" 
              value="<?php echo $row['SITE_LOWEST_PRICE']; ?>" >
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-3 control-label">จำนวนสินค้าต่อบิล</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="site_max_cart" 
              value="<?php echo $row['SITE_MAX_CART']; ?>" >
        </div>
      </div>

      <div class="form-group">
      	<label class="col-md-3 control-label"></label>
        <div class="col-md-8">
        <?php
          if (isActionPermitted($formcode)) {
            echo'<input type="submit" name="edit-website-config" class="btn btn-primary" value="บันทึก">';
          }
        ?>
        	
        </div>
      </div>

		</form>
	</div>
	<br />
</body>
</html>