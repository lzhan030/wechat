<?php
session_start();
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
if(!isset($_GET['weid'])||!isset($_GET['fromuser']))
{
    $weid = $_SESSION['WECID'];
	$fromuser = $_SESSION['fromuser'];
}
else
{
    $weid =  $_GET['weid'];
    $fromuser = $_GET['fromuser'];
	$_SESSION['WECID']=$weid;
	$_SESSION['fromuser']=$fromuser;
}





function curPageURL() 
{
    $pageURL = 'http';

    if ($_SERVER["HTTPS"] == "on") 
    {
        $pageURL .= "s";
    }
    $pageURL .= "://";

    if ($_SERVER["SERVER_PORT"] != "80") 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } 
    else 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function getSiteMeta($keyName, $siteID)
{
	global $wpdb, $table_prefix;
	$tableName = $table_prefix.'orangesitemeta';
	$sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteID."' and `site_key`='".$keyName."'";
	$sitemeta = $wpdb->get_row($sql);
	return $sitemeta->site_value;
}

/*
function web_admin_member_count($weid, $fromuser)
{
    global $wpdb,$table_prefix;
	$tableName = $table_prefix.'wechat_member';
	$myrows = $wpdb->get_results("SELECT COUNT(*) as memberCount FROM ".$tableName." where WEID='".$weid."' and from_user= '".$fromuser."'");   
	return $myrows;
}  
function web_admin_member($weid, $fromuser)
{
    global $wpdb,$table_prefix;
	$tableName = $table_prefix.'wechat_member';
	$myrows = $wpdb->get_results("SELECT * FROM ".$tableName." where WEID='".$weid."' and from_user= '".$fromuser."'");   
	return $myrows;
}  
 */
