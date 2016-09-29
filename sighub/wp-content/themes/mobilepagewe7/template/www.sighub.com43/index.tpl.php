<?php defined('IN_IA') or exit('Access Denied');?><?php include template('site_header', TEMPLATE_INCLUDEPATH);
$upload =wp_upload_dir();
if(stristr($_W['styles']['indexbgimg'],"http")!==false){
	$indexbgimg=$_W['styles']['indexbgimg'];		
}else{
	$indexbgimg=$upload['baseurl'].$_W['styles']['indexbgimg'];
}
?>
<style>
body{
font:<?php echo $_W['styles']['fontsize'];?> <?php echo $_W['styles']['fontfamily'];?>;
color:<?php echo $_W['styles']['fontcolor'];?>;
padding:0;
margin:0;
background-image:url('<?php if(!empty($_W['styles']['indexbgimg'])) { ?><?php echo $indexbgimg;?><?php } ?>');
background-size:cover;
background-color:<?php if(empty($_W['styles']['indexbgcolor'])) { ?>#E9E9E9<?php } else { ?><?php echo $_W['styles']['indexbgcolor'];?><?php } ?>;
<?php echo $_W['styles']['indexbgextra'];?>
}
a{color:<?php echo $_W['styles']['linkcolor'];?>; text-decoration:none;}
<?php echo $_W['styles']['css'];?>
.box{width:100%;overflow:hidden;margin-top:10px;}
/*调整每一个box-item的宽度和显示，从而每行显示3列*/
.box .box-item{float:left;text-align:center;display:block;text-decoration:none;outline:none;width:31%;position:relative; color:#333; background:#FFF; margin:0 0 5% 2%; padding:2% 0;
-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
-moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
}
.box .box-item i{display:inline-block;width:80px;height:80px;line-height:80px;font-size:40px;color:#666; background:#EEE; overflow: hidden; border:2px #FFF solid;}
.box .box-item span{color:<?php echo $_W['styles']['fontnavcolor'];?>;display:block;font-size:14px; width:100%;}
</style>
<div class="box">
	<?php if(is_array($navs)) { foreach($navs as $nav) {
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($nav['url'],"http");
		if(($tmp===false)&&(!empty($nav['url']))){
			$urllink=home_url().$nav['url'];
		}else{				
			$urllink=$nav['url'];
		}	?>
	<a href="<?php echo $urllink;?>" class="box-item">
		<?php if(!empty($nav['icon'])) { ?>
		<i style="background:url(<?php if((empty($nav['icon']))||(stristr($nav['icon'],"http")!==false)){echo $nav['icon'];}else{echo $upload['baseurl'].$nav['icon'];}?>) no-repeat;background-size:cover;" class="img-circle"></i>
		<?php } else { ?>
		<i class="glyphicon <?php echo $nav['css']['icon']['icon'];?> img-circle" style="<?php echo $nav['css']['icon']['style'];?>"></i>
		<?php } ?>
		<span style="<?php echo $nav['css']['name'];?>"><?php echo $nav['name'];?></span>
	</a>
	<?php } } ?>
</div>
<?php //include template('footer', TEMPLATE_INCLUDEPATH);?>