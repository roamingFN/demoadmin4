function initProduct() {
		emailBoxOn = false;
}

// function emailBox(opid){
// 		document.getElementById('addBox').style.visibility = 'hidden';
// 		document.getElementById('refundBox').style.visibility = 'hidden';
// 		document.getElementById('backRefundBox').style.visibility = 'hidden';
// 		document.getElementById('emailLog').style.visibility = 'hidden';
// 		emailBoxOn = !emailBoxOn;
// 		if(emailBoxOn) {
// 				var ret = document.getElementById('ret-'+opid).value;
// 				if (ret!=2) {
// 					alert('รายการนี้ยังไม่ได้คืนเงิน');
// 					emailBoxOn = !emailBoxOn;
// 					return;
// 				}
				
// 				//init----------------------------------------
// 				var subject = '';
// 				var content = '';
// 				document.getElementById('email-oid').value   	= orderId;
// 				document.getElementById('email-ono').value   	= orderNum;
// 				document.getElementById('email-opid').value  	= opid;
// 				document.getElementById('email-cid').value   	= customer_id;
// 				document.getElementById('email-quan1').value = "";
// 				document.getElementById('email-quan').value = "";
// 				document.getElementById('email-sumqaun').value = "";
// 				document.getElementById('email-tran1').value = "";
// 				document.getElementById('email-tran').value = "";
// 				document.getElementById('email-sumtran').value = "";
// 				document.getElementById('email-cpp1').value = "";
// 				document.getElementById('email-cpp').value = "";
// 				document.getElementById('email-sumcpp').value = "";

// 				//get data-------------------------------------
// 				var tran1 = parseFloat(document.getElementById('tran1-'+opid).textContent.replace(/,/g, ''));
// 				var tran = parseFloat(document.getElementById('bTran-'+opid).value);
// 				var diffTran = tran1-tran;
// 				var quan1 = parseInt(document.getElementById('quan1-'+opid).textContent.replace(/,/g, '')); 
// 				var quan = parseInt(document.getElementById('quan-'+opid).value);
// 				var diffQuan = quan1-quan;
// 				var cpp1 = parseFloat(document.getElementById('cpp1-'+opid).textContent.replace(/,/g, ''));
// 				var cpp = parseFloat(document.getElementById('cpp-'+opid).value);
// 				var diffPrice = cpp1-cpp;

// 				content = 'สรุป'; 

// 				if (diffTran>0 && diffQuan>0) {	//case 3
// 					var cpp = document.getElementById('cpp-'+opid).value;
// 					var totalCn = cpp*diffQuan;
// 					var totalTh = totalCn*rate1;
// 					subject = 'แจ้งคืนเงินเลขที่ออเดอร์ '+orderNum;
// 					content = '\t\tสินค้าที่ลูกค้าสั่งจำนวน\t'+quan1+'\tชิ้น\n'+
// 						'\t\tสินค้าที่สั่งได้\t\t'+quan+'\tชิ้น\n'+
// 						'\t\tขาด\t\t\t'+diffQuan+'\tชิ้น\n'+
// 						'\t\tยอดค่าสินค้า\t\t'+totalCn.toFixed(2)+'\tหยวน\n'+
// 						'\t\tยอดรวมทั้งหมด\t\t'+totalCn.toFixed(2)+'\tหยวน\n'+
// 						'\t\tRate\t\t\t'+rate1.toFixed(4)+'\n'+
// 						'\t\tยอดเงิน\t\t\t'+totalTh.toFixed(2)+'\tบาท\n'+
// 						'ยอดเงินที่ต้องทำการคืนลูกค้าเป็นยอด\t\t'+totalTh.toFixed(2)+'\tบาท\n\n'+
// 						'แต่ทางร้านที่จีนได้มีการขอเรียกเก็บค่าขนส่งเพิ่มเป็น\t'+diffTran.toFixed(2)+'\tหยวน\n'+
// 						'\t\tRate\t\t\t'+rate1.toFixed(4)+'\n'+
// 						'\t\tยอดเงิน\t\t\t'+(diffTran*rate1).toFixed(2)+'\tบาท\n\n'+
// 						'  ดังนั้นทางเราจะทำการคืนเงินเข้าสู่ระบบเป็นยอด\t'+(totalTh-(diffTran*rate1)).toFixed(2)+'\tบาท\n';
// 				}
				
