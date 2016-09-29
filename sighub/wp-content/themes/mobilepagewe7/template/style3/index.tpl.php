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
background-color:<?php if(empty($_W['styles']['indexbgcolor'])) { ?>#370f05<?php } else { ?><?php echo $_W['styles']['indexbgcolor'];?><?php } ?>;
<?php echo $_W['styles']['indexbgextra'];?>
}
a{color:<?php echo $_W['styles']['linkcolor'];?>; text-decoration:none;}
<?php echo $_W['styles']['css'];?>
.box{padding:0 2% 0 0; overflow:hidden;margin-top:10px;}
.box .box-item{float:left;text-align:center;display:block;text-decoration:none;outline:none;margin:0 0 2% 2%;width:48%;height:100px; background:#d47314; margin-bottom:8px;position:relative; color:#FFF;}
.box .box-item i{display:inline-block; position:absolute; font-size:14px; color:#FFF; overflow:hidden;}
.box .box-item span{color:<?php echo $_W['styles']['fontnavcolor'];?>; font-size:16px; display:block; position:absolute; text-align:left;}
.box .box-item.icon{}
.box .box-item.icon i{right:5px; bottom:5px; width:40px; height:40px;}
.box .box-item.icon span{top:10px; left:10px; width:50%;}
.box .box-item.pic{width:98%;}
.box .box-item.pic i{width:65%; height:100px; line-height:100px; left:0;}
.box .box-item.pic span{width:31%; right:2%; top:30%;}

</style>
<div class="box">
	<?php $num = 0;?>
	<?php if(is_array($navs)) { foreach($navs as $nav) {
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($nav['url'],"http");
		if(($tmp===false)&&(!empty($nav['url']))){
			$urllink=home_url().$nav['url'];
		}else{				
			$urllink=$nav['url'];
		}	?>
	<?php if($num == 0) $bg = '#d47314';?>
	<?php if($num == 1) $bg = '#50ad38';?>
	<?php if($num == 2) $bg = '#dd399a';?>
	<?php if($num == 3) $bg = '#1f75ae';?>
	<?php if($num == 4) $bg = '#543da5';?>
	<a href="<?php echo $urllink;?>" class="box-item <?php if($num == 2) { ?>pic<?php } else { ?>icon<?php } ?>" style="background:<?php echo $bg;?>;">
		<?php if(!empty($nav['icon'])) { ?>
		<i style="background:url(<?php if((empty($nav['icon']))||(stristr($nav['icon'],"http")!==false)){echo $nav['icon'];}else{echo $upload['baseurl'].$nav['icon'];}?>) no-repeat;background-size:cover;"></i>
		<?php } else { ?>
		<i class="glyphicon <?php echo $nav['css']['icon']['icon'];?> icon" style="<?php echo $nav['css']['icon']['style'];?> top: inherit;"></i>
		<?php } ?>
		<span style="<?php echo $nav['css']['name'];?>"><?php echo $nav['name'];?></span>
	</a>
	<?php $num++; if($num > 4) $num = 0;?>
	<?php } } ?>
</div>
<?php //include template('footer', TEMPLATE_INCLUDEPATH);?>