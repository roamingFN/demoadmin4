amountDialogOn = false;
function showAmountDialog (tid) {
		amountDialogOn = !amountDialogOn;
		if(amountDialogOn) {
				var sum = 0;
				var data = {}; 
				data['tid'] = tid;
				var result = true;
				var xhr = new XMLHttpRequest();
				xhr.open('POST','tracking/utility/getAmount.php',true);
				xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
				xhr.onreadystatechange = function(){
						if(xhr.readyState==4 && xhr.status==200) {
								var objs = JSON.parse(xhr.responseText);
								var len  = Object.keys(objs).length;
								var table = document.getElementById("amountTable");
								for (i=0; i<len; i++) {
									    var row = table.insertRow(-1);
				
									    var cell = row.insertCell(-1);
									    cell.innerHTML = objs[i]['tracking_no'];
										
										amount = parseInt(objs[i]['amount']);
										var cell = row.insertCell(-1);
									    cell.innerHTML = amount;

									    sum += amount;
								}
								//summary row
								var row = table.insertRow(-1);
				
							    var cell = row.insertCell(-1);
							    cell.innerHTML = '<b>รวมทั้งหมด</b>';
								
								var cell = row.insertCell(-1);
							    cell.innerHTML = '<b>'+sum+'</b>';
						}
				};
				xhr.send('data='+JSON.stringify(data));

				$('#amountDialog').css("visibility", "visible");
		}
		else {
				$('#amountDialog').css("visibility", "hidden");
				var rowCount = document.getElementById('amountTable').rows.length;
				for (i=0; i<rowCount-1; i++) {
						document.getElementById("amountTable").deleteRow(-1);
				}
		}
}

function calReturn(shop) {
	var totalQuan = 0;
	var totalYuan = 0;
	var totalBaht = 0;
	var totalMissing = 0;
	var totalMissingBaht = 0;

	$('#shop-' + shop +' tbody tr').each(function () {
		var rate = Number($(this).find("input").eq(3).val());
		var backshopQuan = Number($(this).find("td").eq(6).text());
		var backshopPrice = Number($(this).find("td").eq(7).text());
		var quan = Number($(this).find("input").eq(0).val());
		var yuan = quan*backshopPrice;
		var baht = yuan*rate;
		var missing = backshopQuan-quan-Number($(this).find("input").eq(2).val());
		var missingBaht = missing*backshopPrice*rate;

		//display result 
		$(this).find("td").eq(16).text(numberWithCommas(yuan));
		$(this).find("td").eq(17).text(numberWithCommas(baht));
		$(this).find("input").eq(1).val(yuan.toFixed(2));
		$(this).find("td").eq(19).text(missing);
		$(this).find("td").eq(20).text(numberWithCommas(missingBaht));

		//total
		totalQuan += quan;
		totalYuan += yuan;
		totalBaht += baht;
		totalMissing += missing;
		totalMissingBaht += missingBaht;
	});
	$('#shop-' + shop + ' tfoot').find("td").eq(14).text(totalQuan);
	$('#shop-' + shop + ' tfoot').find("td").eq(16).text(numberWithCommas(totalYuan));
	$('#shop-' + shop + ' tfoot').find("td").eq(17).text(numberWithCommas(totalBaht));
	$('#shop-' + shop + ' tfoot').find("td").eq(18).text(numberWithCommas(totalYuan));
	$('#shop-' + shop + ' tfoot').find("td").eq(19).text(totalMissing);
	$('#shop-' + shop + ' tfoot').find("td").eq(20).text(numberWithCommas(totalMissingBaht));

	//grand total
	var grandTotalMissingBaht = 0;
	$('.quan tbody tr').each(function () {
		grandTotalMissingBaht += Number($(this).find("td").eq(20).text().replace(/,/g, ''));
	});
	$('.grandTotal tr').find("td").eq(0).text(numberWithCommas(grandTotalMissingBaht));
	$('.grandTotal tr').find("td").eq(1).text(numberWithCommas(grandTotalMissingBaht));
}

function calReturn2(shop) {
	var totalYuan = 0;
	$('#shop-' + shop +' tbody tr').each(function () {
		var yuan = Number($(this).find("input").eq(1).val());
		totalYuan += yuan;
	});
	$('#shop-' + shop + ' tfoot').find("td").eq(18).text(numberWithCommas(totalYuan));

	//grand total
	var return_yuan2 = 0;
	$('.quan tbody tr').each(function () {
		return_yuan2 += Number($(this).find("input").eq(1).val());
	});
	$('.grandTotal tr').find("td").eq(1).text(numberWithCommas(return_yuan2));
}