// 				else if (diffTran==0 && diffQuan>0) { //case 1
// 					var cpp = document.getElementById('cpp-'+opid).value;
// 					var totalCn = cpp*diffQuan;
// 					var totalTh = totalCn*rate1;
// 					subject = 'แจ้งคืนเงินเลขที่ออเดอร์ '+orderNum;
// 					content = '\t\tสินค้าที่ลูกค้าสั่งจำนวน\t'+quan1+'\tชิ้น\n'+
// 						'\t\tสินค้าที่สั่งได้\t\t'+quan+'\tชิ้น\n'+
// 						'\t\tขาด\t\t\t'+diffQuan+'\tชิ้น\n'+
// 						'\t\tยอดค่าสินค้า\t\t'+totalCn.toFixed(2)+'\tหยวน\n'+
// 						'\t\tยอดรวมทั้งหมด\t\t'+totalCn.toFixed(2)+'\tหยวน\n'+
// 						'\t\tRate\t\t\t'+rate1.toFixed(4)+'\n'+
// 						'\t\tยอดเงิน\t\t\t'+totalTh.toFixed(2)+'\tบาท\n\n'+
// 						'ยอดเงินได้ถูกคืนเข้าสู่ระบบแล้ว ลูกค้านำยอดเงินที่ได้คืนไปตัดชำระบิลอื่นๆได้ค่ะ';
// 				}
// 				else if (diffTran>0 && diffQuan==0) { //case 2
// 					subject = 'แจ้งโอนเงินเพิ่มเลขที่ออเดอร์ '+orderNum;
// 					content = 'ร้านที่จีนได้มีการขอเรียกเก็บค่าขนส่งเพิ่มเป็น\t'+diffTran.toFixed(2)+'\tหยวน\n'+
// 						'\t\tRate\t\t\t'+rate1.toFixed(4)+'\n'+
// 						'\t\tยอดเงิน\t\t\t'+(diffTran*rate1).toFixed(2)+'\tบาท\n\n'+
// 						'ถ้าลูกค้าต้องการ หรือไม่ต้องการให้สั่งสินค้ารายการนี้ กรุณาแจ้งกลับ email นี้เพื่อทางเราจะได้ดำเนินการต่อไป\n\n'+
// 						'\tถ้าลูกค้าต้องการให้สั่งสินค้ารายการนี้ทางเราจะนำยอด บาท ไปเรียกเก็บในค่าขนส่งรอบ 2\n\n'+
// 						'\tถ้าลูกค้าไม่ต้องการให้สั่งทางเราจะทำการคืนเงินเข้าสู่ระบบเพื่อที่ลูกค้าสามารถนำยอดเงินนี้ไปตัดชำระบิลอื่นได้ค่ะ';
// 				}
				
// 				document.getElementById('email-quan1').value = quan1;
// 				document.getElementById('email-quan').value = quan;
// 				document.getElementById('email-sumqaun').value = diffQuan;
				
// 				document.getElementById('email-tran1').value = tran1.toFixed(2);
// 				document.getElementById('email-tran').value = tran.toFixed(2);
// 				document.getElementById('email-sumtran').value = diffTran.toFixed(2);
				
// 				document.getElementById('email-cpp1').value = cpp1.toFixed(2);
// 				document.getElementById('email-cpp').value = cpp.toFixed(2);
// 				document.getElementById('email-sumcpp').value = diffPrice.toFixed(2);

