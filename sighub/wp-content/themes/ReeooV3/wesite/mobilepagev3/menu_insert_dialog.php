<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
$siteId=$_GET["siteId"];
include '../common/web_constant.php';
//2014-07-10新增修改，添加“会员中心”、“会员注册”、“微学校”选项

?>

<?php
    include '../common/dbaccessor.php';	
	include '../../wechat/common/wechat_dbaccessor.php';
	global $current_user;
	//判断是否是分组管理员中的用户
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$user_id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	//$user_id = (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;

	$GWEID = $_SESSION['GWEID'];
	
	//2014-07-10新增修改
	$wechatactivity_vip=web_admin_function_info_groupnew($GWEID,"wechatvip",$user_id);
	$wechatschool=web_admin_function_info_groupnew($GWEID,"wechatschool",$user_id);

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
	<title>添加新菜单按钮</title>
	<!--判断填写内容是否为空-->
	<script language="javascript">
		function checknull(obj, warning)
		{
		  if (obj.value == "") {
			alert(warning);
			obj.focus();
			return true;
		  }
		return false;
		}

		function validateform()
		{
		  if (checknull(document.content.name, "请填写菜单按钮名称!") == true) {
			return false;
		  }
		  if (checknull(document.content.file, "请上传菜单按钮图片!") == true) {
			return false;
		  }
		  return true; 
		}
	</script>
</head>

<body>
<div class="dlg-panel panel panel-default" style="border-color: #FFF;">
  <div class="panel-heading">
    <h3 class="panel-title dlg-title">添加新菜单</h3>
  </div>
	<form role="form" name ="content" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilepagev3/menu_insert.php?beIframe&siteId=<?php echo $siteId ?>" method="post" enctype="multipart/form-data"> 
		<table width="600" bordercolor="#06c" border="0" align="center" cellpadding="10" cellspacing="0" style="margin-top:25px;">
			<tr>	
				<td>
					<label for="name">菜单名称：</label>
					<input type="text" id="name" class="form-control" name="menu_title"/> 
				</td>
			</tr>
						<tr>	
				<td><label for="pic">上传菜单图片：</label>（建议上传图片大小为100*70）</td>
			</tr>
			<tr>	
				<td>
					<img id="pic" src="#" alt="图片预览" height='90' width='90'/>
					<input type="file" class="form-control" name="file" id="file" onchange="previewImage(this)"/>					 
				</td>
			</tr>
			<br/>
			<tr>	
				<td>
					<div><b>页面模板:</b></div>
					<div>
						<input type="radio" name="menuUrl" value="0"  onclick="disableOut()"><span>添加内链</span>
						<!--<input id="Wmenuurl" type="text" style="visibility:hidden" name='menuiUrl' value=''/>-->
							<input type="button" class="btn btn-xs btn-primary" onclick="javascript:window.open('../common/post_insert_dialog.php?refreshOpener=v2yes&beIframe&artType=<?php echo "post" ?>&siteId=<?php echo $siteId?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"  
								id="menu" value="创建新页面" style="width:120px" disabled="disabled"/>
							<input type="button" class="btn btn-xs btn-default" onclick="javascript:window.open('../common/gallery_list.php?beIframe&artType=<?php echo "post" ?>&siteId=<?php echo $siteId?>&menuiUrl=<?php echo $menu_url_info->meta_value ?>','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')"  
								id="meu" name="meu" value="文章管理" style="display: none" disabled="disabled"/>
							<input id="Wmenuurl" class="form-control" type="text" name='menuiUrl' value='' style="margin-top:5px" disabled="disabled" readonly="readonly"/>
					</div>
					<div>
						<input type="radio" name="menuUrl" value="1" onclick="disableIn()">
						<span> 添加外链（请以http://或https://开头）</span>
						<input type="text" class="form-control" name="menuoUrl" id="menuoUrl" value="" disabled="disabled"/>
					</div>
					<!--2014-07-10新增修改begin-->
					<div>
						<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<input type="radio" name="menuUrl" value="4" onclick="disable()">
						<span> 会员注册</span>
						<input style="visibility:hidden" type="text" class="form-control" name="memregUrl" id="memregUrl" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_register.php" disabled="disabled"/>
						</div>
					</div>
					<div>
						<!--<div style="margin-bottom:10%; <?php //if( !$wechatactivity_vip ) echo " display:none"; ?>">-->
						<div style="margin-bottom:0%; <?php if( !$wechatactivity_vip ) echo " display:none"; ?>">
						<input type="radio" name="menuUrl" value="2" onclick="disable()">
						<span> 会员中心</span>
						<input style="visibility:hidden" type="text" class="form-control" name="memUrl" id="memUrl" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_detail.php" disabled="disabled"/>
						</div>
					</div>
					<div>
						<div style="margin-bottom:0%; <?php if( !$wechatschool ) echo " display:none"; ?>">
						<input type="radio" name="menuUrl" value="3" onclick="disable()">
						<span> 微学校</span>
						<input style="visibility:hidden" type="text" class="form-control" name="memwsUrl" id="memwsUrl" value="<?php echo home_url(); ?>/mobile.php?module=weSchool&do=index" disabled="disabled"/>
						</div>
					</div>
					<!--end-->
				</td>
			</tr>
			<tr>
				<td>
					<div width="50" hight="10"align="right" >
						<input type="submit" class="btn btn-primary" value="添加" style="width:120px"/>	
						<input type="cancel" class="btn btn-default" value="取消" onclick="close2()" style="width:120px"/>
					</div>
				</td>
			</tr>
		</table>
		
	</form>

	<div class="panel-body">
	</div>
</div>

</body>
<script language='javascript'>
	$("#pic").hide();
	function previewImage(file){  
	
		var picsrc = document.getElementById('pic');  
	  
		if (file.files && file.files[0]) {//chrome   
			var reader = new FileReader();
			reader.readAsDataURL(file.files[0]);  
			reader.onload = function(ev){
			picsrc.src = ev.target.result;
			$("#pic").show();
			
			}   
		
		}  else{
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
	function close2(){
		window.opener=null;
		setTimeout("self.close()",0);
	}
	
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
</script>
</html>