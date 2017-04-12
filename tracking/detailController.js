function calTotalQuan(ptid,get) {
		var quan = parseInt(document.getElementById('quan-'+ptid).textContent);
		var received = parseInt(document.getElementById('received-'+ptid).textContent);
		document.getElementById('missing-'+ptid).textContent = quan-(received+parseInt(get));
		//calAll();
}

function calAll() {
		missing = 0;
		total = 0;
		$('#quan-table tbody tr').each(function (){
				missing += parseInt($(this).find("td").eq(2).html().replace(/,/g, ''));
				total += parseInt($(this).find("td").eq(6).html().replace(/,/g, ''));
		});

		$('#quan-table tfoot tr').find("td").eq(2).text(missing);
		$('#quan-table tfoot tr').find("td").eq(6).text(total);
}

function init() {
		//variable
		amountDialogOn = false;
		$(document).ready(function() {
	    		$(".filter").keydown(function (e) {
				        // Allow: backspace, delete, tab, escape, enter and .
				        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 116]) !== -1 ||
				             // Allow: Ctrl+A
				            (e.keyCode == 65 && e.ctrlKey === true) ||
				             // Allow: Ctrl+C
				            (e.keyCode == 67 && e.ctrlKey === true) ||
				             // Allow: Ctrl+X
				            (e.keyCode == 88 && e.ctrlKey === true) ||
				             // Allow: home, end, left, right
				            (e.keyCode >= 35 && e.keyCode <= 39)) {
				                // let it happen, don't do anything
				                return;
				        	}
				        // Ensure that it is a number and stop the keypress
				        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				            	e.preventDefault();
				        }
		    	});
		});

		$(document).ready(function() {
				$(".filter").click(function (){
						if ($(this).val()==0) {
							$(this).val('');
						}
				});
		});

		$(document).ready(function() {
	    		$(".filter").keyup(function () {
	    				if ($(this).val()!='') {
	    						var ptid = $(this).closest('tr').attr('id');
	    						var get = $(this).val();
				        		calTotalQuan(ptid,get);
				       	}
		    	});
		});

		$(document).ready(function() {
	    		$(".m3").keydown(function (e) {
	    				// Allow: backspace, delete, tab, escape, enter and . and f5
				        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 116, 190]) !== -1 ||
				             // Allow: Ctrl+A
				            (e.keyCode == 65 && e.ctrlKey === true) ||
				             // Allow: Ctrl+C
				            (e.keyCode == 67 && e.ctrlKey === true) ||
				             // Allow: Ctrl+X
				            (e.keyCode == 88 && e.ctrlKey === true) ||
				             // Allow: home, end, left, right
				            (e.keyCode >= 35 && e.keyCode <= 39)) {
				                // let it happen, don't do anything
				                return;
				        	}
				        // Ensure that it is a number and stop the keypress
				        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				            	e.preventDefault();
				        }
		    	});
		});

		$(document).ready(function() {
				$(".m3").click(function (){
						if ($(this).val()==0) {
							$(this).val('');
						}
				});
		});

		$(document).ready(function() {
	    		$(".m3").keyup(function () {
	    				if ($(this).val()!='') {
	    						if ($(this).attr('name')!='rate') {
			    						var result = findRate($('#m3-table tbody tr').find("input").eq(4).is(':checked')? 2: 1);
										$('#m3-table tbody tr').find("input").eq(6).val(formatRate(result));	//show rate
								}
	    						calM3();
				       	}
		    	});
		});

		$("input:checkbox").click(function(){
			    var group = "input:checkbox[name='"+$(this).prop("name")+"']";
			    $(group).prop("checked",false);
			    $(this).prop("checked",true);

			    if ($(this).attr('name')!='stat') {
			    	var result = findRate($('#m3-table tbody tr').find("input").eq(4).is(':checked')? 2: 1);
					$('#m3-table tbody tr').find("input").eq(6).val(formatRate(result));	//show rate
			    	calM3();
				}
		});
}

