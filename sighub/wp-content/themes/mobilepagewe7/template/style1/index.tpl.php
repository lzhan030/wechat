<?php defined('IN_IA') or exit('Access Denied');?><?php include template('site_header', TEMPLATE_INCLUDEPATH);?>
<?php include_once IA_ROOT . '/wp-content/themes/mobilepagewe7/template/common/slide.tpl.php';
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
background-color:<?php if(empty($_W['styles']['indexbgcolor'])) { ?>#fbf5df<?php } else { ?><?php echo $_W['styles']['indexbgcolor'];?><?php } ?>;
<?php echo $_W['styles']['indexbgextra'];?>
}
a{color:<?php echo $_W['styles']['linkcolor'];?>; text-decoration:none;}
<?php echo $_W['styles']['css'];?>
.box{width:100%;overflow:hidden;margin-top:10px;}
.box .box-item{float:left;text-align:center;display:block;text-decoration:none;outline:none;width:25%;height:90px;margin-bottom:8px;position:relative; color:#333;}
.box .box-item i{display:inline-block;width:60px;height:60px;line-height:60px;font-size:35px;color:#666; background:#EEE; overflow: hidden; border:2px #FFF solid;}
.box .box-item span{color:<?php echo $_W['styles']['fontnavcolor'];?>;display:block;font-size:14px; margin-top:-5px; position:absolute; bottom:0; width:100%;}
</style>
<div class="box">
	<?php if(is_array($navs)) { foreach($navs as $nav) { 
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($nav['url'],"http");
		if(($tmp===false)&&(!empty($nav['url']))){
			$urllink=home_url().$nav['url'];
		}else{				
			$urllink=$nav['url'];
		}?>
	<a href="<?php echo $urllink;?>" class="box-item">
		<?php if(!empty($nav['icon'])) {
			
		?>
		<i style="background:url(<?php if((empty($nav['icon']))||(stristr($nav['icon'],"http")!==false)){echo $nav['icon'];}else{echo $upload['baseurl'].$nav['icon'];}?>) no-repeat;background-size:cover;" class="img-circle"></i>
		<?php } else { ?>
		<i class="glyphicon <?php echo $nav['css']['icon']['icon'];?> img-circle" style="<?php echo $nav['css']['icon']['style'];?>"></i>
		<?php } ?>
		<span style="<?php echo $nav['css']['name'];?>"><?php echo $nav['name'];?></span>
	</a>
	<?php } } ?>
</div>
<?php //include template('footer', TEMPLATE_INCLUDEPATH);?>