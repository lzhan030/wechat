<?php
session_start();
require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
require_once './wp-content/themes/ReeooV3/wechat/common/jostudio.wechatmenu.php'; 
require_once ('./wp-content/themes/ReeooV3/wesite/common/upload.php'); 
require_once './wp-content/themes/ReeooV3/wesite/common/random.php';
	require_once './wp-content/themes/admin/cgi-bin/virtual_gweid.php';
global $wpdb;

$wid = $_GET['wid'];
$userid = intval($_GET['id']);
$wtype = $_GET['wtype'];
$delimgid = $_POST['delimgid'];//该值为-1，表示删除图片
//根据userid和wid获取对应的weid,然后再更新
$getresult = $wpdb->get_results($wpdb->prepare("SELECT WEID FROM ".$wpdb->prefix."wechat_usechat  WHERE user_id = ".$userid." and wid =%d",$wid));
foreach($getresult as $result)
{
	$newweid = $result->WEID;
}

//根据userid找到其对应的gweid
$getgweids = $wpdb->get_results( "SELECT GWEID FROM ".$wpdb->prefix."wechat_group WHERE user_id = ".$userid);

//2014-06-30新增修改单独更新每一个添加的公众号
if( isset($_POST['accountupdate']) &&!empty($_POST['accountupdate']))
{
    $wid = intval($_POST['accountupdate']);
	$auth = "wechat_auth".$wid;
	$vericode = "vericodeopen".$wid;
    $user_wechatname = $_POST['user_wechatname'];
    $wechat_auth = $_POST[$auth];
	$wechatdesp = $_POST['wechatdesp'];    //获取页面上填写的微信公众号名称字段
	$wechat_vericode = $_POST['user_vericode'];
	$wechat_busexit = $_POST['busexit'];
	$wechat_exireply_content = $_POST['exireply_content'];
	$wechat_vericodeopen = $_POST[$vericode];
	$wechatmenuappid = trim($_POST['menuappId']);
	$wechatmenuappsc = trim($_POST['menuappSc']);
	
	//第三方客服设置入库
	if(isset($_POST['cuservicethird_url'])){
		$cuservicepost=trim($_POST['cuservicethird_url']);
	}else{
		$cuservicepost="";
	}
	
	/*上传图片add20141210*/
	$type =strtolower(strstr($_FILES['file']['name'], '.'));
	if($type == false){
		$_FILES['file']['name'] = $_FILES['file']['name'].".jpg";
		$type = ".jpg";
	}
	$picname = $_FILES['file']['name'];
	$picsize = $_FILES['file']['size'];

	if ($picname != "") {
		if ($picsize > 1024000) {
			$hint = array("status"=>"success","message"=>"图片大小不能超过1M!");
			echo json_encode($hint);
			exit;
		}
		
		if ($type != ".gif" && $type != ".jpg"&& $type != ".png" && $type != ".jpeg") {
			$hint = array("status"=>"success","message"=>"图片格式不对!");
			echo json_encode($hint);
			exit;
		} 
		
		$up=new upphoto();	
		$picUrl=$up->save();
	}
	/*上传图片END*/
	//2014-07-16新增修改
	$weid = $_POST['wechatweid'];
	$gweid = $_POST['wechatgweid'];
	//2014-07-10新增修改
	$getresult = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wechats WHERE wid = %d",$wid));
	foreach($getresult as $result)
	{ 
		$wechattype = $result->wechat_type;
		$wechatauthold = $result->wechat_auth;
	} 
	
	//2014-07-10新增修改,判断公用的验证码不能重复
	if($wechattype == "pub_sub" || $wechattype == "pub_svc")
	{
	    $vericodecounts=web_admin_pubvericode_count($wechat_vericode, $wid, $weid);
		foreach($vericodecounts as $vericodecount){
			$count=$vericodecount->accountCount;
		}
		if($count>=1)
		{ 
		    $submitflag = false; ?>
		    <script>
				alert("验证码添加重复，请重新添加");
			</script>
		<?php $info = "提交失败";}else{
			$submitflag = true;
			//更新微信昵称等字段
			/* $getresult = $wpdb->get_results( "SELECT wid FROM ".$wpdb->prefix."wechat_usechat  WHERE user_id = ".$userid." and GWEID =".$gweid);
			foreach($getresult as $result)
			{ 
				$wid = $result->wid;
			} */
			$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_nikename = '".$user_wechatname."' WHERE wid = ".$wid." ;");		
			//新添加
			//更新微信公众号名称字段
			//$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_name = '".$wechatdesp."' WHERE wid = ".$wid." ;");
			//2014-07-12新增修改，将wechat_name更新到usechat表中去
			//$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat SET wechat_name = '".$wechatdesp."' WHERE WEID = ".$weid." AND user_id = ".$userid." AND wid = ".$wid." AND GWEID = ".$gweid." ;");
			$data = array(
				'wechat_name' => $wechatdesp,
				);
			if(!empty($picUrl))
				$data['wechat_imgurl'] = $picUrl;
			if($delimgid !=-1)
				$data['wechat_imgurl'] = "";
			$wpdb->update($wpdb->prefix.'wechat_usechat', $data, array('WEID' =>$weid,'user_id' =>$userid,'wid' =>$wid,'GWEID' =>$gweid));
			//更新微信公众号认证情况
			$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_auth = '".$wechat_auth."' WHERE wid = ".$wid." ;");
			
			
			if(($wechattype == 'pub_sub')||($wechattype == 'pub_svc'))
			{
			   //$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat SET vericode = '".$wechat_vericode."', flgopen =".$wechat_vericodeopen." WHERE WEID =".$weid." AND GWEID = ".$gweid." AND user_id = ".$userid." and wid =".$wid." ;");
			   //2014-07-13新增修改
			   $wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat SET vericode = '".$wechat_vericode."', flgopen =".$wechat_vericodeopen.",busi_exit='".$wechat_busexit."',prompt_content='".$wechat_exireply_content."' WHERE WEID =".$weid." AND GWEID = ".$gweid." AND user_id = ".$userid." and wid =".$wid." ;");
			}
			$info = "提交成功";	
		}
	}
	else
	{
	    $submitflag = true;
			//更新微信昵称等字段
		/* $getresult = $wpdb->get_results( "SELECT wid FROM ".$wpdb->prefix."wechat_usechat  WHERE user_id = ".$userid." and GWEID =".$gweid);
		foreach($getresult as $result)
		{ 
			$wid = $result->wid;
		} */
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_nikename = '".$user_wechatname."' WHERE wid = ".$wid." ;");		
		//新添加
		//更新微信公众号名称字段
		//$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_name = '".$wechatdesp."' WHERE wid = ".$wid." ;");
		//2014-07-12新增修改，将wechat_name更新到usechat表中去
		// delete 
		//$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat SET wechat_name = '".$wechatdesp."' WHERE WEID = ".$weid." AND user_id = ".$userid." AND wid = ".$wid." AND GWEID = ".$gweid." ;");
		$data = array(
				'wechat_name' => $wechatdesp,
				);
			if(!empty($picUrl))
				$data['wechat_imgurl'] = $picUrl;
			if($delimgid !=-1)
				$data['wechat_imgurl'] = "";
			$wpdb->update($wpdb->prefix.'wechat_usechat', $data, array('WEID' =>$weid,'user_id' =>$userid,'wid' =>$wid,'GWEID' =>$gweid));
		//更新微信公众号认证情况
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_auth = '".$wechat_auth."' WHERE wid = ".$wid." ;");
		//2014-07-08新增修改
		//更新微信公众号(认证的个人订阅号和个人服务号)的menuappid和menuappsc
		/* $wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET menu_appId = '".$wechatmenuappid."' WHERE wid = ".$wid." ;");
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET menu_appSc = '".$wechatmenuappsc."' WHERE wid = ".$wid." ;");
		 */
		 //2014-07-16新增修改
         //只有服务号和认证的订阅号才需要这样更新，个人未认证的订阅号，如果修改为认证的用下面的方式更新
		if($wechattype == "pri_svc" || ($wechattype == "pri_sub" && $wechatauthold == 1))
		{
		    $wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET menu_appId = '".$wechatmenuappid."' WHERE wid = ".$wid." ;");
			$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET menu_appSc = '".$wechatmenuappsc."' WHERE wid = ".$wid." ;");
			
			$info=getWechatGroupInfo_gweid($gweid);//查看该号是否共享
			foreach($info as $winfos){
				$shared_flag=$winfos->shared_flag;
				$user_id=$winfos->user_id;
			}			
			if($shared_flag==1){//如果共享菜单模板采用共享的菜单模板
				$weinfo=getWechatGroupActiveInfo($user_id,2);
				foreach($weinfo as $gweids){
					$GWEIDZERO=$gweids->GWEID;//虚拟号的GWEID
				}
			}else{	
				$GWEIDZERO=$gweid;//自己的GWEID
			}
			
			
			//20140711更新完以后，重新更新自定义菜单到微信-janeen
			require_once './wp-content/themes/ReeooV3/wechat/common/menu_update_forwechat.php';  
		}
		
		/* //更新验证码和是否公布验证码字段
		$getresult = $wpdb->get_results( "SELECT wechat_type FROM ".$wpdb->prefix."wechats WHERE wid = ".$wid);
		foreach($getresult as $result)
		{ 
			$wechattype = $result->wechat_type;
		}  */
		
		//2014-07-10新增修改
		//更新原来是未认证的个人订阅号，现在改为已认证后新输入了menuappid和menuappsc，将其更新到数据库
		if(($wechatauthold == 0 && $wechattype== "pri_sub") && ($wechat_auth == 1 && $wechattype == "pri_sub"))
		{
		    
			$wechatmenuappid1 = trim($_POST['menuappId1']);
			$wechatmenuappsc1 = trim($_POST['menuappSc1']);
			
			$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET menu_appId = '".$wechatmenuappid1."' WHERE wid = ".$wid." ;");
			$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET menu_appSc = '".$wechatmenuappsc1."' WHERE wid = ".$wid." ;");
			
			$info=getWechatGroupInfo_gweid($gweid);//查看该号是否共享
			foreach($info as $winfos){
				$shared_flag=$winfos->shared_flag;
				$user_id=$winfos->user_id;
			}			
			if($shared_flag==1){//如果共享菜单模板采用共享的菜单模板
				$weinfo=getWechatGroupActiveInfo($user_id,2);
				foreach($weinfo as $gweids){
					$GWEIDZERO=$gweids->GWEID;//虚拟号的GWEID
				}
			}else{	
				$GWEIDZERO=$gweid;//自己的GWEID
			}
			
			//20140711更新完以后，重新更新自定义菜单到微信-janeen
			require_once './wp-content/themes/ReeooV3/wechat/common/menu_update_forwechat.php'; 
		}
		
		if(($wechattype == 'pub_sub')||($wechattype == 'pub_svc'))
		{
		   //$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat SET vericode = '".$wechat_vericode."', flgopen =".$wechat_vericodeopen." WHERE WEID =".$weid." AND GWEID = ".$gweid." AND user_id = ".$userid." and wid =".$wid." ;");
		   //2014-07-13新增修改
		   $wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat SET vericode = '".$wechat_vericode."', flgopen =".$wechat_vericodeopen.",busi_exit='".$wechat_busexit."',prompt_content='".$wechat_exireply_content."' WHERE WEID =".$weid." AND GWEID = ".$gweid." AND user_id = ".$userid." and wid =".$wid." ;");
		}
		$info = "提交成功";	
	}
	
	
	//第三方客服入库
	$data = array(
		'wechat_cuservice' => $cuservicepost
	);
	
	$wpdb->update($wpdb->prefix.'wechat_usechat', $data, array('WEID' =>$weid,'user_id' =>$userid,'wid' =>$wid,'GWEID' =>$gweid));
	
}

