<?php
		include '../utility/function.php';
		include '../../database.php';
		include './initTracking.php';
		
		echo '<div>';
					//init===========================================================
					$puncCount = 0;
					$totalQuan = 0;
					$totalBSQuan = 0;
					$totalBSTran = 0;
					$totalYuan = 0;
					$totalBaht = 0;
					$totalRCQuan = 0;
					$totalMSQuan = 0;
					$totalRCBaht = 0;
					$totalRT1Baht = 0;
					$totalMSBaht = 0;
					$totalRT2Baht = 0;

					//get data
					$dataSet = getData($con,$sql,$condition,$orderBy,'','');
					$arrangedDataSet = arrangeData($dataSet);
					$oid = $dataSet[0]['order_id'];
					$cid = $dataSet[0]['customer_id'];
					$ono = $dataSet[0]['order_number'];
					$rate = $dataSet[0]['order_rate'];
					$paidDt = date_create($dataSet[0]['date_order_paid']);
					$remark = $dataSet[0]['remark'];
					$sumReturnFlag = $dataSet[0]['summary_return_flag'];
					$arrangeByTracking = rearrangeDataByTracking($dataSet);
					//get customer id, order number
					//echo '<input type="hidden" id="cid" value="'.$dataSet[0]['customer_id'].'">';
					echo '<input type="hidden" id="ono" value="'.$dataSet[0]['order_number'].'">';

					//pre-result
			        echo '<div style="display: inline">';
			        echo '<table class="preresult green none">
			        			<tr><td>เลขที่ออเดอร์  :</td><td>'.$ono.'</td></tr>
								<tr><td>Rate  :</td><td id="rate">'.number_format($rate,4).'</td></tr>
								<tr><td>วันที่ตัดจ่าย  :</td><td>'.date_format($paidDt,"d/m/Y H:i:s").'</td></tr>
						</table>';
					echo '</div>';
					echo '<br /><br /><br /><br />';
					//table header
					echo '<div>';
			          	$grandTotalMissingBaht = 0;
			          	$grandTotalReturnYuan2 = 0;
			          	$totalRec = 0;
			          	$totalSum = 0;
			          	foreach ($arrangedDataSet as $key => $data) {
			          			echo '<span align="center"><h1>ร้าน '.$key.'</h1></span>';
			          			include 'table0.php';
			     				//echo '<br />';
								// include 'table1.php';
								// echo '<br />';
								// include 'table2.php';
								echo '</br />';
								include 'table3.php';
								echo '<br /><hr></br />';
						}
						echo '</div>';
						
				echo '</div>';

				echo 'สรุป';
				echo '<div>';
					echo '<div style="float:left;width: 100%;">';
						echo '<table class="result green grandTotal" style="width: 30%;margin: 0;">';
							echo '<th>ยอดเสียหาย (บาท)</th>';
							echo '<th>ยอดที่ต้องคืนลูกค้า (บาท)</th>';
							echo '<th>คืนเงิน</th>';
							echo '<tr>';
									echo '<td style="text-align: center;">'.number_format($grandTotalMissingBaht,2).'</td>';
									echo '<td style="text-align: center;">'.number_format($grandTotalReturnYuan2,2).'</td>';
									if ($sumReturnFlag==0) {
										echo '<td style="text-align: center;" onclick="allReturn();"><a>คืนเงิน</a></td>';
									}
									else {
										echo '<td style="text-align: center;" onclick="backReturn()"><a>กลับ</a></td>';
									}
							echo '</tr>';
						echo '</table>';

						echo '<div class="console">';
								echo '<button class="saveButton" style="width: 12%;" onclick="save();">บันทึก</button>';
								echo '<button class="backButton" style="width: 12%;" onclick="location.href=\'./index.php\'" type="button">กลับ</button>';
								echo '<button class="completeButton" style="width: 12%;" onclick="complete()" type="button">Order Complete</button>';
						echo '</div>';
					echo '</div>';
				echo '</div>';

				echo '<div class="remark">';
						echo '<span>หมายเหตุ</span><br>';
						echo '<textarea name="remark">'.$remark.'</textarea>';
						echo '<input type="hidden" id="oid" name="oid" value="'.$oid.'">';
						echo '<input type="hidden" id="cid" name="cid" value="'.$cid.'">';
				echo '</div>';

				echo '<br><br><br>';
				include './dialog/amountDialog.php';
				include './dialog/loading.php';
				echo '<script src="./tracking/controller.js"></script>';
				$con->close();
?>