		function initIndex() {
				searchOn = false;
				cancelOn = false;
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

		function searchBox() {
				searchOn = !searchOn;
				if(searchOn){
						document.getElementById('searchBox').style.visibility = 'visible';
				} else{
						document.getElementById('searchBox').style.visibility = 'hidden';
				}
		}

		function showCancelBox(oid) {
				cancelOn = !cancelOn;
				if(cancelOn){
						document.getElementById('c-ono').textContent = document.getElementById(oid).textContent;
						document.getElementById('c-cname').textContent = document.getElementById(oid+'customer').textContent;
						document.getElementById('c-dt').textContent = document.getElementById(oid+'datetime').textContent;
						document.getElementById('c-amount').textContent = document.getElementById(oid+'price').textContent;
						document.getElementById('c-stat').textContent = document.getElementById(oid+'status').textContent;
						document.getElementById('c-remarkc').value = '';
						
						document.getElementById('c-oid').value = oid;
						document.getElementById('c-cid').value = document.getElementById(oid+'cid').value;
						document.getElementById('cc-ono').value = document.getElementById(oid).textContent;
						document.getElementById('cc-amount').value = document.getElementById('amount-'+oid).value;
						document.getElementById('cc-dt').value = document.getElementById('dt-'+oid).value;

						document.getElementById('cancelBox').style.visibility = 'visible';
				} else{
						document.getElementById('cancelBox').style.visibility = 'hidden';
				}
		}

		function exportExcel(){
				window.open('order_excel.php','_blank');
		}

		function exportProduct(oid){
				window.open('product_excel.php?order_id='+oid,'_blank');
		}

		function toProduct(oid){
				location.href="product.php?order_id=" + oid;
		}

		function numWithCom(x) {
				return Number(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}
		

		function checkStat1(opid){		
			if (document.getElementById('stt1-'+opid).checked) {
				document.getElementById('rem-'+opid).value = "0";
				document.getElementById('rem-'+opid).disabled = true;
				$("#rem-"+opid).attr('disabled', true).trigger('chosen:updated');
				document.getElementById('stt2-'+opid).checked = false;
				// if (document.getElementById('keepQuan-'+opid).value!=0) {
				// 	document.getElementById('quan-'+opid).value = document.getElementById('keepQuan-'+opid).value;
				// 	document.getElementById('tran-'+opid).value = document.getElementById('keepTran-'+opid).value;
				// }
				if (document.getElementById('quan-'+opid).value==0) {
						document.getElementById('quan-'+opid).value = parseInt(document.getElementById('quan1-'+opid).value);
				}
				document.getElementById('quan-'+opid).disabled = false;
				document.getElementById('cpp-'+opid).disabled = false;
				document.getElementById('tran-'+opid).disabled = false;
				calc1(opid);			
			}
			else {
				//document.getElementById('rem-'+opid).value = "0";
				//document.getElementById('rem-'+opid).disabled = false;	
			}
		}
		
		function checkStat2(opid) {
			$("#rem-"+opid).attr('disabled', false).trigger('chosen:updated');
			document.getElementById('rem-'+opid).disabled = false;
			if (document.getElementById('stt2-'+opid).checked) {
				document.getElementById('stt1-'+opid).checked = false;
				document.getElementById('quan-'+opid).value = 0;
				document.getElementById('tran-'+opid).value = 0;
				document.getElementById('quan-'+opid).disabled = true;
				document.getElementById('cpp-'+opid).value = (parseFloat(document.getElementById('cpp1-'+opid).textContent.replace(/,/g, ''))).toFixed(2);
				document.getElementById('cpp-'+opid).disabled = true;
				document.getElementById('tran-'+opid).disabled = true;
				calc1(opid);
			}
			else {
				document.getElementById('rem-'+opid).value = "0";
				document.getElementById('rem-'+opid).disabled = true;
				document.getElementById('quan-'+opid).disabled = false;
				document.getElementById('cpp-'+opid).disabled = false;
				document.getElementById('tran-'+opid).disabled = false;
				$("#rem-"+opid).attr('disabled', true).trigger('chosen:updated');
			}
		}