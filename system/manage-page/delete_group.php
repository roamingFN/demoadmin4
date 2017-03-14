<?php
$formcode = "manage-page";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$group_id = $_POST['group_id'];

mysql_query("delete from website_featured_cate where featured_cate_id  = '$group_id'");
?>