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
*@function:��װgweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
	}

	//20150417 sara new added
	//���ݵ�ǰ��gweidȥ������û�д��ڹ���������£������������µģ���Ҫ��gweid��Ϊ����ŵ�gweid
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
/*�Ƿ���ʾ��Ա��ص�link�Լ�΢ѧУ��΢ԤԼ*/

?>
<!--��header�е�body�ı���ͼƬ���÷��õ�index��ҳ�����ã�����������ҳ��Ϳ��Բ�Ҫ����ҳ�ı���ͼƬ��-->
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
					/*���û��http��֤��Ϊ����������home_url��ʾ������ʱ���ж����ٽ�ȡ���*/
					$tmp = stristr(get_post_meta($post->ID, "menu_item_url", $single = true),"http");
					$itemurl=get_post_meta($post->ID, "menu_item_url", $single = true);
					if(($tmp===false)&&(!empty($itemurl))){
						$menuiUrl=home_url().get_post_meta($post->ID, "menu_item_url", $single = true);
					}else{				
						$menuiUrl=get_post_meta($post->ID, "menu_item_url", $single = true);
					}
					
					//�Ǹ���վ�ڵĲż�weid
					$needle=$_SERVER['HTTP_HOST'];
					$tmparray=stristr($menuiUrl,$needle);
					if((!empty($menuiUrl))&&($tmparray)){
						$tmp=array();
						$noinfo=false;
						$ifhaveone=stristr($menuiUrl,"?");
						$ifhavetwo=stristr($menuiUrl,"#");
						$firloc=strpos($menuiUrl,"?");
						$endloc=strpos($menuiUrl,"#");
						
						if(($ifhaveone)&&($ifhavetwo)){	//���ʺ��о���				
							$query=substr($menuiUrl,$firloc+1,$endloc-$firloc-1);
						}else if(($ifhaveone)&&(!$ifhavetwo)){//���ʺ��޾���
							$query=substr($menuiUrl,$firloc+1);
						}else{//���ʺ��о���+���ʺ��޾���
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