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


$gweid = $_POST["id"];
$status = $_POST["status"];
$active = $_POST["active"];
$setActive = $_POST["setActive"];

if(empty($active)&&(empty($setActive))){//�������������
	$update=getWechatGroup_update($status,$gweid);
	
	$weidinfo=web_admin_usechat_prisvcinfo_group($gweid);//���˷���Ż��߸�����֤���ĺ�
	$pubweidinfo=web_admin_usechat_pubsvcinfo_group($gweid);//���÷���Ż��߹�����֤���ĺţ������в˵��������
	if(!empty($weidinfo)){//˽�˷���Ż�����֤���ĺ�,��΢�Ų˵����и���
		$info=getWechatGroupInfo_gweid($gweid);//get all info by gweid
		foreach($info as $winfos){
			$shared_flag=$winfos->shared_flag;
			$user_id=$winfos->user_id;
		}			
		if($shared_flag==1){
			$weinfo=getWechatGroupActiveInfo($user_id,2);//get info by userid and flg=2
			foreach($weinfo as $gweids){
				$GWEID=$gweids->GWEID;//����ŵ�GWEID
			}
		}else{	
				$GWEID=$gweid;//�Լ���GWEID
		}
		include 'wechat_accountinfo_menu.php';
		

	}else if(!empty($pubweidinfo)){//��������Ż��߹�����֤���ĺ�,�Բ˵��ظ����ݽ�����գ������л�����ز���������

		foreach($pubweidinfo as $pubinfo){	
			$WEID=$pubinfo->WEID;
			$update=wechat_menu_public_updatenull("","",$WEID);
		}

	}
	echo json_encode(array());
}else if(!empty($active)){//������������
	if($status==1){//����Ϊ����
		$status=2;
		$update=getWechatGroup_update($status,$gweid);
		echo json_encode(array());
	}else{//����Ϊ�������������Ϊ����Ķ�Ҫ��ɲ�����
		$update=getWechatGroup_update($status,$gweid);
		$user_id=wechat_group($gweid);
		//���м���䲻������й���˵���Ҫ��������
		$wechatsinfo=getWechatGroupActiveInfo($user_id,1);
		foreach($wechatsinfo as $wechatinfo){
			$gweid=$wechatinfo->GWEID;
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
		echo json_encode(array());
	}
	
}else if(!empty($setActive)){
	$user_id=wechat_group($gweid);
	//�����û�����Ϊ����ı�Ϊ�������Ϊ����
	$update=getWechatGroupActive_updateActive(1,2,$user_id);
	//���øú�Ϊ�����΢�ź�
	$update=getWechatGroup_update(2,$gweid);
	//��������Ϊ����ĺŶ��������ɵ��¼����΢�źŵĲ˵�+�����ո�����Ϊ����ŵ����΢�ź�
	$wechatsinfo=getWechatGroupActiveAllInfo($user_id,1);
	foreach($wechatsinfo as $wechatinfo){
		$gweidshared=$wechatinfo->GWEID;
		$weidinfo=web_admin_usechat_prisvcinfo_group($gweidshared);
		$pubweidinfo=web_admin_usechat_pubsvcinfo_group($gweidshared);
		if(!empty($weidinfo)){	
			$GWEID=$gweid;//�˴�Ϊ����ĺŵ�gweid
			include 'wechat_accountinfo_menu.php';
		}else if(!empty($pubweidinfo)){//��������Ż��߹�����֤���ĺ�,�Բ˵��ظ����ݽ�����գ������л�����ز���������
			foreach($pubweidinfo as $pubinfo){	
				$WEID=$pubinfo->WEID;
				$update=wechat_menu_public_updatenull("","",$WEID);
			}
		}		
	}	
	echo json_encode(array());
}

?>