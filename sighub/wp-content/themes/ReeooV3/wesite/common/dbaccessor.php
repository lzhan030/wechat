<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include 'web_constant.php';


function web_admin_get_table_name($name) 
{
	global $wpdb;
	return $wpdb->prefix.$name;
}
function wp_wechat_usechat_info($user_id) {
	
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." where user_id='".$user_id."'" ); 
	return $myrows;

}
function wp_wechat_users_info($user_id) {
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("users")." where ID='".intval($user_id)."'" ); 
	return $myrows;

}
//登陆验证
function web_admin_verify_user($username, $password)
{
	global $wpdb;
	$myrows = $wpdb->get_results( $wpdb -> prepare("SELECT * FROM $wpdb->users where user_login=%s",$username)); 
	
	return !empty($myrows);
}

//获取模板信息
function web_admin_list_template()
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("md_mobilemeta"));
	return $myrows;
}
//获取新添加模板we7的信息
function web_admin_list_newtemplate()
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("site_templates")." where activate=1");
	return $myrows;
}
//获取原有五套模板的信息
function web_admin_list_orltemplate()
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("theme")." where activate=1 ORDER BY `id` ASC");
	return $myrows;
}
//计算被启用模板we7的个数
function web_admin_count_newtemplate()
{
	global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as newtempCount FROM " .web_admin_get_table_name('site_templates')." where activate=1");
	//echo "SELECT COUNT(*) as newtempCount FROM " .web_admin_get_table_name('site_templates');
	return $myrows;
}
//insert new wesite template
function web_admin_insert_newtemplate($name, $title, $activate, $background, $slide, $menu, $menu_bg, $image_icon)
{
	global $wpdb;
	$wpdb->query( $wpdb->prepare("INSERT INTO " .web_admin_get_table_name("site_templates"). "(name, title, activate, description, background, slide, menu, menu_bg, image_icon)VALUES (%s, %s, %d, %s, %d, %d, %d, %d, %d) ", $name, $title, $activate, $title, $background, $slide, $menu, $menu_bg, $image_icon));
	$id = $wpdb->insert_id;
	return $id;
}
//Get the last id of new template
function web_admin_lastnewtemplate()
{
	global $wpdb;
	$myrow = $wpdb->get_var("SELECT MAX(id) FROM " .web_admin_get_table_name('site_templates'));
	return $myrow;
}
//Select wesite info with id
function web_admin_getnewtemplate($id)
{
	global $wpdb;
	$myrow = $wpdb->get_row($wpdb -> prepare("SELECT * FROM " .web_admin_get_table_name('site_templates') . " WHERE id=%d", $id));
	return $myrow;
}
//Update the template with id
function web_admin_update_newtemplate($id, $title, $activate, $background, $slide, $menu, $menu_bg, $image_icon)
{
	global $wpdb;
	$myrow = $wpdb->update(web_admin_get_table_name("site_templates"),array('title'=>$title, 'activate' => $activate, 'background' => $background, 'slide' => $slide, 'menu' => $menu, 'menu_bg' => $menu_bg,'image_icon' => $image_icon),array('id'=>$id),array("%s", "%d", "%d", "%d", "%d", "%d", "%d"),array("%d"));
	return $myrow;
}
//mark the template as removed
function web_admin_deletenewtemplate($id){
	global $wpdb;
	$myrow = $wpdb->update(web_admin_get_table_name("site_templates"),array('removed'=>1),array('id'=>$id),array("%d"),array("%d"));
	return $myrow;
}
//计算原有的五套模板中被启用的模板个数
function web_admin_count_orgtemplate()
{
	global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as newtempCount FROM " .web_admin_get_table_name('theme')." where activate=1");
	return $myrows;
}

//得到Site配置数据
function web_admin_get_site_resource($site_id,$site_key,$default_value)
{
	global $wpdb;
	
	$myrows = $wpdb->get_results( $wpdb -> prepare("SELECT site_value FROM ".web_admin_get_table_name("orangesitemeta")." where site_key=%s and site_id=%s",$site_key,$site_id ));  
	foreach($myrows as $db_info)	
	{
		return $db_info->site_value;
	}
	return $default_value;	
}
//得到Site的名字
function web_admin_get_site_name($site_id)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT site_name FROM ".web_admin_get_table_name("orangesite")." where id='".intval($site_id)."'" ); 
	foreach($myrows as $db_info)	
	{
		return $db_info->site_name;
	}
	return "";
}

function web_admin_get_post_list($site_id, $post_type)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare( "SELECT * FROM $wpdb->posts where ".constant("CONF_SITE_COLUMN")."='".intval($site_id)."' and post_type=%s",$post_type)); 
	return $myrows;	
}

function  web_admin_set_mobile_themes_parameter($siteId, $site_title, $site_footer, $site_size, $site_color, $site_pic, $site_editor, $site_postpermission, $site_vipmember, $site_vipmember_editor,$site_contact)
{
	global $wpdb;
	
	if(($siteId == "") || ($site_title == "") || ($site_size == "") || ($site_color == "")) 
	{
		return;
	}
	
	if($site_pic == "") 
	{
		$site_pic="false";
	}
	if($site_editor == "") 
	{
		$site_editor="false";
	}
	if($site_postpermission == "") 
	{
		$site_postpermission = "false";
	}	
	if($site_vipmember == "") 
	{
		$site_vipmember="false";
	}
	if($site_vipmember_editor == "") 
	{
		$site_vipmember_editor="false";
	}
	
	$mysql = "insert into ".web_admin_get_table_name("orangesitemeta")."(site_id, site_key, site_value)values(%d, %s, %s)";
	// Remove the old value
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("orangesitemeta")." WHERE site_id=%d", $siteId));
	
	// Create the new value
	$wpdb->query( $wpdb->prepare($mysql, $siteId, "mobilethemeTitle", $site_title));
	$wpdb->query( $wpdb->prepare($mysql, $siteId, "mobilethemeFooter", $site_footer));
	$wpdb->query( $wpdb->prepare($mysql, $siteId, "mobilethemeSize", $site_size));
	$wpdb->query( $wpdb->prepare($mysql, $siteId, "mobilethemeColor", $site_color));
	$wpdb->query( $wpdb->prepare($mysql, $siteId, "mobilethemeIsShowPic", $site_pic));
	$wpdb->query( $wpdb->prepare($mysql, $siteId, "mobilethemeIsShowEditor", $site_editor));
	$wpdb->query( $wpdb->prepare($mysql, $siteId, "mobilethemeIsPostPermission", $site_postpermission));
	$wpdb->query( $wpdb->prepare($mysql, $siteId, "mobilethemeIsShowVipmember", $site_vipmember));
	$wpdb->query( $wpdb->prepare($mysql, $siteId, "mobilethemeIsShowVipmemberEditor", $site_vipmember_editor));	
	$wpdb->query( $wpdb->prepare($mysql, $siteId, "mobilethemeContact", $site_contact));		
}

//2014-07-15新增修改，传递gweid，创建站点
function web_admin_create_site($name, $themes, $userId, $gweid)
{
	global $wpdb;
	//将gweid写入数据库
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("orangesite")."(site_name, themes_key, site_user, GWEID)VALUES (%s, %s ,%d, %d)",$name, $themes,$userId, $gweid));
	$site_id=$wpdb->insert_id;
	
	$url = home_url();
    //$site_url=$url."/?site=".$site_id;
	$site_url="/?site=".$site_id;
	
	$update = $wpdb -> update(wp_orangesite,array('site_url'=>$site_url),array('id'=>$site_id),array("%s"),array("%d"));
	
	
	//在orangesitemeta里添加logo和背景图片的默认值
	//$bacUrl="http://wpcloudforsina-wordpress.stor.sinaapp.com/bac_image/bac_image.jpg";
	//$logoUrl="http://wpcloudforsina-wordpress.stor.sinaapp.com/logo_image/logo_image.png";
	//$bacUrl="/bac_image.jpg";
	$bacUrl="";
	$logoUrl="";
	$bacKey="firstPageBackgroup";
	$logoKey="firstPageLogo";
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("orangesitemeta")."(site_id, site_key,site_value)VALUES (%d, %s, %s )",$site_id, $bacKey, $bacUrl));
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("orangesitemeta")."(site_id, site_key,site_value)VALUES (%d, %s, %s )",$site_id, $logoKey, $logoUrl));
	
	
	return $site_id;
}


//删除站点
function web_admin_delete_site($siteid)
{
	global $wpdb;
	//根据siteid找到posts表中的对应的ID，再根据ID删除postmeta中的post_id
	$myrows = $wpdb->get_results( "SELECT ID,post_content FROM $wpdb->posts where ".constant("CONF_SITE_COLUMN")."='".intval($siteid)."'" ); 
	foreach($myrows as $my){
		$rows=$my->ID;
		file_unlink_from_xml($my->post_content);
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d", $rows ) );

	}
	//根据siteid查出所有的postid，删除postmeta中的postid
	//删除postmeta中post相关的记录
	
	//删除post表的相关记录
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ".constant("CONF_SITE_COLUMN")."= %d", $siteid ) );
	
	//删除orangesitemate中站点链接等的信息记录
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("orangesitemeta")." WHERE site_id = %d", $siteid ) );
	
	//删除站点信息表中的站点记录
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("orangesite")." WHERE id = %d", $siteid ) );
	
}

//列出所有站点
function web_admin_list_site($userId)
{
	global $wpdb;
	
	//自己建的表，这里不生效
	//$myrows = $wpdb->get_results( "SELECT * FROM $wpdb->orangesite"); 
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("orangesite")." where site_user=".intval($userId));
	return $myrows;
}
//2014-07-15新增修改,通过userid以及gweid找到所有站点，不能只通过userid
function web_admin_list_siteNew($userId, $gweid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("orangesite")." where site_user=".intval($userId)." AND GWEID = ".intval($gweid));
	return $myrows;
}

//获取特定站点信息
function web_admin_get_site($site_id)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("orangesite")." where id='".intval($site_id)."'" ); 
	return $myrows;
}
//ma获取新添加的14套模板信息
function web_admin_get_sitewe7($site_id)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("orangesitemeta")." where site_key='we7templatestyle' and site_id='".intval($site_id)."'"  ); 
	return $myrows;
}
//ma
//获取特定站点logo信息
function web_admin_get_site_logo($site_id)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("orangesitemeta")." where site_key='firstPageLogo' and site_id='".intval($site_id)."'" ); 
	return $myrows;
}

//修改特定站点logo信息
function web_admin_update_site_logo($site_id,$site_url)
{
	global $wpdb; 
	$siteKey="firstPageLogo";
	$update = $wpdb -> update(web_admin_get_table_name("orangesitemeta"),array('site_value'=>$site_url),array('site_id'=>$site_id,'site_key'=>$siteKey),array("%s"),array("%d","%s"));
	return $update;
	
}


//获取特定站点背景图片信息
function web_admin_get_site_bacimg($site_id)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("orangesitemeta") ." where site_key='firstPageBackgroup' and site_id='".intval($site_id)."'" );  
	return $myrows;
}

//修改特定站点背景图片信息
function web_admin_update_site_bacimg($site_id,$site_url)
{
	global $wpdb;
	$siteKey="firstPageBackgroup";
	$update = $wpdb -> update(web_admin_get_table_name("orangesitemeta"),array('site_value'=>$site_url),array('site_id'=>$site_id,'site_key'=>$siteKey),array("%s"),array("%d","%s"));
	return $update;
}

//获取所有的menu
function web_admin_list_menu($site_id)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM $wpdb->posts where post_type='icons_menu' and post_status='publish' and ".constant("CONF_SITE_COLUMN")."='".intval($site_id)."'" );		
	return $myrows;
}

//获取所有的menu的个数
function web_admin_count_menu($site_id)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as siteCount FROM $wpdb->posts where post_type='icons_menu' and post_status='publish' and ".constant("CONF_SITE_COLUMN")."='".intval($site_id)."'" );			
	return $myrows;
}


//拿到特定menu信息
function web_admin_get_menu($menid)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM $wpdb->posts where ID='".$menid."'" );	
	return $myrows;
}

//合并
function web_admin_get_gallery($galleryid)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM $wpdb->posts where ID='".$galleryid."'" );		
	return $myrows;
}

//拿到menu的url
function web_admin_get_menu_url($menid)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM $wpdb->postmeta where post_id='".$menid."' and meta_key='menu_item_url'"); 	
	return $myrows;
}

//拿到menu的img
function web_admin_get_menu_img($menid)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM $wpdb->postmeta where post_id='".intval($menid)."' and meta_key='_thumbnail_id'"); 	
	return $myrows;
}
	
//合并
function web_admin_get_gallery_imgs($galleryid)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM $wpdb->postmeta where post_id='".intval($galleryid)."' and meta_key='_thumbnail_id'"); 	
	return $myrows;
}	
	

//创建menu
function web_admin_create_menu($menu_title, $insert_imgid, $menuiUrl,$site_id)
{
	global $wpdb;
	$post_status="public";
	$post_name="hello";
	
	$url = home_url();
	//$guid=$url."/?"."post_type=icons_menu&#038;p=31";
	$guid="/?"."post_type=icons_menu&#038;p=31";
	$post_type="icons_menu";
	//插入最新的menu
		
	
	$wpdb->query( $wpdb->prepare(
	"
		INSERT INTO $wpdb->posts		(post_author,post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,guid,post_type,post_mime_type,post_status,comment_status,ping_status)
		VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s ,%s ,%s)
	",
	
	'1', $menu_title,'first','','','',$site_id,$guid,$post_type,'','publish','closed','closed'
	));
	
	
	//取到最新的插入的menuid
	$insert_meuid=$wpdb->insert_id;
			
	//menu跟图片对应记录
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_meuid, '_thumbnail_id',$insert_imgid));
	

	//menu跟它的链接对应
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_meuid, 'menu_item_url',$menuiUrl));
	
}

//更新menu
function web_admin_update_menu($menuid,$menu_title, $insert_imgid, $imgid,$menuiUrl,$urlid)
{
	global $wpdb;
	$post_status="public";
	$post_name="hello";
	$url = home_url();
	//$guid=$url."/?post_type=icons_menu&#038;p=31";
	$guid="/?post_type=icons_menu&#038;p=31";
	$post_type="icons_menu";
	
	$update = $wpdb -> update($wpdb->posts,array('post_author'=>'1','post_title'=>$menu_title,'post_content'=>'first','post_excerpt'=>'','to_ping'=>'','pinged'=>'','guid'=>$guid,'post_type'=>$post_type),array('ID'=>$menuid),array("%d","%s","%s","%s","%s","%s","%s","%s"),array("%d"));

			
	//menu跟图片对应记录更新（如果没有上传要更新的图片就不用更新了）
	if($insert_imgid!=null){
		if($insert_imgid!=-1){
			$update = $wpdb -> update($wpdb->postmeta,array('post_id'=>$menuid,'meta_key'=>'_thumbnail_id','meta_value'=>$insert_imgid),array('meta_id'=>$imgid),array("%d","%s","%s"),array("%d"));
		}else{
			$update = $wpdb -> update($wpdb->postmeta,array('post_id'=>$menuid,'meta_key'=>'_thumbnail_id','meta_value'=>""),array('meta_id'=>$imgid),array("%d","%s","%s"),array("%d"));
			
		}
	}

	//menu跟它的链接对应更新
	$update = $wpdb -> update($wpdb->postmeta,array('post_id'=>$menuid,'meta_key'=>'menu_item_url','meta_value'=>$menuiUrl),array('meta_id'=>$urlid),array("%d","%s","%s"),array("%d"));	
	
}

//删除menu
function web_admin_delete_menu($menuid)
{
	global $wpdb;
	//通过menuID找到postmeta中对应的menu图片和链接的文章
	//删除menu图片
	$bacid = $wpdb->get_results( "SELECT meta_value FROM ".web_admin_get_table_name("postmeta") ." where meta_key='_thumbnail_id' and post_id='".intval($menuid)."'" );  
	foreach($bacid as $bId){
		$bac_Id=$bId->meta_value;
		$deletes=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %d", $bac_Id ) );
		$deleted=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d", $bac_Id ) );
	}
	//删除menu链接的文章
	/* $postid = $wpdb->get_results( "SELECT meta_value FROM ".web_admin_get_table_name("postmeta") ." where meta_key='_thumbnail_id' and post_id='".$menuid."'" );   
	foreach($postid as $pId){
		$postId=$pId->meta_value;
		$explode=explode('=', $postId);
		$post_Id=$explode[1];
		$deletes=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %d", $post_Id ) );
		$deleted=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d", $post_Id ) );
	} */
	//删除posts中的menu
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d", $menuid ) );
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %d", $menuid ) );
	
	return $delete;
	
}


//获取所有的gallery
function web_admin_list_gallery($site_id)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM $wpdb->posts where post_type='gallery' and post_status='publish' and ".constant("CONF_SITE_COLUMN")."='".intval($site_id)."'" );			
	return $myrows;
}
//获取galley图片
function web_admin_get_gallery_img($post_id)
{ 	global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM $wpdb->postmeta where post_id='".intval($post_id)."' and meta_key='_thumbnail_id'"); 	   
	return $myrows;
}

//在post表里拿到gallery图片的记录
function web_admin_get_gallery_img_record($pic_id)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM $wpdb->posts where ID='".intval($pic_id)."'"); 	
	return $myrows;
}											

//创建gallery
function web_admin_create_gallery($insert_imgid,$site_id)
{
	global $wpdb;
	$post_status="public";
	$post_name="hello";
	$url = home_url();
	//$guid=$url."/?post_type=gallery&#038;p=225";
	$guid="/?post_type=gallery&#038;p=225";
	$post_type="gallery";
			
	$wpdb->query( $wpdb->prepare(
	"
		INSERT INTO $wpdb->posts		(post_author,post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,guid,post_type,post_mime_type,post_status,comment_status,ping_status)
		VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s ,%s ,%s)
	",
	
	'1', 'gallery_title','s','','','',$site_id,$guid,$post_type,'','publish','closed','closed'
	));
	
	
	//取到最新的插入的galleryid
	$insert_galleryid=$wpdb->insert_id;
			
	//gallery跟图片对应记录
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_galleryid, '_thumbnail_id',$insert_imgid));
	
	
}

