<?php
$formcode = "order-remark";

	include '../connect.php';
	include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

	$remark_id = $_POST['remark_id'];

	mysql_query("delete from order_remark where remark_id  = $remark_id");
?>