<?php

$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

require_once ('../wesite/common/dbaccessor.php');

global $current_user;
//判断是否是分组管理员中的用户
$groupadminflag = web_admin_issuperadmin($current_user->ID);
$user_id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
//$user_id =  (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;

//$weid = $_SESSION['WEID'];2014-06-24删除

//2014-06-24新增修改
$gweid = $_SESSION['GWEID'];
//2014-06-24新增修改,通过gweid和userid找到一组wid，


/* 2014-06-24删除
$getwids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." WHERE WEID = ".$weid."  AND user_id = ".$user_id);
foreach($getwids as $getwid)
{
	$wids = $getwid->wid;
} */


$selCheck=array();
if( $_SERVER['REQUEST_METHOD'] == 'POST' ){

    //2014-06-24新增修改
	$getwids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." WHERE GWEID = ".$gweid."  AND user_id = ".$user_id);
	foreach($getwids as $getwid)
	{
		$wids = $getwid->wid;
        $myrows = web_user_display_function($user_id, $wids);
		foreach($myrows as $myrow){
			$selCheck[$myrow->func_name] = 0;
		}
		
	}

    
	/* $result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE WEID =".$weid." AND func_name NOT like '%template%'  AND EXISTS(SELECT * FROM  `wp_wechat_func_info` WHERE  `func_name` =  ".$wpdb->prefix."wechat_initfunc_info.func_name AND  `status` =0 )");
	foreach($result as $initfunc){
		$selCheck[$initfunc->func_name] = $initfunc->func_flag;
	} */
	if(isset($_POST['selCheck'])){
		foreach($_POST['selCheck'] as $check)
			$selCheck[$check] = 1;
			}
	foreach($selCheck as $func_name => $func_flag){
		
		//2014-07-05新增修改
		//只要当前的功能选项对应的取值为0，不论其是从1变为0还是一直为0，对应的所有号对应的该功能都变为0
		if($func_flag == 0)
		{
		    $getwids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." WHERE GWEID = ".$gweid." AND user_id = ".$user_id);
			foreach($getwids as $getwid)
			{
				$wids = $getwid -> wid;
				$weid = $getwid -> WEID;
				$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID',$weid, $func_name, $func_flag));
				
			}
		}
		else
		{
		    //先查看下gweid中该功能选项对应的状态
		    $getflag = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." WHERE type = 'GWEID' AND value = ".$gweid." AND func_name = '".$func_name."'");
			foreach($getflag as $flaginfo)
			{
			    $gweidflag = $flaginfo -> func_flag;
			}
			//如果是从0变为1,则需要把相应开放该功能的公众号对应的都设置为1;如果是从1变为1,则不需要有任何变化
			if($gweidflag == 0)
			{
				
				//2014-07-04新增修改，只要相应的weid开放了这个功能，该功能被选中后就要更新到相应的weid中去
				$display['wechatwebsite'] = 0;
				$display['wechatfuncfirstconcern'] = 0;
				$display['wechatfunckeywordsreply'] = 0;
				$display['wechatfuncmanualreply'] = 0;
				$display['wechatfuncaccountmanage'] = 0;
				$display['wechatfuncmaterialmanage'] = 0;
				$display['wechatfuncmenumanage'] = 0;
				$display['wechatfuncusermanage'] = 0;
				$display['wechatactivity_coupon'] = 0;
				$display['wechatactivity_scratch'] = 0;
				$display['wechatactivity_fortunewheel'] = 0;
				$display['wechatactivity_toend'] = 0;
				$display['wechatactivity_fortunemachine'] = 0;
				$display['wechatfuncnokeywordsreply'] = 0;
				$display['wechatvip'] = 0;
				$display['wechatresearch'] = 0; //research new added
				$display['wechatschool'] = 0;   //wechatschool new added
				
				$getwids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." WHERE GWEID = ".$gweid." AND user_id = ".$user_id);
				foreach($getwids as $getwid)
				{
					$wids = $getwid -> wid;
					$weid = $getwid -> WEID;
					$result=web_user_display_function($user_id, $wids);
					foreach($result as $func){
						$display[$func->func_name] = $func->status;  //表示该号所开放的功能选项
					}
					if( $display[$func_name] == 1)
						$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID',$weid, $func_name, 1));
				}
			
			}
		
		}
		//2014-07-05新增修改，将所有选中的功能选项都更新到对应的gweid中去
		$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$gweid, $func_name, $func_flag));
	}
	
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
$selCheck['wechatfuncnokeywordsreply'] = 0;
$selCheck['wechatvip'] = 0;
$selCheck['wechatresearch'] = 0;
$selCheck['wechatschool'] = 0;   //wechatschool new added

