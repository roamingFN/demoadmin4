String.prototype.escape = function() {
var str = this;
	return(escape(str));
};
String.prototype.replaceAll = function(find, replace) {
var str = this;
    return str.replace(new RegExp(find, 'g'), replace);
};
String.prototype.trim = function() {
var str = this;
    return str.replace(/\s/g, "");
};

String.prototype.isNumber = function() {
var str = this;
    
    if (isNaN(str)) {
        return(false);
    } else {
        return(true);
    }
};

String.prototype.parseDigit = function() {
var str = this;

	return(parseFloat(str));

};

String.prototype.isThaiChar = function() {
var str = this;
var len = str.length;

	for (i=0; i<len; i++) {
		var ch = str.charAt(i);
		var code = ch.charCodeAt(0);

		if ((code >= 3585) || (code <= 3673)) {
			return(true);
		}
	}

	return(false);
};

String.prototype.isFormat = function(number, decimal) {
var value = this;
var n;

	if (value.length === 0) return true;

	n = value.replace(/,/g,"").split(".");

	var rgx;
	rgx = /(\D+)/g; 

	if (rgx.test(n[0].replace(/,/g,""))) {
		// alert("Please insert integer in field");
		return(false);
	}

	if (n.length > 1) {
		if (rgx.test(n[1].replace(/,/g,""))) {
			// alert("Please insert integer in field");
			return(false);
		}
	}

	/* if (isNaN(parseFloat(value.replace(/,/g,"")))) {		
		alert("Please insert integer in field");
		return(false);
	} */

	if (decimal === 0) {
		if (n.length > 1) {
			return(false);
		}

		if (n[0].length > number) {
			return(false);
		}
		return(true);
	} else {
		if (n[0].length > number) {
			return(false);
		}

		if (n.length > 1) {
			if (n[1].length > decimal) {
				return(false);
			}
		}
		return(true);
	}
};

