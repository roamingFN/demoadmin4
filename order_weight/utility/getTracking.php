<?php
		session_start();
		if(!isset($_SESSION['ID'])){
			header("Location: ../login.php");
		}
		require_once('../../database.php');
		
		if (empty($_GET['ono']) && empty($_GET['pType'])) return;

		$ono = $_GET['ono'];
		$pType = $_GET['pType'];
		$myArray = array(
				'data' => array(array()),
				'error' => ''
		);

		//build condition
		$condition = '';
		if (!empty($_GET['ono']) && empty($_GET['pType'])) $condition = ' AND o.order_number LIKE \'%'.$ono.'%\'';
		else if (empty($_GET['ono']) && !empty($_GET['pType'])) $condition = ' AND op.producttypeid='.$pType;
		else $condition = ' AND o.order_number LIKE \'%'.$ono.'%\' AND op.producttypeid='.$pType;

		$sql = 'SELECT pt.tracking_no,op.order_product_id,p.product_id,p.product_img FROM customer_order_product_tracking pt 
		 	JOIN customer_order_product op ON pt.order_product_id=op.order_product_id
		 	JOIN customer_order o ON o.order_id=pt.order_id
		 	JOIN product p ON p.product_id=op.product_id
		 	WHERE op.current_status>=6'. $condition;
		
		$result = $con->query($sql); 
		if (!$result) {
				//throw new Exception("Database Error ".$con->error);
				$myArray['error'] = $con->error;
		}
		else {
				$row = $result->num_rows;
				while ($row = $result->fetch_assoc()) { 
						$myArray['data'][$row['tracking_no']][] = $row; 
				}
		}
		//sleep(5);
		//echo json_encode(array('sql' => $sql));
		echo json_encode($myArray);
?>