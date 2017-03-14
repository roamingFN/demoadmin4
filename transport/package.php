<!DOCTYPE html>
<html>
<head>
<title>Package</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="../css/jquery-ui.css">

<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
<script src="../js/jquery-ui-timepicker-addon.min.js"></script>

<script src="js/ajaxlib.js"></script>
<script src="js/util.js"></script>
                
<script>
	$(function() {
        $( ".datetimepicker" ).datetimepicker({
            dateFormat: "dd-mm-yy",
            timeFormat: "HH:mm:ss",
            showSecond:true
		});        
	});
</script>
<link rel="stylesheet" type="text/css" href="../css/cargo.css">
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
<script>
	var addOn = false;
	function add(){
		/* document.getElementById('editBox').style.visibility = 'hidden';
		document.getElementById('searchBox').style.visibility = 'hidden';
		addOn = !addOn;
		if(addOn){
			document.getElementById('addBox').style.visibility = 'visible';
		}else{
			document.getElementById('addBox').style.visibility = 'hidden';
		} */
		
		document.location = 'detail.php?action=add';
	}
	var editOn = false;
	function edit(oid){
		/* document.getElementById('addBox').style.visibility = 'hidden';
		document.getElementById('searchBox').style.visibility = 'hidden';
		editOn = !editOn;
		if(editOn){
			document.getElementById('editBox').style.visibility = 'visible';
            document.getElementById('e-datetime').value = document.getElementById(oid+'datetime').textContent;
		}else{
			document.getElementById('editBox').style.visibility = 'hidden';
		} */
		
		document.location = 'detail.php?action=edit&id='+oid;
	}
	var searchOn = false;
	function searchBox(){
		document.getElementById('addBox').style.visibility = 'hidden';
		document.getElementById('editBox').style.visibility = 'hidden';
		searchOn = !searchOn;
		if(searchOn){
			document.getElementById('searchBox').style.visibility = 'visible';
		}else{
			document.getElementById('searchBox').style.visibility = 'hidden';
		}
	}
	function exportExcel(){
		// window.open('order_excel.php','_blank');
    }
    function viewdetail(oid){
        //click edit
        if (editOn){
                                    
        } else {
            location.href="product.php?order_id=" + oid;
        }
    }
	function resend() {
		
	}
</script>

<script>
var offset = 20;
var page = 1;

var packages = [
	{"packageid":"1","packagenumber":"P16000006",
	"customer":{"name":"Bundit Suksathan"},
	"createdate":"25/03/2016",
	"total_tracking":"3",
	"shipping":{"name":"นิ่มขนส่ง"},
	"shippingno":"NEM0003",
	"amount":"1,000.00",
	"statusid":"0",
	"adduser":"boon",
	"sentemail":"0"
	}
];

onPageReady = function() {
	packageList();
};

packageList = function() {
var httpreq = new AJAX();
var xmlreq = "";
var url = 'list_package.php?page='+page;

	// xmlreq = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
	// xmlreq += "<logout/>";
        
    httpreq.sendXMLRequest('GET', url, xmlreq, 
		function(result) {
			packages = JSON.parse(result);
			document.getElementById("detail").innerHTML = drawItems(packages);
		}, 
		function(error) {
			
		}
	);
};

