<?php
	$tmp_path = explode ( 'ReeooV3', __FILE__ );
    $template_path=$tmp_path[0];
    require_once $template_path.'ReeooV3/wechat/common/session.php';
	//2014-06-24新增修改
	$GWEID = $_SESSION['GWEID'];
	$weid=$_SESSION['WEID'];
	$wid = $_GET['wid']; //这样首次进入是获取不到的	
	//obtain userId
	global $current_user;
	//当前用户有可能是分组管理员下的
	$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$current_user->ID);
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
	$id = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($usergroupid !=0 && $usergroupflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
	$userId=((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($usergroupid !=0 && $usergroupflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
	$user_login = $current_user->user_login;
	if(empty($_SESSION['GWEID']) && !in_array($_GET['bt'],array('account_selector','wechatAccountManage','globalSetting'))){
		header('Location: '.home_url().'?bt=wechatAccountManage');
		exit;
		}

	//Sara new added
	//判断当前用户添加的是公用的公众号还是个人的公众号
	$wechattypes = $wpdb->get_results( "SELECT w.hash, w.wechat_nikename, w.wechat_type, w.token, u1.vericode, u1.flgopen FROM ".$wpdb->prefix."wechat_usechat u1,".$wpdb->prefix."wechats w WHERE u1.wid = w.wid and u1.user_id = ".$userId." and u1.WEID =".intval($weid));
	
	foreach($wechattypes as $wechattype){
		//显示的url链接
		$userwechattype = $wechattype->wechat_type;
	}
	
	$getcounts = $wpdb->get_results( "SELECT count(*) as widcount FROM ".$wpdb->prefix."wechat_usechat WHERE GWEID = ".$GWEID."  AND user_id = ".$id);
	foreach($getcounts as $getcount)
	{
	    $wcount = $getcount -> widcount;
	}
	
	//2014-07-07如果当前没有添加过公众号，对应都显示不出来
	if($wcount == 0 && !is_super_admin( $user->ID ))
	{
	    $result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wechat_func_info");
		foreach($result as $initfunc){
			$selCheck[$initfunc->func_name] = 0;
		}
	}else{
	  //2014-07-07判断当前所添加的公众号中有没有服务号以及认证的订阅号，只要有其中之一就会显示人工回复和菜单管理
		$menuflag = false;
		$massflag = false;
		$manualflag = false;
		$getwids = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."wechat_usechat WHERE GWEID = ".$GWEID."  AND user_id = ".$id);
		foreach($getwids as $getwid)
		{
			$wids = $getwid->wid;
			$widinfo = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."wechats WHERE wid = ".$wids);
			foreach($widinfo as $getinfo)
			{
				$widtype = $getinfo -> wechat_type;
				$widauth = $getinfo -> wechat_auth;
			}
			if(($widtype == "pri_sub" && $widauth == 1)|| ($widtype == "pub_sub" && $widauth == 1) || $widtype == "pri_svc" || $widtype == "pub_svc")
			{		   
			   $menuflag = true;
				break;
			}
		}
		
		foreach($getwids as $getwid)
		{
			$wids = $getwid->wid;
			$widtype = $wpdb->get_var( "SELECT wechat_type FROM ".$wpdb->prefix."wechats WHERE wid = ".$wids);
			if($widtype == "pri_svc" || $widtype == "pub_svc")
			{		   
			   $manualflag = true;
				break;
			}
		}
		foreach($getwids as $getwid)
		{
			$wids = $getwid->wid;
			$widtype = $wpdb->get_var( "SELECT wechat_type FROM ".$wpdb->prefix."wechats WHERE wid = ".$wids);
			if(($widtype == "pri_sub" && $widauth == 1)|| ($widtype == "pri_svc" && $widauth == 1) )
			{		   
			   $massflag = true;
				break;
			}
		}
		//2014-07-07新增修改,是否选中通过gweid和userid决定
		$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wechat_func_info a WHERE NOT EXISTS(SELECT * FROM ".$wpdb->prefix."wechat_initfunc_info b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$GWEID." AND func_flag = 0) AND EXISTS(SELECT * FROM ".$wpdb->prefix."wechat_initfunc_info b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$id." AND func_flag = 1) LIMIT 0, 100");
		foreach($result as $initfunc){
			if($selCheck[$initfunc->func_name] == 0)
				$selCheck[$initfunc->func_name] = $initfunc->status;
		}
	
	}
	
    $displaynone="display:none";
    $displayblock="display:block";

    $gethgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$userId);

	if(!empty($gethgroupuserids)){
		foreach($gethgroupuserids as $getgroupinfo)
		{
		    $userhgroupid = $getgroupinfo -> group_id;
		    $userhgroupflag = $getgroupinfo -> flag;
		}
		
	}else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
		$userhgroupid = 0;
		$userhgroupflag = 0;
	}	
	//如果是分组管理员，并且选择的是虚拟号，分组管理员也可能添加公众号
	if($userhgroupid !=0 && $userhgroupflag == 1){
		$gethgroupgweids = $wpdb->get_results( "SELECT GWEID FROM {$wpdb -> prefix}wechat_group where user_id = ".$userId." AND WEID = 0");
		foreach($gethgroupgweids as $getgweidinfo)
		{
		    $currentgweid = $getgweidinfo -> GWEID;
		}
		if($currentgweid == $GWEID){
			$ugadminflag = 1;
		}else{
			$ugadminflag = 0;
		}
		
	}else{
		$ugadminflag = 0;
	}

