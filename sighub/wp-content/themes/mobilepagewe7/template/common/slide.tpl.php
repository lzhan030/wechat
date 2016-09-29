<?php defined('IN_IA') or exit('Access Denied');?>
<?php $slide = pdo_fetchall("SELECT * FROM ".tablename('site_nav')." WHERE position = '3' AND status = 1 AND site_id = '{$_W['site_id']}' ORDER BY displayorder DESC");
$upload =wp_upload_dir();?>
<style>
.box_swipe {
  overflow: hidden;
  position: relative;
}
.box_swipe ul {
  overflow: hidden;
  position: relative;
}
.box_swipe ul > li {
  float:left;
  width:100%;
  position: relative;
}
.box_swipe>ol{
	height:20px;
	position: relative;
	z-index:10;
	margin-top:-25px;
	text-align:right;
	padding-right:15px;
	background-color:rgba(0,0,0,0.3);
}
.box_swipe>ol>li{
	display:inline-block;
	margin:5px 0;
	width:8px;
	height:8px;
	background-color:#757575;
	border-radius: 8px;
}
.box_swipe>ol>li.on{
	background-color:#ffffff;
}
</style>
<div id="banner_box" class="box_swipe">
	<ul style="margin:0px;padding:0px;">
	<?php if(is_array($slide)) { foreach($slide as $v) { 
			/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
			$tmp = stristr($v['url'],"http");
			if(($tmp===false)&&(!empty($v['url']))){
				$slideurl=home_url().$v['url'];
			}else{				
				$slideurl=$v['url'];
			}		
	?>
		<li style="margin:0px;padding:0px;">
			<a href="<?php echo link_convert($slideurl);?>">				
				<img src="<?php if((empty($v['icon']))||(stristr($v['icon'],"http")!==false)){echo $v['icon'];}else{echo $upload['baseurl'].$v['icon'];}?>" alt="<?php echo $v['name'];?>" style="width:100%;" />
			</a>
		</li style="margin:0px;padding:0px;">
	<?php } } ?>
	</ul>
	<ol style="margin:0px;padding:0px;">
	<?php $slideNum = 1;?>
	<?php if(is_array($slide)) { foreach($slide as $vv) { ?>
		<li<?php if($slideNum == 1) { ?> class="on"<?php } ?> style="margin:0px;padding:0px;"></li>
		<?php $slideNum++;?>
	<?php } } ?>
	</ol>
</div>
<script>
$(function() {
	new Swipe($('#banner_box')[0], {
		speed:500,
		auto:3000,
		callback: function(){
			var lis = $(this.element).next("ol").children();
			lis.removeClass("on").eq(this.index).addClass("on");
		}
	});
});
</script>