//2014-07-09新增修改
//2014-07-15新增修改,注释

if( isset($_POST['user_nicename']) ){

    //2014-07-14新增修改
	$user_newpassword = $_POST['user_newpassword'];
	$user_startdate = $_POST['startDate'];
	$user_enddate = $_POST['endDate'];
	$user_account = $_POST['user_account'];
	$user_group = $_POST['groupselect'];
	if($user_group == 0) {
		$user_superadmin = 0;
	} else {
		$user_superadmin = $_POST['superadminflag'];
	}
	
	$oldrole = $_POST['oldrole'];
	$oldgroupid = $_POST['oldgroupid'];

	if(($wtype == 'pub_sub')||($wtype == 'pub_svc'))
	{
	   $user_vericode = $_POST['user_vericode'];
	   $vericodecheck = $_POST['vericodecheck'];
	}
	
	//2014-07-14新增修改
    if($_POST['user_newpassword']!="" && $_POST['user_confirmpassword']!="")
	{
	    $updateresult=wp_update_user( 
					array ( 
						'ID' => $_GET['id'], 
						'user_login' => $_POST['user_nicename'], 
						'display_name' => $_POST['display_name'], 
						'user_email' => $_POST['user_email'] ,
						'user_pass' => $_POST['user_newpassword']
					) 
		) ;
		update_user_meta( $_GET['id'], "contact_name", $_POST['contact_name'], "" );
			
		if($updateresult)
			$info = "提交成功，密码修改生效";	
		else
			$info = "提交失败";
	}	
	else
	{
	    $updateresult=wp_update_user(                                 //更新users表
						array ( 
							 'ID' => $_GET['id'], 
							 'user_login' => $_POST['user_nicename'], 
							 'display_name' => $_POST['display_name'], 							 
							 'user_email' => $_POST['user_email']
							) 
		) ;	
		update_user_meta( $_GET['id'], "contact_name", $_POST['contact_name'], "" );
		if($updateresult)
			$info = "提交成功";	
		else
			$info = "提交失败";	
	}
	
	
	//在user_meta表中更新用户的开始和结束时间 
	update_user_meta( $userid, "startdate", $user_startdate, "" );
	update_user_meta( $userid, "enddate", $user_enddate, "" );
	update_user_meta( $userid, "useraccount", $user_account, "" );
	
	
	//20140918将用户分组写入数据库
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."user_group"."(group_id, user_id, flag)VALUES (%d, %d, %d)",$user_group, $userid, $user_superadmin));
	
	//如果该用户被更新的时候更新为分组管理员,则需要将分组管理员对应的虚拟号默认添加上
	if($oldgroupid == $user_group) {
		//group id is not changed
		if($oldrole == 0 && $user_superadmin  == 1) {
			//old group insert userid
			virtual_gweid_exist($user_group, $userid);	
		} 
		if($oldrole == 1 && $user_superadmin == 0){
			//old group set userid = 0
			remove_userid_virtualaccount($user_group, $userid);
		}
	} else {
		//group id is changed
		if($oldrole == 0 && $user_superadmin == 1) {
			//new group insert userid
			virtual_gweid_exist($user_group, $userid);		
		}
		if($oldrole == 1) {
			//old group set userid = 0
			remove_userid_virtualaccount($oldgroupid, $userid);
			if($user_superadmin == 1) {
				virtual_gweid_exist($user_group, $userid);
			}
		}
	}

	//更新验证码和是否公布字段
	if(($wtype == 'pub_sub')||($wtype == 'pub_svc'))
	{
	   $wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat SET vericode = '".$user_vericode."', flgopen =".$vericodecheck." WHERE user_id = ".$userid." and wid =".$wid." ;");
	}
	
	//更新userspace表	
	//获取已用空间大小
	$userspace = $wpdb->get_results("SELECT * from ".$wpdb->prefix."wesite_space WHERE userid = ".$userid);
	foreach($userspace as $space)
	{
		$usedspace = $space -> used_space;
	}
	
	$wpdb->query( "UPDATE ".$wpdb->prefix."wesite_space SET defined_space =".($_POST['currentspace']*1024)." WHERE userid = ".$userid);
	
	$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_name = '".$_POST['templateselect']."' WHERE type = 'userid' AND value = ".$userid." AND func_name like '%template%';");	
	
	
	$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_func_info")." a WHERE `status`=1");
	foreach($myrows as $myrow){
		$selCheck[$myrow->func_name] = 0;
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
	$selCheck['wechatactivity_egg'] = 0;  //egg module added 
	$selCheck['wechatactivity_wxwall'] = 0; 
	$selCheck['wechatactivity_vote'] = 0; 
	$selCheck['wechatfuncnokeywordsreply'] = 0;
	$selCheck['wechatvip'] = 0; 
	$selCheck['wechatfunceditresponse'] = 0;	
	$selCheck['wepay'] = 0;	
	$selCheck['weshopping'] = 0;	//weshopping new added
	$selCheck['wechatcuservice'] = 0;//第三方客服服务
	if(isset($_POST['selCheck'])){
		foreach($_POST['selCheck'] as $check)
			$selCheck[$check] = 1;
		}
	//2014-07-10新增修改，如果微会员没有被选中，则微学校和微预约都不能选中		
	if($selCheck["wechatvip"] == 0)
	    $selCheck["wechatschool"] = 0;
		
	foreach($selCheck as $func_name => $func_flag){
		
		//$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_flag = '$func_flag' WHERE type = 'userid' AND value = ".$userid." AND func_name='$func_name';");
		$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'userid',$userid, $func_name, $func_flag));
		
		//2014-07-07新增修改，更新到该用户所在的gweid组中
		//2014-07-16新增修改，要更新到userid对应的多个gweid中 
		foreach($getgweids as $getgweid)
		{
			$gweid = $getgweid->GWEID;
			$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$gweid, $func_name, $func_flag));
		}
		
	}
}

