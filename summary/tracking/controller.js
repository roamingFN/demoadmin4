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

function countArrayInObject(result) {
		var count = 0;
		for (var tkno in result) {
			    count++;
		}
		return count-1;
}

function returnButton() {
	var r = confirm("ต้องการคืนเงินให้สินค้า ใช่หรือไม่");
	if (r == false) {
	    return 0;
	}
	
	var data = {};
	var totalReturn = 0;
	$('#returnDialog tbody tr').each(function () {
		var oid = $('#dialog_oid').val();
		var opid = $('#dialog_opid').val();
		var opid = $('#dialog_cid').val();
		
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

	console.log(data);

	// console.log(oid);
	// console.log($('#cid').val());
	//console.log(Object.keys(data).length);
	
		// $("#loading").css('visibility', 'visible');
  //   	$.ajax({
  //               type: 'POST',
  //               url: './tracking/utility/returnDialog.php',
  //               data: {'data': JSON.stringify(data)
  //                   		,'cid': $('#cid').val()
  //                   		,'oid': $('#oid').val()
  //                   		,'return_yuan2': $('.grandTotal tr').find("td").eq(1).text().replace(/,/g, '')
  //               }, 
  //               dataType: 'json',
  //               success: function(result) {
  //               	alert('คืนเงินสำเร็จ');
  //            		$("#loading").css('visibility', 'hidden');
  //                	location.reload();
  //                           //console.log(result);
  //           	},
  //               error: function(exception) {
  //                 	alert('Exception: '+ exception);
  //                  	console.log(exception);
  //               	$("#loading").css('visibility', 'hidden');
  //           	}
  //      	});
}

function backReturnButton() {
	alert('ยกเลิกคืนเงิน');
}

shopReturnDialogOn = false;
function showShopReturnDialog (opid) {
		shopReturnDialogOn = !shopReturnDialogOn;
		if(shopReturnDialogOn) {
				$.ajax({
						type: 'GET',
						url: './tracking/utility/getReturnSummary.php?opid='+opid, 
						dataType: 'json',
						success: function(result) {
								var len = countArrayInObject(result['data']);
								if ((len==0) && (result['error'] == '')) {
										alert('การดึงข้อมูลล้มเหลว กรุณาแจ้งเจ้าหน้าที่');
								}
								else if ((len==0) && (result['error'] != '')) {
										alert(result['error']);
								}
								else {
										//console.log(result);
										var total_return_quantity = 0;
										var total_return_yuan = 0;
										var total_total = 0;
										var total_tran_cost = 0;
										var total_yuan = 0;
										var total_baht = 0;

										for (var running in result['data']) {
											var length = result['data'][running].length;
											if (length==0) continue;

											//prepare data
											var rate = result['data'][running]['order_rate'];
											var return_quantity = Number(result['data'][running]['return_quantity']);
											var return_yuan = Number(result['data'][running]['return_yuan']);
											var backshop_quantity = result['data'][running]['backshop_quantity'];
											var backshop_price = result['data'][running]['backshop_price'];
											var tran_cost = Number(result['data'][running]['order_shipping_cn_cost']);
											var yuan = Number(backshop_price*return_quantity)+Number(tran_cost);
											var baht = yuan*rate;
											var status = result['data'][running]['return_status'];
											if ((status==null) || (status!=1)) {
												var status_desc = 'รอคืนเงิน';
												var action = '<a style="cursor: pointer" onclick="returnButton();">คืนเงิน</a>';
											}
											else {
												var status_desc = 'คืนเงินแล้ว';
												var action = '<a style="cursor: pointer" onclick"backReturnButton();">กลับ</a>';
											}
											var remark = result['data'][running]['remark'];
											var oid = result['data'][running]['order_id'];
											var opid = result['data'][running]['order_product_id'];
											var cid = result['data'][running]['customer_id'];

											//summary
											total_return_quantity += return_quantity;
											total_return_yuan += return_yuan;
											total_total += (backshop_price*return_quantity);
											total_tran_cost += tran_cost;
											total_yuan += yuan;
											total_baht += baht;

											//display data
											document.getElementById('title').textContent = rate;
											document.getElementById('return_quantity').value = return_quantity;
											document.getElementById('return_yuan').value = return_yuan.toFixed(2);

											document.getElementById('backshop_price').value = backshop_price;
											document.getElementById('total').value = (backshop_price*return_quantity).toFixed(2);
											document.getElementById('tran_cost').value = tran_cost.toFixed(2);

											document.getElementById('yuan').value = yuan.toFixed(2);
											document.getElementById('baht').value = baht.toFixed(2);

											document.getElementById('status').textContent = status_desc;
											document.getElementById('action').innerHTML = action;

											document.getElementById('remark').value = remark;

											//hidden
											//console.log(oid);
											document.getElementById('dialog_oid').value = oid;
											document.getElementById('dialog_opid').value = opid;
											document.getElementById('dialog_cid').value = cid;
											document.getElementById('backshop_quantity').value = backshop_quantity;
											document.getElementById('backshop_price').value = backshop_price;
											document.getElementById('dialog_rate').value = rate;
										}

										document.getElementById('total_return_yuan').textContent = total_return_yuan.toFixed(2);
										document.getElementById('total_return_quantity').textContent = total_return_quantity;
										document.getElementById('total_total').textContent = total_total.toFixed(2);
										document.getElementById('total_tran_cost').textContent = total_tran_cost.toFixed(2);
										document.getElementById('total_yuan').textContent = total_yuan.toFixed(2);
										document.getElementById('total_baht').textContent = total_baht.toFixed(2); 
								}
						},
						error: function(exception) {
								alert('Exception: '+exception);
								console.log(exception);
						}
				});
				$('#shopReturnDialog').css("visibility", "visible");
		}
		else {
				$('#shopReturnDialog').css("visibility", "hidden");
		}
}


function calReturn(shop) {
	
	//shop refund
	var totalQuan = 0;
	var totalYuan = 0;
	var totalBaht = 0;
	var totalMissingBaht = 0;
	var totalHaveToReturn = 0;
	
	//diff
	var totalMissing = 0;
	var totalReceived = 0;
	var totalDiff = 0;

	$('#shop-' + shop +' tbody tr').each(function () {
		var rate = Number($(this).find("input").eq(5).val());
		if (isNaN(rate)) rate=0;
		
		//shop refund
		var backshopQuan = Number($(this).find("td").eq(6).text());
		var backshopPrice = Number($(this).find("td").eq(7).text());
		var quan = Number($(this).find("input").eq(0).val());
		if (isNaN(quan)) quan=0;
		var yuan = quan*backshopPrice;
		var baht = yuan*rate;
		//var missing = backshopQuan-quan-Number($(this).find("input").eq(2).val());
		
		//diff
		var missing = backshopQuan - quan;
		var missingBaht = missing*backshopPrice*rate;
		var backshop_return = Number($(this).find("td").eq(12).text());
		if (isNaN(backshop_return)) backshop_return=0; 
		var received = Number($(this).find("td").eq(20).text());
		if (isNaN(received)) received=0;
		var diff = received-missing;
		
		//display result 
		$(this).find("td").eq(15).text(numberWithCommas(baht));
		$(this).find("input").eq(1).val(yuan.toFixed(2));
		$(this).find("td").eq(17).text(numberWithCommas(baht-backshop_return));
		$(this).find("td").eq(19).text(missing);
		$(this).find("td").eq(20).text(received);
		$(this).find("td").eq(21).text(diff);

		//total
		totalQuan += quan;
		totalYuan += yuan;
		totalBaht += baht;
		totalMissing += missing;
		totalMissingBaht += missingBaht;
		totalHaveToReturn += baht-backshop_return;
		totalReceived += received;
		totalDiff += diff;
	});
	$('#shop-' + shop + ' tfoot').find("td").eq(14).text(totalQuan);
	$('#shop-' + shop + ' tfoot').find("td").eq(15).text(numberWithCommas(totalBaht));
	$('#shop-' + shop + ' tfoot').find("td").eq(17).text(numberWithCommas(totalHaveToReturn));
	$('#shop-' + shop + ' tfoot').find("td").eq(18).text(numberWithCommas(totalYuan));
	$('#shop-' + shop + ' tfoot').find("td").eq(19).text(totalMissing);
	$('#shop-' + shop + ' tfoot').find("td").eq(20).text(totalReceived);
	$('#shop-' + shop + ' tfoot').find("td").eq(21).text(totalDiff);

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
		if (isNaN(yuan)) yuan=0;
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
		console.log(trackingid);
		var opid = document.getElementById('opid-'+trackingid).value;
		var return_quan = Number($(this).find("input").eq(0).val());
		var return_yuan2 = Number($(this).find("input").eq(1).val());

		var loss_quan = Number($(this).find("input").eq(2).val());
		var loss_baht = Number($(this).find("input").eq(3).val());

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
			return_yuan2: return_yuan2,
			loss_quan: loss_quan,
			loss_baht: loss_baht
		};
	});
	console.log(data);
	$('#loading').css('visibility', 'visible');
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

function returnLoss(opidParam) {
	var r = confirm("ต้องการคืนเงินให้สินค้า ใช่หรือไม่");
	if (r == false) {
	    return 0;
	}
	
	var data = {};
	var totalReturn = 0;
	$('.quan tbody tr').each(function () {
		var trackingid = $(this).attr('id');
		var opid = document.getElementById('opid-'+trackingid).value;
		if (opid!=opidParam) return true;
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
                    url: './tracking/utility/returnLoss.php',
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

function backReturnLoss(opidParam) {
	var r = confirm("ต้องการยกเลิกการคืนเงิน ใช่หรือไม่");
	if (r == false) {
	    return 0;
	}
	
	var data = {};
	var totalReturn = 0;
	$('.quan tbody tr').each(function () {
		var trackingid = $(this).attr('id');
		var opid = document.getElementById('opid-'+trackingid).value;
		if (opid!=opidParam) {
			return true;
		}
		var backReturn = Number($(this).find("input").eq(5).val());

		data[opid] = {
			'backReturn': backReturn
		}  
	});

	//console.log(data);
	
		$("#loading").css('visibility', 'visible');
    	$.ajax({
          	type: 'POST',
          	url: './tracking/utility/backReturnLoss.php',
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