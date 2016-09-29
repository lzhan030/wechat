<?php
    $tmp_path = explode ( 'ReeooV3', __FILE__ );
    $template_path=$tmp_path[0];
    require_once $template_path.'ReeooV3/wechat/common/session.php';
	//echo 'CSQ'; exit;
    $path = explode ( 'wp-content', __FILE__ );
    $wp_root_path = $path [0];
    require_once ($wp_root_path . '/wp-load.php');
	global  $current_user;
	if( !isset($current_user->user_login)|| empty($current_user->user_login))
	{
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
	$weid=$_SESSION['WEID'];
	
    get_header(); 
?>


<?php
    require_once ('wesite/common/dbaccessor.php');

	$wid = $_GET['wid']; //这样首次进入是获取不到的	
	//obtain userId
	global $current_user;
	//判断当前用户是否是某个分组管理员下的
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$id =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	$GWEID = $_SESSION['GWEID'];
	
	//判断该用户是否添加过公众号
	$wechatcount = web_admin_wechat_count($id);
	foreach($wechatcount as $wechat){
		$wechatnumber = $wechat->wechatCount;
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
	$selCheck['wechatactivity_egg'] = 0;   //egg module added
	$selCheck['wechatactivity_redenvelope'] = 0;   //hongbao module added
	$selCheck['wechatactivity_vote'] = 0;
	$selCheck['wechatactivity_wxwall'] = 0;
	$selCheck['wechatfuncnokeywordsreply'] = 0;
	$selCheck['wechatvip'] = 0;
	$selCheck['wechatresearch'] = 0;
	$selCheck['wechatfunceditresponse'] = 0;
	$selCheck['wechatfuncmass'] = 0;
	  	  
  
  	$userId= ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	//Sara new added
	//判断当前用户添加的是公用的公众号还是个人的公众号
	$wechattypes = $wpdb->get_results( "SELECT w.hash, w.wechat_nikename, w.wechat_type, w.token, u1.vericode, u1.flgopen FROM ".$wpdb->prefix."wechat_usechat u1,".$wpdb->prefix."wechats w WHERE u1.wid = w.wid and u1.user_id = ".$userId." and u1.WEID =".$weid);
	
	foreach($wechattypes as $wechattype){
		//显示的url链接
		$userwechattype = $wechattype->wechat_type;
	}
	
	//2014-07-07判断当前所添加的公众号中有没有服务号以及认证的订阅号，只要有其中之一就会显示人工回复和菜单管理
	$menuflag = false;
	$massflag = false;
	$manualflag = false;
	$getwids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." WHERE GWEID = ".$GWEID."  AND user_id = ".$id);
	//自定义菜单
	foreach($getwids as $getwid)
	{
		$wids = $getwid->wid;
		$widinfo = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechats")." WHERE wid = ".$wids);
		foreach($widinfo as $getinfo)
		{
		    $widtype = $getinfo -> wechat_type;
			$widauth = $getinfo -> wechat_auth;
		}
		if(($widtype == "pri_sub" && $widauth == 1)|| ($widtype == "pub_sub" && $widauth == 1) || $widtype == "pri_svc" || $widtype == "pub_svc")
        {		   
		   $menuflag = true;
			break;
		}
	}
	//人工回复
	foreach($getwids as $getwid)
	{
		$wids = $getwid->wid;
		$widtype = $wpdb->get_var( "SELECT wechat_type FROM ".web_admin_get_table_name("wechats")." WHERE wid = ".$wids);
		if($widtype == "pri_svc" || $widtype == "pub_svc")
        {		   
		   $manualflag = true;
			break;
		}
	}
	//群发
	foreach($getwids as $getwid)
	{
		$wids = $getwid->wid;
		$widtype = $wpdb->get_var( "SELECT wechat_type FROM ".web_admin_get_table_name("wechats")." WHERE wid = ".$wids);
		if(($widtype == "pri_sub" && $widauth == 1)|| ($widtype == "pri_svc" && $widauth == 1) )
        {		   
		   $massflag = true;
			break;
		}
	}
	//2014-07-07新增修改,是否选中通过gweid和userid决定
	$result = web_user_display_index_groupnew($GWEID, $id);
    foreach($result as $initfunc){
		if($selCheck[$initfunc->func_name] == 0)
			$selCheck[$initfunc->func_name] = $initfunc->status;
	}

	$displaynone="display:none";
	$displayblock="display:block";
	//mashan 添加是否公布验证码的标识
	// $ov =$_POST['openvericode'];
	if(isset($_POST['openvericode']))
	{
	      $wpdb->query( "UPDATE ".$wpdb->prefix."wechat_usechat  SET flgopen= ".(isset($_POST['openvericode'])&&$_POST['openvericode']!=null&&$_POST['openvericode']!=''?$_POST['openvericode']:"null")." WHERE WEID = '".$weid."'");
	}
	$widusechat = $wpdb->get_var( "SELECT wid from ".$wpdb->prefix."wechat_usechat WHERE WEID =".$weid);
	$usertype = $wpdb->get_var( "SELECT wechat_type from ".$wpdb->prefix."wechats WHERE wid =".$widusechat);


	$gethgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$userId);
	if(!empty($gethgroupuserids)){
		foreach($gethgroupuserids as $getgroupinfo)
		{
		    $userhgroupid = $getgroupinfo -> group_id;
		    $userhgroupflag = $getgroupinfo -> flag;
		}
		
	}else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
		$userhgroupid = 0;
		$userhgroupflag = 0;
	}
	//如果是分组管理员，并且选择的是虚拟号，则只显示群发消息、素材管理、会员管理，分组管理员也可能添加公众号
	if($userhgroupid !=0 && $userhgroupflag == 1){
		$gethgroupgweids = $wpdb->get_results( "SELECT GWEID FROM {$wpdb -> prefix}wechat_group where user_id = ".$userId." AND WEID = 0");
		foreach($gethgroupgweids as $getgweidinfo)
		{
		    $currentgweid = $getgweidinfo -> GWEID;
		}
		if($currentgweid == $GWEID){
			$ugadminflag = 1;
		}else{
			$ugadminflag = 0;
		}
		
	}else{
		$ugadminflag = 0;
	}


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
		elseif($buttonClick == 'editResponse')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/editrep/editreply.php?beIframe';
		elseif($buttonClick == 'wechatmass')
            $src = home_url().'/module.php?module=mass&do=masslist';
        elseif($buttonClick == 'wechatmassgroupadmin')
            $src = home_url().'/module.php?module=mass&do=masslist&fromflag=1';
        elseif($buttonClick == 'accountManage')
            //$src = home_url().'/wp-content/themes/ReeooV3/wechat/account/accountinfo.php?beIframe&id='.$userId;	
			$src = home_url().'/wp-content/themes/ReeooV3/init/account_info.php?beIframe&id='.$userId;				
        elseif($buttonClick == 'userManage')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/user/user_list.php?beIframe';
		elseif($buttonClick == 'vipManage')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/vipmembermanage/vipmember_list.php?beIframe';	
		elseif($buttonClick == 'humanResponse')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/user/user_list.php?beIframe';	
		/*elseif($buttonClick == 'menuManage'){
				if ($usertype == "pub_svc")
			$src = home_url().'/wp-content/themes/ReeooV3/wechat/menupublicsvc/menu.php?beIframe';
				else 
			$src = home_url().'/wp-content/themes/ReeooV3/wechat/menu/menu.php?beIframe';
			}*/
		elseif($buttonClick == 'menuManage'){				
			$src = home_url().'/wp-content/themes/ReeooV3/wechat/menu/menu_all_select.php?beIframe';
		}
		elseif($buttonClick == 'init')
            $src = home_url().'/wp-content/themes/ReeooV3/init/wechat_account.php?beIframe';
		elseif($buttonClick == 'siteCount')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/sitecount/sitedisplaycondition.php?beIframe&id='.$userId;
		elseif($buttonClick == 'actCount')
            $src = home_url().'/wp-content/themes/ReeooV3/wechat/sitecount/sitedisplaycondition.php?beIframe&id='.$userId;
		elseif($buttonClick == 'weSchool')
            $src = home_url().'/module.php?module=weSchool&do=index';
		elseif($buttonClick == 'research')
            $src = home_url().'/module.php?module=research&do=display&weid='.$weid;	
		elseif($buttonClick == 'account_selector')
            $src = home_url().'/module.php?module=research&do=display&weid='.$weid;	
		elseif($buttonClick == 'globalSetting')
            $src = home_url().'/wp-content/themes/ReeooV3/init/account_info.php';		
		elseif($buttonClick == 'wechatAccountManage')
            $src = home_url().'/wp-content/themes/ReeooV3/init/wechat_account_list.php';    
		elseif($buttonClick == 'wepay')
            $src = home_url().'/module.php?module=wepay&do=index';
		elseif($buttonClick == 'weshopping')
			$src = home_url().'/module.php?module=weshopping&do=index';
		elseif($buttonClick == 'scratchcard')
            $src = home_url().'/module.php?module=scratchcard&do=ScratchcardList';
		elseif($buttonClick == 'egg')
            $src = home_url().'/module.php?module=egg&do=EggList';    //egg module added
		elseif($buttonClick == 'redenvelope')
            $src = home_url().'/module.php?module=redenvelope&do=list';    //hongbao module added
        elseif($buttonClick == 'vote')
            $src = home_url().'/module.php?module=vote&do=list';
		elseif($buttonClick == 'wepayorder')
            $src = home_url().'/module.php?module=wepay&do=Ordermanage&id=3';
		elseif($buttonClick == 'weshoppingorder')
            $src = home_url().'/module.php?module=weshopping&do=Ordermanage&id=3';			
		elseif($buttonClick == 'wepayrights')
            $src = home_url().'/module.php?module=wepay&do=Rightmanage&id=3'; 			
		elseif($buttonClick == 'wepayalarm')
            $src = home_url().'/module.php?module=wepay&do=Alarmmanage'; 
		elseif($buttonClick == 'wxwall')
            $src = home_url().'/module.php?module=wxwall&do=list'; 
		else
            echo 'not finish OR error：404';
        if($isIframe = 'true')
        {
        ?>
		<?php if(!empty($buttonClick) && in_array($buttonClick,array('research'))){ ?>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/common.js?v=1398747354"></script>
		<?php } ?>

        <iframe src="<?php echo $src;?>" id="iframepage" name="iframepage" frameBorder=0 scrolling=auto width="100%" onLoad="iFrameHeight()" height="900"></iframe>
        <script type="text/javascript" language="javascript">
            function iFrameHeight() {
                var ifm= document.getElementById("iframepage");
                var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;
				var clientHeight = subWeb.body.clientHeight;//其它浏览器默认值
				//如果是chrome则执行这段
				if(navigator.userAgent.indexOf("Chrome")!=-1)
		        {
		            clientHeight = subWeb.body.scrollHeight;
		        }
				//如果是Firefox则执行这段
		        if(navigator.userAgent.indexOf("Firefox")!=-1)
		        {
		           clientHeight = subWeb.documentElement.scrollHeight;
		        }

                if(ifm != null && subWeb != null) {
				    
					ifm.height = clientHeight + 100;
					//2014-07-10动态改变账户管理页面的长度,点击已认证会新出现两行表格
					var ifmsrc = document.getElementById("iframepage").src;
					if(ifmsrc.indexOf("account_info.php") > 0)
					{
						 ifm.height = clientHeight + 100;
					} 
					if(ifmsrc.indexOf("wechat_account.php") > 0)
					{
						 ifm.height = clientHeight + 300;
					}
				}
             }
			 
        </script>
    <?php
        }
    }
    else{
    ?>

<?php if($wechatnumber != 0){?>	
<?php 
if($ugadminflag == 1){?>
<div style="">
<article id="main">
	<article>
		<p class="title title-top" ><b>| 基础服务</b></p>	
		<section  class="post type-post status-publish format-standard sticky hentry" style="">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=wechatmassgroupadmin" >
					<button type="button" id="mainbtn2" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-fullscreen"></span> 群发消息</button>
				</a>
			</article>
		</section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=materialManage" >
					<button type="button" id="mainbtn6" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-picture"></span> 素材管理</button>
				</a>
			</article>
		</section>
	</article>
</article>
</div>
<div style="">
<article id="main">
	<article>
		<p class="title" ><b>| 高级服务</b></p>
		<section  class="post type-post status-publish format-standard sticky hentry" style="">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=vipManage">
					<button type="button" id="mainbtn18" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-user"></span> 会员管理</button>
				</a>
			</article>
		</section>
		</article> 
</article>
</div>
<?php }else{?>
<article style="<?php if($wechatnumber == 0) echo $displaynone;?>">	
	<article>
	    <!--2014-07-07新增修改，注释设置功能-->
		<p style="display:none;"><a href="#" onclick="javascript:window.open('<?php echo home_url();?>/wp-content/themes/ReeooV3/init/function_custom_dlg.php?siteId=<?php echo $siteId?>','_blank','height=440,width=774,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no, channelmode=yes, titlebar=no')" 
		id="setlink" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-cog"></span>  设置</a></p>
	</article>
</article>
<div style="<?php if(!$selCheck['wechatfuncfirstconcern']&!$selCheck['wechatfunckeywordsreply']&!$selCheck['wechatfuncnokeywordsreply']&!$selCheck['wechatfuncmaterialmanage']&!$selCheck['wechatfuncaccountmanage']&!$selCheck['wechatfunceditresponse']) echo $displaynone; ?>">

<article id="main">
	<article>
		<p class="title title-top" ><b>| 基础服务</b></p>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatfuncfirstconcern']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=firstConcern" >
					<button type="button" id="mainbtn1" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-star"></span> 首次关注</button>
				</a>
			</article>
		</section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatfuncnokeywordsreply']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=nokeyWordResponse" >
					<button type="button" id="mainbtn17" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-comment"></span> 无匹配回复</button>
				</a>
			</article>
		</section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatfunckeywordsreply']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=keyWordResponse" >
					<button type="button" id="mainbtn2" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-cloud"></span> 关键词回复</button>
				</a>
			</article>
		</section>
		<!--20140811newadd-->
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatfunceditresponse']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=editResponse" >
					<button type="button" id="mainbtn2" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-export"></span> 可编程回复</button>
				</a>
			</article>
		</section>
		<?php if($massflag){?>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatfuncmass']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=wechatmass" >
					<button type="button" id="mainbtn2" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-fullscreen"></span> 群发消息</button>
				</a>
			</article>
		</section>
		<?php } ?>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatfuncmaterialmanage']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=materialManage" >
					<button type="button" id="mainbtn6" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-picture"></span> 素材管理</button>
				</a>
			</article>
		</section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatfuncaccountmanage']==1?$displayblock:$displaynone; ?>">
            <article class="thumb">
                <a class="icons" href="<?php echo home_url();?>?bt=accountManage" >
					<button type="button" id="mainbtn4" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-list-alt"></span> 帐户管理</button>
				</a>
            </article>
        </section>

	</article>
</article>
</div>
<div style="<?php if(!$selCheck['wechatwebsite']&!$selCheck['wechatvip']&!$selCheck['wechatfuncmenumanage']&!$selCheck['wechatfuncmanualreply']&!$selCheck['wechatfuncusermanage']&!$selCheck['wechatresearch']) echo $displaynone; ?>">
<article id="main">
	<article>
		<p class="title" ><b>| 高级服务</b></p>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatwebsite']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=webSites" >
					<button type="button" id="mainbtn5" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-phone"></span> 微官网</button>
				</a>
			</article>
		</section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatvip']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=vipManage">
					<button type="button" id="mainbtn18" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-user"></span> 会员管理</button>
				</a>
			</article>
		</section>
		<?php if($menuflag){?>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatfuncmenumanage']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=menuManage" >
					<button type="button" id="mainbtn7" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-list"></span> 菜单管理</button>
				</a>
			</article>
		</section>
		<?php }?>
		<?php if($manualflag){?>
		<!-- <section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatfuncmanualreply']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=humanResponse" >
					<button type="button" id="mainbtn3" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-user"></span> 人工回复</button>
				</a>
			</article>
		</section> -->
		<?php }?>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatfuncusermanage']==1?$displayblock:$displaynone; ?>">
            <article class="thumb" style="display:none;">
                <a class="icons" href="<?php echo home_url();?>?bt=userManage" >
					<button type="button" id="mainbtn8" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-cloud"></span> 微用户管理</button>
				</a>
            </article>
        </section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatresearch']==1?$displayblock:$displaynone; ?>">
            <article class="thumb">
                <a class="icons" href="<?php echo home_url();?>?bt=research" >
					<button type="button" id="mainbtn8" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-saved"></span> 微预约</button>
				</a>
            </article>
        </section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wepay']==1?$displayblock:$displaynone; ?>">
            <article class="thumb">
                <a class="icons" href="<?php echo home_url();?>?bt=wepay" >
					<button type="button" id="mainbtn8" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-credit-card"></span> 微支付</button>
				</a>
            </article>
        </section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['weshopping']==1?$displayblock:$displaynone; ?>">
            <article class="thumb">
                <a class="icons" href="<?php echo home_url();?>?bt=weshopping" >
					<button type="button" id="mainbtn8" class="btn btn-lg btn-info"><span class="glyphicon glyphicon-briefcase"></span> 微商城</button>
				</a>
            </article>
        </section>
	</article> 
</article>
</div>

<div style="<?php if(!$selCheck['wechatactivity_egg']&!$selCheck['wechatactivity_scratch']&!$selCheck['wechatactivity_wxwall']&!$selCheck['wechatactivity_redenvelope']) echo $displaynone; ?>">
<article id="main">
	<article>
		<p class="title" ><b>| 微活动</b></p>
		<!--<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatactivity_coupon']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=favorable" >
					<button type="button" id="mainbtn9" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-tags"></span> 优惠劵</button>
				</a>
			</article>
		</section>-->
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatactivity_scratch']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=scratchcard" >
					<button type="button" id="mainbtn10" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-thumbs-up"></span> 刮刮卡</button>
				</a>
			</article>
		</section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatactivity_egg']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=egg" >
					<button type="button" id="mainbtn10" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-certificate"></span> 砸蛋</button>
				</a>
			</article>
		</section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatactivity_wxwall']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=wxwall" >
					<button type="button" id="mainbtn10" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-th-list"></span> 微信墙</button>
				</a>
			</article>
		</section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatactivity_redenvelope']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=redenvelope" >
					<button type="button" id="mainbtn11" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-envelope"></span> 微红包</button>
				</a>
			</article>
		</section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatactivity_vote']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=vote" >
					<button type="button" id="mainbtn11" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-align-left"></span> 微投票</button>
				</a>
			</article>
		</section>
		<!--<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatactivity_toend']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=winner" >
					<button type="button" id="mainbtn12" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-time"></span> 一站到底</button>
				</a>
			</article>
		</section>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatactivity_fortunemachine']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=luckymachine" >
					<button type="button" id="mainbtn13" class="btn btn-lg btn-warning"><span class="glyphicon glyphicon-gift"></span> 幸运机</button>
				</a>
			</article>
		</section>-->
	</article>
</article>
</div>

<!--wechatschool new added by Sara -->
<div style="<?php if(!$selCheck['wechatschool']) echo $displaynone; ?>">
<article id="main">
	<article>
		<p class="title" style=""><b>| 微行业</b></p>
		<section  class="post type-post status-publish format-standard sticky hentry" >
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=weSchool">
					<button type="button" id="mainbtn18" class="btn btn-lg btn-purple" style="border-color:#C729D1;"><span class="glyphicon glyphicon-flag"></span> 微学校</button>
				</a>
			</article>
		</section>
	</article>
</article>
</div>

<div style="<?php if(!$selCheck['wechatwebsite']) echo $displaynone; ?>">
<article id="main">
	<article>
		<p class="title" style=""><b>| 效果统计</b></p>
		<section  class="post type-post status-publish format-standard sticky hentry" style="<?php echo $selCheck['wechatwebsite']==1?$displayblock:$displaynone; ?>">
			<article class="thumb">
				<a class="icons" href="<?php echo home_url();?>?bt=siteCount" >
					<button type="button" id="mainbtn14" class="btn btn-lg btn-danger"><span class="glyphicon glyphicon-signal"></span> 微官网统计</button>
				</a>
			</article>
		</section>
	</article>
</article>
</div>
<?php }?>
<?php }?>
<?php   }
    get_footer();
 ?>