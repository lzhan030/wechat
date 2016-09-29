<?php defined('IN_IA') or exit('Access Denied');?>
<?php include template('site_header', TEMPLATE_INCLUDEPATH);
$upload =wp_upload_dir();
if(stristr($_W['styles']['indexbgimg'],"http")!==false){
	$indexbgimg=$_W['styles']['indexbgimg'];		
}else{
	$indexbgimg=$upload['baseurl'].$_W['styles']['indexbgimg'];
}
$id = $_GET['site'];
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
	/* [disabled]width: 450px; */
	margin-left: auto;
	margin-right: auto;
	margin-top: 5%;
	position: static;
}

.bn0 {
	/*图片的实际尺寸在手机端偏大*/
	width: 120px;
	height: 120px;
	position: absolute;
	top: 30%;
	right: 10px;
	z-index: 1;
	/*用于显示图片bn0.png*/
	background-image:url('<?php echo (home_url().'/wp-content/themes/mobilepagewe7/template/'."{$template}/images/bn0.png");?>');
	background-repeat: no-repeat;
	background-size:100% 100%;
}
.bncn {
	/*根据手机尺寸适配宽度*/
	width: 100%;
	margin-left: auto;
	margin-right: auto;
	position: relative;
	margin-top: 75%; 
}
.bn {
	margin: 50px 10px;
}
</style>

</head>

<body>
<div class="row">
  <div class="bn0"> 
  </div>

  <div class="bncn">
    <div class="bn">
     <?php if(is_array($navs)) { foreach($navs as $nav) {
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
  <i>
    <!--图片的宽度为100%,高度为按照宽度比例展示-->
	<img src="<?php if((empty($nav['icon']))||(stristr($nav['icon'],"http")!==false)){echo $nav['icon'];}else{echo $upload['baseurl'].$nav['icon'];}?>"  width="100%"  style="margin-top:10px" alt=""/>
  </i>
  <?php } else { ?>
  <i class="glyphicon <?php echo $nav['css']['icon']['icon'];?> " style="<?php echo $nav['css']['icon']['style'];?>"></i>
  <?php } ?>
    <span style="<?php echo $nav['css']['name'];?>; background-color: <?php echo $color[$num];?>"><?php echo $nav['name'];?></span> </a>
  <?php if($num <2) {
			$num++;
		} else {
			$num = 0;
		}?>
  <?php } } ?>
  </div>
 
  </div>  
</div>
</body>
</html>
