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
background-image:url('<?php if(empty($_W['styles']['indexbgimg'])) { ?><?php echo $template_url;?>/images/bg_index.jpg<?php } else { ?><?php echo $indexbgimg;?><?php } ?>');
background-size:cover;
background-color:<?php if(empty($_W['styles']['indexbgcolor'])) { ?>#fbf5df<?php } else { ?><?php echo $_W['styles']['indexbgcolor'];?><?php } ?>;
<?php echo $_W['styles']['indexbgextra'];?>
}
a{color:<?php echo $_W['styles']['linkcolor'];?>; text-decoration:none;}
<?php echo $_W['styles']['css'];?>
.box{width:93%;overflow:hidden;margin-top:10px; padding-left:7%;}
.box .box-item{float:left; display:inline-block; width:45%; text-align:center; margin:0 1%; text-decoration:none;outline:none; height:40px; line-height:40px; margin-bottom:5px; color:#FFF; background:rgba(75, 38, 11, 0.9);}
.box .box-item span{color:<?php echo $_W['styles']['fontnavcolor'];?>; display:inline-block; font-size:14px;}
</style>
<div class="box">
	<?php if(is_array($navs)) { foreach($navs as $nav) {
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($nav['url'],"http");
		if(($tmp===false)&&(!empty($nav['url']))){
			$urllink=home_url().$nav['url'];
		}else{				
			$urllink=$nav['url'];
		}		?>
	<a href="<?php echo $urllink;?>" class="box-item">
		<span style="<?php echo $nav['css']['name'];?>"><?php echo $nav['name'];?></span>
	</a>
	<?php } } ?>
</div>
<?php // include template('footer', TEMPLATE_INCLUDEPATH);?>