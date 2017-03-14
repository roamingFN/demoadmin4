const oid = getUrlParameter('oid');
if (typeof(oid) == 'undefined') window.location.href = './index.php';

function loadPage(num) {
		$.ajax({
				url: getUrl(num), 
				type: 'get',
				data: {oid: oid},
				success: function (data) {
			  			$('#content').html(data);
			  	},
			  	error: function () {
			  			console.log("load page error");
			  	}
		});
}

function getUrl(num) {
		var url;
		if (num==1) {
				url = './tracking/tracking.php';
				$('#menu1').css('background', '#4CAF50');
				$('#menu2').css('background', '#333');
				$('#menu3').css('background', '#333');
		}
		else if (num==2) {
				url = 'payment.php';
				$('#menu2').css('background', '#4CAF50');
				$('#menu1').css('background', '#333');
				$('#menu3').css('background', '#333');
		}
		else if (num==3) {
				url = 'message.php';
				$('#menu3').css('background', '#4CAF50');
				$('#menu1').css('background', '#333');
				$('#menu2').css('background', '#333');
		}
		return url;
}

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1));
    var sURLVariables = sPageURL.split('&');
    var sParameterName;
    var i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

function initDetail() {
		refundOn = false;
		backRefundOn = false;
		emailOn = false;
		emailLogOn = false;
		packageOn = false;

		loadPage(1);
}