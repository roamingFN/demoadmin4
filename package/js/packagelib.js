/*
// Send json string to php
var jsondata;
var flickr = {'action': 'Flickr', 'get':'getPublicPhotos'};
var data = JSON.stringify(flickr);

var xhr = new XMLHttpRequest();
xhr.open("POST", "../phpincl/apiConnect.php", !0);
xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
xhr.send(data);
xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
        // in case we reply back from server
        jsondata = JSON.parse(xhr.responseText);
        console.log(jsondata);
    }
}

*/

function Package() {
	this.url = "service";
}

Package.prototype.request = function(url, method, data, on_success, on_error) {
var httpreq = new AJAX();
var jsonreq = (data === null) ? '' : JSON.stringify(data);
var type = "application/json";
	httpreq.sendXMLRequest(method, url, jsonreq, type, on_success, on_error);    
	
};