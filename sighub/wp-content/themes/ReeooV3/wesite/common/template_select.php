<?php
@session_start(); 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

get_header(); 

include 'dbaccessor.php';
include 'web_constant.php';
$site_name=$_POST['site_name'];
$template_list=web_admin_list_template();
$list_template=web_admin_template_list();
$count_template=web_admin_template_count();
foreach($count_template as $ctemplate){
	$count=$ctemplate->templatecount;
}
$siteId=intval($_GET["siteId"]);
$originaltemplate_list=web_admin_list_orltemplate();
$originaltcount=web_admin_count_orgtemplate();
$newtemplate_list=web_admin_list_newtemplate();
$newcount=web_admin_count_newtemplate();
foreach($newcount as $number){
$countnumber=$number->newtempCount;
}
global $wpdb;
$dbname=$wpdb -> get_var("SELECT site_value FROM {$wpdb -> prefix}orangesitemeta WHERE `site_id`={$siteId} AND `site_key`='we7templatestyle'");
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css"/>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title></title>
	</head>
<script language='javascript'>	
	function  validateform() {
		if($("input[name='templateSelected']:checked").val() == null){
			alert("您未选择网站模板，请选择网站模板");
			return false;
		}
	    return true;
	} 
</script>	
<body>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<form  onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wesite/common/template_select_check.php?beIframe&site_name=<?php echo $site_name ?>" method="post" enctype="multipart/form-data"> 
				<div>
					<div class="main-title">
						<div class="title-1">当前位置：微官网 > 
							<font class="fontpurple">创建新站点第二步：选择网站主题></font>
						</div>
					</div>
					<div style=" margin: 30px 75px 0 60px;">
						<b><font color="red">*</font>&nbsp请选择网站模板:</b>
						<input valign ="bottom" type="submit" class="btn btn-primary" align="center" value="下一步" style="width:120px;float:right;" />
					</div>
					<div>
						<table border="0" style=" margin-left:40px; margin-top:30px;">
							<?php 
							$ocount = 0;
							$upload =wp_upload_dir();
							foreach($originaltemplate_list as $orgtemplate){
									$id=$orgtemplate->id;
									$themename=$orgtemplate->themename;
									$picUrl=$orgtemplate->picUrl;
									if((empty($picUrl))||(stristr($picUrl,"http")!==false)){
										$picUrl=$picUrl;
									}else{
										$picUrl=$upload['baseurl'].$picUrl;
									}
									
									
									$activate=$orgtemplate->activate;
							       
								   if($id==1&&$activate==1){$ocount++;
							?>
							<tr>
								<td align="center" width="200"><div height="260px" id="div_1" class="fileListCss sub04-content-1">
										<img src="<?php echo home_url()?>/wp-content/themes/ReeooV3/images/mv1.png" border="5" vspace="20" width="145px" height="225px" />
					                </div>
									<input valign="middle" align="center" type="radio" name="templateSelected" onclick="check(this.value)" align="center" value="1">列表风格主题<br />
								</td >
								<?php } else if ($id==2&&$activate==1){ $ocount++;?>
								<td align="center" width="200"><div height="300px" id="div_1" class="fileListCss sub04-content-1">
										<img src="<?php echo home_url()?>/wp-content/themes/ReeooV3/images/mv2.png" border="5" vspace="20" width="145px" height="225px" />
					                </div>
									<input valign="middle" align="center" type="radio" name="templateSelected" onclick="check(this.value)" align="center" value="2">图标风格主题<br />
								</td>
								<?php } else if ($id==3&&$activate==1){  $ocount++;?>
								<td align="center" width="200">
								<div height="300px" id="div_1" class="fileListCss sub04-content-1">
										<img src="<?php echo home_url()?>/wp-content/themes/ReeooV3/images/mv3.png" border="5" vspace="20" width="145px" height="225px" />
					                </div>
								    <input valign="middle" align="center" type="radio" name="templateSelected" onclick="check(this.value)" align="center" value="3">相册风格主题<br />
								</td>
								<?php } else if ($id==4&&$activate==1){ $ocount++;?>
								<td align="center" width="200">
								<div height="300px" id="div_1" class="fileListCss sub04-content-1">
										<img src="<?php echo home_url()?>/wp-content/themes/ReeooV3/images/mv4.png" border="5" vspace="20" width="145px" height="225px" />
					                </div>
								    <input valign="middle" align="center" type="radio" name="templateSelected" onclick="check(this.value)" align="center" value="4">视频风格主题<br />
								</td>
							</tr>
								<?php } else if ($id==5&&$activate==1){ $ocount++;?>
							<tr>
								<td align="center" width="200">
								<div height="300px" id="div_1" class="fileListCss sub04-content-1">
										<img src="<?php echo home_url()?>/wp-content/themes/ReeooV3/images/mv5.png" border="5" vspace="20" width="145px" height="225px" />
					                </div>
								    <input valign="middle" align="center" type="radio" name="templateSelected" onclick="check(this.value)" align="center" value="5">图文风格主题<br />
								</td>
								<?php } else if ($id==8&&$activate==1){ $ocount++;?>
								<td align="center" width="200">
								<div height="300px" id="div_1" class="fileListCss sub04-content-1">
										<img src="<?php echo home_url()?>/wp-content/themes/ReeooV3/images/mv1.png" border="5" vspace="20" width="145px" height="225px" />
					                </div>
								    <input valign="middle" align="center" type="radio" name="templateSelected" onclick="check(this.value)" align="center" value="8">列表风格主题(可同步)<br />
								</td>
								<?php }}?>
								<?php 
									for($cid=1;$cid<$count;$cid++)
									foreach($list_template as $list){
									echo "";
									}
								?>
								
							<?php 
								$count = 0;
								foreach($newtemplate_list as $ntemplate){
									if($ntemplate->removed == 0) {
									$count++; 
									$name=$ntemplate->name;
									$title=$ntemplate->title;
									$description=$ntemplate->description;
									if (($count-2)%4 == 1)
										echo"<tr>";
							?>
								<td align="center" width="200"><div height="260px" id="div_1" title="<?php echo $title;?>" class="fileListCss sub04-content-1">
										<img src="<?php echo home_url()?>/wp-content/themes/mobilepagewe7/template/<?php echo $name;?>/preview.jpg" border="5" vspace="20" width="145px" height="225px" />
					                </div>
									<input valign="middle" type="radio" name="templateSelected" onclick="check(this.value)" align="center" value="<?php echo $name;?>" <?php if($dbname==$name){ ?>checked="checked"<?php }?>><?php echo $title;?><br />
								</td >
								<?php
									if (($count-2)%4 == 0 || $count == $countnumber)
										echo"</tr>";
								}}
							?>
						</table>
					</div>					
				</div><!--主体结束-->
			</form>
		</div>
	</div>
</body>
<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>
