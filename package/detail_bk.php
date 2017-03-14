<?php 
	/* session_start();
    if (!isset($_SESSION['ID'])){
        header("Location: ../login.php");
    }
	include '../database.php'; */
	
	$package_id = $_GET['id'];
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
i{color:#0070c0;}
button,.button{color:#0070c0;}
a{color:#0070c0;}
th{background:#0070c0;}
ul {list-style-type:none;margin:0;padding:0;width:100%}
.undivide th{background:#0070c0;}
.order-button:hover{color:#0070c0;}

li.customer_list {height:40px;width:684px;padding:5px;background:white;border-bottom:1px solid #cccccc;cursor:pointer;}
li.customer_list img {float:left;width:40px;height:40px;}
li.customer_list div {width:460px;padding-left:10px;display:table-cell;vertical-align:middle;}
li.customer_list div label {font:bold 11pt arial;}
li.customer_list:hover {background: #c2e1f5;color:black;}

.message_box {padding-top:50px;position:fixed;width:100%;height:0px;background: url('images/overlay.png');left:0px;top:0px;visibility:hidden;z-index:99999;
} 
.filter {padding-top:150px;position:fixed;width:100%;height:100%;background: url('images/overlay.png');left:0px;top:0px;visibility:hidden;z-index:9999;
</style>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
<script src="js/ajaxlib.js"></script>
<script src="js/util.js"></script>
<script src="js/packagelib.js"></script>
<script src="js/detail_ui_events.js"></script>

<script>

$(function() {
	$(".datepicker").datepicker({
        dateFormat: "dd/mm/yy"
	});
	
	/* $( ".timepicker" ).timepicker({
		timeFormat: "HH:mm:ss"
	});*/
});
// Global variable
var offset = 20;
var page = 1;
var pack = new Package();
var package_id = '<?php echo $package_id ?>';
var packagedetail;

</script>		
		
</head>
	
<body onload="on_page_ready();">

<h1><a href="detail.php?package_id=<?php echo $package_id?>">รายละเอียดกล่อง</a></h1>
<h3><a href="index.php">&larr; Back</a>&nbsp;<a href="../index.php">&larr; Home</a></h3>
<br/>
		<div id="package_header">
		<table style="width:1024px;padding:10px;" align="center">
			<tr>
				<td width="20%" style="padding:2px">เลขที่ กล่อง  : </td>
				<td width="20%" style="padding:2px"><label id="package_number"><b></b></label></td>
				<td style="padding:2px">วันที่สร้าง : </td>
				<td style="padding:2px"><input id="datepicker" class="china datepicker" style="padding:2px;" value=""/></td>
			</tr>
			<tr>
				<td width="20%" style="padding:2px">ลูกค้า  : </td>
				<td width="20%" style="padding:2px">
					<input id="customer_name" onfocus="on_list_customer(this.value);" onkeyup="on_list_customer(this.value);">
					<div id="lstcustomer" style="position:absolute;width:280px;height:auto;visibility: hidden;background:white;border:1px solid gray;">
						
					</div>
									
					<!-- input type="text" style="padding:2px;width:200px;"/ -->
				</td>
				<td style="padding:2px"></td>
				<td style="padding:2px"></td>
			</tr>
			<tr>
				<td width="20%" style="padding:2px">บริษัทขนส่ง  :</td>
				<td width="20%" style="padding:2px">
					<select id="shipping_id">

					</select>
					<!-- input type="text" style="padding:2px;width:200px;"/ -->
				</td>
				<td width="20%" style="padding:2px">Tracking ของบริษัทขนส่ง : </td>
				<td style="padding:2px"><input id="shippingno" type="text" style="padding:2px;width:200px;"/></td>
			</tr>
			
		</table>
		</div>
		<br><br>
		
		<div class="package" style="width:1024px;padding:0px;display:table;text-align:center;margin:0 auto;">
			<div id="package_items" style="display:table-cell;width:90%;vertical-align:top;">
					
					<table class="order-product" style="width:100%;padding:0px;" align="center">
						<thead>
							<th></th><th width="40">ลำดับ</th><th>เลขที่<br/>Order</th><th>เลขที่<br/>Trackin(จีน)</th><th>M3</th><th>Kg.</th><th>Rate</th><th>ยอดเงิน</th>
						</thead>
						
					</table>
			</div>
			<div style="display:table-cell;width:10%;vertical-align:top;">
					<div style="width:auto;left:0;right:0;margin-left:auto;margin-right:auto;text-align:center;margin:5px;">
						<button class="order-button" style="width:80px;height:30px;font:11pt tahoma;" onclick="on_add_detail_button_click()">เพิ่ม</button>
					</div>
					<div style="width:auto;left:0;right:0;margin-left:auto;margin-right:auto;text-align:center;margin:5px;">
						<button class="order-cancel" style="width:80px;height:30px;font:11pt tahoma;" onclick="on_remove_detail_button_click()">ลบ</button>
					</div>
			</div>
		</div>
		
		<div style="width:760px;padding:5px;left:0;right:0;margin:0 auto;text-align:right;">
			<label id="package_total_amount" style="font:11pt tahoma;"><b>ราคารวม : 0.00</b></label>
		</div>
		<br><br>
		
		<div class="package" style="width:100%;padding:0px;display:table;">
			<div style="display:table-cell;width:90%;vertical-align:top;">
				<table style="width:1024px;padding:5px;background:#0070c0;color:white;" align="center">
					<tr class="">
						<td colspan="2">ค่าจัดส่งสินค้า จากโกดังไทย-บริษัทขนส่ง</td>
						<td align="right"><input id="amount_cargotothirdparty" type="text" style="text-align: right" placeholder="" value="0.00"/></td>
						<td>บาท</td>
					</tr>
					<tr class="">
						<td colspan="2">ค่าตีลังไม้(ที่จีน)</td>
						<td align="right"><input id="amount_boxchina" type="text" style="text-align: right" placeholder="" value="0.00"/></td>
						<td>บาท</td>
					</tr>
					<tr class="">
						<td colspan="2">ค่าตีลังไม้ที่(ไทย)</td>
						<td align="right"><input id="amount_boxthai" type="text" style="text-align: right" placeholder="" value="0.00"/></td>
						<td>บาท</td>
					</tr>
					<tr class="">
						<td colspan="2">ค่ากล่อง</td>
						<td align="right"><input id="amount_boxpackage" type="text" style="text-align: right" placeholder="" value="0.00"/></td>
						<td>บาท</td>
					</tr>
					<tr class="">
						<td colspan="2">ค่าขนส่ง (ต้นทางของบริษัทขนส่ง)</td>
						<td align="right"><input id="amount_thirdparty" type="text" style="text-align: right" placeholder="" value="0.00"/></td>
						<td>บาท</td>
					</tr>					
					<tr class="">
						<td>ค่าอื่นๆ</td>
						<td align="right"><input id="amount_other" type="text" style="text-align: right" placeholder="Other" value="0.00"/></td>
						<td align="right"><input id="other_specifiy" type="text" style="text-align: right" placeholder="Other Specifiy" value="0.00"/></td>
						<td>บาท</td>
					</tr>
					<tr class="">
						<td>ค่าอื่นๆ</td>
						<td align="right"><input id="amount_other2" type="text" style="text-align: right" placeholder="Other2" value="0.00"/></td>
						<td align="right"><input id="other_specifiy2" type="text" style="text-align: right" placeholder="Other Specifiy2" value="0.00"/></td>
						<td>บาท</td>
					</tr>
				</table>
			</div>
		</div>
		<br>
		
		<div id="package_total_amount" style="width:1024px;padding:5px;left:0;right:0;margin:0 auto;text-align:right;">
			<textarea id="remark" style="width:1024px;height:80px;padding:5px;" placeholder="Remark"></textarea>
		</div>
		<br>
		
		<div style="width:1024px;left:0;right:0;margin-left:auto;margin-right:auto;text-align:center;">
			<button class="order-button" onclick="print()">พิมพ์ที่อยู่คนส่ง , ลูกค้า</button>
			<button class="order-button" onclick="save()">บันทึก</button>
			<button class="order-button" onclick="back()">กลับ</button>
			<button class="order-button" onclick="confirm()">ยืนยัน</button>
		</div>
		
<div id="order_product_tracking_box" class="filter">
	<div id="order_product_tracking_box_main" style="position:relative;width:644px;height:480px;border:1px solid black;background:#ffffff;padding:0px;margin:0 auto;">
		<div style="float:right;width:16px;height:16px;padding:5px;cursor:pointer;">
            <div style="padding:2px;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='';">
                <img src="images/icon_close.png" alt="" width="12" onclick="hide_order_product_tracking();"/>
            </div>
        </div>
		<div style="position:relative;width:644px;height:480px;background-color:white;border-left:1px solid #01A2E8;border-right:1px solid #01A2E8;border-bottom:1px solid #01A2E8;margin:0 auto">
			<div style="position:fixed;height:480px;width:644px;">
				<div style="position:relative;width:644px;height:480px;padding:20px;">
					<div style="padding:0px;display:table;text-align:center;margin:0 auto;">
						<div style="display:table-cell;vertical-align:top;">
							<ul>
								<li style="height:auto;padding:5px;background:white;border-bottom:0px solid #cccccc;">
									<div style="width:160px;display:table-cell;vertical-align:middle;text-align:left;"><label style="font:bold 11pt arial;">Order No : </label></div>
									<div style="width:304px;display:table-cell;vertical-align:middle;">
										<input id="search_order_no" style="width:304px;outline:none;"/>
									</div>
								</li>
								<li style="height:auto;padding:5px;background:white;border-bottom:0px solid #cccccc;">
									<div style="width:160px;display:table-cell;vertical-align:middle;text-align:left;"><label style="font:bold 11pt arial;">Tracking No. : </label></div>
									<div style="width:304px;display:table-cell;vertical-align:middle;">
										<input id="search_tracking_no" style="width:304px;outline:none;"/>
									</div>
								</li>
							</ul>
						</div>
						<div style="display:table-cell;vertical-align:middle;">
							<div style="width:auto;left:0;right:0;margin-left:auto;margin-right:auto;text-align:left;margin:5px;">
								<button class="order-button" style="width:80px;height:30px;font:11pt tahoma;" onclick="on_search_tracking();">Search</button>
							</div>
						</div>
					</div>
					<div id="order_product_tracking_list">
					
					</div>
					<div style="position:absolute;width:644px;bottom:50px;margin-left:auto;margin-right:auto;text-align:center;">
						<button class="order-button" onclick="">OK</button>
						<button class="order-cancel" onclick="hide_order_product_tracking();">Cancel</button>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
		
<div id="message_box" class="message_box">
    <div id="message_box_main" style="position:absolute;width:auto;min-width:380px;height:auto;border:1px solid black;background:#ffffff;padding:0px">
        <div style="float:right;width:16px;height:16px;padding:5px;cursor:pointer;">
            <div style="padding:2px;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='';">
                <img src="images/icon_close.png" alt="" width="12" onclick="hide_message_box('message_box');"/>
            </div>
        </div>
        <div style="padding:0px;height:auto">
            <div id="message_box_title" style="font:bold 11pt tahoma;padding:20px;"></div>
        </div>
        <div style="padding:0px;height:auto">
            <div id="message_box_text" style="font:10pt tahoma;width:auto;padding-top:0px;padding-left:20px;padding-right:20px;padding-bottom:5px;"></div>
        </div>
        <div style="padding:15px;height:30px">
            <button id="btnok_message_box" onclick="hide_message_box('message_box');"  style="cursor:pointer">OK</button>
            <!-- input type="button" value="Cancel"/ -->
        </div>
    </div>
</div>

<div id="confirm_delete_item_box" class="message_box">
    <div id="confirm_delete_item_box_main" style="position:absolute;width:380px;height:auto;border:1px solid black;background:#ffffff;padding:0px">
        <div style="float:right;width:16px;height:16px;padding:5px;cursor:pointer;">
            <div style="padding:2px;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='';">
                <img src="images/icon_close.png" alt="" width="12" onclick="hide_confirm_delete_item('confirm_delete_item_box');"/>
            </div>
        </div>
        <div style="padding:0px;height:auto">
            <div id="confirm_delete_item_title" style="font:bold 11pt tahoma;padding:20px;">ลบรายการ</div>
        </div>
        <div style="padding:0px;height:auto">
            <div id="confirm_delete_item_text" style="font:10pt tahoma;width:340px;padding-top:0px;padding-left:20px;padding-bottom:5px;">คุณต้องการลบรายการที่เลือกใช่หรือไม่</div>
        </div>
        <div style="padding:15px;height:30px">
            <button id="confirm_delete_item_ok" onclick="" style="cursor:pointer">OK</button>
            <button onclick="hide_confirm_delete_item('confirm_delete_item_box');"  style="cursor:pointer">Cancel</button>
        </div>
    </div>
</div>

	</body>
</html>