drawItems = function(items) {
var html = '';
var i = 0;

	html+='<table class="detail">';
	html+='<tr>';
	html+='<th>เลขที่ กล่อง</th>';
	html+='<th>ชื่อลูกค้า</th>';
	html+='<th>วันที่สร้าง</th>';
	html+='<th>จำนวน Tracking</th>';
	html+='<th>บริษัทขนส่ง</th>';
	html+='<th>Shipping Tracking</th>';
	html+='<th>ราคารวม</th>';
	html+='<th>สถานะ</th>';
	html+='<th>Add user</th>';
	html+='<th>Sent Email</th>';
	html+='<th>Action</th>';
    html+='</tr>';

	for(i=0; i<items.length; i++) {
		html+='<tr class="punc normal " onclick="viewdetail(\''+items[i].packageid+'\')">';
		html+='<td>'+items[i].packagenumber+'</td>';  //เลขที่ กล่อง format  'PXXYYYYYY'  -- XX = ปีคศ , YYYYYY = running number ขึ้นปีใหม่ รันเลขใหม่
		html+='<td>'+items[i].customer.name+'</td>';  //ชื่อลูกค้า
		html+='<td>'+items[i].createdate+'</td>'; //วันที่สร้าง  27-03-2016  16:31:5
		html+='<td>'+items[i].total_tracking+'</td>'; //จำนวน Tracking
		html+='<td>'+items[i].shipping.name+'</td>'; //บริษัทขนส่ง
		html+='<td>'+items[i].shippingno+'</td>'; //Shipping Tracking
		html+='<td>'+items[i].amount+'</td>'; //ราคารวม
		switch (items[i].statusid) {
			case "0":
				html+='<td>Draft</td>'; //สถานะ  สถานะ 0 = draft , 1 = confirmed , 2 = ชำระเงินแล้ว
				break;
			case "1":
				html+='<td>Confirmed</td>'; //สถานะ  สถานะ 0 = draft , 1 = confirmed , 2 = ชำระเงินแล้ว
				break;
			case "2":
				html+='<td>ชำระเงินแล้ว</td>'; //สถานะ  สถานะ 0 = draft , 1 = confirmed , 2 = ชำระเงินแล้ว
				break;
			default :
				html+='<td></td>';
				break;
		}
		
		html+='<td>'+items[i].adduser+'</td>'; //Add user
		html+='<td>'+items[i].sentemail+'</td>'; //Sent Email
		// <input id="292st" type="hidden" value="0"/>
		html+='<td>'; // Action
		html+='<button onclick="edit(\''+items[i].packageid+'\');">Edit</button>';
		// <form onsubmit="return confirm('ต้องการลบข้อมูลใช่หรือไม่?');" action="order.php?page=1" method="post">
		// html+='<input name="del" value="292" type="hidden"/>';
		html+='<button onclick="delete(\''+items[i].packageid+'\');">Del</button>';
		html+= (items[i].statusid === '1') ? '<button onclick="resend(\''+items[i].packageid+'\');">Resend</button>' : '';
		// </form>
		html+='</td>';
		html+='</tr>';
	}
	
	html+='</table><br/>';
		
	return(html);

}

drawPaging = function() {
var html = '';

	html+='หน้า&emsp;<a href="?page=1&">1</a><a href="?page=2&">2</a>';
	html+='</div>';
	html+='<div class="results">';
    html+='<table>';
    html+='<tr>';
    html+='<td><b>จำนวนรายการทั้งหมด</b></td>';
    html+='<td>39&nbsp;</td>';
    html+='<td>Orders<br></td>';
    html+='</tr>';
	html+='</table>';
	
	return(html);
};
</script>

</head>

