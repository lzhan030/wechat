<?php
session_start();

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global $current_user;
$id = $current_user->ID;
$weid=$_SESSION['WEID'];

    //2014-06-29新增
	$getgweid = $wpdb->get_results( "SELECT gweid FROM ".$wpdb->prefix."wechat_group where user_id = 0" );
	foreach($getgweid as $gweid)
	{
	    $gweid = $gweid -> gweid;
	}

//是否点击提交事件	
if( isset($_POST['templateselect']) ){
   
    //2014-06-29新增
	$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_name = '".$_POST['templateselect']."' WHERE type = 'GWEID' AND value = ".$gweid." AND func_name like '%template%';");
   
	$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_name = '".$_POST['templateselect']."' WHERE WEID = ".$weid." AND func_name like '%template%';");  //2014-06-29这张表结构后来改过
	
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
	$selCheck['wechatvip'] = 0;
	$selCheck['wechatresearch'] = 0; //research new added
	$selCheck['wechatschool'] = 0;   //wechatschool new added
	
	$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE WEID =".$weid." AND func_name NOT like '%template%' AND EXISTS(SELECT * FROM  `wp_wechat_func_info` WHERE  `func_name` =  ".$wpdb->prefix."wechat_initfunc_info.func_name AND  `status` =0 )");//2014-06-29这张表结构后来改过
	
	//2014-06-29新增
	$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'GWEID' AND value =".$gweid." AND func_name NOT like '%template%' AND EXISTS(SELECT * FROM  `wp_wechat_func_info` WHERE  `func_name` =  ".$wpdb->prefix."wechat_initfunc_info.func_name AND  `status` =0 )");
	
	foreach($result as $initfunc){
		$selCheck[$initfunc->func_name] = $initfunc->func_flag;
	}
	if(isset($_POST['selCheck'])){
		foreach($_POST['selCheck'] as $check)
			$selCheck[$check] = 1;
			}
	foreach($selCheck as $func_name => $func_flag){
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_flag = '$func_flag' WHERE WEID = ".$weid." AND func_name='$func_name';"); //2014-06-29这张表结构后来改过
		
		//2014-06-29新增
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_flag = '$func_flag' WHERE type = 'GWEID' AND value = ".$gweid." AND func_name='$func_name';");
	}
	
}
//页面跳转至公用公众号管理页面
if(isset($_POST[openvericode])){
	$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat  SET flgopen= ".(isset($_POST['openvericode'])&&$_POST['openvericode']!=null&&$_POST['openvericode']!=''?$_POST['openvericode']:"null")." WHERE WEID = '".$weid."'");
	
	//2014-06-29新增
	$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat  SET flgopen= ".(isset($_POST['openvericode'])&&$_POST['openvericode']!=null&&$_POST['openvericode']!=''?$_POST['openvericode']:"null")." WHERE user_id = 0 AND GWEID = ".$gweid." AND WEID = '".$weid."'");
?>

<script>
   location.href = "<?php echo home_url();?>?admin&page=pubwechatmanage";
</script>

<?php
}




get_header();

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

global  $current_user;
$user_id = $current_user->ID;
//echo $user_id; 
$user = get_userdata( $user_id ); 
//echo $user->user_login;
//echo "s".$_SESSION['WEID'];
$rs=web_admin_array_selectvericode($_SESSION['WEID']);//2014-06-29还有问题???
$rs=web_admin_array_selectvericode_group($_SESSION['WEID'], $gweid);//2014-06-29还有问题???
foreach($rs as $vs)
{ $v=$vs->vericode; 
//echo "这是验证码".$v;
}
$widusechat = $wpdb->get_var( "SELECT wid from ".$wpdb->prefix."wechat_usechat WHERE WEID =".$weid);
$usertype = $wpdb->get_var( "SELECT wechat_type from ".$wpdb->prefix."wechats WHERE wid =".$widusechat);
//echo"这是".$usertype;

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/wsite.css" />
		<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/css/bootstrap.min.css">
		<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/jquery.min.js"></script>
		<script src="<?php echo home_url();?>/wp-content/themes/ReeooV3/js/bootstrap.min.js"></script>
		<title>添加公用微信号成功</title>	
	</head>
	<body>	
		<!--<div id="primary" class="site-content">-->
			<!--<div id="content" role="main">-->
			<div>
			   <div>
				<form action="" method="post" target="_parent">
					<div>
						<div class="main-title">
							<div class="title-1">当前位置：公众号添加 > <font class="fontpurple">获取公众号添加结果 </font>
							</div>
						</div>
						<div class="bgimg"></div>				
						<div>
								<div style=" margin-left:300px;margin-top:50px;height:100px; ">
									<td align="center" width="225"><font size="5" face="Verdana"><?php echo("添加公众号已成功")?></font></label></td>
								</div>
				
							<table width="450" height="30" border="0" cellpadding="20px" style=" margin-left:250px; " id="table1">
									<tr>
										<td width="150"><label for="name">您获取的验证码为：</label></td>
										<td width="50"><font class="fontpurple"><?php echo $v; ?></font></td>
				
									</tr>
									<tr>
                                        <td width="150"><label for="name">是否将验证码公开：</label></td>
										<td width="50"><input type="radio" id="openvericode" name="openvericode" value="1" checked="checked"/><span>是</span></td>
										<td width="50"><input type="radio" id="nopenvericode" name="openvericode" value="0" /><span>否</span></td>
									</tr>
									
									
							</table >
							<div style="margin-top: 30px;">
								<input type="submit" class="btn btn-primary" value="完成" style="width:120px; margin-left:400px"/>	
							</div>
						</div>
				</form>
			
			</div>
		</div>
	</body>
</html>