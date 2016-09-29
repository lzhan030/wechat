<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

?>

<?php
    include '../common/dbaccessor.php';
	include '../../wechat/common/wechat_dbaccessor.php';
	include '../common/web_constant.php';
	global $current_user;
	//判断是否是分组管理员中的用户
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$user_id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	//$user_id = (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;

	//拿到window.open里传递过来的值	
	$slideid=intval($_GET["slideid"]);	
	$siteId=intval($_GET["siteId"]);	
		
	//获取特定的slide
	$slide=$wpdb -> get_results("SELECT * FROM {$wpdb -> prefix}site_nav WHERE `site_id`={$siteId} AND id={$slideid}"); 		
	//2014-06-27新增修改
	//是否选中是通过gweid，userid以及wid共同决定的,是由所有的号选中的功能的并集
	$GWEID = $_SESSION['GWEID'];
	$getwids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." WHERE GWEID = ".$GWEID."  AND user_id = ".$user_id);
	
	foreach($getwids as $getwid)
	{
		$wids = $getwid->wid;
		$wechatactivity_vip = web_admin_function_info_group($GWEID,"wechatvip",$wids,$user_id);
		if($wechatactivity_vip == 1){  //只要有一个公众号选择了这个功能即可显示出来
			break;
		}
	}
	foreach($getwids as $getwid)
	{
		$wids = $getwid->wid;
		$wechatschool = web_admin_function_info_group($GWEID,"wechatschool",$wids,$user_id);
		if($wechatschool == 1){  //只要有一个公众号选择了这个功能即可显示出来
			break;
		}
	}
	
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<link rel="stylesheet" href="../../css/wsite.css">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<title>更新幻灯片按钮内容</title>
</head>
<body>
	<div class="panel-body">
		<form name ="content" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilepagewe7/slide_update.php?beIframe" method="post" enctype="multipart/form-data">
			<table width="600" bordercolor=#06c border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px;">
			<?php 
			$upload =wp_upload_dir();
			foreach($slide as $slide_info){
				echo "<tr><td><label for='name'>幻灯片名称：</label>";
				echo "<input type='text' class='form-control' id='name' name='menu_title' value='{$slide_info->name}' /></td></tr>";
				echo "<tr><td><label for='pic'>上传幻灯片图片：</label></td></tr>";
				if($slide_info -> icon ==""){
					echo "<tr><td><img id='pic' src='' height='90' width='90' style='display:none;'/>";
				}else{					
					if(stristr($slide_info -> icon,"http")!==false){
						$upload_slide=$slide_info -> icon;
					}else{
						$upload_slide=$upload['baseurl'].$slide_info -> icon;
					}
					
					echo "<tr><td><img id='pic' src='{$upload_slide}' height='90' width='90'/>";
					echo "<tr><td><a id='picurl' href='#' onclick='delImage()'>删除图片</a>";
				}						
				echo "<input type='file' class='form-control' name='file' id='file' onchange='previewImage(this)' style='margin-bottom:30px;'/></td>";
								
				echo "</tr>";
				echo "<tr><td><label for='name'>页面模板:</label>";
				//$needle ="www";//判断是否包含这个字符
				//$needle=$_SERVER['HTTP_HOST']."/?page_id";
				//$needle=home_url()."/?page_id";
				$needle="/?page_id";
				$tmparray=stristr($slide_info -> url,$needle);
				$isvip="vip_detail";	
				$isvipreg="vip_register";	
				$mem=stristr($slide_info -> url,$isvip);
				$memreg=stristr($slide_info->url,$isvipreg);
				$isweschool = "index";
				$schoollink=stristr($slide_info -> url,$isweschool);
				
				$isvideo = "videolist";
				$videolink=stristr($slide_info -> url,$isvideo);
				$ishomework = "homeworklist";
				$homeworklink=stristr($slide_info -> url,$ishomework);
				$isnotice = "noticelist";
				$noticelink=stristr($slide_info -> url,$isnotice);
				
				/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
				$tmp = stristr($slide_info -> url,"http");
				if(($tmp===false)&&(!empty($slide_info -> url))){
					$slideurllink=home_url().$slide_info -> url;
				}else{				
					$slideurllink=$slide_info -> url;
				}	
				
				
				
				if($tmparray){
						//内链有效
						echo "<tr><td><input type='radio' name='menuUrl' value='0' checked='checked' onclick='disableOut()'><span> 内链 </span>";
					?>
						<!--转移到js里<input type="button" class="btn btn-xs btn-primary" onClick="javascript:window.open('../common/post_insert_dialog.php?refreshOpener=<?php echo 'v2yes'?>&beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $slideurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"
						   id="menu" value="页面编辑" style="width:120px;margin: 5px 0 7px 20px;"/>-->
								<input type="button" class="btn btn-xs btn-primary" onClick='ediPost()'
								id="menu" value="页面编辑" style="width:120px;margin: 5px 0 7px 20px;"/>						
								<input type="button" class="btn btn-xs btn-default" onClick="javascript:window.open('../common/page_list.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $slideurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" 
								id="meu" name="meu" value="文章管理" style="display: none"/>
					<?php
								echo "<input type='text' id='Wmenuurl' class='form-control' name='menuiUrl' value='{$slideurllink}' readonly='readonly'/>";
					?>
					<?php
								echo "</div>";
								echo "<div>";
								echo "<input type='radio' name='menuUrl' value='1' onclick='disableIn()'> <span> 外链（请以http://或https://开头） </span>";
								echo "<input type='text' class='form-control' name='menuoUrl' id='menuoUrl' value='' /></td></tr>";
								//echo "</div>";
								echo "<tr><td>";
						?>		
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
								<input type='radio' name='menuUrl' value='4' onclick='disable()'>
								<span>会员注册</span>
								<input style="visibility:hidden" type="text" class="form-control" name="memregUrl" id="memregUrl" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_register.php" disabled="disabled"/>
								</div>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
								<input type='radio' name='menuUrl' value='2' onclick='disable()'>
								<span>会员中心</span>
								<input style="visibility:hidden" type="text" class="form-control" name="memUrl" id="memUrl" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_detail.php" disabled="disabled"/>
								</div>
								
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
								<input type='radio' name='menuUrl' value='3' onclick='disable()'>
								<span>微学校</span>
								<input style="visibility:hidden" type="text" class="form-control" name="memwsUrl" id="memwsUrl" value="<?php echo home_url(); ?>/mobile.php?module=weSchool&do=index" disabled="disabled"/>
								</div>
								
								</td></tr>
						<?php
						} else{
					//外链或会员中心有效或微视频有效 
							if($mem){
								echo "<br/><input type='radio' name='menuUrl' value='0' onclick='disableOut()'> 内链";
					?>
								<input type="button" class="btn btn-xs btn-primary" onClick="javascript:window.open('../common/post_insert_dialog.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&refreshOpener=<?php echo 'v2yes'?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"
								id="menu" value="创建新页面" disabled="disabled" style="width:120px;margin: 5px 0 7px 20px;"/>
								<input type="button" class="btn btn-xs btn-default" onClick="javascript:window.open('../common/page_list.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $slideurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" 
								id="meu" name="meu" value="文章管理" style="display: none" disabled="disabled"/>
								<input id="Wmenuurl" class="form-control" type="text" name='menuiUrl' value='' style="margin-top:5px" readonly="readonly"/>
					<?php							
								echo "</div>";
								echo "<div>";
								echo "<input type='radio' name='menuUrl' value='1' onclick='disableIn()'><span> 外链（请以http://或https://开头） </span>";
								echo "<input type='text' class='form-control' id='menuoUrl' name='menuoUrl' disabled='disabled' value=''/></td></tr>";
								echo "<tr><td>";
						?>		
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='4' onclick='disable()'>";
								echo "<span>"; 
								echo "会员注册";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memregUrl' id='memregUrl' value='".get_bloginfo('template_directory')."/wesite/common/vip_register.php' />";
								echo "</div>";
						?>						
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='2' checked='checked' onclick='disable()'>";
								echo "<span>"; 
								echo "会员中心";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memUrl' id='memUrl' value='{$slideurllink}' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='3' onclick='disable()'>";
								echo "<span>"; 
								echo "微学校";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memwsUrl' id='memwsUrl' value='".home_url()."/mobile.php?module=weSchool&do=index' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
							
						<?php
																		
								echo "</td></tr>";
								}else if($memreg){
								echo "<br/><input type='radio' name='menuUrl' value='0' onclick='disableOut()'> 内链";
					?>
								<input type="button" class="btn btn-xs btn-primary" onClick="javascript:window.open('../common/post_insert_dialog.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&refreshOpener=<?php echo 'v2yes'?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"
								id="menu" value="创建新页面" disabled="disabled" style="width:120px;margin: 5px 0 7px 20px;"/>
								<input type="button" class="btn btn-xs btn-default" onClick="javascript:window.open('../common/page_list.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $slideurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" 
								id="meu" name="meu" value="文章管理" style="display: none" disabled="disabled"/>
								<input id="Wmenuurl" class="form-control" type="text" name='menuiUrl' value='' style="margin-top:5px" readonly="readonly"/>
					<?php							
								echo "</div>";
								echo "<div>";
								echo "<input type='radio' name='menuUrl' value='1' onclick='disableIn()'><span> 外链（请以http://或https://开头） </span>";
								echo "<input type='text' class='form-control' id='menuoUrl' name='menuoUrl' disabled='disabled' value=''/></td></tr>";
								echo "<tr><td>";
						?>		
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='4' checked='checked' onclick='disable()'>";
								echo "<span>"; 
								echo "会员注册";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memregUrl' id='memregUrl' value='{$slideurllink}' />";
								echo "</div>";
						?>						
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='2'  onclick='disable()'>";
								echo "<span>"; 
								echo "会员中心";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memUrl' id='memUrl' value='".get_bloginfo('template_directory')."/wesite/common/vip_detail.php' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='3' onclick='disable()'>";
								echo "<span>"; 
								echo "微学校";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memwsUrl' id='memwsUrl' value='".home_url()."/mobile.php?module=weSchool&do=index' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
							
						<?php
																		
								echo "</td></tr>";
								}else if($schoollink){  
								echo "<br/><input type='radio' name='menuUrl' value='0' onclick='disableOut()'> 内链";
					?>
								<input type="button" class="btn btn-xs btn-primary" onClick="javascript:window.open('../common/post_insert_dialog.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&refreshOpener=<?php echo 'v2yes'?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"
								id="menu" value="创建新页面" disabled="disabled" style="width:120px;margin: 5px 0 7px 20px;"/>
								<input type="button" class="btn btn-xs btn-default" onClick="javascript:window.open('../common/page_list.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $slideurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" 
								id="meu" name="meu" value="文章管理" style="display: none" disabled="disabled"/>
								<input id="Wmenuurl" class="form-control" type="text" name='menuiUrl' value='' style="margin-top:5px" readonly="readonly"/>
					<?php							
								echo "</div>";
								echo "<div>";
								echo "<input type='radio' name='menuUrl' value='1' onclick='disableIn()'><span> 外链（请以http://或https://开头） </span>";
								echo "<input type='text' class='form-control' id='menuoUrl' name='menuoUrl' disabled='disabled' value=''/></td></tr>";
								echo "<tr><td>";
						?>		
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='4' onclick='disable()'>";
								echo "<span>"; 
								echo "会员注册";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memregUrl' id='memregUrl' value='".get_bloginfo('template_directory')."/wesite/common/vip_register.php' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='2' onclick='disable()'>";
								echo "<span>"; 
								echo "会员中心";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memUrl' id='memUrl' value='".get_bloginfo('template_directory')."/wesite/common/vip_detail.php' />";
								echo "</div>";
						?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
								<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
						<?php
								echo "<input type='radio' name='menuUrl' value='3' checked='checked' onclick='disable()'>";
								echo "<span>"; 
								echo "微学校";
								echo "</span>";
								echo "<input style='visibility:hidden' type='text' class='form-control' name='memwsUrl' id='memwsUrl' value='{$slideurllink}' />";
								echo "</div>";
							?>
								<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
									
						<?php		
								echo "</td></tr>";
								}
								else{
								//外链有效
								echo "<br/><input type='radio' name='menuUrl' value='0' onclick='disableOut()'> 内链";
				?>
							<input type="button" class="btn btn-xs btn-primary" onClick="javascript:window.open('../common/post_insert_dialog.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&refreshOpener=<?php echo 'v2yes'?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"
								id="menu" value="创建新页面" disabled="disabled" style="width:120px;margin: 5px 0 7px 20px;"/>
							<input type="button" class="btn btn-xs btn-default" onClick="javascript:window.open('../common/page_list.php?beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $slideurllink ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')" 
								id="meu" name="meu" value="文章管理" style="display: none" disabled="disabled"/>
							<input id="Wmenuurl" class="form-control" type="text" name='menuiUrl' value='' style="margin-top:5px" readonly="readonly"/>
						<?php							
							echo "</div>";
							echo "<div>";
							echo "<input type='radio' name='menuUrl' value='1' checked='checked' onclick='disableIn()'><span> 外链（请以http://或https://开头） </span>";
							echo "<input type='text' class='form-control' id='menuoUrl' name='menuoUrl' value='{$slideurllink}'/></td></tr>";
							echo "<tr><td>";
							?>
							<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
							<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
							<input type='radio' name='menuUrl' value='4' onclick='disable()'>
							<span>会员注册</span>
							<input style="visibility:hidden" type="text" class="form-control" name="memregUrl" id="memregUrl" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_register.php" disabled="disabled"/>
							</div>
							<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
							<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
							<input type='radio' name='menuUrl' value='2' onclick='disable()'>
							<span>会员中心</span>
							<input style="visibility:hidden" type="text" class="form-control" name="memUrl" id="memUrl" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_detail.php" disabled="disabled"/>
							</div>
							
							<!--<div style="margin-bottom:10%; <?php //if( !$wechatschool ) echo " display:none"; ?>">-->
							<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
							<input type='radio' name='menuUrl' value='3' onclick='disable()'>
							<span>微学校</span>
							<input style="visibility:hidden" type="text" class="form-control" name="memwsUrl" id="memwsUrl" value="<?php echo home_url(); ?>/mobile.php?module=weSchool&do=index" disabled="disabled"/>
							</div>
							
							</td></tr>
						<?php
								}
							} ?>
			<?php 
			echo "<input name='slideid' type='hidden' id='menu_id' value='{$slide_info->id}' maxlength='50' />  ";
			echo "<input name='delimgid' type='hidden' id='delimg_id' value='' maxlength='50' />  ";	
		}	
					?>
		<tr><td>		
		<div width="150" align="right">
			<input type="submit" class="btn btn-primary" value="更新" style="width:120px; margin-top:30px;"/>
			<input type="cancel" class="btn btn-default" value="取消" onclick="close2()" style="width:120px; margin-top:30px;"/>			
		</div>	
		</tr></td>
		</table>
	</form>
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
	
	function ediPost(){	
		var url=document.getElementById("Wmenuurl").value;
		window.open('../common/post_insert_dialog.php?refreshOpener=<?php echo 'v2yes'?>&beIframe&artType=<?php echo "page" ?>&siteId=<?php echo $siteId?>&menuiUrl='+url,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	}
	
	//图片预览
	function previewImage(file){  	
		var picsrc = document.getElementById('pic');  	  
		if (file.files && file.files[0]) {//chrome   
			var reader = new FileReader();
			reader.readAsDataURL(file.files[0]);  
			reader.onload = function(ev){
			picsrc.src = ev.target.result;
			$("#pic").show();
			$("#picurl").hide();
			}   
		
		}else{
			//IE下，使用滤镜 出现问题
			picsrc.style.maxwidth="50px";
			picsrc.style.maxheight = "12px";
			picsrc.style.overflow="hidden";
			var picUpload = document.getElementById('file'); 
			picUpload.select();
			var imgSrc = document.selection.createRange().text;  
			picsrc.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
			picsrc.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\""+imgSrc+"\")";
			
		}                    
	}  
	
	function delImage(){	  
		$("#pic").hide();
		$("#picurl").hide();
		document.getElementById("delimg_id").value=-1;
	}
	//更新时，查看内容是否为空
	function checknull(obj, warning){
		if (obj.value == "") {
			alert(warning);
			obj.focus();
			return true;
		}
		return false;
	}

	function validateform()
	{
	  //if (checknull(document.content.name, "请填写幻灯片按钮名称!") == true) {
		//return false;
	  //}
	  return true; 
	}
	
	//disable/enable相应的内链或外链
	function disableOut() {
		document.getElementById("menuoUrl").disabled=true;
		document.getElementById("Wmenuurl").disabled=false;
		document.getElementById("meu").disabled=false;
		document.getElementById("menu").disabled=false;
		document.getElementById("memUrl").disabled=false;
		document.getElementById("memregUrl").disabled=false;
		document.getElementById("memwsUrl").disabled=false;
	}
	function disableIn() {
		document.getElementById("menuoUrl").disabled=false;
		document.getElementById("Wmenuurl").disabled=true;
		document.getElementById("meu").disabled=true;
		document.getElementById("menu").disabled=true;
		document.getElementById("memUrl").disabled=false;
		document.getElementById("memregUrl").disabled=false;
		document.getElementById("memwsUrl").disabled=false;
	}
	function disable() {
		document.getElementById("menuoUrl").disabled=true;
		document.getElementById("Wmenuurl").disabled=true;
		document.getElementById("meu").disabled=true;
		document.getElementById("menu").disabled=true;
		document.getElementById("memUrl").disabled=false;
		document.getElementById("memregUrl").disabled=false;
		document.getElementById("memwsUrl").disabled=false;
	}
	
	// function closeit() {
		// top.resizeTo(300, 200); //控制网页显示的大小		
		// setTimeout("self.close()", 5000); //毫秒
		// opener.location.reload();  //主页面刷新显示
	// }
    
		function close2(){
		window.opener=null;
		setTimeout("self.close()",0);
	}
	window.resizeTo(850,550);
</script>
</html>