<body onload="onPageReady();">
	<h1><a href="package.php">Package</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
		<i class="material-icons" onclick="add();" title="Add">add_circle</i>
		<i class="material-icons" onclick="exportExcel();" title="Export">insert_drive_file</i>
		<i class="material-icons" onclick="searchBox();" title="Search">find_in_page</i>
	</div>
	<div id="detail">
		<table class="detail">
			<tr>
				<th>เลขที่ กล่อง</th>
				<th>ชื่อลูกค้า</th>
				<th>วันที่สร้าง</th>
				<th>จำนวน Tracking</th>
				<th>บริษัทขนส่ง</th>
				<th>Shipping Tracking</th>
				<th>ราคารวม</th>
				<th>สถานะ</th>
				<th>Add user</th>
				<th>Sent Email</th>
				<th>Action</th>
			</tr>
			<!-- detail -->
			
			<!-- detail -->
		</table><br/>
	</div>
	<div class="paging">
	
	</div>

	<div id="addBox" class="wrap">
                <table>
					<tr><th><h2 id="title">Add</h2></th><td></td></tr>
					<tr><th>เลขที่ กล่อง :</th><td><input name="oid" required="required"/></td></tr>
                    <tr><th>ชื่อลูกค้า :</th><td><select name="cid">
						<?php 
							for($i=0;$i<sizeof($cus_info);$i++){
								echo '<option value="'.$cus_id[$i].'">'.$cus_info[$i].'</option>';
							}
						?>
					</select></td></tr>
                    <tr><th>วันที่สร้าง :</th><td><input class="datetimepicker" name="datetime" required="required"/></td></tr>
					<tr><th>บริษัทขนส่ง :</th><td><select name="cid">
						<?php 
							for($i=0;$i<sizeof($cus_info);$i++){
								echo '<option value="'.$cus_id[$i].'">'.$cus_info[$i].'</option>';
							}
						?>
					</select></td></tr>										
                    <tr><th>Tracking ของบริษัทขนส่ง :</th><td><input type="text"/></td></tr>
					<tr><th>สถานะ :</th>
                                            <td><select name="status">
                                                    <option value="0">Draft</option>
                                                    <option value="1">Confirmed</option>
                                                    <option value="2">ชำระเงินแล้ว</option>
                                                </select>
                                            </td></tr>
					<input type="hidden" name="add" value="1"/>
					<tr class="confirm"><td></td><td><a onclick="add();">Cancel</a>&emsp;<button>Insert</button></td></tr>
				</table>
	</div>
	
	<div id="editBox" class="wrap">
			<form method="post">
				<table>
					<tr><th><h2 id="title">Edit</h2></th><td></td></tr>
					<tr><th>เลขที่ กล่อง:</th><td><input id="e-oid" name="oid" readonly/></td></tr>
                                        <tr><th>ชื่อลูกค้า :</th><td><select name="cid">
						<?php 
							for($i=0;$i<sizeof($cus_info);$i++){
								echo '<option id="e-cid-'.$cus_id[$i].'" value="'.$cus_id[$i].'">'.$cus_info[$i].'</option>';
							}
						?>
					</select></td></tr>
					<tr><th>วันที่สร้าง :</th><td><input class="datetimepicker" name="datetime" step="1"/></td></tr>
					<tr><th>บริษัทขนส่ง :</th><td><select name="cid">
						<?php 
							for($i=0;$i<sizeof($cus_info);$i++){
								echo '<option value="'.$cus_id[$i].'">'.$cus_info[$i].'</option>';
							}
						?>
					</select></td></tr>										
                    <tr><th>Tracking ของบริษัทขนส่ง :</th><td><input type="text"/></td></tr>
					<tr><th>สถานะ :</th>
                                            <td><select name="status">
                                                    <option value="0">Draft</option>
                                                    <option value="1">Confirmed</option>
                                                    <option value="2">ชำระเงินแล้ว</option>
                                                </select>
                                            </td></tr>						
					<input type="hidden" name="edit" value="1"/>
					<tr class="confirm"><td></td><td><a onclick="edit();">Cancel</a>&emsp;<button>Update</button></td></tr>
				</table>
			</form>
	</div>	
	<div id="searchBox" class="wrap">
			<form method="get">
				<table>
					<tr><th><h2 id="title">Search</h2></th><td></td></tr>
                                        <tr><th>เลขที่ กล่อง:</th><td><input name="oid"/></td></tr>
                                        <tr><th>ชื่อลูกค้า :</th><td>
										<input name="cid" list="lst">
										<datalist id="lst">
											<option value="Burin Tajama"/>
											<option value="bundit suksathan"/>
											<option value="test1 test1"/>
											<option value="test2 second"/>
											<option value="boon likhit"/>
											<option value="test third"/>
											<option value="chawakorn thinsuk"/>
											<option value="chawakorn thinsuk"/>
											<option value="bundit suksathan"/>
											<option value="test6 66"/>
											<option value="จักกิด วันรุ่นเมกัน"/>
											<option value="pao pao"/>											
											<option value="jjjjj jjjj"/>
										</datalist>
										
										</td></tr>
                                        <tr><th>From :</th><td><input class="datetimepicker" type="datetime-local" name="from"/></td></tr>
                                        <tr><th>To :</th><td><input class="datetimepicker" type="datetime-local" name="to"/></td></tr>
                                        <tr><th>สถานะ :</th>
                                            <td><select name="status">
                                                    <option value="0">Draft</option>
                                                    <option value="1">Confirmed</option>
                                                    <option value="2">ชำระเงินแล้ว</option>
                                                </select>
                                            </td></tr>
					<tr class="confirm"><td></td><td><a onclick="searchBox();">Cancel</a>&emsp;<button>Search</button></td></tr>
				</table>
			</form>
		</div>
</body>
</html>