$user = get_userdata( $_GET['id'] ); 
$user_contact_name = get_usermeta($_GET['id'],'contact_name'); 

//获取空间大小
$userspace = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."wesite_space WHERE userid = %d",$_GET['id']));
foreach($userspace as $space)
{
    $oldspace = ($space->defined_space)/1024;
	$newspace = ($space->used_space)/1024;
	
}

//获取usermeta各值
$userstartdate = get_user_meta($_GET['id'], "startdate", true);  
$userenddate = get_user_meta($_GET['id'], "enddate", true); 
$useraccount = get_user_meta($_GET['id'], "useraccount", true);
if(empty($useraccount)) {
	$useraccount = 0; 
}

//Get the total number of the fans
$fans_count=wechat_get_count_fans($_GET['id']);
foreach($fans_count as $f) 
{
	$fan_count = $f->fans_count;
}
//Get the total init number of the fans
$usechat_info = wp_wechat_usechat_info($_GET['id']);	
$wechat_init_fans = 0;
if (!empty($usechat_info)) 
{
	foreach($usechat_info as $u) 
	{
		$init_fan = $u->wechat_fan_init;
		$wechat_init_fans = $wechat_init_fans + $init_fan;
	}
}
//fans + init fans
$fan_count = $fan_count + $wechat_init_fans;	


//20140918根据userid找到对应的groupid
//get all groups
$getgroupnames = $wpdb->get_results( "SELECT * FROM {$wpdb -> prefix}group order by ID ASC" );
$getgroups = $wpdb->get_results( "SELECT g1.group_id as gid, g1.flag FROM ".$wpdb->prefix."users u1 left join ".$wpdb->prefix."user_group g1 on u1.ID = g1.user_id left join ".$wpdb->prefix."group g2 on g1.group_id = g2.ID where u1.ID = ".$userid);
foreach($getgroups as $result)
{
	$groupid = $result->gid;
	$userflag = $result->flag;
}
if(empty($groupid))
{
    $groupid = 0;
}


if($user) { 
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
	$selCheck['wechatactivity_egg'] = 0;  //egg module added 
	$selCheck['wechatactivity_redenvelope'] = 0;  //hongbao module added
	$selCheck['wechatactivity_vote'] = 0; 
	$selCheck['wechatactivity_wxwall'] = 0;
	$selCheck['wechatvip'] = 0;
	$selCheck['wechatresearch'] = 0;
	$selCheck['wechatschool'] = 0;   //wechatschool new added
	$selCheck['wechatfunceditresponse'] = 0;
	$selCheck['wepay'] = 0;
    $selCheck['weshopping'] = 0;	//weshopping new added
	$selCheck['wechatcuservice'] = 0;//第三方客服服务
	//$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE WEID = ".$newweid." AND func_name NOT like '%template%'");
	$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wechat_func_info a WHERE EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) LIMIT 0, 100");
	foreach($result as $initfunc){
		$selCheck[$initfunc->func_name] = $initfunc->status;
	}
	$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'userid' AND value = ".$userid." AND func_name like '%template%' limit 1" );
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
	$funcDisplay['wechatactivity_egg'] = 1;   //egg module added 
	$funcDisplay['wechatactivity_redenvelope'] = 1; 
	$funcDisplay['wechatactivity_wxwall'] = 1; 
	$funcDisplay['wechatactivity_vote'] = 1; 
	$funcDisplay['wechatfuncnokeywordsreply'] = 1;
	$funcDisplay['wechatvip'] = 1;
	$funcDisplay['wechatresearch'] = 1;
	$funcDisplay['wechatschool'] = 0;   //wechatschool new added
	$funcDisplay['wechatfunceditresponse'] = 1; 
	$funcDisplay['wepay'] = 1; 
	$funcDisplay['weshopping'] = 1; 
	$funcDisplay['wechatcuservice'] = 1;//第三方客服服务
	
	$result = $wpdb->get_results("SELECT `func_name`,`status` FROM `wp_wechat_func_info`;");
	foreach($result as $func){
		$funcDisplay[$func->func_name] = $func->status;
	}
}

//查找所有分组中的分组管理员情况
$grouparray = array();
foreach($getgroupnames as $getgroupname){
	$groupidn = $getgroupname -> ID;
	$groupnamen = $getgroupname -> group_name;
	$result = $wpdb->get_results("SELECT count(*) as gadmincount from ".$wpdb->prefix."user_group WHERE group_id = ".$groupidn." AND flag = 1 ");
	if(!empty($result)){
		foreach($result as $gadminc){
			$groupadmincount = $gadminc->gadmincount;
		}
	}else{
		$groupadmincount = 0;
	}
	if($groupidn != 0){
		$grouparray[$groupidn] = $groupadmincount;	
	}
	
}

get_header(); 
?>

<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style type="text/css">
	.help-block{clear: both}
	.labeltitle{float:right;margin-right:20px;}
</style>	

