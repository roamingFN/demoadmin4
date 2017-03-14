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
					$ono = $dataSet[0]['order_number'];
					$rate = $dataSet[0]['order_rate'];
					$paidDt = date_create($dataSet[0]['date_order_paid']);
					$remark = $dataSet[0]['remark'];
					$arrangeByTracking = rearrangeDataByTracking($dataSet);
					//get customer id, order number
					echo '<input type="hidden" id="cid" value="'.$dataSet[0]['customer_id'].'">';
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
			          	$totalMissing = 0;
			          	$totalQuan = 0;
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

				echo '<div class="remark">';
						echo '<span>หมายเหตุ</span><br>';
						echo '<textarea name="remark">'.$remark.'</textarea>';
						echo '<input type="hidden" id="oid" name="oid" value="'.$oid.'">';
				echo '</div>';

				include './dialog/amountDialog.php';
				echo '<script src="./tracking/controller.js"></script>';
				$con->close();
?>