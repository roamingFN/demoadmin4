	function validTopup(cashid,topup_no) {
			if (topup_no.length!=12) {
				alert('เลขที่เติมเงินไม่ถูกต้อง');
				return false;
			}
			//check exited
			if (topups[topup_no]) {	
			}
			else {
				alert('ไม่พบเลขที่เติมเงิน');
				return false;
			}
			
			// //check amount
			// var topup_amount = topups[topup_no].topup;
			// var cash_amount = Number(document.getElementById(cashid+'num').textContent.replace(/,/,''));
			// if (topup_amount!=cash_amount) {
			// 	alert('ยอดเงินไม่ตรงกัน');
			// 	return false;
			// }

			// //check date
			// var topup_date = topups[topup_no].date;
			// topup_date = topup_date.substring(8,10)+'-'+topup_date.substring(5,7)+'-'+topup_date.substring(0,4);
			// var cash_date = document.getElementById(cashid+'date').value;
			// if (topup_date!=cash_date) {
			// 	alert('วันที่ไม่ตรงกัน');
			// 	return false;
			// }

			// //check bank
			// var topup_bank = topups[topup_no].bid;
			// var cash_bank = Number(document.getElementById(cashid+'bank').getAttribute('bank'));
			// if (topup_bank!=cash_bank) {
			// 	alert('ชื่อธนาคารไม่ตรงกัน');
			// 	return false;
			// }

			//check topup status
			var status = topups[topup_no].status;
			if (status!=0) {
				alert('สถานะรายการไม่ถูกต้อง');
				return false;
			}
			console.log(topups[topup_no].date);
			return true;		
	}

	function save2(cashid) {
			//get cash status
			var status = document.getElementById(cashid+'status1').value;
			//get topup number
			var topup_no = document.getElementById(cashid+'topup').value.trim();

			if (topup_no=='') {
					alert('กรุณากรอกข้อมูลเลขที่เติมเงิน');
					return 0;
			}

			if (status==0) {
					if (validTopup(cashid,topup_no)) {
						//get data
						var topup_id = topups[topup_no].tid;
						var topup_amount = topups[topup_no].topup;
						var customer_id = topups[topup_no].cid;
						var cash_status = 1; //complete
						var topup_date = topups[topup_no].date;
						var data = {};
						data[cashid] = {
							'topup_id':topup_id,
							'topup_no':topup_no,
							'topup_amount':topup_amount,
							'customer_id':customer_id,
							'cash_status':cash_status,
							'cashid':cashid,
							'topup_date':topup_date.substring(0,4)+'-'+topup_date.substring(5,7)+'-'+topup_date.substring(8,10),
							'topup_bid':topups[topup_no].bid
						};

						$("#loading").css('visibility', 'visible');
						var xhr = new XMLHttpRequest();
						xhr.open('POST','save_payment3.php',true);
						xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
						xhr.onreadystatechange = function(){
							if(xhr.readyState==4 && xhr.status==200){
									if(xhr.responseText=='success'){
											alert("บันทึกข้อมูลเรียบร้อยแล้ว");
											location.reload();
									} else{
											alert(xhr.responseText);
									}
							}
							$("#loading").css('visibility', 'hidden');
						};
						xhr.send('data='+JSON.stringify(data));
					} //end update data

			} //end status 0

			else if (status==1) {
					//not change
					var old_topup_id = document.getElementById(cashid+'tu').value;
					var old_topup_no = document.getElementById(cashid+'tnum').value;
					if(topups_in[topup_no]){
							if (old_topup_id==topups_in[topup_no].tid){
								alert('not change');
								return 0;
							}
					}

					//validate
					if (validTopup(cashid,topup_no)) {
						//get new data
						var new_topup_no = topup_no;
						var new_topup_id = topups[topup_no].tid;
						var new_topup_amount = topups[topup_no].topup;
						var new_customer_id = topups[topup_no].cid;
						
						//get old data
						var old_topup_amount = topups_in[old_topups_no].otopup;
						var old_customer_id = topups_in[old_topup_id].cid;

						var cash_status = 1; //complete
						
						var data = {};
						data[cashid] = {
							'new_topup_id': new_topup_id,
							'new_topup_no': new_topup_no,
							'new_topup_amount': new_topup_amount,
							'new_customer_id': new_customer_id,
							'old_topup_id': old_topup_id,
							'old_topup_no': old_topup_no,
							'old_topup_amount': old_topup_amount,
							'old_customer_id': old_customer_id,
							'cash_status':cash_status
						};

						$("#loading").css('visibility', 'visible');
						var xhr = new XMLHttpRequest();
						xhr.open('POST','save_payment4.php',true);
						xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
						xhr.onreadystatechange = function(){
							if(xhr.readyState==4 && xhr.status==200){
									if(xhr.responseText=='success'){
											alert("บันทึกข้อมูลเรียบร้อยแล้ว");
											location.reload();
									} else{
											alert(xhr.responseText);
									}
							}
							$("#loading").css('visibility', 'hidden');
						};
						xhr.send('data='+JSON.stringify(data));
					} //end update data
			}
	}
	            
	var editOn = false;
	function edit(crn){
		document.getElementById('searchBox').style.visibility = 'hidden';
		editOn = !editOn;
		if(editOn){
			document.getElementById('editBox').style.visibility = 'visible';
			document.getElementById('e-crn').value = document.getElementById(crn).textContent;
			document.getElementById('e-customer').value = document.getElementById(crn+'customer').textContent;
			document.getElementById('e-date').value = document.getElementById(crn+'date').textContent;
			document.getElementById('e-time').value = document.getElementById(crn+'time').value;
			document.getElementById('e-amount').value = document.getElementById(crn+'amount').value;                                        
			document.getElementById('e-branch').value = document.getElementById(crn+'branch').textContent;
			document.getElementById('e-remark').value = document.getElementById(crn+'remark').textContent;
			document.getElementById('e-bid-'+document.getElementById(crn+'bid').value).selected = true;
			//document.getElementById('e-acn').value = document.getElementById(crn+'acn').textContent;
			document.getElementById('e-cbid').value = document.getElementById(crn+'cbid').value;
			//document.getElementById('e-uid-'+document.getElementById(crn+'uid').textContent).selected = true;
			//document.getElementById('e-remarkc').value = document.getElementById(crn+'remarkc').textContent;
			//document.getElementById('e-status-'+document.getElementById(crn+'status').value).selected = true;
		}else{
			document.getElementById('editBox').style.visibility = 'hidden';
		}
	}
	
	var searchOn = false;
	function searchBox(){
		document.getElementById('editBox').style.visibility = 'hidden';
		searchOn = !searchOn;
		if(searchOn){
			document.getElementById('searchBox').style.visibility = 'visible';
		}else{
			document.getElementById('searchBox').style.visibility = 'hidden';
		}
	}
	
	function exportExcel(){
		window.open('payment_excel.php','_blank');
	}

	function cancel(cid) {
		$.ajax({
			type: 'POST',
			url: './function/cancel.php',
			dataType: 'html',
			data: {
			  cid: cid
			},
			success: function(result) {
			  alert(result);
			  window.location.href="payment.php";
			},
			error: function(exception) {
			  alert('Exception: '+exception);
			}
		});
	}