//更新gallery
function web_admin_update_gallery($galleryid, $insert_imgid, $imgid)
{
	global $wpdb;
	$post_status="public";
	$post_name="hello";
	$url = home_url();
	//$guid=$url."/?post_type=gallery&#038;p=225";
	$guid="/?post_type=gallery&#038;p=225";
	$post_type="gallery";
	
	$update = $wpdb -> update($wpdb->posts,array('post_author'=>'1','post_title'=>'gallery_title','post_content'=>'s','post_excerpt'=>'','to_ping'=>'','pinged'=>'','guid'=>$guid,'post_type'=>$post_type),array('ID'=>$galleryid),array("%d","%s","%s","%s","%s","%s","%s","%s"),array("%d"));

			
	//gallery跟图片对应记录更新
	$update = $wpdb -> update($wpdb->postmeta,array('post_id'=>$galleryid,'meta_key'=>'_thumbnail_id','meta_value'=>$insert_imgid),array('meta_id'=>$imgid),array("%d","%s","%s"),array("%d"));
	
	
}

//删除gallery(合并)
function web_admin_delete_gallery($galleryid)
{
	global $wpdb;
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %d", $galleryid ) );
	return $delete;
}

//添加图片
function web_admin_add_image($picUrl,$path,$site_id)
{	
	global $wpdb;
	
	//往post里添加图片记录
	$wpdb->query( $wpdb->prepare(
	"
		INSERT INTO $wpdb->posts		(post_author,post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,guid,post_type,post_mime_type,post_status)
		VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )
	",
	
	'1', '','s','','','',$site_id,$picUrl,'attachment','image/jpeg','inherit'
	
	));

   
   //获取刚插入图片的id
	$insert_imgid=$wpdb->insert_id;


	//往post_meta里添加两条图片信息记录（需要获取图片id）
	
	$s="picDescription";
	
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_imgid, '_wp_attached_file',$path));
	
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_imgid, '_wp_attachment_metadata',$s));
	
	return $insert_imgid;
	
}

function web_admin_create_post_and_attachment($post_title, $post_content,$site_id)
{
  $my_post = array();    
  $my_post['post_title'] = $post_title;    
  $my_post['post_content'] = $post_content;    
  $my_post['post_status'] = 'publish';       
  $my_post['post_content_filtered'] = $site_id; 
  
  $post_id = wp_insert_post($my_post); 
  
  $attachurl = null;
  if($post_content != null) {
	$img = strstr($post_content, "<img");
	if($img != null) {
		$src=strstr($img, "src=");
		if($src != null) {
			$arr = explode("\"", $src);
			foreach($arr as $elem) {
				if(strstr($elem, "http:") != null) {
					$attachurl=$elem;
					break;
				}
			}
		}
	}
  }
  
  if($attachurl == null) {
	return $post_id;
  }
  
  //Now we have the picture URL
  $arr = explode(".", $attachurl);
  $totalSize=count($arr);
  if($totalSize > 0) {
	$type="image/".$arr[$totalSize-1];
  }


  $attach_post = array();    
  $attach_post['post_title'] = "image";    
  $attach_post['post_content'] = "";    
  $attach_post['post_status'] = 'open';   
  $attach_post['post_parent'] = $post_id;   
  $attach_post['post_mime_type'] = $type;   
  $attach_post['guid'] = $attachurl;      
  $attach_post['post_content_filtered'] = $site_id;
  $attach_id = wp_insert_attachment($attach_post); 
  $result = set_post_thumbnail($post_id, $attach_id);
}

//创建post
function web_admin_create_post($post_title, $post_content,$site_id,$post_type)
{
	global $wpdb;
	global  $current_user;
	$post_status="public";
	$post_name="hello";
	$url = home_url();
	//$guid=$url."/?page_id=";
	$guid="/?page_id=";
	
	if($post_type == "post")
	{
		//$guid=$url."/?p=";
		$guid="/?p=";
	}
	date_default_timezone_set('PRC');
	
	
	/*处理文章内容入库时的图片地址问题*/	
	$upload =wp_upload_dir();
	$baseurl=$upload['baseurl'];
	/*var_dump($baseurl);//http://www.sighub.com/wechat/wp-content/uploads
	 $post_content =  str_ireplace($baseurl,"", $post_content);
	
	$post_content =  str_ireplace("/wechat/wp-content/uploads","", $post_content);
	$post_content =  str_ireplace("/mobile/wp-content/uploads","", $post_content);
	$post_content =  str_ireplace($baseurl,"", $post_content);
	*/
	//$post_content =  preg_replace('/(?:\w+\:)?\/\/'. preg_quote(preg_replace('/https?\:\/\/(.+)/i', '$1', get_bloginfo('url')), '/') .'/i', '', '');
	
	$post_content =preg_replace('#(?<=src=(\'|"))(('.home_url().'|(?!\w+://))[\w%\/.\-_]+uploads(?=/))|'.$baseurl.'(?=[\w%\/.\-_]+\")#i','',$post_content);

	//当前用户是否分组管理员
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$user_id = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
	//$user_id = (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;

	$wpdb->query( $wpdb->prepare(
	"
		INSERT INTO $wpdb->posts (post_author,post_date,post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,guid,post_type,post_mime_type,post_status,comment_status,ping_status)
		VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s ,%s ,%s)
	",
	
	$user_id, date('Y-m-d H:i:s'), $post_title,$post_content,'','','',$site_id,$guid,$post_type,'','publish','open','open'
	));
	
	
	//取到最新的插入的postid
	$insert_postid=$wpdb->insert_id;
		
	//在postmeta里插入post对应的记录
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_postid, '_wp_page_template','default'));
	
	$guid_new=$guid.$insert_postid;
	
	$update = $wpdb -> update($wpdb->posts,array('guid'=>$guid_new),array('ID'=>$insert_postid),array("%s"),array("%d"));
	
	//20141015查看该gweid是否需要syncup功能
	$gweidsql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}orangesite where id=%d",$site_id);
	$getgweids = $wpdb->get_results($gweidsql);
	foreach($getgweids as $getgweid)
	{
	    $themes = $getgweid -> themes_key;	
	    //$gweid = $getgweid -> GWEID;	
	}
	if($themes == 8) //20141117改为新添加的mobilethemesync需要同步功能
	{
		$user_info = get_userdata($user_id);
		$post_author = $user_info -> display_name;   //显示文章用的是display_name 
		if($insert_postid!==false){
			
			//20141014add post data to new server
			$url = THIRD_PARTY_ACCESS_URL;        //这个是在wp-config.php中定义的变量
			$post_data = array (
				"to_ping" => "",
				"pinged" => "",
				"post_title" => $post_title,
				"post_content" => $post_content,
				"post_excerpt" => "",
				"post_content_filtered" => $site_id,
				"id" => intval($insert_postid),
				"post_date" => time()*1000,
				"post_name" => "",
				"guid" => $guid_new,
				"menu_order" => "",
				"post_type" => $post_type,
				"post_author" => $post_author,
				//"post_date_gmt" => time()*1000,
				"post_date_gmt" => strtotime("-8 hours")*1000,  //格林时间少8个小时
				"post_status" => "publish",
				"comment_status" => "open",
				"ping_status" => "open",
				"post_password" => "",
				"post_modified" => "",
				"post_modified_gmt" => "",
				"post_parent" => "",
				"post_mime_type" => "",
				"comment_count" => 0
			);
			$postdata = json_encode($post_data);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			//指定post数据
			curl_setopt($ch, CURLOPT_POST, 1);
			//添加变量
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json;charset=UTF-8;Connection:close'));
			curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.2 (KHTML, like Gecko) Ubuntu/10.04 Chromium/15.0.874.106 Chrome/15.0.874.106 Safari/535.2");
			
			$output = curl_exec($ch);
			$error_code = curl_errno($ch);
			$rtn = curl_getinfo($ch,CURLINFO_HTTP_CODE);  
			//返回200ok表示正确sync up了，否则重新提交
			if ($rtn != '200') {
				//向post_meta表中添加一条字段,flag表示文章同步，表示文章同步与否
				$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_postid, 'syncup_status',0));
				return $insert_postid."文章添加成功,但是同步失败,请更新该文章重新进行同步";
				
			}else{
				//向post_meta表中添加一条字段,flag表示文章同步，表示文章同步与否
				$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_postid, 'syncup_status',1));
				//取到最新的插入的postmetaid
				$insert_postmetaid=$wpdb->insert_id;
				if($insert_postmetaid!==false){     //状态在本地数据库插入成功
					return $insert_postid."文章添加成功,同步也成功";
				}else{        //状态在本地数据库插入不成功,需要重新更新文章重新同步
					return $insert_postid."文章添加成功,同步也成功,但是同步状态更新失败,请更新该文章重新进行同步";
				}
			}
			curl_close($ch);

		}else{
			return "error文章添加失败,同步失败,请重新添加并同步";  // 创建成功
		}
		
	}else{
	
		if($insert_postid !== false)
		{	 return $insert_postid."添加成功";
		}
		else{
			return $insert_postid;
		}
	}
	
}

//拿到特定post信息（可以合并）
function web_admin_get_post($postid)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM $wpdb->posts where ID='".intval($postid)."'" );	
	return $myrows;
}

//判断是否有这个post
function web_admin_post_exist($postid)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM $wpdb->posts where ID='".intval($postid)."'" );	
	return !empty($myrows);
}

//更新post
function web_admin_update_post($post_title, $post_content,$post_id)
{
	global $wpdb;
	/*处理文章内容入库时的图片地址问题*/	
	$upload =wp_upload_dir();
	$baseurl=$upload['baseurl'];
	$homeurl=home_url()."/wp-content/uploads";
	/*
	//$post_content =  str_ireplace($baseurl,"", $post_content);
	$post_content =  str_ireplace($homeurl,"", $post_content);
	$post_content =  str_ireplace("/wechat/wp-content/uploads","", $post_content);
	$post_content =  str_ireplace("/mobile/wp-content/uploads","", $post_content);
	$post_content =  str_ireplace($baseurl,"", $post_content);*/
	
	$post_content =preg_replace('#(?<=src=(\'|"))(('.home_url().'|(?!\w+://))[\w%\/.\-_]+uploads(?=/))|'.$baseurl.'(?=[\w%\/.\-_]+\")#i','',$post_content);
	
	$update = $wpdb -> update($wpdb->posts,array('post_title'=>$post_title,'post_content'=>$post_content),array('ID'=>$post_id),array("%s","%s"),array("%d"));
	
	//20141014add post data to new server
	$getposts = $wpdb->get_results(  "SELECT * FROM $wpdb->posts where ID=".intval($post_id));	
	foreach($getposts as $getpost)
	{
	    $user_id = $getpost -> post_author;
		$user_info = get_userdata($user_id);
        $post_author = $user_info -> display_name;   //显示文章用的是display_name 
		$post_date = $getpost -> post_date;
		$post_date_gmt = $getpost -> post_date_gmt;
		$comment_status = $getpost -> comment_status;
		$post_content_filtered = $getpost -> post_content_filtered;
		$guid = $getpost -> guid;
		$menu_order = $getpost -> menu_order;
		$post_type = $getpost -> post_type;
		$post_modified_gmt = $getpost -> post_modified_gmt;
		$post_parent = $getpost -> post_parent;
		$post_mime_type = $getpost -> post_mime_type;
		$comment_count = $getpost -> comment_count;
	}
	//20141117改为mobilethemesync需要同步功能，注意posts表中的post_content_filtered被用来表示对应的site_id
	$gweidsql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}orangesite where id=%d",$post_content_filtered);
	$getgweids = $wpdb->get_results($gweidsql);
	foreach($getgweids as $getgweid)
	{
	    $gweid = $getgweid -> GWEID;
		$themes = $getgweid -> themes_key;	
	}

	if($themes == 8) 
	{
		if($update!==false){
			$url = THIRD_PARTY_ACCESS_URL;    //这个是在wp-config.php中定义的变量
			$post_data = array (
					"to_ping" => "",
					"pinged" => "",
					"post_title" => $post_title,
					"post_content" => $post_content,
					"post_excerpt" => "",
					"post_content_filtered" => $post_content_filtered,
					"id" => intval($post_id),
					"post_date" => (strtotime("-8 hour",strtotime($post_date))*1000),
					"post_name" => "",
					"guid" => $guid,
					"menu_order" => intval($menu_order),
					"post_type" => $post_type,
					"post_author" => $post_author,
					"post_date_gmt" => (strtotime("-16 hour",strtotime($post_date))*1000),  //gmt时间少8个小时
					"post_status" => "publish",
					"comment_status" => $comment_status,
					"ping_status" => "open",
					"post_password" => "",
					"post_modified" => time()*1000,
					"post_modified_gmt" => strtotime("-8 hours")*1000,
					"post_parent" => intval($post_parent),
					"post_mime_type" => $post_mime_type,
					"comment_count" => intval($comment_count)
				);
			
			$postdata = json_encode($post_data);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			//指定post数据
			curl_setopt($ch, CURLOPT_POST, 1);
			//添加变量
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json;charset=UTF-8;Connection:close'));
			curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.2 (KHTML, like Gecko) Ubuntu/10.04 Chromium/15.0.874.106 Chrome/15.0.874.106 Safari/535.2");
			$output = curl_exec($ch);
			$error_code = curl_errno($ch);
			$rtn = curl_getinfo($ch,CURLINFO_HTTP_CODE);  
			
				
			//返回200ok表示正确sync up了，否则重新提交
			if ($rtn != '200') {
				//将同步结果更新到数据库中,先查找下数据库中是否有该记录，否则执行更新或者插入
				$sqlcount = $wpdb -> prepare("SELECT count(*) as scount FROM wp_postmeta WHERE post_id = %d and meta_key = 'syncup_status'", $post_id);
				$getstatus = $wpdb->get_results($sqlcount);
				foreach($getstatus as $gstatus)
				{
					$counts = $gstatus -> scount;
				}
				if($counts == 0)
				{
					$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$post_id, 'syncup_status',0));
				}else{
					$update1 = $wpdb -> update($wpdb->postmeta,array('meta_value'=>0),array('post_id'=>$post_id,'meta_key'=>'syncup_status',),array("%d"),array("%d","%s"));
				}
			    return $update."文章更新成功,但是同步失败,请重新更新进行同步";
				
			}else{
				//将同步结果更新到数据库中,先查找下数据库中是否有该记录，否则执行更新或者插入
				$sqlcount = $wpdb -> prepare("SELECT count(*) as scount FROM wp_postmeta WHERE post_id = %d and meta_key = 'syncup_status'", $post_id);
				$getstatus = $wpdb->get_results($sqlcount);
				foreach($getstatus as $gstatus)
				{
					$counts = $gstatus -> scount;
				}
				if($counts == 0)
				{
					$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$post_id, 'syncup_status',1));
				}else{
					$update1 = $wpdb -> update($wpdb->postmeta,array('meta_value'=>1),array('post_id'=>$post_id,'meta_key'=>'syncup_status',),array("%d"),array("%d","%s"));
				}
				return $update."文章更新成功,同步也成功";
			}
			curl_close($ch);
		}else{
			return "error文章更新失败,同步失败,请重新更新和同步";
		}
		
	}
	else{
		if($update !== false)
		{
		    return $update."更新成功";
		}
		else{
			return "更新失败";
		}
	}
	
	
}
//获取当页的所有数据集
function web_admin_array_post($offset,$pagesize,$site_id,$post_type)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM $wpdb->posts where ".constant("CONF_SITE_COLUMN")."=%s and post_type=%s ORDER BY ID DESC limit ".$offset.",".$pagesize,$site_id,$post_type ));
	
	
	return $myrows;
}
//获取所有的post的个数
function web_admin_count_post($site_id,$post_type)
{
    global $wpdb;
    $myrows = $wpdb->get_results($wpdb -> prepare("SELECT COUNT(*) as postCount FROM $wpdb->posts where ".constant("CONF_SITE_COLUMN")."=%d and post_type=%s",$site_id,$post_type));
	
	return $myrows;
	}
//获取当页数据集的个数
function web_admin_array_post_count($offset,$pagesize,$site_id,$post_type)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb ->prepare("SELECT COUNT(*) as arrayCount FROM $wpdb->posts where ".constant("CONF_SITE_COLUMN")."=%d and post_type=%s limit ".$offset.",".$pagesize,$site_id,$post_type));
    
	return $myrows;
	}   


//删除该网站文章
function web_admin_delete_post($postid)
{
    global $wpdb;
	
	//如果删除成功，则通知第三方server
	//20141105只有模板1需要sync up功能，syncup后，如果删除该文章，需要给第三方server发送一个message
	//需要先取出该post所在的site_id,否则该条语句被删除了就取不到了
	$postsql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}posts where ID = %d",$postid);
	$getposts = $wpdb->get_results($postsql);
	foreach($getposts as $getpost)
	{
	    $user_id = $getpost -> post_author;
	    $post_title = $getpost -> post_title;
		$post_content = $getpost -> post_content;
		$site_id = $getpost -> post_content_filtered;	
		$post_type = $getpost -> post_type; 
		$post_date = $getpost -> post_date;
	}
	file_unlink_from_xml($post_content);
	//删除文章
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("posts")." WHERE ID = %d", $postid) );
	$deletes=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("postmeta")." WHERE post_id = %d", $postid) );
	
	$gweidsql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}orangesite where id=%d",$site_id);
	$getgweids = $wpdb->get_results($gweidsql);
	foreach($getgweids as $getgweid)
	{
		$themes = $getgweid -> themes_key;	
		$gweid = $getgweid -> GWEID;	
	}
	//只有模板一会有同步的功能
	if($themes == 8)  //20141118改为模板8有同步功能
	{
	
		if($delete !== false)
		{
			$url = THIRD_PARTY_ACCESS_URL;        //这个是在wp-config.php中定义的变量
			$user_info = get_userdata($user_id);
		    $post_author = $user_info -> display_name;   //显示文章用的是display_name 
			$post_data = array (
					"to_ping" => "",
					"pinged" => "",
					"post_title" => $post_title,
					"post_content" => $post_content,
					"post_excerpt" => "",
					"post_content_filtered" => $site_id,
					"id" => intval($postid),
					"post_date" => (strtotime("-8 hour",strtotime($post_date))*1000),
					"post_name" => "",
					"guid" => "",
					"menu_order" => "",
					"post_type" => $post_type,
					"post_author" => $post_author,
					"post_date_gmt" => (strtotime("-16 hour",strtotime($post_date))*1000), //gmt少8个小时
					"post_status" => "publish",   //该值为delete时，表示做了删除操作
					"comment_status" => "open",
					"ping_status" => "DELETE",
					"post_password" => "",
					"post_modified" => "",
					"post_modified_gmt" => "",
					"post_parent" => "",
					"post_mime_type" => "",
					"comment_count" => 0
				);
			$postdata = json_encode($post_data);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			//指定post数据
			curl_setopt($ch, CURLOPT_POST, 1);
			//添加变量
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json;charset=UTF-8;Connection:close'));
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.2 (KHTML, like Gecko) Ubuntu/10.04 Chromium/15.0.874.106 Chrome/15.0.874.106 Safari/535.2");
			$output = curl_exec($ch);
			//获取第三方server返回的状态
			$rtn = curl_getinfo($ch,CURLINFO_HTTP_CODE);  				
			//返回200ok表示正确sync up了，否则重新提交
			
			//查看该文章同步成功还是失败，之前同步失败，所以直接返回就可以，不用判断同步删除是否成功
			$sqlstatus = $wpdb -> prepare("SELECT * FROM wp_postmeta WHERE post_id = %d and meta_key = 'syncup_status'", $postid);
			$getstatus = $wpdb->get_results($sqlstatus);
			
			if(empty($getstatus))   //之前同步失败，所以直接返回就可以，不用判断同步删除是否成功
			{
				return $delete."同步状态失败";
			}else{
				foreach($getstatus as $gstatus)
				{
					$status = $gstatus -> meta_value;
				}
				if($status == 0)   //同步原本就是失败，所以直接返回就可以，不用判断同步删除是否成功
				{
					return $delete."同步状态失败";
				}
				elseif($status == 1)
				{
				    if ($rtn != '200') {
						return $delete."同步删除失败";
					}else{
						return $delete."同步删除成功";
					}
				}				   
			}
			curl_close($ch);

		}else{
		    return $delete;
		}
	}
	
	return $delete;
}	

