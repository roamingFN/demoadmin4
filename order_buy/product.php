<?php
	session_start();
	if (!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
	date_default_timezone_set("Asia/Bangkok");
	include '../database.php';
	include 'function.php';
	include '../utility/permission.php';
	
	const FORMID = 5;
	$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
	$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
	if ($_adminFlg==0) {
			if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
	}
	
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
		$_shop = array();
		$_byShop = array();
		$_oPaiddt = '';
		$_oPaid = 0;
		$_ratedt = '';
		
		if($stmt = $con->prepare('SELECT op.order_product_id,confirmed_product_price,op.quantity,order_shipping_cn_m3_size,order_shipping_cn_weight,
			order_shipping_cn_ref_no,op.order_shipping_cn_cost,comment,unconfirmed_product_order,order_status,order_cause,backshop_price,backshop_shipping_cost,
			shop_name,product_img,product_url,product_price,order_shipping_cn_box,order_shipping_rate,
			product_size_china,product_color_china,comment,p.product_id,p.product_url,op.unitprice,
			backshop_quantity,backshop_total_price,order_product_totalprice,remark_id,order_taobao
			,op.first_unitprice,return_status,email_no,chkflg,tracking_company,om.paymore_status,tracking_company
			FROM customer_order_product op JOIN product p ON op.product_id=p.product_id
			JOIN customer_order_shipping os ON op.order_id=os.order_id
			LEFT JOIN customer_order_paymore om ON om.order_product_id=op.order_product_id
			WHERE op.product_id=p.product_id AND op.order_id='.$oid.' GROUP BY op.order_product_id ORDER BY op.order_product_id')) {
			$stmt->execute();
			$stmt->bind_result($opid,$cpp,$quan,$size,$weight,$ref,$cost,$comment,$upo,$status,$cause,
				$bp,$bcost,$shop,$img,$url,$pp,$box,$osr,$pSize,$pColor,$comment,$pid,$purl,$unp,$backQuan,$backTot,
				$opTot,$rid,$taobao,$funit,$rtstat,$emailno,$chkflg,$tracking_company,$pmStat,$trckCom);
			while($stmt->fetch()){
				$enc = base64_encode($shop);
				if(!isset($shops[$enc])){
					$shops[$enc] = array();
				}
				array_push($shops[$enc],array($opid,$cpp,$quan,$size,$weight,$ref,$cost,$comment,$upo,$status,$cause,
					$bp,$bcost,$img,$url,$pp,$box,$osr,$pSize,$pColor,$comment,$pid,$purl,$unp,$backQuan,$backTot,
					$opTot,$rid,$taobao,$funit,$rtstat,$emailno,$chkflg,$tracking_company,$pmStat,$trckCom));
				if($status!=2) {
					array_push($save,array($opid,$enc));
					//for total by shop
					array_push($_shop,array($opid,$shop));

					//for by shop
					if (!isset($_byShop[$enc])) $_byShop[$enc][0] = $opid;
					else array_push($_byShop[$enc],$opid);
				}
			}
		}
		//print_r($_byShop);
		echo $con->error;

		//get rate, order number, customer info
		$sql = 'SELECT order_rate,order_number,order_status_code,'.
			'customer.customer_id,customer.customer_firstname,customer.customer_lastname,customer.customer_email'.
			',date_order_created,date_order_paid,order_price_yuan,order_rate_date,customer_note,user_note,customer_code'.
			' FROM customer_order JOIN customer ON customer.customer_id=customer_order.customer_id'. 
			' WHERE order_id='.$oid;
		if($stmt = $con->prepare($sql))
		$stmt->execute();
		$stmt->bind_result($rate,$ono,$ostat,$cid,$cfname,$clname,$cmail,$odate,$opaiddt,$opaid,$ratedt,$cnote,$unote,$ccode);
		while ($stmt->fetch()) {
			$rate1 = $rate;
			$ono1 = $ono;
			$code1 = $ostat;
			$cid1 = $cid;
			$cname1 = $cfname.' '.$clname;
			$cmail1 = $cmail;
			$_oDate = $odate;
			$_oPaiddt = $opaiddt;
			$_oPaid = $opaid;
			$_ratedt = $ratedt;
			$_cnote = $cnote;
			$_unote = $unote;
			$_ccode = $ccode;
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
                $count = 0;
                if ($opid!="") {
                 		for ($i=1; $i<=10; $i++) {
                 			$track = $_POST['tracking'.$i];
                 			if ($track!="") {
                 				if ($allTrack!="") {
                 					$allTrack = $allTrack.','.$track;
                 				}
                 				else {
                 					$allTrack = $track;
                 				}
                 				$count++;
                 			}
                 		}
                }
                $oid = $_POST['oid'];
                $curr = $_POST['tracking_curr'];
                $res = updTracking($oid,$opid,$allTrack,$curr,$count);
                header("Refresh:0");
		}

		//refund---------------------------------------------------------------------
		if (isset($_POST['refund'])) {
			//update product------------------------
			$stmt = $con->prepare('UPDATE customer_order_product SET backshop_quantity=?,backshop_price=?,backshop_shipping_cost=?,return_baht=? WHERE order_product_id=?');
			$stmt->bind_param('idddi',$_POST['tmp-received'],$_POST['tmp-price'],$_POST['tmp-tran'],$_POST['tmp-total'],$_POST['ref-opid']);
			$res = $stmt->execute();
			if(!$res) {
					echo '<script>alert("เพ่ิมข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['ref-oid'].'";</script>';
					return;
			}

			$rtno = genCRN($con);
			//update customer_order------------------------
			$stmt = $con->prepare('UPDATE customer_order_product SET return_status=2 WHERE order_product_id=?');
			$stmt->bind_param('s',$_POST['ref-opid']);
			$res = $stmt->execute();
			if(!$res) {
					echo '<script>alert("เพ่ิมข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['ref-oid'].'";</script>';
					return;
			}

			//get topup id------------------------
            $tid = getTopupID($con,$_POST['ref-cid'],$_POST['ref-oid']);

            //update customer request topup---------
            $stmt = $con->prepare('UPDATE customer_request_topup SET usable_amout=usable_amout+'.$_POST['tmp-total'].' WHERE topup_id='.$tid);
			$res = $stmt->execute();
			if(!$res) {
					echo '<script>alert("เพ่ิมข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['ref-oid'].'";</script>';
					return;
			}

			//insert return--------------------------
			// $transport = getTransport($con,$_POST['ref-opid']);
			// $refundSQL = 'INSERT INTO customer_order_return (return_no, return_date, order_product_id, first_unitquantity, quantity, loss_quantity, unitprice, total_yuan, rate, total_baht, return_status, topup_id, order_id, return_type, customer_code, pay_unitprice, pay_transport, transport)'.
			// ' VALUES (\''.$rtno.'\', now(), '.$_POST['ref-opid'].', '.$_POST['tmp-ordered'].', '.$_POST['tmp-received'].', '.$_POST['tmp-missed'].', '.$_POST['tmp-price'].', '.$_POST['tmp-totalCn'].', '.$_POST['tmp-rate'].', '.$_POST['tmp-total'].', 1, '.$tid.', '.$_POST['ref-oid'].', 1, \''.$_ccode.'\', '.$_POST['tmp-price1'].','.$transport['pay_transport'].','.$transport['transport'].')';
			// if($stmt = $con->prepare($refundSQL)) {
			// 	$res = $stmt->execute();
			// 	if(!$res) {
			// 			echo '<script>alert("เพิ่มข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['ref-oid'].'";</script>';
			// 			return;
			// 	}
			// 	$lastID = $stmt->insert_id;
			// }
			// else {
			// 		echo "error while inserting customer_order_return ".$stmt->error;
			// 		return;
			// }

			//update customer_order_return
			$sql = 'UPDATE customer_order_return SET return_status=1 WHERE order_product_id=? AND order_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ii',$_POST['ref-opid'],$_POST['ref-oid']);
			$res - $stmt->execute();

			//insert customer_statement--------------
			$refundSQL = 'INSERT INTO customer_statement (customer_id,statement_name,statement_date,debit,credit,order_id, return_id) VALUES (?,?,?,?,?,?,?)';
			$credit = 0;
			$date = date("Y-m-d H:i:s");
			$statement_name = 'คืนเงิน - เลขที่ '.$rtno;
			$stmt = $con->prepare($refundSQL);
			$stmt->bind_param('sssssss',$_POST['ref-cid'],$statement_name,$date,$_POST['tmp-total'],$credit,$_POST['ref-oid'],$lastID); 
			$res = $stmt->execute();
			if(!$res) {
					echo '<script>alert("เพิ่มข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");';
					echo 'window.location.href="product.php?order_id='.$_POST['ref-oid'].'";</script>';
					return;
			}

			//update customer------------------------
			$stmt = $con->prepare('UPDATE customer SET current_amount=current_amount+? WHERE customer_id=?');
			$stmt->bind_param('ss',$_POST['tmp-total'],$_POST['ref-cid']);
			$res = $stmt->execute();
			if(!$res) {
					echo '<script>alert("เพ่ิมข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['ref-oid'].'";</script>';
					return;
			}

            $uid = getuserid($con,$_SESSION['ID']);
            //insert total message log
            $subject = 'คืนเงินค่าสินค้า รายการสั่งซื้อ '.$rtno;
            $content = 'คืนเงินค่าสินค้า จำนวนเงิน '.number_format($_POST['tmp-total'],2).' บาท รายละเอียดคลิกปุ่มลูกศรที่แถวสินค้า <img src="images/more.png">';
            $sql = 'INSERT INTO total_message_log (order_id,order_product_id,customer_id,user_id,subject,content,message_date,active_link)
            	VALUES (?,?,?,?,?,?,now(),1)';
            $stmt = $con->prepare($sql);
	        $stmt->bind_param('iiiiss',$_POST['ref-oid'],$_POST['ref-opid'],$_POST['ref-cid'],$uid,$subject,$content);
	        $stmt->execute();

	        //update order_product.current_status=98 if this shop has no item
			$shopname = getShopName($con,$_POST['ref-opid']);
			if (checkOrderStatusInShop($con,$_POST['ref-oid'],$shopname)) {
				$sql = 'UPDATE customer_order_product SET current_status=98 WHERE order_product_id=?';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('i',$_POST['ref-opid']);
				$res = $stmt->execute();
			}

        	echo '<script>alert("เพิ่มข้อมูลการคืนเงินสำเร็จ");window.location.href="product.php?order_id='.$_POST['ref-oid'].'";</script>';
		}//end refund------------------------------------------------------------------

		//BBBack refund---------------------------------------------------------------------
		if (isset($_POST['backRefund'])) {
			$tmpopid = $_POST['bref-opid'];
			//check balance
			if (checkBal($con,$tmpopid)<$_POST['btmp-refund']) {
					echo '<script>alert("ไม่สามารถคืนเงินได้อเพราะยอดเงินถูกตัดจ่ายไปแล้ว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['bref-oid'].'";</script>';
					return;
			}
			//update customer_order------------------------
			$stmt = $con->prepare('UPDATE customer_order_product SET return_status=1 WHERE order_product_id=?');
			$stmt->bind_param('s',$_POST['bref-opid']);
			$res = $stmt->execute();
			if(!$res) {
					echo '<script>alert("ยกเลิกข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['bref-oid'].'";</script>';
					return;
			}

			//update customer_order_return------------------------
			$date = date("Y-m-d H:i:s");
			$stmt = $con->prepare('UPDATE customer_order_return SET return_status=2,cancel_date=?,cancel_by=? WHERE order_product_id=?');
			$stmt->bind_param('sss',$date,$_SESSION['ID'],$tmpopid);
			$res = $stmt->execute();
			if(!$res) {
					echo '<script>alert("ยกเลิกข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['bref-oid'].'";</script>';
					return;
			}

			$sql = 'SELECT running,return_no,total_baht,topup_id FROM customer_order_return WHERE order_product_id='.$tmpopid;
			$stmt = $con->prepare($sql);
			$stmt->bind_result($rid,$rno,$total,$tid);
			$res = $stmt->execute();
			while ($stmt->fetch()) {
					$rid = $rid;
			 		$tid = $tid;
			 		$rno = $rno;
			 		$total = $total;
			}
			
			//update customer request topup---------
            $stmt = $con->prepare('UPDATE customer_request_topup SET usable_amout=usable_amout-'.$total.' WHERE topup_id='.$tid);
			$res = $stmt->execute();
			if(!$res) {
					echo '<script>alert("เพ่ิมข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['bref-oid'].'";</script>';
					return;
			}

			//insert customer_statement--------------
			$refundSQL = 'INSERT INTO customer_statement (customer_id,statement_name,statement_date,debit,credit,order_id,topup_id,return_id) VALUES (?,?,?,?,?,?,?,?)';
			$credit = $_POST['btmp-refund'];
			$debit = 0;
			$statement_name = 'ยกเลิกคืนเงิน - เลขที่ '.$rno;
			$stmt = $con->prepare($refundSQL);
			$stmt->bind_param('ssssssii',$_POST['bref-cid'],$statement_name,$date,$debit,$credit,$_POST['bref-oid'],$tid,$rid); 
			$res = $stmt->execute();
			if(!$res) {
					echo '<script>alert("ยกเลิกข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['bref-oid'].'";</script>';
					return;
			}

			//delete customer statement
			// $rid = getRID($_POST['bref-opid']);
			// $sql = 'DELETE FROM customer_statement WHERE return_id='.$rid;
			// $stmt = $con->prepare($sql); 
			// $res = $stmt->execute();
			// if(!$res) {
			// 		echo '<script>alert("ยกเลิกข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");';
			// 		echo 'window.location.href="product.php?order_id='.$_POST['bref-oid'].'";</script>';
			// 		return;
			// }

			//update customer_order.flag_return
			$sql = 'UPDATE customer_order SET flag_return=0 WHERE order_id=?';
	        $stmt = $con->prepare($sql);
	        $stmt->bind_param('i',$_POST['bref-oid']);
	        $stmt->execute();

			//update customer------------------------
			$stmt = $con->prepare('UPDATE customer SET current_amount=current_amount-? WHERE customer_id=?');
			$stmt->bind_param('ss',$_POST['btmp-refund'],$_POST['bref-cid']);
			$res = $stmt->execute();
			if(!$res) {
					echo '<script>alert("ยกเลิกข้อมูลการคืนเงินล้มเหลว\n'.$stmt->error.'");window.location.href="product.php?order_id='.$_POST['bref-oid'].'";</script>';
			}
            else {
              		echo '<script>alert("ยกเลิกข้อมูลการคืนเงินสำเร็จ");window.location.href="product.php?order_id='.$_POST['bref-oid'].'";</script>';
                		
            }
		}//end back refund------------------------------------------------------------------

		//email
		if (isset($_POST['email'])) {
				$email   = $_POST['email-cmail'];
				$subject = $_POST['email-subject'];
				$content = $_POST['email-content'];
				//$content = json_encode($content);
				$content = preg_replace("/[\t]/"," ", $content);
				$content = preg_replace("/[\r\n]/","<br />", $content);
				$cid     = $_POST['email-cid'];
				$oid     = $_POST['email-oid'];
				$ono     = $_POST['email-ono'];
				$opid    = $_POST['email-opid'];
				$uid     = $_SESSION['ID'];
				$ccode   = getCustomerCode($con,$cid);
				$emailFlag = sendEmail($subject,$content,$cid,$oid,$ono,$opid,$uid,$ccode,$con);
				if(!$emailFlag) {
					echo '<script>alert("ส่งอีเมลล์ล้มเหลว");window.location.href="product.php?order_id='.$oid.'";</script>';
				}
	            else {
	            	$stmt = $con->prepare('UPDATE customer_order_product SET email_no=email_no+1 WHERE order_product_id=?');
					$stmt->bind_param('s',$opid);
					$res = $stmt->execute();
					// $stmt = $con->prepare('INSERT INTO return_email_log (order_product_id,subject,content,return_type) VALUES (?,?,?,1)');
					// $stmt->bind_param('sss',$opid,$subject,$content);
					// $res = $stmt->execute();
					// $stmt = $con->prepare('INSERT INTO total_message_log (order_id,order_product_id,subject,content) VALUES (?,?,?,?)');
					// $stmt->bind_param('iiss',$oid,$opid,$subject,$content);
					// $res = $stmt->execute();
	              	echo '<script>alert("ส่งอีเมลล์สำเร็จ");window.location.href="product.php?order_id='.$oid.'";</script>';
	            }
		}

	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link rel="stylesheet" type="text/css" href="../css/w3-blueGray.css">
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<!--<script src="../js/jquery-1.10.2.js"></script>-->
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
		function calc1(id) {
			var tran1 = parseFloat(numberify(document.getElementById('tran1-'+id).textContent.replace(/,/g, '')));
			var tran = parseFloat(document.getElementById('bTran-'+id).value);
			if (isNaN(tran)) tran=0;
			var quan = parseFloat(document.getElementById('quan-'+id).value);
			if (isNaN(quan)) quan=0;
			var price = parseFloat(document.getElementById('cpp-'+id).value);
			if (isNaN(price)) price=0;
			var cal1     = (quan*price);
			var totalTh  = (cal1*Number(rate1))+(tran*rate1);
			var totalCn  = cal1+tran;
			document.getElementById('totalTh-'+id).textContent   = numWithCom(totalTh);
			document.getElementById('totalCn-'+id).textContent   = numWithCom(totalCn);
			document.getElementById('totalYuan-'+id).textContent = numWithCom(cal1);

			//refund-----
			// var quan1 = parseInt(document.getElementById('quan1-'+id).textContent.replace(/,/g, ''));
			// var quan = parseInt(document.getElementById('quan-'+id).value);
			// var price1 = parseFloat(document.getElementById('cpp1-'+id).textContent.replace(/,/g, ''));
			// var price = parseFloat(document.getElementById('cpp-'+id).value);
			// if (isNaN(price)) price=0;
			// var diffQuan = quan1-quan
			// var diffPrice = price1-price;
			// var diffTran = tran1-tran;
			// if (diffPrice==0) {
			// 		var grandPrice = price1;
			// }
			// else {
			// 		var grandPrice = diffPrice;
			// }
			// var grandVal = (diffQuan*(grandPrice))+(diffTran);
			var total1 = parseFloat(document.getElementById('tAmountTh1-'+id).textContent.replace(/,/g, ''));
			var cal = (total1)-(totalTh);
			document.getElementById('refund-'+id).textContent = numWithCom(cal);

			if (cal<0) {
				$('#refund-'+id).css('color', 'red');
			}
			else {
				$('#refund-'+id).css('color', 'black');
			}
		}

		function removeDup(str) {
				var result = '';
				var tmpArr = str.split(',');
				result = tmpArr.filter(function(item, pos) {
				    return tmpArr.indexOf(item) == pos;
				});
				return result.toString();
		}

		function isDup(str) {
				var result = '';
				var tmpArr = str.split(',');
				result = tmpArr.filter(function(item, pos) {
				    return tmpArr.indexOf(item) == pos;
				});
				if (result.toString()!=str) return true; 
				else return false;
		}
		
		var orderId = <?php echo $oid; ?>;
		var customer_id = <?php echo $cid1; ?>;
		var cmail = <?php echo '\''.$cmail1.'\''; ?>;
		var ccode = <?php echo '\''.$_ccode.'\''; ?>;
		function save(){
			var save = [];
			var data = {};
			var refund = {};
			var shop = [];
			<?php 
				for($i=0;$i<sizeof($save);$i++){
					echo 'save.push("'.$save[$i][0].'");';
				}
			?>
			
			var grandTotalTh = 0;
			var grandTotalCn = 0;
			var totalTaobao = '';
			var totalTracking = '';
			var tmpShopName = '';
			for(var i=0;i<save.length;i++){
				var id = save[i];

				var chkflg = document.getElementById('ck-'+id).checked;
				var quan1 = parseInt(document.getElementById('quan1-'+id).textContent);
				var cpp1  = numberify(document.getElementById('cpp1-'+id).textContent);
				var tran1 = document.getElementById('tran1-'+id).textContent;
				var totalp1 = numberify(document.getElementById('tAmountTh1-'+id).textContent);
				var totalpCn1 = numberify(document.getElementById('tAmountCn1-'+id).textContent);
				//var comment = document.getElementById('rem-'+id).options[document.getElementById('rem-'+id).selectedIndex].value;
				var stt1 = document.getElementById('stt1-'+id).checked;
				var stt2 = document.getElementById('stt2-'+id).checked;
				var stt;
				if ((stt1==0)&&(stt2==0)) {
					alert("กรุณาเลือกสถานะการสั่ง");
					return 0;		//exit
				}
				if (stt1) {
					stt = 1;
					if (quan1<=0) {
							alert("จำนวนชิ้นสินค้าต้องมากกว่า 0");
							return 0;
					}
				}
				else if (stt2) {
					stt = 2;
						if (comment==0) {
							alert("กรุณาเลือกหมายเหตุ");
							return 0;
						}
				}
				
				//backshop
				var quan = document.getElementById('quan-'+id).value;
				var cpp = numberify(document.getElementById('cpp-'+id).value);
				var bTran = numberify(document.getElementById('bTran-'+id).value);
				//var taobao = document.getElementById('taobao-'+id).value;
				//var ref = document.getElementById('ref-'+id).value;
				var curr_ref = document.getElementById('curr_trck-'+id).value;
				var btTran = numberify(document.getElementById('btTran').textContent);
				var btAmt = numberify(document.getElementById('tamountTh').textContent);

				//new
				var allPriceYuan = numberify(document.getElementById('totalYuan-'+id).textContent);
				var btYuan = numberify(document.getElementById('totalCn-'+id).textContent);
				var ret = parseFloat(document.getElementById('refund-'+id).textContent.replace(/,/g, ''));

				//validte quan with quan1
				if (quan>quan1) {
					alert("จำนวนสินค้าที่สั่งได้ต้องไม่เกินจำนวนที่ลูกค้าสั่ง");
					document.getElementById('quan-'+id).focus();
					return;		//exit
				}

				// //validte price with first_price
				// if (Number(cpp)>Number(cpp1)) {
				// 	alert("ราคาหลังร้านต้องไม่เกินราคาต่อชิ้นที่ลูกค้าสั่ง");
				// 	document.getElementById('cpp-'+id).focus();
				// 	return;		//exit
				// }

				//get shopname for taobao and tracking no.
				var shopname = document.getElementById('shopname-'+id).value;
				var taobao = document.getElementById('taobao-'+shopname).value;
				var ref = document.getElementById('ref-'+shopname).value;
				ref = removeDup(ref);
				var com = document.getElementById('com-'+shopname).value;

				data[id] = {
					'quan1':quan1,
					'tran1':tran1,
					'stt':stt,
					'totalp1':totalp1,
					'totalpCn1':totalpCn1,
					'quan':quan,
					'cpp':cpp,
					'tran':bTran,
					'taobao':taobao,
					'ref':ref,
					'totalp':numberify(document.getElementById('totalTh-'+id).textContent),
					'curr_ref': curr_ref,
					'apy': allPriceYuan,
					'btyuan': btYuan,
					'return': ret,
					'chkflg': chkflg,
					'company': com
				};
				grandTotalTh = Number(grandTotalTh) + Number(totalp1);
				grandTotalCn = Number(grandTotalCn) + Number(totalpCn1);
				//console.log(shopname+ " "+tmpShopName);
				if (shopname!=tmpShopName) {
						if (taobao!='') {
							if (totalTaobao!=''){
								totalTaobao += ',';
							}
							totalTaobao = totalTaobao+taobao;
						}
						if (ref!='') { 
							if (totalTracking!=''){
									totalTracking += ',';
							}
							totalTracking = totalTracking+ref;
						}
						if (isDup(totalTracking)) {
								alert('หมายเลข Tracking ของแต่ละร้าน ต้องไม่ซ้ำกัน');
								document.getElementById('ref-'+shopname).focus();
								return false;
						}
						tmpShopName = shopname;
				}

				//refund
				var current_status = document.getElementById('curr_stat-'+id).value;
				var current_amount = document.getElementById('curr_amount-'+id).value;
				if ((current_status==1) && (stt==2)) {
						refund[id] = {
							'oid': orderId,
							'opid': id,
							'cid': customer_id,
							'amount': numberify(current_amount)
						};	
				}
			}
			data['oid'] = orderId;
			data['grandTotalTh'] = grandTotalTh;
			data['grandTotalCn'] = grandTotalCn;
			data['totalTaobao'] = totalTaobao;
			data['totalTracking'] = totalTracking;
			data['btTran'] = btTran;
			data['btAmt'] = btAmt;
			data['unote'] = document.getElementById('unote').value;
			
			var result = true;
			var xhr = new XMLHttpRequest();
			xhr.open('POST','save_product.php',true);
			xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			xhr.onreadystatechange = function(){
				document.getElementById("loading").style.visibility = 'hidden';
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
			xhr.send('data='+JSON.stringify(data)+'&refund='+JSON.stringify(refund));			
		}
		
		function cancel(){
			window.location = 'index.php';
		}
		
		var orderNum = <?php echo '"'.$ono1.'"'; ?>;
		function confirmOrder(){
			var save = [];
			var data = {};
			var refund = {};
			<?php 
				for($i=0;$i<sizeof($save);$i++){
					echo 'save.push("'.$save[$i][0].'");';
				}
			?>
			var grandTotalTh = 0;
			var grandTotalCn = 0;
			var totalTaobao = '';
			var totalTracking = '';
			var tmpShopName = '';
			for(var i=0;i<save.length;i++) {
				var id = save[i];

				var chkflg = document.getElementById('ck-'+id).checked;
				var quan1 = parseInt(document.getElementById('quan1-'+id).textContent);
				var cpp1  = numberify(document.getElementById('cpp1-'+id).textContent);
				var tran1 = document.getElementById('tran1-'+id).textContent;
				var totalp1 = numberify(document.getElementById('tAmountTh1-'+id).textContent);
				var totalpCn1 = numberify(document.getElementById('tAmountCn1-'+id).textContent);
				var stt1 = document.getElementById('stt1-'+id).checked;
				var stt2 = document.getElementById('stt2-'+id).checked;
				var stt;
				//backshop
				var quan = document.getElementById('quan-'+id).value;
				var cpp = numberify(document.getElementById('cpp-'+id).value);
				var bTran = numberify(document.getElementById('bTran-'+id).value);
				//var taobao = document.getElementById('taobao-'+id).value;
				//var ref = document.getElementById('ref-'+id).value;
				var curr_ref = document.getElementById('curr_trck-'+id).value;
				var btTran = numberify(document.getElementById('btTran').textContent);
				var btAmt = numberify(document.getElementById('tamountTh').textContent);
				//new
				var allPriceYuan = numberify(document.getElementById('totalYuan-'+id).textContent);
				var btYuan = numberify(document.getElementById('totalCn-'+id).textContent);
				var ret = parseFloat(document.getElementById('refund-'+id).textContent.replace(/,/g, ''));
				
				if ((stt1==0)&&(stt2==0)) {
					alert("กรุณาเลือกสถานะการสั่ง");
					return 0;		//exit
				}
				if (stt1) {
					stt = 1;
					if (quan1<=0) {
							alert("จำนวนชิ้นสินค้าต้องมากกว่า 0");
							return 0;
					}
					if (cpp==0) {
						alert('กรุณากรอกข้อมูลให้ครบทุกช่อง');
						return 0;
					}
				}
				else if (stt2) {
					stt = 2;
						if (comment==0) {
							alert("กรุณาเลือกหมายเหตุ");
							return 0;
						}
				}

				//validte quan with quan1
				if (quan>quan1) {
					alert("จำนวนสินค้าที่สั่งได้ต้องไม่เกินจำนวนที่ลูกค้าสั่ง");
					document.getElementById('quan-'+id).focus();
					return;		//exit
				}

				//validte price with first_price
				// if (Number(cpp)>Number(cpp1)) {
				// 	alert("ราคาหลังร้านต้องไม่เกินราคาต่อชิ้นที่ลูกค้าสั่ง");
				// 	document.getElementById('cpp-'+id).focus();
				// 	return;		//exit
				// }

				//get shopname for taobao and tracking no.
				var shopname = document.getElementById('shopname-'+id).value;
				var taobao = document.getElementById('taobao-'+shopname).value;
				var ref = document.getElementById('ref-'+shopname).value;
				ref = removeDup(ref);
				var com = document.getElementById('com-'+shopname).value;

				//check taobao
				if (taobao=='') {
						alert('กรุณากรอกเลขที่ Taobao');
						document.getElementById('taobao-'+shopname).focus();
						return;		//exit
				}

				data[id] = {
					'quan1':quan1,
					'tran1':tran1,
					'stt':stt,
					'totalp1':totalp1,
					'totalpCn1':totalpCn1,
					'quan':quan,
					'cpp':cpp,
					'cpp1':cpp1,
					'tran':bTran,
					'taobao':taobao,
					'ref':ref,
					'totalp':numberify(document.getElementById('totalTh-'+id).textContent),
					'curr_ref': curr_ref,
					'apy': allPriceYuan,
					'btyuan': btYuan,
					'return': ret,
					'chkflg': chkflg,
					'company' : com,
					'rate': rate1
				};
				grandTotalTh = Number(grandTotalTh) + Number(totalp1);
				grandTotalCn = Number(grandTotalCn) + Number(totalpCn1);
				
				if (shopname!=tmpShopName) {
						if (taobao!='') {
							if (totalTaobao!=''){
								totalTaobao += ',';
							}
							totalTaobao = totalTaobao+taobao;
						}
						if (ref!='') {
							if (totalTracking!=''){
								totalTracking += ',';
							}
							totalTracking = totalTracking+ref;
						}
						if (isDup(totalTracking)) {
								alert('หมายเลข Tracking ของแต่ละร้าน ต้องไม่ซ้ำกัน');
								document.getElementById('ref-'+shopname).focus();
								return false;
						}
						tmpShopName=shopname;
				}

				//refund
				// var current_status = document.getElementById('curr_stat-'+id).value;
				// var current_amount = document.getElementById('curr_amount-'+id).value;
				// if ((current_status==1) && (stt==2)) {
				// 		refund[id] = {
				// 			'oid': orderId,
				// 			'opid': id,
				// 			'cid': customer_id,
				// 			'amount': numberify(current_amount)
				// 		};	
				// }

				if (ret<0) {
						refund[id] = {
								'quan1': quan1,
								'quan': quan,
								'cpp': cpp,
								'rate': rate1,
								'totalTh': ret,
								'oid': orderId,
								'ccode': ccode,
								'cpp1': cpp1
						};
				}
			}
			data['oid'] = orderId;
			data['grandTotalTh'] = grandTotalTh;
			data['grandTotalCn'] = grandTotalCn;
			data['totalTaobao'] = totalTaobao;
			data['totalTracking'] = totalTracking;
			data['btTran'] = btTran;
			data['btAmt'] = btAmt;
			data['unote'] = document.getElementById('unote').value;
			
			var result = true;
			var xhr = new XMLHttpRequest();
			xhr.open('POST','confirm_product.php',true);
			xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			xhr.onreadystatechange = function(){
				if(xhr.readyState==4 && xhr.status==200){
					document.getElementById("loading").style.visibility = 'hidden';
					if(xhr.responseText=='success'){
                        alert("บันทึกข้อมูลเรียบร้อยแล้ว");
						location.reload();
					}else{
						//alert('กรุณาใส่ข้อมูลให้ถูกต้องค่ะ!');
						alert(xhr.responseText);
					}
				}
			};
			document.getElementById("loading").style.visibility = 'visible';
			xhr.send('data='+JSON.stringify(data)+'&refund='+JSON.stringify(refund));
		}

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
				document.getElementById('bTran-'+opid).value = 0;
				calc1(opid);
			}
		}

		//get ID====================================================================================
		var shop = [];
		var opid = [];
		<?php 
			for($i=0;$i<sizeof($_shop);$i++) {
				echo 'opid.push("'.$_shop[$i][0].'");';
				echo 'shop.push("'.$_shop[$i][1].'");';
			}
		?>

		//interval----------------------------------------------------------------------
		setInterval(function(){
			//init====================================================================================
			//shop------------------------------
			var shop_totalQuan = 0;
			var shop_totalTran = 0;
			var shop_totalCn = 0;
			var shop_sumQuan = [];
			var shop_sumTran = [];
			var shop_sumCn = [];
			//----------------------------------
			//backshop--------------------------
			var totalQuan = 0;
			var totalTran = 0;
			var totalTh   = 0;
			var totalYuan = 0;
			var totalRefund = 0;
			var sumTh = [];
			var sumQuan = [];
			var sumTran = [];
			var sumYuan = [];
			var sumRefund = [];
			//----------------------------------

			//set empty=================================================================================
			for(var i=0;i<shop.length;i++) {
					//shop--------------------------
					shop_sumQuan[shop[i]] = 0;
					shop_sumTran[shop[i]] = 0;
					shop_sumCn[shop[i]]   = 0;
					//backshop----------------------
					sumQuan[shop[i]] = 0;
					sumTran[shop[i]] = 0;
					sumTh[shop[i]]   = 0;
					sumYuan[shop[i]] = 0;
					sumRefund[shop[i]] = 0;
			}

			//calculate==================================================================================
			for(var i=0;i<opid.length;i++) {
					//ck-opid
					var ck = document.getElementById('ck-'+opid[i]).checked;
					if (ck) {
						//shop--------------------------------------------------
						if (1) {
								//quan
								var shop_quan = numberify(document.getElementById("quan1-"+opid[i]).textContent);
								shop_sumQuan[shop[i]]+=Number(shop_quan);
								//tran
								var shop_tran = numberify(document.getElementById("tran1-"+opid[i]).textContent);
								shop_sumTran[shop[i]]+=Number(shop_tran);
								//amt Cn
								var shop_amtCn = parseFloat(document.getElementById('tAmountCn1-'+opid[i]).textContent.replace(/,/g, ''));
								shop_sumCn[shop[i]]+=Number(shop_amtCn);
								
								//grand total
								shop_totalQuan+=Number(shop_quan);
								shop_totalTran+=Number(shop_tran);
								shop_totalCn+=Number(shop_amtCn);
						}
						//backshop--------------------------------------------------
						if (1) {
								//quan
								var quan = numberify(document.getElementById("quan-"+opid[i]).value);
								sumQuan[shop[i]]+=Number(quan);
								//tran
								var tran = numberify(document.getElementById("bTran-"+opid[i]).value);
								sumTran[shop[i]]+=Number(tran);
								//amt Th
								var amtTh = numberify(document.getElementById("totalTh-"+opid[i]).textContent);
								sumTh[shop[i]]+=Number(amtTh);
								//amt Yuan
								var amtYuan = parseFloat(document.getElementById('totalYuan-'+opid[i]).textContent.replace(/,/g, ''));
								sumYuan[shop[i]]+=Number(amtYuan);
								//amt Ref
								var amtRef = parseFloat(document.getElementById('refund-'+opid[i]).textContent.replace(/,/g, ''));
								sumRefund[shop[i]]+=Number(amtRef);
								
								//grand total
								totalQuan+=Number(quan);
								totalTran+=Number(tran);
								totalTh+=Number(amtTh);	
								totalRefund+=Number(amtRef);
								totalYuan+=Number(amtYuan);
						}
					}
			}

			//total by shop======================================================================================
			for(var i=0;i<shop.length;i++) {
					//shop-----------------------------------
					document.getElementById('tquan1-'+shop[i]).textContent = shop_sumQuan[shop[i]];
					document.getElementById('tTran1-'+shop[i]).textContent = numWithCom(shop_sumTran[shop[i]]);
					document.getElementById('tAmountCn1-'+shop[i]).textContent = numWithCom(shop_sumCn[shop[i]]);
					document.getElementById('tAmountTh1-'+shop[i]).textContent = numWithCom(shop_sumCn[shop[i]]*rate1);

					//backshop-------------------------------
					document.getElementById('tquan-'+shop[i]).textContent = sumQuan[shop[i]];
					document.getElementById('btTran-'+shop[i]).textContent = numWithCom(sumTran[shop[i]]);
					document.getElementById('tamountCn-'+shop[i]).textContent = numWithCom(sumTh[shop[i]]/rate1);
					document.getElementById('tamountTh-'+shop[i]).textContent = numWithCom(sumTh[shop[i]]);
					document.getElementById('tyuan-'+shop[i]).textContent = numWithCom(sumYuan[shop[i]]);
					document.getElementById('trefund-'+shop[i]).textContent = numWithCom(sumRefund[shop[i]]);
			}

			//grand total=========================================================================================
			//shop--------------------------------------
			document.getElementById('tquan1').textContent = shop_totalQuan;
			document.getElementById('tTran1').textContent = numWithCom(shop_totalTran);
			document.getElementById('total-cn').textContent = numWithCom(shop_totalCn);
			document.getElementById('total-cn').value       = shop_totalCn;
			document.getElementById('total-th').textContent = numWithCom(shop_totalCn*rate1);
			document.getElementById('total-th').value       = shop_totalCn*rate1;

			//backshop----------------------------------
			document.getElementById('tquan').textContent = totalQuan;
			document.getElementById('btTran').textContent = numWithCom(totalTran);
			document.getElementById('tamountCn').textContent = numWithCom(totalTh/rate1);
			document.getElementById('tamountTh').textContent = numWithCom(totalTh);
			document.getElementById('tyuan').textContent = numWithCom(totalYuan);
			document.getElementById('trefund').textContent = numWithCom(totalRefund);
			document.getElementById('head-paid').textContent = document.getElementById('total-cn').textContent;
			document.getElementById('head-paid').value = document.getElementById('total-cn').value;
			document.getElementById('head-bought').textContent = numWithCom(totalTh/rate1);
			document.getElementById('head-cal').textContent = numWithCom(document.getElementById('head-paid').value.toFixed(2)-parseFloat(totalTh/rate1));
		},500);

		var backRefundOn = false;
		function backRefund(opid){
				document.getElementById('addBox').style.visibility = 'hidden';
				document.getElementById('refundBox').style.visibility = 'hidden';
				document.getElementById('emailBox').style.visibility = 'hidden';
				document.getElementById('emailLog').style.visibility = 'hidden';
				backRefundOn = !backRefundOn;
				if(backRefundOn){
						var ret = document.getElementById('ret-'+opid).value;
						if (ret!=2) {
							alert('รายการนี้ยังไม่ได้คืนเงิน');
							backRefundOn = !backRefundOn;
							return;
						}
						document.getElementById('backRefundBox').style.visibility = 'visible';
						var refund = document.getElementById('curr_refund-'+opid).value;
						document.getElementById('bref-total').textContent     = numWithCom(refund);
						
						//------------------------
						document.getElementById('btmp-refund').value = refund;
						document.getElementById('bref-oid').value   	= orderId;
						document.getElementById('bref-opid').value  	= opid;
						document.getElementById('bref-cid').value   	= customer_id;
				}
				else{
						document.getElementById('backRefundBox').style.visibility = 'hidden';
				}
		}

		var emailLogOn = false;
		function emailLog(opid){
				document.getElementById('addBox').style.visibility = 'hidden';
				document.getElementById('refundBox').style.visibility = 'hidden';
				document.getElementById('backRefundBox').style.visibility = 'hidden';
				document.getElementById('emailBox').style.visibility = 'hidden';
				emailLogOn = !emailLogOn;
				if(emailLogOn) {
						document.getElementById('emailLog').style.visibility = 'visible';
						var data = {}; 
						data['opid'] = opid;

						var result = true;
						var xhr = new XMLHttpRequest();
						xhr.open('POST','getLog.php',true);
						xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
						xhr.onreadystatechange = function(){
							if(xhr.readyState==4 && xhr.status==200) {
									var objs = JSON.parse(xhr.responseText);
									var len  = Object.keys(objs).length;
									for (i=0; i<len; i++) {
											var table = document.getElementById("email-table");
										    var row = table.insertRow(-1);
										    var cell = row.insertCell(-1);
										    cell.innerHTML = i+1;
										    var cell = row.insertCell(-1);
										    var date = new Date(objs[i]['date']);
										    cell.innerHTML = date.getDate()+'/'+date.getMonth()+'/'+date.getFullYear()
											var cell = row.insertCell(-1);
											var content = (objs[i]['content']);
										    cell.innerHTML = '<a onclick="showEmailLog(\''+content+'\')">'+objs[i]['subject']+'</a>';
									}
							}
						};
						xhr.send('data='+JSON.stringify(data));
				}
				else {
						clearEmailTable();
						document.getElementById('emailLog').style.visibility = 'hidden';
				}
		}

		function clearEmailTable() {
				var rowCount = document.getElementById('email-table').rows.length;
				for (i=0; i<rowCount-1; i++) {
						document.getElementById("email-table").deleteRow(-1);
				}
		}

		function showEmailLog(content) {
				alert(content);
		}

		var selectFlg = 0;
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
		<h2 style="color:#FF9900"><b><a href="product.php?order_id=<?php echo $oid?>">ดำเนินการสั่งซื้อ</a></b></h2>
        <h3 style="color:#FF9900"><a href="index.php">&larr; Back</a>  <a href="../index.php">&larr; Home</a></h3><br>
	
        <div class="menu">
		    <a href="#bottom">↓ไปล่างสุด</a>
			<i class="material-icons" onclick="exportProduct(<?php echo $oid;?>);" title="Export">&#xE24D;</i>
		</div>
        <div>
	        <div>
		        <table style="width:100%;">
					<tr>
						<td>เลขที่ออเดอร์  :</td><td id="order-id"><?php echo $ono1; ?></td><td></td>
						<td>วันที่ออเดอร์  :</td><td><?php if(isset($_oDate)){echo date("d/m/Y H:i:s",strtotime($_oDate));} ?></td><td></td>
						<td>ยอดที่ลูกค้าจ่าย  :</td><td width="25%" align="right" id="head-paid"></td><td>หยวน</td>
					</tr>
					<tr>
						<td></td><td></td><td></td>
						<td>วันที่ตัดจ่าย  :</td><td><?php if(isset($_oPaiddt)){echo date("d/m/Y H:i:s",strtotime($_oPaiddt));} ?></td><td></td>
						<td>ยอดที่สั่งซื้อ  :</td><td width="25%" align="right" id="head-bought"></td><td>หยวน</td>
					</tr>
					<tr class="punc">
						<td>สถานะรายการ :</td><td><?php echo $_codes[$code1]; ?></td><td></td>
						<td>Rate  :</td><td><?php echo number_format($rate1,4).'@'.(($_ratedt!='0000-00-00 00:00:00')? date_format(date_create($_ratedt),"d/m/Y H:i:s"): ''); ?></td><td></td>
						<td>ยอดที่คงเหลือ  :</td><td width="25%" align="right" id="head-cal"></td><td>หยวน</td>
					</tr>
				</table>
			</div>
		</div>
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

			//=======================================

			//code 3 - ชำระเงินแล้ว ดำเนินการสั่งซื้อ - able to edit
			//code 4 - ร้านค้ากำลังส่งสินค้ามาโกดังจีน - able to edit only Taobao, Tracking

			//=======================================

			$tquan = 0;
			$tamount = 0;
			$tthb = 0;
			$tCnCost = 0;
			$no = 1;
			$cncost = 0;
			$total = 0;

			$totalBs = 0;
			$tamountBs = 0;
			$tCnCostBS = 0;
			$firstShop = 1;

			//-------------------
			$grandTotalQuan = 0;
			$grandTotalPrice = 0;
			$grandTotalYuan = 0;
			$grandTotalTran = 0;
			$grandTotalCn = 0;
			$grandTotalTh = 0;
			$grandTotalRefund = 0;

			//loop shop-------------------
			foreach($shops as $key=>$item){
				$tquan_shop = 0;
				$ttran_shop = 0;
				$tamtcn_shop = 0;
				$tamtth_shop = 0;

				//--------------------------
				$totalQuan = 0;
				$totalYuan = 0;
				$totalTran = 0;
				$totalCn   = 0;
				$totalTh   = 0;
				$totalRefund = 0;
				//---------------------------
				$shopid = base64_decode($key);

				echo '<div><table class="order-product">';
				echo '<thead>';
				if ($firstShop) {
					echo '<th><button class="button-select" type="button" onclick="selectAll()">เลือกทั้งหมด</th>';
					$firstShop = 0;
				}
				else echo '<th><button style="visibility: hidden" class="button-select" type="button" onclick="selectAll()">เลือกทั้งหมด</th>';
				echo 	'<th width="2%">ลำดับ</th>'.
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
					//'<th width="10%">order taobao</th>'.
					//'<th width="10%" colspan="2">Tracking No.</th>'.
					'<th width="8%">ยอดคืนเงิน (บาท)</th>'.
					'<th>คืนเงิน</th>'.
					'<th>สถานะ</th>'.
					//'<th>email</th>'.
					//'<th>sent</th>'.
					'</thead>';

				//shopname------------------------------------
				$taobao = '';
				$trckNo = '';
				$company = '';
				for($i=0;$i<sizeof($item);$i++) {
						if (!empty($item[$i][28])) $taobao=$item[$i][28];
						if (!empty($item[$i][5])) $trckNo=$item[$i][5];
						$company = $item[$i][35];
				}
				if ($company!='') {
					$pieces = explode(",", $company);
					$company = $pieces[0];
				}
				echo '<thead class="shopname undivide">'.
					'<th><button class="button-select" type="button" onclick="selectByShop(\''.$key.'\')">เลือกทั้งร้าน</th>'.
					'<th colspan="8">'.'<span>ร้าน '.$shopid.'</span></th>'.
					'<th colspan="3"><span>Taobao</span><input id="taobao-'.$key.'" value="'.$taobao.'" style="width:70%;text-align:right;"></th>'.
					'<th colspan="2"><span>บริษัท</span><input id="com-'.$key.'" readonly value="'.$company.'" style="width:70%;text-align:right;"></th>'.
					'<th colspan="4"><span>Tracking</span><input id="ref-'.$key.'" value="'.$trckNo.'" style="width:60%;text-align:right;"><i class="material-icons" onclick="add(\''.$key.'\');" title="Add">add_circle</i><input type="hidden" id="com-'.$key.'" value="'.$item[0][33].'"></th>'.
					'</thead>';
				
				$puncCount = 0;
				//$tmpopid='';
				//by product----------------------------------------------------------
				for($i=0;$i<sizeof($item);$i++) {
					//check order_product_id
					// if ($item[$i][0]==$tmpopid) continue;
					// $tmpopid = $item[$i][0];
					//back shop---------------------------------------
					$totalBS = (($item[$i][24]*(double)$item[$i][11])+(double)$item[$i][12])*$orate;					
					$trckNo = $item[$i][5];
					$cncostBS = (double)$item[$i][12];
					$tCnCostBS += $cncostBS;
					$tamountBs += $totalBs;

					//total backshop---------------------------------------------------
					$quan       = $item[$i][24];
					$price      = $item[$i][11];
					$yuan       = $quan*$price;
					$tran       = $item[$i][12];
					$totalYuan += $yuan;
					$totalQuan += $quan;
					$totalTran += $tran;
					$totalCn   += ($yuan+$tran);
					$totalTh   += (($yuan+$tran)*$orate);
					//order product id
					$opid = $item[$i][0];
					
					//return flag
					$rtstat = $item[$i][30];
					$rtdesc = '';
					$disabled = '';
					if ($rtstat==2) {
							$disabled = 'disabled';
							$rtdesc = 'คืนแล้ว';
					}

					//order status--------------------------------------------------------------------------------------------
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
						$class = 'none';
						$option = '<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="1" id="stt1-'.$opid.'" onclick="checkStat1('.$opid.')" checked><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="2" id="stt2-'.$opid.'" onclick="checkStat2('.$opid.')"><label> ไม่ได้</label>'.
								'</div>';

						$bsQuan = '<input style="text-align:right;" class="tquan" id="quan-'.$opid.'" value="'.$item[$i][24].'" onkeyup="this.value=numberifyWithOutDot(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'quan-'.$opid.'\')" '.$disabled.'/>';			//quanltity
						$bsPrice = '<input style="text-align:right;" id="cpp-'.$opid.'" value="'.(double)$item[$i][11].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'cpp-'.$opid.'\')" '.$disabled.'/>';	//price
						$bsTran = '<input style="text-align:right;" class="btTran" id="bTran-'.$opid.'" value="'.number_format($cncostBS,2).'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'bTran-'.$opid.'\')" '.$disabled.'/>';
						$bsTaobao = '<input style="text-align:right;" id="taobao-'.$opid.'" value="'.$item[$i][28].'"/>';
						$bsTrack = '<input style="text-align:right;" id="ref-'.$opid.'" value="'.$trckNo.'" '.$disabled.'/>';
						
						//chkflg
						if ($item[$i][32]) {	
								$checkBox = '<input type="checkbox" id="ck-'.$opid.'" checked>';
						}
						else {
								$checkBox = '<input type="checkbox" id="ck-'.$opid.'">';
						}
						
						//total shop----------------------------------------------------------
						$tquan_shop += $item[$i][2];
						$ttran_shop += (double)$item[$i][6];
						$tamtcn_shop += ($item[$i][2]*(double)$item[$i][23])+(double)$item[$i][6];
						$tamtth_shop += $tamtcn_shop*$orate;
						//sum grand total------------------------------------
						$tquan += $item[$i][2];
						$total = ($item[$i][2]*(double)$item[$i][23])+(double)$item[$i][6];		
						$tamount += $total;
						$tthb += $total*$orate;
						if (empty($item[$i][6])) {
							$cncost=0;
						}
						else {
							$cncost = (double)$item[$i][6];
						}
						$tCnCost += $cncost;

						//grand total backshop--------------------------------------------
						$grandTotalQuan  += $quan;
						$grandTotalYuan  += $yuan;
						$grandTotalTran  += $tran;
						$grandTotalCn    += ($yuan+$tran);
						$grandTotalTh    += (($yuan+$tran)*$orate);
						$grandTotalRefund += (($total*$orate)-$totalBS);
						//---------------------

						$addTracking = '<i class="material-icons" onclick="add('.$opid.');" title="Add">add_circle</i>';
						$newEmail = '<i class="material-icons" onclick="emailBox('.$opid.');" title="Add">add_circle</i>';
						if ($code==4) {
							$refAction = '<a onclick="refund('.$opid.')">ตกลง</a> <a onclick="backRefund('.$opid.')">กลับ</a>';

							//****2016/11/06 disable input if order_status code=4
							$bsQuan = '<input style="text-align:right;" class="tquan" id="quan-'.$opid.'" value="'.$item[$i][24].'" onkeyup="this.value=numberifyWithOutDot(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'quan-'.$opid.'\')" disabled/>';			//quanltity
							$bsPrice = '<input style="text-align:right;" id="cpp-'.$opid.'" value="'.(double)$item[$i][11].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'cpp-'.$opid.'\')" disabled/>';	//price
							$bsTran = '<input style="text-align:right;" class="btTran" id="bTran-'.$opid.'" value="'.number_format($cncostBS,2).'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'bTran-'.$opid.'\')" disabled/>';
						}
						else {
								$refAction = '';
						}
					}
					//order fail
					else {
						$class = 'cancel';
						$option = '<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="1" id="stt1-'.$opid.'" onclick="checkStat1('.$opid.')"><label> ได้</label>'.
								'</div>'.
								'&nbsp;&nbsp;&nbsp;<div style="display:inline">'.
									'<input disabled="true" style="width:auto" type="checkbox" value="2" id="stt2-'.$opid.'" onclick="checkStat2('.$opid.')" checked><label> ไม่ได้</label>'.
								'</div>';
						$bsQuan = '<input disabled="true" style="text-align:right;color:red" class="tquan" id="quan-'.$opid.'" value="'.$item[$i][24].'" onkeyup="this.value=numberifyWithOutDot(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'quan-'.$opid.'\')" '.$disabled.'/>';			//quanltity
						$bsPrice = '<input disabled="true" style="text-align:right;color:red" id="cpp-'.$opid.'" value="'.(double)$item[$i][11].'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'cpp-'.$opid.'\')" '.$disabled.'/>';	//price
						$bsTran = '<input disabled="true" style="text-align:right;color:red" class="btTran" id="bTran-'.$opid.'" value="'.number_format($cncostBS,2).'" onkeyup="this.value=numberify(this.value);calc1(\''.$item[$i][0].'\');" onclick="checkZero(this.value,\'bTran-'.$opid.'\')" '.$disabled.'/>';			//transport china cost
						$bsTaobao = '<input disabled="true" style="text-align:right;color:red" id="taobao-'.$opid.'" value="'.$item[$i][28].'"/>';
						$bsTrack = '<input disabled="true" style="text-align:right;color:red" id="ref-'.$opid.'" value="'.$trckNo.'"/>';
						$checkBox = '';
						$cncost=0;
						$total=0;
						$addTracking = '<i class="material-icons" title="Add">add_circle</i>';
						$newEmail = '<i class="material-icons" onclick="" title="Add">add_circle</i>';
						if ($code==4) {
								$refAction = '<a onclick="">ตกลง</a> <a onclick="">กลับ</a>';
						}
						else {
								$refAction = '';
						}
					}
					//--------------------------------------------------------------------------------------

					//refund---------------------------
					$pmStat = $item[$i][34];
					$refund = ($total*$orate)-$totalBS;
					if ($refund==0) $refDesc = 'ชำระเงินเรียบร้อยแล้ว';
					else if ($refund>0) {
							if ($rtstat==1) $refDesc = 'อยู่ระหว่างคืนเงิน';
							if ($rtstat==2) $refDesc = 'คืนเงินเรียบร้อยแล้ว';
					}
					else if ($refund<0) {
							if ($pmStat==1) $refDesc = 'ชำระเงินเรียบร้อยแล้ว';
							else $refDesc = 'รอชำระเงินเพิ่ม';
					}
					$totalRefund += $refund;				
					if ($refund<0) {
						$ref = '<td align="right" style="color:red" class="'.$class.'">'.
						'<div id="refund-'.$opid.'">'.number_format($refund,2).'</div>'.
						//'<div>'.$refDesc.'</div>'.
						'</td>';
					}
					else {
						$ref = '<td align="right" class="'.$class.'">'.
						'<div id="refund-'.$opid.'">'.number_format($refund,2).'</div>'.
						//'<div>'.$refDesc.'</div>'.
						'</td>';
					}
					//----------------------------

					//table data set
					echo '<tr class="'.($puncCount%2==0? 'punc ':'').(($o_status==2)? 'cancel ':'blue').'">'.
					'<td width="2%">'.$checkBox.'</td>'.
						'<td align="center" class="none">'.$no.'</td>'.
						'<td><div style="float:left;"><a href="showImg.php?pid='.$item[$i][21].'" onclick="window.open(\'showImg.php?pid='.$item[$i][21].'\', \'_blank\', \'width=1024, height=768\'); return false;"><img height="150" width="150" src="'.$item[$i][13].'" title="'.$item[$i][18].' '.$item[$i][19].'"/></a></div>'.
						'<div align="center"><a href="'.$item[$i][22].'" onclick="window.open(\''.$item[$i][22].'\', \'_blank\', \'width=+screen.height,height=+screen.height,fullscreen=yes\'); return false"><img class="linkImg" height="20" width="20" src="../css/images/link.png"/></a></div></td>'.
						//'<td>'.$item[$i][18].'</td>'.
						//'<td>'.$item[$i][19].'</td>'.
						'<td align="right" class="quan1" id="quan1-'.$opid.'">'.$item[$i][2].'</td>'.		//quanltity
						'<td align="right" id="cpp1-'.$opid.'">'.number_format($item[$i][23],2).'</td>'.	//price
						'<td align="right" class="tTran" id="tran1-'.$opid.'">'.number_format($cncost,2).'</td>'.											//transport china cost
						'<td align="right" class="tAmountCn1" id="tAmountCn1-'.$opid.'">'.number_format($total,2).'</td>'.							//total chinese
						'<td align="right" class="tAmountTh1" id="tAmountTh1-'.$opid.'">'.number_format($total*$orate,2).'</td>'.					//total thai	
						'<td align="center">'.$option.'</td>';
						/*'<td><select id="rem-'.$opid.'" disabled="true">';
						foreach ($_remark as $key => $value) {
								if($key==$item[$i][27]) echo '<option value="'.$key.'" selected>'.$value.'</option>';
								else echo '<option value="'.$key.'">'.$value.'</option>';
						}*/

						echo '</select></td>'.
						'<td>'.$bsQuan.'</td>'.		//quanltity
						'<td>'.$bsPrice.'</td>'.	//price
						'<td align="right" class="'.$class.'" id="totalYuan-'.$opid.'">'.number_format($yuan,2).'</td>'. //total yuan
						'<td>'.$bsTran.'</td>'.		//transport china cost
						'<td align="right" class="'.$class.'" id="totalCn-'.$opid.'">'.number_format(($totalBS/$orate),2).'</td>'.
						'<td align="right" class="tamount '.$class.'" id="totalTh-'.$opid.'">'.number_format($totalBS,2).'</td>';
						//'<td>'.$bsTaobao.'</td>'.
						//'<td>'.$bsTrack.'</td>'.
						//'<td>'.$addTracking.'</td>';

						//$emailNo = $item[$i][31];
						//if ($emailNo==0) $emailNo='-';
						echo $ref.			//refund
						'<td>'.$refAction.'</td>'.
						'<td class="'.$class.'" id="rtstat-'.$opid.'">'.$rtdesc.'</td>'.
						//'<td>'.$newEmail.'</td>'.
						//'<td align="center" class="'.$class.'"><a onclick="emailLog(\''.$opid.'\')">'.$emailNo.'</a></td>'.
						'<input type="hidden" id="curr_refund-'.$opid.'" value="'.$refund.'" />'.
						'<input type="hidden" id="ret-'.$opid.'" value="'.$rtstat.'" />'.
						'<input type="hidden" id="curr_received-'.$opid.'" value="'.$item[$i][24].'" />'.
						'<input type="hidden" id="curr_price-'.$opid.'" value="'.$item[$i][11].'" />'.
						'<input type="hidden" id="curr_tran-'.$opid.'" value="'.$item[$i][12].'" />'.
						'<input type="hidden" id="curr_stat-'.$opid.'" value="'.$o_status.'" />'.
						'<input type="hidden" id="curr_amount-'.$opid.'" value="'.number_format($total*$orate,2).'" />'.
						'<input type="hidden" id="curr_trck-'.$opid.'" value="'.$trckNo.'" />'.
						'<input type="hidden" id="shopname-'.$opid.'" value="'.$key.'" />'.
						'</tr>';
						$puncCount++;
						$no++;
				}
				//total by shop-----------------------------------------------------------
				//echo '<tbody class="padding '.$class.'">'.
				echo '<tbody class="padding">'.
					'<td></td><td colspan="2" class="cancel">ยอดรวม</td>'.
					'<td align="right" id="tquan1-'.$shopid.'">'.$tquan_shop.'</td><td></td>'.
					'<td align="right" id="tTran1-'.$shopid.'">'.number_format($ttran_shop,2).'</td>'.
					'<td align="right" id="tAmountCn1-'.$shopid.'">'.number_format($tamtcn_shop,2).'</td>'.
					'<td align="right" id="tAmountTh1-'.$shopid.'">'.number_format($tamtth_shop,2).'</td>'.
					'<td></td>'.
					'<td align="right" id="tquan-'.$shopid.'">'.$totalQuan.'</td>'.
					'<td></td>'.
					'<td align="right" id="tyuan-'.$shopid.'">'.number_format($totalYuan,2).'</td>'.
					'<td align="right" id="btTran-'.$shopid.'">'.number_format($totalTran,2).'</td>'.
					'<td align="right" id="tamountCn-'.$shopid.'">'.number_format($totalCn,2).'</td>'.
					'<td align="right" id="tamountTh-'.$shopid.'">'.number_format($totalTh,2).'</td>'.
					'<td align="right" id="trefund-'.$shopid.'">'.number_format($totalRefund,2).'</td>'.
					'<td></td><td></td>'.
					'</tbody><br>';
			}

			//grand total-----------------------------------------------------------------
			echo '<tbody class="padding" style="font-size:14px;">'.
			'<td></td><td class="cancel" colspan="2">ยอดรวมทั้งหมด</td>'.
			'<td align="right" id="tquan1">'.$tquan.'</td><td></td>'.
			'<td align="right" id="tTran1">'.number_format($tCnCost,2).'</td>'.
			'<td align="right" id="total-cn">'.number_format($tamount,2).'</td>'.
			'<td align="right" id="total-th">'.number_format($tthb,2).'</td>'.
			'<td></td>'.
			'<td align="right" id="tquan">'.$grandTotalQuan.'</td>'.
			'<td></td>'.
			'<td align="right" id="tyuan">'.number_format($grandTotalYuan,2).'</td>'.
			'<td align="right" id="btTran">'.number_format($grandTotalTran,2).'</td>'.
			'<td align="right" id="tamountCn">'.number_format($grandTotalCn,2).'</td>'.
			'<td align="right" id="tamountTh">'.number_format($grandTotalTh,2).'</td>'.
			'<td align="right" id="trefund">'.number_format($grandTotalRefund,2).'</td>'.
			'<td></td><td></td>'.
			'</tbody></table></div><br>';

			//comment
			echo '<div style="text-align:center;">';
					echo '<span style="vertical-align:top;font-weight:bold;">Customer note : </span><span><textarea style="font-size:16px;width:40%;height:100px;" readonly>'.$_cnote.'</textarea></span>';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;<span style="vertical-align:top;font-weight:bold;">User note : </span><span><textarea id="unote" style="font-size:16px;width:40%;height:100px;">'.$_unote.'</textarea></span>';
			echo '</div>';
		?>
		<br>
		<div id="bottom"><a href="#top">↑กลับสู่ด้านบน</a><div>
		<div align="center" style="left:0;right:0;margin-left:auto;margin-right:auto;">
			<button class="order-button" onclick="save()">บันทึก</button>
			<button class="order-cancel" onclick="cancel()">กลับ</button>
			<?php
					if ($code==3) {
							echo '<button class="order-update" onclick="confirmOrder()">สั่งซื้อเรียบร้อย</button>';
					}
			?>
			<button class="order-email" onclick="emailBox()">Email</button>
			<?php
					if ($code!=3) {
							echo '<button class="order-update" onclick="allReturn('.$oid.')">คืนเงินทั้งหมด</button>';
					}
			?>
		</div>
		<br>
	</body>

