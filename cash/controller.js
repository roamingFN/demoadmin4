function init() {
		cancelBoxOn = false;
}

function showCancelBox(cid,crn) {
		cancelBoxOn = !cancelBoxOn;
		if(cancelBoxOn){
				document.getElementById('c-cno').textContent = document.getElementById(crn).textContent;
				document.getElementById('c-cname').textContent = document.getElementById(crn+'customer').textContent;
				document.getElementById('c-dt').textContent = document.getElementById(crn+'date').textContent;
				document.getElementById('c-amount').textContent = numWithCom(document.getElementById(crn+'amount').value);
				document.getElementById('c-acn').textContent = document.getElementById(crn+'bname').textContent+' '+document.getElementById(crn+'bacn').textContent;
				document.getElementById('c-remarkc').value = '';

				document.getElementById('c-cid').value = cid;
				document.getElementById('cancelBox').style.visibility = 'visible';
		} else{
				document.getElementById('cancelBox').style.visibility = 'hidden';
		}
}

function numWithCom(x) {
			return Number(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}