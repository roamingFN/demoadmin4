<?php
		echo '<table class="result green" id="'.$oid.'">';
		echo '<thead>';
        //echo '<tr><th colspan="8">ร้าน '.$key.'</th></tr>';
        echo '<tr>
					<th>Tracking no.</th>
					<th>รูปตัวอย่าง</th>
					<th>จำนวนที่สั่ง</th>
					<th>จำนวนที่รับแล้ว</th>
					<th>รับเพิ่ม</th>
					<th>ขาดอีก</th>
					<th>Last update by</th>
					<th>Last edit date</th>
				</tr>';
       	echo '</thead>';

         //table detail====================================================
		echo '<tbody>';
		foreach ($data as $opid => $value) {
				$disabled = '';
      			if ($value['pstat']==1) {
      					$disabled = ' disabled';
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
				$totalMissing += $value['backshop_quantity']-$value['received_amount'];
				$totalQuan += $value['backshop_quantity'];
				$totalRec += $value['received_amount'];
				$totalSum += $value['received_amount'];
		}
		echo '</tbody>';
		echo '</table>';
?>