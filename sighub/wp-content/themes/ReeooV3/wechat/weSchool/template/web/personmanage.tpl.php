<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<?php

	$wid = $_GPC['wid']; //这样首次进入是获取不到的	
	//obtain userId
	global $current_user, $wpdb;
	//当前用户有可能是分组管理员下的
    $getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$current_user->ID);
    if(!empty($getgroupuserids)){
        foreach($getgroupuserids as $getgroupinfo)
        {
            $usergroupid = $getgroupinfo -> group_id;
            $usergroupflag = $getgroupinfo -> flag;
        }
    }else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
        $usergroupid = 0;
        $usergroupflag = 0;
    }
    $userId = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($usergroupid !=0 && $usergroupflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
    
  	//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
	
	 
?>

<?php
    if(isset($_GPC['bt']) && !empty($_GPC['bt']))
    {
        $buttonClick = $_GPC['bt'];
        $isIframe = 'true';
        if($isIframe = 'true')
        {
        ?>
        <iframe src="<?php echo $src;?>" id="iframepage" name="iframepage" frameBorder=0 scrolling=no width="92%" onLoad="iFrameHeight()" height="900"></iframe>
        <script type="text/javascript" language="javascript">
            function iFrameHeight() {
                var ifm= document.getElementById("iframepage");
                var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;
                if(ifm != null && subWeb != null) {
                    ifm.height = subWeb.body.scrollHeight;
                }
             }
        </script>
    <?php
        }
    }
    else{
    ?>

<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/button.css" />
<div style="margin-left:30px;">
<article id="main" style="border-bottom-color: #FFF;">
	<article>
		<!--<p class="title title-top" ><b>| 人员管理</b></p>-->
		<section class="post-1301 post type-post status-publish format-standard sticky hentry" style="display:block">
			<article class="thumb">
				<a href="<?php echo $this->createWebUrl('studentmanage',array('id' => 3));?>" class="button button-circle button-flat-highlight" style="text-decoration:none;font-family:微软雅黑;font-size:14px"><span class="glyphicon glyphicon-tasks"></span>&nbsp学生管理</a>
			</article>
		</section>
		<section class="post-1301 post type-post status-publish format-standard sticky hentry" style="display:block">
		<a href="<?php echo $this->createWebUrl('teachermanage',array('id' => 3));?>" class="button button-circle button-flat-action" style="text-decoration:none;font-family:微软雅黑;font-size:14px"><span class="glyphicon glyphicon-book"></span>&nbsp教师管理</a>
		</section>

	</article>
</article>
</div>

<?php   }
    //get_footer();
 ?>