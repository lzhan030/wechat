<?php

$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';
require_once ('../wesite/common/dbaccessor.php');

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $current_user;
//判断当前用户是否是某个分组管理员下的
$groupadminflag = web_admin_issuperadmin($current_user->ID);
$id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
//$id =  (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
//$weid=$_SESSION['WEID'];
$weid=intval($_GET['weid']);
//2014-06-24新增，通过session获取gweid
$gweid=$_SESSION['GWEID'];


//2014-07-07新增修改，去掉function_custom页面后，提交到该页面下面的方法不执行了
//是否点击提交事件	
   if( isset($_POST['templateselect']) ){
	
	//2014-07-07新增修改注释
	/* $wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_name = '".$_POST['templateselect']."' WHERE type = 'WEID' AND value = ".$weid." AND func_name like '%template%';");
	//2014-06-24新增修改，最终保存到gweid中
	$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_name = '".$_POST['templateselect']."' WHERE type = 'GWEID' AND value = ".$gweid." AND func_name like '%template%';"); */
	
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
	
	$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE WEID =".$weid." AND func_name NOT like '%template%' AND EXISTS(SELECT * FROM  `wp_wechat_func_info` WHERE  `func_name` =  ".$wpdb->prefix."wechat_initfunc_info.func_name AND  `status` =0 )");
	foreach($result as $initfunc){
		$selCheck[$initfunc->func_name] = $initfunc->func_flag;
	}
	if(isset($_POST['selCheck'])){
		foreach($_POST['selCheck'] as $check)
			$selCheck[$check] = 1;
			}
	foreach($selCheck as $func_name => $func_flag){
		
		//$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_flag = '$func_flag' WHERE type = 'WEID' AND value = ".$weid." AND func_name='$func_name';");
		
		$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID',$weid, $func_name, $func_flag));
		
	}
	
	//2014-06-24新增修改，将上面weid的相关记录也写入gweid里面
	//取出当前gweid对应的字段内容和上面的weid中的内容取并集(gweid在创建用户的时候就初始化过，每一次添加公众号都会更新相应的gweid中的字段)
	$selCheckg['wechatwebsite'] = 0;
	$selCheckg['wechatfuncfirstconcern'] = 0;
	$selCheckg['wechatfunckeywordsreply'] = 0;
	$selCheckg['wechatfuncmanualreply'] = 0;
	$selCheckg['wechatfuncaccountmanage'] = 0;
	$selCheckg['wechatfuncmaterialmanage'] = 0;
	$selCheckg['wechatfuncmenumanage'] = 0;
	$selCheckg['wechatfuncusermanage'] = 0;
	$selCheckg['wechatactivity_coupon'] = 0;
	$selCheckg['wechatactivity_scratch'] = 0;
	$selCheckg['wechatactivity_fortunewheel'] = 0;
	$selCheckg['wechatactivity_toend'] = 0;
	$selCheckg['wechatactivity_fortunemachine'] = 0;
	$selCheckg['wechatfuncnokeywordsreply'] = 0;
	$selCheckg['wechatvip'] = 0;
	$selCheckg['wechatresearch'] = 0; //research new added
	$selCheckg['wechatschool'] = 0;   //wechatschool new added
	$gweidresult = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'GWEID' and value =".$gweid);
	
	foreach($gweidresult as $initfunc){
		$selCheckg[$initfunc->func_name] = $initfunc->func_flag;
	}
	foreach($selCheck as $func_name => $func_flag){
	    
		if($selCheckg[$func_name] == 1)
		{
		  
		    $func_flag = 1;
			$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$gweid, $func_name, $func_flag));
		}
		else
		{
		    
			$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$gweid, $func_name, $func_flag));
		}
	}
}

//判断是否是分组管理员
$groupadminflag = web_admin_issuperadmin($current_user->ID);
$user_id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
//$user_id =  (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
$user = get_userdata( $user_id ); 
$rs=web_admin_array_selectvericode($weid);
foreach($rs as $vs)
{ $v=$vs->vericode; 
}

$widusechat = $wpdb->get_var( "SELECT wid from ".$wpdb->prefix."wechat_usechat WHERE WEID =".$weid);
$usertype = $wpdb->get_var( "SELECT wechat_type from ".$wpdb->prefix."wechats WHERE wid =".$widusechat);

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>添加公用微信号成功</title>	
	</head>
	<body>	
		<div id="primary" class="site-content">
			<div id="content" role="main">
				<!--2014-07-15新增修改，页面跳转到添加公众号页面-->
				<form id="myform" action="<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_account.php?beIframe&weid=<?php echo $weid;?>" method="post">
					<div>
						<div class="bgimg"></div>
						<?php if( ($usertype == "pub_sub")||($usertype == "pub_svc") ){?>					
						<div>
							<div class="alert alert-success" style="margin:50px;padding:30px; ">
								<div style="float:left; margin-right:20px;">
									<span class="glyphicon glyphicon-ok" style="font-size: 36px;"></span>
								</div>
								<div>
									<font size="3" face="Verdana">
									<?php echo "添加公众号已成功!<br/>您的公众号信息可以在公众号管理界面进行查询或管理！"?></font>
								</div>

							<table width="400" height="30" border="0" style=" margin-top:50px; " id="table1">
									<tr>
										<td width="150"><label for="name">您获取的验证码为：</label></td>
										<td width="50"><font class="fontpurple"><?php echo $v; ?></font></td>
				
									</tr>
									<tr>
                                        <td width="180"><label for="name">是否将验证码公开：</label></td>
										<td width=""><input type="radio" id="openvericode" name="openvericode" value="1" checked="checked"/><span>是</span></td>
										<td width=""><input type="radio" id="nopenvericode" name="openvericode" value="0" /><span>否</span></td>
									</tr>
							</table >
							</div>
							<div style="margin-top: 30px;">
								<input type="submit" class="btn btn-primary" value="完成" style="width:120px; margin-left:330px"/>	
							</div>
						</div>
				<?php }else {?>
							<div class="alert alert-success" style="margin:50px;padding:30px; ">
								<div style="float:left; margin-right:20px;">
									<span class="glyphicon glyphicon-ok" style="font-size: 36px;"></span>
								</div>
								<div>
									<font size="3" face="Verdana">
									<?php echo "添加公众号已成功!<br/>您的公众号信息可以在公众号管理界面进行查询或管理！"?></font>
								</div>
							</div>
							<div style="margin-top: 30px;">
								<input type="submit" class="btn btn-primary" value="完成" style="width:120px; margin-left:330px"/>	
							</div>
						<?php }  ?>		
				</form>
			
			</div>
		</div>
	</body>
</html>