?>
<?php if(!isset($_GET['beIframe'])){ ?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta charset="UTF-8" />
	<title><?php if (is_home () ) { bloginfo('name'); } elseif ( is_category() ) { single_cat_title();
	echo " - "; bloginfo('name'); } elseif (is_single() || is_page() ) { single_post_title(); echo " - "; bloginfo('name'); }
	elseif (is_search() ) { bloginfo('name'); echo "search results:"; echo
	wp_specialchars($s); } else { wp_title('',true); } ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="http://feeds.feedburner.com/reeoo" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<?php 
		if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
		wp_head();
	?>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.11.1.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap-dropdown.js"></script>
	<style type="text/css">
		.menu{border: 1px solid #FFF;padding:5px 10px;}
		.list-title{padding: 0px 10px;border: 1px solid #FFF;}
		.logo{margin-top:10px;}
		.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
		.header_left{float:left;}
		.left_ul{float:left; padding-top:20px;}
		.left_li{float:left; margin-right:10px;}
		.right_ul{float:right;}
		.right_li{float:left;}
		.right_account_li{float:left; margin-right:20px;}
		.right_group_li{float:left;}
		.new_order{float:left; margin-left:20px;}
	</style>
</head>

<body <?php body_class(); ?> style="background-color:#E7E8EB;">
<div class="row">
	<div class="head_box">
		<div class="inner">
			<div class="header_left">
				<h3 class="logo"><img src="<?php bloginfo('template_directory'); ?>/images/orange.png" style="width:360px;" /><!--?php bloginfo( 'name' ); ?--></h3>
				<ul class="left_ul">
					<li class="left_li"><a data-href="<?php echo home_url()?>" href="javascript:current_account();" id="current_account">当前公众号</a>&nbsp&nbsp&nbsp|</li>
					<li class="left_li"><a href="<?php echo home_url()?>?bt=wechatAccountManage">全局设置</a></li>
				</ul>
			</div>
			<div class="account">
				<ul class="right_ul">
				   <?php 
				     //change select to dropdown method
					if(is_super_admin( $current_user -> ID ))
					{
					    $getgroups = $wpdb->get_results( "SELECT ID as groupid, group_name FROM {$wpdb -> prefix}group order by ID ASC" ); 
						if(!empty($_SESSION['GWEID']))
						{
							//secure query method
							$groupidssql = $wpdb -> prepare("SELECT w2.group_id FROM {$wpdb -> prefix}wechat_group w1 left join {$wpdb -> prefix}user_group w2 on w1.user_id = w2.user_id WHERE w1.user_id != 0 AND w1.GWEID = %d", $_SESSION['GWEID']);
							$groupids = $wpdb->get_results($groupidssql);
							foreach($groupids as $sgroup){    
								$sgroupid = $sgroup -> group_id;	
							}
							if(empty($sgroupid))
							{
								$sgroupid = 0;  //如果是未被分过组的用户，它对应的分组id被赋值为默认分组的id
							}
						}
					    ?>
						<li class="right_group_li dropdown" style="margin-right:10px;">
							<!--<div class="btn-group" id="group_list">-->
							<div class="btn-group" id="group_list">
								<!--<button type="button" id="current_wechat_account" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">-->
								<button type="button" id="current_user_group" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
								   
								   <?php
								   if(!empty($_SESSION['GWEID'])){
										foreach($getgroups as $getgroup){    
											$groupid = $getgroup -> groupid;
											$groupname = $getgroup -> group_name;
											if($sgroupid == $groupid) 
												echo "当前分组:".$groupname;   
										}
									}else
									    echo "请选择分组";
									?>
								   <span class="caret"></span>
								</button>
								<ul class="dropdown-menu" id="dropdownlist" role="menu" style="max-height: 550px; overflow-y: scroll;">
									<?php
									foreach($getgroups as $getgroup){    
										$groupid = $getgroup -> groupid;
										$groupname = $getgroup -> group_name;
									
									?>
									<li><a data-groupid="<?php echo $groupid;?>" href="javascript:group_User_header('<?php echo $groupid; ?>');"  ><?php echo $groupname;?></a></li>
									<?php }?>		
								</ul>
							</div>
						</li>
					<?php }else{
						//Sara new added20150414
						$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$current_user -> ID);
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
							$getgroupinfos = $wpdb->get_results( "SELECT group_name FROM {$wpdb -> prefix}group where ID = ".$usergroupid); 
							foreach($getgroupinfos as $getgroupinfo)
							{   
							    $usergroupname = $getgroupinfo -> group_name;
							}

							?>
								<li class="right_group_li dropdown" style="margin-right:10px;">
								当前分组:<?php echo $usergroupname;?>
								</li>
							<?php
						}
					}?>
				  
					<li class="right_account_li dropdown">
						<div class="btn-group" id="account_list">
						  <button type="button" id="current_wechat_account" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
						  
						  <?php
						  $weidq_in_sql = $wpdb->get_var( "SELECT WEID FROM {$wpdb -> prefix}wechat_group where GWEID='{$_SESSION['GWEID']}'".(is_super_admin( $userId )?'':" AND user_id='{$userId}'") );
						  $wechat_nikename = $wpdb->get_var( "SELECT w.wechat_nikename FROM {$wpdb -> prefix}wechat_usechat u1,{$wpdb -> prefix}wechats w WHERE u1.wid = w.wid AND u1.user_id='{$userId}' and u1.user_id= '{$userId}' and u1.WEID ='{$weidq_in_sql}'");
						  if($weidq_in_sql == 0){
						  		$wechat_nikename = "虚拟号";
						  }
						  echo ($wechat_nikename === NULL || $wechat_nikename === false || empty($_SESSION['GWEID'])) ? '请选择公众号' : ('当前公众号: '.$wechat_nikename);
						  ?>
							 <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu" id="dropdownlist" role="menu" style="max-height: 550px; overflow-y: scroll;">
							<?php
							if(is_super_admin( $current_user -> ID ))
							{
							    if(!empty($_SESSION['GWEID']))
								{
									//secure query method
									$groupidssql1 = $wpdb -> prepare("SELECT w2.group_id FROM {$wpdb -> prefix}wechat_group w1 left join {$wpdb -> prefix}user_group w2 on w1.user_id = w2.user_id WHERE w1.user_id != 0 AND w1.GWEID = %d", $_SESSION['GWEID']);
									$groupids = $wpdb->get_results($groupidssql1);
									foreach($groupids as $sgroup){    
										$sgroupid = $sgroup -> group_id;
									}
									
									if(($sgroupid == 0) || (empty($sgroupid)))  //当前处于默认分组，默认分组对应的id为0
									{
										
										$getuserids = $wpdb->get_results( "SELECT distinct w1.ID as user_id, w1.user_login FROM {$wpdb -> prefix}users w1 left join {$wpdb -> prefix}user_group w2 on w1.ID = w2.user_id WHERE w1.user_pass != '' AND (isnull(w2.group_id) OR w2.group_id = 0) order by w1.ID ASC" );
										
									}else{    //取出当前分组中的用户
										$getuserids = $wpdb->get_results( "SELECT distinct w1.ID as user_id, w1.user_login FROM {$wpdb -> prefix}users w1 left join {$wpdb -> prefix}user_group w2 on w1.ID = w2.user_id WHERE w1.user_pass != '' AND w2.group_id = ".$sgroupid." order by w1.ID ASC" );
									}
								}else{
									//get all userids
									$getuserids = $wpdb->get_results( "SELECT ID as user_id, user_login FROM {$wpdb -> prefix}users where user_pass != '' order by ID ASC" );
								}
								foreach($getuserids as $getuserid)
								{
								    $guserid = $getuserid -> user_id;
									$username = $getuserid->user_login;
									?>
										<li><a href="#" data-userid="<?php echo$guserid;?>" ><?php if($_SESSION['GWEID_matched_userid'] == $guserid || ($guserid == $current_user -> ID && empty($_SESSION['GWEID_matched_userid']))) echo '<span style="color: #c7254e;background-color:#E7E8EB;">';?><?php echo $username.'的公众号:';?><?php if($_SESSION['GWEID_matched_userid'] == $guserid || ($guserid == $current_user -> ID && empty($_SESSION['GWEID_matched_userid']))) echo '</span>';?></a></li>
									<?php
									//如果该用户是分组管理员,其weid为0的表示是虚拟号
									$getgroupids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$guserid);
									if(!empty($getgroupids)){
										foreach($getgroupids as $getgroupinfo)
										{
										    $ugroupid = $getgroupinfo -> group_id;
										    $ugroupflag = $getgroupinfo -> flag;
										}
									}else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
										$ugroupid = 0;
										$ugroupflag = 0;
									}
									//如果是分组管理员
									if($ugroupid !=0 && $ugroupflag == 1){
										$wechat_group=$wpdb->get_results( "SELECT * FROM {$wpdb -> prefix}wechat_group where user_id=".$guserid." order by WEID" );
									}else{
										$wechat_group=$wpdb->get_results( "SELECT * FROM {$wpdb -> prefix}wechat_group where user_id=".$guserid." AND WEID != 0 order by WEID" );
									}
									if(empty($wechat_group)){
										?>
											<li><a href="#">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp尚未添加公众号</a></li>
										<?php
									}else{
										foreach($wechat_group as $wchatp){		
											$gweid = $wchatp->GWEID;
											$weid=$wchatp->WEID;
											$shared_flag=$wchatp->shared_flag;
											$adminshare_flag=$wchatp->adminshare_flag;
											//如果是分组管理员，weid有为0的情况
											if($ugroupid !=0 && $ugroupflag == 1){
												if($weid == 0){  //如果虚拟号没需要判断有没有开启共享状态，没有开启需要给出提示
													//if($adminshare_flag == 1){
														//$wechat_nikename = $wpdb->get_var( "SELECT w.wechat_nikename FROM {$wpdb -> prefix}wechat_usechat u1,{$wpdb -> prefix}wechats w WHERE u1.wid = w.wid and u1.WEID =".$weid);
														?>
														<!--<li><a data-gweid="<?php echo $gweid; ?>" href="javascript:gweid_select('<?php echo $gweid; ?>');">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $wechat_nikename; ?></a></li>-->
														<?php
													//}
													$wechat_nikename = $wpdb->get_var( "SELECT w.wechat_nikename FROM {$wpdb -> prefix}wechat_usechat u1,{$wpdb -> prefix}wechats w WHERE u1.wid = w.wid and u1.WEID =".$weid);
													$wechat_nikename = "虚拟号";  //weid为0的一定是虚拟号
													
													?>
													<li><a data-gweid="<?php echo $gweid; ?>" href="javascript:gweid_group_select('<?php echo $gweid; ?>');">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo "虚拟号"; ?></a></li>
													<?php
												}else{
													$wechat_nikename = $wpdb->get_var( "SELECT w.wechat_nikename FROM {$wpdb -> prefix}wechat_usechat u1,{$wpdb -> prefix}wechats w WHERE u1.wid = w.wid and u1.WEID =".$weid);
													?>
													<li><a data-gweid="<?php echo $gweid; ?>" href="javascript:gweid_select('<?php echo $gweid; ?>');">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $wechat_nikename; ?></a></li>
													<?php
												}
											}else{
												if($weid!=0){
													$wechat_nikename = $wpdb->get_var( "SELECT w.wechat_nikename FROM {$wpdb -> prefix}wechat_usechat u1,{$wpdb -> prefix}wechats w WHERE u1.wid = w.wid and u1.WEID =".$weid);
													?>
													<li><a data-gweid="<?php echo $gweid; ?>" href="javascript:gweid_select('<?php echo $gweid; ?>');">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $wechat_nikename; ?></a></li>
													<?php
												}else{
													$account = null;
												}
											}
											
										}
									}
								}
							}else{
								//Sara new added20150414
								//如果当前用户是分组管理员
								$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$current_user -> ID);
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
									//获取该分组下的所有用户
									$getgroupusers = $wpdb->get_results( "SELECT distinct w1.ID as user_id, w1.user_login FROM {$wpdb -> prefix}users w1 left join {$wpdb -> prefix}user_group w2 on w1.ID = w2.user_id WHERE w1.user_pass != '' AND w2.group_id = ".$usergroupid." order by w1.ID ASC" );
									foreach($getgroupusers as $getuserid)
									{
									    $guserid = $getuserid -> user_id;
										$username = $getuserid->user_login;
										?>
											<li><a href="#" data-userid="<?php echo$guserid;?>" ><?php if($_SESSION['GWEID_matched_userid'] == $guserid || ($guserid == $current_user -> ID && empty($_SESSION['GWEID_matched_userid']))) echo '<span style="color: #c7254e;background-color:#E7E8EB;">';?><?php echo $username.'的公众号:';?><?php if($_SESSION['GWEID_matched_userid'] == $guserid || ($guserid == $current_user -> ID && empty($_SESSION['GWEID_matched_userid']))) echo '</span>';?></a></li>
										<?php
										$wechat_ugroup=$wpdb->get_results( "SELECT * FROM {$wpdb -> prefix}wechat_group where user_id=".$guserid." order by WEID" );
										if(empty($wechat_ugroup)){
											?>
												<li><a href="#">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp尚未添加公众号</a></li>
											<?php
										}								
										else{
											foreach($wechat_ugroup as $wchatp){		
												$gweid = $wchatp->GWEID;
												$weid=$wchatp->WEID;
												$shared_flag=$wchatp->shared_flag;
												$wechat_nikename = $wpdb->get_var( "SELECT w.wechat_nikename FROM {$wpdb -> prefix}wechat_usechat u1,{$wpdb -> prefix}wechats w WHERE u1.wid = w.wid and u1.WEID =".$weid);
												if($weid == 0){
													?>
												<li><a data-gweid="<?php echo $gweid; ?>" href="javascript:gweid_groupselect('<?php echo $gweid; ?>','<?php echo $guserid;?>','<?php echo $weid;?>');">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo "虚拟号"; ?></a></li>	
													<?php
												}else{
												?>
												<li><a data-gweid="<?php echo $gweid; ?>" href="javascript:gweid_groupselect('<?php echo $gweid; ?>','<?php echo $guserid;?>','<?php echo $weid;?>');">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $wechat_nikename; ?></a></li>
												<?php	}							
											}
										}
									}
								}else{
									//如果是普通用户
									$wechat_group=$wpdb->get_results( "SELECT * FROM {$wpdb -> prefix}wechat_group where user_id=".$userId." AND WEID != 0 order by WEID" );
									if(empty($wechat_group)){
										?>
											<li><a href="#">您尚未添加公众号</a></li>
										<?php
									}								
									else{
										foreach($wechat_group as $wchatp){		
											$gweid = $wchatp->GWEID;
											$weid=$wchatp->WEID;
											$shared_flag=$wchatp->shared_flag;
											if($weid!=0){
												$wechat_nikename = $wpdb->get_var( "SELECT w.wechat_nikename FROM {$wpdb -> prefix}wechat_usechat u1,{$wpdb -> prefix}wechats w WHERE u1.wid = w.wid and u1.WEID =".$weid);
												?>
												<li><a data-gweid="<?php echo $gweid; ?>" href="javascript:gweid_select('<?php echo $gweid; ?>');"><?php echo $wechat_nikename; ?></a></li>
												<?php
											}else{
												$account = null;
											}
										}
									}
								}	
							}?>
						  </ul>
						</div>
					</li>
					<li class="right_li"><span class="glyphicon glyphicon-user"></span>&nbsp<?php echo $user_login;?>&nbsp&nbsp|&nbsp&nbsp</li>
					<li class="right_li">
						<a href="<?php echo wp_logout_url();?>" style="color:#222;"><span class="glyphicon glyphicon-off"></span>&nbsp&nbsp退出</a>
						<!--<a href="<?php echo site_url( '/login/?action=login' );?>" style="color:#222;"><span class="glyphicon glyphicon-off"></span>&nbsp&nbsp退出</a>-->
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- Alerting panel-->
<div class="alertpanel">
  <div id="newspace" style="display:none;"><a href="<?php echo home_url(); ?>?bt=globalSetting" style="color:#FFF;"><span class='glyphicon glyphicon-star'></span>&nbsp 请申请新空间</a></div>
  <div id="newoder" style="display:none;"><a href="<?php echo home_url(); ?>?bt=wepayorder" style="color:#FFF;"><span class='glyphicon glyphicon-star'></span>&nbsp 微支付新订单</a></div>
  <div id="wenewoder" style="display:none;"><a href="<?php echo home_url(); ?>?bt=weshoppingorder" style="color:#FFF;"><span class='glyphicon glyphicon-star'></span>&nbsp 微商城新订单</a></div>
  <div id="newalarm" style="display:none;"><a href="<?php echo home_url(); ?>?bt=wepayalarm" style="color:#FFF;"><span class='glyphicon glyphicon-star'></span>&nbsp 新告警</a></div>
  <div id="newrights" style="display:none;"><a href="<?php echo home_url(); ?>?bt=wepayrights" style="color:#FFF;"><span class='glyphicon glyphicon-star'></span>&nbsp 新维权</a></div>
