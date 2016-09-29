<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

require_once ('../wesite/common/dbaccessor.php');

get_header(); 

$wid = intval($_GET['wid']);
$weid = $_SESSION['WEID'];

global $current_user;
//判断是否是分组管理员中的用户
$groupadminflag = web_admin_issuperadmin($current_user->ID);
$user_id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
//$user_id =  (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;

//2014-06-24新增
$gweid = $_SESSION['GWEID'];
$getwids = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."wechat_usechat WHERE WEID = ".$weid."  AND user_id = ".$user_id);
foreach($getwids as $getwid)
{
	$wids = $getwid->wid;
}


//$usertype == "pri_sub" "个人订阅号去掉人工回复，菜单管理，微用户管理"
/* $widusechat = $wpdb->get_var( "SELECT wid from ".$wpdb->prefix."wechat_usechat WHERE WEID =".$weid);
$usertype = $wpdb->get_var( "SELECT wechat_type from ".$wpdb->prefix."wechats WHERE wid =".$widusechat); */

//2014-07-01新增修改
$widusechat = $wpdb->get_var( "SELECT wid from ".$wpdb->prefix."wechat_usechat WHERE user_id = ".$user_id." AND GWEID = ".$gweid." AND WEID =".$weid);
$usertype = $wpdb->get_var( "SELECT wechat_type from ".$wpdb->prefix."wechats WHERE wid =".$widusechat);
//2014-07-07已认证的订阅号也有菜单管理这个功能
$wechatauths = $wpdb->get_results( "SELECT wechat_auth from ".$wpdb->prefix."wechats WHERE wid =".$widusechat);
foreach($wechatauths as $getauth)
{
    $wechatauth = $getauth -> wechat_auth;
}

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
$selCheck['wechatresearch'] = 0; //research new added
$selCheck['wechatschool'] = 0;   //wechatschool new added


/* 下面这块去掉
$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'WEID' AND value =".$weid." AND func_name NOT like '%template%' ");
foreach($result as $initfunc){
	$selCheck[$initfunc->func_name] = $initfunc->func_flag;
} */

//2014-06-24新增判断之前添加的公众号是否选择过某些选项,如果添加过则需要显示出对应选中的状态
//是否选中是通过weid，userid以及wid共同决定的
$j=0;
$result = web_user_display_index_group($gweid, $user_id, $wids);
foreach($result as $initfunc){
	$selCheck[$initfunc->func_name] = $initfunc->status;
	if($initfunc->status == 1)
	   $j++;
}
if($j != 0)
    $flag = true;


if($usertype == 'pri_sub' || $usertype == 'pri_svc')
{
	$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'userid' AND value =".$user_id." AND func_name like '%template%' limit 1" );
}
else
{
    //$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'WEID' AND value =".$weid." AND func_name like '%template%' limit 1" );
	$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'wid' AND value =".$widusechat." AND func_name like '%template%' limit 1" );
}
if($template === FALSE)
	$template = 'template_selno';
	
$funcDisplay['wechatwebsite'] = 0;
$funcDisplay['wechatfuncfirstconcern'] = 0;
$funcDisplay['wechatfunckeywordsreply'] = 0;
$funcDisplay['wechatfuncmanualreply'] = 0;
$funcDisplay['wechatfuncaccountmanage'] = 0;
$funcDisplay['wechatfuncmaterialmanage'] = 0;
$funcDisplay['wechatfuncmenumanage'] = 0;
$funcDisplay['wechatfuncusermanage'] = 0;
$funcDisplay['wechatactivity_coupon'] = 0;
$funcDisplay['wechatactivity_scratch'] = 0;
$funcDisplay['wechatactivity_fortunewheel'] = 0;
$funcDisplay['wechatactivity_toend'] = 0;
$funcDisplay['wechatactivity_fortunemachine'] = 0;
$funcDisplay['wechatfuncnokeywordsreply'] = 0;
$funcDisplay['wechatvip'] = 0;
$funcDisplay['wechatresearch'] = 0; //research new added
$funcDisplay['wechatschool'] = 0;   //wechatschool new added

$getwids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." WHERE WEID = ".$weid."  AND GWEID = ".$gweid." AND user_id = ".$user_id);
foreach($getwids as $getwid)
{
	$wids = $getwid->wid;
}
$result=web_user_display_function($user_id, $wids);
foreach($result as $func){
	    $funcDisplay[$func->func_name] = $func->status;
}
//如果是关注的是公用的公众号，还需要再加一层admin当时添加的功能列表限制





