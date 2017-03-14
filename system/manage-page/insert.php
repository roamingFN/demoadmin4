<?php
$formcode = "manage-page";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isAddPermitted($formcode)) {
  echo "add_not_permitted";
  return;
}

	$name=$_POST['name'];
	$url=$_POST['url'];
	$image=$_POST['img'];
	$price=$_POST['price'];
	$cat=$_POST['cat'];

	if(empty($name)){ $action['result'] = 'error'; array_push($text,'You forgot your name'); }
	if(empty($url)){ $action['result'] = 'error'; array_push($text,'You forgot your url'); }
	if(empty($image)){ $action['result'] = 'error'; array_push($text,'You forgot your image'); }
	if(empty($price)){ $action['result'] = 'error'; array_push($text,'You forgot your price'); }

	// To protect MySQL injection for Security purpose
	$name = stripslashes($name);
	$url = stripslashes($url);
	$image = stripslashes($image);
	$price = stripslashes($price);

	$name = mysql_real_escape_string($name);
	$url = mysql_real_escape_string($url);
	$image = mysql_real_escape_string($image);
	$price = mysql_real_escape_string($price);

	if($action['result'] != 'error'){

		mysql_query("insert into website_featured_item (featured_item_name,featured_item_link,featured_item_img,featured_item_price,featured_item_type) 
					  values ('$name','$url','$image','$price','$cat')");

	}

?>