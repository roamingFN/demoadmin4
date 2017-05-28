<?php
	session_start();
	if (!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	
	if(isset($_GET['order_id'])){
		$oid = $_GET['order_id'];
		$shops = array();
		$save = array();
		
		$rate1 = 1;
		$ono1 = '';
		$cid1 = '';
		$cname1 = '';
		$cmail1 = '';
		$code1 = 0;
		$_codes = array();
		$_remark = array();
		
		if($stmt = $con->prepare('SELECT order_product_id,confirmed_product_price,quantity,order_shipping_cn_m3_size,order_shipping_cn_weight,'.
			'order_shipping_cn_ref_no,customer_order_product.order_shipping_cn_cost,comment,unconfirmed_product_order,order_status,order_cause,backshop_price,backshop_shipping_cost,'.
			'shop_name,product_img,product_url,product_price,order_shipping_cn_box,order_shipping_rate,'.
			'product_size_china,product_color_china,comment,product.product_id,product.product_url,unitprice,'.
			'backshop_quantity,backshop_total_price,order_product_totalprice,remark_id,order_taobao'.
			' FROM customer_order_product,product,customer_order_shipping WHERE customer_order_product.product_id=product.product_id AND customer_order_product.order_id='.$oid.' AND '.
			'customer_order_shipping.order_id='.$oid.' GROUP BY order_product_id ORDER BY order_product_id' )) {
			$stmt->execute();
			$stmt->bind_result($opid,$cpp,$quan,$size,$weight,$ref,$cost,$comment,$upo,$status,$cause,
				$bp,$bcost,$shop,$img,$url,$pp,$box,$osr,$pSize,$pColor,$comment,$pid,$purl,$unp,$backQuan,$backTot,
				$opTot,$rid,$taobao);
			while($stmt->fetch()){
				$enc = base64_encode($shop);
				if(!isset($shops[$enc])) {
					$shops[$enc] = array();
				}
				array_push($shops[$enc],array($opid,$cpp,$quan,$size,$weight,$ref,$cost,$comment,$upo,$status,$cause,
					$bp,$bcost,$img,$url,$pp,$box,$osr,$pSize,$pColor,$comment,$pid,$purl,$unp,$backQuan,$backTot,
					$opTot,$rid,$taobao));
				array_push($save,array($opid,$enc));
			}
		}
		echo $con->error;

		//get rate, order number, customer info
		$sql = 'SELECT order_rate,order_number,order_status_code,'.
			'customer.customer_id,customer.customer_firstname,customer.customer_lastname,customer.customer_email'.
			' FROM customer_order JOIN customer ON customer.customer_id=customer_order.customer_id'. 
			' WHERE order_id='.$oid;
		if($stmt = $con->prepare($sql))
		$stmt->execute();
		$stmt->bind_result($rate,$ono,$ostat,$cid,$cfname,$clname,$cmail);
		while ($stmt->fetch()) {
			$rate1 = $rate;
			$ono1 = $ono;
			$code1 = $ostat;
			$cid1 = $cid;
			$cname1 = $cfname.' '.$clname;
			$cmail1 = $cmail;
		}

		//get status description
		if($stmt = $con->prepare('SELECT des FROM order_status_code')){
					$stmt->execute();
					$stmt->bind_result($des);
					while($stmt->fetch()){
						array_push($_codes,$des);
					}
		}

		//get remark description
		if($stmt = $con->prepare('SELECT remark_id,remark_tha FROM order_remark')){
					$stmt->execute();
					$stmt->bind_result($rid,$rth);
					while($stmt->fetch()){
						$_remark[$rid] = $rth;
					}
		}

		//add tracking
		if(isset($_POST['add'])){
                $opid = $_POST['opid'];
                $allTrack = '';
                if ($opid!="") {
                 		for ($i=1; $i<=5; $i++) {
                 			$track = $_POST['tracking'.$i];
                 			if ($track!="") {
                 				if ($allTrack!="") {
                 					$allTrack = $allTrack.','.$track;
                 				}
                 				else {
                 					$allTrack = $track;
                 				}
                 			}
                 		}
                }
                $oid = $_POST['oid'];
                $curr = $_POST['tracking_curr'];
                $res = updTracking($oid,$opid,$allTrack,$curr);
                header("Refresh:0");
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link rel="stylesheet" type="text/css" href="../css/w3-teal.css">
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<script src="../js/jquery-1.10.2.js"></script>
		<script src="../js/jquery-ui.js"></script>
		<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
		<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
		<script>
			$(function() {
				$( ".datepicker" ).datepicker({
					dateFormat: "dd-mm-yy"
				});
				$( ".timepicker" ).timepicker({
					timeFormat: "HH:mm:ss"
				});
			});
		</script>
	</head>
	<script>
		function numWithCom(x) {
			return Number(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}

		var rate1 = <?php echo $rate1; ?>;
		function calc1(id){
			var tranCost = Number(document.getElementById('btran-'+id).value);
			var cal1 = (document.getElementById('quan-'+id).value*Number(document.getElementById('cpp-'+id).value))+tranCost;
			document.getElementById('totalTh-'+id).textContent = numWithCom(cal1*Number(rate1));
		}
		
		function cancel(){
			close();
		}

		function numberify(txt){
			return txt.replace(/[^0-9.]/g,'');
		}

		function checkZero(val,id) {
				if (val==0) {
					document.getElementById(id).value = '';
				}
		}
		
		function checkStat1(opid){
			if (document.getElementById('stt1-'+opid).checked) {
				document.getElementById('stt2-'+opid).checked = false;
				document.getElementById('rem-'+opid).value = "0";
				document.getElementById('rem-'+opid).disabled = true;
			}
			else {
				document.getElementById('rem-'+opid).disabled = false;	
			}
		}
		
		function checkStat2(opid) {
			document.getElementById('rem-'+opid).disabled = false;
			if (document.getElementById('stt2-'+opid).checked) {
				document.getElementById('stt1-'+opid).checked = false;
				document.getElementById('quan1-'+opid).textContent = 0;
				document.getElementById('tran1-'+opid).textContent = 0;
				document.getElementById('tAmountCn1-'+opid).textContent = 0;
				document.getElementById('tAmountTh1-'+opid).textContent = 0;

				//backshop
				document.getElementById('quan-'+opid).value = 0;
				document.getElementById('btran-'+opid).value = 0;
				calc1(opid);
			}
		}

		setInterval(function(){
			var tquan1 = document.getElementsByClassName('quan1');
			var totalQuan1 = 0;
			for(var i=0;i<tquan1.length;i++){
				totalQuan1 += Number(tquan1[i].textContent);
			}

			var tTran = document.getElementsByClassName('tTran');
			var totalTran = 0;
			for(var i=0;i<tTran.length;i++){
				tranByRow = tTran[i].textContent;
				if (isNaN(Number(tranByRow))) {
						tranByRow=0;
				}
				totalTran += Number(tranByRow);
			}

			var tamount1 = document.getElementsByClassName('tAmountCn1');
			var totalAmount1 = 0;
			for(var i=0;i<tamount1.length;i++){
				amountByRow1 = tamount1[i].textContent;
				if (isNaN(Number(amountByRow1))) {
						amountByRow1 = amountByRow1.toString().replace(/,/g,'');
				}
				totalAmount1 += Number(amountByRow1);
			}
			document.getElementById('tquan1').textContent = totalQuan1;
			document.getElementById('tTran').textContent = numWithCom(totalTran);
			document.getElementById('tAmountCn1').textContent = numWithCom(totalAmount1);
			document.getElementById('tAmountTh1').textContent = numWithCom(totalAmount1*Number(rate1));

			//back shop
			var tquan = document.getElementsByClassName('tquan');
			var totalQuan = 0;
			for(var i=0;i<tquan.length;i++){
				totalQuan += Number(tquan[i].value);
			}

			var btTran = document.getElementsByClassName('btTran');
			var btotalTran = 0;
			for(var i=0;i<btTran.length;i++){
				btotalTran += Number(btTran[i].value);
			}

			var tamount = document.getElementsByClassName('tamount');
			var totalAmount = 0;
			for(var i=0;i<tamount.length;i++){
				amountByRow = tamount[i].textContent;
				if (isNaN(Number(amountByRow))) {
						amountByRow = amountByRow.toString().replace(/,/g,'');
				}
				totalAmount += Number(amountByRow);
			}
			document.getElementById('tquan').textContent = totalQuan;
			document.getElementById('btTran').textContent = numWithCom(btotalTran);
			document.getElementById('tamountTh').textContent = numWithCom(totalAmount);
			
		},500);
	
		var addOn = false;
		function add(opid) {
				addOn = !addOn;
				if(addOn){
					document.getElementById('addBox').style.visibility = 'visible';
					//get tracking
					var allTrack = document.getElementById('curr_trck-'+opid).value;
					if (allTrack!="") {
						var track = allTrack.split(',');
						for(var i = 0; i < track.length; i++) {
								var id = 'tracking'+(i+1)
   								document.getElementById(id).value = track[i];
						}
					}
					document.getElementById('oid').value = orderId;
					document.getElementById('opid').value = opid;
					document.getElementById('tracking_curr').value = document.getElementById('curr_trck-'+opid).value;
				}else{
					document.getElementById('addBox').style.visibility = 'hidden';
				}
		}

	</script>

	<body>
		<h2 style="color:#FF9900"><b><a href="product.php?order_id=<?php echo $oid?>">รายการสั่งซื้อ</a></b></h2>
        <br>
        <table class="order-results">
			<tr><td>เลขที่ออเดอร์  :</td><td id="order-id"><?php echo $ono1; ?></td></tr>
			<tr class="punc"><td>สถานะรายการ :</td><td><?php echo $_codes[$code1]; ?></td></tr>
		</table>
        <?php
        	$orate = 1;
			$process = 0;
        	if($stmt = $con->prepare('SELECT order_status_code,order_rate,process_status FROM customer_order WHERE order_id='.$oid)){
					$stmt->execute();
					$stmt->bind_result($i,$odr,$pro);
					while($stmt->fetch()){
						$code = $i;
						$orate = $odr;
						$process = $pro;
					}
			}

			$tquan = 0;
			$tamount = 0;
			$tthb = 0;
			$tCnCost = 0;
			$no = 1;

			$totalBs = 0;
			$tamountBs = 0;
			$tCnCostBS = 0;
			echo '<table class="order-product">';
			echo '<thead>'.
			'<th>ลำดับที่</th>'.
			'<th>ภาพตัวอย่าง</th>'.
			'<th>ขนาด</th>'.
			'<th>สี</th>'.
			'<th width="5%">จำนวน</th>'.
			'<th width="5%">ราคา/ชิ้น (หยวน)</th>'.
			'<th width="5%">ค่าขนส่งในจีน (หยวน)</th>'.
			'<th>รวม (หยวน)</th>'.
			'<th>รวม (บาท)</th>'.
			'<th width="8%">สถานะการสั่ง</th>'.
			'<th>หมายเหตุ</th>'.
			'<th width="5%">จำนวนหลังร้าน</th>'.
			'<th width="5%">ราคาหลังร้าน (หยวน)</th>'.
			'<th width="5%">ค่าขนส่งหลังร้าน (หยวน)</th>'.
			'<th>ราคารวมหลังร้าน (บาท)</th>'.
			'<th>order taobao</th>'.
			'<th colspan="2">Tracking No.</th>'.
			'</thead>';
			foreach($shops as $key=>$item){
				echo '<thead class="shopname undivide">'.
					'<th colspan="19">ร้าน '.base64_decode($key).'</th></thead>';
				$puncCount = 0;
				for($i=0;$i<sizeof($item);$i++){
					$total = ($item[$i][2]*(double)$item[$i][23])+(double)$item[$i][6];
					$tquan += $item[$i][2];
					$tamount += $total;
					$tthb += $total*$orate;

					$totalBS = (($item[$i][24]*(double)$item[$i][11])+(double)$item[$i][12])*$orate;
					$tamountBs += $totalBs;

					$trckNo = $item[$i][5];
					
					$cncost = '-';
					if(!empty($item[$i][6])){
						$cncost = (double)$item[$i][6];
						$tCnCost += $cncost;
					}

					$cncostBS = '-';
					if(!empty($item[$i][12])){
						$cncostBS = (double)$item[$i][12];
						$tCnCostBS += $cncostBS;
					}

					//order product id
					$opid = $item[$i][0];
					
					//order status
					$o_status = $item[$i][9];
					//no confirm
					if ($o_status==0) {
						$option = '<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="1" id="stt1-'.$opid.'" onclick="checkStat1('.$opid.')"><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="2" id="stt2-'.$opid.'" onclick="checkStat2('.$opid.')"><label> ไม่ได้</label>'.
								'</div>';
					}
					//order success
					else if ($o_status==1) {
						$option = '<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="1" id="stt1-'.$opid.'" onclick="checkStat1('.$opid.')" checked><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="2" id="stt2-'.$opid.'" onclick="checkStat2('.$opid.')"><label> ไม่ได้</label>'.
								'</div>';

						$bsQuan = '<input disabled="true" style="text-align:right;" class="tquan" id="quan-'.$opid.'" value="'.$item[$i][24].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'quan-'.$opid.'\')"/>';			//quanltity
						$bsPrice = '<input disabled="true" style="text-align:right;" id="cpp-'.$opid.'" value="'.(double)$item[$i][11].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'cpp-'.$opid.'\')"/>';	//price
						$bsTran = '<input disabled="true" style="text-align:right;" class="btTran" id="btran-'.$opid.'" value="'.$cncostBS.'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'btTran-'.$opid.'\')"/>';
						$bsTaobao = '<input disabled="true" style="text-align:right;" id="taobao-'.$opid.'" value="'.$item[$i][28].'"/>';
						$bsTrack = '<input disabled="true" style="text-align:right;" id="ref-'.$opid.'" value="'.$trckNo.'"/>';
					}
					//order fail
					else {
						$option = '<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="1" id="stt1-'.$opid.'" onclick="checkStat1('.$opid.')"><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="2" id="stt2-'.$opid.'" onclick="checkStat2('.$opid.')" checked><label> ไม่ได้</label>'.
								'</div>';

						$bsQuan = '<input disabled="true" style="text-align:right;" class="tquan" id="quan-'.$opid.'" value="'.$item[$i][24].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'quan-'.$opid.'\')"/>';			//quanltity
						$bsPrice = '<input disabled="true" style="text-align:right;" id="cpp-'.$opid.'" value="'.(double)$item[$i][11].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'cpp-'.$opid.'\')"/>';	//price
						$bsTran = '<input disabled="true" style="text-align:right;" class="btTran" id="btran-'.$opid.'" value="'.$cncostBS.'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'btTran-'.$opid.'\')"/>';			//transport china cost
						$bsTaobao = '<input disabled="true" style="text-align:right;" id="taobao-'.$opid.'" value="'.$item[$i][28].'"/>';
						$bsTrack = '<input disabled="true" style="text-align:right;" id="ref-'.$opid.'" value="'.$trckNo.'"/>';
					}

					echo '<tr class="'.($puncCount%2==0? 'punc ':'').(empty($trckNo)? 'cancel ':'').'">'.
						'<td align="center">'.$no.'</td>'.
						//<td><a href="'.$item[$i][22].'" target="_blank"><img height="150" width="150" src="'.$item[$i][13].'"/></a></td>'.
						'<td><a href="'.$item[$i][22].'" onclick="window.open(\''.$item[$i][22].'\', \'newwindow\', \'width=800, height=800\'); return false;"><img height="150" width="150" src="'.$item[$i][13].'"/></a></td>'.
						'<td>'.$item[$i][18].'</td>'.
						'<td>'.$item[$i][19].'</td>'.
						'<td align="right" class="quan1" id="quan1-'.$opid.'">'.$item[$i][2].'</td>'.			//quanltity
						'<td align="right">'.(double)$item[$i][23].'</td>'.	//price
						'<td align="right" class="tTran" id="tran1-'.$opid.'">'.$cncost.'</td>'.											//transport china cost
						'<td align="right" class="tAmountCn1" id="tAmountCn1-'.$opid.'">'.number_format($total,2).'</td>'.							//total chinese
						'<td align="right" class="tAmountTh1" id="tAmountTh1-'.$opid.'">'.number_format($total*$orate,2).'</td>'.					//total thai	
						'<td align="center">'.$option.'</td>'.
						'<td><select id="rem-'.$opid.'" disabled="true">';
						foreach ($_remark as $key => $value) {
								if($key==$item[$i][27]) echo '<option value="'.$key.'" selected>'.$value.'</option>';
								else echo '<option value="'.$key.'">'.$value.'</option>';
						}

						echo '</select></td>'.
						'<td>'.$bsQuan.'</td>'.		//quanltity
						'<td>'.$bsPrice.'</td>'.	//price
						'<td>'.$bsTran.'</td>'.		//transport china cost
						'<td align="right" class="tamount" id="totalTh-'.$opid.'">'.number_format($totalBS,2).'</td>'.
						'<td>'.$bsTaobao.'</td>'.
						'<td>'.$bsTrack.'</td>'.
						'<td><i class="material-icons" title="Add">add_circle</i></td>'.
						'<input type="hidden" id="curr_stat-'.$opid.'" value="'.$o_status.'" />'.
						'<input type="hidden" id="curr_amount-'.$opid.'" value="'.number_format($total*$orate,2).'" />'.
						'<input type="hidden" id="curr_trck-'.$opid.'" value="'.$trckNo.'" />'.
						'</tr>';
						$puncCount++;
						$no++;
				}
			}
			echo '<tbody class="padding">'.
			'<td class="cancel">ยอดรวม</td><td></td><td></td><td></td>'.
			'<td align="right" id="tquan1"></td><td></td>'.
			'<td align="right" id="tTran"></td>'.
			'<td align="right" id="tAmountCn1"></td>'.
			'<td align="right" id="tAmountTh1"></td>'.
			'<td></td><td></td>'.
			'<td align="right" id="tquan"></td>'.
			'<td></td>'.
			'<td align="right" id="btTran"></td>'.
			'<td align="right" id="tamountTh"></td>'.
			'<td></td>'.
			'</tbody></table>';
			
		?>
		<br><br>
		<div align="center" style="width:600px;left:0;right:0;margin-left:auto;margin-right:auto;">
			<button class="order-cancel" onclick="cancel()">กลับ</button>
			<?php
					if ($code==3) {
							echo '<button class="order-update" onclick="confirm()">สั่งซื้อเรียบร้อย</button>';
					}
			?>
		</div>
		<br>
	</body>

</html>


<?php
	$con->close();
?>