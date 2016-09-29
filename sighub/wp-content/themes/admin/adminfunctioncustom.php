<?php

session_start();

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

$wid = $_GET['wid'];
//$weid = $_SESSION['WEID'];

global $current_user;
$user_id = $current_user->ID;

 //是否点击提交事件	
   if( isset($_POST['templateselect']) ){
	//$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_name = '".$_POST['templateselect']."' WHERE WEID = ".$weid." AND func_name like '%template%';");
	$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_name = '".$_POST['templateselect']."' WHERE type = 'wid' AND value = ".$wid." AND func_name like '%template%';");
	
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
	$selCheck['wechatresearch'] = 0;// research new added
	$selCheck['wechatschool'] = 0;   //wechatschool new added
	
	//$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE WEID = ".$weid." AND func_name NOT like '%template%' AND EXISTS(SELECT * FROM  `wp_wechat_func_info` WHERE  `func_name` =  ".$wpdb->prefix."wechat_initfunc_info.func_name AND  `status` =0 )");
	$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'wid' AND value = ".$wid." AND func_name NOT like '%template%' AND EXISTS(SELECT * FROM  `wp_wechat_func_info` WHERE  `func_name` =  ".$wpdb->prefix."wechat_initfunc_info.func_name AND  `status` =0 )");
	
	foreach($result as $initfunc){
		$selCheck[$initfunc->func_name] = $initfunc->func_flag;
	}
	if(isset($_POST['selCheck'])){
		foreach($_POST['selCheck'] as $check)
			$selCheck[$check] = 1;
			}
	foreach($selCheck as $func_name => $func_flag){
		
		//$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_flag = '$func_flag' WHERE type = 'wid' AND value = ".$wid." AND func_name='$func_name';");
		
		$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, $func_name, $func_flag));
	
	}
	
	//页面跳转至公用公众号管理页面
	//header("Location: ".home_url()."?admin&page=pubwechatmanage");
	?>
	
	<script>
	   location.href = "<?php echo home_url();?>?admin&page=pubwechatmanage";
	</script>
	
	<?php
}



get_header(); 


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
$selCheck['wechatresearch'] = 0;// research new added
$selCheck['wechatschool'] = 0;   //wechatschool new added

$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'wid' AND value =".$wid." AND func_name NOT like '%template%' ");
foreach($result as $initfunc){
	$selCheck[$initfunc->func_name] = $initfunc->func_flag;
}
//$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE WEID =".$weid." AND func_name like '%template%' limit 1" );
$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'wid' AND value =".$wid." AND func_name like '%template%' limit 1" );
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
$funcDisplay['wechatschool'] = 0;   //wechatschool new added

$result = $wpdb->get_results("SELECT `func_name`,`status` FROM `wp_wechat_func_info`;");
foreach($result as $func){
	$funcDisplay[$func->func_name] = $func->status;
}

