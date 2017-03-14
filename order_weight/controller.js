var amountDialogOn = false;
var searchDialogOn = false;

function showAmountDialog (tid) {
		amountDialogOn = !amountDialogOn;
		if(amountDialogOn) {
				var sum = 0;
				var data = {}; 
				data['tid'] = tid;
				var result = true;
				var xhr = new XMLHttpRequest();
				xhr.open('POST','./utility/getAmount.php',true);
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

function showSearchDialog () {
		searchDialogOn = !searchDialogOn;
		if(searchDialogOn) {
				$('#ono').val('');
				$('#pType').val(0).trigger('chosen:updated');
				$('#containerResult').css("display", "none");
				$('#searchDialog').css("visibility", "visible");
		}
		else {
				$('#searchButton').prop('disabled', false);
				$('#searchDialog').css("visibility", "hidden");
		}
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