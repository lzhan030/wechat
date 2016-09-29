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
require_once 'account_permission_check.php';
global $wpdb;
//$userid = $_GET['id'];
$userid = (isset($_SESSION['GWEID_matched_userid'])&&!empty($_SESSION['GWEID_matched_userid']))?$_SESSION['GWEID_matched_userid']:$current_user -> ID;
$gweid = $_SESSION['GWEID'];

if( isset($_POST['user_nicename']) &&!empty($_POST['user_nicename'])){

    $user_oldpassword = $_POST['user_oldpassword'];
	$user_newpassword = $_POST['user_newpassword'];
	
    $user_wechatname = $_POST['user_wechatname'];
	
	//新添加
	//$wechatdesp = $_POST['wechatdesp'];    //获取页面上填写的微信公众号名称字段
	
	$account = $wpdb->get_results( "SELECT w.hash, w.wechat_nikename, w.wechat_name, w.wechat_type, w.token, u1.vericode, u1.flgopen FROM ".$wpdb->prefix."wechat_usechat u1,".$wpdb->prefix."wechats w WHERE u1.wid = w.wid and u1.user_id = ".$userid." and u1.GWEID =".$gweid);
	
	if($user_oldpassword!="")
	{
		if(!is_wp_error(wp_authenticate_username_password(null,$_POST['user_nicename'], $user_oldpassword )))    //判断旧密码是否和数据库中的一致
	   {
	        $updateresult=wp_update_user(                                 //更新users表
								array ( 
									//'ID' => $_GET['id'], 
									'ID' => $userid, 
									 //'user_nicename' => $_POST['user_nicename'],
									'user_login' => $_POST['user_nicename'],		
									 'display_name' => $_POST['user_displayname'],
									 'user_email' => $_POST['user_email'],
									 'user_pass' => $_POST['user_newpassword']
									 
									) 
			) ;	
			update_user_meta( $userid, "contact_name", $_POST['user_contactname'], "" );
			if($updateresult)
				$info = "提交成功";	
			else
				$info = "提交失败";	
        }
	    else
	    {
		    $info= "旧密码输入有误，请重新填写密码修改信息";
	    }
	}
	else
	{
	    $updateresult=wp_update_user(                                 //更新users表
							array ( 
								 //'ID' => $_GET['id'], 
								 'ID' => $userid, 
								 //'user_nicename' => $_POST['user_nicename'], 
								 'user_login' => $_POST['user_nicename'],
								 'display_name' => $_POST['user_displayname'],
								 'user_email' => $_POST['user_email']
								 
								) 
			);		
		update_user_meta( $userid, "contact_name", $_POST['user_contactname'], "" );
		if($updateresult)
			$info = "提交成功";	
		else
			$info = "提交失败";
	}

	//20140918将用户分组写入数据库,用户分组不允许修改
	//$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."user_group"."(group_id, user_id)VALUES (%d, %d)",$user_group, $userid));
}


//20140925根据userid找到对应的groupid
//get all groups
$getgroupnames = $wpdb->get_results( "SELECT * FROM {$wpdb -> prefix}group order by ID ASC" );
$getgroups = $wpdb->get_results( "SELECT g1.group_id as gid FROM ".$wpdb->prefix."users u1 left join ".$wpdb->prefix."user_group g1 on u1.ID = g1.user_id left join ".$wpdb->prefix."group g2 on g1.group_id = g2.ID where u1.ID = ".$userid);
foreach($getgroups as $result)
{
	$groupid = $result->gid;
}
if(empty($groupid))
{
    $groupid = 0;
}

$user = get_userdata( $userid ); 
$user_contact_name = get_usermeta($userid,'contact_name');  

//获取开始时间和结束时间
$userstartdate = get_user_meta($userid, "startdate", true);  
$userenddate = get_user_meta($userid, "enddate", true); 
$useraccount = get_user_meta($userid, "useraccount", true);
if(empty($useraccount)) {
	$useraccount = 0; 
}

$account = $wpdb->get_results( "SELECT w.wid, w.hash, w.wechat_nikename, w.wechat_name, w.wechat_type, w.wechat_auth, w.token, w.menu_appId, w.menu_appSc, u1.WEID, u1.vericode, u1.flgopen ,u1.busi_exit,u1.prompt_content, u1.wechat_name as wechatname FROM ".$wpdb->prefix."wechat_usechat u1,".$wpdb->prefix."wechats w WHERE u1.wid = w.wid and u1.user_id = ".$userid." and u1.GWEID =".$gweid);

