<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<?php
	global  $current_user, $wpdb;
	//拿到window.open里传递过来的值	
	$menid=$_GET["menuId"];	
	$siteId=$_GET["siteId"];	
	//当前用户有可能是分组管理员下的
    $getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$current_user->ID);
    if(!empty($getgroupuserids)){
        foreach($getgroupuserids as $getgroupinfo)
        {
            $usergroupid = $getgroupinfo -> group_id;
            $usergroupflag = $getgroupinfo -> flag;
        }
    }else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
        $usergroupid = 0;
        $usergroupflag = 0;
    }
    $currentuser = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($usergroupid !=0 && $usergroupflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
    
	//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;

	/* //获取特定的menu
	$menu=web_admin_get_menu($menid); 	
	//获取menu的url
	$menu_url=web_admin_get_menu_url($menid);
	//获取menu的img
	$menu_img=web_admin_get_menu_img($menid); */
	
	/* $wids = 0;
	$getwids=getswid($_SESSION['WEID'],$currentuser);
	foreach($getwids as $getwid){
		$wids = $getwid->wid;
	}
	$wechatactivity_vip=web_admin_function_info($_SESSION['WEID'],"wechatvip",$wids,$currentuser);
	$wechatschool=web_admin_function_info($_SESSION['WEID'],"wechatschool",$wids,$currentuser);  *///判断是否选择微学校服务
	
	
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
	<title>更新菜单按钮内容</title>
</head>
<body>

<div class="dlg-panel panel panel-default" style="border-color: #FFF;">
	<div class="panel-heading">
		<h3 class="panel-title dlg-title">更新菜单按钮内容</h3>
	</div>
	<div class="panel-body">
		<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('updatepic',array('sid' => $sid));?>" method="post" enctype="multipart/form-data">
			<table width="600" bordercolor=#06c border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px;">
			<?php //foreach($menu as $menu_info){
				//foreach($menu_url as $menu_url_info){
					//foreach($menu_img as $menu_img_info){
						//echo "<tr><td><label for='name'>菜单名称：</label>";
						//echo "<input type='text' class='form-control' id='name' name='menu_title' value='{$menu_info->post_title}' /></td></tr>";
						echo "<tr><td><label for='pic'>上传菜单图片：</label>（建议上传图片大小为100*70）</td></tr>";
						/* $img=web_admin_get_post($menu_img_info->meta_value);						
						foreach($img as $imgurl){
							$url=$imgurl->guid;
						}  */
						if($url==""){
							echo "<tr><td><img id='pic' src='' height='90' width='90' style='display:none;'/>";
						}else{
							echo "<tr><td><img id='pic' src='{$weschoolurl}' height='90' width='90'/>";
							//echo "<tr><td><a id='picurl' href='#' onclick='delImage()'>删除图片</a>";
						}						
						echo "<input type='file' class='form-control' name='file' id='file' onchange='previewImage(this)' style='margin-bottom:30px;'/></td>";
					/* }
				}
			}		 */			
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
	  //if (checknull(document.content.name, "请填写菜单按钮名称!") == true) {
		//return false;
	  //}
	  return true; 
	}
	
	//disable/enable相应的内链或外链
/* 	function disableOut() {
		document.getElementById("menuoUrl").disabled=true;
		document.getElementById("Wmenuurl").disabled=false;
		document.getElementById("meu").disabled=false;
		document.getElementById("menu").disabled=false;
		document.getElementById("memUrl").disabled=false;
		document.getElementById("memwsUrl").disabled=false;
	}
	function disableIn() {
		document.getElementById("menuoUrl").disabled=false;
		document.getElementById("Wmenuurl").disabled=true;
		document.getElementById("meu").disabled=true;
		document.getElementById("menu").disabled=true;
		document.getElementById("memUrl").disabled=false;
		document.getElementById("memwsUrl").disabled=false;
	}
	function disable() {
		document.getElementById("menuoUrl").disabled=true;
		document.getElementById("Wmenuurl").disabled=true;
		document.getElementById("meu").disabled=true;
		document.getElementById("menu").disabled=true;
		document.getElementById("memUrl").disabled=false;
		document.getElementById("memwsUrl").disabled=false;
	} */

		function close2(){
		window.opener=null;
		setTimeout("self.close()",0);
	}
	window.resizeTo(850,550);
</script>
</html>
