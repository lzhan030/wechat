<?php
    //echo 'CSQ'; exit;
    $path = explode ( 'wp-content', __FILE__ );
    $wp_root_path = $path [0];
    require_once ($wp_root_path . '/wp-load.php');
	global  $current_user;
	if( !isset($current_user->user_login)|| empty($current_user->user_login))
	{
		//if(is_multisite())
			//wp_redirect('http://'.$current_site->domain . $current_site->path.'login');     //add by CSQ, for
		wp_redirect(wp_login_url());
	}
	
	if (is_ssl() ) 
	{
		if ( 0 === strpos($_SERVER['REQUEST_URI'], 'https') ) 
		{
			wp_redirect(preg_replace('|^https://|', 'http://', $_SERVER['REQUEST_URI']));
		} 
		else 
		{
			wp_redirect('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		}
	}	
    get_header(); 
?>


<?php
    require_once ('wesite/common/dbaccessor.php');

	//obtain userId
	global $current_user;
	$id = $current_user->ID;
	
	//判断该用户是否添加过公众号
	$wechatcount = web_admin_wechat_count($id);
	foreach($wechatcount as $wechat){
		$wechatnumber = $wechat->wechatCount;
	}
	

    //是否点击提交事件	
   if( isset($_POST['templateselect']) ){
	$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_name = '".$_POST['templateselect']."' WHERE user_id = ".$id." AND func_name like '%template%';");
	
	$selCheck['wechatwebsite'] = 0;
	$selCheck['wechatfuncfirstconcern'] = 0;
	$selCheck['wechatfunckeywordsreply'] = 0;
	$selCheck['wechatfuncmanualreply'] = 0;
	$selCheck['wechatfuncaccountmanage'] = 0;
	$selCheck['wechatfuncmaterialmanage'] = 0;
	$selCheck['wechatfuncmenumanage'] = 0;
	$selCheck['wechatfuncusermanage'] = 0;
	$selCheck['wechatactivity_coupon'] = 0;
	$selCheck['wechatactivity_scratch'] = 0;
	$selCheck['wechatactivity_fortunewheel'] = 0;
	$selCheck['wechatactivity_toend'] = 0;
	$selCheck['wechatactivity_fortunemachine'] = 0;
	$selCheck['wechatfuncnokeywordsreply'] = 0;
	$selCheck['wechatvip'] = 0;
	$result = $wpdb->get_results("SELECT func_name,func_flag from ".$wpdb->prefix."wechat_initfunc_info WHERE user_id =".$id." AND func_name NOT like '%template%' AND EXISTS(SELECT * FROM  `wp_wechat_func_info` WHERE  `func_name` =  ".$wpdb->prefix."wechat_initfunc_info.func_name AND  `status` =0 )");
	foreach($result as $initfunc){
		$selCheck[$initfunc->func_name] = $initfunc->func_flag;
	}
	if(isset($_POST['selCheck'])){
		foreach($_POST['selCheck'] as $check)
			$selCheck[$check] = 1;
			}
	foreach($selCheck as $func_name => $func_flag){
		$wpdb->query( "UPDATE ".$wpdb->prefix."wechat_initfunc_info  SET func_flag = '$func_flag' WHERE user_id = ".$id." AND func_name='$func_name';");
	}
}
 		$selCheck['wechatwebsite'] = 0;
		$selCheck['wechatfuncfirstconcern'] = 0;
		$selCheck['wechatfunckeywordsreply'] = 0;
		$selCheck['wechatfuncmanualreply'] = 0;
		$selCheck['wechatfuncaccountmanage'] = 0;
		$selCheck['wechatfuncmaterialmanage'] = 0;
		$selCheck['wechatfuncmenumanage'] = 0;
		$selCheck['wechatfuncusermanage'] = 0;
		$selCheck['wechatactivity_coupon'] = 0;
		$selCheck['wechatactivity_scratch'] = 0;
		$selCheck['wechatactivity_fortunewheel'] = 0;
		$selCheck['wechatactivity_toend'] = 0;
		$selCheck['wechatactivity_fortunemachine'] = 0;
		$selCheck['wechatfuncnokeywordsreply'] = 0;
        $selCheck['wechatvip'] = 0;
	  	  
  
  	$userId=$current_user->ID;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." WHERE user_id = ".$userId."   AND EXISTS(SELECT * FROM  `".$wpdb->prefix."wechat_func_info` WHERE ".$wpdb->prefix."wechat_func_info.`func_name` =  ".$wpdb->prefix."wechat_initfunc_info.func_name AND  `status` =1 )");
	foreach($myrows as $initfunc)
	{
		$selCheck[$initfunc->func_name] = $initfunc->func_flag;		
	    }
  

	   $displaynone="display:none";
	   $displayblock="display:block";
?>

<?php
    if(isset($_GET['bt']) && !empty($_GET['bt']))
    {
        $buttonClick = $_GET['bt'];
        $isIframe = 'true';
        if($buttonClick == 'webSites')
            $src = home_url().'/wp-content/themes/ReeooV3/wesite/common/website_list.php?beIframe';
        elseif($buttonClick == 'materialManage')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/material/material.php?beIframe';
        elseif($buttonClick == 'keyWordResponse')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/keyword/keyword_list.php?beIframe';
		elseif($buttonClick == 'nokeyWordResponse')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/nokeyword/nokeyword.php?beIframe';
			
		elseif($buttonClick == 'firstConcern')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/autorep/autoreply.php?beIframe';
        elseif($buttonClick == 'accountManage')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/account/accountinfo.php?beIframe&id='.$userId;			
        elseif($buttonClick == 'userManage')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/user/user_list.php?beIframe';
		elseif($buttonClick == 'vipManage')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/vipmembermanage/vipmember_list.php?beIframe';	
		elseif($buttonClick == 'humanResponse')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/user/user_list.php?beIframe';	
		elseif($buttonClick == 'menuManage')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/menu/menu.php?beIframe';
		elseif($buttonClick == 'init')
            $src = home_url().'/wp-content/themes/ReeooV3/init/wechat_account.php?beIframe';
		elseif($buttonClick == 'siteCount')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/sitecount/sitedisplaycondition1.php?beIframe&id='.$userId;
		elseif($buttonClick == 'actCount')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/sitecount/sitedisplaycondition.php?beIframe&id='.$userId;					
        else
            echo 'not finish OR error：404';
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

<article style="<?php if($wechatnumber == 0) echo $displaynone;?>">	
	<article>
		<p><a href="#" onclick="javascript:window.open('<?php echo home_url();?>/wp-content/themes/ReeooV3/init/function_custom_dlg.php?siteId=<?php echo $siteId?>','_blank','height=440,width=774,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no, channelmode=yes, titlebar=no')" 
		id="setlink" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-cog"></span>  设置</a></p>
	</article>
</article>
<div style="<?php if(!$selCheck['wechatfuncfirstconcern']&!$selCheck['wechatfunckeywordsreply']&!$selCheck['wechatfuncnokeywordsreply']&!$selCheck['wechatfuncmaterialmanage']&!$selCheck['wechatfuncaccountmanage']) echo $displaynone; ?>">
<article id="main">
	<article>
		<p class="title title-top" ><b>| 基础服务</b></p>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatfuncfirstconcern']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=firstConcern" >
					<button type="button" id="mainbtn1" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-star"></span> 首次关注</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatfuncnokeywordsreply']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=nokeyWordResponse" >
					<button type="button" id="mainbtn17" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-comment"></span> 无匹配回复</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatfunckeywordsreply']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=keyWordResponse" >
					<button type="button" id="mainbtn2" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-comment"></span> 关键词回复</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatfuncmaterialmanage']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=materialManage" >
					<button type="button" id="mainbtn6" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-picture"></span> 素材管理</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatfuncaccountmanage']==1?$displayblock:$displaynone; ?>">
            <article class="thumb">
                <a class="icons" href="<?php echo home_url();?>?bt=accountManage" >
					<button type="button" id="mainbtn4" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-list-alt"></span> 帐户管理</button>
				</a>
            </article>
        </section>

	</article>
</article>
</div>
<div style="<?php if(!$selCheck['wechatwebsite']&!$selCheck['wechatvip']&!$selCheck['wechatfuncmenumanage']&!$selCheck['wechatfuncmanualreply']&!$selCheck['wechatfuncusermanage']) echo $displaynone; ?>">
<article id="main">
	<article>
		<p class="title" ><b>| 高级服务</b></p>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatwebsite']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=webSites" >
					<button type="button" id="mainbtn5" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-phone"></span> 微官网</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatvip']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=vipManage">
					<button type="button" id="mainbtn18" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-cog"></span> 会员管理</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatfuncmenumanage']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=menuManage" >
					<button type="button" id="mainbtn7" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-list"></span> 菜单管理</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatfuncmanualreply']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=humanResponse" >
					<button type="button" id="mainbtn3" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-user"></span> 人工回复</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatfuncusermanage']==1?$displayblock:$displaynone; ?>">
            <article class="thumb">
                <a class="icons" href="<?php echo home_url();?>?bt=userManage" >
					<button type="button" id="mainbtn8" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-cloud"></span> 微用户管理</button>
				</a>
            </article>
        </section>
	</article> 
</article>
</div>

<!--<div style="<?php //if(!$selCheck['wechatvip']) echo $displaynone; ?>">
<article id="main">
	<article>
		<p class="title" style=""><b>| 微会员</b></p>
		<section <?php //post_class() ?> style="<?php //echo $selCheck['wechatvip']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php //echo home_url();?>?bt=vipManage">
					<button type="button" id="mainbtn18" class="btn btn-lg btn-purple"><span class="glyphicon glyphicon-cog"></span> 会员管理</button>
				</a>
			</article>
		</section>
	</article>
</article>
</div>-->

<div style="<?php if(!$selCheck['wechatactivity_coupon']&!$selCheck['wechatactivity_scratch']&!$selCheck['wechatactivity_fortunewheel']&!$selCheck['wechatactivity_toend']&!$selCheck['wechatactivity_fortunemachine']) echo $displaynone; ?>">
<article id="main">
	<article>
		<p class="title" ><b>| 微活动</b></p>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatactivity_coupon']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=favorable" >
					<button type="button" id="mainbtn9" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-tags"></span> 优惠劵</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatactivity_scratch']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=scraftcard" >
					<button type="button" id="mainbtn10" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-thumbs-up"></span> 刮刮卡</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatactivity_fortunewheel']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=fortunewheel" >
					<button type="button" id="mainbtn11" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-certificate"></span> 幸运大转盘</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatactivity_toend']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=winner" >
					<button type="button" id="mainbtn12" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-time"></span> 一站到底</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatactivity_fortunemachine']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=luckymachine" >
					<button type="button" id="mainbtn13" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-gift"></span> 幸运机</button>
				</a>
			</article>
		</section>
	</article>
</article>
</div>
<div style="<?php if(!$selCheck['wechatwebsite']&!$selCheck['wechatactivity_coupon']&!$selCheck['wechatactivity_scratch']&!$selCheck['wechatactivity_fortunewheel']&!$selCheck['wechatactivity_toend']&!$selCheck['wechatactivity_fortunemachine']) echo $displaynone; ?>">
<article id="main">
	<article>
		<p class="title" style=""><b>| 效果统计</b></p>
		<section <?php post_class() ?> style="<?php echo $selCheck['wechatwebsite']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=siteCount" >
					<button type="button" id="mainbtn14" class="btn btn-lg btn-danger"><span class="glyphicon glyphicon-signal"></span> 微官网统计</button>
				</a>
			</article>
		</section>
		<section <?php post_class() ?> style="<?php if(!$selCheck['wechatactivity_coupon']&!$selCheck['wechatactivity_scratch']&!$selCheck['wechatactivity_fortunewheel']&!$selCheck['wechatactivity_toend']&!$selCheck['wechatactivity_fortunemachine']) echo $displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=actCount">
					<button type="button" id="mainbtn15" class="btn btn-lg btn-danger"><span class="glyphicon glyphicon-cog"></span> 微活动统计</button>
				</a>
			</article>
		</section>
	</article>
</article>
</div>
<div style="<?php if($wechatnumber >= 1) echo $displaynone;?>">
<article id="main">
	<article>
		<p class="title" style=""><b>| 初始化</b></p>
		<section <?php post_class() ?>>
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=init">
					<button type="button" id="mainbtn16" class="btn btn-lg btn-danger"><span class="glyphicon glyphicon-cog"></span> 添加微信公众号</button>
				</a>
			</article>
		</section>
	</article>
</article>
</div>
<?php   }
    get_footer();
 ?>