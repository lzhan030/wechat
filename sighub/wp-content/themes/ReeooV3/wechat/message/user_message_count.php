<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

get_header();?>


<?php
	include '../common/wechat_dbaccessor.php';
	$OpenId=$_GET["OpenId"];

	$msg_ct=wechat_message_all();
	$msg_c=wechat_message_user_count() ;

?>

<div id="primary" class="site-content">
	<div id="content" role="main">
	<form> 
		<div><!--主体-->
		<!--主体-标题-->
			<tr>
			<tr>
			<div class="main-title">
				<div class="title-1">　当前位置：用户消息统计 > <font class="fontpurple">第二步：用户消息统计 ></font></div>
				</div>
			<!--主体-标题结束-->
				
			<!--分割线-->
				<div class="bgimg"></div>
			<!--分割线结束-->

			<!--二级导航-->
				<div class="submenu">
				</div>
			<!--二级导航结束-->

			<!--内容开始-->
				<div><!--表单-->
					<table width="300"  border="0" style=" margin-left:150px; margin-top:30px;"						
						<tr width="400">
							<td width="50" height="30">消息次数</td>						
							<td width='150'>
									<?php   foreach($msg_ct as $mct)
									{	echo "<tr>"	;					
										echo "<td height='40'><input name='weChat_openid' type='text' id='weChat_openid' value='{$mct->FromUserName}' maxlength='50' /> </td> ";
										echo "<td><input name='weChat_count' type='text' id='weChat_count' value='{$mct->count}' maxlength='50' /> </td> ";										
										echo "</tr>";
										echo "<input name='weChat_textid[]' type='hidden' id='post_ids' value='{$mct->TextId}' maxlength='50' />  ";	
									 }
							  
									?>
								
							</td>
							<td width="50" height="30">消息发送人数</td>						
							<td width='150'>
									<?php   foreach($msg_c as $mc)
									{	echo "<tr>"	;														
										echo "<td><input name='user_count' type='text' id='weChat_count' value='{$mc->ucount}' maxlength='50' /> </td> ";										
										echo "</tr>";
									 }
							  
									?>
								
							</td>
						</tr>	
									
					</table>
				</div>
			</div><!--主体结束-->
		</form>
	</div>
</div>
