<?php
session_start();
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';


/**
*@function: get
*/
global $gweid;

$gweid =  $_GET['gweid'];
$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];
$siteId =  $_GET['site'];
if(empty($siteId)){
	$siteId = $_SESSION['orangeSite'];
}else{
	 $_SESSION['orangeSite']= $siteId;
}

/**
*@function:封装gweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
		$gweidt=$siteinfo->GWEID;
	}

	//20150417 sara new added
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$gweid = virtualgweid_open($gweid);
	
	$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
	$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];
	
}


/**
*@function:判断会员是否审核
*/
$vipauditinfo=web_admin_usechat_info_group($gweid);
foreach($vipauditinfo as $vaudit){
	$vipaudit=$vaudit->wechat_vipaudit;
}

/*获取fromuser*/
$fromuser=$_SESSION['gopenid'][intval($gweid)];
$weid =  $_SESSION['weid'][intval($gweid)];

//如果没有获取到fromuser则通过oauth的获取试试
if(empty($fromuser)){
	if($_SESSION['oauth_openid_common']['gweid']==$gweid){
		$fromuser=$_SESSION['oauth_openid_common']['openid'];
		$weid=$_SESSION['oauth_weid_common']['weid'];
	}
}


/**
*@function:通过fromuser拿到会员信息
*/
$countmember=false;
$memberinfo_wgroup=null;
if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){
	
	$countnumber = web_admin_member_count_wgroup($weid,$gweid,$fromuser);
	$memberinfo_wgroup =  web_admin_member_wgroup($weid,$gweid,$fromuser);		
}
if(!empty($memberinfo_wgroup)){
	foreach($memberinfo_wgroup as $minfo_wgroup){
		$mid=$minfo_wgroup->mid;
	}
	//$memberinfo =  web_admin_member_mid($mid,$weid);
	$memberinfo =  web_admin_member_mid_group($mid,$gweid);
	foreach($memberinfo as $minfo){
		$isaudit=$minfo->isaudit;
	}
}else{
	$memberinfo=null;
}
if(!empty($countnumber)){		
	foreach($countnumber as $memberNumber){
		$countmember=$memberNumber->memberCount;
	}
}
if($countmember==0){
	$countmember=false;
}
/**
*@function:已经登陆通过mid拿到会员信息
*/
if((!$countmember)&&(!empty($mid))){
	$countnumber = web_admin_member_count_mid($mid);
	//$memberinfo =  web_admin_member_mid($mid,$weid);
	$memberinfo =  web_admin_member_mid_group($mid,$gweid);
	foreach($memberinfo as $minfo){
		$au_password=$minfo->password;
		$isaudit=$minfo->isaudit;
	}
	if($auth!= md5($mid.$au_password."weauth3647668")){
		$memberinfo=null;
		$countmember=false;
		unset($_SESSION['gmid'][intval($gweid)]);
	}else{
		foreach($countnumber as $memberNumber){
			$countmember=$memberNumber->memberCount;
		}
	}	
}

	
if(!empty($memberinfo))	{
	foreach($memberinfo as $member){
		$realname = $member->realname;
		$nickname = $member->nickname;
		$email = $member->email;
		$isaudit=$member->isaudit;
	}
}

$result = web_user_display_index_groupnew_wesforsel($gweid);
foreach($result as $initfunc){
	if($selCheck[$initfunc->func_name] == 0)
		$selCheck[$initfunc->func_name] = $initfunc->status;
}
	
$isShowVipmember = getSiteMeta('mobilethemeIsShowVipmember', $siteId);
$isShowVipmember_editor = getSiteMeta('mobilethemeIsShowVipmemberEditor', $siteId);

/*
if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')&&(!$countmember))){//开启页面会员限制，并且不是会员
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_login.php?gweid={$gweid}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
		exit();	
}
if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')&&($countmember)&&($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0')))){
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_perdenied.php?gweid={$gweid}&isaudit={$isaudit}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
		exit();	
}*/

?>
<script>
<?php 

echo "var isShowVipmember_editor='".$isShowVipmember_editor."';\n";
echo "var countmember='".$countmember."';\n";
echo "var fromuser='".$fromuser."';\n";
echo "var mid='".$mid."';\n";
echo "var selvip='".$selCheck['wechatvip']."';\n";
?>
function checkvip(){	
	if((selvip=='1')&&((isShowVipmember_editor=='true')&&(!countmember))){
		alert("请先登录");		
		location.href="<?php bloginfo('template_directory'); ?>/../ReeooV3/wesite/common/vip_login.php?gweid=<?php echo $gweid;?>&redirect_url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>#wechat_redirect";
			
		return false;
	}else{ 
		return true;
	}
}
</script>


