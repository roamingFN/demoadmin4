<?php
$formcode = "manage-class";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

	$class_id = $_POST['class_id'];

	mysql_query("update customer set class_id = '1' where class_id = '$class_id'");

	mysql_query("delete from customer_class where class_id = '$class_id'");
?>