<!--Add Box-->
<!-- <div id="addBox" class="bgwrap">
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
				<input type="hidden" name="oid" id="oid" value="">
				<input type="hidden" name="opid" id="opid" value="">
				<input type="hidden" name="tracking_curr" id="tracking_curr" value="">
				<input type="hidden" name="add" value="1"/>
				<a onclick="add();">Cancel</a>&emsp;<button>Add</button>
			</div>
			</form>
		</div>
</div>
 -->
<!--Refund Box-->
<!-- <div id="refundBox" class="bgwrap">
		<div class="container" style="width:800px">
			<div class="containerheader">
        		<h2 id="title">ยอดคืนเงิน</h2>
     		</div>
			
			<form method="post">		
     		<div>
        		<table style="width:700px">
        			<tr>
        				<th>จำนวนที่สั่ง</th>
        				<th>จำนวนที่สั่งได้</th>
        				<th>ขาด</th>
        				<th>ราคา/ชิ้น (หยวน)</th>
        				<th>รวม (หยวน)</th>
        				<th>เรท</th>
        				<th>รวม (บาท)</th>
        			</tr>
        			<tr>
        				<th id="ref-ordered"></th>
        				<th id="ref-received"></th>
        				<th id="ref-missed"></th>
        				<th id="ref-price"></th>
        				<th id="ref-totalCn"></th>
        				<th id="ref-rate"></th>
        				<th id="ref-totalTh"></th>
        			</tr>
        			<tr>
        				<th colspan="4">ร้านเรียกเก็บค่ารถเพิ่ม</th>
        				<th id="ref-TranCn"></th>
        				<th id="ref-TranRate"></th>
        				<th id="ref-TranTh"></th>
        			</tr>
				</table>
			</div>

			<div>
				<table style="width:700px;">
        			<tr>
        				<th style="text-align:center;font-size:18px;">ยอดคืนเงิน</th>
        				<th style="text-align:center;font-size:18px;" id="ref-total"></th>
        				<th style="text-align:left;font-size:18px;">บาท</th>
        			</tr>
				</table>
			</div>
		
			<div style="text-align:center;padding:10px">
				<input type="hidden" name="ref-oid" id="ref-oid" value="">
				<input type="hidden" name="ref-opid" id="ref-opid" value="">
				<input type="hidden" name="ref-cid" id="ref-cid" value="">
				<input type="hidden" name="tmp-ordered" id="tmp-ordered" value="">
				<input type="hidden" name="tmp-received" id="tmp-received" value="">
				<input type="hidden" name="tmp-missed" id="tmp-missed" value="">
				<input type="hidden" name="tmp-price" id="tmp-price" value="">
				<input type="hidden" name="tmp-totalCn" id="tmp-totalCn" value="">
				<input type="hidden" name="tmp-rate" id="tmp-rate" value="">
				<input type="hidden" name="tmp-tran" id="tmp-tran" value="">
				<input type="hidden" name="tmp-total" id="tmp-total" value="">
				<input type="hidden" name="refund" value="1"/>
				<button class="order-button">ตกลง</button>
				<a onclick="refund();"><button class="order-cancel" type="button">กลับ</button></a>
			</div>
			</form>
		</div>
