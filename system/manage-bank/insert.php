<?php
$formcode = "manage-bank";

include '../connect.php';
include '../session.php';
include '../permission.php';
include './function.php';

if (!isAddPermitted($formcode)) {
  echo "add_not_permitted";
  return;
}

	$bankid=$_POST['bankid'];
	$account_name=$_POST['account_name'];
	$account_no=$_POST['account_no'];
	$bank_name_th=$_POST['bank_name_th'];
	$bank_name_en=$_POST['bank_name_en'];
	$branch=$_POST['branch'];

	if(empty($bankid)){ $action['result'] = 'error'; array_push($text,'You forgot your bank id'); }
	if(empty($account_name)){ $action['result'] = 'error'; array_push($text,'You forgot your account name'); }
	if(empty($account_no)){ $action['result'] = 'error'; array_push($text,'You forgot your account number'); }
	if(empty($bank_name_th)){ $action['result'] = 'error'; array_push($text,'You forgot your bank name TH'); }
	if(empty($bank_name_en)){ $action['result'] = 'error'; array_push($text,'You forgot your bank name EN'); }
	if(empty($branch)){ $action['result'] = 'error'; array_push($text,'You forgot your branch'); }

	// To protect MySQL injection for Security purpose
	$bankid = stripslashes($bankid);
	$account_name = stripslashes($account_name);
	$account_no = stripslashes($account_no);
	$bank_name_th = stripslashes($bank_name_th);
	$bank_name_en = stripslashes($bank_name_en);
	$branch = stripslashes($branch);

	$bankid = mysql_real_escape_string($bankid);
	$account_name = mysql_real_escape_string($account_name);
	$account_no = mysql_real_escape_string($account_no);
	$bank_name_th = mysql_real_escape_string($bank_name_th);
	$bank_name_en = mysql_real_escape_string($bank_name_en);
	$branch = mysql_real_escape_string($branch);

	if($action['result'] != 'error') {

		$add = mysql_query("insert into bank_payment (bank_id,account_name,account_no,bank_name_th,bank_name_en,bank_branch) 
					  values ('$bankid','$account_name','$account_no','$bank_name_th','$bank_name_en','$branch')");

		if (!$add) {
			echo mysql_error();
		}
	}

?>