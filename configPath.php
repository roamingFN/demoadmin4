<?php
		include 'database.php';

		$_path_frontend = 'demo2';
		$_path_backend  = 'demoadmin3';

		$_site_url = '';
		$sql = 'SELECT site_url FROM website_config';
		$stmt = $con->prepare($sql);
		$stmt->bind_result($_site_url);
		$stmt->execute();
		while ($stmt->fetch()) {
				$_site_url = $_site_url;
		}
		//echo $_site_url;
?>