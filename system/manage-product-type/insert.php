<?php
$formcode = "manage-product-type";

include '../connect.php';
include '../session.php';
include '../permission.php';
include './function.php';

if (!isAddPermitted($formcode)) {
  echo "add_not_permitted";
  return;
}

	$producttypeid=$_POST['producttypeid'];
	$rate_type=$_POST['rate_type'];
	$producttypename=$_POST['producttypename'];
	$product_type=$_POST['product_type'];

	if(empty($producttypeid)){ $action['result'] = 'error'; array_push($text,'You forgot your producttypeid'); }
	if(empty($rate_type)){ $action['result'] = 'error'; array_push($text,'You forgot your rate_type'); }
	if(empty($producttypename)){ $action['result'] = 'error'; array_push($text,'You forgot your producttypename'); }
	else { if(isDupProdTypeName($producttypename)) { echo 'add_duplicate_name'; return;}}
	if(empty($product_type)){ $action['result'] = 'error'; array_push($text,'You forgot your product_type'); }

	// To protect MySQL injection for Security purpose
	$producttypeid = stripslashes($producttypeid);
	$rate_type = stripslashes($rate_type);
	$producttypename = stripslashes($producttypename);
	$product_type = stripslashes($product_type);

	$producttypeid = mysql_real_escape_string($producttypeid);
	$rate_type = mysql_real_escape_string($rate_type);
	$producttypename = mysql_real_escape_string($producttypename);
	$product_type = mysql_real_escape_string($product_type);

	if($action['result'] != 'error') {

		$add = mysql_query("insert into product_type (producttypeid,rate_type,producttypename,product_type) 
					  values ('$producttypeid','$rate_type','$producttypename','$product_type')");

		if (!$add) {
			echo mysql_error();
		}
	}

?>