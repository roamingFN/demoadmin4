<?php
$formcode = "profile";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isAddPermitted($formcode)) {
	echo "add_not_permitted";
	return;
}

	$email=$_POST['email'];
	$password=$_POST['password'];
	$firstname=$_POST['firstname'];
	$lastname=$_POST['lastname'];
	$phone=$_POST['phone'];
	$cus_class=$_POST['cus_class'];

	if(empty($email)){ $action['result'] = 'error'; array_push($text,'You forgot your email'); }
	if(empty($password)){ $action['result'] = 'error'; array_push($text,'You forgot your password'); }
	if(empty($firstname)){ $action['result'] = 'error'; array_push($text,'You forgot your firstname'); }
	if(empty($lastname)){ $action['result'] = 'error'; array_push($text,'You forgot your lastname'); }
	if(empty($phone)){ $action['result'] = 'error'; array_push($text,'You forgot your phone'); }
	if(empty($cus_class)){ $action['result'] = 'error'; array_push($text,'You forgot your class'); }

	// To protect MySQL injection for Security purpose
	$email = stripslashes($email);
	$password = stripslashes($password);
	$firstname = stripslashes($firstname);
	$lastname = stripslashes($lastname);
	$phone = stripslashes($phone);
	$cus_class = stripslashes($cus_class);

	$email = mysql_real_escape_string($email);
	$password = mysql_real_escape_string($password);
	$firstname = mysql_real_escape_string($firstname);
	$lastname = mysql_real_escape_string($lastname);
	$phone = mysql_real_escape_string($phone);
	$cus_class = mysql_real_escape_string($cus_class);

	if($action['result'] != 'error'){

		//encode password
		$password = sha1($password);

		$duplicate_email = mysql_num_rows(mysql_query("select * from customer where customer_email = '$email'"));

		if ($duplicate_email > 0) {

			$action['result'] = 'error';
			array_push($text,'Email '.$email.' is already exist, Please try to use another email.');
		
		}else{ 

			// SQL query to fetch information of registerd users and finds user match.
			// echo "insert into customer (customer_firstname,customer_lastname,customer_phone,customer_email,passwd,class_id) 
			// 		  values ('$firstname','$lastname','$phone','$email','$password','$cus_class')";
			$add = mysql_query("insert into customer (customer_firstname,customer_lastname,customer_phone,customer_email,passwd,class_id,add_datetime,active,add_user_id) 
					  values ('$firstname','$lastname','$phone','$email','$password','$cus_class',NOW(),1,'".$_SESSION['ID']."')");
		
			if ($add) {

				//get the new user id
				$userid = mysql_insert_id();

				//update customer code
				$cus_code = "O2E".str_pad($userid, 4, '0', STR_PAD_LEFT);
				$update_customer_code = mysql_query("update customer set customer_code = '$cus_code' where customer_id = '$userid'");
				
				//create a random key
				$key = $firstname . $email . date('mY');
				$key = md5($key);
				
				//add confirm row
				$confirm = mysql_query("insert into `customer_confirm` values(NULL,'$userid','$key','$email')");	

				if ($confirm) {
					
					//include the swift class
					include_once 'inc/php/swift/swift_required.php';
				
					//put info into an array to send to the function
					$info = array(
						'username' => $firstname,
						'email' => $email,
						'key' => $key);
				
					//send the email
					if(send_email($info)){
									
						//email sent
						$action['result'] = 'success';
						array_push($text,'Thanks for signing up. Please check your email for confirmation!');
					
					}else{
						
						$action['result'] = 'error';
						array_push($text,'Could not send confirm email');
					
					}

				}else{
					
					$action['result'] = 'error';
					array_push($text,'Confirm row was not added to the database. Reason: ' . mysql_error());
					
				}

				//session_start(); 
				//$_SESSION['login_user']=$email;
				//header("Location: index.php");
			}else{

				$action['result'] = 'error';
				array_push($text,'User could not be added to the database. Reason: ' . mysql_error());
			}	
		}

	}

?>