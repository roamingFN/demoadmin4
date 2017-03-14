/* page events */
on_page_ready = function() {
	on_list_shipping();
	on_list_customer('');
	// on_get_package_detail();
};

on_add_detail_button_click = function(e) {
	// show popup find order / tracking
	show_order_product_tracking();
};

on_search_tracking = function() {
var order_no = document.getElementById("search_order_no").value;
var tracking_no	= document.getElementById("search_tracking_no").value;
var customer_id = '';
	var data = {'action': 'search_tracking', 'order_no':order_no, 'tracking_no':tracking_no, 'customer_id':customer_id};
	
	pack.request('service/search_tracking.php', 'POST', data,
		function(result) {
			console.log(result);
			var items = JSON.parse(result);
			if (items.length === 0) {
				show_message_box('Search Result', 'ไม่พบข้อมูล');
			} else {
				document.getElementById("order_product_tracking_list").innerHTML = draw_order_product_tracking(items);
			}
		},
		function(error) {
			show_message_box('Search Result', error);
		}	
	);
};

on_remove_detail_button_click = function(e) {
	// show confirm remove detail
	var obj = document.getElementsByName("chkitem");
        var i = 0;
        var ischk = false;
		var items = [];
        for (i=0; i<obj.length; i++) {
            if (obj[i].checked) {
                items[items.length] = obj[i].value;
            }
        }
        
        if (ischk) {
            show_confirm_delete_item('Delete Items', 'Are you sure you want to delete items', function(e) {
                remove_items(items);        
            });
        }
};

remove_items = function(items) {
    var i = 0;

    for (i=0; i<items.length; i++) {
        
    }
	
	var data = {'action': 'remove_items', 'id':package_id};
	
	pack.request('service/package_detail.php', 'POST', data,
		function(result) {
			console.log(result);
			document.getElementById("package_items").innerHTML = show_package_item(result);
		},
		function(error) {
			show_message_box('Package', error);
		}	
	);	
	
    hide_confirm_delete_item('confirm_delete_item_box');
    addItemList(taxinv.items, document.getElementById("taxinv_item_list"));    
};

on_print_button_click = function(e) {
	// show address popup
	show_customer_address();
};

on_save_button_click = function(e) {
	// save
	
};

on_back_button_click = function(e) {
	document.location.href = 'index.php';
};

on_confirm_button_click = function(e) {
	// confirm and send mail
};

on_list_shipping = function() {
	var data = null;
	pack.request('service/list_shipping.php', 'GET', data,
		function(result) {
			console.log(result);
			document.getElementById("shipping_id").innerHTML = draw_shipping(result);
			on_get_package();
		},
		function(error) {
			show_message_box('Package', error);
		}	
	);	
	
	
};
on_list_customer = function(search) {
	var data = {'action': 'list_customer', 'search':search};
	
	pack.request('service/list_customer.php', 'POST', data,
		function(result) {
			console.log(result);
			if (JSON.parse(result).length === 0) {
				show_message_box('Package Detail', 'ไม่พบข้อมูลลูกค้า');
			} else {
				draw_customer(result, document.getElementById("lstcustomer"));	
			}
			
			// document.getElementById("lstcustomer").innerHTML = draw_customer(result);
		},
		function(error) {
			show_message_box('Package Detail', error);
		}	
	);	
	
	
};

on_get_package_detail = function() {
	var data = {'action': 'package_detail', 'id':package_id};
	
	pack.request('service/package_detail.php', 'POST', data,
		function(result) {
			console.log(result);
			packagedetail.items = JSON.parse(result);			
			show_package();
			document.getElementById("package_items").innerHTML = show_package_item();
		},
		function(error) {
			show_message_box('Package', error);
		}	
	);	
	
};

on_get_package = function() {
	var data = {'action': 'package', 'id':package_id};
	
	pack.request('service/package.php', 'POST', data,
		function(result) {
			console.log(result);
			packagedetail = JSON.parse(result);
			on_get_package_detail();
			
		},
		function(error) {
			show_message_box('Package', error);
		}	
	);	
};

on_select_tracking = function() {
	
};

show_order_product_tracking = function() {
	document.getElementById("search_order_no").value = '';
	document.getElementById("search_tracking_no").value = '';
    document.getElementById("order_product_tracking_box").style.visibility = 'visible';
};

