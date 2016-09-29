<?php
/*---------------------MENU SETTINGS--------------------*/
function com_save_metaa($postId)
{
	
	if(isset($_POST['menu_item_url']) )
    {
    	/*如果包含homeurl，则截取后入数据库*/
		$tmp = stristr($_POST['menu_item_url'],home_url());
		if($tmp===false){
			$linkurl=$_POST['menu_item_url'];
		}else{
			$str = stristr($_POST['menu_item_url'], home_url());
			$postion=intval($str)+intval(strlen(home_url()));
			$linkurl=substr($_POST['menu_item_url'], $postion);		
		}	
		update_post_meta($postId, 'menu_item_url', $linkurl);  
    }
    if(isset($_POST['selectMenu']))
    {
        global $wpdb,$table_prefix;
        define('TABLE_POSTS', $table_prefix.'posts');
        $wpdb->query("UPDATE ".TABLE_POSTS." SET post_content_filtered = '".$_POST['selectMenu']."' WHERE `ID`= '".$postId."'");
    }
}
add_action('save_post', 'com_save_metaa');

function com_post_metaa()
{
    $content = 'first';
    if(isset($_REQUEST['post']) && is_numeric($_REQUEST['post']))
    {
        $post = (int)$_REQUEST['post'];
        $post = get_post($post);
		
		$menu_item_url = get_post_meta($post->ID, 'menu_item_url', true);
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($menu_item_url,"http");
		if(($tmp===false)&&(!empty($menu_item_url))){
			$linkurl=home_url().$menu_item_url;
		}else{				
			$linkurl=$menu_item_url;
		}
        $content =  $post->post_content;
    }

?>
    <div style="padding:10px;float:left;">
         <label style="width:200px; float: left; padding-top:6px;">Link URL</label>
         <div style="float:left;">
         <input type="text" name="menu_item_url" style="width:300px" value="<?php echo $linkurl; ?>" />
         <em style="padding:5px 0px; display:block;">Icon link URL</em>
         </div>
    </div>
    <div style="padding:10px;float:left;">
        <label style="width:200px; float: left; padding-top:6px;">所属菜单</label>
        <div style="float:left;">
             <select name="selectMenu" id="selectMenu">
                 <option value ="first" <?php if($content == "1")echo 'selected="selected"'; ?>>1</option>
                 <option value ="member" <?php if($content == "2")echo 'selected="selected"'; ?>>2</option>
             </select>

        </div>
    </div>

    <div class="clear"></div> 
    
<?php
}
function com_register_meta_boxx()
{
    add_meta_box('custom_metaa', __('Menu Options'), 'com_post_metaa', 'icons_menu', 'normal', 'high');
}
add_action('admin_init', 'com_register_meta_boxx');

?>