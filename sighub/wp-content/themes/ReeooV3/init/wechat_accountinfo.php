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
if(isset($_GET['userid'])){
    $userid = $_GET['userid'];
}else{
    $userid = $current_user->ID;
}

//判断当前用户是否是分组管理员
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
			//admin登录条件下的iframeheight的变化
		    function changeWechatiFrameHeight() {
                var ifm= document.getElementById("useraccount_iframe");
                var subWeb = document.frames ? document.frames["useraccount_iframe"].document : ifm.contentDocument;
				
                if(ifm != null && subWeb != null) {
                    ifm.height = subWeb.body.scrollHeight;
					var newheight =  parseInt(ifm.height) +  parseInt(500);
					var newheight1 =  parseInt(ifm.height) +  parseInt(300);
					//2014-07-16新增修改改变外层iframe的高度
					window.parent.window.parent.document.getElementById("iframepage").height = newheight;
					window.parent.document.getElementById("wechatmanage_iframe").height = newheight1;
					//2014-07-10动态改变账户管理页面的长度,点击已认证会新出现两行表格
					var ifmsrc = document.getElementById("useraccount_iframe").src;
					if(ifmsrc.indexOf("wechat_admin_accountinfo.php") > 0)
					{
						ifm.height = subWeb.body.scrollHeight + 200;
						//alert(ifm.height); 原值为100
					}
				}
            } 
            //groupadmin登录条件下的iframeheight的变化
            function changeWechatgroupiFrameHeight(){

            	var ifm= document.getElementById("groupuseraccount_iframe");
                var subWeb = document.frames ? document.frames["groupuseraccount_iframe"].document : ifm.contentDocument;
				
                if(ifm != null && subWeb != null) {
                    ifm.height = subWeb.body.scrollHeight;
					var newheight =  parseInt(ifm.height) +  parseInt(600);
					var newheight1 =  parseInt(ifm.height) +  parseInt(300);
					//2014-07-16新增修改改变外层iframe的高度
					window.parent.window.parent.document.getElementById("iframepage").height = newheight;
					window.parent.document.getElementById("wechatmanage_iframe").height = newheight1;
					//2014-07-10动态改变账户管理页面的长度,点击已认证会新出现两行表格
					var ifmsrc = document.getElementById("groupuseraccount_iframe").src;
					if(ifmsrc.indexOf("wechat_admin_accountinfo.php") > 0)
					{
						ifm.height = subWeb.body.scrollHeight + 200;
						//alert(ifm.height); //原值为100
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
			$(function(){
				$('.make-switch').on('switch-change', function (e, data) {   
					var obj = $(this);
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
						xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/init/wechataccountdelete.php?beIframe&wid="+id+"&userid="+userid+"&gweid="+gweid+"&weid="+weid,true);
						xmlHttp.onreadystatechange = function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							alert("删除成功");
							//刷新右上角分组下拉列表以及公众号列表
							top.refersh_account_list();
							if(userid == <?php echo $current_user -> ID;?>){
							    parent.location.reload();		
							}else{
							    window.location.reload();
							}									
						}
					}				
						xmlHttp.send(null);					 
					}				
				}else{
					if(confirm("确定删除吗？")){
						createXMLHttpRequest();
							xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/init/wechataccountdelete.php?beIframe&wid="+id+"&userid="+userid+"&gweid="+gweid+"&weid="+weid,true);
							xmlHttp.onreadystatechange = function(){
							if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
								alert("删除成功");
								top.refersh_account_list();
								if(userid == <?php echo $current_user -> ID;?>){
									parent.location.reload();		
								}		
								else{
									window.location.reload();
								}							
							}
						}				
							xmlHttp.send(null);
					}
				}
				
			}	
			//切换用户分组，对应公众号下拉改变
			function changeUserGroup(groupid)
			{
				$("#usergroup").val(groupid);
				selGroupUser(groupid);
			}
            function selUser(userid){
			    //change the src of iframe
				$("#useraccount_iframe").attr("src","<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_admin_accountinfo.php?beIframe&userid="+userid);
			}	
			function selgroupUser(userid){
			    //change the src of iframe
				//$("#groupuseraccount_iframe").attr("src","<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_groupadmin_accountinfo.php?beIframe&userid="+userid);
				$("#groupuseraccount_iframe").attr("src","<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_admin_accountinfo.php?beIframe&userid="+userid+"&fromflag=1");
				$(".alertdisplay").css("display","none");
			}	
            function selGroupUser(groupid)
			{
			    jQuery.post(//切换时，公共号自定义菜单回复内容的删除，个人号菜单的更新
					"<?php bloginfo('template_directory'); ?>/init/wechat_group_user.php",
					{id : groupid},
					function(data, textStatus, jqXHR){
					    //先清空select中的值,再赋值
					    $("#wechatusers").find("option").remove();
						//alert(data.length);
						if(data.length == 0) //当前分组没有用户
						{
						    $("#adminwechataccount").css('display','none'); 
							$("#nouseraccount").css('display','block'); 

						}else{
						    $("#adminwechataccount").css('display','block'); 
							$("#nouseraccount").css('display','none'); 
							for(var i=0; i<data.length; i++)
							{ 
							   
								$('#wechatusers').append(
									'<option value="'+data[i].id+'" >'+data[i].name+'</option>'
								);
							}
							//重新显示下拉列表中的第一个用户对应的公众号
							selUser(data[0].id);
						}
						
					},
					"json"
				); 	
			}	
							
		</script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
		<style>
			table{font-size:14px;}
		</style>
	</head>
<div class="main_auto" style="padding-left:0;">	
<?php if(is_super_admin( $current_user -> ID ) && !isset($_GET['no_user_select'])){
	//get all userids
	//secure query method
	$getuseridsql = $wpdb -> prepare("SELECT ID as user_id FROM {$wpdb -> prefix}users where user_pass != '' and ID != %d order by user_id ASC", $current_user -> ID);
	$getuserids = $wpdb->get_results($getuseridsql);
	$getgroups = $wpdb->get_results( "SELECT ID as groupid, group_name FROM {$wpdb -> prefix}group order by ID ASC" );
?>
<div>
    <table width="610" height="30" border="0" cellpadding="15px" style=" margin-left:0px; margin-top:10px;" id="table3">
		<tr>
			<td width="75"><label for="name" style="font-weight:bold;">请选择用户: </label></td>
			<td width="125">
				<select name="usergroup" class="form-control" size="1" type="text;" id="usergroup" value="5" maxlength="20" onchange="selGroupUser(this.options[this.selectedIndex].value)">
					<option value="-1" selected="selected">请选择分组</option>
					<?php 
						foreach($getgroups as $getgroup){
						    $groupid = $getgroup -> groupid;
						    $groupname = $getgroup -> group_name;
						?>
						<option value="<?php echo $groupid;?>" ><?php echo $groupname;?></option>
					<?php }?>	
				</select>
			</td>		
			<td width="125">
				<select name="wechatusers" class="form-control" size="1" type="text;" id="wechatusers" value="5" maxlength="20" onchange="selUser(this.options[this.selectedIndex].value)">
					<option value="<?php echo $current_user -> ID;?>" selected="selected">admin</option>
					<?php foreach($getuserids as $getuserid){
						$guserid = $getuserid -> user_id;
						$user_info = get_userdata($guserid);
						$username = $user_info->user_login;?>
					<option value="<?php echo $guserid;?>" <?php if($username == "admin") echo 'selected="selected"'; ?>><?php echo $username;?></option>
					<?php }?>	
				</select>
			</td>						
		</tr>	
    </table >
</div>
<div id="adminwechataccount" style="margin-top:10px;">
    <iframe frameborder="0" id="useraccount_iframe" name="useraccount_iframe" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_admin_accountinfo.php?beIframe&userid=<?php echo $current_user -> ID;?>" width="100%" height="900" scrolling="auto" onLoad="changeWechatiFrameHeight()"></iframe>
</div>
<div id="nouseraccount" class="alert alert-block" style="text-align: center; display: none;">
	<p>当前分组暂无用户.</p> 
</div>
<?php }else{
	
	//如果是分组管理员,并且不是从编辑页面跳转
	if($usergroupid !=0 && $usergroupflag == 1 && !isset($_GET['no_user_select'])){
		//获取该分组下的所有用户
		$getgroupusers = $wpdb->get_results( "SELECT distinct w1.ID as user_id, w1.user_login FROM {$wpdb -> prefix}users w1 left join {$wpdb -> prefix}user_group w2 on w1.ID = w2.user_id WHERE w1.user_pass != '' AND w2.group_id = ".$usergroupid." order by w1.ID ASC" );
	?>
	<div>
	    <table width="360" height="30" border="0" cellpadding="15px" style=" margin-left:0px; margin-top:10px;" id="table3">
			<tr>
				<td width="75"><label for="name" style="font-weight:bold;">请选择用户: </label></td>	
				<td width="125">
					<select name="wechatusers" class="form-control" size="1" type="text;" id="wechatusers" value="5" maxlength="20" onchange="selgroupUser(this.options[this.selectedIndex].value)">
						<?php foreach($getgroupusers as $getuserid){
							$guserid = $getuserid -> user_id;
							$user_info = get_userdata($guserid);
							$username = $user_info->user_login;?>
							<option value="<?php echo $guserid;?>" <?php if($guserid == $current_user -> ID) echo 'selected="selected"'; ?>><?php echo $username;?></option>
						<?php }?>	
					</select>
				</td>						
			</tr>	
	    </table >
	</div>	
	<div id="groupadminwechataccount" style="margin-top:10px;">
	    <iframe frameborder="0" id="groupuseraccount_iframe" name="groupuseraccount_iframe" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_admin_accountinfo.php?beIframe&userid=<?php echo $current_user -> ID;?>" width="100%" height="900" scrolling="auto" onLoad="changeWechatgroupiFrameHeight()"></iframe>
	</div>
	<div id="nogroupuseraccount" class="alert alert-block" style="text-align: center; display: none;">
		<p>当前分组暂无用户.</p> 
	</div>
	<?php }else{
?>
<form id="accountupdaten" action="" method="post" style="margin-bottom:0px;">
			
	<?php	
		//active function 2014-08-18
		
		$activearray=getWechatGroupActiveInfo($userid,2);
		$hasactive=count($activearray);
		$wechat_group=getWechatGroupInfo($userid);
		foreach($wechat_group as $wchatp){		
			$gweid = $wchatp->GWEID;
			$weid=$wchatp->WEID;
			$shared_flag=$wchatp->shared_flag;
			if($weid!=0){
				$account = web_admin_wechat_info_forwechat($weid);
			}else{
				$account = null;
			}
						
			if(!empty($account)){
				foreach($account as $useraccount){
		
					$wechatweid = $useraccount -> WEID;
					$wechatnikename = $useraccount -> wechat_nikename;					
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
		<div class="account" style="padding-top:10px;">
			<!--wechat account manage-->
			<div class="navbar-inner thead">
				<h5>
					<span class="pull-right">
						<a style="cursor:pointer;" onClick="deleWechatAccount('<?php echo $id;?>','<?php echo $userid;?>','<?php echo $gweid;?>','<?php echo $wechatweid;?>','<?php echo $shared_flag;?>')" value="<?php echo $id;?>">删除</a>
						<a href="<?php bloginfo('template_directory'); ?>/init/account_edit.php?wid=<?php echo $id; ?>&weid=<?php echo $wechatweid;?>&gweid=<?php echo $gweid; ?>&userid=<?php echo $userid;?>">编辑</a>
						<a href="javascript:top.gweid_select('<?php echo $gweid; ?>');">切换</a>				
						<div  <?php if((($wechattype == "pub_sub")||($wechattype == "pub_svc"))||($shared_flag == '0')){?> style="display:none" <?php } else{?>  style="margin-left: 15px;height:22px;font-weight: normal;display:inline" <?php } ?>>
							<input type="radio" name="active" value="<?php echo $gweid ?>" onclick="setActive('<?php echo $gweid ?>')" <?php if($shared_flag == '2') {?> checked="checked" disabled="true" <?php }  ?> style="vertical-align:middle;  margin-bottom:5px;margin-right:-4px;"><a>激活</a></input>	
						</div>											
						<div style="margin-left: 15px;height:22px;font-weight: normal;" class="make-switch switch-small" id="raw" data-on-label="共享" data-off-label="不共享"  data-gweid="<?php echo $gweid ?>" data-hasactive="<?php echo $hasactive ?> " data-sharedflag="<?php echo $shared_flag ?>" >
							<input type="checkbox" <?php if(($shared_flag == '1')&&(!empty($hasactive))){ echo 'checked';  }else if(($shared_flag == '2')){  echo 'checked disabled'; }else if((empty($hasactive))&&(($wechattype == "pub_sub")||($wechattype == "pub_svc"))){ echo 'disabled'; }?>/>
						</div>
					</span>
					
					<span class="pull-left"><?php echo $wechatnikename; ?> 
						<small>
						（微信号：<?php echo $wechatnikename; ?>）
						（类型：<span><?php if($wechattype == "pri_sub") { echo '个人微信订阅号';} ?> <?php if($wechattype == "pri_svc") { echo '个人微信服务号';} ?> <?php if(($wechattype == "pub_sub") && ($wechatauth == 0)) { echo '公用微信未认证订阅号';} ?><?php if(($wechattype == "pub_sub") && ($wechatauth == 1)) { echo '公用微信认证订阅号';} ?> <?php if(($wechattype == "pub_svc") && ($wechatauth == 0)) { echo '公用微信未认证服务号';} ?><?php if(($wechattype == "pub_svc") && ($wechatauth == 1)) { echo '公用微信认证服务号';} ?>  </span>）
						（粉丝数量：<?php echo $weid_fan_count; ?>）
						</small>
					</span>
				</h5>
			</div>
			<!--wechat account manage end-->
		<?php if(($wechattype == "pri_sub")||($wechattype == "pri_svc")){?>	
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
<?php }}}
?>
<?php if(!($usergroupid !=0 && $usergroupflag == 1)){?>
<div class="alertdisplay alert alert-block" style="text-align:center;margin-top:10px;">
	<p>注：设置为共享的个人微信公众号可以设置为激活状态,作为其他微信号的共享号.第一个设置为共享号的个人微信号默认为激活状态.</p>
</div>
<?php }?>
<?php }?>
		
</html>
