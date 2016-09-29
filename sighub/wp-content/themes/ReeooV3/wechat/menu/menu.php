<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); 
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
include '../../wesite/common/web_constant.php';
include '../common/wechat_dbaccessor.php';
include 'menu_permission_check.php';
$menusecid=$_GET["menusecid"];//used for selected
$newid=$_GET["newid"];
$savnewid=$_GET["newid"];
$needle=home_url()."/?site";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/editor/themes/default/default.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/easyui/themes/default/easyui.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/easyui/themes/icon.css" />
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/wsite.css" />
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/bootstrap.min.css">
	<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.min.js"></script>
	<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/bootstrap.min.js"></script>
	<script charset="utf-8" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/editor/kindeditor.js"></script>
	<script charset="utf-8" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/editor/lang/zh_CN.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/easyui/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/easyui/locale/easyui-lang-zh_CN.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/easyui/addin/datagrid-detailview.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.json-2.4.min.js"></script>
	</head>
	<body>
	<div class="main_auto">
		<div class="main-title" style="height:100px;width:100%;">
			<div class="title-1">当前位置：个人微信服务号菜单管理> <font class="fontpurple">菜单内容设置 </font></div>
			<!--菜单微信交互-->
			<div id="primary" class="site-content" style="clear:both;">
				<div id="content" role="main">
					<input type="button" onclick="deletemenu()" class="btn btn-default" value="清空菜单" style="width:120px;float:right;margin:20px 8px 0 0;" />	
					<input type="button" onclick="uploadmenu()" class="btn btn-primary" value="上传菜单" style="width:120px;float:right;margin:20px 15px 0 0;" />	
				</div>
			</div>
			<!--菜单微信交互END-->
		</div>
		<div style="clear:both; float:left;">
			<input class="menu btn btn-primary" type="button" value="添加根菜单" style="margin-bottom:10px; margin-top:12px;position:relative;width: 120px;" />
			<ul id="ttre" class="easyui-tree"></ul>
		</div>		
		<div class="main keyWordMain" style="margin-left:150px; width:700px;height:700px;">
			<!--TAB-->
			<div id="nav-main" class="navs" style="margin-bottom:10px; width:650px;">
				<ul class="nav nav-tabs" style="height:42px;" >
					<li class="active selected"><a href="javascript:void(0)">文本</a></li>
					<li ><a href="javascript:void(0)">图文消息</a></li>
					<li ><a href="javascript:void(0)">链接到网页</a></li>
				</ul>				
			</div>
			<!--TAB END-->
			<!--多图文-->
			<div class="main_bd">
				<div  style="width:60%">
					<form action="" method="post">
						<td>							
							<input class="newsconnect btn btn-primary" type="button"  value="保存"/>
							<input class="newsunconnect btn btn-default" type="button" value="删除"/>	
							<input type='button' onClick='selectNews()' class='btn btn-warning' name='del' id='buttondel' value='选择多图文素材' class='btn_add'/>
						</td>
						<table class="table table-striped" width="450"  border="1" align="center">
							<tr>
							</tr>
						</table>
					</form>
				</div>
				<div class="left nub0" >
					<div  class="pre-title0">
						<div class="pre-bg" >
							<p>封面图片</p>
						</div>
						 <span class="title1">标题</span> 
						 <input class="newsUrl" type="text" style="display:none" value=""/>
						 <input class="newsId" type="text"style="display:none"  value=""/>
					</div><!--封面-->
					<div class="pre-title1 pre-title" >
						<span class="title1" >标题</span>
						 <input class="newsUrl" type="text" style="display:none" value=""/>
						 <input class="newsId" type="text"style="display:none"  value=""/>
						<div > <span >缩略图</span> </div>						
					</div><!--标题-->					
				</div><!--left-->				
			</div>
			<!--多图文END-->
			<!--文本-->			
			<div class="textNews">				
				<div class="nub-1">
					<input class="connect btn btn-primary" type="button" value="保存" style="margin-bottom:10px" />
					<input class="unconnect btn btn-default" type="button" value="删除" style="margin-bottom:10px"/>	
					<textarea id="editor_id" name="sendContent" cols="42" rows="11" style="width:650px;hight=215px" ></textarea>				
				</div>
			</div>
			<!--文本END-->
			<!--链接-->				
			<div class="webUrl">				
				<div class="nub-1">
					<input class="urlconnect btn btn-primary" type="button" value="保存" style="margin-bottom:10px" />
					<input class="urlunconnect btn btn-default" type="button" value="删除" style="margin-bottom:10px"/>	
					<p><b>添加链接:</b></p>
					<input type="radio" name="menuUrl" id="menuinUrl" value="0" checked="checked">
					<span> 添加内链</span>
					<input type="button" onClick="selectSite()" class="btn btn-sm btn-primary" id="menu" value="点击选择微官网站点" />
					<input id="siurl" type='text' name='meniUrl' value='' class="form-control" readonly='true' style="margin-top:5px;"/>
					<!--添加外联-->
					<br/>
					<input type="radio" name="menuUrl" value="1">
					<span> 添加外链(请以http://或https://开头,最长256个字节)</span>
					<input id="sourl" type="text" class="form-control" name="menoUrl" value="" />
				</div>
			</div>
			<!--链接END-->	
            <div class="black-bg"></div>
		</div>
	</div>
	<?php		
		$nc=wechat_get_news_act_group($_SESSION['GWEID']);//拿到标志用一个多图文的id
		$i=1;
		$newsay=array();
		foreach($nc as $ns){			
			$newsay[$i]=$ns->news_item_id;
			$i++;
		}
		$news_count=count($nc);		
	?>
		
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

		//选择多图文素材
		function selectNews(){	   
			if(menuId==undefined){
				alert("请先选中一个菜单进行编辑");
			}else if(((menuId==n1)&&(c1>0))||((menuId==n2)&&(c2>0))||((menuId==n3)&&(c3>0))){			
				alert("根菜单有下级子菜单不能设置回复内容");
			}else{
				if(menuId==""){
					menuId=-1;
					menuType="weChat_news";	
				}
				window.open('menu_news_select.php?beIframe&menuId='+menuId+'&menuType='+menuType+'&menuKey='+menuKey+'&menuPad='+menuPad,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
			}
		}
		
		//微信上传菜单
		function uploadmenu(){
			$.ajax({
				url: "<?php echo constant("CONF_THEME_DIR");  ?>/wechat/menu/menu_create.php?beIframe",  
				type: "POST",
				dataType: 'json',
				error: function(){  
					alert('Error loading XML document');  
				},  
				success: function(data,status){//如果调用php成功
					alert(data.message);
					var url="<?php echo get_template_directory_uri(); ?>";
					location.href=url+'/wechat/menu/menu.php?beIframe';
				}
			});		
		}
		
		//微信删除菜单
		function deletemenu(){
			if(confirm("确定删除吗？")){
				$.ajax({
					url: "<?php echo constant("CONF_THEME_DIR");  ?>/wechat/menu/menu_delete.php?beIframe",  
					type: "POST",
					dataType: 'json',
					error: function(){  
						alert('Error loading XML document');  
					},  
					success: function(data,status){//如果调用php成功
						alert(data.message);
						var url="<?php echo get_template_directory_uri(); ?>";
						location.href=url+'/wechat/menu/menu.php?beIframe';
					}
				});	
			}
		}
		
		//封装多图文
		var itemTitle=new Array();
		var picUrl=new Array();
		var itemId=new Array();
		var itemUrl=new Array();
		var newsItemId=new Array();
		var n_count=<?php echo $news_count ?>;		
		for(var i=0;i<n_count+5;i++){
			itemTitle[i]=new Array();
			picUrl[i]=new Array();
			itemId[i]=new Array();
			itemUrl[i]=new Array();		
		}				
		var newsCount=new Array();	
	<?php		
		for($s=1;$s<=$news_count;$s++){									
			$i=0;
			$j=0;
			$k=0;
			$v=0;			
			$materials=wechat_news_get($newsay[$s]);		
			$upload =wp_upload_dir();
			foreach($materials as $material){				
				/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
				$tmp = stristr($material->news_item_url,"http");
				if(($tmp===false)&&(!empty($material->news_item_url))){
					$newsitemurl=home_url().$material->news_item_url;
				}else{				
					$newsitemurl=$material->news_item_url;
				}
				if((empty($material->news_item_picurl))||(stristr($material->news_item_picurl,"http")!==false)){
					$newsitempicurl=$material->news_item_picurl;
				}else{
					$newsitempicurl=$upload['baseurl'].$material->news_item_picurl;
				}
				echo "itemTitle[".$s."][".$i++."]=\"".$material->news_item_title."\";\n";		
				echo "picUrl[".$s."][".$j++."]=\"".$newsitempicurl."\";\n";
				echo "itemId[".$s."][".$k++."]=\"".$material->news_id."\";\n";
				echo "itemUrl[".$s."][".$v++."]=\"".$newsitemurl."\";\n";
			}	
			$newsc=wechat_get_news_count($newsay[$s]); 
			echo "newsItemId[".$s."]=\"".$newsay[$s]."\";\n";	
			foreach($newsc as $newc){				
				echo "newsCount[".$s."]=\"".$newc->counts."\";\n";				
			}		
		}	
	?>
				
		$(document).ready(function(e){//循环出所有多图文个数的框架			
			var m=<?php echo $news_count ?>;//拿到多图文总的条数		
			for(var j=1;j<m;j++){	
				var $addLeft=$(".left.nub0").clone("deep");			
				$addLeft.removeClass().attr("class","left nub"+j);
				$addLeft.insertAfter(".left.nub"+(j-1));//在left.nubi后面加上addLeft
			}
		})

		$(document).ready(function(e) {			
			var m=<?php echo $news_count ?>;//多图文的显示
			for(var s=1;s<=m;s++){		
				var n=newsCount[s];	
				//a是封面+第一个小图文
				var a=".nub"+(s-1)+" .pre-title0,.nub"+(s-1)+" .pre-title1";
				//b是第一个小图文
				var b=".nub"+(s-1)+" .pre-title1";				
				//刚开始封面+第一个小图文框架循环出来，现在是根据每条多图文的个数再把各自的框架显示出来
				if(n<1){$(a).css("display","none");}
				if(n<2){$(b).css("display","none");}
				if(n>2){	
					for(var j=2;j<n;j++){	
						var cloneUrl=".nub"+(s-1)+">.pre-title1";
						var $add=$(cloneUrl).clone("deep");
						var changeId="pre-title"+j+" pre-title";				
						$add.attr("class",changeId);
						$(".nub"+(s-1)).append($add);
					}
				}
				
				//上面各自多图文的结构出现后，现在开始赋值
				for(var k=0;k<n;k++){	 			 
					var changeId=".nub"+(s-1)+">.pre-title"+k;
					var bgid=".nub"+(s-1)+">.pre-title"+k+">div";
					var textid=".nub"+(s-1)+">.pre-title"+k+" span.title1";
					var newsUrl=".nub"+(s-1)+">.pre-title"+k+" input.newsUrl";
					var newsId=".nub"+(s-1)+">.pre-title"+k+" input.newsId";		  
			  
					var url="url("+picUrl[s][k]+")";
					var text=itemTitle[s][k];
					var news_Url=itemUrl[s][k];
					var news_Id=itemId[s][k];
								
					$(newsUrl).val(news_Url);						
					$(newsId).val(news_Id);				  
					$(bgid).css("background",url);
					$(bgid).css("background-size","100% 100%");
					$(textid).text(text);		  			 
				}	
				//全部读出来先隐藏，点击menu时显示对应的
				$(".left").hide();
			}				
		})
				
		//点击子菜单
		<?php 
		global $newid;
		if($newid==null){
			$newid=-1;
		}
		?>
		newid=<?php global $newid; echo $newid; ?>;
		function togchild(menuId,menuType,menuKey,menuPad,menuName){//菜单点击事件
			$(".left").hide();
			editor.html("");
			$("#siurl").attr("value","");
			$("#sourl").attr("value","");
			var textContent;
			var murl;
			var item;
			//如果newsid=-1就正常显示，如果是文本就显示文本，多图文就显示多图文
			//如果newsid不是-1，就是用于选择多图文后的显示，此时还没有保存
			if(newid==-1){
				if(menuType=="weChat_text"){
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
								if($(".textNews").css("display")=="none"){
									$("#nav-main ul li:first-child").trigger("click");
								}
							}
						});
				}else if(menuType=="weChat_news"){
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
					editor.html("");
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
								$("#siurl").val(murl);
								$("#sourl").val("");								
							}else{
								$(':radio[name="menuUrl"][value="1"]').attr("checked","checked");
								$("#sourl").val(murl);
								$("#siurl").val("");
							}
														
							if($(".webUrl").css("display")=="none"){$("#nav-main ul li:nth-child(3)").trigger("click")}
							}
						});
				}else{
					document.getElementById("menuinUrl").checked=true;
					if($(".textNews").css("display")=="none"){
						$("#nav-main ul li:first-child").trigger("click");
					}
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
				editor.html("");
			}
			
		}
		
		//文本保存
		$(".connect").click(function(e){
			editor.sync();
			var content=document.getElementById('editor_id').value;	
		
			if(content==""){
				alert("请输入文本内容");
			}else if(menuId==undefined){
				alert("请先选中一个菜单进行编辑");
			
			}else{ 
				
				if(menuPad!=-1)	{
					menuType="weChat_text";
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
					if(((menuId==n1)&&(c1>0))||((menuId==n2)&&(c2>0))||((menuId==n3)&&(c3>0))){	
						alert("根菜单有下级子菜单不能设置回复内容");
					}else{
						menuType="weChat_text";
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
					}		
				}
			}
		})
		
		//文本删除
		$(".unconnect").click(function(e){
			editor.sync();
			var content=document.getElementById('editor_id').value;	
			if(menuType=="weChat_text")	{
				if(confirm("确定删除吗？")){	
					createXMLHttpRequest();
					xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_content_delete.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuKey="+menuKey,true);
					xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert(" 删除成功");
							//window.location.reload()会导致GET到的menusec不会丢导致默认选中不友好
							var url="<?php echo get_template_directory_uri(); ?>";
							location.href=url+'/wechat/menu/menu.php?beIframe';
						}				
					}
					xmlHttp.send(null);	
				}
			}else{
				alert("没有找到删除内容，删除失败");			
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
		//多图文保存
		$(".newsconnect").click(function(e){			
			//设置为-1是防止点击其他的关键词时，仍然使用该id
			if(savnewid==-1){
				alert("您当前没有选择新的多图文");
			}else if(menuId==undefined){
				alert("请先选中一个菜单进行编辑");
			
			}
			else{ 	
				if(menuPad!=-1)	{
					var menuType="weChat_news";
					jQuery.post(
				        "<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId,
				        {menuType:menuType,menuName:menuName,menuPad:menuPad,menuKey:savnewid},
				        function(data, textStatus, jqXHR){
							alert("保存成功");
							var url="<?php echo get_template_directory_uri(); ?>";
							location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;	
				        },
				        "text"
				    );	
				}else{
				
					if(((menuId==n1)&&(c1>0))||((menuId==n2)&&(c2>0))||((menuId==n3)&&(c3>0))){	
						alert("根菜单有下级子菜单不能设置回复内容");
					}else{
						var menuType="weChat_news";
						jQuery.post(
					        "<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId,
					        {menuType:menuType,menuName:menuName,menuPad:menuPad,menuKey:savnewid},
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
				savnewid=-1;								
		})
		//多图文删除
		$(".newsunconnect").click(function(e){	   
			if(menuType=="weChat_news"){				
					if(confirm("确定删除吗？")){	
						createXMLHttpRequest();
						xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_content_delete.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuPad="+menuPad+"&menuKey="+savnewid,true);
						xmlHttp.onreadystatechange = function(){
							if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
								alert("删除成功");
								var url="<?php echo get_template_directory_uri(); ?>";
								location.href=url+'/wechat/menu/menu.php?beIframe';
							}
						}
						xmlHttp.send(null);
					}
				}else{
					alert("没有找到删除内容，删除失败");
				}
			})
		
		
		//链接保存
		$(".urlconnect").click(function(e){		
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
			if(menuurl==""){
				alert("请选择或添加一个网址");
			}else if(menuId==undefined){
				alert("请先选中一个菜单进行编辑");
			
			}else{ 
				if(menuPad!=-1)	{
					menuType="view";
					jQuery.post(
				        "<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId,
				        {menuType:menuType,menuName:menuName,menuKey:menuurl,menuPad:menuPad},
				        function(data, textStatus, jqXHR){
							alert("保存成功");
							var url="<?php echo get_template_directory_uri(); ?>";
							location.href=url+'/wechat/menu/menu.php?beIframe&menusecid='+menuId;
				        },
				        "text"
				    );
				}else{				
					if(((menuId==n1)&&(c1>0))||((menuId==n2)&&(c2>0))||((menuId==n3)&&(c3>0))){	
						alert("根菜单有下级子菜单不能设置回复内容");
					}else{
						menuType="view";
						jQuery.post(
					        "<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_update.php?beIframe&menuId="+menuId,
					        {menuType:menuType,menuName:menuName,menuKey:menuurl,menuPad:menuPad},
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
		//链接删除
		$(".urlunconnect").click(function(e){			
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
				
			if(menuType=="view")	{
				if(confirm("确定删除吗？")){
					createXMLHttpRequest();
					xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_content_delete.php?beIframe&menuId="+menuId+"&menuType="+menuType+"&menuName="+menuName+"&menuKey="+encodeURIComponent(menuurl)+"&menuPad="+menuPad,true);
					xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("删除成功");
							var url="<?php echo get_template_directory_uri(); ?>";
							location.href=url+'/wechat/menu/menu.php?beIframe';
						}				
					}
					xmlHttp.send(null);	
				}
			}else{
				alert("没有找到删除内容，删除失败");			
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
		
		
		$(document).ready(function(e) {
			//放在这个位置，包含autoclick，避免图文切换文本时editor不存在
			KindEditor.ready(function(K) {
				window.editor = K.create('#editor_id', {
					items:["link","unlink"],
					afterCreate:function(){
						menuPage();
					}
				});
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
				
				function createColumnMenu(id,fid){ 
					$('#tmenu').remove();
				  var tmenu = $('#tmenu');
				if (!$('#tmenu').length){ 
					tmenu = $('<div id="tmenu" style="width:100px;"></div>').appendTo('body');  
				} else {
					var tmenu = $('#tmenu');
					tmenu = $('<div id="tmenu" style="width:100px;"></div>').appendTo('body');
				}
				$('<div iconCls="icon-ok" id="新建"/>').html("新建").appendTo(tmenu);
				$('<div iconCls="icon-ok" id="修改"/>').html("修改").appendTo(tmenu);
				$('<div iconCls="icon-ok" id="删除"/>').html("删除").appendTo(tmenu); 			
				tmenu.menu({  
					onClick: function(item){  
						if (item.id=='新建'){
							if(fid==n1){
							if(c1>4){
								alert("最多建五个子菜单");
							}else{
								window.open("add_menu_dlg.php?menuId="+tcurrentId+"&parid="+tparenId+"&ismenuid="+tcurrentId,"_blank","height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
							}
						}else if(fid==n2){
							if(c2>4){
								alert("最多建五个子菜单");
							}else{
								window.open("add_menu_dlg.php?menuId="+tcurrentId+"&parid="+tparenId+"&ismenuid="+tcurrentId,"_blank","height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
							}
						}else if(fid==n3){
							if(c3>4){
								alert("最多建五个子菜单");
							}else{
								window.open("add_menu_dlg.php?menuId="+tcurrentId+"&parid="+tparenId+"&ismenuid="+tcurrentId,"_blank","height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
							}
						}else{
							alert("子菜单不能建立二级子菜单！");
						}
						} 
						else if(item.id=='修改'){
							window.open("update_menu_dlg.php?menuId="+tcurrentId+"&ismenuid="+tcurrentId,"_blank","height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
		
						}
						else{
							if(confirm("确定删除吗？")){
								$.ajax({
									url: "<?php echo constant("CONF_THEME_DIR"); ?>/wechat/menu/menu_del.php?beIframe&menuId="+tcurrentId,  
									type: "POST",
									dataType: 'json',
									error: function(){  
										alert('Error loading XML document');  
									},  
									success: function(data,status){//如果调用php成功
										alert(data.message);
										var url="<?php echo get_template_directory_uri(); ?>";
										location.href=url+'/wechat/menu/menu.php?beIframe';
									}
								});		
							}							
						}				
					}  
				});  
			} 
						
		})
		
		function menuPage() {
			$.ajax({
				type: "GET",
				url: "<?php bloginfo('template_directory'); ?>/wechat/menu/menu_data.php",
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
							var node = toDo.shift();// the parent node
							// get the children nodes
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
									toDo.push(child);
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

		$(".menu").click(function(e){
			if(root>2){
				alert("根目录已经达到3个，不能创建！");
			}else{
				window.open("add_menu_dlg.php?menuId=-1&ismenuid=-1","_blank","height=320,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
			}
		})
		
		function selectSite(){	
			var siteurl = document.getElementById("siurl").value;
			
			var sidsel=siteurl.split("?site=")[1];			
			window.open('../common/wesite_list.php?beIframe&sidsel='+sidsel,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		
		}		
	</script>		
	</body>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
