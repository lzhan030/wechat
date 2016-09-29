<?php

	require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
	require_once './wp-content/themes/ReeooV3/wesite/common/random.php';
	require_once './wp-content/themes/admin/cgi-bin/virtual_gweid.php';
	global $wpdb;

	$id = $_GET['id'];
	$wid = $_GET['wid'];
	
	//get all groups
	$getgroupnames = $wpdb->get_results( "SELECT * FROM {$wpdb -> prefix}group order by ID ASC" );

	if( isset($_POST['user_login']) ) {

	   	$user_login = $_POST['user_login'];
	   	$user_name = $_POST['user_name'];
	   	$user_contactname = $_POST['user_contactname'];
	   	$user_email = $_POST['user_email'];
	   	$user_password = $_POST['user_password'];
	   	$user_space = $_POST['user_space'];
	   	$user_account = $_POST['user_account']; 
	   	$user_startdate = $_POST['startDate'];
	   	$user_enddate = $_POST['endDate'];
	   	$user_group = $_POST['groupselect'];
		if($user_group == 0) {
			$user_superadmin = 0;
		} else {
			$user_superadmin = $_POST['superadminflag'];
		} 
	   
	   $user_id = username_exists( $user_login );
		if ( !$user_id && email_exists($user_email) == false ) {
			$user_id = wp_create_user( $user_login, $user_password, $user_email );
			if($user_id)
			{
			   //wp_update_user( array ( 'ID' => $user_id, 'user_nicename' => $user_name ) ) ;
			   //2014-07-14新增修改
			   wp_update_user( array ( 'ID' => $user_id, 'display_name' => $user_name ) ) ;
			   update_user_meta( $user_id, "contact_name", $user_contactname, "" );
			   update_user_meta( $user_id, "useraccount", $user_account, "" );
			   update_user_meta( $user_id, "startdate", $user_startdate, "" );
			   update_user_meta( $user_id, "enddate", $user_enddate, "" );
			}
		} else {
			$user_id =0;
		}

		
		if($user_id != 0 ){

			//将用户空间写入数据库
			$wpdb->query( "UPDATE ".$wpdb->prefix."wesite_space SET defined_space = ".$user_space.", used_space = 0.00 WHERE userid = ".$user_id);
			
			//20140918将用户分组写入数据库
			$wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."user_group"."(group_id, user_id, flag)VALUES (%d, %d, %d)",$user_group, $user_id, $user_superadmin));
			
			//admin给用户添加功能项
			$selCheck['wechatwebsite'] = 0;
			$selCheck['wechatfuncfirstconcern'] = 0;
			$selCheck['wechatfunckeywordsreply'] = 0;
			$selCheck['wechatfuncmanualreply'] = 0;
			$selCheck['wechatfuncaccountmanage'] = 0;
			$selCheck['wechatfuncmaterialmanage'] = 0;
			$selCheck['wechatfuncmenumanage'] = 0;
			//$selCheck['wechatfuncusermanage'] = 0; 2014-07-15新增修改，注释
			$selCheck['wechatactivity_coupon'] = 0;
			$selCheck['wechatactivity_scratch'] = 0;
			$selCheck['wechatactivity_fortunewheel'] = 0;
			$selCheck['wechatactivity_toend'] = 0;
			$selCheck['wechatactivity_fortunemachine'] = 0;
			$selCheck['wechatactivity_egg'] = 0;  //egg module added
			$selCheck['wechatactivity_wxwall'] = 0;
			$selCheck['wechatactivity_redenvelope'] = 0;//hongbao module added
			$selCheck['wechatactivity_vote'] = 0;
			$selCheck['wechatfuncnokeywordsreply'] = 0;
			$selCheck['wechatvip'] = 0;
			$selCheck['wechatresearch'] = 0; //research new added 
			$selCheck['wechatschool'] = 0;   //wechatschool new added
			$selCheck['wechatfunceditresponse'] = 0;
			$selCheck['wepay'] = 0;
			$selCheck['wechatcuservice'] = 0;//微三方服务
			
			if(isset($_POST['selCheck'])){
				foreach($_POST['selCheck'] as $check)
					$selCheck[$check] = 1;
			}
			//2014-07-10新增修改，如果微会员没有被选中，则微学校和微预约都不能选中
			if($selCheck["wechatvip"] == 0)
			{
				$selCheck["wechatschool"] = 0;
				$selCheck["wechatresearch"] = 0;
			}
					
			foreach($selCheck as $func_name => $func_flag){
				
				//将insert into改为replace //into表示，如果该功能是新添加的，原有的数据库中是不存在该记录的，这时就可以直接insert，如果该功能是原有的，原有的数据库//中存在，这时就可以先删除原来的记录，然后再insert
				$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'userid',$user_id, $func_name, $func_flag));
				
			}
			//将选择的模板写入数据库
			$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'userid',$user_id, $_POST['templateselect'], 0));

			//group admin linked to virtual account
			if($user_superadmin == 1) {
				$virtual_rlt = virtual_gweid_exist($user_group, $user_id);
			}
		}
	}

	$selCheck['wechatwebsite'] = 0;
	$selCheck['wechatfuncfirstconcern'] = 0;
	$selCheck['wechatfunckeywordsreply'] = 0;
	$selCheck['wechatfuncmanualreply'] = 0;
	$selCheck['wechatfuncaccountmanage'] = 0;
	$selCheck['wechatfuncmaterialmanage'] = 0;
	$selCheck['wechatfuncmenumanage'] = 0;
	//$selCheck['wechatfuncusermanage'] = 0; 2014-07-15新增修改,注释
	$selCheck['wechatactivity_coupon'] = 0;
	$selCheck['wechatactivity_scratch'] = 0;
	$selCheck['wechatactivity_fortunewheel'] = 0;
	$selCheck['wechatactivity_toend'] = 0;
	$selCheck['wechatactivity_fortunemachine'] = 0;
	$selCheck['wechatfuncnokeywordsreply'] = 0;
	$selCheck['wechatactivity_egg'] = 0; //egg module added
	$selCheck['wechatactivity_redenvelope'] = 0;//hongbao module added
	$selCheck['wechatactivity_wxwall'] = 0;
	$selCheck['wechatvip'] = 0;
	$selCheck['wechatresearch'] = 0; //research new added 
	$selCheck['wechatschool'] = 0;   //wechatschool new added
	$selCheck['wechatfunceditresponse'] = 0;
	$selCheck['wepay'] = 0;
	$selCheck['weshopping'] = 0;  //weshopping new added 
	$selCheck['wechatcuservice'] = 0;//微三方服务

	$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'wid' AND value =".$wid." AND func_name NOT like '%template%' ");
	foreach($result as $initfunc){
		$selCheck[$initfunc->func_name] = $initfunc->func_flag;
	}
	//$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE WEID =".$weid." AND func_name like '%template%' limit 1" );
	$template = $wpdb->get_var( "SELECT func_name from ".$wpdb->prefix."wechat_initfunc_info WHERE type = 'wid' AND value =".$wid." AND func_name like '%template%' limit 1" );
	if($template === FALSE)
		$template = 'template_selno';
		
	$funcDisplay['wechatwebsite'] = 1;
	$funcDisplay['wechatfuncfirstconcern'] = 1;
	$funcDisplay['wechatfunckeywordsreply'] = 1;
	$funcDisplay['wechatfuncmanualreply'] = 1;
	$funcDisplay['wechatfuncaccountmanage'] = 1;
	$funcDisplay['wechatfuncmaterialmanage'] = 1;
	$funcDisplay['wechatfuncmenumanage'] = 1;
	//$funcDisplay['wechatfuncusermanage'] = 1;2014-07-15新增修改,注释
	$funcDisplay['wechatactivity_coupon'] = 1;
	$funcDisplay['wechatactivity_scratch'] = 1;
	$funcDisplay['wechatactivity_fortunewheel'] = 1;
	$funcDisplay['wechatactivity_toend'] = 1;
	$funcDisplay['wechatactivity_fortunemachine'] = 1;
	$funcDisplay['wechatfuncnokeywordsreply'] = 1;
	$funcDisplay['wechatactivity_egg'] = 1;  //egg module added
	$funcDisplay['wechatactivity_redenvelope'] = 1;//hongbao module added
	$funcDisplay['wechatactivity_wxwall'] = 1;
	$funcDisplay['wechatvip'] = 1;
	$funcDisplay['wechatresearch'] = 1;
	$funcDisplay['wechatschool'] = 1;   //wechatschool new added
	$funcDisplay['wechatfunceditresponse'] = 1; 
	$funcDisplay['wepay'] = 1; 
	$funcDisplay['weshopping'] = 1;  //weshopping new added
	$funcDisplay['wechatcuservice'] = 1;//微三方服务
	$result = $wpdb->get_results("SELECT `func_name`,`status` FROM `wp_wechat_func_info`;");
	foreach($result as $func){
		$funcDisplay[$func->func_name] = $func->status;
	}

	//查找所有分组中的分组管理员情况
	$grouparray = array();
	foreach($getgroupnames as $getgroupname){
		$groupid = $getgroupname -> ID;
		$groupname = $getgroupname -> group_name;
		$result = $wpdb->get_results("SELECT count(*) as gadmincount from ".$wpdb->prefix."user_group WHERE group_id = ".$groupid." AND flag = 1 ");
		if(!empty($result)){
			foreach($result as $gadminc){
				$groupadmincount = $gadminc->gadmincount;
			}
		}else{
			$groupadmincount = 0;
		}
		if($groupid != 0){
			$grouparray[$groupid] = $groupadmincount;	
		}
		
	}

