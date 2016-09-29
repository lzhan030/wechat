<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
include '../common/wechat_dbaccessor.php';
include 'keyword_permission_check.php';

//get all keywords list
$news=material_news_getlist_group($_SESSION['GWEID']);
?>
 
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table1.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	</head>
	<body>
		<div style="padding:0 30px">
		<form>
			<div class="main-title">
				<div class="title-1"><font class="fontpurple">图文列表： </font>
				</div>
			</div>
			<div class="bgimg"></div>
			<div class="submenu">
				<div class="panel panel-default" style="margin-top:30px">
					<div class="panel-heading">已建图文列表</div>
					<table class="table table-striped" width="800"  border="1" align="center">
						<tr>
							<th></th>
							<th>编号</th>
							<th>素材名称</th>
						</tr>
						<?php   
						foreach($news as $ns){
							echo "<tr>";								
							echo "<td width=50 style='text-align:center'><input type='radio' id='myCheck' name='inputCheckBox' value='".$ns->news_item_id."'/></td>";					
							echo "<td>$ns->news_item_id</td>";
							echo "<td>$ns->news_name</td>";
							echo "</tr>";
						}
						?>
					</table>
				</div>
				<div style="text-align:center">
					<input class=" btn btn-primary btmtxtbtn" type="button" value="保存" onclick="OK()"/>
					<input class="btn btn-default btmtxtbtn" type="button" value="删除" onclick="Cancle()"/>
				</div>
			</div>
		</form>
		</div>
	</body>
	<script language='javascript'>
	function OK(){
		var m=0;
		var keywordId=<?php echo $keywordId;?>;
		var aCheckBox=document.getElementsByName('inputCheckBox');
		for(var i=0; i<aCheckBox.length; i++){
			if(aCheckBox[i].getAttribute('type')=='radio'){
				if(aCheckBox[i].checked==true){
					var nid = aCheckBox[i].value;
					opener.location.href='<?php echo get_template_directory_uri(); ?>/wechat/keyword/new_keyword.php?beIframe&tab=1&news_item_id='+nid;
					m=m+1;
					window.close();	
				}
			}
		}
		if(m==0){
			alert("请先选择一个素材！");
		}
		
	}
	function Cancle(){
		var aCheckBox=document.getElementsByName('inputCheckBox');

		for(var i=0; i<aCheckBox.length; i++)
		{
			if(aCheckBox[i].getAttribute('type')=='checkbox')
			{
				aCheckBox[i].checked=false;
			}
		}
		window.opener=null;
		setTimeout("self.close()",0);
	}
	
	</script>
</html>