//产生随机token
function generate_password( $length = 10 ) { 
	// 密码字符集，可任意添加你需要的字符 
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';  
	$password = '';  
	for ( $i = 0; $i < $length; $i++ )  
	{  
	 	// 这里提供两种字符获取方式  
	 	// 第一种是使用 substr 截取$chars中的任意一位字符；  
	 	// 第二种是取字符数组 $chars 的任意元素  
	 	// $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);  
	    $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
	}  
	    return $password; 
} 

//获取当前用户的初始化功能列表
function web_admin_list_initfunction($userId)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." WHERE user_id = ".$userId."   AND EXISTS(SELECT * FROM  `".$wpdb->prefix."wechat_func_info` WHERE ".$wpdb->prefix."wechat_func_info.`func_name` =  ".$wpdb->prefix."wechat_initfunc_info.func_name AND  `status` =1 )");
	return $myrows;
}

//更新用户的初始化功能列表
function web_admin_update_initfunction($userId,$funcname,$funcflag)
{
	global $wpdb; 
	$update = $wpdb -> update(web_admin_get_table_name("wechat_initfunc_info"),array('func_flag'=>$funcflag),array('user_id'=>$userId,'func_name'=>$funcname),array("%d"),array("%d","%s"));
	return $update;
}

//更新用户的初始化功能（模板选择）列表
function web_admin_update_inittemplatefunction($userId,$funcname,$funcflag)
{
	global $wpdb; 
	
	$wpdb->query( " UPDATE ".web_admin_get_table_name('wechat_initfunc_info')." SET func_name = '".$funcname."' WHERE user_id = ".$userId." AND func_name like '%template%' ");
	
	return $update;
}

//获取所有的user的个数
function web_admin_count_user()
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as userCount FROM ".web_admin_get_table_name("users")." where user_pass !='' ");                            
	return $myrows;
}
//获取相应group中的user的个数
function web_admin_count_user_groupid($groupid)
{
    global $wpdb;
	if($groupid == -1) //列出全部
	{
		$myrows = $wpdb->get_results("SELECT COUNT(*) as userCount FROM ".web_admin_get_table_name("users")." where user_pass !='' ");
	}
	elseif($groupid == 0) //默认分组对应的id为0的，但是有些用户没有分过组
	{
	   $myrows = $wpdb->get_results( "SELECT COUNT(*) as userCount FROM ".web_admin_get_table_name("users")." w2 left join ".web_admin_get_table_name("user_group")." w3 on w2.ID = w3.user_id WHERE w2.user_pass != '' AND (isnull(w3.group_id) OR w3.group_id = 0) order by w2.ID ASC" );
	}
	else{    //取出当前分组中的用户
	   $myrows = $wpdb->get_results( "SELECT COUNT(*) as userCount FROM ".web_admin_get_table_name("users")." w2 left join ".web_admin_get_table_name("user_group")." w3 on w2.ID = w3.user_id WHERE w3.group_id = ".intval($groupid)." order by w2.ID ASC" );
	}
	                            
	return $myrows;
}
//获取user当页的所有数据集
function web_admin_array_user($offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("users")." w1 where w1.user_pass != '' ORDER BY w1.ID DESC limit ".$offset.",".$pagesize );
	return $myrows;
}
//获取user当页的所有数据集
function web_admin_array_user_groupid($offset,$pagesize,$groupid)
{
    global $wpdb;
	
	if($groupid == -1) //列出全部
	{
		$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("users")." w1 where w1.user_pass != '' ORDER BY w1.ID DESC limit ".$offset.",".$pagesize );
	}
	elseif($groupid == 0) //取出默认分组中的用户
	{
	    $myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("users")." w2 left join ".web_admin_get_table_name("user_group")." w3 on w2.ID = w3.user_id WHERE w2.user_pass != '' AND (isnull(w3.group_id) OR w3.group_id = 0) order by w2.ID DESC limit ".$offset.",".$pagesize );
	}
	else{    //取出当前分组中的用户
	    $myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("users")." w2 left join ".web_admin_get_table_name("user_group")." w3 on w2.ID = w3.user_id WHERE w3.group_id = ".intval($groupid)." order by w2.ID DESC limit ".$offset.",".$pagesize );
	}
	
	return $myrows;
}

//获取当页数据集的个数
function web_admin_array_user_count($offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("users")." where user_pass != '' limit ".$offset.",".$pagesize ); 
	return $myrows;
}   

 //获取当页的所有数据集
function web_admin_array_website($offset,$pagesize,$userId)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("orangesite"). " where site_user=".intval($userId)." ORDER BY id DESC limit ".$offset.",".$pagesize );
	return $myrows;
}

//2014-07-15新增修改
function web_admin_array_websiteNew($offset,$pagesize,$userId,$gweid)
{
    global $wpdb;
  	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("orangesite"). " where site_user = ".$userId." AND GWEID = ".$gweid." ORDER BY id DESC limit ".$offset.",".$pagesize );
	return $myrows;
}
//获取所有的website的个数
function web_admin_count_website($userId)
{
    global $wpdb;
    $myrows = $wpdb->get_results("SELECT COUNT(*) as websiteCount FROM ".web_admin_get_table_name("orangesite")." where site_user=".$userId);               
	return $myrows;
}
//2014-07-15新增修改
function web_admin_count_websiteNew($userId, $gweid)
{
    global $wpdb;
    $myrows = $wpdb->get_results("SELECT COUNT(*) as websiteCount FROM ".web_admin_get_table_name("orangesite")." where site_user=".intval($userId)." AND GWEID = ".intval($gweid));               
	return $myrows;
}
//获取当页数据集的个数
function web_admin_array_website_count($offset,$pagesize,$userId)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("orangesite"). " where site_user=".intval($userId)." ORDER BY id DESC limit ".$offset.",".$pagesize);
    
	return $myrows;
}   
//2014-07-15新增修改
function web_admin_array_website_countNew($offset,$pagesize,$userId,$gweid)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("orangesite"). " where site_user=".intval($userId)." AND GWEID = ".intval($gweid)." ORDER BY id DESC limit ".$offset.",".$pagesize);
    
	return $myrows;
} 

//获取特定站点背景图片的总数量
function web_admin_get_site_bacimg_count($site_id)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as bacCount FROM ".web_admin_get_table_name("orangesitemeta") ." where site_key='firstPageBackgroup' and site_id='".intval($site_id)."'" );  
	return $myrows;
}

//增加特定站点背景图片信息
function web_admin_insert_site_bacimg($site_id,$site_url)
{
	global $wpdb;
	$siteKey="firstPageBackgroup";
	//$update = $wpdb -> update(web_admin_get_table_name("orangesitemeta"),array('site_value'=>$site_url),array('site_id'=>$site_id,'site_key'=>$siteKey),array("%s"),array("%d","%s"));
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("orangesitemeta")."(site_id,site_key,site_value)VALUES (%d, %s, %s)",$site_id, $siteKey, $site_url));
	return $update;
}

//删除特定站点背景图片
function web_admin_delete_site_bacimg($Id)
{
	global $wpdb;
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("posts")." WHERE ID = %d", $Id ) );
	return $delete;
}

//创建模板3的menu
function web_admin_create_menu3($menu_title, $insert_imgid, $menuiUrl,$site_id)
{
	global $wpdb;
	$post_status="public";
	$post_name="hello";
	
	$url = home_url();
	//$guid=$url."/?"."post_type=icons_menu_v3&#038;p=31";
	$guid="/?"."post_type=icons_menu_v3&#038;p=31";
	$post_type="icons_menu_v3";
	//插入最新的menu
		
	
	$wpdb->query( $wpdb->prepare(
	"
		INSERT INTO $wpdb->posts		(post_author,post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,guid,post_type,post_mime_type,post_status,comment_status,ping_status)
		VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s ,%s ,%s)
	",
	
	'1', $menu_title,'first','','','',$site_id,$guid,$post_type,'','publish','closed','closed'
	));
	
	
	//取到最新的插入的menuid
	$insert_meuid=$wpdb->insert_id;
			
	//menu跟图片对应记录
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_meuid, '_thumbnail_id',$insert_imgid));
	

	//menu跟它的链接对应
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_meuid, 'menu_item_url',$menuiUrl));
	
}

//获取模板3所有的menu
function web_admin_list_menu3($site_id)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM $wpdb->posts where post_type='icons_menu_v3' and post_status='publish' and ".constant("CONF_SITE_COLUMN")."='".intval($site_id)."'" );		
	
	return $myrows;
}

//创建模板3的gallery
function web_admin_create_post3($post_title, $post_content,$site_id,$post_type)
{
	global $wpdb;
	global  $current_user;
	$post_status="public";
	$post_name="hello";
	$url = home_url();
	//$guid=$url."/?post_id=";
	$guid="/?post_id=";
	
	if($post_type == "post")
	{
		//$guid=$url."/?p=";
		$guid="/?p=";
	}
	date_default_timezone_set('PRC');
	
	/*处理文章内容入库时的图片地址问题*/	
	$upload =wp_upload_dir();
	$baseurl=$upload['baseurl'];
	
	//$post_content =  str_ireplace($baseurl,"", $post_content);
	
	/*$post_content =  str_ireplace("/wechat/wp-content/uploads","", $post_content);
	$post_content =  str_ireplace("/mobile/wp-content/uploads","", $post_content);
	$post_content =  str_ireplace($baseurl,"", $post_content);
	*/
	
	$post_content =preg_replace('#(?<=src=(\'|"))(('.home_url().'|(?!\w+://))[\w%\/.\-_]+uploads(?=/))|'.$baseurl.'(?=[\w%\/.\-_]+\")#i','',$post_content);
	//当前用户是否分组管理员
	$groupadminflag = web_admin_issuperadmin($current_user->ID);
	$user_id = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	
	//$user_id = (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;

	$wpdb->query( $wpdb->prepare(
	"
		INSERT INTO $wpdb->posts (post_author,post_date,post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,guid,post_type,post_mime_type,post_status,comment_status,ping_status)
		VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s ,%s ,%s)
	",
	
	$user_id, date('Y-m-d H:i:s'), $post_title,$post_content,'','','',$site_id,$guid,$post_type,'','publish','open','open'
	));
	
	
	//取到最新的插入的postid
	$insert_postid=$wpdb->insert_id;
	//$insert_postid_title=$post_title;
			
	//在postmeta里插入post对应的记录
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_postid, '_wp_page_template','default'));
	
	$guid_new=$guid.$insert_postid;
	
	$update = $wpdb -> update($wpdb->posts,array('guid'=>$guid_new),array('ID'=>$insert_postid),array("%s"),array("%d"));
	
	return $insert_postid;
	
}

//获取所有的模板3的menu的个数
function web_admin_count_menu3($site_id)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as siteCount FROM $wpdb->posts where post_type='icons_menu_v3' and post_status='publish' and ".constant("CONF_SITE_COLUMN")."='".intval($site_id)."'" );		
	
	return $myrows;
}

//更新模板3的menu
function web_admin_update_menu3($menuid,$menu_title, $insert_imgid, $imgid,$menuiUrl,$urlid)
{
	global $wpdb;
	$post_status="public";
	$post_name="hello";
	$url = home_url();
	//$guid=$url."/?post_type=icons_menu_v3&#038;p=31";
	$guid="/?post_type=icons_menu_v3&#038;p=31";
	$post_type="icons_menu_v3";
	
	$update = $wpdb -> update($wpdb->posts,array('post_author'=>'1','post_title'=>$menu_title,'post_content'=>'first','post_excerpt'=>'','to_ping'=>'','pinged'=>'','guid'=>$guid,'post_type'=>$post_type),array('ID'=>$menuid),array("%d","%s","%s","%s","%s","%s","%s","%s"),array("%d"));

			
	//menu跟图片对应记录更新（如果没有上传要更新的图片就不用更新了）
	if($insert_imgid!=null){	
		$update = $wpdb -> update($wpdb->postmeta,array('post_id'=>$menuid,'meta_key'=>'_thumbnail_id','meta_value'=>$insert_imgid),array('meta_id'=>$imgid),array("%d","%s","%s"),array("%d"));
	}

	//menu跟它的链接对应更新
	$update = $wpdb -> update($wpdb->postmeta,array('post_id'=>$menuid,'meta_key'=>'menu_item_url','meta_value'=>$menuiUrl),array('meta_id'=>$urlid),array("%d","%s","%s"),array("%d"));	
	
}

//往模板3添加背景图片
function web_admin_add_image3($picUrl,$path,$site_id,$baimg_title)
{	
	global $wpdb;
	
	//往post里添加图片记录
	$wpdb->query( $wpdb->prepare(
	"
		INSERT INTO $wpdb->posts		(post_author,post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,guid,post_type,post_mime_type,post_status,post_name)
		VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
	",
	
	'1', '','','','','',$site_id,$picUrl,'attachment','image/jpeg','inherit',''
	
	));

   
   //获取刚插入图片的id
	//echo $wpdb->insert_id;
	$insert_imgid=$wpdb->insert_id;


	//往post_meta里添加两条图片信息记录（需要获取图片id）
	
	$s="picDescription";
	
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_imgid, '_wp_attached_file',$path));
	
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_imgid, '_wp_attachment_metadata',$s));
	
	return $insert_imgid;
	
}

  //产生模板3背景名称容器
function web_admin_create_slider($baimg_title,$site_id,$insert_imgid)
{
	global $wpdb;
	$post_status="public";
	$post_name="hello";
	
	$url = home_url();
	//$guid=$url."/?"."post_type=slider&#038;p=31";
	$guid="/?"."post_type=slider&#038;p=31";
	$post_type="slider";
	//插入最新的slider
		
	
	$wpdb->query( $wpdb->prepare(
	"
		INSERT INTO $wpdb->posts(post_author,post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,guid,post_type,post_mime_type,post_status,comment_status,ping_status)
		VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s ,%s ,%s)
	",
	
	'1', $baimg_title,'','','','',$site_id,$guid,$post_type,'','publish','closed','closed'
	));
	
	
	//取到最新的插入的sliderid
	$insert_meuid=$wpdb->insert_id;
			
	//slider跟图片对应记录
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_meuid, '_thumbnail_id',$insert_imgid));
	

	//slider跟它的链接对应
	//$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_meuid, 'menu_item_url',$menuiUrl));
	
}

//获取模板3特定站点slider信息
function web_admin_get_site_slider3($site_id)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare( "SELECT * FROM ".web_admin_get_table_name("posts") ." where post_type='slider' and post_content_filtered=%s",$site_id) );  
	return $myrows;
}

//删除模板3特定站点背景图片
function web_admin_delete_site_bacimg3($Id)
{
	global $wpdb;
	
	//通过slider的post_id找到对应的图片的ID
	$bacId = $wpdb->get_results( "SELECT meta_value FROM ".web_admin_get_table_name("postmeta") ." where meta_key='_thumbnail_id' and post_id='".intval($Id)."'" );  
	foreach($bacId as $bId){
		$bac_Id=$bId->meta_value;
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %d", $bac_Id ) );
		$deleted=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d", $bac_Id ) );
	}
	//找到对应的slider,并删除posts中的slider
	$deletes=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("posts")." WHERE ID = %d", $Id ) );
	
	//删除对应的postmeta中的slider
	$deletes=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("postmeta")." WHERE post_id = %d", $Id ) );
	//删除背景图片
	
	
	return $delete;
}

//更新模板3特定站点背景图片，此处是获得slider的内容，进行更新
function web_admin_get_slider_title($Id)
{
	global $wpdb;
	$sliderId = $wpdb->get_results( "SELECT post_id FROM ".web_admin_get_table_name("postmeta") ." where meta_key='_thumbnail_id' and meta_value='".intval($Id)."'" ); 
	foreach ($sliderId as $Id){
	$sliderid=$Id->post_id;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("posts") ." where ID ='".intval($sliderid)."'" );  
	}
	return $myrows;
}

//得到模板三某slider对应的siteid
function web_admin_get_site_id3($Id)
{
	global $wpdb;
	$site_Id = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("posts") ." where ID ='".intval($Id)."'" );
	
	return $site_Id;
}

//得到模板三slider对应的背景图片
function web_admin_get_site_bacimg3($bac_slider)
{
	global $wpdb;
	$bac_Id = $wpdb->get_results( "SELECT meta_value FROM ".web_admin_get_table_name("postmeta") ." where meta_key='_thumbnail_id' and post_id ='".intval($bac_slider)."'" );
	foreach ($bac_Id as $Id){
		$ID=$Id->meta_value;
		
	}
	$bac_img = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("posts") ." where ID ='".intval($ID)."'" );
	return $bac_img;
}

