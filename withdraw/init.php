<?php
		$_customers = getCusInfo($con);
		$_banks = getBankInfo($con);

		//----------------------------------------------------------------------------------
		$sql = '';
		$sqlAll = '';
		$request = '';
		$condition = ' WHERE w.withdraw_status=0';
		$orderBy = ' ORDER BY w.withdraw_request_id DESC';
	
		//search
        $_SESSION['sql'] = '';
		$cases = array();
		if (!empty($_GET['cid'])) {
				array_push($cases,' w.customer_id='.$_GET['cid']);
				$request .= 'cid='.$_GET['cid'];
		}
        if (!empty($_GET['cbid'])) {
        		array_push($cases,' w.customer_bank_account_id='.getCbid($_GET['cbid']));
        		$request .= 'cbid='.$_GET['cbid'];
        }
		if (!empty($_GET['from'])) {            
            	array_push($cases,' w.withdraw_date>="'.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2).' 00:00:00"');
            	$request .= 'from='.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2);
        }
		if (!empty($_GET['to'])) {
            	array_push($cases,' w.withdraw_date<="'.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2).' 23:59:59"');
            	$request .= 'to='.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2);
        }
		if (!empty($_GET['amount'])) {
				array_push($cases,' w.withdraw_amount="'.$_GET['amount'].'"');
				$request .= 'amount='.$_GET['amount'];
		}
		if (isset($_GET['status'])) {
        		if($_GET['status']=='-') {
        				array_push($cases,' w.withdraw_status>=0');
        				$request .= 'status=-';
        		}                  
                else {
                		array_push($cases,' w.withdraw_status='.$_GET['status']);
                		$request .= 'status='.$_GET['status'];
                }                             
        }       

		if(sizeof($cases)>0) {
				$condition = ' WHERE '.$cases[0];
				for($i=1;$i<sizeof($cases);$i++) {
						$condition .= ' AND'.$cases[$i];
				}
		}

		$sql = 'SELECT w.withdraw_request_id,w.withdraw_number,w.customer_id,w.customer_bank_account_id,w.withdraw_amount,w.withdraw_date,w.withdraw_payment_date,w.withdraw_status,w.comment,c.customer_firstname,c.customer_lastname,cb.bank_name,cb.account_no,cb.account_name
			FROM customer_request_withdraw w JOIN customer c ON w.customer_id=c.customer_id
			JOIN customer_bank_account cb ON w.customer_bank_account_id=cb.bank_account_id';

		$sqlCount = 'SELECT COUNT(*) 
			FROM customer_request_withdraw w JOIN customer c ON w.customer_id=c.customer_id
			JOIN customer_bank_account cb ON w.customer_bank_account_id=cb.bank_account_id';

        $_SESSION['sql'] = $sql;
        $_SESSION['condition'] = $condition;
        
?>