<?php
$formcode = "manage-class";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isAddPermitted($formcode)) {
  echo "add_not_permitted";
  return;
}

	$class_name=$_POST['class_name'];

	if(empty($class_name)){ $action['result'] = 'error'; array_push($text,'You forgot your class_name'); }

	// To protect MySQL injection for Security purpose
	$class_name = stripslashes($class_name);

	$class_name = mysql_real_escape_string($class_name);

	if($action['result'] != 'error'){

		mysql_query("insert into customer_class (class_name) 
					  values ('$class_name')");
	}

?>