//更新模板三的背景以及slider
function web_admin_update_slider($slider_title,$sliderid,$insert_imgid)
{
	global $wpdb;
	$post_status="public";
	$post_name="hello";
	$url = home_url();
		//更新slider
	$updates = $wpdb -> update($wpdb->posts,array('post_author'=>'1','post_title'=>$slider_title,'post_content'=>'first','post_excerpt'=>'','to_ping'=>'','pinged'=>''),array('ID'=>$sliderid),array("%d","%s","%s","%s","%s","%s"),array("%d"));

			
	/*//menu跟图片对应记录更新（如果没有上传要更新的图片就不用更新了）
	if($insert_imgid!=null){	
		$update = $wpdb -> update($wpdb->postmeta,array('post_id'=>$menuid,'meta_key'=>'_thumbnail_id','meta_value'=>$insert_imgid),array('meta_id'=>$imgid),array("%d","%s","%s"),array("%d"));
	}

	//menu跟它的链接对应更新
	$update = $wpdb -> update($wpdb->postmeta,array('post_id'=>$menuid,'meta_key'=>'menu_item_url','meta_value'=>$menuiUrl),array('meta_id'=>$urlid),array("%d","%s","%s"),array("%d"));	
	*/
}

//当更新了模板3背景图片
function web_admin_update_slider_img($slider_title,$sliderid,$picUrl,$path,$site_id)
{
	
	global $wpdb;
	
	//往post里添加图片记录
	$wpdb->query( $wpdb->prepare(
	"
		INSERT INTO $wpdb->posts		(post_author,post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,guid,post_type,post_mime_type,post_status,post_name)
		VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
	",
	
	'1','','','','','',$site_id,$picUrl,'attachment','image/jpeg','inherit',''
	
	));

   
   //获取刚插入图片的id
	//echo $wpdb->insert_id;
	$insert_imgid=$wpdb->insert_id;

	

	//往post_meta里添加两条图片信息记录（需要获取图片id）
	
	$s="picDescription";
	
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_imgid, '_wp_attached_file',$path));
	
	$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta(post_id, meta_key,meta_value)VALUES (%d, %s ,%s )",$insert_imgid, '_wp_attachment_metadata',$s));
	

	//更新slider
	$updates = $wpdb -> update($wpdb->posts,array('post_author'=>'1','post_title'=>$slider_title,'post_content'=>'first','post_excerpt'=>'','to_ping'=>'','pinged'=>''),array('ID'=>$sliderid),array("%d","%s","%s","%s","%s","%s"),array("%d"));
	//更新图片
	$update = $wpdb -> update($wpdb->postmeta,array('meta_value'=>$insert_imgid),array('post_id'=>$sliderid),array("%s"),array("%d"));

}
	//限制背景图片的数量
function web_admin_count_bacimg3($site_id)
{
	 global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as bacCount FROM $wpdb->posts where post_type='slider' and post_status='publish' and ".constant("CONF_SITE_COLUMN")."='".intval($site_id)."'" );		
	
	return $myrows;
}
  //删除被更新的图片
 function web_admin_delete_slider_img($sliderid)
 {
 	global $wpdb;
	$bac_Id = $wpdb->get_results( "SELECT meta_value FROM ".web_admin_get_table_name("postmeta") ." where meta_key='_thumbnail_id' and post_id ='".intval($sliderid)."'" );
	foreach ($bac_Id as $Id){
		$ID=$Id->meta_value;
		
	}
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("postmeta")." WHERE post_id = %d", $ID) );
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("posts")." WHERE ID = %d", $ID) );
	return $delete;
 }
 
 //列出所有图文
function web_admin_list_material($WEID)
{
	global $wpdb;
	
	//自己建的表，这里不生效
	//$myrows = $wpdb->get_results( "SELECT * FROM $wpdb->orangesite"); 
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_material_news")." where WEID=".intval($WEID)." GROUP BY news_item_id");
	
	return $myrows;
}
//20140623 janeen update
function web_admin_list_material_group($GWEID)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_material_news")." where GWEID=".intval($GWEID)." GROUP BY news_item_id");
	
	return $myrows;
}

//获取所有图文的个数
function web_admin_count_material($WEID)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(DISTINCT news_item_id) as materialCount FROM ".web_admin_get_table_name("wechat_material_news")." where WEID=".intval($WEID));
	
	return $myrows;
}
//20140623 janeen add 
function web_admin_count_material_group($GWEID)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(DISTINCT news_item_id) as materialCount FROM ".web_admin_get_table_name("wechat_material_news")." where GWEID=".intval($GWEID));
	
	return $myrows;
}

//获取material当页的所有数据集
function web_admin_array_material($WEID,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT DISTINCT news_item_id,news_name FROM ".web_admin_get_table_name("wechat_material_news")." where WEID=".intval($WEID)." ORDER BY news_item_id limit ".$offset.",".$pagesize );
	
	return $myrows;
	
}
//20140623 janeen add 
function web_admin_array_material_group($GWEID,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT DISTINCT news_item_id,news_name FROM ".web_admin_get_table_name("wechat_material_news")." where GWEID=".intval($GWEID)." ORDER BY news_item_id desc limit ".$offset.",".$pagesize );
	
	return $myrows;
	
}

//获取当页数据集的个数
function web_admin_array_material_count($WEID,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(DISTINCT news_item_id) as arrayCount FROM ".web_admin_get_table_name("wechat_material_news")." where WEID=".intval($WEID)." limit ".$offset.",".$pagesize );
	return $myrows;
} 

//20140623 janeen add
function web_admin_array_material_count_group($GWEID,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(DISTINCT news_item_id) as arrayCount FROM ".web_admin_get_table_name("wechat_material_news")." where GWEID=".intval($GWEID)." limit ".$offset.",".$pagesize );
	return $myrows;
}

//获取创建站点的基本信息
function web_admin_template_list()
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("theme"));
	
	return $myrows;
}

//获取模板类型总数
function web_admin_template_count()
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT count(*) as templatecount FROM ".web_admin_get_table_name("theme"));
	
	return $myrows;
}

//获得站点模型种类
function web_admin_template($stid,$site_id = 0)
{
	global $wpdb;
	$site_id = intval($site_id);
	if($stid == 7){
		$myrows = $wpdb->get_results( "SELECT `title` as themename FROM ".web_admin_get_table_name("site_templates")." where name=(SELECT `site_value` FROM ".web_admin_get_table_name('orangesitemeta')." WHERE site_id='{$site_id}' AND site_key='we7templatestyle')");
		return $myrows;
	}
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("theme")." where id='".intval($stid)."'");
		
	
	return $myrows;
}

//获取该post_id评论总数
function web_admin_count_comment($sid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT count(*) as commentcount FROM $wpdb->comments where comment_post_ID= '".intval($sid)."'" );
	return $myrows;
}

//获取该post_id当页数据集信息
function web_admin_array_comment($offset,$pagesize,$sid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM $wpdb->comments where comment_post_ID='".intval($sid)."' ORDER BY comment_ID DESC limit ".$offset.",".$pagesize );
		
	return $myrows;
}
//获取该post_id当页数据集信息总数
function web_admin_array_comment_count($offset,$pagesize,$sid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT count(*) as arrayCount FROM $wpdb->comments where comment_post_ID='".intval($sid)."' ORDER BY comment_ID DESC limit ".$offset.",".$pagesize );

	return $myrows;
}
function web_admin_delete_coment($commentid)
{
	global $wpdb;
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->comments WHERE comment_ID = %d", $commentid) );
	return $delete;
}
//获取会员活动相关信息
if(!function_exists('web_admin_initfunc_info')){
	function web_admin_initfunc_info($user_id,$funcname)
	{
		global $wpdb;
		$myrows = $wpdb->get_results( $wpdb -> prepare("SELECT func_flag FROM ".web_admin_get_table_name("wechat_initfunc_info")." where user_id=%d and func_name=%s",$user_id,$funcname));

		foreach($myrows as $func_info)	
		{
			return $func_info->func_flag;
		}
	}
}

//订阅号：2014-07-12新增修改
function web_admin_add_wechat_prisub($hash, $sitename, $wechat_name, $wechattype, $wechat_auth, $token, $weid, $userId, $vericode, $GWEID, $wechat_fans, $picUrl,$cuservicepost)
{
	global $wpdb;
	
	//2014-07-12新增修改，wechat_name放到usechat这张表中去了
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechats")."(hash, wechat_nikename, wechat_type, wechat_auth, token)VALUES (%s, %s, %s, %d, %s)",$hash, $sitename, $wechattype, $wechat_auth, $token));
	$wid=$wpdb->insert_id;//获取wid
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_usechat")."(WEID, user_id, wid, vericode, wechat_name, GWEID, wechat_fan_init, wechat_imgurl,wechat_cuservice)VALUES (%d, %d, %d, %s, %s, %d, %d, %s, %s)",$weid, $userId, $wid, $vericode, $wechat_name, $GWEID, $wechat_fans, $picUrl,$cuservicepost));
	return $wid;
}

//2014-07-07针对已认证的订阅号的添加
function web_admin_add_wechat_prisubrenzheng($hash, $sitename, $wechat_name, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc, $weid, $userId, $vericode, $GWEID, $wechat_fans, $picUrl,$cuservicepost)
{
	global $wpdb;
	//2014-07-12新增修改
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechats")."(hash, wechat_nikename, wechat_type, wechat_auth, token, menu_appId, menu_appSc)VALUES (%s, %s, %s, %d, %s, %s, %s)",$hash, $sitename, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc));
	$wid=$wpdb->insert_id;//获取wid
	//2014-07-12新增修改
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_usechat")."(WEID, user_id, wid, vericode, wechat_name, GWEID, wechat_fan_init, wechat_imgurl,wechat_cuservice)VALUES (%d, %d, %d, %s, %s, %d, %d, %s, %s)",$weid, $userId, $wid, $vericode, $wechat_name, $GWEID, $wechat_fans, $picUrl,$cuservicepost));
	return $wid;
}

function web_admin_add_wechat_prisvc($hash, $sitename, $wechat_name, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc, $weid, $userId, $vericode, $GWEID, $wechat_fans, $picUrl,$cuservicepost)
{
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechats")."(hash, wechat_nikename, wechat_type, wechat_auth, token, menu_appId, menu_appSc)VALUES (%s, %s, %s, %d, %s, %s, %s)",$hash, $sitename, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc));
	$wid=$wpdb->insert_id;//获取wid
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_usechat")."(WEID, user_id, wid, vericode, wechat_name, GWEID, wechat_fan_init, wechat_imgurl,wechat_cuservice)VALUES (%d, %d, %d, %s, %s, %d, %d, %s, %s)",$weid, $userId, $wid, $vericode, $wechat_name, $GWEID, $wechat_fans, $picUrl,$cuservicepost));
	
	return $wid;
}

//添加微信昵称信息公共的服务号
function web_admin_add_wechat_pubsvc($hash, $sitename, $wechattype, $token, $menuappid, $menuappsc, $weid, $userId, $vericode)
{
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechats")."(hash, wechat_nikename, wechat_type, token, menu_appId, menu_appSc)VALUES (%s, %s, %s, %s, %s, %s)",$hash, $sitename, $wechattype, $token, $menuappid, $menuappsc));
	//$wid=$wpdb->insert_id;//获取wid
	//$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_usechat")."(WEID, user_id, wid, vericode)VALUES (%d, %d, %d, %s)",$weid, $userId, $wid, $vericode));
	
	$wid=$wpdb->insert_id;
	return $wid;
}

//2014-07-09新增修改
function web_admin_add_wechat_pubsvc1($hash, $sitename, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc, $weid, $userId, $vericode)
{
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechats")."(hash, wechat_nikename, wechat_type, wechat_auth, token, menu_appId, menu_appSc)VALUES (%s, %s, %s, %d, %s, %s, %s)",$hash, $sitename, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc));
	$wid=$wpdb->insert_id;//获取wid
	
	return $wid;
}

//admin添加微信昵称信息公共的订阅号
function web_admin_add_wechat_pubsub($hash, $sitename, $wechattype, $token, $weid, $userId, $vericode)
{
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechats")."(hash, wechat_nikename, wechat_type, token)VALUES (%s, %s, %s, %s)",$hash, $sitename, $wechattype, $token));
	$wid=$wpdb->insert_id;
	return $wid;
}

//2014-07-09新增修改
function web_admin_add_wechat_pubsub1($hash, $sitename, $wechattype, $wechat_auth, $token, $weid, $userId, $vericode)
{
	global $wpdb;
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechats")."(hash, wechat_nikename, wechat_type, wechat_auth, token)VALUES (%s, %s, %s, %d, %s)",$hash, $sitename, $wechattype, $wechat_auth, $token));
	$wid=$wpdb->insert_id;//获取wid
	
	return $wid;
}

//2014-07-08新增修改添加经过认证的公众订阅号
//2014-07-09新增修改
function web_admin_add_wechat_pubsub1renzheng($hash, $sitename, $wechatname, $wechattype, $wechat_auth, $token,$menuappid, $menuappsc, $weid, $userId, $vericode)
{
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechats")."(hash, wechat_nikename, wechat_name, wechat_type, wechat_auth, token, menu_appId, menu_appSc)VALUES (%s, %s, %s, %s, %d, %s, %s, %s)",$hash, $sitename, $wechatname, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc));
	$wid=$wpdb->insert_id;//获取wid
	
	return $wid;
}

//2014-07-09新增修改
//给商家添加微信昵称信息公共的订阅号
//2014-07-11新增修改
function web_admin_add_wechat_pubsubper($weid, $userId, $wid, $wechat_name, $vericode, $GWEID)
{
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_usechat")."(WEID, user_id, wid, vericode, GWEID)VALUES (%d, %d, %d, %s, %d)",$weid, $userId, $wid, $vericode, $GWEID));
	$wpdb->query( "UPDATE ".web_admin_get_table_name("wechats")." SET wechat_name ='".$wechat_name."' where wid = ".$wid);
	
	return $site_id;
}
function web_admin_add_wechat_pubsubper_all($weid, $userId, $wid, $wechat_name, $vericode, $busexit,$exireply_content,$GWEID, $wechat_fans, $picUrl,$cuservicepost)
{
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_usechat")."(WEID, user_id, wid, wechat_name,vericode, busi_exit,prompt_content,GWEID, wechat_fan_init, wechat_imgurl,wechat_cuservice)VALUES (%d, %d, %d, %s, %s, %s, %s, %d, %d, %s, %s)",$weid, $userId, $wid,$wechat_name,$vericode,$busexit,$exireply_content, $GWEID, $wechat_fans,$picUrl,$cuservicepost));
	//echo $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_usechat")."(WEID, user_id, wid, wechat_name,vericode, busi_exit,prompt_content,GWEID, wechat_fan_init, wechat_imgurl)VALUES (%d, %d, %d, %s, %s, %s, %s, %d, %d, %s)",$weid, $userId, $wid,$wechat_name,$vericode,$busexit,$exireply_content, $GWEID, $wechat_fans,$picUrl);
}

//根据wechat_nikename获取对应的wid
function web_admin_get_wechat_pubsubwid($wechatnickname)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare( "SELECT wid FROM ".web_admin_get_table_name("wechats")." where wechat_nikename=%s" ,$wechatnickname)); 
	foreach($myrows as $db_info)	
	{
		return $db_info->wid;
	}
	return "";
   
}

//根据wechat_nikename获取对应的wid
function web_admin_get_wechat_pubsubtokenhash($wechatnickname)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare( "SELECT * FROM ".web_admin_get_table_name("wechats")." where wechat_nikename=%s",$wechatnickname) ); 
	
	return $myrows;
   
}

//根据userid、wid查找数据表中是不是已经添加过该记录
function web_admin_get_wechat_pubsubcount($userId, $wid)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as countpub FROM ".web_admin_get_table_name("wechat_usechat")." where user_id = ".intval($userId)." and wid = ".intval($wid));
	
	foreach($myrows as $db_info)	
	{
		return $db_info->countpub;
	}
	return "";
	
}

//获取微信昵称信息公共的订阅号或者服务号列表
function web_admin_get_wechat_pub($wechattype)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechats")." where wechat_type=%s",$wechattype));
    //echo "SELECT * FROM ".web_admin_get_table_name("wechats")." where wechat_type='".$wechattype."';";
	return $myrows;
}

//2014-07-09新增修改获取指定类型和认证情况的公用订阅号或者服务号
function web_admin_get_wechat_pubnew($pubtype,$pubauth)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechats")." where wechat_type=%s AND wechat_auth = %d",$pubtype,$pubauth));
	
	return $myrows;
}

//获取当前传递过来的会员个数
function web_admin_member_count($weid, $fromuser)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT COUNT(*) as memberCount FROM ".web_admin_get_table_name("wechat_member")." where WEID=%s and from_user= %s",$weid,$fromuser));
    
	return $myrows;
} 
 
	
//获取当前传递过来的会员详细信息
function web_admin_member($weid, $fromuser)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where WEID=%s and from_user= %s",$weid,$fromuser));
    
	return $myrows;
}  

//获取当前传递过来的会员个数
function web_admin_member_count_mid($mid)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as memberCount FROM ".web_admin_get_table_name("wechat_member")." where mid= ".intval($mid));
    
	return $myrows;
}   
	
//获取当前传递过来的会员详细信息
function web_admin_member_mid($mid,$weid)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where mid= '".$mid."' and WEID='".$weid."'");
    
	return $myrows;
} 
function web_admin_member_mid_group($mid,$gweid)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where mid= '".intval($mid)."' and GWEID=".intval($gweid));
    
	return $myrows;
} 
function web_admin_member_login($weid,$email,$password)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where WEID=%d and email=%s and password= %s",$weid,$email,$password));
    
	return $myrows;
}   
function web_admin_member_login_group($gweid,$email,$password)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where GWEID=%d and email=%s and password= %s",$gweid,$email,$password));
    
	return $myrows;
}   
//邮箱验证不允许重复
function web_admin_member_email($weid,$email)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where WEID=%d and email= %s",$weid,$email));
    
	return $myrows;
}  
//邮箱修改不允许重复
function web_admin_member_upemail($weid,$mid,$email)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where WEID=%d and email= %s and mid!=%s",$weid,$email,$mid));
    
	return $myrows;
} 
function web_admin_member_upemail_group($gweid,$mid,$email)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where GWEID=%d and email= %s and mid!=%d",$gweid,$email,$mid));
    
	return $myrows;
} 
function web_admin_member_upmobilenumber_group($gweid,$mid,$mobilenumber)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where GWEID=%d and mobilenumber= %s and mid!=%d",$gweid,$mobilenumber,$mid));
    
	return $myrows;
} 
function web_admin_member_keyget($weid,$value,$key)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where WEID=%d and `".$key."` = %s",$weid,$value));
    
	return $myrows;
} 
function web_admin_member_keyget_group($gweid,$value,$key)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where GWEID=%d and `".$key."` = %s",$gweid,$value));
    
	return $myrows;
} 
function web_admin_member_nickname($weid,$nickname,$email)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member")." where WEID=%d and nickname= %s and email!=%s",$weid,$nickname,$email));
    
	return $myrows;
} 