function findRate(mode) {
		var result = 0;
		var rate = 0;

		if (mode==1) {
				rate = parseFloat($("#rateWeight").val());
		}
		else {
				rate = parseFloat($("#rateM3").val());
		}
		//user rate
		if (rate!=0) {
				result = rate;
		}
		else {	
				//auto rate
				for (id in _rate) {
						if ((mode==1) && (_rate[id].rate_type==1)) {
								weight = parseFloat($('#m3-table tbody tr').find("input").eq(3).val());
								if (isNaN(weight)) weight=0; 
								if ((weight>=_rate[id].begincal) && (weight<=_rate[id].endcal)) {
										result = parseFloat(_rate[id].rate_amount);
								}
						}
						else if ((mode==2) && (_rate[id].rate_type==2)) {
								m3 = parseFloat($('#m3-table tbody tr').find("td").eq(3).text());
								if (isNaN(m3)) m3=0;
								if ((m3>=_rate[id].begincal) && (m3<=_rate[id].endcal)) {
										//console.log(weight + ' ' + _rate[id].begincal + ' ' + _rate[id].endcal + ' ' + _rate[id].rate_amount);
										result = parseFloat(_rate[id].rate_amount);
								}
						}
				}

				//if not found rate
				if (result==0) {
						result = $('#m3-table tbody tr').find("input").eq(6).val();
						//console.log(result);
				}
		}
		return result;
}

function calM3() {
		var weight = $('#m3-table tbody tr').find('input').eq(0).val();
		var length = $('#m3-table tbody tr').find('input').eq(1).val();
		var height = $('#m3-table tbody tr').find('input').eq(2).val();

		var m3 = (weight*length*height)/1000000;
		// 12/04/2017 if m3 < 0.0000, set it to 0.0001
		if (m3<0.0001) {
			m3 = 0.0001;
		}
		$('#m3-table tbody tr').find('td').eq(3).text(m3.toFixed(4));
		calTran();
		calAvg();
}

function calTran() {
		if ($('#m3-table tbody tr').find('input').eq(4).is(':checked')) {
				var rate = $('#m3-table tbody tr').find('input').eq(6).val().replace(/,/g, '');
				var tran = $('#m3-table tbody tr').find('td').eq(3).text() * rate;
				$('#m3-table tbody tr').find('input').eq(7).val(numWithCom(tran));
				//console.log(tran.toFixed(2));
		}
		if ($('#m3-table tbody tr').find('input').eq(5).is(':checked')) {
				var rate = $('#m3-table tbody tr').find('input').eq(6).val().replace(/,/g, '');
				var tran = $('#m3-table tbody tr').find('input').eq(3).val() * rate;
				$('#m3-table tbody tr').find('input').eq(7).val(numWithCom(tran));
		}
}

function calAvg() {
		var avg = 0;
		var m3 = parseFloat($('#m3-table tbody tr').find('td').eq(3).text());
		if (isNaN(m3)) m3=0;
		var total = parseFloat($('#m3-table tbody tr').find('input').eq(7).val());
		//console.log(total/m3);
		if (m3!=0) {
				avg = total / m3;
		}
		//console.log(total + ' ' + m3 + ' ' + avg);
		$('#m3-table tbody tr').find('td').eq(9).text(numWithCom(avg));
}

