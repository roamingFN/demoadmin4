<?php
	// session_start();
	include '../../database.php';

	if($stmt = $con->prepare('SELECT transport_id, transport_th_name '
							.'FROM website_transport '
							.'ORDER BY transport_id')) {
		$stmt->execute();
		$stmt->store_result();
		$count = $stmt->num_rows;
		
		$stmt->bind_result($transport_id,$transport_th_name);
		
		while($stmt->fetch()){
			$result .= ($result == '') ? '' : ',';
			$result .= '{"id":"'.$transport_id.'","name":"'.$transport_th_name.'"}';	
		}
		$stmt->close();
	}
	echo '['.$result.']';
	$con->close();
?>