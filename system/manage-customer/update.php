<?php
$formcode = "profile";

include '../connect.php';
include '../session.php';
include 'inc/php/functions_statusConvert.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$error = '';
$customer_id = $_GET['cusid'];

	//create new topup requset
	if (!empty($_POST['edit-profile'])) {

		$profile_img 		= $_FILES["profile-img"]["name"];
		$customer_firstname = $_POST['customer-firstname'];
		$customer_lastname 	= $_POST['customer-lastname'];
		$customer_email		= $_POST['customer-email'];
		$customer_phone 	= $_POST['customer-phone'];
		$customer_class 	= $_POST['customer-class'];
		$customer_gender 	= $_POST['customer-gender'];
		$customer_birthdate = $_POST['customer-birthdate'];

		$customer_birthdate = str_replace('/', '-', $customer_birthdate);
		$customer_birthdate = date('m/d/Y', strtotime($customer_birthdate));

		$SQL_QUERY = "update customer set ";
		$UPDATE_WITH_IMAGE = false;
		$FIRST_VAR  = true;
		//echo "profile img=".$profile_img;

		// if(empty($profile_img)){ $error .= '<li>คุณยังไม่ได้ใส่รูปภาพประจำตัว</li>';}
		// if(empty($customer_firstname)){ $error .= '<li>คุณยังไม่ได้กรอกชื่อ</li>';}
		// if(empty($customer_lastname)){ $error .= '<li>คุณยังไม่ได้กรอกนามสกุล</li>';}
		// if(empty($customer_email)){ $error .= '<li>คุณยังไม่ได้กรอกอีเมล์</li>';}
		// if(empty($customer_phone)){ $error .= '<li>คุณยังไม่ได้กรอกเบอร์โทรศัพท์</li>';}
		// if(empty($customer_gender)){ $error .= '<li>คุณยังไม่ได้กรอกเพศ</li>';}
		// if(empty($customer_birthdate)){ $error .= '<li>คุณยังไม่ได้กรอกวันเกิด</li>';}

		if(!empty($profile_img)){
			$profile_img 		= stripcslashes($profile_img);
			$profile_img 		= mysql_real_escape_string($profile_img);
			$UPDATE_WITH_IMAGE = true;
		}
		if(!is_null($customer_firstname)){
			$customer_firstname = stripcslashes($customer_firstname);
			$customer_firstname = mysql_real_escape_string($customer_firstname);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " customer_firstname = '$customer_firstname' ";
		}
		if(!is_null($customer_lastname)){
			$customer_lastname 	= stripcslashes($customer_lastname);
			$customer_lastname 	= mysql_real_escape_string($customer_lastname);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " customer_lastname = '$customer_lastname' ";
		}
		if(!empty($customer_email)){
			$customer_email 	= stripcslashes($customer_email);
			$customer_email 	= mysql_real_escape_string($customer_email);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " customer_email = '$customer_email' ";
		}
		if(!is_null($customer_phone)){
			$customer_phone 	= stripcslashes($customer_phone);
			$customer_phone 	= mysql_real_escape_string($customer_phone);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " customer_phone = '$customer_phone' ";
		}
		if(!is_null($customer_class)){
			$customer_class 	= stripcslashes($customer_class);
			$customer_class 	= mysql_real_escape_string($customer_class);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " class_id = '$customer_class' ";
		}
		if(!empty($customer_gender)){
			$customer_gender 	= stripcslashes($customer_gender);
			$customer_gender 	= mysql_real_escape_string($customer_gender);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " customer_gender = '$customer_gender' ";
		}
		if(!empty($customer_birthdate)){
			$customer_birthdate = stripcslashes($customer_birthdate);
			$customer_birthdate = mysql_real_escape_string($customer_birthdate);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " customer_birthdate = STR_TO_DATE('$customer_birthdate','%c/%e/%Y %T') ";
		}

    if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
    $SQL_QUERY .= " edit_datetime = NOW(), edit_user_id = '".$_SESSION['ID']."' ";

		if ($UPDATE_WITH_IMAGE) {

      $query_site_url = mysql_query("select * from website_config");
      $site_url_row = mysql_fetch_array($query_site_url);
      $site_path = $site_url_row['SITE_PATH'];
      $site_url = $site_url_row['SITE_URL'];

			$target_dir = $site_path."/profile_img/".$customer_id;
			if (!is_dir($target_dir)&& strlen($target_dir)>0) {
				mkdir($target_dir, "0777");
				chmod($target_dir, 0777);
			}
			$target_file = $target_dir . basename($_FILES["profile-img"]["name"]);
			$uploadOk = 1;
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			// Check if image file is a actual image or fake image
			if(isset($_POST["edit-profile"])) {
			    $check = getimagesize($_FILES["profile-img"]["tmp_name"]);
			    if($check !== false) {
			        //echo "File is an image - " . $check["mime"] . ".";
			        $uploadOk = 1;
			    } else {
			        $error .= "<li>File is not an image.</li>";
			        $uploadOk = 0;
			    }
			}
			// Check if file already exists
			$target_file = file_newname($target_dir,basename($_FILES["profile-img"]["name"]));

			// Check file size
			if ($_FILES["profile-img"]["size"] > 500000) {
			    $error .=  "<li>Sorry, your file is too large.</li>";
			    $uploadOk = 0;
			}
			// Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) {
			    $error .=  "<li>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</li>";
			    $uploadOk = 0;
			}
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
			    $error .=  "<li>Sorry, your file was not uploaded.</li>";
			// if everything is ok, try to upload file
			} else {
			    if (move_uploaded_file($_FILES["profile-img"]["tmp_name"], $target_file)) {
			        //echo "The file ". basename( $_FILES["profile-img"]["name"]). " has been uploaded.";
			    } else {
			        $error .=  "<li>Sorry, there was an error uploading your file.</li>";
			    }
			}

      $target_file = str_replace($site_path,"",$target_file);

			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " customer_profile_img = '$target_file' ";
			$SQL_QUERY .= " where customer_id = '$customer_id' ";
			
			//echo $SQL_QUERY;

			$add_topup_req = mysql_query($SQL_QUERY);

			if ($add_topup_req) {
				echo '<div class="alert alert-success container" role="alert"><label>แก้ไขข้อมูลส่วนตัวสำเร็จ</div>';
			}else{
				$error .= "<li>fail".mysql_error()."</li>";
			}
		}else if(!$UPDATE_WITH_IMAGE){
			//echo $SQL_QUERY;
			$SQL_QUERY .= " where customer_id = '$customer_id' ";

			$add_topup_req = mysql_query($SQL_QUERY);
			if ($add_topup_req) {
				echo '<div class="alert alert-success container" role="alert"><label>แก้ไขข้อมูลสำเร็จ</label></div>';
			}else{
				$error .= "<li>Error : ".mysql_error()."</li>";
			}
		}else{
			echo '<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label>'.$error.'</div>';
		}

		if (isset($_POST['address_id'])) {
			//########## update address ##########
  		$address_id 				= $_POST['address_id'];
  		$address_name 			= $_POST['address_name'];
  		$address_line1 			= $_POST['address_line1'];
  		$address_city 			= $_POST['address_city'];
  		$address_country 		= $_POST['address_country'];
  		$address_zipcode 		= $_POST['address_zipcode'];
  		$address_phone 			= $_POST['address_phone'];
  		$countUpdate	    	= count($address_id);

  		for ($i = 0; $i < $countUpdate; $i++) {

  			$address_id[$i] 			= stripcslashes($address_id[$i]);
  			$address_name[$i] 		= stripcslashes($address_name[$i]);
  			$address_line1[$i] 		= stripcslashes($address_line1[$i]);
  			$address_city[$i] 		= stripcslashes($address_city[$i]);
  			$address_country[$i] 	= stripcslashes($address_country[$i]);
  			$address_zipcode[$i] 	= stripcslashes($address_zipcode[$i]);
  			$address_phone[$i] 		= stripcslashes($address_phone[$i]);

  			$address_id[$i] 			= mysql_real_escape_string($address_id[$i]);
  			$address_name[$i] 		= mysql_real_escape_string($address_name[$i]);
  			$address_line1[$i] 		= mysql_real_escape_string($address_line1[$i]);
  			$address_city[$i] 		= mysql_real_escape_string($address_city[$i]);
  			$address_country[$i] 	= mysql_real_escape_string($address_country[$i]);
  			$address_zipcode[$i] 	= mysql_real_escape_string($address_zipcode[$i]);
  			$address_phone[$i] 		= mysql_real_escape_string($address_phone[$i]);

  		}

  		for ($i = 0; $i < $countUpdate; $i++) {

  			if ($address_id[$i] != '') {
  					$updateCustomer = mysql_query("update customer_address
  					set address_name = '$address_name[$i]', 
  					line_1 = '$address_line1[$i]', 
  					city = '$address_city[$i]', 
  					country = '$address_country[$i]', 
  					zipcode = '$address_zipcode[$i]', 
  					phone = '$address_phone[$i]'  
  					where address_id = '$address_id[$i]'", $connection);
  			
  				if ($updateCustomer) {
  					//echo "Success".$address_id[$i];
  				}else{
  					echo $error .= "fail".mysql_error();
  				}
  			}
  		}
  		//########################################
		}

		if (isset($_POST['bank_account_id'])) {
      //########## update address ##########
      $bank_account_id        = $_POST['bank_account_id'];
      $bank_name              = $_POST['bank_name'];
      $account_name           = $_POST['account_name'];
      $account_no             = $_POST['account_no'];
      $branch                 = $_POST['branch'];
      $notes                  = $_POST['bank_note'];
      $countUpdate            = count($bank_account_id);

      for ($i = 0; $i < $countUpdate; $i++) {

        $bank_account_id[$i]       = stripcslashes($bank_account_id[$i]);
        $bank_name[$i]     = stripcslashes($bank_name[$i]);
        $account_name[$i]    = stripcslashes($account_name[$i]);
        $account_no[$i]     = stripcslashes($account_no[$i]);
        $branch[$i]  = stripcslashes($branch[$i]);
        $notes[$i]  = stripcslashes($notes[$i]);

        $bank_account_id[$i]       = mysql_real_escape_string($bank_account_id[$i]);
        $bank_name[$i]     = mysql_real_escape_string($bank_name[$i]);
        $account_name[$i]    = mysql_real_escape_string($account_name[$i]);
        $account_no[$i]     = mysql_real_escape_string($account_no[$i]);
        $branch[$i]  = mysql_real_escape_string($branch[$i]);
        $notes[$i]  = mysql_real_escape_string($notes[$i]);

      }

      for ($i = 0; $i < $countUpdate; $i++) {

        if ($bank_account_id[$i] != '') {

            $updateCustomer = mysql_query("update customer_bank_account
            set bank_name = '$bank_name[$i]', 
            account_name = '$account_name[$i]', 
            account_no = '$account_no[$i]', 
            bank_branch = '$branch[$i]', 
            user_notes = '$notes[$i]' 
            where bank_account_id = '$bank_account_id[$i]'", $connection);
        
          if ($updateCustomer) {
            //echo "Success".$address_id[$i];
          }else{
            echo $error .= "fail".mysql_error();
          }
        }
      }
      //########################################
    }

	}

	if (isset($_GET['action'])) {
		if ($_GET['action'] == 'delete') {
			$delete_id = $_GET['id'];

			$delete_address = mysql_query("delete from customer_address where address_id = '$delete_id'");

			if ($delete_address) {
			 	echo '<div class="alert alert-success container" role="alert"><label>ลบข้อมูลสำเร็จ</label></div>';
			}else{
				$error .= mysql_error(); 
			} 
		}
    if ($_GET['action'] == 'delete_bank') {
      $bank_account_id = $_GET['bank_account_id'];

      $delete_address = mysql_query("delete from customer_bank_account where bank_account_id = '$bank_account_id'");

      if ($delete_address) {
        echo '<div class="alert alert-success container" role="alert"><label>ลบข้อมูลสำเร็จ</label></div>';
      }else{
        $error .= mysql_error(); 
      } 
    }
	}

	if (isset($_POST['add_address'])) {
		$address_name = $_POST['address_name'];
    $address_line1 = $_POST['address_line1'];
    $address_country = "ไทย";
    $address_zipcode = $_POST['address_zipcode'];
    $address_phone = $_POST['address_tel'];
    //new address function handle
    $province_id = $_POST['province'];
    $amphur_id   = $_POST['amphoe'];
    $district_id = $_POST['district'];

    $select_province = mysql_query("select * from tbl_province where province_id = '$province_id'");
    $province_row = mysql_fetch_array($select_province);
    $province_name = $province_row['PROVINCE_NAME'];

    $select_amphur = mysql_query("select * from tbl_amphur where amphur_id = '$amphur_id'");
    $amphur_row = mysql_fetch_array($select_amphur);
    $amphur_name = $amphur_row['AMPHUR_NAME'];

    $select_district = mysql_query("select * from tbl_district where district_id = '$district_id'");
    $district_row = mysql_fetch_array($select_district);
    $district_name = $district_row['DISTRICT_NAME'];

    //add detail to address line 1 -> (complatiably)
    $address_line1 .= " ".$district_name." ".$amphur_name;

		$addAddress = mysql_query("insert into customer_address(customer_id,address_name,line_1,city,country,zipcode,phone,district_id,amphur_id,province_id) 
            values('$customer_id','$address_name','$address_line1','$province_name','$address_country','$address_zipcode','$address_phone','".$_POST['district']."','".$_POST['amphoe']."','".$_POST['province']."')");

		if ($addAddress) {
			echo '<div class="alert alert-success container" role="alert"><label>เพิ่มข้อมูลสำเร็จ</label></div>';
		}else{
			$error .= "<li>Error : ".mysql_error()."</li>";
		}
	}

  if (isset($_POST['add_bank_account'])) {
    $bank_name        = $_POST['add_bank_name'];
    $account_name     = $_POST['add_account_name'];
    $account_no       = $_POST['add_account_no'];
    $branch           = $_POST['add_branch'];
    $user_notes       = $_POST['add_user_notes'];

    $addBankAccount = mysql_query("insert into customer_bank_account(customer_id,bank_name,account_name,account_no, bank_branch,user_notes) 
      values('$customer_id','$bank_name','$account_name','$account_no',
        '$branch','$user_notes')");

    if ($addBankAccount) {
      echo '<div class="alert alert-success container" role="alert"><label>เพิ่มข้อมูลสำเร็จ</label></div>';
    }else{
      $error .= "<li>Error : ".mysql_error()."</li>";
    }
  }

	if ($error != '') {
		'<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label>'.$error.'</div>';
	}


?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage Customer</title>
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
		thead a:hover{
			color:#000;
		}
	form{
			width: 100%;
		}
		.datepicker tr {background-color: #fff;}
  </style>

</head>
<body>
	<div class="container">
		<h1><a href="index.php">Manage Customer</a></h1>
		<h3><a href="index.php">&larr; Back</a></h3>
		<br />
		<form class="form-horizontal" role="form" action="update.php?cusid=<?php echo $customer_id ?>" 
			method="post" enctype="multipart/form-data" >
		<?php
			$select_customer = mysql_query("select * from customer where customer_id ='$customer_id'");
			$customer = mysql_fetch_array($select_customer);

      $query_site_url = mysql_query("select * from website_config");
      $site_url_row = mysql_fetch_array($query_site_url);
      $site_url = $site_url_row['SITE_URL'];
		?>

			<div class="form-group" style="vertical-align: text-top;">
				<label for="profile-img" class="col-md-3 control-label">รูปประจำตัว</label>
          <div class="col-md-8">
          	<p>
          	<?php 
          		if ($customer['customer_profile_img'] != "") {
          			echo "<img src='".$site_url.$customer['customer_profile_img']."' class='img img-thumbnail' 
          			style='height:150px;'>";
          		} else{
          			echo "<img src='".$site_url."/images/default-avatar.png' class='img img-thumbnail' 
          			style='width:150px; height:150px;'>";
          		}
          	?>
            </p><br />
            <input type="file" class="form-control" name="profile-img" id="profile-img">
        	</div>
      </div>
      <div class="form-group">
        <label for="firstname" class="col-md-3 control-label">รหัสสมาชิก</label>
          <div class="col-md-8">
              <input disabled type="text" class="form-control" name="customer-code" 
              id="customer-code" placeholder="" value="<?php echo $customer['customer_code']; ?>">
          </div>
        </div>
			<div class="form-group">
				<label for="firstname" class="col-md-3 control-label">ชื่อ</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="customer-firstname" 
            id="customer-firstname" placeholder="ชื่อจริง" value="<?php echo $customer['customer_firstname']; ?>">
        </div>
      </div>

			<div class="form-group">
				<label for="lastname" class="col-md-3 control-label">นามสกุล</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="customer-lastname" 
            id="customer-lastname" placeholder="นามสกุล" value="<?php echo $customer['customer_lastname']; ?>">
        </div>
      </div>

      <div class="form-group">
				<label for="email" class="col-md-3 control-label">อีเมล์</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="customer-email" 
            id="customer-email" placeholder="อีเมล์" value="<?php echo $customer['customer_email']; ?>">
        </div>
      </div>

      <div class="form-group">
				<label for="phone" class="col-md-3 control-label">เบอร์โทรศัพท์</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="customer-phone" onkeypress="return isPhoneNumber(event)" 
            id="customer-phone" placeholder="มือถือ, บ้าน" value="<?php echo $customer['customer_phone']; ?>">
        </div>
      </div>

      <div class="form-group">
				<label for="phone" class="col-md-3 control-label">Class</label>
        <div class="col-md-8">
            <select class="form-control" name="customer-class" id="customer-class">
					    	<?php 
					    	$select_class = mysql_query("select * from customer_class");
					    	while ($row = mysql_fetch_array($select_class)) {
					    		$selected = "";
					    		if ($row['class_id']== $customer['class_id']) {
					    			$selected = "selected";
					    		}
					    		echo "<option value=".$row['class_id']." ".$selected." >".$row['class_name']."</option>";
					    	}
					    	?>
							</select>
        </div>
      </div>

      <div class="form-group">
				<label for="gender" class="col-md-3 control-label">เพศ</label>
        <div class="col-md-8">
        	<div class="form-vetical">
        		<label class="radio-inline">
        			<input type="radio" name="customer-gender" value="male" 
        			<?php
        				if ($customer['customer_gender'] == "male") {
        					echo "checked";
        				}
        			?> 
        			> ชาย 
        		</label>
        		<label class="radio-inline">
        			<input type="radio" name="customer-gender" value="female"
        			<?php
        				if ($customer['customer_gender'] == "female") {
        					echo "checked";
        				}
        			?> 
        			> หญิง 
        		</label>
        	</div>
        </div>
      </div>

			<div class="form-group">
        <label class="col-md-3 control-label">วันเกิด</label>
        <div class="col-md-8 date">
            <div class="input-group input-append date" id="datePicker">
              <input type="text" class="form-control" name="customer-birthdate" value="<?php 
              	if ($customer['customer_birthdate']!=null) {
              		echo date('d/m/Y', strtotime($customer['customer_birthdate']));
              	}
              	?>" />
              <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
	    </div>

      <div class="form-group">
				<label for="birthdate" class="col-md-3 control-label">ที่อยู่</label>
        <div class="col-md-8">
        <button class="btn btn-default" type="button" data-toggle="modal" data-target="#addAddress">
        	เพิ่มที่อยู่ใหม่
        </button><br /><br />

	      <?php
				$exist_address = mysql_query("select * from customer_address where customer_id = '$customer_id'");
				if (mysql_num_rows($exist_address) > 0) {
					while ($row = mysql_fetch_array($exist_address)) {
					echo '
					<div class="well">
						<input type="hidden" class="form-control" name="address_id[]" value="'.$row['address_id'].'">
					  <div class="form-group">
					    <label class="col-sm-2 control-label">ชื่อผุ้รับ</label>
					    <div class="col-sm-10">
					      <input type="text" class="form-control" name="address_name[]" 
					      placeholder="ชื่อ นามสกุล" value="'.$row['address_name'].'">
					    </div>
					  </div>
					  <div class="form-group">
					    <label class="col-sm-2 control-label">ที่อยู่</label>
					    <div class="col-sm-10">
					      <input type="text" class="form-control" name="address_line1[]" 
					      placeholder="ที่อยู่" value="'.$row['line_1'].'">
					    </div>
					  </div>
					  <div class="form-group">
					    <label class="col-sm-2 control-label">จังหวัด</label>
					    <div class="col-sm-10">
					      <input type="text" class="form-control" name="address_city[]" 
					      placeholder="จังหวัด" value="'.$row['city'].'">
					    </div>
					  </div>
					  <div class="form-group">
					    <label class="col-sm-2 control-label">ประเทศ</label>
					    <div class="col-sm-10">
					      <input type="text" class="form-control" name="address_country[]" 
					      placeholder="ประเทศ" value="'.$row['country'].'" >
					    </div>
					  </div>
					  <div class="form-group">
					    <label class="col-sm-2 control-label">ไปรษณีย์</label>
					    <div class="col-sm-10">
					      <input type="text" class="form-control" name="address_zipcode[]" 
					      placeholder="รหัสไปรษณีย์" value="'.$row['zipcode'].'">
					    </div>
					  </div>
					  <div class="form-group">
					    <label class="col-sm-2 control-label">โทรศัพท์</label>
					    <div class="col-sm-10">
					      <input type="text" class="form-control" name="address_phone[]" 
					      placeholder="เบอร์โทรศัพท์" value="'.$row['phone'].'">
					    </div>
					  </div>
					  <div class="text-right">
					  	<a href="update.php?cusid='.$customer_id.'&action=delete&id='.$row['address_id'].'">'; ?>
					  		<button type="button" class="btn btn-default" onclick="return confirm('คุณต้องการลบที่อยู่นี้หรือไม่')">ลบที่อยู่</button>
					  	<?php echo'
					  	</a>
					  </div>
					</div>
					';
					}
					
				}else{
					echo "<div class='well'><p>ยังไม่มีที่อยู่ที่บันทึกไว้<p></div>";
				}
				?>

				</div>
    	</div>

      <div class="form-group">
        <label for="birthdate" class="col-md-3 control-label">บัญชีธนาคาร</label>
        <div class="col-md-8">
        <button class="btn btn-default" type="button" data-toggle="modal" data-target="#addBankAccount">
          เพิ่มบัญชีใหม่
        </button><br /><br />

        <?php
        $exist_address = mysql_query("select * from customer_bank_account where customer_id = '$customer_id'");
        if (mysql_num_rows($exist_address) > 0) {
          while ($row = mysql_fetch_array($exist_address)) {
          echo '
          <div class="well">
            <input type="hidden" class="form-control" name="bank_account_id[]" value="'.$row['bank_account_id'].'">
            <div class="form-group">
              <label class="col-sm-2 control-label">ธนาคาร</label>
              <div class="col-sm-10">
                <select name="bank_name[]" class="form-control">
                  <option value="">ไม่เลือก</option>'; ?>
                  <option value="ธนาคารกรุงเทพ" <?php if($row['bank_name']=="ธนาคารกรุงเทพ"){echo "selected";} ?> >
                    ธนาคารกรุงเทพ</option>
                  <option value="ธนาคารกสิกรไทย" <?php if($row['bank_name']=="ธนาคารกสิกรไทย"){echo "selected";} ?> >
                    ธนาคารกสิกรไทย</option>
                  <option value="ธนาคารไทยพาณิชย์" <?php if($row['bank_name']=="ธนาคารไทยพาณิชย์"){echo "selected";} ?> >
                    ธนาคารไทยพาณิชย์</option>
                  <option value="ธนาคารกรุงไทย" <?php if($row['bank_name']=="ธนาคารกรุงไทย"){echo "selected";} ?> >
                    ธนาคารกรุงไทย</option>
                  <option value="ธนาคารกรุงศรีอยุธยา" <?php if($row['bank_name']=="ธนาคารกรุงศรีอยุธยา"){echo "selected";} ?> >
                    ธนาคารกรุงศรีอยุธยา</option>
                  <option value="ธนาคารเกียรตินาคิน" <?php if($row['bank_name']=="ธนาคารเกียรตินาคิน"){echo "selected";} ?> >
                    ธนาคารเกียรตินาคิน</option>
                  <option value="ธนาคารทหารไทย" <?php if($row['bank_name']=="ธนาคารทหารไทย"){echo "selected";} ?> >
                    ธนาคารทหารไทย</option>
                  <option value="ธนาคารธนชาต" <?php if($row['bank_name']=="ธนาคารธนชาต"){echo "selected";} ?> >
                    ธนาคารธนชาต</option>
                <?php echo '
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">ชื่อบัญชี</label>
              <div class="col-sm-10">
                <input type="text" name="account_name[]" class="form-control" value="'.$row['account_name'].'">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">เลขบัญชี</label>
              <div class="col-sm-10">
                <input type="text" name="account_no[]" class="form-control" onkeypress="return isAccount(event)" value="'.$row['account_no'].'">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">สาชา</label>
              <div class="col-sm-10">
                <input type="text" name="branch[]" class="form-control" value="'.$row['bank_branch'].'">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">หมายเหตุ</label>
              <div class="col-sm-10">
                <input type="text" name="bank_note[]" class="form-control" value="'.$row['user_notes'].'">
              </div>
            </div>
            <div class="text-right">
              <a href="update.php?cusid='.$customer_id.'&action=delete_bank&bank_account_id='.$row['bank_account_id'].'">'; ?>
                <button type="button" class="btn btn-default" onclick="return confirm('คุณต้องการลบบัญชีนี้หรือไม่')">ลบบัญชี</button>
              <?php echo'
              </a>
            </div>
          </div>
          ';
          }
          
        }else{
          echo "<div class='well'><p>ยังไม่มีบัญชีที่บันทึกไว้<p></div>";
        }
        ?>

        </div>
      </div>

      <div class="form-group">
      	<label for="email" class="col-md-3 control-label"></label>
        <div class="col-md-8">
        	<input type="submit" name="edit-profile" class="btn btn-primary" value="บันทึก">
        </div>
      </div>

		</form>
	</div>
<br />

<div class="modal fade" id="addAddress" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">เพิ่มที่อยู่ใหม่</h4>
      </div>
      <div class="modal-body">
        <form action="update.php?cusid=<?php echo $customer_id; ?>" method="post" class="form-horizontal">
					<?php
          echo "
                <div class='form-group'>
                  <div class='col-md-3'>
                    <label>ชื่อผู้รับ </label>
                  </div>
                  <div class='col-md-8'>
                    <input type='text' class='form-control' name='address_name' id='address_name'>
                  </div>
                </div>";

            include_once 'inc/php/DB.php';

            $database = new DB();
            $result =  $database->query("SELECT * FROM tbl_province")->findAll();
             
            // ตรวจสอบ
            if(!empty($result)){
                // พบข้อมูล
              echo '<div class="form-group">
                      <div class="col-md-3">
                        <label>จังหวัด </label>
                      </div>
                      <div class="col-md-8">
                        <select id="province" name="province" class="form-control" >
                          <option value=""> --- เลือกจังหวัด --- </option>';
              foreach ($result as $province) {
                echo     '<option value="' . $province->PROVINCE_ID . '">' . $province->PROVINCE_NAME . '</option>';
              }
                  echo '</select>
                      </div>
                    </div>';
            }
             
            // อำเภอ
            echo '<div class="form-group">
                    <div class="col-md-3">
                      <label>อำเภอ </label>
                    </div>';
            echo '  <div class="col-md-8">
                      <select id="amphoe" name="amphoe" class="form-control">';
            echo '      <option value=""> --- กรุณาเลือกจังหวัด (ก่อน) --- </option>';
            echo '    </select>
                    </div>
                  </div>';
             
             
            // ตำบล
            echo '<div class="form-group">
                    <div class="col-md-3">
                      <label>ตำบล </label>
                    </div>';
            echo '  <div class="col-md-8">
                      <select id="district" name="district" class="form-control">>';
            echo '      <option value=""> --- กรุณาเลือกอำเภอ (ก่อน) --- </option>';
            echo '    </select>
                    </div>
                  </div>';
                               
            echo "<div class='form-group'>
                  <div class='col-md-3'>
                    <label>รายละเอียดที่อยู่</label>
                  </div>
                  <div class='col-md-8'>
                    <input type='text' class='form-control' name='address_line1' id='address_line1'
                      placeholder='รายละเอียดเพิ่มเติม (บ้านเลขที่,ซอย)'>
                  </div>
                </div>
                <div class='form-group'>
                  <div class='col-md-3'>
                    <label>ประเทศ </label>
                  </div>
                  <div class='col-md-8'>
                    <input type='text' class='form-control' name='address_country' id='address_country' 
                     value='ไทย' disabled >
                  </div>
                </div>
                <div class='form-group'>
                  <div class='col-md-3'>
                    <label>รหัสไปรษณีย์ </label>
                  </div>
                  <div class='col-md-8'>
                    <input type='text' class='form-control' name='address_zipcode' id='address_zipcode'>
                  </div>
                </div>
                <div class='form-group'>
                  <div class='col-md-3'>
                    <label>โทรศัพท์ </label>
                  </div>
                  <div class='col-md-8'>
                    <input type='text' class='form-control' name='address_tel' id='address_tel'>
                  </div>
                </div>
                ";
          ?>
      </div>
      <div class="modal-footer">
      	<input type="submit" class="btn btn-primary" name="add_address" value="เพิ่ม" >
      	<a href="index.php"><button class="btn btn-default" data-dismiss="modal">ยกเลิก</button></a>
				</form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="addBankAccount" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">เพิ่มบัญชีใหม่</h4>
      </div>
      <div class="modal-body">
        <form action="update.php?cusid=<?php echo $customer_id; ?>" method="post" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label">ธนาคาร</label>
            <div class="col-sm-10">
              <select name="add_bank_name" class="form-control">
                <option value="">ไม่เลือก</option>
                <option value="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>
                <option value="ธนาคารกสิกรไทย">ธนาคารกสิกรไทย</option>
                <option value="ธนาคารไทยพาณิชย์">ธนาคารไทยพาณิชย์</option>
                <option value="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>
                <option value="ธนาคารกรุงศรีอยุธยา">ธนาคารกรุงศรีอยุธยา</option>
                <option value="ธนาคารเกียรตินาคิน">ธนาคารเกียรตินาคิน</option>
                <option value="ธนาคารทหารไทย">ธนาคารทหารไทย</option>
                <option value="ธนาคารธนชาต">ธนาคารธนชาต</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">ชื่อบัญชี</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="add_account_name" >
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">เลขบัญชี</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="add_account_no" onkeypress="return isAccount(event)" >
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">สาขา</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="add_branch" >
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">หมายเหตุ</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="add_user_notes" >
            </div>
          </div> 
      </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-primary" name="add_bank_account" value="เพิ่ม" >
        <a href="index.php"><button class="btn btn-default" data-dismiss="modal">ยกเลิก</button></a>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function isPhoneNumber(evt) {
  //Enable arrow for firefox.
  if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
      if (evt.keyCode == 8 || evt.keyCode == 46 || evt.keyCode == 37 || evt.keyCode == 39) {
        return true;
    }
  }
  evt = (evt) ? evt : window.event;
  var charCode = (evt.which) ? evt.which : evt.keyCode;

  //Enable dash.
  if (charCode == 45) { return true; };

  if (charCode > 31 && (charCode < 48 || charCode > 57 )) {
      return false;
  }
  return true;
}

function isAccount(evt) {
    //Enable arrow for firefox.
    if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
        if (evt.keyCode == 8 || evt.keyCode == 46 || evt.keyCode == 37 || evt.keyCode == 39) {
          return true;
      }
    }

      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;

      if (charCode > 31 && (charCode < 48 || charCode > 57 )) {
          return false;
      }
      return true;
  }

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

jQuery(function($) {
    jQuery('body').on('change','#province',function(){
        jQuery.ajax({
            'type':'POST',
            'url':'inc/php/amphoe.php',
            'cache':false,
            'data':{province:jQuery(this).val()},
            'success':function(html){
                jQuery("#amphoe").html(html);
            }
        });
        return false;
    });
  jQuery('body').on('change','#amphoe',function(){
        jQuery.ajax({
            'type':'POST',
            'url':'inc/php/district.php',
            'cache':false,
            'data':{amphoe:jQuery(this).val()},
            'success':function(html){
                jQuery("#district").html(html);
            }
        });
        return false;
    });
  });


</script>
  </body>
</html>