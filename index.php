<!DOCTYPE html> 
<html>
<?php
    session_start();
    if (!isset($_SESSION['ID'])) {
        	header("Location: login.php");
    }
?>
<head>
	<title>Order2Easy Administration</title>
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

	<?php
			include './database.php';
			include './utility/permission.php';
			
			$_permission = array();
			$_permission = json_decode(getAccessPermission($con,$_SESSION['USERID']));
			$_adminFlg = getAdminFlag($con,$_SESSION['ID']); 
	?>
</head>

<body class="main">
	<br>
	<div class="header">
			<h1 align="center" style="font-weight:bold;"><i class="material-icons" style="font-size:32px;">build</i> Order2Easy Administration</h1>
	</div>
	<!-- <h1 align="center" style="font-weight:bold;color:#444;">
		<i class="material-icons" style="font-size:32px;">build</i> Order2Easy Administration
	</h1> -->
	<div align="center" style="width:100%;margin-top: 8%;">
		<div style="max-height:200px;width:100%;text-align: center;display: flex;justify-content: center;">
			<?php
					$tmpFlg = false;
					if ($_adminFlg==1) {
							echo '<a class="cctext" href="cash/cash.php"><i class="circleGreen material-icons">&#xE227;</i>Cash</a>';
							echo '<a class="cctext" href="topup/topup.php"><i class="circleRed material-icons">&#xE2C6;</i>Top up</a>';
							echo '<a class="cctext" href="payment/payment.php"><i class="circleIndigo material-icons">&#xE8A1;</i>Payment</a>';
							$tmpFlg = true;
					}
					else {
							foreach ($_permission as $key => $value) {
									$formid = $value->formid;
									$visible = $value->visible;
									if ($visible==0) continue;
									
									if ($formid==1) {
											echo '<a class="cctext" href="cash/cash.php">
												<i class="circleGreen material-icons">&#xE227;</i>Cash</a>';
											$tmpFlg = true;
									}
									else if ($formid==2) {
											echo '<a class="cctext" href="topup/topup.php">
												<i class="circleRed material-icons">&#xE2C6;</i>Top up</a>';
											$tmpFlg = true;
									}
									else if ($formid==3) {
											echo '<a class="cctext" href="payment/payment.php">
												<i class="circleIndigo material-icons">&#xE8A1;</i>Payment</a>';
											$tmpFlg = true;
									}
							}
					}
					if ($tmpFlg) echo '<br /><br />';
			?>
		</div>

		<div style="width:100%;text-align: center;display: flex;justify-content: center;">
			<?php
					$tmpFlg = false;
					if ($_adminFlg==1) {
							echo '<a class="cctext" href="order_confirm"><i class="circleOrange material-icons">&#xE065;</i>รอตรวจสอบ</a>';
							echo '<a class="cctext" href="order_buy"><i class="circleBlueGray material-icons">&#xE8CC;</i>ดำเนินการสั่งซื้อ</a>';
							echo '<a class="cctext" href="order_weight"><i class="circleTeal material-icons">&#xE861;</i>ใส่น้ำหนัก</a>';
							echo '<a class="cctext" href="tracking"><i class="circle orange material-icons">&#xE905;</i>Tracking</a>';
							$tmpFlg = true;
					}
					else {
							foreach ($_permission as $key => $value) {
									$formid = $value->formid;
									$visible = $value->visible;
									if ($visible==0) continue;

									if ($formid==4) {
										echo '<a class="cctext" href="order_confirm">
											<i class="circleOrange material-icons">&#xE065;</i>รอตรวจสอบ
										</a>';
									}
									else if ($formid==5) {
										echo '<a class="cctext" href="order_buy">
											<i class="circleBlueGray material-icons">&#xE8CC;</i>ดำเนินการสั่งซื้อ
										</a>';
									}
									else if ($formid==6) {	
										echo '<a class="cctext" href="order_weight">
											<i class="circleTeal material-icons">&#xE861;</i>ใส่น้ำหนัก
										</a>';
									}
									else if ($formid==7) {
										echo '<a class="cctext" href="tracking">
											<i class="circle orange material-icons">&#xE905;</i>Tracking
										</a>';
									}
							}
					}
			?>
		</div>
		<br><br>
		
		<div style="width: 100%;text-align: center;display: flex;justify-content: center;">
			<?php
					$tmpFlg = false;
					if ($_adminFlg==1) {
							echo '<a class="cctext" href="package"><i class="circlePurple material-icons">&#xE146;</i>กล่อง</a>';
							echo '<a class="cctext" href="transport"><i class="circle material-icons">&#xE558;</i>จัดส่ง</a>';
							echo '<a class="cctext" href="summary"><i class="circle green material-icons">&#xE877;</i>สรุป Order</a>';
							echo '<a class="cctext" href="message"><i class="circle material-icons">&#xE0B7;</i>Message</a>';
					}
					else {
							foreach ($_permission as $key => $value) {
									$formid = $value->formid;
									$visible = $value->visible;
									if ($visible==0) continue;

									if ($formid==8) {
										echo '<div class="floatMenu">
												<a class="cctext" href="package">
													<i class="circlePurple material-icons">&#xE146;</i>กล่อง
												</a>
										</div>';
									}
									else if ($formid==28) {
										echo '<div class="floatMenu">
												<a class="cctext" href="transport">
													<i class="circle material-icons">&#xE558;</i>จัดส่ง
												</a>
										</div>';
									}
									else if ($formid==9) {
										echo '<div class="floatMenu">
												<a class="cctext" href="summary">
													<i class="circle green material-icons">&#xE877;</i>สรุป Order
												</a>
										</div>';
									}
									else if ($formid==30) {
										echo '<div class="floatMenu">
												<a class="cctext" href="message">
													<i class="circle material-icons">&#xE0B7;</i>Message
												</a>
										</div>';
									}
							}
					}
			?>
		</div>
		<br>
		<div style="width: 100%;text-align: center;display: flex;justify-content: center;">
			<?php
					$tmpFlg = false;
					if ($_adminFlg==1) {
							echo '<a class="cctext" href="withdraw/withdraw.php"><i class="circleBrown material-icons">&#xE263;</i>Withdraw</a>';
							echo '<a class="cctext" href="system"><i class="circlePink material-icons">&#xE869;</i>ข้อมูลพื้นฐาน</a>';
					}
					else {
							foreach ($_permission as $key => $value) {
									$formid = $value->formid;
									$visible = $value->visible;
									if ($visible==0) continue;

									if ($formid==29) {
										echo '<div class="floatMenu">
												<a class="cctext" href="withdraw/withdraw.php">
													<i class="circleBrown material-icons">&#xE263;</i>Withdraw
												</a>
										</div>';
									}
									else if ($formid==10) {
										echo '<div class="floatMenu">
												<a class="cctext" href="system">
													<i class="circlePink material-icons">&#xE869;</i>ข้อมูลพื้นฐาน
												</a>
										</div>';
									}
							}
					}
			?>
		</div>
		<br>
		<!--<a class="cctext" href="pending/pending.php"><i class="circle material-icons">archive</i>เตรียมส่งสินค้า</a>
        <a class="cctext" href="order_summary/order_summary.php"><i class="circle material-icons">assignment_turned_in</i>สรุปรายการสั่งสินค้า</a>
        <a class="cctext" href="portage_summary/portage_summary.php"><i class="circle material-icons">local_shipping</i>สรุปค่าขนส่ง</a>
		-->
	</div>
	<br><br><br>
	<div align="center">
		<i class="material-icons">exit_to_app</i><br>
		<?php echo $_SESSION['ID'] ?>
		<a href="logout.php">log out</a>
	</div>
	<br><br><br>
</body>
</html>
