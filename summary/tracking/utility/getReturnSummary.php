<?php
		session_start();
		if(!isset($_SESSION['ID'])){
			header("Location: ../login.php");
		}
		require_once('../../../database.php');
		
		if (empty($_GET['opid'])) return;

		$opid = $_GET['opid'];
		$myArray = array(
				'data' => array(array()),
				'error' => ''
		);

		//build condition
		$condition = '';

		$sql = 'SELECT op.return_quantity,op.order_product_id,op.return_yuan,op.backshop_price,o.order_id,o.order_rate,o.customer_id,op.order_shipping_cn_cost,op.backshop_quantity,rs.return_status,rs.remark
		FROM customer_order_product op JOIN customer_order o ON op.order_id=o.order_id
		LEFT JOIN customer_order_return_summary rs ON op.order_product_id=rs.order_product_id
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