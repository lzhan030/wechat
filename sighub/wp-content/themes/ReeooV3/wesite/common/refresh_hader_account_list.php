<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $wpdb;
global $current_user;
//当前用户如果是分组管理员
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
//如果是分组管理员，和admin的是类似的
$userId=((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($usergroupid !=0 && $usergroupflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;

?>
<div class="btn-group">
  <button type="button" id="current_wechat_account" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
  <?php  
	$WEID = $wpdb->get_var( "SELECT WEID FROM {$wpdb -> prefix}wechat_group where user_id='{$userId}' AND GWEID='{$_SESSION['exact_GWEID']}'" );
	//if(!empty($WEID))$WEID有可能为0，empty(0)和empty("")结果是一样的
	//if(!empty($WEID))
	if($WEID !== null)
		$wechat_nikename = $wpdb->get_var( "SELECT w.wechat_nikename FROM {$wpdb -> prefix}wechat_usechat u1,{$wpdb -> prefix}wechats w WHERE u1.wid = w.wid and u1.WEID ='".intval($WEID)."'");
 	if($WEID == 0){
 		$wechat_nikename = "虚拟号";
 	}
 	echo $wechat_nikename === NULL || $wechat_nikename === false || ($WEID === null) ? '请选择公众号' : ('当前公众号: '.$wechat_nikename);
  ?>
	 <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu" style="max-height: 550px; overflow-y: scroll;">
	<?php
	
	if(is_super_admin( $current_user -> ID ))
	{
		//get all userids
		//echo $_SESSION['GWEID'];
		if(!empty($_SESSION['GWEID']))
		{	
			//$groupids = $wpdb->get_results( "SELECT w2.group_id FROM {$wpdb -> prefix}wechat_group w1 left join {$wpdb -> prefix}user_group w2 on w1.user_id = w2.user_id WHERE w1.user_id != 0 AND w1.GWEID = ".$_SESSION['GWEID']);
			//secure db query 
			$gsql = $wpdb -> prepare("SELECT w2.group_id FROM {$wpdb -> prefix}wechat_group w1 left join {$wpdb -> prefix}user_group w2 on w1.user_id = w2.user_id WHERE w1.user_id != 0 AND w1.GWEID = %d", $_SESSION['GWEID']);
			$groupids = $wpdb->get_results($gsql);
			foreach($groupids as $sgroup){    
				$sgroupid = $sgroup -> group_id;
			}
			
			if(($sgroupid == 0) || (empty($sgroupid)))  //当前处于默认分组,默认分组是未分组的状态,未分组对应的id是0
			{
				$getuserids = $wpdb->get_results( "SELECT distinct w1.ID as user_id, w1.user_login as name, w2.group_id FROM {$wpdb -> prefix}users w1 left join {$wpdb -> prefix}user_group w2 on w1.ID = w2.user_id WHERE w1.user_pass != '' AND (isnull(w2.group_id) OR w2.group_id = 0) order by w1.ID ASC" );
			    
			}else{    //取出当前分组中的用户
				$getuserids = $wpdb->get_results( "SELECT distinct w1.ID as user_id, w1.user_login as name, w2.group_id FROM {$wpdb -> prefix}users w1 left join {$wpdb -> prefix}user_group w2 on w1.ID = w2.user_id WHERE w1.user_pass != '' AND w2.group_id = ".$sgroupid." order by w1.ID ASC" );
			}
		}
		else{  //当前没有切换某个公众号，显示所有的公众号
		   
			$getuserids = $wpdb->get_results( "SELECT ID as user_id FROM {$wpdb -> prefix}users where user_pass != '' order by ID ASC" );
		}
		foreach($getuserids as $getuserid)
		{
			$guserid = $getuserid -> user_id;
			//get username by id
			$user_info = get_userdata($guserid);
			$username = $user_info->user_login;
			?>
				<li><a href="#" data-userid="<?php echo$guserid;?>"><?php if($_SESSION['GWEID_matched_userid'] == $guserid || ($guserid == $current_user -> ID && empty($_SESSION['GWEID_matched_userid']))) echo '<span style="color: #c7254e;background-color:#E7E8EB;">';?><?php echo $username.'的公众号:';?><?php if($_SESSION['GWEID_matched_userid'] == $guserid || ($guserid == $current_user -> ID && empty($_SESSION['GWEID_matched_userid']))) echo '</span>';?></a></li>
			<?php
			//如果该用户是分组管理员,其weid为0的表示是虚拟号
			if($usergroupid !=0 && $usergroupflag == 1){
				$wechat_group=$wpdb->get_results( "SELECT * FROM {$wpdb -> prefix}wechat_group where user_id=".intval($guserid)." order by WEID" );
			}else{
				$wechat_group=$wpdb->get_results( "SELECT * FROM {$wpdb -> prefix}wechat_group where user_id=".intval($guserid)." AND WEID != 0 order by WEID" );
			}
			if(empty($wechat_group)){
				?>
					<li><a href="#">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp尚未添加公众号</a></li>
				<?php
			}								
			else{
				foreach($wechat_group as $wchatp){		
					$gweid = $wchatp->GWEID;
					$weid=$wchatp->WEID;
					$shared_flag=$wchatp->shared_flag;
					$adminshare_flag=$wchatp->adminshare_flag;
					//如果是分组管理员，weid有为0的情况
					if($usergroupid !=0 && $usergroupflag == 1){
						if($weid == 0){  //如果虚拟号则需要判断下对应的共享状态是否已经开启
							//if($adminshare_flag == 1){
								//$wechat_nikename = $wpdb->get_var( "SELECT w.wechat_nikename FROM {$wpdb -> prefix}wechat_usechat u1,{$wpdb -> prefix}wechats w WHERE u1.wid = w.wid and u1.WEID =".$weid);
								?>
								<!--<li><a data-gweid="<?php echo $gweid; ?>" href="javascript:gweid_select('<?php echo $gweid; ?>');">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $wechat_nikename; ?></a></li>-->
								<?php
							//}
							$wechat_nikename = $wpdb->get_var( "SELECT w.wechat_nikename FROM {$wpdb -> prefix}wechat_usechat u1,{$wpdb -> prefix}wechats w WHERE u1.wid = w.wid and u1.WEID =".$weid);
							$wechat_nikename = "虚拟号";  //weid为0的一定是虚拟号
							?>
							<li><a data-gweid="<?php echo $gweid; ?>" href="javascript:gweid_group_select('<?php echo $gweid; ?>');">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $wechat_nikename; ?></a></li>
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
						?>
						<li><a data-gweid="<?php echo $gweid; ?>" href="javascript:gweid_groupselect('<?php echo $gweid; ?>','<?php echo $guserid;?>','<?php echo $weid;?>');">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $wechat_nikename; ?></a></li>
						<?php								
					}
				}
			}
		}else{
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