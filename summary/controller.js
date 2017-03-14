function initIndex() {
		searchOn = false;
}

function setDatePicker() {
		$(function() {
               	$( ".datepicker" ).datepicker({
               			dateFormat: "dd-mm-yy"
                     	//timeFormat: "HH:mm:ss",
                    	//showSecond:true
				});        
		});
}

function showSearchBox() {
		searchOn = !searchOn;
		if(searchOn){
				document.getElementById('searchBox').style.visibility = 'visible';
		} else{
				document.getElementById('searchBox').style.visibility = 'hidden';
		}
}

function showRefundBox(opid){
		refundOn = !refundOn;
		if(refundOn) {
				var return_baht = parseFloat($('#'+opid).closest('tr').find('td:eq(14)').text().replace(/,/g, ''));
				var rtStat = parseInt($('#rtStat-'+opid).val());
				if (rtStat==2) {
						alert('รายการนี้เป็นสถานะคืนเงินแล้ว');
						refundOn = !refundOn;
						return;
				}

				if (return_baht>0) {
						var rate = parseFloat($('#rate').text().replace(/,/g, ''));
						var bsQuan = parseInt($('#'+opid).closest('tr').find('td').eq(3).text());
						var bsPrice = parseFloat($('#'+opid).closest('tr').find('td').eq(4).text().replace(/,/g, ''));
						var bsTran = parseFloat($('#'+opid).closest('tr').find('td').eq(5).text().replace(/,/g, ''));
						var rcQuan = parseInt($('#'+opid).closest('tr').find('td').eq(9).text());
						var missing = parseInt($('#'+opid).closest('tr').find('td').eq(10).text());
						
						document.getElementById('refx-bsQuan').textContent = bsQuan;
						document.getElementById('refx-rcQuan').textContent = rcQuan;
						document.getElementById('refx-missing').textContent = missing;
						document.getElementById('refx-bsPrice').textContent = toCurrency(bsPrice);
						document.getElementById('refx-return_yuan').textContent = toCurrency((return_baht/rate));
						document.getElementById('refx-rate').textContent = rate.toFixed(4);
						document.getElementById('refx-bsRate').textContent = rate.toFixed(4);
						document.getElementById('refx-return_baht').textContent = toCurrency(return_baht);
						document.getElementById('refx-bsTran_yuan').textContent = toCurrency(bsTran);
						document.getElementById('refx-bsTran_baht').textContent = toCurrency((bsTran*rate));
						document.getElementById('refx-total_baht').textContent = toCurrency(return_baht+(bsTran*rate));

						document.getElementById('ref-oid').value = document.getElementById('oid').value;
						document.getElementById('ref-opid').value = opid;
						document.getElementById('ref-cid').value = document.getElementById('cid').value;
						document.getElementById('ref-bsQuan').value = bsQuan;
						document.getElementById('ref-rcQuan').value = rcQuan;
						document.getElementById('ref-missing').value = missing;
						document.getElementById('ref-bsPrice').value = bsPrice;
						document.getElementById('ref-return_yuan').value = (return_baht/rate);
						document.getElementById('ref-return_baht').value = return_baht;
						document.getElementById('ref-rate').value = rate;
						document.getElementById('ref-total_baht').value = return_baht+(bsTran*rate);
				}
				else {
						alert('ไม่มียอดที่ต้องคืน');
						refundOn = !refundOn;
						return;
				}

				document.getElementById('refundBox').style.visibility = 'visible';
		}
		else{
				document.getElementById('refundBox').style.visibility = 'hidden';
		}
}

function showBackRefundBox(opid){
		backRefundOn = !backRefundOn;
		if(backRefundOn){
				var retStat = parseInt($('#rtStat-'+opid).val());
				if (retStat!=2) {
						alert('รายการนี้ยังไม่ได้คืนเงิน');
						backRefundOn = !backRefundOn;
						return;
				}

				var return_baht = parseFloat($('#'+opid).closest('tr').find('td:eq(14)').text().replace(/,/g, ''));
				document.getElementById('brefx-return_baht').textContent = toCurrency(return_baht);

				document.getElementById('bref-oid').value = document.getElementById('oid').value;
				document.getElementById('bref-opid').value = opid;
				document.getElementById('bref-cid').value = document.getElementById('cid').value;


				document.getElementById('backRefundBox').style.visibility = 'visible';
		}
		else{
				document.getElementById('backRefundBox').style.visibility = 'hidden';
		}
}

function showEmailBox(opid){
		emailOn = !emailOn;
		if(emailOn){
				var retStat = parseInt($('#rtStat-'+opid).val());
				if (retStat!=2) {
						alert('รายการนี้ยังไม่ได้คืนเงิน');
						backRefundOn = !backRefundOn;
						return;
				}

				document.getElementById('emailBox').style.visibility = 'visible';
				//init----------------------------------------
				var subject = '';
				var content = '';
				document.getElementById('email-oid').value = document.getElementById('oid').value;
				document.getElementById('email-ono').value = document.getElementById('ono').value;
				document.getElementById('email-opid').value = opid;
				document.getElementById('email-cid').value = document.getElementById('cid').value;
				
				document.getElementById('emailBox').style.visibility = 'visible';
		}
		else{
				document.getElementById('emailBox').style.visibility = 'hidden';
		}
}

function showEmailLogBox(opid){
		emailLogOn = !emailLogOn;
		if(emailLogOn){
				var data = {}; 
				data['opid'] = opid;

				var result = true;
				var xhr = new XMLHttpRequest();
				xhr.open('POST','utility/getEmailLog.php',true);
				xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
				xhr.onreadystatechange = function(){
					if(xhr.readyState==4 && xhr.status==200) {
							var objs = JSON.parse(xhr.responseText);
							var len  = Object.keys(objs).length;
							for (i=0; i<len; i++) {
									var table = document.getElementById("email-table");
								    var row = table.insertRow(-1);
								    var cell = row.insertCell(-1);
								    cell.innerHTML = i+1;
								    var cell = row.insertCell(-1);
								    var date = new Date(objs[i]['date']);
								    cell.innerHTML = date.getDate()+'/'+date.getMonth()+'/'+date.getFullYear()
									var cell = row.insertCell(-1);
									var content = (objs[i]['content']);
								    cell.innerHTML = '<a class="green" style="cursor:pointer;" onclick="showEmailLog(\''+content+'\')">'+objs[i]['subject']+'</a>';
							}
					}
				};
				xhr.send('data='+JSON.stringify(data));

				document.getElementById('emailLogBox').style.visibility = 'visible';
		}
		else{
				clearEmailTable();
				document.getElementById('emailLogBox').style.visibility = 'hidden';
		}
}

function showPackageBox(oid,opid,pid) {
		packageOn = !packageOn;
		if(packageOn) {
				var data = {}; 
				data['oid'] = oid;
				data['opid'] = opid;
				data['pid'] = pid;
				$('#package-content').load('./dialog/packageContent.php',{'data' : data});
				document.getElementById('packageBox').style.visibility = 'visible';
		}
		else {
				document.getElementById('packageBox').style.visibility = 'hidden';
		}	
}

function clearEmailTable() {
		var rowCount = document.getElementById('email-table').rows.length;
		for (i=0; i<rowCount-1; i++) {
				document.getElementById("email-table").deleteRow(-1);
		}
}

function showEmailLog(content) {
		alert(content);
}

function exportExcel() {
		window.open('./utility/exportExcel.php','_self');
}

function toCurrency(number) {
		return Number(number).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function toDetail(oid){
		window.location.href='./detail.php?oid='+oid;
}