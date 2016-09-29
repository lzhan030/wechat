<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
require_once ('../wesite/common/upload.php');
require_once '../wesite/common/dbaccessor.php';
require_once '../wechat/common/wechat_dbaccessor.php';
require_once '../wechat/common/jostudio.wechatmenu.php';  
require_once 'account_permission_check.php';
global $wpdb;
$userid = intval($_GET['userid']);
//$gweid = $_SESSION['GWEID'];
$gweid = intval($_GET['gweid']);
$weid = intval($_GET['weid']);
$wid = intval($_GET['wid']);
$delimgid = $_POST['delimgid'];//该值为-1，表示删除图片


//微三方服务判断
$funcDisplay['wechatcuservice'] = 1;
$result = $wpdb->get_results("SELECT `func_name`,`status` FROM `wp_wechat_func_info`;");
foreach($result as $func){
	$funcDisplay[$func->func_name] = $func->status;
}

$selCheck['wechatcuservice'] = 0;//第三方客服服务
$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wechat_func_info a WHERE EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) LIMIT 0, 100");
foreach($result as $initfunc){
	$selCheck[$initfunc->func_name] = $initfunc->status;
}
//微三方服务判断END	

if( isset($_POST['accountupdate']) &&!empty($_POST['accountupdate']))
{

    //$wid = intval($_POST['accountupdate']);
	//$auth = "wechat_auth".$wid;
	//$vericode = "vericodeopen".$wid;
    $user_wechatname = $_POST['user_wechatname'];
    $wechat_auth = $_POST['wechat_auth'];
	$wechatdesp = $_POST['wechatdesp'];    //获取页面上填写的微信公众号名称字段
	$wechat_vericode = $_POST['user_vericode'];
	$wechat_busexit = $_POST['busexit'];
	$wechat_exireply_content = $_POST['exireply_content'];
	$wechat_vericodeopen = $_POST['vericodeopen'];
	$wechatmenuappid = trim($_POST['menuappId']);
	$wechatmenuappsc = trim($_POST['menuappSc']);
	
	//第三方客服设置入库
	if(isset($_POST['cuservicethird_url'])){
		$cuservicepost=trim($_POST['cuservicethird_url']);
	}else{
		$cuservicepost="";
	}
	
	$menuflag = true;
	/*上传图片*/ //add20141208
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
	//2014-07-09新增修改
	$weid = intval($_POST['wechatweid']);
	
	$getresult = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."wechats WHERE wid = ".$wid);
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
			//新添加
			//更新微信公众号名称字段
			//$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat SET wechat_name = '".$wechatdesp."' WHERE WEID = ".$weid." AND user_id = ".$userid." AND wid = ".$wid." AND GWEID = ".$gweid." ;");
			$data = array(
				'wechat_name' => $wechatdesp,
				);
			if(!empty($picUrl))
				$data['wechat_imgurl'] = $picUrl;
			if($delimgid !=-1)
				$data['wechat_imgurl'] = "";
			$wpdb->update($wpdb->prefix.'wechat_usechat', $data, array('WEID' =>$weid,'user_id' =>$userid,'wid' =>$wid,'GWEID' =>$gweid));			
			//更新验证码和是否公布验证码字段
			if(($wechattype == 'pub_sub')||($wechattype == 'pub_svc'))
			{
			   $wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat SET vericode = '".$wechat_vericode."', flgopen =".$wechat_vericodeopen.",busi_exit='".$wechat_busexit."',prompt_content='".$wechat_exireply_content."' WHERE WEID =".$weid." AND GWEID = ".$gweid." AND user_id = ".$userid." and wid =".$wid." ;");
			}
			$info = "提交成功";	

		}
	}
	else
	{
	    $submitflag = true;
		//更新微信昵称等字段
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_nikename = '".$user_wechatname."' WHERE wid = ".$wid." ;");		
		//新添加
		//更新微信公众号名称字段
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
		//只对个人公众号的认证情况进行更新,公共的只有管理员可以改
		
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_auth = '".$wechat_auth."' WHERE wid = ".$wid." ;");
		
		//更新自身就是微信公众号(认证的个人订阅号和个人服务号)的menuappid和menuappsc
		if(($wechat_auth == 1 && $wechattype == "pri_sub") || $wechattype == "pri_svc")
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
			include '../wechat/common/menu_update_forwechat.php';
		
		}
		
		
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
			include '../wechat/common/menu_update_forwechat.php';
			
		}
		
		//更新验证码和是否公布验证码字段
		
		$info = "提交成功";	
	}
	
	//第三方客服入库
	$data = array(
		'wechat_cuservice' => $cuservicepost
	);
	
	$wpdb->update($wpdb->prefix.'wechat_usechat', $data, array('WEID' =>$weid,'user_id' =>$userid,'wid' =>$wid,'GWEID' =>$gweid));
	
	
}
$user = get_userdata( $userid );  
$account = $wpdb->get_results( "SELECT w.wid, w.hash, w.wechat_nikename, w.wechat_name, w.wechat_type, w.wechat_auth, w.token, w.menu_appId, w.menu_appSc, u1.WEID, u1.vericode, u1.flgopen ,u1.busi_exit,u1.prompt_content, u1.wechat_name as wechatname, u1.wechat_fan_init,u1.wechat_imgurl,u1.wechat_cuservice FROM ".$wpdb->prefix."wechat_usechat u1,".$wpdb->prefix."wechats w WHERE u1.wid = w.wid and u1.WEID = ".$weid." and u1.user_id = ".$userid." and u1.GWEID =".$gweid." and w.wid = ".$wid);
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>公众号编辑</title>
		<script>
		
			//对于个人订阅号点击已认证，出现menuappid和menuappsc
			$(function(){  
				/* $(".rzradio").click(function(){  
                   	alert("here");		
				    var val;
					var seltable = "";  
					alert($(this));
					alert($(this).attr("checked"));
					if($(this).attr("checked")){              
						val = $(this).attr("value");
                        valattri = $(this).attr("name");	
						alert("1:"+val);
						alert("2:"+seltable);
					}
					if(val == 1)
					{
					   //将获取的指定的某一个wechat框中的menuid和menusc显示出来
					   $(".showmenuinfo").css("display","block"); 
				    }
					else
					{
					    //$("#table3").css("display","none");
						$(".showmenuinfo").css("display","none"); 						
					}
				}); */
				
				$("#authnokc").click(function(){   
				   
				   document.getElementById('table3').style.display="none";
				   
				});
				$("#authokc").click(function(){   
				   
				   document.getElementById('table3').style.display="block";
				
				});
				
			});
		   
			function returnlast()
			{
			   //var url="<?php echo get_template_directory_uri(); ?>";
			   var url="histroy";
			   location.href=url;
			}
		</script>
		<script>
		    
			//判断是否为空
			function checknull(weid,wechattype,wechatauth)
			{
			    
				var wechatname = document.getElementById("user_wechatname").value;
			    //判断微信昵称、验证码不能为空
               
				//如果是公用的微信号才需要判断该字段是否为空
				if((wechattype == 'pub_sub')||(wechattype == 'pub_svc'))
		        {
			        var vericode = document.getElementById('user_vericode').value;
					var busexit = document.getElementById('busexit').value;
					var exireply_content = document.getElementById('exireply_content').value;
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
						    var menuappid = document.getElementById('menuappId').value;
							var menuappsc = document.getElementById('menuappSc').value;
							if(!((menuappid!='')&&(menuappsc!='')))
							{
								alert("您没有输入微信菜单appid和微信菜单appsc，将没有自定义菜单这个功能");
							}
						}
						if(wechattype == 'pri_sub' && wechatauth == '0')
						{
						    if(document.getElementById("table3").style.display ==='block')
							{	
								var menuappid1 = document.getElementById('menuappId1').value;
								var menuappsc1 = document.getElementById('menuappSc1').value;
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
			function applySpace(id){	
			
				window.open('applyspace.php?beIframe&artType=post&userid='+id,'_blank','height=520,width=500,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		
		    }
			<?php if(isset($info)){?>
				top.refersh_account_list();
			<?php }?>
			
		</script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
		<style>
			table{font-size:14px;}
			.ae-line{
			    line-height: 40px; clear: both;
			}
			.ae-label{
				float: left; width: 210px;
			}
			.ae-left{
				float: left;
			}
		</style>
	</head>
	<div style="width:90%;margin:20px 0 0 10px;" class="main_auto alert alert-info" role="alert">
		<div class="main-title">
			<div class="title-1"><font style="color:#3a87ad;margin-left:20px;">公众号信息更新 </font>
			</div>
		</div>
		<div class="bgimg"></div>
		
		<?php
		if( isset($_POST['accountupdate'])){
		?>
		<div style="background-color:#ffffe0; border-color:#e6db55; width:95%; font-size:18px; margin-left:18px;"><p style="padding-left:0%;"><?php if(isset($info)) echo $info;?><br>
		</p></div>
		<?php
		} ?>

		<?php foreach($account as $useraccount){
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
			
			//三方服务
			//共享后会员gweid是共享号的，所以此处要与刮刮卡等生产的链接gweid一样。bootstrap功能处理中会根据处理后的gweid判断是否虚拟号gweid会员，此处不需要
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
		<form id="accountupdaten" action="" method="post" onsubmit="return checknull('<?php echo $wechatweid;?>','<?php echo $wechattype;?>','<?php echo $wechatauth;?>');" enctype="multipart/form-data">
			<input type="hidden" value="<?php echo $wechatweid; ?>" id="wechatweid" name="wechatweid">
			<input type="hidden" value="<?php echo $id; ?>" id="wechatwid" name="wechatwid">
			<div style="margin-left: 3%; margin-top:0px;" id="table2">
				<div class="ae-line" >
					<div class="ae-label" ><label>微信昵称:</label></div>
					<div class="ae-left" ><input style="width: 480px;" type="text" value="<?php echo $wechatnikename; ?>" class="form-control" id="user_wechatname" name="user_wechatname" <?php if($wechattype == "pub_sub" || $wechattype == "pub_svc" || $wechatweid == 0) { echo 'readonly="true"';}?>></div>
				</div>
				<div class="ae-line" >
					<div class="ae-label" ><label>初始化粉丝数量:</label></div>
					<div class="ae-left" ><input style="width: 480px;" type="text" value="<?php echo $wechat_init_fan;?>" class="form-control" id="fan_init" name="fan_init" readonly="true" /></div>
				</div>
				<div class="ae-line" >
					<div class="ae-label" ><label>认证情况:</label></div>
					<div class="ae-left" >
						<input type="radio" class="rzradio" id="authnokc" name="wechat_auth" value="0" <?php if($wechatauth == 0){ echo 'checked="checked"';}if($wechattype == "pub_sub" || $wechattype == "pub_svc" || ($wechattype == "pri_sub" && $wechatauth == 1)){ echo 'disabled="disabled"';} ?> style="margin-left:0px;"/><span>未认证</span>
						<input type="radio" class="rzradio" id="authokc" name="wechat_auth" value="1" <?php if($wechatauth == 1){ echo 'checked="checked"';}if($wechattype == "pub_sub" || $wechattype == "pub_svc"){ echo 'disabled="disabled"';}   ?> style="margin-left:15px;"/><span>已认证</span>
					</div>
				</div>
			
			</div>
			
			<?php if($wechattype == "pri_sub"){?>
			<div style="margin-left: 3%; margin-top:0px; display:none;" id="table3" class="showmenuinfo">
				<div class="ae-line" >
					<div class="ae-label" ><label>微信菜单AppId:</label></div>
					<div class="ae-left" ><input style="width: 480px;"  type="text" value="" class="form-control" id="menuappId1" name="menuappId1"></div>
				</div>
				<div class="ae-line" >
					<div class="ae-label" ><label>微信菜单AppSecret:</label></div>
					<div class="ae-left" ><input style="width: 480px;"  type="text" value="" class="form-control" id="menuappSc1" name="menuappSc1"></div>
				</div>
			</div>
			
			<?php }?>
			
			
			<div style="margin-left: 3%; margin-top:0px;" id="table2">
				<div class="ae-line" >
					<div class="ae-label" ><label>微信号类别:</label></div>
					<div class="ae-left" >
						<select name="user_wechattype" class="form-control" size="1" type="text;" style="width: 480px;" id="user_wechattype" value="5" maxlength="20" readonly="true">
							<option value="pri_sub" <?php if($wechattype == "pri_sub") { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >个人微信订阅号</option>
							<option value="pri_svc" <?php if($wechattype == "pri_svc") { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >个人微信服务号</option>
							<option value="pub_sub" <?php if(($wechattype == "pub_sub") && ($wechatauth == 0)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信未认证订阅号</option>
							<option value="pub_sub" <?php if(($wechattype == "pub_sub") && ($wechatauth == 1)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信认证订阅号</option>
							<option value="pub_svc" <?php if(($wechattype == "pub_svc") && ($wechatauth == 0)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信未认证服务号</option>
							<option value="pub_svc" <?php if(($wechattype == "pub_svc") && ($wechatauth == 1)) { echo 'selected="selected"';}else{ echo 'disabled'; } ?> >公用微信认证服务号</option>
						</select>
					</div>
				</div>
				
				<?php if(($wechattype == "pub_sub")||($wechattype == "pub_svc")){?>
				<div class="ae-line" >
					<div class="ae-label" ><label>验证码:</label></div>
					<div class="ae-left" >
						<input style="width: 480px;"  type="text" value="<?php echo $vericode; ?>" class="form-control" id="user_vericode" name="user_vericode">
					</div>
				</div>
				<div class="ae-line" >
					<div class="ae-label" ><label>验证码是否显示在用户列表中:</label></div>
					<div class="ae-left" >
						<input type="radio" name="vericodeopen" value="1"  <?php if($flgopen == 1) echo 'checked="checked"';?>>是 <input type="radio" name="vericodeopen" value="0"  <?php if($flgopen == 0) echo 'checked="checked"';?> style="margin-left:25px;">否
					</div>
				</div>
				<div class="ae-line" >
					<div class="ae-label" ><label>请输入退出关注商家关键词:</label></div>
					<div class="ae-left">
						<input style="width: 480px;"  type="text" class="form-control" id="busexit" name="busexit"style="margin-left:0px;" value="<?php echo $busexit; ?>"/>
					</div>
				</div>
				<div class="ae-line" >
					<div class="ae-label" ><label>请填写退出商家时的回复信息:</label></div>
					<div class="ae-left" >
						<textarea style="width: 480px;"  id="exireply_content" name="exireply_content"  class="form-control" style="height:80px;margin-left:0px;"><?php echo $exireply_content; ?></textarea>
					</div>
				</div>
				
				<?php }?>
				
				<?php if((($wechattype == "pri_sub")||($wechattype == "pri_svc")) && ( $wechatweid != 0) ){?>
				<div class="ae-line" >
					<div class="ae-label" ><label>URL:</label></div>
					<div class="ae-left" >
						<input style="width: 480px;"  type="text" value="<?php echo $url; ?>" class="form-control" id="URL" name="URL"  readonly="true">
					</div>
				</div>
				<div class="ae-line" >
					<div class="ae-label" ><label>Token:</label></div>
					<div class="ae-left" >
						<input style="width: 480px;" type="text" value="<?php echo $token; ?>" class="form-control" id="Token" name="Token" readonly="true">
					</div>
				</div>
				
				<?php }?>
				
				<!--功能开启即显示三方服务设置-->
				<?php if( $funcDisplay['wechatcuservice'] && $selCheck['wechatcuservice']){ ?>
				
				<div class="ae-line" style="padding-top:6px;">
					<div class="ae-label" ><label>第三方客服url设置:</label></div>
					<div class="ae-left" >
						<input style="width: 370px;float:left" type="text" value="<?php echo empty($cuservicethirdurl)?"":$cuservicethirdurl; ?>" class="form-control" name="cuservicethird_url" <?php if($gweid!=$disgweid){ ?> readonly="true" <?php } ?>>&(?)token=xxx
					</div>
				</div>
				
				<div class="ae-line">
					<div class="ae-label" ><label></label></div>
					<div class="ae-left"  style="width:480px">
						<p style="font-size:11px;line-height:20px">范例：若输入http://ip:port/ocsfront/ocsfront/html5/webChat.jsp?channel=xxx, 则系统将生成:http://ip:port/ocsfront/ocsfront/html5/webChat.jsp?channel=xxx&token=xxx
						<p style="font-size:11px;line-height:20px">注：url必须以http或https开头</p>
					</div>
				</div>
				
				<div class="ae-line" >
					<div class="ae-label" ><label>第三方客服url:</label></div>
					<div class="ae-left" >
						<input style="width: 480px;" type="text" value="<?php echo $cuserviceurl; ?>" class="form-control" name="cuserviceurl" readonly="true">
					</div>
				</div>
				
				<div class="ae-line" >
					<div class="ae-label" ><label></label></div>
					<div class="ae-left" style="width:480px;height:37px;">
						<input type="button" class="btn btn-success btn-sm" onclick="ExportThirdServiceExcel('<?php echo $disgweid ?>')" value="第三方客服用户手机统计下载" />
					</div>
				</div>
				
				<?php }?>
				<!--功能开启即显示三方服务设置END-->
				
				<?php if((($wechattype == "pri_sub" && $wechatauth == 1)||($wechattype == "pri_svc")) && ( $wechatweid != 0) ){?>
				<div class="ae-line" >
					<div class="ae-label" ><label>微信菜单AppId:</label></div>
					<div class="ae-left" >
						<input style="width: 480px;" type="text" value="<?php echo $wechatmenuappid; ?>" class="form-control" id="menuappId" name="menuappId">
					</div>
				</div>
				<div class="ae-line" >
					<div class="ae-label" ><label>微信菜单AppSecret:</label></div>
					<div class="ae-left" >
						<input style="width: 480px;" type="text" value="<?php echo $wechatmenuappsc; ?>" class="form-control" id="menuappSc" name="menuappSc">
					</div>
				</div>
				
				<?php }?>
				
				<div class="ae-line" style="padding-top:10px;">
					<div class="ae-label" ><label>微信站点名称:</label></div>
					<div class="ae-left" >
						<input style="width: 480px;" type="text" value="<?php echo $wechatname; ?>" class="form-control" id="wechatdesp" name="wechatdesp" >
					</div>
				</div>
				<div class="ae-line" >
					<div class="ae-label"><label>请上传图片:</label></div>
					<div class="ae-left" >
						<?php if(!empty($wechatimagurl)) { $upload =wp_upload_dir(); $upwechatimagurl=$upload['baseurl'].$wechatimagurl;?>
						<img id="pic" src="<?php echo $upwechatimagurl; ?>"  height='90' width='90'/>
						<?php } else { ?>
						<img id="pic" href="javascript:void(0)"  height='90' width='90'/>
						<?php } ?>
					</div>
					<div class="ae-left" style="line-height:80px;margin-left:10px;">
						<a id='picurl' href='#' onclick='delImage()' style="display:none;" >删除图片</a></td>
						<input name='delimgid' type='hidden' id='delimg_id' value='-1'/>
					</div>
				</div>
					<div class="ae-line" >
					<div class="ae-label" ><label></label></div>
					<div class="ae-left" >
						<input style="width: 480px;margin-top:5px;" type="file" class="form-control" id="file" name="file" onchange="previewImage(this)"/>
					</div>
				</div>
				<div style="padding-top:30px; margin-left:210px; margin-bottom: 30px;clear:both;">
					<button type="submit" class="btn btn-sm btn-warning" name="accountupdate" id="buttondelck" value="<?php echo $id;?>" style="width:70px;font-size:13px;">更新</button>
					<button type="button" style="margin-left:20px;width:70px;font-size:13px;" class="btn btn-sm btn-default" name="accountdelete" id="buttondel" onClick="location.href='<?php echo home_url();?>/wp-content/themes/ReeooV3/init/wechat_accountinfo.php?beIframe&no_user_select=1&userid=<?php echo $userid;?>'">返回</button>
				</div>	
			</div>
		</form>
		<?php }?>
</div>
<script>
	function previewImage(file){  
		$("#picurl").show();
		document.getElementById("delimg_id").value="-1";   //是否更新图片
		var picsrc = document.getElementById('pic');  
		if (file.files && file.files[0]) {//chrome   
				var reader = new FileReader();
				reader.readAsDataURL(file.files[0]);  
				reader.onload = function(ev){
				picsrc.src = ev.target.result;
				$("#pic").show();
			}   
		
		}  else{
			//IE下，使用滤镜 出现问题
			picsrc.style.maxwidth="50px";
			picsrc.style.maxheight = "12px";
			picsrc.style.overflow="hidden";
			var picUpload = document.getElementById('file'); 
			picUpload.select();
			var imgSrc = document.selection.createRange().text;  
			picsrc.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
			picsrc.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\""+imgSrc+"\")";
		}                    
	}  
	function delImage(){	  
		$("#pic").attr('src',""); 
		$("#picurl").hide();
		document.getElementById("delimg_id").value="";
		document.getElementById("file").value=""; //清空file input的内容

	}
	
	//下载手机统计信息
	function ExportThirdServiceExcel(disgweid){
		window.location.href='<?php echo home_url();?>/module.php?module=webchat&do=exportData&gweid='+disgweid;		
	}
	
	
	$(function(){ 
    <?php 
	if(!empty($wechatimagurl)){
	?>
		$("#picurl").show();
	<?php }?> 
	}); 
</script>
</html>
