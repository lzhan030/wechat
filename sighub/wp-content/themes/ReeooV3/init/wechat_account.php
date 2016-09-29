<?php

	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');
	require_once ('../wesite/common/dbaccessor.php');
	require_once ('../wechat/common/wechat_dbaccessor.php');
	//get_header(); 
	global  $current_user;
	//判断是否是分组管理员中的用户
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$user_id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	//$user_id =  (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	$user = get_userdata($user_id );  
	//active function 2014-08-18
	$activeinfo=getWechatGroupActiveInfo($user_id,2);
	$isactive=count($activeinfo);
	
	//第三方客服url，如果是共享号则获取激活号的第三方客户设置
	if(is_array($activeinfo)){
		foreach($activeinfo as $watinfo){
			$gweidforurl=$watinfo->GWEID;
			$cuservicethirdurl=$wpdb->get_var( $wpdb -> prepare("SELECT wechat_cuservice FROM {$wpdb->prefix}wechat_usechat where GWEID=%d",intval($gweidforurl)));
		}
	}
		
	$useraccount = get_user_meta($user_id, "useraccount", true);
	$wechat_count = getWechatGroup_count($user_id);
	foreach($wechat_count as $w) {
		$wechat_number = $w->wechatCount;
	}
	//2014-07-16新增修改    
	$weid = $_GET['weid'];
	//mashan 添加是否公布验证码的标识
	// $ov =$_POST['openvericode'];
	if(isset($_POST['openvericode'])){
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat  SET flgopen= ".(isset($_POST['openvericode'])&&$_POST['openvericode']!=null&&$_POST['openvericode']!=''?$_POST['openvericode']:"null")." WHERE WEID = '".$weid."'");
	}
	
	//微三方服务判断
	$funcDisplay['wechatcuservice'] = 1;
	$result = $wpdb->get_results("SELECT `func_name`,`status` FROM `wp_wechat_func_info`;");
	foreach($result as $func){
		$funcDisplay[$func->func_name] = $func->status;
	}

	$selCheck['wechatcuservice'] = 0;//第三方客服服务
	$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wechat_func_info a WHERE EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$user_id." AND func_flag = 1) LIMIT 0, 100");
	foreach($result as $initfunc){
		$selCheck[$initfunc->func_name] = $initfunc->status;
	}
	//微三方服务判断end
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
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
		</style>
			
		<title>初始化</title>
		<script>
			
			function selWechatType(type)
			{
			    if(type=='pri_sub')
			    {
			        document.getElementById('table1').style.display="block";
				    document.getElementById('table2').style.display="none";
					document.getElementById('table3').style.display="none";
				    document.getElementById('table4').style.display="none";
					document.getElementById('commondiv').style.display="none";
					document.getElementById('table6').style.display="block";
				    document.getElementById('table5').style.display="none";
					document.getElementById('table7').style.display="block";
					document.getElementById('table8').style.display="none";
					document.getElementById('table9').style.display="none";
					document.getElementById('table10').style.display="none";
					$('input:radio:first').attr('checked', 'checked');  //设置第一个radio为选中的值
			    }
			    if(type=='pri_svc')
			    {
			        document.getElementById('table1').style.display="none";
				    document.getElementById('table2').style.display="block";
					document.getElementById('table3').style.display="none";
				    document.getElementById('table4').style.display="none";
					document.getElementById('commondiv').style.display="none";
					document.getElementById('table6').style.display="none";
				    document.getElementById('table5').style.display="none";
				    document.getElementById('table7').style.display="block";
					document.getElementById('table8').style.display="none";
					document.getElementById('table9').style.display="none";
					document.getElementById('table10').style.display="none";
			    }
				if(type=='pub_subnrz')
				{   
					<?php 
					   $pubtype = 'pub_sub';
					   $pubauth = 0;
					   $pubsub = web_admin_get_wechat_pubnew($pubtype,$pubauth);
					?>
					document.getElementById('table1').style.display="none";
				    document.getElementById('table2').style.display="none";
					document.getElementById('table3').style.display="block";
				    document.getElementById('table4').style.display="none";
					document.getElementById('commondiv').style.display="none";
					document.getElementById('table6').style.display="none";
				    document.getElementById('table5').style.display="none";
					document.getElementById('table7').style.display="block";
					document.getElementById('table8').style.display="none";
					document.getElementById('table9').style.display="none";
					document.getElementById('table10').style.display="block";
					
				}
				if(type=='pub_subrz')
				{   
					<?php 
					   $pubtype = 'pub_sub';
					   $pubauth = 1;
					   $pubsubrz = web_admin_get_wechat_pubnew($pubtype,$pubauth);
					?>
					document.getElementById('table1').style.display="none";
				    document.getElementById('table2').style.display="none";
					document.getElementById('table3').style.display="none";
				    document.getElementById('table4').style.display="none";
					document.getElementById('commondiv').style.display="block";
					//menuiframe.window.location.reload();
					document.getElementById('table6').style.display="none";
				    document.getElementById('table5').style.display="none";
					document.getElementById('table7').style.display="block";
					document.getElementById('table8').style.display="block";
					document.getElementById('table9').style.display="none";
					document.getElementById('table10').style.display="block";
				}
				if(type=='pub_svcnrz')
				{
				  
					<?php
					   $pubtype = 'pub_svc';
					   $pubauth = 0;
					   $pubsvc = web_admin_get_wechat_pubnew($pubtype,$pubauth);
					?>
					document.getElementById('table1').style.display="none";
				    document.getElementById('table2').style.display="none";
					document.getElementById('table3').style.display="none";
				    document.getElementById('table4').style.display="block";
					document.getElementById('commondiv').style.display="block";
					//menuiframe.window.location.reload();
					document.getElementById('table6').style.display="none";
				    document.getElementById('table5').style.display="none";
					document.getElementById('table7').style.display="block";
					document.getElementById('table8').style.display="none";
					document.getElementById('table9').style.display="none";
					document.getElementById('table10').style.display="block";
				}
				if(type=='pub_svcrz')
				{
				  
					<?php
					   $pubtype = 'pub_svc';
					   $pubauth = 1;
					   $pubsvcrz = web_admin_get_wechat_pubnew($pubtype,$pubauth);
					?>
					document.getElementById('table1').style.display="none";
				    document.getElementById('table2').style.display="none";
					document.getElementById('table3').style.display="none";
				    document.getElementById('table4').style.display="none";
					document.getElementById('commondiv').style.display="block";
					//menuiframe.window.location.reload();
					document.getElementById('table6').style.display="none";
				    document.getElementById('table5').style.display="none";
					document.getElementById('table7').style.display="block";
					document.getElementById('table8').style.display="none";
					document.getElementById('table9').style.display="block";
					document.getElementById('table10').style.display="block";
				}
				
				//2014-12-09 shanshan添加公用公众号判断,当下拉出来的公用公众号为空时，"下一步"按钮不起作用
				submitStatus = new Object();
				submitStatus["pri_sub"] = "true";
				submitStatus["pub_subnrz"] = "<?php if(!empty($pubsub)) echo "true" ; else echo "false" ?>";
				submitStatus["pub_subrz"] = "<?php if(!empty($pubsubrz)) echo "true" ; else echo "false" ?>";
				submitStatus["pub_svcnrz"] = "<?php if(!empty($pubsvc)) echo "true" ; else echo "false" ?>";
				submitStatus["pub_svcrz"] = "<?php if(!empty($pubsvcrz)) echo "true" ; else echo "false" ?>";
				
				$('#sub3').attr('disabled',false);
				if(submitStatus[$('#wechattype').val()] == "false")
					$('.suba').attr('disabled','disabled');
				else
					$('.suba').removeAttr('disabled');
			}
		   
		    function checkmenuinfo()
		    {
		        var nicename1=document.getElementById("nicename1").value;
		        var menuappid=document.getElementById("appid").value;
			    var menuappsc=document.getElementById("appsc").value;
			   
			    if(nicename1=='')
			    {
			     alert("请输入您的微信昵称");
			    }
			    else
			    {
			        if(!((menuappid!='')&&(menuappsc!='')))
			        {
			         alert("您没有输入menuappid和menuappsc，将没有自定义菜单这个功能");
			        }
			   
			        document.getElementById('accountform').submit();
			    }
			   
		    }
		   
		  function checknicename()
		  {
		     var nicename=document.getElementById("nicename").value;
			 if(nicename=='')
			 {
			     alert("请输入您的微信昵称");
			 }
			 else
			 {
			    document.getElementById('accountform').submit();
			 }
		  }
		  
		  function checknicename1()
		  {
		     var nicename=document.getElementById("nicenamem").value;
			 var menuappid=document.getElementById("appid1").value;
			 var menuappsc=document.getElementById("appsc1").value;
			   
			 if(nicename=='')
			 {
			     alert("请输入您的微信昵称");
			 }
			 else
			 {
				if(!((menuappid!='')&&(menuappsc!='')))
				{
				 alert("您没有输入menuappid和menuappsc，将没有自定义菜单这个功能");
				}
		   
				document.getElementById('accountform').submit();
			 }
		  }
		  function checkexireply(isactive)
		  {
		     var Shares=document.getElementsByName("share");
			 for(var i=0;i<Shares.length;i++){
                    if(Shares[i].checked){
                        isShare = Shares[i].value; 
                        break;
                    }
            }
			
			var busexit=document.getElementById("busexit").value;
			var exireply=document.getElementsByName("exireply")[1];
			var exireply_content=document.getElementById("exireply_content").value;
			
			if((isShare=='1')&&(isactive=='0'))	{
				alert("公用微信号在没有设置激活微信号之前不能设置为共享");
			}else if(busexit==''){
				alert("请输入退出关注商家关键词");
			}else if(exireply_content==''){
					alert("请输入退出关注商家时的回复信息");
			}else{		   
					document.getElementById('accountform').submit();
				}

		  }
		  function hint(isactive){
				if(isactive==0){
					alert("该微信号是第一个设置为共享的微信号，同时默认设置为激活的微信号");
				}
				
				url='<? echo $cuservicethirdurl ?>';
				$("#cuservicethird_url").val(url);
				$("#cuservicethird_url").attr("readonly","readonly");	
				
			}
			function nocheck(){
				$("#cuservicethird_url").val("");
				$("#cuservicethird_url").removeAttr("readonly");					
			}
		
		  $(function(){ 
				$("#authok").click(function(){   
				   
				   document.getElementById('table6').style.display="none";
				   document.getElementById('table5').style.display="block";
				   document.getElementById('table8').style.display="none";
				   document.getElementById('table9').style.display="none";
				   
				});
				$("#authnok").click(function(){   
				   
				   document.getElementById('table6').style.display="block";
				   document.getElementById('table5').style.display="none";
				   document.getElementById('table8').style.display="none";
				   document.getElementById('table9').style.display="none";
				   
				});	
				
				//如果是post（apply_success post到该页面）过来的，则执行下面的if条件
				<?php if( 'POST' == $_SERVER['REQUEST_METHOD'])
				{?>
					//parent.location.reload();reload方法会引起firefox出现resend现象
				    parent.location.href="<?php echo get_bloginfo('template_directory')?>/../ReeooV3/init/wechat_account_list.php";
				<?php }?>
				
				
			}); 
		</script>
	
	</head>
	<body>	
		<div id="primary" class="site-content">
			<div id="content" role="main">
				<form id="accountform" action="<?php echo constant("CONF_THEME_DIR"); ?>/init/wechat_config.php?beIframe" method="post" enctype="multipart/form-data"> 
					<div>
						<?php if(($useraccount <= $wechat_number)||empty($useraccount)) {?>
							<div class="alert alert-warning" role="alert" style="margin-top:20px;">您建立的公众号数量已经达到限定值，请在账号管理里申请修改公众号创建数量限制！</div>
						<?php }?>
						<div>
							<table width="500" height="30" border="0" cellpadding="20px" style=" margin-left:40px; margin-top:15px;">
								<div>
									<tr>
										<td width="225"><label for="name">请选择微信公众号类型: </label></td>
										<td>
											<select name="wechattype" class="form-control" size="1" type="text;" id="wechattype" value="5" maxlength="20"
											onchange="selWechatType(this.options[this.selectedIndex].value)">
												<option value="pri_sub" selected="selected">个人微信订阅号</option>
												<option value="pri_svc">个人微信服务号</option> 
												<option value="pub_subnrz">公用微信未认证订阅号</option>
												<option value="pub_subrz">公用微信认证订阅号</option>
												<option value="pub_svcnrz">公用微信未认证服务号</option>
												<option value="pub_svcrz">公用微信认证服务号</option>
											</select>
										</td>						
									</tr>
								</div>	
							</table >		
							<table width="500" height="300" border="0" cellpadding="20px" style=" margin-left:40px; margin-top: -5px;" id="table7">	
							<div>
							     
									<tr>
										<td width="215"><label for="name">是否共享: </label></td>
										<td width="285">
											<input type="radio" id="shareok" name="share" value="0" checked="checked" onclick="nocheck()" /><span>否</span>
											<input type="radio" id="sharenok" name="share" value="1" style="margin-left:40px;" onclick="hint('<?php echo $isactive ?>')" /><span>是</span>							
										<?php if($isactive=='0'){?> <input type="hidden"  name="active" value="2" />
										<?php }?>
										
										</td>								
									</tr>
									
									<tr>
										<td width="225"><label for="name">请输入站点名称: </label></td>
										<td width="225"><input type="text" class="form-control" id="wechatname" name="wechatname"/></td>
											
									</tr>
									
									<tr>
										<td width="225"><label for="name">请上传图片: </label></td>
										<td><img id="pic" href="javascript:void(0)" height='90' width='90'/>
										<a id='picurl' href='#' onclick='delImage()' style="display:none;">删除图片</a></td> 
										<input name='delimgid' type='hidden' id='delimg_id' value='-1'/>
									</tr>
									<tr><td></td><td><input type="file" class="form-control" id="file" name="file" onchange="previewImage(this)"/></td></tr> 
									<tr>
										<td><label for="fans">初始化粉丝数量: </label></td>
										<td><input type="text" class="form-control" id="wechatfans" name="wechatfans" /></td>
									</tr>
								</div>
							</table >
							
							<table width="500" height="50" border="0" cellpadding="20px" style=" margin-left:40px; margin-top: 80px; <?php if($funcDisplay['wechatcuservice'] && $selCheck['wechatcuservice']){ ?> display:block; <?php }else{ ?> display:none; <?php }?>"  id="tableserv">	
								<div>
									
									<!--功能开启即显示第三方客服设置-->
									<tr>
										<td width="225"><label for="name">第三方客服url设置: </label></td>
										<td><div style="padding-top:40px;"><input type="text"  class="form-control" id="cuservicethird_url" name="cuservicethird_url" style="margin-left: -5px;width: 102%;">
										<span>处理后:url?(&)token=****（请以http://或https://开头）</span></div></td>
									</tr>
									<!--功能开启即显示第三方客服设置END-->
								</div>
							</table >
							
							<table width="500" height="60" border="0" cellpadding="20px" style=" margin-left:40px; margin-top: 100px;" id="table1">
							       <!--20140623newaddedbegin-->
							        <tr>
										<td width="225"><label for="name">认证情况: </label></td>
										<td width="">
											<input type="radio" id="authnok" name="auth" value="0" 
											checked="checked" /><span>未认证</span>
											<input type="radio" id="authok" name="auth" value="1" style="margin-left:15px;"/><span>已认证</span>
										</td>								
									</tr>
									<!--20140623newaddedend-->
							</table >	
							<table width="600" height="60" border="0" cellpadding="20px" style=" margin-left:40px; margin-top:25px;" id="table6">								
									<tr>
										<td width="225"><label for="name">请输入微信昵称: </label></td>
										<td width="225"><input type="text" class="form-control" id="nicename" name="site_name"/></td>
                                        <td><input type="button" onclick="checknicename();" class="btn btn-primary subb" value="下一步" id="sub3"
										style="width:70px" <?php if(($useraccount <= $wechat_number)||empty($useraccount)) { echo 'disabled="disabled"';} ?> /></td>										
									</tr>
									
							</table >
							<table width="600" height="100" border="0" cellpadding="20px" style=" margin-left:40px; margin-top:25px; display:none;" id="table5" >
							<!--2014-07-07newadded-->
									<tr>
										<td width="225"><label for="name">微信菜单AppId: </label></td>
										<td width="275"><input type="text" class="form-control" id="appid1" name="menu_appId1"  style="width:188px;"/></td>
														
									</tr>
									<tr>
										<td width="225"><label for="name">微信菜单AppSecret: </label></td>
										<td><input type="text" class="form-control" id="appsc1" name="menu_appSc1"  style="width:188px;"/></td>
																
									</tr>
									<tr>
										<td width="225"><label for="name">请输入微信昵称: </label></td>
										<td width="225"><input type="text" class="form-control" id="nicenamem" name="site_namem"/></td>
                                        <td><input type="button" onclick="checknicename1();" class="btn btn-primary subc" value="下一步" id="sub3"
										style="width:70px" /></td>										
									</tr>
							</table >
								
							<table width="600" height="200" border="0" cellpadding="20px" style=" margin-left:40px; margin-top: 100px; display:none;" id="table2">
								    <!--20140623newaddedbegin-->
								   <tr>
										<td ><label for="name">认证情况: </label></td>
										<td width="">
											<input type="radio" id="authnokfw" name="authfw" value="0" checked="checked" style="margin-left:48px;"/><span>未认证</span>
											<input type="radio" id="authokfw" name="authfw" value="1" style="margin-left:15px;"/><span>已认证</span>
										</td>								
									</tr>
									<!--20140623newaddedend-->
									
									<tr>
										<td><label for="name">请输入微信昵称: </label></td>
										<td><input type="text" class="form-control" id="nicename1" name="site_name1"  style="width:188px; margin-left:48px;"/></td>														
									</tr>
									<tr>
										<td><label for="name">微信菜单AppId: </label></td>
										<td><input type="text" class="form-control" id="appid" name="menu_appId"  style="width:188px; margin-left:48px;"/></td>
														
									</tr>
									<tr>
										<td><label for="name">微信菜单AppSecret: </label></td>
										<td><input type="text" class="form-control" id="appsc" name="menu_appSc"  style="width:188px; margin-left:48px;"/></td>
										<td><input type="button" onclick="checkmenuinfo();" class="btn btn-primary" value="下一步" id="sub4"
										style="width:70px" <?php if(($useraccount <= $wechat_number)||empty($useraccount)) { echo 'disabled="disabled"';} ?> /></td>						
									</tr>
												
							</table>
							
							<table width="600" height="30" border="0" cellpadding="20px" style=" margin-left:40px; margin-top:100px;display:none;" id="table3">
									<tr>
										<td width="225"><label for="name">请选择公共未认证订阅号: </label></td>
										<td width="225">
										
										<select name="wechatpubsub" class="form-control" size="1" type="text;" id="theme_size" value="5" maxlength="20" onchange="">
										    <?php foreach($pubsub as $pubsublist){?>
												<option value="<?php echo $pubsublist->wid;?>" selected="selected"><?php echo $pubsublist->wechat_nikename;?></option>
											<?php }?>
												
										</select>
										
										</td>							
									</tr>
									
							</table >
							
							<table width="600" height="30" border="0" cellpadding="20px" style=" margin-left:40px; display:none; margin-top:100px;" id="table8">
									<tr>
										<td width="225"><label for="name">请选择公共认证订阅号: </label></td>
										<td width="225">
										
										<select name="wechatpubsubrz" class="form-control" size="1" type="text;" id="theme_size1" value="5" maxlength="20" onchange="">
										    <?php foreach($pubsubrz as $pubsublist){?>
												<option value="<?php echo $pubsublist->wid;?>" selected="selected"><?php echo $pubsublist->wechat_nikename;?></option>
											<?php }?>
												
										</select>
										
										</td>							
									</tr>
									
							</table >
							
							<table width="600" height="30" border="0" cellpadding="20px" style=" margin-left:40px; display:none; margin-top:100px;" id="table4">
									<tr>
										<td width="225"><label for="name">请选择公共未认证服务号: </label></td>
										<td width="225">
										<select name="wechatpubsvc" class="form-control" size="1" type="text;" id="theme_size2" value="5" maxlength="20" onchange="">
										    <?php foreach($pubsvc as $pubsvclist){?>
												<option value="<?php echo $pubsvclist->wid;?>" selected="selected"><?php echo $pubsvclist->wechat_nikename;?></option>
											<?php }?>
												
										</select>
										
										</td>							
									</tr>
									
							</table >
							
							<table width="600" height="30" border="0" cellpadding="20px" style=" margin-left:40px; display:none; margin-top:100px;" id="table9">
									<tr>
										<td width="225"><label for="name">请选择公共认证服务号: </label></td>
										<td width="225">
										<select name="wechatpubsvcrz" class="form-control" size="1" type="text;" id="theme_size3" value="5" maxlength="20" onchange="">
										    <?php foreach($pubsvcrz as $pubsvclist){?>
												<option value="<?php echo $pubsvclist->wid;?>" selected="selected"><?php echo $pubsvclist->wechat_nikename;?></option>
											<?php }?>
												
										</select>
										
										</td>							
									</tr>
									
							</table >
							<table width="600" height="30" border="0" cellpadding="20px" style=" margin-left:40px; display:none; margin-top:30px; " id="table10">
									<!--20140623newaddedend-->
									<tr>
										<td><label for="name">请输入退出关注商家关键词: </label></td>
										<td width="188px" style="margin-left:-125px">
											<input type="text" class="form-control" id="busexit" name="busexit"style="width:189px; margin-left:-127px;"/>
										
										</td>
									</tr>								
									<tr>
										<td><label for="name">请填写退出商家时的回复信息: </label></td>
										<td width="188px" style="margin-left:-125px">
										<textarea id="exireply_content" name="exireply_content"  class="form-control" style="width:390px;height:80px;margin-left:-127px;" ></textarea>
										</td>
									</tr>
									<tr height="60px">
										<td width="225">
											<input type="button" onclick="checkexireply('<?php echo $isactive;?>');" class="btn btn-primary suba" value="下一步" id="sub3" 
											style="margin-left:240px;width:70px" <?php if(($useraccount <= $wechat_number)||empty($useraccount)) { echo 'disabled="disabled"';} ?> /></td>
									</tr>
							</table >
						</div>
			</form>
			
				
			<div id="commondiv" style="display:none;margin-top: 220px;">
				 <iframe frameborder="0" id="menuiframe" name="menuiframe" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/init/displaymenu.php?beIframe" width="100%" height="600" scrolling="auto"></iframe>
			</div>
			</div>
		</div>

	</body>
<script>
	selWechatType(document.getElementById('wechattype').options[document.getElementById('wechattype').selectedIndex].value); 
	function previewImage(file){  
		$("#picurl").show();
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
</script>
</html>

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>