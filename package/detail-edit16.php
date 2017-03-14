
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
	border-right:none;
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

function on_search_tracking(){
	$.getJSON("./package-do.php",{ searchTracking :$('#searchOrderNumber,#searchTrackingNo').serialize() }, function (data) {
		console.log(data);
		//$('#packagestatusid > b').text(data.packagestatusname);
			
			var html='';
				   //console.log(data.name);
				   var i=0;
				   $.each(data,function(k,v){
						//if(v.order_status_code==='7'){
							html+='<tr style="'+((v.statusid=='0')?'color:#f00':'')+'" class='+((i % 2 == 0) ? "punc" : "") + '>';	
							html+='<td id="'+v.order_number+'">'+v.order_number+'</td>';
							html+='<td id="'+v.order_number+'">'+v.tracking_no_copt+'</td>';4
							html+='<td id="'+v.order_number+'">'+'<?php echo date('d/m/Y');?>'+'</td>';
							html+='<td id="'+v.order_number+'">'+v.m3+'</td>';
							html+='<td id="'+v.order_number+'">'+v.weight+'</td>';
							html+='<td id="'+v.order_number+'">'+v.rate+'</td>';
							html+='<td id="'+v.order_number+'">'+((v.type==1)?'kg':'คิว')+'</td>';
							html+='<td id="'+v.order_number+'">'+v.total+'</td>';
							html+='<td id="'+v.order_number+'">'+((v.type==1)?((parseFloat(v.total)/parseFloat(v.weight))).toFixed(2):(parseFloat(v.total)/parseFloat(v.m3)).toFixed(2))+'</td>';
							
// 							if(v.statustrackingID==0){
// 								html+='<td><input type="checkbox" class="chkorderComplete" name="chkorder" value="'+v.order_number+'"  ></td>';
// 							}else{
								html+='<td><input type="checkbox" class="chkorderComplete" name="chkorder" value="'+v.order_number+'"  ></td>';
							//}
							
							html+='</tr>';
						//}
						i++;
					});
				   	$('.detail-order-complete > tbody').empty();
					$('.detail-order-complete > tbody').append(html);
					console.log(html);
	});
}

$(function() {
	$(".datepicker").datepicker({
        dateFormat: "dd/mm/yy"
	});
	
	/* $( ".timepicker" ).timepicker({
		timeFormat: "HH:mm:ss"
	});*/
});

