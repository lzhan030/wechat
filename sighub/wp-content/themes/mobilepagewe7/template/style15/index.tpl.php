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
.box{overflow:hidden; position:fixed; width:96%; bottom:0px; margin:0 2% 5% 2%; /*background:rgba(0, 0, 0, 0.4); border:1px rgba(0, 0, 0, 0.1) solid;*/}
.box .box-item{float:left;text-align:center;display:block;text-decoration:none;outline:none;width:50%;height:80px;position:relative; color:#FFF;padding:5px 0;}
.box .box-item i{display:inline-block;width:50px;height:50px;line-height:50px;font-size:35px;color:#FFF; overflow: hidden;background:rgba(255, 255, 255, 0.2);}
.box .box-item span{color:<?php echo $_W['styles']['fontnavcolor'];?>;display:block;font-size:14px; position:absolute; bottom:5px; width:100%;}
.menu-txt span{color:<?php echo $_W['styles']['fontnavcolor'];?>;}
.box .menu-box{width:100%; height:auto; padding:10px 0;; overflow:hidden; float:left; position:relative;}
.menu-item{width:48%;height:50px;margin:1%; float:left;background:rgba(255, 255, 255, 0.4);border-radius: 5px; -webkit-border-radius: 5px;}
.menu-left{width:60px;height:50px;line-height:50px; text-align:center;float:left}
.menu-img{width:60px;height:50px;border-top-left-radius:5px;border-bottom-left-radius:5px;border-top-right-radius:0px;border-bottom-right-radius:0px;-webkit-border-top-left-radius:5px;-webkit-border-bottom-left-radius:5px;-webkit-border-top-right-radius:0px;-webkit-border-bottom-right-radius:0px;}
.menu-txt{float:left;height:50px; padding-left:5%;line-height:50px; color: white;text-decoration: none;}
#footer{color:#FFF;height:0}
</style>
<div class="box" id="box">
		<?php $num = 0;?>
		<?php if(is_array($navs)) { foreach($navs as $nav) {
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($nav['url'],"http");
		if(($tmp===false)&&(!empty($nav['url']))){
			$urllink=home_url().$nav['url'];
		}else{				
			$urllink=$nav['url'];
		}		?>
		<?php if($num%6 == 0 && $num != 0) { ?></div><?php } ?>
		<?php if($num%6 == 0 || $num == 0) { ?><div class="menu-box"><?php } ?>
		<a href="<?php echo $urllink;?>">
		<div class="menu-item">
			<div class="menu-left">
				<?php if(!empty($nav['icon'])) { ?>
				<div class="menu-img" style="background:url(<?php if((empty($nav['icon']))||(stristr($nav['icon'],"http")!==false)){echo $nav['icon'];}else{echo $upload['baseurl'].$nav['icon'];}?>) no-repeat;background-size:100% 100%;"></div>
				<?php } else { ?>
				<i class="glyphicon <?php echo $nav['css']['icon']['icon'];?> img-circle" style="line-height:50px;<?php echo $nav['css']['icon']['style'];?>"></i>
				<?php } ?>
			</div>
			<div class="menu-txt">
				<span style="<?php echo $nav['css']['name'];?>"><?php echo $nav['name'];?></span>
			</div>
		</div>
		</a>
		<?php $num++;?>
		<?php } } ?>
		
</div>
<script>
/*
	$(function() {
		new Swipe($('#box')[0], {
			speed:500,
			auto:5000
		});
	});
	*/
</script>
<?php //include template('footer', TEMPLATE_INCLUDEPATH);?>