<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); ?>



<?php

	include '../common/wechat_dbaccessor.php';	
	$user_list=wechat_users_get();

?>
<style type="text/css">
table,tr,td{border:1px;}
td{height: 60px;}
</style>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>用户管理</title>
	</head>
<div id="primary" class="site-content">
	<div id="content" role="main">
		<form>
			<div class="panel panel-default" style="margin-left:20px;margin-right:50px; margin-top:30px">
				<div class="panel-heading">用户记录</div>
				<table class="table table-striped" width="800"  border="1" align="center">
							<tr>
								<th scope="col" height='40'><label>头像</label></th>
								<th scope="col" height='40'><label>用户姓名</label></th>
								<th scope="col" height='40'><label>查看消息</label></th>
								<th scope="col"><label>回复</label> </th>
							</tr>
					<?php 					
						foreach($user_list as $user){	
							echo "<tr>";
							echo "<td>";
							echo "<img style='width:40px;height: 34px;vertical-align: middle;'  src='http://wpforsae-wordpress.stor.sinaapp.com/weChatImageStorage/2.jpg' />	";	
							echo "</td>";
							echo "<td>$user->OpenId</td>";
							echo "<td>";
							echo '<a href="../message/user_message_list.php?beIframe&OpenId='.$user->OpenId.'">';
							echo "<input type='button' class='btn btn-primary' value='查看消息记录'></a>";
							echo "</td>";
							echo "<td>";
							echo '<a class="reply btn btn-primary" >回复信息</a>';
							echo "</td>";
							echo "</tr>";
						 }
					?>							 
				</table>									
			</div>	
		</form>
	</div>
	<div class="replyDisplay" style="display:none"><!--表单-->
		
		<div><a>文字平铺</a><a>图片广告</a><a>图文信息</a></div>
		<textarea id="text" class="selected" value="" style="width: 261px; height: 96px;resize: none;line-height: 20px;padding: 10px;text-align: center;font-size: 14px;" onchange="textPreview()" ></textarea>
		<input type="file"   id="pic" value="" />
										    
	</div
</div>
<style type="text/css">
.replyDisplay{position: absolute;
border: 4px solid rgb(224, 217, 217);
padding: 6px;
left: 43%;
top: 0;
width: 400px;
background: whiteSmoke;
z-index: 99;}
.replyDisplay>div{position:absolue;}
.replyDisplay>div>a{margin:5px;
display: inline-block;
width: 66px;}
.replyDisplay>div>a:hover{cursor: pointer;}
.replyDisplay>div>a:active{color:red;backgroud:blue;}
.replyDisplay>div>a.selected{top: -1px;
border-top: 3px solid #70b213;}
</style>
<script type="text/javascript">
$(document).ready(function(e){
	$(".reply").click(function(){
		$(".replyDisplay").show();
	})
	
	$("#pic").hide();
	$(".replyDisplay>div>a:nth-child(1)").click(function(){
		$("#text").show();
		$("#pic").hide();
	})
	$(".replyDisplay>div>a:nth-child(2)").click(function(){
		$("#text").hide();
		$("#pic").show();
	})
	$(".replyDisplay>div>a:nth-child(3)").click(function(){
		$("#text").show();
		$("#pic").show();
	})
	
	
	$(".replyDisplay>div>a").click(function(e){
			
			$(".replyDisplay>div>a").removeClass("selected");
			$(this).addClass("selected");
			
			
			})
})
</script>
</html>

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>