<?php
$formcode = "manage-user";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$error = '';
$user_id = $_GET['user_id'];
$alert = '';

	//create new topup requset
	if (!empty($_POST['edit-user'])) {

    // -- update user info -- //
		$userid = $_POST['userid'];
		$password 	= $_POST['password'];
    $email      = $_POST['email'];
    $disable    =  $_POST['disable'];

		$SQL_QUERY = "update user set ";
		$FIRST_VAR  = true;

		if(!is_null($password)){
			$password 	= stripcslashes($password);
			$password 	= mysql_real_escape_string($password);
			$password 	= md5($password);
			if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
			$SQL_QUERY .= " password = '$password' ";
		}
    if(!empty($email)){
      $email   = stripcslashes($email);
      $email   = mysql_real_escape_string($email);
      if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
      $SQL_QUERY .= " email = '$email' ";
    }
    if(!empty($disable)){
      $disable   = stripcslashes($disable);
      $disable   = mysql_real_escape_string($disable);
      if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
      if ($disable == "no") {
        $disable = 0;
      }else if ($disable == "yes") {
        $disable = 1;
      }
      $SQL_QUERY .= " disable = '$disable' ";
    }
    
    if (!$FIRST_VAR) { $SQL_QUERY .= ",";} else { $FIRST_VAR = false;}
    $SQL_QUERY .= " editdate = NOW(), edituser = '".$_SESSION['ID']."' ";

		$SQL_QUERY .= " where userid = '$userid' ";

    $add_user = mysql_query($SQL_QUERY);

    // -- update permission -- //
    $select_user = mysql_query("select * from user where userid = '$userid'");
    $user_row = mysql_fetch_array($select_user);
    if ($user_row['flag_admin'] != 1) {
      //update permission only not admin
      $formid    = $_POST['formid'];
      $visible   = $_POST['visible'];
      $canadd    = $_POST['canadd'];
      $action    = $_POST['action'];

      //delete old permission
      $delete_permission = mysql_query("delete from useraccess where userid = '$userid'");

      //insert new permission row
      for ($i=0; $i < count($formid) ; $i++) { 
        mysql_query("insert into useraccess(userid,formid,visible,canadd,action) 
          value('$userid',".$formid[$i].",0,0,0)");
      }

      //update visible  
      for ($i=0; $i < count($visible) ; $i++) { 
        mysql_query("update useraccess set visible = 1 where formid = ".$visible[$i]." and userid = ".$userid." ");
      }

      //update canadd
      for ($i=0; $i < count($canadd) ; $i++) { 
        mysql_query("update useraccess set canadd = 1 where formid = ".$canadd[$i]." and userid = ".$userid." ");
      }

      //update action
      for ($i=0; $i < count($action) ; $i++) { 
        mysql_query("update useraccess set action = 1 where formid = ".$action[$i]." and userid = ".$userid." ");
      }

    }
		
		if ($add_user) {
			$alert = '<div class="alert alert-success container" role="alert"><label>แก้ไขข้อมูลสำเร็จ</label></div>';
		}else{
			$error .= "<li>Error : ".mysql_error()."</li>";
			$alert = '<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label>'.$error.'</div>';
		}
	}


?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage User</title>
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
  <?php echo $alert; ?>
	<div class="container">
  <h1><a href="index.php">Manage User</a></h1>
  <h3><a href="index.php">&larr; Back</a></h3><br>
		<br />
		<form class="form-horizontal" role="form" action="update.php?user_id=<?php echo $user_id ?>" 
		method="post" enctype="multipart/form-data" >
		<?php
			$select_user = mysql_query("select * from user where userid ='$user_id'");
			$row = mysql_fetch_array($select_user);
		?>

			<div class="form-group">
				<label class="col-md-3 control-label">รหัสผู้ใช้งาน</label>
        <div class="col-md-8">
            <input type="text" class="form-control" 
            placeholder="รหัสผู้ใช้งาน" value="<?php echo $row['uid']; ?>" disabled>
            <input type="hidden" class="form-control" name="userid" value="<?php echo $row['userid']; ?>" >
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-3 control-label">Email</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="email" value="<?php echo $row['email']; ?>"
            placeholder="Email">
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-3 control-label">Disable</label>
        <div class="col-md-8">
            <select class="form-control" name="disable" >
              <option value="no" <?php if($row['disable']==0)echo "selected"; ?> >No</option>
              <option value="yes" <?php if($row['disable']==1)echo "selected"; ?> >Yes</option>
            </select>
        </div>
      </div>

			<div class="form-group">
				<label class="col-md-3 control-label">รหัสผ่าน</label>
        <div class="col-md-8">
            <input type="password" class="form-control" name="password" 
            placeholder="รหัสผ่าน" value="">
        </div>
      </div>

      <div class="form-group">
        <label class="col-md-3 control-label">Permission</label>
        <div class="col-md-8">
            <table>
              <tr>
                <th>Form</th>
                <th>Visible</th>
                <th>Add</th>
                <th>Action</th>
              </tr>
              <?php

                $select_form = mysql_query("select * from form order by form.formid");
                while ($row = mysql_fetch_array($select_form)) {

                  $select_user = mysql_query("select * from user where userid = '$user_id'");
                  $user_row = mysql_fetch_array($select_user);
                  if ($user_row['flag_admin'] == 1) {
                    $checkbox_visible = "checked disabled";
                    $checkbox_canadd  = "checked disabled";
                    $checkbox_action  = "checked disabled";
                  }else{
                    $select_access = mysql_query("select * from useraccess a 
                      where a.formid = '".$row['formid']."' 
                      and a.userid = '$user_id'");
                    if (mysql_num_rows($select_access) == 1) {
                      $access_row = mysql_fetch_array($select_access);
                      $checkbox_visible = "";
                      $checkbox_canadd = "";
                      $checkbox_action = "";

                      if ($access_row['visible'] == 1) {
                        $checkbox_visible = "checked";
                        $disable_value = "";
                      }else{
                        $disable_value = "disabled";
                      }
                      if ($access_row['canadd'] == 1) {
                        $checkbox_canadd = "checked";
                      }
                      if ($access_row['action'] == 1) {
                        $checkbox_action = "checked";
                      }
                    }
                  }
                  
                  echo "
                  <tr>
                    <td>".$row['formcode']." (".$row['remark'].")<input type='hidden' name='formid[]' value='".$row['formid']."' ></td>
                    <td><input type='checkbox' name='visible[]' value='".$row['formid']."' $checkbox_visible ></td>
                    <td><input type='checkbox' name='canadd[]' value='".$row['formid']."' $checkbox_canadd $disable_value ></td>
                    <td><input type='checkbox' name='action[]' value='".$row['formid']."' $checkbox_action $disable_value ></td>
                  </tr>";
                }
              ?>
            </table>
        </div>
      </div>

      <div class="form-group">
      	<label class="col-md-3 control-label"></label>
        <div class="col-md-8">
        	<input type="submit" name="edit-user" class="btn btn-primary" value="บันทึก">
        </div>
      </div>

		</form>
	</div>
	<br />
<script type="text/javascript">
  $(document).ready(function() {
    $('input:checkbox[name="visible[]"]').change(function() {
      if (this.checked == true) {
        var formid = this.value;
        var canadd = document.getElementsByName('canadd[]');
        for(var i=0; i<canadd.length; i++) {
          //alert(canadd[i].value);
          if (canadd[i].value == formid) {
            canadd[i].disabled = false;
          }
        }
        var action = document.getElementsByName('action[]');
        for(var i=0; i<action.length; i++) {
          if (action[i].value == formid) {
            action[i].disabled = false;
          }
        }
      }else{
        var formid = this.value;
        var canadd = document.getElementsByName('canadd[]');
        for(var i=0; i<canadd.length; i++) {
          //alert(canadd[i].value);
          if (canadd[i].value == formid) {
            canadd[i].checked = false;
            canadd[i].disabled = true;
          }
        }
        var action = document.getElementsByName('action[]');
        for(var i=0; i<action.length; i++) {
          if (action[i].value == formid) {
            action[i].checked = false;
            action[i].disabled = true;
          }
        }
      }
    });
  });
</script>
</body>
</html>