//申请会员
function web_admin_create_member($weid, $fromuser, $realname, $nickname, $mobilenumber, $email, $password,$regtype)
{
	global $wpdb;
	$midvalue = rad();
	$Status = $wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_member")."(mid, WEID, from_user, realname, nickname, mobilenumber, rtime, email, password,reg_type)VALUES (%s, %d, %s , %s, %s, %s, now(), %s, %s,%s)",$midvalue, $weid, $fromuser, $realname, $nickname, $mobilenumber, $email, $password, $regtype));
	//$newmember=$wpdb->insert_id;//主键自增，才会获取到这个值
	return $Status!==FALSE?true:false;
}
function web_admin_create_member_group($gweid, $realname, $nickname, $mobilenumber, $email, $password,$regtype,$isaudit)
{
	global $wpdb;
	$midvalue = rad();
	$Status = $wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_member")."(mid, GWEID, realname, nickname, mobilenumber, rtime, email, password,reg_type,isaudit)VALUES (%s, %d, %s, %s, %s, now(), %s, %s,%s,%d)",$midvalue, $gweid, $realname, $nickname, $mobilenumber, $email, $password, $regtype,$isaudit));
	//$newmember=$wpdb->insert_id;//主键自增，才会获取到这个值
	return $Status!==FALSE?true:false;
}
//更新会员信息-包括password
function web_admin_update_member_pwd($mid, $realname, $nickname, $point, $level, $rtime,$mobilenumber,$email,$password)
{
	global $wpdb;
	$returnvalue = $wpdb->query( "UPDATE ".web_admin_get_table_name("wechat_member")." SET realname ='".$realname."', nickname ='".$nickname."', point ='".$point."', level ='".$level."', rtime ='".$rtime."', mobilenumber ='".$mobilenumber."', email ='".$email."', password ='".md5($password)."' WHERE mid = '".$mid."'");
	return $returnvalue;
}
//更新会员信息-不包括password
function web_admin_update_member_nopwd($mid, $realname, $nickname, $point, $level, $rtime,$mobilenumber,$email)
{
	global $wpdb;
	$returnvalue = $wpdb->query( "UPDATE ".web_admin_get_table_name("wechat_member")." SET realname ='".$realname."', nickname ='".$nickname."', point ='".$point."', level ='".$level."', rtime ='".$rtime."', mobilenumber ='".$mobilenumber."', email ='".$email."' WHERE mid = '".$mid."'");
	return $returnvalue;
}
//更新会员审核状态
function web_admin_update_member_audit($mid,$auditstatus)
{
	global $wpdb;
	$returnvalue = $wpdb->query( "UPDATE ".web_admin_get_table_name("wechat_member")." SET isaudit =".$auditstatus." WHERE mid = '".$mid."'");
	return $returnvalue;
}


//更新会员的fromuser
function web_admin_update_fromuser($mid,$fromuser){
	
	global $wpdb;
	$returnvalue = $wpdb->query( "UPDATE ".web_admin_get_table_name("wechat_member")." SET from_user ='".$fromuser."' where mid = '".$mid."'");

}
//重置会员密码
function web_admin_update_password($weid,$password,$email){
	
	global $wpdb;
	$status = $wpdb->query( "UPDATE ".web_admin_get_table_name("wechat_member")." SET password ='".$password."' where email = '".$email."' and WEID='".$weid."'");
	return $Status!==FALSE?true:false;
}
function web_admin_update_password_group($gweid,$password,$email){
	
	global $wpdb;
	$status = $wpdb->query( "UPDATE ".web_admin_get_table_name("wechat_member")." SET password ='".$password."' where email = '".$email."' and GWEID='".$gweid."'");
	return $Status!==FALSE?true:false;
}

//判断该用户是否添加过公众号
function web_admin_wechat_count($userid)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as wechatCount FROM ".web_admin_get_table_name("wechat_usechat")." where user_id='".intval($userid)."'");
    
	return $myrows;
}   


//列出所有会员
function web_admin_list_vmember($weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_member")." where WEID=".intval($weid));
	return $myrows;
}
//20140623 janeen add
function web_admin_list_vmember_group($gweid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_member")." where GWEID=".intval($gweid));
	return $myrows;
}

//获取会员的个数
function web_admin_count_vmember($weid)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(DISTINCT mid) as memberCount FROM ".web_admin_get_table_name("wechat_member")." where WEID=".intval($weid));
	
	return $myrows;
}
//20140623 janeen add
function web_admin_count_vmember_group($gweid)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(DISTINCT mid) as memberCount FROM ".web_admin_get_table_name("wechat_member")." where GWEID=".intval($gweid));
	return $myrows;
}

//获取会员当页的所有数据集
function web_admin_array_vmember($weid,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_member")." where WEID=".intval($weid)." limit ".$offset.",".$pagesize );
	return $myrows;
	
}
//20140623 janeen add
function web_admin_array_vmember_group($gweid,$offset,$pagesize)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_member")." where GWEID=".intval($gweid)." ORDER BY `rtime` DESC limit ".$offset.",".$pagesize );
	return $myrows;
	
}

//获取当页数据集的个数
function web_admin_array_vmember_count($weid,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(DISTINCT mid) as arrayCount FROM ".web_admin_get_table_name("wechat_member")." where WEID=".intval($weid)." limit ".$offset.",".$pagesize );
	return $myrows;
} 

//关于会员，列出所有查询到的会员
function web_admin_list_selectvmember($weid,$indata,$rg)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("wechat_member")." where WEID=%d and ".$rg." like %%%s%%",$weid,$indata ));
	//var_dump($myrows);
	return $myrows;
}
function web_admin_list_selectvmember_group($gweid,$indata,$rg)
{
	global $wpdb;
	if($rg=='isaudit'){
		if(trim($indata)=='审批通过'){
			$indata=1;
		}else if(trim($indata)=='审批中'){
			$indata=2;
		}else if(trim($indata)=='拒绝'){
			$indata=0;
		}else{
			$indata=5;
		}
	}
	$myrows = $wpdb->get_results($wpdb -> prepare( "SELECT * FROM ".web_admin_get_table_name("wechat_member")." where GWEID=%d and ".$rg." like '%%%s%%'",$gweid,$indata));
	return $myrows;
}
//获取查询到的会员个数
function web_admin_count_selectvmember($weid,$indata,$rg)
{
    global $wpdb;
    $myrows = $wpdb->get_results($wpdb->prepare( "SELECT COUNT(*) as memberCount FROM ".web_admin_get_table_name("wechat_member")." where WEID=%d and ".$rg." like %%%s%%",$weid,$indata));
	
	return $myrows;
}
//20140623 janeen add
function web_admin_count_selectvmember_group($gweid,$indata,$rg)
{
    global $wpdb;
	if($rg=='isaudit'){
		if(trim($indata)=='审批通过'){
			$indata=1;
		}else if(trim($indata)=='审批中'){
			$indata=2;
		}else if(trim($indata)=='拒绝'){
			$indata=0;
		}else{
			$indata=5;
		}
	}
	
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT COUNT(*) as memberCount FROM ".web_admin_get_table_name("wechat_member")." where GWEID=%d and ".$rg." like '%%%s%%'",$gweid,$indata));
    return $myrows;
}

//关于会员，获取查询到的会员当页的所有数据集
function web_admin_array_selectvmember($weid,$indata,$rg,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_member")." where WEID=".$weid." and ".$rg." like '%".$indata."%' limit ".$offset.",".$pagesize );

	return $myrows;
	
}
//20140623 janeen add
function web_admin_array_selectvmember_group($gweid,$indata,$rg,$offset,$pagesize)
{
    global $wpdb;
	if($rg=='isaudit'){
		if(trim($indata)=='审批通过'){
			$indata=1;
		}else if(trim($indata)=='审批中'){
			$indata=2;
		}else if(trim($indata)=='拒绝'){
			$indata=0;
		}else{
			$indata=5;
		}
	}

	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_member")." where GWEID=".$gweid." and ".$rg." like '%".$indata."%' ORDER BY `rtime` DESC limit ".$offset.",".$pagesize );
	return $myrows;
	
}
//获取所有的用户账户的个数
function web_admin_count_useraccount()
{
    global $wpdb;
    //$myrows = $wpdb->get_results("SELECT COUNT(*) as accountCount FROM ".web_admin_get_table_name("wechat_usechat"));
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as accountCount FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("users")." u2,".web_admin_get_table_name("wechats")." w WHERE u1.user_id = u2.ID AND u1.wid = w.wid");
	
	return $myrows;
}

//获取admin的公众号的个数
function web_admin_count_adminaccount()
{
    global $wpdb;
	//$myrows = $wpdb->get_results( "SELECT COUNT(*) as accountCount FROM ".web_admin_get_table_name("wechats")." WHERE wechat_type = 'pub_sub' OR wechat_type = 'pub_svc'");
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as accountCount FROM ".web_admin_get_table_name("wechats")." u1 left join ".web_admin_get_table_name("wechats_info")." u2 "."on u1.wid=u2.wid left join ".web_admin_get_table_name("wechat_add_menu")." u3 "." on u3.M_id=u2.M_id WHERE u1.wechat_type = 'pub_sub' OR u1.wechat_type = 'pub_svc'");
	
	return $myrows;
}

//获取所有账号当页的所有数据集
function web_admin_array_account($offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT w.wid, u2.ID, u2.user_nicename, w.wechat_type, w.hash, w.token FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("users")." u2,".web_admin_get_table_name("wechats")." w WHERE u1.user_id = u2.ID AND u1.wid = w.wid"." limit ".$offset.",".$pagesize );
	
	return $myrows;
}
//获取admin的所有数据集mashan
function web_admin_array_adminaccount($offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechats")." u1 left join ".web_admin_get_table_name("wechats_info")." u2 "."on u1.wid=u2.wid left join ".web_admin_get_table_name("wechat_add_menu")." u3 "." on u3.M_id=u2.M_id WHERE u1.wechat_type = 'pub_sub' OR u1.wechat_type = 'pub_svc' ORDER BY u1.wid desc limit ".$offset.",".$pagesize);
	//echo "SELECT * FROM ".web_admin_get_table_name("wechats_info")." u1 left join ".web_admin_get_table_name("wechats")." u2 "."on u1.wid=u2.wid left join ".web_admin_get_table_name("wechat_add_menu")." u3 "." on u3.M_id=u1.M_id limit ".$offset.",".$pagesize;
	return $myrows;
}
//获取admin账号的所有数据集的个数
function web_admin_array_account_count($offset,$pagesize)
{

	 global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("wechats")." WHERE wechat_type = 'pub_sub' OR wechat_type = 'pub_svc' limit ".$offset.",".$pagesize );
	
	return $myrows;
}   

//获取所有账号的所有数据集的个数
function web_admin_array_adminaccount_count($offset,$pagesize)
{

	 global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("users")." u2,".web_admin_get_table_name("wechats")." w WHERE u1.user_id = u2.ID AND u1.wid = w.wid".") limit ".$offset.",".$pagesize );
	
	return $myrows;
}  

function account_delete($wid)
{
	global $wpdb;	
	if($wid!=null){
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechats")." WHERE wid=%d", $wid));
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_usechat")." WHERE wid=%d", $wid));
		return $delete;
	}else{
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechats")." WHERE news_id=%d", $wid));
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_usechat")." WHERE wid=%d", $wid));
		return $delete;
	}

}	


function adminaccount_delete($wid)
{
	global $wpdb;	
	if($wid!=null){
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechats")." WHERE wid=%d", $wid));
		//$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_usechat")." WHERE wid=%d and user_id=0", $wid));//这里为什么要是user_id=0?
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_usechat")." WHERE wid=%d", $wid));
		return $delete;
	}

}  
function rad($length=6) { 
 // 密码字符集，可任意添加你需要的字符 
 $chars = '123456789';  
 $mid = '';  
 for ( $i = 0; $i < $length; $i++ )  
 {  
 // 这里提供两种字符获取方式  
 // 第一种是使用 substr 截取$chars中的任意一位字符；  
 // 第二种是取字符数组 $chars 的任意元素  
 // $mid .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);  
    $mid.= $chars[ mt_rand(0, strlen($chars) - 1) ];  }  
    return $mid; 
}

//添加共用微信服务号的退出验证码以及退出回复的内容
function web_admin_add_wechats_info($wid,$busi_exit, $prompt_type, $prompt_content,$mid)
{
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechats_info")."(wid,busi_exit, prompt_type, prompt_content, M_id)VALUES (%s, %s, %s, %s, %d)",$wid,$busi_exit, $prompt_type, $prompt_content, $mid));
}
function web_admin_delete_wechats_info($wid)
{
	global $wpdb;
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechats_info")." WHERE wid=%s", $wid));
	return $delete;
}
function web_admin_update_wechats_info($wid,$busi_exit, $prompt_type, $prompt_content)
{
	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechats_info"),array('busi_exit'=>$busi_exit,'prompt_type'=>$prompt_type,'prompt_content'=>$prompt_content),array('wid'=>$wid),array("%s","%s","%s"),array("%s"));
	
}
function web_admin_get_wechats_info($wid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ". web_admin_get_table_name("wechats_info")." where wid='".intval($wid)."'");
	return $myrows;
	
}

//初始化用户功能表wp_wechat_initfunc_info
function web_admin_initfunction($weid)
{
	global $wpdb;
	//将insert改为replace
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatwebsite', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatfuncfirstconcern', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatfunckeywordsreply', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatfuncnokeywordsreply', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatfuncmanualreply', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatfuncaccountmanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatfuncmaterialmanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatfuncmenumanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatfuncusermanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatactivity_coupon', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatactivity_scratch', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatactivity_fortunewheel', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatactivity_toend', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatactivity_fortunemachine', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatresearch', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatschool', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'wechatvip', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'WEID', $weid, 'template_selno', 0));
	
	return $site_id;
}
function web_admin_array_selectvericode($weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." where WEID='".intval($weid)."'" );
	
	return $myrows;
}
function web_admin_array_selectvericode_group($weid, $gweid)
{
	global $wpdb;
	
	//2014-06-29新增
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." where user_id = 0 AND GWEID = ".intval($gweid)." AND WEID='".intval($weid)."'" );
	
	return $myrows;
}
//初始化用户功能表wp_wechat_initfunc_info
function web_user_addspaceapply($userid, $space, $desc)
{
	global $wpdb;
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wesite_spaceapply")."(userid, space, `desc`, time, status)VALUES (%d, %f, %s, now(), %d)",$userid, $space, $desc, 0));
}

//商家申请更改可建立公众号数量
function web_user_set_accountapply($userid, $account, $desc)
{
	global $wpdb;
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_accountapply")."(userid, app_account, `desc`, time, status)VALUES (%d, %d, %s, now(), %d)",$userid, $account, $desc, 0));
}

//设定用户对应的共用号菜单内容
function web_user_wechat_menu_add($menu_id,$menu_type,$menu_key,$userid,$WEID,$wid,$M_id){
	global $wpdb;
	
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_user_menu")."(menu_id, menu_type, menu_key,menu_user, WEID,wid,M_id)VALUES (%d, %s, %s, %d,%d, %d, %d)",$menu_id, $menu_type, $menu_key,$userid,$WEID,$wid,$M_id));
	
	return $insert;

}

function web_user_wechat_menu_content_get($M_id){
	
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_content_menu")." where M_id=".intval($M_id));
	return $myrows;
}


//获取所有未审核的扩容空间的用户个数
function web_admin_count_spaceaccount()
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as accountCount FROM ".web_admin_get_table_name("wesite_spaceapply")." WHERE status = 0");
	return $myrows;
}

//获取所有的扩容空间的用户个数
function web_admin_count_spaceallaccount()
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as accountCount FROM ".web_admin_get_table_name("wesite_spaceapply"));
	
	return $myrows;
}


//获取当前页面的所有未审核扩容空间的用户个数
function web_admin_array_spaceaccount($offset,$pagesize)
{
    global $wpdb;
	//$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wesite_spaceapply")." s ,".web_admin_get_table_name("users")." u WHERE s.userid = u.ID AND s.status = 0"." order by s.time DESC limit ".$offset.",".$pagesize );
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wesite_spaceapply")." s left join ".web_admin_get_table_name("users")." u on s.userid = u.ID where s.status = 0"." order by s.time DESC limit ".$offset.",".$pagesize );
	return $myrows;
}

//获取当前页面的所有扩容空间的用户个数
function web_admin_array_spaceallaccount($offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wesite_spaceapply")." s ,".web_admin_get_table_name("users")." u WHERE s.userid = u.ID"." limit ".$offset.",".$pagesize );
	
	return $myrows;
}