//count row data
var rows=0;
var format = function(num){
	var str = num.toString().replace("", ""), parts = false, output = [], i = 1, formatted = null;
	if(str.indexOf(".") > 0) {
		parts = str.split(".");
		str = parts[0];
	}
	str = str.split("").reverse();
	for(var j = 0, len = str.length; j < len; j++) {
		if(str[j] != ",") {
			output.push(str[j]);
			if(i%3 == 0 && j < (len - 1)) {
				output.push(",");
			}
			i++;
		}
	}
	formatted = output.reverse().join("");
	return( formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
};
$(function(){
    $(".amount").keyup(function(e){
        $(this).val(format($(this).val()));
    });
});



// Global variable
var offset = 20;
var page = 1;
var pack = new Package();
var package_id = '<?php echo $package_id ?>';
var packagedetail;


$(window).load(function(){

	

	//check total
	$('input[class="amount"]').bind("change keyup", function() {
	    var totalTmp = 0;
	    $('input[class="amount"]').each(function() {
	        //console.log($(this).val());
	        totalTmp += Number($(this).val().replace(/[^0-9\.]+/g,""));
	    });
	    console.log("totalInner:" + totalTmp);
	    $("#ttval").text(formatIn(totalTmp));
	    $("#ttval").append('<input type="hidden" name="total" value="'+totalTmp+'" />');

	});

	function formatIn(n) {
        return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    }


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


function btnConfirm(id){
	 $.getJSON("./package-do.php",{ confirmOrder : id},function(data){
			if(data=="Y"){
				//location.reload();
				alert('ยืนยันสำเร็จ');
			}else{
				alert('ทำรายการไม่สำเร็จ');
			}
	});
	
}

function printAddress(id){
	 window.open("./print.php?id="+id, "MsgWindow", "width=550,height=440");
	//myWindow.document.write("<p>This is 'MsgWindow'. I am 200px wide and 100px tall!</p>");
}

var orderCompleteOn=false;
function orderComplete(){

	/**
	get remian from table 
	send id to เพิ่ม
	select mot in id
	*/
	var id=[];
	$('#package_items  tbody  tr').each(function(key,val){
		console.log(val.id);
		id.push(val.id);
	});
	

	
	orderCompleteOn = !orderCompleteOn;
	var objParam='<?php  echo json_encode((isset($_SESSION['addOrder']))? $_SESSION['addOrder']:''); ?>';
	searchOrderCompleteJSON(id);
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

	console.log(JSON.stringify($items));
	 $.getJSON("./package-do.php",{ removeItemOrder : $items},function(data){
		if(data.success){
			//location.reload();
		}
	});
	$('#action').val('deleteItems');//actionPackage
	
	$.getJSON("./package-do.php",{ actionPackageDelEdit : $('#frmActionPackage').serialize() }, function (data) {
		console.log(data[0]);

		if(data[0]=='Y'){
			//console.log(data.hide.order_product_id);

			//hide row
			
			/**
			 box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
			 */
			$.each(data.hide.order_product_id,function(key,val){
				$('#'+val).hide(100);
				$('#'+val).remove();
				
				//$('#'+val).closest("tr").empty();
			});
			//end hide row
			
			//update ลำดับ
			var index=0;
			$('#package_items table tbody tr').each(function(key,val){
				$(val).find('td:first-child').text((index+1));
				index++;
			});
			
			$('#package_items table tbody tr:nth-child('+(index-1)+') td:first-child').text("จำนวนกล่อง");
			
			//update row
			var box=data.sum.box;
			var tracking=data.sum.tracking;
			var m3=data.sum.m3;
			var kg=data.sum.kg;
			var total=data.sum.total;

			$('#total_ordernumber_text').text(box);
			$('#total_ordernumber').val(box);

			$('#total_tracking_text').text(tracking);
			$('#total_tracking').val(tracking);

			$('#total_m3_text').text(m3);
			$('#total_m3').val(m3);

			$('#total_weight_text').text(kg);
			$('#total_weight').val(kg);

			$('#amount_text').text(total+' บาท');
			$('#amount').val(total);

			
		}else if('Z'){
			alert('ในกล่องต้องมีอย่างน้อย 1 order');
			location.reload(true);
		}
		//$('#packagestatusid > b').text(data.packagestatusname);
		location.reload();
	});
}

function update(customerId){

	$.getJSON("./package-do.php",{ updateDetail : $('#frmHeader,#frmAmount,#frmActionPackage').serialize()},function(data){
		if(data.status=='Y'){
			$('#btnConfirm').attr('confirm',data.packageno);
			alert('บันทึกรายการสำเร็จ');
			window.location.href = './detail-edit.php?id='+data.packageno;
		}else if(data.dup){
			var msgDup='มีการบันทึกทึกรายการซ้ำ\n';
			$.each(data.dup,function(k,v){
				console.log(v.order_number);
				msgDup+='Order Number:'+v.order_number+'-> Tracking Number:'+v.tracking_number+'\n';
			});
			
			alert(msgDup);
		}else{
			alert('บันทึกทึกรายการผิดพลาด');
		}
	});
	// 1 . check address customer 
// 	$.getJSON("./package-do.php",{getAddressbyCustomer:customerId},function(data){
// 		document.getElementById('addressBox1').style.visibility = 'visible';	
// 		var html='';
// 		var j=1;
		
// 		$.each(data,function(k,v){
	
// 			html+='<tr>';
// 			html+='<td>';
// 			if(j==1){
// 				html+='<input checked="checked" ';
// 			}else{
// 				html+='<input ';
// 			}
			
// 			html+='type="radio" value="'+v.address_id+'" name="chkAddress" class="chkAddress"></td>';
// 			html+='<td>'+v.line_1+" "+v.city+" "+v.country+" "+v.zipcode+" Tel."+v.phone+'</td>';
// 		    html+='</tr>';
// 		    //alert(html);
// 		    j++;
			
// 		});

		
// 		html+='<tr class="confirm">';
// 		html+='<td></td>';
		
// 		html+='<td><div style="width: 100%; left: 0; right: 0; margin-left: auto; margin-right: auto; margin-bottom: 18px; text-align: center;">';
// 		html+='<button onclick="confirmAddress()" class="order-button">ตกลง</button>';
// 		html+='<button onclick="back()" class="order-button back-button">กลับ</button></td>';
		
// 		$('#addressBox1 > table > tbody').empty();
// 		$('#addressBox1 > table > tbody').append(html);


// 		//getShippingByOrder
// 		$('#package_items')
// 		$orderProductId=
			
// 		$.getJSON("./package-do.php",{getShippingByOrder:''},function(data){

// 			if(data=='Z'){
				
// 			}else{
// 				var html='';
// 				var i=1;
// 				$.each(data,function(k,v){
				
// 					html+='<tr>';
// 					html+='<td>';
// 					if(i==1){
// 						html+='<input checked="checked" ';
// 					}else{
// 						html+='<input ';
// 					}
// 					html+='type="radio" value="'+v.order_shipping_id+'" name="chkShipping" class="chkShipping"></td>';
// 					html+='<td>'+v.order_shipping_th_option+'</td>';
// 				    html+='</tr>';
// 				    document.getElementById('customerOrderShippingBox').style.visibility = 'visible';
				    
// 				    //alert(html);
// 				    i++;
					
// 				});

// 				html+='<tr class="confirm">';
// 				html+='<td></td>';
				
// 				html+='<td><div style="width: 100%; left: 0; right: 0; margin-left: auto; margin-right: auto; margin-bottom: 18px; text-align: center;">';
// 				html+='<button onclick="confirmShippingBox()" class="order-button">ตกลง</button>';
// 				html+='<button onclick="backShippingBox()" class="order-button back-button">กลับ</button></td>';

// 				$('#customerOrderShippingBox > table > tbody').empty();
// 				$('#customerOrderShippingBox > table > tbody').append(html);
				
// 			}
			
// 		});
// 	});

	
	
	
	
// 	$.getJSON("./package-do.php",{ updateDetail : $('#frmHeader,#frmAmount,#frmActionPackage').serialize()},function(data){
// 		if(data.status=='Y'){
// 			$('#btnConfirm').attr('confirm',data.packageno);
// 			alert('บันทึกรายการสำเร็จ');
// 			window.location.href = './detail-edit.php?id='+data.packageno;
// 		}else{
// 			alert('บันทึกทึกรายการผิดพลาด');
// 		}
// 	});
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
		   document.getElementById('addressBox1').style.visibility = 'hidden';
		   
	});

	
}

function confirmShippingBox(){
	//$('#address').attr('id')=
	//alert($('input[name="chkAddress"]:checked'));

	$('input[name="chkShipping"]:checked').each(function() {
// 		   /console.log(this.value); 
		   $('#customer_order_shipping').attr('customer_order_shipping',this.value);
		   $obj=$(this).parent().parent().find('td:nth-child(2)');
		   //console.log($obj.text());
		   $('#customer_order_shipping').attr('value',$obj.text());
		   document.getElementById('customerOrderShippingBox').style.visibility = 'hidden';
		   
	});

	
}






function searchOrderCompleteJSON(id){
	
	var html='';
	$.getJSON("./package-do.php",{ addOrderCompleteEdit : '<?php echo base64_encode(date('Y-m-d h:i')); ?>', params:id }, function (data) {
		   //console.log(data.name);
		   var i=0;
		   
		   if(data.snull=="snull"){
			   
			   alert('กรุณาเลือกลูกค้าจากหน้าหลักก่อน');
			   document.getElementById('orderComplete').style.visibility = 'hidden';
				
				
		   }else{
			   $.each(data,function(k,v){
					//if(v.order_status_code==='7'){
						html+='<tr style="'+((v.statusid=='0')?'color:#f00':'')+'" class='+((i % 2 == 0) ? "punc" : "") + '>';	
						html+='<td id="'+v.order_number+'">'+v.order_number+'</td>';
						html+='<td id="'+v.order_number+'">'+v.tracking_no_copt+'</td>';4
						html+='<td id="'+v.order_number+'">'+'<?php echo date('d/m/Y');?>'+'</td>';
						html+='<td id="'+v.order_number+'">'+v.m3+'</td>';
						html+='<td id="'+v.order_number+'">'+v.weight+'</td>';
						html+='<td id="'+v.order_number+'">'+v.rate+'</td>';
						html+='<td id="'+v.order_number+'">'+((v.type==1)?'kg':'คิว')+'</td>';
						html+='<td id="'+v.order_number+'">'+v.total+'</td>';
						if(v.m3==0 || v.weight==0){
							html+='<td id="'+v.order_number+'">0.00</td>';
						}else{
							html+='<td id="'+v.order_number+'">'+((v.type==1)?((parseFloat(v.total)/parseFloat(v.weight))).toFixed(2):(parseFloat(v.total)/parseFloat(v.m3)).toFixed(2))+'</td>';
						}
						
						
						if(v.statustrackingID==0){
							html+='<td></td>';
						}else{
							html+='<td><input type="checkbox" class="chkorderComplete" name="chkorder" value="'+v.tracking_no_copt+'"  ></td>';
						}
						
						html+='</tr>';
					//}
					i++;
				});
			   	$('.detail-order-complete > tbody').empty();
				$('.detail-order-complete > tbody').append(html);
				console.log(html);
		   }
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
		$.getJSON("./package-do.php",{ addOrderItems : '<?php echo base64_encode(date('Y-m-d h:i')); ?>', params:$paramArray }, function (val) {
			//console.log(val);

			//add data to package_items
			var sizeOfRow=$('#package_items table tbody tr').length;
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
			//$('#package_items table tbody tr').after(html);
			
			
			
			document.getElementById('orderComplete').style.visibility = 'hidden';
			//$('#orderComplete').
			location.reload(true);
		});
		//location.reload(true);
	}else{
		alert('กรุณาเลือกรายการ');
	}
	
	
}

function addHTMLToPackageItems(i,val){
	//console.log(val);
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

 	$('#package_items > table > tbody').append(html);

	
}

var addressBoxOn1=false;
function addressBox1(data,op){
	
	addressBoxOn1 = !addressBoxOn1;
	
	
	if(addressBoxOn1){
		if(op==1){
			
			var html=data[0].line_1+" "+data[0].city+" "+data[0].country+" "+data[0].zipcode+" Tel."+data[0].phone;
			//alert(html);
			//$('#address').attr('addressId',data[0].address_id);
			
			$( document ).ready(function() {
				$('#address').attr('addressId',data[0].address_id);
			    $('#address').attr('value',html);
			   
			});
			
		}
		if(op==0){
			
			document.getElementById('addressBox1').style.visibility = 'visible';	
			var html='';
			var j=1;
			$.each(data,function(k,v){
		
				html+='<tr>';
				html+='<td>';
				if(j==1){
					html+='<input checked="checked" ';
				}else{
					html+='<input ';
				}
				
				html+='type="radio" value="'+v.address_id+'" name="chkAddress" class="chkAddress"></td>';
				html+='<td>'+v.line_1+" "+v.city+" "+v.country+" "+v.zipcode+" Tel."+v.phone+'</td>';
			    html+='</tr>';
			    //alert(html);
			    j++;
				
			});

			
			html+='<tr class="confirm">';
			html+='<td></td>';
			
			html+='<td><div style="width: 100%; left: 0; right: 0; margin-left: auto; margin-right: auto; margin-bottom: 18px; text-align: center;">';
			html+='<button onclick="confirmAddress()" class="order-button">ตกลง</button>';
			html+='<button onclick="back()" class="order-button back-button">กลับ</button></td>';
			
			$('#addressBox1 > table > tbody').empty();
			$('#addressBox1 > table > tbody').append(html);
			
		}
		
		

	}
		
}

var customerOrderShipping=false;
function customerOrderShippingBox(data,op){
	customerOrderShipping = !customerOrderShipping;
	
	if(customerOrderShipping){
		if(op==1){
			
			var html=data[0].ORDER_SHIPPING_TH_OPTION;
			//alert(html);
			//$('#address').attr('addressId',data[0].address_id);
			$( document ).ready(function() {
				$('#customer_order_shipping').attr('addressId',data[0].ORDER_SHIPPING_ID);
			    $('#customer_order_shipping').attr('value',html);
			});
			
		}else{
				
			
			var html='';
			var i=1;
			$.each(data,function(k,v){
			
				html+='<tr>';
				html+='<td>';
				if(i==1){
					html+='<input checked="checked" ';
				}else{
					html+='<input ';
				}
				html+='type="radio" value="'+v.ORDER_SHIPPING_ID+'" name="chkShipping" class="chkShipping"></td>';
				html+='<td>'+v.ORDER_SHIPPING_TH_OPTION+'</td>';
			    html+='</tr>';
			    document.getElementById('customerOrderShippingBox').style.visibility = 'visible';
			    
			    //alert(html);
			    i++;
				
			});

			html+='<tr class="confirm">';
			html+='<td></td>';
			
			html+='<td><div style="width: 100%; left: 0; right: 0; margin-left: auto; margin-right: auto; margin-bottom: 18px; text-align: center;">';
			html+='<button onclick="confirmShippingBox()" class="order-button">ตกลง</button>';
			html+='<button onclick="backShippingBox()" class="order-button back-button">กลับ</button></td>';

			$('#customerOrderShippingBox > table > tbody').empty();
			$('#customerOrderShippingBox > table > tbody').append(html);
		}
		
		

	}
		
}



function back(){
	
}

function confirmAddress1(){
	//document.getElementById('addressBox1').style.visibility = 'hidden';
	
}



</script>

</head>

<!-- <body onload="on_page_ready();"> -->
<body>

<div class="wrap" id="addressBox1" style="z-index: 100;" >
			<table>
				<thead>
					<th colspan="2" style="text-align: left; background-color: #0070c0;color:#ffffff;">รายการสินค้าที่ลูกค้าเลือกมีที่อยู่ไม่ตรงกัน กรุณาระบุที่อยู่จัดส่ง</th>
				</thead>
				<tbody>
					
				</tbody>

			</table>
				

	</div>
	
	<div class="wrap" id="customerOrderShippingBox" style="z-index: 100;" >
			<table>
				<thead>
					<th colspan="2" style="text-align: left; background-color: #0070c0;color:#ffffff;">รายการสินค้าที่ลูกค้าเลือกมีที่อยู่ไม่ตรงกัน กรุณาระบุวิธีจัดส่ง</th>
				</thead>
				<tbody>
					
				</tbody>

			</table>
				

	</div>
<?php
/**
 * 1.select packageno for เลขที่กล่อง
 */



/**
	select data edit
	
 */
if(isset($_GET['id'])){
	$arrayData=array();
	$sqlPackage='select * from package p
inner join customer_address ca on ca.address_id=p.shipping_address
inner join customer_order_shipping cos on cos.order_shipping_id=p.shippingid
where packageno=?';
	//echo $sqlPackage;
	if ($stmt = $con->prepare ( $sqlPackage )) {
		$stmt->bind_param ( "s", trim ( $_GET['id'] ) );
		$stmt->execute ();
		$result=$stmt->get_result();
		while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
			$arrayData[]=$row;
		}
	}
	
	if(count($arrayData)<=0){
		//echo '<script>window.location.href = "./index.php"</script>';
	}
// 	echo '<pre>';
// 		print_r($arrayData);
// 	echo '</pre>';
	
	
	$sqlGetBox='select * from package p
inner join package_detail pd on pd.packageid=p.packageid
inner join customer_order co on co.order_id=pd.order_id
inner join customer_order_product cop on cop.order_id=co.order_id
inner join customer_order_product_tracking copt on copt.order_product_id=cop.order_product_id
where p.packageno=?';
	
// 	echo $sqlGetBox;
	if ($stmt = $con->prepare ( $sqlGetBox )) {
		$stmt->bind_param ( "s", trim ( $_GET['id'] ) );
		$stmt->execute ();
		$result=$stmt->get_result();
		while ($row = $result->fetch_assoc()) {
			//echo json_encode($row);
			$arrayBoxData[]=$row;
		}
	}
	
	if(count($arrayBoxData)>0){
		echo "<pre>";
		//print_r($arrayBoxData);
		echo "</pre>";
	}	
	
	$htmlGrid='';
	if(count($arrayBoxData)>0){
		$arrTempSessionDetail=array();
		$i=0;
		$m3=0;
		$weight=0;
		$rate=0;
		foreach($arrayBoxData as $key=>$val){

			//add to session
			
// 			if(isset($_SESSION['details'])){
// 				unset($_SESSION['details']);
// 			}
			
			$tempObjArray=array();
			$tempObjArray['order_product_id']=$val['order_product_id'];
			$tempObjArray['customer_id']=$val['customer_id'];
			$tempObjArray['tracking_no']=$val['tracking_no'];
			$arrTempSessionDetail[]=$tempObjArray;
			
			
			
			
		}
			
			
	}
// 		echo "<pre>";
// 		print_r($arrTempSessionDetail);
// 		echo "</pre>";
	if(count($arrTempSessionDetail)>0){
// 		unset($_SESSION['details']);
// 		echo "<pre>";
// 		print_r($arrTempSessionDetail);
// 		echo "</pre>";
		
// 		if(!isset($_SESSION['edit'])){
// 			//print_r($_SESSION['details']);
// 			$_SESSION['details']=$arrTempSessionDetail;
// 		}else{
// 			echo $_SESSION['edit'];
// 			print_r($_SESSION['details']);
// 			//unset($_SESSION['edit']);
// 		}
		if(isset($_SESSION['edit'])){
			//echo $_SESSION['edit'];
// 			echo "<pre>";
// 			print_r($_SESSION['details']);
// 			echo "</pre>";
		}else{
			$_SESSION['edit']='Y';
			
			$_SESSION['details']=$arrTempSessionDetail;
		}
	
	}
	
	
}else{
	//echo '<script>window.location.href = "./index.php"</script>';
}




if(isset($_SESSION['addOrder']) || isset($_SESSION['details'])){

	
	echo "<pre>";
 	print_r($_SESSION['details']);
 	echo "</pre>";
	//$_IN= '('.implode($_SESSION['details'], ',').')';
	$_IN=array();
	foreach($_SESSION['details'] as $key => $val){
		$_IN[]=$val['order_product_id'];
	}
	
	if(isset($_SESSION['details'][0]['customer_id'])){
		$_CUSID=$_SESSION['details'][0]['customer_id'];
	}else{
		$_CUSID=0;
	}
	

	$sql='SELECT CO.*,C.*,COP.*,COPT.* FROM CUSTOMER_ORDER CO ';
	$sql.='INNER JOIN CUSTOMER C ON C.CUSTOMER_ID = CO.CUSTOMER_ID ';
	$sql.='INNER JOIN CUSTOMER_ORDER_PRODUCT COP ON COP.ORDER_ID= CO.ORDER_ID ';
	$sql.='INNER JOIN CUSTOMER_ORDER_PRODUCT_TRACKING COPT ON COPT.ORDER_ID= CO.ORDER_ID ';
	$sql.='where CO.CUSTOMER_ID ='.$_CUSID.'  and COPT.order_product_id  in ('.implode(',', $_IN).')';
	$sql.=' and COPT.statusid=1';
	$sql.=' GROUP BY COPT.tracking_no';
	//echo $sql;
	//echo '<br/>';

	$ordersItems=array();
	//echo $sql;
	if ($result = $con->query ( $sql )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$ordersItems [] = $row;
		}
		//print_r($ordersItems);
		//add data to table
		$html='';
		
		if(count($ordersItems)>0){
			$i=0;
			$m3=0;
			$weight=0;
			$total=0;
			foreach($ordersItems as $key=>$val){
				//echo json_encode($val);
				//$html.='<script>addHTMLToPackageItems(1,'.json_encode($val).')</script>';
				//addHTMLToPackageItems
				
				$htmlGrid.='<tr id="'.$val['order_product_id'].'" style="'.(($val['statusid']==0)?"color:#f00":"").'">';
				//console.log(i);
				$htmlGrid.='<td>'.(++$i).'</td>';
				$htmlGrid.='<td>'.$val['order_number'].'</td>';
				$htmlGrid.='<td>'.$val['tracking_no'].'</td>';
				$htmlGrid.='<td>'.date('d/m/Y').'</td>';
				$htmlGrid.='<td>'.$val['m3'].'</td>';
				$m3+=$val['m3'];	
				$htmlGrid.='<td>'.$val['weight'].'</td>';
				$weight+=$val['weight'];
				$htmlGrid.='<td>'.$val['rate'].'</td>';
				$total+=$val['total'];
				$htmlGrid.='<td>'.$val['type'].'</td>';
				$htmlGrid.='<td>'.$val['total'].'</td>';
				$htmlGrid.='<td><input type="checkbox" class="chkorderComplete" name="chkorder[]" value="'.$val['order_product_id'].'"  ></td>';
				$htmlGrid.='</tr>';
			}
			
			$htmlGrid.='<tr style="background-color:#c4bd97;border: 1px solid #a19e8d;">';
			$htmlGrid.='<td colspan="">จำนวนกล่อง</td>';
			$htmlGrid.='<td></td>';
			$htmlGrid.='<td colspan="">จำนวนTracking</td>';
			$htmlGrid.='<td></td>';
			$htmlGrid.='<td colspan="">คิว</td>';
			$htmlGrid.='<td>Kg</td>';
			$htmlGrid.='<td></td>';
			$htmlGrid.='<td></td>';
			$htmlGrid.='<td></td>';
 			$htmlGrid.='<td></td>';
			$htmlGrid.='</tr>';
			
			$htmlGrid.='<tr style="background-color:#c4bd97;border: 1px solid #a19e8d;">';
			$htmlGrid.='<td colspan="" id="total_ordernumber_text">'.($i).'</td><input type="hidden" id="total_ordernumber" name="total_ordernumber" value="'.($i).'"/>';
			$htmlGrid.='<td></td>';
			$htmlGrid.='<td colspan="" id="total_tracking_text">'.($i).'</td><input type="hidden" id="total_tracking" name="total_tracking"  value="'.($i).'"/>';
			$htmlGrid.='<td></td>';
			$htmlGrid.='<td colspan="" id="total_m3_text">'.$m3.'</td><input type="hidden" id="total_m3" name="total_m3"  value="'.($m3).'"/>';
			$htmlGrid.='<td id="total_weight_text">'.$weight.'</td><input type="hidden" id="total_weight"  name="total_weight"  value="'.($weight).'"/>';
			$htmlGrid.='<td></td>';
			$htmlGrid.='<td>ยอดรวม</td>';
			$htmlGrid.='<td id="amount_text">'.(number_format($total, 2, '.', '')).' บาท</td><input type="hidden" id="amount" name="amount"  value="'.(number_format($total, 2, '.', '')).'"/>';
			$htmlGrid.='<td></td>';
			$htmlGrid.='</tr>';
			//$('#package_items > table > tbody').append(html);
			
		}
		
	}
	
	$addressItems=array();
	
	$sqlAddress='SELECT CUSADD.* FROM CUSTOMER_ADDRESS CUSADD WHERE CUSADD.CUSTOMER_ID='.$_CUSID;
	//echo $sqlAddress;
	$js='';
	if ($result = $con->query ( $sqlAddress )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$addressItems [] = $row;
		}
		
		if(count($addressItems)>1){
			$js='<script>addressBox1('.json_encode($addressItems).',0);</script>';
		
		}else{
			
			$js='<script>addressBox1('.json_encode($addressItems).',1);</script>';
		}
		echo $js;
	}
	
	
	//shiping
	//customerOrderShippingBox
	$orderProductId=array();
	if(count($_SESSION['details'])>0){
		foreach($_SESSION['details'] as $val){
			$orderProductId[]=$val['order_product_id'];
		}
	}
	
	$shippingItems=array();
