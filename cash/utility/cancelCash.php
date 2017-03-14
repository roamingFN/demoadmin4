<html>
		<head>
				<meta charset="utf-8">
		</head>

<?php
		include '../../database.php';
		session_start();
		if(!isset($_SESSION['ID'])){
			header("Location: ../login.php");
		}
		if (empty($_POST)) return;
		if ($stmt = $con->prepare ( 'UPDATE cash SET remarkc="' . $_POST ['c-remarkc'] . '",status=2,cancel_date=now(),cancel_by=\''.$_SESSION['ID'].'\' WHERE cashid="' . $_POST ['c-cid'] . '"' )) {
				$res = $stmt->execute ();
				if (! $res) {
					echo '<script>alert("ยกเลิกรายการล้มเหลว\n'.$stmt->error.'");';
					echo 'window.location.href="../cash.php";</script>';
				} else {
					echo '<script>alert("ยกเลิกรายการสำเร็จ");';
					echo 'window.location.href="../cash.php";</script>';
				}
		}
?>

</html>