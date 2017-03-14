
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

#frmHeader input, #frmHeader select{
	width: 90%;
}

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

#address,#customer_order_shipping {
    display: block;
    margin-left: 20px;
    min-height: 25px;
    min-width: 91% !important;
    text-align: left;
    width: 92% !important;
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
body{
	padding-bottom: 100px;
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
    margin-right: 0;
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
}?>

<script>
var addressBoxOn1=false;
function addressBox1(data,op){
	console.log('Data Address:'+data);
	
	
	addressBoxOn1 = !addressBoxOn1;
	
	
	if(addressBoxOn1){
		if(op==1){
			
			var html=data[0].line_1+" "+data[0].city+" "+data[0].country+" "+data[0].zipcode+" Tel."+data[0].phone;

			$(window).load(function() {
				$('#address').attr('addressId',data[0].address_id);
				//$('#address').text(v.line_1+" "+v.city+" "+v.country+" "+v.zipcode+" Tel."+v.phone);
				$('#addressid').attr('value',data[0].address_id);
				$('#address').text(html);
			    $('#address').attr('value',html);

			   
			   
			});
			
		}
		if(op==0){
			
			$(window).load(function() {
				
				document.getElementById('addressBox1').style.visibility = 'visible';	
			});
			var html='';
			var j=1;
			
			$.each(data,function(k,v){
				
				//alert(v.city);
				//document.getElementById('addressBox1').style.visibility = 'visible';
		
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
			html+='<button onclick="backAddressBox1()" class="order-button back-button">กลับ</button></td>';
			$(window).load(function() {
				$('#addressBox1 > table > tbody').empty();
				$('#addressBox1 > table > tbody').append(html);
			});
			
		}
	}
		
}

//var addressBoxOn=false;
function addressBox(data){	
	var addressBoxOn=false;
	addressBoxOn = !addressBoxOn;
	var size = Object.size(data);
	console.log('Data Address(addressBox):'+size);
	if(addressBoxOn){
		
		if(size==1){
			$.each(data,function(k,v){
			    $('#address').attr('addressid',v.address_id);							  
				$('#address').text(v.line_1+" "+v.city+" "+v.country+" "+v.zipcode+" Tel."+v.phone);
				$('#addressid').val($('#address').attr('addressid'));							 							
			});
		}else{

			//check order visible address box
			var orderIdArr=new Array();
			$.each($('#package_items table tbody tr'),function(key,val){
				orderIdArr.push($(this).attr('orderid'));
			});
			
			var lastOrderId=orderIdArr[orderIdArr.length-3];
			var flagAddressBox=false;
			if(orderIdArr.length>=4){				
				for( i=0;i < (orderIdArr.length)-3;i++){					
					if(orderIdArr[i]==lastOrderId){
						flagAddressBox=true;						
					}
				}
			}
 			if(!flagAddressBox){
				document.getElementById('addressBox').style.visibility = 'visible';
				flagAddressBox=false;
			}				

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
		
			    j++;
				
			});
		
			html+='<tr class="confirm">';
			html+='<td></td>';
			
			html+='<td style="padding-top: 50px;"><div style="width: 100%; left: 0; right: 0; margin-left: auto; margin-right: auto; margin-bottom: 18px; text-align: center;">';
			html+='<button onclick="confirmAddress1()" class="order-button">ตกลง</button>';
			html+='<button onclick="backAddress()" class="order-button back-button">กลับ</button></td>';
		
			$('#addressBox > table > tbody').empty();
			$('#addressBox > table > tbody').append(html);
				
		} // end chek size

	
	}else{
		document.getElementById('addressBox').style.visibility = 'hidden';
	}	
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

var customerOrderShipping=false;
function customerOrderShippingBox(data,op){
	customerOrderShipping = !customerOrderShipping;	
	
	if(customerOrderShipping){
		console.log(data);
		
		if(op==1){
			
			var html=data[0].transport_th_name;
			//alert(html);
			//$('#address').attr('addressId',data[0].address_id);
			$(window).load(function() {
				//console.log("Data:"+data[0].transport_th_name)
				$('#customer_order_shipping').attr('addressId',data[0].order_shipping_th_option);
				
				$('#shipingid').val(data[0].order_shipping_th_option);
				$('#customer_order_shipping').text(html);
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
				html+='type="radio" value="'+v.transport_id+'" name="chkShipping" class="chkShipping"></td>';
				html+='<td>'+v.transport_th_name+'</td>';
			    html+='</tr>';
			    $(window).load(function() {				   
			    	document.getElementById('customerOrderShippingBox').style.visibility = 'visible';
			    	
			    });
			    
			    //alert(html);
			    i++;
				
			});

			html+='<tr class="confirm">';
			html+='<td></td>';
			
			html+='<td><div style="width: 100%; left: 0; right: 0; margin-left: auto; margin-right: auto; margin-bottom: 18px; text-align: center;">';
			html+='<button onclick="confirmShippingBox()" class="order-button">ตกลง</button>';
			html+='<button onclick="backShippingBox()" class="order-button back-button">กลับ</button></td>';
			$(window).load(function() {
				$('#customerOrderShippingBox > table > tbody').empty();
				$('#customerOrderShippingBox > table > tbody').append(html);
			});
		}
		
		

	}
		
}

function addressBox2(data,op){

	
	addressBoxOn1 = !addressBoxOn1;
	
	
	if(addressBoxOn1){
		if(op==1){
			
			var html=data[0].line_1+" "+data[0].city+" "+data[0].country+" "+data[0].zipcode+" Tel."+data[0].phone;

			$(window).load(function() {
				$('#address').attr('addressId',data[0].address_id);
				$('#addressid').attr('value',data[0].address_id);
				
			    //$('#address').attr('value',html);
				$('#address').text(html);
			   
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
</script>



<?php
$data=array();
$response=array();
$js_array1='';
$js_array2='';
if(isset($_POST['orderCompleate'])){

	
	$chkorder=(isset($_POST['chkorder'])?$_POST['chkorder']:"");
// 	/window.location = "http://www.yoururl.com";
	if(empty($chkorder)){
		echo '<script>window.location = "./index.php";</script>';
		exit;
	}
	
	/*$sql='select co.*,c.*,cop.*,copt.* from customer_order co 
	inner join customer c on c.customer_id = co.customer_id 
	inner join customer_order_product cop on cop.order_id=co.order_id
	inner join customer_order_product_tracking copt on  copt.order_product_id=cop.order_product_id
	where co.order_status_code=7 
	and co.order_number IN ("' . implode('", "', $chkorder) . '")
	and copt.order_product_id not in (select order_product_id from package_detail)';*/
	
	$sql='select c.*,co.*,cop.*,copt.*,copt.tracking_no as tracking_no_copt,copm.total_baht from customer_order co
inner join customer c on c.customer_id = co.customer_id 
inner join customer_order_product cop on cop.order_id=co.order_id
inner join customer_order_product_tracking copt on copt.order_product_id=cop.order_product_id
left join customer_order_paymore copm on copm.order_id =copt.order_id and  copm.order_product_id = copt.order_product_id 
where co.order_status_code=7
and copt.order_product_tracking_id not in (select order_product_tracking_id from package_detail)
and co.order_number IN ("' . implode('", "', $chkorder) . '")
and copt.masterflg=1	
and copt.statusid = 1';
//order by copt.last_edit_date desc';

	//echo $sql; //Trace1
	$stmt=$con->prepare($sql);
	$stmt->execute();
	
	//result
	$result=$stmt->get_result();
	$num_of_rows = $result->num_rows;
	
	
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$data[]=$row;
	}
	

	if(count($data)>0){
		$response['data']=$data;
		foreach($data as $val){
			$orderIdArray[]=$val['order_id'];
		}
	}
	
	
	
	//select customer adddress
	$sqlCustomerAddress='select ca.* from customer_address ca
where ca.customer_id=?';
	$stmt=$con->prepare($sqlCustomerAddress);
	$stmt->bind_param('i',$data[0]['customer_id']);
	$stmt->execute();
	$result=$stmt->get_result();
	$customerAddress=array();
	while ($row = $result->fetch_assoc()) {
		$customerAddress[]=$row;
	}

	if(count($customerAddress)>0){
		$response['address']=$customerAddress;
	}
// 	echo "<pre>";
// 	print_r(json_encode($response['address']));
// 	echo "</pre>";

// 	echo '<pre>';
// 	print_r($response['address']);
// 	echo '</pre>';
	
	
	
	$customerOrderShippingArray=array();
	$sqlCustomerOrderShipping='select cop.*,wt.* from customer_order_shipping cop inner join website_transport wt ON wt.transport_id=cop.order_shipping_th_option
   where cop.order_id in ('.implode(",",$orderIdArray).')';
	
	
	//echo $sqlCustomerOrderShipping;
	if ($result = $con->query ( $sqlCustomerOrderShipping )) {
			
		while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
			$customerOrderShippingArray[] = $row;
		}
	}
	if(count($customerOrderShippingArray)>0){
		$response['shipping']=$customerOrderShippingArray;
	}
	$js_array1="";
	$js_array2="";

	if(count($response['address'])>0){
//addressBox1(data.address,0);
//addressBox(data.address);

//customerOrderShippingBox(data.shipping,0);

		$js_array1 = json_encode($response['address']);
		//echo '<script>addressBox1("'.$js_array1.'","0")</script>';
		
		$js_array2 = json_encode($response['shipping']);
	
	
		
		

	}	
	
	
}


?>

<?php if(!empty($js_array1)){?>

	<?php if(count($response['address'])>1){ ?>
		<script>
		
		addressBox1(<?php echo $js_array1;?>,'0');
		</script>
	<?php }else{?>
		<script>
		
		addressBox1(<?php echo $js_array1;?>,'1');
		</script>
	<?php }?>
<?php }?>

<?php if(!empty($js_array2)){?>
	<?php if(count($response['shipping'])>1){ ?>
		<script>				
		customerOrderShippingBox(<?php echo $js_array2;?>,'0');
		</script>
	<?php }else{?>
		<script>	
		customerOrderShippingBox(<?php echo $js_array2;?>,'1');
		</script>
	<?php }?>
<?php }?>
<script>




function removeOrderSelect_bk(){
	//$('.chkorderComplete').
	var i=$('#cBoxs').text(); //2
	var tm3=$('#tm3').text();
	var tkg=$('#tkg').text();
	var ttotal=$('#ttotal').text();
	console.log("i:"+i);
	var j=0;
	var m3=0;
	var kg=0;
	var total=0;
	$('input[name="chkorder"]:checked').each(function() {
			m3+=parseFloat($('#tracking-'+this.value+' > td[class="tdm3"]').text());
			kg+=parseFloat($('#tracking-'+this.value+' > td[class="tdkg"]').text());
			total+=parseFloat($('#tracking-'+this.value+' > td[class="tdttotal"]').text());
			$('#tracking-'+this.value).remove();
			i--;
	});
	
	$('#cBoxs').text(i);
	$('#total_ordernumber').val(i);
	
	$('#cTracking').text(i);
	$('#total_tracking').val(i);
	
	$('#tm3').text(parseFloat(tm3-m3).toFixed(4));
	$('#total_m3').val(parseFloat(tm3-m3).toFixed(4));

	
	$('#tkg').text(parseFloat(tkg-kg).toFixed(2));
	$('#total_weight').val(parseFloat(tkg-kg).toFixed(2));
	
	$('#ttotal').text(parseFloat(ttotal-total).toFixed(2));
	$('#amount').val(parseFloat(ttotal-total).toFixed(2));
	
}


var removeTrackingId=[]||{};
function removeOrderSelect(){
	
	var totalSizeChecked=$('input[name="chkorderMain"]').length;
	var sizeCurrentChecked=$('input[name="chkorderMain"]:checked').length;	

	//if(totalSizeChecked==sizeCurrentChecked){
		//alert('กล่องต้องไม่เป็นค่าว่าง');
	//}else{
		var i=$('#cBoxs').text(); //2
		var tm3=$('#tm3').text();
		var tkg=$('#tkg').text();
		var ttotal=$('#ttotal').text();		
		var m3=0;
		var kg=0;
		var total=0;
		var total_baht=0;
		var oldpayMore=$('#paymoreh').val();
		
		$('input[name="chkorderMain"]:checked').each(function() {
			//console.log($(this).attr("id"));
			m3+=parseFloat($('#tracking-'+this.value+' > td[class="tdm3"]').text());
			kg+=parseFloat($('#tracking-'+this.value+' > td[class="tdkg"]').text());
			total+=parseFloat($('#tracking-'+this.value+' > td[class="tdttotal"]').text());
			total_baht+=parseFloat($('#tracking-'+this.value+' > input.total_baht[type="hidden"]').val());
			//.myClass[type=checkbox]
			//alert(parseFloat(total_baht));
			$('#paymore').text(formatNumber((parseFloat(oldpayMore)-total_baht))*-1);
			$('#paymoreh').val((parseFloat(oldpayMore)-total_baht));
			
			$('#tracking-'+this.value).remove();			
			if(i==1){
				console.log("index :"+i);
				$('#package_items').find('table > tbody > tr').remove();
				$('#customerId').val('');			
				$('#address,#customer_order_shipping').text('');								
				$('#orderValidate table > tbody tr').remove();
				$('#btnAddTracking').attr('onclick','orderComplete()');
				//btnAddTracking
			}
			i--;
		});
		removeTrackingId=[]||{};
		var index=1;
		$('input[name="chkorderMain"]:not(:checked)').each(function() {
			
			removeTrackingId.push(this.value);
			/**
			1.order number in table 
			
			*/
			$('#tracking-'+this.value).find("td:first").text(index);
			index++;
		});

		$('#cBoxs').text(i);
		$('#total_ordernumber').val(i);
		
		$('#cTracking').text(i);
		$('#total_tracking').val(i);
		
		$('#tm3').text(parseFloat(tm3-m3).toFixed(4));
		$('#total_m3').val(parseFloat(tm3-m3).toFixed(4));

		$('#tkg').text(parseFloat(tkg-kg).toFixed(2));
		$('#total_weight').val(parseFloat(tkg-kg).toFixed(2));
		$('#ttotal').text(formatNumber((formatNumberC(ttotal)-formatNumberC(parseFloat(total).toFixed(2)))));
		$('#amount').val(parseFloat(ttotal-total).toFixed(2));
	//} //end else
	
	
	
}

function on_search_tracking(param,trackingid){
	$.getJSON("./package-do.php",{ searchTracking :$('#searchOrderNumber,#searchTrackingNo').serialize(),id:param,trackingRemove:trackingid }, function (data) {
		console.log(data);
		//$('#packagestatusid > b').text(data.packagestatusname);
		//console.log(data.length);
			var html='';
				   //console.log(data.name);
				   var i=0;
				   var i=0;
				   $.each(data,function(k,v){
						//if(v.order_status_code==='7'){
							html+='<tr id="'+v.order_tracking_id+'" style="'+((v.statusid=='0')?'color:#f00':'')+'" class='+((i % 2 == 0) ? "punc" : "") + '>';	
							html+='<td id="'+v.order_number+'">'+v.order_number+'</td>';
							html+='<td id="'+v.order_number+'">'+v.tracking_no_copt+'</td>';4
							html+='<td id="'+v.order_number+'">'+v.last_edit_date+'</td>';
							html+='<td class="tdsm3">'+v.m3+'</td>';
							html+='<td id="'+v.order_number+'">'+v.weight+'</td>';
							html+='<td id="'+v.order_number+'">'+v.rate+'</td>';
							html+='<td id="'+v.order_number+'">'+((v.type==1)?'M3':'Kg')+'</td>';
							html+='<td id="'+v.order_number+'">'+v.total+'</td>';
							if(v.m3==0 || v.weight==0){
								html+='<td id="'+v.order_number+'">0.00</td>';
							}else{
								html+='<td id="'+v.order_number+'">'+((v.type==1)?((parseFloat(v.total)/parseFloat(v.weight))).toFixed(2):(parseFloat(v.total)/parseFloat(v.m3)).toFixed(2))+'</td>';
							}
							
							
							if(v.statustrackingID==0){
								html+='<td></td>';
							}else{
								html+='<td><input type="checkbox" class="chkorderComplete" name="chkorder" value="'+v.order_product_tracking_id+'"  ></td>';
							}
							
							html+='</tr>';
						//}
						i++;
					});
				   	$('.detail-order-complete > tbody').empty();
					$('.detail-order-complete > tbody').append(html);
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

	$('#btnConfirm').on('click',function(){
		var confirmVal=$(this).attr('confirm');
		alert(confirmVal);
	});

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

	//select
	
	$("#customerId").bind('input', function () {
   		//window.checkModelData(this);
	});

	$('#customerId').bind('focusout',function(){
		var inputVal=$(this).val();
		
		//console.log(inputVal);
		window.checkModelData(this);
	});
	var oldCustomerId=0;
	window.checkModelData = function(params)
	{
		
	   //alert('');
	    var customerId=0;
	    var x = $(params).val();	   
	    var z = $('#chkCustomerName');
	  	var val = $(z).find('option[value="' + x + '"]');
	  	if(val.attr('cusid') !=undefined){
	  		var endval = val.attr('cusid').split('-')[1];
	  		console.log(x);
			customerId=endval;
			//onclick="btnOrderAdd();"
			//alert(customerId);
			$('#btnOrderAdd').attr('onclick','btnOrderAdd('+customerId+')');
			$('#btnAddTracking').attr('onclick','orderComplete('+customerId+')');
			$('#cutomerIdHiden').val(customerId);
			
			$.getJSON("./package-do.php",{ getOrderByCustomerId :customerId}, function (data) {				
				
				if(data!='no'){
					console.log('old customerId:'+oldCustomerId+"->new customer"+customerId);
					
					if(oldCustomerId!=0 && oldCustomerId != customerId){
						alert('ท่านเปลี่ยนลูกค้า รายการที่เลือกมาจะหายหมด');
						$('#package_items > table > tbody').empty();
					}else if(oldCustomerId == customerId){
						$('#package_items > table > tbody').empty();
					}
					oldCustomerId=customerId;
					var i=1;
					var m3=0;
					var weight=0;
					var rate=0;
					var total=0;
					$.each(data.data,function(key,val){
						console.log(val.customer_firstname);
						$('#customerName > b').text(val.customer_firstname);
						//addHTMLToPackageItemsCustom(i,val);
						m3+=(parseFloat(val.m3));
						weight+=(parseFloat(val.weight));
						rate+=(parseFloat(val.rate));
						total+=(parseFloat(val.total));
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
					html+='<td colspan="" id="cBoxs">'+(i-1)+'</td><input type="hidden" id="total_ordernumber" name="total_ordernumber" value="'+(i-1)+'"/>';
					html+='<td></td>';
					html+='<td colspan="" id="cTracking">'+(i-1)+'</td><input type="hidden" id="total_tracking" name="total_tracking" value="'+(i-1)+'"/>';
					html+='<td></td>';	
					html+='<td colspan="" id="tm3">'+parseFloat(m3).toFixed(4)+'</td><input type="hidden" id="total_m3" name="total_m3" value="'+parseFloat(m3).toFixed(4)+'"/>';
					html+='<td id="tkg">'+parseFloat(weight).toFixed(2)+'</td><input type="hidden" id="total_weight" name="total_weight" value="'+parseFloat(weight).toFixed(4)+'"/>';
					html+='<td></td>';
					html+='<td>ยอดรวม</td>';	
					html+='<td>'+parseFloat(total).toFixed(2)+'</td><input type="hidden" id="amount" name="amount"  value="'+parseFloat(total).toFixed(2)+'"/>';
					html+='<td>บาท</td>';
					html+='</tr>';
					$('#btnAddTracking').attr('onclick','orderComplete('+customerId+')');
					
				}else{
					$('#package_items > table > tbody').empty();
					alert('ไม่มีรายการ');
				}
			});	
	  	}else{
			/*else not found user in list*/
			var lengthOfParam=x;	
			if(lengthOfParam.length >=1){
				alert('ไม่มีลูกค้ารายนี้');
				$('#package_items > table > tbody').empty();
				$('#customerId').val('');
				$('#btnAddTracking').attr('onclick','orderComplete()');
			}
	  	}

	}


	

});



function printAddress(){
	var myWindow = window.open("", "MsgWindow", "width=200,height=100");
	myWindow.document.write("<p>This is 'MsgWindow'. I am 200px wide and 100px tall!</p>");
}

var orderCompleteOn=false;
function orderComplete(param){

	orderCompleteOn = !orderCompleteOn;
	//call data with ajax
	var objParam='<?php  echo json_encode((isset($_SESSION['addOrder']))? $_SESSION['addOrder']:''); ?>';
	searchOrderCompleteJSON(param);
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
	//location.reload();
	$('#orderValidate table > tbody tr').remove();
}

function on_remove_detail_button_click(){

// 	$items=[];
// 	$('input[name="chkorder"]:checked').each(function() {
// 		   //console.log(this.value); 
// 		   $items.push(this.value);
// 	});

// 	 $.getJSON("./package-do.php",{ removeItemOrder : $items},function(data){
// 		if(data.success){
// 			location.reload();
// 		}
// 	});
	$('#action').val('deleteItems');//actionPackage
	
	$.getJSON("./package-do.php",{ actionPackage : $('#frmActionPackage').serialize() }, function (data) {
		//console.log(data.packagestatusname);
		//$('#packagestatusid > b').text(data.packagestatusname);
		location.reload();
	});
}

function save(customerId){
	// 1 . check address customer 
	
	$.getJSON("./package-do.php",{ saveDetail : $('#frmHeader,#frmAmount,#frmActionPackage').serialize()},function(data){
		if(data.status=='Y'){
			//$('#btnConfirm').attr('confirm',data.packageno);
			alert('บันทึกรายการสำเร็จ');
			window.location.href = './detail-edit.php?id='+data.packageId;
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
}

function saveCustom(customerId){
	var totalSizeChecked=$('input[name="chkorderMain"]').length;
	var sizeCurrentChecked=$('input[name="chkorderMain"]:checked').length;
	if(totalSizeChecked==sizeCurrentChecked){
		alert('กล่องต้องไม่เป็นค่าว่าง');
	}else{
		$.getJSON("./package-do.php",{ saveDetail : $('#frmHeader,#frmAmount,#frmActionPackage').serialize()},function(data){
			if(data.status=='Y'){
				//$('#btnConfirm').attr('confirm',data.packageno);
				alert('บันทึกรายการสำเร็จ');
				window.location.href = './detail-edit.php?id='+data.packageId;
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
	}
	
	
}


function confirmAddress(){
	$('input[name="chkAddress"]:checked').each(function() {
		   $('#address').attr('addressId',this.value);
		   $obj=$(this).parent().parent().find('td:nth-child(2)');
		   //$('#address').attr('value',$obj.text());
		   $('#address').text($obj.text());
		   $('#addressid').val(this.value);
		   document.getElementById('addressBox1').style.visibility = 'hidden';   
	});
}

function confirmAddress1(){
	$('input[name="chkAddress"]:checked').each(function() {
		   $('#address').attr('addressId',this.value);
		   $obj=$(this).parent().parent().find('td:nth-child(2)');
		   //$('#address').attr('value',$obj.text());
		   $('#address').text($obj.text());
		   $('#addressid').val(this.value);
		   document.getElementById('addressBox').style.visibility = 'hidden';   
	});
}

function confirmShippingBox(){
	$('input[name="chkShipping"]:checked').each(function() {
// 		   /console.log(this.value); 
		   $('#customer_order_shipping').attr('customer_order_shipping',this.value);
		   $obj=$(this).parent().parent().find('td:nth-child(2)');
		   //console.log($obj.text());
		   //$('#customer_order_shipping').attr('value',$obj.text());
// 		   $('#customer_order_shipping').attr('value',$obj.text());
			$('#customer_order_shipping').text($obj.text());
		   $('#shipingid').val($('#customer_order_shipping').attr('customer_order_shipping'));
		   document.getElementById('customerOrderShippingBox').style.visibility = 'hidden';
		   
	});

}






function searchOrderCompleteJSON(param){
	console.log(removeTrackingId);
	var removeTrackingId=[]||{};
	if(removeTrackingId.length <=0){
		
		$('input[name="chkorderMain"]:not(:checked)').each(function() {
			
			removeTrackingId.push(this.value);
			console.log(this.value);
		});
	}
	
	var html='';
	$.getJSON("./package-do.php",{ addOrderComplete : '1', id:param,trackingRemove:removeTrackingId }, function (data) {
		   //console.log(data.name);
		   var i=0;
		   
		   if(data=="n"){
			   //alert('');
			  $('.detail-order-complete > tbody > tr').remove();
			   
			  // alert('กรุณาเลือกลูกค้าจากหน้าหลักก่อน');
			  // document.getElementById('orderComplete').style.visibility = 'hidden';
				
				
		   }else{
			   $.each(data,function(k,v){
					//if(v.order_status_code==='7'){
						html+='<tr order_product_tracking_id="'+v.order_product_tracking_id+'" style="'+((v.statusid=='0')?'color:#f00':'')+'" class='+((i % 2 == 0) ? "punc" : "") + '>';	
						html+='<td id="'+v.order_number+'">'+v.order_number+'</td>';
						html+='<td id="'+v.order_number+'">'+v.tracking_no_copt+'</td>';4
						html+='<td id="'+v.last_edit_date+'">'+v.last_edit_date+'</td>';
						html+='<td class="tdsm3">'+v.m3+'</td>';
						html+='<td id="'+v.order_number+'">'+v.weight+'</td>';
						html+='<td id="'+v.order_number+'">'+v.rate+'</td>';
						html+='<td id="'+v.order_number+'">'+((v.type==1)?'Kg':'M3')+'</td>';
						html+='<td id="'+v.order_number+'">'+v.total+'</td>';
						if(v.m3==0 || v.weight==0){
							html+='<td id="'+v.order_number+'">0.00</td>';
						}else{
							html+='<td id="'+v.order_number+'">'+((v.type==1)?((parseFloat(v.total)/parseFloat(v.weight))).toFixed(2):(parseFloat(v.total)/parseFloat(v.m3)).toFixed(2))+'</td>';
						}
						
						
						if(v.statustrackingID==0){
							html+='<td></td>';
						}else{
							html+='<td><input type="checkbox" class="chkorderComplete" name="chkorder" value="'+v.order_product_tracking_id+'"  ></td>';
						}
						
						html+='</tr>';
					//}
					i++;
				});
			   	$('.detail-order-complete > tbody').empty();
				$('.detail-order-complete > tbody').append(html);
				console.log(html);
				
				 
		   }
		   document.getElementById('orderComplete').style.visibility = 'visible';

		   if ($('#orderComplete').css("visibility") == "visible") {			  		
				$('#onSearchTracking').attr('onclick','on_search_tracking('+param+',"'+removeTrackingId+'");');
		   }
	});
}

var totalBaht=0;
var totalBahtH=0;
function btnOrderAdd(id){
	var shipingid=$('#shipingid').val();
	$orderCheckList=$('#orderValidate').serialize();
	$paramAmp=$orderCheckList.split('&');
	$paramArray=[];
	if($paramAmp.length>0){
		for(i=0;i<$paramAmp.length;++i){
			$paramArray[i]=$paramAmp[i].split('=')[1];
		}
	}

	if($orderCheckList.length !=0){

		
		var arrayTrackingId=[]||{};
		$('input[name="chkorder"]:checked').each(function() {

			arrayTrackingId.push(this.value);
		});

		
		
		$.getJSON("./package-do.php",{ addTrackingId: arrayTrackingId}, function (data) {
			
			var html='';
			var i=0;
			var order_shipping_id=[]||{};
			var orderNumberArray=[]||{};
			 $.each(data,function(k,v){
				 
				 		removeTrackingId.push(v.order_product_tracking_id);

						/*
							1. check order_number new to old 
							if(not same order_number alert('shipping and address'))
								
						*/
						//alert(v.order_number);
						//alert(((v.total_baht)?v.total_baht:0));
				 		
						html+='<tr id="tracking-'+v.order_product_tracking_id+'" orderID='+v.order_id+'>';	
						html+='<td id="">'+(i+1)+'</td>';
						//html+='';
						html+='<td>'+v.order_number+'</td><input type="hidden" value="'+v.order_id+'" name="orderId[]"/>';
						html+='<td>'+v.tracking_no_copt+'</td><input type="hidden" value="'+v.order_product_tracking_id+'" name="order_product_tracking_id[]"/><input type="hidden" value="'+v.tracking_no+'" name="tracking_no[]"/><input type="hidden" value="'+v.product_id+'" name="product_id[]"/><input type="hidden" value="'+v.order_product_id+'" name="order_product_id[]"/><input class="total_baht" type="hidden" value="'+((v.total_baht)?v.total_baht:0)+'" name="total_baht[]"/>';
						html+='<td>'+v.last_edit_date+'</td>';
						html+='<td class="tdm3">'+v.m3+'</td>';
						html+='<td class="tdkg">'+v.weight+'</td>';
						html+='<td>'+v.rate+'</td>';
						html+='<td >'+((v.type==1)?"Kg":"M3")+'</td>';
						html+='<td class="tdttotal">'+v.total+'</td>';
						
						if(v.statustrackingID==0){
							html+='<td></td>';
						}else{
							html+='<td><input type="checkbox" class="chkorderComplete" name="chkorderMain" value="'+v.order_product_tracking_id+'"  ></td>';
						}
						
						html+='</tr>';
						order_shipping_id.push(v.order_shipping_id);
					//}
					i++;
					
					
					var nulls=0;
					if(v.total_baht){						
						totalBaht+=parseFloat(v.total_baht);
						totalBahtH+=Number(v.total_baht)					
					}
					
				});

			 $('#paymore').text(formatNumber(totalBaht*-1));
			 $('#paymoreh').val(totalBahtH*-1);		 

			var flag=false;
			console.log("order_shipingID"+order_shipping_id);
// 			if(order_shipping_id.length>1){
// 				customerOrderShippingBox(data,0);
// 			}
			
			var rowCount = $('.order-product > tbody > tr').length;	
			console.log('rowCount:'+rowCount);

			 if(id !=undefined){
				 
				 var html2='<tr style="background-color:#c4bd97;border: 1px solid #a19e8d;">';
				 html2+='<td colspan="">จำนวนกล่อง</td>';
				 html2+='<td></td>';
				 html2+='<td colspan="">จำนวนTracking</td>';
				 html2+='<td></td>';	
				 html2+='<td colspan="">คิว</td>';
				 html2+='<td>Kg</td>';
				 html2+='<td></td>';
				 html2+='<td></td>';	
				 html2+='<td></td>';
				 html2+='<td></td>';
				 html2+='</tr>';

				 html2+='<tr style="background-color:#c4bd97;border: 1px solid #a19e8d;">';
				 html2+='<td colspan="" id="cBoxs">0</td><input type="hidden" id="total_ordernumber" name="total_ordernumber" value="0"/>';
				 html2+='<td></td>';
				 html2+='<td colspan="" id="cTracking">0</td><input type="hidden" id="total_tracking" name="total_tracking" value="0"/>';
				 html2+='<td></td>';	
				 html2+='<td colspan="" id="tm3">0</td><input type="hidden" id="total_m3" name="total_m3" value="0"/>';
				 html2+='<td id="tkg">0</td><input type="hidden" id="total_weight" name="total_weight" value="0"/>';
				 html2+='<td></td>';
				 html2+='<td>ยอดรวม</td>';	
				 html2+='<td id="ttotal">0</td><input type="hidden" id="amount" name="amount"  value="0"/>';
				 html2+='<td>บาท</td>';
				 html2+='</tr>';
				$('#package_items > table > tbody').append(html2);
				$('#btnOrderAdd').attr('onclick','btnOrderAdd()');
			}
			if(rowCount==1){
				$('.order-product > tbody > tr').eq(0).after(html);
			}else if(rowCount==0){
				$('.order-product > tbody > tr').eq(0).before(html);
			}else{
				$('.order-product > tbody > tr').eq(rowCount-3).after(html);
			}	
				
			
			var cBox=Number($('#cBoxs').text())+i;
			
			$('#cBoxs').text(cBox);
			$('#cTracking').text(cBox);
			$('#total_ordernumber').val(cBox);
			$('#total_tracking').val(cBox);


			var tm3=$('#tm3').text();
			var tkg=$('#tkg').text();
			var ttotal=$('#ttotal').text();
			var j=0;
			var m3=0;
			var kg=0;
			var total=0;
			$('input[name="chkorderMain"]').each(function() {
					m3+=parseFloat($('#tracking-'+this.value+' > td[class="tdm3"]').text());
					kg+=parseFloat($('#tracking-'+this.value+' > td[class="tdkg"]').text());
					total+=parseFloat($('#tracking-'+this.value+' > td[class="tdttotal"]').text());
					//$('#tracking-'+this.value).remove();
					//i--;
			});
			
			$('#tm3').text(parseFloat(m3).toFixed(4));
			$('#total_m3').val(parseFloat(m3).toFixed(4));

			$('#tkg').text(parseFloat(kg).toFixed(2));
			$('#total_weight').val(parseFloat(kg).toFixed(2));

			//total=number_format(total,2);
			//$('#ttotal').text(parseFloat(total).toFixed(2));
			//console.log('total: '+total.toLocaleString('en'));
			
			$('#ttotal').text(formatNumber(total));
			$('#amount').val(parseFloat(total).toFixed(2));
			
			//new tracking-xxxx
			var rowCountNew = $('.order-product > tbody > tr').length-2;
			for(var i=0;i<rowCountNew;++i){
				$('.order-product > tbody > tr').eq(i).find("td:first").text((i+1));
			}
			//End new tracking-xxx
			//orderComplete
			$('#orderComplete').css({'visibility':'hidden'});

//check address and shipping popup 1

			var lengthOrderNumber=$('#package_items tbody tr').length;
			//alert(lengthOrderNumber)
			
			var orderNumberUnique=[]||{};
			for(var i=1;i<=lengthOrderNumber-2;i++){
				var orderNumber=$('#package_items tbody tr:nth-child('+i+') td:nth-child(2)').text();
				if(orderNumberUnique.length==0){
					orderNumberUnique.push(orderNumber);
				}else{
					var flagOrderNumberUnique=false;
					for(var j=0;j<orderNumberUnique.length;++j){
						if(orderNumberUnique[j]==orderNumber){
							flagOrderNumberUnique=true;
						}
					}
					if(!flagOrderNumberUnique){
						orderNumberUnique.push(orderNumber);
					}
				}
				
			}


			
			console.log(orderNumberUnique);
			if(orderNumberUnique.length>0){
				//
				$.getJSON('./package-do.php',{getAddShipping:orderNumberUnique},function(data){
					
					addressBox(data);
					
					
				});
				$.getJSON('./package-do.php',{getHowShipping:orderNumberUnique},function(data){
					customerOrderShippingBox2(data,0);
					
				});
				//customerOrderShippingBox2(data,0);
			}

			

//check address and shipping popup end 

		});
	}else{
		alert('กรุณาเลือกรายการ');
	}
	
	
}

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

function formatNumberC(param){
	
	return param.replace(/[^\d\.\-\ ]/g, '');
}

function addHTMLToPackageItems(i,val){
	//console.log(val.order_product_tracking_id);
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

function addHTMLToPackageItemsCustom(i,val){
	//console.log(val.order_product_tracking_id);
	
	var html='<tr id="tracking-'+val.order_product_tracking_id+'" style="'+((val.statusid=='0')?'color:#f00':'')+'" class="'+((i % 2==0)? 'punc' : '')+'">';
	//console.log(i);
	html+='<td>'+(i)+'</td>';
	html+='<td>'+val.order_number+'</td>';
	html+='<td>'+val.tracking_no+'</td>';
	html+='<td>'+'<?php echo date('d/m/Y');?>'+'</td>';
	html+='<td class="tdm3">'+val.m3+'</td>';
	html+='<td class="tdkg">'+val.weight+'</td>';
	html+='<td>'+val.rate+'</td>';
	html+='<td>'+val.type+'</td>';
	html+='<td class="tdttotal">'+val.total+'</td>';
	html+='<td><input type="checkbox" class="chkorderComplete" name="chkorder" value="'+val.order_product_tracking_id+'"  ></td>';
	html+='<input type="hidden" name="orderProductId[]" value="'+val.order_product_id+'"/>';
	html+='</tr>';

 	$('#package_items > table > tbody').append(html);

	
}






function customerOrderShippingBox2(data,op){
	var customerOrderShipping=false;
	
	customerOrderShipping = !customerOrderShipping;
	var size = Object.size(data);
	console.log('op:'+op+'->size object:'+size);

	//if(size==1){
		
	//}else{
			if(customerOrderShipping){
				//console.log(data);
				if(op==1){
					
					var html=data[0].order_shipping_th_option;
					$( document ).ready(function() {
						$('#customer_order_shipping').attr('addressId',data[0].order_shipping_id);
						
						$('#shipingid').val(data[0].order_shipping_id);
						
					    $('#customer_order_shipping').attr('value',html);
					});
					
				}else{
						
					
					var html='';
					var i=1;
					
					if(size==1){
						$.each(data,function(k,v){
						    $('#customer_order_shipping').attr('customer_order_shipping',v.transport_id);	
						    				  
							$('#customer_order_shipping').text(v.transport_th_name);
							$('#shipingid').val($('#customer_order_shipping').attr('customer_order_shipping'));							 							
						});
					}else{
						$.each(data,function(k,v){
							
							html+='<tr>';
							html+='<td>';
							if(i==1){
								html+='<input checked="checked" ';
							}else{
								html+='<input ';
							}
							html+='type="radio" value="'+v.transport_id+'" name="chkShipping" class="chkShipping"></td>';
							html+='<td>'+v.transport_th_name+'</td>';
						    html+='</tr>';

						  //check order visible address box
			  				var orderIdArr=new Array();
			  				$.each($('#package_items table tbody tr'),function(key,val){
			  					orderIdArr.push($(this).attr('orderid'));
			  				});
			  				
			  				var lastOrderId=orderIdArr[orderIdArr.length-3];
			  				var flagAddressBox=false;
			  				if(orderIdArr.length>=4){				
			  					for( i=0;i < (orderIdArr.length)-3;i++){					
			  						if(orderIdArr[i]==lastOrderId){
			  							flagAddressBox=true;						
			  						}
			  					}
			  				}
			  	 			if(!flagAddressBox){
			  	 				document.getElementById('customerOrderShippingBox').style.visibility = 'visible';
			  					flagAddressBox=false;
			  				}
						    //document.getElementById('customerOrderShippingBox').style.visibility = 'visible';
						    
						    //alert(html);
						    i++;
							
						});
			
						html+='<tr class="confirm">';
						html+='<td></td>';
						
						html+='<td style="padding-top: 50px;"><div style="width: 100%; left: 0; right: 0; margin-left: auto; margin-right: auto; margin-bottom: 18px; text-align: center;">';
						html+='<button onclick="confirmShippingBox()" class="order-button">ตกลง</button>';
						html+='<button onclick="back()" class="order-button back-button">กลับ</button></td>';
			
						$('#customerOrderShippingBox > table > tbody').empty();
						$('#customerOrderShippingBox > table > tbody').append(html);
					}
					
					
				}
				
				
		
			}
	//}	//end check size of object
		
}

function backToIndex(){
	window.location.href = './index.php';
}

function backAddressBox1(){
	$('#addressBox1').css('visibility','hidden');
}

function back(){
	$('#customerOrderShippingBox').css('visibility','hidden');
}

function backAddress(){
	$('#addressBox').css('visibility','hidden');
}





</script>

</head>

<!-- <body onload="on_page_ready();"> -->
<body>

<div class="wrap" id="addressBox1" style="z-index: 100;" >
			<table style="margin-top:10%">
				<thead>
					<th colspan="2" style="text-align: left; background-color: #0070c0;color:#ffffff;">รายการสินค้าที่ลูกค้าเลือกมีที่อยู่ไม่ตรงกัน กรุณาระบุที่อยู่จัดส่ง</th>
				</thead>
				<tbody>
					
				</tbody>

			</table>
				

	</div>
	
	<div class="wrap" id="customerOrderShippingBox" style="z-index: 100;" >
			<table style="margin-top:10%">
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



if ($result = $con->query ( 'select packageno from package  order by packageno desc limit 1 ' )) {
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

<?php } //end session
} else {
unset($_SESSION['customerId']);
//clear session 
 } ?>
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
				<td width="10%" style="padding: 2px">เลขที่ กล่อง :</td>
				<td width="20%" style="padding: 2px"><label id="package_number"><b>-</b></label></td>
				<td style="padding: 2px; width: 10%;">วันที่สร้าง :</td>
				<td style="padding: 2px"><input id="datepickerCreate"
					class="china datepicker" style="padding: 2px;" value="" name="datepickerCreate" /></td>
			</tr>

			<tr>
				<td width="" style="padding: 2px">สถานะกล่อง :</td>
				<td width="20%" style="padding: 2px"><label id="packagestatusid"><b>-</b></label></td>
				<td style="padding: 2px">ที่อยู่ :</td>
				<td style="padding: 2px;">
				<label id="address"></label>
				<!-- <input id="address" class="china" name="address" style="padding: 2px;" value="" readonly /> -->
					<input id="addressid" type="hidden" name="addressid" value="" />
					</td>
			</tr>
			<tr>
				<td width="" style="padding: 2px">ลูกค้า :</td>
				<?php if(empty($_POST['orderCompleate'])){
					$sqlCustomer='select c.customer_id,customer_firstname,customer_lastname,customer_code from customer c';

					$dataCustomer=array();
					if ($result = $con->query ( $sqlCustomer )) {
						while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
							$dataCustomer [] = $row;
					
						}
					}
					echo "<pre>";
					//print_r($data);
					echo "</pre>";
					$htmlData='';
					if(count($dataCustomer)>0){
						foreach($dataCustomer as $val){						
							//$htmlData.='<option value="'.$val['customer_id'].'">'.$val['customer_firstname'].'</option>';
							$htmlData.='<option cusId="cus-'.$val['customer_id'].'" value="'.$val['customer_firstname'].' '.$val['customer_lastname'].' ('.$val['customer_code'].')"/>';
						}
					}
					
					echo '<td width="35%" style="padding: 2px"><input list="chkCustomerName" id="customerId" name="customerId"><datalist  id="chkCustomerName">'.$htmlData.'					
	
</datalist><input type="hidden" id="cutomerIdHiden" name="cutomerIdHiden" /></td>';
				}else{?>
					<td width="20%" style="padding: 2px"><label id="customerName"><b><?php echo (count($data)>0)?$data[0]['customer_firstname'].'  '.$data[0]['customer_lastname'].' ('.$data[0]['customer_code'].')':'-';?></b></label></td><input type="hidden" value="<?php echo $data[0]['customer_id']; ?>" name="cutomerIdHiden"/>
				<?php }?>
				<td style="padding: 2px">วิธีส่ง :</td>
				<td style="padding: 2px">
				<label id="customer_order_shipping"></label>
				<!-- <input id="customer_order_shipping" class="china" name="customer_order_shipping" style="padding: 2px;" value="" readonly /> -->
					<input id="shipingid" type="hidden" name="shipingid" value="" />
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
					<th id="hM3">M3</th>
					<th id="hKG">Kg.</th>
					<th>Rate</th>
					<th>Type</th>
					<th>ยอดค่าขนส่ง<br/>จีน-ไทย</th>		
					<th>Action</th> 
				</thead>
			<tbody>
			<?php if(count($data)>0){
				
				$htmlGrid='';
				$i=0;
				$m3=0;
				$weight=0;
				$total=0;
					foreach($data as $key=>$val){
						if($val['statusid']!=0){
							$htmlGrid.='<tr id="tracking-'.$val['order_product_tracking_id'].'" style="'.(($val['statusid']==0)?"color:#f00":"").'">';
							$htmlGrid.='<td>'.(++$i).'</td>';
							$htmlGrid.='<td>'.$val['order_number'].'</td><input type="hidden" value="'.$val['order_id'].'" name="orderId[]"/>';
							$htmlGrid.='<td>'.$val['tracking_no'].'</td><input type="hidden" value="'.$val['order_product_tracking_id'].'" name="order_product_tracking_id[]"/><input type="hidden" value="'.$val['tracking_no'].'" name="tracking_no[]"/><input type="hidden" value="'.$val['order_product_id'].'" name="order_product_id[]"/>';
							$htmlGrid.='<td>'.date('d/m/Y').'</td>';
							$htmlGrid.='<td class="tdm3">'.$val['m3'].'</td>';
							$m3+=$val['m3'];
							$htmlGrid.='<td class="tdkg">'.$val['weight'].'</td>';
							$weight+=$val['weight'];
							$htmlGrid.='<td>'.$val['rate'].'</td>';
							$total+=$val['total'];
							$htmlGrid.='<td>'.(($val['type']==1)?"M3":"Kg").'</td>';
							$htmlGrid.='<td class="tdttotal">'.$val['total'].'</td>';
							$htmlGrid.='<td><input id="'.$val['order_product_tracking_id'].'" type="checkbox" class="chkorderComplete" name="chkorderMain" value="'.$val['order_product_tracking_id'].'"  ></td>';
							$htmlGrid.='</tr>';

						}
						
					}
				$htmlGrid.='<tr class="footerBox" style="background-color:#c4bd97;border: 1px solid #a19e8d;">';
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
				$htmlGrid.='<td id="cBoxs" colspan="">'.($i).'</td><input type="hidden" name="total_ordernumber" value="'.($i).'"/>';
				$htmlGrid.='<td></td>';
				$htmlGrid.='<td id="cTracking" colspan="">'.($i).'</td><input id="total_ordernumber" type="hidden" name="total_tracking"  value="'.($i).'"/>';
				$htmlGrid.='<td></td>';
				$htmlGrid.='<td id="tm3" colspan="">'. number_format($m3, 4, '.', ' ').'</td><input type="hidden" id="total_m3" name="total_m3"  value="'.($m3).'"/>';
				$htmlGrid.='<td id="tkg">'.$weight.'</td><input id="total_weight" type="hidden" name="total_weight"  value="'.($weight).'"/>';
				$htmlGrid.='<td></td>';
				$htmlGrid.='<td>ยอดรวม</td>';
				$htmlGrid.='<td id="ttotal">'.$total.'</td><input type="hidden" name="amount"  value="'.($total).'"/>';
				$htmlGrid.='<td>บาท</td>';
				$htmlGrid.='</tr>';
				echo $htmlGrid;
			 }
			 
			?>
			
			</tbody>
			</table>
		</div>
		<div style="display: table-cell; width: 10%; vertical-align: top;">
			<div
				style="width: auto; left: 0; right: 0; margin-left: auto; margin-right: auto; text-align: center; margin: 5px;">
	
					<button class="order-button"
					id="btnAddTracking"
					style="width: 80px; height: 30px; font: 11pt tahoma;"
					onclick="orderComplete(<?php echo (isset($data[0]['customer_id'])?$data[0]['customer_id']:""); ?>)">เพิ่ม</button>
				
			</div>
			<div
				style="width: auto; left: 0; right: 0; margin-left: auto; margin-right: auto; text-align: center; margin: 5px;">
				
				<input type="hidden" name="action" value="" id="action"/>
			
					<button class="order-cancel"
						style="width: 80px; height: 30px; font: 11pt tahoma;"
						onclick="removeOrderSelect()">ลบ</button>
	
					
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
					<td colspan="2">ค่าสินค้าที่ต้องจ่ายเพิ่ม</td>
					<input type="hidden" id="paymoreh" value="<?php echo abs($totalBaht);?>" />
					<td align="right"><label id="paymore" style="display: block; width: 40% ! important; background-color: rgb(221, 221, 221); color: rgb(0, 0, 0); border: 1px solid rgb(221, 221, 221);">0.00</label></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td colspan="2">ค่าตีลังไม้(ที่จีน)</td>
					<td align="right"><input id="amount_boxchina" class="amount" type="text" name="amount_boxchina"
						style="text-align: right" placeholder="" value="0.00" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
					<td>บาท</td>
				</tr>				
				<tr class="">
					<td colspan="2">ค่ากล่อง</td>
					<td align="right"><input id="amount_boxpackage" class="amount" type="text" name="amount_boxpackage"
						style="text-align: right" placeholder="" value="0.00" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td colspan="2">ค่าฝากส่ง(ค่าส่งสินค้าจากโกดังไปบริษัทขนส่ง)</td>
					<td align="right"><input id="amount_pass" class="amount" type="text" name="amount_pass"
						style="text-align: right" placeholder="" value="0.00" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
					<td>บาท</td>
				</tr>
				<tr class="">
					<td colspan="2">ค่าขนส่งในไทย</td>
					<td align="right"><input id=amount_thirdparty class="amount" type="text" name="amount_thirdparty"
						style="text-align: right" placeholder="" value="0.00" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
					<td>บาท</td>
				</tr>

				<tr class="">
					<td>ค่าอื่นๆ</td>
					<!-- ข้อความ -->
					<td align="right"><input id="amount_other2" type="text" 
						style="text-align: right" placeholder="Other2" name="amount_other2"   /></td>
					<td align="right"><input id="other_specifiy2" type="text" class="amount" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" name="other_specifiy2"
						style="text-align: right" placeholder="Other Specifiy2"
						value="0.00" /></td>
					<td>บาท</td>
				</tr>
				
				<tr style="border: 1px solid rgb(161, 158, 141); background-color: rgb(196, 189, 151);">
					<td colspan="" style="color:#000">จำนวนกล่อง</td>
					<td colspan="" style="text-align: right;color:#000;"><input name="total_count" type="text" id="total_count" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" style="text-align: right;" placeholder="" value="0"></td>
					<td style="text-align: right; position: relative;color:#000;" colspan=""><span style="position: relative; left: -150px;">รวม</span> <span style="" id="ttval">0.00<input type="hidden" value="0.00" name="total"></span> บาท</td>

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
			placeholder="Remark" name="remark"></textarea>
	</div>
	</form>
	<br>

	<div
		style="width: 1024px; left: 0; right: 0; margin-left: auto; margin-right: auto; text-align: center;">
		<!-- <button class="order-button" onclick="printAddress()">พิมพ์ที่อยู่คนส่ง ,ลูกค้า</button> --> 
		<?php if(!isset($_POST['orderCompleate'])){?>
			<button class="order-button" onclick="saveCustom()">บันทึก</button>
		<?php }else{?>
			<button class="order-button" onclick="save()">บันทึก</button>
		<?php }?>
		
		<button class="order-button" onclick="backToIndex()">กลับ</button>
<!-- 	    <button class="order-button" id="btnConfirm" confirm="">ยืนยัน</button> -->
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
										onclick="on_search_tracking(0);">Search</button>
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
								<button id="onSearchTracking" onclick="on_search_tracking(0);" style="width:80px;height:30px;font:11pt tahoma;" class="order-button">Search</button>
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
								<th style="width: 20% ! important;">วันที่คีย์Tracking</th>
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
						<td style="padding: 10px 20px;"><a onclick="cancelBtn();">Cancel</a>&emsp;
							<input type="hidden" name="uid" value="<?php echo $_SESSION['ID'] ?>"/>
							<input type="hidden" name="addOrder" value="1"/>
							<input type="hidden" id="customerId" name="customerId" value=""/>
							<button id="btnOrderAdd" onclick="btnOrderAdd();" style="padding:10px 20px;">Add</button></td>
						</tr>
					</table>
				

	</div>
	<!-- End block order complete -->
	
	
	<div class="wrap" id="addressBox">
			<table style="margin-top:10%">
			<thead>
				<th colspan="2" style="text-align: left; background-color: #0070c0;color:#ffffff;">รายการสินค้าที่ลูกค้าเลือกมีที่อยู่ไม่ตรงกัน กรุณาระบุที่อยู่จัดส่ง</th>
			</thead>
			<tbody>
				
			</tbody>
					</table>
	</div>
</body>
</html>
