<?php 
/**
 *    ��Էÿͳ�ʼ��
 *
 *    $sn$
 */

global $wpdb;
defined('IN_IA') or exit('Access Denied');

//new add for the oauth2 without wechat check 
$module_name_oauth = $_GPC['module'];

$MODULE_NAME = array(
	'scratchcard' =>'�ιο�',
	'egg' => '�ҵ�',
	'redenvelope' => '΢���',
	'research' => '΢ԤԼ',
	'weshopping' => '΢�̳�',
	'vote' =>'΢ͶƱ',
	'webchat' =>'���ӷ�װ��ת'
);

$_W['gweid'] = intval($_GPC['gweid']);
$_W['gweidv'] = intval($_GPC['gweid']);  //���������gweidvȫ�ֱ�������Ա��ص���Ҫȡ��Ӧ��gweid

if(in_array($MODULE_NAME[$_GPC['module']],$MODULE_NAME) || empty($_GPC['gweid'])){

	/*if($_GPC['module']=='webchat'){//�������Ź��ںű༭����gweid�����Լ�����ģ���Ӧ������Ĵ��룬����ǹ���������Ҫ
		$info=getWechatGroupInfo_gweid($_W['gweid']);//�鿴�ú��Ƿ���
		foreach($info as $winfos){
			$shared_flag=$winfos->shared_flag;
			$user_id=$winfos->user_id;
		}			
		if($shared_flag==1){//�������˵�ģ����ù���Ĳ˵�ģ��
			$weinfo=getWechatGroupActiveInfo($user_id,2);
			foreach($weinfo as $gweids){
				$disgweid=$gweids->GWEID;//����ŵ�GWEID
			}
		}else{	
			$disgweid=$_W['gweid'];//�Լ���GWEID
		}
		$_W['gweid'] = $disgweid;
		$_W['gweidv']=$disgweid;
		$_W['gweidtrue']=intval($_GPC['gweid']);
	}*/

	//20150420 sara new added
	//���ݵ�ǰ��gweidȥ������û�д��ڹ���������£������������µģ���Ҫ��gweid��Ϊ����ŵ�gweid
	$getgroupids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_group w left join {$wpdb->prefix}user_group u on w.user_id = u.user_id where w.GWEID=".$_W['gweid'],ARRAY_A);
	//echo "SELECT * FROM {$wpdb->prefix}wechat_group w left join {$wpdb->prefix}user_group u on w.user_id = u.user_id where w.GWEID=".$_W['gweid'];

	//obtain the groupid
	if(!empty($getgroupids)){
		foreach ($getgroupids as $getgroupid) {
			$gid = $getgroupid['group_id'];
		}
		//echo $gid;
		
		if(!empty($gid)){
			$getflags = $wpdb->get_results("SELECT count(*) as flagcount FROM {$wpdb->prefix}user_group u where u.group_id=".$gid." and u.flag = 1",ARRAY_A);
			//obtain the groupid
			foreach ($getflags as $getflag) {
				$flagcount = $getflag['flagcount'];
			}
			//echo $flagcount;
			
			//����з������Ա,�鿴�����е�������Ƿ��ǿ���״̬
			if($flagcount != 0){
				$getadminusers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_group u where u.group_id=".$gid." and u.flag = 1",ARRAY_A);
				//obtain the groupid
				foreach ($getadminusers as $getadminuser) {
					$adminuserid = $getadminuser['user_id'];
				}
				//echo $adminuserid;
				//$getvgweids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_group u left join {$wpdb->prefix}wechat_group w on u.user_id=w.user_id where u.flag = 1 and w.adminshare_flag = 1 and u.user_id =".$adminuserid,ARRAY_A);
				$getvgweids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_group u left join {$wpdb->prefix}wechat_group w on u.user_id=w.user_id where u.flag = 1 and w.adminshare_flag = 1 and u.group_id=".$gid,ARRAY_A);
				
				//obtain the groupid
				if(!empty($getvgweids)){
					foreach ($getvgweids as $getvgweid) {
						$vgweid = $getvgweid['GWEID'];
					}
					$_W['gweidv'] =  $vgweid;   //������ŵ�gweid������
				}
			}
			// echo $_W['gweidv'];
			// exit();
		}
	}
}

