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
if(isset($_POST['submit']))
{
    //unset($_POST['submit']);
    include 'webManagConn.php';	
	$blogTitle=$_POST['blogTitle'];
	$blogContent=$_POST['blogContent'];
	$blogAuthor=$_POST['blogAuthor'];
	$postSiteId = $_POST['siteId'];
	
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}users(user_login,user_nicename,display_name)VALUES (%s,%s,%s)",md5($blogAuthor.time()),$blogAuthor, $blogAuthor));
	
	$userId =$wpdb->insert_id;

	$result=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}posts(post_author,post_date,post_date_gmt,post_content,post_title,post_name,post_content_filtered)VALUES (%s,now(),utc_timestamp(),%s,%s,%s,%s)",$userId,$blogContent,$blogTitle,$blogAuthor,$postSiteId));
    
	?>
    <script>
     location.reload();
    </script>
	<?php
}
 curPageURL() ;
/*
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
*/
if(1)
 {
     $siteId = $_SESSION['orangeSite'];//$siteId  = $_COOKIE['orangeSite'];

	 $isShowPic = getSiteMeta('mobilethemeIsShowPic', $siteId);
	 $isShowEditor = getSiteMeta('mobilethemeIsShowEditor', $siteId);
	 $useContact = getSiteMeta('mobilethemeContact', $siteId);
	 
get_header(); ?>



<div class="entry">
<?php if(($useContact !=null) && (strlen($useContact)>0)) { ?>
	<div class="post">
		<U><a href="tel:<?php echo $useContact ?>">点此预定,联系我们</a></U>
		<br clear="all" />
	</div>
<?php };?>	
    <div id="container">
	
        <?php query_posts(array( 'post_type' => 'post','post_content_filtered'=> $siteId, 'orderby' => 'ID', 'order' => 'DESC', 'showposts' => '999'));?>
        <?php if(have_posts()) : ?>
		<?php 
		//
		global $wpdb, $table_prefix;
		$tableName = $table_prefix.'orangesitemeta';
		$siteId = intval($_GET['site']);
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
					$post_content_array = split("\n",get_the_content()); 
					$post_content_link = $post_content_array[0];
					$post_content_length =$post_content_array[1];
					$i--;
					if($i>=0)
						continue;
					$count++;
					if($count>$postCount)
							break;
			?>
                <div class="post" id="post-<?php the_ID(); ?>">
                    <?php if($isShowPic){ ?>
                    <div class="imgpost"><?php mtheme_thumb(); ?></div>
                    <?php } ?>
                    <div class="title">
                        <h2><a href="<?php echo $post_content_link; ?>" title="<?php the_title(); ?>"><font><?php the_title(); if( is_sticky() ) echo '&nbsp;&nbsp;<span style="color:red;">置顶</span>';?> </font></a></h2>
                    </div>
					
					<div class="date"><?php echo $post_content_length;?>       <?php if($isShowPic){ the_author();}?>
                        发布于 <?php the_time('F j, Y') ?>
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
        
  
        <?php //get_sidebar(); ?><!--remove search function-->
        <?php get_footer();  }
            else
                echo '请先激活必要的插件';
        ?>