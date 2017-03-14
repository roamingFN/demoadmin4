<?php
$formcode = "manage-bank";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}
  
	$bankid = $_POST['bankid'];

	mysql_query("delete from bank_payment where bank_id = '$bankid'");
?>