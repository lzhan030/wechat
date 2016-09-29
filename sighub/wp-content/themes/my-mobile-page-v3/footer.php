<?php global $theme,$siteId,$gweid;?>


   <!-- update by janeen
	<div id="footer" class="black_gradient">
        <a href="<?php echo home_url().'/?site='.$siteId; ?>" class="back_button black_button"><?php $theme->option('home_button'); ?></a>
        <div class="page_title"><?php $theme->option('footer_text'); ?></div>
        <a onClick="jQuery('html, body').animate( { scrollTop: 0 }, 'slow' );"  href="javascript:void(0);" id="top" class="black_button"><?php $theme->option('top_button'); ?></a>
        <div class="clear"></div>
    </div>
	-->

<?php wp_footer(); ?>
<?php $theme->option('analytics_code'); ?>
<?php 
	$sharetitle = get_bloginfo('name','display');
	share_page_in_wechat($gweid, array(
	'title' => $sharetitle,
	'desc' => "微官网活动，期待您的参与！",
	'link' => 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com' ));
?>
</body>
</html>