</div>

<div class="row container_box" style="width:1200px; margin-left:auto; margin-right:auto; padding:0; margin-top: 36px; margin-bottom:88px;background-color:#FFF;">
<div class="cell_layout">
<div class="sidediv">
<aside id="side">
	<header id="header" style="min-height: 400px;">
		<div class="list-group">
		<nav id="nav"><?php ;//wp_nav_menu( array('menu' => 'header-menu' )); ?>
		<?php
		if(!in_array($_GET['bt'],array('account_selector','wechatAccountManage','globalSetting'))){?>
			<?php if($ugadminflag == 1){?>
				<a class="menu list-group-item"  style="padding-left:35px;" href="<?php echo home_url(); ?>" id="first" rel="首页"><span class="glyphicon glyphicon-home"></span>&nbsp&nbsp首页</a>
				<a class="list-title list-group-item" style="padding-left:35px;border-top: 1px solid #e7e7eb;"><span class="glyphicon glyphicon-th-large"></span>&nbsp&nbsp基础服务</a>
				<section  style="">
					<a class="menu list-group-item" id="wechatmass" href="<?php echo home_url();?>?bt=wechatmassgroupadmin" rel="首页">群发消息</a>
				</section>
				<section  style="">
					<a class="menu list-group-item" id="material" href="<?php echo home_url();?>?bt=materialManage" rel="首页">素材管理</a>
				</section>
					<a class="list-title list-group-item" style="padding-left:35px;border-top: 1px solid #e7e7eb;"><span class="glyphicon glyphicon-list"></span>&nbsp&nbsp高级服务</a>
				<section  style="">
					<a class="menu list-group-item" id="vip" href="<?php echo home_url();?>?bt=vipManage" rel="首页">会员管理</a>
				</section>

			<?php }else{?>
					<a class="menu list-group-item"  style="padding-left:35px;" href="<?php echo home_url(); ?>" id="first" rel="首页"><span class="glyphicon glyphicon-home"></span>&nbsp&nbsp首页</a>
				<a class="list-title list-group-item" style="padding-left:35px;border-top: 1px solid #e7e7eb;"><span class="glyphicon glyphicon-th-large"></span>&nbsp&nbsp基础服务</a>
				<section  style="<?php echo $selCheck['wechatfuncfirstconcern']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="firstconcern" href="<?php echo home_url();?>?bt=firstConcern" rel="首页">首次关注</a>
				</section>
				<section  style="<?php echo $selCheck['wechatfuncnokeywordsreply']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="nokeyword"  href="<?php echo home_url();?>?bt=nokeyWordResponse" rel="首页">无匹配回复</a>
				</section>	
				<section  style="<?php echo $selCheck['wechatfunckeywordsreply']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="keyword" href="<?php echo home_url();?>?bt=keyWordResponse" rel="首页">关键词回复</a>
				</section>
				<section  style="<?php echo $selCheck['wechatfunceditresponse']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="editResponse" href="<?php echo home_url();?>?bt=editResponse" rel="首页">可编程回复</a>
				</section>
				<?php if($massflag){?>
				<section  style="<?php echo $selCheck['wechatfuncmass']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="wechatmass" href="<?php echo home_url();?>?bt=wechatmass" rel="首页">群发消息</a>
				</section>
				<?php } ?>
				<section  style="<?php echo $selCheck['wechatfuncmaterialmanage']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="material" href="<?php echo home_url();?>?bt=materialManage" rel="首页">素材管理</a>
				</section>
				<section  style="<?php echo $selCheck['wechatfuncaccountmanage']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="account" href="<?php echo home_url();?>?bt=accountManage" rel="首页">账户管理</a>
				</section>
				<a class="list-title list-group-item" style="padding-left:35px;border-top: 1px solid #e7e7eb;"><span class="glyphicon glyphicon-list"></span>&nbsp&nbsp高级服务</a>
				<section  style="<?php echo $selCheck['wechatwebsite']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="website" href="<?php echo home_url();?>?bt=webSites" rel="首页">微官网</a>
				</section>
				<section  style="<?php echo $selCheck['wechatvip']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="vip" href="<?php echo home_url();?>?bt=vipManage" rel="首页">会员管理</a>
				</section>
				<?php if($menuflag){?>
				<section  style="<?php echo $selCheck['wechatfuncmenumanage']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="menu" href="<?php echo home_url();?>?bt=menuManage" rel="首页">菜单管理</a>
				</section>
				<?php }?>
				<?php if($manualflag){?>
				<!-- <section  style="<?php echo $selCheck['wechatfuncmanualreply']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="humanresponse"  href="<?php echo home_url();?>?bt=humanResponse" rel="首页">人工回复</a>
				</section> -->
				<?php }?>
				<section  style="<?php echo $selCheck['wechatfuncusermanage']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="usermanage"  href="<?php echo home_url();?>?bt=userManage" rel="首页">微用户管理</a>
				</section>
				<section  style="<?php echo $selCheck['wechatresearch']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="research" href="<?php echo home_url();?>?bt=research" rel="首页">微预约</a>
				</section>
				<section  style="<?php echo $selCheck['wepay']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="wepay" href="<?php echo home_url();?>?bt=wepay" rel="首页">微支付</a>
				</section>
				<section  style="<?php echo $selCheck['weshopping']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="weshopping" href="<?php echo home_url();?>?bt=weshopping" rel="首页">微商城</a>
				</section>
				<a class="list-title list-group-item" style="padding-left:35px;border-top: 1px solid #e7e7eb;"><span class="glyphicon glyphicon-th"></span>&nbsp&nbsp微活动</a>
				<section  style="<?php echo $selCheck['wechatactivity_coupon']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="favorable" href="<?php echo home_url();?>?bt=favorable" rel="首页">优惠劵</a>
				</section>
				<section  style="<?php echo $selCheck['wechatactivity_scratch']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="scratchcard"  href="<?php echo home_url();?>?bt=scratchcard" rel="首页">刮刮卡</a>
				</section>
				<section  style="<?php echo $selCheck['wechatactivity_egg']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="egg"  href="<?php echo home_url();?>?bt=egg" rel="首页">砸蛋</a>
				</section>
				<section  style="<?php echo $selCheck['wechatactivity_wxwall']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="wxwall"  href="<?php echo home_url();?>?bt=wxwall" rel="首页">微信墙</a>
				</section>
				<section  style="<?php echo $selCheck['wechatactivity_redenvelope']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="redenvelope"  href="<?php echo home_url();?>?bt=redenvelope" rel="首页">微红包</a>
				</section>
				<section  style="<?php echo $selCheck['wechatactivity_vote']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="vote"  href="<?php echo home_url();?>?bt=vote" rel="首页">微投票</a>
				</section>
				<section  style="<?php echo $selCheck['wechatactivity_fortunewheel']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="fortunewheel" href="<?php echo home_url();?>?bt=fortunewheel" rel="首页">幸运大转盘</a>
				</section>
				<section  style="<?php echo $selCheck['wechatactivity_toend']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="winner" href="<?php echo home_url();?>?bt=winner" rel="首页">一站到底</a>
				</section>
				<section  style="<?php echo $selCheck['wechatactivity_fortunemachine']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="luckymachine"  href="<?php echo home_url();?>?bt=luckymachine" rel="首页">幸运机</a>
				</section>
				<a class="list-title list-group-item" style="padding-left:35px;border-top: 1px solid #e7e7eb;"><span class="glyphicon glyphicon-link"></span>&nbsp&nbsp微行业</a>
				<section  style="<?php if(!$selCheck['wechatschool']) echo $displaynone; ?>" >
					<a class="menu list-group-item" id="weschool" href="<?php echo home_url();?>?bt=weSchool" rel="首页">微学校</a>
				</section>
				<a class="list-title list-group-item" style="padding-left:35px;border-top: 1px solid #e7e7eb;"><span class="glyphicon glyphicon-cloud"></span>&nbsp&nbsp微统计</a>
				<section  style="<?php echo $selCheck['wechatwebsite']==1?$displayblock:$displaynone; ?>">
					<a class="menu list-group-item" id="sitecount" href="<?php echo home_url();?>?bt=siteCount" rel="首页">微官网统计</a>
				</section>
					<a class="menu list-group-item" href="<?php echo wp_logout_url();?>" rel="首页"><span class="glyphicon glyphicon-off"></span>&nbsp&nbsp退出</a>
					<!--<a class="menu list-group-item" href="<?php echo site_url( '/login/?action=login' );?>" rel="首页"><span class="glyphicon glyphicon-off"></span>&nbsp&nbsp退出</a>-->
			<?php }?>
		
		<?php }else{?>
		<?php if(is_super_admin( $user->ID )){ ?><a class="menu list-group-item" style="padding-left: 35px; border-bottom: 1px solid #e7e7eb;" href="<?php echo home_url();?>/?admin" id="admin_portal" rel="平台管理员界面"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;平台管理员界面</a><?php } ?>
			
		<div id="systemmanage">
			<a class="list-title list-group-item" style="margin-top:10px;padding-left:35px;border-top: 0px solid #e7e7eb;"><span class="glyphicon glyphicon-th-large"></span>&nbsp&nbsp系统管理</a>
			<section>
				<a class="menu list-group-item" href="<?php echo home_url(); ?>?bt=wechatAccountManage" id="pubmanageheader" rel="首页">&nbsp&nbsp公众号管理</a>
			</section>
			<section>
				<a class="menu list-group-item"  href="<?php echo home_url(); ?>?bt=globalSetting" id="usersettingheader" rel="首页">&nbsp&nbsp账户管理</a>
			</section>
		</div>	
			
		<?php }?>
		</nav>
		</div>
	</header>
	<footer id="footer">
		<p>©2013 <a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>.</p>
		<p>Powered by <a href="#" target="_blank">XXX</a>.</p>
	</footer>
