function AJAX() {
	this.request = this.getXMLHttpRequest();
	this.agent = this.getUserAgent();
}

AJAX.prototype.sendXMLRequest = function(method, url, xmlreq, type, success_handle, fail_handle) {
var httpreq = this.getXMLHttpRequest();
	httpreq.open(method, url , true); 
	httpreq.setRequestHeader("Content-type", type);
	// this.httpreq.setRequestHeader("Content-type", "text/xml"); 
	//httpreq.setRequestHeader("Content-length", xmlreq.length); 
	//httpreq.setRequestHeader("Connection", "close"); 
    httpreq.send(xmlreq);
        
	httpreq.onreadystatechange = function() {

		if(httpreq.readyState === 4) { 

                    var result = httpreq.responseText;
                    result = result.replace(/\n/g, "");
                    result = result.replace(/\r/g, "");                    
			switch (httpreq.status) {
			case 200 :
                                
				success_handle(result);					
				break;
			case 500 :
				// HTTP - 500 Server error
				fail_handle(result);
				break;
			default : 
				fail_handle('HTTP '+httpreq.status+' - '+result);
				break;
			}
		}
	};
    
};

AJAX.prototype.getXMLHttpRequest = function() {

var progIDs = ['MSXML2.XMLHTTP.6.0', 'MSXML2.XMLHTTP.3.0', 'Microsoft.XMLHTTP'];
	
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		// ...otherwise, use the ActiveX control for IE5.x and IE6.
        for (i = 0; i < progIDs.length; i++) {

            try {
                var xmlHttp = new ActiveXObject(progIDs[i]);
                return xmlHttp;
            }
            catch (ex) {
            }
        }

	}

	/* if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		return new XMLHttpRequest();
	}
	if (window.ActiveXObject) {
	// code for IE6, IE5
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
	return null; */
};

AJAX.prototype.getUserAgent = function() {
var userAgent = "";

	if (/MSIE/g.test(navigator.userAgent)) {
		userAgent = "MSIE";
	} else if (/Firefox/g.test(navigator.userAgent)) {
		userAgent = "Firefox";
	} else if (/Opera/g.test(navigator.userAgent)) {
		userAgent = "Opera";
	} else if (/Safari/g.test(navigator.userAgent)) {
		userAgent = "Safari";
	} else {
		userAgent = "Unknow";
	}

	return(userAgent);
};