/* //是否选中是通过weid，userid以及wid共同决定的
$result = web_user_display_index($weid, $user_id, $wids);
foreach($result as $initfunc){
	$selCheck[$initfunc->func_name] = $initfunc->status;
} */

//2014-06-24新增修改
//是否选中是通过gweid，userid以及wid共同决定的,是由所有的号选中的功能的并集
$getwids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." WHERE GWEID = ".$gweid."  AND user_id = ".$user_id);
foreach($getwids as $getwid)
{
	$wids = $getwid->wid;
	$result = web_user_display_index_group($gweid, $user_id, $wids);
	foreach($result as $initfunc){
	    if($selCheck[$initfunc->func_name] == 0)
			$selCheck[$initfunc->func_name] = $initfunc->status;
	}
	
}


//$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'WEID' AND value =".$weid." AND func_name like '%template%' limit 1" );
//2014-06-24新增修改
$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'GWEID' AND value =".$gweid." AND func_name like '%template%' limit 1" );
if($template === FALSE)
	$template = 'template_selno';
	
//2014-06-24初始值由0改为1
$funcDisplay['wechatwebsite'] = 0;
$funcDisplay['wechatfuncfirstconcern'] = 0;
$funcDisplay['wechatfunckeywordsreply'] = 0;
$funcDisplay['wechatfuncmanualreply'] = 0;
$funcDisplay['wechatfuncaccountmanage'] = 0;
$funcDisplay['wechatfuncmaterialmanage'] = 0;
$funcDisplay['wechatfuncmenumanage'] = 0;
$funcDisplay['wechatfuncusermanage'] = 0;
$funcDisplay['wechatactivity_coupon'] = 0;
$funcDisplay['wechatactivity_scratch'] = 0;
$funcDisplay['wechatactivity_fortunewheel'] = 0;
$funcDisplay['wechatactivity_toend'] = 0;
$funcDisplay['wechatactivity_fortunemachine'] = 0;
$funcDisplay['wechatfuncnokeywordsreply'] = 0;
$funcDisplay['wechatvip'] = 0;
$funcDisplay['wechatresearch'] = 0; //research new added
$funcDisplay['wechatschool'] = 0;   //wechatschool new added

//2014-06-24
//显示不显示是通过userid以及wid共同决定的,循环所有的wid，然后取最后的交集
$i = 0;
foreach($getwids as $getwid)
{
    $i++;
	$wids = $getwid->wid;
	$result=web_user_display_function($user_id, $wids);
	if($i == 1)
	{
	    foreach($result as $func){
			$funcDisplay[$func->func_name] = $func->status;
		}
	}	
	else
	{
		 foreach($result as $func){
		  
			//if(isset($funcDisplay[$func->func_name]) && $funcDisplay[$func->func_name] == 1)	
			if(isset($funcDisplay[$func->func_name]))				 
				$funcDisplay[$func->func_name] = 1;
			else
				$funcDisplay[$func->func_name] = 0; 
		}
	}
	 
	
	
	$getwidtype = $wpdb->get_var( "SELECT wechat_type FROM ".web_admin_get_table_name("wechats")." WHERE wid = ".$wids);
	if($getwidtype == 'pri_svc' || $getwidtype == 'pub_svc' )
	{ 
	    $usertype = 'svc';  //判断有没有添加过服务号
	}
}
/* $result=web_user_display_function($user_id, $wids);
foreach($result as $func){
		$funcDisplay[$func->func_name] = $func->status;
} */


