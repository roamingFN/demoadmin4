<?php
		if (!isset($_SESSION['ID'])) {
				header("Location: login.php");
		}

	    function getAccessPermission($con,$userid) {
	    		if (empty($userid)) return;

	    		$data = array();
	    		$sql = 'SELECT accessid,formid,visible,canadd,action FROM useraccess WHERE userid='.$userid;
	    		$result = $con->query($sql); 
				if (!$result) {
						throw new Exception("Database Error ".$con->error);
				}
				else {
			    		$row = $result->num_rows;
						while ($row = $result->fetch_assoc()) { 
								$data[] = $row;
						}
				}
	    		return json_encode($data);
	    }

	    function getAccessForm($con,$formid,$userid) {
	    		$data = array();
	    		$sql = 'SELECT visible,canadd,action FROM useraccess WHERE formid=? AND userid=? ORDER BY formid asc';
	    		$stmt = $con->prepare($sql);
	    		$stmt->bind_param('ii',$formid,$userid);
	    		$stmt->execute();
	    		$res = $stmt->get_result();
				while($row = $res->fetch_assoc()) { 
						$data[] = $row;       
				}
	    		return json_encode($data);
	    }

	    function getAdminFlag($con,$uid) {
			$flg = 0;
			$sql = 'SELECT flag_admin FROM user WHERE uid=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('s',$uid);
			$stmt->bind_result($flg);
			$stmt->execute();
			while ($stmt->fetch()) {
					$flg=$flg;
			}
			return $flg;
		}
?>