<?php
$formcode = "manage-transportation";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isAddPermitted($formcode)) {
  echo "add_not_permitted";
  return;
}

	$transport_th_name=$_POST['transport_th_name'];
	$transport_eng_name=$_POST['transport_eng_name'];

	// if(empty($starting_date)){ $action['result'] = 'error'; array_push($text,'You forgot your starting_date'); }
	if(empty($transport_th_name)){ $action['result'] = 'error'; array_push($text,'You forgot your transport_th_name'); }
	if(empty($transport_eng_name)){ $action['result'] = 'error'; array_push($text,'You forgot your transport_eng_name'); }

	// To protect MySQL injection for Security purpose
	$transport_th_name = stripslashes($transport_th_name);
	$transport_eng_name = stripslashes($transport_eng_name);

	$transport_th_name = mysql_real_escape_string($transport_th_name);
	$transport_eng_name = mysql_real_escape_string($transport_eng_name);

	if($action['result'] != 'error'){


		mysql_query("insert into website_transport (transport_th_name,transport_eng_name) 
					  values ('$transport_th_name','$transport_eng_name')");

	}

?>