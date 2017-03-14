<?php
		include '../../database.php';
		include '../utility/function.php';
		$data = $_POST['data'];
		$_statDesc = getPackageStatInfo($con);

		$sql = 'SELECT o.order_number,p.packageid,p.packageno,p.statusid, pt.remark, pt.uid, pt.last_edit_date, pt.tracking_no, pt.received_amount, pp.product_img FROM package p JOIN package_detail pd ON p.packageid=pd.packageid
			JOIN customer_order_product_tracking pt ON pt.packageid=p.packageid
			JOIN customer_order_product op ON op.order_product_id=pt.order_product_id
			JOIN customer_order o ON o.order_id=op.order_id
			JOIN product pp ON op.product_id= pp.product_id';
		$condition = ' WHERE pd.order_product_id='.$data['opid'];

		$dataSet = getData($con,$sql,$condition,'','','');
		
		//rearrange dataSet order By package number
		$result = array();
		foreach ($dataSet as $key => $value) {
				$result[$value['packageid']][] = $value;
		}
		
		//show result
		echo '<div>';
		foreach ($result as $key => $package) {
				//print_r($package);
				$ono = $package[0]['order_number'];
				$pno = $package[0]['packageno'];
				$pstat = $package[0]['statusid'];
				echo '<div>';
		        echo '<table class="preresult green none" style="width:500px;">
		        			<tr><td>เลขที่ออเดอร์  :</td><td>'.$ono.'</td><td></td><td></td></tr>
							<tr><td>เลขที่กล่อง  :</td><td>'.$pno.'</td>
							<td>สถานะกล่อง : </td><td>'.$_statDesc[$pstat].'</td>
							</tr>
					</table>';
				echo '</div>';

				echo '<div>';
						echo '<table class="result green">
								<thead>
										<tr>
												<th>Tracking No.</th>
												<th>รูป</th>
												<th>จำนวนที่ขาด</th>
												<th>จำนวนที่สั่ง</th>
												<th>จำนวนที่เคยรับ</th>
												<th>จำนวนที่ได้รับ</th>
												<th>รวม</th>
												<th>User Add</th>
												<th>Add Date</th>
												<th>หมายเหตุ</th>
										</tr>
								</thead>';
						foreach ($package as $key2 => $tracking) {
								if($tracking['last_edit_date']=='0000-00-00 00:00:00') $dt = '';
								else $dt = date_format(date_create($tracking['last_edit_date']),"d/m/Y H:i:s");

								echo '<tbody>
								<tr>
										<td>'.$tracking['tracking_no'].'</td>
										<td><img src="'.$tracking['product_img'].'"></td>
										<td class="number"></td>
										<td class="number"></td>
										<td class="number"></td>
										<td class="number">'.number_format($tracking['received_amount']).'</td>
										<td class="number"></td>
										<td>'.$tracking['uid'].'</td>
										<td>'.$dt.'</td>
										<td>'.$tracking['remark'].'</td>
								</tr>
							</tbody>';	
						}
				echo '<tfoot>
						<td></td>
						<td>รวม</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tfoot>';
				echo '</table>';
				echo '<br>';
		}
				
		echo '</div>';

		echo '</div>';
		echo '</div>';
		$con->close();
?>