<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/we7/script/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/we7/script/common.mobile.js.js?v=<?php echo TIMESTAMP;?>"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/we7/script/jquery.form.js"></script>
<script type="text/javascript">
		$(function(){
			isSubmitting = false;
			var actionparm="mobiletheme_comment";
			var ajax_option={			
				url:"<?php bloginfo('template_directory'); ?>/mobiletheme_comment.php?action="+actionparm+"&gweid=<?php echo $gweidt;?>"+"&siteId=<?php echo $siteId;?>"+"&redirect_url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>#wechat_redirect",
				success: function(data){
					if (data.status == 'insertsuc'){
						location.reload();
					}else if (data.status == 'success'){
						alert(data.message);						
						location.href="<?php bloginfo('template_directory'); ?>"+data.url;
					}else if (data.status == 'error'){
						alert(data.message);
					}else{
						alert("出现错误");
					}
					isSubmitting = false;
				},
		       error: function(data){
					alert("您的评论为空或您提交的速度太快或有重复评论提交");
					isSubmitting = false;
				},
				dataType: 'json'
			}
			$('#commentform').submit(function(){
				if(isSubmitting)
				return false;
				isSubmitting = true;
				$(this).ajaxSubmit(ajax_option);
				
				return false;
			});
		});
</script>	
</head>
</html>



<?php


// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>

<p class="nocomments">Password protected.</p>
<?php
		return;
	}
?>
<!-- You can start editing here. -->
<div class="comments-box"> <a name="comments" id="comments"></a>
  <?php if ( have_comments() ) : ?>
  <h3>
    <?php comments_number('没有评论', '1条评论', '% 评论' );?>
  </h3>
  <div class="navigation">
    <div class="previous">
      <?php previous_comments_link() ?>
    </div>
    <div class="next">
      <?php next_comments_link() ?>
    </div>
  </div>
  <ol class="commentlist">
    <?php wp_list_comments('avatar_size=48'); ?>
  </ol>
  <div class="navigation">
    <div class="previous">
      <?php previous_comments_link() ?>
    </div>
    <div class="previous">
      <?php next_comments_link() ?>
    </div>
  </div>
  <?php else : // this is displayed if there are no comments so far ?>
  <?php if ('open' == $post->comment_status) : ?>
  <!-- If comments are open, but there are no comments. -->
  <?php else : // comments are closed ?>
  <!-- If comments are closed. -->
  <p>评论关闭</p>
  <?php endif; ?>
  <?php endif; ?>
  <?php if ('open' == $post->comment_status) : ?>
  <div id="respond">
    <h3>评论</h3>
	<div id="nid" style="font-size:10px">			(在本网站的评论或表述的任何意见均属于发布者个人意见，并不代表本网站的意见。请您遵守中华人民共和国相关法律法规，严禁发布扰乱社会秩序、破坏社会稳定以及包含色情、暴力等的言论。本网站有对您的评论进行删除的权利)
	</div>
    <div class="cancel-comment-reply"> <small>
      <?php cancel_comment_reply_link(); ?>
      </small> </div>
    <?php if ( get_option('comment_registration') && !$user_ID ) : ?>
    <p><?php print 'You must be'; ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php print 'Logged in'; ?></a> <?php print 'to post comment'; ?>.</p>
    <?php else : ?>
    <!--<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php?site=<?php echo $siteId?>" method="post" id="commentform" onsubmit="return checkvip()">-->
	<form action="" method="post" id="commentform" >
      <?php if ( $user_ID ) : ?>
      <p><?php print '登陆身份'; ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account"><?php print '退出'; ?> &raquo;</a></p>
      <?php else : ?>
      
	  <?php if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')||($countmember))){?>
	  <p>微信昵称:<?php echo $nickname ;?></p>
	  <p style="display: none">
        <input class="author" type="text" value="<?php echo $nickname ;?>" onclick="this.value='';" name="author"  id="author" size="22" tabindex="1"/>
      </p>
      <p style="display: none">
        <input class="email" type="text" value="<?php echo $email ;?>" onclick="this.value='';" name="email"  id="" size="email" tabindex="2" />
      </p>
      <p style="display: none">
        <input class="url" type="text" value="" onclick="this.value='';" name="url"  id="url" size="22" tabindex="3"/>
      </p> 	  
	  <?php }else{?>
	  <?php if(($selCheck['wechatvip']==1)&&(($isShowVipmember_editor == 'true')||($countmember))){?>
	  <p>会员姓名（只有会员登陆可进行发表）</p>
	  <?php }else{?>
	  <p <?php if(($selCheck['wechatvip']==1)&&(($isShowVipmember_editor == 'true')||($countmember))){?> style="display: none" <?php }?> >
        <input class="author" type="text" value="输入姓名..." onclick="this.value='';" name="author" id="author" size="22" tabindex="1"/><label for="author"><small><?php if ($req) echo "(必填)"; ?></small></label>
      </p>
      <p style="display:none">
        <input class="email" type="text" value="输入邮箱..." onclick="this.value='';" name="email" id="" size="email" tabindex="2" /><label for="email"><small>(可选 不会显示)</small></label>
      </p>
      <p style="display:none">
        <input class="url" type="text" value="输入网址..." onclick="this.value='';" name="url" id="url" size="22" tabindex="3"/><label for="email"><small>(可选)</small></label>
      </p> 
	  <?php }} ?>	
      <?php endif; ?>
      <!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->
      <p>
        <textarea name="comment" id="comment" tabindex="4"></textarea>
      </p>
      <p>
        <input class="submit" name="submit" type="submit" id="submit" tabindex="5" value="提交" />
        <?php comment_id_fields(); ?>
      </p>
      <?php do_action('comment_form', $post->ID); ?>
    </form>
    <?php endif; // If registration required and not logged in ?>
  </div>
  <?php endif; // if you delete this the sky will fall on your head ?>
</div>
 <?php get_footer(); ?>
