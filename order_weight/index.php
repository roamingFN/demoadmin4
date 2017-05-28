<!DOCTYPE html>
<html>
	<head>
		<title>Order Weight</title>
		<meta charset="utf-8">                 
		<script src="https://code.jquery.com/jquery-3.0.0.min.js" integrity="sha256-JmvOoLtYsmqlsWxa7mDSLMwa6dZ9rrIdtrrVYRnDRH0=" crossorigin="anonymous"></script>
		<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.7/angular.min.js"></script> -->
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link rel="stylesheet" type="text/css" href="../css/w3-teal.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.min.css" />
		
		<style type="text/css">
			.weight-customer-code {
				margin-top: 10px;
				padding-left: 0px;
				margin-bottom: 10px;
				width: 200px;
				height: 20px;
			}
		</style>

		<?php
    			session_start();
                if (!isset($_SESSION['ID'])) {
                	header("Location: ../login.php");
                }
                        
				include '../database.php';
				include '../utility/permission.php';

				const FORMID = 6;
				$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
				$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
				if ($_adminFlg==0) {
						if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
				}
        		
        		$sql = '';
        		$sql1 = '';
        		$sql2 = '';
        		$_save1 = array();
        		$_save2 = array();
        		$_shopName = '';
        		$_pType = array();

        		$done = array();
        		$child = array();
        		$cidToPrint = array();
				$printCid = '';
				if(!empty($_GET['tracking'])) {
						// $sql1 = 'SELECT pt.order_product_tracking_id,pt.order_product_id,pt.order_id,pt.tracking_no,pt.width,pt.length,pt.height,pt.m3,pt.weight,pt.rate,pt.total,pt.received_amount,pt.remark'.
						// ',o.order_number,o.customer_id'.
						// ',op.producttypeid'.
						// ' FROM customer_order_product_tracking pt'.
						// ' JOIN customer_order o ON o.order_id=pt.order_id'.
						// ' JOIN customer_order_product op ON pt.order_product_id=op.order_product_id'.
						// ' WHERE pt.tracking_no=\''.$_GET['tracking'].'\' AND (op.current_status>=6 AND op.current_status!=99)'.
						// ' GROUP BY o.order_number ORDER BY o.order_number';
						$sql1 = 'SELECT pt.order_product_tracking_id,pt.order_product_id,pt.order_id,pt.tracking_no,pt.width,pt.length,pt.height,pt.m3,pt.weight,pt.rate,pt.total,pt.received_amount,pt.remark,o.order_number,o.customer_id, c.customer_code,op.producttypeid,pt.statusid'. 
						' FROM customer_order_product_tracking pt'. 
						' JOIN customer_order o ON o.order_id=pt.order_id'. 
						' JOIN customer_order_product op ON pt.order_product_id=op.order_product_id'. 
						' JOIN customer c ON o.customer_id=c.customer_id'. 
						' WHERE pt.tracking_no=\''.$_GET['tracking'].'\' AND (op.current_status>=6 AND op.current_status!=99) AND masterflg=1'. 
						' GROUP BY o.order_number ORDER BY o.order_number';
						if ($stmt = $con->prepare($sql1)) {
                			$stmt->execute();
                			$stmt->bind_result($tid,$opid,$oid,$tno,$width,$length,$height,$m3,$weight,$rate,$total,$amount,$remark,$ono,$cid,$ccode,$ptid,$statusid);
                			while ($stmt->fetch()) {     
								array_push($cidToPrint, $cid);		
                				array_push($_save1,$tid);
                			}
                		}
                		//echo $sql1;
                		$printCid = $cidToPrint[0];      		

						// $sql2 = 'SELECT ot.order_product_tracking_id,ot.tracking_no,ot.order_product_id,ot.order_id,ot.received_amount'.
						// ',op.quantity'.
						// ',p.product_name,p.product_img,p.product_url,p.shop_name'.
						// ',o.order_number, o.customer_id, ot.uid, ot.last_edit_date'.
						// ' FROM customer_order_product_tracking ot'. 
						// ' JOIN customer_order_product op ON ot.order_product_id=op.order_product_id'.
						// ' JOIN product p ON op.product_id=p.product_id'.
						// ' JOIN customer_order o ON o.order_id=op.order_id'.
						// // ' JOIN user u ON ot.uid=u.userid'.
						// ' WHERE ot.tracking_no=\''.$_GET['tracking'].'\' AND (op.current_status>=6 AND op.current_status!=99)'.
						// ' ORDER BY o.order_number';
						$sql2 = 'SELECT ot.order_product_tracking_id,ot.tracking_no,ot.order_product_id,ot.order_id,ot.received_amount'. 
						',op.backshop_quantity, c.customer_code'. 
						',p.product_name,p.product_img,p.product_url,p.shop_name'. 
						',o.order_number, o.customer_id, ot.uid, ot.last_edit_date,p.product_color,p.product_size'. 
						' FROM customer_order_product_tracking ot'. 
						' JOIN customer_order_product op ON ot.order_product_id=op.order_product_id'. 
						' JOIN product p ON op.product_id=p.product_id'. 
						' JOIN customer_order o ON o.order_id=op.order_id'. 
						' JOIN customer c ON o.customer_id=c.customer_id'. 
						// ' JOIN user u ON ot.uid=u.userid'. 
						' WHERE ot.tracking_no=\''.$_GET['tracking'].'\' AND (op.current_status>=6 AND op.current_status!=99)'. 
						' ORDER BY o.order_number,ot.order_product_tracking_id ASC';
						if ($stmt = $con->prepare($sql2)) {
                			$stmt->execute();
                			$stmt->bind_result($tid,$tno,$opid,$oid,$amount,$quantity,$ccode,$pname,$pimg,$purl,$sname,$ono,$customerID,$userID, $lastEditDate,$pColor,$pSize);
                			while ($stmt->fetch()) {
                				array_push($_save2,$tid);
                				$_shopName = $sname;
                			}
                		}
                }
                else {		//first load
                		$_GET["tracking"] = '';
                }
                

				function getOrderNumber($oid) {
					include '../database.php';
					$result = '';
					$sql = 'SELECT order_id,order_number FROM customer_order WHERE order_id='.$oid;
					if ($stmt = $con->prepare($sql)) {
                		$stmt->execute();
						$stmt->bind_result($oid,$ono);
						while ($stmt->fetch()) {
							$result = $ono;
						}
						$stmt->close();
					}
					return $result;
				}

				function getSumReceived($opid,$oid) {
					include '../database.php';
					$result = '';
					$sql = 'SELECT SUM(received_amount) FROM customer_order_product_tracking'.
                			' WHERE order_product_id='.$opid.' AND order_id='.$oid;
					if ($stmt = $con->prepare($sql)) {
                		$stmt->execute();
						$stmt->bind_result($sum);
						while ($stmt->fetch()) {
							$result = $sum;
						}
						$stmt->close();
					}
					return $result;	
				}

				//get product type
				$_pType[0] = "-";
				if($stmt = $con->prepare('SELECT producttypeid,producttypename,rate_type,product_type FROM product_type ORDER BY producttypename')){
						$stmt->execute();
						$stmt->bind_result($ptid,$ptname,$rate,$type);
						while($stmt->fetch()){
								$_pType[$ptid] = $ptname;
						}
				}

		?>
		<script>
			var cid = <?php echo "'".$printCid."'"; ?>;
			var tracking_no = <?php echo "'".$_GET["tracking"]."'"; ?>;
			function cal(id) {
				var width = document.getElementById('width-'+id).value;
				var length = document.getElementById('length-'+id).value;
				var height = document.getElementById('height-'+id).value;
				
				document.getElementById('m3-'+id).textContent = ((Number(width) * Number(length) * Number(height))/Math.pow(10,6)).toFixed(4);
			}

			function calTotal(id) {
					var quan = document.getElementById('quan-'+id).textContent;
					var received = document.getElementById('rec-'+id).textContent;
					var amount = Number(document.getElementById('add-'+id).value);
					if (isNaN(amount)) amount=0;

					document.getElementById('missing-'+id).textContent = Number(quan) - (Number(received) + amount);

					//cal total
					var addTotal = 0;
					var missingTotal = 0;
					$('.received tbody tr').each(function () {
						var add = Number($(this).find("input").eq(0).val());
						if (isNaN(add)) add=0;
						addTotal += add;

						var missing = Number($(this).find("td").eq(6).text());
						console.log(missing);
						missingTotal += missing;
					});
					$('.received tfoot').find("td").eq(4).text(addTotal);
					$('.received tfoot').find("td").eq(5).text(missingTotal);

			}

			function numberWithCommas(x) {
				//console.log(Number(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				return Number(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}

			function numberify(txt) {
				return txt.replace(/[^0-9.]/g,'');
			}

			function checkZero(val,id) {
					if (val==0) {
						document.getElementById(id).value = '';
					}
			}

			function save() {
				//if no tracking no.
				if (tracking_no=='') return;

				var save1 = [];
				var data1 = {};
				var userID = <?php echo '"'.$_SESSION['ID'].'"' ?>; // find userID
				//console.log(userID);
				<?php
					for($i=0;$i<sizeof($_save1);$i++) {
							echo 'save1.push("'.$_save1[$i].'");';
					}
				?>

				for(var i=0;i<save1.length;i++) {

						var tid = save1[i];
						// console.log(tid)
						var width = document.getElementById('width-'+tid).value;
						var length = document.getElementById('length-'+tid).value;
						var height = document.getElementById('height-'+tid).value;

						var m3 = Number(document.getElementById('m3-'+tid).textContent).toFixed(4);
						if (isNaN(m3)) m3=0;
						var weight = document.getElementById('weight-'+tid).value;

						var tno = document.getElementById('tno').value;
						var oid = document.getElementById('oid-'+tid).value;
						var ptid = document.getElementById('ptid-'+tid).value;
						var cid = document.getElementById('cid-'+tid).value;
						var remark = '';
						if (document.getElementById('remark-'+tid)) {
							remark = document.getElementById('remark-'+tid).value;
						}else{
							remark = "-";
						}

						//validate input
						if ((width=='') || (width==0)) {
								document.getElementById('width-'+tid).focus();
								alert('กรุณากรอกความกว้าง');
								return;
						}
						if ((length=='') || (length==0)){
								document.getElementById('length-'+tid).focus();
								alert('กรุณากรอกความยาว');
								return;
						}
						if ((height=='') || (height==0)) {
								document.getElementById('height-'+tid).focus();
								alert('กรุณากรอกความสูง');
								return;
						}
						if ((weight=='') || (weight==0)) {
								document.getElementById('weight-'+tid).focus();
								alert('กรุณากรอกน้ำหนัก');
								return;
						}

						data1[tid] = {
							'width': width,
							'length': length,
							'height': height,
							'm3':m3,
							'weight':weight,
							'tno':tno,
							'oid':oid,
							'ptid':ptid,
							'cid':cid,
							'remark':remark
						};
				}

				var save2 = [];
				var data2 = {};
				
				<?php
					for($i=0;$i<sizeof($_save2);$i++) {
						echo 'save2.push("'.$_save2[$i].'");';
					}
				?>

				for(var i=0;i<save2.length;i++) {
						var tid = save2[i];
						var userID = <?php echo '"'.$_SESSION['ID'].'"' ?>; // find userID
						//console.log(userID);
						// var amount = '';
						// var quan = parseInt(document.getElementById('quan-'+tid).textContent);
						// if(document.getElementById('amount-'+tid)){
						// 	amount = parseInt(document.getElementById('amount-'+tid).value);
						// }else {
						// 	amount = "0.00";
						// }
						// if (amount>quan) {
						// 		alert('จำนวนรวมต้องไม่มากกว่าจำนวนที่สั่ง');
						// 		document.getElementById('amount-'+tid).focus();
						// 		return;
						// }
						// var total = parseInt(document.getElementById('total-'+tid).textContent);
						// var sumRec = parseInt(document.getElementById('quan-'+tid).textContent);

						// if (total>sumRec) {
						// 		alert('จำนวนรวมต้องไม่มากกว่าจำนวนที่สั่ง');
						// 		document.getElementById('amount-'+tid).focus();
						// 		return;
						// }
						var quan = document.getElementById('quan-'+tid).textContent;

						var missing = parseInt(document.getElementById('missing-'+tid).textContent);
						if (missing<0 || missing>quan) {
								alert('จำนวนรับเพิ่มเกินจำนวนที่สั่งได้');
								document.getElementById('add-'+tid).focus();
								return;
						}

						var amount = parseInt(document.getElementById('add-'+tid).value);
						if (isNaN(amount)) {
							alert('จำนวนรับเพิ่มไม่ถูกต้อง');
							document.getElementById('add-'+tid).focus();
							return;
						}

						data2[tid] = {
							'amount':amount,
							'backshop_amount': quan,
							'userAdd': userID
						}; 
				}
				//console.log('data 2 : ',data2);
				var result = true;
				var xhr = new XMLHttpRequest();
				xhr.open('POST','save.php',true);
				xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
				xhr.onreadystatechange = function(){
					if(xhr.readyState==4 && xhr.status==200){
						document.getElementById("loading").style.visibility = 'hidden';
						if(xhr.responseText=='success'){
                        	alert("บันทึกข้อมูลเรียบร้อยแล้ว");
							location.reload();
						} else{
							//alert('กรุณาใส่ข้อมูลให้ถูกต้องค่ะ!');
							alert(xhr.responseText);
							location.reload();
						}
					}
				};
				document.getElementById("loading").style.visibility = 'visible';
				xhr.send('data1='+JSON.stringify(data1)+'&data2='+JSON.stringify(data2));
			}
			function cancel(){
				window.location = '../index.php';
			}

			function print() {
				if (tracking_no == '') {
					alert('ไม่พบข้อมูล');
					return 0;
				}

				var url = 'print.php?cid='+cid+'&tracking_no='+tracking_no;
				// var url = 'print.php?tracking_no='+tracking_no;
				window.open(url,'_blank');
			}
		</script>
	</head>

	<body>
       	<h1><b><a href="index.php">ใส่น้ำหนัก</a></b></h1>
        <h3><a href="../index.php">&larr; Back</a>  <a href="../index.php">&larr; Home</a></h3><br></h3>
        <div class="menu">
			<i class="material-icons" onclick="showSearchDialog();" title="Search">&#xE880;</i>
		</div>
		
        <!-- tracking table -->
        <div>
        	<table style="text-align:center;">
        		<tr>
        			<td>Tracking No. :&nbsp;&nbsp;
        			<form action="index.php" method="get" id="form1">
        				<input name="tracking" value=<?php if(!empty($_GET['tracking'])){ echo '"'.$_GET['tracking'].'"';}?> >
        			</form>&nbsp;&nbsp;
        			<button type="submit" form="form1" value="submit">Search</button></td>
        		</tr>
        	</table>
        </div>
		<table class="order-product">
                <tr>
                	<th>หมายเลขลูกค้า</th>
					<th>เลขที่ Order</th>
					<th>Tracking no.</th>
					<th>กว้าง(cm)</th>
					<th>ยาว(cm)</th>
					<th>สูง(cm)</th>
					<th>ขนาด(คิว)</th>
					<th>น้ำหนัก(kg)</th>
					<th width="20%">หมายเหตุ</th>
                </tr>

               	<?php
               			$saveFlg = 0;
                		if ($stmt = $con->prepare($sql1)) {
                			$stmt->execute();
                			$stmt->bind_result($tid,$opid,$oid,$tno,$width,$length,$height,$m3,$weight,$rate,$total,$amount,$remark,$ono,$cid,$ccode,$ptid,$statusid);
							$puncCount = 0;							

							// echo $result;
							if ($statusid==0) {
								$disable = 0;
								$disStatId = 0;
							}
							else {
								$disable = 1;
								$disStatId = 1;
								$saveFlg = 1;
							}
							while ($stmt->fetch()) {
									echo '<tr class="'.($puncCount%2==0? 'punc ':'').'">';
									echo '<td align="center">'.$ccode.'</td>'; 
									echo '<td align="center"><a href="product.php?order_id='.$oid.'" target="_blank">'.$ono.'</a></td>';
									echo '<td align="center">'.$tno.'</td>';
									if ($disable == 0) {
										echo '<td>'.
										'<input style="text-align:right;" class="num" tid="'.$tid.'" id="width-'.$tid.'" value="'.$width.'" onclick="checkZero(this.value,\'width-'.$tid.'\')"/>'.
										'</td>';
										echo '<td>'.
										'<input style="text-align:right;" class="num" tid="'.$tid.'" id="length-'.$tid.'" value="'.$length.'" onclick="checkZero(this.value,\'length-'.$tid.'\')"/>'.
										'</td>';
										echo '<td>'.
										'<input style="text-align:right;" class="num" tid="'.$tid.'" id="height-'.$tid.'" value="'.$height.'" onclick="checkZero(this.value,\'height-'.$tid.'\')"/>'.
										'</td>';
										echo '<td style="text-align:right;" id="m3-'.$tid.'">'.number_format($m3,4).'</td>';
										echo '<td>'.
										'<input style="text-align:right;" class="weight" id="weight-'.$tid.'" value="'.$weight.'" onclick="checkZero(this.value,\'weight-'.$tid.'\')"/>'.
										'<input type="hidden" id="tno" value='.$tno.'>'.
										'<input type="hidden" id="oid-'.$tid.'" value='.$oid.'>'.
										'<input type="hidden" id="ptid-'.$tid.'" value='.$ptid.'>'.
										'<input type="hidden" id="cid-'.$tid.'" value='.$cid.'>'.
										'</td>';
										echo '<td><input id="remark-'.$tid.'" value="'.$remark.'" style="text-align:center" type="text" ></td>';
										
									}
									else {
										echo '<td>'.
										'<input disabled style="text-align:right;" tid="'.$tid.'" class="num" id="width-'.$tid.'" value="'.$width.'" onclick="checkZero(this.value,\'width-'.$tid.'\')"/>'.
										'</td>';
										echo '<td>'.
										'<input disabled style="text-align:right;" tid="'.$tid.'" class="num" id="length-'.$tid.'" value="'.$length.'"  onclick="checkZero(this.value,\'length-'.$tid.'\')"/>'.
										'</td>';
										echo '<td>'.
										'<input disabled style="text-align:right;" tid="'.$tid.'" class="num" id="height-'.$tid.'" value="'.$height.'" onclick="checkZero(this.value,\'height-'.$tid.'\')"/>'.
										'</td>';
										echo '<td style="text-align:right;" id="m3-'.$tid.'">'.number_format($m3,4).'</td>';
										echo '<td>'.
										'<input disabled style="text-align:right;" tid="'.$tid.'" class="weight" id="weight-'.$tid.'" value="'.$weight.'" onclick="checkZero(this.value,\'weight-'.$tid.'\')"/>'.
										'<input type="hidden" id="tno" value='.$tno.'>'.
										'<input type="hidden" id="oid-'.$tid.'" value='.$oid.'>'.
										'<input type="hidden" id="ptid-'.$tid.'" value='.$ptid.'>'.
										'<input type="hidden" id="cid-'.$tid.'" value='.$cid.'>'.
										'</td><td></td>';
									}
									
									echo '</tr>';
									$puncCount++;
									$disable++;
							}
							$stmt->close();
                		}
                ?>
		</table><br><br>

		<!-- move to table /////////////////////////////////////////////////////////////////////////////////////////////// -->
		<!-- Tracking No -->
		<div style="text-align:center">
			<label>Tracking No. : </label><input style="text-align:right" disabled class="weight-customer-code" type="text" value=<?php echo $_GET['tracking'] ?> >
		</div>

		<div> 
			<table style="text-align:center;">
				<tr>
					<td>Tracking Detail </td>
				</tr>
			</table>
		</div>
       	 <!-- <table class="order-product" -->
                <?php 
            			if ($stmt = $con->prepare($sql2)) {
            				$puncCount = 0;
							$onoTmp = '';
							$temp = '';
							$disrow = 0;
							$totalQuan = 0;
							$totalReceived = 0;
							$totalAdd = 0;
							$totalMissing = 0;

                			$stmt->execute();
                			$stmt->bind_result($tid,$tno,$opid,$oid,$amount,$quantity,$ccode,$pname,$pimg,$purl,$sname,$ono,$customerID,$userID, $lastEditDate,$pColor,$pSize);
							while ($stmt->fetch()) {
									if ($onoTmp!=$ono||$onoTmp=='') {
										echo '<table class="order-product received">';
										echo '<thead>';
										echo '<br><th colspan="9"style="text-align:left;background-color:white;color:black"><div>'.
											'Customer Code : '.$ccode.
											'</div><br>'.
											'<div style="margin-top:-15px">'.
											'เลขที่ order : '.$ono.
											'</div></th>';
										
						                echo '<tr><th>Tracking no.</th>'.
						                	'<th>รูปตัวอย่าง</th>'.
						                	'<th>สี/ไซด์</th>'.
						                	'<th>จำนวนที่สั่ง</th>'.
											'<th>จำนวนที่รับแล้ว</th>'.
											'<th>รับเพิ่ม</th>'.
											'<th>ขาดอีก</th>'.
											'<th>Last update by</th>'.
											'<th>Last edit date</th></tr>'.
						                	'</thead>';

										echo '<thead class="shopname undivide">'.
										'<th style="background-color:#33CC99;" colspan="19"> '.$ono.'</th></thead>';
										$onoTmp = $ono;
										$disrow++;	
									}
									
									//$sum_received = getSumReceived($opid,$oid);
									if($lastEditDate=='' || $lastEditDate=='0000-00-00 00:00:00') $lastEditDate = '';
									else $lastEditDate = date_format(date_create($lastEditDate),"d/m/Y H:i:s");
									echo '<tr class="'.($puncCount%2==0? 'punc ':'').'">';
									echo '<td align="center">'.$tno.'</td>';
									echo '<td align="center"><a href="'.$purl.'" target="_blank"><img height="150" width="150" src="'.$pimg.'"/></a></td>';
									echo '<td align="center">'.$pColor.'  '.$pSize.'</td>';
									echo '<td align="center" id="quan-'.$tid.'">'.$quantity.'</td>';
									echo '<td align="center" id="rec-'.$tid.'"><a onclick="showAmountDialog('.$tid.')">'.number_format($amount).'</a></td>';
					
									if($disrow == 0 || $disStatId==0) {
										echo '<td>'.
										'<input style="text-align:right;" class="quan" id="add-'.$tid.'" value="0" tid="'.$tid.'" onclick="checkZero(this.value,\'add-'.$tid.'\')"/>'.
										'</td>';
									}
									else {
										echo '<td>'.
										'<input disabled id="disme" style="text-align:right;" class="quan" id="add-'.$tid.'" value="0"/>'.
										'</td>';
									}
									echo '<td align="center" id="missing-'.$tid.'">'.($quantity-$amount).'</td>';
									echo '<td id="userID-"'.$tid.'" align="center">'.$userID.'</td>';
									echo '<td id="lastEditDate-"'.$tid.'" align="center">'.$lastEditDate.'</td>';
									echo '</tr>';
									//echo '<input type="hidden" id="sumRec-'.$tid.'" value='.$sum_received.'>';
									$puncCount++;

									//total
									$totalQuan += $quantity;
									$totalReceived += $amount;
									$totalMissing += ($quantity-$amount); 									
							}
							$stmt->close();

							//total
							echo '<tfoot align="center" style="font-weight:bold;">';
							echo '<td colspan="2">ยอดรวม</td>';
							echo '<td></td><td>'.$totalQuan.'</td>';
							echo '<td>'.$totalReceived.'</td>';
							echo '<td align="right">'.$totalAdd.'</td>';
							echo '<td>'.$totalMissing.'</td><td></td><td></td>';
							echo '</tfoot>';
                		}  
                ?>
		</table>
		
		<br>
		<div align="center" style="width:600px;left:0;right:0;margin-left:auto;margin-right:auto;">
			<button class="order-button-blue" onclick="print()">พิมพ์ barcode</button>
			<?php 
					if ($saveFlg==0) {
							echo '<button class="order-button" onclick="save()">บันทึก</button>';
					} 
			?>
			<button class="order-cancel" onclick="cancel()">กลับ</button>
		</div>
		<br>
		<br>
		<br>

		<?php
			include './dialog/amountDialog.php';
			include './dialog/searchDialog.php';
			include './dialog/loading.php';
			$con->close();
		?>
		<script src="./controller.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js"></script>
		<script type="text/javascript">
			$('.search-select').chosen();

			$(document).ready(function() {
	    		$(".num").keydown(function (e) {
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
			                cal($(this).attr('tid'));
			                return;
			        	}
			        // Ensure that it is a number and stop the keypress
			        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			            e.preventDefault();
			        }
			        cal($(this).attr('tid'));
		    	});
		    	$(".weight").keydown(function (e) {
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
		    	$(".quan").keydown(function (e) {
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
			        if (e.keyCode==109 || e.keyCode==189) {
			        	return;
			    	}
			        // Ensure that it is a number and stop the keypress
			        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			            e.preventDefault();
			        }
		    	});
		    	$(".quan").keyup(function (e) {
		    		calTotal($(this).attr('tid'));
		    	});
			});
		</script>
	</body>
</html>

