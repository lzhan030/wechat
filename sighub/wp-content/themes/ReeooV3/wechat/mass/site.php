<?php

defined('IN_IA') or exit('Access Denied');
require_once 'wp-content/themes/ReeooV3/wechat/mass/template/web/sdk.php';
class MassModuleSite extends ModuleSite {

	public $WECHAT_RESPONSE = array('0' =>'创建成功',
			'40001' => 'AppId和AppSecret有错误',
			'40002' => '不合法的凭证类型',
			'40003' => '不合法的OpenID',
			'40004' =>'不合法的媒体文件类型',
			'40005' =>'不合法的文件类型',
			'40006' =>'不合法的文件大小',
			'40007' =>'不合法的媒体文件id',
			'40008' =>'不合法的消息类型',
			'40009' =>'不合法的图片文件大小',
			'40010' =>'不合法的语音文件大小',
			'40011' =>'不合法的视频文件大小',
			'40012' =>'不合法的缩略图文件大小',
			'40013' =>'不合法的APPID',
			'40014' =>'不合法的access_token',
			'40015' =>'不合法的菜单类型',
			'40016' =>'不合法的按钮个数',
			'40017' =>'不合法的按钮个数',
			'40018' =>'不合法的按钮名字长度',
			'40019' =>'不合法的按钮KEY长度',
			'40020' =>'不合法的按钮URL长度',
			'40021' =>'不合法的菜单版本号',
			'40022' =>'不合法的子菜单级数',
			'40023' =>'不合法的子菜单按钮个数',
			'40024' =>'不合法的子菜单按钮类型',
			'40025' =>'不合法的子菜单按钮名字长度',
			'40026' =>'不合法的子菜单按钮KEY长度',
			'40027' =>'不合法的子菜单按钮URL长度',
			'40028' =>'不合法的自定义菜单使用用户',
			'40029' =>'不合法的oauth_code',
			'40030' =>'不合法的refresh_token',
			'40031' =>'不合法的openid列表',
			'40032' =>'不合法的openid列表长度',
			'40033' =>'不合法的请求字符,不能包含\uxxxx格式的字符',
			'40035' =>'不合法的参数',
			'40038' =>'不合法的请求格式',
			'40039' =>'不合法的URL长度',
			'40050' =>'不合法的分组id',
			'40051' =>'分组名字不合法',
			'41001' =>'缺少access_token参数',
			'41002' =>'缺少appid参数',
			'41003' =>'缺少refresh_token参数',
			'41004' =>'缺少secret参数',
			'41005' =>'缺少多媒体文件数据',
			'41006' =>'缺少media_id参数',
			'41007' =>'缺少子菜单数据',
			'41008' =>'缺少oauth code',
			'41009' =>'缺少openid',
			'42001' =>'access_token超时',
			'42002' =>'refresh_token超时',
			'42003' =>'oauth_code超时',
			'43001' =>'需要GET请求',
			'43002' =>'需要POST请求',
			'43003' =>'需要HTTPS请求',
			'43004' =>'需要接收者关注',
			'43005' =>'需要好友关系',
			'44001' =>'多媒体文件为空',
			'44002' =>'POST的数据包为空',
			'44003' =>'图文消息内容为空',
			'44004' =>'文本消息内容为空',
			'45001' =>'多媒体文件大小超过限制',
			'45002' =>'消息内容超过限制',
			'45003' =>'标题字段超过限制',
			'45004' =>'描述字段超过限制',
			'45005' =>'链接字段超过限制',
			'45006' =>'图片链接字段超过限制',
			'45007' =>'语音播放时间超过限制',
			'45008' =>'图文消息超过限制',
			'45009' =>'接口调用超过限制',
			'45010' =>'创建菜单个数超过限制',
			'45015' =>'回复时间超过限制',
			'45016' =>'系统分组，不允许修改',
			'45017' =>'分组名字过长',
			'45018' =>'分组数量超过上限',
			'46001' =>'不存在媒体数据',
			'46002' =>'不存在的菜单版本',
			'46003' =>'不存在的菜单数据',
			'46004' =>'不存在的用户',
			'47001' =>'解析JSON/XML内容错误',
			'48001' =>'api功能未授权',
			'50001' =>'	用户未授权该api');
	
	public $SEND_STATE = array(
			'SENDING' => '微信群发中',
			'SEND_SUCCESS' => '微信群发成功',
			'send success' => '微信群发成功',
			'send fail' => '微信群发失败',
			'err(10001)' => '内容涉嫌广告,微信群发失败',
			'err(20001)' => '内容涉嫌政治,微信群发失败',
			'err(20004)' => '内容涉嫌社会,微信群发失败',
			'err(20002)' => '内容涉嫌色情,微信群发失败',
			'err(20006)' => '内容涉嫌违法犯罪,微信群发失败',
			'err(20008)' => '内容涉嫌欺诈,微信群发失败',
			'err(20013)' => '内容涉嫌版权,微信群发失败',
			'err(22000)' => '内容涉嫌涉嫌互推(互相宣传),微信群发失败',
			'err(21000)' => '内容涉嫌其他,微信群发失败');
	
	//群发管理
	public function doWebMasslist(){	
		global $_W, $wpdb, $current_user;
	    $gweid =  $_SESSION['GWEID'];
		
		//判断是否是分组管理员,分组管理员不需要进行此功能的check
		$groupadmincount = $this->is_superadmin($gweid);
		if($groupadmincount == 0)
			$this->Perdenied($gweid);

		//初始化fromflag
		$fromflag = 0;
		if(isset($_GET['fromflag'])){
			$fromflag = $_GET['fromflag'];
		}else{
			$fromflag = $_POST['fromflag'];	
		}
		 
		$search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		
		$search = array(
			'all' => '',
			'id' => "AND id ='{$search_content}'",
			'mass_name' => "AND mass_name LIKE '%%{$search_content}%%'"
		);
		
	    $sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wechat_mass WHERE gweid=%s {$search[$search_condition]} ",$gweid);
		$total = $wpdb->get_var($sql);
		
	    $pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_mass where gweid=%s {$search[$search_condition]} ORDER BY id DESC Limit {$offset},{$psize}",$gweid);
		$list = $wpdb->get_results($sql,ARRAY_A);
		
        if(isset($_POST['mass_del']) && !empty($_POST['mass_del']) ){							
			$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wechat_mass WHERE id=%s", $_POST['massid']));			
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$hint = array("status"=>"success","message"=>"删除成功");
			}
			echo json_encode($hint);
			exit;	
		}
		
