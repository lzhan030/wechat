// JavaScript Document 
var xmlHttp; //定义一个全局对象 
function createXMLHttpRequest(){
	if(window.ActiveXObject)
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
	else if(window.XMLHttpRequest)
		xmlHttp = new XMLHttpRequest();
}
//check alarm url
//define global variable
function checkpaymenturl(){ 

    var payment_url=document.mysetting.payment_url.value; 
    document.getElementById('checkresult3').innerHTML = ""; //每次检查url前先清空之前的提示
	var alarmurl=document.mysetting.alarm_url.value; 
	var nativepay_url=document.mysetting.nativepay_url.value; 
	
	if(payment_url != ""){
        if(alarmurl != "" && payment_url == alarmurl){
			alert("支付授权url和告警url重复，请重新输入支付授权url");
		}else if(nativepay_url != "" && payment_url == nativepay_url){
			alert("支付授权url和原生支付url重复，请重新输入支付授权url");
		}else{
			var validatestr = /^(?!\/)(\/{0,1}[a-zA-Z0-9_])+$/;  //判断url格式是否符合规范：只能以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线或者反斜杠组成
			//var validatestr = /^[a-zA-Z0-9_]+$/;
			if(!validatestr.test(payment_url)){
				alert("支付授权URL要以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线(_)或者反斜杠(/)组成且不能出现连续相同的反斜杠，请重新输入！");
			}
			else{
				createXMLHttpRequest(); 
				xmlHttp.open("GET",'module.php?module=wepay&do=urlcheck&urlstring='+payment_url+'&value=""',true);
				//xmlHttp.open("GET","cgi-bin/check_group_name.php?name="+username,true);//true:表示异步传输，而不等send()方法返回结果，这正是ajax的核心思想 
				xmlHttp.onreadystatechange=function(){
					if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
						if(xmlHttp.responseText)
							document.getElementById('checkresult3').innerHTML=xmlHttp.responseText;
							
					}
				}
				xmlHttp.send(); 
			}
		}
	}
} 
function checkalarmurl(){ 
    var payment_url=document.mysetting.payment_url.value; 
    var alarmurl=document.mysetting.alarm_url.value; 
	var nativepay_url=document.mysetting.nativepay_url.value; 
    document.getElementById('checkresult').innerHTML = ""; //每次检查url前先清空之前的提示
	if(alarmurl == ""){
	    alert("告警url不能为空");
	}else{
			if(nativepay_url != "" && alarmurl == nativepay_url){
				alert("告警url和原生支付url重复，请重新输入告警url");
			}else if(payment_url != "" && alarmurl == payment_url){
				alert("告警url和支付授权url重复，请重新输入告警url");
			}else{
				//var validatestr = /^(?!\/)(?!.*?\/$)[a-zA-Z0-9_\/]+$/;  //判断url格式是否符合规范：只能以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线或者反斜杠组成
				var validatestr = /^(?!\/)(\/{0,1}[a-zA-Z0-9_])+$/;
				if(!validatestr.test(alarmurl)){
					alert("告警url要以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线(_)或者反斜杠(/)组成且不能出现连续相同的反斜杠，请重新输入");
				}
				else{
					createXMLHttpRequest(); 
					xmlHttp.open("GET",'module.php?module=wepay&do=urlcheck&urlstring='+alarmurl+'&value="AlarmNotify"',true);
					//xmlHttp.open("GET","cgi-bin/check_group_name.php?name="+username,true);//true:表示异步传输，而不等send()方法返回结果，这正是ajax的核心思想 
					xmlHttp.onreadystatechange=function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							if(xmlHttp.responseText)
								document.getElementById('checkresult').innerHTML=xmlHttp.responseText;
						}
					}
					xmlHttp.send(); 
				}
			}
	}
} 

//check notify url
function checknativeurl(){ 
    var payment_url=document.mysetting.payment_url.value;
    var alarmurl=document.mysetting.alarm_url.value; 
	var nativepay_url=document.mysetting.nativepay_url.value; 
    document.getElementById('checkresult2').innerHTML = ""; //每次检查url前先清空之前的提示
	if(nativepay_url == ""){
	    alert("原生支付url不能为空");
	}else{
		
			if(alarmurl != "" && alarmurl == nativepay_url){
				alert("原生支付url和告警url重复，请重新输入原生支付url");
			}else if(payment_url != "" && nativepay_url == payment_url){
				alert("原生支付url和支付授权url重复，请重新输入原生支付url");
			}else{
				//var validatestr = /^(?!\/)(?!.*?\/$)[a-zA-Z0-9_\/]+$/;  //判断url格式是否符合规范：只能以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线或者反斜杠组成
				var validatestr = /^(?!\/)(\/{0,1}[a-zA-Z0-9_])+$/;
				if(!validatestr.test(nativepay_url)){
					alert("原生支付url要以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线(_)或者反斜杠(/)组成且不能出现连续相同的反斜杠，请重新输入");
				}else{
					createXMLHttpRequest(); 
					xmlHttp.open("GET",'module.php?module=wepay&do=urlcheck&urlstring='+nativepay_url+'&value="NativePayNotify"',true);
					//xmlHttp.open("GET","cgi-bin/check_group_name.php?name="+username,true);//true:表示异步传输，而不等send()方法返回结果，这正是ajax的核心思想 
					xmlHttp.onreadystatechange=function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							if(xmlHttp.responseText)
								document.getElementById('checkresult2').innerHTML=xmlHttp.responseText;
						}
					}
					xmlHttp.send(); 
				}
			}
	}
}

