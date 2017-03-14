<?php
		function getData($con,$sql,$condition,$groupBy,$orderBy,$paging) {
				if(!isset($con)) return;
				if(!isset($sql)) return;
				if(!isset($condition)) $condition = '';
				if(!isset($orderBy)) $orderBy = '';
				if(!isset($groupBy)) $groupBy = '';
				if(!isset($paging)) $paging = '';
				//echo $sql.$condition.$orderBy.$groupBy.$paging;
				//get data
				$dataSet = array();
				$queryResult = $con->query($sql.$condition.$groupBy.$orderBy.$paging); 
				if (!$queryResult) {
						echo ("Error while getting data ".$con->error);
						return;
				} 
				while ($row = $queryResult->fetch_assoc()) {
						$dataSet[] = $row; 
				}
				return $dataSet;
		}

		function getNumberOfRows($con,$sql,$condition,$groupBy) {
				if(!isset($con)) return;
				if(!isset($sql)) return;
				if(!isset($condition)) $condition = '';
				if(!isset($groupBy)) $groupBy = '';

				$result = 0;
				if ( $stmt = $con->prepare($sql.$condition.$groupBy) ) {
					    $stmt->execute();
						$stmt->bind_result($count);
						while($stmt->fetch()) {
								$result = $count;
						}
				}
				else {
						echo "Error while getting number of rows ".$con->error;
				}
				return $result;
		}

		function getCusInfo($con) {
				//get customer info
				$_cus = array();
				if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
						$stmt->execute();
						$stmt->bind_result($cid,$cfname,$clname);
						while($stmt->fetch()){
	                 		$_cus[$cid] = $cfname.' '.$clname;
						}
				}
				else {
						echo ("Error while getting customer information ".$con->error);
				}
				$stmt->close();
				return $_cus;
		}

		function getBankInfo($con) {
				//get bank info
				$_bank = array();
				if($stmt = $con->prepare('SELECT bank_account_id,bank_name,account_no,account_name FROM customer_bank_account')){
						$stmt->execute();
						$stmt->bind_result($bid,$bname,$accnum,$accname);
						while($stmt->fetch()){
	                 		$_bank[$bid] = $bname.' '.$accnum.' '.$accname;
						}
				}
				else {
						echo ("Error while getting bank information ".$con->error);
				}
				$stmt->close();
				return $_bank;
		}

		function genWithdrawNumber($con) {
				$i = 0;
				$newTT = '0000001';
				$stmt = $con->prepare ( 'SELECT withdraw_number FROM customer_request_withdraw ORDER BY withdraw_number DESC LIMIT 1' );
				$stmt->execute ();
				$result = $stmt->get_result ();
				while ( $row = $result->fetch_array ( MYSQLI_NUM ) ) {
					foreach ( $row as $r ) {
						$i += 1;
					}
				}
				// find year
				$year = substr ( ( string ) date ( "Y" ), 2, 2 );
				// cash has records
				if ($i) {
					$tempTT = ( string ) (( int ) substr ( $r, - 7 ) + 1);
					$len = strlen ( $tempTT );
					$yearBase = substr ( $r, 1, 2 );
					if ($year != $yearBase) {
					} else if ($len == 6) {
						$newTT = '0' . $tempTT;
					} else if ($len == 5) {
						$newTT = '00' . $tempTT;
					} else if ($len == 4) {
						$newTT = '000' . $tempTT;
					} else if ($len == 3) {
						$newTT = '0000' . $tempTT;
					} else if ($len == 2) {
						$newTT = '00000' . $tempTT;
					} else if ($len == 1) {
						$newTT = '000000' . $tempTT;
					} else {
						$newTT = $tempTT;
					}
				}
				$stmt->close();
				return 'R' . $year . $newTT;
		}
?>