<?php
	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');	
	require_once $wp_root_path.'/wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
	require_once $wp_root_path.'/wp-content/themes/ReeooV3/wesite/common/random.php';

	global $wpdb;

	function virtual_gweid_exist($groupid, $userid){
		$gweid = getvirtualaccount_withgroupid($groupid);
		if($gweid){
			//update db tables
			$rlt = update_virtual_withuserid($userid, $gweid);
		} else {
			$gweid = getvirtualaccount_withuserid($groupid);
			if($gweid) {
				//update wechat_group group id
				$rlt = updatevirtualaccount_withgroupid($groupid, $gweid);
			} else {
				//insert one new row at table wechat_group
				$rlt = add_virtual_gweid($userid, $groupid);
			}
		}

		if($rlt) 
			return true;
		else 
			return false;
	}

	function add_virtual_gweid($userid, $groupid){
		$gweid = gweid();  

		//insert wechat_group; wechat_initfunc_info
		$rlt1 = insertvirtualaccount_wechatgroup($gweid,$userid,$groupid); 
			
		$hash = random(5);
		$token=generate_password(10);
		$sitename = '虚拟号';
		$wechat_name = '虚拟号';
		$wechattype = 'pri_svc';
		$wechat_auth = 1;
		$weid = 0;
		$vericode = '';
		$wechat_fans = '';
		$picUrl = '';
		//insert wechats; wechat_usechat
		$rlt2 = web_admin_add_wechat_prisub($hash, $sitename, $wechat_name, $wechattype, $wechat_auth, $token, $weid, $userid, $vericode, $gweid, $wechat_fans, $picUrl,"");

		if($rlt1 && $rlt2)
			return true;
		else
			return false;
	}

	function update_virtual_withgroupid($groupid, $gweid){
		$rlt = updatevirtualaccount_withgroupid($groupid, $gweid);
		return $rlt;
	}

	function update_virtual_withuserid($userid, $gweid){
		//update the user_id at table wechat_group
		$rlt1 = updatevirtualaccount_withuserid($userid, $gweid);
		//update the user_id at table wechat_userchat
		$rlt2 = updateusechat_withuserid($userid, $gweid);

		if($rlt1 && $rlt2)
			return true;
		else
			return false;	
	}

	function remove_userid_virtualaccount($groupid, $userid){
		//reset wechat_group table's $userid =0 and $groupid = $groupid
		$rlt1 = update_wechat_group_resetuserid($groupid, $userid);
		//reset wechat_usechat table's $userid = 0
		$rlt2 = update_wechat_usechat_resetuserid($userid);

		if($rlt1 && $rlt2)
			return true;
		else
			return false;
	}

?>
