<?php defined('IN_IA') or exit('Access Denied');?>
<?php include template('site_header', TEMPLATE_INCLUDEPATH);
$upload =wp_upload_dir();
if(stristr($_W['styles']['indexbgimg'],"http")!==false){
	$indexbgimg=$_W['styles']['indexbgimg'];		
}else{
	$indexbgimg=$upload['baseurl'].$_W['styles']['indexbgimg'];
}
?>
<style>
body {
	font:<?php echo $_W['styles']['fontsize'];?> <?php echo $_W['styles']['fontfamily'];?>;
	color:<?php echo $_W['styles']['fontcolor'];?>;
	padding: 0;
	margin: 0;
	background-image: url('<?php if(!empty($_W['styles']['indexbgimg'])) { ?><?php echo $indexbgimg;?><?php } ?>');
	background-size: cover;
background-color:<?php if(empty($_W['styles']['indexbgcolor'])) {
?>#E9E9E9<?php
}
else {
?><?php echo $_W['styles']['indexbgcolor'];
?><?php
}
?>;
<?php echo $_W['styles']['indexbgextra'];
?>
}
a {
	color:<?php echo $_W['styles']['linkcolor'];?>;
	text-decoration: none;
}
<?php echo $_W['styles']['css'];
?> 
.box {
	width:100%;
	overflow:hidden;
	margin-top:0px;
}
.box .box-item {
	text-align: center;
	display: block;
	text-decoration: none;
	outline: none;
	width: 100%;
	position: relative;
	color: #333;
	background: #FFF;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
	-moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
	box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
	margin:0;
	padding:0;
}
.box .box-item i {
	display: inline-block;
	width: 100%;
	line-height: 80px;
	font-size: 40px;
	color: #666;
	background: #EEE;
}
.box .box-item span {
	color:<?php echo $_W['styles']['fontnavcolor'];?>;
	display: block;
	font-size: 14px;
	font-weight:bold;
	position: absolute;
	bottom: 23px;
	width: 30%;
	opacity:0.7;  
	text-align: center;
	height: 30px;
	right: 0px;
	line-height: 30px;
}
#footer{height:0px;}
</style>
<div class="box">
  <?php if(is_array($navs)) { foreach($navs as $nav) {
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($nav['url'],"http");
		if(($tmp===false)&&(!empty($nav['url']))){
			$urllink=home_url().$nav['url'];
		}else{				
			$urllink=$nav['url'];
		}
		?>
		<a href="<?php echo $urllink;?>" class="box-item">
			<?php if(!empty($nav['icon'])) { ?>
			<i><img src="<?php if((empty($nav['icon']))||(stristr($nav['icon'],"http")!==false)){echo $nav['icon'];}else{echo $upload['baseurl'].$nav['icon'];}?>" width="100%" alt=""/></i>
			<?php } else { ?>
			<i class="glyphicon <?php echo $nav['css']['icon']['icon'];?> " style="<?php echo $nav['css']['icon']['style'];?>"></i>
			<?php } ?>
			<span style="<?php echo $nav['css']['name'];?>; <?php echo $nav['css']['menubgcolor'];?>"><?php echo $nav['name'];?></span> 
		</a>
  <?php } } ?>

  </div>
