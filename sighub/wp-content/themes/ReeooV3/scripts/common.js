/*
 * Common Utilities
 */
var tipsTimeout = 5000;
 
/*
 * The common jQuery ajax request
 */
function sendDataRequest(type, uri, params, data, success, failure) {
	var protocol = window.location.protocol;
    var host = window.location.host;
    var targetUrl = protocol + "//" + host + uri;
	if (params != null) {
		targetUrl += "?" + jQuery.param(params)
	}
	$.ajax({
		type: type,
		url: targetUrl,
		timeout: 15000,
		cache: false,
		data: $.toJSON(data),
		processData: false,
		dataType: "text",
		contentType: "application/json; charset=utf-8"
	}).done(function(response) {
		var result = undefined;
		if (response != null && response.length > 0) {
			try {
				result = $.secureEvalJSON(response);
			}
			catch(err) {
				result = response;
			}
		}
		success(result);
	}).fail(function(jqXHR) {
		var errMsg = undefined;
		if (jqXHR.readyState === 4) {
			try {
				errMsg = $.secureEvalJSON(jqXHR.responseText);
			}
			catch(err) {
				errMsg = {code:"SERVER_FAILURE"};
			}
			
		}
		else {
			errMsg = {code:"SERVER_UNREACHABLE"};
		}
		failure(errMsg);
	});
}

function sendRequest(type, uri, params, success, failure) {
	var protocol = window.location.protocol;
    var host = window.location.host;
    var targetUrl = protocol + "//" + host + uri;
	if (params != null) {
		targetUrl += "?" + jQuery.param(params)
	}
	$.ajax({
		type: type,
		url: targetUrl,
		timeout: 15000,
		cache: false,
		dataType: "text"
	}).done(function(response) {
		var result = undefined;
		if (response != null && response.length > 0) {
			try {
				result = $.secureEvalJSON(response);
			}
			catch(err) {
				result = response;
			}
		}
		success(result);
	}).fail(function(jqXHR) {
		var errMsg = undefined;
		if (jqXHR.readyState === 4) {
			try {
				errMsg = $.secureEvalJSON(jqXHR.responseText);
			}
			catch(err) {
				errMsg = {code:"SERVER_FAILURE"};
			}
			
		}
		else {
			errMsg = {code:"SERVER_UNREACHABLE"};
		}
		failure(errMsg);
	});
}

/**//**//**//**//**//**//**//**  
 * 返回日期
 * @param d the delimiter  
 * @param p the pattern of your date  
 */  
String.prototype.toDate = function(style) {   
  var y = this.substring(style.indexOf('y'),style.lastIndexOf('y')+1);//年
  var M = this.substring(style.indexOf('M'),style.lastIndexOf('M')+1);//月
  var d = this.substring(style.indexOf('d'),style.lastIndexOf('d')+1);//日
  var h = this.substring(style.indexOf('h'),style.lastIndexOf('h')+1);//时
  var m = this.substring(style.indexOf('m'),style.lastIndexOf('m')+1);//分
  var s = this.substring(style.indexOf('s'),style.lastIndexOf('s')+1);//秒
  
  if(s == null ||s == "" || isNaN(s)) {s = new Date().getSeconds();}   
  if(m == null ||m == "" || isNaN(m)) {m = new Date().getMinutes();}   
  if(h == null ||h == "" || isNaN(h)) {h = new Date().getHours();}   
  if(d == null ||d == "" || isNaN(d)) {d = new Date().getDate();}   
  if(M == null ||M == "" || isNaN(M)) {M = new Date().getMonth()+1;}   
  if(y == null ||y == "" || isNaN(y)) {y = new Date().getFullYear();}   
  var dt ;   
  eval ("dt = new Date('"+ y+"', '"+(M-1)+"','"+ d+"','"+ h+"','"+ m+"','"+ s +"')");   
  return dt;   
}   
  
/**//**//**//**//**//**//**//**  
 * 格式化日期
 * @param   d the delimiter  
 * @param   p the pattern of your date  
 * @author  meizz  
 */  
Date.prototype.format = function(style) {   
  var o = {   
    "M+" : this.getMonth() + 1, //month   
    "d+" : this.getDate(),      //day   
    "h+" : this.getHours(),     //hour   
    "m+" : this.getMinutes(),   //minute   
    "s+" : this.getSeconds(),   //second   
    "w+" : "天一二三四五六".charAt(this.getDay()),   //week   
    "q+" : Math.floor((this.getMonth() + 3) / 3),  //quarter   
    "S"  : this.getMilliseconds() //millisecond   
  }   
  if(/(y+)/.test(style)) {   
    style = style.replace(RegExp.$1,   
    (this.getFullYear() + "").substr(4 - RegExp.$1.length));   
  }   
  for(var k in o){   
    if(new RegExp("("+ k +")").test(style)){   
      style = style.replace(RegExp.$1,   
        RegExp.$1.length == 1 ? o[k] :   
        ("00" + o[k]).substr(("" + o[k]).length));   
    }   
  }   
  return style;   
}