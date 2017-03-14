<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include 'database.php';
	
	$data = json_decode($_POST['data'],true);
	while($item = current($data)){
		$stmt = $con->prepare('UPDATE cash SET topup_id=?,status=? WHERE cashid=?');
		$stmt->bind_param('sss',$item['tid'],$item['stat'],key($data));
		$res = $stmt->execute();
		if(!$res){
			echo $con->error;
		}
		/*$stmt = $con->prepare(
			'DELIMITER // '.

			'CREATE FUNCTION Save() '.
			'BEGIN '.

			'IF EXISTS (select * from customer_statement where topup_id = ?) THEN '.
				'update customer_request_topup set topup_status=0 where topup_id=?;'.
				'update customer_request_topup set topup_status=1 where topup_id=?;'.
				'update customer set current_amount = current_amount-? where customer_id=?;'.
				'delete from customer_statement where topup_id=?;'.
				'insert into customer_statement (customer_id,statement_name,statement_date,debit,credit,topup_id) values (?,"เติมเงิน -  " + ?,?,?,0,?);'.
				
			'ELSE '.
				'update customer_request_topup set topup_status=1 where topup_id=?;'.
				'update customer set current_amount = current_amount+? where customer_id=?;'.
				'insert into customer_statement (customer_id,statement_name,statement_date,debit,credit,topup_id) values (?,"เติมเงิน -  " + ?,?,?,0,?);'.
			'END IF;'.
			'END; //'.

			'DELIMITER ;'
		);
		$stmt->bind_param('sssdsssssdssdssssds',$item['tid'],$item['old'],$item['tid'],$item['topup'],$item['cid'],$item['tid'],$item['cid'],$item['tnum'],$item['date'],$item['topup'],$item['tid'],$item['tid'],$item['topup'],$item['cid'],$item['cid'],$item['tnum'],$item['date'],$item['topup'],$item['tid']);
		$res = $stmt->execute();
		if(!$res){
			echo $con->error;
		}*/
		$sql = 'CALL SavePayment('.$item['tid'].','.$item['old'].','.$item['topup'].','.$item['otopup'].','.$item['cid'].',"'.$item['tnum'].'","'.$item['date'].'")';
		if(!$res = $con->query($sql)){
			echo $con->error;
		}
		
		next($data);
	}
	echo 'success';
	
	$con->close();
?>