</div> -->

<!--BackRefund Box-->
<div id="backRefundBox" class="bgwrap">
		<div class="container" style="width:800px">
			<form method="post">		
     		<div>
        		<table style="width:700px">
        			<tr>
        				<th style="text-align:center;font-size:18px;">ต้องการนำยอดคืนเงินกลับสู่ระบบ</th>
        			</tr>
				</table>
			</div>

			<div>
				<table style="width:700px;">
        			<tr>
        				<th style="text-align:center;font-size:18px;">ยอดเงิน</th>
        				<th style="text-align:center;font-size:18px;" id="bref-total"></th>
        				<th style="text-align:left;font-size:18px;">บาท</th>
        			</tr>
				</table>
			</div>
		
			<div style="text-align:center;padding:10px">
				<input type="hidden" name="bref-oid" id="bref-oid" value="">
				<input type="hidden" name="bref-opid" id="bref-opid" value="">
				<input type="hidden" name="bref-cid" id="bref-cid" value="">
				<input type="hidden" name="btmp-refund" id="btmp-refund" value="">
				<input type="hidden" name="backRefund" value="1"/>
				<button class="order-button">ตกลง</button>
				<a onclick="backRefund();"><button class="order-cancel" type="button">กลับ</button></a>
			</div>
			</form>
		</div>
