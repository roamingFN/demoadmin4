<!DOCTYPE html>
<html>
		<head>
				<title>Tracking</title>
				<meta charset="utf-8">
				<!-- CSS -->
				<link rel="stylesheet" type="text/css" href="../css/materialIcons.css">
				<link rel='stylesheet' type='text/css' href="../css/OpenSans.css">
				<link rel="stylesheet" type="text/css" href="../css/orderAdmin.css">
				<link rel="stylesheet" type="text/css" href="../css/dialog.css">
				<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.css">
				<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
				<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.min.css" />
				
				<!-- SCRIPT -->
				<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
				<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js"></script>
				<script src="controller.js"></script>
			
				<?php
			            session_start();
			            if (!isset($_SESSION['ID'])){
			                header("Location: ../login.php");
			            }
			            
						include './utility/function.php';
						include '../database.php';
						include 'initIndex.php';
						include '../utility/permission.php';

						const FORMID = 7;
						$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
						$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
						if ($_adminFlg==0) {
								if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
						}
				?>
		</head>
		
		<body>
				<div>
						<div>
								<h1><b><a class="orange" href="index.php">Tracking</a></b></h1>
						      	<h3><a class="orange" href="../index.php">&larr; Back</a></h3><br>
				      	</div>	
						<div class="icon orange">
								<i class="material-icons" onclick="exportExcel();" title="Export">&#xE24D;</i>
								<i class="material-icons" onclick="showSearchBox();" title="Search">&#xE880;</i>
								<i class="material-icons" onclick="showSearchByPic();" title="Search">&#xE880;</i>
						</div>
				</div>

				<div>
						<?php
								//init===========================================================
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
								echo '<table class="result orange">';
					            echo '<thead>
					            			<tr>
													<th>Tracking No.</th>
													<th>เลขที่ Order</th>
													<th>วันที่ Order</th>
													<th>วันที่ถึงไทย</th>
													<th>ชื่อลูกค้า</th>
													<th>รหัสลูกค้า</th>
													<th>คิว</th>
													<th>KG</th>
													<th>Type</th>
													<th>Rate</th>
													<th>ค่าขนส่งจีนไทย</th>
													<th>สถานะ Tracking</th>
													<th>สถานะกล่อง</th>
													<th>ค่าเฉลี่ย</th>
													<th>ผู้ตรวจ</th>
													<th>หมายเหตุ</th>
													<th>Action</th>
											</tr>
					                </thead>';
							
								//table detail====================================================
								echo '<tbody class="none">';
								$dataSet = getData($con,$sql,$condition,$orderBy,'',$paging);
								foreach ($dataSet as $key => $value) {
										//init--------------------
										$oid = $value['order_id'];
										$m3 = $value['m3'];
										// 12/04/2017 if m3 < 0.0000, set it to 0.0001
						                if ($m3<0.0001) {
											$m3 = 0.0001;
										}
										$kg = $value['weight'];
										$ptid = $value['order_product_tracking_id'];
										//calcurate---------------
										if ($value['type']==2) {		//m3
												$typeDesc = 'คิว';
												$rate = $value['ratem3'];
												$tran = $m3*$rate;
										}
										else if ($value['type']==1) {		//kg
												$typeDesc = 'Kg';
												$rate = $value['rateweight'];
												$tran = $kg*$rate;
										}
										
										if ($value['tstatusid']==0) {
												$statDesc = 'incomplete';
										}
										else if ($value['tstatusid']==1) {
												$statDesc = 'complete';
										}
										if ($m3==0) {
												$avg=0;
										}
										else {
												$avg = $tran/$m3;
										}
										if($value['date_order_created']=='' || $value['date_order_created']=='0000-00-00 00:00:00') $odt = '';
										else $odt = date_format(date_create($value['date_order_created']),"d/m/Y H:i:s");
										if($value['last_edit_date']=='' || $value['last_edit_date']=='0000-00-00 00:00:00') $dt = '';
										else $dt = date_format(date_create($value['last_edit_date']),"d/m/Y H:i:s");
										
										//show data----------------
										echo '<tr onclick="toDetail(\''.$value['tracking_no'].'\','.$value['order_id'].')" class="'.($puncCount%2==0? 'punc':'').'">';
										echo '<td class="fixed">'.$value['tracking_no'].'</td>';
										echo '<td class="fixed">'.$value['order_number'].'</td>';
										echo '<td>'.$odt.'</td>';
										echo '<td>'.$dt.'</td>';
										echo '<td>'.$value['customer_firstname'].' '.$value['customer_lastname'].'</td>';
										echo '<td class="center">'.$value['customer_code'].'</td>';
										echo '<td class="number">'.number_format($m3,4).'</td>';
										echo '<td class="number">'.number_format($kg,2).'</td>';
										echo '<td class="center">'.$typeDesc.'</td>';
										echo '<td class="number">'.number_format($rate,2).'</td>';
										echo '<td class="number">'.number_format($value['total'],2).'</td>';
										echo '<td class="center">'.$statDesc.'</td>';
										echo '<td class="number">'.((!empty($value['pstatusid']))?$_pStatDesc[$value['pstatusid']]:"").'</td>';
										echo '<td class="number">'.number_format($avg,2).'</td>';
										echo '<td class="number">'.$value['uid'].'</td>';
										echo '<td>'.$value['remark'].'</td>';
										echo '<td><a class="orange" href="./detail.php?ptno='.$value['tracking_no'].'&oid='.$oid.'">Detail</a></td>';
										echo '</tr>';
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
										if (($nowPage+1)!=$i) echo '<a class="orange" href="?page='.$i.$request.'"><ins>'.$i.'</ins></a>';
										else echo '<a class="orange" href="?page='.$i.$request.'">'.$i.'</a>';
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
                        </table>
				</div>

				<?php
						include './dialog/searchBox.php';
						include './dialog/searchByPicBox.php';
						include './dialog/loading.php';
				?>
		</body>
</html>

<?php  
		$con->close();
?>

		<!-- init Script-->
		<script>initIndex();</script>
		<script>setDatePicker();</script>