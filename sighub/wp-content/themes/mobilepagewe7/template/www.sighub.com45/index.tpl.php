<?php defined('IN_IA') or exit('Access Denied');?>
<?php include template('site_header', TEMPLATE_INCLUDEPATH);?>
<?php 
//引入slide部分，来显示顶端图片
include_once IA_ROOT . '/wp-content/themes/mobilepagewe7/template/common/slide.tpl.php';
//include template('site_header', TEMPLATE_INCLUDEPATH);
$upload =wp_upload_dir();
if(stristr($_W['styles']['indexbgimg'],"http")!==false){
	$indexbgimg=$_W['styles']['indexbgimg'];		
}else{
	$indexbgimg=$upload['baseurl'].$_W['styles']['indexbgimg'];
}
?>


<style type="text/css">
body {
 font:<?php echo $_W['styles']['fontsize'];
?> <?php echo $_W['styles']['fontfamily'];
?>;
 color:<?php echo $_W['styles']['fontcolor'];
?>;
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

.row {
	/*设置距离两边的距离 */
	margin-left: 15%;
	margin-right: 15%;
	position: static;
}

.bn0 {
	width: 100%;
	position: relative;
	z-index: 1;
}
.bncn {
	/*自适应宽度为100%*/
	width: 100%;
	margin-left: auto;
	margin-right: auto;
	position: relative;
	
}
.bn {
	/*自适应宽度为100%*/
	width: 100%;
	margin-bottom: 50px;
	margin-top: 10%;
}
#footer{height:0}
</style>

<div class="row">
  <!--div class="bn0">
   <?php if(!empty($nav['icon'])) { ?>
  <i><img src="<?php if((empty($nav['icon']))||(stristr($nav['icon'],"http")!==false)){echo $nav['icon'];}else{echo $upload['baseurl'].$nav['icon'];}?>"  width="100%"   alt=""/></i>
  <?php } else { ?>
  <i class="glyphicon <?php echo $nav['css']['icon']['icon'];?> " style="<?php echo $nav['css']['icon']['style'];?>"></i>
  <?php } ?>
    <span style="<?php echo $nav['css']['name'];?>; background-color: <?php echo $color[$num];?>"><?php echo $nav['name'];?></span> 
  <?php if($num <2) {
			$num++;
		} else {
			$num = 0;
		}?>
  </div-->
  
  <div class="bncn">
    <div class="bn">  <?php if(is_array($navs)) { foreach($navs as $nav) {
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($nav['url'],"http");
		if(($tmp===false)&&(!empty($nav['url']))){
			$urllink=home_url().$nav['url'];
		}else{				
			$urllink=$nav['url'];
		}
		?>
  <a href="<?php echo $urllink;?>" >
  <?php if(!empty($nav['icon'])) { ?>
  <!--设定图片宽度填充100%，高度按照比例显示，每张图片的底部设置一定空隙-->
  <i><img src="<?php if((empty($nav['icon']))||(stristr($nav['icon'],"http")!==false)){echo $nav['icon'];}else{echo $upload['baseurl'].$nav['icon'];}?>"  width="100%" style="margin-bottom:20px" alt=""/></i>
  <?php } else { ?>
  <i class="glyphicon <?php echo $nav['css']['icon']['icon'];?> " style="<?php echo $nav['css']['icon']['style'];?>"></i>
  <?php } ?>
    <span style="<?php echo $nav['css']['name'];?>; background-color: <?php echo $color[$num];?>"><?php echo $nav['name'];?></span> </a>
  <?php if($num <2) {
			$num++;
		} else {
			$num = 0;
		}?>
  <?php } } ?></div>
  </div>  
</div>
