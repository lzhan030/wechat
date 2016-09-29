<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
require_once './wp-content/themes/ReeooV3/wechat/common/jostudio.wechatmenu.php';
global $wpdb;


$wid = $_GET['wid'];
$exist=wechat_select_demo_exist($wid);
$M_id=$_GET['mid'];
$M_name=$_GET['mname'];
$demomenu=wechat_select_menu_demo();

//2014-07-13新增修改，注释掉
//下面这段可以删了
//根据userid和wid获取对应的weid,然后再更新,注意admin在usechat这张表中对应的user_id设置为0了
/* $getresult = $wpdb->get_results( "SELECT WEID FROM ".$wpdb->prefix."wechat_usechat  WHERE user_id = 0 and wid =".$wid);
foreach($getresult as $result)
{
	$newweid = $result->WEID;
} */


if( isset($_POST['user_name']) ){

   $user_name = $_POST['user_name'];
   $wechatype = $_POST['wechat_type'];
   $nikename = $_POST['wechat_nikename'];
   $url = $_POST['URL'];
   $token = $_POST['token'];
   
   //2014-06-30 新增修改
   $wechatauth = $_POST['wechat_auth'];
   
   //2014-07-08 新增修改
   $wechatmenuappid = trim($_POST['menuappId']);
   $wechatmenuappsc = trim($_POST['menuappSc']);
   $demomenu_forup = $_POST['demomenu'];
   
   //2014-07-10新增修改
   $getresult = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."wechats WHERE wid = %d",$wid));
   foreach($getresult as $result)
   { 
	    $wechattype = $result->wechat_type;
		$wechatauthold = $result->wechat_auth;
   } 
	
   //$veriinfo = $_POST['admin_vericode'];
   //$flag = $_POST['admin_vericodeopen'];
    //获取url中的hash字符串,这里的更新只是更新对应的hash和token两个字段
   $start = strrpos($url,'=');  //获取hash=后面的字符串
   //$length = strlen($url);
   $hash = substr($url, $start+1);
     
   //$busexit=$_POST['busexit'];
   
   //判断微信昵称不能重复
   $accountcounts=web_admin_pubaccountl_count($wid, $nikename);
   foreach($accountcounts as $accountcount){
		$count=$accountcount->accountCount;
	}
   
    if(empty($nikename)){ $flag1failed = true;?>
		<script>
			alert("微信昵称不能为空");
		</script>
	<?php }
	else if($count == 1){ $flag1failed = true;?>
		<script>
			alert("微信昵称修改重复,请重新输入");
		</script>
	<?php }
	else{
			
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_nikename = '".$nikename."' WHERE wid = ".$wid." ;");
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_auth = ".$wechatauth." WHERE wid = ".$wid." ;");
		
		//2014-07-08新增修改
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET menu_appId = '".$wechatmenuappid."' WHERE wid = ".$wid." ;");
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET menu_appSc = '".$wechatmenuappsc."' WHERE wid = ".$wid." ;");
		include './wp-content/themes/ReeooV3/wechat/common/menu_update_forwechat_forpub.php';
			 
    }
}
//echo $funcid;

$vericodeinfo = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wechat_usechat WHERE wid = %d AND user_id = 0",$wid));
foreach($vericodeinfo as $info)
{
    $vericode = $info->vericode;
	$flgopen = $info->flgopen;
}
$account = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wechats WHERE wid = %d",$wid));
$wechats_info=web_admin_get_wechats_info($wid);


//$usertype == "pri_sub" "个人订阅号去掉人工回复，菜单管理，微用户管理"
//$widusechat = $wpdb->get_var( "SELECT wid from ".$wpdb->prefix."wechat_usechat WHERE WEID =".$newweid);
//$usertype = $wpdb->get_var( "SELECT wechat_type from ".$wpdb->prefix."wechats WHERE wid =".$widusechat);
$usertype = $wpdb->get_var($wpdb->prepare("SELECT wechat_type from ".$wpdb->prefix."wechats WHERE wid =%d",$wid));

