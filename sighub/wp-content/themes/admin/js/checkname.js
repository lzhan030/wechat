// JavaScript Document 
var xmlHttp; //定义一个全局对象 
function createXMLHttpRequest(){
	if(window.ActiveXObject)
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
	else if(window.XMLHttpRequest)
		xmlHttp = new XMLHttpRequest();
}
function checkgrpname(){ 
	var username=document.mygroup.group_name.value; 
	if(username != "") {
		createXMLHttpRequest(); 
		xmlHttp.open("GET","cgi-bin/check_group_name.php?name="+username,true);//true:表示异步传输，而不等send()方法返回结果，这正是ajax的核心思想 
		xmlHttp.onreadystatechange=function(){
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
				if(xmlHttp.responseText)
					document.getElementById('checkbox').innerHTML=xmlHttp.responseText;
			}
		}
		xmlHttp.send(); 
	}
} 
