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
//get the passed userid
$userid =  $_GET['userid'];
//如果是分组管理员页面跳转过来的
if(isset($_GET['fromflag'])){
	$fromflag = $_GET['fromflag'];
}else{
	$fromflag = 0;
}
//如果当前用户是分组管理员
if($userid == $current_user -> ID){
	$groupadminflag = 1;
}else{
	$groupadminflag = 0;
}

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/css/wsite.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/style/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/style/font-awesome.min.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/style/common.css" />
		<script type="text/javascript" src="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/script/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php echo home_url()?>/wp-content/themes/ReeooV3/we7/script/bootstrap.js"></script>

		
		<title>公众号管理</title>
		<script>		
			var xmlHttp;
			function createXMLHttpRequest(){
			if(window.ActiveXObject)
			 xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			else if(window.XMLHttpRequest)
			 xmlHttp = new XMLHttpRequest();
			}
			$(function(){
				$('.make-switch').on('switch-change', function (e, data) {   
					var obj = $(this);
					//如果点击的是虚拟号的分组共享设置的共享，则执行下面的操作
					if($(this).data('id') == 'groupshareswitch'){
						status = ( $(this).bootstrapSwitch('status') == true ? 1 : 0);
						//将是否共享的状态写入db
						jQuery.post(
								"<?php bloginfo('template_directory'); ?>/init/wechat_group_changeshareflag.php",
								{id : $(this).data('gweid'),status : status, WEID:0 },
								function(data, textStatus, jqXHR){
									
								},
								"json"
							); 
					}else{
						status = ( $(this).bootstrapSwitch('status') == true ? 1 : 0);
						var hasactive=$(this).data('hasactive');
						var sharedflag=$(this).data('sharedflag');
						
						if(((hasactive==0) && (status==1))||(sharedflag==2)){					
							jQuery.post(
								"<?php bloginfo('template_directory'); ?>/init/wechat_accountinfo_update.php",
								{id : $(this).data('gweid'),status : status, active:1 },
								function(data, textStatus, jqXHR){
									top.gweid_select(obj.data('gweid'));
									window.location.reload();
									
								},
								"json"
							); 
						
						}else{
							jQuery.post(//切换时，公共号自定义菜单回复内容的删除，个人号菜单的更新
								"<?php bloginfo('template_directory'); ?>/init/wechat_accountinfo_update.php",
								{id : $(this).data('gweid'),status : status },
								function(data, textStatus, jqXHR){
									top.gweid_select(obj.data('gweid'));
									window.location.reload();
								},
								"json"
							); 					
						}		
					}
							
				});
				
			});
			
			
			$(".deactivate").bind("click",function(){
				alert("需要将该微信号设置为非激活时才可以变为非共享");
			});			
			function setActive(gweid){				
				alert("该微信号设置为激活状态，原来激活的微信号将设置为非激活状态");
				jQuery.post(//激活项更新时：原有激活项变为共享+设置激活项+原有共享项菜单重新生成
							"<?php bloginfo('template_directory'); ?>/init/wechat_accountinfo_update.php",
						{id : gweid,setActive : 2 },
						function(data, textStatus, jqXHR){
							top.gweid_select(gweid);
							window.location.reload();
						},
						"json"
				); 				
			}
			function deleWechatAccount(id,userid,gweid,weid,shared_flag){
				if(shared_flag==2){
					 if(confirm("激活号被删除，设置为共享的微信号将自动变为不共享，确定删除吗")){
						createXMLHttpRequest();
						xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/init/wechataccountdelete.php?beIframe&header=0&footer=0&wid="+id+"&userid="+userid+"&gweid="+gweid+"&weid="+weid,true);
						xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("删除成功");
	
                            //只有admin在删除自己的公众号时才需要整个页面刷新，删除其他用户的公众号只需要刷新所在的iframe	
                            var str = xmlHttp.responseText;		
                            if(userid == <?php echo $current_user -> ID;?>)
							{    
							    //刷新右上角分组下拉列表以及公众号列表
							    //top.refersh_group_list();
								if(str.indexOf("success1success")>0)
								{
									top.refersh_account_list();  //这个是全部刷新
									top.parent.location.reload();	
								}
								else if(str.indexOf("success0success")>0)
								{
									
									var groupid = parseInt(str);
								    top.changeUserGroup(groupid);
									parent.parent.window.location.reload();	//通过parent向上层找iframe
								}		
							}		
                            else{
							    //如果没有切换过公众号则公众号列表全部刷新
							    if(str.indexOf("success1success")>0)
								{
									top.refersh_account_list();  //这个是全部刷新
								}
								else if(str.indexOf("success0success")>0)
								{
									//top.refersh_account_list();  //这个是全部刷新
								    var groupid = parseInt(str);
								    top.changeUserGroup(groupid);
								}
								window.location.reload();
							}							
						}
					}				
						xmlHttp.send(null);					 
					}				
				}else{
					if(confirm("确定删除吗？")){
					createXMLHttpRequest();
						xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/init/wechataccountdelete.php?beIframe&header=0&footer=0&wid="+id+"&userid="+userid+"&gweid="+gweid+"&weid="+weid,true);
						xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("删除成功");
							//parent.location.reload();
							//window.parent.location.reload();	
							var str = xmlHttp.responseText;	
							if(userid == <?php echo $current_user -> ID;?>)
							{
							    //刷新右上角分组下拉列表以及公众号列表
							    //top.refersh_group_list();
								//top.refersh_account_list();
								
								if(str.indexOf("success1success")>0)
								{
									top.refersh_account_list();  //这个是全部刷新
									top.parent.location.reload();	
								}
								else if(str.indexOf("success0success")>0)
								{
									//top.refersh_account_list();  //这个是全部刷新
									//top.parent.location.reload();	
								    var groupid = parseInt(str);
								    top.changeUserGroup(groupid);
									parent.parent.window.location.reload();	//通过parent向上层找iframe
								}
								
							}		
                            else{
							    //如果没有切换过公众号则公众号列表全部刷新
							    if(str.indexOf("success1success")>0)
								{
									top.refersh_account_list();  //这个是全部刷新
								}
								else if(str.indexOf("success0success")>0)
								{
									//top.refersh_account_list();  //这个是全部刷新
								    var groupid = parseInt(str);
								    top.changeUserGroup(groupid);
								}
								window.location.reload();
							}								
						}
					}				
						xmlHttp.send(null);
					}
				}
				
			}			
		</script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
		<style>
			table{font-size:14px;}
		</style>
	</head>
