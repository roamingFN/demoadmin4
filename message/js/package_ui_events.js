/* page events */
on_page_ready = function() {
	on_package_list();
};

on_package_list = function() {
	pack.request('service/list_package.php','GET', null,
		function(result) {
			console.log(result);
			document.getElementById("detail").innerHTML = draw_items(result);
		},
		function(error) {
			show_message_box('Package', error);
		}		
	);
};

on_package_search = function(search) {
	var data = {'action': 'search', 'value':search};
	
	pack.request('service/search_package.php', 'POST', data,
		function(result) {
			document.getElementById("detail").innerHTML = draw_items(result);
		},
		function(error) {
			show_message_box('Package', error);
		}	
	);
};

on_select_package = function(id) {
	document.location.href = 'detail.php?id='+id;
};

on_add_button_click = function(e) {
	document.location.href = 'detail.php?id=';
};

on_search_button_click = function(e) {
	
};

draw_items = function(result) {
var items = JSON.parse(result);

var html = '';
var i = 0;

	html+='<table class="detail">';
	html+='<tr>';
	html+='<th>เลขที่<br/>กล่อง</th>';
	html+='<th>วันที่สร้าง<br/>กล่อง</th>';
	html+='<th>ชื่อลูกค้า</th>';
	html+='<th>เลขที่ Order</th>';
	html+='<th>จำนวน<br/>Tracking</th>';
	html+='<th>Tracking<br/>complete</th>';
	html+='<th>Tracking<br/>incomplete</th>';
	html+='<th>สถานะ<br/>Order</th>';
	html+='<th>ราคาค่า<br/>ขนส่ง</th>';
	html+='<th>วิธีการส่ง</th>';
	html+='<th>ที่อยู่</th>';
	html+='<th>Remark</th>';
    html+='</tr>';

	for(i=0; i<items.length; i++) {
		html+='<tr class="punc normal " onclick="on_select_package(\''+items[i].packageid+'\')">';
		html+='<td>'+items[i].packagenumber+'</td>';  //เลขที่ กล่อง format  'PXXYYYYYY'  -- XX = ปีคศ , YYYYYY = running number ขึ้นปีใหม่ รันเลขใหม่		
		html+='<td align="center">'+items[i].createdate+'</td>'; //วันที่สร้าง  27-03-2016  16:31:5
		html+='<td>'+items[i].customer.name+'</td>';  //ชื่อลูกค้า
		html+='<td>'+items[i].orderno+'</td>'; //เลขที่ Order
		html+='<td align="center">'+items[i].tracking+'</td>'; //จำนวน Tracking
		html+='<td align="center">'+items[i].tracking_complete+'</td>'; //Tracking complete
		html+='<td align="center">'+items[i].tracking_incomplete+'</td>'; //Tracking incomplete
		
		html+='<td align="center">'+items[i].order_status+'</td>'; //สถานะ Order
		html+='<td align="center">'+items[i].transport_amount+'</td>'; //ราคาค่าขนส่ง
		html+='<td align="center">'+items[i].transport_type+'</td>'; //วิธีการส่ง
		html+='<td align="left">'+items[i].transport_address+'</td>'; //ที่อยู่
		html+='<td align="left">'+items[i].remark+'</td>'; //Remark
		
		/* html+='<td>'; // Action
		html+='<button onclick="edit(\''+items[i].packageid+'\');">Edit</button>';
		html+='<button onclick="delete(\''+items[i].packageid+'\');">Del</button>';
		html+= (items[i].statusid === '1') ? '<button onclick="resend(\''+items[i].packageid+'\');">Resend</button>' : '';
		html+='</td>'; */
		
		html+='</tr>';
	}
	
	html+='</table><br/>';
		
	return(html);	
};

draw_paging = function() {
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


