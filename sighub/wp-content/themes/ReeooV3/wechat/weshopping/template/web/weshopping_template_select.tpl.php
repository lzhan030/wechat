<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php 
foreach($list as $temlist){
		$uid=$temlist[uid];
		$shopname=$temlist[goodsindex_name];
}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css"/>
		<title>编辑微商城</title>
	</head>
<style>  
	#left,#right   {float:left;border:0; padding:10px;}  
</style> 
<script language='javascript'>	
	function  validateform() {
		if($('#shoppingname').val()==""){
			alert("商城名称不能为空,请填写商城名称!");
			return false;
		}
		if($("input[name='templateSelected']:checked").val() == null){
			alert("您未选择微商城模板，请选择微商城模板");
			return false;
		}
		return true;
	} 
</script>	
<body>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<form  onSubmit="return validateform()" action="<?php echo $this -> createWebUrl('weshoppingsiteset',array('gweid' => $gweid));?>" method="post" enctype="multipart/form-data"> 
				<div>
					<div class="main-title">
						<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> > 
							<font class="fontpurple">微商城基本设置</font>
						</div>
					</div>
					<div style=" margin: 20px 75px 0 60px;">
						<div id="left"><label for="name"><font color="red">*</font>商城名称: </label></div>
						<div id="right"><input type="text" class="form-control" id="shoppingname" name="shoppingname" style="width:500px" value="<?php echo $shopname; ?>"/></div>
						<div style="clear:both"></div>  
					</div>
					<div style=" margin: 20px 75px 0 60px;">
						<div id="left"><label for="name"><label for="name"><font color="red">*</font>首页链接: </label></div>
						<div id="right"><input type="text" class="form-control" id="inputInfo" readonly="readonly"  style="width:500px" value="<?php echo home_url();?>/mobile.php?module=weshopping&do=list&gweid=<?php echo $gweid;?>"></div>
						<div style="clear:both"></div>  
					</div>
					<div style=" margin: 20px 75px 0 60px;">
						<div id="left"><label for="template"><font color="red">*</font>选择模板:</div>
					</div>
					<div>
						<table border="0" style=" margin-left:40px; margin-top:20px;">
							<tr>
							<?php 
							$upload =wp_upload_dir();
							foreach($template_list as $template){
									$id=$template[id];
									$templatename=$template[name];
									$picUrl=$template[thumb];
							?>
						
								<td align="center" width="200"><div height="260px" id="div_1" title="<?php echo $name;?>" class="fileListCss sub04-content-1">
										<img src="<?php echo home_url()?>/wp-content/themes/ReeooV3/wechat/weshopping/images/<?php echo $picUrl;?>" border="5" vspace="20" width="145px" height="225px" />
					                </div>
									<input valign="middle" type="radio" name="templateSelected" onclick="check(this.value)" align="center" value="<?php echo $id;?>" <?php if($uid==$id){ ?>checked="checked"<?php }?>><?php echo $templatename;?><br />
								</td>
							<?php
							}
							?>
							</tr>
						</table>
					</div>					
				</div><!--主体结束-->
				<div style="margin: 30px 100px 0 0;float:right">
					<input type="submit" class="btn btn-primary" value="下一步" style="width:120px;border-radius:0px">
				</div>
			</form>
		</div>
	</div>
</body>
</html>