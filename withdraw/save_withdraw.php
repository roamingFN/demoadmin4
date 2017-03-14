<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	
	$data = json_decode($_POST['data'],true);
	while($item = current($data)) {
		//update withdraw status
		$stmt = $con->prepare('UPDATE customer_request_withdraw SET withdraw_status=? WHERE withdraw_request_id=?');
		$stmt->bind_param('ss',$item['status'],key($data));
		$res = $stmt->execute();
		
		if($item['mode']==1) {
				//select topup
				$amount = $item['amount'];
				$tmpAmount = $amount;
				$cid = $item['cid'];

				$sql = 'SELECT topup_id,usable_amout FROM customer_request_topup WHERE customer_id=? AND usable_amout>0 ORDER BY topup_id ASC';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('s',$cid);
				$stmt->bind_result($tid,$usable_amount);
				$stmt->execute();
				while ($stmt->fetch()) {
						//echo $tid.' '.$usable_amount;
						if (($usable_amount>=$tmpAmount) && ($tmpAmount>0)) {
								updTopup($tmpAmount,$tid);
								$tmpAmount = 0;
						}
						else if ($tmpAmount>0) {
								updTopup($usable_amount,$tid);
								$tmpAmount = $tmpAmount-$usable_amount;
						}
				}

				//update current_amount
				$stmt = $con->prepare('UPDATE customer SET current_amount=current_amount-? WHERE customer_id=?');
				$stmt->bind_param('ss',$item['amount'],$item['cid']);
				$res = $stmt->execute();

				//insert statement
				$statement_name = 'withdraw เลขที่ '.$item['wno'];
				$debit = 0;
				$stmt = $con->prepare('INSERT into customer_statement (customer_id,statement_name,statement_date,debit,credit,withdraw_request_id) VALUES (?,?,?,?,?,?)');
				$stmt->bind_param('ssssss',$item['cid'],$statement_name,$item['datetime'],$debit,$item['amount'],key($data));
				$res = $stmt->execute();
		}
		else if($item['mode']==2) {
				//update current_amount
				$stmt = $con->prepare('UPDATE customer SET current_amount=current_amount+? WHERE customer_id=?');
				$stmt->bind_param('ss',$item['amount'],$item['cid']);
				$res = $stmt->execute();

				//delete statement
				$stmt = $con->prepare('DELETE from customer_statement WHERE withdraw_request_id=?');
				$stmt->bind_param('s',key($data));
				$res = $stmt->execute();
		}

		if(!$res){
				echo $con->error;
		}
		next($data);
	}
	echo 'success';
	
	$con->close();

	function updTopup ($amount,$tid) {
			include '../database.php';
			$sql = 'UPDATE customer_request_topup SET usable_amout=usable_amout-? WHERE topup_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ds',$amount,$tid);
			$stmt->execute();
			$stmt->close();
			$con->close();
	}
?>