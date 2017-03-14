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
				<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.css">
				<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
				<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
				<!-- SCRIPT -->
				<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
				<script src="controller.js"></script>


				<?php
			            session_start();
			            if (!isset($_SESSION['ID'])){
			                header("Location: ../login.php");
			            }
			            
						include './utility/function.php';
						include 'initIndex.php';
						include './dialog/searchBox.php';
						include '../database.php';

						include '../utility/permission.php';

						const FORMID = 9;
						$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
						$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
						if ($_adminFlg==0) {
								if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
						}

				?>

				<!-- init Script-->
				<script>initIndex();</script>
				<script>setDatePicker();</script>
		</head>
		
		<body>
				<div>
						<div>
								<h1><b><a class="green" href="index.php">สรุป Order</a></b></h1>
						      	<h3><a class="green" href="../index.php">&larr; Back</a></h3><br>
				      	</div>	
						<div class="icon green">
								<i class="material-icons" onclick="exportExcel();" title="Export">&#xE24D;</i>
								<i class="material-icons" onclick="showSearchBox();" title="Search">&#xE880;</i>
						</div>
				</div>

				<div>
						<?php
								//init===========================================================
								$sumTotalReturn = 0;
								$puncCount = 0;
								
								//paging
								$pageSize = 20;
								$allRows = getNumberOfRows($con,$sqlCount,$condition,'');
								$allPage = ceil($allRows/$pageSize);
								if(isset($_GET['page'])) {
										$nowPage = $_GET['page']-1;
								} else{
										$nowPage = 0;
								}
								$paging = ' LIMIT '.$nowPage*$pageSize.','.$pageSize;
								
								//table header===================================================
								echo '<table class="result green">';
					            echo '<thead>
					            			<tr>
													<th>เลขที่ Order</th>
													<th>ชื่อลูกค้า</th>
													<th>ID ลูกค้า</th>
													<th>จำนวน Tracking</th>
													<th>จำนวน Tracking ที่ปิดกล่องแล้ว</th>
													<th>จำนวนที่ลูกค้าสั่ง (ชิ้น)</th>
													<th>จำนวนที่ร้านค้า Confirm (ชิ้น)</th>
													<th>จำนวนที่ถึงโกดังไทย (ชิ้น)</th>
													<th>จำนวนที่ขาด (ชิ้น)</th>
													<th>ยอดที่ลูกค้าโอน (บาท)</th>
													<th>ยอดที่ร้านค้า Confirm (บาท)</th>
													<th>ยอดค่าสินค้าที่ถึงโกดังไทย (บาท)</th>
													<th>ยอดคืนเงินครั้งที่ 1</th>
													<th>ยอดค่าสินค้าที่ขาด (บาท)</th>
													<th>ยอดคืนเงินแล้ว</th>
													<th>Action</th>
											</tr>
					                </thead>';
							
								//table detail====================================================
								echo '<tbody class="none">';
								$dataSet = getData($con,$sql,$condition,$orderBy,'',$paging);
								foreach ($dataSet as $key => $value) {
										$oid = $value['order_id'];
										echo '<tr class="'.($puncCount%2==0? 'punc':'').'">';
										echo '<td class="fixed">'.$value['order_number'].'</td>';
										echo '<td>'.$value['customer_firstname'].' '.$value['customer_lastname'].'</td>';
										echo '<td class="center">'.$value['customer_code'].'</td>';
										echo '<td class="number">'.number_format($value['total_tracking']).'</td>';
										echo '<td class="number">'.$value['total_tracking_con'].'</td>';
										echo '<td class="number">'.$value['product_quantity'].'</td>';
										echo '<td class="number">'.$value['product_available'].'</td>';
										echo '<td class="number">'.$value['received_amount'].'</td>';
										echo '<td class="number">'.($value['missing']).'</td>';
										echo '<td class="number">'.number_format($value['order_price'],2).'</td>';
										echo '<td class="number">'.number_format($value['order_price_back'],2).'</td>';
										echo '<td class="number">'.number_format($value['received_price'],2).'</td>';
										echo '<td class="number">'.number_format($value['return1'],2).'</td>';
										echo '<td class="number">'.number_format($value['return1Missing'],2).'</td>';
										echo '<td class="number">'.number_format($value['returnComplete'],2).'</td>';
										//echo '<td>'.$value['remark'].'</td>';
										echo '<td><a class="green" href="./detail.php?oid='.$value['order_id'].'">Detail</a></td>';
										echo '</tr>';
										$sumTotalReturn += $value['return1'];
										$puncCount++;
								}
								echo '</tbody>';
								echo '</table>';
						?>
				</div>

				<div class="paging">
						<?php
								echo 'หน้า ';
								for($i=1;$i<=$allPage;$i++) {
										if (($nowPage+1)!=$i) echo '<a class="green" href="?page='.$i.$request.'"><ins>'.$i.'</ins></a>';
										else echo '<a class="green" href="?page='.$i.$request.'">'.$i.'</a>';
								}
						?>
				</div>

				<div class="summary">
						<table>
		                        <tr>
		                            <td><b>จำนวนรายการทั้งหมด</b></td>
		                            <td class="number"><?php echo number_format($allRows); ?></td>
		                            <td>รายการ<br></td>
		                        </tr>
		                        <tr>
		                            <td><b>จำนวนยอดคืนเงินทั้งหมด</b></td>
		                            <td class="number"><?php echo number_format($sumTotalReturn,2); ?></td>
		                            <td>บาท<br></td>
		                        </tr>
                        </table>
				</div>
		</body>
</html>

<?php
		$con->close();
?>