String.prototype.xmlEcapeChar = function() {
var str = this;

	str = str.replace(/\&/g, "&amp;");
	str = str.replace(/</g, "&lt;");
	str = str.replace(/>/g, "&gt;");
	str = str.replace(/\"/g, "&quot;");
	str = str.replace(/\'/g, "&#039;");

	return(str);

};

Date.prototype.getMonthName = function(m) {
    var months = new Array('January','Febuary','March','April',
                        'May','June','July','August',
			'September','October','November','December'); 

    // var m = this.getMonth();
    return(months[m]);
    
};

function formatNumber(str, number, decimal, name) {
// var str = obj.value;

	if (!str.isFormat(number, decimal)) {
		if (decimal !== 0) {
			// alert("\""+name +"\" must be "+(number+decimal)+"N DE"+decimal+" format");                        
                        alert("\""+name+"\" must be a number or decimal point only. ค่าจำนวนเงินสูงสุดจะต้องกรอกเป็นตัวเลขหรือจุดทศนิยมเท่านั้น\"");                        
		} else {
			// alert("\""+name +"\" must be "+number+"N format");
                        alert("\""+name+"\" must be a number or decimal point only. ค่าจำนวนเงินสูงสุดจะต้องกรอกเป็นตัวเลขหรือจุดทศนิยมเท่านั้น\"");                        
		}		
		return(str);
	}

	if (str.length === 0) {
		str = "0";
	}

	str += ''; 
	str = str.replace(/\,/g,"");

	if (isNaN(parseFloat(str))) {
		// alert("Please insert integer in field");
		return(str);
	}

	x = str.split('.'); 

	x1 = x[0]; 

	x2 = '';
	if (x.length > 1) {
		if (decimal > 0) {
			x2 = '.'+padding_right(x[1], '0', decimal);
		} else {
			x2 = '.'+x[1];
		}
	} else {
		if (decimal > 0) {
			i = 0;
			x2 = '.';
			while (i < decimal) {
				x2 += '0';
				i++;
			}
		}
	}

	// x2 = x.length > 1 ? '.' + x[1] : ''; 
	var rgx = /(\d+)(\d{3})/; 

	while (rgx.test(x1)) { 
		x1 = x1.replace(rgx, '$1' + ',' + '$2'); 
	} 
	return x1 + x2; 
}

// left padding s with c to a total of n chars
function padding_left(s, c, n) {
    if (! s || ! c || s.length >= n) {
        return s;
    }

    var max = (n - s.length)/c.length;
    for (var i = 0; i < max; i++) {
        s = c + s;
    }

    return s;
}

// right padding s with c to a total of n chars
function padding_right(s, c, n) {
    if (! s || ! c || s.length >= n) {
        return s;
    }

    var max = (n - s.length)/c.length;
    for (var i = 0; i < max; i++) {
        s += c;
    }

    return s;
}

function GetBrowser()
{
	if (NavCheck('iPhone') || NavCheck('iPod'))
		return 'iPhone';
	else if (NavCheck('iPod'))
		return 'iPod';
	else if (NavCheck('Android'))
		return 'Android';
};

function NavCheck(check)
{
	return navigator.userAgent.indexOf(check) !== -1;
}

amtCheckDigit = function(event) {
var value = document.getElementById(this.id).value;    

	if (window.event) {
		event = window.event;
	}
	
        console.log(event.charCode);
        if ((event.charCode >= 48) && (event.charCode <= 57)) {
            return(true);
        } else if ((event.charCode === 8) || (event.charCode === 46)) {
            return(true);
        } else {
            document.getElementById(this.id).value = value;        
            return(false);
        }

};

checkDigit = function(event) {
var value = document.getElementById(this.id).value;
	// alert(this.id);
	if (window.event) {
		event = window.event;
	}
	
        console.log(event.charCode);
        if ((event.charCode >= 48) && (event.charCode <= 57)) {
            return(true);
        } else {
            document.getElementById(this.id).value = value;        
            return(false);
        }
        
//        
//        if (!event.shiftKey) {
//                    
//            if ((event.keyCode >= 48) && (event.keyCode <= 57)) {
//                    return(true);
//            } else if ((event.keyCode >= 96) && (event.keyCode <= 105)) {                
//                    return(true);
//            } else if ((event.keyCode === 8) || (event.keyCode === 46)) {
//                    return(true);
//            } else if ((event.keyCode === 190)|| (event.keyCode === 110)) {
//
//                    if (value.indexOf('.') !== -1) {
//                        return(false);
//                    } else {
//                        return(true);
//                    }
//
//            } else {
//                    document.getElementById(this.id).value = value;
//                    return(false);
//            }
//        } else {
//            return(false);
//        }
};

function formatDate(d, format) {
var dt = "";
var yy, mm, dd, hh, nn, ss;

	// yy = d.getYear();
	yy = d.getFullYear();
	mm = ((d.getMonth()+1)<10) ? '0'+(d.getMonth()+1) : (d.getMonth()+1);
	dd = ((d.getDate())<10) ? '0'+d.getDate() : d.getDate();

	hh = ((d.getHours())<10) ? '0'+d.getHours() : d.getHours();
	nn = ((d.getMinutes())<10) ? '0'+d.getMinutes() : d.getMinutes();
	ss = ((d.getSeconds())<10) ? '0'+d.getSeconds() : d.getSeconds();

	switch (format) {
	case "yyyy-MM-dd hh:mi:ss" :
		dt = yy+"-"+mm+"-"+dd+" "+hh+":"+nn+":"+ss;
		break;
	case "yyyy-MM-dd" :
		dt = yy+"-"+mm+"-"+dd;
		break;
	case "dd-MM-yyyy" :
		dt = dd+"-"+mm+"-"+yy;
		break;
        case "dd/MM/yyyyBC" :
		dt = dd+"/"+mm+"/"+(yy+543);
		break;
        case "dd/MM/yyyy" :
		dt = dd+"/"+mm+"/"+yy;
		break;
	case "hh:mi:ss" :
		dt = hh+":"+nn+":"+ss;
		break;
	case "dd-MM-yyyy hh:mi:ss" :
		dt = dd+"-"+mm+"-"+yy+" "+hh+":"+nn+":"+ss;
		break;
        case "yyyyMMdd" :
                dt = yy+mm+dd;
                break;
        case "yyMMdd" :
		// dt = dd+"-"+mm+"-"+yy+" "+hh+":"+nn+":"+ss;                
                dt = (""+yy).substr(2, 2)+mm+dd;
		break;
	}
 
	return(dt);

}

function getStaleDate(d) {
var curdate = new Date(d);
var st_month = curdate.getMonth() - 6;
var st_year = curdate.getFullYear();
var st_date = curdate.getDate();
var stale_date = new Date(d);

    if (st_month < 0) {
        st_month = st_month + 12;
        st_year = st_year - 1;
    }

    if (st_month === 1) {
        if ((st_year % 4) === 0) {
            st_date =  (st_date > 29) ? 29 : st_date;
        } else {
            st_date =  (st_date > 28) ? 28 : st_date;
        }
    } else {
        switch (st_month) {
            case 3:
            case 5:
            case 8:
            case 10:
              st_date = (st_date > 30) ? 30 : st_date;
              break;
        }
    }

    stale_date.setDate(st_date);
    stale_date.setMonth(st_month);
    stale_date.setYear(st_year);
    return(stale_date);
}

function getFullDate(str, s) {
var d = new Date();   
var a = str.split(s);
    d.setFullYear(a[0], (a[1]-1), a[2]);
    return(d);
}

function getCurrentDate(d, s) {
var dt = "";
var yy, mm, dd, hh, nn, ss;

	// yy = d.getYear();
	yy = d.getFullYear();
	mm = ((d.getMonth()+1)<10) ? '0'+(d.getMonth()+1) : (d.getMonth()+1);
	dd = ((d.getDate())<10) ? '0'+d.getDate() : d.getDate();

	hh = ((d.getHours())<10) ? '0'+d.getHours() : d.getHours();
	nn = ((d.getMinutes())<10) ? '0'+d.getMinutes() : d.getMinutes();
	ss = ((d.getSeconds())<10) ? '0'+d.getSeconds() : d.getSeconds();


	dt = yy+s+mm+s+dd;
	// dt = yy+mm+dd;
	// alert(dt);
	return(dt);
}

function getAbsoluteLeft(obj) {
var parent = obj.offsetParent;
var left = obj.offsetLeft;
	while (parent.tagName.toUpperCase() !== "BODY") {
            
            
		left = left + parent.offsetLeft;
		parent = parent.offsetParent;
                                
                if (parent === null) {
                    return(left);
                } else {
                }
                
	}
	return(left);

}

function getAbsoluteTop(obj) {
var parent = obj.offsetParent;
var top = obj.offsetTop;

	while (parent.tagName.toUpperCase() !== "BODY") {
		top = top + parent.offsetTop;
		parent = parent.offsetParent;
                if (parent === null) return(top);		
	}

	return(top);

}

function on_changepass() {
var w = 320;
var h = 220;
var scroll = "no";
var paramWindow = setParamx(w, h, scroll);

var height = (document.body.offsetHeight > screen.availHeight) ? document.body.offsetHeight : screen.availHeight;

	document.getElementById("filter_screen").style.height = height + "px";
	document.getElementById("filter_screen").style.visibility = "visible";


var d = window.showModalDialog("changepass.jsp", null , paramWindow);

	if (d !== null) {
		if (d[0] === 200) {
			// refresh page
			
			// document.location.href = default_page;
			alert("Your password has changed");
			document.getElementById("filter_screen").style.visibility = "hidden";
		} else if (d[0] === "") {
			// the operation was cancel by user
			document.getElementById("filter_screen").style.visibility = "hidden";
		} else {
			// error
			// login_required(default_page);
			alert(d[1]);
			document.getElementById("filter_screen").style.visibility = "hidden";
		}
	}

}



function login_required(default_page) {
var w = 320;
var h = 220;
var scroll = "no";
var paramWindow = setParamx(w, h, scroll);

var height = (document.body.offsetHeight > screen.availHeight) ? document.body.offsetHeight : screen.availHeight;

	document.getElementById("filter_screen").style.height = height + "px";
	document.getElementById("filter_screen").style.visibility = "visible";


var d = window.showModalDialog("login.jsp", null , paramWindow);

	if (d !== null) {
		if (d[0] === 200) {
			// refresh page
			
			document.location.href = default_page;
		} else if (d[0] === "") {
			// the operation was cancel by user
			document.getElementById("filter_screen").style.visibility = "hidden";
		} else {
			// error
			login_required(default_page);
		}
	}

}

function setParamx(w, h, scroll) {
    var param1="dialogHeight:"+h+"px;dialogWidth:"+w+"px;resizable:no;scroll:"+scroll+";status:no;edge:sunken;help:no;";
    return(param1);
}

function validate_password(user, pass) {
var LOWER = /[a-z]/,
	UPPER = /[A-Z]/,
	DIGIT = /[0-9]/,
	DIGITS = /[0-9].*[0-9]/,
	SPECIAL = /[^a-zA-Z0-9]/,
	SAME = /^(.)\1+$/;

	// alert(user + pass);

		if (!pass || pass.length < 8) {
			// too-short
			document.getElementById("password-meter-message").className = 'password-meter-message-very-weak';
			document.getElementById("password-meter-message").innerHTML = "Very weak";
			document.getElementById("password-meter-bar").className = 'password-meter-very-weak';
			return(true);
		}

		/* if (user && pass.toLowerCase().match(user.toLowerCase())) {
			// too-short
			document.getElementById("password-meter-message").className = 'password-meter-message-very-weak';
			document.getElementById("password-meter-message").innerHTML = "Very weak";
			document.getElementById("password-meter-bar").className = 'password-meter-very-weak';
			return(true);
			// similar-to-username
		} */

		if (SAME.test(pass)) {
			document.getElementById("password-meter-message").className = 'password-meter-message-very-weak';
			document.getElementById("password-meter-message").innerHTML = "Very weak";
			document.getElementById("password-meter-bar").className = 'password-meter-very-weak';
			return(true);
			// very-weak
		}

		var lower = LOWER.test(pass),
			upper = UPPER.test(uncapitalize(pass)),
			digit = DIGIT.test(pass),
			digits = DIGITS.test(pass),
			special = SPECIAL.test(pass);
		
		if (lower && upper && digit || lower && digits || upper && digits || special) {
			document.getElementById("password-meter-message").className = 'password-meter-message-strong';
			document.getElementById("password-meter-message").innerHTML = "Strong";
			document.getElementById("password-meter-bar").className = 'password-meter-strong';
			return(true);
			// strong
		}

		if (lower && upper || lower && digit || upper && digit) {
			document.getElementById("password-meter-message").className = 'password-meter-message-good';
			document.getElementById("password-meter-message").innerHTML = "Good";
			document.getElementById("password-meter-bar").className = 'password-meter-good';
			return(true);
			// good
		}

		document.getElementById("password-meter-message").className = 'password-meter-message-weak';
		document.getElementById("password-meter-message").innerHTML = "Weak";
		document.getElementById("password-meter-bar").className = 'password-meter-weak';
		return(true);
			// weak
}

function uncapitalize(str) {
	return str.substring(0, 1).toLowerCase() + str.substring(1);
}

function s4() {
  return Math.floor((1 + Math.random()) * 0x10000)
             .toString(16)
             .substring(1);
};

function guid() {
  return s4() + "" + s4() + '-' + s4() + '-' + s4() + '-' +
         s4() + '-' + s4() + s4() + s4();
}

function show_confirm_box(title, text, onok) {
var obj = document.getElementById("confirm_box_main");
    document.getElementById("confirm_box_title").innerHTML = title;
    document.getElementById("confirm_box_text").innerHTML = text;

    document.getElementById("confirm_box").style.height = window.innerHeight + "px";
    
    obj.style.top = ((window.innerHeight - obj.offsetHeight)/2 - 100) + "px";
    obj.style.left = (window.innerWidth - obj.offsetWidth)/2 + "px";
    
    
    document.getElementById("confirm_box").style.visibility = "visible";    
    document.getElementById("btnok_confirm_box").onclick = onok;
    document.getElementById("btnok_confirm_box").focus();
    obj = null;
}

function hide_confirm_box(id) {
var obj = document.getElementById(id);

    document.getElementById("confirm_box").style.visibility = "hidden";
    // obj.style.top = window.innerHeight +"px";

    /* obj.style.top = (obj.offsetTop + 50) + "px";
    if (obj.offsetTop > height) {
        obj.style.top = height +"px";
        document.getElementById("form_screen").style.visibility = "hidden";
	clearTimeout(timerId);
    } else {
	timerId = setTimeout("hide_form('"+id+"')", timeInterval);
    } */
    obj = null;
        
}

function hide_continue_box(id) {
var obj = document.getElementById(id);

    document.getElementById("continue_box").style.visibility = "hidden";
    // obj.style.top = window.innerHeight +"px";

    /* obj.style.top = (obj.offsetTop + 50) + "px";
    if (obj.offsetTop > height) {
        obj.style.top = height +"px";
        document.getElementById("form_screen").style.visibility = "hidden";
	clearTimeout(timerId);
    } else {
	timerId = setTimeout("hide_form('"+id+"')", timeInterval);
    } */
    obj = null;
        
}

function show_host_file_data_box(filename) {
var obj = document.getElementById("host_file_data_main");
//var height = (document.body.offsetHeight > screen.availHeight) ? document.body.offsetHeight : screen.availHeight;
//var width = (document.body.offsetWidth > screen.availWidth) ? document.body.offsetWidth : screen.availWidth;
    document.getElementById("host_filename").innerHTML = filename;
    document.getElementById("host_file_data_dialog").style.visibility = "visible";
    
    
    obj = null;    
}

function show_account_remark_box(title, text) {
var obj = document.getElementById("account_remark_box");
//var height = (document.body.offsetHeight > screen.availHeight) ? document.body.offsetHeight : screen.availHeight;
//var width = (document.body.offsetWidth > screen.availWidth) ? document.body.offsetWidth : screen.availWidth;

    document.getElementById("account_remark_text").innerHTML = text;
    document.getElementById("account_remark_box").style.visibility = "visible";
    
    
    obj = null;
    
}

function show_security_log_box() {
var obj = document.getElementById("security_log_box");
//var height = (document.body.offsetHeight > screen.availHeight) ? document.body.offsetHeight : screen.availHeight;
//var width = (document.body.offsetWidth > screen.availWidth) ? document.body.offsetWidth : screen.availWidth;

    document.getElementById("security_log_box").style.visibility = "visible";

    obj = null;
    
}

function show_operation_log_box() {
var obj = document.getElementById("operation_log_box");
//var height = (document.body.offsetHeight > screen.availHeight) ? document.body.offsetHeight : screen.availHeight;
//var width = (document.body.offsetWidth > screen.availWidth) ? document.body.offsetWidth : screen.availWidth;

    document.getElementById("operation_log_box").style.visibility = "visible";

    obj = null;
    
}

function show_message_box(title, text) {
var obj = document.getElementById("message_box_main");
//var height = (document.body.offsetHeight > screen.availHeight) ? document.body.offsetHeight : screen.availHeight;
//var width = (document.body.offsetWidth > screen.availWidth) ? document.body.offsetWidth : screen.availWidth;

	document.getElementById("message_box_title").innerHTML = title;
	
    document.getElementById("message_box_text").innerHTML = text;

    document.getElementById("message_box").style.height = window.innerHeight + "px";
    
    obj.style.top = ((window.innerHeight - obj.offsetHeight)/2 - 100) + "px";
    obj.style.left = (window.innerWidth - obj.offsetWidth)/2 + "px";
    
    
    document.getElementById("message_box").style.visibility = "visible";
    document.getElementById("btnok_message_box").focus();
    obj = null;
    
}

function show_continue_box(title, text) {
var obj = document.getElementById("continue_box_main");
//var height = (document.body.offsetHeight > screen.availHeight) ? document.body.offsetHeight : screen.availHeight;
//var width = (document.body.offsetWidth > screen.availWidth) ? document.body.offsetWidth : screen.availWidth;

    document.getElementById("continue_box_title").innerHTML = title;
    document.getElementById("continue_box_text").innerHTML = text;

    document.getElementById("continue_box").style.height = window.innerHeight + "px";
    
    obj.style.top = ((window.innerHeight - obj.offsetHeight)/2 - 100) + "px";
    obj.style.left = (window.innerWidth - obj.offsetWidth)/2 + "px";
    
    
    document.getElementById("continue_box").style.visibility = "visible";
    document.getElementById("btnok_continue_box").focus();
    obj = null;
    
}

function show_reset_confirm_box(text) {
var obj = document.getElementById("reset_confirm_box_main");
//var height = (document.body.offsetHeight > screen.availHeight) ? document.body.offsetHeight : screen.availHeight;
//var width = (document.body.offsetWidth > screen.availWidth) ? document.body.offsetWidth : screen.availWidth;

    document.getElementById("reset_confirm_text").innerHTML = text;

    document.getElementById("reset_confirm_box").style.height = window.innerHeight + "px";
    
    obj.style.top = ((window.innerHeight - obj.offsetHeight)/2 - 100) + "px";
    obj.style.left = (window.innerWidth - obj.offsetWidth)/2 + "px";
    
    
    document.getElementById("reset_confirm_box").style.visibility = "visible";
    document.getElementById("reset_confirm_ok").focus();
    obj = null;
    
}

function hide_reset_confirm_box(id) {
var obj = document.getElementById(id);

    document.getElementById("reset_confirm_box").style.visibility = "hidden";
    // obj.style.top = window.innerHeight +"px";

    /* obj.style.top = (obj.offsetTop + 50) + "px";
    if (obj.offsetTop > height) {
        obj.style.top = height +"px";
        document.getElementById("form_screen").style.visibility = "hidden";
	clearTimeout(timerId);
    } else {
	timerId = setTimeout("hide_form('"+id+"')", timeInterval);
    } */
    obj = null;
        
}

function hide_message_box(id) {
var obj = document.getElementById(id);

    document.getElementById("message_box").style.visibility = "hidden";
    // obj.style.top = window.innerHeight +"px";

    /* obj.style.top = (obj.offsetTop + 50) + "px";
    if (obj.offsetTop > height) {
        obj.style.top = height +"px";
        document.getElementById("form_screen").style.visibility = "hidden";
	clearTimeout(timerId);
    } else {
	timerId = setTimeout("hide_form('"+id+"')", timeInterval);
    } */
    obj = null;
        
}

function toggle_nav(ref, id, t, l) {
   // g_roomno = room;
   // g_ref = ref;
        // g_nav_type = ref;
        if (document.getElementById(id).style.visibility === "visible") {
            hide_nav(id);
	} else {
			// document.getElementById("digit_pad_cell").innerHTML = "";
            show_nav(id, ref, t, l);
	}
}
function show_nav(id, ref, t, l) {
    // document.getElementById(id).style.top = ((getAbsoluteTop(ref) + ref.offsetHeight + 2) - t) - document.getElementById("main_panel").scrollTop + "px";
    show_dialog('filter');
    document.getElementById(id).style.top = ((getAbsoluteTop(ref) + ref.offsetHeight + 2) - t) + "px";
    var left = document.getElementById(id+"_inner").offsetWidth / 2;
    document.getElementById(id).style.left = (getAbsoluteLeft(ref) - 48 + l) + "px";
    // document.getElementById(id).style.right = "5px";
    document.getElementById(id).style.visibility = "visible";
    
}
function hide_nav(id) {
    document.getElementById(id).style.visibility = "hidden";
    hide_dialog('filter');
}

function show_dialog(id) {
	document.getElementById(id).style.height = screen.availHeight + "px";
	document.getElementById(id).style.visibility = "visible";
//    $('#'+id).css('height', '\''+screen.availHeight+'\'');
//    $('#'+id).css('visibility', 'visible');
} 

function hide_dialog(id) {
	document.getElementById(id).style.visibility = "hidden";
//    $('#'+id).css('visibility', 'hidden');
}

function datediff(date1, date2) {
var diff = 0;

    diff = (date2.getTime() - date1.getTime());
    
    return(diff);
}


function show_tooltip(ref, t, text) {
	// document.getElementById("events_tooltip").style.left = left + "px";
    // alert(document.getElementById("main_panel").scrollTop);
    // alert(getAbsoluteTop(ref));
    // document.getElementById("tooltip").style.top = ((getAbsoluteTop(ref) - ref.offsetHeight - 10) - t)  + "px";      
    document.getElementById("tooltip").style.top = ((getAbsoluteTop(ref) + ref.offsetHeight + 2) - t)  + "px";
    // document.getElementById("events_tooltip_inner").innerHTML = "<div style=\"width:180px;height:100px;background:url('images/loading_icon_small.gif') center center no-repeat\"></div>";
	document.getElementById("tooltip_text").innerHTML = text;
    var left = document.getElementById("tooltip_inner").offsetWidth / 2;
    // document.getElementById("tooltip").style.left = (getAbsoluteLeft(ref) - (left - 8)) + "px";
    document.getElementById("tooltip").style.left = (getAbsoluteLeft(ref) - (left - 13)) + "px";
    document.getElementById("tooltip").style.visibility = "visible";
}
function hide_tooltip() {
    // g_cell_ref.style.border = "1px solid pink";
    // g_cell_ref.style.backgroundColor = "red";
    document.getElementById("tooltip").style.visibility = "hidden";
}