//	$sqlShipping='SELECT ORDER_SHIPPING_TH_OPTION FROM CUSTOMER_ORDER_SHIPPING WHERE ORDER_ID IN ('.implode(',', $orderProductId).') GROUP BY ORDER_ID';
	$sqlShipping='SELECT COS.ORDER_SHIPPING_ID ,COS.ORDER_SHIPPING_TH_OPTION FROM CUSTOMER_ORDER_PRODUCT CO
INNER JOIN CUSTOMER_ORDER_SHIPPING COS ON COS.ORDER_ID=CO.ORDER_ID
WHERE CO.ORDER_PRODUCT_ID IN ('.implode(',', $orderProductId).')  GROUP BY COS.ORDER_SHIPPING_TH_OPTION';
	//echo $sqlShipping;
	$js='';
	if ($result = $con->query ( $sqlShipping )) {
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$shippingItems [] = $row;
		}
		if(count($shippingItems)>1){
			$js='<script>customerOrderShippingBox('.json_encode($shippingItems).',0);</script>';
	
		}else{
				
			$js='<script>customerOrderShippingBox('.json_encode($shippingItems).',1);</script>';
		}
		echo $js;
	}
// 	echo "</pre>";
	
}

if ($result = $con->query ( 'select packageno from package where packageno="'.$_GET['id'].'"')) {
	$row = $result->fetch_array ( MYSQL_ASSOC );
	// echo $row['packageno'];
	// echo '<br/>';
	echo "<br/>";
	$numb = $row['packageno'];
	
	// echo $numb;
}