//amdin功能选择列表
$selCheck['wechatwebsite'] = 0;
$selCheck['wechatfuncfirstconcern'] = 0;
$selCheck['wechatfunckeywordsreply'] = 0;
$selCheck['wechatfuncmanualreply'] = 0;
$selCheck['wechatfuncaccountmanage'] = 0;
$selCheck['wechatfuncmaterialmanage'] = 0;
$selCheck['wechatfuncmenumanage'] = 0;
$selCheck['wechatfuncusermanage'] = 0;
$selCheck['wechatactivity_coupon'] = 0;
$selCheck['wechatactivity_scratch'] = 0;
$selCheck['wechatactivity_fortunewheel'] = 0;
$selCheck['wechatactivity_toend'] = 0;
$selCheck['wechatactivity_fortunemachine'] = 0;
$selCheck['wechatfuncnokeywordsreply'] = 0;
$selCheck['wechatvip'] = 0;
$selCheck['wechatresearch'] = 0;
$selCheck['wechatschool'] = 0;   //wechatschool new added

//$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE WEID = ".$newweid." AND func_name NOT like '%template%'");
$result = $wpdb->get_results($wpdb->prepare("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'wid' AND value = %d AND func_name NOT like '%template%'",$wid));
if(is_array($result))
{
	foreach($result as $initfunc){
		$selCheck[$initfunc->func_name] = $initfunc->func_flag;
	}
}
//$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE WEID = ".$newweid." AND func_name like '%template%' limit 1" );
$template = $wpdb->get_var($wpdb->prepare("SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'wid' AND value = %d AND func_name like '%template%' limit 1" ,$wid));

if($template === FALSE)
	$template = 'template_selno';

$funcDisplay['wechatwebsite'] = 1;
$funcDisplay['wechatfuncfirstconcern'] = 1;
$funcDisplay['wechatfunckeywordsreply'] = 1;
$funcDisplay['wechatfuncmanualreply'] = 1;
$funcDisplay['wechatfuncaccountmanage'] = 1;
$funcDisplay['wechatfuncmaterialmanage'] = 1;
$funcDisplay['wechatfuncmenumanage'] = 1;
$funcDisplay['wechatfuncusermanage'] = 1;
$funcDisplay['wechatactivity_coupon'] = 1;
$funcDisplay['wechatactivity_scratch'] = 1;
$funcDisplay['wechatactivity_fortunewheel'] = 1;
$funcDisplay['wechatactivity_toend'] = 1;
$funcDisplay['wechatactivity_fortunemachine'] = 1;
$funcDisplay['wechatfuncnokeywordsreply'] = 1;
$funcDisplay['wechatvip'] = 1;
$funcDisplay['wechatresearch'] = 1;
$funcDisplay['wechatschool'] = 1;   //wechatschool new added
$result = $wpdb->get_results("SELECT `func_name`,`status` FROM `wp_wechat_func_info`;");
foreach($result as $func){
	$funcDisplay[$func->func_name] = $func->status;
}	
//$demomenu=wechat_select_menu_demo();	

?>
<script>
    //如果menuappid和menuappsc没有输入对应的值，则提示
	function checknikename(wechattype,wechatauth)
	{
	    if((wechattype == 'pub_sub' && wechatauth =='1') || wechattype == 'pub_svc')
		{
  		    var menuappid = document.getElementById('menuappId').value;
			var menuappsc = document.getElementById('menuappSc').value;
			if(!((menuappid!='')&&(menuappsc!='')))
			{
				alert("您没有输入微信菜单appid和微信菜单appsc，将没有自定义菜单这个功能");
			}
		}	
		return true;
		
	}
    function check_all(obj,cName)
	{
		var checkboxs = document.getElementsByName(cName);
		for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;}
	}
</script>

<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
<script>
	$(function(){ 
	
	//2014-07-10新增修改,对于未认证的个人订阅号以及公共订阅号点击已认证，出现menuappid和menuappsc
		//2014-07-11新增修改，注释 
		/*  $(".rzradio").click(function(){   
		  
			if($(this).attr("checked")){              
				val = $(this).attr("value");			
			}
		
			if(val == 1)
			{
			    //document.getElementById('table3').style.display="block";
			    //$("#table3").css("display","block"); 
			    //将获取的指定的某一个wechat框中的menuid和menusc显示出来
			    //alert($("."+seltable));
			    //$("."+seltable).css("display","block"); 
			    $("#table3").show();
			}
			else
			{
				$("#table3").css("display","none");
									
			}
		}); */
				
	
	});
