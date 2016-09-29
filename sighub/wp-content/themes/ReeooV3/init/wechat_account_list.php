<?php
	session_start();
	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');
	require_once ('../wechat/common/wechat_dbaccessor.php');
	require_once ('../wesite/common/dbaccessor.php');
	global $wpdb;
	global $current_user;
	$gweid=$_SESSION['GWEID'];
	$user_id =  $current_user->ID;
	//Get the total number of the wechat accounts
	$wechat_count = getWechatGroup_count($user_id);
	foreach($wechat_count as $w) {
		$wechat_number = $w->wechatCount;
	}
	
	//Get the total number of the fans
	$fans_count=wechat_get_count_fans($user_id);
	foreach($fans_count as $f) 
	{
		$fan_count = $f->fans_count;
	}
	
	//Get the total init number of the fans
	$usechat_info = wp_wechat_usechat_info($user_id);
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

	//当前用户是否是分组管理员
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
		$user_login = $current_user->user_login;
		//获取该分组管理员对应的虚拟号gweid
		$getgweids = $wpdb->get_results( "SELECT GWEID, adminshare_flag FROM {$wpdb -> prefix}wechat_group where user_id = ".$current_user -> ID." AND WEID = 0");
		foreach($getgweids as $getgweid)
		{
		    $groupgweid = $getgweid -> GWEID;
		    $adminflag = $getgweid -> adminshare_flag;
		}
	}

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
		
	</head>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/button.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min-3.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap-switch-3.css">
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-1.10.2.js"></script>
	<!--<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery1.83.js"></script>-->
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap-switch-3.min.js"></script>
	<script type="text/javascript" language="javascript">
        function changeWechatiFrameHeight() {
            var ifm= document.getElementById("wechatmanage_iframe");
            var subWeb = document.frames ? document.frames["wechatmanage_iframe"].document : ifm.contentDocument;
			var clientHeight = subWeb.body.clientHeight;//其它浏览器默认值
			//如果是chrome则执行这段
		   if(navigator.userAgent.indexOf("Chrome")!=-1)
	        {
	            clientHeight = subWeb.body.scrollHeight;
	        }
			//如果是Firefox则执行这段
	        if(navigator.userAgent.indexOf("Firefox")!=-1)
	        {
	           //clientHeight = subWeb.documentElement.scrollHeight;
			   clientHeight = 1800;
	        } 

            if(ifm != null && subWeb != null) {
                //ifm.height = subWeb.body.scrollHeight;
				ifm.height = clientHeight;
				var newheight =  parseInt(ifm.height) +  parseInt(300);
				//2014-07-16新增修改改变外层iframe的高度
				window.parent.window.parent.document.getElementById("iframepage").height = newheight;
				//2014-07-10动态改变账户管理页面的长度,点击已认证会新出现两行表格
				var ifmsrc = document.getElementById("wechatmanage_iframe").src;
				if(ifmsrc.indexOf("wechat_accountinfo.php") > 0)
				{
					//ifm.height = clientHeight + 10;2014-12-11
					ifm.height = clientHeight + 200;
				}
				
			}
         }
		 
    </script>
    <script>
	   $(function(){
			//$('.nav-tabs a:first').tab('show');
			$('.nav-tabs a:last').tab('show');
			$('.nav-tabs a:last').click(function (e) {
			});
			<?php 
			//如果是分组管理员，且不是平台admin
			if(!is_super_admin( $current_user -> ID ) && $usergroupid !=0 && $usergroupflag == 1){?>
			
			//初始化switch
			$('input[name="my-checkbox"]').bootstrapSwitch();
			//绑定切换事件  
			$('input[name="my-checkbox"]').on('switchChange.bootstrapSwitch', function(event, state) {  
			   if(state == true){  //如果切换到共享状态
			   		$('.switcha').attr("href","javascript:top.gweid_groupselect('<?php echo $groupgweid; ?>','<?php echo $current_user->ID;?>', 0);");
			   		//将共享状态写入数据库
			   		jQuery.post(
						"<?php bloginfo('template_directory'); ?>/wesite/common/sharedadminflag_change.php",
						{groupgweid : <?php echo $groupgweid?>,weid:0,flag:1 },
						function(data, textStatus, jqXHR){
							
						},
						"json"
					);   
			   		
			   }else{ 
			   		$('.switcha').attr('href','#');
			   		//将不共享状态写入数据库
			   		jQuery.post(
						"<?php bloginfo('template_directory'); ?>/wesite/common/sharedadminflag_change.php",
						{groupgweid : <?php echo $groupgweid?>,weid:0,flag:0 },
						function(data, textStatus, jqXHR){
							
						},
						"json"
					);   
			   		
			   }
			});  
			//如果当前switch处于被选中状态
			if($('input[name="my-checkbox"]').bootstrapSwitch("state")){
				$('.switcha').attr("href","javascript:top.gweid_groupselect('<?php echo $groupgweid; ?>','<?php echo $current_user->ID;?>', 0);");
			}else{
				$('.switcha').attr('href','#');
			}
			//链接a的单击事件
			$('.switcha').click(function(){ 
		        if($('.switcha').attr("href")=="#"){
		        	alert("请先开启共享设置中的共享按钮,将该虚拟号设置为共享状态后再进行切换操作");
		        }
		    });
			<?php }?>

		});
		
		function switab(str)
		{
			if(str == "wechat_add")
			{
			     $("#wechatmanage_iframe").attr("src","<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_account.php?beIframe");
			}
			else if(str == "wechat_manage")
			{
			     $("#wechatmanage_iframe").attr("src","<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_accountinfo.php?beIframe");
			}
		}

	</script>
	<style>
		.summary{float:left;width:46.5%; margin-right:1%;}
		.summary_ul1{background-color:#7CBAE5;border:1px solid #6eb0dd;height:50px;color:white;}
		.summary_ul2{background-color:#60d295;border:1px solid #57c78b;height:50px;color:white;}
		.summary_font{font-family:"微软雅黑";}
		body{font-family: "微软雅黑";}
		a{color: #0088cc;}
	</style>
	
<div class="main_auto">
	<div class="main-titlenew" style="margin-bottom:10px; margin-left:0; width:100%;">
		<div class="title-1" style="height:auto;width:100%; margin:20px 0">
			<div class="summary">
				<ul class="summary_ul1">
					<li>
						<span class="glyphicon glyphicon-tags">
							&nbsp
							<font class="summary_font">
								<?php if(is_super_admin( $current_user->ID )){ echo "admin";}else{if( $usergroupid !=0 && $usergroupflag == 1) echo $user_login;}?>添加的公众号数量：<?php echo $wechat_number; ?>个
							</font>
						</span>
					</li>
				</ul>
			</div>
			<div class="summary">
				<ul class="summary_ul2">
					<li>
						<span class="glyphicon glyphicon-user">
							&nbsp
							<font class="summary_font">
								<?php if(is_super_admin( $current_user->ID )){echo "admin";}else{if( $usergroupid !=0 && $usergroupflag == 1) echo $user_login;}?>粉丝总人数：<?php echo $fan_count;?>人
							</font>
						</span>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<!--group admin share account setting 20150414 new added-->
	<?php 
	//如果是分组管理员，且不是平台admin
	if(!is_super_admin( $current_user -> ID ) && $usergroupid !=0 && $usergroupflag == 1){?>
	
	<div class="" style="border-color: #DDD;-webkit-border-radius: 0;-moz-border-radius: 0;border-radius: 0;">
		<div class="wechat_accountdiv">
			<span class="wechatpull_left fontweight lineheight">分组共享设置:</span>
			<span class="wechatpull_left wechatmargin">
				<div id="weswitch" class="bootstrap-switch bootstrap-switch-mini">
					<input type="checkbox" <?php if($adminflag == 1) echo "checked";?> name="my-checkbox" data-on-text="共享" data-off-text="不共享" data-on-color="primary">
				</div>
			</span>
		</div>
	</div>
	<div class="wechat_account">	
		<!--wechat account manage-->
		<div class="wechat_accountthead">
			<div class="wechat_accountdiv">
				<span class="wechatpull_right">	
					<a class="switcha">切换</a>													
				</span>
				<span class="wechatpull_left fontweight">虚拟号</span>
			</div>
		</div>
		<!--wechat account manage end-->
	</div>
	<div class="help-block" style="margin-bottom:50px;">注：如果激活分组共享设置，则该分组下所有的公众号的群发及会员管理功能都将在该分组管理员的虚拟号里进行管理。</div>
	<?php }?>
	
	<div>
		<ul class="nav nav-tabs" id="tabselect">
			<li><a href="#wechat_add" onclick="switab('wechat_add')" data-toggle="tab" >添加公众号</a></li>
			<li class="active selected"><a href="#wechat_manage" onclick="switab('wechat_manage')" data-toggle="tab" >管理公众号</a></li>
		</ul>
	</div>
	
	<div class="tab-content" style="margin-right: 55px;" >
		  <iframe frameborder="0" id="wechatmanage_iframe" name="wechatmanage_iframe" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_accountinfo.php?beIframe" width="100%" height="1900" scrolling="auto" onLoad="changeWechatiFrameHeight()"></iframe>
	</div>
</div>	
</html>

<?php   
    get_footer();
 ?>