//check key
function checkkey(){ 

    var appkey=document.mysetting.appkey.value; 
    
	if(appkey != ""){

		//var validatestr = /^(?!(?:\d+|[a-zA-Z]+)$)[\da-zA-Z]{32}$/;  //32位数字、字母组合，但是不能全为字母或者数字 
		var validatestr = /^[a-zA-Z0-9]{32}$/; //32位的数字和大小写字母组合
		if(!validatestr.test(appkey)){
			alert("支付秘钥key必须是32位的数字和大小写字母组合！");
		}
		else{
			createXMLHttpRequest(); 
			xmlHttp.open("GET",'module.php?module=wepay&do=appKeycheck&keystring='+appkey,true); //true:表示异步传输，而不等send()方法返回结果，这正是ajax的核心思想 
			xmlHttp.onreadystatechange=function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
					if(xmlHttp.responseText.indexOf("该key已存在,请重新填写") > 0)
						alert("该key已存在,请重新填写");
				}
			}
			xmlHttp.send(); 
		} 
		
	}
} 
//check key again
function checkkeyagain(){ 

    var appkeyagin=document.mysetting.appkeyagin.value; 
    
	if(appkeyagin != ""){

		//var validatestr = /^(?!(?:\d+|[a-zA-Z]+)$)[\da-zA-Z]{32}$/;  //32位数字、字母组合，但是不能全为字母或者数字 
		var validatestr = /^[a-zA-Z0-9]{32}$/; //32位的数字和大小写字母组合
		if(!validatestr.test(appkeyagin)){
			alert("重新输入的支付秘钥key必须是32位的数字和大小写字母组合！");
		}
		else{
			createXMLHttpRequest(); 
			xmlHttp.open("GET",'module.php?module=wepay&do=appKeycheck&keystring='+appkeyagin,true); //true:表示异步传输，而不等send()方法返回结果，这正是ajax的核心思想 
			xmlHttp.onreadystatechange=function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
					if(xmlHttp.responseText.indexOf("该key已存在,请重新填写") > 0)
						alert("重新输入的支付秘钥key已存在,请重新填写");
				}
			}
			xmlHttp.send(); 
		} 
		
	}
}

//check test payment url
function checktestpaymenturl(){ 

    var payment_url=document.paymenttestset.testpayment_url.value; 
    document.getElementById('checkresult').innerHTML = ""; //每次检查url前先清空之前的提示
	
	if(payment_url != ""){

		//var validatestr = /^(?!\/)(?!.*?\/$)[a-zA-Z0-9_\/]+$/;  //判断url格式是否符合规范：只能以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线或者反斜杠组成
		//var validatestr = /^[a-zA-Z0-9_]+$/;
		var validatestr = /^(?!\/)(\/{0,1}[a-zA-Z0-9_])+$/;
		if(!validatestr.test(payment_url)){
			alert("测试目录URL要以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线(_)或者反斜杠(/)组成且不能出现连续相同的反斜杠，请重新输入！");
		}
		else{
			createXMLHttpRequest(); 
			xmlHttp.open("GET",'module.php?module=wepay&do=urlcheck&urlstring='+payment_url,true);
			//xmlHttp.open("GET","cgi-bin/check_group_name.php?name="+username,true);//true:表示异步传输，而不等send()方法返回结果，这正是ajax的核心思想 
			xmlHttp.onreadystatechange=function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
					if(xmlHttp.responseText)
						document.getElementById('checkresult').innerHTML=xmlHttp.responseText;
				}
			}
			xmlHttp.send(); 
		}
		
	}
} 
//check test payment url
function checkemail(){ 
    var contactemail=document.mysetting.contactemail.value; 
	var emailreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;	
	if(contactemail !="" && !emailreg.test(contactemail)){
		alert("您的邮箱格式不正确，请重新输入！");
	}

}
//check key
function checkmobilenumber(){ 

    var contactnumber=document.mysetting.contactnumber.value; 
    
	if(contactnumber != ""){
		var validatestr = /^[0-9-]+$/; //联系电话是数字字符串或者带有横线
		if(!validatestr.test(contactnumber)){
			alert("联系人电话必须是数字或横线的组合，请重新输入！");
		}
	}
} 