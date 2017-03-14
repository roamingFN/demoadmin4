
<!DOCTYPE html>
<html>
<head>
<title>Package Detail</title>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="../css/cargo.css">
<link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	rel="stylesheet">
<link
	href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300'
	rel='stylesheet' type='text/css'>
<style>

table {
    border-collapse: collapse;
}

table td input{
	width:25% !important;
}

table td label{
	width:25% !important;
}

i {
	color: #0070c0;
}

button,.button {
	color: #0070c0;
}

a {
	color: #0070c0;
}

th {
	background: #0070c0;
}

ul {
	list-style-type: none;
	margin: 0;
	padding: 0;
	width: 100%
}

.undivide th {
	background: #0070c0;
}

.order-button:hover {
	color: #0070c0;
}

.back-button{
	 background-color: #f00 !important;
}

li.customer_list {
	height: 40px;
	width: 684px;
	padding: 5px;
	background: white;
	border-bottom: 1px solid #cccccc;
	cursor: pointer;
}

li.customer_list img {
	float: left;
	width: 40px;
	height: 40px;
}

li.customer_list div {
	width: 460px;
	padding-left: 10px;
	display: table-cell;
	vertical-align: middle;
}

li.customer_list div label {
	font: bold 11pt arial;
}

li.customer_list:hover {
	background: #c2e1f5;
	color: black;
}

.message_box {
	padding-top: 50px;
	position: fixed;
	width: 100%;
	height: 0px;
	background: url('images/overlay.png');
	left: 0px;
	top: 0px;
	visibility: hidden;
	z-index: 99999;
}

.filter {
	padding-top: 150px;
	position: fixed;
	width: 100%;
	height: 100%;
	background: url('images/overlay.png');
	left: 0px;
	top: 0px;
	visibility: hidden;
	z-index: 9999;
}

.undivide th,.detail-order-complete th{
	background: #0070c0;
}
.detail-order-complete th{
	border-right: 1px solid #00796b;
    color: #fff;
    padding: 4px;
    text-align: center;
    width: 127px !important;
}

.order-button:hover {
	color: #0070c0;
}


.wrap th {
	width: 32%;
}

#orderComplete table{
	width:60%;
}



.detail-order-complete{
 	box-shadow: none !important;
    display: block !important;
    max-height: 400px !important;
    position: relative !important;
    width: 98% !important;
    overflow-y: auto;
}

        #search input {
	background: #e4f1fb none repeat scroll 0 0;
	border: 0 none;
	color: #7F7F7F;
	float: left;
	font: 12px 'Helvetica','Lucida Sans Unicode','Lucida Grande',sans-serif;
	height: 20px;
	margin: 0;
	padding: 10px;
	transition: background 0.3s ease-in-out 0s;
	width: 300px;
}

#search button {
	background: url("images/search.png") no-repeat scroll center center #0070c0 ;
	cursor: pointer;
	height: 40px;
	text-indent: -99999em;
	transition: background 0.3s ease-in-out 0s;
	width: 40px;
	border: 2px solid #fff;
}

#search button:hover {
	background-color:#021828;
}

.searchBox{
    margin-right: 24px;
    float: right;
}

table.detail-order-complete  tr:hover{
	background: #b2dfdb none repeat scroll 0 0 !important;
}
</style>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
<script src="js/ajaxlib.js"></script>
<script src="js/util.js"></script>
<script src="js/packagelib.js"></script>
<script src="js/detail.js"></script>
<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';

if (isset ( $_GET ['id'] )) {
	$package_id = $_GET ['id'];
} else {
	$package_id = '';
}

?>
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


$(window).load(function(){


	$( "#datepickerCreate" ).datepicker({
	    dateFormat: "dd-mm-yy"
	});

	$( "#datepickerCreate" ).datepicker(
		    'setDate', new Date()
	);
	
	$.getJSON("./package-do.php",{ getPackageStatus : '<?php echo base64_encode(date('Y-m-d h:i')); ?>' }, function (data) {
		//console.log(data.packagestatusname);
		$('#packagestatusid > b').text(data.packagestatusname);
	});

	

});
var orderCompleteOn=false;
function orderComplete(){
	//alert('');
	//document.getElementById('addBox').style.visibility = 'hidden';
	//document.getElementById('editBox').style.visibility = 'hidden';
	orderCompleteOn = !orderCompleteOn;
	//call data with ajax
	searchOrderCompleteJSON();
	//end call
	if(orderCompleteOn){

		var CheckBoxes=document.getElementsByClassName('chkorderComplete');
		for (var i = 0; i < CheckBoxes.length; i++) {
		    CheckBoxes[i].checked = false;        
		}
		
		document.getElementById('orderComplete').style.visibility = 'visible';	
		
	}else{
		document.getElementById('orderComplete').style.visibility = 'hidden';
	}	
}

