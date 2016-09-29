<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>


<?php
    include '../common/dbaccessor.php';
	include '../common/web_constant.php';
	$siteId=$_GET["siteId"];
	//获取所有的menu
	$menu_list=web_admin_list_menu($siteId);
	$logo=web_admin_get_site_logo($siteId);
	$bac_img=web_admin_get_site_bacimg($siteId);
	if(isset($_POST['submit'])){    
		echo "<script language='javascript'></script>";       
		
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
	<title>首页定制</title>
</head>

<body>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<form action="" method="post" enctype="multipart/form-data"> 
				<div>
					<div class="main-titlenew">
						<div class="title-1">当前位置：微官网 > 图标风格主题 > <font class="fontpurple"><?php if(!isset($_GET['isupdate'])){echo "创建新站点第三步：首页定制>";}else{echo "首页定制更新";} ?> </font></div>
					</div>
					<div class="bgimg"></div>
					<input type="submit" class="btn btn-primary" name="submit" style="margin:10px 0px 0px 0px; width: 120px" value="完成" />
					<div class="panel panel-info upload-panel">
						<div class="panel-heading">LOGO定制<button type="button" onClick='delLogo(<?php echo $siteId ?>)' class="btn btn-sm btn-info upload-btn">清除LOGO图片</button><button type="button" onClick='updateLogo(<?php echo $siteId ?>)' class="btn btn-sm btn-warning upload-btn">上传LOGO图片</button></div>
						<div class="panel-body">
						<?php 
						$upload =wp_upload_dir();
						foreach($logo as $logoinfo){	
							if((empty($logoinfo->site_value))||(stristr($logoinfo->site_value,"http")!==false)){
								$logo=$logoinfo->site_value;
							}else{
								$logo=$upload['baseurl'].$logoinfo->site_value;
							}
							echo "<img src='{$logo}' height='150' width='150'/>";
						}
						?>
						</div>
					</div>
					<div class="panel panel-info upload-panel">
						<div class="panel-heading">首页背景图片配置<button type="button" onClick='updateBaImg(<?php echo $siteId ?>)' class="btn btn-sm btn-warning upload-btn">上传背景图片</button></div>
						<div class="panel-body">
							<?php 
							$upload =wp_upload_dir();
							foreach($bac_img as $bac_img_info){
								if((empty($bac_img_info->site_value))||(stristr($bac_img_info->site_value,"http")!==false)){
									$bacimg=$bac_img_info->site_value;
								}else{
									$bacimg=$upload['baseurl'].$bac_img_info->site_value;
								}
								
								
								echo "<img src='{$bacimg}' height='150' width='150'/>";
							}
							?>
						</div>
					</div>
					<div class="panel panel-info upload-panel">
						<div class="panel-heading">首页菜单按钮定制 （注：最多可添加六个菜单按钮）
						<!--a href="#"
								onclick="javascript:window.open('web_manage_theme_v2_custom_made_menu_insert_dialog.php?siteId=<?php echo $siteId?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"-->
							<button type="button" onclick="javascript:window.open('menu_insert_dialog.php?siteId=<?php echo $siteId?>','_blank','height=480,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" class="btn btn-sm btn-warning upload-btn">添加菜单按钮</button>
						<!--/a-->
						</div>
						<div class="panel-body">
							<form class="navbar-form navbar-left" role="form">
							<?php foreach($menu_list as $menu){			
								echo "";
								echo "<div class='form-group'>";
								echo "菜单名称： <input name='post_titles[]' type='text' class='form-control' style='width:60%;margin-bottom:5px' id='post_titles' value='{$menu->post_title}' maxlength='50' />  ";
								echo "<input type='button' onClick='deleMen({$menu->ID})' class='btn btn-sm btn-default' name='del' id='buttondel' value='删除菜单' class='btn_add'> " ;
								echo "<input type='button' onClick='updateMen({$menu->ID},{$siteId})' class='btn btn-sm btn-primary' name='upd' id='buttonupd' value='修改菜单' class='btn_add'> " ;
								echo "<input name='post_ids[]' type='hidden' id='post_ids' value='{$menu->ID}' maxlength='50' />  ";
								echo "</div>";
							}
							?>
							</form>
						</div>
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
		createXMLHttpRequest();
		xmlHttp.open("GET","logo_delete.php?beIframe&siteid="+siteid,true);
		xmlHttp.onreadystatechange = function(){
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				alert("删除成功");
			window.location.reload();
		}
		xmlHttp.send(null);
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
	
		
</script>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
