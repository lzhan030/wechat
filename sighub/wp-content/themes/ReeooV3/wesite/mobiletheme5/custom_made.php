<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
get_header(); ?>

<?php
    include '../common/dbaccessor.php';
	include '../common/web_constant.php';
	$siteId=$_GET["siteId"];
	$site_name = web_admin_get_site_name($siteId);
	// Get Site information. If site does not exist, use the default value. 
	$site_title = web_admin_get_site_resource($siteId, "mobilethemeTitle", $site_name);
	$site_footer = web_admin_get_site_resource($siteId, "mobilethemeFooter", "Copyright Orange@2013");	
	$site_size = web_admin_get_site_resource($siteId, "mobilethemeSize", 5);
	$site_color = web_admin_get_site_resource($siteId, "mobilethemeColor","0xFFFFFF");
	//$site_picture = web_admin_get_site_resource($siteId, "mobilethemeIsShowPic","false");
	$site_editor = web_admin_get_site_resource($siteId, "mobilethemeIsShowEditor","false");	
	$site_vipmember = web_admin_get_site_resource($siteId, "mobilethemeIsShowVipmember","false");
	$site_vipmember_editor = web_admin_get_site_resource($siteId, "mobilethemeIsShowVipmemberEditor","false");	
	$site_contact = web_admin_get_site_resource($siteId, "mobilethemeContact","");
	
	$result = web_user_display_index_groupnew_wesforsel($_SESSION['GWEID']);
	foreach($result as $initfunc){
		if($selCheck[$initfunc->func_name] == 0)
			$selCheck[$initfunc->func_name] = $initfunc->status;
	}
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/custom_yanse.js"></script>
	<!--<script src="<?php bloginfo('template_directory'); ?>/js/custom_yanse1.js"></script>-->
	<link rel="stylesheet" href="../../css/wsite.css" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<script src="../../js/bootstrap.min.js"></script>

	<title>MobileTheme主题</title>
</head>

