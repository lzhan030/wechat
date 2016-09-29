<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
<?php
	//obtain userId & gweid
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
	$gweid=$_SESSION['GWEID'];
?>

<?php
    if(isset($_GPC['bt']) && !empty($_GPC['bt'])) {
        $buttonClick = $_GPC['bt'];
        $isIframe = 'true';
        if($isIframe = 'true') {
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
<?php   }
    } else {
?>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/button.css" />
		<style>
		 .button{text-decoration:none;font-family:微软雅黑;font-size:14px;}
		 .post{display:block;}
		</style>

		<div class="main_auto">
			<div class="main-title">
				<div class="title-1">当前位置：微商城 &gt; <font class="fontpurple">统计图表 </font>
				</div>
			</div>
			<div style="margin-top:50px;">
				<article id="main" style="border-bottom-color: #ffffff;padding-left:0px;">
					<article>
						
						<section class="post-1301 post type-post status-publish format-standard sticky hentry">
							<article class="thumb">
								<a href="<?php echo $this->createWebUrl('orderstatistic',array('gweid' => $gweid));?>" class="button button-circle button-flat-action"><span class="glyphicon glyphicon-qrcode"></span>&nbsp订单统计</a>
							</article>
						</section>
						
						<section class="post-1301 post type-post status-publish format-standard sticky hentry">
							<article class="thumb">
								<a href="<?php echo $this->createWebUrl('orderstatisticclick',array('gweid' => $gweid));?>" class="button button-circle button-flat-highlight"><span class="glyphicon glyphicon-log-out"></span>&nbsp流量统计</a>
							</article>
						</section>
						
						
					</article>
				</article>	
			</div>
		</div>
<?php   
	}
?>