$_W['weid'] = $_SESSION['weid'][$_W['gweidv']];
$_W['fans']['from_user'] = $_SESSION['gopenid'][intval($_W['gweidv'])];


/*for test*/

// if(in_array($MODULE_NAME[$_GPC['module']],$MODULE_NAME)){

// 		$usewidinfo_oauth= $wpdb->get_row( "SELECT u1.WEID,u2.wid,u2.menu_appId,u2.menu_appSc FROM {$wpdb->prefix}wechat_usechat u1,{$wpdb->prefix}wechats u2 where u1.wid=u2.wid and (u2.wechat_type='pri_svc' and u2.wechat_auth='1') and u1.WEID != 0 and u1.GWEID='".intval(intval($_W['gweid']))."'" ,ARRAY_A);
// 		//ͨ��gweid�õ�˽�е���֤�������Ϣ������������ò���
// 		if(!empty($usewidinfo_oauth['wid'])){
// 			$errorcode=$_GET['errorcode'];
// 			if(empty($errorcode)){
// 				$_W['fans']['from_user']=$_SESSION['oauth_openid_common']['openid'];
// 				$_W['weid']=$_SESSION['oauth_weid_common']['weid'];
// 				if(empty($_W['fans']['from_user'])||($_SESSION['oauth_openid_common']['gweid']!=intval($_W['gweidv']))){
// 					$appid=$usewidinfo_oauth['menu_appId'];
// 					$secret=$usewidinfo_oauth['menu_appSc'];
// 					$reurl=urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
// 					$rurl=home_url().'/wp-content/themes/ReeooV3/wesite/common/common_oauth_openid_test.php?appid='.$appid.'&secret='.$secret.'&gweid='.intval($_W['gweidv']).'&gweidtrue='.intval($_W['gweid']).'&reurl='.$reurl;
// 					$redriect_url=urlencode($rurl);
// 					$url = $rurl.'&appid='.$appid.'&redirect_uri='.$redriect_url.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
// 					header("Location:".$url);
// 					$_W['fans']['from_user']=$_SESSION['oauth_openid_common']['openid'];
// 					$_W['weid']=$_SESSION['oauth_weid_common']['weid'];
// 				}
// 			}
// 			if(empty($_W['fans']['from_user'])||($_SESSION['oauth_openid_common']['gweid']!=intval($_W['gweidv']))){
// 				exit();
// 			}
// 		}
// }
$global_vars = $wpdb -> get_row($wpdb -> prepare("SELECT * FROM {$wpdb -> prefix}shopping_global WHERE gweid=%s",$_W['gweid']),ARRAY_A);
if($_GPC['module']=='weshopping' && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){
	//$_SESSION['oauth_openid_common']['gweid'] = intval($_W['gweidv']);
	//$_SESSION['oauth_openid_common']['openid'] = 'abcdtest';
	if($_SESSION['oauth_openid_common']['gweid'] ==intval($_W['gweidv']))
		$_W['fans']['from_user']=$_SESSION['oauth_openid_common']['openid'];	
	else
		$_W['fans']['from_user'] = null;
}
if(in_array($MODULE_NAME[$_GPC['module']],$MODULE_NAME) || empty($_W['gweid'])){
	if((isset($_SERVER['HTTP_USER_AGENT']))&&(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)){
		if(empty($_W['fans']['from_user'])){
			$usewidinfo_oauth= $wpdb->get_row( "SELECT u1.WEID,u2.wid,u2.menu_appId,u2.menu_appSc FROM {$wpdb->prefix}wechat_usechat u1,{$wpdb->prefix}wechats u2 where u1.wid=u2.wid and (u2.wechat_type='pri_svc' and u2.wechat_auth='1') and u1.WEID != 0 and u1.GWEID='".intval(intval($_W['gweid']))."'" ,ARRAY_A);
			//ͨ��gweid�õ�˽�е���֤�������Ϣ������������ò���
			if(!empty($usewidinfo_oauth['wid'])){
				$errorcode=$_GET['errorcode'];
				if(empty($errorcode)){
					$_W['fans']['from_user']=$_SESSION['oauth_openid_common']['openid'];
					$_W['weid']=$_SESSION['oauth_weid_common']['weid'];
					if(empty($_W['fans']['from_user'])||($_SESSION['oauth_openid_common']['gweid']!=intval($_W['gweidv']))){
						$appid=$usewidinfo_oauth['menu_appId'];
						$secret=$usewidinfo_oauth['menu_appSc'];
						$reurl=urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
						$rurl=home_url().'/wp-content/themes/ReeooV3/wesite/common/common_oauth_openid.php?appid='.$appid.'&secret='.$secret.'&gweid='.intval($_W['gweidv']).'&gweidtrue='.intval($_W['gweid']).'&reurl='.$reurl;
						$redriect_url=urlencode($rurl);
						$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redriect_url.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
						header("Location:".$url);
						$_W['fans']['from_user']=$_SESSION['oauth_openid_common']['openid'];
						$_W['weid']=$_SESSION['oauth_weid_common']['weid'];
					}
				}
				if(empty($_W['fans']['from_user'])||($_SESSION['oauth_openid_common']['gweid']!=intval($_W['gweidv']))){
					exit();
				}
			}
		}
	}
}

