	
    	<div id="footer">

            <?php
            global $wpdb, $table_prefix,$gweid;
            $tableName = $table_prefix.'orangesitemeta';
            //$siteId = ($_GET['site'] == ''||$_GET['site']==null)? '1': $_GET['site'];
            $siteId = $_SESSION['orangeSite']; //$_COOKIE['orangeSite'];
            $keyName = 'mobilethemeFooter';
            $sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
            $sitemeta = $wpdb->get_row($sql);
            $footer = $sitemeta->site_value;
            if(empty($footer))
            {
                //$footer = 'Copyright @ '.date(Y);
                //$wpdb->query("INSERT INTO `".$tableName."`( `site_id`, `site_key`,`site_value`) VALUE('".$siteId."', '".$keyName."','".$footer."')");
            }
            ?>
                    	<div class="copyright"><p align="center"><?php echo $footer;?></p><!--Copyright &#169; <?php print(date(Y)); ?> <?php bloginfo('name'); ?> | Moblog by <a href="http://www.blogohblog.com/" title="Premium WordPress Themes">Blog Oh! Blog</a>--></div>
               
        </div>
        
	</div>
	<?php 
   	$sharetitle = get_bloginfo('name','display');
	share_page_in_wechat($gweid, array(
	'title' => $sharetitle,
	'desc' => "微官网活动，期待您的参与！",
	'link' => 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com' ));
	?>
</body>
</html>