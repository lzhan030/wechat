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
.box{width:100%;overflow:hidden;margin-top:40%;}
.box .box-item{display:block; text-decoration:none;outline:none; height:35px; line-height:35px; margin-bottom:5px; color:#FFF; }
.box .box-item div{display:inline-block; background:rgba(13, 94, 97, 0.7); padding:0 20% 0 5px; width:auto; position:relative;}
.box .box-item div i{position:absolute; right:10px; top:10px;}
.box .box-item span{color:<?php echo $_W['styles']['fontnavcolor'];?>; display:inline-block; font-size:14px;}
#footer{color:#FFF;}
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
		<div><i class="icon-chevron-right"></i><span style="<?php echo $nav['css']['name'];?>"><?php echo $nav['name'];?></span></div>
	</a>
	<?php } } ?>
</div>
<?php //include template('footer', TEMPLATE_INCLUDEPATH);?>