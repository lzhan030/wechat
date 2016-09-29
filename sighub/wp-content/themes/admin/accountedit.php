<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

global $wpdb;


$id = $_GET['id'];
$wid = $_GET['wid'];

if( isset($_POST['user_name']) ){

   $user_name = $_POST['user_name'];
   $wechatype = $_POST['wechat_type'];
   $nikename = $_POST['wechat_nikename'];
   $url = $_POST['URL'];
   $token = $_POST['token'];
   
   //获取url中的hash字符串,这里的更新只是更新对应的hash和token两个字段
   $start = strrpos($url,'=');  //获取hash=后面的字符串
   //$length = strlen($url);
   $hash = substr($url, $start+1);
   //echo $hash;
   
   $wpdb->query( "UPDATE ".$wpdb->prefix."wechats SET wechat_nikename = '".$nikename."' WHERE wid = ".$wid." ;");
  
}
//echo $funcid;
$account = $wpdb->get_results($wpdb->prepare( "SELECT w.wid, u2.user_nicename, w.wechat_type, w.wechat_nikename, w.hash, w.token FROM ".$wpdb->prefix."wechat_usechat u1,".$wpdb->prefix."users u2,".$wpdb->prefix."wechats w WHERE u1.user_id = u2.ID AND u1.wid = w.wid and u2.ID = %d and u1.wid = %d",$id,$wid));


?>
<div>
	<form id="funcedit" action="" method="post">
	
	<div class="main-title">
		<div class="title-1">当前位置：账号列表管理 > <font class="fontpurple">账号详细信息 </font>
		</div>
	</div>
	<div class="bgimg"></div>
	<?php
		if( isset($_POST['user_name']) ){
		?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 80px;font-size: 18px;margin-left: 230px;">	提交成功!<br>
		</p>
	<?php
		} ?>
	<?php		
		foreach ($account as $accountdata) {
		    //显示的url链接
			$url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoin.php?hash='.$accountdata->hash;
			$url=preg_replace('|^https://|', 'http://', $url);
	?>
	<table width="300" height="230" border="0" cellpadding="20px" style="margin-left: 150px; margin-top:30px;" id="table2">
		<tbody>
			<tr>
				<td><label for="user_name">用户名: </label></td>
				<td width="230"><input type="text" value="<?php echo $accountdata->user_nicename; ?>" class="form-control" id="user_name" name="user_name" readonly="true"></td>
				<td></td>
			</tr>
			<tr>
				<td><label for="wechat_type">公众号类型: </label></td>
				<td><input type="text" value="<?php if($accountdata->wechat_type == 'pri_sub') echo '个人微信订阅号'; if($accountdata->wechat_type == 'pri_svc') echo '个人微信服务号'; if($accountdata->wechat_type == 'pub_sub') echo '公共微信订阅号'; if($accountdata->wechat_type == 'pub_svc') echo '公共微信服务号';?>" class="form-control" id="wechat_type" name="wechat_type" readonly="true">
				</td>
			</tr>
			<tr>
				<td><label for="wechat_nikename">微信昵称: </label></td>
				<td><input type="text" value="<?php echo $accountdata->wechat_nikename; ?>" class="form-control" id="wechat_nikename" name="wechat_nikename">
				</td>
			</tr>
			<tr>
				<td><label for="URL">URL: </label></td>
				<td><input type="text" value="<?php echo $url; ?>" class="form-control" id="URL" name="URL" readonly="true">
				</td>
			</tr>
			<tr>
				<td><label for="token">token: </label></td>
				<td><input type="text" value="<?php echo $accountdata->token; ?>" class="form-control" id="token" name="token" readonly="true"> </td>
			</tr>
			
		</tbody>
	</table>
	<?php } ?>
	<div style="margin-top:3%; margin-left:250px;">
	    <!--<input type="submit" class="btn btn-primary" value="保存" id="sub3" style="width:70px">
	    <input type="button" onclick="location.href='?admin&page=accountmanage'" class="btn btn-primary" value="取消" id="sub3" style="width:70px; margin-left:20px;">-->
		<input type="button" onclick="location.href='?admin&page=accountmanage'" class="btn btn-primary" value="返回" id="sub3" style="width:70px; margin-left:50px;">
	</div>
	</form>
</div>
<?php //} ?>