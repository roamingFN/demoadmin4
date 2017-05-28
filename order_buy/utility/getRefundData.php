<?php
		session_start();
		if(!isset($_SESSION['ID'])){
			header("Location: ../login.php");
		}
		require_once('../../database.php');
		
		if (empty($_GET['opid'])) return;

		$opid = $_GET['opid'];
		$myArray = array(
				'data' => array(array()),
				'error' => ''
		);

		//build condition
		$condition = '';

		$sql = 'SELECT ore.remark_tha, ot.return_status 
		FROM order_remark ore JOIN customer_order_product op on op.remark_id=ore.remark_id
		LEFT JOIN customer_order_return ot on ot.order_product_id=op.order_product_id
		WHERE op.order_product_id='.$opid;
		
		$result = $con->query($sql); 
		if (!$result) {
				//throw new Exception("Database Error ".$con->error);
				$myArray['error'] = $con->error;
		}
		else {
				$row = $result->num_rows;
				while ($row = $result->fetch_assoc()) { 
						$myArray['data'][] = $row; 
				}
		}
		//sleep(5);
		//echo json_encode(array('sql' => $sql));
		$con->close();
		echo json_encode($myArray);
?>