//获取所有扩容空间未审核的个数
function web_admin_array_spaceaccount_count($offset,$pagesize)
{

	 global $wpdb;
	 $myrows = $wpdb->get_results( "SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("wesite_spaceapply")." s,".web_admin_get_table_name("users")." u WHERE s.userid = u.ID AND s.status = 0"." limit ".$offset.",".$pagesize );
	 
	return $myrows;
} 

//获取所有扩容空间未审核的个数
function web_admin_array_spaceapply_count()
{

	 global $wpdb;
	 $myrows = $wpdb->get_results( "SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("wesite_spaceapply")." s,".web_admin_get_table_name("users")." u WHERE s.userid = u.ID AND s.status = 0");
	 
	return $myrows;
} 
//获取所有扩容空间的个数
function web_admin_array_spaceallaccount_count($offset,$pagesize)
{

	 global $wpdb;
	 $myrows = $wpdb->get_results( "SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("wesite_spaceapply")." s,".web_admin_get_table_name("users")." u WHERE s.userid = u.ID"." limit ".$offset.",".$pagesize );
	 
	return $myrows;
} 
//admin撤销用户扩容操作
function space_delete($userid)
{
	global $wpdb;	
	if($userid!=null){
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechats")." WHERE wid=%d", $wid));
		
		return $delete;
	}

}

/*Feature: 用户可用公众号数目管理
  Date: Aug 12, 2014
*/

	//Get all unchecked account app: status =0
	function web_admin_count_accountapp(){
		global $wpdb;
		$myrows = $wpdb->get_results("SELECT COUNT(*) as accountCount FROM ".web_admin_get_table_name("wechat_accountapply")." WHERE status = 0");
		return $myrows;
	}
	//get all account app with $offset
	function web_admin_array_accountapp($offset,$pagesize){
		global $wpdb;
		$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_accountapply")." s left join ".web_admin_get_table_name("users")." u on s.userid = u.ID where s.status = 0"." order by s.time DESC limit ".$offset.",".$pagesize );
		return $myrows;
	} 
	//Get all account app count
	function web_admin_count_accountappall(){
		global $wpdb;
		$myrows = $wpdb->get_results( "SELECT COUNT(*) as accountappcount FROM ".web_admin_get_table_name("wechat_accountapply"));
		return $myrows;
	}
	function web_admin_array_accountappall($offset,$pagesize){
		global $wpdb;
		$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_accountapply")." s left join ".web_admin_get_table_name("users")." u on s.userid = u.ID"." order by s.time DESC limit ".$offset.",".$pagesize );
		return $myrows;
	} 
	
/*end*/

//获取根据查询条件得到的公众号个数
function web_admin_count_selectpub($indata,$rg)
{
    global $wpdb;
    //$myrows = $wpdb->get_results( "SELECT COUNT(*) as memberCount FROM ".web_admin_get_table_name("wechats")." where (wechat_type = 'pub_sub' OR wechat_type = 'pub_svc') and ".$rg." like '%".$indata."%'");
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as memberCount FROM ".web_admin_get_table_name("wechats")." u1 left join ".web_admin_get_table_name("wechats_info")." u2 "."on u1.wid=u2.wid left join ".web_admin_get_table_name("wechat_add_menu")." u3 "." on u3.M_id=u2.M_id where ".$rg." like '%".$indata."%' AND (u1.wechat_type = 'pub_sub' OR u1.wechat_type = 'pub_svc')");
	
	
	return $myrows;
}

function web_admin_array_selectpub($indata,$rg,$offset,$pagesize)
{
    global $wpdb;
	//$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechats_info")." u1 left join ".web_admin_get_table_name("wechats")." u2 "."on u1.wid=u2.wid left join ".web_admin_get_table_name("wechat_add_menu")." u3 "." on u3.M_id=u1.M_id where ".$rg." like '%".$indata."%' limit ".$offset.",".$pagesize );
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechats")." u1 left join ".web_admin_get_table_name("wechats_info")." u2 "."on u1.wid=u2.wid left join ".web_admin_get_table_name("wechat_add_menu")." u3 "." on u3.M_id=u2.M_id where ".$rg." like '%".$indata."%' AND (u1.wechat_type = 'pub_sub' OR u1.wechat_type = 'pub_svc') ORDER BY u1.wid DESC limit ".$offset.",".$pagesize );
	
	
	return $myrows;
}

//获取根据查询条件得到的用户个数
function web_admin_count_selectuser($indata,$rg)
{
    global $wpdb;
    $myrows = $wpdb->get_results("SELECT COUNT(*) as memberCount FROM ".web_admin_get_table_name("users")." w1 WHERE w1.user_pass != '' and w1.".$rg." like '%".$indata."%'");
	
	return $myrows;
}

//获取根据查询条件得到的用户数据集
function web_admin_array_selectuser($indata,$rg,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("users")." w1 WHERE w1.user_pass != '' and w1.".$rg." like '%".$indata."%' ORDER BY w1.ID DESC limit ".$offset.",".$pagesize );
	return $myrows;
}

//获取普通用户关注公众号的个数
function web_admin_wechats_info_bywid($wid)
{
	global $wpdb;
	
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as usercount FROM ".web_admin_get_table_name("wechat_usechat")." WHERE user_id != 0 AND wid =".intval($wid));
	foreach($myrows as $row)
	{
	    $countnumber = $row->usercount;
	}
	return $countnumber;
}
//建立公众号选择菜单模板
function wechat_select_menu_demo()
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_add_menu"));
	
	return $myrows;
}
//获取当页的所有数据集
function web_admin_array_adminmenu($offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_add_menu"). " ORDER BY M_id DESC limit ".$offset.",".$pagesize );
	
	return $myrows;
}
//获取所有的website的个数
function web_admin_count_adminmenu()
{
    global $wpdb;
    $myrows = $wpdb->get_results("SELECT COUNT(*) as menuCount FROM ".web_admin_get_table_name("wechat_add_menu"));
	                             //( "SELECT * FROM ".web_admin_get_table_name("orangesite")." where id='".$site_id."'" )
	return $myrows;
}
//获取当页数据集的个数
function web_admin_array_adminmenu_count($offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("wechat_add_menu"). " limit ".$offset.",".$pagesize);
    
	return $myrows;
}
// 管理员添加菜单名称
function wechat_add_menu_name($menu_name)
{
	global $wpdb;
	$result = $wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_add_menu")."(M_name)VALUES (%s)" , $menu_name ));
	return $result;
}

//index页面最终显示的功能项
function web_user_display_index($WEID, $userid, $wid)
{
    global $wpdb;
    //$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_func_info")." a WHERE NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'WEID' AND value = ".$WEID." AND func_flag = 0) AND NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 0) AND NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b3 WHERE a.func_name=b3.func_name AND type = 'wid' AND value = ".$wid." AND func_flag = 0) LIMIT 0, 100");
	$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_func_info")." a WHERE NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'WEID' AND value = ".$WEID." AND func_flag = 0) AND EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) AND NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b3 WHERE a.func_name=b3.func_name AND type = 'wid' AND value = ".$wid." AND func_flag = 0) LIMIT 0, 100");
	
	return $myrows;
}

//将admin添加的公用公众号的功能列表添加到wechat_initfunc_info
function web_admin_pub_initfunction($wid)
{
	global $wpdb;
	
	//将insert改为replace
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatwebsite', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatfuncfirstconcern', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatfunckeywordsreply', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatfuncnokeywordsreply', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatfuncmanualreply', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatfuncaccountmanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatfuncmaterialmanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatfuncmenumanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatfuncusermanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatactivity_coupon', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatactivity_scratch', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatactivity_fortunewheel', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatactivity_toend', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatactivity_fortunemachine', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'wechatvip', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'wid',$wid, 'template_selno', 0));
	
	
	return $site_id;
}

//functioncustomer调用能显示的功能项
function web_user_display_function($userid, $wid)
{
    global $wpdb;
    //$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_func_info")." a WHERE NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 0) AND NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b3 WHERE a.func_name=b3.func_name AND type = 'wid' AND value = ".$wid." AND func_flag = 0) LIMIT 0, 100");
	$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_func_info")." a WHERE EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) AND NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b3 WHERE a.func_name=b3.func_name AND type = 'wid' AND value = ".intval($wid)." AND func_flag = 0) LIMIT 0, 100");
	
	
	return $myrows;
}

//如果此用户被删除了，其空间信息也被删除
function wp_delete_space($userid)
{
   	//删除用户空间信息
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wesite_space")." WHERE userid = %d", $userid ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wesite_spaceapply")." WHERE userid = %d", $userid ) );
}

//如果此用户被删除了，其公众号在wechat_group表中的信息也被删除
function wp_delete_wechatnumber($userid)
{
   	//删除公众号
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_group")." WHERE user_id = %d", $userid ) );
}

//如果此用户被删除了，其所在分组也被删除
function wp_delete_usergroup($userid)
{
   	//删除所在分组
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("user_group")." WHERE user_id = %d", $userid ) );
}


//获取admin账号的所有数据集的个数
function web_admin_pubaccount_count($nikename)
{

	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT COUNT(*) as accountCount FROM ".web_admin_get_table_name("wechats")." WHERE (wechat_type = 'pub_sub' OR wechat_type = 'pub_svc') AND wechat_nikename = %s",$nikename ));
	
	return $myrows;
} 

//2014-07-09新增修改，判断同一个wid中的验证码是否唯一
function web_admin_pubvericode_count($vericode, $wid, $weid)
{

	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT COUNT(*) as accountCount FROM ".web_admin_get_table_name("wechat_usechat")." WHERE wid = %d AND WEID != %d AND vericode = %s",$wid,$weid,$vericode ));
	
	return $myrows;
} 

function web_admin_pubaccountl_count($wid, $nikename)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT COUNT(*) as accountCount FROM ".web_admin_get_table_name("wechats")." WHERE (wechat_type = 'pub_sub' OR wechat_type = 'pub_svc') AND wechat_nikename = %s AND wid !=%d",$nikename,$wid ));
	
	return $myrows;
}
//插入微作业基本信息
function web_admin_create_homework($work_title, $htmlData,$home_gradeclass,$sDate1,$sDate2,$weid)
{
	global $wpdb;
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("school_homework")."(homework_title,homework_content,homework_starttime,homework_endtime,homework_gradeclass,homework_class,WEID)VALUES (%s, %s ,%s,%s,%s,%s,%d)",$work_title, $htmlData,$sDate1,$sDate2,$home_gradeclass,$weid));
	$homework_id=$wpdb->homework_id;
	return $homework_id;
}
//20140624 janeen add
function web_admin_create_homework_group($work_title, $htmlData,$home_gradeclass,$sDate1,$sDate2,$gweid)
{
	global $wpdb;
	$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("school_homework")."(homework_title,homework_content,homework_starttime,homework_endtime,homework_gradeclass,homework_class,GWEID)VALUES (%s, %s ,%s,%s,%s,%s,%d)",$work_title, $htmlData,$sDate1,$sDate2,$home_gradeclass,$gweid));
	$homework_id=$wpdb->homework_id;
	return $homework_id;
}
//更新微作业基本信息
function web_admin_update_homework($work_title, $htmlData,$home_gradeclass,$sDate1,$sDate2,$homework_id)
{
	global $wpdb; 
	$update = $wpdb -> update(web_admin_get_table_name("school_homework"),array('homework_title'=>$work_title,'homework_content'=>$htmlData,'homework_starttime'=>$sDate1,'homework_endtime'=>$sDate2,'homework_gradeclass'=>$home_gradeclass),array('homework_id'=>$homework_id),array("%s","%s","%s","%s","%s","%s"),array("%d"));
	return $update;
}
//判断是否有这个homework
function web_admin_homework_exist($homework_id)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM ".web_admin_get_table_name("school_homework")." where homework_id=".($homework_id));	
	return !empty($myrows);
}
//拿到特定的homework信息
function web_admin_get_homework($homework_id)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM ".web_admin_get_table_name("school_homework")." where homework_id=".intval($homework_id));	
	return $myrows;
}
//更新微公告基本信息
function web_admin_update_notice($notice_title, $htmlData,$rights,$commentSelected,$notice_id)
{
	global $wpdb; 
	$update = $wpdb -> update(web_admin_get_table_name("school_notice"),array('notice_title'=>$notice_title,'notice_content'=>$htmlData,'notice_allowcomments'=>$commentSelected,'notice_rights'=>$rights),array('notice_id'=>$notice_id),array("%s", "%s","%d","%s"),array("%d"));
	return $update;
}
//判断是否有这个notice
function web_admin_get_teaid_exist($notice_id)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM ".web_admin_get_table_name("school_notice")." where notice_id=".intval($notice_id));	
	return !empty($myrows);
}
//拿到特定的notice信息
function web_admin_get_noticinfo($notice_id)
{
    global $wpdb;
	$myrows = $wpdb->get_results(  "SELECT * FROM ".web_admin_get_table_name("school_notice")." where notice_id=".intval($notice_id));	
	return $myrows;
}


//列出所有作业
function web_admin_list_homework($weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_homework")." where WEID=".intval($weid));
	return $myrows;
}
//20140624 janeen add
function web_admin_list_homework_group($gweid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_homework")." where GWEID=".intval($gweid));
	return $myrows;
}

//获取作业的个数
function web_admin_count_homework($weid)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(*) as homeworkCount FROM ".web_admin_get_table_name("school_homework")." where WEID=".intval($weid));
	
	return $myrows;
}
//20140624 janeen add
function web_admin_count_homework_group($gweid)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(*) as homeworkCount FROM ".web_admin_get_table_name("school_homework")." where GWEID=".intval($gweid));
	
	return $myrows;
}

//获取作业当页的所有数据集
function web_admin_array_homework($weid,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_homework")." where WEID=".intval($weid)." limit ".$offset.",".$pagesize );
	return $myrows;
	
}
//20140624 janeen add
function web_admin_array_homework_group($gweid,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_homework")." where GWEID=".intval($gweid)." limit ".$offset.",".$pagesize );
	return $myrows;
	
}

//获取当页数据集的个数
function web_admin_array_homework_count($weid,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("school_homework")." where WEID=".intval($weid)." limit ".$offset.",".$pagesize );
	return $myrows;
} 

//关于作业，列出所有查询到的作业
function web_admin_list_selecthomework($weid,$indata,$rg)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_homework")." where WEID='".$weid."' and ".$rg." like '%".$indata."%'");
	return $myrows;
}
//20140624 janeen add
function web_admin_list_selecthomework_group($gweid,$indata,$rg)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_homework")." where GWEID='".$gweid."' and ".$rg." like '%".$indata."%'");
	return $myrows;
}
//获取查询到的作业个数
function web_admin_count_selecthomework($weid,$indata,$rg)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(*) as homeworkCount FROM ".web_admin_get_table_name("school_homework")." where WEID='".$weid."' and ".$rg." like '%".$indata."%'");
	
	return $myrows;
}
//20140624 janeen add
function web_admin_count_selecthomework_group($gweid,$indata,$rg)
{
    global $wpdb;
    $myrows = $wpdb->get_results($wpdb->prepare( "SELECT COUNT(*) as homeworkCount FROM ".web_admin_get_table_name("school_homework")." where GWEID=%d and ".$rg." like %%%s%%",$gweid,$indata));
	
	return $myrows;
}
//关于作业，获取查询到的作业当页的所有数据集
function web_admin_array_selecthomework($weid,$indata,$rg,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("school_homework")." where WEID=%d and ".$rg." like %%%s%% limit ".$offset.",".$pagesize,$weid,$indata ));

	return $myrows;
	
}
//20140624 janeen add
function web_admin_array_selecthomework_group($gweid,$indata,$rg,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("school_homework")." where GWEID=%d and ".$rg." like %%%s%% limit ".$offset.",".$pagesize,$gweid,$indata ));

	return $myrows;
	
}

function web_admin_delete_homework($hid)
{
	global $wpdb;	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("school_homework")." WHERE homework_id=%s", $hid));
	return $delete;	

}


//列出所有公告
function web_admin_list_notice($weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_notice")." where WEID=".intval($weid));
	return $myrows;
}
//20140624 janeen add
function web_admin_list_notice_group($gweid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_notice")." where GWEID=".intval($gweid));
	return $myrows;
}
//获取公告的个数
function web_admin_count_notice($weid)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(*) as noticeCount FROM ".web_admin_get_table_name("school_notice")." where WEID=".intval($weid));
	
	return $myrows;
}
//20140624 janeen add
function web_admin_count_notice_group($gweid)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(*) as noticeCount FROM ".web_admin_get_table_name("school_notice")." where GWEID=".intval($gweid));
	
	return $myrows;
}

//获取公告当页的所有数据集
function web_admin_array_notice($weid,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_notice")." where WEID=".intval($weid)." limit ".$offset.",".$pagesize );
	return $myrows;
	
}
//20140624 janeen add
function web_admin_array_notice_group($gweid,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_notice")." where GWEID=".intval($gweid)." limit ".$offset.",".$pagesize );
	return $myrows;
	
}

//获取当页数据集的个数
function web_admin_array_notice_count($weid,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("school_notice")." where WEID=".intval($weid)." limit ".$offset.",".$pagesize );
	return $myrows;
} 
//20140624 janeen add
function web_admin_array_notice_count_group($gweid,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("school_notice")." where GWEID=".intval($gweid)." limit ".$offset.",".$pagesize );
	return $myrows;
} 

//关于公告，列出所有查询到的公告
function web_admin_list_selectnotice($weid,$indata,$rg)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("school_notice")." where WEID=%d and ".$rg." like %%%s%%",$weid,$indata));
	return $myrows;
}

//20140624 janeen add
function web_admin_list_selectnotice_group($gweid,$indata,$rg)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("school_notice")." where GWEID=%d and ".$rg." like %%%s%%",$gweid,$indata));
	return $myrows;
}
//获取查询到的公告个数
function web_admin_count_selectnotice($weid,$indata,$rg)
{
    global $wpdb;
    $myrows = $wpdb->get_results($wpdb->prepare(  "SELECT COUNT(*) as noticeCount FROM ".web_admin_get_table_name("school_notice")." where WEID=%d and ".$rg." like %%%s%%",$weid,$indata));
	
	return $myrows;
}
//20140624 janeen add
function web_admin_count_selectnotice_group($gweid,$indata,$rg)
{
    global $wpdb;
    $myrows = $wpdb->get_results($wpdb->prepare( "SELECT COUNT(*) as noticeCount FROM ".web_admin_get_table_name("school_notice")." where GWEID=%d and ".$rg." like %%%s%%",$gweid,$indata));
	
	return $myrows;
}

//关于公告，获取查询到的公告当页的所有数据集
function web_admin_array_selectnotice($weid,$indata,$rg,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("school_notice")." where WEID=%d and ".$rg." like %%%s%% limit ".$offset.",".$pagesize,$weid,$indata ));

	return $myrows;
	
}
//20140624 janeen add
function web_admin_array_selectnotice_group($gweid,$indata,$rg,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("school_notice")." where GWEID=%d and ".$rg." like %%%s%% limit ".$offset.",".$pagesize,$gweid,$indata ));

	return $myrows;
	
}
function web_admin_delete_notice($hid)
{
	global $wpdb;	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("school_notice")." WHERE notice_id=%s", $hid));
	return $delete;	

}


//列出所有评论
function web_admin_list_reply($weid,$ntid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_reply")." where WEID=".intval($weid)." and notice_id=".intval($ntid));
	return $myrows;
}
//20140624 janeen add
function web_admin_list_reply_group($gweid,$ntid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_reply")." where GWEID=".intval($gweid)." and notice_id=".intval($ntid));
	return $myrows;
}

