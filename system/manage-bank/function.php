<?php

function isDupProdTypeName($producttypename) {
	include '../../database.php';
	$result = 0;

	$sql = 'SELECT producttypeid FROM product_type'.
				' WHERE producttypename =\''.$producttypename.'\'';
	$stmt = $con->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($producttypeid);
	while($stmt->fetch()) {
			$result = ++$result;
	}
	$con->close();
	return $result;
}