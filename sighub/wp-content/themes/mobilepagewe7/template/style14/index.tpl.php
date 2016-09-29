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
<?php echo $_W['styles']['indexbgextra'];?>
}
a{color:<?php echo $_W['styles']['linkcolor'];?>; text-decoration:none;}
<?php echo $_W['styles']['css'];?>
#banner_box{  height:100%; overflow: :hidden;}
img{ height:100%;min-height:100%; max-height:100%; }
.box_swipe ul{ height:100%; }
.box_swipe>ol { z-index: 10;text-align: right; padding-right: 15px; background-color: rgba(0,0,0,0.3);padding: 0;width: 100%;height: 20px;line-height: 20px;color: #666;position: absolute;bottom: 0px;overlflow:hidden;}
#footer{display: none;height:0px;}
</style>
