<?php
$formcode = "manage-rate";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isAddPermitted($formcode)) {
  echo "add_not_permitted";
  return;
}

	$starting_date=$_POST['starting_date'];
	$begin_time=$_POST['begin_time'];
	$rate_cny=$_POST['rate_cny'];

	if(empty($starting_date)){ $action['result'] = 'error'; array_push($text,'You forgot your starting_date'); }
	if(empty($rate_cny)){ $action['result'] = 'error'; array_push($text,'You forgot your rate_cny'); }

	// To protect MySQL injection for Security purpose
	$starting_date = stripslashes($starting_date);
	$rate_cny = stripslashes($rate_cny);

	$starting_date = mysql_real_escape_string($starting_date);
	$rate_cny = mysql_real_escape_string($rate_cny);

	if($action['result'] != 'error'){

		$last_rate_query = mysql_query("select * from website_rate order by website_rate_id desc, begin_time desc limit 1");
		$last_rate_row = mysql_fetch_array($last_rate_query);
		$last_rate_date = $last_rate_row['starting_date'];
		$last_rate_time = $last_rate_row['begin_time'];

		$date1 = str_replace('/', '-', $starting_date." ".$begin_time);
		$date1 = date('d-m-Y H:i', strtotime($date1));

		$date2 = str_replace('/', '-', $last_rate_date." ".$last_rate_time);
		$date2 = date('Y-m-d H:i:s', strtotime($date2));

		// echo date('1>> Y-m-d H:i:s', strtotime($date1));
		// echo date('2>> Y-m-d H:i:s', strtotime($date2));

		if(strtotime($date1) > strtotime($date2)){

			$starting_date = date('Y-m-d', strtotime($date1));
			$begin_time = date('H:i:s', strtotime($date1));
			$user_id = $_SESSION['USERID'];

			$insert = mysql_query("insert into website_rate (starting_date,begin_time,rate_cny,add_user_id,add_datetime) 
					  values ('$starting_date','$begin_time','$rate_cny','$user_id',NOW())");
			if ($insert) {
				//update rate to all order in status 0,1
				$update_order = mysql_query("update customer_order set order_rate = '$rate_cny' where order_status_code = '0' 
					or order_status_code = '1'");

				if ($update_order) {
					echo "SUCCESS";
				}else{
					echo mysql_error();
				}
				
			}else{
				echo mysql_error();
			}
		}else{
			echo "FAIL";
		}


		

	}

?>