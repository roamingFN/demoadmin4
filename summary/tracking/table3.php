<?php
		foreach ($arrangeByTracking as $trackingNo => $valueTest) {
				if ($valueTest[0]['shop_name']!=$data[0]['shop_name']) continue;
				
				echo '<span><h3>- Tracking '.$trackingNo.'</h3></span>';
				echo '<table class="result orange" id="'.$oid.'">';
				echo '<thead>';
		        //echo '<tr><th colspan="8">ร้าน '.$key.'</th></tr>';
		        echo '<tr>
						<th>Tracking no.</th>
						<th>รูปตัวอย่าง</th>
						<th>จำนวนที่สั่ง</th>
						<th>จำนวนที่รับแล้ว (รวม)</th>
						<th>รับจริง</th>
						<th>ขาดอีก</th>
						<th>Last update by</th>
						<th>Last edit date</th>
					</tr>';
		       	echo '</thead>';

		         //table detail====================================================
				echo '<tbody>';
				foreach ($valueTest as $opid => $value) {
						$disabled = '';
		      			if ($value['pstat']==1) {
		      					$disabled = ' disabled';
		      			}
		      			
		      			$ptid = $value['order_product_tracking_id'];
		      			if($value['last_edit_date']=='0000-00-00 00:00:00') $dt = '';
						else $dt = date_format(date_create($value['last_edit_date']),"d/m/Y H:i:s");
						echo '<tr class="none" id="'.$ptid.'">
								<td class="center">'.$value['tracking_no'].'</td>
								<td class="center"><a href="showImg.php?pid='.$value['product_img'].'" onclick="window.open(\'tracking/showImg.php?pid='.$value['product_id'].'\', \'_blank\', \'width=1024, height=768\'); return false;"><img height="150" width="150" src="'.$value['product_img'].'" title="'.$value['product_color_china'].' '.$value['product_size_china'].'"/></a></td>
								<td class="number" id="quan-'.$ptid.'">'.number_format($value['backshop_quantity']).'</td>
								<td class="center" id="received-'.$ptid.'"><a style="color: #00766a;" onclick="showAmountDialog('.$value['order_product_tracking_id'].')">'.number_format($value['received_amount']).'</a></td>
								<td class="number">'.number_format($value['amount']).'</td>
								<td class="number" id="missing-'.$ptid.'">'.number_format($value['backshop_quantity']-$value['received_amount']).'</td>
								<td class="center">'.$value['uid'].'</td>
								<td class="center">'.$dt.'</td>
							</tr>';
						echo '<input id="opid-'.$ptid.'" type="hidden" value="'.$value['order_product_id'].'">';
						$totalMissing += $value['backshop_quantity']-$value['received_amount'];
						$totalQuan += $value['backshop_quantity'];
						$totalRec += $value['received_amount'];
						$totalSum += $value['received_amount'];
				}
				echo '</tbody>';
				echo '</table>';

				//table2-------------------------------------------------------
				echo '<table class="result orange" id="m3-table">';
				echo '<thead>
            			<tr>
								<th style="width:5%;">กว้าง</th>
								<th style="width:5%;">ยาว</th>
								<th style="width:5%;">สูง</th>
								<th style="width:8%;">คิว</th>
								<th style="width:8%;">น้ำหนัก</th>
								<th style="width:8%;">ชนิดสินค้า</th>
								<th>Type</th>
								<th>เรท</th>
								<th style="width:8%;">ยอดค่าขนส่ง</th>
								<th style="width:8%;">ค่าเฉลี่ย</th>
								<th style="width:100px;">สถานะ</th>
								<th>User Add</th>
								<th>Add Date</th>
						</tr>
                </thead><tbody>';

				foreach ($valueTest as $opid => $value) {
						if ($value['masterflg']==0) continue;
						$rate = array();
						$ptypeInfo = array();
						$ptypeInfo = getpTypeInfo($con,	$value['producttypeid']);
						
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
					 	if (isset($ptypeInfo[0]['rate_type']) && isset($ptypeInfo[0]['product_type']) && ($value['rateweight']==0||$value['ratem3']==0) ) {
							 	$rate = getRate($con,$_userClass,$ptypeInfo[0]['rate_type'],$ptypeInfo[0]['product_type']);
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
			                //init 
			                $checkedM3 = '';
			                $checkedKg = '';
			                $checkedIncom = '';
			                $checkedCom = '';

			                //calcurate---------------
			                $m3 = ($value['width']*$value['length']*$value['height'])/1000000;
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
							}

		          			//table weight
		          			//get date
							if($value['last_edit_date']=='' || $value['last_edit_date']=='0000-00-00 00:00:00') $dt = '';
							else $dt = date_format(date_create($value['last_edit_date']),"d/m/Y H:i:s");
					
			                echo '<tr>
									<td><input class="input m3" value='.$value['width'].$disabled.'></td>
									<td><input class="input m3" value='.$value['length'].$disabled.'></td>
									<td><input class="input m3" value='.$value['height'].$disabled.'></td>
									<td class="number">'.number_format($m3,4,'.','').'</td>
									<td><input class="input m3" value='.number_format($value['weight'],2,'.','').$disabled.'></td>';
									echo '<td><select name="ptype"'.$disabled.'>
											<option value="" selected="">-</option>';
												foreach ($_productInfo as $keyOption => $option) {
													if ($keyOption==$value['producttypeid']) {
														echo '<option value="'.$keyOption.'" selected>'.$option.'</option>';
													}
													else {
														echo '<option value="'.$keyOption.'">'.$option.'</option>';
													}
												}
									echo '</select></td>';
									echo '<td class="fixed"><div style="display:inline"><input name="type" class="type" type="checkbox" '.$checkedM3.' value="2" '.$disabled.'><span>M3</span><input name="type" class="type" type="checkbox" '.$checkedKg.' value="1" '.$disabled.'><span>Kg</span></div></td>
									<td><input class="input m3" name="rate" value='.number_format($defaultRate,4,'.','').$disabled.'></td>
									<td class="number">'.number_format($tran,2,'.','').'</td>
									<td class="number">'.number_format($avg,2,'.','').'</td>
									<td><div style="display:inline"><input name="stat" type="checkbox" '.$checkedIncom.$disabled.'><span>Incomplete</span><input name="stat" type="checkbox" '.$checkedCom.$disabled.'><span>complete</span></div></td>
									<td>'.$value['uid'].'</td>
									<td>'.$dt.'</td>
								</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '<br />';
				}
?>