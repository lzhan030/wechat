<?php defined('IN_IA') or exit('Access Denied');?>
<?php 
	$upload =wp_upload_dir();
?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/jquery.gcjs.js"></script>
<script type='text/javascript' src='<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/touchslider.min.js'></script>
<script language='javascript' src='<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/photoswipe/simple-inheritance.min.js'></script>
<script language='javascript' src='<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/photoswipe/photoswipe-1.0.11.min.js'></script>
<link href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/photoswipe/photoswipe.css" rel="stylesheet" />
<script language="javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/touchslider.min.js"></script>
<script language="javascript" src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/swipe.js"></script>
<!--<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/style.css">-->
<style>
	.box_swipe>ol>li{margin-bottom:-4px;}
	.box_swipe span{line-height: 1.428571429;}
</style>
<!--还需要处理某一处显示哪个图片以及对应的位置-->
<div class="detail-img" style="margin:10px 5px;">
	<div id="banner_box" class="box_swipe">
		<ul style="background:#FFF;">
			<?php  if(is_array($rlist2)) { foreach($rlist2 as $row) { ?>
			<li style="text-align:center;list-style: none;">
				<a href="<?php  echo $this->createMobileUrl('detail', array('id' => $row['id'], 'gweid' => $gweid))?>" >	
					<?php if(!(empty($row['thumb']))){?>
						<img src="<?php if((empty($row['thumb']))||(stristr($row['thumb'],"http")!==false)){echo $row['thumb'];}else{echo $upload['baseurl'].$row['thumb'];}?>" alt="" height="200px" style=""/>
					<?php }else{?>
						<img src="<?php bloginfo('template_directory'); ?>/wechat/weshopping/images/nopic.jpg" alt="" height="200px" style=""/>
					<?php }?>
				</a>
				<span class="title"><?php  echo $row['title'];?></span>
			</li>
			<?php  } } ?>
		</ul>
		<ol>
			<?php  if(is_array($rlist2)) { foreach($rlist2 as $row) { ?>
			<li class="on"></li>
			<?php  } } ?>
		</ol>
	</div>
	<script>
	var proimg_count = <?php  echo count($rlist)?>;
	$(function() {
		new Swipe($('#banner_box')[0], {
			speed:500,
			auto:3000,
			callback: function(){
				var lis = $(this.element).next("ol").children();
				lis.removeClass("on").eq(this.index).addClass("on");
			}
		});
		if (proimg_count > 0) {
			(function(window, $, PhotoSwipe) {
				$('#banner_box ul li a[rel]').photoSwipe({});
			}(window, window.jQuery, window.Code.PhotoSwipe));
		}
	});
	</script>
</div>
