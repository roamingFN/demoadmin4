function sendEmail(tid) {
  //get data
  var tno = document.getElementById(tid+'tno').textContent;
  var cname = document.getElementById(tid+'cname').textContent;
  var cmail = document.getElementById(tid+'cmail').value;
  var total = document.getElementById(tid+'amount').value;
  var date = document.getElementById(tid+'date').textContent;
  var acnum = document.getElementById(tid+'acn').textContent;

  //build data
  var data = {};
  data['tid'] = tid;
  data['tno'] = tno;
  data['cname'] = cname;
  data['cmail'] = cmail;
  data['total'] = total;
  data['date'] = date;
  data['acnum'] = acnum;
  data['cid'] = document.getElementById(tid+'customer').value;

  var xhr = new XMLHttpRequest();
  xhr.open('POST','email_cancel.php',true);
  xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
  	if(xhr.readyState==4 && xhr.status==200){
  		if(xhr.responseText=='success'){
          	alert("ส่งอีเมลล์เรียบร้อยแล้ว");
              window.location = 'topup.php';
          } else{
                  alert(xhr.responseText);
       	}
  	}
  };
  xhr.send('data='+JSON.stringify(data));
}

function save(tid){                          
  var status = document.getElementById(tid+'status').options[document.getElementById(tid+'status').selectedIndex].value;
  var remarkc = "";
                                               
  if(status==2) {
    remarkc = prompt("กรุณาใส่เหตุผลที่ต้องการยกเลิก");
  }
  if (remarkc==null) return;
                  
  var data = {};
  data[tid] = {
    'status':status,
      'remarkc':remarkc
  };
  //alert(data[tid].status+data[tid].remarkc);
                  
  var xhr = new XMLHttpRequest();
  xhr.open('POST','save_topup.php',true);
  xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if(xhr.readyState==4 && xhr.status==200){
      if(xhr.responseText=='success'){
            alert("บันทึกข้อมูลเรียบร้อยแล้ว");
              window.location = 'topup.php';
          } else{
                  //alert('กรุณาใส่ข้อมูลให้ถูกต้องค่ะ!');
                  alert(xhr.responseText);
        }
  }
  };
  xhr.send('data='+JSON.stringify(data));
}