//获取评论的个数
function web_admin_count_reply($weid,$ntid)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(*) as replyCount FROM ".web_admin_get_table_name("school_reply")." where WEID=".intval($weid)." and notice_id=".intval($ntid));
	return $myrows;
}
//20140624 janeen add
function web_admin_count_reply_group($gweid,$ntid)
{
    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT COUNT(*) as replyCount FROM ".web_admin_get_table_name("school_reply")." where GWEID=".intval($gweid)." and notice_id=".intval($ntid));
	return $myrows;
}
//获取评论的所有数据集
function web_admin_array_reply($weid,$offset,$pagesize,$ntid)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_reply")." where WEID=".intval($weid)." and notice_id=".intval($ntid)." limit ".$offset.",".$pagesize );
	return $myrows;
	
}
//20140624 janeen add
function web_admin_array_reply_group($gweid,$offset,$pagesize,$ntid)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("school_reply")." where GWEID=".intval($gweid)." and notice_id=".intval($ntid)." limit ".$offset.",".$pagesize );
	return $myrows;
	
}

//获取当页数据集的个数
function web_admin_array_reply_count($weid,$offset,$pagesize,$ntid)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("school_reply")." where WEID=".intval($weid)." and notice_id=".intval($ntid)." limit ".$offset.",".$pagesize );
	return $myrows;
} 
//20140624 janeen add
function web_admin_array_reply_count_group($gweid,$offset,$pagesize,$ntid)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT COUNT(*) as arrayCount FROM ".web_admin_get_table_name("school_reply")." where GWEID=".intval($gweid)." and notice_id=".intval($ntid)." limit ".$offset.",".$pagesize );
	return $myrows;
}
//关于评论，列出所有查询到的评论
function web_admin_list_selectreply($weid,$indata,$rg,$ntid)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".web_admin_get_table_name("school_reply")." where WEID=%d and ".$rg." like %%%s%% and notice_id=%d",$weid,$indata,$ntid));
	return $myrows;
}
//20140624 janeen add
function web_admin_list_selectreply_group($gweid,$indata,$rg,$ntid)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".web_admin_get_table_name("school_reply")." where GWEID=%d and ".$rg." like %%%s%% and notice_id=%d",$gweid,$indata,$ntid));
	return $myrows;
}
//获取查询到的评论个数
function web_admin_count_selectreply($weid,$indata,$rg,$ntid)
{
    global $wpdb;
    $myrows = $wpdb->get_results($wpdb->prepare("SELECT COUNT(*) as replyCount FROM ".web_admin_get_table_name("school_reply")." where WEID=%d and ".$rg." like %%%s%% and notice_id=%d",$ntid,$weid,$indata));
	
	return $myrows;
}
//20140624 janeen add
function web_admin_count_selectreply_group($gweid,$indata,$rg,$ntid)
{
    global $wpdb;
    $myrows = $wpdb->get_results($wpdb->prepare( "SELECT COUNT(*) as replyCount FROM ".web_admin_get_table_name("school_reply")." where GWEID=%d and ".$rg." like %%%s%%"." and notice_id=%s",$gweid,$indata,$ntid));
	
	return $myrows;
}

//关于评论，获取查询到的评论当页的所有数据集
function web_admin_array_selectreply($weid,$indata,$rg,$offset,$pagesize,$ntid)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".web_admin_get_table_name("school_reply")." where WEID=%d and notice_id=%d and ".$rg." like %%%s%% limit ".$offset.",".$pagesize,$weid,$ntid,$indata) );

	return $myrows;
	
}
//20140624 janeen add
function web_admin_array_selectreply_group($gweid,$indata,$rg,$offset,$pagesize,$ntid)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("school_reply")." where GWEID=%s and notice_id=%s and ".$rg." like %%%s%% limit ".$offset.",".$pagesize,$gweid,$ntid,$indata ));

	return $myrows;
	
}
function web_admin_delete_reply($rpid)
{
	global $wpdb;	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("school_reply")." WHERE reply_id=%d", $rpid));
	return $delete;	

}

//找到对应fromuser的teaid
function web_admin_select_teaid($fromuser){
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("school_teacher")." where tea_fromuser=%s",$fromuser ));

	return $myrows;
}
//插入老师添加的公告
function web_admin_create_notice($notice_title, $notice_content, $commentSelected, $notice_right, $date,$tea_id, $weid)
{
	global $wpdb;
	$Status = $wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("school_notice")."(notice_title, notice_content, notice_allowcomments, notice_rights, notice_date, notice_publisher,WEID)VALUES (%s, %s, %d , %s, %s,%s, %d)",$notice_title, $notice_content, $commentSelected, $notice_right, $date, $tea_id, $weid));
	//$newmember=$wpdb->insert_id;//主键自增，才会获取到这个值
	return $Status!==FALSE?true:false;
}

//找到所有老师的年级数
function web_admin_select_all_gradeclass()
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT DISTINCT  tea_gradeclass FROM ".web_admin_get_table_name("school_teacher"). " ORDER BY tea_gradeclass");

	return $myrows;
}
//找到所有家长的
function web_admin_select_all_stugradeclass()
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT DISTINCT  stu_gradeclass FROM ".web_admin_get_table_name("school_student"). " ORDER BY stu_gradeclass");

	return $myrows;
}
//找到对应fromuser的stuid
function web_admin_select_stuid($fromuser)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("school_student")." where stu_fromuser=%s",$fromuser) );

	return $myrows;
}
function web_admin_select_gradeteacher()
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT  * FROM ".web_admin_get_table_name("school_teacher"). " ORDER BY tea_gradeclass");

	return $myrows;
}
//找到所有老师的年级加*
function web_admin_select_all_gc()
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT DISTINCT SUBSTR(tea_gradeclass,1,4) as sub_tea FROM ".web_admin_get_table_name("school_teacher"). " ORDER BY tea_gradeclass");

	return $myrows;
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

function getWechatGroupInfo($userid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_group")." where user_id=".intval($userid)." order by WEID" );
	return $myrows;
}
//2014-07-16新增修改
function getWechatGroupInfoCount($userid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT count(*) as groupcount FROM ".web_admin_get_table_name("wechat_group")." where user_id=".intval($userid)." AND WEID != 0" );
	return $myrows;
}
//2014-07-16新增修改
function getWechatGroupInfoByPage($offset,$pagesize)
{

	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT w.wid, w.hash, w.wechat_nikename, w.wechat_name, w.wechat_type, w.wechat_auth, w.token, w.menu_appId, w.menu_appSc, u1.WEID, u1.vericode, u1.flgopen, u1.busi_exit, u1.prompt_content, u1.wechat_name as wechatname FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("wechats")." w WHERE u1.wid = w.wid and u1.WEID != 0 limit ".$offset.",".$pagesize);
	return $myrows;
	
}
//2014-07-14新增修改
function getWechatShareGroup($userid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_group")." where user_id=".intval($userid)." AND WEID = 0 AND shared_flag = 0" );
	return $myrows;
}
function getWechatShareGweidinfo($gweid)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." where type = 'GWEID' AND value =".intval($gweid));
	return $myrows;
}


function getWechatGroupInfo_gweid($GWEID)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_group")." where GWEID=".intval($GWEID) );
	return $myrows;
}
function getWechatGroupInfo_pubgweid($userid,$weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_group")." where user_id=".intval($userid)." and WEID=".intval($weid) );
	return $myrows;
}
function getWechatGroupInfo_gweid_shared($userid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_group")." where user_id=".intval($userid)." AND WEID != 0 AND shared_flag = 1" );
	return $myrows;
}
function getWechatGroupInfo_gweid_activeshared($userid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_group")." where user_id=".intval($userid)." AND WEID != 0 AND (shared_flag = 1 or shared_flag=2)" );
	return $myrows;
}

//Get virtual account with group id -- 20150514
function getvirtualaccount_withgroupid($groupid)
{
	global $wpdb;
	$myrow = $wpdb->get_var($wpdb->prepare("SELECT gweid FROM ".web_admin_get_table_name("wechat_group")." where group_id=%d AND WEID = %d", $groupid, 0 ));
	return $myrow;
}
//Get virtual account with user id -- 20150514
function getvirtualaccount_withuserid($groupid)
{
	global $wpdb;
	$myrow = $wpdb->get_var($wpdb->prepare("SELECT wgroup.gweid as gweid FROM ".web_admin_get_table_name("wechat_group")." as wgroup," .web_admin_get_table_name("user_group") ." as ugroup where ugroup.group_id=%d AND ugroup.flag = %d AND wgroup.weid = %d AND ugroup.user_id = wgroup.user_id", $groupid, 1,0 ));
	return $myrow;
}
//update group id at wechat_group for virtual account -- 20150514
function updatevirtualaccount_withgroupid($groupid, $gweid)
{
	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechat_group"),array('group_id'=>$groupid),array('GWEID'=>$gweid),array("%d"),array("%d"));
	return $update;
}
//update user id at wechat_group for virtual account -- 20150514
function updatevirtualaccount_withuserid($userid, $gweid)
{
	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechat_group"),array('user_id'=>$userid),array('GWEID'=>$gweid),array("%d"),array("%d"));
	return $update;
}
//update user id at wechat_userchat for virtual account -- 20150514
function updateusechat_withuserid($userid, $gweid)
{
	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechat_usechat"),array('user_id'=>$userid),array('GWEID'=>$gweid),array("%d"),array("%d"));
	return $update;
}
//insert wechat corresponding datas
function insertvirtualaccount_wechatgroup($gweid,$user_id,$group_id)
{	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."wechat_group"."(gweid, user_id, group_id,WEID, shared_flag, send_flag, adminshare_flag)VALUES (%d, %d, %d, %d, %d, %d, %d)",$gweid, $user_id, $group_id, 0, 0, 0, 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$gweid, 'wechatfuncmass', 1));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$gweid, 'wechatvip', 1));
	return $insert!==FALSE?true:false;
}
//Update wechat_group table to reset userid = 0
function update_wechat_group_resetuserid($groupid, $userid)
{
	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechat_group"),array('group_id'=>$groupid,'user_id'=>0),array('user_id'=>$userid, 'weid' => 0),array("%d", "%d"),array("%d", "%d"));
	return $update;
}
//update wechat_userchat to reset userid = 0 -- 20150514
function update_wechat_usechat_resetuserid($userid)
{
	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechat_usechat"),array('user_id'=>0),array('user_id'=>$userid, 'weid' => 0),array("%d"),array("%d", "%d"));
	return $update;
}
//2014-07-15新增修改
function getShareGweidFuncinfo($gweid)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." WHERE type = 'GWEID' AND value = ".intval($gweid));
	return $myrows;
}
//2014-08-20新增修改
function getUseridFuncinfo($userId)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." WHERE type = 'userid' AND value = ".intval($userId));
	return $myrows;
}
function web_admin_wechat_info_get_forwechat($userid,$gweid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT w.wid, w.hash, w.wechat_nikename, w.wechat_name, w.wechat_type, w.wechat_auth, w.token, u1.WEID, u1.vericode,u1.wechat_name as wechatname FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("wechats")." w WHERE u1.wid = w.wid and u1.user_id = ".intval($userid)." and u1.GWEID =".intval($gweid));
	return $myrows;
}
function web_admin_wechat_info_forwechat($weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT w.wid, w.hash, w.wechat_nikename, w.wechat_name, w.wechat_type, w.wechat_auth, w.token, w.menu_appId, w.menu_appSc, u1.WEID, u1.vericode, u1.flgopen, u1.busi_exit, u1.prompt_content, u1.wechat_name as wechatname FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("wechats")." w WHERE u1.wid = w.wid and u1.WEID =".intval($weid));
	return $myrows;
}
function web_admin_wechat_info_forwechatgroupbyuid($userid,$weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT w.wid, w.hash, w.wechat_nikename, w.wechat_name, w.wechat_type, w.wechat_auth, w.token, w.menu_appId, w.menu_appSc, u1.WEID, u1.vericode, u1.flgopen, u1.busi_exit, u1.prompt_content, u1.wechat_name as wechatname FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("wechats")." w WHERE u1.wid = w.wid and u1.WEID =".intval($weid)." and u1.user_id =".$userid);
	return $myrows;
}
function web_admin_wechat_info_forwechatNew($weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT w.wid, w.hash, w.wechat_nikename, w.wechat_name, w.wechat_type, w.wechat_auth, w.token, w.menu_appId, w.menu_appSc, u1.WEID, u1.vericode, u1.flgopen, u1.busi_exit, u1.prompt_content, u1.wechat_name as wechatname, u1.wechat_fan_init, w1.shared_flag, u1.wechat_imgurl,u1.wechat_cuservice FROM ".web_admin_get_table_name("wechat_usechat")." u1," .web_admin_get_table_name("wechats")." w,".web_admin_get_table_name("wechat_group")." w1 WHERE u1.wid = w.wid and u1.WEID = w1.WEID and u1.WEID =".intval($weid));
	return $myrows;
}
function getWechatGroup_insert($GWEID,$user_id,$WEID,$shared_flag)
{	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_group")."(GWEID, user_id, WEID,shared_flag) VALUES (%d, %d, %d, %d)",$GWEID,$user_id, $WEID, $shared_flag));
	
	return $insert!==FALSE?true:false;
}
function getWechatGroup_update($status,$gweid)
{	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechat_group"),array('shared_flag'=>$status),array('GWEID'=>$gweid),array("%d"),array("%d"));
	return $update;
}
function web_admin_usechat_pubsvcinfo_group($gweid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT u1.WEID,u2.wechat_nikename FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("wechats")." u2 where u1.wid=u2.wid and ((u2.wechat_type='pub_svc') or (u2.wechat_type='pub_sub' and u2.wechat_auth='1')) and u1.GWEID='".intval($gweid)."'" );
	return $myrows;
}

function web_admin_usechat_info_group($gweid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." where GWEID='".intval($gweid)."'" );
	return $myrows;
}
function web_admin_usechat_info_dis_group($gweid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT distinct wid FROM ".web_admin_get_table_name("wechat_usechat")." where GWEID='".intval($gweid)."'" );
	return $myrows;
}
function web_admin_usechat_prisvcinfo_group($gweid)
{
	global $wpdb;
	//$myrows = $wpdb->get_results( "SELECT wechat_nikename FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("wechats")." u2 where u1.wid=u2.wid and ((u2.wechat_type='pri_svc') or (u2.wechat_type='pri_sub' and u2.wechat_auth='1')) and u1.GWEID='".$gweid."'" );
	$myrows = $wpdb->get_results( "SELECT u2.wechat_nikename,u2.wid,u2.menu_appId,u2.menu_appSc,u2.menu_token FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("wechats")." u2 where u1.wid=u2.wid and ((u2.wechat_type='pri_svc') or (u2.wechat_type='pri_sub' and u2.wechat_auth='1')) and u1.WEID != 0 and u1.GWEID='".intval($gweid)."'" );
	return $myrows;
}
function web_admin_usechat_allinfo_group($gweid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT wechat_nikename FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("wechats")." u2 where u1.wid=u2.wid and u1.GWEID='".intval($gweid)."'" );
	return $myrows;
}
function web_admin_usechat_allpriinfo_group($gweid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT wechat_nikename FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("wechats")." u2 where u1.wid=u2.wid and ((u2.wechat_type='pri_svc') or ((u2.wechat_type='pri_sub') and ( u2.wechat_auth='1'))) and u1.WEID != 0 and u1.GWEID='".intval($gweid)."'" );
	return $myrows;
}

function web_admin_wechats_info($wid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechats")." where wid='".intval($wid)."'" );
	return $myrows;
}
function web_admin_usechat_winfo($weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." where WEID='".intval($weid)."'" );
	return $myrows;
}
function web_admin_usechat_winfo_bywid($wid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." where wid='".intval($wid)."'" );
	return $myrows;
}
function web_admin_usechat_winfo_bywids($wid,$GWEID)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." where wid='".intval($wid)."' and GWEID=".intval($GWEID) );
	return $myrows;
}
//判断该用户是否在wp_wechat_initfunc_info表中添加过gweid的相关记录
function web_wechat_countgweid_group($gweid)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as gweidCount FROM ".web_admin_get_table_name("wechat_initfunc_info")." where type='GWEID' and value = ".intval($gweid));
    
	return $myrows;
}  
//设置会员是否需要审核
function web_admin_update_usechat_audit($auditStatus,$gweid)
{
	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechat_usechat"),array('wechat_vipaudit'=>$auditStatus),array('GWEID'=>$gweid),array("%d"),array("%d"));
	return $update;
	
}  
//初始化用户功能表wp_wechat_initfunc_info中的gweid的相关记录
function web_admin_initfunction_group($gweid)
{
	global $wpdb;
	//将insert改为replace
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatwebsite', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatfuncfirstconcern', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatfunckeywordsreply', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatfuncnokeywordsreply', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatfuncmanualreply', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatfuncaccountmanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatfuncmaterialmanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatfuncmenumanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatfuncusermanage', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatactivity_coupon', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatactivity_scratch', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatactivity_fortunewheel', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatactivity_toend', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatactivity_fortunemachine', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatresearch', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatschool', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatvip', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'template_selno', 0));
	$wpdb->query( $wpdb->prepare("REPLACE INTO ".web_admin_get_table_name("wechat_initfunc_info")."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID', $gweid, 'wechatfuncmass', 0));
	
	return $site_id;
}


//function_custome页面在第二次以后的添加中需要显示出之前选择过的一些功能项
function web_user_display_index_group($GWEID, $userid, $wid)
{
    global $wpdb;

	$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_func_info")." a WHERE NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$GWEID." AND func_flag = 0) AND EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) AND NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b3 WHERE a.func_name=b3.func_name AND type = 'wid' AND value = ".$wid." AND func_flag = 0) LIMIT 0, 100");
	//echo "SELECT * FROM ".web_admin_get_table_name("wechat_func_info")." a WHERE NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$GWEID." AND func_flag = 0) AND EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) AND NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b3 WHERE a.func_name=b3.func_name AND type = 'wid' AND value = ".$wid." AND func_flag = 0) LIMIT 0, 100";
	
	return $myrows;
}

//2014-07-07新增修改，功能选项最终是由平台和admin添加用户两者决定的
function web_user_display_index_groupnew($GWEID, $userid)
{
    global $wpdb;

	$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_func_info")." a WHERE NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$GWEID." AND func_flag = 0) AND EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) LIMIT 0, 100");
	return $myrows;
}