<body>
<div id="primary" class="site-content">
	<div id="content" role="main">
		<!--<form action="<?php echo content_url(); ?>/themes/silver-blue/web_manage_theme_mobile_list_made.php?siteId=<?php echo $siteId ?>" method="post" enctype="multipart/form-data">--> 
		<form action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobiletheme5/post_list.php?beIframe<?php if(isset($_GET['isupdate'])){echo "&isupdate";} ?>&siteId=<?php echo $siteId ?>" method="post" enctype="multipart/form-data">		
			<div>
				<div class="main-title">
					<div class="title-1">当前位置：微官网 > 列表风格主题 > <font class="fontpurple"><?php if(!isset($_GET['isupdate'])){echo "创建新站点第三步：填写站点基本信息>";}else{echo "修改基本信息>";} ?>  </font>
					</div>
				</div>
				<div class="bgimg"></div>
				<div><!--表单-->
					<table width="700" height="350" border="0" style=" margin-left:100px; margin-top:10px;">
						<tr>
							<td width="300" height="50"><b><font color="red">*</font> 设置标题</b>
								<input name='theme_title' class="form-control" size='30' type='text' id='theme_title' value='<?php echo $site_title ?>' maxlength='50' /> 
							</td>
						</tr>	
						<tr>
							<td width="300" height="50"><b><font color="red">*</font> 版权信息</b>
								<input name='theme_footer' class="form-control" size='26' type='text' id='theme_footer' value='<?php echo $site_footer ?>' maxlength='50' /> 
							</td>
						</tr>	
						<tr>
							<td width="300" height="50"><b><font color="red">*</font> 首页显示条目个数</b>
							    <select name="theme_size" class="form-control" size="1" type="text;margin-left:500px;" id="theme_size" value='<?php echo $myIndex ?>' maxlength="20">
								<?php
									for($myIndex=1;$myIndex<16;$myIndex++)
									{
										if($site_size == $myIndex)
										{
											echo "<option value='".$myIndex."' selected='selected'>".$myIndex."</option>";
										}
										else
										{
											echo "<option value='".$myIndex."'>".$myIndex."</option>";
										}
									}
								?>
								</select>
							</td>							
						</tr>	
						
						<tr>
							<td width="300" height="50"><b> 留下联系方式</b>
									<input name='theme_contact' class="form-control" size='30' type='text' id='theme_contact' value='<?php echo $site_contact ?>' maxlength='50' /> 
							</td>	
						</tr>						
						
						<tr>
							<td width="300" height="50"><b><font color="red">*</font> 主题颜色(双击选择颜色)</b>
								<input name='theme_color' class="form-control" size='30' type='text' id='theme_color' value='<?php echo $site_color ?>' maxlength='50' /> 
							</td>
							<div>
								
							</div>
							<div><center>
								<table border="0" cellspacing="10" cellpadding="0" style="margin-left:120px;">
									<tr>
										<td  align="center">选中色彩
											<table  ID=ShowColor bgcolor=<?php echo $site_color ?> border="1" width="40" height="30" cellspacing="0" cellpadding="0" style="margin-bottom:20px">
												<tr><td></td></tr>
											</table>
										</td>
										<td rowspan="5">
											<div style="display:none;">基色: <SPAN ID=RGB></SPAN><br>
												<SPAN ID=GRAY>120</SPAN><br>
											</div>
										</td>
										<td>
											<table width="180" ID=ColorTable BORDER=0 CELLSPACING=0 CELLPADDING=0 style='cursor:pointer; margin-left:0px;' onMouseOut="ColorTableonmouseout()"  onMouseOver="ColorTableonmouseover()" onClick="ColorTableonclick()">
												<SCRIPT LANGUAGE=JavaScript>
													function wc(r, g, b, n)
													{
													 r = ((r * 16 + r) * 3 * (15 - n) + 0x80 * n) / 15;
													 g = ((g * 16 + g) * 3 * (15 - n) + 0x80 * n) / 15;
													 b = ((b * 16 + b) * 3 * (15 - n) + 0x80 * n) / 15;
													 document.write('<TD BGCOLOR=#' + ToHex(r) + ToHex(g) + ToHex(b) + ' height=8 width=8></TD>');
													}
													var cnum = new Array(1, 0, 0, 1, 1, 0, 0, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1, 1, 0, 0);
													  for(i = 0; i < 16; i ++)
													  {
														 document.write('<TR>');
														 for(j = 0; j < 30; j ++)
														 {
														  n1 = j % 5;
														  n2 = Math.floor(j / 5) * 3;
														  n3 = n2 + 3;
														  wc((cnum[n3] * n1 + cnum[n2] * (5 - n1)),
														   (cnum[n3 + 1] * n1 + cnum[n2 + 1] * (5 - n1)),
														   (cnum[n3 + 2] * n1 + cnum[n2 + 2] * (5 - n1)), i);
														 }
														 document.writeln('</TR>');
													}
												</SCRIPT>
											</table>
										</td>
									</tr>
									<tr>
										<!--<td width="170">
												<label class="checkbox">
													<input type="checkbox" name='theme_picture' id='theme_picture' value='true' <?php if($site_picture == "true") {echo "checked";} ?>> 
													在标题中显示图标
												</label>
										</td>-->
										<td width="200" style="<?php if($selCheck['wechatvip']!=1) echo ' display:none'; ?>">
												<label class="checkbox">
													<input type="checkbox" name='theme_vipmember' id='theme_vipmember' value='true' <?php if($site_vipmember == "true") {echo "checked";} ?>> 	
													开启页面访问会员限制
												</label>
										</td>
										<td width="170" style="<?php if($selCheck['wechatvip']!=1) echo ' display:none'; ?>">
												<label class="checkbox">
													<input type="checkbox" name='theme_vipmember_editor' id='theme_vipmember_editor' value='true' <?php if($site_vipmember_editor == "true") {echo "checked";} ?>> 
													开启评论会员限制
												</label>
										</td>
										<td>
												<label class="checkbox">
													<input type="checkbox" name='theme_editor' id='theme_editor' value='true' <?php if($site_editor == "true") {echo "checked";} ?>> 
													允许用户评论
												</label>
										</td>
									</tr>								
									<tr></tr>
								</table>
							</center></div>
						</tr>									
						<tr>
							<td width="150"></td>
							<td width="150" height="20">
								<input type="submit" class="btn btn-primary" value="下一步" style="margin-left:320px; margin-top:30px; width:120px;"/>	
							</td>
						</tr>										
					</table>
				</div>
			</div><!--主体结束-->
		</form>		
			
	</div>
</div>
</body>
</html>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
