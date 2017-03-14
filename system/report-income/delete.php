<?php
	include '../connect.php';
	include '../session.php';
	$rate_id = $_POST['rate_id'];
  $user_id = $_SESSION['USERID'];

	//mysql_query("delete from website_rate where website_rate_id  = '$rate_id'");
  mysql_query("update website_rate set edit_user_id = '$user_id', edit_datetime = NOW(), status = '2' where website_rate_id  = '$rate_id'");
?>