//2014-06-24注释
//$usertype == "pri_sub" "个人订阅号去掉人工回复，菜单管理，微用户管理"
/* $widusechat = $wpdb->get_var( "SELECT wid from ".$wpdb->prefix."wechat_usechat WHERE WEID =".$weid);
$usertype = $wpdb->get_var( "SELECT wechat_type from ".$wpdb->prefix."wechats WHERE wid =".$widusechat); */
//echo $usertype;


//2014-07-04新增修改，添加相应公众号所开放的功能罗列
$wechatFuncDisplay['wechatwebsite'] = 0;
$wechatFuncDisplay['wechatfuncfirstconcern'] = 0;
$wechatFuncDisplay['wechatfunckeywordsreply'] = 0;
$wechatFuncDisplay['wechatfuncmanualreply'] = 0;
$wechatFuncDisplay['wechatfuncaccountmanage'] = 0;
$wechatFuncDisplay['wechatfuncmaterialmanage'] = 0;
$wechatFuncDisplay['wechatfuncmenumanage'] = 0;
$wechatFuncDisplay['wechatfuncusermanage'] = 0;
$wechatFuncDisplay['wechatactivity_coupon'] = 0;
$wechatFuncDisplay['wechatactivity_scratch'] = 0;
$wechatFuncDisplay['wechatactivity_fortunewheel'] = 0;
$wechatFuncDisplay['wechatactivity_toend'] = 0;
$wechatFuncDisplay['wechatactivity_fortunemachine'] = 0;
$wechatFuncDisplay['wechatfuncnokeywordsreply'] = 0;
$wechatFuncDisplay['wechatvip'] = 0;
$wechatFuncDisplay['wechatresearch'] = 0; //research new added
$wechatFuncDisplay['wechatschool'] = 0;   //wechatschool new added


$getwidcounts = $wpdb->get_results( "SELECT count(*) as widcount FROM ".web_admin_get_table_name("wechat_usechat")." WHERE GWEID = ".$gweid." AND user_id = ".$user_id);
foreach($getwidcounts as $getcount)
{
    $counts = $getcount -> widcount;
}
if($counts == 0)
    $flag = false;