function cancelBtn(){
	document.getElementById('orderComplete').style.visibility = 'hidden';
	//document.getElementById('addBox').style.visibility = 'hidden';
	//document.getElementById('editBox').style.visibility = 'hidden';
	location.reload();
	
}

function on_remove_detail_button_click(){

	$items=[];
	$('input[name="chkorder"]:checked').each(function() {
		   //console.log(this.value); 
		   $items.push(this.value);
	});

	 $.getJSON("./package-do.php",{ removeItemOrder : $items},function(data){
		if(data.success){
			location.reload();
		}
	});
}

function save(customerId){
	// 1 . check address customer 
	$.getJSON("./package-do.php",{ address : customerId},function(data){
		if(data.length>1){
			// show pupup for choose address add save address to text box address
			//$('#addresBox').show();
			addressBox(data);
			//alert('');
			//showAddress();
		}
	});
}


var addressBoxOn=false;
function addressBox(data){

	addressBoxOn = !addressBoxOn;
	//call data with ajax
	//searchOrderCompleteJSON();
	//end call
	if(addressBoxOn){

		
		document.getElementById('addressBox').style.visibility = 'visible';	

		/*<tr>
		<td><input type="checkbox" value="340" name="chkAddress" class="chkAddress"></td>
		<td></td>
	</tr>
	<tr></tr>
	<tr class="confirm">
			<td></td>
			<td><a onclick="cancelBtn();">Cancel</a>&emsp;<input type="hidden" value="test00" name="uid"><input type="hidden" value="1" name="addOrder"><button onclick="btnOrderAdd();">Add</button></td>
	</tr>*/
	var html='';
	$.each(data,function(k,v){
		html+='<tr>';
		html+='<td><input type="radio" value="'+v.address_id+'" name="chkAddress" class="chkAddress"></td>';
		html+='<td>'+v.line_1+" "+v.city+" "+v.country+" "+v.zipcode+" Tel."+v.phone+'</td>';
	    html+='</tr>';
	    //alert(html);
		
	});

	html+='<tr class="confirm">';
	html+='<td></td>';
	
	html+='<td><div style="width: 100%; left: 0; right: 0; margin-left: auto; margin-right: auto; margin-bottom: 18px; text-align: center;">';
	html+='<button onclick="confirmAddress()" class="order-button">ตกลง</button>';
	html+='<button onclick="back()" class="order-button back-button">กลับ</button></td>';

	$('#addressBox > table > tbody').empty();
	$('#addressBox > table > tbody').append(html);
	
	}else{
		document.getElementById('addressBox').style.visibility = 'hidden';
	}	
}

function confirmAddress(){
	//$('#address').attr('id')=
	//alert($('input[name="chkAddress"]:checked'));

	$('input[name="chkAddress"]:checked').each(function() {
// 		   /console.log(this.value); 
		   $('#address').attr('addressId',this.value);
		   $obj=$(this).parent().parent().find('td:nth-child(2)');
		   //console.log($obj.text());
		   $('#address').attr('value',$obj.text());
		   document.getElementById('addressBox').style.visibility = 'hidden';
		   
	});

	
}




