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
//2014-08-18 ���ɾ������ںţ���������Ϊ����Ĺ��ںŶ�Ҫ��ɷǹ���,�˵���������
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
//����в˵�����΢���ϵĲ˵�ɾ��
if(($wechat_type == "pri_svc")||(($wechat_type == "pri_sub")&&($wechat_auth == "1"))){

	$result=wechat_menu_delete($ACC_TOKEN);
}
$po_delete=web_admin_delete_wechatnumber_group($userid, $gweid, $wid, $weid);
//����Ǹ��˹��ںţ���Ҫɾ��wechats��wechat_usechat�Լ�inintfunc���ű�����ǹ������ںţ���Ҫɾ��usechat�Լ�initfunc��wechat_subscribe����ɾwechat_group
//����Աɾ��
$delall=web_admin_delete_vmember_all($gweid);
$delallgroup=web_admin_delete_vmember_all_group($gweid);

//��������ģ�飬ɾ���ϴ���ͼƬ
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

//��һ����Ϊ��������λ�õ��º����gweid�ʹ˴���gweid����
//2014-08-18���ɾ������ںţ���������Ϊ����Ĺ��ںŶ�Ҫ��ɷǹ���,�˵���������
if($shared_flag==2){
	$watsinfo=getWechatGroupActiveInfo($user_id,1);
	foreach($watsinfo as $watinfo){
		$gweid=$watinfo->GWEID;
		$weidinfo=web_admin_usechat_prisvcinfo_group($gweid);//���˷���Ż��߸�����֤���ĺ�
		$pubweidinfo=web_admin_usechat_pubsvcinfo_group($gweid);//���÷���Ż��߹�����֤���ĺţ������в˵��������
		if(!empty($weidinfo)){//˽�˷���Ż�����֤���ĺ�,��΢�Ų˵����и���	
			$GWEID=$gweid;//�Լ���GWEID
			include 'wechat_accountinfo_menu.php';
		}else if(!empty($pubweidinfo)){//��������Ż��߹�����֤���ĺ�,�Բ˵��ظ����ݽ�����գ������л�����ز���������
			foreach($pubweidinfo as $pubinfo){	
				$WEID=$pubinfo->WEID;
				$update=wechat_menu_public_updatenull("","",$WEID);
			}
		}
	}		
	$update=getWechatGroupActive_update(0,$user_id);	
}
//2014-08-18 end



//�жϸ��û��Ĺ��ں��Ƿ񶼱�ɾ��
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
		//�õ����û��ķ���id,����û���δ���ֹ��飬���ᱻ��ΪĬ�Ϸ�����
		$getusergroups = $wpdb->get_results( "SELECT w2.group_id as id FROM ".$wpdb->prefix."users w1 left join ".$wpdb->prefix."user_group w2 on w1.ID = w2.user_id WHERE w1.ID = ".$userid);
		//secure query method
		/* $getusergroupsql = $wpdb -> prepare("SELECT w2.group_id as id FROM ".$wpdb->prefix."users w1 left join ".$wpdb->prefix."user_group w2 on w1.ID = w2.user_id WHERE w1.ID = %d", $userid);
		$getusergroups = $wpdb->get_results($getusergroupsql); */
		foreach($getusergroups as $getusergroup){	
			$groupid = $getusergroup -> id;
		}
		if($groupid == 0 || empty($groupid)) //ȡ��Ĭ�Ϸ����е��û�,Ĭ�Ϸ����Ӧ��id��0�����ǹ̶���
		{
			echo "0success0";
		}else{
			echo $groupid."success0";
		} 
	}
	echo "success";
}

?>