<div class="main_auto" style="padding-left:0;">	
<form id="accountupdaten" action="" method="post" style="margin-bottom:0px;">
			
	<?php	

		//active function 2014-08-18
		$activearray=getWechatGroupActiveInfo($userid,2);
		$hasactive=count($activearray);
		$wechat_group=getWechatGroupInfo($userid);
		//如果该用户是某分组管理员,但当前用户不是分组管理员，对应的虚拟号是可以显示出来的，其WEID为0
		$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$userid);
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
		
		foreach($wechat_group as $wchatp){		
			$gweid = $wchatp->GWEID;
			$weid=$wchatp->WEID;
			$shared_flag=$wchatp->shared_flag;
			$adminshare_flag=$wchatp->adminshare_flag; //分组管理员的虚拟号是否处于共享状态
			//如果是某分组管理员
			if($usergroupid !=0 && $usergroupflag == 1){
				if($groupadminflag == 1){  //如果当前用户是分组管理员，对应某一个用户下的公众号不需要列出weid为0的
					if($weid!=0){
						$account = web_admin_wechat_info_forwechatgroupbyuid($userid,$weid);
					}	
				}else{  //如果当前用户不是分组管理员，对应某一个用户下的公众号需要列出weid为0的
					// if($adminshare_flag == 0){  //如果没有开启共享，则不显示
					// 	if($weid!=0){
					// 		$account = web_admin_wechat_info_forwechatgroupbyuid($userid,$weid);
					// 	}
					// }else{
					// 	$account = web_admin_wechat_info_forwechatgroupbyuid($userid,$weid);
					// }
						
						$account = web_admin_wechat_info_forwechatgroupbyuid($userid,$weid);						
				}
			}else{
				if($weid!=0){
					$account = web_admin_wechat_info_forwechat($weid);
				}else{
					$account = null;
				}
			}
					
			if(!empty($account)){
				foreach($account as $useraccount){
		
					$wechatweid = $useraccount -> WEID;
					$wechatnikename = $useraccount -> wechat_nikename;
					if($weid == 0)
					 	$wechatnikename = "虚拟号"; //如果weid为0，则一定是虚拟号					
					$wechattype = $useraccount->wechat_type;
					$token = $useraccount->token;
					$id = $useraccount->wid;
					
					$weid_fans_count = wechat_get_count_weid_fans($wechatweid);
					foreach($weid_fans_count as $wf) 
					{
						$weid_fan_count = $wf->fans_weid_count;
					}
					
					$userchat_info = web_admin_array_selectvericode($wechatweid);
					foreach($userchat_info as $ui) 
					{
						$wechat_fan_init = $ui->wechat_fan_init;
					}
					//fans + init fans
					$weid_fan_count = $weid_fan_count + $wechat_fan_init;
					
					if(($wechattype == 'pub_sub')||($wechattype == 'pub_svc')){
						$url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoinPublic.php?hash='.$useraccount->hash;
						$url=preg_replace('|^https://|', 'http://', $url);
					}else{
						$url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoin.php?hash='.$useraccount->hash;
						$url=preg_replace('|^https://|', 'http://', $url);
					}
		?>		
		<!--wechat account display-->
		<?php if($usergroupid !=0 && $usergroupflag == 1 && $weid == 0){?>
		<div class="" style="border-color: #DDD;-webkit-border-radius: 0;-moz-border-radius: 0;border-radius: 0;">
			<div class="wechat_accountdiv">
				<span class="wechatpull_left fontweight lineheight">分组共享设置:</span>
				<span class="wechatpull_left wechatmargin">
					<div style="margin-left: 15px;height:22px;font-weight: normal;" class="make-switch switch-small" id="groupshareswitch" data-on-label="共享" data-off-label="不共享"  data-gweid="<?php echo $gweid ?>" data-id="groupshareswitch" >
						<input type="checkbox" <?php if($adminshare_flag == 1){ echo 'checked';  }?>/>
					</div>
				</span>
			</div>
		</div>
		<?php }?>
		<div class="account" style="padding-top:5px;width: 100%;clear: both;">
			<!--wechat account manage-->
			<div class="navbar-inner thead">
				<h5>
					<span class="pull-right">
						<?php if(!($usergroupid !=0 && $usergroupflag == 1 && $weid == 0)){?>
						<a style="cursor:pointer;" onClick="deleWechatAccount('<?php echo $id;?>','<?php echo $userid;?>','<?php echo $gweid;?>','<?php echo $wechatweid;?>','<?php echo $shared_flag;?>')" value="<?php echo $id;?>">删除</a>
						<a href="<?php bloginfo('template_directory'); ?>/init/account_edit.php?wid=<?php echo $id; ?>&weid=<?php echo $wechatweid;?>&gweid=<?php echo $gweid; ?>&userid=<?php echo $userid;?>">编辑</a>
						<?php }?>
						<?php if($fromflag == 0){
							if($weid == 0){ //如果是虚拟号对应的切换，需要判断当前是否已经处于共享状态,否则给出提示
						?>
							<a href="javascript:top.gweid_group_select('<?php echo $gweid; ?>');">切换</a>	
						<?php
							}else{
						?>
							<a href="javascript:top.gweid_select('<?php echo $gweid; ?>');">切换</a>	
						<?php }}else{   //如果是分组管理员的虚拟号处于不共享状态,则该切换链接无效
							if($usergroupid !=0 && $usergroupflag == 1 && $weid == 0 && $adminshare_flag == 0){?>
							<a href="#">切换</a>
							<?php }else{?>	
							<a href="javascript:top.gweid_groupselect('<?php echo $gweid; ?>','<?php echo $userid;?>','<?php echo $wechatweid;?>');">切换</a>	
						<?php }}?>	
						<?php if(!($usergroupid !=0 && $usergroupflag == 1 && $weid == 0)){?>	
						<div  <?php if((($wechattype == "pub_sub")||($wechattype == "pub_svc"))||($shared_flag == '0')){?> style="display:none" <?php } else{?>  style="margin-left: 15px;height:22px;font-weight: normal;display:inline" <?php } ?>>
							<input type="radio" name="active" value="<?php echo $gweid ?>" onclick="setActive('<?php echo $gweid ?>')" <?php if($shared_flag == '2') {?> checked="checked" disabled="true" <?php }  ?> style="vertical-align:middle;  margin-bottom:5px;margin-right:-4px;"><a>激活</a></input>	
						</div>											
						<div style="margin-left: 15px;height:22px;font-weight: normal;" class="make-switch switch-small" id="raw" data-on-label="共享" data-off-label="不共享"  data-gweid="<?php echo $gweid ?>" data-hasactive="<?php echo $hasactive ?> " data-sharedflag="<?php echo $shared_flag ?>" >
							<input type="checkbox" <?php if(($shared_flag == '1')&&(!empty($hasactive))){ echo 'checked';  }else if(($shared_flag == '2')){  echo 'checked disabled'; }else if((empty($hasactive))&&(($wechattype == "pub_sub")||($wechattype == "pub_svc"))){ echo 'disabled'; }?>/>
						</div>
						<?php }?>	
					</span>
					
					<span class="pull-left"><?php echo $wechatnikename; ?> 
						<?php if(!($usergroupid !=0 && $usergroupflag == 1 && $weid == 0)){?>
						<small>
						（微信号：<?php echo $wechatnikename; ?>）
						（类型：<span><?php if($wechattype == "pri_sub") { echo '个人微信订阅号';} ?> <?php if($wechattype == "pri_svc") { echo '个人微信服务号';} ?> <?php if(($wechattype == "pub_sub") && ($wechatauth == 0)) { echo '公用微信未认证订阅号';} ?><?php if(($wechattype == "pub_sub") && ($wechatauth == 1)) { echo '公用微信认证订阅号';} ?> <?php if(($wechattype == "pub_svc") && ($wechatauth == 0)) { echo '公用微信未认证服务号';} ?><?php if(($wechattype == "pub_svc") && ($wechatauth == 1)) { echo '公用微信认证服务号';} ?>  </span>）
						（粉丝数量：<?php echo $weid_fan_count; ?>）
						</small>
						<?php }?>
					</span>
				</h5>
			</div>
			<!--wechat account manage end-->
		<?php if((($wechattype == "pri_sub")||($wechattype == "pri_svc")) && !($usergroupid !=0 && $usergroupflag == 1 && $weid == 0)){?>	
			<div class="tbody">
				<div class="con">
					<div class="name pull-left">url地址</div>
					<div class="input-append pull-left" id="api_20">
						<input id="" type="text" value="<?php echo $url; ?>" readonly=enable style="height:30px;border-radius: 4px;width: 100%;" />
					</div>
				</div>
				<div class="con">
					<div class="name pull-left">Token</div>
					<div class="input-append pull-left" id="token_20">
						<input id="" type="text" value="<?php echo $token; ?>" readonly=enable style="height:30px;border-radius: 4px;width: 100%;" />
					</div>
				</div>
			</div>
		<?php }?>	
		</div>		
		<!--wechat account display end-->
<?php }?>		
</form>
<?php }}
?>
<div class="alert alert-block" style="text-align:center">
	<p>设置为共享的个人微信公众号可以设置为激活状态,作为其他微信号的共享号.第一个设置为共享号的个人微信号默认为激活状态.</p>
</div>
					
</html>