// 				document.getElementById('email-subject').value = subject;
// 				document.getElementById('email-content').value = content;

// 				document.getElementById('emailBox').style.visibility = 'visible';
// 		}
// 		else {
// 				document.getElementById('emailBox').style.visibility = 'hidden';
// 		}
// }

function emailBox(opid){
		emailBoxOn = !emailBoxOn;
		if(emailBoxOn) {
				//init----------------------------------------
				var subject = '';
				var content = '';
				document.getElementById('email-oid').value   	= orderId;
				document.getElementById('email-ono').value   	= orderNum;
				document.getElementById('email-opid').value  	= opid;
				document.getElementById('email-cid').value   	= customer_id;
				document.getElementById('email-cmail').value    = cmail;

				subject = 'สรุปการสั่งซื้อ เลขที่ Order '+orderNum;
				document.getElementById('email-subject').value = subject;
				document.getElementById('email-content').value = content;

				document.getElementById('emailBox').style.visibility = 'visible';
		}
		else {
				document.getElementById('emailBox').style.visibility = 'hidden';
		}
}

var refundOn = false;
function refund(opid){
		document.getElementById('addBox').style.visibility = 'hidden';
		document.getElementById('backRefundBox').style.visibility = 'hidden';
		document.getElementById('emailBox').style.visibility = 'hidden';
		document.getElementById('emailLog').style.visibility = 'hidden';
		refundOn = !refundOn;
		if(refundOn){
				var refund = parseFloat(document.getElementById('refund-'+opid).textContent.replace(/,/g, ''));
				var ret = document.getElementById('ret-'+opid).value;
				if (ret!=1) {
					alert('รายการนี้เป็นสถานะคืนเงินแล้ว');
					refundOn = !refundOn;
					return;
				}
				if (refund>0) {
					var ordered  = parseInt(document.getElementById('quan1-'+opid).textContent.replace(/,/g, ''));
					//var received = document.getElementById('curr_received-'+opid).value;
					var received = parseInt(document.getElementById('quan-'+opid).value);
					if (isNaN(received)) received=0;
					//var price    = document.getElementById('curr_price-'+opid).value;
					var price1 = parseFloat(document.getElementById('cpp1-'+opid).textContent.replace(/,/g, ''));
					var price = parseFloat(document.getElementById('cpp-'+opid).value);
					if (isNaN(price)) price=0;
					var rate     = rate1;
					var tranPaid = parseFloat(numberify(document.getElementById('tran1-'+opid).textContent.replace(/,/g, '')));
					//var tranCn   = document.getElementById('curr_tran-'+opid).value;
					var tranCn   = parseFloat(document.getElementById('bTran-'+opid).value); 
					if (isNaN(tranCn)) tranCn=0;

					//validte quan with quan1
					if (received>ordered) {
						alert("จำนวนสินค้าที่สั่งได้ต้องไม่เกินจำนวนที่ลูกค้าสั่ง");
						document.getElementById('quan-'+opid).focus();
						refundOn = !refundOn;
						return;		//exit
					}

					//validate price with first_price
					if (Number(price)>Number(price1)) {
						alert("ราคาหลังร้านต้องไม่เกินราคาต่อชิ้นที่ลูกค้าสั่ง");
						document.getElementById('cpp-'+opid).focus();
						refundOn = !refundOn;
						return;		//exit
					}

					document.getElementById('refundBox').style.visibility = 'visible';
					
					//cal
					var diffQuan = ordered-received
					var diffPrice = price1-price;
					var diffTran = tranPaid-tranCn;

					//show data
					document.getElementById('ref-quan1').value = ordered;
					document.getElementById('ref-quan').value = received;
					document.getElementById('ref-sumquan').value = diffQuan;
					document.getElementById('ref-cpp1').value = numWithCom(price1);
					document.getElementById('ref-cpp').value = numWithCom(price);
					document.getElementById('ref-sumcpp').value = numWithCom(diffPrice);
					document.getElementById('ref-tran1').value = numWithCom(tranPaid);
					document.getElementById('ref-tran').value = numWithCom(tranCn);
					document.getElementById('ref-sumtran').value = numWithCom(diffTran);

					var total1 = parseFloat(document.getElementById('tAmountTh1-'+opid).textContent.replace(/,/g, ''));
					var total = parseFloat(document.getElementById('totalTh-'+opid).textContent.replace(/,/g, ''));
					// if (diffPrice==0) {
					// 		var grandPrice = price1;
					// }
					// else {
					// 		var grandPrice = diffPrice;
					// }
					// var grandVal = (diffQuan*(grandPrice))+(diffTran);
					var grandVal = total1-total;
					if (grandVal<0) {
							var desc = 'ต้องเรียกเก็บเงินเพิ่ม';
					}
					else {
							var desc = 'ต้องคืนเงินลูกค้า';
					}
					document.getElementById('ref-grandQuan').textContent = diffQuan;
					document.getElementById('ref-desc').textContent = 'ค่าสินค้า'+desc;
					document.getElementById('ref-grandVal').textContent = numWithCom(grandVal/rate);
					document.getElementById('ref-grandRate').textContent = numWithCom(rate); 
					document.getElementById('ref-grandDesc').textContent = desc;
					document.getElementById('ref-grandSum').textContent = numWithCom(grandVal);


					$.ajax({
						type: 'GET',
						url: './utility/getRefundData.php?opid='+opid, 
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
										for (var running in result['data']) {
											var length = result['data'][running].length;
											if (length==0) continue;
											document.getElementById('ref-remark').textContent = result['data'][running]['remark_tha'];
											if (result['data'][running]['return_status']==null) {
												document.getElementById('ref-status').textContent = 'กำลังดำเนินการ';
											}
											else if (result['data'][running]['return_status']==1){
												document.getElementById('ref-status').textContent = 'คืนแล้ว';	
											}
											else if (result['data'][running]['return_status']==2){
												document.getElementById('ref-status').textContent = 'ยกเลิก';	
											}
										}
								}
						},
						error: function(exception) {
								alert('Exception: '+exception);
						}
					});

					//prepare data-----------------------------------------------------------------
					document.getElementById('ref-oid').value = orderId;
					document.getElementById('ref-opid').value = opid;
					document.getElementById('ref-cid').value = customer_id;
					document.getElementById('tmp-ordered').value = ordered;
					document.getElementById('tmp-received').value = received;
					document.getElementById('tmp-missed').value = diffQuan;
					document.getElementById('tmp-price').value = price;
					document.getElementById('tmp-totalCn').value = (grandVal/rate);
					document.getElementById('tmp-rate').value = rate;
					document.getElementById('tmp-total').value = grandVal;
					document.getElementById('tmp-tran').value = tranCn;
					document.getElementById('tmp-price1').value = price1;

					//check total amount
					if (diffTran<0) {
					 		$('#ref-sumtran').css('color', 'red');
					 		$('#ref-sumtran').css('color', 'red');
					} 
					else {
					 		$('#ref-sumtran').css('color', 'black');
					 		$('#ref-sumtran').css('color', 'black');
					}
				}
				else {
					alert('ไม่มียอดที่ต้องคืนเงิน');
					refundOn = !refundOn;
				}
		}
		else {
				document.getElementById('refundBox').style.visibility = 'hidden';
		}
}

