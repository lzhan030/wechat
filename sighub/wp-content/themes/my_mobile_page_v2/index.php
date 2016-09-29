<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
session_start();
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once 'wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
get_header(); 

/**
*@author: janeen
*@version: add by janeen 20140430
*/
/**
*@function: get
*/
global $gweid ;

$gweid =  $_GET['gweid'];
$siteId = $_GET['site'];

/**
*@function:封装gweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
	}

	//20150417 sara new added
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$gweid = virtualgweid_open($gweid);
}


$isvip="vip_detail";
$isvipreg="vip_register";
$isweschool = "index";
$isresearch = "module=research";					
$mem=stristr($menu_url_info->meta_value,$isvip);
$memreg=stristr($menu_url_info->meta_value,$isvipreg);
$schoollink=stristr($menu_url_info->meta_value,$isweschool);
$researchlink=stristr($menu_url_info->meta_value,$isresearch);
/*是否显示会员相关的link以及微学校、微预约*/

?>
<!--把header中的body的背景图片设置放置到index首页来设置，这样其他的页面就可以不要有首页的背景图片了-->
<?php
global $wpdb, $table_prefix;
$tableName = $table_prefix.'orangesitemeta';
$siteId = ($_GET['site'] == ''||$_GET['site']==null)? '1': $_GET['site'];
$keyName = 'firstPageBackgroup';
$sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
$sitemeta = $wpdb->get_row($sql);
$backgroup = $sitemeta->site_value;
if((!empty($backgroup ))&&(stristr($backgroup,"http")===false)){
	$upload =wp_upload_dir();
	$backgroup=$upload['baseurl'].$backgroup;
}
if(empty($backgroup)){
	$backgroup=home_url()."/wp-content/themes/ReeooV3/images/bac_image.jpg";
}
?>
<!--<body background="<?php echo $backgroup;?>">-->
<body background="<?php echo $backgroup;?>">

    <?php
        global $wpdb, $table_prefix;
        $tableName = $table_prefix.'orangesitemeta';
		$siteTableName=$table_prefix.'orangesite';
		
        //$siteId = ($_GET['site'] == ''||$_GET['site']==null)? '1': $_GET['site'];
        $siteId  = $_GET['site'];
		
		
		
        $keyName = 'firstPageLogo';
        $sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
        $sitemeta = $wpdb->get_row($sql);
        $logo = $sitemeta->site_value;
        if((!empty($logo))&&(stristr($logo,"http")===false))
        {	$upload =wp_upload_dir();
			$logo=$upload['baseurl'].$logo;
		?>
			
			<div class="logo">
				<a href="<?php ;//echo home_url( '/' ); ?>"><img src="<?php echo $logo;?>" alt="" title="" border="0" /></a>
			</div>
     <?php   
		}
	?>
	

     
    
	<div class="menu">
    	<ul>  
        <?php
        //echo 'CSQ: '.$menuType;
        query_posts(array( 'post_type' => 'icons_menu','post_content_filtered'=> $siteId, 'orderby' => 'ID', 'order' => 'ASC', 'showposts' => '999'));
		//query_posts(array( 'post_type' => 'icons_menu','post_content_filtered'=> $siteId, 'orderby' => 'ID', 'order' => //'DESC', 'showposts' => '999'));
        ?>
             <?php if (have_posts()) : ?>
             <?php while (have_posts()) : the_post();
                     //echo $post->post_content;
                    // echo $post->post_content_filtered;
                 if($post->post_content_filtered ==  $siteId)
                 {
				 
					 /**
					*@description: add weid
					*@author: janeen
					*@version: add by janeen 20140430
					*/
					$menuiUrl=get_post_meta($post->ID, "menu_item_url", $single = true);
					/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
					$tmp = stristr(get_post_meta($post->ID, "menu_item_url", $single = true),"http");
					$itemurl=get_post_meta($post->ID, "menu_item_url", $single = true);
					if(($tmp===false)&&(!empty($itemurl))){
						$menuiUrl=home_url().get_post_meta($post->ID, "menu_item_url", $single = true);
					}else{				
						$menuiUrl=get_post_meta($post->ID, "menu_item_url", $single = true);
					}
					
					//是该网站内的才加weid
					$needle=$_SERVER['HTTP_HOST'];
					$tmparray=stristr($menuiUrl,$needle);
					if((!empty($menuiUrl))&&($tmparray)){
						$tmp=array();
						$noinfo=false;
						$ifhaveone=stristr($menuiUrl,"?");
						$ifhavetwo=stristr($menuiUrl,"#");
						$firloc=strpos($menuiUrl,"?");
						$endloc=strpos($menuiUrl,"#");
						
						if(($ifhaveone)&&($ifhavetwo)){	//有问号有井号				
							$query=substr($menuiUrl,$firloc+1,$endloc-$firloc-1);
						}else if(($ifhaveone)&&(!$ifhavetwo)){//有问号无井号
							$query=substr($menuiUrl,$firloc+1);
						}else{//无问号有井号+无问号无井号
							$noinfo=true;						
						}
						
						if(!$noinfo){
							$kvs=explode("&",$query);
							//print_r($kvs);							
							foreach($kvs as $k=>$v){
								$tmpkv = explode("=",$v);
								$tmp= array_merge ( $tmp, array($tmpkv[0] => $tmpkv[1] ) );
							}			
							
							if(empty($tmp['gweid'])){
								$tmp['gweid'] = $gweid;
							}
							//20140430$tmp['fromuser'] = $fromuser;
							
							$queryString = http_build_query($tmp);
							
							$las=explode("#",$menuiUrl);
							
							$paramurl=substr($menuiUrl,0,$firloc)."?".$queryString.($las[1]?"#".$las[1]:'');
							$menuiUrl=$paramurl;
						}else{
							
							if(empty($tmp['gweid'])){
								$tmp['gweid'] = $gweid;
							}
							//20140430$tmp['fromuser'] = $fromuser;
							$queryString = http_build_query($tmp);
							$las=explode("#",$menuiUrl);
							$paramurl=$menuiUrl."?".$queryString.($las[1]?"#".$las[1]:'');
							$menuiUrl=$paramurl;
						}
						
					}	//end				 
				 ?>
                    <li><a href="<?php echo  $menuiUrl?>"><?php the_post_thumbnail('menu-icon-size'); ?><?php the_title(); ?></a></li>
            <?php }
            endwhile; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>    
<?php get_footer(); ?>