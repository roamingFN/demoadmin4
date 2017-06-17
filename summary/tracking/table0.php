<?php
		//company
		$company = $data[0]['tracking_company'];
		if ($company!='') {
			$pieces = explode(",", $company);
			$company = $pieces[0];
		}

		echo 	'<table class="quan result green" id="shop-'.$key.'">'.
					'<thead>'.
					// '<th colspan="9" style="border:0px;">'.'<span>ร้าน '.$key.'</span></th>'.
					// '<th colspan="4" style="border:0px;"><span>Taobao</span><input class="input" value="'.$data[0]['order_taobao'].'" style="width:70%;text-align:right;"></th>'.
					// '<th colspan="4" style="border:0px;"><span>บริษัท</span><input readonly class="input" value="'.$company.'" style="width:70%;text-align:right;"></th>'.
					// '<th colspan="4" style="border:0px;"><span>Tracking</span><input class="input" value="'.$data[0]['oTracking'].'" style="width:60%;text-align:right;"></th>'.
					// '</thead>'.
					// '<thead>'.
					'<tr>'.
						'<th rowspan="2" width="3%" style="background-color: #cc7a00;">ลำดับ</th>'.
						'<th rowspan="2" width="4%" style="background-color: #cc7a00;">ภาพตัวอย่าง</th>'.
						'<th rowspan="2" width="4%" style="background-color: #cc7a00;">จำนวน</th>'.
						'<th rowspan="2" width="4%" style="background-color: #cc7a00;">ราคา/ชิ้น (หยวน)</th>'.
						'<th rowspan="2" width="4%" style="background-color: #cc7a00;">ค่าขนส่งในจีน (หยวน)</th>'.
						//'<th width="7%">รวม (หยวน)</th>'.
						'<th rowspan="2" width="5%" style="background-color: #cc7a00;">รวม (บาท)</th>'.
						//'<th width="8%">สถานะการสั่ง</th>'.
						'<th rowspan="2" width="5%" style="background-color: #4d636f;">จำนวนที่สั่งได้</th>'.
						'<th rowspan="2" width="5%" style="background-color: #4d636f;">ราคาหลังร้าน (หยวน)</th>'.
						'<th rowspan="2" width="5%" style="background-color: #4d636f;">รวม (หยวน)</th>'.
						'<th rowspan="2" width="5%" style="background-color: #4d636f;">ค่ารถ (หยวน)</th>'.
						'<th rowspan="2" width="5%" style="background-color: #4d636f;">รวมทั้งหมด (หยวน)</th>'.
						'<th rowspan="2" width="5%" style="background-color: #4d636f;">รวมทั้งหมด (บาท)</th>'.
						'<th rowspan="2" width="5%" style="background-color: #4d636f;">ยอดคืนเงิน (บาท)</th>'.
						'<th rowspan="2" style="background-color: #4d636f;">คืนเงิน</th>'.
						//'<th>สถานะ</th>'.
						'<th colspan="5">ร้านค้าคืนเงิน</th>'.
						'<th rowspan="2" style="background-color: #2196F3;">จำนวนที่สั่งได้จริง</th>'.
						'<th rowspan="2" style="background-color: #2196F3;">จำนวนที่ได้รับจริง</th>'.
						'<th rowspan="2" style="background-color: #2196F3;">diff</th>'.
						'<th colspan="3" style="background-color: #F44336">ร้านค้าคืนเงิน</th>'.
					'</tr>'.
					'<tr>'.
						'<th>จำนวน</th>'.
						'<th>ยอดที่ต้องคืน</th>'.
						'<th>ยอดคืนแล้ว (บาท)</th>'.
						'<th>คงค้าง (บาท)</th>'.
						'<th>ร้านคืนจริง</th>'.
						'<th style="background-color: #F44336">จำนวนชิ้น</th>'.
						'<th style="background-color: #F44336">คืนเงินลูกค้ายอดเงิน (บาท)</th>'.
						'<th style="background-color: #F44336">Action</th>'.
					'</tr></thead>';

		//shopname------------------------------------
		$totalQuan = 0;
		$totalTran = 0;
		$totalPrice = 0;
		$totalBackQuan = 0;
		$totalBackPrice = 0;
		$totalBackTran = 0;
		$totalGrandYuan = 0;
		$totalGrandBaht = 0;
		$totalReturn = 0;
		$totalReturnQuan = 0;
		$totalReturnYuan = 0;
		$totalReturnBaht = 0;
		$totalReturnYuan2 = 0;
		$totalMissing = 0;
		$totalMissingBaht = 0;
		$totalMustReturn = 0;
		$totalReceived = 0;
		$totalDiff = 0;
		$totalLossQuan = 0;
		$totalLossBaht = 0;
		$no = 1;
		$tmpArray = array();

		$minOpid = findMinOpid($data);
		echo '<tbody>';
		foreach ($data as $value) {
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

			$opid = $value['order_product_id'];
			$trackingid = $value['order_product_tracking_id'];
			//return
			$return_yuan = $value['return_quantity']*$value['backshop_price'];
			$return_baht = $return_yuan*$value['order_rate']; 
			if ($minOpid==$value['order_product_id']) echo '<tr class="none" id="'.$trackingid.'" style="background-color: #EF9A9A;">';
			else echo '<tr class="none" id="'.$trackingid.'">';
			
			echo '<td align="center" class="none">'.$no.'</td>'.
					'<td><div style="float:left;"><a href="showImg.php?pid='.$value['product_img'].'" onclick="window.open(\'tracking/showImg.php?pid='.$value['product_id'].'\', \'_blank\', \'width=1024, height=768\'); return false;"><img height="150" width="150" src="'.$value['product_img'].'" title="'.$value['product_color_china'].' '.$value['product_size_china'].'"/></a></div>'.
					'<div align="center"><a href="'.$value['product_url'].'" onclick="window.open(\''.$value['product_url'].'\', \'_blank\', \'width=+screen.height,height=+screen.height,fullscreen=yes\'); return false"><img class="linkImg" height="20" width="20" src="../css/images/link.png"/></a></div></td>'.
					'<td align="right">'.$value['quantity'].'</td>'.		//quanltity
					'<td align="right">'.number_format($value['unitprice'],2).'</td>'.	//price
					'<td align="right">'.number_format($value['order_shipping_cn_cost'],2).'</td>'.											//transport china cost
					//'<td align="right">'.number_format($value['order_product_totalprice']/$value['order_rate'],2).'</td>'.							//total chinese
					'<td align="right">'.number_format($value['order_product_totalprice'],2).'</td>'.					//total thai	
					//'<td align="center">'.$option.'</td>';
					//echo '</select></td>'.
					'<td align="right">'.$value['backshop_quantity'].'</td>'.		//quanltity
					'<td class="number">'.$value['backshop_price'].'</td>'.	//price
					'<td align="right">'.number_format(($value['backshop_total_price']/$value['order_rate'])-$value['backshop_shipping_cost'],2).'</td>'. //total yuan
					'<td class="number">'.number_format($value['backshop_shipping_cost'],2).'</td>'.		//transport china cost
					'<td align="right">'.number_format(($value['backshop_total_price']/$value['order_rate']),2).'</td>'.
					'<td align="right">'.number_format($value['backshop_total_price'],2).'</td>'.
					'<td class="number">'.number_format($value['return_baht'],2).'</td>'.
					'<td class="center green"><a>ตกลง</a> <a>กลับ</a></td>';
					//'<td>'.($value['return_status']==2?'คืนแล้ว':'').'</td>'; 
			
			//shop refund------------------
					if ($value['backshop_quantity']==$value['received_amount']) {
						echo '<td><input style="border-bottom: 0px;" disabled class="input return return1" shop="'.$key.'" value='.$value['return_quantity'].'></td>';
					}
					else {
						echo '<td><input style="width: 70%;" class="input return return1" shop="'.$key.'" value='.$value['return_quantity'].'>';
						echo '  <i class="material-icons" onclick="showShopReturnDialog(\''.$opid.'\');">add_circle</i></td>';
					}
					echo	'<td class="number">'.number_format($value['backshop_price']*$value['return_quantity']*$value['order_rate'],2).'</td>'.
							'<td class="number">'.number_format($value['return_baht'],2).'</td>'.
							'<td class="number">'.number_format(($value['backshop_price']*$value['return_quantity']*$value['order_rate'])-$value['return_baht'],2).'</td>';
							if ($value['backshop_quantity']==$value['received_amount']) { 
								echo '<td><input style="border-bottom: 0px;" disabled class="input return return2" shop="'.$key.'" value='.$value['return_yuan'].'></td>';
							}
							else {
								echo '<td><input class="input return return2" shop="'.$key.'" value='.$value['return_yuan'].'></td>';
							}
					$totalMustReturn += ($value['backshop_price']*$value['return_quantity'])-$return_baht;

			//diff-------------------------
					$missing = $value['backshop_quantity']-$value['return_quantity'];
					$missing_baht = $missing*$value['backshop_price']*$value['order_rate'];
					$diff = ($value['received_amount']-$missing);
					echo	'<td class="number">'.$missing.'</td>'.
							'<td class="number">'.(int)$value['received_amount'].'</td>'.
							'<td class="number">'.$diff.'</td>';

			//customer refund--------------------
					echo '<td><input class="input loss" shop="'.$key.'" value='.$value['loss_quantity'].'></td>';
					echo '<td><input class="input loss" shop="'.$key.'" value='.$value['loss_baht'].'></td>';
					if ($value['loss_status']==0) echo '<td><a onclick="returnLoss('.$opid.')">คืนเงิน</a></td>';
					else echo '<td><a onclick="backReturnLoss('.$opid.')">กลับ</a></td>';

			//hiddent field
					echo '<input type="hidden" value='.$value['received_amount'].'>';
					echo '<input type="hidden" value='.$value['order_rate'].'>';
					echo '<input id="opid-'.$trackingid.'" type="hidden" value="'.$opid.'">';
					echo '<input id="backReturn-'.$trackingid.'" type="hidden" value='.$value['return_yuan'].'>';

			$no++;
			echo '</tr>';	//end row

			$totalQuan += $value['quantity'];
			$totalPrice += $value['order_product_totalprice'];
			$totalTran += $value['order_shipping_cn_cost'];

			$totalBackQuan += $value['backshop_quantity'];
			$totalBackPrice += ($value['backshop_total_price']/$value['order_rate']);
			$totalBackTran += $value['backshop_shipping_cost'];
			$totalGrandYuan += ($value['backshop_total_price']/$value['order_rate']);
			$totalGrandBaht += $value['backshop_total_price'];
			$totalReturn += $value['return_baht'];

			$totalReturnQuan += $value['return_quantity'];
			$totalReturnYuan += $return_yuan;
			$totalReturnBaht += $return_baht;
			$totalReturnYuan2 += $value['return_yuan'];
			$totalMissing += $missing;
			$totalMissingBaht += $missing_baht;
			$totalReceived += (int)$value['received_amount'];
			$totalDiff += $diff;

			//loss
			$totalLossQuan += $value['loss_quantity'];
			$totalLossBaht += $value['loss_baht'];
		}
		echo '</tbody>';

		//footer--------------------------------------------------------------
		echo '<tfoot style="text-align: right;color: black;">';
			echo '<td>ยอดรวม</td>';
			echo '<td></td><td>'.$totalQuan.'</td>';
			echo '<td></td><td>'.number_format($totalTran,2).'</td>';
			echo '<td>'.number_format($totalPrice,2).'</td>';
			echo '<td>'.$totalBackQuan.'</td>';
			echo '<td></td><td>'.number_format($totalBackPrice,2).'</td>';
			echo '<td>'.number_format($totalBackTran,2).'</td>';
			echo '<td>'.number_format($totalGrandYuan,2).'</td>';
			echo '<td>'.number_format($totalGrandBaht,2).'</td>';
			echo '<td>'.number_format($totalReturn,2).'</td><td></td>';
			echo '<td id="returnQuan-'.$key.'">'.$totalReturnQuan.'</td>';
			echo '<td>'.number_format($totalReturnBaht,2).'</td>';
			echo '<td id="returnYuan-'.$key.'">'.number_format($totalReturn,2).'</td>';
			echo '<td id="missingBaht-'.$key.'">'.number_format($totalReturnBaht,2).'</td>';
			echo '<td id="returnYuan2-'.$key.'">'.number_format($totalReturnYuan2,2).'</td>';
			echo '<td id="missing-'.$key.'">'.$totalMissing.'</td>';
			echo '<td id="missingBaht-'.$key.'">'.$totalReceived.'</td>';
			echo '<td>'.$totalDiff.'</td>';
			echo '<td>'.$totalLossQuan.'</td>';
			echo '<td>'.number_format($totalLossBaht,2).'</td>';
			echo '<td></td>';
		echo '</tfoot>';
		echo '</table>';

		//grandTotal
		$grandTotalMissingBaht += $totalMissingBaht;
		$grandTotalReturnYuan2 += $totalReturnYuan2;