?>

<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
<style type="text/css">
	.labeltitle{float:right;margin-right:20px;}
</style>

<div>
	<form id="useradd" action="" method="post" onsubmit="return checkinputinfo();">
	
	<div class="main-title">
		<div class="title-1">当前位置：用户管理 > <font class="fontpurple">添加用户信息 </font>
		</div>
	</div>
	<?php
		if( isset($_POST['user_login']) &&  $user_id != 0){
		?>
		<script>
		    alert("提交成功!");
			location.href='?admin&page=usermanage';
		</script>
	<?php
	} else if( isset($_POST['user_login']) &&  $user_id == 0){?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 20%;font-size: 18px;margin-left: 230px;">	用户名或邮箱已存在!<br>
		</p>
	<?php }?>
	
	<table width="520" height="350" border="0" cellpadding="20px" style="margin-left: 160px; margin-top:30px;" id="table2">
		<tbody>
			<tr>
				<td height="50px;"><label class="labeltitle">用户名:</label></td>
				<td width="380"><input type="text" value="" class="form-control" id="user_login" name="user_login"></td>
				<td></td>
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">密码: </label></td>
				<td><!--<input type="password" value="<?php //echo wp_generate_password( $length=6, $include_standard_special_chars=false ) ?>" class="form-control" id="user_password" name="user_password">-->
				<input type="password" value="" class="form-control" id="user_password" name="user_password">
				</td>
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">确认密码: </label></td>
				<td><input type="password" value="" class="form-control" id="user_confirmpassword" name="user_confirmpassword">
				</td>
			</tr>			
		    <tr>
				<td height="60px;"><label class="labeltitle">所属分组: </label></td>
				<td>
					<select name="groupselect" class="form-control" size="1" type="text;margin-left:500px;" id="theme_size" value="5" maxlength="20" onchange="selectgroup(this)">
						<?php foreach($getgroupnames as $getgroupname){
							$groupid = $getgroupname -> ID;
							$groupname = $getgroupname -> group_name;
							?>
							<option value="<?php echo $groupid;?>"><?php echo $groupname;?></option>
						<?php }?>	
					</select>
				</td>						
			</tr>
			<tr class="superadminselect">
				<td height="50px;" style="vertical-align: top;"><label class="labeltitle">角色: </label></td>
				<td>
					<select class="form-control" name="superadminflag" id="roleselect">
						<option value="0" selected="selected">普通用户</option>
						<option value="1">分组管理员</option>
					</select> 
					<div style="clear:both;display:none" class="help-block" id="havegadmin">该分组已有分组管理员</div>
				</td>						
			</tr>
			<tr>
				<td height="50px;" style="vertical-align: top;"><label class="labeltitle">显示昵称: </label></td>
				<td><input type="text" value="" class="form-control" id="user_name" name="user_name">
				<div style="clear:both" class="help-block">作为后台发表文章时的显示昵称</div>
				</td>
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">分配空间大小:</label></td>
				<td>
					<input type="text" value="" class="form-control" id="user_space" name="user_space" style="display: inline; width: 90%;"> M
				</td>
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">可建立公众号数目:</label></td>
				<td>
					<input type="text" value="" class="form-control" id="user_account" name="user_account">
				</td>
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">开始时间:</label></td>
				<td><input name="startDate" type="text" class="form-control" id="start_date" size="10" maxlength="10" onclick="new Calendar().show(this);" value="<?php echo date("Y-m-d"); ?>" /></td>
				
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">结束时间:</label></td>
				<td><input name="endDate" type="text" class="form-control" id="end_date" size="10" maxlength="10" onclick="new Calendar().show(this);"  value="<?php echo date("Y-m-d",strtotime("+3 month -1 day")) ?>"/></td>
				
			</tr>
			<tr>
				<td height="50px;"><label class="labeltitle">联系人: </label></td>
				<td width="280"><input type="text" value="" class="form-control" id="user_contactname" name="user_contactname"></td>
				<td></td>
			</tr>			
			<tr>
				<td height="50px;"><label class="labeltitle">E-mail: </label></td>
				<td><input type="text" value="" class="form-control" id="user_email" name="user_email">
				</td>
			</tr>
			
			<!--tr>
				<td>选择模板: </td>
				<td>
					<select name="templateselect" class="form-control" size="1" type="text;margin-left:500px;" id="theme_size" value="5" maxlength="20">
					    <option value="template_selno" >未选择</option>
						<option value="template_selbank" >银行</option>
						<option value="template_selschool" >学校</option>
						<option value="template_selcloth" >服装</option>
						<option value="template_selbuildmaterial" >建材</option>
						<option value="template_selrepast" >餐饮</option>
					</select>
				</td>						
			</tr-->
			
		</tbody>
	</table>
	
	<div class="alert alert-warning" style="margin-left:140px;padding-bottom:60px;width:600px;margin-top:10px;">
	<div>
		<label for="name" style="margin-left:0px; margin-top:1%;">功能选择: </label>
		<input type="checkbox" name="allChecked" onclick="check_all(this, 'selCheck[]')" value="true" style="margin-right:10px">全选/取消全选</input>
	</div>
	<div class="bgimg_warning"></div>
	<div style="margin-top: 1%; margin-left: 20px; <?php if( !$funcDisplay['wechatwebsite'] ) echo " display:none"; ?>" >
		<ul class="applist">
		<li><input type="checkbox" name="selCheck[]" id="wechatwebsite" value="wechatwebsite"
			style="" <?php if( $selCheck['wechatwebsite'] ) echo " checked"; ?>> 微官网</input></li>
		</ul>
	</div>
	<div style="margin-top: 1%; margin-left: 20px; <?php if( !$funcDisplay['wechatcuservice'] ) echo " display:none"; ?>" >
		<ul class="applist">
		<li><input type="checkbox" name="selCheck[]" id="wechatcuservice" value="wechatcuservice"
			style="" <?php if( $selCheck['wechatcuservice'] ) echo " checked"; ?>> 第三方客服</input></li>
		</ul>
	</div>
	<div style="margin-top: 8%; <?php if( !$funcDisplay['wechatfuncfirstconcern']&&!$funcDisplay['wechatfunckeywordsreply']&&!$funcDisplay['wechatfuncnokeywordsreply']&&!$funcDisplay['wechatfuncmanualreply']&&!$funcDisplay['wechatfuncaccountmanage']&&!$funcDisplay['wechatfuncaccountmanage']&&!$funcDisplay['wechatfuncmaterialmanage']&&!$funcDisplay['wechatfuncmenumanage']) echo " display:none"; ?>" >
		<div style="margin-left: 20px;">
			<label for="name">微信功能 </label>
		</div>
		<div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatfuncfirstconcern'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncfirstconcern" value="wechatfuncfirstconcern" style="" <?php if( $selCheck['wechatfuncfirstconcern'] ) echo " checked"; ?>> 首次关注 </input></li>
				<?php } ?>
				
				<?php if( $funcDisplay['wechatfunckeywordsreply'] ) {?>  
				<li><input type="checkbox" name="selCheck[]" id="wechatfunckeywordsreply" value="wechatfunckeywordsreply" style="margin-left: 5%;" <?php if( $selCheck['wechatfunckeywordsreply'] ) echo " checked"; ?>> 关键词回复</input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatfuncnokeywordsreply'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncnokeywordsreply" value="wechatfuncnokeywordsreply" style="margin-left: 5%;" <?php if( $selCheck['wechatfuncnokeywordsreply'] ) echo " checked"; ?>> 无匹配回复</input></li>
			    <?php }  ?>
				
				<?php if( $funcDisplay['wechatfunceditresponse'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatfunceditresponse" value="wechatfunceditresponse" style="margin-left: 5%;" <?php if( $selCheck['wechatfunceditresponse'] ) echo " checked"; ?>> 可编程回复</input></li>
			    <?php }  ?>
				
				<?php if( $funcDisplay['wechatfuncmanualreply'] ) {?> 
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncmanualreply" value="wechatfuncmanualreply"style="margin-left: 5%;" <?php if( $selCheck['wechatfuncmanualreply'] ) echo " checked"; ?>> 人工回复 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatfuncmass'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncmass" value="wechatfuncmass" style="" <?php if( $selCheck['wechatfuncmass'] ) echo " checked"; ?>> 群发消息 </input></li>
				<?php } ?>
			</ul>
			</div>
			<div style="margin-top: 1%; margin-left: 40px;">
			<ul class="applist">
			    <?php if( $funcDisplay['wechatfuncaccountmanage'] ) {?>
			    <li><input type="checkbox" name="selCheck[]" id="wechatfuncaccountmanage" value="wechatfuncaccountmanage"  > 账户管理</input></li>
				<?php }  ?>
				<?php if( $funcDisplay['wechatfuncmaterialmanage'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncmaterialmanage" value="wechatfuncmaterialmanage" style="margin-left: 5%;" > 素材管理 </input></li>
				<?php }  ?>
				<?php if( $funcDisplay['wechatfuncmenumanage'] ) {?> 
				<li><input type="checkbox" name="selCheck[]" id="wechatfuncmenumanage" value="wechatfuncmenumanage" style="margin-left: 5%;" > 菜单管理 </input></li>
				<?php }  ?>
				
				<!--<input type="checkbox" name="selCheck[]" value="wechatfuncusermanage" style="margin-left: 5%;" <?php //if( $selCheck['wechatfuncusermanage'] ) echo " checked"; ?>> 微用户管理</input>-->
			</ul>
			</div>
		</div>
	</div>
	<div style="margin-top: 15%; <?php if( !$funcDisplay['wechatactivity_egg']&&!$funcDisplay['wechatactivity_scratch']&&!$funcDisplay['wechatactivity_wxwall']&&!$funcDisplay['wechatactivity_redenvelope'] ) echo " display:none"; ?>" >
		<div style="margin-left: 20px;">
			<label for="name">微活动 </label>
		</div>
		<div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">
			    <!--<?php if( $funcDisplay['wechatactivity_coupon'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_coupon" value="wechatactivity_coupon" style="" <?php if( $selCheck['wechatactivity_coupon'] ) echo " checked"; ?>> 优惠券 </input></li>
				<?php }  ?>-->
				
				<?php if( $funcDisplay['wechatactivity_scratch'] ) {?>  
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_scratch" value="wechatactivity_scratch" style="margin-left: 5%;" <?php if( $selCheck['wechatactivity_scratch'] ) echo " checked"; ?>> 刮刮卡 </input></li>
				<?php }  ?>
				<?php if( $funcDisplay['wechatactivity_egg'] ) {?>  
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_egg" value="wechatactivity_egg" style="margin-left: 5%;" <?php if( $selCheck['wechatactivity_egg'] ) echo " checked"; ?>> 砸蛋 </input></li>
				<?php }  ?>
				<?php if( $funcDisplay['wechatactivity_wxwall'] ) {?>  
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_wxwall" value="wechatactivity_wxwall" style="margin-left: 5%;" <?php if( $selCheck['wechatactivity_wxwall'] ) echo " checked"; ?>> 微信墙 </input></li>
				<?php }  ?>
				<?php if( $funcDisplay['wechatactivity_redenvelope'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_redenvelope" value="wechatactivity_redenvelope" style="margin-left: 3%;" <?php if( $selCheck['wechatactivity_redenvelope'] ) echo " checked"; ?>> 微红包</input></li>
				<?php }  ?>
				
				<!--<?php if( $funcDisplay['wechatactivity_toend'] ) {?> 
                <li><input type="checkbox" name="selCheck[]" id="wechatactivity_toend" value="wechatactivity_toend" style="margin-left: 3%;" <?php if( $selCheck['wechatactivity_toend'] ) echo " checked"; ?>> 一站到底 </input></li>
				<?php }  ?>
				
				<?php if( $funcDisplay['wechatactivity_fortunemachine'] ) {?>
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_fortunemachine" value="wechatactivity_fortunemachine" style="margin-left: 3%;" <?php if( $selCheck['wechatactivity_fortunemachine'] ) echo " checked"; ?>>幸运机</input></li>
				<?php }  ?>-->
			</ul>
			</div>
			<div style="margin-top:0; margin-left: 40px;">
			<ul class="applist">				
				<?php if( $funcDisplay['wechatactivity_vote'] ) {?>  
				<li><input type="checkbox" name="selCheck[]" id="wechatactivity_vote" value="wechatactivity_vote" style="margin-left: 5%;" <?php if( $selCheck['wechatactivity_vote'] ) echo " checked"; ?>> 微投票 </input></li>
				<?php }  ?>
			</ul>
			</div>
		</div>
	</div>
	<div style="margin-top: 15%; <?php if( !$funcDisplay['wechatvip'] && !$funcDisplay['wechatresearch'] && !$funcDisplay['wepay'] && !$funcDisplay['weshopping'] ) echo " display:none"; ?>">
		<div style="margin-left: 20px;">
			<label for="name">微服务 </label>
		</div>
		<div>
		
			<div style="margin-top:0; margin-left:40px;">
			<ul class="applist">
			<?php if( $funcDisplay['wechatvip'] ) {?>
			<li><input type="checkbox" name="selCheck[]" id="wechatvip" value="wechatvip" style="" <?php if( $selCheck['wechatvip'] ) echo " checked"; ?>> 会员管理 </input></li>
			<?php }  ?>	
			
			<?php if( $funcDisplay['wechatresearch'] ) {?>
			<li><input type="checkbox" name="selCheck[]" id="wechatresearch" value="wechatresearch" style="" <?php if( $selCheck['wechatresearch'] ) echo " checked"; ?>> 微预约 </input></li>
			<?php }  ?>	
			
			<?php if( $funcDisplay['wepay'] ) {?>
			<li><input type="checkbox" name="selCheck[]" id="wepay" value="wepay" style="" <?php if( $selCheck['wepay'] ) echo " checked"; ?>> 微支付 </input></li>
			<?php }  ?>	
			<!--微商城20141224-->
			<?php if( $funcDisplay['weshopping'] ) {?>
			<li><input type="checkbox" name="selCheck[]" id="weshopping" value="weshopping" style="" <?php if( $selCheck['weshopping'] ) echo " checked"; ?>> 微商城 </input></li>
			<?php }  ?>	
			</ul>
			</div>
		
		</div>
	</div>
	
	<!--wechatschool new added by Sara -->
	<div style="margin-top: 8%; <?php if( !$funcDisplay['wechatschool'] ) echo " display:none"; ?>">
		<div style="margin-left: 20px;">
			<label for="name">微行业 </label>
		</div>
		<div>
		
			<div style="margin-top:0; margin-left:40px;">
			<ul class="applist">
			<?php if( $funcDisplay['wechatschool'] ) {?>
			<li><input type="checkbox" name="selCheck[]" id="wechatschool" value="wechatschool" style="" <?php if( $selCheck['wechatschool'] ) echo " checked"; ?>> 微学校 </input></li>
			<?php }  ?>	
			</ul>
			</div>
		
		</div>
	</div>
	
	</div>
	
	<div style="margin-top:3%; margin-left:340px; margin-bottom:50px;">
	    <input type="submit" class="btn btn-primary" value="保存" id="sub3" style="width:70px">
	    <input type="button" onclick="location.href='?admin&page=usermanage'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
		<!--<input type="button" onclick="location.href='?admin&page=accountmanage'" class="btn btn-primary" value="返回" id="sub3" style="width:70px; margin-left:50px;">-->
	</div>
	</form>
</div>

<script type="text/javascript">
	function check_all(obj,cName)
	{
		var checkboxs = document.getElementsByName(cName);
		for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;}
	}
    function checkinputinfo()
	{
	    if(document.getElementById('user_login').value == "")
		{
		    alert("用户名不能为空!");
			return false;
		}
		if(document.getElementById('user_contactname').value == "")
		{
		    alert("联系人不能为空!");
			return false;
		}
		if(document.getElementById('user_name').value == "")
		{
		    alert("显示昵称不能为空!");
			return false;
		}
		if(document.getElementById('user_email').value == "")
		{
		    alert("邮箱不能为空!");
			return false;
		}
		if(document.getElementById('user_password').value == "")
		{
		     alert("密码不能为空!");
			 return false;
		}
		if(document.getElementById('user_password').value != "")
		{
		    
		    if(document.getElementById('user_password').value.length<6)
			{
			    alert("密码长度不能小于6位字符!");
			    return false;
			}
			else
			{
				if(document.getElementById('user_password').value != document.getElementById('user_confirmpassword').value)
				{
					alert("密码和确认密码输入不一致，请重新输入!");
					return false;
				}
			}
		}
		if(document.getElementById('start_date').value > document.getElementById('end_date').value) {
			alert("结束时间必须晚于开始时间！");
			return false;
		}
		if(document.getElementById('user_email').value != "")
		{
		    var myreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
			if(!myreg.test(document.getElementById('user_email').value))
			{
				alert("您的邮箱格式不正确，请重新输入！");
				return false;
			}	
			else
			{
			    document.getElementById('useradd').submit();
				return true;
			}
		}
		
		
	}

	function selectgroup(s)
	{	
		var groupid = s[s.selectedIndex].value;
		$("#roleselect").val("0");
		if(groupid != 0) {  //如果不是默认分组
			//判断当前分组是否已经存在管理员
			$(".superadminselect").css("display","");
			<?php foreach ($grouparray as $key=>$value) {?>
				if(groupid == <?php echo $key;?>){
					var val = <?php echo $value;?>;
					if(val == 0) {
						$("#roleselect").attr("disabled", "");
						$("#havegadmin").css("display", "none");
					}
					else {
						$("#roleselect").attr("disabled", "disabled");
						$("#havegadmin").css("display", "");
					}
				}
			<?php }?>
		}else{
			$(".superadminselect").css("display","none");
		}
	}
	
	//2014-07-10新增修改，判断微学校或者微预约是否被选中，如果有任何一个选中，会员管理都需选中
	//如果会员管理取消选中，微学校和微预约都会是未选中状态
	$(function(){ 
	     $("#wechatschool").change(function() {
		     
			 if(($("#wechatschool").attr("checked")==true) && ($("#wechatvip").attr("checked")==false) )
			 {
			     $("#wechatvip").attr("checked",'checked');
				 alert("选中微学校,会员管理也需要被选中");
			 }
		 });
		 $("#wechatresearch").change(function() {
		    
			 if(($("#wechatresearch").attr("checked")==true) && ($("#wechatvip").attr("checked")==false) )
			 {
			     $("#wechatvip").attr("checked",'checked');
				 alert("选中微预约,会员管理也需要被选中");
			 }
		 });
		  $("#wechatvip").change(function() {
		     
			 if(($("#wechatvip").attr("checked")==false) && (($("#wechatresearch").attr("checked")==true) || ($("#wechatschool").attr("checked")==true)))
			 {
			    $("#wechatresearch").attr("checked",false);
				$("#wechatschool").attr("checked",false);
				alert("取消会员管理,微学校和微预约都需要取消选中");
			 }
		 });
		  $(".superadminselect").css("display","none");
		
	});
</script>