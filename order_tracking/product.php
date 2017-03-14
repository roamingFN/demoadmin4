<?php
	session_start();
	if (!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';
	include '../order_buy/function.php';

	function getRecieved($oid,$opid) {
		include '../database.php';
		$result = 0;
		$sql = 'SELECT SUM(received_amount) FROM customer_order_product_tracking WHERE order_product_id='.$opid.' AND order_id='.$oid;
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($r);
		while($stmt->fetch()) {
			$result = $r;
		}
		return $result;
	}
	
	if(isset($_GET['order_id'])) {
		$oid = $_GET['order_id'];
		$shops = array();
		$save = array();
		$shopname = array();

		//add tracking
		if(isset($_POST['add'])) {
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
                $curr = $_POST['tracking_curr'];
                $res = updTracking($oid,$opid,$allTrack,$curr);
                header("Refresh:0");
		}
		
		//get product order
		if ($stmt = $con->prepare('SELECT order_product_id,confirmed_product_price,quantity,order_shipping_cn_m3_size,order_shipping_cn_weight,'.
			'order_shipping_cn_ref_no,customer_order_product.order_shipping_cn_cost,comment,unconfirmed_product_order,order_status,order_cause,backshop_price,backshop_shipping_cost,'.
			'shop_name,product_img,product_url,product_price,order_shipping_cn_box,order_shipping_rate,'.
			'product_size,product_color,comment,product.product_id,product.product_url,unitprice,'.
			'backshop_quantity,backshop_total_price,order_product_totalprice'.
			' FROM customer_order_product,product,customer_order_shipping WHERE customer_order_product.product_id=product.product_id AND customer_order_product.order_id='.$oid.' AND '.
			'customer_order_shipping.order_id='.$oid)) {
			$stmt->execute();
			$stmt->bind_result($opid,$cpp,$quan,$size,$weight,$ref,$cost,$comment,$upo,$status,$cause,
				$bp,$bcost,$shop,$img,$url,$pp,$box,$osr,$pSize,$pColor,$comment,$pid,$purl,$unp,$backQuan,$backTot,
				$opTot);
			while($stmt->fetch()){
				$enc = base64_encode($shop);
				if(!isset($shops[$enc])){
					$shops[$enc] = array();
				}
				array_push($shops[$enc],array($opid,$cpp,$quan,$size,$weight,$ref,$cost,$comment,$upo,$status,$cause,
					$bp,$bcost,$img,$url,$pp,$box,$osr,$pSize,$pColor,$comment,$pid,$purl,$unp,$backQuan,$backTot,
					$opTot));
				array_push($save,array($opid,$enc));
				$shopname[$opid] = $shop;			//shopname
			}
		}
		echo $con->error;

		//get product tracking List
		$tracking = array();
		$sql = 'SELECT pt.order_product_tracking_id,pt.order_product_id,pt.order_id,pt.tracking_no,pt.width,pt.length,pt.height,pt.m3,pt.weight,pt.rate,pt.total,pt.statusid'.
			',o.taobao'.
			',op.producttypeid,t.rate_type,t.product_type'.
			',pt.type'.
			' FROM customer_order_product_tracking pt'.
			' JOIN customer_order o ON pt.order_id=o.order_id'.
			' JOIN customer_order_product op on op.order_product_id=pt.order_product_id'.
			' LEFT JOIN product_type t ON op.producttypeid=t.producttypeid'.
			' WHERE pt.order_id='.$oid.
			' ORDER BY pt.tracking_no ASC';
		//echo $sql;
		if ($stmt = $con->prepare($sql)) {
				$stmt->execute();
				$stmt->bind_result($proTrck,$opID,$oID,$trckNo,$width,$length,$height,$m3,$weight,$rate,$total,$stat,$taobao,$ptid,$rtype,$ptype,$type);
				while ($stmt->fetch()) {
						$tmpObj = new stdClass();
						$tmpObj->trckID = $proTrck;
						$tmpObj->opID = $opID;
						$tmpObj->oID = $oID;
						$tmpObj->trckNo = $trckNo;
						$tmpObj->width = $width;
						$tmpObj->length = $length;
						$tmpObj->height = $height;
						$tmpObj->m3 = $m3;
						$tmpObj->weight = $weight;
						$tmpObj->rate = $rate;
						$tmpObj->total = $total;
						$tmpObj->stat = $stat;
						$tmpObj->box = $taobao;
						$tmpObj->ptID = $ptid;
						$tmpObj->rType = $rtype;
						$tmpObj->pType = $ptype; 
						$tmpObj->type = $type;
						array_push($tracking, $tmpObj);
				}
		}

		//get product tracking Detail
		$_trackingDetail = array();
		$_m3Total = 0;
		$_tranTotal = 0;
		$sql = 'SELECT pt.order_product_tracking_id,pt.order_product_id,pt.order_id,pt.tracking_no,pt.m3,pt.weight,pt.rate,pt.total,pt.received_amount,pt.uid'.
			',op.quantity,op.order_shipping_cn_cost,op.unitprice'.
			',p.product_name,p.product_img,p.product_url'.
			' FROM customer_order_product_tracking pt'.
			' JOIN customer_order_product op ON pt.order_product_id=op.order_product_id'.
			' JOIN product p ON op.product_id=p.product_id'.
			' WHERE pt.order_id='.$oid.
			' ORDER BY pt.tracking_no ASC';
		if ($stmt = $con->prepare($sql)) {
				$stmt->execute();
				$stmt->bind_result($proTrck,$opID,$oID,$trckNo,$m3,$weight,$rate,$total,$recAmount,$uid,$quan,$tran,$price,$pname,$pimg,$purl);
				while ($stmt->fetch()) {
						$tmpObj = new stdClass();
						$tmpObj->trckID = $proTrck;
						$tmpObj->opID = $opID;
						$tmpObj->oID = $oID;
						$tmpObj->trckNo = $trckNo;
						$tmpObj->m3 = $m3;
						$tmpObj->weight = $weight;
						$tmpObj->rate = $rate;
						$tmpObj->total = $total;
						$tmpObj->rec = $recAmount;
						$tmpObj->uid = $uid;
						$tmpObj->quan = $quan;
						$tmpObj->price = $price;
						$tmpObj->pname = $pname;
						$tmpObj->pimg = $pimg;
						$tmpObj->purl = $purl;
						array_push($_trackingDetail, $tmpObj);
						$_m3Total += $m3;
						$_tranTotal += $tran;
				}
		}
		//==============================================================================================
		$_rate = 1;
		$_cid = '';
		$_cmail = '';
		$code1 = 0;
		$_ono = '';
		$_codes = array();
		$_odate = '';
		$_cname = '';
		$_rates = array();
		$_ptype = array();
		$_comStat = 0;
		$_orderRemark = '';
		//get rate, order number, customer info
		$sql = 'SELECT order_rate,order_number,order_status_code,date_order_created'.
			',customer.customer_id,customer.customer_firstname,customer.customer_lastname,customer.customer_email'.
			',rate_type,product_type,rate_amount'.
			',remark,complete_status'.
			' FROM customer_order'. 
			' JOIN customer ON customer.customer_id=customer_order.customer_id'.
			' JOIN customer_class_rate ON customer.class_id=customer_class_rate.class_id'. 
			' WHERE order_id='.$oid;
		if($stmt = $con->prepare($sql)) {
				$stmt->execute();
				$stmt->bind_result($rate,$ono,$ostat,$odate,$cid,$cfname,$clname,$cmail,$rtype,$ptype,$ramount,$remark,$comStat);
				while ($stmt->fetch()) {
						$_rate = $rate;
						$_ono = $ono;
						$code1 = $ostat;
						$_cid = $cid;
						$_cname = $cfname.' '.$clname;
						$_cmail = $cmail;
						$_odate = $odate;
						$_rates[$rtype][$ptype] = $ramount;
						$_comStat = $comStat;
						$_orderRemark = $remark;
				}
		}
		
		//get status description
		if($stmt = $con->prepare('SELECT status_id,des FROM order_status_code')) {
				$stmt->execute();
				$stmt->bind_result($id,$des);
				while($stmt->fetch()){
						$_codes[$id] = $des;
				}
		}

		//get remark description
		if($stmt = $con->prepare('SELECT remark_id,remark_tha FROM order_remark')) {
				$stmt->execute();
				$stmt->bind_result($rid,$rth);
				while($stmt->fetch()){
						$_remark[$rid] = $rth;
				}
		}

		//get product type
		$_ptype[0] = "-";
		if($stmt = $con->prepare('SELECT producttypeid,producttypename,rate_type,product_type FROM product_type')){
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
		<link rel="stylesheet" type="text/css" href="../css/w3-lime.css">
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

		var rate = <?php echo $_rate ?>;
		function calM3(id) {
				var width = document.getElementById('width-'+id).value;
				var length = document.getElementById('length-'+id).value;
				var height = document.getElementById('height-'+id).value;
				var rate = document.getElementById('rate-'+id).value;
				document.getElementById('m3-'+id).textContent = numWithCom((Number(width) * Number(length) * Number(height))/Math.pow(10,6));

				var m3 = document.getElementById('m3-'+id).textContent;
				var type = document.getElementsByName('type-'+id);
				if(type[0].checked) {
						if (m3!=0) {
							document.getElementById('avg-'+id).textContent = numWithCom((Number(rate)*Number(m3))/Number(m3));
						}
						else {
							document.getElementById('avg-'+id).textContent = "0.00";
						}
						document.getElementById('total-'+id).textContent = numWithCom(Number(m3)*Number(rate));
				}
		}

		function calWeight(id) {
				var weight = document.getElementById('weight-'+id).value;
				var rate = document.getElementById('rate-'+id).value;
				var m3 = document.getElementById('m3-'+id).textContent;
				var type = document.getElementsByName('type-'+id);
				if(type[1].checked) {
						if (m3!=0) {
							document.getElementById('avg-'+id).textContent = numWithCom((Number(rate)*Number(weight))/Number(m3));
						}
						else {
							document.getElementById('avg-'+id).textContent = "0.00";
						}
						document.getElementById('total-'+id).textContent = numWithCom(Number(weight)*Number(rate));
				}
		}

		function calAvg(id) {
				var weight = document.getElementById('weight-'+id).value;
				var rate = document.getElementById('rate-'+id).value;

				var type = document.getElementsByName('type-'+id);
				if(type[1].checked) {
						document.getElementById('total-'+id).textContent = Number(weight)*Number(rate);
				}
		}
		/*
		function calRate(id) {
				var m3 = document.getElementById('m3-'+id).textContent;
				var weight = document.getElementById('weight-'+id).value;
				var rate = document.getElementById('rate').value;
				var type = document.getElementsByName('type-'+id);
				if(type[0].checked) {
						document.getElementById('total-'+id).textContent = Number(m3)*Number(rate);
				}
				if(type[1].checked) {
						document.getElementById('total-'+id).textContent = Number(weight)*Number(rate);
				}

				var setRate = document.getElementsByName('rate');
				for(var i=0;i<setRate.length;i++) {
					setRate[i].value = rate;

				}
		}*/

		function calRate(id) {
				var rate = document.getElementById('rate-'+id).value;
				//var setRate = document.getElementsByName('rate');
				//for(var i=0;i<setRate.length;i++) {
				//	setRate[i].value = rate;
				//}

				/*var id = [];
				<?php
					for($i=0;$i<sizeof($tracking);$i++) {
						echo 'id.push("'.$tracking[$i]->trckID.'");';
					}
				?>
				for(var i=0;i<id.length;i++) {
						var m3 = document.getElementById('m3-'+id[i]).textContent;
						var weight = document.getElementById('weight-'+id[i]).value;
						var type = document.getElementsByName('type-'+id[i]);
						if(type[0].checked) {
								document.getElementById('total-'+id[i]).textContent = Number(m3)*Number(rate);
						}
						if(type[1].checked) {
								document.getElementById('total-'+id[i]).textContent = Number(weight)*Number(rate);
						}
				}*/
				var m3 = document.getElementById('m3-'+id).textContent;
				var weight = document.getElementById('weight-'+id).value;
				var type = document.getElementsByName('type-'+id);
				if(type[0].checked) {
						document.getElementById('total-'+id).textContent = numWithCom(Number(m3)*Number(rate));
				}
				if(type[1].checked) {
						document.getElementById('total-'+id).textContent = numWithCom(Number(weight)*Number(rate));
				}
		}

		function changeTypeM3(id) {
				var m3 = document.getElementById('m3-'+id).textContent;
				var rate = document.getElementById('rate-'+id).value;
				
				document.getElementById('total-'+id).textContent = numWithCom(Number(m3)*Number(rate));
				if (m3!=0) {
					document.getElementById('avg-'+id).textContent = numWithCom((Number(m3)*Number(rate))/Number(m3));
				}
				else {
					document.getElementById('avg-'+id).textContent = numWithCom(0);
				}
		}

		function changeTypeWeight(id) {
				var m3 = document.getElementById('m3-'+id).textContent;
				var weight = document.getElementById('weight-'+id).value;
				var rate = document.getElementById('rate-'+id).value;

				document.getElementById('total-'+id).textContent = numWithCom(Number(weight)*Number(rate));
				if (m3!=0) {
					document.getElementById('avg-'+id).textContent = numWithCom((Number(weight)*Number(rate))/Number(m3));
				}
				else {
					document.getElementById('avg-'+id).textContent = numWithCom(0);
				}
		}

		function calDetail(id){
				var quan = document.getElementById('quan-'+id).textContent;
				var rec = document.getElementById('rec-'+id).value;
				var price = document.getElementById('price-'+id).value;
				
				document.getElementById('quan2-'+id).textContent = (Number(quan)-Number(rec));
				document.getElementById('refund-'+id).textContent = (Number(quan)-Number(rec))*Number(price);
		}
		
		var oid = <?php echo $oid; ?>;
		var _uid = <?php echo "'".$_SESSION['ID']."'"; ?>;
		function save(){
			//backshop
			var save_tracking = [];
			var data_tracking = {};
			<?php
				for($i=0;$i<sizeof($save);$i++) {
					echo 'save_tracking.push("'.$save[$i][0].'");';
				}
			?>
			var totalTracking = '';
			var rmk = document.getElementById('remark').value;
			
			for(var i=0;i<save_tracking.length;i++){
				var opid = save_tracking[i];
				var ref = document.getElementById('ref-'+opid).value;
				var curr_ref = document.getElementById('curr_trck-'+opid).value;
				
				data_tracking[opid] = {
					'ref':ref,
					'curr_ref': curr_ref
				};

				if (ref!='') {
					if (totalTracking!=''){
						totalTracking += ',';
					}
					totalTracking = totalTracking+ref;
				}
			}
			data_tracking['oid'] = oid;
			data_tracking['totalTracking'] = totalTracking;
			data_tracking['remark'] = rmk;
			//List--------------------------------------------------------------------------------------------------
			var save = [];
			var data = {};
			<?php 
				for($i=0;$i<sizeof($tracking);$i++){
					echo 'save.push("'.$tracking[$i]->trckNo.'");';
				}

			?>
			
			var stat = 0;
			for(var i=0;i<save.length;i++){
				var id = save[i];
				var m3 = document.getElementById('m3-'+id).textContent;
				var width = document.getElementById('width-'+id).value;
				var length = document.getElementById('length-'+id).value;
				var height = document.getElementById('height-'+id).value;
				var weight = document.getElementById('weight-'+id).value;
				var rate = document.getElementById('rate-'+id).value;
				var total = numberify(document.getElementById('total-'+id).textContent);
				var stat = document.getElementsByName('stat-'+id);
				if(stat[0].checked) {
						stat=1;		//complete
				}
				else if(stat[1].checked) {
						stat=0;		//incomplete
				}
				var type = document.getElementsByName('type-'+id);
				if(type[0].checked) {
						type=2;		//m3
				}
				else if(type[1].checked) {
						type=1;		//weight
				}

				data[id] = {
					'm3':m3,
					'width':width,
					'length':length,
					'height':height,
					'weight':weight,
					'rate':rate,
					'total':total,
					'stat':stat,
					'type':type
				};
			}

			//Detail-----------------------------------------------------------------------------------------------
			var save_detail = [];
			var data_detail = {};
			<?php
				for($i=0;$i<sizeof($_trackingDetail);$i++) {
					echo 'save_detail.push("'.$_trackingDetail[$i]->trckID.'");';
				}
			?>

			for(var i=0;i<save_detail.length;i++){
				var trckID = save_detail[i];
				var rec = document.getElementById('rec-'+trckID).value;
				var curr_rec = document.getElementById('curr_rec-'+trckID).value;
				var uid = document.getElementById('uid-'+trckID).textContent;
				if (rec!=curr_rec) {
						uid = _uid;
				}
				data_detail[trckID] = {
					'rec':rec,
					'uid':uid
				};
			}

			var result = true;
			var xhr = new XMLHttpRequest();
			xhr.open('POST','save_tracking.php',true);
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
			xhr.send('data='+JSON.stringify(data)+'&data_tracking='+JSON.stringify(data_tracking)+'&data_detail='+JSON.stringify(data_detail));
		}
		
		var ono = <?php echo "'".$_ono."'"; ?>;
		var cid = <?php echo "'".$_cid."'"; ?>;
		var cname = <?php echo "'".$_cname."'"; ?>;
		var cmail = <?php echo "'".$_cmail."'"; ?>;
		function saveOrder() {
				//get data
				var refnd = numberify(document.getElementById('amtRefnd').textContent);
				var rmk = document.getElementById('remark').value;
				var tm3 = numberify(document.getElementById('tm3').textContent);
				var tweight = numberify(document.getElementById('tweight').textContent);

				//prepare data
				var data = {};
				data = {
					'stat': 1,
					'ono': ono,
					'oid': oid,
					'cid': cid,
					'refnd': refnd,
					'cname': cname,
					'cmail': cmail,
					'rmk': rmk,
					'tm3': tm3,
					'tweight': tweight
				};
				
				var result = true;
				var xhr = new XMLHttpRequest();
				xhr.open('POST','save_order.php',true);
				xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
				xhr.onreadystatechange = function(){
				if(xhr.readyState==4 && xhr.status==200) {
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
			window.location = 'index.php';
		}

		function numberify(txt){
			return txt.replace(/[^0-9.]/g,'');
		}

		function checkZero(val,id) {
				if (val==0) {
					document.getElementById(id).value = '';
				}
		}
		
		setInterval(function(){
			//List
			var tm3 = document.getElementsByClassName('m3');
			var totalM3 = 0;
			for(var i=0;i<tm3.length;i++){
				amountByRow1 = tm3[i].textContent;
				if (isNaN(Number(amountByRow1))) {
						amountByRow1 = amountByRow1.toString().replace(/,/g,'');
				}
				totalM3 += Number(amountByRow1);
			}
			document.getElementById('tm3').textContent = numWithCom(totalM3);

			var tweight = document.getElementsByClassName('weight');
			var totalWeight = 0;
			for(var i=0;i<tweight.length;i++){
				amountByRow1 = tweight[i].value;
				if (isNaN(Number(amountByRow1))) {
						amountByRow1 = amountByRow1.toString().replace(/,/g,'');
				}
				totalWeight += Number(amountByRow1);
			}
			document.getElementById('tweight').textContent = numWithCom(totalWeight);

			var tTotal = document.getElementsByClassName('total');
			var total = 0;
			for(var i=0;i<tTotal.length;i++) {
				total += Number(tTotal[i].textContent.toString().replace(/,/g,''));
			}
			document.getElementById('tTotal').textContent = numWithCom(total);

			//Detail
			var trec = document.getElementsByClassName('rec');
			var totalrec = 0;
			for(var i=0;i<trec.length;i++) {
				totalrec += Number(trec[i].value);
			}
			document.getElementById('trec').textContent = totalrec;

			var tquan2 = document.getElementsByClassName('quan2');
			var totalQuan2 = 0;
			for(var i=0;i<tquan2.length;i++){
				totalQuan2 += Number(tquan2[i].textContent);
			}
			document.getElementById('tquan2').textContent = totalQuan2;

			var trefund = document.getElementsByClassName('refund');
			var totalRefund = 0;
			for(var i=0;i<trefund.length;i++){
				amountByRow1 = trefund[i].textContent;
				if (isNaN(Number(amountByRow1))) {
						amountByRow1 = amountByRow1.toString().replace(/,/g,'');
				}
				totalRefund += Number(amountByRow1);
			}
			document.getElementById('trefund').textContent = totalRefund;
			
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
					document.getElementById('opid').value = opid;
					document.getElementById('tracking_curr').value = document.getElementById('curr_trck-'+opid).value;
				}else{
					document.getElementById('addBox').style.visibility = 'hidden';
				}
		}
	</script>

	<body>
		<h2 style="color:#FF9900"><a href="product.php?order_id=<?php echo $oid?>">Tracking</a></h2>
        <h3 style="color:#FF9900"><a href="index.php">&larr; Back</a></h3><br>
   	<div>
        <table class="order-results" style="width:800px;">
			<tr>
				<td>เลขที่ออเดอร์  :</td><td id="order-id"><?php echo $_ono; ?></td>
				<td>วันที่ออเดอร์  :</td><td><?php echo $_odate; ?></td>
				<td>สถานะการตรวจสอบ :</td><td><?php if ($_comStat==0){echo "Incomplete";} else {echo "Complete";} ?></td>
			</tr>
			<tr class="punc">
				<td>ชื่อลูกค้า  :</td><td><?php echo $_cname; ?></td>
				<td>สถานะรายการ  :</td><td><?php echo $_codes[$code1]; ?></td>
				<td>Average รวม :</td><td><?php if($_m3Total==0) {echo 0;} else {echo number_format($_tranTotal/$_m3Total,2);} ?></td>
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

			$totalBs = 0;
			$tquanBS = 0;
			$tamountBs = 0;
			$tCnCostBS = 0;

			$tAmtRec = 0;
			$tAmtNotRec = 0;
			$tAmtRefnd = 0;

			echo '<table class="order-product">';
			echo '<thead>'.
			'<th>ลำดับ</th>'.
			'<th>ภาพตัวอย่าง</th>'.
			'<th>ขนาด</th>'.
			'<th>สี</th>'.
			'<th width="5%">จำนวน</th>'.
			'<th width="5%">ราคา/ชิ้น (หยวน)</th>'.
			'<th width="5%">ค่าขนส่งในจีน (หยวน)</th>'.
			'<th>รวม (หยวน)</th>'.
			'<th>รวม (บาท)</th>'.
			'<th width="8%">สถานะการสั่ง</th>'.
			'<th style="display:none;">หมายเหตุ</th>'.
			'<th width="5%">จำนวนหลังร้าน (ชิ้น)</th>'.
			'<th>จำนวนที่ได้รับ (ชิ้น)</th>'.
			'<th>ยอดขาด (ชิ้น)</th>'.
			'<th>ค่าสินค้าที่ได้รับแล้ว (บาท)</th>'.
			'<th>ค่าสินค้าที่ยังไม่ได้รับ (บาท)</th>'.
			'<th>ยอดคืนเงิน (บาท)</th>'.
			'<th style="display:none;" width="5%">ราคาหลังร้าน (หยวน)</th>'.
			'<th style="display:none;" width="5%">ค่าขนส่งหลังร้าน (หยวน)</th>'.
			'<th style="display:none;">ราคารวมหลังร้าน (บาท)</th>'.
			'<th>order taobao</th>'.
			'<th colspan="2">Tracking No.</th>'.
			'</thead>';
			
			foreach($shops as $key=>$item) {
				echo '<thead class="shopname undivide">'.
					'<th colspan="19">ร้าน '.base64_decode($key).'</th></thead>';
				$puncCount = 0;
				for($i=0;$i<sizeof($item);$i++) {
					//order status
					$o_status = $item[$i][9];

					if ($o_status!=1) {
							$tamount += $total;
							$tthb += $total*$orate;
							$total = ($item[$i][2]*(double)$item[$i][23])+(double)$item[$i][6];
							$tquan += $item[$i][2];
					}
					$trckNo = $item[$i][5];
					
					//$cncost = '-';
					$cncost = (double)$item[$i][6];
					$tCnCost += $cncost;

					//backshop
					$tquanBS += $item[$i][24];
					$cncostBS = (double)$item[$i][12];
					$tCnCostBS += $cncostBS;
					$totalBS = (($item[$i][24]*(double)$item[$i][11])+(double)$item[$i][12])*$orate;
					$tamountBs += $totalBS;

					//order product id
					$opid = $item[$i][0];
					
					//new--------------
					$rec = getRecieved($oid,$opid);
					$amtRec = ($rec*$item[$i][23])*$orate;
					$amtNotRec = ($item[$i][23]*($item[$i][24]-$rec))*$orate;
					$amtRefnd = 0;
					if ($rec!=0) {
						$amtRefnd = ($item[$i][23]*($item[$i][24]-$rec))*$orate;
					}
					
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
									'<input disabled=""true" style="width:auto" type="checkbox" value="2" id="stt2-'.$opid.'" onclick="checkStat2('.$opid.')"><label> ไม่ได้</label>'.
								'</div>';
					}
					//order fail
					else {
						$option = '<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="1" id="stt1-'.$opid.'" onclick="checkStat1('.$opid.')"><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="2" id="stt2-'.$opid.'" onclick="checkStat2('.$opid.')" checked><label> ไม่ได้</label>'.
								'</div>';
					}

					echo '<tr class="'.($puncCount%2==0? 'punc ':'').(empty($trckNo)? 'cancel ':'').'">'.
						'<td align="center">'.$no.'</td>'.
						'<td><a href="'.$item[$i][22].'" target="_blank"><img height="150" width="150" src="'.$item[$i][13].'"/></a></td>'.
						'<td>'.$item[$i][18].'</td>'.
						'<td>'.$item[$i][19].'</td>'.
						'<td align="right" class="quan1" id="quan1-'.$opid.'">'.$item[$i][2].'</td>'.			//quanltity
						'<td align="right">'.(double)$item[$i][23].'</td>'.	//price
						'<td align="right" class="tTran" id="tran1-'.$opid.'">'.$cncost.'</td>'.											//transport china cost
						'<td align="right" class="tAmountCn1" id="tAmountCn1-'.$opid.'">'.number_format($total,2).'</td>'.							//total chinese
						'<td align="right" class="tAmountTh1" id="tAmountTh1-'.$opid.'">'.number_format($total*$orate,2).'</td>'.					//total thai	
						'<td align="center">'.$option.'</td>'.
						'<td style="display:none;"><select id="rem-'.$opid.'" disabled="true">';
						foreach ($_remark as $key => $value) {
								if($key==$item[$i][27]) echo '<option value="'.$key.'" selected>'.$value.'</option>';
								else echo '<option value="'.$key.'">'.$value.'</option>';
						}

						echo '</select></td>'.
						'<td align="right" class="tquan" id="quan-'.$opid.'">'.$item[$i][24].'</td>'.			//quanltity
						'<td align="right" id="T_rec-'.$opid.'">'.number_format($rec).'</td>'.
						'<td align="right" id="T_NotRec-'.$opid.'">'.($item[$i][24]-$rec).'</td>'.
						'<td align="right" id="amtRec-'.$opid.'">'.number_format($amtRec,2).'</td>'.
						'<td align="right" id="amtNotRec-'.$opid.'">'.number_format($amtNotRec,2).'</td>'.
						'<td align="right" id="amtRefnd">'.number_format($amtRefnd,2).'</td>'.
						'<td align="right" style="display:none;" id="cpp-'.$opid.'">'.(double)$item[$i][11].'</td>'.	//price
						'<td align="right" style="display:none;" class="btTran" id="btran-'.$opid.'">'.$cncostBS.'</td>'.											//transport china cost
						'<td align="right" style="display:none;" class="tamount" id="totalTh-'.$opid.'">'.number_format($totalBS,2).'</td>'.
						'<td align="right" id="taobao-'.$opid.'"></td>'.
						'<td><input style="text-align:right;" id="ref-'.$opid.'" value="'.$trckNo.'"/></td>'.
						'<td><i class="material-icons" onclick="add('.$opid.');" title="Add">add_circle</i></td>'.
						'<input type="hidden" id="curr_stat-'.$opid.'" value="'.$o_status.'" />'.
						'<input type="hidden" id="curr_amount-'.$opid.'" value="'.number_format($total*$orate,2).'" />'.
						'<input type="hidden" id="curr_trck-'.$opid.'" value="'.$trckNo.'" />'.
						'</tr>';
						$puncCount++;
						$no++;
						$tAmtRec+=$amtRec;
						$tAmtNotRec+=$amtNotRec;
						$tAmtRefnd+=$amtRefnd;
				}
			}
			echo '<tbody class="padding">'.
			'<td class="cancel">ยอดรวม</td><td></td><td></td><td></td>'.
			'<td align="right">'.$tquan.'</td><td></td>'.
			'<td align="right">'.$tCnCost.'</td>'.
			'<td align="right">'.number_format($tamount).'</td>'.
			'<td align="right">'.number_format($tamount*$orate,2).'</td>'.
			'<td></td><td></td>'.
			'<td align="right" style="display:none;">'.$tquanBS.'</td>'.
			'<td></td>'.
			'<td align="right" style="display:none;">'.$tCnCostBS.'</td>'.
			'<td align="right" style="display:none;">'.$tamountBs.'</td>'.
			'<td></td>'.
			'<td align="right">'.number_format($tAmtRec,2).'</td>'.
			'<td align="right">'.number_format($tAmtNotRec,2).'</td>'.
			'<td align="right">'.number_format($tAmtRefnd,2).'</td>'.
			'<td></td><td></td><td></td>'.
			'</tbody></table>';
		?>
		<br>
		<div>
			<div style="display:inline;">
			Remark : <br>
				<textarea id='remark' style="float:left" rows="4" cols="50"><?php echo $_orderRemark; ?></textarea>
			</div>
			<div style="display:inline;">
				<button class="order-button" onclick="saveOrder()">ORDER COMPLETE</button>
			</div>
		</div>
	</div>
	<br>
	<br>	
	<div>
		<!--tracking List-->
		<h3><b>Tracking List</b></h3>
		<table class="shipping">
			<tr>
				<th>Tracking No.</th>
				<th width=7%>Width</th>
				<th width=7%>Length</th>
				<th width=7%>Height</th>
				<th width=7%>M3</th>
				<th width=7%>น้ำหนัก Kg.</th>
				<th>Product Type</th>
				<th>Type</th>
				<th width=7%>Rate</th>
				<th>ค่าเฉลี่ย</th>
				<th>ยอดรวม (บาท)</th>
				<th>กล่อง</th>
				<th>สถานะ</th>
			</tr>
			<?php
				$tbox = 0;
				$tm3 = 0;
				$tkg = 0;
				$puncCount = 0;
				$tmpTrckNo = '';
				foreach($tracking as $item){
					if ($tmpTrckNo==$item->trckNo) {
						$ptID = $item->ptID;
						echo '<tr class="'.(--$puncCount%2==0? 'punc':'').'"><td></td><td colspan="5"></td>'.
						'<td><select id="type-'.$thisID.'">';
						foreach ($_ptype as $key => $value) {
								if($key==$ptID) echo '<option value="'.$key.'" selected>'.$value.'</option>';
								else echo '<option value="'.$key.'">'.$value.'</option>';
						}
						echo '</select></td>'.
						'<td colspan="6"></tr>';
						$puncCount++;
						continue;
					}
					$tmpTrckNo = $item->trckNo;
					//$thisID = $item->trckID;
					$thisID = $item->trckNo;
					$width = $item->width;
					$length = $item->length;
					$height = $item->height;
					$m3 = $item->m3;
					$weight = $item->weight;
					$rate = $item->rate;
					$stat = $item->stat;
					$box = $item->box;

					$ptID = $item->ptID;
					$rType = $item->rType;
					$pType = $item->pType;

					//new type for m3/weight
					$type = $item->type;

					/* old rate
					if (is_null($ptID)||$ptID==0) {
						$rate = 1;
					}
					else {
						$rate = $_rates[$rType][$pType];
					}*/
					
					if ($stat==0) {
							$echoStat = '<div style="display:inline">'.
										'<input type="radio" style="width:auto" name="stat-'.$thisID.'" value="1" /> Complete'.
										'<input type="radio" style="width:auto" name="stat-'.$thisID.'" value="0" checked="checked" /> Incomplete'.
										'</div>';
					}
					else {
							$echoStat = '<div style="display:inline">'.
										'<input type="radio" style="width:auto" name="stat-'.$thisID.'" value="1" checked="checked" /> Complete'.
										'<input type="radio" style="width:auto" name="stat-'.$thisID.'" value="0" /> Incomplete'.
										'</div>';	
					}

					//use $type instead $rType
					if ($type==2) {
							$echoType = '<div style="display:inline">'.
										'<input type="radio" style="width:auto" name="type-'.$thisID.'" value="m3" checked="checked" onclick="changeTypeM3(\''.$thisID.'\');" /> m3'.
										'<input type="radio" style="width:auto" name="type-'.$thisID.'" value="weight" onclick="changeTypeWeight(\''.$thisID.'\');" /> weight'.
										'</div>';
							$tot = $m3*$rate;
							if ($m3!=0) {
									$avg = ($rate*$m3)/$m3;
							}
							else {
									$avg = 0;
							}
					}
					else {
							$echoType = '<div style="display:inline">'.
										'<input type="radio" style="width:auto" name="type-'.$thisID.'" value="m3" onclick="changeTypeM3(\''.$thisID.'\');" /> m3'.
										'<input type="radio" style="width:auto" name="type-'.$thisID.'" value="weight" checked="checked" onclick="changeTypeWeight(\''.$thisID.'\');" /> weight'.
										'</div>';
							$tot = $weight*$rate;
							if ($m3!=0) {
									$avg = ($rate*$weight)/$m3;
							}
							else {
									$avg = 0;
							}
					}

					echo '<tr class="'.($puncCount%2==0? 'punc':'').'">'.
						'<td id="cn-ref-'.$thisID.'" class="china break">'.$item->trckNo.'</td>'.
						'<td><input style="text-align:right;" id="width-'.$thisID.'" value="'.$width.'" onkeyup="this.value=numberify(this.value);calM3(\''.$thisID.'\');" onclick="checkZero(this.value,\'width-'.$thisID.'\')" /></td>'.
						'<td><input style="text-align:right;" id="length-'.$thisID.'" value="'.$length.'" onkeyup="this.value=numberify(this.value);calM3(\''.$thisID.'\');" onclick="checkZero(this.value,\'length-'.$thisID.'\')" /></td>'.
						'<td><input style="text-align:right;" id="height-'.$thisID.'" value="'.$height.'" onkeyup="this.value=numberify(this.value);calM3(\''.$thisID.'\');" onclick="checkZero(this.value,\'height-'.$thisID.'\')" /></td>'.
						'<td align="right" class="m3" id="m3-'.$thisID.'">'.$m3.'</td>'.
						'<td><input class="weight" style="text-align:right;" id="weight-'.$thisID.'" value="'.$weight.'" onkeyup="this.value=numberify(this.value);calWeight(\''.$thisID.'\');"/></td>'.
						'<td><select id="type-'.$thisID.'">';
						foreach ($_ptype as $key => $value) {
								if($key==$ptID) echo '<option value="'.$key.'" selected>'.$value.'</option>';
								else echo '<option value="'.$key.'">'.$value.'</option>';
						}
						echo '</select></td>'.		
						'<td align="center">'.$echoType.'</td>'.
						'<td><input style="text-align:right;" name="rate" id="rate-'.$thisID.'" value="'.$rate.'" onkeyup="this.value=numberify(this.value);calRate(\''.$thisID.'\');" /></td>'.
						'<td align="right" id="avg-'.$thisID.'">'.number_format($avg,2).'</td>'.
						'<td align="right" class="total" id="total-'.$thisID.'">'.number_format($tot,2).'</td>'.
						'<td align="right" id="box-'.$thisID.'">'.$box.'</td>'.
						'<td align="center">'.$echoStat.'</td>'.
						'</tr>';
					$tm3 += $m3;
					$tkg += $weight;
					$total = $tkg*$rate;
					$puncCount++;
				}
				echo '<tr class="padding"><td>ยอดรวม</td><td></td><td></td><td></td>'.
				'<td align="right" id="tm3">'.number_format($tm3,2).'</td>'.
				'<td align="right" id="tweight">'.number_format($tkg,2).'</td>'.
				'<td></td><td></td><td></td><td></td>'.
				'<td align="right" id="tTotal"></td><td></td><td></td>'.
				'</tr>';
			?>
		</table>
	</div>

	<div>
		<!--tracking Detail-->
		<br>
		<h3><b>Tracking Detail</b></h3>
		<!--<table class="shipping">
			<thead>
				<th>Tracking No.</th>
				<th>ชื่อสินค้า</th>
				<th>จำนวนที่สั่ง</th>
				<th>จำนวนที่ได้รับ</th>
				<th>จำนวนที่ขาด</th>
				<th>จำนวนเงินที่ต้องคืน</th>
			</thead>-->
			<?php
				$tquan = 0;
				$trec = 0;
				$tquan2 = 0;
				$trefund = 0;
				$puncCount = 0;
				$trckTmp = '';
				$flg = 0;
				foreach($_trackingDetail as $item) {
					$thisID = $item->trckID;
					$trckNO = $item->trckNo;
					$pname = $item->pname;
					$quan = $item->quan;
					$rec = $item->rec;
					$quan2 = $item->quan-$item->rec;
					$price = $item->price;
					$refund = $quan2*$price;
					$pimg = $item->pimg;
					$purl = $item->purl;
					$uid = $item->uid;
					
					if ($flg==1) { echo '<br>'; }
					if ($trckTmp!=$trckNO||$trckTmp=='') {
							echo '<table class="shipping">
								<thead>
									<th width="10%">Tracking No.</th>
									<th>รูป</th>
									<th width="40%">ชื่อสินค้า</th>
									<th>จำนวนที่สั่ง</th>
									<th>จำนวนที่ได้รับ</th>
									<th style="display:none">จำนวนที่ขาด</th>
									<th style="display:none">จำนวนเงินที่ต้องคืน</th>
									<th>ผู้บันทึก</th>
									</thead>';
							$trckTmp = $trckNO;
							$flg = 1;
					}
					echo '<tr class="'.($puncCount%2==0? 'punc':'').'">'.
						'<td>'.$trckNO.'</td>'.
						'<td><a href="'.$purl.'" onclick="window.open(\''.$purl.'\', \'newwindow\', \'width=800, height=800\'); return false;"><img height="150" width="150" src="'.$pimg.'"/></a></td>'.
						'<td>'.$pname.'</td>'.
						'<td align="right" id="quan-'.$thisID.'">'.$quan.'</td>'.
						'<td><input style="text-align:right;" class="rec" id="rec-'.$thisID.'" value="'.$rec.'" onkeyup="this.value=numberify(this.value);calDetail(\''.$thisID.'\');" onclick="checkZero(this.value,\'rec-'.$thisID.'\')"/></td>'.
						'<td align="right" style="display:none" class="quan2" id="quan2-'.$thisID.'">'.$quan2.'</td>'.
						'<td align="right" style="display:none" class="refund" id="refund-'.$thisID.'">'.number_format($refund,2).'</td>'.
						'<td align="right" id="uid-'.$thisID.'">'.$uid.'</td>'.
						'<input type="hidden" id="price-'.$thisID.'" value='.$price.'>'.
						'<input type="hidden" id="curr_rec-'.$thisID.'" value='.$rec.'>'.
						'</tr>';
					
					$tquan += $quan;
					$trec += $rec;
					$tquan2 += $quan2;
					$trefund += $refund;
					$puncCount++;
				}
				echo '<tr class="padding" style="display:none"><td>ยอดรวม</td><td></td>'.
				'<td align="right" id="tquan">'.$tquan.'</td>'.
				'<td align="right" id="trec">'.$trec.'</td>'.
				'<td align="right" id="tquan2">'.$tquan2.'</td>'.
				'<td align="right" id="trefund">'.number_format($trefund,2).'</td></tr>';
			?>
		</table>
	</div>
		<br>
		<div style="width:300px;left:0;right:0;margin-left:auto;margin-right:auto;">
			<button class="order-button" onclick="save()">บันทึก</button>
			<button class="order-cancel" onclick="cancel()">กลับ</button>
		</div>
		<br>
	</body>

<!--Add Box-->
<div id="addBox" class="bgwrap">
		<div class="container">
			<div class="containerheader">
        		<h2 id="title">Add Tracking</h2>
     		</div>
			
			<form method="post">		
     		<div>
        		<table>
        			<tr>
        				<th>1</th>
        				<td><input name="tracking1" id="tracking1"/></td>
        			</tr>
        			<tr>
        				<th>2</th>
        				<td><input name="tracking2" id="tracking2"/></td>
        			</tr>
        			<tr>
        				<th>3</th>
        				<td><input name="tracking3" id="tracking3"/></td>
        			</tr>
        			<tr>
        				<th>4</th>
        				<td><input name="tracking4" id="tracking4"/></td>
        			</tr>
        			<tr>
        				<th>5</th>
        				<td><input name="tracking5" id="tracking5"/></td>
        			</tr>
        			<tr>
        				<th>6</th>
        				<td><input name="tracking6" id="tracking6"/></td>
        			</tr>
        			<tr>
        				<th>7</th>
        				<td><input name="tracking7" id="tracking7"/></td>
        			</tr>
        			<tr>
        				<th>8</th>
        				<td><input name="tracking8" id="tracking8"/></td>
        			</tr>
        			<tr>
        				<th>9</th>
        				<td><input name="tracking9" id="tracking9"/></td>
        			</tr>
        			<tr>
        				<th>10</th>
        				<td><input name="tracking10" id="tracking10"/></td>
        			</tr>
				</table>
			</div>
		
			<div class="containerfooter">
				<input type="hidden" name="opid" id="opid" value="">
				<input type="hidden" name="tracking_curr" id="tracking_curr" value="">
				<input type="hidden" name="add" value="1"/>
				<a onclick="add();">Cancel</a>&emsp;<button>Add</button>
			</div>
			</form>
		</div>
</div>
</html>
<?php
	$con->close();
?>