</aside>
</div>

<div class="col_main"<?php if(isset($_GET['no_sidebar'])){?>  style="margin-left: auto;"<?php }?>>
<article id="content">
   <?php }
        else{
?>
    <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>" />
            <meta name="viewport" content="width=device-width" />
            <title><?php wp_title( '|', true, 'right' ); ?></title>
            <link rel="profile" href="http://gmpg.org/xfn/11" />
            <link rel="pingback" href="<?php bloginfo('template_directory'); ?>/css/style.css" />
        </head>
        <body >
         <div id="page" class="hfeed site">
             <div id="main" class="wrapper">
                 <div class="site-wrap">
   <?php }?>
   
   <script>
        var id;
		function show_account_list(){
		   if($('#account_list').hasClass('open')){
			  $('#account_list').removeClass('open');
			  }
		   else{
			  $('#account_list').addClass('open');
			  }
		 }
        $('header a').bind('click', function(){ 
			$('a').removeClass('linkcolor'); 
			$(this).addClass('linkcolor'); 
			$(".linkcolor").css("color","#428bca");
			id = this.id;
			//由于页面会重新刷新，所以需要在cookie中获取对应刷新前选中的id或者class属性
			//2014-07-20新增修改
			if(id != "pubmanageheader" && id != "usersettingheader")
			{
			    setCookie("cookieindexname", id);
			}
			else
			{
			   setCookie("cookiename",id);
			}
			
			
		}); 
		$(function(){ 
			$("#first").css("color","#428bca");
			$("#pubmanageheader").css("color","#428bca");

			$("#"+getCookie("cookiename")).css("color","#428bca");
			$("#"+getCookie("cookieindexname")).css("color","#428bca");
			if(getCookie("cookieindexname") == "first")
			{
				$("#first").css("color","#428bca");
				$("#pubmanageheader").css("color","#428bca");
			}
			else
			{
			    $("#first").css("color","#222");
				$("#pubmanageheader").css("color","#428bca");
			}
		
			if(getCookie("cookiename") == "pubmanageheader" )
			{
				$("#pubmanageheader").css("color","#428bca");
				
			}	
			else
			{
			     $("#pubmanageheader").css("color","#222");
				
			}
			
			//use the following js to dropdown
			$('.dropdown-toggle').dropdown();
			 
		}); 

		//设置cookie
        function setCookie(cookiename,value) 
		{ 
		 var Days = 30; 
		 var exp = new Date(); 
		 exp.setTime(exp.getTime() + Days*24*60*60*1000); 
		 document.cookie = cookiename + "="+ escape (value) + ";expires=" + exp.toGMTString(); 
		} 
		//取cookie 
		function getCookie(cookiename) 
		{ 
		 var arr,reg=new RegExp("(^| )"+cookiename+"=([^;]*)(;|$)");
		 
		 if(arr=document.cookie.match(reg))
		 
		  return unescape(arr[2]); 
		 else 
		  return null; 
		} 
		//删除cookies 
		function delCookie(cookiename) 
		{ 
			var exp = new Date(); 
			exp.setTime(exp.getTime() - 1); 
			var cval=getCookie(cookiename); 
			if(cval!=null) 
				document.cookie= cookiename + "="+cval+";expires="+exp.toGMTString(); 
		} 

		function gweid_select(gweid){
			jQuery.post(
				"<?php bloginfo('template_directory'); ?>/wesite/common/gweid_change.php",
				{gweid : gweid },
				function(data, textStatus, jqXHR){
					if(data.exact_gweid == gweid ){
						$('#current_wechat_account').text("当前公众号: " + $.trim($("a[data-gweid='" + gweid +"']").text()));
						$('#account_list').find('span').parent().text($('#account_list').find('span').text());
						$('#account_list').find('a[data-userid="'+data.user_id+'"]').html("<span style='color: #c7254e;background-color:#E7E8EB;'>"+$('#account_list').find('a[data-userid="'+data.user_id+'"]').html()+"</span>");
						alert("切换成功,请点击'当前公众号'后进行该公众号内容管理");
					}
					/* get_usergroup_bygweid(gweid); */
					get_usergroup_bygweid_change(gweid);
				},
				"json"
			);    
		}

		//用于admin下对分组管理员的分组共享设置进行切换时的判断
		function gweid_group_select(gweid){
			jQuery.post(
				"<?php bloginfo('template_directory'); ?>/wesite/common/get_sharedadminflag.php",
				{gweid : gweid, weid : 0 },
				function(data, textStatus, jqXHR){
					if(data.status == 1){  //如果当前共享设置处于共享状态
						jQuery.post(
							"<?php bloginfo('template_directory'); ?>/wesite/common/gweid_change.php",
							{gweid : gweid },
							function(data, textStatus, jqXHR){
								if(data.exact_gweid == gweid ){
									$('#current_wechat_account').text("当前公众号: " + $.trim($("a[data-gweid='" + gweid +"']").text()));
									$('#account_list').find('span').parent().text($('#account_list').find('span').text());
									$('#account_list').find('a[data-userid="'+data.user_id+'"]').html("<span style='color: #c7254e;background-color:#E7E8EB;'>"+$('#account_list').find('a[data-userid="'+data.user_id+'"]').html()+"</span>");
									alert("切换成功,请点击'当前公众号'后进行该公众号内容管理");
								}
								/* get_usergroup_bygweid(gweid); */
								get_usergroup_bygweid_change(gweid);
							},
							"json"
						);
					}else{
						alert("请先开启共享设置中的共享按钮,将该虚拟号设置为共享状态后再进行切换操作");	
					}
				},
				"json"
			); 


			    

		}

		//用于分组管理员虚拟号的切换
		function gweid_groupselect(gweid,userid,weid){
			//如果当前用户是分组管理员，则在点击相应的共享号的时候，必须确保处于共享状态
			//获取对应switch的状态
			//如果点击的是分组管理员对应的虚拟号
			if((userid == <?php echo $current_user->ID;?>) && (weid == 0)){
				var obj=document.getElementById("iframepage").contentWindow;  
				var ifmObj=obj.document.getElementById("weswitch").innerHTML;  
				if(ifmObj.indexOf("bootstrap-switch-on")<0){
					alert("请先开启共享设置中的共享按钮,将该虚拟号设置为共享状态后再进行切换操作");
				}else{
					jQuery.post(
						"<?php bloginfo('template_directory'); ?>/wesite/common/gweid_change.php",
						{gweid : gweid},
						function(data, textStatus, jqXHR){

							if(data.exact_gweid == gweid ){
								$('#current_wechat_account').text("当前公众号: " + $.trim($("a[data-gweid='" + gweid +"']").text()));
								$('#account_list').find('span').parent().text($('#account_list').find('span').text());
								$('#account_list').find('a[data-userid="'+data.user_id+'"]').html("<span style='color: #c7254e;background-color:#E7E8EB;'>"+$('#account_list').find('a[data-userid="'+data.user_id+'"]').html()+"</span>");
								alert("切换成功,请点击'当前公众号'后进行该公众号内容管理");
							}
						},
						"json"
					);    
				}
			}else{
				jQuery.post(
					"<?php bloginfo('template_directory'); ?>/wesite/common/gweid_change.php",
					{gweid : gweid,user_id: userid },
					function(data, textStatus, jqXHR){

						if(data.exact_gweid == gweid ){
							$('#current_wechat_account').text("当前公众号: " + $.trim($("a[data-gweid='" + gweid +"']").text()));
							$('#account_list').find('span').parent().text($('#account_list').find('span').text());
							$('#account_list').find('a[data-userid="'+data.user_id+'"]').html("<span style='color: #c7254e;background-color:#E7E8EB;'>"+$('#account_list').find('a[data-userid="'+data.user_id+'"]').html()+"</span>");
							alert("切换成功,请点击'当前公众号'后进行该公众号内容管理");
						}
					},
					"json"
				);    

			}
		}
		function refersh_account_list(){
			jQuery.post(
					"<?php bloginfo('template_directory'); ?>/wesite/common/refresh_hader_account_list.php",
					{},
					function(data, textStatus, jqXHR){
						//alert(data);
						if(data!="" && data!=undefined ){
							$('.right_account_li').html(data);
								$('.btn-group').click(function(){
								   if($(this).hasClass('open'))
									  $(this).removeClass('open')
								   else
									  $(this).addClass('open')
								 });
							}
					},
					"text"
				);    
		 }
		 function refersh_group_list(){
			jQuery.post(
					"<?php bloginfo('template_directory'); ?>/wesite/common/refresh_group_list.php",
					{},
					function(data, textStatus, jqXHR){
					    //先清空select中的值,再赋值
						$("#usergroup").find("option").remove();
						$('#usergroup').append(
								'<option value="-1" >请选择分组</option>'
						);
						$.each($.parseJSON(data),function(i,item){
						    if(item.id == null || item.id == 0)
							{
							    $('#usergroup').append(
									'<option value="0" >默认分组</option>'
								);
							}
							else{
							    $('#usergroup').append(
									'<option value="'+item.id+'" >'+item.name+'</option>'
								);
							}
						});
					},
					"text"
				);    
		 }
		function groupUserheader(groupid)
		{
		    jQuery.post(
					"<?php bloginfo('template_directory'); ?>/wesite/common/get_hader_account_groupid_list.php",
					{id: groupid},
					function(data, textStatus, jqXHR){
						if(data!="" && data!=undefined ){
							$('.right_account_li').html(data);
							$('.dropdown-toggle').dropdown();
						}
					},
					"text"
			);    
		}	 
		//change select to dropdown method 
		function group_User_header(groupid)
		{
		    jQuery.post(
					"<?php bloginfo('template_directory'); ?>/wesite/common/get_hader_account_groupid_list.php",
					{id: groupid},
					function(data, textStatus, jqXHR){
						if(data!="" && data!=undefined ){
						    $('#current_user_group').text("当前分组: " + $.trim($("a[data-groupid='" + groupid +"']").text()));   //设置选中的分组显示在dropdown第一行
							$('#group_list').find('span').parent().text($('#group_list').find('span').text());
							$('.right_account_li').html(data);
							$('.dropdown-toggle').dropdown();
							 
							}
					},
					"text"
			);    
		}	 
			
		function get_usergroup_bygweid_change(gweid)
		{
		    jQuery.post(
					"<?php bloginfo('template_directory'); ?>/init/get_usergroup_bygweid.php",
					{gweid : gweid },
					function(data, textStatus, jqXHR){
					    $.each(data,function(i,item){
						   
						    if(item.id == null)
							{
								$('#current_user_group').text("当前分组: " + $.trim($("a[data-groupid='0']").text()));
								groupUserheader(0);   //并刷新分组对应的公众号列表
							}
							else{
								$('#current_user_group').text("当前分组: " + $.trim($("a[data-groupid='" + item.id +"']").text()));
								groupUserheader(item.id);
							}
						});
					},
					"json"
				);    
		}
		 //切换用户分组，对应公众号下拉改变
		function changeUserGroup(groupid)
		{
		
			group_User_header(groupid);
		}	

		<!--space not enough reminder -->
		spacereminder();
		function spacereminder(){
			var str = $.ajax({url:'<?php bloginfo('template_directory'); ?>/init/notenough_space_reminder.php' ,type:'GET',async:false,cache:false}).responseText;
			if(str>0){
				$("#newspace").css({"display":"block"});
			}else{
				$("#newspace").css({"display":"none"});
			}
		};
		<!--shopping order reminder -->
		//当前时间
		orderreminder();		
		function orderreminder(){
			jQuery.post(
					"<?php echo home_url()?>/module.php?module=wepay&do=orderCheck?module=wepay&do=orderCheck",
					{},
					function(data, textStatus, jqXHR){
						if(data.new_status == true ) {
							$("#newoder").css({"display":"block"});
						}
						if(data.new_status == false ) {
							$("#newoder").css({"display":"none"});
						}
					},
					"json"
				); 
		};
		<!--weshopping order reminder -->
		//当前时间
		weorderreminder();		
		function weorderreminder(){
			jQuery.post(
					"<?php echo home_url()?>/module.php?module=weshopping&do=orderCheck?module=weshopping&do=orderCheck",
					{},
					function(data, textStatus, jqXHR){
						if(data.new_status == true ) {
							$("#wenewoder").css({"display":"block"});
						}
						if(data.new_status == false ) {
							$("#wenewoder").css({"display":"none"});
						}
					},
					"json"
				); 
		};
		alarmReminder();
		function alarmReminder(){
			jQuery.post(
							"<?php echo home_url()?>/module.php?module=wepay&do=alarmCheck?module=wepay&do=alarmCheck",
							{},
							function(data, textStatus, jqXHR){
								if(data.new_status == true ) {
									$("#newalarm").css({"display":"block"});
								}
								if(data.new_status == false ) {
									$("#newspace").css({"display":"none"});
								}
							},
							"json"
						); 
		}
		<!--shopping rights reminder -->
		rightsreminder();
		function rightsreminder(){
			jQuery.post(
							"<?php echo home_url()?>/module.php?module=wepay&do=rightsCheck?module=wepay&do=rightsCheck",
							{},
							function(data, textStatus, jqXHR){
								if(data.new_status == true ) {
									$("#newrights").css({"display":"block"});
								}
								if(data.new_status == false ) {
									$("#newrights").css({"display":"none"});
								}
							},
							"json"
						); 
		}
		function getNowFormatDate() {
			var date = new Date();
			var seperator1 = "-";
			var seperator2 = ":";
			var month = date.getMonth() + 1;
			var strDate = date.getDate();
			if (month >= 1 && month <= 9) {
				month = "0" + month;
			}
			if (strDate >= 0 && strDate <= 9) {
				strDate = "0" + strDate;
			}
			var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
					+ " " + date.getHours() + seperator2 + date.getMinutes()
					+ seperator2 + date.getSeconds();
			return currentdate;
		}	
		
		$(function() {
		setInterval("spacereminder()", 600000);
		setInterval("orderreminder()", 600000);
		setInterval("weorderreminder()", 600000);
		setInterval("rightsreminder()", 600000);
		setInterval("alarmReminder()", 600000);
		
		});
		function current_account(){
			jQuery.post(
				"<?php bloginfo('template_directory'); ?>/wesite/common/ajax_current_account_index.php",
				{},
				function(data, textStatus, jqXHR){
					if(data.status == 'reload' )
						window.location.reload();
					if(data.status == 'failed' )
						alert(data.message);
					if(data.status == 'success' )
						window.location.href= $('#current_account').data('href');
				},
				"json"
			); 
            //2014-07-20新增修改					
            //重新设置cookie的值	
			refersh_account_list();					
			setCookie("cookiename","pubmanageheader");
			setCookie("cookieindexname","first");
		}
   </script>
 
   