//获取用户空间大小
$userspace = $wpdb->get_results("SELECT * from ".$wpdb->prefix."wesite_space WHERE userid = ".$userid);
foreach($userspace as $space)
{
    $oldspace = $space->defined_space;
	$newspace = $space->used_space;
}

//20150422根据userid找到对应的groupid
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


?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>账户管理</title>
		<script>
		
			function returnlast()
			{
			   //var url="<?php echo get_template_directory_uri(); ?>";
			   var url="histroy";
			   location.href=url;
			}
		</script>
		<script>
		    function checkaccountinfo()
			{
			    
			    var username = document.getElementById('user_nicename').value;
				var contactname = document.getElementById('user_contactname').value;
				var fordisplayname = document.getElementById('user_displayname').value;
				var email =  document.getElementById('user_email').value;
				var oldpassword = document.getElementById('user_oldpassword').value;
				var newpassword = document.getElementById('user_newpassword').value;
				var confirmpassword = document.getElementById('user_confirmpassword').value;
				
				if(contactname == "")
				{
				    alert("联系人不能为空");
				}
				else if(fordisplayname == "")
				{
				    alert("显示昵称不能为空");
				}
				else if(email == "")
				{
				    alert("Email地址不能为空");
				}
				else if(email != "")
				{
				    //var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
					var myreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
					if(!myreg.test(email))
					{
					     alert("您的邮箱格式不正确，请重新输入！");
					}	
					else
					{
					    if(newpassword=="")
				        {
							if(oldpassword=="")
							{
								document.getElementById('accountedit').submit();
							}
							else
							{
								alert("新密码不能为空，不能少于6位字符");
							}
						}
						else
						{
							if(newpassword.length<6)
							{
								alert("新密码的长度不能少于6位字符");
							}
							else
							{
								if(oldpassword=="")
								{
									alert("请先输入旧密码");
								}
								else
								{
									if(confirmpassword=="")
									{
										alert("请再次输入确认密码");
									}
									else
									{
										if(confirmpassword!=newpassword)
										{
											 alert("新密码和确认密码不一致，请重新输入");
										}
										else
										{
											document.getElementById('accountedit').submit();
										}
									}
								}
							
							}
						}
				    }
				}
			
			}

			function applyAccount(id){	
				window.open('apply_account.php?beIframe&artType=post&userid='+id,'_blank','height=260,width=550,top=120,left=240,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no,titlebar=no')
		    }
			
			function applySpace(id){	
				window.open('applyspace.php?beIframe&artType=post&userid='+id,'_blank','height=520,width=500,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		    }
			

		</script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
		<style>
			table{font-size:14px;}
			label{font-weight:normal;}
		</style>
	</head>
    <div class="main_auto">
		<form id="accountedit" action="" method="post">
		
		<div class="main-title">
			<div class="title-1">当前位置：账户管理 > <font class="fontpurple">账户信息更新 </font>
			</div>
		</div>
		<div class="bgimg"></div>
		<?php
			if( isset($_POST['user_nicename'])){
			?>
			<div style="background-color:#ffffe0; border-color:#e6db55; width:95%; font-size:18px; margin-left:18px;"><p style="padding-left:0%;"><?php if(isset($info)) echo $info;?><br>
			</p></div>
			<?php
			} ?>
		<div style="width:690px;margin-left:90px;margin-top:30px;color:black;" class="alert alert-warning" role="alert">
			<div style="margin-left: 30px; margin-top:10px;" id="table2">
				<div class="ai-line">
					<div class="ai-label"><label>用户名:</label></div>
					<div class="ai-left"><input type="text" style="width:280px;"  value="<?php echo $user->user_login; ?>" class="form-control" id="user_nicename" name="user_nicename" readonly="true"></div>
				</div>	
				<div class="ai-line">
					<div class="ai-label"><label>所属分组:</label></div>
					<div class="ai-left"><select name="groupselect" style="width:280px;" readonly="true" class="form-control" size="1" type="text;margin-left:500px;" id="theme_size" value="5" maxlength="20"> 
								<?php foreach($getgroupnames as $getgroupname){
									$groupidh = $getgroupname -> ID;
									$groupname = $getgroupname -> group_name;
									?>
										<option value="<?php echo $groupidh;?>" <?php if($groupid == $groupidh){echo 'selected="selected"';}?> disabled><?php echo $groupname;?></option>
								<?php }?>	
							</select>
					</div>
				</div>	
				<div class="ai-line superadminselect">
					<div class="ai-label"><label>分组管理员:</label></div>
					<div class="ai-left">
						<div>
							<div style="float:left">
								<input type="radio" class="superadminflagok" name="superadminflag" value="1" <?php if($userflag == 1){echo 'checked = "checked" ';}?> disabled>是 
							</div>
							<div style="float:left;margin-left:60px">
								<input type="radio" class="superadminflagno" name="superadminflag" value="0" <?php if($userflag == 0){echo 'checked = "checked" ';}?> disabled>否
							</div>
						</div>
						
					</div>
				</div>
				<div class="ai-line">
					<div class="ai-label"><label>联系人:</label></div>
					<div class="ai-left"><input style="width:280px;" type="text" value="<?php echo $user_contact_name; ?>" class="form-control" id="user_contactname" name="user_contactname"></div>
				</div>	
				<div class="ai-line">
					<div class="ai-label"><label>显示昵称:</label></div>
					<div class="ai-left"><input style="width:280px;" type="text" value="<?php echo $user->display_name; ?>" class="form-control" id="user_displayname" name="user_displayname"></div>
					<div class="ai-left">(作为后台发表文章时的显示昵称)</div>
				</div>	
				<div class="ai-line">
					<div class="ai-label"><label>E-mail:</label></div>
					<div class="ai-left"><input style="width:280px;" type="text" value="<?php echo $user->user_email; ?>" class="form-control" id="user_email" name="user_email"></div>
				</div>	
				<div class="ai-line">
					<div class="ai-label"><label>开始时间:</label></div>
					<div class="ai-left"><input style="width:280px;" name="startDate" type="text" class="form-control" id="startDate" size="10" maxlength="10" value="<?php echo $userstartdate; ?>" readonly="true"/></div>
				</div>				
				<div class="ai-line">
					<div class="ai-label"><label>结束时间:</label></div>
					<div class="ai-left"><input style="width:280px;" name="endDate" type="text" class="form-control" id="endDate" size="10" maxlength="10" value="<?php echo $userenddate; ?>" readonly="true"/></div>
				</div>	
				<div class="ai-line">
					<div class="ai-label"><label>可建立公众号数目:</label></div>
					<div class="ai-left"><input style="width:280px;" type="text" name="user_account" id="user_account" class="form-control" value="<?php echo $useraccount; ?>" disabled="disabled" /></div>
					<div class="ai-left"><input type="button"  style="margin-left:20px;" class="btn btn-sm btn-warning" onClick="applyAccount('<?php echo $userid;?>')" name="account_btn" id="account_btn" value="公众号申请"></div>
				</div>	
				<div class="ai-line">
					<div class="ai-label"><label>当前空间大小:</label></div>
					<div class="ai-left"><input style="width:280px;" type="text" value="<?php echo $oldspace?$oldspace." M":"0.00"." M"; ?>" class="form-control" id="currentspace" name="currentspace"  readonly="true"></div>
					<div class="ai-left"><input type="button"  style="margin-left:20px;" class="btn btn-sm btn-warning" onClick="applySpace('<?php echo $userid;?>')" name="apply" id="buttondel" value="扩容申请"></div>
				</div>				
				<div class="ai-line">
					<div class="ai-label"><label>剩余空间大小:</label></div>
					<div class="ai-left"><input style="width:280px;" type="text" value="<?php echo number_format(($oldspace - $newspace),2,".","")."M"; ?>" class="form-control" id="remainspace" name="remainspace" readonly="true"></div>
				</div>	
				<div class="ai-line">
					<div class="ai-label"><label>旧密码:</label></div>
					<div class="ai-left"><input style="width:280px;" type="password" value="" class="form-control" id="user_oldpassword" name="user_oldpassword"></div>
				</div>	
				<div class="ai-line">
					<div class="ai-label"><label>新密码:</label></div>
					<div class="ai-left"><input style="width:280px;" type="password" value="" class="form-control" id="user_newpassword" name="user_newpassword"></div>
				</div>	
				<div class="ai-line">
					<div class="ai-label"><label>确认密码:</label></div>
					<div class="ai-left"><input style="width:280px;" type="password" value="" class="form-control" id="user_confirmpassword" name="user_confirmpassword"></div>
				</div>	
			</div>
		<div style="padding-top:30px; margin-left:180px; margin-bottom: 30px;clear:both;">
			<input type="button" onclick="checkaccountinfo();" class="btn btn-primary" value="保存" id="checkaccount" style="width:100px">
			<a href="<?php echo home_url(); ?>/index.php" target="_parent"><input type="button" class="btn btn-default" value="取消" id="sub3" style="width:100px; margin-left:20px;"></a>
		</div>
		
		</div>
		</form>
	
</div>
</html>
