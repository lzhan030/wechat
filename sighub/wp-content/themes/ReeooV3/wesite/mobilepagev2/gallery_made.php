<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); ?>


<?php
    include '../common/dbaccessor.php';
	include '../common/web_constant.php';
	$siteId=$_REQUEST["siteId"];
	//获取所有的menu
	$gallery_list=web_admin_list_gallery($siteId);  	  
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
<title>无标题文档</title>
</head>

<div id="primary" class="site-content">
	<div id="content" role="main">
		<form action="" method="post" enctype="multipart/form-data"> 
			<div><!--主体-->
			<!--主体-标题-->
			<tr>
			<tr>
			<div class="main-title">
				<div class="title-1">　当前位置：创建新网站 > <font class="fontpurple">第四步：gallery定制 ></font></div>
				</div>
			<!--主体-标题结束-->
				
			<!--分割线-->
				<div class="bgimg"></div>
			<!--分割线结束-->

			<!--二级导航-->
				<div class="submenu">
				</div>
			<!--二级导航结束-->

			<!--内容开始-->
				<div><!--表单-->
					<table width="700" height="420" border="0" style=" margin-left:150px; margin-top:30px;">
						<tr>
							<td width="150"><font color="red">*</font>gallery定制</td>
							<td >							
							</td>
						</tr>						
						<tr>
							<td width="150">gallery图片定制</td>						
							<td width='350'>
									<?php  foreach($gallery_list as $gallery){
	
										$post_id=$gallery->ID;
			
										//在postmeta表里拿到gallery对应的图片的meta_value
										$gallery_img=web_admin_get_gallery_img($post_id);
										foreach($gallery_img as $gallery_img_info){
											$pic_id=$gallery_img_info->meta_value;
				
											//在post表里拿到图片的记录
											$pic_record=web_admin_get_gallery_img_record($pic_id);
											foreach($pic_record as $pic_record_info){			
																								
												$upload =wp_upload_dir();
												if((empty($pic_record_info->guid))||(stristr($pic_record_info->guid,"http")!==false)){
													$picrecord=$pic_record_info->guid;
												}else{
													$picrecord=$upload['baseurl'].$pic_record_info->guid;
												}	
												echo "<img src='{$picrecord}'  height='150' width='200'/>";	
												echo "<input type='button' onClick='deleGallery({$gallery->ID})' name='del' id='buttondel' value='-' class='btn_add'> " ;
												echo "<input type='button' onClick='updateGallery({$gallery->ID})' name='upd' id='buttonupd' value='updateGallery' class='btn_add'> " ;											
											}		
										}													
									 }									 
									?>								
								<div id="div_zc" class="dynInput">
									<!--<input name="post_titles[]" type="text" id="post_titles" value="" maxlength="50" /> -->
								</div>							
							</td>								
							<td>
								<a href="#"
								onclick="javascript:window.open('gallery_insert_dialog.php?beIframe&siteId='+<?php echo $siteId?>,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"><input type="button" onClick=  id="menu" value="addgallery"/>
								</a>
							</td>
						</tr>
						<tr>
							<td width="150"></td>
							<td width="150">
								<input type="submit" value="下一步" />	
							</td>
						</tr>										
					</table>
				</div>
			</div><!--主体结束-->
		</form>					
	</div>
</div>

	
<script language='javascript'>
		
	var xmlHttp;
    function createXMLHttpRequest(){
    if(window.ActiveXObject)
     xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    else if(window.XMLHttpRequest)
     xmlHttp = new XMLHttpRequest();
    }
	
	
	function deleGallery(id){
	   
		createXMLHttpRequest();
		xmlHttp.open("GET","gallery_delete.php?beIframe&galleryid="+id,true);
		xmlHttp.onreadystatechange = function(){
			//if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
			//alert("服务器返回: " + xmlHttp.responseText);
			window.location.reload();
		}
		xmlHttp.send(null);
	}
	
	
	
	function updateGallery(id){
	   
	   window.param=id;
	   window.open('gallery_made_update_dialog.php?galleryId='+id,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	}
			
</script>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
