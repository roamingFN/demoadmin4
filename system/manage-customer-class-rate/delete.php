<?php
$formcode = "manage-class-rate";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

	$running = $_POST['running'];

	mysql_query("delete from customer_class_rate where running = '$running'");
?>