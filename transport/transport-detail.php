<!DOCTYPE html>
<?php 
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';
date_default_timezone_set('Asia/Bangkok');

?>
<html>
<head>
<title>Delivery detail</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	
	<link rel="stylesheet" href="../css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../css/cargo.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	rel="stylesheet">
<link
	href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300'
	rel='stylesheet' type='text/css'>
	
	<link href="./index.css" rel="stylesheet">

<style>
i {
	color: #0070c0;
}

.paging a {
    text-decoration: underline;
}

a.current-page {
    text-decoration: none;
}

button,.button {
	color: #0070c0;
}

a {
	color: #e36c09;
}

th {
	background: #0070c0;
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

.tracking_thai{
	background-color: #ddd9c3;
}

table.detail-order-complete  tr:hover{
	background: #b2dfdb none repeat scroll 0 0 !important;
}
.trackingTH{
	margin-top: 1em;
}
.trackingTH label{
	padding: 0 5px;
}

.trackingTH div{
	margin: 5px 0px;
}

form{
	display: block;
}

.material-icons {
	font-size: 18px;
	cursor: pointer;
}

i{
	color:#f00;
	padding-left: 5px;
    vertical-align: middle;
}

.in{
	border:1px solid #000;
	width:90%;
	padding: 20px;
}
</style>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
<script src="../js/jquery-ui-timepicker-addon.min.js"></script>

<script src="js/ajaxlib.js"></script>
<script src="js/util.js"></script>
<script src="js/packagelib.js"></script>
<script src="js/package_ui_events.js"></script>

<script type="text/javascript">
	$( document ).ready(function() {
		  
	  //getDetail 
		  $.getJSON("./transport-do.php",{ getDetail :'<?php echo (isset($_GET['id']))?$_GET['id']:"";?>'}, function (data) {
				if(data!=null){
					$.each(data,function(key,val){
						console.log(key);
						$('#packageid').val(val.packageid);
						$('#packageno span').text(data[0].packageno);
						$('#orderno').append(val.order_number+((data.length>1 && key < data.length-1)?', ':''));
						$('#customer_firstname span').text(data[0].customer_firstname+" "+data[0].customer_lastname);
						$('#send_user').val(data[0].customer_firstname);
						$('#total span').text(self.formatNumber(val.total));
						$('#customer_code span').text(val.customer_code);	
						$('#status span').text(val.packagestatusname);
						//$('#paydate span').text(val.paydate);
						$('#transport_th_name span').text(val.transport_th_name);
						$('#total_count span').text(val.total_count);
						$('.total_box').val(val.total_count-val.total_count_sent);
						$('#total_count_sent span').text(val.total_count_sent);
						
						var totalNotSend=Number(val.total_count)-Number(val.total_count_sent);
						$('#totalNotSend span').text(totalNotSend);
												
						if(val.statusid===3){
							$('#btn_submit,#btnAdd').hide().remove();
							
						}

						if(totalNotSend == 0){
							$('#btnAdd').remove();
						}
						
											
					});
					function formatNumber(number)
					  {
					      number = number.toFixed(2) + '';
					      x = number.split('.');
					      x1 = x[0];
					      x2 = x.length > 1 ? '.' + x[1] : '';
					      var rgx = /(\d+)(\d{3})/;
					      while (rgx.test(x1)) {
					          x1 = x1.replace(rgx, '$1' + ',' + '$2');
					      }
					      return x1 + x2;
					  }
					
				}
		   });




		   //get box detail insert
		    $.getJSON("./transport-do.php",{ getBoxDetail :'<?php echo (isset($_GET['id']))?$_GET['id']:"";?>'}, function (data) {
		    	var html='';
		    	//initial
					if(data=='i'){
						html+='<div class="box-1">';
						html+='<p>รอบที่1</p>';
						html+='<div class="in">';
						html+='<div>';
						html+='<div class="col cl_1">วันที่ส่ง: <input id="datepickerCreate" class="china datepicker" style="padding: 2px;" value="" name="datepickerCreate[]" /></div>';
						html+='<div class="col cl_2">จำนวนกล่องที่ส่ง: <input id="total_box_1" class="total_box" class="amount" type="text" name="total_box[]"';
						html+='style="text-align: right" placeholder="" value="0" onkeyup="this.value=this.value.replace(/[^0-9]/g,\'\');" /></div>';
						html+='<button id="btn-frm-1" class="gr" onclick="chkBoxRemain(document.getElementById(\'total_box_1\').value); return false;">ok</button>';
						html+='</div>';
						html+='<div>';
						html+='<div class="col cl_1">ผู้ส่ง: ';
						html+='</div>';
						html+='<div class="col cl_2">วันที่คีย์: </div>';
						html+='</div>';
						html+='<div class="tracking_thai">';
						html+='<div class="col cl_1">เลขที่Trackingไทย: </div>';
						html+='</div>';
						html+='<div>';
						html+='<div class="trackingTH" id="trackingTH">';
						html+='</div>';
						html+='</div>';
						html+='<p>หมายเหตุ</p>';
						html+='<div>';
						html+='<textarea class="remark" name="remark[]"></textarea>';
						html+='</div>';
						html+='</div>';
						html+='</div>';

						$('#btnAdd').hide();

						
						
					}else if(data !=null){
						
						var i=data.length;
						var packageidArr='';
						$.each(data,function(key,val){
							html+='<div class="box-'+(i)+'">';
							
							html+='<p>รอบที่ '+(i)+'</p>';
							html+='<div class="in">';
							if(val.statusid!=3){
								html+='<i class="material-icons" id="close-'+key+'" style="float:right;" onclick="clearPackageSend('+val.sendid+')" title="clear">clear</i>';
							}
							
							html+='<div>';
							html+='<div class="col cl_1">วันที่ส่ง: <input id="datepickerCreate" class="china datepicker" style="padding: 2px;" value="'+val.send_date+'" name="datepickerCreate[]" /></div>';
							html+='<div class="col cl_2">จำนวนกล่องที่ส่ง: ';
							
							if(i==0){
								html+='<input id="total_box_1"  value="'+val.total_send+'" class="total_box" class="amount" type="text" name="total_box[]" style="text-align: right" placeholder="" value="0" onkeyup="this.value=this.value.replace(/[^0-9]/g,\'\');" /></div>';
								html+='<button id="btn-frm-1" class="gr" onclick="chkBoxRemain(document.getElementById(\'total_box_1\').value); return false;">ok</button>';
							}else{
								html+='<input readonly id="total_box_1" value="'+val.total_send+'" class="total_box" class="amount" type="text" name="total_box[]" style="text-align: right" placeholder="" value="0" onkeyup="this.value=this.value.replace(/[^0-9]/g,\'\');" /></div>';
							}
							
							html+='</div>';
							html+='<div>';
							html+='<div class="col cl_1">ผู้ส่ง: ';
							html+='</div>';
							html+='<div class="col cl_2">วันที่คีย์: <span><?php echo date('m/d/Y')?></span> </div>';
							html+='</div>';
							html+='<div class="tracking_thai">';
							html+='<div class="col cl_1">เลขที่Trackingไทย: <span>'+val.trackingno_thai+'</span> </div>';
							html+='</div>';
							html+='<div>';
							html+='<div class="trackingTH" id="trackingTH">';
							html+='</div>';
							html+='</div>';
							html+='<p>หมายเหตุ</p>';
							html+='<div>';
							html+='<textarea class="remark" '+((val.statusid==3)?'readonly':'')+' name="remark[]">'+val.send_remark+'</textarea>';
							html+='</div>';
							html+='</div>';
							html+='</div>';

							i--;
							packageidArr+=val.packageid;
							if(i<0){
								packageidArr+=',';
							}



						
							
					
							
						});

						$('#btnAdd').val(packageidArr);

						
							
						


						
					}
					
					 $('#showBox').append(html);
					 
					  $( "#datepickerCreate" ).datepicker({
						    dateFormat: "yy-mm-dd"
					  });
	
					  $( "#datepickerCreate" ).datepicker(
							    'setDate', new Date()
					 );
		    });


		    $('#btnAdd').on('click',function(){

				if($(this).val()!=""){
					//alert($(this).val());
				}

			    
			    var i=2;
			    var html='';
		    	html+='<div class="box-'+(i)+'">';
				html+='<p>รอบที่ '+(i)+'</p>';
				html+='<div class="in">';
				html+='<div>';
				html+='<div class="col cl_1">วันที่ส่ง: <input id="datepickerCreate_'+i+'" class="china datepicker" style="padding: 2px;" value="" name="datepickerCreate[]" /></div>';
				html+='<div class="col cl_2">จำนวนกล่องที่ส่ง: <input id="total_box_1" value="" class="total_box" class="amount" type="text" name="total_box[]"';
				html+='style="text-align: right" placeholder="" value="0" onkeyup="this.value=this.value.replace(/[^0-9]/g,\'\');" /></div>';
				html+='<button id="btn-frm-1" class="gr" onclick="chkBoxRemain(document.getElementById(\'total_box_1\').value); return false;">ok</button>';
				html+='</div>';
				html+='<div>';
				html+='<div class="col cl_1">ผู้ส่ง: ';
				html+='</div>';
				html+='<div class="col cl_2">วันที่คีย์: <span><?php echo date('m/d/Y')?></span> </div>';
				html+='</div>';
				html+='<div class="tracking_thai">';
				html+='<div class="col cl_1">เลขที่Trackingไทย: <span></span> </div>';
				html+='</div>';
				html+='<div>';
				html+='<div class="trackingTH" id="trackingTH">';
				html+='</div>';
				html+='</div>';
				html+='<p>หมายเหตุ</p>';
				html+='<div>';
				html+='<textarea class="remark" name="remark[]"></textarea>';
				html+='</div>';
				html+='</div>';
				html+='</div>';


				 $('#showBox').prepend(html);
				 
				  $( "#datepickerCreate_"+i ).datepicker({
					    dateFormat: "yy-mm-dd"
				  });

				  $( "#datepickerCreate_"+i ).datepicker(
						    'setDate', new Date()
				 );
					 
			});


			$('#btnBack').on('click',function(){
				window.location.href ='./index.php';
			});


	});

function clearPackageSend(param){
	 $.getJSON("./transport-do.php",{clearPackageSend:param}, function (data) {
		alert('ลบรายการสำเร็จ');
		window.location.href = './transport-detail.php?id='+'<?php echo (isset($_GET['id']))?$_GET['id']:"";?>';
	});
}

// function chkBoxRemainBk(param){
// $.getJSON("./transport-do.php",{chkBoxRemain:param,id :'<?php /*echo (isset($_GET['id']))?$_GET['id']:"";*/?>'//}, function (data) {
// 		if(data=="n"){
// 			alert('จำนวนกล่องไม่ถูกต้อง');
// 		}else{
// 			if(data!=null){
// 				var html='';
// 				$('#trackingTH').empty();
// 				var i=0;
// 				var j=0;
// 				$.each(data,function(key,val){
// 					console.log(val.tracking_no);
// 					html+='<div id="box_trackingno_'+(i)+'"><label>'+(key+1)+'</label><input id="trackno_'+(key)+'" type="text" readonly value="'+val.tracking_no+'" name="tracking_th[]"  />';
// 					if(i!=0){
// 						html+='<i class="material-icons" onclick="clearTracking('+(j)+')" title="clear">clear</i>';
// 					}
					
// 					html+='</div>';
// 					i++;
// 					j++;
// 				});

// 				$('#trackingTH').append(html);
// 			}
// 		}
// 	 });
// }

function chkBoxRemain(param){
	//alert(param);
	var totalCount=$('#totalCount').text();
	var totalCountSend=$('#totalCountSend').text();
	if(param<=(totalCount - totalCountSend)){
		var html='';
		for(i=0;i<Number(param);++i){
			html+='<div id="box_trackingno_'+(i)+'"><label>'+(i+1)+'</label><input id="trackno_'+(i)+'" type="text" value="" name="tracking_th[]"  />';
			//if(i!=0){
				//alert(i);
				html+='<i class="material-icons" onclick="clearTracking('+(i)+')" title="clear">clear</i>';
			//}
			html+='</div>';

		}
		$('#trackingTH').empty();
		$('#trackingTH').append(html);
	}else{
		alert('จำนวนกล่องที่ส่ง มากกว่า กล่องที่ยังไม่ได้ส่ง');
	}
	
	
	
}


function clearTracking(index){
	var totalCount=$('#totalNotSend span').text();
	
	--totalCount;
	//alert(totalCount);
	console.log(totalCount);
	
	//$('#total_box_'+index).val(totalCount);
	var o=$('#box_trackingno_'+index);
	o.hide( "100", function() {
		$(this).remove();
	});
	//o.remove();
}



function btnOk(){
	//alert('');
	var trackingNo=$('#trackno_1').val();
	if(typeof $('input[name="tracking_th[]"]').val() !== "undefined"){
		$('input[name="tracking_th[]"]').each(function(key,val) {
	         //console.log($(this).val());
	        console.log(val.value);
	     });
		 alert("บันทึกรายการสำเร็จ");
	     return true;
	}else{
		alert("กรุณาเลือก Tracking");
		return false;
	}
	 
	
}



</script>

</head>

<body>



<h1>
		<a href="./index.php">รายละเอียดหน้าจัดส่ง</a>
</h1>
	<h3>
		<a href="index.php">&larr; Back</a>&nbsp;<a href="../index.php">&larr;
			Home</a>
	</h3>
	<br />
<header></header>
<div class="contain sc_1">
	<div class="detail">
		<div class="box">
			<div class="col cl_1" id="packageno">เลขที่กล่อง: <span>B00005</span></div>
			<div class="col" id="orderno">เลขที่ Order: </div>
		</div>
		<div class="box">
			<div class="col cl_1" id="customer_firstname">ชื่อลูกค้า: <span>PAO</span></div>
			<div class="col cl_2" id="total">ยอดค่าขนส่ง: <span>20.00</span></div>
		</div>
		<div class="box">
			<div class="col cl_1"id="customer_code">ID ลูกค้า: <span>N001</span></div>
			<div class="col cl_2" id="status">สถานะ: <span></span></div>
			<div class="col cl_3"id="paydate">วันที่ชำระ: <span><?php echo date("Y-m-d H:i:s", time());?></span></div>
		</div>
		<div class="box">
			<div class="col cl_1" id="transport_th_name">วิธีส่ง: <span>KERRY</span></div>
			<div class="col cl_2" id="total_count">จำนวนกล่อง: <span id="totalCount">3</span></div>
			<div class="col cl_3" id="total_count_sent">ส่งแล้ว: <span id="totalCountSend">1</span></div>
			<div class="col cl_4" id="totalNotSend">ยังไม่ได้ส่ง: <span>2000</span></div>
		</div>
		</div>
	</div>
</div>

	<div class="contain sc_2">
 	<form action="./transport-do.php" method="post" onsubmit="return btnOk();"> 
		<div class="detail">
		<input type="hidden" name="insertDeail" id="insertDeail" value="1"/>
		<input type="hidden" name="packageid" id="packageid" value=""/>
		<input type="hidden" name="send_user" id="send_user" value=""/>
			<div id="showBox"></div>
		</div>
		<div class="detail">
			<div class="box">
				<button class="btn" id="btnAdd" onclick="return false;">คีย์รายการที่ส่ง</button>
				
			</div>
		</div>
		<div class="detail">
			<div class="box_dt">
				<button class="btn_submit gr" id="btn_submit">ตกลง</button>
				<button id="btnBack" class="btn_submit pk" onclick="return false;">ย้อนกลับ</button>
			</div>
		</div>
		</form> 
	</div>

</body>
</html>