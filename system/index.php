<!DOCTYPE html> 
<html>
<?php
		$formcode = "system";

    session_start();
    if (!isset($_SESSION['ID'])) {
        header("Location: login.php");
    }

    // include 'connect.php';
    // include 'permission.php';
    // if (!isViewPermitted($formcode)) {
	// 	echo "<script>alert('permission denied');</script>";
	// 	header('Location: ../index.php?error_code=view_not_permitted');
	// }
	
    //24/12/2016	Pratchaya Ch.	add checking permission
    include '../database.php';
	include '../utility/permission.php';
	const FORMID = 10;
	$_permission = array();
	$_permission = json_decode(getAccessPermission($con,$_SESSION['USERID']));
	$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
	if ($_adminFlg==0) {
			if (empty($_permission)) header ("Location: ../login.php");

			$_visible = 0;
			foreach ($_permission as $key => $value) {
					if ($value->formid==FORMID && $value->visible==1) $_visible=1;
			}

			if ($_visible==0) header ("Location: ../login.php");
	}

?>
<head>
	<title>Order2Easy System Management</title>
	<meta charset="utf-8">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="description" content="website description" />
	<meta name="keywords" content="website keywords, website keywords" />	
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>	
	<link rel="stylesheet" type="text/css" href="css/w3-green.css">
	<link rel="stylesheet" type="text/css" href="css/w3-red.css">
	<link rel="stylesheet" type="text/css" href="css/w3-indigo.css">
	<link rel="stylesheet" type="text/css" href="css/w3-orange.css">
	<link rel="stylesheet" type="text/css" href="css/w3-blueGray.css">
	<link rel="stylesheet" type="text/css" href="css/w3-teal.css">
	<link rel="stylesheet" type="text/css" href="css/w3-lime.css">
	<link rel="stylesheet" type="text/css" href="css/w3-purple.css">
	<link rel="stylesheet" type="text/css" href="css/w3-pink.css">
	<link rel="stylesheet" type="text/css" href="css/w3-brown.css">
	<link rel="stylesheet" type="text/css" href="css/cargo.css" />
</head>

