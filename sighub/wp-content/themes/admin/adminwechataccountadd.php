<?php

/*
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); */

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

$demomenu=wechat_select_menu_demo();

/*
global  $current_user;
$user_id = $current_user->ID;
//echo $user_id; 
$user = get_userdata( $user_id );  
//echo $user->user_login;*/
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>管理员添加公众号</title>
		<script>
			
			var xmlHttp;
			function createXMLHttpRequest(){
				if(window.ActiveXObject)
					xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
				else if(window.XMLHttpRequest)
					xmlHttp = new XMLHttpRequest();
			}
			
			function selWechatType(type)
			{
			    //alert(type);
				//2014-07-11新增修改，公众号分为四种类型
				if(type=='pub_subnrz')
				{   
			        document.getElementById('table1').style.display="block";
				    document.getElementById('table2').style.display="none";
					//$('input:radio:first').attr('checked', 'checked');  //设置第一个radio为选中的值
					document.getElementById('table3').style.display="block";
					document.getElementById('table4').style.display="none";
				}
				if(type=='pub_subrz')
				{   
			        document.getElementById('table1').style.display="none";
				    document.getElementById('table2').style.display="block";
					document.getElementById('table3').style.display="none";
					document.getElementById('table4').style.display="none";
				}
				if(type=='pub_svcnrz')
				{
				    
				    document.getElementById('table1').style.display="none";
				    document.getElementById('table2').style.display="block";
					document.getElementById('table3').style.display="none";
					document.getElementById('table4').style.display="none";
				}
				if(type=='pub_svcrz')
				{
				    
				    document.getElementById('table1').style.display="none";
				    document.getElementById('table2').style.display="block";
					document.getElementById('table3').style.display="none";
					document.getElementById('table4').style.display="none";
				}
				
			}
		   
		    function checkmenuinfo()
		    {
		        var nicename1=document.getElementById("nicename1").value;
		        var menuappid=document.getElementById("appid").value;
			    var menuappsc=document.getElementById("appsc").value;
				//var busexit1=document.getElementById("busexit1").value;
			    //var exireply1=document.getElementsByName("exireply1")[1];
				//var exireply_content1=document.getElementById("exireply_content1").value;	
				
			    if(nicename1=='')
			    {
			     alert("请输入您的微信昵称");
			    }/*else if(busexit1==''){
					alert("请输入退出关注商家关键词");
				}else if(exireply1.checked){
					if(exireply_content1==''){
					alert("请输入退出关注商家时的回复信息");
					}else{
					    createXMLHttpRequest();
						xmlHttp.open("GET","?admin&page=pubaccountsearch&header=0&footer=0&nicename="+nicename1,true);
						xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							
							var str = xmlHttp.responseText;
							//alert(str);
							if(str.indexOf("微信昵称添加重复，请重新添加")>0){
								alert("微信昵称添加重复，请重新添加");
								}
							else
								document.getElementById('accountform').submit();
							 
							}
					    }
					    xmlHttp.send(null);
						 //document.getElementById('accountform').submit();
					}
			 
				}*/
			    else
			    {
			        if(!((menuappid!='')&&(menuappsc!='')))
			        {
			         alert("您没有输入menuappid和menuappsc，将没有自定义菜单这个功能");
			        }
			   
				    createXMLHttpRequest();
					xmlHttp.open("GET","?admin&page=pubaccountsearch&header=0&footer=0&nicename="+nicename1,true);
					xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
						
						var str = xmlHttp.responseText;
						//alert(str);
						if(str.indexOf("微信昵称添加重复，请重新添加")>0){
							alert("微信昵称添加重复，请重新添加");
							}
						else
							document.getElementById('accountform').submit();
						 
						}
					}
					xmlHttp.send(null);
					 //document.getElementById('accountform').submit();
			    }
			   
		    }
		   
		  function checknicename()
		  {
		    //2014-07-08新增修改获取选中的radio值，只有选中了已认证才需要判断menuappid和menuappsc
		    //alert($("input[name='auth']:checked").val()); 
		    var radiock = $("input[name='auth']:checked").val();
		    var nicename=document.getElementById("nicename").value;
			//var busexit=document.getElementById("busexit").value;
			//var exireply=document.getElementsByName("exireply")[1];
			//var exireply_content=document.getElementById("exireply_content").value;
            if(radiock == 1)
			{
				var menuappid=document.getElementById("appid1").value;
				var menuappsc=document.getElementById("appsc1").value;
			}
			 if(nicename=='')
			 {
			     alert("请输入您的微信昵称");
			 }/*else if(busexit==''){
				alert("请输入退出关注商家关键词");
			 }else if(exireply.checked){
				if(exireply_content==''){
					alert("请输入退出关注商家时的回复信息");
				}else{
				
					createXMLHttpRequest();
					xmlHttp.open("GET","?admin&page=pubaccountsearch&header=0&footer=0&nicename="+nicename,true);
					xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
						
						var str = xmlHttp.responseText;
						//alert(str);
						if(str.indexOf("微信昵称添加重复，请重新添加")>0){
							alert("微信昵称添加重复，请重新添加");
							}
						else
							document.getElementById('accountform').submit();
					     
						}
					}
					xmlHttp.send(null);
				
				}
			 
			 }*/
			 else
			 {
			    if(!((menuappid!='')&&(menuappsc!='')) && (radiock == 1))
				{
					alert("您没有输入menuappid和menuappsc，将没有自定义菜单这个功能");
				}
			    
			    createXMLHttpRequest();
				xmlHttp.open("GET","?admin&page=pubaccountsearch&header=0&footer=0&nicename="+nicename,true);
				xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
					
					var str = xmlHttp.responseText;
					//alert(str);
					if(str.indexOf("微信昵称添加重复，请重新添加")>0){
						alert("微信昵称添加重复，请重新添加");
						}
					else
						document.getElementById('accountform').submit();
					 
					}
				}
				xmlHttp.send(null);
			 }
		  }
		  
		$(function(){ 
			$("#authok").click(function(){   
			   
			   document.getElementById('table3').style.display="block";
			   document.getElementById('table4').style.display="block";
			   
			});
			$("#authnok").click(function(){   
			   
			   document.getElementById('table3').style.display="block";
			   document.getElementById('table4').style.display="none";
			   
			});
				
		});
		   
		</script>
	
	</head>
	<body>	
		<!--<div>-->
			<!--<div>-->
				<form id="accountform" action="?admin&page=adminwechatconfig" method="post" > 
					<div>
						<div class="main-title">
							<div class="title-1">当前位置：管理员公众号添加 > <font class="fontpurple">添加微信公众号 </font>
							</div>
						</div>
						<div class="bgimg"></div>
						<div>
							<table width="450" height="60" border="0" cellpadding="20px" style=" margin-left:150px; margin-top:15px;">
								<div>
									<tr>
										<!--<td width="225"><label for="name">请选择微信公众号类型: </label></td>-->
										<td width="225">请选择微信公众号类型: </td>
										<td>
											<select name="wechattype" class="form-control" size="1" type="text;" id="wechattype" value="5" maxlength="20"
											onchange="selWechatType(this.options[this.selectedIndex].value)">
												<!--<option value="pri_sub" >个人微信订阅号</option>
												<option value="pri_svc">个人微信服务号</option> -->
												<option value="pub_subnrz" selected="selected">公用微信未认证订阅号</option>
												<option value="pub_subrz">公用微信认证订阅号</option>
												<option value="pub_svcnrz">公用微信未认证服务号</option>
												<option value="pub_svcrz">公用微信认证服务号</option>
											</select>
										</td>						
									</tr>
								</div>
							</table >
							<table width="600" height="40" border="0" cellpadding="20px" style=" margin-left:150px; " id="table1">
								<tr>
									<!--<td><label for="name">请输入微信昵称: </label></td>-->
									<td>请输入微信昵称: </td>
									<td height="50px;">
										<input type="text" class="form-control" id="nicename" name="site_name" style="width:225px; margin-left:122px;"/>
									</td>
								</tr>
								<!--20140623newaddedbegin-->
								<!--20140711del--> 
								<!--<tr>
									<td >认证情况: </td>
									<td height="50px;">
										<input type="radio" id="authnok" name="auth" value="0" 
										checked="checked" style="margin-left:125px;"/><span>未认证</span>
										<input type="radio" id="authok" name="auth" value="1" style="margin-left:15px;"/><span>已认证</span>
									</td>								
								</tr>-->
								<!--<tr>
									<!--<td><label for="name">请输入微信昵称: </label></td>
									<td>请输入站点名称: </td>
									<td height="50px;">
										<input type="text" class="form-control" id="dispname" name="dispname" style="width:225px; margin-left:122px;"/>
									</td>
								</tr>-->
							</table>
							
							<table width="600" height="80" border="0" cellpadding="20px" style=" margin-left:150px; margin-top:70px; display:none;" id="table4">
							    <tr>
									<!--<td><label for="name">menu_appId: </label></td>-->
									<td>微信菜单AppId: </td>
									<td height="50px;"><input type="text" class="form-control" id="appid1" name="menu_appId1"  style="width:225px; margin-left:81px;"/></td>
													
								</tr>
								<tr>
									<!--<td><label for="name">menu_appSc: </label></td>-->
									<td>微信菜单AppSecret: </td>
									<td height="50px;"><input type="text" class="form-control" id="appsc1" name="menu_appSc1"  style="width:225px; margin-left:81px;"/></td>
															
								</tr>
								
								<tr>
									<!--<td><label for="name">请选择使用的菜单模板: </label></td>-->
									<td>请选择使用的菜单模板: </td>
									<td height="50px;"  >
									<select style="width:225px; margin-left:81px;"id="seltemId" class="form-control" name="demomenu1" onchange="selTem(this.options[this.selectedIndex].value)">
										<?php foreach($demomenu as $demo){ 
											echo "<option value='$demo->M_id '> $demo->M_name </option>";
											} ?>
									</select>
									</td>
								</tr>
							
							</table>
							
							<table width="600" height="30" border="0" cellpadding="20px" style=" margin-left:150px; margin-top: 15px; " id="table3">
									
									<tr height="60px">
										<td width="225">
											<input type="button" onclick="checknicename();" class="btn btn-primary" value="下一步" id="sub3"
										style="margin-left:240px; width:70px" /></td>
									</tr>
							</table >
							
							
							
								
							<table width="600" height="200" border="0" cellpadding="20px" style=" margin-left:150px; display:none;" id="table2">
								
									<tr>
										<!--<td><label for="name">请输入微信昵称: </label></td>-->
										<td>请输入微信昵称: </td>
										<td height="50px;"><input type="text" class="form-control" id="nicename1" name="site_name1"  style="width:225px; margin-left:-85px;"/></td>														
									</tr>
									<!--20140623newaddedbegin-->
									<!--20140711del-->
							        <!--<tr>
										<td >认证情况: </td>
										<td height="50px;">
											<input type="radio" id="authnokfw" name="authfw" value="0" 
											checked="checked" style="margin-left:-82px;"/><span>未认证</span>
											<input type="radio" id="authokfw" name="authfw" value="1" style="margin-left:15px;"/><span>已认证</span>
										</td>								
									</tr>-->
									<!--20140623newaddedend-->
									<!--20140709newaddedbegin-->
									<!--<tr>
										<!--<td><label for="name">请输入微信昵称: </label></td>
										<td>请输入站点名称: </td>
										<td height="50px;"><input type="text" class="form-control" id="dispname1" name="dispname1"  style="width:225px; margin-left:-85px;"/></td>														
									</tr>-->
									<!--20140709newaddedend-->
									
									<tr>
										<!--<td><label for="name">menu_appId: </label></td>-->
										<td>微信菜单AppId: </td>
										<td height="50px;"><input type="text" class="form-control" id="appid" name="menu_appId"  style="width:225px; margin-left:-85px;"/></td>
														
									</tr>
									<tr>
										<!--<td><label for="name">menu_appSc: </label></td>-->
										<td>微信菜单AppSecret: </td>
										<td height="50px;"><input type="text" class="form-control" id="appsc" name="menu_appSc"  style="width:225px; margin-left:-85px;"/></td>
																
									</tr>
									<tr>
										<!--<td><label for="name">请选择使用的菜单模板: </label></td>-->
										<td>请选择使用的菜单模板: </td>
										<td height="50px;"  >
										<select style="width:225px; margin-left:-85px;"id="seltemId" class="form-control" name="demomenu" onchange="selTem(this.options[this.selectedIndex].value)">
										<?php foreach($demomenu as $demo){ 
											echo "<option value='$demo->M_id '> $demo->M_name </option>";
											} ?>
									</select>
										</td>
									</tr>
									
									<tr>
										<td height="60px"><input type="button" onclick="checkmenuinfo();" class="btn btn-primary" value="下一步" id="sub4"
										style="margin-left:240px; width:70px" /></td>
									</tr>				
							</table>							
							
						</div>
			</form>
			
			<!--</div>-->
		<!--</div>-->
		<script>
		   selWechatType(document.getElementById('wechattype').options[document.getElementById('wechattype').selectedIndex].value);
		</script>
	</body>
</html>

