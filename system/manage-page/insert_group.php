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
	$text_icon=$_POST['text_icon'];

	if(empty($name)){ $action['result'] = 'error'; array_push($text,'You forgot your name'); }
	if(empty($url)){ $action['result'] = 'error'; array_push($text,'You forgot your url'); }
	if(empty($image)){ $action['result'] = 'error'; array_push($text,'You forgot your image'); }
	if(empty($text_icon)){ $action['result'] = 'error'; array_push($text,'You forgot your text_icon'); }

	// To protect MySQL injection for Security purpose
	$name = stripslashes($name);
	$url = stripslashes($url);
	$image = stripslashes($image);
	$text_icon = stripslashes($text_icon);

	$name = mysql_real_escape_string($name);
	$url = mysql_real_escape_string($url);
	$image = mysql_real_escape_string($image);
	$text_icon = mysql_real_escape_string($text_icon);

	if($action['result'] != 'error'){

		mysql_query("insert into website_featured_cate (featured_cate_name,featured_cate_link,featured_cate_img,featured_cate_text_icon) 
					  values ('$name','$url','$image','$text_icon')");

	}

?>