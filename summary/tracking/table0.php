<?php
		echo 	'<table class="result green">'.
					'<thead>'.
					'<th width="2%">ลำดับ</th>'.
					'<th width="3%">ภาพตัวอย่าง</th>'.
					'<th width="3%">จำนวน</th>'.
					'<th width="5%">ราคา/ชิ้น (หยวน)</th>'.
					'<th width="5%">ค่าขนส่งในจีน (หยวน)</th>'.
					'<th width="7%">รวม (หยวน)</th>'.
					'<th width="7%">รวม (บาท)</th>'.
					'<th width="8%">สถานะการสั่ง</th>'.
					'<th width="5%">จำนวนที่สั่งได้</th>'.
					'<th width="5%">ราคาหลังร้าน (หยวน)</th>'.
					'<th width="8%">รวม (หยวน)</th>'.
					'<th width="5%">ค่ารถ (หยวน)</th>'.
					'<th width="8%">รวมทั้งหมด (หยวน)</th>'.
					'<th width="8%">รวมทั้งหมด (บาท)</th>'.
					'<th width="8%">ยอดคืนเงิน (บาท)</th>'.
					'<th>คืนเงิน</th>'.
					'<th>สถานะ</th>'.
					'</thead>'.
					'<thead>'.
					'<th colspan="10" style="border:0px;">'.'<span>ร้าน '.$key.'</span></th>'.
					'<th colspan="3" style="border:0px;"><span>Taobao</span><input class="input" value="'.$data[0]['taobao'].'" style="width:70%;text-align:right;"></th>'.
					'<th colspan="4" style="border:0px;"><span>Tracking</span><input class="input" value="'.$data[0]['oTracking'].'" style="width:60%;text-align:right;"></th>'.
					'</thead>';

		//shopname------------------------------------
		$no = 1;
		$tmpArray = array();
		echo '<tbody>';
		foreach ($data as $opid => $value) { 
				if (array_key_exists($value['order_product_id'], $tmpArray)) continue;
				$tmpArray[$value['order_product_id']]="";
				if ($value['order_status']==1) {
						$option = '<div style="display:inline">'.
									'<input disabled style="width:auto" type="checkbox" value="1" checked><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled style="width:auto" type="checkbox" value="2"><label> ไม่ได้</label>'.
								'</div>';
				}
				else {
						$option = '<div style="display:inline">'.
									'<input disabled style="width:auto" type="checkbox" value="1"><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled style="width:auto" type="checkbox" value="2" checked><label> ไม่ได้</label>'.
								'</div>';
				}

				echo '<tr class="none">'.
						'<td align="center" class="none">'.$no.'</td>'.
						'<td><div style="float:left;"><a href="showImg.php?pid='.$value['product_img'].'" onclick="window.open(\'showImg.php?pid='.$value['product_url'].'\', \'_blank\', \'width=1024, height=768\'); return false;"><img height="150" width="150" src="'.$value['product_img'].'" title="'.$value['product_color'].' '.$value['product_size'].'"/></a></div>'.
						'<div align="center"><a href="'.$value['product_url'].'" onclick="window.open(\''.$value['product_url'].'\', \'_blank\', \'width=+screen.height,height=+screen.height,fullscreen=yes\'); return false"><img class="linkImg" height="20" width="20" src="../css/images/link.png"/></a></div></td>'.
						'<td align="right">'.$value['quantity'].'</td>'.		//quanltity
						'<td align="right">'.number_format($value['unitprice'],2).'</td>'.	//price
						'<td align="right">'.number_format($value['order_shipping_cn_cost'],2).'</td>'.											//transport china cost
						'<td align="right">'.number_format($value['order_product_totalprice']/$value['order_rate'],2).'</td>'.							//total chinese
						'<td align="right">'.number_format($value['order_product_totalprice'],2).'</td>'.					//total thai	
						'<td align="center">'.$option.'</td>';
						echo '</select></td>'.
						'<td>'.$value['backshop_quantity'].'</td>'.		//quanltity
						'<td class="number">'.$value['backshop_price'].'</td>'.	//price
						'<td align="right">'.number_format(($value['backshop_total_price']/$value['order_rate'])-$value['backshop_shipping_cost'],2).'</td>'. //total yuan
						'<td class="number">'.number_format($value['backshop_shipping_cost'],2).'</td>'.		//transport china cost
						'<td align="right">'.number_format(($value['backshop_total_price']/$value['order_rate']),2).'</td>'.
						'<td align="right">'.number_format($value['backshop_total_price'],2).'</td>'.
						'<td class="number">'.number_format($value['return_baht'],2).'</td>'.
						'<td class="center green"><a>ตกลง</a> <a>กลับ</a></td>'.
						'<td>'.($value['return_status']==2?'คืนแล้ว':'').'</td>';
						$no++;
				echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';

?>