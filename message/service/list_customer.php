<?php
	// session_start();
	/* if(!isset($_SESSION['ID'])){
		header("Location: ../../login.php");
	} */
				
	include '../../database.php';
	$json = file_get_contents('php://input');
	$data = json_decode($json, true);
	
	if($stmt = $con->prepare('SELECT c.customer_id, c.customer_firstname, c.customer_lastname '
							.'FROM customer c INNER JOIN customer_order o ON c.customer_id = o.customer_id '
							.'INNER JOIN customer_order_product_tracking t ON o.order_id = t.order_id '
							.'WHERE t.statusid = 2 '
							.'AND (c.customer_firstname LIKE \''.$data['search'].'%\' OR c.customer_lastname LIKE \''.$data['search'].'%\') '
							.'ORDER BY c.customer_firstname, c.customer_lastname')) {
		$stmt->execute();
		$stmt->store_result();
		$count = $stmt->num_rows;
		
		$stmt->bind_result($customer_id, $customer_firstname, $customer_lastname);
		
		while($stmt->fetch()){
			$result .= ($result == '') ? '' : ',';
			$result .= '{"id":"'+$customer_id+'", "name":"'.$customer_firstname.' '.$customer_lastname.'"}';
		}
		$stmt->close();
	}
	echo '['.$result.']';
	$con->close();
?>