?>

<script type="text/javascript">
	$(document).ready(function() {
		$(".return").keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and . and f5
	        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 116, 190]) !== -1 ||
	             // Allow: Ctrl+A
	            (e.keyCode == 65 && e.ctrlKey === true) ||
	             // Allow: Ctrl+C
	            (e.keyCode == 67 && e.ctrlKey === true) ||
	             // Allow: Ctrl+X
	            (e.keyCode == 88 && e.ctrlKey === true) ||
	             // Allow: home, end, left, right
	            (e.keyCode >= 35 && e.keyCode <= 39)) {
	                // let it happen, don't do anything
	                return;
	        	}
	        // Ensure that it is a number and stop the keypress
	        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
	            	e.preventDefault();
	        }
		});

		$(".return1").keyup(function (e) {
			calReturn($(this).attr('shop'));
		});

		$(".return2").keyup(function (e) {
			calReturn2($(this).attr('shop'));
		});

		$(".return").click(function (){
			if ($(this).val()==0) {
				$(this).val('');
			}
		});

		//loss------------------------------------------------------------
		$(".loss").keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and . and f5
	        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 116, 190]) !== -1 ||
	             // Allow: Ctrl+A
	            (e.keyCode == 65 && e.ctrlKey === true) ||
	             // Allow: Ctrl+C
	            (e.keyCode == 67 && e.ctrlKey === true) ||
	             // Allow: Ctrl+X
	            (e.keyCode == 88 && e.ctrlKey === true) ||
	             // Allow: home, end, left, right
	            (e.keyCode >= 35 && e.keyCode <= 39)) {
	                // let it happen, don't do anything
	                return;
	        	}
	        // Ensure that it is a number and stop the keypress
	        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
	            	e.preventDefault();
	        }
		});

		$(".loss").keyup(function (e) {
			var shop = $(this).attr('shop');
			
			var totalLossQuan = 0;
			var totalLossBaht = 0;
			$('#shop-' + shop +' tbody tr').each(function () {
				var loss = Number($(this).find("input").eq(2).val());
				if (isNaN(loss)) loss=0;

				var backshopPrice = Number($(this).find("td").eq(7).text());
				if (isNaN(backshopPrice)) backshopPrice=0;

				var total = loss*backshopPrice;
				
				totalLossQuan += loss;
				totalLossBaht += total;

				//display loss baht
				$(this).find("input").eq(3).val(total.toFixed(2));				
			});

			$('#shop-' + shop + ' tfoot').find("td").eq(22).text(totalLossQuan);
			$('#shop-' + shop + ' tfoot').find("td").eq(23).text(numberWithCommas(totalLossBaht));
		});

		$(".loss").click(function (){
			if ($(this).val()==0) {
				$(this).val('');
			}
		});


	});
</script>