//unset( $_SESSION ['order_id']);
//unset($_SESSION ['session_order_id']);

$customerHtml='-';
if (isset ( $_SESSION ['order_id'] ) && isset($_SESSION ['session_order_id'])) {
// 	echo $_SESSION ['session_order_id'].'<br/>';
// 	print_r ( $_SESSION ['order_id'] );
// 	echo $_SESSION['customerId'];
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
	<form action="POST" id="frmHeader" style="width: 1024px; padding: 0px; display: table; text-align: center; margin: 0 auto;">
		<table style="width: 1024px; padding: 10px;" align="center">
			<tr>
				<td width="20%" style="padding: 2px">เลขที่ กล่อง :</td>
				<td width="20%" style="padding: 2px"><label id="package_number"><b><?php echo $numb;?><input type="hidden" name="packageno" value="<?php echo $numb;?>" /></b></label></td>
				<td style="padding: 2px">วันที่สร้าง :</td>
				<td style="padding: 2px"><input id="datepickerCreate"
					class="china datepicker" style="padding: 2px;" value="" name="datepickerCreate" /></td>
			</tr>

			<tr>
				<td width="20%" style="padding: 2px">สถานะกล่อง :</td>
				<td width="20%" style="padding: 2px"><label id="packagestatusid"><b>-</b></label></td>
				<td style="padding: 2px">ที่อยู่ :</td>
				<td style="padding: 2px;"><input id="address" class="china" name="address"
					style="padding: 2px;" value="<?php echo $arrayData[0]['line_1'].' '.$arrayData[0]['line_1'].' '.$arrayData[0]['city'].' '.$arrayData[0]['country'].' '.$arrayData[0]['zipcode'].' '.$arrayData[0]['phone'];?>" readonly />
					<input id="addressid" type="hidden" name="addressid" value="<?php echo $arrayData[0]['address_id'];?>" />
					</td>
					
			</tr>
			<tr>
				<td width="20%" style="padding: 2px">ลูกค้า :</td>
				<td width="20%" style="padding: 2px"><label id="customerName"><b><?php echo $customerHtml;?></b></label></td>
				<td style="padding: 2px">วิธีส่ง :</td>
				<td style="padding: 2px"><input id="customer_order_shipping" class="china" name="customer_order_shipping"
					style="padding: 2px;" value="<?php echo $arrayData[0]['order_shipping_th_option'];?>" readonly />
					<input id="shipingid" type="hidden" name="shipingid" value="<?php echo $arrayData[0]['order_shipping_id']?>" />
					</td>

			</tr>


		</table>
	</form>
	</div>
	<br>
	<br>

	<form id="frmActionPackage" onsubmit="return false;" method="post" action="./package-do.php" style="width: 1024px; padding: 0px; display: table; text-align: center; margin: 0 auto;">
	<div  class="package">
		
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
			<?php echo $htmlGrid;?>
				
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
				
				<input type="hidden" name="action" value="" id="action"/>
				<button class="order-cancel"
					style="width: 80px; height: 30px; font: 11pt tahoma;"
					onclick="on_remove_detail_button_click()">ลบ</button>
					
			</div>
		</div>
	</div>
	</form>
	
	

	<!--  <div
		style="width: 760px; padding: 5px; left: 0; right: 0; margin: 0 auto; text-align: right;">
		<label id="package_total_amount" style="font: 11pt tahoma;"><b>ราคารวม
				: 0.00</b></label>
	</div>-->
	<br>
	<br>

	<form method="get" id="frmAmount" style="width: 1024px; padding: 0px; display: table; text-align: center; margin: 0 auto;">
	<div class="package" style="width: 100%; padding: 0px; display: table;">
		<div style="display: table-cell; width: 90%; vertical-align: top;">
			<table
				style="width: 1024px; padding: 5px; background: #0070c0; color: white;"
				align="center">
				
				<tr class="">
					<td colspan="2">ค่าตีลังไม้(ที่จีน)</td>
					<td align="right"><input id="amount_boxchina" class="amount" type="text" name="amount_boxchina"
						style="text-align: right" placeholder="" value="<?php echo number_format($arrayData[0]['amount_rack'], 2, '.', '');?>" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
					<td>บาท</td>
				</tr>				
				<tr class="">
					<td colspan="2">ค่ากล่อง</td>
					<td align="right"><input id="amount_boxpackage" class="amount" type="text" name="amount_boxpackage"
						style="text-align: right" placeholder="" value="<?php echo number_format($arrayData[0]['amount_box'], 2, '.', '');?>" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td colspan="2">ค่าฝากส่ง(ค่าส่งสินค้าจากโกดังไปบริษัทขนส่ง)</td>
					<td align="right"><input id="amount_pass" class="amount" type="text" name="amount_pass"
						style="text-align: right" placeholder="" value="<?php echo number_format($arrayData[0]['amount_pass'], 2, '.', '');?>" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td colspan="2">ค่าขนส่งในไทย</td>
					<td align="right"><input id="amount_thirdparty" class="amount" type="text" name="amount_thirdparty"
						style="text-align: right" placeholder="" value="<?php echo number_format($arrayData[0]['amount_thirdparty'], 2, '.', '');?>" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
					<td>บาท</td>
				</tr>

				<tr class="">
					<td>ค่าอื่นๆ</td>
					<td align="right"><input id="amount_other2" type="text" value="<?php echo $arrayData[0]['amount_other_specify'];?>"
						style="text-align: right" placeholder="Other2" name="amount_other2"   /></td>
					<td align="right"><input id="other_specifiy2" type="text" class="amount" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" name="other_specifiy2"
						style="text-align: right" placeholder="Other Specifiy2"
						value="<?php echo number_format($arrayData[0]['amount_other'], 2, '.', '');?>" /></td>
					<td>บาท</td>
				</tr>
				
				<tr style="border: 1px solid rgb(161, 158, 141); background-color: rgb(196, 189, 151);">
					<td colspan="" style="color:#000">จำนวนกล่อง</td>
					<td colspan="" style="text-align: right;color:#000;"><input name="total_count" type="text" id="total_count" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" style="text-align: right;" placeholder="" value="<?php echo number_format($arrayData[0]['total_count'], 2, '.', '');?>"></td>
					<td style="text-align: right; position: relative;color:#000;" colspan=""><span style="position: relative; left: -150px;">รวม</span> <span style="" id="ttval"><input type="hidden" name="total" value="<?php echo number_format($arrayData[0]['total'], 2, '.', '');?>" /><?php echo number_format($arrayData[0]['total'], 2, '.', '');?></span> บาท</td>

				<td colspan=""></td>
				</tr>
			</table>
		</div>
	</div>
	
	
	<br>

	<div id="package_total_amount"
		style="width: 1024px; padding: 5px; left: 0; right: 0; margin: 0 auto; text-align: right;">
		<textarea id="remark"
			style="width: 1024px; height: 80px; padding: 5px;"
			placeholder="Remark" name="remark" ><?php echo $arrayData[0]['remark'];?></textarea>
	</div>
	</form>
	<br>

	<div
		style="width: 1024px; left: 0; right: 0; margin-left: auto; margin-right: auto; text-align: center;">
		<button class="order-button" onclick="printAddress('<?php echo $numb;?>')">พิมพ์ที่อยู่คนส่ง ,
			ลูกค้า</button>
		<button class="order-button" onclick="update('<?php echo (isset($_SESSION['details']))? $_SESSION['details'][0]['customer_id']:''; ?>')">บันทึก</button>
		<button class="order-button" onclick="back()">กลับ</button>
	    <button class="order-button" id="btnConfirm" onclick="btnConfirm('<?php echo $numb;?>')">ยืนยัน</button>
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
					<td ><input type="text" name="searchOrderNumber" id="searchOrderNumber" /></td>
				</tr>
				<tr>
					<td align="right" style="width: 5%;"><label>Tracking No:</label></td>
					<td><input type="text" name="searchTrackingNo" id="searchTrackingNo" /></td>
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
