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

				<!-- SCRIPT -->
				<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>

				<?php
			            session_start();
			            if (!isset($_SESSION['ID'])){
			                header("Location: ../login.php");
			            }
			            
						include './utility/function.php';
						include '../database.php';
						include './initDetail.php';
						include './dialog/amountDialog.php';
						include './dialog/loading.php';
						include '../utility/permission.php';

						const FORMID = 7;
						$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
						$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
						if ($_adminFlg==0) {
								if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
						}
				?>
		</head>

<?php
		//init-----------------------------------------------------------------------------
		$dataSet = getData($con,$sql,$condition,$groupBy,$orderBy,'');
		foreach ($dataSet as $key => $value) {
				if ($value['masterflg']==1) {
						$ptypeid = $value['producttypeid']; 
				}
		}
		
	 	//content--------------------------------------------------------------------------
		echo '<body>';
				echo '<div>';
						echo '<div>';
								echo '<h1><b><a class="orange" href="" onclick="location.reload();">Tracking</a></b></h1>';
						      	echo '<h3><a class="orange" href="index.php">&larr; Back</a>  <a href="../index.php">&larr; Home</a></h3><br>';
				      	echo '</div>';
				echo '</div>';

				echo '<div>';
						if($dataSet[0]['adddate']=='' || $dataSet[0]['adddate']=='0000-00-00 00:00:00') $dt = '';
						else $dt = date_format(date_create($dataSet[0]['adddate']),"d/m/Y H:i:s");
						echo '<table class="preresult orange none">
									<tr><td>ชื่อลูกค้า  :</td><td>'.$dataSet[0]['customer_firstname'].' '.$dataSet[0]['customer_lastname'].' ('.$dataSet[0]['customer_code'].')</td></tr>
				        			<tr><td>เลขที่ออเดอร์  :</td><td>'.$dataSet[0]['order_number'].'</td></tr>
									<tr><td>เลขที่กล่อง  :</td><td>'.$dataSet[0]['packageno'].'</td></tr>
									<tr><td>สถานะ  :</td><td>'.((!empty($dataSet[0]['pstatusid']))?$_pStatDesc[$dataSet[0]['pstatusid']]:"").'</td></tr>
							</table>';
				echo '</div>';

				echo '<div>';
						echo '<table class="result orange" id="quan-table">';
						echo '<thead>
			            			<tr>
											<th>Tracking no.</th>
											<th>รูปตัวอย่าง</th>
											<th>จำนวนที่สั่ง</th>
											<th>จำนวนที่รับแล้ว</th>
											<th>รับเพิ่ม</th>
											<th>ขาดอีก</th>
											<th>Last update by</th>
											<th>Last edit date</th>
									</tr>
			                </thead>';
			          	echo '<tbody>';

			          	$totalMissing = 0;
			          	$totalQuan = 0;
			          	$totalRec = 0;
			          	$totalSum = 0;
			          	foreach ($dataSet as $key => $value) {
			          			$disabled = '';
			          			if ($value['pstat']==1 || $value['packageid']!=0) {
			          					$disabled = ' disabled';
			          					if ($_adminFlg==1) $disabled = ' ';
			          			}
			          			
			          			$ptid = $value['order_product_tracking_id'];
			          			if($value['last_edit_date']=='0000-00-00 00:00:00') $dt = '';
								else $dt = date_format(date_create($value['last_edit_date']),"d/m/Y H:i:s");
								echo '<tr class="none" id="'.$ptid.'">
										<td class="center">'.$value['tracking_no'].'</td>
										<td class="center"><img src="'.$value['product_img'].'"></td>
										<td class="number" id="quan-'.$ptid.'">'.number_format($value['backshop_quantity']).'</td>
										<td class="center" id="received-'.$ptid.'"><a style="color: #00766a;" onclick="showAmountDialog('.$value['order_product_tracking_id'].')">'.number_format($value['received_amount']).'</a></td>
										<td class="number"><input id="get-'.$ptid.'" class="input filter" value=0'.$disabled.'></td>
										<td class="number" id="missing-'.$ptid.'">'.number_format($value['backshop_quantity']-$value['received_amount']).'</td>
										<td class="center">'.$value['uid'].'</td>
										<td class="center">'.$dt.'</td>
									</tr>';
								echo '<input id="opid-'.$ptid.'" type="hidden" value="'.$value['order_product_id'].'">';
								$totalMissing += $value['quantity']-$value['received_amount'];
								$totalQuan += $value['quantity'];
								$totalRec += $value['received_amount'];
								$totalSum += $value['received_amount'];
			          	}
		                echo '</tbody>';
						// echo '<tfoot>
						// 		<td></td>
						// 		<td>รวม</td>
						// 		<td class="number">'.number_format($totalMissing).'</td>
						// 		<td class="number">'.number_format($totalQuan).'</td>
						// 		<td class="number">'.number_format($totalRec).'</td>
						// 		<td></td>
						// 		<td class="number">'.number_format($totalSum).'</td>
						// 		<td></td>
						// 	</tfoot>';
						echo '</table>';
				echo '</div>';

				//=======================================================================================
				echo '<br><div>';
				$saveFlg = 1;
				foreach ($dataSet as $key => $value) {
						if ($value['masterflg']==0) continue;
						$rate = array();
						$ptypeInfo = array();
						$ptypeInfo = getpTypeInfo($con,$ptypeid);
						
					 	$_defaultRateM3 = 0;
					 	$_defaultRateWeight = 0;
					 	$_userClass = $dataSet[0]['class_id'];
					 	$defaultRate = 0;

					 	if ($value['type']==1) {						//kg
					 			$defaultRate = $value['rateweight'];
					 	}
					 	else {											//m3
					 			$defaultRate = $value['ratem3'];	
					 	}

					 	//find rate
					 	$rate = getRate($con,$_userClass,$ptypeInfo[0]['rate_type'],$ptypeInfo[0]['product_type']);
					 	if (isset($ptypeInfo[0]['rate_type']) && isset($ptypeInfo[0]['product_type']) && ($value['rateweight']==0||$value['ratem3']==0) && $defaultRate==0) {
			          			foreach ($rate as $rid => $rateInfo) { 
			          					if ($value['type']==$rateInfo['rate_type'] && $ptypeInfo[0]['product_type']==$rateInfo['product_type']) {
			          							if ($value['type']==1 && $value['weight']>=$rateInfo['begincal'] && $value['weight']<=$rateInfo['endcal']) {
			          									$defaultRate = $rateInfo['rate_amount'];
			          									$_defaultRateWeight = $rateInfo['rate_amount'];
			          							}
			          							else if ($value['type']==2 && $value['m3']>=$rateInfo['begincal'] && $value['m3']<=$rateInfo['endcal']) { 
			          									$defaultRate = $rateInfo['rate_amount'];
			          									$_defaultRateM3 = $rateInfo['rate_amount'];
			          							}
			          					}
			          			}
	          			}
	          			echo '<input type="hidden" id="rateWeight" value='.$value['rateweight'].'>';
	          			echo '<input type="hidden" id="rateM3" value='.$value['ratem3'].'>';
						echo '<table class="result orange" id="m3-table">';
						echo '<thead>
			            			<tr>
											<th style="width:5%">กว้าง</th>
											<th style="width:5%">ยาว</th>
											<th style="width:5%">สูง</th>
											<th style="width:8%">คิว</th>
											<th style="width:8%">น้ำหนัก</th>
											<th style="width:8%">ชนิดสินค้า</th>
											<th>Type</th>
											<th>เรท</th>
											<th style="width:8%">ยอดค่าขนส่ง</th>
											<th style="width:8%">ค่าเฉลี่ย</th>
											<th style="width:100px;">สถานะ</th>
											<th>User Add</th>
											<th>Add Date</th>
									</tr>
			                </thead>';
			                //init 
			                $checkedM3 = '';
			                $checkedKg = '';
			                $checkedIncom = '';
			                $checkedCom = '';

			                //calcurate---------------
			                $m3 = ($value['width']*$value['length']*$value['height'])/1000000;
			                // 12/04/2017 if m3 < 0.0000, set it to 0.0001
			                if ($m3<0.0001) {
								$m3 = 0.0001;
							}
			                $kg = $value['weight'];
			                
							if ($value['type']==2) {		//m3
									$checkedM3 = 'checked';
									$tran = $m3*$defaultRate;
							}
							else if ($value['type']==1) {		//kg
									$checkedKg = 'checked';
									$tran = $kg*$defaultRate;
							}

							if ($m3==0) $avg=0;
							else $avg = $tran/$m3;
							$disabled = '';
							if ($value['pstat']==0) {
									$checkedIncom = 'checked';
							}
							else if ($value['pstat']==1) {
									$checkedCom = 'checked';
									$saveFlg = 0;
									$disabled = ' disabled';
									if ($_adminFlg==1) {
										$disabled = ' ';
										$saveFlg=1;
									}
							}

		          			//table weight
		          			//get date
							if($value['last_edit_date']=='' || $value['last_edit_date']=='0000-00-00 00:00:00') $dt = '';
							else $dt = date_format(date_create($value['last_edit_date']),"d/m/Y H:i:s");
							
			                echo '<tbody><tr>
										<td><input class="input m3" value='.$value['width'].$disabled.'></td>
										<td><input class="input m3" value='.$value['length'].$disabled.'></td>
										<td><input class="input m3" value='.$value['height'].$disabled.'></td>
										<td class="number">'.number_format($m3,4,'.','').'</td>
										<td><input class="input m3" value='.number_format($value['weight'],2,'.','').$disabled.'></td>
										<td><select name="ptype"'.$disabled.'/>
												<option value="" selected="">-</option>';
													foreach ($_productInfo as $keyOption => $option) {
														if ($keyOption==$value['producttypeid']) {
															echo '<option value="'.$keyOption.'" selected>'.$option.'</option>';
														}
														else {
															echo '<option value="'.$keyOption.'">'.$option.'</option>';
														}
													}
										echo '</select></td>
										<td class="fixed"><div style="display:inline"><input name="type" class="type" type="checkbox" '.$checkedM3.' value="2" '.$disabled.'><span>M3</span><input name="type" class="type" type="checkbox" '.$checkedKg.' value="1" '.$disabled.'><span>Kg</span></div></td>
										<td><input class="input m3" name="rate" value='.number_format($defaultRate,4,'.',',').$disabled.'></td>
										<td class="number"><input class="input" value="'.number_format($tran,2,'.',',').'"></td>
										<td class="number">'.number_format($avg,2,'.',',').'</td>
										<td><div style="display:inline"><input name="stat" type="checkbox" '.$checkedIncom.$disabled.'><span>Incomplete</span><input name="stat" type="checkbox" '.$checkedCom.$disabled.'><span>complete</span></div></td>
										<td>'.$value['uid'].'</td>
										<td>'.$dt.'</td>
									</tr></tbody>';
			          	echo '</table>';
				}
				echo '</div>';

				//===============================================================================
				echo '<div class="remark" id='.$dataSet[0]['order_id'].'>';
						$remark = $dataSet[0]['remark'];
						echo '<span>หมายเหตุ</span><br>';
						if ($saveFlg) echo '<textarea style="width:99%" name="remark">'.$remark.'</textarea>';
						else echo '<textarea style="width:99%" name="remark" disabled>'.$remark.'</textarea>';
				echo '</div>';
				
				echo '<div class="console">';
						if ($saveFlg) echo '<button class="saveButton" onclick="save();">บันทึก</button>';
						echo '<button class="backButton" onclick="location.href=\'./index.php\'" type="button">กลับ</button>';
				echo '</div>';
		$con->close();
		//print_r($rate);
		?>
		
		<script type="text/javascript">
				var _rate = <?php echo json_encode($rate); ?>;
		</script>
		<script src="detailController.js"></script>
		<script>init();</script>
		
		</body>
</html>