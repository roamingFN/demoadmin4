<?php
$formcode = "manage-product-type";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}
  
	$producttypeid = $_POST['producttypeid'];

	mysql_query("delete from product_type where producttypeid = '$producttypeid'");
?>