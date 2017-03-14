function initIndex() {
		searchOn = false;
		searchByPicOn = false;
		$('.search-select').chosen();
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
				$('#searchBox').css("visibility", "visible");
		} else{
				$('#searchBox').css("visibility", "hidden");
		}
}

function showSearchByPic() {
		searchByPicOn = !searchByPicOn;
		if(searchByPicOn){
				$('#ono').val('');
				$('#pType').val(0).trigger('chosen:updated');
				$('#containerResult').css("display", "none");
				$('#searchByPicBox').css("visibility", "visible");
		} else{
				$('#searchByPicBox').css("visibility", "hidden");
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

function clearEmailTable() {
		var rowCount = document.getElementById('email-table').rows.length;
		for (i=0; i<rowCount-1; i++) {
				document.getElementById("email-table").deleteRow(-1);
		}
}

function showEmailLog(content) {
		alert(content);
}

function ctrlTextArea() {
		$("textarea").keydown(function(e) {
			    if(e.keyCode === 9) { // tab was pressed
			        // get caret position/selection
			        var start = this.selectionStart;
			            end = this.selectionEnd;

			        var $this = $(this);

			        // set textarea value to: text before caret + tab + text after caret
			        $this.val($this.val().substring(0, start)
			                    + "\t"
			                    + $this.val().substring(end));

			        // put caret at right position again
			        this.selectionStart = this.selectionEnd = start + 1;

			        // prevent the focus lose
			        return false;
			    }
		});
}

function exportExcel() {
		window.open('./utility/exportExcel.php','_blank');
}

function toCurrency(number) {
		return Number(number).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function setColor(oid){
		//tabls's body
		$('#'+oid+' tbody tr').each(function (){
				var msQuan = parseInt($(this).find("td").eq(10).html().replace(/,/g, ''));
				var msBaht = parseFloat($(this).find("td").eq(13).html().replace(/,/g, ''));

				if (msQuan<0) $(this).find("td").eq(10).css('color','red');
				if (msBaht<0) $(this).find("td").eq(13).css('color','red');
		});

		//table's footer
		var msQuanTotal = parseInt($('#'+oid+' tfoot tr').find('td').eq(10).html().replace(/,/g, ''));
		var msBahtTotal = parseFloat($('#'+oid+' tfoot tr').find('td').eq(13).html().replace(/,/g, ''));
		if (msQuanTotal<0) $('#'+oid+' tfoot tr').find("td").eq(10).css('color','red');
		if (msBahtTotal<0) $('#'+oid+' tfoot tr').find("td").eq(13).css('color','red');
}

function toDetail(tno,oid){
		window.location.href='./detail.php?ptno='+tno+'&oid='+oid;
}

function searchTracking () {
		$('#searchButton').prop('disabled', true);
		var resultDiv = $('#searchResult');
		resultDiv.text('');
		var resultStr = '';
		var condition = '';
		var ono = $('#ono').val();
		var pType = $('#pType').val();

		if ((ono=='') && (pType==0)) {
				$('#searchButton').prop('disabled', false); 
				return;
		}

		$.ajax({
			type: 'GET',
			url: './utility/getTracking.php?ono='+ono+'&pType='+pType, 
			dataType: 'json',
			success: function(result) {
					var len = countArrayInObject(result['data']);
					if ((len==0) && (result['error'] == '')) {
							resultDiv.text('ไม่พบ Tracking');
					}
					else if ((len==0) && (result['error'] != '')) {
							resultDiv.text(result['error']);
					}
					else {						
							appendResult(result,resultDiv);
					}
					$("#search-loading").css('display', 'none');
					$('#searchButton').prop('disabled', false);
			},
			error: function(exception) {
					alert('Exception: '+exception);
					$("#search-loading").css('display', 'none');
					$('#searchButton').prop('disabled', false);
			}
		});	
		$('#containerResult').css('display', 'flex');
		$('#search-loading').css('display', 'flex');
}

function appendResult(result,resultDiv) {
		var resultStr = '';
		for (var tkno in result['data']) {
				resultStr = '<p><a href="index.php?tracking='+tkno+'"><b>Tracking number '+tkno+'</b></a></p>';
				var length = result['data'][tkno].length
				if (length==0) continue;
				for (var i=0; i<length; i++) {
						resultStr = resultStr+'<img style="width: 10%;padding-right: 2px;" src="'+result['data'][tkno][i]['product_img']+'">';
				}
				resultDiv.append('<div>'+resultStr+'</div>');
		}
		return resultStr;
}

function countArrayInObject(result) {
		var count = 0;
		for (var tkno in result) {
			    count++;
		}
		return count-1;
}