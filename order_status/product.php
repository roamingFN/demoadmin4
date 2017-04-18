<?php
	echo '<a name="top"></a>';
	session_start();
	if (!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
	
	include '../database.php';
	include '../utility/permission.php';
	const FORMID = 4;
	$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
	$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
	if ($_adminFlg==0) {
			if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
	}

	if(isset($_GET['order_id'])) {
		$oid = $_GET['order_id'];
		$shops = array();
		$save = array();
		$_shop = array();
		$_byShop = array();
		
		$rate1 = 1;
		$ono1 = '';
		$cid1 = '';
		$cname1 = '';
		$cmail1 = '';
		$code1 = 0;
		$codes = array();
		$_remark = array();
		$_ptype = array();
		$_oDate = array();
		$_ratedt = '';
		$_adminFlg = '';
		
		if($stmt = $con->prepare('SELECT order_product_id, confirmed_product_price, quantity, order_shipping_cn_m3_size, order_shipping_cn_weight,
			order_shipping_cn_ref_no, customer_order_product.order_shipping_cn_cost, comment, unconfirmed_product_order,order_status,order_cause,backshop_price,backshop_shipping_cost,
			shop_name,product_img,product_url,product_price,order_shipping_cn_box,order_shipping_rate,
			product_size,product_color,comment,product.product_id,product.product_url,unitprice,remark_id,producttypeid,first_unitprice,first_unitquantity,chkflg
			FROM customer_order_product,product,customer_order_shipping WHERE customer_order_product.product_id=product.product_id AND customer_order_product.order_id='.$oid.' AND '.
			'customer_order_shipping.order_id='.$oid)){
			$stmt->execute();
			$stmt->bind_result($opid,$cpp,$quan,$size,$weight,$ref,$cost,$comment,$upo,$status,$cause,$bp,$bcost,$shop,$img,$url,$pp,$box,$osr,$pSize,$pColor,$comment,$pid,$purl,$unp,$rid,$ptid,$fuprice,$fuquan,$chkflg);
			while($stmt->fetch()){
				$enc = base64_encode($shop);
				if(!isset($shops[$enc])){
					$shops[$enc] = array();
				}
				array_push($shops[$enc],array($opid,$cpp,$quan,$size,$weight,$ref,$cost,$comment,$upo,$status,$cause,$bp,$bcost,$img,$url,$pp,$box,$osr,$pSize,$pColor,$comment,$pid,$purl,$unp,$rid,$ptid,$fuprice,$fuquan,$chkflg));
				array_push($save,array($opid,$enc));
				//for total by shop
				array_push($_shop,array($opid,$shop));

				//for by shop
				if (!isset($_byShop[$enc])) $_byShop[$enc][0] = $opid;
				else array_push($_byShop[$enc],$opid);
			}
		}
		echo $con->error;
		
		//get rate, order number, customer info
		$sql = 'SELECT order_rate,order_number,order_status_code,'.
			'customer.customer_id,customer.customer_firstname,customer.customer_lastname,customer.customer_email'.
			',date_order_created,order_rate_date,customer_note,user_note'.
			' FROM customer_order JOIN customer ON customer.customer_id=customer_order.customer_id'. 
			' WHERE order_id='.$oid;
		if($stmt = $con->prepare($sql))
		$stmt->execute();
		$stmt->bind_result($rate,$ono,$ostat,$cid,$cfname,$clname,$cmail,$odate,$ratedt,$cnote,$unote);
		while ($stmt->fetch()) {
				$rate1 = $rate;
				$ono1 = $ono;
				$code1 = $ostat;
				$cid1 = $cid;
				$cname1 = $cfname.' '.$clname;
				$cmail1 = $cmail;
				$_oDate = $odate;
				$_ratedt = $ratedt;
				$_cnote = $cnote;
				$_unote = $unote;
		}

		//get status description
		if($stmt = $con->prepare('SELECT status_id,des FROM order_status_code')){
				$stmt->execute();
				$stmt->bind_result($id,$des);
				while($stmt->fetch()){
						$codes[$id] = $des;
				}
		}

		//get Admin flag
		if($stmt = $con->prepare('SELECT flag_admin FROM user WHERE uid=\''.$_SESSION['ID'].'\'')){
				$stmt->execute();
				$stmt->bind_result($flg);
				while($stmt->fetch()){
						$_adminFlg = $flg;
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

		//get product type
		$_ptype[0] = "-";
		if($stmt = $con->prepare('SELECT producttypeid,producttypename,rate_type,product_type FROM product_type ORDER BY producttypename')){
				$stmt->execute();
				$stmt->bind_result($ptid,$ptname,$rate,$type);
				while($stmt->fetch()){
						$_ptype[$ptid] = $ptname;
				}
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link rel="stylesheet" type="text/css" href="../css/w3-orange.css">
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<script src="../js/jquery-1.10.2.js"></script>
		<script src="../js/jquery-ui.js"></script>
		<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
		<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.jquery.js"></script>
		<script src="./controller.js"></script>
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
			var tranCost = Number(document.getElementById('tran-'+id).value.replace(/,/g, ''));
			if (isNaN(tranCost)) {
				tranCost = 0;
			}
			document.getElementById('totalCn-'+id).textContent = numWithCom(
				(Number(document.getElementById('quan-'+id).value.replace(/,/g, ''))*Number(document.getElementById('cpp-'+id).value.replace(/,/g, '')))+tranCost
			);
			document.getElementById('totalTh-'+id).textContent = numWithCom(
				((Number(document.getElementById('quan-'+id).value.replace(/,/g, ''))*Number(document.getElementById('cpp-'+id).value.replace(/,/g, '')))+tranCost)*Number(rate1)
			);
		}
		
		var orderId = <?php echo $oid; ?>;
		function save() {

			var data = {};
			data['oid'] = orderId;
			data['status'] = document.getElementById('status-'+orderId).options[document.getElementById('status-'+orderId).selectedIndex].value;;
			
			var result = true;
			var xhr = new XMLHttpRequest();
			xhr.open('POST','save_product.php',true);
			xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			document.getElementById("loading").style.visibility = 'hidden';
			xhr.onreadystatechange = function(){
				if(xhr.readyState==4 && xhr.status==200){
					if(xhr.responseText=='success'){
                        alert("บันทึกข้อมูลเรียบร้อยแล้ว");
						location.reload();
					}else{
						//alert('กรุณาใส่ข้อมูลให้ถูกต้องค่ะ!');
						alert(xhr.responseText);
						location.reload();
					}
				}
			};
			document.getElementById("loading").style.visibility = 'visible';
			xhr.send('data='+JSON.stringify(data));
		}
		
		function cancel(){
			window.location = 'index.php';
		}
		
		var orderNum = <?php echo '"'.$ono1.'"'; ?>;
		var cusId = <?php echo $cid1; ?>;
		var cusName = <?php echo '"'.$cname1.'"'; ?>;
		var cusMail = <?php echo '"'.$cmail1.'"'; ?>;

		function numberify(txt){
			return txt.replace(/[^0-9.]/g,'');
		}

		function numberifyWithOutDot(txt){
			return txt.replace(/[^0-9.]/g,'').replace(/\./g,'');
		}

		function checkZero(val,id) {
				if (val==0) {
					document.getElementById(id).value = '';
				}
		}

		var selectFlg = 1;
		function selectAll() {
				selectFlg = !selectFlg;
				var save = [];
				<?php 
				for($i=0;$i<sizeof($save);$i++){
						echo 'save.push("'.$save[$i][0].'");';
				}
				?>
				for(var i=0;i<save.length;i++) {
						document.getElementById('ck-'+save[i]).checked = selectFlg;
				}
		}

		setInterval(function(){
			var totalfQuan = 0;
			var totalQuan = 0;
			var totalTran = 0;
			var totalCn = 0;
			var totalTh = 0;
			//total by shop------------------------------------------------------------------------
			var shop = [];
			var opid = [];

			<?php 
				for($i=0;$i<sizeof($_shop);$i++) {
					echo 'opid.push("'.$_shop[$i][0].'");';
					echo 'shop.push("'.$_shop[$i][1].'");';
				}
			?>
			
			var sumCn = [];
			var sumTh = [];
			var sumfQuan = [];
			var sumQuan = [];
			var sumTran = [];
			//set
			for(var i=0;i<shop.length;i++) {
					sumfQuan[shop[i]] = 0;
					sumQuan[shop[i]] = 0;
					sumTran[shop[i]] = 0;
					sumCn[shop[i]] = 0;
					sumTh[shop[i]] = 0;
			}
			//cal
			for(var i=0;i<opid.length;i++) {
					//ck-opid
					var ck = document.getElementById('ck-'+opid[i]).checked;
					if (ck) {
						//first 
						var fquan = numberify(document.getElementById("quan1-"+opid[i]).value);
						sumfQuan[shop[i]]+=Number(fquan);

						//quan
						var quan = numberify(document.getElementById("quan-"+opid[i]).value);
						sumQuan[shop[i]]+=Number(quan);
						//tran
						var tran = numberify(document.getElementById("tran-"+opid[i]).value);
						sumTran[shop[i]]+=Number(tran);
						//amt
						var amtCn = numberify(document.getElementById("totalCn-"+opid[i]).textContent);
						sumCn[shop[i]]+=Number(amtCn);
						//amt
						var amtTh = numberify(document.getElementById("totalTh-"+opid[i]).textContent);
						sumTh[shop[i]]+=Number(amtTh);

						//grand total
						totalfQuan+=Number(fquan);
						totalQuan+=Number(quan);
						totalTran+=Number(tran);
						totalCn+=Number(amtCn);
						totalTh+=Number(amtTh);		
					}
			}
			//show
			for(var i=0;i<shop.length;i++) {
					document.getElementById('tfquan-'+shop[i]).textContent = sumfQuan[shop[i]];
					document.getElementById('tquan-'+shop[i]).textContent = sumQuan[shop[i]];
					document.getElementById('ttran-'+shop[i]).textContent = numWithCom(sumTran[shop[i]]);
					document.getElementById('tamtcn-'+shop[i]).textContent = numWithCom(sumCn[shop[i]]);
					document.getElementById('tamtth-'+shop[i]).textContent = numWithCom(sumTh[shop[i]]);
			}
			
			document.getElementById('tfquan').textContent = totalfQuan;
			document.getElementById('tquan').textContent = totalQuan;
			document.getElementById('tTran').textContent = numWithCom(totalTran);
			document.getElementById('tamountCn').textContent = numWithCom(totalCn);
			document.getElementById('tamountTh').textContent = numWithCom(totalTh);
		},500);

		var selectShopFlg = [];
		//init select all product in shop
		function selectByShop(shopid) {	
				if (typeof selectShopFlg[shopid] == 'undefined') {
    					selectShopFlg[shopid] = 1;
				}
				else {
						selectShopFlg[shopid] = !selectShopFlg[shopid];
				}
				var save = [];
				<?php 
						for($i=0;$i<sizeof($save);$i++){
								echo 'save.push("'.$save[$i][0].'");';
						}
				?>
				for(var i=0;i<save.length;i++) {
						if (document.getElementById('shopname-'+save[i]).value==shopid) {
								document.getElementById('ck-'+save[i]).checked = selectShopFlg[shopid];
						}
				}
		}

	</script>

	<body>
		<h2 style="color:#FF9900"><b><a href="product.php?order_id=<?php echo $oid?>">สถานะรายการ</a></b></h2>
        <h3 style="color:#FF9900"><a href="index.php">&larr; Back</a>  <a href="../index.php">&larr; Home</a></h3><br>
        <div class="menu">
			<i class="material-icons" onclick="exportProduct(<?php echo $oid;?>);" title="Export">&#xE24D;</i>
		</div>
        <table class="order-results" style="width:800px;">
			<tr>
				<td>เลขที่ออเดอร์  :</td><td id="order-id"><?php echo $ono1; ?></td>
				<td>วันที่ออเดอร์  :</td><td><?php echo $_oDate; ?></td>
			</tr>
			<tr class="punc">
				<td>สถานะรายการ :</td>
				<td><?php //echo $codes[$code1];
					echo '<select class="search-select" id="status-'.$oid.'">';
					foreach($codes as $key=>$item) {
						if($key==$code1) echo '<option value="'.$key.'" selected> '.$key.' - '.$item.'</option>';
						else echo '<option value="'.$key.'"> '.$key.' - '.$item.'</option>';
					} 
					echo '</select>';
					?>
						
				</td>
				<td>Rate  :</td><td><?php echo number_format($rate1,4).'@'.(($_ratedt!='0000-00-00 00:00:00')? date_format(date_create($_ratedt),"d/m/Y H:i:s"): ''); ?></td>
			</tr>
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
			$firstShop = 1;
			$grandfquan = 0;

		//begin table
			foreach($shops as $key=>$item) {
				echo '<table class="order-product">';
				echo '<thead>';
				if ($firstShop) {
					echo '<th width="5%"></th>';
					$firstShop = 0;
				}
				else echo '<th width="5%"></th>';
				echo '<th width="2%">ลำดับ</th>'.
					'<th width="8%">ภาพตัวอย่าง</th>'.
					'<th width="5%">ขนาด</th>'.
					'<th width="5%">สี</th>'.
					'<th width="8%">ประเภทสินค้า</th>'.
					'<th width="7%">จำนวนที่ลูกค้าสั่ง</th>'.
					'<th width="7%">จำนวนที่สั่งได้</th>'.
					'<th width="7%">ราคา/ชิ้น (หยวน)</th>'.
					'<th width="7%">ราคาแก้ไข (หยวน)</th>'.
					'<th width="7%">ค่าขนส่งในจีน (หยวน)</th>'.
					'<th>รวม (หยวน)</th>'.
					'<th>รวม (บาท)</th>'.
					'<th>สถานะการสั่ง</th>'.
					'<th width="10%">หมายเหตุ</th>'.
					'</thead>';
				echo '<tbody class="shopname undivide">'.
					'<th colspan="19">ร้าน '.base64_decode($key).'</th></tbody>';
				$shopid = base64_decode($key);
				$sumfquan = 0;
				$puncCount = 0;
				for($i=0;$i<sizeof($item);$i++){
					$total = ($item[$i][2]*(double)$item[$i][23])+(double)$item[$i][6];
					$tquan += $item[$i][2];
					$tamount += $total;
					$tthb += $total*$orate;
					if (empty($item[$i][6])) {
						$cncost=0;
					}
					else {
						$cncost = (double)$item[$i][6];
					}
					$tCnCost += $cncost;

					//order product id
					$opid = $item[$i][0];

					//sum of first unit quantity
					$sumfquan += $item[$i][27];
					$grandfquan += $sumfquan;

					//order status========================================
					$o_status = $item[$i][9];
					if ($o_status==0) {
						$option = '<div style="display:inline">'.
									'<input disabled style="width:auto" type="checkbox" value="1" id="stt1-'.$opid.'" onclick="checkStat1('.$opid.')"><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled style="width:auto" type="checkbox" value="2" id="stt2-'.$opid.'" onclick="checkStat2('.$opid.')"><label> ไม่ได้</label>'.
								'</div>';
					}
					else if ($o_status==1) {
						$option = '<div style="display:inline">'.
									'<input disabled style="width:auto" type="checkbox" value="1" id="stt1-'.$opid.'" onclick="checkStat1('.$opid.')" checked><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled style="width:auto" type="checkbox" value="2" id="stt2-'.$opid.'" onclick="checkStat2('.$opid.')"><label> ไม่ได้</label>'.
								'</div>';
					}
					else {
						$option = '<div style="display:inline">'.
									'<input disabled style="width:auto" type="checkbox" value="1" id="stt1-'.$opid.'" onclick="checkStat1('.$opid.')"><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled style="width:auto" type="checkbox" value="2" id="stt2-'.$opid.'" onclick="checkStat2('.$opid.')" checked><label> ไม่ได้</label>'.
								'</div>';
					}

					//chkflg
					$chkflg = '';
					if ($item[$i][28]) {
							$chkflg = ' checked';
					}

					echo '<tr class="'.($puncCount%2==0? 'punc ':'').($o_status==0? 'cancel ':'').'">'.
						'<td width="2%"><input disabled type="checkbox" id="ck-'.$opid.'"'.$chkflg.'></td>'.
						'<td align="center">'.$no.'</td>'.
						'<td><div style="float:left;"><a href="showImg.php?pid='.$item[$i][21].'" onclick="window.open(\'showImg.php?pid='.$item[$i][21].'\', \'_balnk\', \'width=1024, height=768\'); return false;"><img height="150" width="150" src="'.$item[$i][13].'"/></a></div>'.
						'<div align="center"><a href="'.$item[$i][22].'" onclick="window.open(\''.$item[$i][22].'\', \'_blank\', \'width=+screen.height,height=+screen.height,fullscreen=yes\'); return false;"><img class="linkImg" height="20" width="20" src="../css/images/link.png"/></a><div></td>'.
						'<td>'.$item[$i][18].'</td>'.
						'<td>'.$item[$i][19].'</td>'.
						'<td><select id="type-'.$opid.'" disabled>';
						foreach ($_ptype as $key => $value) {
								if($key==$item[$i][25]) echo '<option value="'.$key.'" selected>'.$value.'</option>';
								else echo '<option value="'.$key.'">'.$value.'</option>';
						}
						echo '</select></td>'.
						//'<td align="right" id="fquan-'.$opid.'">'.$item[$i][27].'</td>'.
						'<td><input disabled style="text-align:right;" class="tquan" id="quan1-'.$opid.'" value="'.$item[$i][27].'" onkeyup="this.value=numberify(this.value);" onclick="checkZero(this.value,\'quan1-'.$opid.'\')"/></td>'.
						'<td><input disabled style="text-align:right;" class="tquan" id="quan-'.$opid.'" value="'.$item[$i][2].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'quan-'.$opid.'\')"/></td>'.			//quanltity
						'<td id="cpp1-'.$opid.'" align="right">'.number_format($item[$i][26],2).'</td>'.
						'<td><input disabled style="text-align:right;" id="cpp-'.$opid.'" value="'.number_format($item[$i][23],2).'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'cpp-'.$opid.'\')"/></td>'.	//price
						'<td><input disabled style="text-align:right;" class="tTran" id="tran-'.$opid.'" value="'.number_format($cncost,2).'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'tran-'.$opid.'\')"/></td>'.											//transport china cost
						'<td align="right" class="tamount" id="totalCn-'.$opid.'">'.number_format($total,2).'</td>'.							//total chinese
						'<td align="right" id="totalTh-'.$opid.'">'.number_format($total*$orate,2).'</td>'.					//total thai
						//'<td><select id="stt-'.$item[$i][0].'">'.$option.'</select></td>'.
						//'<td><input id="comm-'.$item[$i][0].'" value="'.$item[$i][20].'"/></td>'.				//comment	
						'<td align="center">'.$option.'</td>'.
						'<td><select disabled id="rem-'.$opid.'">';
						foreach ($_remark as $key => $value) {
								if($key==$item[$i][24]) echo '<option value="'.$key.'" selected>'.$value.'</option>';
								else echo '<option value="'.$key.'">'.$value.'</option>';
						}

						echo '</select></td>'.
						'<input type="hidden" id="keepQuan-'.$opid.'" value='.$item[$i][2].'>'.
						'<input type="hidden" id="keepTran-'.$opid.'" value='.$cncost.'>'.
						'<input type="hidden" id="shopname-'.$opid.'" value="'.$key.'" />'.
						'</tr>';
						$puncCount++;
						$no++;
				}
				//total shop
				echo '<tbody class="padding">'.
				'<td></td><td colspan="5" class="cancel">ยอดรวม</td>'.
				'<td align="right" id="tfquan-'.$shopid.'">'.$sumfquan.'</td>'.
				'<td align="right" id="tquan-'.$shopid.'">'.$tquan.'</td><td></td><td></td>'.
				'<td align="right" id="ttran-'.$shopid.'">'.number_format($tCnCost,2).'</td>'.
				'<td align="right" id="tamtcn-'.$shopid.'">'.number_format($tamount,2).'</td>'.
				'<td align="right" id="tamtth-'.$shopid.'">'.number_format($tthb,2).'</td>'.
				'<td</td><td></td><td></td></tbody><br>';
			}
		
		//grand total
		echo '<tbody class="padding" style="font-size:14px;">'.
			'<td></td><td class="cancel" colspan="2">ยอดรวมทั้งหมด</td><td></td><td></td><td></td>'.
			'<td align="right" id="tfquan">'.$grandfquan.'</td>'.
			'<td align="right" id="tquan"></td>'.
			'<td></td><td></td>'.
			'<td align="right" id="tTran"></td>'.
			'<td align="right" id="tamountCn"></td>'.
			'<td align="right" id="tamountTh"></td>'.
			'<td></td><td></td></tbody></table><br>';

		//comment
		echo '<div style="text-align:center;overflow:hidden;position:relative;display:table;width:100%;">';
				echo '<div style="width:50%;display:table-cell;"><span style="vertical-align:top;font-weight:bold;">Customer note : </span><span><textarea style="font-size:16px;width:60%;height:100px;" readonly>'.$_cnote.'</textarea></span></div>';
				echo '<div style="width:50%;display:table-cell;"><span style="vertical-align:top;font-weight:bold;">User note : </span><span><textarea id="unote" style="font-size:16px;width:60%;height:100px;" readonly>'.$_unote.'</textarea></span></div>';
		echo '</div>';

			
?>
		<br>
		<div><a href="#top">↑กลับสู่ด้านบน</a><div>
		<br><br>
		<div align="center" style="width:600px;left:0;right:0;margin-left:auto;margin-right:auto;">
			<button class="order-button" onclick="save()">บันทึก</button>';
			<button class="order-cancel" onclick="cancel()">กลับ</button>
		</div>
		<br>
		</body>
</html>
<script>
		$('.search-select').chosen({
			width: "100%"
		});
</script>
<?php
		include './dialog/loading.php';
		$con->close();
?>