		include $this -> template('masslist');
	}
	
	//群发记录
	public function doWebStatisticslist(){	
		global $_W, $wpdb, $current_user;
	    $gweid =  $_SESSION['GWEID'];
	    
		$massid = $_GET['massid'];
		//判断是否是分组管理员,分组管理员不需要进行此功能的check
		$groupadmincount = $this->is_superadmin($gweid);
		if($groupadmincount == 0)
			$this->Perdenied($gweid);
		
		if(isset($_GET['fromflag'])){
			$fromflag = $_GET['fromflag'];
		}else{
			$fromflag = 0;
		}
		
		$search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		if($search_condition == 'wid'){
			$search_content = trim($_GET['wid_name']);
		}
		$search = array(
			'all' => '',
			'wid' => "AND wid ='{$search_content}'"
		);
		
	    $sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wechat_mass_statistics WHERE massid=%s {$search[$search_condition]} ",$massid);
		$total = $wpdb->get_var($sql);
		
	    $pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_mass_statistics where massid=%s {$search[$search_condition]} ORDER BY id DESC Limit {$offset},{$psize}",$massid);
		$list = $wpdb->get_results($sql,ARRAY_A);
		
		/*获取公众号名称供搜索和显示*/
		$sql=$wpdb -> prepare("SELECT wid,wechat_nikename FROM  {$wpdb->prefix}wechats where wid IN (SELECT distinct wid FROM {$wpdb->prefix}wechat_mass_statistics WHERE massid=%s)",$massid);
		$winfo= $wpdb->get_results($sql,ARRAY_A);
		$winfoarray=array();
		foreach($winfo as $info){
			$winfoarray[$info['wid']]=$info['wechat_nikename'];
		}
		include $this -> template('statisticslist');
	}
	
	//群发编辑
	public function doWebMass(){
		global $wpdb, $current_user;
	    $gweid =  $_SESSION['GWEID'];

	    //判断是否是groupadmin中的群发
		if(isset($_GET['fromflag'])){
			$fromflag = $_GET['fromflag'];
			//获取该分组下的所有公众号，群发功能只有认证订阅号和认证服务号有权限,分组管理员如果存在符合条件的公众号也是可以列出来的
			$getgroupids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_group w left join {$wpdb->prefix}user_group u on w.user_id = u.user_id where w.GWEID=".$gweid,ARRAY_A);
			//obtain the groupid
			foreach ($getgroupids as $getgroupid) {
				$gid = $getgroupid['group_id'];
			}
			//$getgroupaccounts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_usechat w1 left join {$wpdb->prefix}wechats w2 on w1.wid = w2.wid left join {$wpdb->prefix}user_group u on w1.user_id = u.user_id where ((w2.wechat_type='pri_svc') or (w2.wechat_type='pri_sub' and w2.wechat_auth='1')) and w1.WEID != 0 and u.flag !=1 AND u.group_id=".$gid,ARRAY_A);	
			$getgroupaccounts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_usechat w1 left join {$wpdb->prefix}wechats w2 on w1.wid = w2.wid left join {$wpdb->prefix}user_group u on w1.user_id = u.user_id where ((w2.wechat_type='pri_svc' and w2.wechat_auth='1') or (w2.wechat_type='pri_sub' and w2.wechat_auth='1')) and w1.WEID != 0 AND u.group_id=".$gid,ARRAY_A);	
		}else{
			$fromflag = 0;
		}

		$massid=$_REQUEST["massid"];
		$news_item_id=$_GET["news_item_id"];		
		
		
		//新建
		if(!isset($massid)||($massid=='')){
			$massname=$_GET["massname"];
			if ($news_item_id!=null) {
				$massnews = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_material_news where news_item_id=".intval($news_item_id)." order by news_id",ARRAY_A);
				
				//obtain the newsid
				foreach ($massnews as $content) {
					$newsid = $content['news_item_id'];
				}				
			}
			if ($_GET['tab'] == null) {
				$tab_ul=0;
			} else {
				$tab_ul=$_GET['tab'];
			}
			
		
		}else{//编辑
			$mass = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wechat_mass where id=".intval($massid),ARRAY_A);
			//text
			if ($mass['mass_type'] == "0"){
				$flag=0;
				$tab_ul=0;
				$masstext = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wechat_material_text where text_id=".intval($mass['massmesg_id']),ARRAY_A);		
			}else if ($mass['mass_type'] == "1"){//news
				$flag=1; 
				$tab_ul=1;
				$massnews = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_material_news where news_item_id=".intval($mass['massmesg_id'])." order by news_id",ARRAY_A);
				$massmesg_id=$mass['massmesg_id'];
			}
			
			if ($news_item_id!=null) {
				$massnews = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_material_news where news_item_id=".intval($news_item_id)." order by news_id",ARRAY_A);	
			}
			if ($_GET['tab'] != null) {
				$tab_ul=$_GET['tab'];
			}

			//判断是否已经有公众号被选中群发过，并且群发成功
			//获取该分组下的所有公众号,包含分组管理员本身创建的可以群发的公众号
			$gweidarray = array();
			$m = 0;
			$getgroupgweids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_group w left join {$wpdb->prefix}user_group u on w.user_id = u.user_id where w.GWEID=".$gweid,ARRAY_A);
			//obtain the groupid
			foreach ($getgroupgweids as $getgroupid) {
				$gid = $getgroupid['group_id'];
			}
			//分组管理员中的公众号如果存在符合条件的公众号，则也可以列出来
			//$getgroupwids = $wpdb->get_results("SELECT w1.wid, w1.GWEID FROM {$wpdb->prefix}wechat_usechat w1 left join {$wpdb->prefix}wechats w2 on w1.wid = w2.wid left join {$wpdb->prefix}user_group u on w1.user_id = u.user_id where u.flag !=1 AND u.group_id=".$gid,ARRAY_A);
			$getgroupwids = $wpdb->get_results("SELECT w1.wid, w1.GWEID FROM {$wpdb->prefix}wechat_usechat w1 left join {$wpdb->prefix}wechats w2 on w1.wid = w2.wid left join {$wpdb->prefix}user_group u on w1.user_id = u.user_id where w1.WEID != 0 AND u.group_id=".$gid,ARRAY_A);
			foreach ($getgroupwids as $getwid) {
				$wid = $getwid['wid'];
				//判断该wid对应的群发结果是否成功
				$getwidresults = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_mass_statistics w where w.wid=".$wid." order by time desc limit 1",ARRAY_A);
				foreach ($getwidresults as $getwid) {
					$widstatus = $getwid['status'];
				}
				if($widstatus === "send success"){
					$gweidarray[$m] = $getwid['GWEID'];
					$m++;
				}
			}
			//数组中可能会有重复的
			$gweidarray = array_unique($gweidarray);
			$outarray = array();  //array_unique之后的数组有可能key值不是连续的，需要处理下
			$count = 0;
			foreach ($gweidarray as $key=>$value) {
		       $outarray[$count] = $value;
		       $count++ ;
			}
			$gweidarray = $outarray;

		}
		
		//文本保存
		if(isset($_POST['mass_txt']) && !empty($_POST['mass_txt']) ){							
			//先通过unescape解码js传递过来的escape编码后的内容
			$content=stripslashes($this->unescape($_REQUEST['content']));
			$massid=$_GET["massid"];
			
			//新建群发
			if($massid==''){
				//判断是否是分组管理员中的用户
				$groupadminflag = $this->site_issuperadmin($current_user->ID);
				$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
				//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
				$massname=$_POST["massname"];
				
				$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_material_text (text_title,text_content,text_user,GWEID)VALUES (%s,%s, %d, %d)","mass",$content, $currentuser,$gweid));
				$insert_id=$wpdb->insert_id;
				
				$update=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass(mass_type,massmesg_id,mass_name,gweid)VALUES (%s, %s ,%s,%s)",'0', $insert_id,$massname,$gweid));$massid=$wpdb->insert_id;								
			}else{//编辑群发
				$massname=$_POST["massname"];
				$mass = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wechat_mass where id=".intval($massid),ARRAY_A);
			
				if ($mass['mass_type'] == "0"){
					$text_id = $mass['massmesg_id'];
					$update = $wpdb -> update($wpdb->prefix.'wechat_material_text',array('text_content'=>$content),array('text_id'=>intval($text_id)),array("%s"),array("%s"));
					$update = $wpdb -> update($wpdb->prefix.'wechat_mass',array('mass_name'=>$massname),array('id'=>$massid),array("%s"),array("%s"));
				} else if ($mass['mass_type'] == "1"){
					//判断是否是分组管理员中的用户
					$groupadminflag = $this->site_issuperadmin($current_user->ID);
					$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
					//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
					$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_material_text (text_title,text_content,text_user,GWEID)VALUES (%s,%s, %d, %d)","mass",$content, $currentuser,$gweid));
					$insert_id=$wpdb->insert_id;
					$update = $wpdb -> update($wpdb->prefix.'wechat_mass',array('mass_type'=>'0','massmesg_id'=>$insert_id,'mass_name'=>$massname),array('id'=>$massid),array("%s","%s","%s"),array("%s"));
				}
						
			}
			
			
			if($content==null||$content==""||$update===false){
				$hint = array("status"=>"error","message"=>"文本消息保存失败！");
			}else{
				$hint = array("status"=>"success","message"=>"文本消息保存成功！",'massid'=>$massid);
			}
			echo json_encode($hint);
			exit;	
		}
		
		//素材保存
		if(isset($_POST['mass_news']) && !empty($_POST['mass_news']) ){							
			
			
			$news_item_id=$_POST["news_item_id"];
			$massid=$_POST["massid"];
			$massname=$_POST["massname"];
			
			//新建群发
			if($massid==''){
				//判断是否是分组管理员中的用户
				$groupadminflag = $this->site_issuperadmin($current_user->ID);
				$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
				//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
				$update=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass(mass_type,massmesg_id,mass_name,gweid)VALUES (%s, %s ,%s,%s)",'1', $news_item_id,$massname,$gweid));
				$massid=$wpdb->insert_id;	
				
			}else{//编辑群发
			
				$mass = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wechat_mass where id=".intval($massid),ARRAY_A);
				$update = $wpdb -> update($wpdb->prefix.'wechat_mass',array('mass_type'=>'1','massmesg_id'=>$news_item_id,'mass_name'=>$massname),array('id'=>$massid),array("%s","%s","%s"),array("%s"));
			
			}
			if(empty($news_item_id)||$update===false){
				$hint = array("status"=>"error","message"=>"图文消息保存失败！");
			}else{
				$hint = array("status"=>"success","message"=>"图文消息保存成功！",'massid'=>$massid);
			}
			echo json_encode($hint);
			exit;	
		}
		
		
		//删除该群发
		if(isset($_POST['mass_del']) && !empty($_POST['mass_del']) ){							
			$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wechat_mass WHERE id=%s", $_POST['massid']));			
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$hint = array("status"=>"success","message"=>"删除成功");
			}
			echo json_encode($hint);
			exit;	
		}
		
		
		//实现群发
		if(isset($_POST['mass']) && !empty($_POST['mass']) ){
			//@1数据准备
			$allresult='';//用来alert给商家哪些公众号出现问题			
			$mass_array=array();//用来存放access_token,wid,循环群发
			$wechatinfoarray=array();
			$weixin = new WeixinMass();
			$massid=$_POST["massid"];

			
			//@2判断DB中是否有这条群发内容
			$mass = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wechat_mass where id=".intval($massid),ARRAY_A);
			if(empty($mass['id'])){
				$hint = array("status"=>"error","message"=>"发送失败");
				echo json_encode($hint);
				exit;			
			}else{//取出要群发的文本或素材
				if($mass['mass_type']=='0'){
					//获取文本内容
					$content = $wpdb->get_var("SELECT text_content FROM {$wpdb->prefix}wechat_material_text where text_id=".intval($mass['massmesg_id']));
				}
				if($mass['mass_type']=='1'){
					//获取素材内容
					$mynews = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_material_news where news_item_id=".intval($mass['massmesg_id'])." order by news_id",ARRAY_A);
				}
			}
			
			
			//@3从group表获取该号信息，用于判断是否激活号或者独立的号
			$groupinfo = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wechat_group where GWEID=".intval($gweid),ARRAY_A);
			$userid=$groupinfo['user_id'];
			$shared_flag=$groupinfo['shared_flag'];
			//分组管理员对应的共享号
			$adminshare_flag=$groupinfo['adminshare_flag'];
			
			//@@分组管理员对应的选中的公众号，封装app_id和app_secret
			if($adminshare_flag=='1'){
				$i=0;
				//获取选中的公众号,传递过来的是以逗号分隔的
				$selectedaccount = $_POST["selectedaccount"];
				//echo "选中的公众号:".$selectedaccount;
				$accountarray = explode(",", $selectedaccount);
				for($j=0; $j<count($accountarray); $j++){
					//获取app_id和app_secret(DB中记录为认证订阅号或者服务号，需排除分组管理员的虚拟号(是认证订阅号))
					$winfo= $wpdb->get_row( "SELECT u2.wechat_nikename,u2.wid,u2.menu_appId,u2.menu_appSc FROM {$wpdb->prefix}wechat_usechat u1,{$wpdb->prefix}wechats u2 where u1.wid=u2.wid and ((u2.wechat_type='pri_svc') or (u2.wechat_type='pri_sub' and u2.wechat_auth='1')) and u1.WEID !=0 and u1.GWEID='".intval($accountarray[$j])."'" ,ARRAY_A);
					$wechatinfoarray[$i]['app_id']=$winfo['menu_appId'];
					$wechatinfoarray[$i]['app_secret']=$winfo['menu_appSc'];
					$wechatinfoarray[$i]['wechat_nikename']=$winfo['wechat_nikename'];
					$wechatinfoarray[$i]['wid']=$winfo['wid'];
					$i=$i+1;
				}
			}else{

				//@4独立的号,封装app_id和app_secret
				if($shared_flag=='0'){
					//@4.1DB中记录为认证订阅号或者服务号
					$winfo= $wpdb->get_row( "SELECT u2.wechat_nikename,u2.wid,u2.menu_appId,u2.menu_appSc FROM {$wpdb->prefix}wechat_usechat u1,{$wpdb->prefix}wechats u2 where u1.wid=u2.wid and ((u2.wechat_type='pri_svc') or (u2.wechat_type='pri_sub' and u2.wechat_auth='1')) and u1.WEID !=0 and u1.GWEID='".intval($gweid)."'" ,ARRAY_A);
					
					$wechatinfoarray[0]['app_id']=$winfo['menu_appId'];
					$wechatinfoarray[0]['app_secret']=$winfo['menu_appSc'];
					$wechatinfoarray[0]['wechat_nikename']=$winfo['wechat_nikename'];
					$wechatinfoarray[0]['wid']=$winfo['wid'];
				}
				
				//@5激活被共享的号,封装app_id和app_secret
				if($shared_flag=='2'){
					$i=0;
					//@5.1取到所有共享号以及激活号本身
					$myrows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wechat_group where user_id=".intval($userid)." AND WEID != 0 AND (shared_flag = 1 OR shared_flag = 2)" ,ARRAY_A);
					foreach($myrows as $myrow){
						$gweidev=$myrow['GWEID'];
						//获取app_id和app_secret(DB中记录为认证订阅号或者服务号)
						$winfo= $wpdb->get_row( "SELECT u2.wechat_nikename,u2.wid,u2.menu_appId,u2.menu_appSc FROM {$wpdb->prefix}wechat_usechat u1,{$wpdb->prefix}wechats u2 where u1.wid=u2.wid and ((u2.wechat_type='pri_svc') or (u2.wechat_type='pri_sub' and u2.wechat_auth='1')) and u1.WEID !=0 and u1.GWEID='".intval($gweidev)."'" ,ARRAY_A);
						
						$wechatinfoarray[$i]['app_id']=$winfo['menu_appId'];
						$wechatinfoarray[$i]['app_secret']=$winfo['menu_appSc'];
						$wechatinfoarray[$i]['wechat_nikename']=$winfo['wechat_nikename'];
						$wechatinfoarray[$i]['wid']=$winfo['wid'];
						$i=$i+1;
					}
				}
			}

			//@6封装access_token
			foreach($wechatinfoarray as $k=>$val){
				$i=0;
				//@微信接口调用->获取access_token
				$result=$weixin->re_Token($val['app_id'],$val['app_secret']);
				if((in_array($this ->WECHAT_RESPONSE[$result['errcode']],$this ->WECHAT_RESPONSE))&&($result['errcode']!=0)){//微信识别的错误
					$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,sendstatus,wid,time)VALUES (%s, %s,%s,%s)",$massid,$this ->WECHAT_RESPONSE[$result['errcode']],$val['wid'],time()));
					$allresult=$allresult."公众号'".$val['wechat_nikename']."'".$this ->WECHAT_RESPONSE[$result['errcode']].",群发失败;  ";
				}else if(empty($result) || empty($result['access_token'])){
					$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,sendstatus,wid,time)VALUES (%s, %s,%s,%s)",$massid,"access_token获取失败,群发失败",$val['wid'],time()));
					$allresult=$allresult."公众号'".$val['wechat_nikename']."'access_token获取失败,群发失败;  ";
				}else{
					$mass_array[$i]['access_token']=$result['access_token'];
					$mass_array[$i]['wid']=$val['wid'];
					$mass_array[$i]['wechat_nikename']=$val['wechat_nikename'];
					$i=$i+1;
				}
			}
			//测试数据
			/*$mass_array[0]['access_token']="Omnu0lUv16KCBMski1-k3QTkruYQ0vLKFnpspGZW3196JweQtnOaKtsF0xTUCYWukUeJkp0kJ7oTmdQWvL3yh8oOFk9awukRXovSoI_aZzI";
			$mass_array[0]['wid']="1201";
			$mass_array[0]['wechat_nikename']="群发服务";*/
			
			
			//@7群发
			foreach($mass_array as $k=>$val){
				$userlistopenid=array();
				
				//@微信接口调用->获取openid列表
				$userlist=$weixin->userget($val['access_token'],"");
				if((in_array($this ->WECHAT_RESPONSE[$userlist['errcode']],$this ->WECHAT_RESPONSE))&&($userlist['errcode']!=0)){//微信识别的错误
					$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,sendstatus,wid,time)VALUES (%s, %s,%s,%s)",$massid,$this ->WECHAT_RESPONSE[$userlist['errcode']].",获取关注者列表失败",$val['wid'],time()));
					$allresult=$allresult."公众号'".$val['wechat_nikename']."'".$this ->WECHAT_RESPONSE[$userlist['errcode']].",获取关注者列表失败,群发失败;  ";
				}else if(empty($userlist) || empty($userlist['total'])){
					$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,sendstatus,wid,time)VALUES (%s, %s,%s,%s)",$massid,"获取关注者列表失败,群发失败",$val['wid'],time()));
					$allresult=$allresult."公众号'".$val['wechat_nikename']."',获取关注者列表失败,群发失败;  ";
				}else{
					$userlistopenid=$userlist['data']['openid'];
					//判断关注者是否超过10000，如果超过则继续拉取关注者列表
					if($userlist['total']>=10000){
						$next_openid=$userlist['next_openid'];
						for($j=0;$j<$userlist['total']/10000;$j++){
							if(!empty($next_openid)){
								//@微信接口调用->获取大于10000后的openid列表
								$userlistcontinue=$weixin->userget($val['access_token'],$next_openid);
								$next_openid=$userlistcontinue['next_openid'];
								$userlistopenid=array_merge ($userlistopenid,$userlistcontinue['data']['openid']);
							}
						}
					}
				
				//if(true){
					//群发文本
					if($mass['mass_type']=='0'){
					
						//文本群发
						$data=array();
						$data['touser']=$userlistopenid; //array('OPENID1','OPENID2')
						//$data['touser']='oMjb0sidWlBY5ViGaHtwgiWpPrAc';
						$data['msgtype']="text";
						
						//过滤HTML
						$content = htmlspecialchars_decode($content);
						$content = str_replace(array('<br>', '&nbsp;', "<p>\n\t", "</p>\n"), array("\n", ' ','',''), $content);
						$content = str_replace(array("<p>\n", "</p>"), array('',''), $content);
						$content = str_replace(array("<p>", "</p>"), array('',''), $content);
						$content = strip_tags($content, '<a>');
						
						$data['text']=array("content"=>$content);
						//@微信接口调用->群发
						//$result=$weixin->mass_preview($val['access_token'],$this->encode_json($data));
						$result=$weixin->mass($val['access_token'],$this->encode_json($data));
						if((in_array($this ->WECHAT_RESPONSE[$result['errcode']],$this ->WECHAT_RESPONSE))&&($result['errcode']!=0)){
							$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,sendstatus,wid,time)VALUES (%s, %s,%s,%s)",$massid,$this ->WECHAT_RESPONSE[$result['errcode']],$val['wid'],time()));
							$allresult=$allresult."公众号'".$val['wechat_nikename']."'".$this ->WECHAT_RESPONSE[$result['errcode']].",群发失败;  ";
						}else if(empty($result)||empty($result['msg_id'])){
							$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,sendstatus,wid,time)VALUES (%s, %s,%s,%s)",$massid,"群发失败",$val['wid'],time()));
							$allresult=$allresult."公众号'".$val['wechat_nikename']."',群发失败;  ";							
						}else{
							$msg_id=$result['msg_id'];//2351428645
							$msg_id_post=array('msg_id'=>$msg_id);
							//@微信接口调用->群发成功后获取到msg_id查询发送结果
							$statusresult=$weixin->mass_status($val['access_token'],json_encode($msg_id_post));
							if(!empty($statusresult) && !empty($statusresult['msg_id'])){
								$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,msgid,sendstatus,status,wid,time)VALUES (%s,%s,%s,%s,%s,%s)",$massid,$statusresult['msg_id'],"成功",$statusresult['msg_status'],$val['wid'],time()));
							}else{
								$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,msgid,sendstatus,status,wid,time)VALUES (%s,%s,%s,%s,%s,%s)",$massid,$result['msg_id'],"成功","微信处理中",$val['wid'],time()));
							}
						}
					}
				
				
					//群发图文
					if($mass['mass_type']=='1'){
						$massnews=array();
						//循环上传图片素材到微信并获取mediaid,重新组装素材
						$i=0;
						$upload =wp_upload_dir();
						foreach($mynews as $material){
							
							//@微信接口调用->上传缩略图
							$result=$weixin->upload_thumb($val['access_token'],$upload['basedir'].$material['news_item_picurl']);
							if((in_array($this ->WECHAT_RESPONSE[$result['errcode']],$this ->WECHAT_RESPONSE))&&($result['errcode']!=0)){
								$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,sendstatus,wid,time)VALUES (%s, %s,%s,%s)",$massid,$this ->WECHAT_RESPONSE[$result['errcode']].",图片上传失败",$val['wid'],time()));
								$allresult=$allresult."公众号'".$val['wechat_nikename']."'".$this ->WECHAT_RESPONSE[$result['errcode']].",图片上传失败;  ";
							}else if(empty($result) ||empty($result['media_id'])){//????media_id
								$allresult=$allresult."公众号'".$val['wechat_nikename']."'图片上传失败;  ";							
							}else{
								$tmp = stristr($material['news_item_url'],"http");
								if(($tmp===false)&&(!empty($material['news_item_url']))){
									$newsitemurl=home_url().$material['news_item_url'];
								}else{				
									$newsitemurl=$material['news_item_url'];
								}
								//$description=strip_tags(str_replace(" ","&nbsp;",$material['news_item_description']));
								$description=str_replace('\"', '"', $material['news_item_description']);
								if(count($mynews)==1){
									$massnews[]=array('thumb_media_id'=>$result['media_id'],'title'=>$material['news_item_title'],'content_source_url'=>$newsitemurl,'digest'=>$material['news_item_abstract'],'content'=>$description);//???必填//????media_id
								}else{
									$massnews[]=array('thumb_media_id'=>$result['media_id'],'title'=>$material['news_item_title'],'content_source_url'=>$newsitemurl,'content'=>$description);//???必填//????media_id
								}
								
							}
						}
						
						//测试数据
						/*$massnews[]=array('thumb_media_id'=>'w6f4PDDXX5f8VirKO1ccNJphZJqJTZoS8ZwQ8M7oXIsytcYTslj7RpQNQ69vscdV','title'=>'群发测试','content_source_url'=>'http://www.baidu.com','content'=>"测试内容");//????media_id
						$massnews[]=array('thumb_media_id'=>'w6f4PDDXX5f8VirKO1ccNJphZJqJTZoS8ZwQ8M7oXIsytcYTslj7RpQNQ69vscdV','title'=>'群发','content_source_url'=>'','content'=>"正确与否");//????media_id*/
						
						$data=array();
						$data['articles']=$massnews;
						//@微信接口调用->上传图文获取mediaid
						$result=$weixin->upload_news($val['access_token'],$this->encode_json($data));					
						if((in_array($this ->WECHAT_RESPONSE[$result['errcode']],$this ->WECHAT_RESPONSE))&&($result['errcode']!=0)){
							$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,sendstatus,wid,time)VALUES (%s, %s,%s,%s)",$massid,$this ->WECHAT_RESPONSE[$result['errcode']].",上传图文错误",$val['wid'],time()));
							$allresult=$allresult."公众号'".$val['wechat_nikename']."'".$this ->WECHAT_RESPONSE[$result['errcode']].",上传图文错误;  ";
						}else if(empty($result) ||empty($result['media_id'])){
							$allresult=$allresult."公众号'".$val['wechat_nikename']."'上传图文错误;  ";							
						}else{
							$media_id=$result['media_id'];	
							//群发图文
							$data=array();
							$data['touser']=$userlistopenid;
							//$data['touser']='oMjb0sidWlBY5ViGaHtwgiWpPrAc';
							$data['msgtype']="mpnews";
							$data['mpnews']=array("media_id"=>$media_id);
							
							//@微信接口调用->群发
							//$result=$weixin->mass_preview($val['access_token'],json_encode($data));
							$result=$weixin->mass($val['access_token'],json_encode($data));
							if((in_array($this ->WECHAT_RESPONSE[$result['errcode']],$this ->WECHAT_RESPONSE))&&($result['errcode']!=0)){
								$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,sendstatus,wid,time)VALUES (%s, %s,%s,%s)",$massid,$this ->WECHAT_RESPONSE[$result['errcode']],$val['wid'],time()));
								$allresult=$allresult."公众号'".$val['wechat_nikename']."'".$this ->WECHAT_RESPONSE[$result['errcode']].",群发失败; ";
							}else if(empty($result)||empty($result['msg_id'])){
								$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,sendstatus,wid,time)VALUES (%s, %s,%s,%s)",$massid,"群发失败",$val['wid'],time()));
								$allresult=$allresult."公众号'".$val['wechat_nikename']."'群发失败;  ";							
							}else{
								$msg_id=$result['msg_id'];
								$msg_id_post=array('msg_id'=>$msg_id);
								//@微信接口调用->群发成功后获取到msg_id查询发送结果
								$statusresult=$weixin->mass_status($val['access_token'],json_encode($msg_id_post));
								if(!empty($statusresult) && !empty($statusresult['msg_id'])){
									$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,msgid,sendstatus,status,wid,time)VALUES (%s,%s,%s,%s,%s,%s)",$massid,$statusresult['msg_id'],"成功",$statusresult['msg_status'],$val['wid'],time()));
								}else{
									$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_mass_statistics(massid,msgid,sendstatus,status,wid,time)VALUES (%s,%s,%s,%s,%s,%s)",$massid,$result['msg_id'],"成功","微信处理中",$val['wid'],time()));
								}
							}
						}
					}
				}
			}
			
			if(($allresult=='')){//没有错误
				$hint = array("status"=>"success","message"=>"群发成功");
				echo json_encode($hint);
				exit;	
			}else{
				$hint = array("status"=>"error","message"=>$allresult);
				echo json_encode($hint);
				exit;
			}
		}
		
		include $this -> template('mass');
    }
	
	//修改群发名称
	public function doWebUpdateMassname(){		
		global $wpdb;
		$massid=$_GET["massid"];
		$mass_update=$_POST["mass_update"];
		if(!empty($mass_update)){
			$massid=$_POST["massid"];
			$massname=$_POST["massname"];		
			$update=$wpdb->update( $wpdb->prefix.'wechat_mass', array('mass_name'=>$massname),array('id'=>$massid), array('%s'),array('%s'));
			if($update===false){
				$hint = array("status"=>"error","message"=>"更新失败");
			}else{
				$hint = array("status"=>"success","message"=>"更新成功");
			}
			echo json_encode($hint);
			exit;	
		}else{
			$mass = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wechat_mass where id=".intval($massid),ARRAY_A);
			include $this -> template('update_massname_dlg');		
		}
		
	
	}
	
	public function doWebSelectNews(){		
		global $wpdb;
		$gweid =  $_SESSION['GWEID'];
		$massid=$_GET["massid"];
		$selectnewsid=$_GET["selectnewsid"];
		$massname=$_GET["massname"];
		$fromflag = $_GET['fromflag'];  //是否是groupadmin的群发
		
		$sql = $wpdb -> prepare("SELECT COUNT(DISTINCT news_item_id) FROM {$wpdb->prefix}wechat_material_news WHERE GWEID=%s",$gweid);
		$total = $wpdb->get_var($sql);
		
	    $pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		
		$news = $wpdb->get_results("select news_item_id, news_name FROM {$wpdb->prefix}wechat_material_news where GWEID=".$gweid." GROUP BY news_item_id desc Limit {$offset},{$psize}",ARRAY_A);
		
		include $this -> template('mass_news_list_dlg');
	
	}
	
	public function doWebMassmaterial(){	
		global $_W, $wpdb, $current_user;
	    $gweid =  $_SESSION['GWEID'];
	    //判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$user_id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		//$user_id = (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$needle=home_url();
		$massid=$_GET["massid"];
		$fromflag = $_GET['fromflag'];  //是否是groupadmin的群发
		
		if(isset($_POST['massnewsadd']) && !empty($_POST['massnewsadd']) ){							
			//判断是否是分组管理员中的用户
			$groupadminflag = $this->site_issuperadmin($current_user->ID);
			$currentuser =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
			//$currentuser= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
			$upload =wp_upload_dir();
			$itemId_save=$_POST["itemId_save"];
			$itemTitle_save=$_POST["itemTitle_save"];
			$itemDes_save=$_POST["itemDes_save"];
			$itemUrl_save=$_POST["itemUrl_save"];
			$picUrl_save=$_POST["picUrl_save"];
			$newsId_save=$_POST["newsId_save"];
			
			$news_name=$_POST["material_name"];

			$itemIdAry=explode('|',$itemId_save);
			$itemTitleAry=explode('|',$itemTitle_save);
			$itemDesAry=explode('|',$itemDes_save);
			$itemUrlAry=explode('|',$itemUrl_save);
			$picUrlAry=explode('|',$picUrl_save);

			$itemIdCot=count($itemIdAry);
			if($newsId_save==0){
				$maxnid = $wpdb->get_var("SELECT Max(news_item_id) FROM {$wpdb->prefix}wechat_material_news");
				$newsItemId=intval($maxnid)+1;
				for($i=0;$i<$itemIdCot-1;$i++){		
					/*截取后入数据库*/
					$tmp = stristr($picUrlAry[$i],$upload['baseurl']);
					if($tmp===false){
						$insertPicUrl=$picUrlAry[$i];
					}else{
						$str = stristr($picUrlAry[$i], $upload['baseurl']);
						$postion=intval($str)+intval(strlen($upload['baseurl']));
						$insertPicUrl=substr($picUrlAry[$i], $postion);		
					}
					/*如果包含homeurl，则截取后入数据库*/
					$tmp = stristr($itemUrlAry[$i],home_url());
					if($tmp===false){
						$inserturl=$itemUrlAry[$i];
					}else{
						$str = stristr($itemUrlAry[$i], home_url());
						$postion=intval($str)+intval(strlen(home_url()));
						$inserturl=substr($itemUrlAry[$i], $postion);		
					}
					
					$update=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_material_news(news_item_title,news_item_url,news_item_picurl,news_item_description,news_item_id,news_user,news_name,GWEID)VALUES (%s, %s ,%s,%s, %s,%d, %s ,%d)",$itemTitleAry[$i], $inserturl,$insertPicUrl,$itemDesAry[$i],$newsItemId,$currentuser,$news_name,$gweid));
				}
				
			}else{
				$newsList = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_material_news where news_item_id=".intval($newsId_save));

				foreach($newsList as $news){
					$fordel=true;
					for($i=0;$i<$itemIdCot-1;$i++){
						if($itemIdAry[$i]>0){
							if($news->news_id==$itemIdAry[$i]){
								$fordel=false;
							}
												
						}
					}
					if($fordel){
						$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wechat_material_news WHERE news_id=%d", $news->news_id));
					}
				}
				for($i=0;$i<$itemIdCot-1;$i++){
					$tmp = stristr($picUrlAry[$i],$upload['baseurl']);
					if($tmp===false){
						$insertPicUrl=$picUrlAry[$i];
					}else{
						$str = stristr($picUrlAry[$i], $upload['baseurl']);
						$postion=intval($str)+intval(strlen($upload['baseurl']));
						$insertPicUrl=substr($picUrlAry[$i], $postion);		
					}
					/*如果包含homeurl，则截取后入数据库*/
					$tmp = stristr($itemUrlAry[$i],home_url());
					if($tmp===false){
						$inserturl=$itemUrlAry[$i];
					}else{
						$str = stristr($itemUrlAry[$i], home_url());
						$postion=intval($str)+intval(strlen(home_url()));
						$inserturl=substr($itemUrlAry[$i], $postion);		
					}
					if($itemIdAry[$i]<=0||$itemIdAry[$i]==""){
						$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}wechat_material_news(news_item_title,news_item_url,news_item_picurl,news_item_description,news_item_id,news_user,news_name,GWEID)VALUES (%s, %s ,%s, %s, %s, %d, %s ,%d)",$itemTitleAry[$i], $inserturl,$insertPicUrl,$itemDesAry[$i],$newsId_save,$currentuser,$news_name,$gweid));
					}
					$myrows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_material_news where news_id=".intval($itemIdAry[$i]));
					
					
					if(!empty($myrows)){
						if($insertPicUrl==null){
							$update = $wpdb -> update($wpdb->prefix.'wechat_material_news',array('news_item_title'=>$itemTitleAry[$i],'news_item_url'=>$inserturl,'news_item_description'=>$itemDesAry[$i],'news_name'=>$news_name),array('news_id'=>$itemIdAry[$i]),array("%s","%s","%s","%s"),array("%d"));
							
						}else{
							$update = $wpdb -> update($wpdb->prefix.'wechat_material_news',array('news_item_title'=>$itemTitleAry[$i],'news_item_url'=>$inserturl,'news_item_picurl'=>$insertPicUrl,'news_item_description'=>$itemDesAry[$i],'news_name'=>$news_name),array('news_id'=>$itemIdAry[$i]),array("%s","%s","%s","%s","%s"),array("%d"));
							
						}
	
					}		
				}
			}
			
			$hint = array("status"=>"success","message"=>"操作成功");
			echo json_encode($hint);
			exit;	
		}
		
		include $this -> template('mass_material_edit');
	
	
	}
	
	
	public function doWebMassmaterialupload(){	
		require_once 'wp-content/themes/ReeooV3/wechat/mass/template/web/upload.php';
		global $_W, $wpdb;
	    $gweid =  $_SESSION['GWEID'];
		
		$picname = $_FILES['file']['name'];
		$picsize = $_FILES['file']['size'];
		if ($picname != "") {
			if ($picsize > 1024000) {
				echo '图片大小不能超过1M';
				exit;
			}
			$type = strstr($picname, '.');
			if ($type != ".gif" && $type != ".jpg"&& $type != ".png"&& $type != ".bmp") {
				echo '图片格式不对！';
				exit;
			}
			
			$up=new upphoto();
			$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
			$up->get_ph_type($_FILES["file"]["type"]);
			$up->get_ph_size($_FILES["file"]["size"]);
			$up->get_ph_name($_FILES["file"]["name"]);
			$picarray=$up->save();
			if(!empty($picarray)){
				$picUrl=$picarray['path'];
				$serverpath=$picarray['serverpath'];
			}else{
				$picUrl=false;
				$serverpath="";
			}
			if($picUrl!=false){
				$path=substr( $picUrl,strripos($picUrl,'uploads/')+8 );
			}else{
				echo '图片上传错误，可能是空间不足，请检查后重试';
				exit;
			}
		}
		$size = round($picsize/1024,2);
		$upload =wp_upload_dir();
		
		if((empty($picUrl))||(stristr($picUrl,"http")!==false)){
			$echopicurl=$picUrl;
		}else{
			$echopicurl=$upload['baseurl'].$picUrl;
		}
		
		$arr = array(
			'name'=>$picname,
			'pic'=>$echopicurl,
			'size'=>$size,
			'serverpath'=>$serverpath
		);
		
		echo json_encode($arr);
	}
	/*分页设置*/
	function doWebpaginationa_page($tcount, $pindex, $psize = 15, $url = '', $context = array('before' => 5, 'after' => 4),$attach = array(),$remove = array()) {
		$pdata = array(
			'tcount' => 0,
			'tpage' => 0,
			'cindex' => 0,
			'findex' => 0,
			'pindex' => 0,
			'nindex' => 0,
			'lindex' => 0,
			'options' => ''
		);

		$pdata['tcount'] = $tcount;
		$pdata['tpage'] = ceil($tcount / $psize);
		if($pdata['tpage'] <= 1) {
			return '';
		}
		$cindex = $pindex;
		$cindex = min($cindex, $pdata['tpage']);
		$cindex = max($cindex, 1);
		$pdata['cindex'] = $cindex;
		$pdata['findex'] = 1;
		$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
		$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
		$pdata['lindex'] = $pdata['tpage'];
		$GET = $_GET;
		if(!empty($attach))
			$GET = array_merge($GET,$attach);
		if(!empty($remove))
			$GET = array_diff_key($GET,$remove);
		if(in_array('beIframe',$_GET))
		$GET['beIframe'] ='1';
		//var_dump($_GET);
			if($url) {
				$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
				$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
				$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
				$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
			} else {
				$GET['page'] = $pdata['findex'];
				$pdata['faa'] = 'href="' .'?' . http_build_query($GET) . '"';
				$GET['page'] = $pdata['pindex'];
				$pdata['paa'] = 'href="' . '?' . http_build_query($GET) . '"';
				$GET['page'] = $pdata['nindex'];
				$pdata['naa'] = 'href="' . '?' . http_build_query($GET) . '"';
				$GET['page'] = $pdata['lindex'];
				$pdata['laa'] = 'href="' .'?' . http_build_query($GET) . '"';
			}

		$html = '<ul class="pagination pagination-centered">';
		if($pdata['cindex'] > 1) {
			$html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
			$html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
		}
		//页码算法：前5后4，不足10位补齐
		if(!$context['before'] && $context['before'] != 0) {
			$context['before'] = 5;
		}
		if(!$context['after'] && $context['after'] != 0) {
			$context['after'] = 4;
		}

		if($context['after'] != 0 && $context['before'] != 0) {
			$range = array();
			$range['start'] = max(1, $pdata['cindex'] - $context['before']);
			$range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
			if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
				$range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
				$range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
			}
			for ($i = $range['start']; $i <= $range['end']; $i++) {
					if($url) {
						$aa = 'href="?' . str_replace('*', $i, $url) . '"';
					} else {
						$GET['page'] = $i;
						$aa = 'href="?' . http_build_query($GET) . '"';
					}
				$html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
			}
		}

		if($pdata['cindex'] < $pdata['tpage']) {
			$html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
			$html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
		}
		$html .= '</ul>';
		return $html;
	}
	
	public function Perdenied($gweid){
		if(!$this->has_module($gweid)){
			include $this -> template('perdenied');
			exit;
		}
	}
	/*是否开启功能权限*/
	public function has_module($gweid){
		global $_W,$wpdb;
		$result = $wpdb -> get_results($wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_func_info a WHERE NOT EXISTS(SELECT * FROM wp_wechat_initfunc_info b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = %s AND func_flag = 0) LIMIT 0, 100",$gweid),ARRAY_A);
		foreach($result as $initfunc){
			if($selCheck[$initfunc['func_name']] == 0)
				$selCheck[$initfunc['func_name']] = $initfunc['status'];
		}
		if($selCheck['wechatfuncmass']!=1){
			return false;
		}else{
			return true;
		}
	}
	//用于php针对js使用escape函数进行编码后的解码函数
	function unescape($str){ 
		$ret = ''; 
		$len = strlen($str); 
		for ($i = 0; $i < $len; $i++){ 
			if ($str[$i] == '%' && $str[$i+1] == 'u'){ 
				$val = hexdec(substr($str, $i+2, 4)); 
			if ($val < 0x7f) $ret .= chr($val); 
			else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
			else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 
				$i += 5; 
			} 
			else if ($str[$i] == '%'){ 
				$ret .= urldecode(substr($str, $i, 3)); 
				$i += 2; 
			} 
		   else $ret .= $str[$i]; 
		} 
		return $ret; 
	}
	
	function encode_json($str) {  
		//return urldecode(json_encode($this->url_encode($str)));
		//header('Content-type: text/html;charset=utf-8');
		$code = json_encode($str);
		$os=null;
		$os=substr(PHP_OS,0,3);
		if('WIN'==strtoupper($os)){  
            $s_format = 'UCS-2';  
        }else {  
            $s_format = 'UCS-2BE';  
        }  
		
		return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('{$s_format}', 'UTF-8', pack('H4', '\\1'))", $code); 
	}  
	  
	/** 
	 *  
	 */  
	function url_encode($str) {  
		if(is_array($str)) {  
			foreach($str as $key=>$value) {  
				$str[urlencode($key)] = $this->url_encode($value);  
			}  
		} else {  
			$str = urlencode($str);  
		}  
		  
		return $str;  
	}  
	//当前用户有可能是分组管理员下的，如果分组管理员下的切换，需要找到对应的session中的值
	function site_issuperadmin($currentuserid){
	   	global $_W,$wpdb;
		$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$currentuserid);
		if(!empty($getgroupuserids)){
			foreach($getgroupuserids as $getgroupinfo)
			{
			    $usergroupid = $getgroupinfo -> group_id;
			    $usergroupflag = $getgroupinfo -> flag;
			}
		}else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
			$usergroupid = 0;
			$usergroupflag = 0;
		}
		//如果是分组管理员
		if($usergroupid !=0 && $usergroupflag == 1){
			$groupadminflag = 1;
		}else{
			$groupadminflag = 0;
		}

		return $groupadminflag;
	}
	//判断是否是分组管理员
	function is_superadmin($gweid){
	   	global $_W,$wpdb;
		$getgroupadmins = $wpdb->get_results( "SELECT count(*) as acount FROM {$wpdb -> prefix}wechat_group where WEID = 0 and adminshare_flag = 1 and GWEID = ".$gweid);
		
		foreach($getgroupadmins as $getgroupadmin)
		{
		    $groupadminc = $getgroupadmin -> acount;
		}
		
		return $groupadminc;  //如果为1，则表示分组管理员
	}


}

?>