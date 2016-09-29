<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<?php

	$wid = $_GPC['wid']; //这样首次进入是获取不到的	
	//obtain userId
	global $current_user;
	global $_W,$wpdb;
	//判断是否是分组管理员下的用户
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
<style>
 .button{text-decoration:none;font-family:微软雅黑;font-size:14px;}
 .post{display:block;}
</style>

<div class="main_auto">
	<div class="main-title">
		<div class="title-1">当前位置：微支付 &gt; <font class="fontpurple">功能列表 </font>
		</div>
	</div>
	<div style="margin-top:50px;">
		<article id="main" style="border-bottom-color: rgb(180,180,180);padding-left:0px;">
			<article>
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
					<article class="thumb">
						<a href="<?php echo $this->createWebUrl('Globalsetting',array('gweid' => $gweid));?>" class="button button-circle button-flat-primary" ><span class="glyphicon glyphicon-cog"></span>&nbsp全局设置</a>
					</article>
				</section>
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
					<article class="thumb">
						<a href="<?php echo $this->createWebUrl('Paymenttest',array('gweid' => $gweid));?>" class="button button-circle button-flat-action"><span class="glyphicon glyphicon-check"></span>&nbsp支付测试</a>
					</article>
				</section>
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
					<a href="<?php echo $this->createWebUrl('Goodsindexmanage',array('id' => 3));?>" class="button button-circle button-flat-caution"><span class="glyphicon glyphicon-tags"></span>&nbsp网页支付</a>
				</section>
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
					<a href="<?php echo $this->createWebUrl('Qrcodemanage',array('id' => 3));?>" class="button button-circle button-flat-royal"><span class="glyphicon glyphicon-qrcode"></span>&nbsp原生支付</a>
				</section>
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
					<article class="thumb">
						<a href="<?php echo $this->createWebUrl('Ordermanage',array('id' => 3));?>" class="button button-circle button-flat-highlight"><span class="glyphicon glyphicon-tasks"></span>&nbsp订单管理</a>
					</article>
				</section>
			</article>
		</article>
		<article id="main" style="border-bottom-color: #FFF;padding-left:0px;">
			<article>
					
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
				<a href="<?php echo $this->createWebUrl('Rightmanage',array('id' => 3));?>" class="button button-circle button-flat-highlight"><span class="glyphicon glyphicon-hand-up"></span>&nbsp维权管理</a>
				</section>
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
				<a href="<?php echo $this->createWebUrl('refund',array('id' => 3));?>" class="button button-circle button-flat-royal"><span class="glyphicon glyphicon-usd"></span>&nbsp退款管理</a>
				</section>
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
					<a href="<?php echo $this->createWebUrl('Alarmmanage',array('id' => 3));?>" class="button button-circle button-flat-caution"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp告警管理</a>
				</section>
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
				<a href="<?php echo $this->createWebUrl('downloadorder',array('id' => 3));?>" class="button button-circle button-flat-action"><span class="glyphicon glyphicon-download"></span>&nbsp对账单下载</a>
				</section>
				<section class="post-1301 post type-post status-publish format-standard sticky hentry">
					<article class="thumb">
						<a href="<?php echo $this->createWebUrl('indexstatistic',array('gweid' => $gweid));?>" class="button button-circle button-flat-primary"><span class="glyphicon glyphicon-stats"></span>&nbsp统计图表</a>
					</article>
				</section>	
			</article>
		</article>	
	</div>
</div>

<?php   }
    //get_footer();
 ?>