<?php
$formcode = "manage-class-rate";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isAddPermitted($formcode)) {
  echo "add_not_permitted";
  return;
}

	$class_id=$_POST['class_id'];
	$rate_type=$_POST['rate_type'];
	$begincal=$_POST['begincal'];
	$endcal=$_POST['endcal'];
	$product_type=$_POST['product_type'];
	$rate_amount=$_POST['rate_amount'];

	if(empty($class_id)){ $action['result'] = 'error'; array_push($text,'You forgot your class_id'); }
	if(empty($rate_type)){ $action['result'] = 'error'; array_push($text,'You forgot your rate_type'); }
	if(empty($begincal)){ $action['result'] = 'error'; array_push($text,'You forgot your begincal'); }
	if(empty($endcal)){ $action['result'] = 'error'; array_push($text,'You forgot your endcal'); }
	if(empty($product_type)){ $action['result'] = 'error'; array_push($text,'You forgot your product_type'); }
	if(empty($rate_amount)){ $action['result'] = 'error'; array_push($text,'You forgot your rate_amount'); }

	// To protect MySQL injection for Security purpose
	$class_id = stripslashes($class_id);
	$rate_type = stripslashes($rate_type);
	$begincal = stripslashes($begincal);
	$endcal = stripslashes($endcal);
	$product_type = stripslashes($product_type);
	$rate_amount = stripslashes($rate_amount);

	$class_id = mysql_real_escape_string($class_id);
	$rate_type = mysql_real_escape_string($rate_type);
	$begincal = mysql_real_escape_string($begincal);
	$endcal = mysql_real_escape_string($endcal);
	$product_type = mysql_real_escape_string($product_type);
	$rate_amount = mysql_real_escape_string($rate_amount);

	if($action['result'] != 'error'){

		mysql_query("insert into customer_class_rate (class_id,rate_type,begincal,endcal,product_type,rate_amount) 
					  values ('$class_id','$rate_type','$begincal','$endcal','$product_type','$rate_amount')");

	}

?>