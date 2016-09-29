<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); ?>


<?php
    include '../common/dbaccessor.php';
	include '../common/web_constant.php';
	global $wpdb;
	$siteId=intval($_GET["siteId"]); 
	$we7templateSelected=$_GET["we7templateSelected"];
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("orangesitemeta")."(site_id, site_key, site_value)VALUES (%d, %s, %s)",$siteId, 'we7templatestyle',$we7templateSelected));
	if(empty($we7templateSelected))
		$we7templateSelected = $wpdb -> get_var("SELECT site_value FROM {$wpdb -> prefix}orangesitemeta WHERE site_id='{$siteId}' AND site_key = 'we7templatestyle'");
	$template = $wpdb -> get_row("SELECT * FROM {$wpdb -> prefix}site_templates WHERE name='{$we7templateSelected}'");
	//获取所有的menu
	$menu_list=$wpdb -> get_results("SELECT * FROM {$wpdb -> prefix}site_nav WHERE `site_id`={$siteId} AND position=1");
	//获取所有的slide
	$background = $wpdb -> get_var("SELECT content FROM {$wpdb -> prefix}site_styles WHERE site_id = '{$siteId}' AND templateid = '{$we7templateSelected}' AND `variable`='indexbgimg'");
	$upload =wp_upload_dir();
	if((empty($background))||(stristr($background,"http")!==false)){
		$backgroundimg=$background;
	}else{
		$backgroundimg=$upload['baseurl'].$background;
	}
	
	
	
	$slides_list=$wpdb -> get_results("SELECT * FROM {$wpdb -> prefix}site_nav WHERE `site_id`={$siteId} AND position=3");
	$styles_indb = $wpdb -> get_results("SELECT variable,content FROM {$wpdb -> prefix}site_styles WHERE site_id = '{$siteId}' AND templateid = '{$we7templateSelected}'");
	$styles = array();
	foreach($styles_indb as $style_indb){
		$styles[$style_indb -> variable] = $style_indb -> content;
	}
	
	
	
	if(isset($_POST['submit'])){
?>
   <script>
		location.href="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/website_list.php?beIframe";
	</script>
<?php	}?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!--<script src="/wp-content/themes/silver-blue/js/jquery.min.js"></script>-->
	<link rel="stylesheet" type="text/css"  href="<?php bloginfo('template_directory'); ?>/css/webpage2.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css"/>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/script/colorpicker/spectrum.css">
	<script src="<?php bloginfo('template_directory'); ?>/we7/script/colorpicker/spectrum.js"></script>
	<title>首页定制</title>
</head>

