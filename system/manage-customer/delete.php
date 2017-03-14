<?php
$formcode = "profile";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$customer_id = $_POST['cusid'];
mysql_query("update customer set status='2' where customer_id = '$customer_id'");
// mysql_query("delete from customer where customer_id = '$customer_id'");
?>