<div>
	<form id="useredit" action="" method="post" onsubmit="return checkinput();">
	
	<div class="main-title">
		<div class="title-1">当前位置：用户管理 > <font class="fontpurple">更新用户信息 </font>
		</div>
	</div>
	<?php
		if( isset($_POST['user_nicename']) && !isset($_POST['accountupdate']) ){
		?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 90%;font-size: 18px;margin-left:10px;">	<?php if(isset($info)) echo $info;?><br>
		</p>
		<?php
	} ?>
	<?php
		if( isset($_POST['accountupdate']) ){
		?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 90%;font-size: 18px;margin-left: 10px;">	<?php if(isset($info)) echo $info;?><br>
		</p>
		<?php
	} ?>
	<input type="hidden" value="<?php echo number_format(($newspace),4,".","");?>" id="spaceused">
	<table width="520" height="150" border="0" cellpadding="20px" style="margin-left: 160px; margin-top:15px;" id="table2">
		<tbody>
			<tr>
				<td height="50px;"><label class="labeltitle">编号:</label></td>
				<td width="380"><?php echo $user->ID; ?></td>
				<td></td>
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">用户名:</label></td>
				<td width="380"><input type="text" value="<?php echo $user->user_login; ?>" class="form-control" id="user_nicename" name="user_nicename" readonly="true"></td>
			</tr>
			<tr>  
				<!--<td><label for="user_newpassword">新密码: </label></td>-->
				<td height="50px;"><label class="labeltitle">新密码:</label></td>
				<td><input type="password" value="" class="form-control"
					id="user_newpassword" name="user_newpassword"></td>
			</tr>
			<tr>
				<!--<td><label for="user_confirmpassword">确认密码: </label></td>-->
				<td height="50px;"><label class="labeltitle">确认密码:</label></td>
				<td><input type="password" value="" class="form-control"
					id="user_confirmpassword" name="user_confirmpassword"></td>
			</tr>
		    <tr>
				<td height="50px;"><label class="labeltitle">所属分组:</label></td>
				<td>
					<select name="groupselect" class="form-control" size="1" type="text;margin-left:500px;" id="theme_size" value="5" maxlength="20" onchange="selectgroup(this)" >					   
						<?php foreach($getgroupnames as $getgroupname){
							$groupidh = $getgroupname -> ID;
							$groupname = $getgroupname -> group_name;
							?>
								<option value="<?php echo $groupidh;?>" <?php if($groupid == $groupidh){echo 'selected="selected"';}?> ><?php echo $groupname;?></option>
						<?php }?>	
					</select>
					<input type="hidden" name="oldgroupid" value="<?php echo $groupid;?>" />
				</td>						
			</tr>
			<tr class="superadminselect" style="display:<?php if($groupid == 0){echo 'none';}?>">
				<td height="50px;" style="vertical-align: top;"><label class="labeltitle">角色:</label></td>
				<td>
					<select class="form-control" name="superadminflag" id="roleselect" <?php if($userflag == 0 && $grouparray[$groupid] == 1) {?>disabled="disabled"<?php }?> >
						<option value="0" <?php if($userflag == 0) {?>selected="selected"<?php }?> >普通用户</option>
						<option value="1" <?php if($userflag == 1) {?>selected="selected"<?php }?> >分组管理员</option>						
					</select>
					<div class="help-block" id="havegadmin" >注：若该组已有分组管理员，则其他同组用户不能再设定为分组管理员。</div>
					<input type="hidden" name="oldrole" value="<?php echo $userflag; ?>" />
				</td>						
			</tr>
			<tr>
				<td height="50px;" style="vertical-align: top;"><label class="labeltitle">显示昵称:</label></td>
				<td>
					<input type="text" value="<?php echo $user->display_name; ?>" class="form-control" id="display_name" name="display_name">
					<div class="help-block">作为后台发表文章时的显示昵称</div>
				</td>
			</tr>			
			<tr>
				<td height="50px;"><label class="labeltitle">粉丝总数量:</label></td>
				<td><input type="text" value="<?php echo $fan_count; ?>" class="form-control" id="fan_count" name="fan_count" disabled="disabled" /></td>
			</tr>
			<tr>
				<!--<td height="50px;">当前空间大小: </td>
				<td><input type="text" value="<?php echo $oldspace?$oldspace." M":"0.00"." M"; ?>" class="form-control" id="currentspace" name="currentspace"  readonly="true"></td>-->
				<td height="50px;"><label class="labeltitle">当前空间大小:</label></td>
				<td><input type="text" style="width:90%;display: inline;" value="<?php echo $oldspace?number_format(($oldspace),4,".",""):"0.00"; ?>" class="form-control" id="currentspace" name="currentspace"> G</td>
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">剩余空间大小:</label></td>
				<td><input type="text" style="width:90%;display: inline;" value="<?php echo number_format(($oldspace - $newspace),4,".","").""; ?>" class="form-control" id="remainspace" name="remainspace" readonly="true"> G</td>
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">可建立公众号数目:</label></td>
				<td><input type="text" value="<?php echo $useraccount; ?>" class="form-control" id="user_account" name="user_account" /></td>
			</tr>
			<?php if( $user->user_login != "admin" ) {?>  
			<tr>
				<td height="50px;"><label class="labeltitle">开始时间:</label></td>
				<td><input name="startDate" type="text" class="form-control" id="startDate" size="10" maxlength="10" onclick="new Calendar().show(this);" value="<?php echo $userstartdate; ?>" /></td>
				
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">结束时间:</label></td>
				<td><input name="endDate" type="text" class="form-control" id="endDate" size="10" maxlength="10" onclick="new Calendar().show(this);"  value="<?php echo $userenddate; ?>"/></td>
				
			</tr>
			<?php }  ?>
			<tr>
				<td height="50px;"><label class="labeltitle">联系人:</label></td>
				<td>
				<input type="text" value="<?php echo $user_contact_name; ?>" class="form-control" id="contact_name" name="contact_name">
				</td>
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">E-mail:</label></td>
				<td><input type="text" value="<?php echo $user->user_email; ?>" class="form-control"
					id="user_email" name="user_email"></td>
			</tr>			
		</tbody>
	</table>
	
	<div class="alert alert-warning" style="margin-left:144px;padding-bottom:60px;width:600px;margin-top:10px;">
	<div>
		<label for="name" style="margin-left:0px; margin-top:1%;">功能选择: </label>
		<input type="checkbox" name="allChecked" onclick="check_all(this, 'selCheck[]')" value="true" style="margin-right:10px">全选/取消全选</input>
	</div>
	<div class="bgimg_warning"></div>
	<div style="margin-top: 1%; margin-left: 20px; <?php if( !$funcDisplay['wechatwebsite'] ) echo " display:none"; ?>">
		<ul class="applist">
		<li><input type="checkbox" name="selCheck[]" id="wechatwebsite" value="wechatwebsite"
			style="" <?php if( $selCheck['wechatwebsite'] ) echo " checked"; ?>> 微官网</input>
		</li>
		</ul>
	</div>
	<div style="margin-top: 1%; margin-left: 20px; <?php if( !$funcDisplay['wechatcuservice'] ) echo " display:none"; ?>">
		<ul class="applist">
		<li><input type="checkbox" name="selCheck[]" id="wechatcuservice" value="wechatcuservice"
			style="" <?php if( $selCheck['wechatcuservice'] ) echo " checked"; ?>> 第三方客服</input>
		</li>
		</ul>
	</div>
	<div style="margin-top: 8%; <?php if( !$funcDisplay['wechatfuncfirstconcern']&&!$funcDisplay['wechatfunckeywordsreply']&&!$funcDisplay['wechatfuncnokeywordsreply']&&!$funcDisplay['wechatfuncmanualreply']&&!$funcDisplay['wechatfuncaccountmanage']&&!$funcDisplay['wechatfuncaccountmanage']&&!$funcDisplay['wechatfuncmaterialmanage']&&!$funcDisplay['wechatfuncmenumanage']&&!$funcDisplay['wechatfuncusermanage']&&!$funcDisplay['wechatfunceditresponse']) echo " display:none"; ?>">
		<div style="margin-left: 20px;">
			<label for="name">微信功能 </label>
		</div>
		<div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatfuncfirstconcern'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncfirstconcern" value="wechatfuncfirstconcern" style="" <?php if( $selCheck['wechatfuncfirstconcern'] ) echo " checked"; ?>> 首次关注 </input></li>
				<?php } ?>
				
				<?php if( $funcDisplay['wechatfunckeywordsreply'] ) {?>  
				<li><input type="checkbox" name="selCheck[]" id="wechatfunckeywordsreply" value="wechatfunckeywordsreply" style="margin-left: 5%;" <?php if( $selCheck['wechatfunckeywordsreply'] ) echo " checked"; ?>> 关键词回复</input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatfuncnokeywordsreply'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncnokeywordsreply" value="wechatfuncnokeywordsreply" style="margin-left: 5%;" <?php if( $selCheck['wechatfuncnokeywordsreply'] ) echo " checked"; ?>> 无匹配回复</input></li>
				 <?php }  ?>
				 
				<?php if( $funcDisplay['wechatfunceditresponse'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatfunceditresponse" value="wechatfunceditresponse" style="margin-left: 5%;" <?php if( $selCheck['wechatfunceditresponse'] ) echo " checked"; ?>> 可编程回复</input></li>
				 <?php }  ?>
				 
				<?php if( ($funcDisplay['wechatfuncmanualreply'])&&($wechattype != "pri_sub")&&($wechattype != "pub_sub") ) {?> 
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncmanualreply" value="wechatfuncmanualreply"style="margin-left: 5%;" <?php if( $selCheck['wechatfuncmanualreply'] ) echo " checked"; ?>> 人工回复 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatfuncmass'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncmass" value="wechatfuncmass" style="" <?php if( $selCheck['wechatfuncmass'] ) echo " checked"; ?>> 群发消息 </input></li>
				<?php } ?>
			</ul>	
			</div>
			<div style="margin-top: 1%; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatfuncaccountmanage'] ) {?>
			    <li><input type="checkbox" name="selCheck[]" id="wechatfuncaccountmanage" value="wechatfuncaccountmanage"  <?php if( $selCheck['wechatfuncaccountmanage'] ) echo " checked"; ?>> 账户管理</input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatfuncmaterialmanage'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncmaterialmanage" value="wechatfuncmaterialmanage" style="margin-left: 5%;" <?php if( $selCheck['wechatfuncmaterialmanage'] ) echo " checked"; ?>> 素材管理 </input></li>
				<?php }  ?>
				
				<?php if( ($funcDisplay['wechatfuncmenumanage'])&&($wechattype != "pri_sub")&&($wechattype != "pub_sub") ) {?> 
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncmenumanage" value="wechatfuncmenumanage" style="margin-left: 5%;" <?php if( $selCheck['wechatfuncmenumanage'] ) echo " checked"; ?>> 菜单管理 </input></li>
				<?php }  ?>
				
				<?php if( ($wechattype != "pri_sub")&&($wechattype != "pub_sub") ) {?> 
				<!--<input type="checkbox" name="selCheck[]" value="wechatfuncusermanage" style="margin-left: 5%;" <?php //if( $selCheck['wechatfuncusermanage'] ) echo " checked"; ?>> 微用户管理</input>-->
				<?php }  ?>
			</ul>
			</div>
		</div>
	</div>
	<div style="margin-top: 15%; <?php if( !$funcDisplay['wechatactivity_egg']&&!$funcDisplay['wechatactivity_scratch']) echo " display:none"; ?>">
		<div style="margin-left: 20px;">
			<label for="name">微活动 </label>
		</div>
		<div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
			    <!--<?php if( $funcDisplay['wechatactivity_coupon'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_coupon" value="wechatactivity_coupon" style="" <?php if( $selCheck['wechatactivity_coupon'] ) echo " checked"; ?>> 优惠券 </input></li>
				<?php }  ?>-->
				
				<?php if( $funcDisplay['wechatactivity_scratch'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_scratch" value="wechatactivity_scratch" style="margin-left: 5%;" <?php if( $selCheck['wechatactivity_scratch'] ) echo " checked"; ?>> 刮刮卡 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatactivity_egg'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_egg" value="wechatactivity_egg" style="margin-left: 5%;" <?php if( $selCheck['wechatactivity_egg'] ) echo " checked"; ?>> 砸蛋 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatactivity_wxwall'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_wxwall" value="wechatactivity_wxwall" style="margin-left: 5%;" <?php if( $selCheck['wechatactivity_wxwall'] ) echo " checked"; ?>> 微信墙 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatactivity_redenvelope'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_redenvelope" value="wechatactivity_redenvelope" style="margin-left: 3%;" <?php if( $selCheck['wechatactivity_redenvelope'] ) echo " checked"; ?>> 微红包</input></li>
				<?php }  ?>
				
				<!--<?php if( $funcDisplay['wechatactivity_toend'] ) {?> 
                <li><input type="checkbox" name="selCheck[]" id="wechatactivity_toend" value="wechatactivity_toend" style="margin-left: 3%;" <?php if( $selCheck['wechatactivity_toend'] ) echo " checked"; ?>> 一站到底 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatactivity_fortunemachine'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_fortunemachine" value="wechatactivity_fortunemachine" style="margin-left: 3%;" <?php if( $selCheck['wechatactivity_fortunemachine'] ) echo " checked"; ?>>幸运机</input></li>
				<?php }  ?>-->
			</ul>
			</div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
				
				<?php if( $funcDisplay['wechatactivity_vote'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_vote" value="wechatactivity_vote" style="margin-left: 5%;" <?php if( $selCheck['wechatactivity_vote'] ) echo " checked"; ?>> 微投票 </input></li>
				<?php }  ?>

			</ul>
			</div>
		</div>
	</div>
	<div style="margin-top: 15%; <?php if( !$funcDisplay['wechatvip'] && !$funcDisplay['wechatresearch'] && !$funcDisplay['weshopping'] ) echo " display:none"; ?>">
		<div style="margin-left: 20px;">
			<label for="name">微服务 </label>
		</div>
		<div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatvip'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatvip" value="wechatvip" style="" <?php if( $selCheck['wechatvip'] ) echo " checked"; ?>> 会员管理 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatresearch'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatresearch" value="wechatresearch" style="" <?php if( $selCheck['wechatresearch'] ) echo " checked"; ?>> 微预约 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wepay'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wepay" value="wepay" style="" <?php if( $selCheck['wepay'] ) echo " checked"; ?>> 微支付 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['weshopping'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="weshopping" value="weshopping" style="" <?php if( $selCheck['weshopping'] ) echo " checked"; ?>> 微商城 </input></li>
				<?php }  ?>
			</ul>
			</div>
		</div>
	</div>
	
	<!--wechatschool new added by Sara -->
	<div style="margin-top: 8%; <?php if( !$funcDisplay['wechatschool'] ) echo " display:none"; ?>">
		<div style="margin-left: 20px;">
			<label for="name">微行业 </label>
		</div>
		<div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatschool'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatschool" value="wechatschool" style="" <?php if( $selCheck['wechatschool'] ) echo " checked"; ?>> 微学校 </input></li>
				<?php }  ?>
			</ul>
			</div>
		</div>
	</div>
	
	
	</div>
	
	<div style="margin-top:2%; margin-left:350px; margin-bottom:15px;">
	    <input type="submit" class="btn btn-primary" value="保存" id="sub3" style="width:70px">
	    <input type="button" onclick="location.href='?admin&page=usermanage'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
	</div>
	</form>
</div>

<!--2014-07-08用户添加多个公众号，都需要显示出来-->
	<?php 
	$wechat_group=getWechatGroupInfo($userid);
	foreach($wechat_group as $wchatp){		
		$gweid = $wchatp->GWEID;
		$weid=$wchatp->WEID;
		$shared_flag=$wchatp->shared_flag;
		if($weid!=0){
			$account = web_admin_wechat_info_forwechatNew($weid);
		}else{
			$account = null;
		}
		if(!empty($account)){
		    foreach($account as $useraccount){
				//显示的url链接
				$wechatweid = $useraccount -> WEID;
				$wechattype = $useraccount->wechat_type;
				if(($wechattype == 'pub_sub')||($wechattype == 'pub_svc'))
				{
					$url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoinPublic.php?hash='.$useraccount->hash;
					$url=preg_replace('|^https://|', 'http://', $url);
					$vericode = $useraccount->vericode;
					$flgopen = $useraccount->flgopen;
					$busexit= $useraccount->busi_exit;
					$exireply_content= $useraccount->prompt_content;
				}
				else
				{
					$url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoin.php?hash='.$useraccount->hash;
					$url=preg_replace('|^https://|', 'http://', $url);
				}
				$token = $useraccount->token;
				$wechatnikename = $useraccount->wechat_nikename;
				$wechatname = $useraccount->wechatname;
				$wechat_init_fan = $useraccount->wechat_fan_init;
				$wechatimagurl = $useraccount->wechat_imgurl;
				$wechatauth = $useraccount->wechat_auth;
				$id = $useraccount->wid;
				$wechatmenuappid = $useraccount -> menu_appId;
				$wechatmenuappsc = $useraccount -> menu_appSc;
				$wechatshare = $useraccount -> shared_flag;
				
				//第三方客服
				$info=getWechatGroupInfo_gweid($gweid);//查看该号是否共享
				foreach($info as $winfos){
					$shared_flag=$winfos->shared_flag;
					$user_id=$winfos->user_id;
				}			
				if($shared_flag==1){//如果共享菜单模板采用共享的菜单模板
					$weinfo=getWechatGroupActiveInfo($user_id,2);
					foreach($weinfo as $gweids){
						$disgweid=$gweids->GWEID;//共享号的GWEID
					}
				}else{	
					$disgweid=$gweid;//自己的GWEID
				}
				
				//如果是共享的，则显示的是共享的且不允许编辑，如果是虚拟的话，也是共享的
				$cuservicethirdurl=$wpdb->get_var( $wpdb -> prepare("SELECT wechat_cuservice FROM {$wpdb->prefix}wechat_usechat where GWEID=%d",$disgweid));
				//$cuservicethirdurl=$useraccount -> wechat_cuservice;
				$cuserviceurl=home_url().'/mobile.php?module=webchat&do=Chat&gweid='.$disgweid;
		?>
		<form id="accountupdate" action="" method="post"  onsubmit="return checknull('<?php echo $wechatweid;?>','<?php echo $wechattype;?>','<?php echo $id; ?>','<?php echo $wechatauth; ?>');" enctype="multipart/form-data">
		<div style="width:63%;margin-left:17%;" class="alert alert-info" role="alert">
		<input type="hidden" value="<?php echo $gweid; ?>" id="wechatgweid" name="wechatgweid">
		<input type="hidden" value="<?php echo $wechatweid; ?>" id="wechatweid" name="wechatweid">
		<input type="hidden" value="<?php echo $id; ?>" id="wechatwid" name="wechatwid">
		<table width="505" height="100" border="0" cellpadding="10px" style="margin-left: 3%; margin-top:0px;" id="table2">
		<tr>
			<td height="50px;" width="210">微信昵称: </td>
			<td><input type="text" value="<?php echo $wechatnikename; ?>" class="form-control" id="user_wechatname<?php echo $wechatweid;?>" name="user_wechatname" <?php if($wechattype == "pub_sub" || $wechattype == "pub_svc") { echo 'readonly="true"';}?>></td>
		</tr>
		<tr>
			<td height="50px;" width="210">初始化粉丝数量：</td>
			<td><input type="text" value="<?php echo $wechat_init_fan; ?>" class="form-control" id="fan_init" name="fan_init" readonly="true" /></td>
		</tr>
		
		<!--20140630newaddedbegin-->
		<tr>
			<td height="50px;">认证情况: </td>
			<td>
				<input type="radio" class="rzradio" id="authnokfw" name="wechat_auth<?php echo $id;?>" value="0" <?php if($wechatauth == 0){ echo 'checked="checked"';} if(($wechattype == "pub_sub") || ($wechattype == "pub_svc") || ($wechattype == "pri_sub" && $wechatauth == 1)){ echo 'disabled="disabled"';}?> style="margin-left:0px;"/><span>未认证</span>
				<input type="radio" class="rzradio" id="authokfw" name="wechat_auth<?php echo $id;?>" value="1" <?php if($wechatauth == 1){ echo 'checked="checked"';} if($wechattype == "pub_sub" || $wechattype == "pub_svc"){ echo 'disabled="disabled"';} ?> style="margin-left:15px;"/><span>已认证</span>
			</td>								
		</tr>
		<!--20140630newaddedend-->
		</table>
		
		<?php if($wechattype == "pri_sub"){?>
		<table width="505" height="100" border="0" cellpadding="10px" style="margin-left: 3%; margin-top:0px;display:none;" id="table3" class="<?php echo $id;?>">
		
			<tr>
				<!--<td><label for="URL">URL: </label></td>-->
				<td width="210">微信菜单AppId: </td>
				<td><input type="text" value="" class="form-control" id="menuappId1<?php echo $id;?>" name="menuappId1"></td>
			</tr>
			
			<tr>
				<!--<td><label for="Token">Token: </label></td>-->
				<td width="210">微信菜单AppSecret: </td>
				<td><input type="text" value="" class="form-control" id="menuappSc1<?php echo $id;?>" name="menuappSc1"></td>
			</tr>
		
		</table>
		<?php }?>
		
		<table width="505" height="100" border="0" cellpadding="10px" style="margin-left: 3%; margin-top:0px;" id="table2">
		<tr>
			<!--<td><label for="user_email">E-mail: </label></td>-->
			<td height="50px;"  width="210">微信号类别: </td>
			<td>
				<select name="user_wechattype" class="form-control" size="1" type="text;margin-left:500px;" id="user_wechattype" value="5" maxlength="20" readonly="true">
					<option value="pri_sub" <?php if($wechattype == "pri_sub") { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >个人微信订阅号</option>
					<option value="pri_svc" <?php if($wechattype == "pri_svc") { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >个人微信服务号</option>
					<option value="pub_sub" <?php if(($wechattype == "pub_sub") && ($wechatauth == 0)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信未认证订阅号</option>
					<option value="pub_sub" <?php if(($wechattype == "pub_sub") && ($wechatauth == 1)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信认证订阅号</option>
					<option value="pub_svc" <?php if(($wechattype == "pub_svc") && ($wechatauth == 0)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信未认证服务号</option>
					<option value="pub_svc" <?php if(($wechattype == "pub_svc") && ($wechatauth == 1)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信认证服务号</option>
				</select>
			</td>
		</tr>
		<?php if(($wechattype == "pub_sub")||($wechattype == "pub_svc")){?>
		<tr>
			<!--<td><label for="URL">URL: </label></td>-->
			<td height="50px;"  width="210">验证码: </td>
			<td><input type="text" value="<?php echo $vericode; ?>" class="form-control" id="user_vericode<?php echo $wechatweid;?>" name="user_vericode"></td>
		</tr>
		
		<tr>
			<!--<td><label for="URL">URL: </label></td>-->
			<td height="50px;" width="210">验证码是否显示在用户列表中: </td>
			<td><input type="radio" name="vericodeopen<?php echo $id;?>" value="1"  <?php if($flgopen == 1) echo 'checked="checked"';?>>是 <input type="radio" name="vericodeopen<?php echo $id;?>" value="0"  <?php if($flgopen == 0) echo 'checked="checked"';?> style="margin-left:25px;">否 </td>
		</tr>
		<tr>
			<!--<td><label for="name">请输入退出关注商家关键词: </label></td>-->
			<td height="50px;"  width="210">请输入退出关注商家关键词: </td>
			<td>
				<input type="text" class="form-control" id="busexit<?php echo $wechatweid;?>" name="busexit" style="width:295px; margin-left:0px;" value="<?php echo $busexit; ?>"/>
			
			</td>
		</tr>								
		<tr>
			<!--<td width="225"><input type="radio" name="exireply" value="1"><label for="name">自定义文本回复内容: </label></td>-->
			<td height="50px;"  width="210">请填写退出商家时的回复信息: </td>
			<td>
			<textarea id="exireply_content<?php echo $wechatweid;?>" name="exireply_content"  class="form-control" style="width:295px;height:80px;margin-left:0px;"><?php echo $exireply_content; ?></textarea>
			</td>
		</tr>
		<?php }?>
		
		<tr>
			<!--<td><label for="URL">URL: </label></td>-->
			<td height="50px;"  width="210">URL: </td>
			<td><input type="text" value="<?php echo $url; ?>" class="form-control" id="URL" name="URL"  readonly="true"></td>
		</tr>
		
		
		<tr>
			<!--<td><label for="Token">Token: </label></td>-->
			<td height="50px;"  width="210">Token: </td>
			<td><input type="text" value="<?php echo $token; ?>" class="form-control" id="Token" name="Token" readonly="true"></td>
		</tr>
		
		<!--功能开启即显示第三方客服设置-->
		<?php if( $funcDisplay['wechatcuservice'] && $selCheck['wechatcuservice']){ ?>
		
		<tr>
			<td height="50px;"  width="210">第三方客服url设置: </td>
			<td><input type="text" style="float:left;width:190px" value="<?php echo empty($cuservicethirdurl)?"":$cuservicethirdurl; ?>" class="form-control" id="cuservicethird_url<?php echo $wechatweid;?>" name="cuservicethird_url"  <?php if($gweid!=$disgweid){ ?> readonly="true" <?php } ?>>&(?)token=***</td>
		</tr>
		
		<tr>
			<td height="50px;"  width="210"></td>
			<td>
				<p style="font-size:11px;line-height:20px">范例：若输入http://ip:port/ocsfront/ocsfront/html5/webChat.jsp?channel=xxx, 则系统将生成:http://ip:port/ocsfront/ocsfront/html5/webChat.jsp?channel=xxx&token=xxxx</p>
				<p style="font-size:11px;line-height:20px">注：url必须以http或https开头</p>
			</td>
		</tr>
		
		<tr>
			<td height="50px;"  width="210">第三方客服url: </td>
			<td><input type="text" value="<?php echo $cuserviceurl; ?>" class="form-control" id="cuserviceurl<?php echo $wechatweid;?>" name="cuserviceurl" readonly="true"></td>
		</tr>
		
		<tr>
			<td height="50px;"  width="210"></td>
			<td>
				<input type="button" class="btn btn-success btn-sm" onclick="ExportThirdServiceExcel('<?php echo $disgweid ?>')" value="第三方客服用户手机统计下载" />
			</td>
		</tr>
		
		<?php }?>
		<!--功能开启即显示第三方客服设置END-->
		
		<?php if(($wechattype == "pri_sub" && $wechatauth == 1)||($wechattype == "pri_svc")){?>
		<tr>
			<!--<td><label for="URL">URL: </label></td>-->
			<td height="50px;"  width="210">微信菜单AppId: </td>
			<td><input type="text" value="<?php echo $wechatmenuappid; ?>" class="form-control" id="menuappId<?php echo $wechatweid;?>" name="menuappId"></td>
		</tr>
		
		
		<tr>
			<!--<td><label for="Token">Token: </label></td>-->
			<td height="50px;"  width="210">微信菜单AppSecret: </td>
			<td><input type="text" value="<?php echo $wechatmenuappsc; ?>" class="form-control" id="menuappSc<?php echo $wechatweid;?>" name="menuappSc"></td>
		</tr>
		<?php }?>
		
		<!--添加微信公众号名称，在账户管理这块进行修改-->
		<tr>
			<td height="50px;"  width="210">微信站点名称: </td>
			<td><input type="text" value="<?php echo $wechatname; ?>" class="form-control" id="wechatdesp" name="wechatdesp" ></td>
			<td><button style="display:none;" type="submit" class="btn btn-sm btn-warning" name="accountupdate" id="buttondel" value="<?php //echo $id;?>">更新</button></td>
		</tr>
		<tr>
			<td width="210">请上传图片：</td>
			<td>
				<?php if(!empty($wechatimagurl)) { $upload =wp_upload_dir(); $upwechatimagurl=$upload['baseurl'].$wechatimagurl;?>
				<img name="pic" src="<?php echo $upwechatimagurl; ?>"  height='90' width='90'/>
				<?php } else { ?>
				<img name="pic" href="javascript:void(0)"  height='90' width='90'/>
				<?php } ?>
				<a name='picurl' href='javascript:void(0)' <?php if(empty($wechatimagurl)) {?>style="display:none;"<?php }?> >删除图片</a>
				<input type="file" class="form-control" id="file" name="file"/>
				<input name='delimgid' type='hidden' id='delimg_id' value='-1'/>
			</td>
		</tr>
		
		<tr>
			<td> </td>
			
			<td>
				<button type="submit" class="btn btn-sm btn-warning" name="accountupdate" id="buttondel" value="<?php echo $id;?>" style="width:70px;font-size:13px;margin-top:15px;">更新</button>
				<button type="button" style="margin-left:20px;width:70px;font-size:13px;margin-top:15px;" class="btn btn-sm btn-default" name="accountdelete" id="buttondel" onClick="deleWechatAccount('<?php echo $id;?>','<?php echo $userid;?>','<?php echo $gweid;?>','<?php echo $wechatweid;?>')" value="<?php echo $id;?>">删除</button>
			</td>
			<td></td>
		</tr>
		</table>
		</div>
		</form>
	<?php }?>

<?php }} ?>
<script>
	//下载手机统计信息
	function ExportThirdServiceExcel(disgweid){
		window.location.href='<?php echo home_url();?>/module.php?module=webchat&do=exportData&gweid='+disgweid;
	}
//有多张图片上传时，需要的功能
	function getFullPath(file) { //得到图片的完整路径 	
		var url = null ; 	
		if (window.createObjectURL!=undefined) { // basic 	
			url = window.createObjectURL(file) ; 	
		} else if (window.URL!=undefined) { // mozilla(firefox) 	
			url = window.URL.createObjectURL(file) ; 	
		} else if (window.webkitURL!=undefined) { // webkit or chrome 	
			url = window.webkitURL.createObjectURL(file) ; 	
		} 	
		return url ; 	
 	} 	
 	 	
 	$("input[name='file']").change(function(){ 	
		objUrl = getFullPath(this.files[0]); 	
		if (objUrl) {
			$(this).parent().find("img[name='pic']").show();
			$(this).parent().find("img[name='pic']").attr("src", objUrl); 	
			$(this).parent().find('a[name="picurl"]').show();
		} 	
 	}); 	
	$('a[name="picurl"]').click(function(){	  
		$(this).parent().find("img[name='pic']").attr('src',""); 
		$(this).hide();
		$(this).parent().find("input[name='delimgid']").val("");
		$(this).parent().find("input[name='file']").val(""); //清空file input的内容

	});

    function check_all(obj,cName)
	{
		var checkboxs = document.getElementsByName(cName);
		for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;}
	}
    function checkinput()
    {
	    var username = document.getElementById('user_nicename').value;
		var contactname = document.getElementById('contact_name').value;
		var displayname = document.getElementById('display_name').value;
		
		<?php if($userid != 1){?>
			var startdate = document.getElementById('startDate').value;
			var enddate = document.getElementById('endDate').value;
		<?php }?>
		//var oldpassword = document.getElementById('user_oldpassword').value;
		var newpassword = document.getElementById('user_newpassword').value;
		var confirmpassword = document.getElementById('user_confirmpassword').value;
		var spacecurrent = document.getElementById('currentspace').value;
		var spaceuse = document.getElementById('spaceused').value;
		<?php if($wechatnikename != ""){?>
		//var nikename = document.getElementById('user_nikename').value;
		<?php }?>
		
		<?php if(($wechattype == 'pub_sub')||($wechattype == 'pub_svc')){?>
		//var vericode = document.getElementById('user_vericode').value;
		<? }?>
		
		var email =  document.getElementById('user_email').value;
	    
        if(username == "")
		{
			alert("用户名不能为空！");
			return false;
		}else if(contactname == "")
		{
			alert("联系人不能为空！");
			return false;
		}
		else if(displayname == "")
		{
			alert("显示昵称不能为空！");
			return false;
		}
		<?php if($wechatnikename != ""){?>
		/* else if(nikename == "")
		{
			alert("微信昵称不能为空！");
			return false;
		} */
		<?php }?>
		<?php if(($wechattype == 'pub_sub')||($wechattype == 'pub_svc')){?>
		/* else if(vericode == "")
		{
			alert("验证码不能为空！");
			return false;
		}	 */	
		<? }?>
		else if(email == "")
		{
			alert("Email地址不能为空！");
			return false;
		} 
		<?php if($userid != 1){?>
		else if (startdate > enddate)
		{
			alert("结束时间必须晚于开始时间！");
			return false;
		}
		<? }?>
		else if(spacecurrent < spaceuse)
		{
		    alert("当前空间大小必须大于已用空间大小:"+spaceuse+"M");
			return false;
		}
		else
		{
		   var myreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
			if(!myreg.test(email))
			{
				alert("您的邮箱格式不正确，请重新输入！");
				return false;
			}	
			else
			{
				if(newpassword!="")
				{
				   if(newpassword.length<6)
					{
						alert("新密码的长度不能少于6位字符");
						return false;
					}
					else
					{
					   if(newpassword != confirmpassword)
					    {
						   alert("两次输入的密码不一致，请重新输入");
						   return false;
						}
						else
						{
						   document.getElementById('useredit').submit();
						   return true;
						}
					}
				}
				else 
				{
					if(confirmpassword == "")
					{
					    document.getElementById('useredit').submit();
						return true;
					}
					else
					{
					    alert("两次输入的密码不一致，请重新输入");
						return false;
					}
				}	
			}
			
			
		}
    }
	
	//2014-07-13新增修改，判断是否为空
	function checknull(weid,wechattype,wid,wechatauth)
	{
		//alert(weid);只有weid才可以唯一区分不同的公众号，有可能会添加同一个公众号多次
		//alert(wechattype);
		
		var wechatname = document.getElementById("user_wechatname"+weid).value;
		//2014-07-13新增修改，判断微信昵称、验证码不能为空
	   
		//如果是公用的微信号才需要判断该字段是否为空
		if((wechattype == 'pub_sub')||(wechattype == 'pub_svc'))
		{
			var vericode = document.getElementById('user_vericode'+weid).value;
			var busexit = document.getElementById('busexit'+weid).value;
			var exireply_content = document.getElementById('exireply_content'+weid).value;
			//alert(vericode);
		}
		
		if(wechatname == "")
		{
			alert("微信昵称不能为空");
			return false;
		}
		else
		{
		
			if((wechattype == 'pub_sub')||(wechattype == 'pub_svc'))
			{
				if(vericode == "")
				{
					alert("验证码不能为空");
					return false;
				}else if(busexit == "")
				{
					alert("商家退出码不能为空");
					return false;
				}else if(exireply_content == "")
				{
					alert("商家退出内容不能为空");
					return false;
				}
				else
				{
					return true;
				}
			}
			else
			{
			    //如果menuappId和menuappSc全为空，则表示不会有自定义菜单的功能
				if((wechattype == 'pri_sub' && wechatauth == '1') || wechattype == 'pri_svc')
				{
					var menuappid = document.getElementById('menuappId'+weid).value;
					var menuappsc = document.getElementById('menuappSc'+weid).value;
					if(!((menuappid!='')&&(menuappsc!='')))
					{
						alert("您没有输入微信菜单appid和微信菜单appsc，将没有自定义菜单这个功能");
					}
				}
				if(wechattype == 'pri_sub' && wechatauth == '0')
				{
				    //判断点击已认证后的menuappid和menuappsc是否显示出来
					if($("."+wid).is(":visible"))
					{	
						var menuappid1 = document.getElementById('menuappId1'+wid).value;
						var menuappsc1 = document.getElementById('menuappSc1'+wid).value;
						if(!((menuappid1!='')&&(menuappsc1!='')))
						{
							alert("您没有输入微信菜单appid和微信菜单appsc，将没有自定义菜单这个功能");
						}
					}
				}
				return true;
			}
		}
	
	}
	
	var xmlHttp;
	function createXMLHttpRequest(){
	if(window.ActiveXObject)
	 xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
	else if(window.XMLHttpRequest)
	 xmlHttp = new XMLHttpRequest();
	}

	function deleWechatAccount(id,userid,gweid,weid){	   
	  
		if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			//if(confirm('确定删除？')) {
			//  xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/account/wechataccountdelete.php?beIframe&wid="+id+"&userid="+userid+"&gweid="+gweid+"&weid="+weid,true);
			//2014-07-16新增修改
			xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/init/wechataccountdelete.php?beIframe&wid="+id+"&userid="+userid+"&gweid="+gweid+"&weid="+weid,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				{
					alert("删除成功");
					window.location.reload();
				}
			}
			
			xmlHttp.send(null);
		}
	}

	function selectgroup(s)
	{
		var groupid = s[s.selectedIndex].value;
		//如果不是默认分组,判断当前分组是否已经存在管理员
		if(groupid != 0){  
			$(".superadminselect").css("display","");
			<?php foreach ($grouparray as $key=>$value) {?>
				if(groupid == <?php echo $key;?>){
					var val = <?php echo $value;?>;
					if(val == 0){
						$("#roleselect").attr("disabled", "");
						$("#havegadmin").css("display", "none");
					}
					else {
						$("#roleselect").val("0");
						$("#roleselect").attr("disabled", "disabled");
						$("#havegadmin").css("display", "");
					}
				}
			<?php } ?>
		}else{
			$(".superadminselect").css("display","none");
		}
	}

	//2014-07-10新增修改，判断微学校或者微预约是否被选中，如果有任何一个选中，会员管理都需选中
	//如果会员管理取消选中，微学校和微预约都会是未选中状态
	$(function(){ 
	
	     //document.getElementById('table3').style.display="none";
		 
	     $("#wechatschool").change(function() {
		     
			 if(($("#wechatschool").attr("checked")==true) && ($("#wechatvip").attr("checked")==false) )
			 {
			     $("#wechatvip").attr("checked",'checked');
				 alert("选中微学校,会员管理也需要被选中");
			 }
		 });
		  $("#wechatvip").change(function() {
		     
			 if(($("#wechatvip").attr("checked")==false) && $("#wechatschool").attr("checked")==true)
			 {
				$("#wechatschool").attr("checked",false);
				alert("取消会员管理,微学校需要取消选中");
			 }
		 });
		 
		 //2014-07-10新增修改,对于未认证的个人订阅号以及公共订阅号点击已认证，出现menuappid和menuappsc
		 
		 $(".rzradio").click(function(){   
		   
			var  val;
			var seltable = "";  
			if($(this).attr("checked")){              
				val = $(this).attr("value");
				valattri = $(this).attr("name");				
			}
			
			seltable = valattri.substr(11,valattri.length - 1);
			//alert(seltable);
			if(val == 1)
			{
			   $("."+seltable).show();
			}
			else
			{
				//$("#table3").css("display","none");
				$("."+seltable).css("display","none"); 						
			}
		});
		
	
		
		<?php if($groupid == 0){?>
			$(".superadminselect").css("display","none");
		<?php }?>
		
	});
</script>