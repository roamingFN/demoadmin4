<?php
$formcode = "manage-transportation";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}
  
	$transport_id = $_POST['transport_id'];

	mysql_query("delete from website_transport where transport_id  = '$transport_id'");
?>