function numberWithCommas(x) {
	//console.log(Number(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
	return Number(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function save() {
	var data = {};
	$('.quan tbody tr').each(function () {
		var trackingid = $(this).attr('id');
		var opid = document.getElementById('opid-'+trackingid).value;
		var return_quan = Number($(this).find("input").eq(0).val());
		var return_yuan2 = Number($(this).find("input").eq(1).val());

		//validate
		var backshop_quan = Number($(this).find("td").eq(6).text());
		if (return_quan>backshop_quan) {
			alert('จำนวนรับที่ขาดต้องไม่เกินจำนวนที่สั่ง');
			$(this).find("input").eq(0).focus();
			return;
		}

		data[trackingid] = {
			opid: opid,
			return_quan: return_quan,
			return_yuan2: return_yuan2
		};
	});

	//request
	var xhr = new XMLHttpRequest();
	xhr.open('POST','./tracking/utility/save.php',true);
	xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	xhr.onreadystatechange = function(){
		if(xhr.readyState==4 && xhr.status==200) {
			$('#loading').css('visibility', 'hidden');
			if(xhr.responseText=='success') {
                alert("บันทึกข้อมูลเรียบร้อยแล้ว");
				location.reload();
			} else{
				alert(xhr.responseText);
			}
		}
	};
	$('#loading').css('visibility', 'visible');
	xhr.send('data='+JSON.stringify(data));
}

function allReturn() {
	var r = confirm("ต้องการคืนเงินให้สินค้าทุกชิ้น ใช่หรือไม่");
	if (r == false) {
	    return 0;
	}
	
	var data = {};
	var totalReturn = 0;
	$('.quan tbody tr').each(function () {
		var trackingid = $(this).attr('id');
		var opid = document.getElementById('opid-'+trackingid).value;
		
		var backshop_quan  = Number($(this).find("td").eq(6).text().replace(/,/g, ''));
		var return_quan = Number($(this).find("input").eq(0).val());
		if (isNaN(return_quan)) return_quan=0;

		var backshop_price = Number($(this).find("td").eq(7).text().replace(/,/g, ''));
		var return_price = Number($(this).find("td").eq(7).text().replace(/,/g, ''));
		if (isNaN(return_price)) price=0;

		var rate = Number($(this).find("input").eq(3).val());
		var missing = backshop_quan-return_quan-Number($(this).find("input").eq(2).val());
		var missingBaht = missing*backshop_price*rate;

		
		data[opid] = {
			'backshop_quan': backshop_quan,
			'return_quan': return_quan,
			'backshop_price': backshop_price,
			'return_price': return_price,
			'rate': rate
		}  
	});

	// console.log(oid);
	// console.log($('#cid').val());
	//console.log(Object.keys(data).length);
	if (Object.keys(data).length>0) {
			$("#loading").css('visibility', 'visible');
            $.ajax({
                    type: 'POST',
                    url: './tracking/utility/return.php',
                    data: {'data': JSON.stringify(data)
                    		,'cid': $('#cid').val()
                    		,'oid': $('#oid').val()
                    		,'return_yuan2': $('.grandTotal tr').find("td").eq(1).text().replace(/,/g, '')
                    }, 
                    dataType: 'json',
                    success: function(result) {
                            alert('คืนเงินสำเร็จ');
                            $("#loading").css('visibility', 'hidden');
                            location.reload();
                            //console.log(result);
                    },
                    error: function(exception) {
                            alert('Exception: '+ exception);
                            console.log(exception);
                            $("#loading").css('visibility', 'hidden');
                    }
       		});
	}
	else {
			alert('ไม่พบสินค้าที่ต้องคืนเงิน');
	}
}

function backReturn() {
	var r = confirm("ต้องการย้อนกลับการคืนเงินให้สินค้าทุกชิ้น ใช่หรือไม่");
	if (r == false) {
	    return 0;
	}

	var data = {};
	var totalReturn = 0;
	$('.quan tbody tr').each(function () {
		var trackingid = $(this).attr('id');
		var opid = document.getElementById('opid-'+trackingid).value;

		var backReturn = Number($(this).find("input").eq(5).val());

		data[opid] = {
			'backReturn': backReturn
		}  
	});

	//console.log(data);
	if (Object.keys(data).length>0) {
			$("#loading").css('visibility', 'visible');
            $.ajax({
                    type: 'POST',
                    url: './tracking/utility/backReturn.php',
                    data: {'data': JSON.stringify(data)
                    		,'cid': $('#cid').val()
                    		,'oid': $('#oid').val()
                    }, 
                    dataType: 'json',
                    success: function(result) {
                            $("#loading").css('visibility', 'hidden');
                            if (typeof(result.error)!='undefined') {
                            	alert(result.error);
                            	return;
                            }
                            alert('คืนเงินสำเร็จ');
                            location.reload();
                            //console.log(result);
                    },
                    error: function(exception) {
                            alert('Exception: '+ exception);
                            console.log(exception);
                            $("#loading").css('visibility', 'hidden');
                    }
       		});
	}
	else {
			alert('ไม่พบสินค้าที่ต้องคืนเงิน');
	}
}