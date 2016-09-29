<?php

	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');

	get_header();?>

<?php
	
	include '../common/wechat_dbaccessor.php';	
	$openId=$_GET["OpenId"];
	$message_list=wechat_message_get($openId);

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!--<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table.css" />-->
	<link rel="pingback" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table1.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
	<title>消息管理</title>
</head>
<div id="primary" class="site-content">
	<div id="content" role="main">
		<form> 
			<div class="panel panel-default">
				<div class="panel-heading">消息记录</div>
				<table class="table table-striped" width="800"  border="1" align="center">														
							<tr>
								<th scope="col" height='40'><label>用户名</label></th>
								<th scope="col" height='40'><label>消息内容</label></th>
								<th scope="col"><label>时间</label> </th>
							</tr>
								
							<?php   foreach($message_list as $message){	
								echo "<tr>"	;
								echo "<td height='40'>$message->FromUserName</td> ";
								echo "<td height='40'>$message->Content</td> ";
								echo "<td>$message->Time</td> ";										
								echo "</tr>";
								echo "<input name='weChat_textid[]' type='hidden' id='weChat_textid' value='{$message->id}' maxlength='50' />  ";	
							 }
					  
							?>																			
				</table>				
			</div><!--主体结束-->
		</form>
	</div>
</div>
</html>