else
{
    $flag = true;
	$allinfo = "";
	$getwids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." WHERE GWEID = ".$gweid." AND user_id = ".$user_id);
	foreach($getwids as $getwid)
	{
	    
		$wids = $getwid->wid;
		//当前的公众号昵称
		$widinfos = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechats")." WHERE wid = ".intval($wids));
		foreach($widinfos as $widinfo)
		{
			$widname = $widinfo -> wechat_nikename;
			$widtype = $widinfo -> wechat_type;
			if($widtype == 'pri_sub')
			   $typeinfo = '个人订阅号';
			elseif($widtype == 'pri_svc')
			   $typeinfo = '个人服务号';
			elseif($widtype == 'pub_sub')
			   $typeinfo = '公用订阅号';
			else
			   $typeinfo = '公用服务号'; 
			   
		}
		$info1 = $typeinfo.":".$widname."所开放的功能有:";
		$result=web_user_display_function($user_id, $wids);
		foreach($result as $func){
			$wechatFuncDisplay[$func -> func_name] = $func -> status;
			if($func -> status == 1)
			{
				if($func -> func_name == 'wechatwebsite')
				{
					$zhname = '微官网';
					$info = $info."  ".$zhname;
				}
				elseif($func -> func_name == 'wechatfuncfirstconcern')  
                {				
					$zhname = '首次关注';
					$info = $info."  ".$zhname;
				}
				elseif($func -> func_name == 'wechatactivity_coupon') 
                {				
					$zhname = '优惠券';
					$info = $info."  ".$zhname;
				}
				elseif($func -> func_name == 'wechatactivity_scratch')   
                {				
					$zhname = '刮刮卡';
					$info = $info."  ".$zhname;
				}
				elseif($func -> func_name == 'wechatactivity_fortunewheel')
				{				
					$zhname = '幸运大转盘';
					$info = $info."  ".$zhname;
				}
				elseif($func -> func_name == 'wechatactivity_toend')    
				{
					$zhname = '一站到底';
					$info = $info."  ".$zhname;
				}
				elseif($func -> func_name == 'wechatactivity_fortunemachine')    
				{
					$zhname = '幸运机';
					$info = $info."  ".$zhname;
				}
				elseif($func -> func_name == 'wechatfunckeywordsreply')    
				{	
					$zhname = '关键词回复';
					$info = $info."  ".$zhname;
				}
				elseif($func -> func_name == 'wechatfuncmanualreply')    
				{
					$zhname = '人工回复';	
					if(($widtype != "pri_sub")&&($widtype != "pub_sub"))
					{
					    $info = $info."  ".$zhname;
					}
					
				}
				elseif($func -> func_name == 'wechatfuncmass')  
                {				
					$zhname = '群发消息';
					$info = $info."  ".$zhname;
				}
				elseif($func -> func_name == 'wechatfuncaccountmanage')    
				{	
				    $zhname = '账户管理';	
					$info = $info."  ".$zhname;
				}
				elseif($func -> func_name == 'wechatfuncmaterialmanage')    
				{	
				    $zhname = '素材管理';	
					$info = $info."  ".$zhname;
				}					
				elseif($func -> func_name == 'wechatfuncmenumanage')    
				{
					$zhname = '菜单管理';	
					if(($widtype != "pri_sub")&&($widtype != "pub_sub"))
					{
					    $info = $info."  ".$zhname;
					}
				}					
				elseif($func -> func_name == 'wechatfuncnokeywordsreply')    
				{	
					$zhname = '无匹配回复';	
					$info = $info."  ".$zhname;
				}	
				elseif($func -> func_name == 'wechatvip')    
				{	
					$zhname = '微会员';	
					$info = $info."  ".$zhname;
				}	
				elseif($func -> func_name == 'wechatschool')    
				{	
				    $zhname = '微学校';	
					$info = $info."  ".$zhname;
				}					
				elseif($func -> func_name == 'wechatresearch')   
				{
					$zhname = '微预约';
					$info = $info."  ".$zhname;
				}	
				
			}
			
		}
		$allinfo = $info1.$info."<br/>".$allinfo;
		$info = "";
		
	}
	
}

