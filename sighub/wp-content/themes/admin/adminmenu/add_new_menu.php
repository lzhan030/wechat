<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
$menusecid=$_GET["menusecid"];
$newid=$_GET["newid"];
$savnewid=$_GET["newid"];
//$needle=$_SERVER['HTTP_HOST'];
$needle=home_url()."/?site";
$M_id=$_GET['Mid'];
$Mname=$_GET['Mname'];
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/jquery.Jcrop.css" type="text/css" />
	<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/admin/js/easyui/themes/default/easyui.css" />
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/easyui/themes/icon.css" />
	<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/easyui/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/easyui/locale/easyui-lang-zh_CN.js"></script>
	<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/easyui/addin/datagrid-detailview.js"></script>
	<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.json-2.4.min.js"></script>
		<title>菜单管理</title>
	</head>
</html>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/jquery.Jcrop.css" type="text/css" />
	<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/admin/js/easyui/themes/default/easyui.css" />
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/easyui/themes/icon.css" />
	<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/easyui/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/easyui/locale/easyui-lang-zh_CN.js"></script>
	<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/easyui/addin/datagrid-detailview.js"></script>
	<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.json-2.4.min.js"></script>
	<style>
		.input-group{margin-bottom:5px;}
		.submenu{margin-left:70px;}
	</style>
	</head>
	<body>
		<div class="main-title">
			<div class="title-1">当前位置：菜单管理> <font class="fontpurple">菜单内容设置 </font>
			</div>
		</div>
		<div class="bgimg"></div>
		<div style="margin-left:580px;">
			<input type="button" class="btn btn-sm btn-primary" style="width:120px" onclick="back()" value="保存菜单" />
			<input type="button" class="btn btn-sm btn-primary" style="width:120px" onclick="delmenu()" value="删除菜单" />
		</div>
		<div class="main keyWordMain" style="width:700px;height:500px;margin-top: 10px;">
			<div  style="position: relative;">
				<input class="addmenu btn btn-primary" id="menu" type="button" value="添加根菜单" style="margin-bottom:10px; position:relative;width: 120px;height: 30px; border-radius: 0px;" />
				<ul id="ttre" class="easyui-tree"></ul>
			</div>
            <div class="black-bg"></div>
		</div>
		  <div>
			<span>注：请在菜单上点击右键进行菜单的添加、修改、删除。</span>
		  </div>
		
		
		<script type="text/javascript">		
		var xmlHttp;
		function createXMLHttpRequest(){
		if(window.ActiveXObject)
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		else if(window.XMLHttpRequest)
		xmlHttp = new XMLHttpRequest();
		}
		
		var root=0;
		var c1=0;
		var c2=0;
		var c3=0;
		var n1=0;
		var n2=0;
		var n3=0;
		var t=0;
		var menuId;
		var menuType;
		var menuKey;
		var menuPad;
		var menuName;
		function selectNews(){	   
			if(menuId==""){
				menuId=-1;
				menuType="weChat_news";			
			}
			window.open('menu_news_select.php?beIframe&menuId='+menuId+'&menuType='+menuType+'&menuKey='+menuKey+'&menuPad='+menuPad,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		}
				
		var itemTitle=new Array();
		var picUrl=new Array();
		var itemId=new Array();
		var itemUrl=new Array();
		var newsItemId=new Array();
				
		for(var i=0;i<7;i++){
			itemTitle[i]=new Array();
			picUrl[i]=new Array();
			itemId[i]=new Array();
			itemUrl[i]=new Array();		
		}				
		var newsCount=new Array();	


		function togchild(menuId,menuType,menuKey,menuPad,menuName){
			$(".left").hide();
			editor.html("");
			var textContent;
			var murl;
			var item;
			if(newid==-1){
				if(menuType=="weChat_text"){
					$("#siurl").attr("value","");
					$("#sourl").attr("value","");
					$.ajax({
							url: "menu_select.php",
							type: "POST",
							data:{trans_data:menuKey},
							dataType: "text",
							error: function(){  
								alert('Error loading XML document');  
							},  
							success: function(data,status){//如果调用php成功    
								textContent=data;					 
								editor.html(textContent);
								if($(".textNews").css("display")=="none"){$("#nav-main ul li:first-child").trigger("click")}
							}
						});
				}else if(menuType=="weChat_news"){
					$("#siurl").attr("value","");
					$("#sourl").attr("value","");
					var key=menuKey;
					for(var i=1;i<newsItemId.length;i++){								
						if(key==newsItemId[i]){
							key=i-1; break;
						}else{
							$(".left").hide();
						}
					}	
					item=".nub"+key;
					$(item).show();
					if($(".main_bd").css("display")=="none"){$("#nav-main ul li:nth-child(2)").trigger("click")}				
				}else if(menuType=="view"){
					$.ajax({
							url: "menu_select.php",  
							type: "POST",
							data:{menuid:menuId},
							dataType: "text",
							error: function(){  
								alert('Error loading XML document');  
							},  
							success: function(data,status){//如果调用php成功    
								murl=data;					 
							var need='<?php echo $needle ?>';
							if(data.indexOf(need)>=0){								
								$(':radio[name="menuUrl"][value="0"]').attr("checked","checked");
								$("#siurl").css("visibility","visible");
								$("#siurl").val(murl);
								$("#sourl").val("");								
							}else{
								$(':radio[name="menuUrl"][value="1"]').attr("checked","checked");
								$("#siurl").css("visibility","hidden");
								$("#sourl").val(murl);
								$("#siurl").val("");
							}
														
							if($(".webUrl").css("display")=="none"){$("#nav-main ul li:nth-child(3)").trigger("click")}
							}
						});
				}
			}else{			
				var key=newid;
				for(var i=1;i<newsItemId.length;i++){								
					if(key==newsItemId[i]){
						key=i-1; break;
					}else{
						$(".left").hide();
					}
				}						
				item=".nub"+key;
				newid=-1;
				$(item).show();
				if($(".main_bd").css("display")=="none"){$("#nav-main ul li:nth-child(2)").trigger("click")}							
			}
		}
					
		$(".connect").click(function(e){
			menuType="weChat_text";
			editor.sync();
			var content=document.getElementById('editor_id').value;	
			if(menuPad!=-1)	{
				jQuery.post(
			        "<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId,
			        {menuType:menuType,menuName:menuName,content:content,menuPad:menuPad},
			        function(data, textStatus, jqXHR){
						alert("保存成功");
						var url="<?php echo get_template_directory_uri(); ?>";
						location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;
			        },
			        "text"
			    );	
			}else{
				if(menuId==n1){
						if(c1>0){
							alert("根菜单有下级子菜单不能设置回复内容");
						}else{
						jQuery.post(
					        "<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&content="+content+"&menuPad="+menuPad,
					        {menuType:menuType,menuName:menuName,content:content,menuPad:menuPad},
					        function(data, textStatus, jqXHR){
								alert("保存成功");
								var url="<?php echo get_template_directory_uri(); ?>";
								location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;			
					        },
					        "text"
				    	);		
						}
					}else if(menuId==n2){
							if(c2>0){
								alert("根菜单有下级子菜单不能设置回复内容");
							}else{
								jQuery.post(
							       	"<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&content="+content+"&menuPad="+menuPad,
							        {menuType:menuType,menuName:menuName,content:content,menuPad:menuPad},
							        function(data, textStatus, jqXHR){
										alert("保存成功");
										var url="<?php echo get_template_directory_uri(); ?>";
										location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;				
							        },
							        "text"
						    	);	
							}
						}else if(menuId==n3){
							if(c3>0){
								alert("根菜单有下级子菜单不能设置回复内容");
							}else{
								jQuery.post(
							       	"<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&content="+content+"&menuPad="+menuPad,
							        {menuType:menuType,menuName:menuName,content:content,menuPad:menuPad},
							        function(data, textStatus, jqXHR){
										alert("保存成功");
										var url="<?php echo get_template_directory_uri(); ?>";
										location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;				
							        },
							        "text"
						    	);	
							}
						}		
			}
		})
		
		
		<?php 
		global $savnewid;
		if($savnewid==null){
			$savnewid=-1;
		}
		?>
		//这里不能放在click事件的里面，因为只编译一次，赋值一次，点击别的词还是拿到这个值
		savnewid=<?php global $savnewid; echo $savnewid; ?>;
		//应该是可以跟connect合并
		$(".newsconnect").click(function(e){	   
			var menuType="weChat_news";			
			//设置为-1是防止点击其他的关键词时，仍然使用该id
			if(savnewid==-1){
				alert("您当前没有选择新的多图文");
			}
			else{
				if(menuPad!=-1)	{				
					createXMLHttpRequest();
					xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuPad="+menuPad+"&menuKey="+savnewid,true);
					xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("保存成功");
							var url="<?php echo get_template_directory_uri(); ?>";
							//location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+id;
							location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;												
						}
						
					}
					xmlHttp.send(null);		
				}else{
					if(menuId==n1){
						if(c1>0){
							alert("根菜单有下级子菜单不能设置回复内容");
						}else{
						createXMLHttpRequest();
					xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuPad="+menuPad+"&menuKey="+savnewid,true);
					xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("保存成功");
							var url="<?php echo get_template_directory_uri(); ?>";
							location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;												
						}
					}
					xmlHttp.send(null);			
						}
					}else if(menuId==n2){
							if(c2>0){
								alert("根菜单有下级子菜单不能设置回复内容");
							}else{
							createXMLHttpRequest();
					xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuPad="+menuPad+"&menuKey="+savnewid,true);
					xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("保存成功");
							var url="<?php echo get_template_directory_uri(); ?>";
							location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;												
						}
					}
					xmlHttp.send(null);	
							}
						}else if(menuId==n3){
							if(c3>0){
								alert("根菜单有下级子菜单不能设置回复内容");
							}else{
								createXMLHttpRequest();
					xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuPad="+menuPad+"&menuKey="+savnewid,true);
					xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("保存成功");
							var url="<?php echo get_template_directory_uri(); ?>";
							location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;												
						}
					}
					xmlHttp.send(null);	
							}
						}
				}
			}		
				savnewid=-1;								
		})
		
		
		$(".urlconnect").click(function(e){
			menuType="view";			
			var radios = document.getElementsByName("menuUrl");
			var menuurl;
			var val;
			for(radio in radios) {
				if(radios[radio].checked) {
					val = radios[radio].value;
					break;
				}
			}
			if(val==0){
				menuurl=$("#siurl").val();					
			}else{
				menuurl=$("#sourl").val();
			}
				
			if(menuPad!=-1)	{
				createXMLHttpRequest();
				xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuKey="+menuurl+"&menuPad="+menuPad,true);
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
						alert("保存成功");
						var url="<?php echo get_template_directory_uri(); ?>";
						location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;
					}				
				}
				xmlHttp.send(null);	
			}else{
				if(menuId==n1){
						if(c1>0){
							alert("根菜单有下级子菜单不能设置回复内容");
						}else{
						createXMLHttpRequest();
					xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuKey="+menuurl+"&menuPad="+menuPad,true);
					xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("保存成功");
							var url="<?php echo get_template_directory_uri(); ?>";
							location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;												
						}
					}
					xmlHttp.send(null);			
						}
					}else if(menuId==n2){
							if(c2>0){
								alert("根菜单有下级子菜单不能设置回复内容");
							}else{
							createXMLHttpRequest();
					xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuKey="+menuurl+"&menuPad="+menuPad,true);
					xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("保存成功");
							var url="<?php echo get_template_directory_uri(); ?>";
							location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;												
						}
					}
					xmlHttp.send(null);	
							}
						}else if(menuId==n3){
							if(c3>0){
								alert("根菜单有下级子菜单不能设置回复内容");
							}else{
								createXMLHttpRequest();
					xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuKey="+menuurl+"&menuPad="+menuPad,true);
					xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("保存成功");
							var url="<?php echo get_template_directory_uri(); ?>";
							location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;												
						}
					}
					xmlHttp.send(null);	
							}
						}
			}
		})
		
		
		$(".main_bd").hide();
		$(".webUrl").hide();
		$("#nav-main>ul:nth-child(1) li:nth-child(1)").click(function(){
			$(".main_bd").fadeOut();
			$(".textNews").fadeIn();
			$(".webUrl").fadeOut();
		})
		$("#nav-main>ul:nth-child(1) li:nth-child(2)").click(function(){
			$(".textNews").fadeOut();
			$(".main_bd").fadeIn();	
			$(".webUrl").fadeOut();
		})
		$("#nav-main>ul:nth-child(1) li:nth-child(3)").click(function(){
			$(".textNews").fadeOut();
			$(".main_bd").fadeOut();	
			$(".webUrl").fadeIn();
		})
		$("#nav-main>ul:nth-child(1) li").click(function(e){			
			$("#nav-main ul li.selected").removeClass("active selected");
			$(this).addClass("active selected");
					
		})	
		

		function trclick(node){
			$('#ttre').tree('toggle', node.target);
			togchild(node.id,node.attributes.type,node.attributes.key,node.attributes.pid,node.text);
			//这里记得赋值，否则加载页面后，报值空			
			menuId=node.id;
			menuType=node.attributes.type;
			menuKey=node.attributes.key;
			menuPad=node.attributes.pid;
			menuName=node.text;
		}
			
		//菜单树部分
		$(document).ready(function(e) {
			var tcurrentId;
			var tparenId;
			
			$('#ttre').tree({
					animate:true,
					checkbox: false,
					onClick: function(node){
						 $(this).tree('toggle', node.target);
						 togchild(node.id,node.attributes.type,node.attributes.key,node.attributes.pid,node.text);	
						menuId=node.id;
						menuType=node.attributes.type;
						menuKey=node.attributes.key;
						menuPad=node.attributes.pid;
						menuName=node.text;						 
						 
					},						
					onContextMenu: function(e, field){
						tcurrentId = field.id;
						tparenId=field.attributes.pid;
						 e.preventDefault();
						createColumnMenu(field.attributes.pid,field.id);
						$('#tmenu').menu('show', {
							left:e.pageX,  
							top:e.pageY  
						});  	
					}
				 });			
			menuPage();	
			
			function createColumnMenu(id,fid){ 
					$('#tmenu').remove();
				  var tmenu = $('#tmenu');
				if (!$('#tmenu').length){ 
					tmenu = $('<div id="tmenu" style="width:100px; position: absolute;"></div>').appendTo('body');  
				} else {
					var tmenu = $('#tmenu');
					tmenu = $('<div id="tmenu" style="width:100px; position: absolute;"></div>').appendTo('body');
				}
				$('<div iconCls="icon-ok" id="新建"/>').html("新建").appendTo(tmenu);
				$('<div iconCls="icon-ok" id="修改"/>').html("修改").appendTo(tmenu);
				$('<div iconCls="icon-ok" id="删除"/>').html("删除").appendTo(tmenu); 			
				tmenu.menu({  
					onClick: function(item){
						var Mid='<?php echo $M_id ?>';
						if (item.id=='新建'){ 
							if(fid==n1){
							if(c1>4){
								alert("最多建五个子菜单");
							}else{
								window.open("?admin&page=adminmenu/add_menu_dlg&header=0&footer=0&M_id="+Mid+"&menuId="+tcurrentId+"&parid="+tparenId,"_blank","height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
							}
						}else if(fid==n2){
							if(c2>4){
								alert("最多建五个子菜单");
							}else{
								window.open("?admin&page=adminmenu/add_menu_dlg&header=0&footer=0&M_id="+Mid+"&menuId="+tcurrentId+"&parid="+tparenId,"_blank","height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
							}
						}else if(fid==n3){
							if(c3>4){
								alert("最多建五个子菜单");
							}else{
								window.open("?admin&page=adminmenu/add_menu_dlg&header=0&footer=0&M_id="+Mid+"&menuId="+tcurrentId+"&parid="+tparenId,"_blank","height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
							}
						}else{
							alert("子菜单不能建立二级子菜单！");
						}
						} 
						else if(item.id=='修改'){  
							window.open("?admin&page=adminmenu/update_menu_dlg&header=0&footer=0&M_id="+Mid+"&menuId="+tcurrentId,"_blank","height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
						}
						else{
							if(confirm("确定删除吗？")){							
								createXMLHttpRequest();
								xmlHttp.open("GET","?admin&page=adminmenu/menu_del&header=0&footer=0&menuId="+tcurrentId,true);
								xmlHttp.onreadystatechange = function(){
									if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
										alert("删除成功");
										window.location.reload();
									}	
								}									
								xmlHttp.send(null);
							}
						}				
					}  
				});  
			} 					
		})
		
		function menuPage() {
		var Mid='<?php echo $M_id ?>';
			$.ajax({
				type: "GET",
				url: "?admin&page=adminmenu/menu_data&header=0&footer=0&M_id="+Mid,
				timeout: 15000,
				cache: false,
				processData: false,
				dataType: "text",
				contentType: "application/json; charset=utf-8"
				}).done(function(response) {
					function exists(rows, parentId){
						for(var i=0; i<rows.length; i++){
							if (rows[i].id == parentId) return true;
						}
						return false;
					}
					var result = undefined;
					var obj;
					if (response != null && response.length > 0) {
						try {
						    result = $.secureEvalJSON(response);
							var obj = jQuery.parseJSON(response);
						}catch(err) {
						   result = response;
							obj = jQuery.parseJSON(result);
						}
						
						var tree = [];			
						for(var idx=0;idx<obj.length;idx++) {					
							var menu = obj[idx];
							if(!exists(obj,menu.pid)){
								tree.push({
									id:menu.id,
									text:menu.name,
									attributes: {
										pid: menu.pid,
										type: menu.type,
										key: menu.key
									}
								});
							
							}
						}
						
						var toDo = [];
						for(var i=0; i<tree.length; i++){
							toDo.push(tree[i]);
							root++;
						}
						
						while(toDo.length){
							var node = toDo.shift();	// the parent node
							t++;
							if(t==1){
								n1=node.id;
							}
							if(t==2){
								n2=node.id;
							}
							if(t==3){
								n3=node.id;
							}
							for(var i=0; i<obj.length; i++){
								var row = obj[i];
								if (row.pid == node.id){
									var child = {
										id:row.id,
										text:row.name,
										attributes: {
											pid: row.pid,
											type:row.type,
											key: row.key
										}
									};
									if (node.children){
										node.children.push(child);
									} else {
										node.children = [child];
									}
									if (t==1){//判断加几个子菜单
										c1++;
									}
									if(t==2){
										c2++;
									}
									if(t==3){
										c3++; 
									}
								}
							}
						}						
					}		
					$('#ttre').tree('loadData', tree);
					var mid='<?php echo $menusecid ?>';
					var selectNodes=$('#ttre').tree('find',mid);
					if((selectNodes!=null)&&(selectNodes.target!=null)){	
						$('#ttre').tree('select',selectNodes.target);
						trclick(selectNodes);
					}
															
				}).fail(function(jqXHR) {
					 var errMsg = undefined;
					 if (jqXHR.readyState === 4) {
						try {
							errMsg = $.secureEvalJSON(jqXHR.responseText);
						}
						catch(err) {
							errMsg = {code:"SERVER_FAILURE"};
						}
				
					}else {
						errMsg = {code:"SERVER_UNREACHABLE"};
					}
				});		
		}

		$(".addmenu").click(function(e){
		var M_id='<?php echo $M_id ?>';
			if(root>2){
				alert("根目录已经达到3个，不能创建！");
			}else{
				window.open('?admin&page=adminmenu/add_menu_dlg&menuId=-1&header=0&footer=0&M_id='+M_id,'_blank','height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no');
			}
		})
		
		function back(){
			var M_id='<?php echo $M_id ?>';
			$.ajax({
				url: '?admin&page=adminmenu/menu_public_update_create&header=0&footer=0&M_id='+M_id,  
				type: "POST",
				dataType: 'json',
				error: function(){  
					alert('Error loading XML document');  
				},  
				success: function(data,status){//如果调用php成功
					alert(data.message);
				}
			});	
		}
		
		function delmenu(){
			var M_id='<?php echo $M_id ?>';
			if(confirm("确定删除吗？")){
				$.ajax({
					url: '?admin&page=adminmenu/menu_public_update_delete&header=0&footer=0&M_id='+M_id,  
					type: "POST",
					dataType: 'json',
					error: function(){  
						alert('Error loading XML document');  
					},  
					success: function(data,status){//如果调用php成功
						alert(data.message);
						window.location.reload();
					}
				});	
			}
		}
	</script>
	</body>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
