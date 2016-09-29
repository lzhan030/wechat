<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<body >
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=3.0, user-scalable=yes" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadexcel.css">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/common.css?v=<?php echo TIMESTAMP;?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/video.css">
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/bootstrap.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/common.js?v=<?php echo TIMESTAMP;?>"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/emotions.js"></script>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite_mobile.css">

		<style type="text/css">
			a:visited {
				color: #FF00FF
			}
			body {
			    background: #ECECEC;
				font: 16px/1.5 "Microsoft Yahei","微软雅黑",Tahoma,Arial,Helvetica,STHeiti;
			}
			
		</style>

<script>
	KindEditor.ready(function(K) {
		window.editor = K.create('#conUrl', {
		items:["emoticons","link","unlink"],
		width:'650px',
		height:'215px'}); 
		});
</script>
<style type="text/css">
	.dlg-panel{
		height:100%;
	}
</style>
  <div class="mobile-div img-rounded">
	<div class="mobile-hd">微作业 > <font class="fontpurple">家庭作业 </font></div>
	
	<div class="mobile-content">
	<h4>标题：<?php echo $work_title ?></h4>
	</div>
  </div>
  <!--测试-->
	<div class="mobile-div img-rounded">
		<div class="mobile-hd">
   <font class="">作业内容</font>
	</div>
	
	<div class="neirong">
		<table cellpadding="1%" cellspacing="1%" class="neirong-box" style="margin-left:3%;">
			<tbody>
				<tr>
				   <td id="article_content">					     
					<h4><?php echo 	$work_content ;?></h4>
					</td>
				</tr>
				<tr>
					<td style="font-size: 13px;"><i><label for="user_nikename"><font color=Red>作业截止时间: </font></label>
						<?php echo $home_starttime ?>至<?php echo $home_endtime ?></i>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<!--测试-->
	</div>


</body>
	
<script language='javascript'>

	KindEditor.ready(function(K) {
			//var editor1 = K.create('textarea[name="content1"]', {
			window.editor1 = K.create('textarea[name="content1"]', {
				cssPath : '<?php bloginfo('template_directory'); ?>/js/editor/plugins/code/prettify.css',
				uploadJson : '<?php bloginfo('template_directory'); ?>/js/editor/php/sae_upload_json.php',
				fileManagerJson : '<?php bloginfo('template_directory'); ?>/js/editor/php/sae_file_manager_json.php',
				allowFileManager : true,
				afterCreate : function() {
					var self = this;
					K.ctrl(document, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
					K.ctrl(self.edit.doc, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
				}
			});
			
			prettyPrint();
		});
	
	function my_picktrue(obj) { 
		editor1.clickToolbar("image");  
	}
	
	function close2(){
		window.opener=null;
		setTimeout("self.close()",0);
	}
</script>
</html>
<?php include $this -> template('footer');?>