function reverseStatus(tid) {
  $.ajax({
    type: 'POST',
    url: './utility/reverseStatus.php',
    dataType: 'html',
    data: {
      tid: tid
    },
    success: function(result) {
      alert(result);
      window.location.href="topup.php";
    },
    error: function(exception) {
      alert('Exception: '+exception);
    }
  });
}

      var addOn = false;
      function add(){
          document.getElementById('editBox').style.visibility = 'hidden';
          document.getElementById('searchBox').style.visibility = 'hidden';
          document.getElementById('imgBox').style.visibility = 'hidden';
          document.getElementById('cancelBox').style.visibility = 'hidden';
          addOn = !addOn;
          if(addOn){
              document.getElementById('addBox').style.visibility = 'visible';
          }
          else{
              document.getElementById('addBox').style.visibility = 'hidden';
          }
      }

      var editOn = false;
      function edit(tid){
          document.getElementById('addBox').style.visibility = 'hidden';
          document.getElementById('searchBox').style.visibility = 'hidden';
          document.getElementById('imgBox').style.visibility = 'hidden';
          document.getElementById('cancelBox').style.visibility = 'hidden';
          editOn = !editOn;
          if(editOn){
              document.getElementById('editBox').style.visibility = 'visible';
              document.getElementById('e-tid').value = document.getElementById(tid).value;
              document.getElementById('e-cid-'+document.getElementById(tid+'customer').value).selected = true;
              document.getElementById('e-datetime').value = document.getElementById(tid+'datetime').value;
              document.getElementById('e-amount').value = document.getElementById(tid+'amount').value;                                        
              document.getElementById('e-method').value = document.getElementById(tid+'branch').textContent;
              document.getElementById('e-note').value = document.getElementById(tid+'remark').textContent;
              document.getElementById('e-bid-'+document.getElementById(tid+'bid').value).selected = true;
          } 
          else{
              document.getElementById('editBox').style.visibility = 'hidden';
          }
      }

      var searchOn = false;
      function searchBox(){
          document.getElementById('addBox').style.visibility = 'hidden';
          document.getElementById('editBox').style.visibility = 'hidden';
          document.getElementById('imgBox').style.visibility = 'hidden';
          document.getElementById('cancelBox').style.visibility = 'hidden';
          searchOn = !searchOn;
          if(searchOn){
              document.getElementById('searchBox').style.visibility = 'visible';
          }
          else{
              document.getElementById('searchBox').style.visibility = 'hidden';
          }
      }
      
      var picOn = false;
      function showPic(src){
          document.getElementById('addBox').style.visibility = 'hidden';
          document.getElementById('editBox').style.visibility = 'hidden';
          document.getElementById('searchBox').style.visibility = 'hidden';
          document.getElementById('cancelBox').style.visibility = 'hidden';
          picOn = !picOn;
          if(picOn){
              document.getElementById('imgBox').style.visibility = 'visible';
              document.getElementById("picSlip").src = 'demo/'+src;
          } 
          else{
              document.getElementById('imgBox').style.visibility = 'hidden';
          }
          //alert(document.getElementById("picSlip").src);
      }

      var cancelOn = false;
      function cancelBox(id) {
          document.getElementById('addBox').style.visibility = 'hidden';
          document.getElementById('editBox').style.visibility = 'hidden';
          document.getElementById('imgBox').style.visibility = 'hidden';
          document.getElementById('searchBox').style.visibility = 'hidden';
          cancelOn = !cancelOn;
          if(cancelOn){
            document.getElementById('c-tid').value = id;
            document.getElementById('c-cid').value = document.getElementById(id+'customer').value;
            document.getElementById('cc-cname').textContent = document.getElementById(id+'cname').textContent;
            document.getElementById('cc-amount').textContent = numWithCom(document.getElementById(id+'amount').value);
            document.getElementById('cc-tno').textContent = document.getElementById(id+'tno').textContent;
            document.getElementById('cc-date').textContent = document.getElementById(id+'date').textContent;
            document.getElementById('cc-time').textContent = document.getElementById(id+'time').textContent;
            document.getElementById('cc-acname').textContent = document.getElementById(id+'act').textContent;
            document.getElementById('cc-acnum').textContent = document.getElementById(id+'acn').textContent;
            document.getElementById('cc-bname').textContent = document.getElementById(id+'bname').textContent;
            document.getElementById('cc-by').textContent = document.getElementById(id+'branch').textContent;
            //document.getElementById('cc-rmkcc').textContent = document.getElementById(id+'remarkc').textContent;
            document.getElementById('cc-rmkcc').value = '';

            //tmp
            document.getElementById('tmp-tno').value = document.getElementById('cc-tno').textContent;
            document.getElementById('tmp-cmail').value = document.getElementById(id+'cmail').value;
            document.getElementById('tmp-cname').value = document.getElementById('cc-cname').textContent;
            document.getElementById('tmp-amount').value = document.getElementById(id+'amount').value;
            document.getElementById('tmp-date').value = document.getElementById(id+'date').textContent;
            document.getElementById('tmp-acnum').value = document.getElementById(id+'acn').textContent;
            document.getElementById('tmp-ccode').value = document.getElementById(id+'ccode').value;
            //console.log(document.getElementById('tmp-cname').value);

            document.getElementById('cancelBox').style.visibility = 'visible';
          }else{
            document.getElementById('c-tid').value = '';
            document.getElementById('cancelBox').style.visibility = 'hidden';
          }
      }
      
      function numWithCom(x) {
          return Number(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }

      function exportExcel() {
          window.open('topup_excel.php','_blank');
      }