</script>

<div>
	
	<!--<form id="pubchatedit" action="" method="post">-->
	<div class="main-title">
		<div class="title-1">当前位置：公用公众号管理 > <font class="fontpurple">公用公众号信息编辑 </font>
		</div>
	</div>
	<div class="bgimg"></div>
	<?php
		if( isset($_POST['user_name'])&&(!$flag1failed)){
		?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 80px;font-size: 18px;margin-left: 360px;">	提交成功!<br>
		</p>
	<?php
		} ?>
	<?php		
		foreach ($account as $accountdata) {
		    //显示的url链接
			$url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoinPublic.php?hash='.$accountdata->hash;
			$url=preg_replace('|^https://|', 'http://', $url);
	?>
	<form id="pubchatedit" action="" method="post" onsubmit="return checknikename('<?php echo $accountdata -> wechat_type;?>','<?php echo $accountdata -> wechat_auth;?>');">
	<input type="hidden" id="wechattype" value="<?php echo $accountdata -> wechat_type;?>">
	<input type="hidden" id="wechatauth" value="<?php echo $accountdata -> wechat_auth;?>">
	<table width="600" height="100" border="0" cellpadding="20px" style="margin-left: 150px; margin-top:30px;" id="table2">
		<tbody>
			<tr>
				<!--<td height="50px;"><label for="user_name">用户名: </label></td>-->
				<td height="50px;" width="210">用户名: </td>
				<td><input type="text" value="admin" class="form-control" id="user_name" name="user_name" readonly="true"></td>
				<td></td>
			</tr>
			<tr>
				<!--<td height="50px;"><label for="wechat_type">微信号类别: </label></td>-->
				<td height="50px;">微信号类别: </td>
				<td>
				    <select name="wechat_type" class="form-control" size="1" type="text;margin-left:500px;" id="wechat_type" value="5" maxlength="20" readonly="true">
						<option value="pub_sub" <?php if(($accountdata->wechat_type == "pub_sub") && ($accountdata ->wechat_auth == 0)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信未认证订阅号</option>
						<option value="pub_sub" <?php if(($accountdata->wechat_type == "pub_sub") && ($accountdata ->wechat_auth == 1)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信认证订阅号</option>
						<option value="pub_svc" <?php if(($accountdata->wechat_type == "pub_svc") && ($accountdata ->wechat_auth == 0)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信未认证服务号</option>
						<option value="pub_svc" <?php if(($accountdata->wechat_type == "pub_svc") && ($accountdata ->wechat_auth == 1)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信认证服务号</option>
					</select>
				</td>
				
			</tr>
			
			<!--20140630newaddedbegin-->
			<!--20140711del-->
		    <!--<tr>
				<td height="50px;">认证情况: </td>
				<td width="">
					<input type="radio" class="rzradio" id="authnokfw" name="wechat_auth" value="0" <?php //if($accountdata ->wechat_auth == 0){ echo 'checked="checked"';} ?> style=""/><span>未认证</span>
					<input type="radio" class="rzradio" id="authokfw" name="wechat_auth" value="1" <?php //if($accountdata ->wechat_auth == 1){ echo 'checked="checked"';} ?> style="margin-left:45px;"/><span>已认证</span>
				</td>								
			</tr>-->
			<!--20140630newaddedend-->
		</table>
		
		<?php if($accountdata->wechat_type == "pub_sub" ){?>
		<!--<table width="600" height="100" border="0" cellpadding="20px" style="margin-left: 150px; margin-top:0px; display:none;" id="table3">		
		<tr>
			<!--<td><label for="URL">URL: </label></td>
			<td width="210">微信菜单AppId: </td>
			<td><input type="text" value="" class="form-control" id="menuappId1" name="menuappId1"></td>
		</tr>
		
		<tr>
			<td width="210">微信菜单AppSecret: </td>
			<td><input type="text" value="" class="form-control" id="menuappSc1" name="menuappSc1"></td>
		</tr>-->
		<?php }?>	
			
		</table>
		
        <table width="600" height="130" border="0" cellpadding="20px" style="margin-left: 150px; margin-top:0px;" id="table2">		
			
			<tr>
				<!--<td height="50px;"><label for="wechat_nikename">微信昵称: </label></td>-->
				<td height="50px;" width="210">微信昵称: </td>
				<td><input type="text" value="<?php echo $accountdata->wechat_nikename; ?>" class="form-control" id="wechat_nikename" name="wechat_nikename">
				</td>
			</tr>
			<tr>
				<!--<td height="50px;"><label for="URL">URL: </label></td>-->
				<td height="50px;" width="210">URL: </td>
				<td><input type="text" value="<?php echo $url; ?>" class="form-control" id="URL" name="URL" readonly="true">
				</td>
			</tr>
			<tr>
				<!--<td height="50px;"><label for="token">token: </label></td>-->
				<td height="50px;" width="210">token: </td>
				<td><input type="text" value="<?php echo $accountdata->token; ?>" class="form-control" id="token" name="token" readonly="true"> </td>
			</tr>
			
			<?php if(($accountdata -> wechat_type == "pub_sub" && $accountdata -> wechat_auth == 1)||($accountdata -> wechat_type == "pub_svc")){?>
			<tr>
				<!--<td><label for="URL">URL: </label></td>-->
				<td height="50px;" width="210">微信菜单AppId: </td>
				<td><input type="text" value="<?php echo $accountdata -> menu_appId; ?>" class="form-control" id="menuappId" name="menuappId"></td>
			</tr>
			
			<tr>
				<!--<td><label for="Token">Token: </label></td>-->
				<td height="50px;" width="210">微信菜单AppSecret: </td>
				<td><input type="text" value="<?php echo $accountdata -> menu_appSc; ?>" class="form-control" id="menuappSc" name="menuappSc"></td>
			</tr>
			<?php }?>
			
			<?php   if($M_id != null) { ?>
			<tr>
				<!--<td><label for="name">使用的菜单模板: </label></td>-->
				<td>使用的菜单模板: </td>
				<td height="50px;"  >
					<select id="seltemId" class="form-control" name="demomenu"  onchange="selTem(this.options[this.selectedIndex].value)" readonly="true">
					<?php foreach($demomenu as $demo){ 
							if ($M_name==$demo->M_name){
								echo "<option value='$demo->M_id ' selected='selected'> $demo->M_name </option>";
							}else{
								echo "<option value='$demo->M_id ' disabled> $demo->M_name </option>";
							}
						 } ?>
				   </select>
				</td>
			</tr>
			<?php }?>
			
			<!--<tr>
				<td height="50px;"><label for="user_vericode">验证码: </label></td>
				<td><input type="text" value="<?php //echo $vericode; ?>" class="form-control" id="admin_vericode" name="admin_vericode"></td>
			</tr>
			
			<tr>
				<td height="50px;"><label for="vericodeopen">是否将验证码显示在用户列表中: </label></td>
				<td><input type="radio" name="admin_vericodeopen" value="1"  <?php //if($flgopen == 1) echo 'checked="checked"';?>>是 <input type="radio" name="admin_vericodeopen" value="0"  <?php //if($flgopen == 0) echo 'checked="checked"';?> style="margin-left:25px;">否 </td>
			</tr>-->
			
			
		
		    <!--<tr>
				<!--<td height="50px;"><label for="name">选择模板: </label></td>
				<td height="50px;">选择模板: </td>
				<td>
					<select name="templateselect" class="form-control" size="1" type="text;margin-left:500px;" id="theme_size" value="5" maxlength="20">
					    <option value="template_selno" <?php //if($template == "template_selno") echo 'selected="selected"'; ?>>未选择</option>
						<option value="template_selbank" <?php //if($template == "template_selbank") echo 'selected="selected"'; ?>>银行</option>
						<option value="template_selschool" <?php //if($template == "template_selschool") echo 'selected="selected"'; ?>>学校</option>
						<option value="template_selcloth" <?php //if($template == "template_selcloth") echo 'selected="selected"'; ?>>服装</option>
						<option value="template_selbuildmaterial" <?php //if($template == "template_selbuildmaterial") echo 'selected="selected"'; ?>>建材</option>
						<option value="template_selrepast" <?php //if($template == "template_selrepast") echo 'selected="selected"'; ?>>餐饮</option>
					</select>
				</td>						
			</tr>-->
		
		
		</tbody>
	</table>
	<?php } ?>
	
	<!--<div class="alert alert-warning" style="margin-left:140px;padding-bottom:60px;width:615px;margin-top:10px;">
	<div>
		<label for="name" style="margin-left:0px; margin-top:1%;">功能选择: </label>
		<input type="checkbox" name="allChecked" onclick="check_all(this, 'selCheck[]')" value="true" style="margin-right:10px">全选/取消全选</input>
	</div>
	<div class="bgimg_warning"></div>
	<div style="margin-top: 1%; margin-left: 20px; <?php if( !$funcDisplay['wechatwebsite'] ) echo " display:none"; ?>">
		<ul class="applist">
		<li><input type="checkbox" name="selCheck[]" value="wechatwebsite"
			style="" <?php if( $selCheck['wechatwebsite'] ) echo " checked"; ?>> 微官网</input>
			</li>
		</ul>
	</div>
	<div style="margin-top: 8%; <?php if( !$funcDisplay['wechatfuncfirstconcern']&&!$funcDisplay['wechatfunckeywordsreply']&&!$funcDisplay['wechatfuncnokeywordsreply']&&!$funcDisplay['wechatfuncmanualreply']&&!$funcDisplay['wechatfuncaccountmanage']&&!$funcDisplay['wechatfuncaccountmanage']&&!$funcDisplay['wechatfuncmaterialmanage']&&!$funcDisplay['wechatfuncmenumanage']&&!$funcDisplay['wechatfuncusermanage']) echo " display:none"; ?>">
		<div style="margin-left: 20px;">
			<label for="name">微信功能 </label>
		</div>
		<div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatfuncfirstconcern'] ) {?>
				<li><input type="checkbox" name="selCheck[]" value="wechatfuncfirstconcern" style="" <?php if( $selCheck['wechatfuncfirstconcern'] ) echo " checked"; ?>> 首次关注 </input></li>
				<?php } ?>
				
				<?php if( $funcDisplay['wechatfunckeywordsreply'] ) {?>  
				<li><input type="checkbox" name="selCheck[]" value="wechatfunckeywordsreply" style="margin-left: 5%;" <?php if( $selCheck['wechatfunckeywordsreply'] ) echo " checked"; ?>> 关键词回复</input></li>
				<?php } ?>
				
				<?php if( $funcDisplay['wechatfuncnokeywordsreply'] ) {?>
				<li><input type="checkbox" name="selCheck[]" value="wechatfuncnokeywordsreply" style="margin-left: 5%;" <?php if( $selCheck['wechatfuncnokeywordsreply'] ) echo " checked"; ?>> 无匹配回复</input></li>
				<?php } ?>
				
				<?php if( ($funcDisplay['wechatfuncmanualreply'])&&($usertype != "pri_sub")&&($usertype != "pub_sub") ) {?> 
				<li><input type="checkbox" name="selCheck[]" value="wechatfuncmanualreply"style="margin-left: 5%;" <?php if( $selCheck['wechatfuncmanualreply'] ) echo " checked"; ?>> 人工回复 </input></li>
				<?php }  ?>
			</ul>	
			</div>
			<div style="margin-top: 1%; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatfuncaccountmanage'] ) {?>
			    <li><input type="checkbox" name="selCheck[]" value="wechatfuncaccountmanage"  <?php if( $selCheck['wechatfuncaccountmanage'] ) echo " checked"; ?>> 账户管理</input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatfuncmaterialmanage'] ) {?>
				<li><input type="checkbox" name="selCheck[]" value="wechatfuncmaterialmanage" style="margin-left: 5%;" <?php if( $selCheck['wechatfuncmaterialmanage'] ) echo " checked"; ?>> 素材管理 </input></li>
				<?php }  ?>
				
				<?php if( ($funcDisplay['wechatfuncmenumanage'])&&($usertype != "pri_sub")&&($usertype != "pub_sub") ) {?> 
				<li><input type="checkbox" name="selCheck[]" value="wechatfuncmenumanage" style="margin-left: 5%;" <?php if( $selCheck['wechatfuncmenumanage'] ) echo " checked"; ?>> 菜单管理 </input></li>
				<?php }  ?>
				
				<?php if( ($usertype != "pri_sub")&&($usertype != "pub_sub") ) {?> 
				<!--<input type="checkbox" name="selCheck[]" value="wechatfuncusermanage" style="margin-left: 5%;" <?php //if( $selCheck['wechatfuncusermanage'] ) echo " checked"; ?>> 微用户管理</input>-->
				<?php }  ?>
			<!--</ul>
			</div>
		</div>
	</div>
	<div style="margin-top: 15%;">
		<div style="margin-left: 20px; <?php if( !$funcDisplay['wechatactivity_coupon']&&!$funcDisplay['wechatactivity_scratch']&&!$funcDisplay['wechatactivity_fortunewheel']&&!$funcDisplay['wechatactivity_toend']&&!$funcDisplay['wechatactivity_fortunemachine'] ) echo " display:none"; ?>">
			<label for="name">微活动 </label>
		</div>
		<div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatactivity_coupon'] ) {?>
				<li><input type="checkbox" name="selCheck[]" value="wechatactivity_coupon" style="" <?php if( $selCheck['wechatactivity_coupon'] ) echo " checked"; ?>> 优惠券 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatactivity_scratch'] ) {?> 
				<li><input type="checkbox" name="selCheck[]" value="wechatactivity_scratch" style="margin-left: 5%;" <?php if( $selCheck['wechatactivity_scratch'] ) echo " checked"; ?>> 刮刮卡 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatactivity_fortunewheel'] ) {?>
				<li><input type="checkbox" name="selCheck[]" value="wechatactivity_fortunewheel" style="margin-left: 3%;" <?php if( $selCheck['wechatactivity_fortunewheel'] ) echo " checked"; ?>> 幸运大转盘</input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatactivity_toend'] ) {?> 
                <li><input type="checkbox" name="selCheck[]" value="wechatactivity_toend" style="margin-left: 3%;" <?php if( $selCheck['wechatactivity_toend'] ) echo " checked"; ?>> 一站到底 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatactivity_fortunemachine'] ) {?>
				<li><input type="checkbox" name="selCheck[]" value="wechatactivity_fortunemachine" style="margin-left: 3%;" <?php if( $selCheck['wechatactivity_fortunemachine'] ) echo " checked"; ?>>幸运机</input></li>
				<?php }  ?>
			</ul>
			</div>
		</div>
	</div>
	<div style="margin-top: 15%;  <?php if( !$funcDisplay['wechatvip'] && !$funcDisplay['wechatresearch']  ) echo " display:none"; ?>">
		<div style="margin-left: 20px;">
			<label for="name">微服务 </label>
		</div>
		<div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatvip'] ) {?>
				<li><input type="checkbox" name="selCheck[]" value="wechatvip" style="" <?php if( $selCheck['wechatvip'] ) echo " checked"; ?>> 会员管理 </input></li>
				<?php }  ?>
				
				 <?php if( $funcDisplay['wechatresearch'] ) {?>
				<li><input type="checkbox" name="selCheck[]" value="wechatresearch" style="" <?php if( $selCheck['wechatresearch'] ) echo " checked"; ?>> 微预约 </input></li>
				<?php }  ?>
			</ul>	
			</div>
		</div>
	</div>-->
	
	<!--wechatschool new added by Sara -->
	<!--<div style="margin-top: 8%;  <?php if( !$funcDisplay['wechatschool'] ) echo " display:none"; ?>">
		<div style="margin-left: 20px;">
			<label for="name">微行业 </label>
		</div>
		<div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatschool'] ) {?>
				<li><input type="checkbox" name="selCheck[]" value="wechatschool" style="" <?php if( $selCheck['wechatschool'] ) echo " checked"; ?>> 微学校 </input></li>
				<?php }  ?>
			</ul>	
			</div>
		</div>
	</div>
	
	</div>-->
	
	<div style="margin-bottom:50px; margin-top:3%; margin-left:360px;">
	    <input type="submit" class="btn btn-primary" value="更新" id="sub3" style="width:70px">
	    <input type="button" onclick="location.href='?admin&page=pubwechatmanage'" class="btn btn-primary" value="取消" id="sub3" style="width:70px; margin-left:20px;">
		<!--<input type="button" onclick="location.href='?admin&page=pubwechatmanage'" class="btn btn-primary" value="返回" id="sub3" style="width:70px; margin-left:50px;">-->
	</div>
	</form>
</div>
<?php //} ?>