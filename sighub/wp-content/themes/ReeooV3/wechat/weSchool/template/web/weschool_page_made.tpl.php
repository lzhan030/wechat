<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<?php
//20140624 janeen update
//$weid=$_SESSION['WEID'];
$gweid=$_SESSION['GWEID'];
//end
?>
<?php
    //include '../common/dbaccessor.php';
	//include '../common/web_constant.php';
	$siteId=$_GET["siteId"];
	//获取所有的menu
	//$menu_list=web_admin_list_menu($siteId);
	//$logo=web_admin_get_site_logo($siteId);
	//$bac_img=web_admin_get_site_bacimg($siteId);
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
	<!--<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>-->
	<title>首页定制</title>
	
	<script>
	    function test()
		{
		   window.location.reload(); 
		}
	</script>
	
</head>

<body>
	<div id="primary" class="site-content">
		<div id="" role="main">
			<form action="<?php echo $this->createWebUrl('backindex',array());?>" method="post" enctype="multipart/form-data"> 
				<div>
					<!--<div class="main-title">
						<div class="title-1">当前位置：微学校 >  <font class="fontpurple"><?php //if(!isset($_GET['isupdate'])){echo "首页定制>";}else{echo "首页定制更新";} ?> </font></div>
					</div>
					<div class="bgimg"></div>-->
					<!--2014-07-08newadd-->
					<div style="margin-top:15px;">
						<table width="82%">
							<tr>	
								<td width="15%">
									<span for="inputInfo">微学校首页链接:</span>
								</td>
								<td>
									<!--<input type="text" class="form-control" id="inputInfo" value="http://2.wpcloudforsina.sinaapp.com/mobile.php?module=weSchool&do=index&gweid=<?php echo $gweid;?>">-->
									<input type="text" class="form-control" id="inputInfo" value="<?php echo home_url();?>/mobile.php?module=weSchool&do=index&gweid=<?php echo $gweid;?>">	
									
								</td>
							</tr>
						</table>
					</div>
					
					<!--<input type="submit" class="btn btn-primary" name="submit" style="margin:20px 0px 0px 0px; width: 120px" value="完成" />-->
					<input type="button" class="btn btn-primary" onclick="test()" name="submit" style="margin:20px 0px 0px 0px; width: 120px" value="完成" />
					
					<div class="panel panel-info upload-panel">
						<div class="panel-heading">首页背景图片配置
							<button type="button" onClick='updateBaImg(<?php echo $gweid; ?>)' class="btn btn-sm btn-warning upload-btn" >上传背景图片</button>
							<button type="button" onClick='deleBaImg(<?php echo $gweid; ?>)' class="btn btn-sm upload-btn btn-default" style="margin-right:3px;">恢复默认背景</button></div>
						<div class="panel-body">
							<?php  
								//$pic=$this -> doWebsdfjlsd($in,$r,$offset,$pagesize,$gweid);
								$upload =wp_upload_dir();
								foreach($pic as $bac_img_info)
								$bgp=$bac_img_info['bg_url'];
								if((empty($bgp))||(stristr($bgp,"http")!==false)){
									$weschoolbgp=$bgp;
								}else{
									$weschoolbgp=$upload['baseurl'].$bgp;
								}
								if($bgp==null){
									$bgppicurl=home_url()."/wp-content/themes/ReeooV3/images/schoolnew112.jpg";
								?>
								<img src='<?php echo $bgppicurl?>' height='150' width='150'/>
							<?php	
							}else 
							{ 
								echo "<img src='{$weschoolbgp}' height='150' width='150'/>";
							} 
							?>
						</div>
					</div>
					<div class="panel panel-info upload-panel">
						<div class="panel-heading">首页菜单按钮定制 
							<!--<button type="button" onclick="javascript:window.open('menu_insert_dialog.php?siteId=<?php echo $siteId?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" class="btn btn-sm btn-warning upload-btn">添加菜单按钮</button>-->
						</div>
						<div class="panel-body">
							<form class="navbar-form navbar-left" role="form">
							<div class='form-group'>菜单名称： <input name='post_titles[]' type='text' class='form-control' style='width:60%;margin-bottom:5px' id='post_titles' value='图片和视频' maxlength='50' readonly='readonly'/>  <input type='button' onClick='deleMen(1)' class='btn btn-sm btn-default' name='del' id='buttondel' value='恢复默认图标' class='btn_add'> <input type='button' onClick='updateMen(1)' class='btn btn-sm btn-primary' name='upd' id='buttonupd' value='修改菜单图标' class='btn_add'> <input name='post_ids[]' type='hidden' id='post_ids' value='1820' maxlength='50' /></div>
							<div class='form-group'>菜单名称： <input name='post_titles[]' type='text' class='form-control' style='width:60%;margin-bottom:5px' id='post_titles' value='作业' maxlength='50' readonly='readonly'/>  <input type='button' onClick='deleMen(2)' class='btn btn-sm btn-default' name='del' id='buttondel' value='恢复默认图标' class='btn_add'> <input type='button' onClick='updateMen(2)' class='btn btn-sm btn-primary' name='upd' id='buttonupd' value='修改菜单图标' class='btn_add'> <input name='post_ids[]' type='hidden' id='post_ids' value='1822' maxlength='50' />  </div>
							<div class='form-group'>菜单名称： <input name='post_titles[]' type='text' class='form-control' style='width:60%;margin-bottom:5px' id='post_titles' value='公告' maxlength='50' readonly='readonly'/>  <input type='button' onClick='deleMen(3)' class='btn btn-sm btn-default' name='del' id='buttondel' value='恢复默认图标' class='btn_add'> <input type='button' onClick='updateMen(3)' class='btn btn-sm btn-primary' name='upd' id='buttonupd' value='修改菜单图标' class='btn_add'> <input name='post_ids[]' type='hidden' id='post_ids' value='1824' maxlength='50' />  </div>
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
			//alert("执行删除操作");
			xmlHttp.open("GET","<?php echo $this->createWebUrl('deletemenupic',array());?>"+"&del="+id,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
				
				//window.location.reload()
					alert("默认图标恢复成功！");
				  window.location.reload();
				  }
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
	function updateMen(id){	   
	   //window.param=id;	 
	   window.open('<?php echo $this->createWebUrl('menuUpdate',array());?>'+'&sid='+id,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	   
	}
	
	//恢复默认背景
	function deleBaImg(id){	   
	   createXMLHttpRequest();
			//alert("执行删除操作");
			xmlHttp.open("GET","<?php echo $this->createWebUrl('deletebaimg',array());?>"+"&del="+id,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
				
				//window.location.reload()
					alert("默认背景恢复成功！");
				  window.location.reload();
				  }
			}
			xmlHttp.send(null); 
	}
	
	//更新logo
	function updateLogo(siteId){	   
	   window.param=siteId;
	   window.open('logo_update_dialog.php?siteId='+siteId,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	}
	
	
	//更新背景图片
	function updateBaImg(gweid){
	   window.param=gweid;
	   //window.open('baimg_update_dialog.php?siteId='+gweid,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	   window.open('<?php echo $this->createWebUrl('picUpdate');?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	   
	}
	
		
</script>
</html>
<?php //get_sidebar( 'front' ); ?>
<?php //get_footer(); ?>
