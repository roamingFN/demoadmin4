<?php
$formcode = "manage-user";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

	$user_id = $_POST['user_id'];

	mysql_query("delete from user where userid  = '$user_id'");
?>