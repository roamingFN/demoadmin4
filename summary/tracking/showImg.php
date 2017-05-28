<html>
<head>
	<meta charset="utf-8">
</head>
<?php
	session_start();
	if (!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../../database.php';

	if(isset($_GET['pid'])) {
		if($stmt = $con->prepare('SELECT product_img,product_color_china,product_size_china FROM product WHERE product_id='.$_GET['pid'])){
					$stmt->execute();
					$stmt->bind_result($img,$color,$size);
					while($stmt->fetch()) {
						$image = $img;
					}
		}
		echo '<br><center><img src="'.$image.'"/></center>';
		echo '<center><p>ขนาด : '.$size.'</p></center>';
		echo '<center><p>สี : '.$color.'</p></center>';
	}
?>
</html>