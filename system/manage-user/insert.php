<?php
$formcode = "manage-user";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isAddPermitted($formcode)) {
  echo "add_not_permitted";
  return;
}

	$uid=$_POST['uid'];
	$email=$_POST['email'];
	$disable=$_POST['disable'];
	$password=$_POST['password'];

	if(empty($uid)){ $action['result'] = 'error'; array_push($text,'You forgot your uid'); }
	if(empty($password)){ $action['result'] = 'error'; array_push($text,'You forgot your password'); }

	// To protect MySQL injection for Security purpose
	$uid = stripslashes($uid);
	$email = stripslashes($email);
	$disable = stripslashes($disable);
	$password = stripslashes($password);

	$uid = mysql_real_escape_string($uid);
	$email = mysql_real_escape_string($email);
	$disable = mysql_real_escape_string($disable);
	$password = mysql_real_escape_string($password);

	if ($disable == "no") {
    $disable = 0;
  }else if ($disable == "yes") {
    $disable = 1;
  }

	if($action['result'] != 'error'){

		$password = md5($password);

		mysql_query("insert into user (uid,password,email,disable,adduser,adddate) 
					  values ('$uid','$password','$email',$disable,'".$_SESSION['ID']."',NOW())");

    $userid = mysql_insert_id();

    $select_form = mysql_query("select * from form");
    while ($row = mysql_fetch_array($select_form)) {
      mysql_query("insert into useraccess(userid,formid,visible,canadd,action) 
          value('$userid',".$row['formid'].",1,0,0)");
    }

	}

?>