?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/init.css">
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
		<div id="primary" class="site-content">
			<div id="content" role="main">  
				<!--<form action="<?php bloginfo('wpurl'); ?>/index.php?wid=<?php echo $wid;?>" method="post" target="_parent">-->
				<form action="<?php echo constant("CONF_THEME_DIR"); ?>/init/apply_success.php?beIframe" method="post">
				<!--<form action="<?php echo constant("CONF_THEME_DIR"); ?>/index.php" method="post" >-->
					<div>
						<div class="main-title">
							<div class="title-1">当前位置：帐户信息初始化 > <font class="fontpurple">功能定制</font>
							</div>
						</div>
						<div class="bgimg"></div>
						<?php if($flag){?>
						<div class='alert alert-warning'>您之前添加公众号选择的功能选项已经处于选中状态，您还可以再添加新的功能<br>
						</div>
						<?php }?>
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
							<div class="alert alert-warning" style="margin-left:170px;padding-bottom:60px;">
								<div>
									<h4><label for="name">功能选择: </label></h4>
									<input type="checkbox" name="allChecked" onclick="check_all(this, 'selCheck[]')" value="true" style="margin-right:10px">全选/取消全选</input>
								</div>
								<div class="bgimg_warning"></div>
								<div style="margin-top:2%;<?php if( !$funcDisplay['wechatwebsite']&&!$funcDisplay['wechatvip']&&!$funcDisplay['wechatresearch'] ) echo " display:none"; ?>">
									<div><label for="name">微官网 </label></div>
									<div>
										<div>
											<ul class="applist">
											    <?php if( $funcDisplay['wechatwebsite'] ) {?>
												<li>
													<input type="checkbox" name="selCheck[]" value="wechatwebsite" <?php if( $selCheck['wechatwebsite'] ) echo " checked"; ?> >  微官网</input>
												</li>
												<?php }  ?>
												<?php if( $funcDisplay['wechatvip'] ) {?>
												<li><input type="checkbox" name="selCheck[]" value="wechatvip" <?php if( $selCheck['wechatvip'] ) echo " checked"; ?> >  会员管理</input></li>
											    <?php }  ?>
											    <?php if( $funcDisplay['wechatresearch'] ) {?>
												<li><input type="checkbox" name="selCheck[]" value="wechatresearch" <?php if( $selCheck['wechatresearch'] ) echo " checked"; ?> >  微预约</input></li>
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
											<?php } ?>
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
											<?php if( ($funcDisplay['wechatfuncmenumanage'])&&(($usertype != "pri_sub") && ($wechatauth == 0))&&($usertype != "pub_sub") ) {?> 
											<li><input type="checkbox" name="selCheck[]" value="wechatfuncmenumanage" <?php if( $selCheck['wechatfuncmenumanage'] ) echo " checked"; ?> >  菜单管理</input></li>
											<?php }  ?>
											<?php if( ($funcDisplay['wechatfuncusermanage'])&&($usertype != "pri_sub")&&($usertype != "pub_sub") ) {?>
											<li style="display:none;"><input type="checkbox" name="selCheck[]" value="wechatfuncusermanage" <?php if( $selCheck['wechatfuncusermanage'] ) echo " checked"; ?> >  微用户管理</input></li>
											<?php }  ?>
										</div>
									</div>
								</div>
								
								<div style="margin-top:12%; margin-bottom:10%; <?php if( !$funcDisplay['wechatactivity_coupon']&&!$funcDisplay['wechatactivity_scratch']&&!$funcDisplay['wechatactivity_fortunewheel']&&!$funcDisplay['wechatactivity_toend']&&!$funcDisplay['wechatactivity_fortunemachine'] ) echo " display:none"; ?>">
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
										</div>
									</div>
								</div>
								
								<!--wechatschool new added by Sara -->
								<div style="margin-top:12%; margin-bottom:10%; <?php if( !$funcDisplay['wechatschool']) echo " display:none"; ?>">
									<div><label for="name">微行业 </label></div>
									<div>
										<div>
										<ul class="applist">
											<?php if( $funcDisplay['wechatschool'] ) {?>
												<li><input type="checkbox" name="selCheck[]" value="wechatschool" <?php if( $selCheck['wechatschool'] ) echo " checked"; ?> >  微学校</input></li>
											<?php }  ?>
											
										</div>
									</div>
								</div>
								
							</div>
							<div style="margin-top: 30px;">
								<input type="submit" class="btn btn-primary" value="下一步" style="width:120px; margin-left:400px"/>	
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