if(1)
 {
     $siteId = $_SESSION['orangeSite'];//$siteId  = $_COOKIE['orangeSite'];
	 
	 $isShowPic = getSiteMeta('mobilethemeIsShowPic', $siteId);
	 $isShowEditor = getSiteMeta('mobilethemeIsShowEditor', $siteId);
	 $isShowVipmember = getSiteMeta('mobilethemeIsShowVipmember', $siteId);
	 $isShowVipmember_editor = getSiteMeta('mobilethemeIsShowVipmemberEditor', $siteId);
	 $useContact = getSiteMeta('mobilethemeContact', $siteId);
	 
	$countnumber = web_admin_member_count($weid, $fromuser);
	$memberinfo =  web_admin_member($weid, $fromuser);
	foreach($countnumber as $memberNumber){
		$countmember=$memberNumber->memberCount;
	}
		//echo $countmember;查看该用户是否是会员
	foreach($memberinfo as $member){
		$realname = $member->realname;
		$nickname = $member->nickname;	
	}
if(isset($_POST['submit']))
{
    //unset($_POST['submit']);
    include 'webManagConn.php';	
	
	if($fromuser==null){?>
	<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script>
			alert("请从微信公众号处进行访问并注册为会员");
		</script>
			
		<script>
			 <?php if(isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER']))
		{?>
		   location.href="<?php echo $_SERVER['HTTP_REFERER']; ?>";
		<?php }else
		{?>
		   location.href="http://www.xiaohuivip.com";
		<?php }?>
		</script>
		</head>
		<body>
		</body>
		</html>
	
	<?php exit;}
	else if(($isShowVipmember_editor=='true')&&(!$countmember)){?>
	<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script>
			alert("您还不是会员,请先填写申请会员信息");
		</script>
			
		<script>
			location.href="<?php bloginfo('template_directory'); ?>/../ReeooV3/wesite/common/vip_register.php?weid=<?php echo $weid;?>&fromuser=<?php echo $fromuser;?>";
		</script>
		</head>
		<body>
		</body>
		</html>
	<?php
	exit;
	}
	
	$blogTitle=$_POST['blogTitle'];
	$blogContent=$_POST['blogContent'];
	$blogAuthor=$_POST['blogAuthor'];
	$postSiteId = $_POST['siteId'];
	$insertUserSql="insert into wp_users (user_login,user_nicename,display_name) values ('".$blogAuthor."','".$blogAuthor."','".$blogAuthor."')";
 	$result = mysql_query($insertUserSql);

	$selectIdSql="select ID from wp_users where user_login='".$blogAuthor."'";
	//$userId = mysql_query($selectIdSql);
	$resultc = mysql_query($selectIdSql);
	$rowc = mysql_fetch_array($resultc);
	$userId =$rowc['ID'];

	//$insertSql="insert into wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_name) values (1,now(),now(),'".$blogContent."','".$blogTitle."','".$blogAuthor."')";
	$insertSql="insert into wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_name,post_content_filtered) values ('".$userId."',now(),now(),'".$blogContent."','".$blogTitle."','".$blogAuthor."','".$postSiteId."')";
    //var_dump($insertSql);
    $result = mysql_query($insertSql);
   // var_dump($result); exit;
	?>
    <script>
     location.reload();
    </script>
	<?php
}
get_header(); ?>

<?php if(($isShowVipmember == 'true')&&(!$countmember)){	
?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script>
        alert("您还不是会员,请先填写申请会员信息");
	</script>
		
	<script>
		location.href="<?php bloginfo('template_directory'); ?>/../ReeooV3/wesite/common/vip_register.php?weid=<?php echo $weid;?>&fromuser=<?php echo $fromuser;?>";
	</script>
	</head>
<body>
</body>
</html>
<?  exit;}?>	
			
<div class="entry">
<?php if(($useContact !=null) && (strlen($useContact)>0)) { ?>
	<div class="post">
		<U><a href="tel:<?php echo $useContact ?>">点此预定,联系我们</a></U>
		<br clear="all" />
	</div>
<?php };?>	
    <div id="container">
        <?php query_posts(array( 'post_type' => 'post','post_content_filtered'=> $siteId, 'orderby' => 'date', 'order' => 'DESC', 'showposts' => '999'));?>
        <?php if(have_posts()) : ?>
		<?php 
		//
		global $wpdb, $table_prefix;
		$tableName = $table_prefix.'orangesitemeta';
		$siteId = $_GET['site'];
		$keyName = 'mobilethemeSize';
		$sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
		$sitemeta = $wpdb->get_row($sql);
		$postCount = $sitemeta->site_value;
		$tablecount = $table_prefix.'posts';
		$keypost = 'post';
		$sql = "SELECT count(*) as count_p FROM  $wpdb->posts WHERE `post_content_filtered`='".$siteId."' and `post_type`='".$keypost."'";
		$post = $wpdb->get_row($sql);
		$postC = $post->count_p;
		//$postC = count($post);
		//echo $postC;
		if($postCount <= 0)
			$postCount = 5;	
		$haveView = 0;
		if(isset($_GET['haveview']))
			$haveView = $_GET['haveview'];
		$i=$haveView;
		$count = 0; ?>
		
            <?php while(have_posts()) : the_post(); 
			?>
            <?php if($post->post_content_filtered ==  $siteId){
					$i--;
					if($i>=0)
						continue;
					$count++;
					if($count>$postCount)
							break;
			?>
                <div <?php post_class(); ?> class="post" id="post-<?php the_ID(); ?>">
                    <?php if($isShowPic=='true'){?>					
                    <div class="imgpost"><?php mtheme_thumb_v2(); ?></div>
                    <?php } ?>
                    <div class="title">
                        <h2><a href="<?php the_permalink(); echo '&site='.$siteId; ?>" title="<?php the_title(); ?>"><?php the_title(); if( is_sticky() ) echo '&nbsp;&nbsp;<span style="color:red;">置顶</span>'; ?></a></h2>
                    </div>
                    <div class="date"><?php if($isShowPic=='false'){  the_author();?>
                        发布于 <?php the_time('F j, Y');}?> 
                    </div>

                    <br clear="all" />
                </div>

            <?php } endwhile; ?>

            <div class="navigation">
			<?php 
				$url = curPageURL();
				if(stristr($url,"haveview=")){
					$url=substr( $url,0,strripos($url,'&haveview='));
				}
			?>
			<?php if($haveView>0) { 
					if($haveView>$postC-$postCount){?>
					<div class="goleft"><a href="<?php echo $url.'&haveview='.($haveView-$postCount)?>">上一页</a></div>
					<?php } else{ ?>
					<div class="goleft"><a href="<?php echo $url.'&haveview='.($haveView-$postCount)?>">上一页</a></div>
					<div class="goright"><a href="<?php echo $url.'&haveview='.($haveView+$postCount) ?>">下一页</a></div>
					<?PHP } } ?>
			<?php if($haveView<=0) {?>
					<div class="goright"><a href="<?php echo $url.'&haveview='.($haveView+$postCount) ?>">下一页</a></div>
				<?php } ?>
                <div class="clear"></div>
			<?php  ?>
            </div>

        <?php else : ?>

            <div class="post" id="post-<?php the_ID(); ?>">
                <h2><?php _e('No posts are added.'); ?></h2>
            </div>

        <?php endif; ?>

    </div>


</div> <!--entry-->
<br/><br/>
        
  
        <?php get_sidebar(); ?>
		
		 <!--add the function of publish the blog-->
      <?php if($isShowEditor == 'true'){ ?>
		<div id="respond">
          <h3>发表论题</h3>
		  <div style="font-size:10px">			(在本网站的评论或表述的任何意见均属于发布者个人意见，并不代表本网站的意见。请您遵守中华人民共和国相关法律法规，严禁发布扰乱社会秩序、破坏社会稳定以及包含色情、暴力等的言论。本网站有对您的论题进行删除的权利)
		  </div>
          <div class="cancel-comment-reply">
		   <small></small> 
		  </div>
		  
          <form action="" method="post" id="commentform">
			<?php if(($isShowVipmember == 'true')||($countmember!='0')){?>
			<p><?php echo $nickname ;?></p>
			<p style="display: none">			   
			   <input class="author" type="text" value="<?php echo $nickname ;?>" onclick="this.value='';" name="blogAuthor" id="blogAuthor" size="22" tabindex="1"/><label for="author"><small><?php //if ($req) echo "(必填)"; ?></small></label>
			</p>
		<?php }else{?>
			<?php if(($isShowVipmember_editor == 'true')||($fromuser==null)){?>
			<p>
				会员姓名（只有会员从微信公众号处登陆可进行发表）
			</p>
			<?php }else{?>
			<p <?php if(($isShowVipmember_editor == 'true')||($fromuser==null)){?> style="display: none" <?php }?> >
			   <input class="author" type="text" value="输入姓名..." onclick="this.value='';" name="blogAuthor" id="blogAuthor" size="22" tabindex="1"/><label for="author"><small><?php //if ($req) echo "(必填)"; ?></small></label>
			</p>
			<?php }} ?>		
          <p>
           <input class="url" type="text" value="输入标题..." onclick="this.value='';" name="blogTitle" id="blogTitle" size="22" tabindex="3"/><label for="email"><small>(必填)</small></label>
          </p>
          <p>
           <textarea name="blogContent" id="blogContent" tabindex="4"></textarea>
          </p>
          <p>
           <input type="hidden" name="siteId" value="<?php echo $siteId ?>"/>
           <input class="submit" name="submit" type="submit" id="submit" tabindex="5" value="提交" />
          </p>
          </form>
        </div>
       <?php }?>

        <?php get_footer();    }
            else
                echo '请先激活必要的插件';
        ?>