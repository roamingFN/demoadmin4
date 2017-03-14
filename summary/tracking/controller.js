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