function searchOrderCompleteJSON(param){

	var html='';
	$.getJSON("./package-do.php",{ addOrderComplete : '<?php echo base64_encode(date('Y-m-d h:i')); ?>', params:'<?php echo (isset($_SESSION['customerId']))? $_SESSION['customerId']:''; ?>' }, function (data) {
		   //console.log(data.name);
		   var i=0;
		   $.each(data,function(k,v){
				//if(v.order_status_code==='7'){
					html+='<tr style="'+((v.statusid=='0')?'color:#f00':'')+'" class='+((i % 2 == 0) ? "punc" : "") + '>';	
					html+='<td id="'+v.order_number+'">'+v.order_number+'</td>';
					html+='<td id="'+v.order_number+'">'+v.tracking_no_copt+'</td>';
					html+='<td id="'+v.order_number+'">'+'<?php echo date('d/m/Y');?>'+'</td>';
					html+='<td id="'+v.order_number+'">'+v.m3+'</td>';
					html+='<td id="'+v.order_number+'">'+v.weight+'</td>';
					html+='<td id="'+v.order_number+'">'+v.rate+'</td>';
					html+='<td id="'+v.order_number+'">'+((v.type==1)?'kg':'คิว')+'</td>';
					html+='<td id="'+v.order_number+'">'+v.total+'</td>';
					html+='<td id="'+v.order_number+'">'+((v.type==1)?((parseFloat(v.total)/parseFloat(v.weight))).toFixed(2):(parseFloat(v.total)/parseFloat(v.m3)).toFixed(2))+'</td>';
					html+='<td><input type="checkbox" class="chkorderComplete" name="chkorder" value="'+v.order_number+'"  ></td>';
					html+='</tr>';
				//}
				i++;
			});
		   	$('.detail-order-complete > tbody').empty();
			$('.detail-order-complete > tbody').append(html);
			console.log(html);
	});
}

function btnOrderAdd(){
	$orderCheckList=$('#orderValidate').serialize();
	$paramAmp=$orderCheckList.split('&');
	$paramArray=[];
	if($paramAmp.length>0){
		for(i=0;i<$paramAmp.length;++i){
			$paramArray[i]=$paramAmp[i].split('=')[1];
		}
	}
	
	if($orderCheckList.length !=0){
		$.getJSON("./package-do.php",{ addOrder : '<?php echo base64_encode(date('Y-m-d h:i')); ?>', params:$paramArray }, function (data) {
			//console.log(data);
			var strError='ไม่สามารถเลือกลงกล่องได้ เนื่องจากเลือกลงกล่องไปแล้ว\n';
			var flagError=false;
			var strSuccess='เลือกลงกล่องสำเร้จ\n';
			var flagSuccess=false;
			var flagErrorCus=false;
			
			$.each(data,function(k,v){
				if('error'===k.trim()){
					var index=0;
					$.each(v,function(k,v1){
						console.log('error['+(index)+']'+(v1));						
						strError+='เลขที่ Order: '+v1+'\n';
						flagError=true;
						index++;
					});
					
				}

				if('success'===k.trim()){
					var index=0;
					$.each(v,function(k,v1){
						console.log('strSuccess['+(index)+']'+(v1));						
						strSuccess+='เลขที่ Order: '+v1+'\n';
						flagSuccess=true;
						index++;
					});
				}

				if('errorCus'===k.trim()){
					flagErrorCus=true;
				}

				
			});

			

			if(flagError){
				alert(strError);
			}

			if(flagSuccess){
				alert(strSuccess);
			}

			if(flagErrorCus){
				alert('ควรเลือกลูกค้าชื่อเดียวกัน');
			}

				location.reload(true);
			
			
		});
	}else{
		alert('กรุณาเลือกรายการ');
	}
	
	
}

function addHTMLToPackageItems(i,val){
	var html='<tr style="'+((val.statusid=='0')?'color:#f00':'')+'" class="'+((i % 2==0)? 'punc' : '')+'">';
	//console.log(i);
	html+='<td>'+(i)+'</td>';
	html+='<td>'+val.order_number+'</td>';
	html+='<td>'+val.tracking_no+'</td>';
	html+='<td>'+'<?php echo date('d/m/Y');?>'+'</td>';
	html+='<td>'+val.m3+'</td>';
	html+='<td>'+val.weight+'</td>';
	html+='<td>'+val.rate+'</td>';
	html+='<td>'+val.type+'</td>';
	html+='<td>'+val.rate+'</td>';
	html+='<td><input type="checkbox" class="chkorderComplete" name="chkorder" value="'+val.order_id+'"  ></td>';
	html+='</tr>';
	//console.log(html);
	$('#package_items > table > tbody').append(html);

	
}



</script>

</head>

<!-- <body onload="on_page_ready();"> -->
<body>
<?php
/**
 * 1.select packageno for เลขที่กล่อง
 */
if ($result = $con->query ( 'select packageno from package  ORDER BY packageno DESC LIMIT 1 ' )) {
	$row = $result->fetch_array ( MYSQL_ASSOC );
	// echo $row['packageno'];
	// echo '<br/>';
	echo "<br/>";
	$numb = date ( "y" ) . str_pad ( ( int ) (substr ( $row ['packageno'], 3, strlen ( $row ['packageno'] ) )) + 1, 6, "0", STR_PAD_LEFT );
	// echo $numb;
}


