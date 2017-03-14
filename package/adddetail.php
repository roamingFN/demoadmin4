<?php 
	session_start();
    if (!isset($_SESSION['ID'])){
        header("Location: ../login.php");
    }
	include '../database.php';
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Package Detail</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<style>
			i{color:#6633FF;}
			button,.button{color:#6633FF;}
			a{color:#6633FF;}
			th{background:#6633FF;}
			.undivide th{background:#6633FF;}
			.order-button:hover{color:#6633FF;}
		</style>
		<script src="../js/jquery-1.10.2.js"></script>
		<script src="../js/jquery-ui.js"></script>
		<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
		<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
		
		<script src="js/ajaxlib.js"></script>
		<script src="js/util.js"></script>
		
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
		
		<script>
			function show() {
				document.getElementById("addBox").style.visibility = 'visible';
			}
			function hide() {
				document.getElementById("addBox").style.visibility = 'hidden';
			}
			function search() {
				var httpreq = new AJAX();
				var xmlreq = "";
				var order_no = document.getElementById("search_order_no").value;
				var tracking_no = document.getElementById("search_tracking_no").value;
				var url = 'search_tracking.php?order_no='+order_no+'&tracking_no='+tracking_no;
						
					httpreq.sendXMLRequest('GET', url, xmlreq, 
						function(result) {
							console.log(result);
							var items = JSON.parse(result);
							document.getElementById("search_result").innerHTML = drawItems(items);
						}, 
						function(error) {
							console.log(error);
						}
					);
			}
			function adddetail() {
				
			}
			function back() {
				document.location = 'index.php';
			}
			
			drawItems = function(items) {
				var html = '';
				var i = 0;
				html+='<table class="detail">';
				html+='<tr>';
				html+='<th>Order No.</th>';
				html+='<th>M3</th>';
				html+='<th>Wg.</th>';
				html+='<th>Rate</th>';
				html+='<th>ราคา</th>';
				html+='</tr>';
				
				for(i=0; i<items.length; i++) {
					html+='<tr class="punc normal">';
					html+='<td>'+items[i].order_number+'</td>';  //Order no
					html+='<td>'+items[i].m3+'</td>';  //M3
					html+='<td>'+items[i].weight+'</td>'; //Wg
					html+='<td>'+items[i].rate+'</td>'; //Rate
					html+='<td>'+items[i].total+'</td>'; //ราคา
					html+='</tr>';
					
				}
				
				html+='</table><br/>';
		
				return(html);
			};
		</script>
		
<script>
var packagedetail = {
	"packageid":"1","packagenumber":"P16000006",
	"customer":{"id":"35","name":"Bundit Suksathan"},
	"createdate":"25/03/2016",
	"total_tracking":"3",
	"shipping":{"id":"1","name":"นิ่มขนส่ง"},
	"shippingno":"NEM0003",
	"amount":1000,
	"statusid":"0",
	"adduser":"boon",
	"sentemail":"0",
	"items":[
		{"order":"R16040600001","m3":"100","wg":"1000","rate":"50","amount":"5000"},
		{"order":"R16040600002","m3":"100","wg":"1000","rate":"50","amount":"5000"}
	]
};
</script>		
		
	</head>
	
	<body>
		<h1><a href="detail.php?package_id=292">รายละเอียดกล่อง</a></h1>
                <h3><a href="index.php">&larr; Back</a>&nbsp;<a href="../index.php">&larr; Home</a></h3>
				<br>
		<table style="width:100%;padding:10px;">
			<tr>
				<td width="15%" style="padding:2px">เลขที่ กล่อง  : </td>
				<td width="20%" style="padding:2px"><label><b></b></label></td>
				<td style="padding:2px">วันที่สร้าง : </td>
				<td style="padding:2px"><input id="th-date" class="china datepicker" style="padding:2px;" value="25/03/2016"/></td>
			</tr>
			<tr>
				<td width="15%" style="padding:2px">ลูกค้า  : </td>
				<td width="20%" style="padding:2px">
					<!-- input name="cid" list="lst" -->
					<select id="customer_id">
						<?php
						
						if($stmt = $con->prepare('SELECT customer_id, customer_firstname, customer_lastname FROM customer ORDER BY customer_firstname, customer_lastname')){
							$stmt->execute();
							$stmt->bind_result($customer_id,$customer_firstname,$customer_lastname);
							while($stmt->fetch()){
								echo '<option value="'.$customer_id.'">'.$customer_firstname.' '.$customer_lastname.'</option>';
							}
							$stmt->close();
						}
						?>
					</select>
									
					<!-- input type="text" style="padding:2px;width:200px;"/ -->
				</td>
				<td style="padding:2px"></td>
				<td style="padding:2px"></td>
			</tr>
			<tr>
				<td width="15%" style="padding:2px">บริษัทขนส่ง  :</td>
				<td width="20%" style="padding:2px">
					<select id="shipping_id">
					<?php
						if($stmt = $con->prepare('SELECT transport_id, transport_th_name, transport_eng_name FROM website_transport ORDER BY transport_id')){
							$stmt->execute();
							$stmt->bind_result($transport_id,$transport_th_name,$transport_eng_name);
							while($stmt->fetch()){
								echo '<option value="'.$transport_id.'">'.$transport_th_name.'</option>';
							}
							$stmt->close();
						}
					?>						
					</select>
					<!-- input type="text" style="padding:2px;width:200px;"/ -->
				</td>
				<td width="15%" style="padding:2px">Tracking ของบริษัทขนส่ง : </td>
				<td style="padding:2px"><input id="tracking_no" type="text" style="padding:2px;width:200px;"/></td>
			</tr>
			
		</table>
		
		<br><br>
		
		<div class="package" style="width:100%;padding:0px;display:table;">
			<div style="display:table-cell;width:90%;vertical-align:top;">
					<table class="order-product" style="width:100%;padding:0px;" align="left">
						<thead>
							<th></th><th width="40">ลำดับที่</th><th>Order No.</th><th>M3</th><th>Wg.</th><th>Rate</th><th>ราคา</th>				
						</thead>
						<!-- tr class="">
							<td><input type="checkbox"/></td>
							<td align="right">1</td>
							<td>R16040600001</td>
							<td align="right">100</td>
							<td align="right">1,000</td>
							<td align="right">50</td>
							<td align="right">5,000.00</td>
						</tr>
						<tr class="">
							<td><input type="checkbox"/></td>
							<td align="right">2</td>
							<td>R16040600001</td>
							<td align="right">100</td>
							<td align="right">1,000</td>
							<td align="right">50</td>
							<td align="right">5,000.00</td>
						</tr -->
					</table>
			</div>
			<div style="display:table-cell;width:10%;vertical-align:top;">
					<div style="width:auto;left:0;right:0;margin-left:auto;margin-right:auto;text-align:center;margin:5px;">
						<button class="order-button" style="width:80px;height:30px;font:11pt tahoma;" onclick="show()">เพิ่ม</button>
					</div>
					<div style="width:auto;left:0;right:0;margin-left:auto;margin-right:auto;text-align:center;margin:5px;">
						<button class="order-cancel" style="width:80px;height:30px;font:11pt tahoma;" onclick="delete()">ลบ</button>
					</div>
			</div>
		</div>
		
		<div style="width:80%;padding:5px;left:0;right:0;margin-left:auto;margin-right:auto;text-align:right;">
			<label style="font:11pt tahoma;"><b>ราคารวม : 0.00</b></label>
		</div>
		<br><br>
		<div style="width:1024px;left:0;right:0;margin-left:auto;margin-right:auto;text-align:center;">
			<button class="order-button" onclick="print()">พิมพ์ที่อยู่คนส่ง , ลูกค้า</button>
			<button class="order-button" onclick="save()">บันทึก</button>
			<button class="order-button" onclick="back()">กลับ</button>
			<button class="order-button" onclick="confirm()">ยืนยัน</button>
		</div>
		
		<div id="addBox" style="position:fixed;top:0px;width:100%;height:100%;visibility:hidden;background:rgba(0,0,0,0.5)">
			<div style="position:relative;top:100px;width:800px;background:white;margin:0 auto;">
			<table style="width:800px;" align="center">
				<tr>
					<td>
						<table>
							<tr><td>Order No:</td><td><input id="search_order_no"/></td><td><button class="order-button" onclick="search();">Search</button></td></tr>
							<tr><td>Tracking No. :</td><td><input id="search_tracking_no"/></td><td></td></tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<div id="search_result">
							<table class="detail">
								<thead>
									<th>Order no.</th><th>M3</th><th>Wg.</th><th>Rate</th><th>ราคา</th>				
								</thead>
							</table>						
						</div>
					</td>
				</tr>
			</table>
			<br><br>
						<div style="width:800px;left:0;right:0;margin-left:auto;margin-right:auto;text-align:center;">
							<button class="order-button" onclick="adddetail()">ตกลง</button>
							<button class="order-button" onclick="hide();">ออก</button>
						</div>
			
			</div>
		</div>
	</body>
</html>