</div>

<!--email log-->
<div id="emailLog" class="bgwrap">
		<div class="container" style="width:800px;"">
			<div class="containerheader">
        		<h2 id="title">ประวัติการส่งอีเมลล์</h2>
     		</div>

     		<div style="overflow-y:scroll;max-height: 330px;">
     			<table id="email-table" style="width:700px;text-align:center;border-style:solid;">
 						<th style="text-align:center;">ลำดับที่</th>
 						<th style="text-align:center;">วันที่ส่งอีเมลล์</th>
 						<th style="text-align:center;">Subject</th>
     			</table>
			</div>
			
			<div style="text-align:center;padding:10px">
				<a onclick="emailLog();"><button class="order-cancel" type="button">กลับ</button></a>
			</div>
		</div>
</div>
<BR><BR>
</html>

<?php
	include './dialog/loading.php';
	include './dialog/emailBox.php';
	include './dialog/refundBox.php';
	include './dialog/trackingBox.php';
	$con->close();
?>

<script type="text/javascript">
		$("textarea").keydown(function(e) {
			    if(e.keyCode === 9) { // tab was pressed
			        // get caret position/selection
			        var start = this.selectionStart;
			            end = this.selectionEnd;

			        var $this = $(this);

			        // set textarea value to: text before caret + tab + text after caret
			        $this.val($this.val().substring(0, start)
			                    + "\t"
			                    + $this.val().substring(end));

			        // put caret at right position again
			        this.selectionStart = this.selectionEnd = start + 1;

			        // prevent the focus lose
			        return false;
			    }
		});
		$('#ref-form').submit( function(event) {
		    var formId = this.id,
		        form = this;
		    event.preventDefault();
		    $('#ref-submit').css("background","#009688");
		    $('#ref-submit').css("color","#fff");
		    $('#ref-submit').prop('disabled', true);
		    
		    form.submit();
		});

		var _save = [];
		<?php
			for($i=0;$i<sizeof($save);$i++){
				echo '_save.push("'.$save[$i][0].'");';
			}
		?>
</script>

<script src="./controller.js"></script>
<script type="text/javascript">initProduct();</script>