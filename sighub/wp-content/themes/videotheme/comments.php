<?php
//add 20141207
/**
*@function: get
*/
session_start();
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
global $gweid;

$gweid =  $_GET['gweid'];
$siteId = $_GET['site'];
$_SESSION['orangeSite']=$siteId;
/**
*@function:封装gweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
	}
}
// add end
?>
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
    <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
      <?php if ( $user_ID ) : ?>
      <p><?php print '登陆身份'; ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account"><?php print '退出'; ?> &raquo;</a></p>
      <?php else : ?>
      <p>
        <input class="author" type="text" value="输入姓名..." onclick="this.value='';" name="author" id="author" size="22" tabindex="1"/><label for="author"><small><?php if ($req) echo "(必填)"; ?></small></label>
      </p>
      <p>
        <input class="email" type="text" value="输入邮箱..." onclick="this.value='';" name="email" id="" size="email" tabindex="2" /><label for="email"><small>(可选 不会显示)</small></label>
      </p>
      <p>
        <input class="url" type="text" value="输入网址..." onclick="this.value='';" name="url" id="url" size="22" tabindex="3"/><label for="email"><small>(可选)</small></label>
      </p> 
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