?>
<?php
require_once ('../wesite/common/dbaccessor.php');
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/init.css">
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<title>常用功能设置</title>
		<script>  
		<?php if( $_SERVER['REQUEST_METHOD'] == 'POST' ) { ?> 
		        window.opener.location.reload();  				
		        window.opener=null;
				window.open('', '_self', '');
				window.close();  <?php } ?>
		function ObtainWeChatData()
		{
		   //alert("success!!!");
		   document.getElementById('alertopen').style.display = "block";
		}
		function check_all(obj,cName)
		{
			var checkboxs = document.getElementsByName(cName);
			for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;}
		}
		function close2(){
		    window.close();
	    }
		</script>
	</head>
	<body>
		<div class="dlg-panel panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title dlg-title">常用功能设置</h3>
			</div>
			
			<?php if($flag){?>
				<div class='alert alert-warning'>
					<?php echo $allinfo;?><br>
				</div>
			<?php }?>
			
			<form role="form" action="" method="post"> 
				<div class="i-formbox" style="min-height:180px; margin-left:10px">
					<div>
						<input type="checkbox" name="allChecked" onclick="check_all(this, 'selCheck[]')" value="true" style="margin-right:10px">全选/取消全选</input>
					</div>
					<div class="bgimg"></div>
					<div style="margin-top:2%;<?php if( !$funcDisplay['wechatwebsite']&!$funcDisplay['wechatvip']&!$funcDisplay['wechatresearch'] ) echo " display:none"; ?>">
						<div><label for="name">微官网 </label></div>
						<div>
							<div>
								<ul class="applist">
								    <?php if( $funcDisplay['wechatwebsite'] ) {?>
									<li>
										<input type="checkbox" name="selCheck[]" value="wechatwebsite" <?php if( $selCheck['wechatwebsite'] ) echo " checked"; ?> >  微官网</input>
									</li>
									<?php }  ?>
									<?php if( $funcDisplay['wechatvip'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatvip" <?php if( $selCheck['wechatvip'] ) echo " checked"; ?>>  会员管理</input></li>
									<?php }  ?>
									<?php if( $funcDisplay['wechatresearch'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatresearch" <?php if( $selCheck['wechatresearch'] ) echo " checked"; ?>>  微预约</input></li>
									<?php }  ?>
								</ul>
							</div>
						</div>
					</div>
					<!--div style="margin-top:5%; margin-left:6%;"><input type="checkbox" name="selCheck1[]" value="wechatwebsite" style="20px 10px 0px 50px">  微官网</input></div-->
					<div style="margin-top:8%;<?php if( !$funcDisplay['wechatfuncfirstconcern']&&!$funcDisplay['wechatfunckeywordsreply']&&!$funcDisplay['wechatfuncnokeywordsreply']&&!$funcDisplay['wechatfuncmanualreply']&&!$funcDisplay['wechatfuncaccountmanage']&&!$funcDisplay['wechatfuncmaterialmanage']&&!$funcDisplay['wechatfuncmenumanage']&&!$funcDisplay['wechatfuncusermanage'] ) echo " display:none"; ?>">
						<div><label for="name">微信功能 </label></div>
						<div>
							<div>
								<ul class="applist">
									<?php if( $funcDisplay['wechatfuncfirstconcern'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatfuncfirstconcern" <?php if( $selCheck['wechatfuncfirstconcern'] ) echo " checked"; ?> >  首次关注</input></li> 
									<?php } ?>
									<?php if( $funcDisplay['wechatfunckeywordsreply'] ) {?>  
									<li><input type="checkbox" name="selCheck[]" value="wechatfunckeywordsreply" <?php if( $selCheck['wechatfunckeywordsreply'] ) echo " checked"; ?> >  关键词回复</input></li>
									<?php }  ?>
									<?php if( $funcDisplay['wechatfuncnokeywordsreply'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatfuncnokeywordsreply" <?php if( $selCheck['wechatfuncnokeywordsreply'] ) echo " checked"; ?> >  无匹配回复</input></li>
									<?php }  ?>
									
									<!--2014-06-24自定义菜单和人工回复现在不能根据当前weid来决定-->
									<?php //if( ($funcDisplay['wechatfuncmanualreply'])&&($usertype != "pri_sub")&&($usertype != "pub_sub")  ) {?> 
									<?php if( ($funcDisplay['wechatfuncmanualreply']) && ($usertype == "svc") ) {?> 
									<li><input type="checkbox" name="selCheck[]" value="wechatfuncmanualreply" <?php if( $selCheck['wechatfuncmanualreply'] ) echo " checked"; ?> >  人工回复</input></li>
									<?php }  ?>
									<?php if( $funcDisplay['wechatfuncmass'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatfuncmass" <?php if( $selCheck['wechatfuncmass'] ) echo " checked"; ?> >  群发消息</input></li> 
									<?php } ?>
								</ul>
							</div>
							<div style="">
								<ul class="applist">
									<?php if( $funcDisplay['wechatfuncaccountmanage'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatfuncaccountmanage" <?php if( $selCheck['wechatfuncaccountmanage'] ) echo " checked"; ?> >  账户管理</input></li>
									<?php }  ?>
									<?php if( $funcDisplay['wechatfuncmaterialmanage'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatfuncmaterialmanage" <?php if( $selCheck['wechatfuncmaterialmanage'] ) echo " checked"; ?> >  素材管理</input></li>
									<?php }  ?>
									<!--2014-06-24自定义菜单和人工回复现在不能根据当前weid来决定-->
									<?php //if( ($funcDisplay['wechatfuncmenumanage'])&&($usertype != "pri_sub")&&($usertype != "pub_sub") ) {?> 
									<?php if( ($funcDisplay['wechatfuncmenumanage']) && ($usertype == "svc")  ) {?> 
									<li><input type="checkbox" name="selCheck[]" value="wechatfuncmenumanage" <?php if( $selCheck['wechatfuncmenumanage'] ) echo " checked"; ?> >  菜单管理</input></li>
									<?php }  ?>
									<?php if( ($funcDisplay['wechatfuncusermanage'])&&($usertype != "pri_sub")&&($usertype != "pub_sub") ) {?>
									<li style="display:none;"><input type="checkbox" name="selCheck[]" value="wechatfuncusermanage" <?php if( $selCheck['wechatfuncusermanage'] ) echo " checked"; ?> >  微用户管理</input></li>
									<?php }  ?>
									
								</ul>
							</div>
						</div>
					</div>
					<div style="margin-top:12%; <?php if( !$funcDisplay['wechatactivity_coupon']&&!$funcDisplay['wechatactivity_scratch']&&!$funcDisplay['wechatactivity_fortunewheel']&&!$funcDisplay['wechatactivity_toend']&&!$funcDisplay['wechatactivity_fortunemachine'] ) echo " display:none"; ?>">
						<div><label for="name">微活动 </label></div>
						<div>
							<div>
								<ul class="applist">
									<?php if( $funcDisplay['wechatactivity_coupon'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatactivity_coupon" <?php if( $selCheck['wechatactivity_coupon'] ) echo " checked"; ?> >  优惠券</input></li>
									<?php }  ?>
									<?php if( $funcDisplay['wechatactivity_scratch'] ) {?>  
									<li><input type="checkbox" name="selCheck[]" value="wechatactivity_scratch" <?php if( $selCheck['wechatactivity_scratch'] ) echo " checked"; ?> >  刮刮卡</input></li>
									<?php }  ?>
									<?php if( $funcDisplay['wechatactivity_fortunewheel'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatactivity_fortunewheel" <?php if( $selCheck['wechatactivity_fortunewheel'] ) echo " checked"; ?> >  幸运大转盘</input></li>
									<?php }  ?>
									<?php if( $funcDisplay['wechatactivity_toend'] ) {?> 
									<li><input type="checkbox" name="selCheck[]" value="wechatactivity_toend" <?php if( $selCheck['wechatactivity_toend'] ) echo " checked"; ?> >  一站到底</input></li>
									<?php }  ?>
									<?php if( $funcDisplay['wechatactivity_fortunemachine'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatactivity_fortunemachine" <?php if( $selCheck['wechatactivity_fortunemachine'] ) echo " checked"; ?> >  幸运机</input></li>
									<?php }  ?>
								</ul>
							</div>
						</div>
					</div>
					
					<!--wechatschool new added by Sara -->
				    <div style="margin-top:8%; margin-bottom:10%; <?php if( !$funcDisplay['wechatschool']) echo " display:none"; ?>">
						<div><label for="name">微行业 </label></div>
						<div>
							<div>
								<ul class="applist">
									<?php if( $funcDisplay['wechatschool'] ) {?>
									<li><input type="checkbox" name="selCheck[]" value="wechatschool" <?php if( $selCheck['wechatschool'] ) echo " checked"; ?>>  微学校</input></li>
									<?php }  ?>
								</ul>
							</div>
						</div>
					</div>
					
				</div>
				<!-- <div width="50" hight="10"align="right" >
					<input type="submit" class="btn btn-sm btn-primary" value="保存设置" style="width:120px; margin-bottom:30px"/>	
					<input type="cancel" class="btn btn-sm btn-default" value="取消" onclick="close2()" style="width:120px; margin: 0 20px 30px 10px"/>
				</div> -->
				<div style="margin-bottom:10px;margin-top:50px;margin-left:500px;">
					<input type="submit" class="btn btn-sm btn-primary" value="保存设置" style="width:120px;"/>	
					<input type="cancel" class="btn btn-sm btn-default" value="取消" onclick="close2()" style="width:120px;"/>
				</div>
			</form>
		</div>
	</body>	
</html>