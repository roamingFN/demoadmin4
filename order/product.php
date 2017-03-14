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
		
		$m3 = array();
		$kg = array();
		
		//$rate1 = 0;
		
		if($stmt = $con->prepare('SELECT order_product_id,confirmed_product_price,quantity,order_shipping_cn_m3_size,order_shipping_cn_weight,'.
			'order_shipping_cn_ref_no,customer_order_product.order_shipping_cn_cost,comment,unconfirmed_product_order,order_status,order_cause,backshop_price,backshop_shipping_cost,'.
			'shop_name,product_img,product_url,product_price,order_shipping_cn_box,order_shipping_rate,confirmed_quantity '.
			'FROM customer_order_product,product,customer_order_shipping WHERE customer_order_product.product_id=product.product_id AND customer_order_product.order_id='.$oid.' AND '.
			'customer_order_shipping.order_id='.$oid)){
			$stmt->execute();
			$stmt->bind_result($opid,$cpp,$quan,$size,$weight,$ref,$cost,$comment,$upo,$status,$cause,$bp,$bcost,$shop,$img,$url,$pp,$box,$osr,$cquan);
			while($stmt->fetch()){
				$enc = base64_encode($shop);
				if(!isset($shops[$enc])){
					$shops[$enc] = array();
				}
				if(!isset($m3[$enc])){
					$m3[$enc] = 0;
				}
				if(!isset($kg[$enc])){
					$kg[$enc] = 0;
				}
				
				array_push($shops[$enc],array($opid,$cpp,$quan,$size,$weight,$ref,$cost,$comment,$upo,$status,$cause,$bp,$bcost,$img,$url,$pp,$box,$osr,$cquan));
				array_push($save,array($opid,$enc));
				
				$m3[$enc] += (double)$size;
				$kg[$enc] += (double)$weight;
				
				//$rate1 = $osr;
			}
		}
		echo $con->error;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
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
		
		function calc1(id){
			document.getElementById('t-'+id).textContent = numWithCom(
				(Number(document.getElementById('quan-'+id).value)*Number(document.getElementById('cpp-'+id).value))+
				Number(document.getElementById('cost-'+id).value));
		}
		
		function calc2(key){
			document.getElementById('cn-total-'+key).textContent = numWithCom(
				(Number(document.getElementById('cn-kg-'+key).value)*Number(document.getElementById('cn-rate-'+key).value))+
				Number(document.getElementById('cn-box-'+key).value));
		}
		
		function bcalc(id1,id2,id3,id4){
			document.getElementById(id4).textContent = numWithCom(
				Number(Number(document.getElementById(id1).value)*Number(document.getElementById(id2).value))
				+Number(document.getElementById(id3).value));
		}
		
		var orderId = <?php echo $oid; ?>;
		function save(){
			var save = [],china = [];
			var data = {};
			<?php 
				for($i=0;$i<sizeof($save);$i++){
					echo 'save.push("'.$save[$i][0].'");';
					echo 'china.push("'.$save[$i][1].'");';
				}
			?>
			for(var i=0;i<save.length;i++){
				var id = save[i];
				data[id] = {
					'ref':document.getElementById('ref-'+id).value,
					'stt':document.getElementById('stt-'+id).options[document.getElementById('stt-'+id).selectedIndex].value,
					'quan':document.getElementById('quan-'+id).value,
					'cause':document.getElementById('cause-'+id).value,'cpp':document.getElementById('cpp-'+id).value,
					'cost':document.getElementById('cost-'+id).value,'bp':document.getElementById('bp-'+id).value,
					'bcost':document.getElementById('bcost-'+id).value,
					//china
					'cn-box':document.getElementById('cn-box-'+china[i]).value,
					'cn-m3':document.getElementById('cn-m3-'+china[i]).value,
					'cn-kg':document.getElementById('cn-kg-'+china[i]).value,
					'cn-rate':document.getElementById('cn-rate-'+china[i]).value
				};
			}
			data['code'] = document.getElementById('order-code').value;
			data['oid'] = orderId;
			
			var d = document.getElementById('th-date').value;
			data['th-date'] = d.substring(6,10)+'-'+d.substring(3,5)+'-'+d.substring(0,2);
			data['th-ref'] = document.getElementById('th-ref').value;
			data['th-kg'] = document.getElementById('th-kg').value;
			data['th-cost'] = document.getElementById('th-cost').value;
			
			data['total1'] = document.getElementById('total1').textContent.replace(/,/,'');
			data['total2'] = document.getElementById('total2').textContent.replace(/,/,'');
			
			//สถานะสั่งซื้อ
			var radios = document.getElementsByName('process');
			for (var i=0,length=radios.length;i<length;i++){
				if (radios[i].checked) {
					data['process'] = radios[i].value;
					break;
				}
			}
			
			var xhr = new XMLHttpRequest();
			xhr.open('POST','save_product.php',true);
			xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			xhr.onreadystatechange = function(){
				if(xhr.readyState==4 && xhr.status==200){
					if(xhr.responseText=='success'){
                        alert("บันทึกข้อมูลเรียบร้อยแล้ว");
						location.reload();
					}else{
						//alert('กรุณาใส่ข้อมูลให้ถูกต้องค่ะ!');
						alert(xhr.responseText);
					}
				}
			};
			xhr.send('data='+JSON.stringify(data));
		}
		function cancel(){
			window.location = 'order.php';
		}
		function numberify(txt){
			return txt.replace(/[^0-9.]/g,'');
		}
	</script>
	<body>
		<h1><a href="product.php?order_id=<?php echo $oid?>">รายการสั่งซื้อหลังร้าน</a></h1>
                <h3><a href="order.php">&larr; Back</a></h3><br>
		<table class="order-results">
			<tr><td>เลขที่ออเดอร์  :</td><td id="order-id"><?php 
				if($stmt = $con->prepare('SELECT order_number FROM customer_order WHERE order_id='.$oid)){
					$stmt->execute();
					$stmt->bind_result($onum);
					while($stmt->fetch()){
						echo $onum;
					}
				}
			?></td></tr>
			<?php
				$code = 0;
				$codes = array();
				$orate = 1;
				$process = 0;
				if($stmt = $con->prepare('SELECT des FROM order_status_code')){
					$stmt->execute();
					$stmt->bind_result($des);
					while($stmt->fetch()){
						array_push($codes,$des);
					}
				}
				if($stmt = $con->prepare('SELECT order_status_code,order_rate,process_status FROM customer_order WHERE order_id='.$oid)){
					$stmt->execute();
					$stmt->bind_result($i,$odr,$pro);
					while($stmt->fetch()){
						$code = $i;
						echo '<tr class="punc"><td>สถานะรายการ :</td><td>'.$codes[$i].'</td></tr>';
						$orate = $odr;
						$process = $pro;
					}
				}
				echo '<tr><td width="130">สถานะต่อไป :</td><td><select id="order-code">';
				for($i=0;$i<sizeof($codes);$i++){
					echo '<option value="'.$i.'" '.($i==$code? 'selected':'').'>'.$codes[$i].'</option>';
				}
				echo '</select></td></tr>';
			
				$shipping = '';
				$th_date = '';
				$th_ref = '';
				$th_kg = '';
				$th_cost = '';
				if($stmt = $con->prepare('SELECT order_shipping_th_option,order_shipping_th_date,order_shipping_th_ref_no,order_shipping_th_weight,order_shipping_th_cost'.
					' FROM customer_order_shipping WHERE order_id='.$oid)){
					$stmt->execute();
					$stmt->bind_result($opt,$tdate,$tref,$tkg,$tcost);
					while($stmt->fetch()){
						echo '<tr class="punc"><td>บริการขนส่งในประเทศ :</td><td>'.$opt.'</td></tr>'.
							'<tr><td>เลขที่ขนส่งในประเทศ :</td><td>'.$tref.'</td></tr>';
						$shipping = $opt;
						$th_date = $tdate;
						$th_ref = $tref;
						$th_kg = $tkg;
						$th_cost = $tcost;
					}
				}
				echo $con->error;
			?>
		</table>
		<table class="order-results">
			<tr><td>ยอดค่าสินค้า (รอบที่1)</td><td id="total1"> - </td><td>THB</td></tr>
			<tr class="punc"><td>ยอดค่าขนส่ง (รอบที่2)</td><td id="total2"> - </td><td>THB</td></tr>
			<tr><td></td><td id="totalall" class="cancel"> - </td><td class="cancel">THB</td></tr>
		</table>
		<fieldset>
			<legend>สถานะการสั่งซื้อ :</legend>
			<input type="radio" id="pro0" name="process" value="0">
			<label for="pro0">รอสั่ง</label><br>
			<input type="radio" id="pro1" name="process" value="1">
			<label for="pro1">กำลังสั่ง</label><br>
			<input type="radio" id="pro2" name="process" value="2">
			<label for="pro2">สั่งแล้ว</label>
		</fieldset>
		<br><br><br><br><br><br><br><br><br>
		<script>
			window.onload = function(){
				//disable all inputs
				if(<?php echo ($code>=2? 'true':'false'); ?>){
					var e = document.getElementsByTagName('input');
					for (var i=0;i<e.length;i++) {
						e[i].disabled = true;
					}
					var e = document.getElementsByTagName('textarea');
					for (var i=0;i<e.length;i++) {
						e[i].disabled = true;
					}
					var e = document.getElementsByTagName('select');
					for (var i=0;i<e.length;i++) {
						e[i].disabled = true;
					}
				}
				if(<?php echo ($code==0|| $code==1? 'true':'false'); ?>){
					var e = document.getElementsByClassName('track');
					for (var i=0;i<e.length;i++) {
						e[i].disabled = true;
					}
					var e = document.getElementsByClassName('china');
					for (var i=0;i<e.length;i++) {
						e[i].disabled = true;
					}
				}
				if(<?php echo ($code==2? 'true':'false'); ?>){
					var e = document.getElementsByClassName('track');
					for (var i=0;i<e.length;i++) {
						e[i].removeAttribute('disabled');
					}
				}
				if(<?php echo ($code>=5? 'true':'false'); ?>){
					var e = document.getElementsByClassName('china');
					for (var i=0;i<e.length;i++) {
						e[i].removeAttribute('disabled');
					}
				}
				document.getElementById('order-code').removeAttribute('disabled');
				
				//สถานะสั่งซื้อ
				var radios = document.getElementsByName('process');
				for (var i=0,length=radios.length;i<length;i++){
					radios[i].removeAttribute('disabled');
					if (radios[i].value==<?php echo $process;?>) {
						radios[i].checked = true;
					}
				}
			}
			
			var rate1 = <?php echo $orate; ?>;
			setInterval(function(){
				var tquan = document.getElementsByClassName('tquan');
				var total1 = 0;
				for(var i=0;i<tquan.length;i++){
					total1 += Number(tquan[i].value);
				}
				var tamount = document.getElementsByClassName('tamount');
				var total2 = 0;
				for(var i=0;i<tamount.length;i++){
					total2 += Number(tamount[i].textContent.replace(/,/,''));
				}
				var tshop = document.getElementsByClassName('tshop');
				var total3 = 0;
				for(var i=0;i<tshop.length;i++){
					total3 += Number(tshop[i].textContent.replace(/,/,''));
				}
				document.getElementById('tquan').textContent = numWithCom(total1);
				document.getElementById('tamount').textContent = numWithCom(total2);
				document.getElementById('tshop').textContent = numWithCom(total3);
				document.getElementById('total1').textContent = numWithCom(total2*rate1);
				
				var china = [];
				<?php 
					foreach($m3 as $key=>$item){
						echo 'china.push("'.$key.'");';
					}
				?>
				var cnbox = 0,cnm3 = 0,cnkg = 0,cntotal = 0;
				for(var i=0;i<china.length;i++){
					cnbox += Number(document.getElementById('cn-box-'+china[i]).value);
					cnm3 += Number(document.getElementById('cn-m3-'+china[i]).value);
					cnkg += Number(document.getElementById('cn-kg-'+china[i]).value);
					cntotal += Number(document.getElementById('cn-total-'+china[i]).textContent.replace(/,/,''));
				}
				document.getElementById('cn-tbox').textContent = numWithCom(cnbox);
				document.getElementById('cn-tm3').textContent = numWithCom(cnm3);
				document.getElementById('cn-tkg').textContent = numWithCom(cnkg);
				document.getElementById('cn-total').textContent = numWithCom(cntotal);
				
				document.getElementById('th-total2').textContent = numWithCom(
					Number(document.getElementById('cn-total').textContent.replace(/,/,''))+Number(document.getElementById('th-cost').value)
				);
				
				document.getElementById('total2').textContent = document.getElementById('th-total2').textContent;
				document.getElementById('totalall').textContent = numWithCom(
					Number(document.getElementById('total1').textContent.replace(/,/,''))+
					Number(document.getElementById('total2').textContent.replace(/,/,'')));
				
			},500);
		</script>
		<?php
			$tquan = 0;
			$tamount = 0;
			$tthb = 0;
			
			echo '<table class="order-product">';
			echo '<thead><th>ภาพตัวอย่าง</th><th>จำนวน</th><th>ราคา(ยังไม่confirm)</th><th>ค่าขนส่ง(จีน)</th><th>รวม</th>'.
				'<th>Rate@Date</th><th>THB</th><th>สถานะ</th><th>Trackingจีน</th><th>Link</th><th>สถานะการสั่ง</th><th>เหตุที่ไม่ได้</th><th>จำนวนตัว</th>'.
				'<th>ราคาที่confirm</th><th>ค่าขนส่ง(จีน)</th><th>ราคารวม</th><th>ราคาหลังร้าน</th><th>ค่าขนส่ง(จีนหลังร้าน)</th><th>ราคารวม(หลังร้าน)</th></thead>';
			foreach($shops as $key=>$item){
				echo '<thead class="shopname undivide">'.
					'<th colspan="19">ร้าน '.base64_decode($key).'</th></thead>';
				$puncCount = 0;
				for($i=0;$i<sizeof($item);$i++){
					$total = ($item[$i][18]*(double)$item[$i][15])+(double)$item[$i][6];
					$tquan += $item[$i][18];
					$tamount += $total;
					$tthb += $total*$orate;
					
					$cncost = '-';
					if(!empty($item[$i][6])){
						$cncost = (double)$item[$i][6];
					}
					//สถานะ
					$option = '<option value="0">-</option><option value="1">ได้</option><option value="2">ไม่ได้</option>';
					$status = '-';
					if($item[$i][9]===1){
						$status = 'ได้';
						$option = '<option value="0">-</option><option value="1" selected>ได้</option><option value="2">ไม่ได้</option>';
					}else if($item[$i][9]===2){
						$status = 'ไม่ได้';
						$option = '<option value="0">-</option><option value="1">ได้</option><option value="2" selected>ไม่ได้</option>';
					}
					
					echo '<tr class="'.($puncCount%2==0? '':'punc').'">'.
						'<td><img height="150" width="150" src="'.$item[$i][13].'"/></td><td>'.$item[$i][2].'</td><td>'.(double)$item[$i][15].'</td><td>'.$cncost.'</td><td>'.number_format($total).
						'</td><td>'.$orate.'</td><td>'.number_format($total*$orate).'</td><td>'.$status.'</td>'.
						'<td><textarea class="track" id="ref-'.$item[$i][0].'">'.$item[$i][5].'</textarea></td><td><a target="_blank" href="'.$item[$i][14].'">'.$item[$i][14].'</a></td>'.
						'<td><select id="stt-'.$item[$i][0].'">'.$option.'</select></td>'.
						'<td><textarea id="cause-'.$item[$i][0].'">'.$item[$i][7].'</textarea></td><td><input class="tquan" id="quan-'.$item[$i][0].
						'" value="'.$item[$i][18].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');"/></td>'.
						'<td><input id="cpp-'.$item[$i][0].'" value="'.(double)$item[$i][1].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');"/>'.
						'</td><td><input id="cost-'.$item[$i][0].'" value="'.(double)$item[$i][6].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');"/></td>'.
						'<td '.($status=='ไม่ได้'?'':'class="tamount"').' id="t-'.$item[$i][0].'">'.number_format(($item[$i][18]*(double)$item[$i][1])+(double)$item[$i][6]).'</td>'.
						'<td><input id="bp-'.$item[$i][0].'" value="'.(double)$item[$i][11].'" onkeyup="this.value=numberify(this.value);bcalc(\'quan-'.$item[$i][0].'\',\'bp-'.$item[$i][0].'\',\'bcost-'.$item[$i][0].'\',\'bt-'.$item[$i][0].'\');"/>'.
						'</td><td><input id="bcost-'.$item[$i][0].'" value="'.(double)$item[$i][12].'" onkeyup="this.value=numberify(this.value);bcalc(\'quan-'.$item[$i][0].'\',\'bp-'.$item[$i][0].'\',\'bcost-'.$item[$i][0].'\',\'bt-'.$item[$i][0].'\');"/></td>'.
						'<td '.($status=='ไม่ได้'?'':'class="tshop"').' id="bt-'.$item[$i][0].'">'.number_format(((double)$item[$i][18]*(double)$item[$i][11])+(double)$item[$i][12]).'</td>'.
						'</tr>';
					$puncCount++;
				}
				//echo '</table>';
			}
			echo '<tbody class="padding"><td class="cancel">ยอดรวม</td><td>'.number_format($tquan).'</td><td></td><td></td><td>'.number_format($tamount).'</td>'.
					'<td></td><td>'.number_format($tthb).'</td><td></td><td></td><td></td><td></td><td></td><td id="tquan"></td>'.
					'<td></td><td></td><td id="tamount"></td><td></td><td></td><td id="tshop"></td></tbody></table>';
			
		?>
		<br><br>
		<div style="width:300px;left:0;right:0;margin-left:auto;margin-right:auto;">
			<button class="order-button" onclick="save()">บันทึก</button>
			<button class="order-cancel" onclick="cancel()">ยกเลิก</button>
		</div>
		<br>
		<h3>ค่าขนส่งจีน-ไทย</h3>
		<table class="shipping">
			<tr><th>ร้านค้า</th><th>Trackingจีน</th><th>ค่าตีลังไม้</th><th>ขนาด M3</th><th>น้ำหนัก Kg.</th><th>Rate</th><th>ยอดรวม(บาท)</th></tr>
			<?php
				$tbox = 0;
				$tm3 = 0;
				$tkg = 0;
				$puncCount = 0;
				
				function trackingBundle($shop){
					$str = '';
					if(sizeof($shop)>0){
						$str = $shop[0][5];
						for($i=1;$i<sizeof($shop);$i++){
							$str .= ','.$shop[$i][5];
						}
					}
					return $str;
				}
				
				foreach($m3 as $key=>$item){
					echo '<tr class="'.($puncCount%2==0? 'punc':'').'">'.
						'<td>'.base64_decode($key).'</td><td id="cn-ref-'.$key.'" class="china break">'.trackingBundle($shops[$key]).'</td>'.
						'<td><input id="cn-box-'.$key.'" class="china" value="'.$shops[$key][0][16].'" onkeyup="this.value=numberify(this.value);calc2(\''.$key.'\');"/></td>'.
						'<td><input id="cn-m3-'.$key.'" class="china" value="'.$item.'" onkeyup="this.value=numberify(this.value);"/></td>'.
						'<td><input id="cn-kg-'.$key.'" class="china" value="'.$kg[$key].'" onkeyup="this.value=numberify(this.value);calc2(\''.$key.'\');"/></td>'.
						'<td><input id="cn-rate-'.$key.'" class="china" value="'.$shops[$key][0][17].'" onkeyup="this.value=numberify(this.value);calc2(\''.$key.'\');"/></td>'.
						'<td id="cn-total-'.$key.'">'.number_format(($kg[$key]*$shops[$key][0][17])+$shops[$key][0][16]).'</td></tr>';
					$tbox += $shops[$key][0][16];
					$tm3 += $item;
					$tkg += $kg[$key];
					$puncCount++;
				}
				echo '<tr class="padding"><td>ยอดรวม</td><td></td><td id="cn-tbox">'.$tbox.'</td><td id="cn-tm3">'.$tm3.'</td><td id="cn-tkg">'.$tkg.'</td><td></td><td id="cn-total"></td></tr>';
			?>
		</table>
		<br><h3>ค่าขนส่งภายในประเทศ</h3>
		<table class="shipping">
			<tr><th>บริการขนส่งในประเทศ</th><th>วันที่ส่งสินค้า</th><th>Trackingไทย/เลขที่บิล</th><th>น้ำหนัก Kg.</th><th>ค่าขนส่ง(บาท)</th></tr>
			<tr class="punc"><th><?php echo $shipping;?></th>
			<?php
			echo '<td><input id="th-date" class="china datepicker" value="'.substr($th_date,-2).'-'.substr($th_date,5,2).'-'.substr($th_date,0,4).'"/></td>'.
				'<td><input id="th-ref" class="china" value="'.$th_ref.'"/></td>'.
				'<td><input id="th-kg" class="china" value="'.$th_kg.'" onkeyup="this.value=numberify(this.value);"/></td>'.
				'<td><input id="th-cost" class="china" value="'.$th_cost.'" onkeyup="this.value=numberify(this.value);"/></td></tr>'.
				'<tr><th></th><td></td><td></td><td class="cancel">ยอดค่าขนส่ง(รอบที่2)</td><td class="cancel" id="th-total2">-</td></tr>';
			?>
		</table>
		<br><br><br>
		
	</body>
</html>
<?php
	$con->close();
?>