function save() {
		var saveFlg = 1;
		var data = {};
		$('#quan-table tbody tr').each(function () {
				var key = $(this).attr('id');
				//var rec = parseInt($(this).find("td").eq(6).html().replace(/,/g, ''));
				var rec = parseInt($(this).find("input").eq(0).val());
				//var remark = document.getElementById('ptRemark-'+key).value;
				var opid = document.getElementById('opid-'+key).value;
				var quan = document.getElementById('quan-'+key).textContent;

				var missing = parseInt($(this).find('td').eq(5).text());
				if (missing<0) {
						alert('จำนวนรับเพิ่มเกินจำนวนที่สั่งได้');
						document.getElementById('get-'+key).focus();
						saveFlg = 0;
						return;
				}

				if (isNaN(rec)) {
						alert('จำนวนต้องไม่เป็นค่าว่าง');
						document.getElementById('get-'+key).focus();
						saveFlg = 0;
						return;
				}
				
				var rateWeight = parseFloat($('#rateWeight').val());
				var rateM3 = parseFloat($('#rateM3').val());

				var m3Table = $('#m3-table tbody tr');
				var width = m3Table.find("input").eq(0).val();
				var length = m3Table.find("input").eq(1).val();
				var height = m3Table.find("input").eq(2).val();
				var m3 = m3Table.find("td").eq(3).text();
				var weight = m3Table.find("input").eq(3).val();
				var ptype = m3Table.find("select").eq(0).val()==''? 0 : m3Table.find("select").eq(0).val();
				var type = m3Table.find("input").eq(4).is(':checked')? 2: 1;
				var rate = m3Table.find("input").eq(6).val().replace(/,/g ,'');
				if (type==1) {
						rateWeight = rate;
				}
				else {
						rateM3 = rate;
				}
				var stat = m3Table.find('input').eq(8).is(':checked')? 0: 1;
				var total = m3Table.find("input").eq(7).val().replace(/,/g ,'');
				var remark = $('.remark').find('textarea').eq(0).val();

				//22/12/2016	add condition to check width/length/height/weight/rate/total
				if (width==0) {
						alert('กรุณากรอกความกว้าง');
						m3Table.find("input").eq(0).focus();
						saveFlg = 0;
						return false;
				}
				if (length==0) {
						alert('กรุณากรอกความยาว');
						m3Table.find("input").eq(1).focus();
						saveFlg = 0;
						return false;
				}
				if (height==0) {
						alert('กรุณากรอกความสูง');
						m3Table.find("input").eq(2).focus();
						saveFlg = 0;
						return false;
				}
				if (weight==0) {
						alert('กรุณากรอกน้ำหนัก');
						m3Table.find("input").eq(3).focus();
						saveFlg = 0;
						return false;
				}
				if (rate==0) {
						alert('กรุณากรอกค่า Rate');
						m3Table.find("input").eq(6).focus();
						saveFlg = 0;
						return false;
				}
				if (total==0) {
						alert('กรุณากรอกค่าขนส่ง');
						m3Table.find("input").eq(7).focus();
						saveFlg = 0;
						return false;
				}
				
				data[key] = {
						'rec': rec,
						'width' : width,
						'length' : length,
						'height' : height,
						'm3' : m3,
						'weight' : weight,
						'ptype' : ptype,
						'type' : type,
						'rate' : rate,
						'stat' : stat,
						'total' : total,
						'opid' : opid,
						'remark' : remark,
						'rateWeight': rateWeight,
						'rateM3': rateM3
				};
		});
		data['oid'] = $('.remark').attr('id');
		// data['oremark'] = $('.remark').find('textarea').eq(0).val();
		//console.log(data);
		if (saveFlg) {
				var result = true;
				var xhr = new XMLHttpRequest();
				xhr.open('POST','./utility/save.php',true);
				xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
				xhr.onreadystatechange = function(){
					if(xhr.readyState==4 && xhr.status==200){
						$('#loading').css('visibility', 'hidden');
						if(xhr.responseText=='success'){
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
}

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

function numWithCom(x) {
		//console.log(Number(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
		return Number(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function formatRate(val) {
	if (typeof(val)=='undefined') return 0;
	if (val=='') return 0;

	var result = '';
	var tmp = val.toString().split('.');
	var int = tmp[0].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	
	if (typeof(tmp[1])=='undefined') {
		decimal = '0000';
	}
	else {
		decimal = tmp[1];
		var length = decimal.length;
		if (length==1) {
			decimal = decimal + '000';
		}
		else if (length==2) {
			decimal = decimal + '00';
		}
		else if (length==3) {
			decimal = decimal + '0';
		}
	}

	result = int + '.' + decimal;
	return result;
}