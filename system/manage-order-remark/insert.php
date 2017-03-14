<?php
$formcode = "order-remark";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isAddPermitted($formcode)) {
  echo "add_not_permitted";
  return;
}
	
	$remark_id=$_POST['remark_id'];
	$remark_eng=$_POST['remark_eng'];
	$remark_tha=$_POST['remark_tha'];

	if(empty($remark_id)){ $action['result'] = 'error'; array_push($text,'You forgot your remark_id'); }
	if(empty($remark_eng)){ $action['result'] = 'error'; array_push($text,'You forgot your remark_eng'); }
	if(empty($remark_tha)){ $action['result'] = 'error'; array_push($text,'You forgot your remark_tha'); }

	// To protect MySQL injection for Security purpose
	$remark_id = stripslashes($remark_id);
	$remark_eng = stripslashes($remark_eng);
	$remark_tha = stripslashes($remark_tha);

	$remark_id = mysql_real_escape_string($remark_id);
	$remark_eng = mysql_real_escape_string($remark_eng);
	$remark_tha = mysql_real_escape_string($remark_tha);

	if($action['result'] != 'error'){

		$insert = mysql_query("insert into order_remark (remark_id,remark_tha,remark_eng) 
					      values ('$remark_id','$remark_tha','$remark_eng')");

		if ($insert) {
			echo "success";
		}else{
			echo mysql_error();
		}
	}

?>