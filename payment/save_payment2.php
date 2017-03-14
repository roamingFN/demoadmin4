<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	
	$data = json_decode($_POST['data'],true);
	//print_r($data);
	while($item = current($data)){
		//update cash topup_id
		$stmt = $con->prepare('UPDATE cash SET topup_id=?,status=? WHERE cashid=?');
		$stmt->bind_param('sss',$item['tid'],$item['stat'],key($data));
		$res = $stmt->execute();

		if($item['mode']==1) {		
				//update topup status 0->1
				$stmt = $con->prepare('UPDATE customer_request_topup SET topup_status=1 WHERE topup_id=?');
				$stmt->bind_param('s',$item['tid']);
				$res = $stmt->execute();

				//update current amount
				$stmt = $con->prepare('UPDATE customer SET current_amount=current_amount+? WHERE customer_id=?');
				$stmt->bind_param('ss',$item['topup'],$item['cid']);
				$res = $stmt->execute();

				//insert statement
				$statement_name = 'เติมเงิน -  '.$item['tnum'];
				$stmt = $con->prepare('INSERT into customer_statement (customer_id,statement_name,statement_date,debit,credit,topup_id) VALUES (?,?,?,?,?,?)');
				$credit = 0;
				$stmt->bind_param('ssssss',$item['cid'],$statement_name,$item['date'],$item['topup'],$credit,$item['tid']);
				$res = $stmt->execute();
		}
		else if($item['mode']==2) {
				//update old topup status 1->0
				$stmt = $con->prepare('UPDATE customer_request_topup SET topup_status=0 WHERE topup_id=?');
				$stmt->bind_param('s',$item['old']);
				$res = $stmt->execute();

				//update new topup status 0->1
				$stmt = $con->prepare('UPDATE customer_request_topup SET topup_status=1 WHERE topup_id=?');
				$stmt->bind_param('s',$item['tid']);
				$res = $stmt->execute();

				//update current amount
				$stmt = $con->prepare('UPDATE customer SET current_amount=current_amount-?+? WHERE customer_id=?');
				$stmt->bind_param('sss',$item['otopup'],$item['topup'],$item['cid']);
				$res = $stmt->execute();

				//delete old statement
				$stmt = $con->prepare('DELETE from customer_statement WHERE topup_id=?');
				$stmt->bind_param('s',$item['old']);
				$res = $stmt->execute();
		
				//insert statement
				$statement_name = 'เติมเงิน -  '.$item['tnum'];
				$stmt = $con->prepare('INSERT into customer_statement (customer_id,statement_name,statement_date,debit,credit,topup_id) VALUES (?,?,?,?,?,?)');
				$credit = 0;
				$stmt->bind_param('ssssss',$item['cid'],$statement_name,$item['date'],$item['topup'],$credit,$item['tid']);
				$res = $stmt->execute();
		}
		else if($item['mode']==3) {
				//update old topup status 1->0
				$stmt = $con->prepare('UPDATE customer_request_topup SET topup_status=0 WHERE topup_id=?');
				$stmt->bind_param('s',$item['old']);
				$res = $stmt->execute();

				//update current amount
				$stmt = $con->prepare('UPDATE customer SET current_amount=current_amount-? WHERE customer_id=?');
				$stmt->bind_param('ss',$item['otopup'],$item['cid']);
				$res = $stmt->execute();

				//delete old statement
				$stmt = $con->prepare('DELETE from customer_statement WHERE topup_id=?');
				$stmt->bind_param('s',$item['old']);
				$res = $stmt->execute();
		}

		if(!$res){
				echo $con->error;
		}
		next($data);
	}
	echo 'success';
	
	$con->close();
?>