hide_order_product_tracking = function() {
	document.getElementById("order_product_tracking_box").style.visibility = 'hidden';
};

show_package = function() {

	document.getElementById("package_number").innerHTML = packagedetail.packagenumber;
	document.getElementById("datepicker").value = packagedetail.createdate;
	document.getElementById("customer_name").value = packagedetail.customer.name;
	document.getElementById("shipping_id").value = packagedetail.transport_id;
	document.getElementById("shippingno").value = packagedetail.shippingno;
	document.getElementById("remark").value = packagedetail.remark;
	
	document.getElementById("package_total_amount").innerHTML = '0.00';
};

show_package_item = function() {
var html = '';
var i;
html+='<table class="order-product" style="width:100%;padding:0px;" align="center">';
html+='<thead>';
html+='<th></th><th width="40">ลำดับ</th><th>เลขที่<br/>Order</th><th>เลขที่<br/>Trackin(จีน)</th><th>M3</th><th>Kg.</th><th>Rate</th><th>ยอดเงิน</th>';
html+='</thead>';

for (i=0; i<packagedetail.items.length; i++) {
	html+='<tr class="">';
	html+='<td><input name="chkitem" type="checkbox" value="'+packagedetail.items[i].packageorder+'"/></td>';
	html+='<td align="right">'+packagedetail.items[i].packageorder+'</td>';
	html+='<td>'+packagedetail.items[i].order_number+'</td>';
	html+='<td>'+packagedetail.items[i].tracking_no+'</td>';
	html+='<td>0</td>';
	html+='<td align="right">'+packagedetail.items[i].m3+'</td>';
	html+='<td align="right">'+packagedetail.items[i].weight+'</td>';
	html+='<td align="right">'+packagedetail.items[i].rate+'</td>';
	html+='<td align="right">'+packagedetail.items[i].amount+'</td>';
	html+='</tr>';
}

html+='</table>';

return(html);
};

show_package_detail = function(result) {
var p = JSON.parse(result);
var html = '';
var c, t = 0;
	html+='<table style="width:100%;padding:10px;">';
	html+='<tr>';
	html+='<td width="15%" style="padding:2px">เลขที่ กล่อง  : </td>';
	html+='<td width="20%" style="padding:2px"><label id="package_number"><b>P16000006</b></label></td>';
	html+='<td style="padding:2px">วันที่สร้าง : </td>';
	html+='<td style="padding:2px"><input id="package_createdate" class="china datepicker" style="padding:2px;" value="25/03/2016"/></td>';
	html+='</tr>';
	html+='<tr>';
	html+='<td width="15%" style="padding:2px">ลูกค้า  : </td>';
	html+='<td width="20%" style="padding:2px">';
	html+='<input id="customer_name" list="lstcustomer">';

	html+='<datalist id="lstcustomer">';

	for (c=0; c<item.customer.length; c++) {
		html+='<option value="'+item.customer[c].customer_firstname+' '+item.customer[c].customer_lastname+'"/>';
	}
	
	html+='</datalist>';
	html+='</td>';
	html+='<td style="padding:2px"></td>';
	html+='<td style="padding:2px"></td>';
	html+='</tr>';
	html+='<tr>';
	html+='<td width="15%" style="padding:2px">บริษัทขนส่ง  :</td>';
	html+='<td width="20%" style="padding:2px">';
	html+='<select id="shipping_id">';

	for (c=0; c<item.transport.length; c++) {
		html+='<option value="'+item.tranport[c].id+'">'+item.tranport[c].name+'</option>';
	}
	html+='</select>';
	html+='</td>';
	html+='<td width="15%" style="padding:2px">Tracking ของบริษัทขนส่ง : </td>';
	html+='<td style="padding:2px"><input type="text" style="padding:2px;width:200px;"/></td>';
	html+='</tr>';
	html+='</table>';

	return(html);
};