<body>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<form action="" method="post" enctype="multipart/form-data"> 
				<div>
					<div class="main-title">
						<div class="title-1">当前位置：微官网 > 新图标风格主题 > <font class="fontpurple"><?php if(!isset($_GET['isupdate'])){echo "创建新站点第三步：首页定制>";}else{echo "首页定制更新";} ?> </font></div>
					</div>
					<div class="bgimg"></div>
					<input type="submit" class="btn btn-primary" name="submit" style="margin:10px 0px 0px 0px; width: 120px" value="完成" />
					<?php if($template -> background == '1'){ ?>
					<div class="panel panel-info upload-panel">
						<div class="panel-heading">首页背景图片配置<button type="button" onClick='updateBaImg(<?php echo $siteId ?>)' class="btn btn-sm btn-warning upload-btn">上传背景图片</button></div>
						<div class="panel-body">
							<img src='<?php echo $background?$backgroundimg:(home_url().'/wp-content/themes/mobilepagewe7/template/'."{$we7templateSelected}/images/bg_index.jpg")?>' height='191' width='115'/>
						</div>
					</div>
					<?php } ?>
					<?php if($template->menu == 1 ) {?>
					<div class="panel panel-info upload-panel">
						<div class="panel-heading">首页菜单按钮定制
						<!--a href="#"
								onclick="javascript:window.open('web_manage_theme_v2_custom_made_menu_insert_dialog.php?siteId=<?php echo $siteId?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"-->
							<button type="button" onclick="javascript:window.open('menu_insert_dialog.php?siteId=<?php echo $siteId?>','_blank','height=580,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" class="btn btn-sm btn-warning upload-btn">添加菜单按钮</button>
						<!--/a-->
						</div>
						<div class="panel-body">
							<?php
							foreach($menu_list as $menu){			
								echo "";
								echo "<div class='form-group'>";
								echo "菜单名称： <input name='post_titles[]' type='text' class='form-control' style='width:60%;margin-bottom:5px' id='post_titles' value='{$menu->name}' maxlength='50' />  ";
								echo "<input type='button' onClick='deleMen({$menu->id})' class='btn btn-sm btn-default' name='del' id='buttondel' value='删除菜单' class='btn_add'> " ;
								echo "<input type='button' onClick='updateMen({$menu->id},{$siteId})' class='btn btn-sm btn-primary' name='upd' id='buttonupd' value='修改菜单' class='btn_add'> " ;
								echo "<input name='post_ids[]' type='hidden' id='post_ids' value='{$menu->id}' maxlength='50' />  ";
								echo "</div>";
							}
							?>	
						</div>
					</div>
					<?php }?>
					<?php if($template -> slide == '1'){ ?>
					<div class="panel panel-info upload-panel">
						<div class="panel-heading">首页幻灯片定制 
							<button type="button" onclick="javascript:window.open('slide_insert_dialog.php?siteId=<?php echo $siteId?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" class="btn btn-sm btn-warning upload-btn">添加新幻灯片</button>
						</div>
						<div class="panel-body">
							<?php if($template->id == 15) {?>
								<div class="help-block">建议幻灯片图片像素为500*800</div>
							<?php }?>
							<?php
							foreach($slides_list as $slide){			
								echo "";
								echo "<div class='form-group'>";
								echo "幻灯片名称： <input name='post_titles[]' type='text' class='form-control' style='width:60%;margin-bottom:5px' id='post_titles' value='{$slide->name}' maxlength='50' />  ";
								echo "<input type='button' onClick='deleSlide({$slide->id})' class='btn btn-sm btn-default' name='del' id='buttondel' value='删除幻灯片' class='btn_add'> " ;
								echo "<input type='button' onClick='updateSlide({$slide->id},{$siteId})' class='btn btn-sm btn-primary' name='upd' id='buttonupd' value='修改幻灯片' class='btn_add'> " ;
								echo "<input name='post_ids[]' type='hidden' id='post_ids' value='{$slide->id}' maxlength='50' />  ";
								echo "</div>";
							}
							?>
						</div>
				</div>
				<?php }?>
				<div class="panel panel-info upload-panel">
					<div class="panel-heading">页面风格设置
						<button type="button" onclick="javascript:window.open('style_dialog.php?siteId=<?php echo $siteId?>','_blank','height=320,width=500,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" class="btn btn-sm btn-warning upload-btn">设置</button>
					</div>
				</div>
			</form>					
		</div>
	</div>
</body>
	
<script language='javascript'>
		
	var xmlHttp;
    function createXMLHttpRequest(){
    if(window.ActiveXObject)
     xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    else if(window.XMLHttpRequest)
     xmlHttp = new XMLHttpRequest();
    }
	
	//删除按钮
	function deleMen(id){	  
		createXMLHttpRequest();
		xmlHttp.open("GET","menu_delete.php?beIframe&menid="+id,true);
		xmlHttp.onreadystatechange = function(){
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
			alert("删除成功");
			window.location.reload();
		}
		xmlHttp.send(null);
	}
	function delLogo(siteid){	  
		if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			xmlHttp.open("GET","logo_delete.php?beIframe&siteid="+siteid,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
					alert("删除成功");
				window.location.reload();
			}
			xmlHttp.send(null);
		}
	}
	
	//更新按钮
	function updateMen(id,siteId){	   
	   //window.param=id;	 
	   window.open('menu_update_dialog.php?menuId='+id+'&siteId='+siteId,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	}
	
	//更新logo
	function updateLogo(siteId){	   
	   window.param=siteId;
	   window.open('logo_update_dialog.php?siteId='+siteId,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	}
	
	
	//更新背景图片
	function updateBaImg(siteId){
	   window.param=siteId;
	   window.open('baimg_update_dialog.php?siteId='+siteId,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	}
		//更新幻灯片
	function updateSlide(id,siteId){	   
	   //window.param=id;	 
	   window.open('slide_update_dialog.php?slideid='+id+'&siteId='+siteId,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	}
		//删除幻灯片
	function deleSlide(id){	  
		if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			xmlHttp.open("GET","slide_delete.php?beIframe&slideid="+id,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				alert("删除成功");
				window.location.reload();
			}
			xmlHttp.send(null);
		}
	}
	 	
		
</script>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
