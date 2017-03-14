    function save(wid){
    		//get data                          
            var status = document.getElementById(wid+'statusFront').options[document.getElementById(wid+'statusFront').selectedIndex].value;
            var cid = document.getElementById(wid+'customer').value;
            var amount = document.getElementById(wid+'amount').value;
            var datetime = document.getElementById(wid+'datetime').value;
            var wno = document.getElementById(wid+'wno').value;
            var mode = 0;
            var current_stat = document.getElementById(wid+'status').value;
    		
    		//check mode
    		//0 status not changed
    		//1 waiting -> complete
    		//2 complete -> waiting or complete -> cancel
    		//3 waiting -> cancel or cancel -> waiting
            if (current_stat==status) {
            		mode = 0;
            }
            else if (current_stat==0&&status==1) {
            		mode = 1;
            }
            else if ((current_stat==1&&status==0)||(current_stat==1&&status==2)) {
            		mode = 2;
            }
            else if ((current_stat==0&&status==2)||(current_stat==2&&status==0)) {
            		mode = 3;
            }
            
            //check mode 0 
            if (mode==0){
            		alert("ไม่มีการเปลี่ยนสถานะรายการ");
            		return 0;
            }

            //set data
            var data = {};
            data[wid] = {
                    'status':status,
                    'cid':cid,
                    'amount':amount,
                    'datetime':datetime,
                    'wno':wno,
                    'mode':mode
            };
            //alert(data[tid].status+data[tid].remarkc);
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST','save_withdraw.php',true);
            xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
            xhr.onreadystatechange = function(){
                    if(xhr.readyState==4 && xhr.status==200){
                            if(xhr.responseText=='success'){
                                    alert("บันทึกข้อมูลเรียบร้อยแล้ว");
                                    window.location = 'withdraw.php';
                            } else{
                                    //alert('กรุณาใส่ข้อมูลให้ถูกต้องค่ะ!');
                                    alert(xhr.responseText);
                            }
                    }
            };
            xhr.send('data='+JSON.stringify(data));
    }
                
	var addOn = false;
	function add(){
    		document.getElementById('editBox').style.visibility = 'hidden';
    		document.getElementById('searchBox').style.visibility = 'hidden';
                            addOn = !addOn;
    		if(addOn){
    			document.getElementById('addBox').style.visibility = 'visible';
    		}else{
    			document.getElementById('addBox').style.visibility = 'hidden';
    		}
	}
	
	var editOn = false;
	function edit(wid){
    		document.getElementById('addBox').style.visibility = 'hidden';
    		document.getElementById('searchBox').style.visibility = 'hidden';
            editOn = !editOn;
    		if(editOn){
    			document.getElementById('editBox').style.visibility = 'visible';
                document.getElementById('e-wid').value = document.getElementById(wid).value;
    			document.getElementById('e-cid-'+document.getElementById(wid+'customer').value).selected = true;
    			document.getElementById('e-datetime').value = document.getElementById(wid+'datetime').value;
    			document.getElementById('e-amount').value = document.getElementById(wid+'amount').value;
    			document.getElementById('e-stat-'+document.getElementById(wid+'status').value).selected = true;
    			document.getElementById('e-comment').value = document.getElementById(wid+'comment').textContent;
    			document.getElementById('e-bid-'+document.getElementById(wid+'bid').value).selected = true;
    		} else{
    			document.getElementById('editBox').style.visibility = 'hidden';
    		}
	}

	var searchOn = false;
	function searchBox(){
    		document.getElementById('addBox').style.visibility = 'hidden';
    		document.getElementById('editBox').style.visibility = 'hidden';
    		searchOn = !searchOn;
    		if(searchOn){
    			document.getElementById('searchBox').style.visibility = 'visible';
    		}else{
    			document.getElementById('searchBox').style.visibility = 'hidden';
    		}
	}

	function exportExcel(){
		  window.open('withdraw_excel.php','_blank');
    }

    function del(wid){
            var r = confirm("ต้องการลบข้อมูลใช่หรือไม่");
            if (r == true) {
                    $("#loading").css('visibility', 'visible');
                    var data = { 'wid' :wid};
                    $.ajax({
                            type: 'POST',
                            url: './utility/delete.php',
                            data: data, 
                            dataType: 'json',
                            success: function(result) {
                                    alert('ลบข้อมูลสำเร็จ');
                                    $("#loading").css('visibility', 'hidden');
                                    location.reload();
                            },
                            error: function(exception) {
                                    alert('Exception: '+exception);
                                    $("#loading").css('visibility', 'hidden');
                            }
                    });
            }
    }