<?php include template('site_header', TEMPLATE_INCLUDEPATH);
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
background-image:url('<?php if(empty($_W['styles']['indexbgimg'])) { ?><?php echo $template_url;?>/images/bg_index.jpg<?php } else { ?><?php echo $indexbgimg;?><?php } ?>');
background-size:cover;
background-color:<?php if(empty($_W['styles']['indexbgcolor'])) { ?>#F9F9F9<?php } else { ?><?php echo $_W['styles']['indexbgcolor'];?><?php } ?>;
<?php echo $_W['styles']['indexbgextra'];?>
}
a{color:<?php echo $_W['styles']['linkcolor'];?>; text-decoration:none;}
<?php echo $_W['styles']['css'];?>
.box{overflow:hidden; position:fixed; width:96%; bottom:0px; margin:0 2% 5% 2%; background:rgba(0, 0, 0, 0.4); border:1px rgba(0, 0, 0, 0.1) solid;}
.box .box-item{float:left;text-align:center;display:block;text-decoration:none;outline:none;width:<?php echo (100/3).'%';?>;height:80px;position:relative; color:#FFF;padding:5px 0;}
.box .box-item i{display:inline-block;width:50px;height:50px;line-height:50px;font-size:35px;color:#FFF; overflow: hidden;background:rgba(255, 255, 255, 0.2);}
.box .box-item span{color:<?php echo $_W['styles']['fontnavcolor'];?>;display:block;font-size:14px; position:absolute; bottom:5px; width:100%;}
.box .slide-nav{width:100%; height:180px; padding:10px 0l;; overflow:hidden; float:left; position:relative;}
#footer{color:#FFF;}
</style>
<div class="box" id="box">
		<ul>
		<?php $num = 0;?>
		<?php if(is_array($navs)) { foreach($navs as $nav) {
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($nav['url'],"http");
		if(($tmp===false)&&(!empty($nav['url']))){
			$urllink=home_url().$nav['url'];
		}else{				
			$urllink=$nav['url'];
		}		?>
		<?php if($num%6 == 0 && $num != 0) { ?></li><?php } ?>
		<?php if($num%6 == 0 || $num == 0) { ?><li class="slide-nav"><?php } ?>
		<a href="<?php echo $urllink;?>" class="box-item">
			<?php if(!empty($nav['icon'])) { ?>
			<i style="background:url(<?php if((empty($nav['icon']))||(stristr($nav['icon'],"http")!==false)){echo $nav['icon'];}else{echo $upload['baseurl'].$nav['icon'];}?>) no-repeat;background-size:cover;" class="img-circle"></i>
			<?php } else { ?>
			<i class="glyphicon <?php echo $nav['css']['icon']['icon'];?> img-circle" style="<?php echo $nav['css']['icon']['style'];?>"></i>
			<?php } ?>
			<span style="<?php echo $nav['css']['name'];?>"><?php echo $nav['name'];?></span>
		</a>
		<?php $num++;?>
		<?php } } ?>
		</ul>
</div>
<script>
	$(function() {
		new Swipe($('#box')[0], {
			speed:500,
			auto:5000
		});
	});
</script>
<?php //include template('footer', TEMPLATE_INCLUDEPATH);?>