//unset( $_SESSION ['order_id']);
//unset($_SESSION ['session_order_id']);

$customerHtml='-';
if (isset ( $_SESSION ['order_id'] ) && isset($_SESSION ['session_order_id'])) {
	echo $_SESSION ['session_order_id'].'<br/>';
	print_r ( $_SESSION ['order_id'] );
	echo $_SESSION['customerId'];
	if (count ( $_SESSION ['order_id'] ) > 0) {
		

?>
	<script>
	$(window).load(function(){
		$.getJSON("./package-do.php",{ getCustomerOrderStatusBySession : '<?php echo base64_encode(date('Y-m-d h:i')); ?>',session:<?php echo json_encode($_SESSION ['order_id']); ?> }, function (data) {
			if(data!='no'){
				var i=1;
				var m3=0;
				var weight=0;
				var rate=0;
				$.each(data,function(key,val){
					console.log(val.customer_firstname);
					$('#customerName > b').text(val.customer_firstname);
					addHTMLToPackageItems(i,val);
					m3+=(parseFloat(val.m3)+3.2131);
					weight+=(parseFloat(val.weight));
					rate+=(parseFloat(val.rate));
					i++;
				});
				var html='<tr style="background-color:#c4bd97;border: 1px solid #a19e8d;">';
				html+='<td colspan="">จำนวนกล่อง</td>';
				html+='<td></td>';
				html+='<td colspan="">จำนวนTracking</td>';
				html+='<td></td>';	
				html+='<td colspan="">คิว</td>';
				html+='<td>Kg</td>';
				html+='<td></td>';
				html+='<td></td>';	
				html+='<td></td>';
				html+='<td></td>';
				html+='</tr>';

				html+='<tr style="background-color:#c4bd97;border: 1px solid #a19e8d;">';
				html+='<td colspan="">'+(i-1)+'</td>';
				html+='<td></td>';
				html+='<td colspan="">'+(i-1)+'</td>';
				html+='<td></td>';	
				html+='<td colspan="">'+parseFloat(m3).toFixed(4);+'</td>';
				html+='<td>'+parseFloat(weight).toFixed(2)+'</td>';
				html+='<td></td>';
				html+='<td>ยอดรวม</td>';	
				html+='<td>'+parseFloat(rate).toFixed(2)+' บาท</td>';
				html+='<td></td>';
				html+='</tr>';
				$('#package_items > table > tbody').append(html);
			}else{
				alert('ไม่มีกรายการ');
			}
		});
	});
	</script>
<?php } //end session
} else {
unset($_SESSION['customerId']);
//clear session 
	?>
	<script>
// 	$(window).load(function(){
//		$.getJSON("./package-do.php",{ getCustomerOrderStatus : '<?php //echo base64_encode(date('Y-m-d h:i')); ?>' }, function (data) {
// 			if(data!='no'){
// 				var i=0;
// 				$.each(data,function(key,val){
// 					console.log(val.customer_firstname);
// 					$('#customerName > b').text(val.customer_firstname);
// 					addHTMLToPackageItems(i,val);
// 					i++;
// 				});
// 			}else{
// 				alert('ไม่มีกรายการ');
// 			}
// 		});
// 	});

	</script>
<?php } ?>
<h1>
		<a href="detail.php?package_id=<?php echo $package_id?>">รายละเอียดกล่อง</a>
	</h1>
	<h3>
		<a href="index.php">&larr; Back</a>&nbsp;<a href="../index.php">&larr;
			Home</a>
	</h3>
	<br />
	<div id="package_header">
		<table style="width: 1024px; padding: 10px;" align="center">
			<tr>
				<td width="20%" style="padding: 2px">เลขที่ กล่อง :</td>
				<td width="20%" style="padding: 2px"><label id="package_number"><b><?php echo 'P'.$numb;?></b></label></td>
				<td style="padding: 2px">วันที่สร้าง :</td>
				<td style="padding: 2px"><input id="datepickerCreate"
					class="china datepicker" style="padding: 2px;" value="" /></td>
			</tr>

			<tr>
				<td width="20%" style="padding: 2px">สถานะกล่อง :</td>
				<td width="20%" style="padding: 2px"><label id="packagestatusid"><b>-</b></label></td>
				<td style="padding: 2px">ที่อยู่ :</td>
				<td style="padding: 2px;"><input id="address" class="china"
					style="padding: 2px;" value="" readonly /></td>
			</tr>
			<tr>
				<td width="20%" style="padding: 2px">ลูกค้า :</td>
				<td width="20%" style="padding: 2px"><label id="customerName"><b><?php echo $customerHtml;?></b></label></td>
				<td style="padding: 2px">วิธีส่ง :</td>
				<td style="padding: 2px"><input id="packagestatusid" class="china"
					style="padding: 2px;" value="" readonly /></td>

			</tr>


		</table>
	</div>
	<br>
	<br>

	
	<div class="package"
		style="width: 1024px; padding: 0px; display: table; text-align: center; margin: 0 auto;">
		
		<div id="package_items" style="display: table-cell; width: 90%; vertical-align: top;">
		
			<table class="order-product" style="width: 100%; padding: 0px;"
				align="center">
				<thead>
				
					<th width="80">ลำดับ</th>
					<th>เลขที่<br />Order
					</th>
					<th>เลขที่<br />Trackin(จีน)</th>
					<th>วันที่คีย์<br/>Tracking</th>
					<th>M3</th>
					<th>Kg.</th>
					<th>Rate</th>
					<th>Type</th>
					<th>ยอดค่าขนส่ง<br/>จีน-ไทย</th>
					<th>Action</th>
				</thead>
			<tbody>
			
				
			</tbody>
			</table>
		</div>
		<div style="display: table-cell; width: 10%; vertical-align: top;">
			<div
				style="width: auto; left: 0; right: 0; margin-left: auto; margin-right: auto; text-align: center; margin: 5px;">
				<button class="order-button"
					style="width: 80px; height: 30px; font: 11pt tahoma;"
					onclick="orderComplete()">เพิ่ม</button>
			</div>
			<div
				style="width: auto; left: 0; right: 0; margin-left: auto; margin-right: auto; text-align: center; margin: 5px;">
				
				<button class="order-cancel"
					style="width: 80px; height: 30px; font: 11pt tahoma;"
					onclick="on_remove_detail_button_click()">ลบ</button>
					
			</div>
		</div>
	</div>
	
	

	<!--  <div
		style="width: 760px; padding: 5px; left: 0; right: 0; margin: 0 auto; text-align: right;">
		<label id="package_total_amount" style="font: 11pt tahoma;"><b>ราคารวม
				: 0.00</b></label>
	</div>-->
	<br>
	<br>

	<div class="package" style="width: 100%; padding: 0px; display: table;">
		<div style="display: table-cell; width: 90%; vertical-align: top;">
			<table
				style="width: 1024px; padding: 5px; background: #0070c0; color: white;"
				align="center">
				<tr class="">
					<td colspan="2">ค่าจัดส่งสินค้า จากโกดังไทย-บริษัทขนส่ง</td>
					<td align="right"><input id="amount_cargotothirdparty" type="text"
						style="text-align: right" placeholder="" value="0.00" /></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td colspan="2">ค่าตีลังไม้(ที่จีน)</td>
					<td align="right"><input id="amount_boxchina" type="text"
						style="text-align: right" placeholder="" value="0.00" /></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td colspan="2">ค่าตีลังไม้ที่(ไทย)</td>
					<td align="right"><input id="amount_boxthai" type="text"
						style="text-align: right" placeholder="" value="0.00" /></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td colspan="2">ค่ากล่อง</td>
					<td align="right"><input id="amount_boxpackage" type="text"
						style="text-align: right" placeholder="" value="0.00" /></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td colspan="2">ค่าขนส่ง (ต้นทางของบริษัทขนส่ง)</td>
					<td align="right"><input id="amount_thirdparty" type="text"
						style="text-align: right" placeholder="" value="0.00" /></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td>ค่าอื่นๆ</td>
					<td align="right"><input id="amount_other" type="text"
						style="text-align: right" placeholder="Other" value="0.00" /></td>
					<td align="right"><input id="other_specifiy" type="text"
						style="text-align: right" placeholder="Other Specifiy"
						value="0.00" /></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td>ค่าอื่นๆ</td>
					<td align="right"><input id="amount_other2" type="text"
						style="text-align: right" placeholder="Other2" value="0.00" /></td>
					<td align="right"><input id="other_specifiy2" type="text"
						style="text-align: right" placeholder="Other Specifiy2"
						value="0.00" /></td>
					<td>บาท</td>
				</tr>
			</table>
		</div>
	</div>
	<br>

	<div id="package_total_amount"
		style="width: 1024px; padding: 5px; left: 0; right: 0; margin: 0 auto; text-align: right;">
		<textarea id="remark"
			style="width: 1024px; height: 80px; padding: 5px;"
			placeholder="Remark"></textarea>
	</div>
	<br>

	<div
		style="width: 1024px; left: 0; right: 0; margin-left: auto; margin-right: auto; text-align: center;">
		<button class="order-button" onclick="print()">พิมพ์ที่อยู่คนส่ง ,
			ลูกค้า</button>
		<button class="order-button" onclick="save('<?php echo (isset($_SESSION['customerId']))? $_SESSION['customerId']:''; ?>')">บันทึก</button>
		<button class="order-button" onclick="back()">กลับ</button>
		<button class="order-button" onclick="confirm()">ยืนยัน</button>
	</div>

	<div id="order_product_tracking_box" class="filter">
		<div id="order_product_tracking_box_main"
			style="position: relative; width: 644px; height: 480px; border: 1px solid black; background: #ffffff; padding: 0px; margin: 0 auto;">
			<div
				style="float: right; width: 16px; height: 16px; padding: 5px; cursor: pointer;">
				<div style="padding: 2px;"
					onmouseover="this.style.backgroundColor='#efefef';"
					onmouseout="this.style.backgroundColor='';">
					<img src="images/icon_close.png" alt="" width="12"
						onclick="hide_order_product_tracking();" />
				</div>
			</div>
			<div
				style="position: relative; width: 644px; height: 480px; background-color: white; border-left: 1px solid #01A2E8; border-right: 1px solid #01A2E8; border-bottom: 1px solid #01A2E8; margin: 0 auto">
				<div style="position: fixed; height: 480px; width: 644px;">
					<div
						style="position: relative; width: 644px; height: 480px; padding: 20px;">
						<div
							style="padding: 0px; display: table; text-align: center; margin: 0 auto;">
							<div style="display: table-cell; vertical-align: top;">
								<ul>
									<li
										style="height: auto; padding: 5px; background: white; border-bottom: 0px solid #cccccc;">
										<div
											style="width: 160px; display: table-cell; vertical-align: middle; text-align: left;">
											<label style="font: bold 11pt arial;">Order No : </label>
										</div>
										<div
											style="width: 304px; display: table-cell; vertical-align: middle;">
											<input id="search_order_no"
												style="width: 304px; outline: none;" />
										</div>
									</li>
									<li
										style="height: auto; padding: 5px; background: white; border-bottom: 0px solid #cccccc;">
										<div
											style="width: 160px; display: table-cell; vertical-align: middle; text-align: left;">
											<label style="font: bold 11pt arial;">Tracking No. : </label>
										</div>
										<div
											style="width: 304px; display: table-cell; vertical-align: middle;">
											<input id="search_tracking_no"
												style="width: 304px; outline: none;" />
										</div>
									</li>
								</ul>
							</div>
							<div style="display: table-cell; vertical-align: middle;">
								<div
									style="width: auto; left: 0; right: 0; margin-left: auto; margin-right: auto; text-align: left; margin: 5px;">
									<button class="order-button"
										style="width: 80px; height: 30px; font: 11pt tahoma;"
										onclick="on_search_tracking();">Search</button>
								</div>
							</div>
						</div>
						<div id="order_product_tracking_list"></div>
						<div
							style="position: absolute; width: 644px; bottom: 50px; margin-left: auto; margin-right: auto; text-align: center;">
							<button class="order-button" onclick="">OK</button>
							<button class="order-cancel"
								onclick="hide_order_product_tracking();">Cancel</button>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div id="message_box" class="message_box">
		<div id="message_box_main"
			style="position: absolute; width: auto; min-width: 380px; height: auto; border: 1px solid black; background: #ffffff; padding: 0px">
			<div
				style="float: right; width: 16px; height: 16px; padding: 5px; cursor: pointer;">
				<div style="padding: 2px;"
					onmouseover="this.style.backgroundColor='#efefef';"
					onmouseout="this.style.backgroundColor='';">
					<img src="images/icon_close.png" alt="" width="12"
						onclick="hide_message_box('message_box');" />
				</div>
			</div>
			<div style="padding: 0px; height: auto">
				<div id="message_box_title"
					style="font: bold 11pt tahoma; padding: 20px;"></div>
			</div>
			<div style="padding: 0px; height: auto">
				<div id="message_box_text"
					style="font: 10pt tahoma; width: auto; padding-top: 0px; padding-left: 20px; padding-right: 20px; padding-bottom: 5px;"></div>
			</div>
			<div style="padding: 15px; height: 30px">
				<button id="btnok_message_box"
					onclick="hide_message_box('message_box');" style="cursor: pointer">OK</button>
				<!-- input type="button" value="Cancel"/ -->
			</div>
		</div>
	</div>

	<div id="confirm_delete_item_box" class="message_box">
		<div id="confirm_delete_item_box_main"
			style="position: absolute; width: 380px; height: auto; border: 1px solid black; background: #ffffff; padding: 0px">
			<div
				style="float: right; width: 16px; height: 16px; padding: 5px; cursor: pointer;">
				<div style="padding: 2px;"
					onmouseover="this.style.backgroundColor='#efefef';"
					onmouseout="this.style.backgroundColor='';">
					<img src="images/icon_close.png" alt="" width="12"
						onclick="hide_confirm_delete_item('confirm_delete_item_box');" />
				</div>
			</div>
			<div style="padding: 0px; height: auto">
				<div id="confirm_delete_item_title"
					style="font: bold 11pt tahoma; padding: 20px;">ลบรายการ</div>
			</div>
			<div style="padding: 0px; height: auto">
				<div id="confirm_delete_item_text"
					style="font: 10pt tahoma; width: 340px; padding-top: 0px; padding-left: 20px; padding-bottom: 5px;">คุณต้องการลบรายการที่เลือกใช่หรือไม่</div>
			</div>
			<div style="padding: 15px; height: 30px">
				<button id="confirm_delete_item_ok" onclick=""
					style="cursor: pointer">OK</button>
				<button
					onclick="hide_confirm_delete_item('confirm_delete_item_box');"
					style="cursor: pointer">Cancel</button>
			</div>
		</div>
	</div>
	
	
	<!--  Block order complete -->
		<div id="orderComplete" class="wrap">
			<table>
				<tr>
					<th colspan="2" style="text-align: left"><h2addresBox id="title"></h2></th>
				</tr>
				
						
				<tr>
					<td colspan="2">
						<div class="searchBox">						
								<!-- <div  id="search" >
						              <button type="submit" onclick="searchOrderComplete()">Submit</button>
								</div>		-->
								<button onclick="on_search_tracking();" style="width:80px;height:30px;font:11pt tahoma;" class="order-button">Search</button>
						</div>
					</td>
				</tr>
				
				<tr>
					<td align="right" style="width: 5%;"><label>Order No:</label></td>
					<td ><input type="text" name="orderNo" /></td>
				</tr>
				<tr>
					<td align="right" style="width: 5%;"><label>Tracking No:</label></td>
					<td><input type="text" name="trackingNo" /></td>
				</tr>
				<tr>
					<td colspan="2">
					<form id="orderValidate" method="post" action="./package-do.php">
						<table class="detail-order-complete">
						  <thead> 
							<tr>
								<th>Order No.</th>
								<th>Tracking No.</th>
								<th>วันที่คีย์Tracking</th>
								<th>M3</th>
								<th>Kg.</th>
								<th>Rate</th>
								<th>TYPE</th>
								<th>ค่าขนส่งจีน-ไทย</th>
								<th>ค่าเฉลี่ย</th>
								<th>Action</th>
							</tr>
						  </thead>
						  <tbody>
						  	
						  </tbody>
						</table>
					</form>
						</td>
					</tr>
					<tr class="confirm">
						<td></td>
						<td><a onclick="cancelBtn();">Cancel</a>&emsp;
							<input type="hidden" name="uid" value="<?php echo $_SESSION['ID'] ?>"/>
							<input type="hidden" name="addOrder" value="1"/>
							<button onclick="btnOrderAdd();">Add</button></td>
						</tr>
					</table>
				

	</div>
	<!-- End block order complete -->
	
	
	<div class="wrap" id="addressBox">
			<table>
			<thead>
				<th colspan="2" style="text-align: left; background-color: #0070c0;color:#ffffff;">รายการสินค้าที่ลูกค้าเลือกมีที่อยู่ไม่ตรงกัน กรุณาระบุที่อยู่จัดส่ง</th>
			</thead>
			<tbody>
				
			</tbody>

					</table>
				

	</div>

</body>
</html>
