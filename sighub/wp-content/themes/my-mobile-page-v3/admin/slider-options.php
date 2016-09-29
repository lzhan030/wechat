<?php	 	
/*---------------------MENU SETTINGS--------------------*/
function com_save_metaaa($postId)
{
	
	if(isset($_POST['slider_item_url']) )
    {
    	update_post_meta($postId, 'slider_item_url', $_POST['slider_item_url']);  
    }
}
add_action('save_post', 'com_save_metaaa');

function com_post_metaaa()
{
    if(isset($_REQUEST['post']) && is_numeric($_REQUEST['post']))
    {
        $post = (int)$_REQUEST['post'];
        $post = get_post($post);
		
		$slider_item_url = get_post_meta($post->ID, 'slider_item_url', true);		
    }

?>
    <div style="padding:10px;float:left;">
         <label style="width:200px; float: left; padding-top:6px;">Link URL</label>
         <div style="float:left;">
         <input type="text" name="slider_item_url" style="width:300px" value="<?php	 	 echo $slider_item_url; ?>" />
         <em style="padding:5px 0px; display:block;">Link URL</em>
         </div>
    </div>
    <div class="clear"></div> 
    
<?php	 	
}
function com_register_meta_boxxx()
{
    add_meta_box('custom_metaaa', __('Add a link to your slider image'), 'com_post_metaaa', 'slider', 'normal', 'high');
}
add_action('admin_init', 'com_register_meta_boxxx');

?>