function countArrayInObject(result) {
		var count = 0;
		for (var tkno in result) {
			    count++;
		}
		return count-1;
}

		// var addOn = false;
		// function add(opid) {
		// 		document.getElementById('refundBox').style.visibility = 'hidden';
		// 		document.getElementById('backRefundBox').style.visibility = 'hidden';
		// 		document.getElementById('emailBox').style.visibility = 'hidden';
		// 		document.getElementById('emailLog').style.visibility = 'hidden';
		// 		addOn = !addOn;
		// 		if(addOn){
		// 			document.getElementById('addBox').style.visibility = 'visible';
		// 			//get tracking
		// 			var allTrack = document.getElementById('curr_trck-'+opid).value;
		// 			for(var i = 0; i < 10; i++) {
		// 					var id = 'tracking'+(i+1)
  //  							document.getElementById(id).value = "";
		// 			}
		// 			if (allTrack!="") {
		// 				var track = allTrack.split(',');
		// 				for(var i = 0; i < track.length; i++) {
		// 						var id = 'tracking'+(i+1)
  //  								document.getElementById(id).value = track[i];
		// 				}
		// 			}
		// 			document.getElementById('oid').value = orderId;
		// 			document.getElementById('opid').value = opid;
		// 			document.getElementById('tracking_curr').value = document.getElementById('curr_trck-'+opid).value;
		// 		}else{
		// 			document.getElementById('addBox').style.visibility = 'hidden';
		// 		}
		// }

		var addOn = false;
		// function add(opid) {
		// 		addOn = !addOn;
		// 		if(addOn){
		// 				//get tracking
		// 				var allTrack = document.getElementById('ref-'+opid).value;
		// 				//set blank
		// 				for(var i = 0; i < 10; i++) {
		// 						var id = 'tracking'+(i+1)
	 //   							document.getElementById(id).value = "";
		// 				}
		// 				//set list
		// 				if (allTrack!="") {
		// 					var track = allTrack.split(',');
		// 					for(var i = 0; i < track.length; i++) {
		// 							var id = 'tracking'+(i+1)
	 //   								document.getElementById(id).value = track[i];
		// 					}
		// 				}
		// 				//opid tmp
		// 				document.getElementById('tmpopid').value = opid;
		// 				document.getElementById('addBox').style.visibility = 'visible';
		// 		} 
		// 		else{
		// 				//clear opid tmp
		// 				document.getElementById('tmpopid').value = '';
		// 				document.getElementById('addBox').style.visibility = 'hidden';
		// 		}
		// }
		
		//for shop taobao and trakcing
		function add(shopid) {
				addOn = !addOn;
				if(addOn){
						//get tracking
						var allTrack = document.getElementById('ref-'+shopid).value;
						var allCom = document.getElementById('com-'+shopid).value;

						//set blank
						for(var i = 0; i < 10; i++) {
								var id = 'tracking'+(i+1)
	   							document.getElementById(id).value = "";

	   							var companyid = 'company'+(i+1)
	   							document.getElementById(companyid).value= "";
						}
						//set list company
						if (allCom!="") {
							var com = allCom.split(',');
							for(var i = 0; i < com.length; i++) {
									var id = 'company'+(i+1)
	   								document.getElementById(id).value = com[i];
							}
						}
						//set list tracking
						if (allTrack!="") {
							var track = allTrack.split(',');
							for(var i = 0; i < track.length; i++) {
									var id = 'tracking'+(i+1)
	   								document.getElementById(id).value = track[i];
							}
						}
						//shopid tmp
						document.getElementById('tmpopid').value = shopid;
						document.getElementById('addBox').style.visibility = 'visible';
				} 
				else{
						//clear shopid tmp
						document.getElementById('tmpopid').value = '';
						document.getElementById('addBox').style.visibility = 'hidden';
				}
		}

		// function back(opid) {
		// 		addOn = !addOn;
		// 		trckTotal = '';
		// 		for(var i = 0; i < 10; i++) {
		// 				var id = 'tracking'+(i+1)
	 //   					trck = document.getElementById(id).value;
	 //   					if (trck!='') {
	 //   							if (trckTotal!='') trckTotal = trckTotal + ',' +trck;
	 //   							else trckTotal = trck;
	 //   					}
		// 		}
		// 		//set all tracking
		// 		document.getElementById('ref-'+document.getElementById('tmpopid').value).value = trckTotal;

		// 		//clear opid tmp
		// 		document.getElementById('tmpopid').value = '';
		// 		document.getElementById('addBox').style.visibility = 'hidden';
		// }

		//for shop taobao and trakcing
		function back(shopid) {
				addOn = !addOn;
				comTotal = '';
				for(var i = 0; i < 10; i++) {
						var id = 'company'+(i+1)
	   					com = document.getElementById(id).value;
	   					if (com!='') {
	   							if (comTotal!='') comTotal = comTotal + ',' +com;
	   							else comTotal = com;
	   					}
				}				

				//set all company
				document.getElementById('com-'+document.getElementById('tmpopid').value).value = comTotal;

				trckTotal = '';
				for(var i = 0; i < 10; i++) {
						var id = 'tracking'+(i+1)
	   					trck = document.getElementById(id).value;
	   					if (trck!='') {
	   							if (trckTotal!='') trckTotal = trckTotal + ',' +trck;
	   							else trckTotal = trck;
	   					}
				}
				//set all tracking
				document.getElementById('ref-'+document.getElementById('tmpopid').value).value = trckTotal;
				//set tracking
				document.getElementById('com-'+document.getElementById('tmpopid').value).value = document.getElementById('company1').value;
				
				//clear opid tmp
				document.getElementById('tmpopid').value = '';
				document.getElementById('addBox').style.visibility = 'hidden';
		}

		function exportProduct(oid){
				window.open('product_excel.php?order_id='+oid,'_blank');
		}

		function allReturn(oid) {
				var r = confirm("ต้องการคืนเงินให้สินค้าทุกชิ้น ใช่หรือไม่");
				if (r == false) {
				    return 0;
				}
				
				var data = {};
				var totalReturn = 0;
				for(var i=0;i<_save.length;i++) {
						var opid = _save[i];
						//console.log(opid);
						var quan1  = parseInt($('#quan1-'+opid).text().replace(/,/g, ''));
						var quan = parseInt($('#quan-'+opid).val());
						if (isNaN(quan)) quan=0;
						var price1 = parseFloat($('#cpp1-'+opid).text().replace(/,/g, ''));
						var price = parseFloat($('#cpp-'+opid).val());
						if (isNaN(price)) price=0;
						var rate     = rate1;
						var tran1 = parseFloat(numberify($('#tran1-'+opid).text().replace(/,/g, '')));
						var tran   = parseFloat($('#bTran-'+opid).val()); 
						if (isNaN(tran)) tran=0;

						var ret = $('#ret-'+opid).val();
						var returnBaht = parseFloat($('#refund-'+opid).text().replace(/,/g, ''));
						if ((returnBaht>0) && (ret!=2)) {
								data[opid] = {
										'returnBaht': returnBaht,
										'quan1': quan1,
										'quan': quan,
										'price1': price1,
										'price': price,
										'tran1': tran1,
										'tran': tran,
										'rate': rate
								}
								totalReturn+=returnBaht;		
						}  
				}
				console.log(Object.keys(data).length);
				if (Object.keys(data).length>0) {
						$("#loading").css('visibility', 'visible');
		                $.ajax({
		                        type: 'POST',
		                        url: './utility/allReturn.php',
		                        data: {'data': JSON.stringify(data), 'cid': customer_id, 'oid': oid, 'totalReturn': totalReturn}, 
		                        dataType: 'json',
		                        success: function(result) {
		                                alert('คืนเงินสำเร็จ');
		                                $("#loading").css('visibility', 'hidden');
		                                location.reload();
		                                //console.log(result);
		                        },
		                        error: function(exception) {
		                                alert('Exception: '+exception);
		                                $("#loading").css('visibility', 'hidden');
		                                //console.log(exception);
		                        }
		           		});
            	}
            	else {
            			alert('ไม่พบสินค้าที่ต้องคืนเงิน');
            	}
		}