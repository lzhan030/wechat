<?php 
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
require_once '../wesite/common/dbaccessor.php';
require_once '../wechat/common/wechat_dbaccessor.php';
require_once '../wechat/common/jostudio.wechatmenu.php';
global $wpdb;
global  $current_user;
$userid = $current_user -> ID;

?>

<?php
$wid=$_GET["wid"];
$userid = $_GET["userid"];
$gweid = $_GET["gweid"];
$weid = $_GET["weid"];
//2014-08-18 如果删除激活公众号，所有设置为共享的公众号都要变成非共享,菜单重新生成
$wechatsinfo=getWechatGroupInfo_gweid($gweid);
foreach($wechatsinfo as $wechatinfo){
	$shared_flag=$wechatinfo->shared_flag;
	$user_id=$wechatinfo->user_id;
}

$widsinfo=web_admin_wechats_info($wid);
foreach($widsinfo as $winfo){
	$wechat_type=$winfo->wechat_type;
	$wechat_auth=$winfo->wechat_auth;
	$APPID=$winfo->menu_appId;
	$APPSECRET=$winfo->menu_appSc;
}
	
$ACC_TOKEN=re_Token($APPID,$APPSECRET);
//如果有菜单，将微信上的菜单删除
if(($wechat_type == "pri_svc")||(($wechat_type == "pri_sub")&&($wechat_auth == "1"))){

	$result=wechat_menu_delete($ACC_TOKEN);
}
$po_delete=web_admin_delete_wechatnumber_group($userid, $gweid, $wid, $weid);
//如果是个人公众号，需要删除wechats和wechat_usechat以及inintfunc三张表；如果是公共公众号，需要删除usechat以及initfunc、wechat_subscribe。都删wechat_group
//将会员删除
$delall=web_admin_delete_vmember_all($gweid);
$delallgroup=web_admin_delete_vmember_all_group($gweid);

//遍历所有模块，删除上传的图片
$module_list = array('material','wesite','research','wepay','weshopping','egg','scratchcard','wxwall','vote','redenvelope');
defined('IN_SYS') || define('IN_SYS', true);
require_once ABSPATH.'/framework/bootstrap.inc.php';
foreach ($module_list as $module) {
	$method = 'onWechatAccountDelete';
	$module_site = WeUtility::createModuleSite($module);
	if (method_exists($module_site, $method)) {
		$module_site->onWechatAccountDelete($gweid);
	}
}

//这一块因为放在上面位置导致后面的gweid和此处的gweid混淆
//2014-08-18如果删除激活公众号，所有设置为共享的公众号都要变成非共享,菜单重新生成
if($shared_flag==2){
	$watsinfo=getWechatGroupActiveInfo($user_id,1);
	foreach($watsinfo as $watinfo){
		$gweid=$watinfo->GWEID;
		$weidinfo=web_admin_usechat_prisvcinfo_group($gweid);//个人服务号或者个人认证订阅号
		$pubweidinfo=web_admin_usechat_pubsvcinfo_group($gweid);//共用服务号或者共用认证订阅号，对现有菜单内容清空
		if(!empty($weidinfo)){//私人服务号或者认证订阅号,对微信菜单进行更新	
			$GWEID=$gweid;//自己的GWEID
			include 'wechat_accountinfo_menu.php';
		}else if(!empty($pubweidinfo)){//公共服务号或者公共认证订阅号,对菜单回复内容进行清空，采用切换后的素材重新设置
			foreach($pubweidinfo as $pubinfo){	
				$WEID=$pubinfo->WEID;
				$update=wechat_menu_public_updatenull("","",$WEID);
			}
		}
	}		
	$update=getWechatGroupActive_update(0,$user_id);	
}
//2014-08-18 end



//判断该用户的公众号是否都被删除
//$countnmubers = $wpdb->get_results( "SELECT count(*) as countw FROM ".$wpdb->prefix."wechat_group where user_id = ".$userid );
//secure query method
$countnmuberssql = $wpdb -> prepare("SELECT count(*) as countw FROM ".$wpdb->prefix."wechat_group where user_id = %d", $userid);
$countnmubers = $wpdb->get_results($countnmuberssql);
foreach($countnmubers as $countnmuber){	
	$countwechatnumber = $countnmuber -> countw;
}


if($po_delete===false){
	echo "error";
}else{

    if(empty($_SESSION['GWEID'])) {
	    echo "success1";
	}else{
		//得到该用户的分组id,如果用户从未被分过组，它会被分为默认分组中
		$getusergroups = $wpdb->get_results( "SELECT w2.group_id as id FROM ".$wpdb->prefix."users w1 left join ".$wpdb->prefix."user_group w2 on w1.ID = w2.user_id WHERE w1.ID = ".$userid);
		//secure query method
		/* $getusergroupsql = $wpdb -> prepare("SELECT w2.group_id as id FROM ".$wpdb->prefix."users w1 left join ".$wpdb->prefix."user_group w2 on w1.ID = w2.user_id WHERE w1.ID = %d", $userid);
		$getusergroups = $wpdb->get_results($getusergroupsql); */
		foreach($getusergroups as $getusergroup){	
			$groupid = $getusergroup -> id;
		}
		if($groupid == 0 || empty($groupid)) //取出默认分组中的用户,默认分组对应的id是0，这是固定的
		{
			echo "0success0";
		}else{
			echo $groupid."success0";
		} 
	}
	echo "success";
}

?>