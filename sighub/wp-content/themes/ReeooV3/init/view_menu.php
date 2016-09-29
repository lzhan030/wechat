<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';


$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
require_once ('../wesite/common/dbaccessor.php');
require_once ('../wechat/common/wechat_dbaccessor.php');
$menusecid=$_GET["menusecid"];
$newid=$_GET["newid"];
$savnewid=$_GET["newid"];
$needle=$_SERVER['HTTP_HOST'];

$M_id=$_GET["Mid"];
$M_name=$_GET["Mname"]
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="/wp-content/themes/ReeooV3/css/wsite.css" />
		<link rel="stylesheet" href="/wp-content/themes/ReeooV3/css/bootstrap.min.css">
		<script src="/wp-content/themes/ReeooV3/js/jquery.min.js"></script>
		<script src="/wp-content/themes/ReeooV3/js/bootstrap.min.js"></script>
		<title>菜单管理</title>
	</head>
	
	<body>	
		<div id="primary" class="site-content">
			<div id="content" role="main">
				<!--<form action="<?php echo constant("CONF_THEME_DIR");  ?>/wechat/menu/menu_create.php?beIframe" method="post" > 
				
					<td>
						<input type="submit" class="btn btn-primary" value="确认添加菜单" style="width:120px" />	
					</td>
								
				</form>-->
<!--			<form action="<?php echo constant("CONF_THEME_DIR");?>	/wechat/menu/menu_delete.php?beIframe" method="post" > 					
					<td>
						<input type="submit" class="btn btn-primary" value="确认删除菜单" style="width:120px" />	
					</td>								
				</form>-->
			</div>
		</div>
	</body>
</html>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="/wp-content/themes/ReeooV3/js/jquery.min.js" type="text/javascript"></script>
	<script src="/wp-content/themes/ReeooV3/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="/wp-content/themes/ReeooV3/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="/wp-content/themes/ReeooV3/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="/wp-content/themes/ReeooV3/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/editor/themes/default/default.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/easyui/themes/default/easyui.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/easyui/themes/icon.css" />
	<script charset="utf-8" src="/wp-content/themes/ReeooV3/js/editor/kindeditor.js"></script>
	<script charset="utf-8" src="/wp-content/themes/ReeooV3/js/editor/lang/zh_CN.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/easyui/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/easyui/locale/easyui-lang-zh_CN.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/easyui/addin/datagrid-detailview.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.json-2.4.min.js"></script>
	</head>
	<body>
		<div class="main-title">
			<div class="title-1">当前位置：公共微信服务号菜单预览> <font class="fontpurple">菜单预览 </font>
			</div>
		</div>
		<div id="primary" class="site-content">
			<div id="content" role="main" style="margin: 10px 0 0 45px; width:80%;">
				<form role="form" name="updatemenu" onSubmit="return validateform()" action="?admin&page=adminmenu/menu_up&header=0&menuId=<?php echo $menuId;?>" method="post" enctype="multipart/form-data"> 
					<div class="form-group">
						<label for='pic' style='font-size:16px;margin-top:10px; margin-left:10px;'>菜单名称：</label>
						<input type="text" class="form-control" name="menuname" value="<?php echo $M_name;?>" style="margin-bottom:30px; width:40%;" disabled />
					</div>
					<div style="margin-top:45px; float:right;">
						<input type="button" class="btn btn-sm btn-default" style="width:120px" value="关闭" onclick="Cancel()" />	
					</div>
				</form>
			</div>	
		</div>

		<div  style="position: absolute;left: 45px;top: 135px;">
				<!--input class="menu btn btn-primary" type="button" value="添加根菜单" style="margin-bottom:10px; position:relative" />-->
			<ul id="ttre" class="easyui-tree"></ul>
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

		
	
		
		$(document).ready(function(e) {
			//放在这个位置，包含autoclick，避免图文切换文本时editor不存在
			KindEditor.ready(function(K) {
				window.editor = K.create('#editor_id', {
					items:["emoticons","link","unlink"],
					afterCreate:function(){
						menuPage();
					}
				}); //配置kindeditor编辑器的工具栏菜单项	
				
			});			
		});	
		
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
						  /*
						 alert('you dbclick '+node.text);
						 alert('you dbclick '+node.id);
						 alert('you dbclick '+node.attributes.pid);
						 alert('you dbclick '+node.attributes.type);
						 */
						//if(node.attributes.pid!=-1){
							//$("#siurl").empty();
						//$("#sourl").empty();
							togchild(node.id,node.attributes.type,node.attributes.key,node.attributes.pid,node.text);	
						//}
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
						 //$('#ttre').tree('select', node.target);
						 //currentId = node.id;
						//e.preventDefault();  
						/* if (!$('#tmenu').length){ 
							createColumnMenu(field.attributes.pid);  
						}   */
						
						createColumnMenu(field.attributes.pid,field.id);
						$('#tmenu').menu('show', {
							left:e.pageX,  
							top:e.pageY  
						});  	
					}
				 });			
			//menuPage();	
		})
		
		function menuPage() {
			var Mid='<?php echo $M_id ?>';
			$.ajax({
				type: "GET",
				url: "<?php bloginfo('template_directory'); ?>/init/menu_data.php?mid="+Mid,
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
							//alert(response.list[0].text);
							var obj = jQuery.parseJSON(response);
							//alert(obj[0].name);
						}catch(err) {
						   result = response;
							obj = jQuery.parseJSON(result);
							//alert(obj[0].name);
							//alert(obj.length);
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
							// get the children nodes
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
									toDo.push(child);
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
		
	function Cancel(){
		window.close();
	}
	</script>
		
		
	</body>
</html>