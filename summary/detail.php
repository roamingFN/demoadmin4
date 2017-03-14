<!DOCTYPE html>
<html>
		<head>
				<title>สรุป Order</title>
				<meta charset="utf-8">
				<!-- CSS -->
				<link rel="stylesheet" type="text/css" href="../css/materialIcons.css">
				<link rel='stylesheet' type='text/css' href="../css/OpenSans.css">
				<link rel="stylesheet" type="text/css" href="../css/orderAdmin.css">
				<link rel="stylesheet" type="text/css" href="../css/dialog.css">

				<!-- SCRIPT -->
				<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
				<script src="./detailController.js"></script>

				<?php
			            session_start();
			            if (!isset($_SESSION['ID'])){
			                header("Location: ../login.php");
			            }
			            
						//include './dialog/refundBox.php';
						//include './dialog/backRefundBox.php';
						//include './dialog/emailBox.php';
						//include './dialog/emailLogBox.php';
						//include './dialog/packageBox.php';
				?>

				<style>
						ul {
						    list-style-type: none;
						    margin: 0;
						    padding: 0;
						    overflow: hidden;
						    background-color: #333;
						    width: 30%;
						}

						li {
						    float: left;
						    width: 33.33%;
						}

						li a {
						    display: block;
						    color: white;
						    text-align: center;
						    padding: 14px 16px;
						    text-decoration: none;
						}

						/* Change the link color to #111 (black) on hover */
						li a:hover {
						    background-color: #4CAF50;
						    cursor: pointer;
						}
				</style>
		</head>

		<?php
		echo '<body>';
				echo '<div>';
						echo '<div>';
								echo '<h1><b><a class="green" href="" onclick="location.reload();">สรุป Order</a></b></h1>';
						      	echo '<h3><a class="green" href="index.php">&larr; Back</a>  <a class="green" href="../index.php">&larr; Home</a></h3><br>';
				      	echo '</div>';
				echo '</div>';

				echo '<ul>';
						echo '<li id="menu1"><a onclick="loadPage(1)">สรุป</a></li>';
						echo '<li id="menu2"><a onclick="loadPage(2)">ยอดจ่าย</a></li>';
						echo '<li id="menu3"><a onclick="loadPage(3)">Message</a></li>';
				echo '</ul>';

				echo '<div id="content">';
				echo '</div>';
				
				echo '<script>initDetail();</script>';
		echo '</body>';
		?>
</html>