//new add for the oauth2 end

$_W['fans']['mid'] = $_SESSION['gmid'][intval($_W['gweidv'])]['mid'];

if(!empty($_W['fans']['from_user']))
	$_SESSION['fromuser'] = $_W['fans']['from_user'];
else
	unset($_SESSION['fromuser']);

if(!empty($_W['fans']['from_user']) && !empty($_W['weid']) && !empty($_W['gweidv']) && empty($_W['fans']['mid'])){
	
	$_W['fans']['mid'] = $wpdb -> get_var("SELECT mid FROM {$wpdb -> prefix}wechat_member_group WHERE `WEID`='{$_W['weid']}' AND `GWEID`='{$_W['gweidv']}' AND `from_user`='{$_W['fans']['from_user']}'");
}

if((empty($_W['fans']['mid']))){
	$_W['fans']['mid'] = NULL;
	if((!empty($_W['fans']['from_user']))&&(!empty($_W['weid']))&&(!empty($_W['gweidv']))){
		$_W['fans']['mid'] = $wpdb->get_var($wpdb->prepare("SELECT mid FROM {$wpdb->prefix}wechat_member_group where WEID=%d and GWEID=%d and from_user= %s",$W['weid'],$_W['gweidv'],$_W['fans']['from_user']));
	}			
}

if(!empty($_W['fans']['mid'])){
	$_SESSION['mid'] = $_W['fans']['mid'];
}


//new add for the third party service
if($_GPC['module']=='webchat'){//�������ͷ�����

	$_W['fans']['serviceid'] = "";

	if(!empty($_W['fans']['from_user']) && !empty($_W['weid']) && !empty($_W['gweidv']) && empty($_W['fans']['serviceid'])){

		$_W['fans']['serviceid'] = $wpdb -> get_var("SELECT id FROM {$wpdb -> prefix}wechat_member_thirdservice WHERE `WEID`='{$_W['weid']}' AND `GWEID`='{$_W['gweidv']}' AND `from_user`='{$_W['fans']['from_user']}'");
	}

	if((empty($_W['fans']['serviceid']))){
		$_W['fans']['serviceid'] = NULL;
		if((!empty($_W['fans']['from_user']))&&(!empty($_W['weid']))&&(!empty($_W['gweidv']))){
			$_W['fans']['serviceid'] = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}wechat_member_thirdservice where WEID=%d and GWEID=%d and from_user= %s",$W['weid'],$_W['gweidv'],$_W['fans']['from_user']));
		}			
	}
}
//new add for the third party service end