//$usertype == "pri_sub" "个人订阅号去掉人工回复，菜单管理，微用户管理"
//$widusechat = $wpdb->get_var( "SELECT wid from ".$wpdb->prefix."wechat_usechat WHERE WEID =".$weid);
//$usertype = $wpdb->get_var( "SELECT wechat_type from ".$wpdb->prefix."wechats WHERE wid =".$widusechat);
$usertype = $wpdb->get_var( "SELECT wechat_type from ".$wpdb->prefix."wechats WHERE wid =".$wid);
?>
<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/init.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>初始化</title>
		<script>
		function ObtainWeChatData()
		{
		   //alert("success!!!");
		   document.getElementById('alertopen').style.display = "block";
		}
		function check_all(obj,cName)
		{
			var checkboxs = document.getElementsByName(cName);
			for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;}
		}
		</script>
	</head>
	<body>	
		<!--<div id="primary" class="site-content">-->
		<div>
			<div>  
			<!--<div id="content" role="main">-->
				<form action="" method="post" target="_parent">
				<!--<form action="?admin&page=adminapplysuccess" method="post">-->
					<div>
						<div class="main-title">
							<div class="title-1">当前位置：帐户信息初始化 > <font class="fontpurple">功能定制</font>
							</div>
						</div>
						<div class="bgimg"></div>
						<div style="width:88%;">
							<div>
								<table width="400" height="100" border="0" cellpadding="20px" style=" margin-left:150px; margin-top:15px;">
									<tr>
										<td><label for="name">选择模板: </label></td>
										<td>
											<select name="templateselect" class="form-control" size="1" type="text;margin-left:500px;" id="theme_size" value="5" maxlength="20">
												<option value="template_selno" <?php if($template == "template_selno") echo 'selected="selected"'; ?> >未选择</option>
												<option value="template_selbank" <?php if($template == "template_selbank") echo 'selected="selected"'; ?> >银行</option>
												<option value="template_selschool" <?php if($template == "template_selschool") echo 'selected="selected"'; ?> >学校</option>
												<option value="template_selcloth" <?php if($template == "template_selcloth") echo 'selected="selected"'; ?> >服装</option>
												<option value="template_selbuildmaterial" <?php if($template == "template_selbuildmaterial") echo 'selected="selected"'; ?> >建材</option>
												<option value="template_selrepast" <?php if($template == "template_selrepast") echo 'selected="selected"'; ?> >餐饮</option>
											</select>
										</td>						
									</tr>
								</table>
							</div>
							<div class="alert alert-warning" style="margin-left:150px;padding-bottom:60px;">
								<div>
									<h4><label for="name">功能选择: </label></h4>
									<input type="checkbox" name="allChecked" onclick="check_all(this, 'selCheck[]')" value="true" style="margin-right:10px">全选/取消全选</input>
								</div>
								<div class="bgimg_warning"></div>
								<div style="margin-top:2%;<?php if( !$funcDisplay['wechatwebsite']&&!$funcDisplay['wechatvip'] ) echo " display:none"; ?>">
									<div><label for="name">微官网 </label></div>
									<div>
										<div>
											<ul class="applist">
											    <?php if( $funcDisplay['wechatwebsite'] ) {?>
												<li><input type="checkbox" name="selCheck[]" value="wechatwebsite" <?php if( $selCheck['wechatwebsite'] ) echo " checked"; ?> >  微官网</input></li>
												<?php }  ?>
												
												<?php if( $funcDisplay['wechatvip'] ) {?>
												<li><input type="checkbox" name="selCheck[]" value="wechatvip" <?php if( $selCheck['wechatvip'] ) echo " checked"; ?> >  会员管理</input></li>
											    <?php }  ?>
											</ul>
										</div>
									</div>
								</div>
								<!--div style="margin-top:5%; margin-left:6%;"><input type="checkbox" name="selCheck1[]" value="wechatwebsite" style="20px 10px 0px 50px">  微官网</input></div-->
								<div style="margin-top:8%;<?php if( !$funcDisplay['wechatfuncfirstconcern']&&!$funcDisplay['wechatfunckeywordsreply']&&!$funcDisplay['wechatfuncnokeywordsreply']&&!$funcDisplay['wechatfuncmanualreply']&&!$funcDisplay['wechatfuncaccountmanage']&&!$funcDisplay['wechatfuncaccountmanage']&&!$funcDisplay['wechatfuncmaterialmanage']&&!$funcDisplay['wechatfuncmenumanage']&&!$funcDisplay['wechatfuncusermanage']) echo " display:none"; ?>">
									<div><label for="name">微信功能 </label></div>
									<div>
										<div>
											<ul class="applist">
											<?php if( $funcDisplay['wechatfuncfirstconcern'] ) {?>
											<li><input type="checkbox" name="selCheck[]" value="wechatfuncfirstconcern" <?php if( $selCheck['wechatfuncfirstconcern'] ) echo " checked"; ?> >  首次关注</input></li> 
											<?php } ?>
											<?php if( $funcDisplay['wechatfunckeywordsreply'] ) {?>  
											<li><input type="checkbox" name="selCheck[]" value="wechatfunckeywordsreply" <?php if( $selCheck['wechatfunckeywordsreply'] ) echo " checked"; ?> >  关键词回复</input></li>
											<?php }  ?>
											<?php if( $funcDisplay['wechatfuncnokeywordsreply'] ) {?>
											<li><input type="checkbox" name="selCheck[]" value="wechatfuncnokeywordsreply" <?php if( $selCheck['wechatfuncnokeywordsreply'] ) echo " checked"; ?> >  无匹配回复</input></li>
											<?php }  ?>
											<?php if( ($funcDisplay['wechatfuncmanualreply'])&&($usertype != "pri_sub")&&($usertype != "pub_sub") ) {?> 
											<li><input type="checkbox" name="selCheck[]" value="wechatfuncmanualreply" <?php if( $selCheck['wechatfuncmanualreply'] ) echo " checked"; ?> >  人工回复</input></li>
											<?php }  ?>
											<?php if( $funcDisplay['wechatfuncmass'] ) {?>  
											<li><input type="checkbox" name="selCheck[]" value="wechatfuncmass" <?php if( $selCheck['wechatfuncmass'] ) echo " checked"; ?> >  群发消息</input></li>
											<?php }  ?>
											</ul>
										</div>
										<div style="">
											<ul class="applist">
											<?php if( $funcDisplay['wechatfuncaccountmanage'] ) {?>
											<li><input type="checkbox" name="selCheck[]" value="wechatfuncaccountmanage" <?php if( $selCheck['wechatfuncaccountmanage'] ) echo " checked"; ?> >  账户管理</input></li>
											<?php }  ?>
											<?php if( $funcDisplay['wechatfuncmaterialmanage'] ) {?>
											<li><input type="checkbox" name="selCheck[]" value="wechatfuncmaterialmanage" <?php if( $selCheck['wechatfuncmaterialmanage'] ) echo " checked"; ?> >  素材管理</input></li>
											<?php }  ?>
											<?php if( ($funcDisplay['wechatfuncmenumanage'])&&($usertype != "pri_sub")&&($usertype != "pub_sub") ) {?> 
											<li><input type="checkbox" name="selCheck[]" value="wechatfuncmenumanage" <?php if( $selCheck['wechatfuncmenumanage'] ) echo " checked"; ?> >  菜单管理</input></li>
											<?php }  ?>
											<?php if( ($funcDisplay['wechatfuncusermanage'])&&($usertype != "pri_sub")&&($usertype != "pub_sub") ) {?>
											<li style="display:none;"><input type="checkbox" name="selCheck[]" value="wechatfuncusermanage" <?php if( $selCheck['wechatfuncusermanage'] ) echo " checked"; ?> >  微用户管理</input></li>
											<?php }  ?>
											</ul>
										</div>
									</div>
								</div>
								
								<div style="margin-top:12%; margin-bottom:6%; <?php if( !$funcDisplay['wechatactivity_coupon']&&!$funcDisplay['wechatactivity_scratch']&&!$funcDisplay['wechatactivity_fortunewheel']&&!$funcDisplay['wechatactivity_toend']&&!$funcDisplay['wechatactivity_fortunemachine'] ) echo " display:none"; ?>">
									<div><label for="name">微活动 </label></div>
									<div>
										<div>
										<ul class="applist">
											<?php if( $funcDisplay['wechatactivity_coupon'] ) {?>
												<li><input type="checkbox" name="selCheck[]" value="wechatactivity_coupon" <?php if( $selCheck['wechatactivity_coupon'] ) echo " checked"; ?> >  优惠券</input></li>
												<?php }  ?>
											<?php if( $funcDisplay['wechatactivity_scratch'] ) {?>  
												<li><input type="checkbox" name="selCheck[]" value="wechatactivity_scratch" <?php if( $selCheck['wechatactivity_scratch'] ) echo " checked"; ?> >  刮刮卡</input></li>
												<?php }  ?>
											<?php if( $funcDisplay['wechatactivity_fortunewheel'] ) {?>
												<li><input type="checkbox" name="selCheck[]" value="wechatactivity_fortunewheel" <?php if( $selCheck['wechatactivity_fortunewheel'] ) echo " checked"; ?> >  幸运大转盘</input></li>
												<?php }  ?>
											<?php if( $funcDisplay['wechatactivity_toend'] ) {?> 
											<li><input type="checkbox" name="selCheck[]" value="wechatactivity_toend" <?php if( $selCheck['wechatactivity_toend'] ) echo " checked"; ?> >  一站到底</input></li>
											<?php }  ?>
											<?php if( $funcDisplay['wechatactivity_fortunemachine'] ) {?>
											<li><input type="checkbox" name="selCheck[]" value="wechatactivity_fortunemachine" <?php if( $selCheck['wechatactivity_fortunemachine'] ) echo " checked"; ?> >  幸运机</input></li>
											<?php }  ?>
											</ul>
										</div>
									</div>
								</div>
								
								<!--wechatresearch new added by Sara -->
								<div style="  margin-bottom:6%;<?php if( !$funcDisplay['wechatresearch']) echo " display:none"; ?>">
									<div><label for="name">微服务 </label></div>
									<div>
										<div>
										<ul class="applist">
											<?php if( $funcDisplay['wechatresearch'] ) {?>
												<li><input type="checkbox" name="selCheck[]" value="wechatresearch" <?php if( $selCheck['wechatresearch'] ) echo " checked"; ?> >  微预约</input></li>
											<?php }  ?>
										</ul>
										</div>
									</div>
								</div>
								
								<!--wechatschool new added by Sara -->
								<div style="<?php if( !$funcDisplay['wechatschool']) echo " display:none"; ?>">
									<div><label for="name">微行业 </label></div>
									<div>
										<div>
										<ul class="applist">
											<?php if( $funcDisplay['wechatschool'] ) {?>
												<li><input type="checkbox" name="selCheck[]" value="wechatschool" <?php if( $selCheck['wechatschool'] ) echo " checked"; ?> >  微学校</input></li>
											<?php }  ?>
										</ul>	
										</div>
									</div>
								</div>
								
							</div>
								
								
								
							</div>
							<div style="margin-top: 30px;">
								<input type="submit" class="btn btn-primary" value="完成" style="width:120px; margin-left:400px"/>	
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>