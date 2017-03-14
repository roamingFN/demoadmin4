<?php
$formcode = "manage-page";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isActionPermitted($formcode)) {
  echo "action_not_permitted";
  return;
}

$page_id = $_POST['page_id'];

mysql_query("delete from website_featured_item where featured_item_id  = '$page_id'");
?>