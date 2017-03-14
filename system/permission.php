<?php
function isAdmin(){
	$select_user = mysql_query("select * from user where uid = '".$_SESSION['ID']."'");
  $user_row = mysql_fetch_array($select_user);
  if ($user_row['flag_admin'] == 1){
  	return true;
  }else{
  	return false;
  }
}

function isViewPermitted($formcode){
	if (isAdmin()){return true;}
	if (isset($formcode)) {
		$query = mysql_query("select * from useraccess a, form f, user u 
							where a.formid = f.formid 
							and a.userid = u.userid
							and u.uid = '".$_SESSION['ID']."' 
							and f.formcode = '".$formcode."' ");  
		if (mysql_num_rows($query) > 0) {
			$row = mysql_fetch_array($query);
			if ($row['visible'] == 1) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function isAddPermitted($formcode){
	if (isAdmin()){return true;}
	if (isset($formcode)) {
		$query = mysql_query("select * from useraccess a, form f, user u 
							where a.formid = f.formid 
							and a.userid = u.userid
							and u.uid = '".$_SESSION['ID']."' 
							and f.formcode = '".$formcode."' ");
		if (mysql_num_rows($query) > 0) {
			$row = mysql_fetch_array($query);
			if ($row['canadd'] == 1) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function isActionPermitted($formcode){
	if (isAdmin()){return true;}
	if (isset($formcode)) {
		$query = mysql_query("select * from useraccess a, form f, user u 
							where a.formid = f.formid 
							and a.userid = u.userid
							and u.uid = '".$_SESSION['ID']."' 
							and f.formcode = '".$formcode."' ");
		if (mysql_num_rows($query) > 0) {
			$row = mysql_fetch_array($query);
			if ($row['action'] == 1) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}

?>