//2014-07-07新增修改删除用户添加的公众号
//2014-07-09新增修改
function web_admin_delete_wechatnumber_group($userid, $gweid, $wid, $weid)
{
	global $wpdb;
	
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechats")." where wid = ".intval($wid) ); 
	foreach($myrows as $my){
		$wechatype = $my -> wechat_type;
	}//如果是个人公众号，需要删除wechats和usechat以及inintfunc三张表；如果是公共公众号，需要删除usechat以及initfunc两张表
	if($wechatype == "pri_sub" ||$wechatype == "pri_svc")
	{
	    $getweids = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_usechat")." where user_id = ".intval($userid)." AND GWEID = ".intval($gweid)." AND wid=".intval($wid) ); 
		foreach($getweids as $getweid){
			$weid = $getweid->WEID;
		}
	    //删除usechat中该公众号的信息记录
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_usechat")." WHERE wid = %d AND user_id = %d AND GWEID = %d", $wid, $userid, $gweid) );
		//判断当前删除完该公众号后是不是还有别的公众号，如果没有则清空gweid中的功能选项
		
	    //删除wechats中该公众号的信息记录
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechats")." WHERE wid = %d", $wid ) );
		//删除WEID在initfunc表中的记录
		
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_initfunc_info")." WHERE type = 'WEID' AND value = %d", $weid ) );
		//删除wid在initfunc表中的记录
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_initfunc_info")." WHERE type = 'wid' AND value = %d", $wid ) );
		
		
	}
	else
	{
	    //2014-07-08新增修改删除这段
		//删除usechat中该公众号的信息记录
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_usechat")." WHERE WEID = %d AND wid = %d AND user_id = %d AND GWEID = %d", $weid, $wid, $userid, $gweid) );
		
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_subscribe")." WHERE WEID = %d", $weid) );
		
		//删除WEID在initfunc表中的记录
		
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_initfunc_info")." WHERE type = 'WEID' AND value = %d", $weid ) );
		
		//公用公众号的wid是不能删除的
		//$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_func_info")." a WHERE a.status = 1 AND EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) AND EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$gweid." AND func_flag = 1) AND EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND func_flag = 1 AND type = 'WEID' AND b2.value IN (SELECT WEID from ".web_admin_get_table_name("wechat_usechat")." WHERE user_id = ".$userid." AND GWEID =".$gweid.")) AND (EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b3 WHERE a.func_name=b3.func_name AND type = 'wid' AND value = ".$wid." AND func_flag = 1) OR EXISTS( SELECT * FROM ".web_admin_get_table_name("wechats")." a1 WHERE (a1.wechat_type = 'pri_sub' OR a1.wechat_type = 'pri_svc') AND a1.wid IN (SELECT wid from ".web_admin_get_table_name("wechat_usechat")." WHERE user_id = ".$userid." AND GWEID =".$gweid."))) LIMIT 0, 100");
	}
		//2014-07-15新增修改$userid, $gweid, $wid, $weid
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("wechat_group")." WHERE GWEID = %d AND user_id = %d AND WEID = %d", $gweid,$userid,  $weid) );
		
	
	
}
/*共用公众号权限设置*/
function web_user_display_index_groupnew_wesforsel($GWEID)
{
    global $wpdb;

	$myrows = $wpdb->get_results("SELECT * FROM ".web_admin_get_table_name("wechat_func_info")." a WHERE NOT EXISTS(SELECT * FROM ".web_admin_get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$GWEID." AND func_flag = 0) LIMIT 0, 100");
	
	return $myrows;
}
/*共用公众号权限设置end*/
function web_admin_member_group_fromuser_isexist($mid,$fromuser){
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".web_admin_get_table_name("wechat_member_group")." where mid=%d and from_user=%s" ,$mid,$fromuser));
	return $myrows;

}
function web_admin_member_group_insert($mid,$weid,$gweid,$fromuser){
	global $wpdb;
	
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".web_admin_get_table_name("wechat_member_group")."(mid, WEID, GWEID,from_user)VALUES (%s, %d, %d,%s)",$mid, $weid, $gweid,$fromuser));
	return $insert!==FALSE?true:false;
}
function web_admin_member_wgroup($weid,$gweid,$fromuser)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".web_admin_get_table_name("wechat_member_group")." where WEID=%d and GWEID=%d and from_user= %s",$weid,$gweid,$fromuser));
    
	return $myrows;
} 
function web_admin_member_count_wgroup($weid,$gweid,$fromuser)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare("SELECT COUNT(*) as memberCount FROM ".web_admin_get_table_name("wechat_member_group")." where WEID=%d and GWEID=%d and from_user= %s",$weid,$gweid,$fromuser));
    
	return $myrows;
} 

//删除某一文件夹下的所有文件
function deldir($dir) {
  //先删除目录下的文件：
  try
  {
	  $dh=opendir($dir);
	  while ($file=readdir($dh)) {
		if($file!="." && $file!="..") {
		  $fullpath=$dir."/".$file;
		  if(!is_dir($fullpath)) {
			  @unlink($fullpath);
		  } else {
			  deldir($fullpath);
		  }
		}
	  }
	  closedir($dh);
	  //删除当前文件夹：
	  if(rmdir($dir)) {
		return true;
	  } else {
		return false;
	  }
  }
  catch (Exception $e){return $e->message();}
  
}
function getWechatGroupActiveInfo($userid,$shared_flag)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_group")." where WEID != 0 and user_id=".intval($userid)." and shared_flag=".intval($shared_flag)." order by WEID" );
	return $myrows;
}
function getWechatGroupActiveAllInfo($userid,$shared_flag)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("wechat_group")." where WEID != 0 and user_id=".intval($userid)." and (shared_flag=".$shared_flag." or shared_flag=2) order by WEID" );
	return $myrows;
}
function getWechatGroupActive_update($status,$user_id)
{	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechat_group"),array('shared_flag'=>$status),array('user_id'=>$user_id),array("%d"),array("%d"));
	return $update;
}
function getWechatGroupActive_updateActive($setshared_flag,$shared_flag,$user_id)
{	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechat_group"),array('shared_flag'=>$setshared_flag),array('user_id'=>$user_id,'shared_flag'=>$shared_flag),array("%d"),array("%d","%d"));
	return $update;
}
function web_admin_post_content($postContent)
{
	/*$upload =wp_upload_dir();
	$baseurl=$upload['baseurl'];
	$sp = '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i'; 
	preg_match_all( $sp, $postContent, $aPics );  
	$np = count($aPics[0]); 
	if ( $np > 0 ) {   
		for ( $i=0; $i < $np ; $i++ ) {  
			$post_picurl=$baseurl.$aPics[1][$i];
			if(stristr($aPics[1][$i],"http")===false){
				$postContent=str_ireplace($aPics[1][$i],$post_picurl,$postContent);
			}
		}
	}
	return $postContent;*/
	$upload =wp_upload_dir();
	$baseurl=$upload['baseurl']; 
	$sp='~<img [^\>]*\ ?/?>~';
	preg_match_all( $sp, $postContent, $aPics ); 

	$np = count($aPics[0]); 
	$SoImgAddress="/\<img.*?src\=\"(.*?)\"[^>]*>/i";  //正则表达式语句
	
	if ( $np > 0 ) {   
		for ( $i=0; $i < $np ; $i++ ) {  			
			$ImgUrl = $aPics[0][$i];
			preg_match($SoImgAddress,$ImgUrl,$imagesurl);
			$post_picurl=$baseurl.$imagesurl[1];
			if((stristr($imagesurl[1],"http")===false) && (stristr($imagesurl[1],'file://')===false)&&(stristr($imagesurl[1],'data:')===false)){
				$postContent=str_ireplace($imagesurl[1],$post_picurl,$postContent);
			}
		}
	}
	return $postContent;
}

//new feature 20140918
//获取所有的group的个数
function web_admin_count_usergroup()
{
    global $wpdb;
	//$myrows = $wpdb->get_results("SELECT COUNT(*) as groupCount FROM ".web_admin_get_table_name("group"));   
	$myrows = $wpdb->get_results("SELECT COUNT(*) as groupCount FROM ".web_admin_get_table_name("group"));                                           
	return $myrows;
}
//获取group当页的所有数据集
function web_admin_array_usergroup($offset,$pagesize)
{
    global $wpdb;
	//$myrows = $wpdb->get_results( "SELECT * FROM ".web_admin_get_table_name("group")." w1 ORDER BY w1.ID DESC limit ".$offset.",".$pagesize );
	$myrows = $wpdb->get_results( "SELECT w1.ID as ID,w1.group_name as group_name, w3.user_login as user_login  FROM ".web_admin_get_table_name("group")." w1 left join ".web_admin_get_table_name("user_group")." w2 on w1.ID = w2.group_id AND w2.flag = 1 left join ".web_admin_get_table_name("users")." w3 on w2.user_id = w3.ID ORDER BY w1.ID DESC limit ".$offset.",".$pagesize );
	return $myrows;
}

//获取根据查询条件得到的group个数
function web_admin_count_selectusergroup($indata,$rg)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare("SELECT COUNT(*) as memberCount FROM ".web_admin_get_table_name("group")." w1 WHERE w1.".$rg." like %%%s%%",$indata));
	return $myrows;
}
//获取根据查询条件得到的group数据集
function web_admin_array_selectusergroup($indata,$rg,$offset,$pagesize)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT  w1.ID as ID,w1.group_name as group_name, w3.user_login as user_login FROM ".web_admin_get_table_name("group")." w1 left join ".web_admin_get_table_name("user_group")." w2 on w1.ID = w2.group_id AND w2.flag = 1 left join ".web_admin_get_table_name("users")." w3 on w2.user_id = w3.ID WHERE w1.".$rg." like '%%%s%%' ORDER BY w1.ID DESC limit ".$offset.",".$pagesize,$indata) );
	return $myrows;
}

//拿到对应group中的users20140924update
function wechat_group_user_list($groupid)
{
	global $wpdb;	
    //取出全部用户
    if($groupid == -1)
    {
		$myrows = $wpdb->get_results( "SELECT u1.ID as id, u1.user_login as name FROM ".web_admin_get_table_name("users")." u1 where u1.user_pass != '' order by u1.ID ASC" );
    }	
	elseif($groupid == 0) //取出默认分组中的用户,默认分组就是未分组，对应的id为0，名称可能会变
	{
		$myrows = $wpdb->get_results( "SELECT distinct w1.ID as id, w2.group_id, w1.user_login as name FROM ".web_admin_get_table_name("users")." w1 left join ".web_admin_get_table_name("user_group")." w2 on w1.ID = w2.user_id WHERE w1.ID != 0 AND w1.user_pass != '' AND (isnull(w2.group_id) OR w2.group_id = 0) order by w1.ID ASC" );
	}
	else{    //取出当前分组中的用户
        //secure query method
		$myrowssql = $wpdb -> prepare("SELECT distinct w1.ID as id, w2.group_id, w1.user_login as name FROM ".web_admin_get_table_name("users")." w1 left join ".web_admin_get_table_name("user_group")." w2 on w1.ID = w2.user_id WHERE w1.ID != 0 AND w1.user_pass != '' AND w2.group_id = %d order by w1.ID ASC", $groupid);
		$myrows = $wpdb->get_results($myrowssql);
	
	}
	
	return $myrows;
}

//如果分组下没有用户，则直接删除该分组
function wp_delete_group($groupid)
{
   	//删除用户空间信息
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".web_admin_get_table_name("group")." WHERE ID = %d", $groupid ) );
	
}
function wechat_get_group_name($userid)
{
    global $wpdb;
	
	//$myrows = $wpdb->get_results( "SELECT w3.group_name as name FROM ".web_admin_get_table_name("users")." w1 left join ".web_admin_get_table_name("user_group")." w2 on w1.ID = w2.user_id left join ".web_admin_get_table_name("group")." w3 on w2.group_id = w3.ID WHERE w1.ID = ".$userid);
	//secure query db
	$myrowssql2 = $wpdb -> prepare("SELECT w3.group_name as name FROM ".web_admin_get_table_name("users")." w1 left join ".web_admin_get_table_name("user_group")." w2 on w1.ID = w2.user_id left join ".web_admin_get_table_name("group")." w3 on w2.group_id = w3.ID WHERE w1.ID = %d", $userid);
	$myrows = $wpdb->get_results($myrowssql2);
	return $myrows;
}

function wechat_get_group_name_byid($groupid)
{
    global $wpdb;
	
	//$myrows = $wpdb->get_results( "SELECT w3.group_name as name FROM ".web_admin_get_table_name("users")." w1 left join ".web_admin_get_table_name("user_group")." w2 on w1.ID = w2.user_id left join ".web_admin_get_table_name("group")." w3 on w2.group_id = w3.ID WHERE w1.ID = ".$userid);
	//secure query db
	$myrowssql2 = $wpdb -> prepare("SELECT w3.group_name FROM ".web_admin_get_table_name("group")." w3 WHERE w3.ID = %d", $groupid);
	$myrows = $wpdb->get_results($myrowssql2);
	return $myrows;
}

//取出该用户下的公众号
function wechat_group_user_account_list($userid)
{
	global $wpdb;	
	//$myrows = $wpdb->get_results( "SELECT distinct w1.GWEID as gweid, w3.wechat_nikename as nickname FROM ".web_admin_get_table_name("wechat_group")." w1, ".web_admin_get_table_name("wechat_usechat")." w2, ".web_admin_get_table_name("wechats")." w3 where w1.WEID = w2.WEID AND w2.wid = w3.wid AND w1.user_id = ".$userid );
    //secure query db
	$myrowssql1 = $wpdb -> prepare("SELECT distinct w1.GWEID as gweid, w3.wechat_nikename as nickname FROM ".web_admin_get_table_name("wechat_group")." w1, ".web_admin_get_table_name("wechat_usechat")." w2, ".web_admin_get_table_name("wechats")." w3 where w1.WEID = w2.WEID AND w2.wid = w3.wid AND w1.user_id = %d", $userid);
	$myrows = $wpdb->get_results($myrowssql1);
	return $myrows;
}

//拿到对应group中的users
function wechat_group_user_account_count($userid)
{
	global $wpdb;	 
	//secure query db
    $myrowssql3 = $wpdb -> prepare("SELECT count(*) as ucount FROM ".web_admin_get_table_name("wechat_group")." w1 where w1.user_id = %d", $userid);
	$myrows = $wpdb->get_results($myrowssql3);
	return $myrows;
}
function wechat_getgroup($gweid)
{
    global $wpdb;	
    $myrows = $wpdb->get_results( "SELECT w2.group_id FROM ".web_admin_get_table_name("wechat_group")." w1 left join ".web_admin_get_table_name("user_group")." w2 on w1.user_id = w2.user_id WHERE w1.user_id != 0 AND w1.GWEID = ".$gweid);
	return $myrows;
}
function wechat_getgroup_list()
{
    global $wpdb;	
	$myrows = $wpdb->get_results( "SELECT distinct w2.group_id as id, w3.group_name as name FROM ".web_admin_get_table_name("wechat_group")." w1 left join ".web_admin_get_table_name("user_group")." w2 on w1.user_id = w2.user_id left join ".web_admin_get_table_name("group")." w3 on w2.group_id = w3.ID ");
	return $myrows;
}
/* oauth openid Function
   2015-04-16
*/
function web_admin_getpri_gweid($gweid)
{
	global $wpdb;
	$winfo= $wpdb->get_row( "SELECT u1.WEID,u2.wid,u2.menu_appId,u2.menu_appSc FROM ".web_admin_get_table_name("wechat_usechat")." u1,".web_admin_get_table_name("wechats")." u2 where u1.wid=u2.wid and (u2.wechat_type='pri_svc' and u2.wechat_auth='1') and u1.WEID != 0 and u1.GWEID='".intval($gweid)."'" ,ARRAY_A);
	return $winfo;
}

//20150420 sara new added
/*该公众号是否处于开启共享状态的分组管理员对应的虚拟号下*/
function virtualgweid_open($gweid){
	global $_W,$wpdb;
	
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$getgroupids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_group w left join {$wpdb->prefix}user_group u on w.user_id = u.user_id where w.GWEID=".$gweid,ARRAY_A);
	//obtain the groupid
	if(!empty($getgroupids)){
		foreach ($getgroupids as $getgroupid) {
			$gid = $getgroupid['group_id'];
		}
		if(!empty($gid)){
			$getflags = $wpdb->get_results("SELECT count(*) as flagcount FROM {$wpdb->prefix}user_group u where u.group_id=".$gid." and u.flag = 1",ARRAY_A);
			//obtain the groupid
			foreach ($getflags as $getflag) {
				$flagcount = $getflag['flagcount'];
			}
			//如果有分组管理员,查看分组中的虚拟号是否是开启状态
			if($flagcount != 0){
				$getvgweids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_group u, {$wpdb->prefix}wechat_group w where u.user_id=w.user_id and u.flag = 1 and w.adminshare_flag = 1 and u.group_id=".$gid,ARRAY_A);
				//obtain the groupid
				if(!empty($getvgweids)){
					foreach ($getvgweids as $getvgweid) {
						$vgweid = $getvgweid['GWEID'];
					}
					$gweid = $vgweid;   //将虚拟号的gweid赋过来
				}
			}
		}
	}
	return $gweid;
}

//当前用户有可能是分组管理员下的，如果分组管理员下的切换，需要找到对应的session中的值
function web_admin_issuperadmin($currentuserid){
   	global $_W,$wpdb;
	$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$currentuserid);
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
	//如果是分组管理员
	if($usergroupid !=0 && $usergroupflag == 1){
		$groupadminflag = 1;
	}else{
		$groupadminflag = 0;
	}
	return $groupadminflag;
}

//判断是否是分组管理员
function is_superadmin($gweid){
   	global $_W,$wpdb;
	$getgroupadmins = $wpdb->get_results( "SELECT count(*) as acount FROM {$wpdb -> prefix}wechat_group where WEID = 0 and adminshare_flag = 1 and GWEID = ".$gweid);
	
	foreach($getgroupadmins as $getgroupadmin)
	{
	    $groupadminc = $getgroupadmin -> acount;
	}
	
	return $groupadminc;  //如果为1，则表示分组管理员
}

//更新分组共享设置中的共享状态
function web_admin_changegroupshare($gweid,$weid,$status)
{	
	global $wpdb;
	$update = $wpdb -> update(web_admin_get_table_name("wechat_group"),array('adminshare_flag'=>$status),array('GWEID'=>$gweid,'WEID'=>$weid),array("%d"),array("%d","%d"));
	return $update;
}

?>