<body class="main">
	<?php
		if ($_GET['error_code'] == "view_not_permitted") {
			$error_text = "หน้าที่คุณเรียกได้หายไป หรือคุณไม่มีสิทธ์ในการใช้งาน";
		}

		if (isset($error_text)) {
			if ($error_text!="") {
				echo '<br><center><div style="background-color:#f5b8b2;width:400px;border-radius:5px;margin:10px;padding:10px;"><u><i><label>'.$error_text.'</i></u></div><center>';
			}
		}
	?>
	<br>

	<?php
			//------------------------------------------------------------------------------------------------------------------------------------
			//manage
			echo '<h1 align="center" style="font-weight:bold;color:#444;"><i class="material-icons" style="font-size:32px;">build</i> System Management</h1>';
			echo '<br>';
			echo '<p align="center" ><a href="../index.php">← Back to Order2Easy Administration</a></p>';
			echo '<br><br>';

			echo '<div align="center">';
			const CUSTOMERFORMID = 12;
			const CUSTOMERGROUPFORMID = 13;
			const CUSTOMERRATEFORMID = 14;
			$tmpFlg = false;
			if ($_adminFlg==1) {
					echo '<a class="cctext" href="manage-customer/"><i class="circleGreen material-icons">build</i>ลูกค้า</a>';
					echo '<a class="cctext" href="manage-customer-class/"><i class="circleRed material-icons">build</i>กลุ่มลูกค้า</a>';
					echo '<a class="cctext" href="manage-customer-class-rate/"><i class="circleIndigo material-icons">build</i>เรทกลุ่มลูกค้า</a>';
					$tmpFlg = true;
			}
			else {
					foreach ($_permission as $key => $value) {
							$formid = $value->formid;
							$visible = $value->visible;
							if ($visible==0) continue;
							
							if ($formid==CUSTOMERFORMID) {
									echo '<a class="cctext" href="manage-customer/"><i class="circleGreen material-icons">build</i>ลูกค้า</a>';
									$tmpFlg = true;
							}
							else if ($formid==CUSTOMERGROUPFORMID) {
									echo '<a class="cctext" href="manage-customer-class/"><i class="circleRed material-icons">build</i>กลุ่มลูกค้า</a>';
									$tmpFlg = true;
							}
							else if ($formid==CUSTOMERRATEFORMID) {
									echo '<a class="cctext" href="manage-customer-class-rate/"><i class="circleIndigo material-icons">build</i>เรทกลุ่มลูกค้า</a>';
									$tmpFlg = true;
							}
					}
			}
			if ($tmpFlg) echo '<br /><br />';

			const PAGEFORMID = 15;
			const PRODUCTTYPEFORMID = 16;
			const RATEFORMID = 16;
			const TRANSPORTFORMID = 18;
			$tmpFlg = false;
			if ($_adminFlg==1) {
					echo '<a class="cctext" href="manage-page/"><i class="circleOrange material-icons">build</i>สินค้าแนะนำ</a>';
					echo '<a class="cctext" href="manage-product-type/"><i class="circleBlueGray material-icons">build</i>ประเภทสินค้า</a>';
					echo '<a class="cctext" href="manage-rate/"><i class="circleTeal material-icons">build</i>อัตราแลกเปลี่ยน</a>';
					echo '<a class="cctext" href="manage-transportation/"><i class="circleLime material-icons">build</i>การขนส่ง</a>';
					$tmpFlg = true;
			}
			else {
					foreach ($_permission as $key => $value) {
							$formid = $value->formid;
							$visible = $value->visible;
							if ($visible==0) continue;
							
							if ($formid==PAGEFORMID) {
									echo '<a class="cctext" href="manage-page/"><i class="circleOrange material-icons">build</i>สินค้าแนะนำ</a>';
									$tmpFlg = true;
							}
							else if ($formid==PRODUCTTYPEFORMID) {
									echo '<a class="cctext" href="manage-product-type/"><i class="circleBlueGray material-icons">build</i>ประเภทสินค้า</a>';
									$tmpFlg = true;
							}
							else if ($formid==RATEFORMID) {
									echo '<a class="cctext" href="manage-rate/"><i class="circleTeal material-icons">build</i>อัตราแลกเปลี่ยน</a>';
									$tmpFlg = true;
							}
							else if ($formid==TRANSPORTFORMID) {
									echo '<a class="cctext" href="manage-transportation/"><i class="circleLime material-icons">build</i>การขนส่ง</a>';
									$tmpFlg = true;
							}
					}
			}
			if ($tmpFlg) echo '<br /><br />';

			const USERFORMID = 19;
			const REMARKFORMID = 20;
			const INFOFORMID = 21;
			$tmpFlg = false;
			if ($_adminFlg==1) {
					echo '<a class="cctext" href="manage-user/"><i class="circlePurple material-icons">build</i>จัดการผู้ดูแล</a>';
					echo '<a class="cctext" href="manage-order-remark/"><i class="circlePurple material-icons">build</i>หมายเหตุออร์เดอร์</a>';
					echo '<a class="cctext" href="manage-website/"><i class="circlePurple material-icons">build</i>ข้อมูลทั่วไป</a>';
					$tmpFlg = true;
			}
			else {
					foreach ($_permission as $key => $value) {
							$formid = $value->formid;
							$visible = $value->visible;
							if ($visible==0) continue;
							
							if ($formid==USERFORMID) {
									echo '<a class="cctext" href="manage-user/"><i class="circlePurple material-icons">build</i>จัดการผู้ดูแล</a>';
									$tmpFlg = true;
							}
							else if ($formid==REMARKFORMID) {
									echo '<a class="cctext" href="manage-order-remark/"><i class="circlePurple material-icons">build</i>หมายเหตุออร์เดอร์</a>';
									$tmpFlg = true;
							}
							else if ($formid==INFOFORMID) {
									echo '<a class="cctext" href="manage-website/"><i class="circlePurple material-icons">build</i>ข้อมูลทั่วไป</a>';
									$tmpFlg = true;
							}
					}
			}
			if ($tmpFlg) echo '<br />';

			const BANKFORMID = 31;
			$tmpFlg = false;
			if ($_adminFlg==1) {
					echo '<a class="cctext" href="manage-bank/"><i class="circleRed material-icons">build</i>จัดการข้อมูลบัญชี</a>';
					$tmpFlg = true;
			}
			else {
					foreach ($_permission as $key => $value) {
							$formid = $value->formid;
							$visible = $value->visible;
							if ($visible==0) continue;
							
							if ($formid==BANKFORMID) {
									echo '<a class="cctext" href="manage-bank/"><i class="circleRed material-icons">build</i>จัดการผู้ดูแล</a>';
									$tmpFlg = true;
							}
					}
			}
			if ($tmpFlg) echo '<br />';
			
			// <!--<a class="cctext" href="pending/pending.php"><i class="circle material-icons">archive</i>เตรียมส่งสินค้า</a>
		 	//        <a class="cctext" href="order_summary/order_summary.php"><i class="circle material-icons">assignment_turned_in</i>สรุปรายการสั่งสินค้า</a>
		 	//        <a class="cctext" href="portage_summary/portage_summary.php"><i class="circle material-icons">local_shipping</i>สรุปค่าขนส่ง</a>
			// 	-->
			echo '</div>';
	?>

	<br><br>
	<hr />
	<br><br>

	<?php
			//------------------------------------------------------------------------------------------------------------------------------------
			//report
			echo '<h1 align="center" style="font-weight:bold;color:#444;"><i class="material-icons" style="font-size:32px;">build</i> Report</h1>';
			echo '<br><br>';

			echo '<div align="center">';
			const RANKFORMID = 22;
			const INCOMEFORMID = 23;
			const PACKAGEFORMID = 24;
			$tmpFlg = false;
			if ($_adminFlg==1) {
					echo '<a class="cctext" href="report-customer-rank/"><i class="circleGreen material-icons">playlist_add_check</i>Report Customer Rank</a>';
					echo '<a class="cctext" href="report-income/"><i class="circleRed material-icons">playlist_add_check</i>Report Income</a>';
					echo '<a class="cctext" href="report-package/"><i class="circleIndigo material-icons">playlist_add_check</i>Report Package</a>';
					$tmpFlg = true;
			}
			else {
					foreach ($_permission as $key => $value) {
							$formid = $value->formid;
							$visible = $value->visible;
							if ($visible==0) continue;
							
							if ($formid==RANKFORMID) {
									echo '<a class="cctext" href="report-customer-rank/"><i class="circleGreen material-icons">playlist_add_check</i>Report Customer Rank</a>';
									$tmpFlg = true;
							}
							else if ($formid==INCOMEFORMID) {
									echo '<a class="cctext" href="report-income/"><i class="circleRed material-icons">playlist_add_check</i>Report Income</a>';
									$tmpFlg = true;
							}
							else if ($formid==PACKAGEFORMID) {
									echo '<a class="cctext" href="report-package/"><i class="circleIndigo material-icons">playlist_add_check</i>Report Package</a>';
									$tmpFlg = true;
							}
					}
			}
			if ($tmpFlg) echo '<br /><br />';

			const PAYMENTFORMID = 25;
			const REPRATEFORMID = 26;
			const TRAFFICFORMID = 27;
			$tmpFlg = false;
			if ($_adminFlg==1) {
					echo '<a class="cctext" href="report-payment/"><i class="circleOrange material-icons">playlist_add_check</i>Report Payment</a>';
					echo '<a class="cctext" href="report-rate/"><i class="circleBlueGray material-icons">playlist_add_check</i>Report Rate</a>';
					echo '<a class="cctext" href="report-traffic/"><i class="circleLime material-icons">playlist_add_check</i>Report Traffic</a>';
					$tmpFlg = true;
			}
			else {
					foreach ($_permission as $key => $value) {
							$formid = $value->formid;
							$visible = $value->visible;
							if ($visible==0) continue;
							
							if ($formid==PAYMENTFORMID) {
									echo '<a class="cctext" href="report-payment/"><i class="circleOrange material-icons">playlist_add_check</i>Report Payment</a>';
									$tmpFlg = true;
							}
							else if ($formid==REPRATEFORMID) {
									echo '<a class="cctext" href="report-rate/"><i class="circleBlueGray material-icons">playlist_add_check</i>Report Rate</a>';
									$tmpFlg = true;
							}
							else if ($formid==TRAFFICFORMID) {
									echo '<a class="cctext" href="report-traffic/"><i class="circleLime material-icons">playlist_add_check</i>Report Traffic</a>';
									$tmpFlg = true;
							}
					}
			}
			if ($tmpFlg) echo '<br />';
			echo '</div>';
	?>

	<br><br><br>
	<div align="center">
		<i class="material-icons">exit_to_app</i><br>
		<?php echo $_SESSION['ID'] ?>
		<a href="../logout.php">log out</a>
	</div>
	<br><br><br>
</body>
</html>
