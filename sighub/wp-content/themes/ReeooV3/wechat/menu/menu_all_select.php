<?php
	$tmp_path = explode ( 'ReeooV3', __FILE__ );
	$template_path=$tmp_path[0];
	require_once $template_path.'ReeooV3/wechat/common/session.php';
	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');
	get_header(); 
	global  $current_user;
	if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
	}	
	include '../../wesite/common/web_constant.php';
	include '../common/wechat_dbaccessor.php';
	include '../../wesite/common/dbaccessor.php';
	include 'menu_permission_check.php';

	$wechat_group=getWechatGroupInfo_gweid($_SESSION['GWEID']);//get information by the GWEID
	foreach($wechat_group as $wgroup){
		$ISVWEID=$wgroup->WEID;
		$user_id=$wgroup->user_id;
		$shared_flag=$wgroup->shared_flag;
	}
	if($shared_flag!=2){//非激活的

		$isusechatinfo=web_admin_usechat_info_dis_group($_SESSION['GWEID']);//get wid,usechat,gweid		
		foreach($isusechatinfo as $ucinfo){					
			$iswid=$ucinfo->wid;				
			$iswidinfo=wechat_wechats_info($iswid);//get wechats by wid			
			foreach($iswidinfo as $wifo){
					$iswechat_type=$wifo->wechat_type;
					$iswechat_auth=$wifo->wechat_auth;				
					if(($iswechat_type=="pub_svc")||(($iswechat_type=="pub_sub")&&($iswechat_auth=="1"))){	
						$isweidinfo=web_admin_usechat_winfo_bywids($iswid,$_SESSION['GWEID']);//usechat info by wid+gweid
						foreach($isweidinfo as $winfo){
								$WEIDPUB=$winfo->WEID;								
						}
						$ispub="ispub";	
					}else{
						$ispub="ispri";

					}			
			} 
		}
	}
?>
<?php if($ispub=="ispub"){?>
			<script>
				location.href='<?php bloginfo("template_directory"); ?>/wechat/menupublicsvc/menu.php?beIframe&WEID=<?php echo $WEIDPUB;?>';
			</script>
<?php	}else if($ispub=="ispri"){?>
			<script>
				location.href='<?php bloginfo('template_directory'); ?>/wechat/menu/menu.php?beIframe';
			</script>
<?php } ?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/wsite.css" />
		<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/bootstrap.min.css">
		<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.min.js"></script>
		<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/bootstrap.min.js"></script>
		<title>菜单管理</title>
	</head>
</html>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/editor/themes/default/default.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/easyui/themes/default/easyui.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/easyui/themes/icon.css" />
	<script charset="utf-8" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/editor/kindeditor.js"></script>
	<script charset="utf-8" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/editor/lang/zh_CN.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/easyui/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/easyui/locale/easyui-lang-zh_CN.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/easyui/addin/datagrid-detailview.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.json-2.4.min.js"></script>
	<script>		
		function selWechat(weid){
			if(weid=='-1'){
				location.href='<?php bloginfo('template_directory'); ?>/wechat/menu/menu.php?beIframe';
			}else if(weid=='-2'){
				location.href='<?php bloginfo('template_directory'); ?>/wechat/menu/menu_invented.php?beIframe';
			}else{
				location.href='<?php bloginfo('template_directory'); ?>/wechat/menupublicsvc/menu.php?beIframe&WEID='+weid;
			}
		}		   
		</script>
	</head>
	<body>
	<div class="main_auto">
		<div class="main-title">
			<div class="title-1">当前位置：菜单管理> <font class="fontpurple">菜单管理 </font>
			</div>
		</div>
		<div class="bgimg"></div>
		<div class="main keyWordMain" style="margin-left:0px; width:570px;height:120px;">
			<tr>
				<td width="225"><label for="name">请选择要操作的微信号: </label></td>
				<td>
					<select name="wechattype" class="form-control" size="1" type="text;" id="wechattype" value="5" maxlength="20"
					onchange="selWechat(this.options[this.selectedIndex].value)">
						<option value="" selected="selected">请选择</option>
						<?php //处理激活的菜单下拉
							$wechat_group=getWechatGroupInfo_gweid($_SESSION['GWEID']);
							foreach($wechat_group as $wgroup){
								$WEID=$wgroup->WEID;
								$user_id=$wgroup->user_id;
								$shared_flag=$wgroup->shared_flag;
							}
							if($shared_flag==2){//处理激活的菜单下拉?>
								<option value="-2" >共享微信菜单设置</option>
							<?php
								$gweids=getWechatGroupInfo_gweid_shared($user_id);//所有共享号
								foreach($gweids as $gweidinfo){
									$gweid=$gweidinfo->GWEID;
									$weidinfo=web_admin_usechat_pubsvcinfo_group($gweid);//公共号+有菜单
									foreach($weidinfo as $winfo){
										$WEID=$winfo->WEID;
										$wechat_nikename=$winfo->wechat_nikename;
							?>		
										<option value="<?php echo $WEID;?>"><?php echo $wechat_nikename;?></option>
							<?php   }	
								}
							}else{//非共享号的菜单下拉
						
						
							$usechatinfo=web_admin_usechat_info_dis_group($_SESSION['GWEID']);
							$haveprisvc=web_admin_usechat_prisvcinfo_group($_SESSION['GWEID']);
							?>	
							<?php	if(!empty($haveprisvc)){?>
								<option value="-1" >个人微信服务号</option>
							<?php }	?>
						<?php foreach($usechatinfo as $ucinfo){
								$wid=$ucinfo->wid;
								$widinfo=wechat_wechats_info($wid);
								foreach($widinfo as $wifo){
									$wechat_type=$wifo->wechat_type;
									$wechat_nikename=$wifo->wechat_nikename;
									$wechat_auth=$wifo->wechat_auth;
									//$weidinfo=web_admin_usechat_winfo_bywid($wid);
								if(($wechat_type=="pub_svc")||(($wechat_type=="pub_sub")&&($wechat_auth=="1"))){	
									$weidinfo=web_admin_usechat_winfo_bywids($wid,$_SESSION['GWEID']);
									foreach($weidinfo as $winfo){
										$WEID=$winfo->WEID;
									?><option value="<?php echo $WEID;?>"><?php echo $wechat_nikename;?></option> <?php
									}} 
								} 
							}	}?>
					</select>
				</td>						
			</tr>			
		</div>
	</div>
	</body>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