draw_order_product_tracking = function(items) {
var html = '';
var i = 0;

	html+='<table class="order-product" style="width:100%;padding:0px;" align="left">';
	html+='<thead>';
	html+='<th></th><th width="40">Order No.</th><th>Tracking No.</th><th>Order<br/>Status</th><th>วันที่(Tracking complete)</th><th>วิธีจัดส่ง</th><th>ที่อยู่</th><th>M3</th><th>Kg.</th><th>Rate</th><th>ราคา</th>';
	html+='</thead>';

	for (i=0; item.details.length; i++) {
		html+='<tr class="">';
		html+='<td><input type="checkbox" value="'+i+'"/></td>';
		html+='<td>'+items[i].order_number+'</td>';
		html+='<td>'+items[i].tracking_no+'</td>';
		html+='<td>'+items[i].order_complete_status+'</td>';
		html+='<td>0</td>';
		html+='<td>'+items[i].transport_th_name+'</td>';
		html+='<td>'+items[i].transport_address+'</td>';
		html+='<td align="right">'+items[i].m3+'</td>';
		html+='<td align="right">'+items[i].weight+'</td>';
		html+='<td align="right">'+items[i].rate+'</td>';
		html+='<td align="right">'+items[i].total+'</td>';
		html+='</tr>';	
	}

	html+='</table>';

	return(html);	
};

draw_shipping = function(result) {
var items = JSON.parse(result);
var i = 0;
var html = '';

	for (i=0;i<items.length;i++) {
		html+='<option value="'+items[i].id+'">'+items[i].name+'</option>';
	}
	
	return(html);
};

/* draw_customer = function(result) {
var items = JSON.parse(result);
var i = 0;
var html = '';

	for (i=0;i<items.length;i++) {
		html+='<option value="'+items[i].name+'"/>';
	}
	
	return(html);	
}; */

draw_customer = function(result, list) {
var items = JSON.parse(result);
var html = '';
var i = 0;
    html+='<ul>';
    
    for(i=0; i<items.length; i++) {
        html+='<li class="customer_list" value="'+i+'">';
        html+='<img src="images/user_unknow.png" alt=""/>';
        html+='<div><label>'+items[i].name+'</label></div>';
        html+='</li>';
        html+='</li>';
    }
    html+=' </ul>';
    
    list.innerHTML = html;
    
    var objs = document.getElementsByClassName("customer_list");
    
    for(i=0; i<objs.length; i++) {
        objs[i].onclick = function(e) {
            on_select_customer(items[this.value]);
        };
    }
    
};

on_select_customer = function(customer) {
    console.log('Select Customer');
    console.log(JSON.stringify(customer));
};

draw_item = function(result) {
var item = JSON.parse(result);

var html = '';
var i = 0;

	html+='<table class="order-product" style="width:100%;padding:0px;" align="left">';
	html+='<thead>';
	html+='<th></th><th width="40">ลำดับที่</th><th>Order No.</th><th>M3</th><th>Wg.</th><th>Rate</th><th>ราคา</th>';
	html+='</thead>';

	for (i=0; item.details.length; i++) {
		html+='<tr class="">';
		html+='<td><input type="checkbox"/></td>';
		html+='<td align="right">1</td>';
		html+='<td>R16040600001</td>';
		html+='<td align="right">100</td>';
		html+='<td align="right">1,000</td>';
		html+='<td align="right">50</td>';
		html+='<td align="right">5,000.00</td>';
		html+='</tr>';	
	}

	html+='</table>';

	return(html);
}

show_confirm_delete_item = function(title, text, onok) {
var obj = document.getElementById("confirm_delete_item_box_main");
    document.getElementById("confirm_delete_item_title").innerHTML = title;
    document.getElementById("confirm_delete_item_text").innerHTML = text;

    document.getElementById("confirm_delete_item_box").style.height = window.innerHeight + "px";
    
    obj.style.top = ((window.innerHeight - obj.offsetHeight)/2 - 100) + "px";
    obj.style.left = (window.innerWidth - obj.offsetWidth)/2 + "px";
    
    
    document.getElementById("confirm_delete_item_box").style.visibility = "visible";    
    document.getElementById("confirm_delete_item_ok").onclick = onok;
    document.getElementById("confirm_delete_item_ok").focus();
    obj = null;
}

hide_confirm_delete_item = function(id) {
var obj = document.getElementById(id);

    document.getElementById("confirm_delete_item_box").style.visibility = "hidden";
    // obj.style.top = window.innerHeight +"px";

    /* obj.style.top = (obj.offsetTop + 50) + "px";
    if (obj.offsetTop > height) {
        obj.style.top = height +"px";
        document.getElementById("form_screen").style.visibility = "hidden";
	clearTimeout(timerId);
    } else {
	timerId = setTimeout("hide_form('"+id+"')", timeInterval);
    } */
    obj = null;
        
};