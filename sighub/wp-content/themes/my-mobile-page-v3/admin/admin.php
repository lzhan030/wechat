<?php
class AdmincoreAdmin
{
    var $theme;
    
    function AdmincoreAdmin()
    {
        if(is_admin()) {
            global $theme, $pagenow;
            $this->theme = $theme;
            
            if(isset($_GET['activated'] ) && $pagenow == "themes.php") {
                wp_redirect( admin_url('themes.php?page=admincore') );
                exit();
            } 
            
            add_action('admin_menu', array(&$this, 'loadMenu'));
            add_action('admin_head', array(&$this, 'loadHead') );
            add_action('wp_ajax_admincore_ajax', array(&$this, 'Ajax') );
            
            $this->setupAdmincore();
        }
    }
    
    function setupAdmincore($reset = false)
    {
        if(!$this->theme->options['theme_options'] || $reset) {
            if(is_array($this->theme->admin_options)) {
                $save_options = array();
                foreach($this->theme->admin_options as $admincore_options) {
                    
                    if(is_array($admincore_options['content'])) {
                        foreach($admincore_options['content'] as $admincore_elements) {
                            if(is_array($admincore_elements['content'])) {
                                
                                $elements = $admincore_elements['content'];
                                if($elements['type'] !='content' && $elements['type'] !='raw') {
                                    $save_options[$elements['name']] = $elements['value'];
                                }
                            }
                        }
                    }
                }
                update_option($this->theme->options['theme_options_field'], $save_options);
                $this->theme->options['theme_options'] = $save_options;
            }
        }
    }

    function loadMenu()
	{
	   add_theme_page($this->theme->theme_name . " Theme Options", $this->theme->theme_name . " Theme Options", 'administrator', 'admincore',  array(&$this, 'AdmincoreThemeOptions'));
	}
    
    function loadHead()
	{
		echo "<script type='text/javascript'> var admincore_nonce = \"" . wp_create_nonce( 'admincore-nonce' ) . "\"; </script> \n";
        echo "<script type='text/javascript' src='" . ADMINCORE_ADMIN_URL . "/js/ajaxupload.js'></script> \n";
		echo "<script type='text/javascript' src='" . ADMINCORE_ADMIN_URL . "/js/common.js'></script> \n";
		echo "<link rel='stylesheet' href='" . ADMINCORE_ADMIN_URL . "/css/admin-style.css' type='text/css' media='all' /> \n";
        echo "<script type='text/javascript' src='" . ADMINCORE_ADMIN_URL . "/js/jscolor/jscolor.js'></script> \n";
        echo '<!--[if lt IE 8]><style type="text/css"><!-- input.admin-text, textarea.admin-textarea { width: 97%; } --></style><![endif]-->' . "\n";
	}
    
    function Ajax() 
	{
		check_ajax_referer( "admincore-nonce");
        $act = 'ajax_' . $this->theme->request('act');
        
		if (is_callable(array(get_class($this), $act))) {
            $this->$act();
        } else {
            echo 'Call to not defined ajax function: ' . $act;
        }
		exit();
	}
    
    function ajax_savechanges()
    {
        if($_POST) {
           $options = $this->theme->options['theme_options'];
           foreach($options as $option_key =>$option_val) {
                $saveval = isset($_POST[$option_key]) ? $_POST[$option_key] : '';
                $options[$option_key] = $saveval;  
           }
           update_option($this->theme->options['theme_options_field'], $options);
        }
        echo '<span style="color:green; font-weight:bold;">Changes saved successfully!</span>';
    }
    
    function ajax_imageupload()
    {
        $allowed_imagetypes = array('.jpg', '.jpeg', '.gif', '.png', '.bmp', '.ico');
        $imgname = $_POST['imgname'];
        $filename = $_FILES[$imgname];
        $filename['name'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', $filename['name']); 
        $filename_ext = substr($filename['name'], strpos($filename['name'],'.'), strlen($filename['name'])-1); 
        
        if(!in_array(strtolower($filename_ext),$allowed_imagetypes)) {
            echo 'Upload Error: The file extension ' . $filename_ext . ' is not allowed!'; 
        } else {
            $override['test_form'] = false;
            $override['action'] = 'wp_handle_upload';    
            $uploaded_image = wp_handle_upload($filename,$override);
            
            if(!empty($uploaded_image['error'])) {
                echo 'Upload Error: ' . $uploaded_image['error']; 
            } else { 
                echo  $uploaded_image['url']; 
            }
        }
    }
    
    function ajax_admin_options()
    {
        $do = $this->theme->request('do');
        if($do == 'reset') {
            $this->setupAdmincore(true);
            echo '<div class="admin-success">The options was reset successfully. <a href="'. admin_url() . 'themes.php?page=admincore">Click here</a> to reload the Options Page.</div>';
        }
    }
    
    function get_priority($array = array(), $current_priority) 
    {
        if(isset($array[$current_priority])) {
            $return_priority = $this->get_priority($array, $current_priority+1);
        } else {
            $return_priority = $current_priority;
        }
        return $return_priority;
    }
    
    function do_priority($array = array()) 
    {
        $i = time();
        $return = array();
        foreach($array as $key=>$val) {
            $i++;
            $priority = $val['priority'] ? $val['priority'] : $i;
            $val['name'] = $key;
            $return[$this->get_priority($return, $priority)] = $val;
        }
        ksort($return);
        return $return;
    }
    
    function apply_attributes($attributes = array()) 
    {
        $skip = array('type', 'name', 'value', 'help', 'priority', 'display', 'options', 'prefix', 'suffix', 'callback');
        $returnval = '';
		foreach ($attributes as $key => $val) {
			if(!in_array($key, $skip)) {
				$returnval .= ' ' . $key . '="' . $val . '" ';
			}
		}
		return $returnval;
    }
    
    
    function form_text ($name, $attributes = array())
	{
        $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '';
		return $prefix . '<input type="text" class="admin-text" name="' . $name . '" value="' . $this->theme->get_option($name) .'" ' . $this->apply_attributes($attributes) . ' />' . $suffix;
	}
    
    function form_textarea ($name, $attributes = array())
	{
        $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '';
		return $prefix . '<textarea class="admin-textarea" name="' . $name . '" ' . $this->apply_attributes($attributes) . ' />' . $this->theme->get_option($name) . '</textarea>' . $suffix;
	}
    
    function form_checkbox ($name, $attributes = array())
	{
	    $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '';
		$checked = (strlen($this->theme->get_option($name)) > 0) ? ' checked="checked" ' : '';
        $thevalue = (strlen($attributes['value']) > 0) ? $attributes['value'] : $name;
		return $prefix . '<input type="checkbox" class="tt-checkbox" name="' . $name . '" value="' . $thevalue . '"' . $checked . '' . $this->apply_attributes($attributes) . ' />' . $suffix;
	}
    
    function form_checkboxes ($name, $attributes = array())
	{
	    $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '<br />';
	    $current_val = is_array($this->theme->get_option($name)) ? $this->theme->get_option($name) : array();
		$options = $attributes['options'];
        $returnval = '';
        foreach ($options as $option_name => $option_value) {
         
            $checked = in_array($option_name,$current_val) ? ' checked="checked" ' : '';
            $returnval .=  $prefix . '<input type="checkbox" class="tt-checkbox" name="' . $name . '[]" value="' .$option_name . '"' . $checked . '' . $this->apply_attributes($attributes) . ' /> ' . $option_value . $suffix . "\n";
        }
		return $returnval;
	}
    
    function form_radio ($name, $attributes = array())
	{
	    $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '<br />';
	    $current_val = $this->theme->get_option($name);
		$options = $attributes['options'];
        $returnval = '';
        foreach ($options as $option_name => $option_value) {
            $checked = $current_val ==  $option_name ? ' checked="checked" ' : '';
            $returnval .= $prefix . '<input class="tt-radio" type="radio" name="' . $name . '" value="' . $option_name . '"' . $checked . '' . $this->apply_attributes($attributes) . ' /> ' . $option_value . $suffix . "\n";
        }
		return $returnval;
	}
    
    function form_select ($name, $attributes = array())
	{
	    $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '';
	    $current_val = $this->theme->get_option($name);
		$options = $attributes['options'];
        $returnval = $prefix . '<select class="admin-select" name="' . $name . '" ' . $this->apply_attributes($attributes) . '>' . "\n";
        foreach ($options as $option_name => $option_value) {
            $selected = $current_val ==  $option_name ? ' selected="selected" ' : '';
            $returnval .= "\t" . '<option value="' . $option_name . '"' . $selected . '>' . $option_value . '</option>' . "\n";
        }
		$returnval .= '</select>' . $suffix . "\n";
		return $returnval;
	}
    
    function form_hidden ($name, $attributes = array())
	{
	    $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '';
		return $prefix . '<input type="hidden" name="' . $name . '" value="' . $this->theme->get_option($name) .'" ' . $this->apply_attributes($attributes) . ' />' . $suffix;
	}
    
    function form_content ($name, $attributes = array())
	{
	    $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '';
		return $prefix . '<div class="tt-element-content" ' . $this->apply_attributes($attributes) . '>' . $attributes['value'] . '</div>' . $suffix;
	}
    
    function form_callback ($name, $attributes = array())
    {
        if(isset($attributes['callback']) && is_array(($attributes['callback']))) {
            $callback = $attributes['callback'];
            $callback[0]->$callback[1]();
        } else {
            if(function_exists($name)) {
                return $name($attributes);
            }
        }
        
    }

    function form_colorpicker ($name, $attributes = array())
    {
        $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '';
        $return = $prefix . "<input name=\"$name\"  value=\"" . $this->theme->get_option($name) . "\" class=\"color {required:false}\" style=\"border:1px solid #dcdfe4; width:250px;height:18px;  \" " .  $this->apply_attributes($attributes) . ">" . $suffix;
        return $return;
    }
    
    function form_imageupload ($name, $attributes = array())
    {
        $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '';
        $current_image = $this->theme->get_option($name);
        echo $prefix;
        ?>
    	<div style="width:360px; float:left;">
        <div id="<?php echo $name; ?>_error" class="admin-error" style="display: none; margin-bottom:10px;" ></div>
        <div id="<?php echo $name; ?>_preview" style="<?php if(!$current_image) { ?>display: none;<?php }?>" class="admin-image-preview" ><?php if($current_image) { ?><a href="<?php echo $current_image; ?>" target="_blank"><img src="<?php echo $current_image; ?>" title="The image might be resized, click for full preview!" alt=""  /></a><?php } ?></div>
        <div style="padding-bottom:15px;"><input class="admin-text admincore_image_upload_<?php echo $name; ?>" type="text" name="<?php echo $name; ?>" value="<?php echo  $current_image; ?>" <?php  echo $this->apply_attributes($attributes); ?> /></div>
        <div style="padding-bottom:10px;">
            <a id="admincore_image_upload_<?php echo $name; ?>" class="admin_imageupload" >Upload Now</a>
            <a <?php if(!$current_image) { ?> style="display: none;" <?php }?> id="<?php echo $name; ?>_reset" title="<?php echo $name; ?>" class="admin_imageupload_reset" >Remove</a>
        </div>
    	</div>
        <div style="clear:both;"></div>
        <?php
        echo $suffix;
    }
    
    function form_raw ($name, $attributes = array())
	{
	    $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $suffix = isset($attributes['suffix']) ? $attributes['suffix'] : '';
		return $prefix . $attributes['value'] . $suffix;
	}
    
    
    function AdmincoreThemeOptions()
    {
    ?>
        <div class="wrap">
        <form id="ttForm" method="POST">
                
             <div id="icon-themes" class="icon32"><br /></div>
            <h2><?php  echo $this->theme->theme_name;?> Theme Options </h2>
            <?php $this->optionsPageMenu(); ?>
         
                
             <div class="admin_container">
                <div class="admin_content">
                        <?php 
                            $optionsPageContent = $this->optionsPageContent();
                            if($optionsPageContent == 'no-options') {
                                echo 'No theme options available!';
                            } else {
                            ?>   
                            <div class="save_changes">
                                <a class="button_save_changes" onclick="admincore_savechanges('savechanges','ttForm','adminSaveChanges');">Save Changes</a>
                                <div id="adminSaveChanges"></div> 
                            </div>
                        <?php
                            }
                        ?>
                </div>
             </div>
        </form>
    
        </div>
    <?php
    }
    
    function optionsPageMenu()
    {
        ?>
        
            <div class="admintab">
                <?php
                    $tabids = 0;
                    $theme_optionspage = $this->do_priority($this->theme->admin_options);
                                            
                    if( is_array($theme_optionspage) && count($theme_optionspage) > 0) {
                        
                        foreach($theme_optionspage as $optionspage) {
                            $tabids++;
                            $default_active = $tabids == '1' ? ' admin-tab-active' : '';
                            echo "<a href=\"#\" class=\"admin-tab$default_active\" id=\"tab$tabids\">$optionspage[name]</a>";
                        }
                    } else {
                        echo 'No options set!';
                    }
                ?>
            </div>
        <?php
    }
    
    function optionsPageContent()
    {
        $menuids = 0;
        $admincore_options = $this->do_priority($this->theme->admin_options);
        if( is_array($admincore_options) && count($admincore_options) > 0) {
            foreach($admincore_options as $menu) {
                $menuids++;
                
                if(isset($menu['content']) && is_array($menu['content'])) {
                    $menu_items_content = $this->do_priority($menu['content']);
                    $default_first_menu = ($menuids == '1') ? ' admin-menu-content-first' : '';
                ?>
                    <div class="tab<?php echo $menuids; ?> admin-menu-content <?php echo $default_first_menu?>">
                        <?php $this->optionsPageContentItems($menu_items_content); ?>
                    </div>
                <?php
                }
            }
        } else {
            return 'no-options';
        }
    }
    
    function optionsPageContentItems($content = array())
    {
        $valid_form_elements = array('text', 'textarea', 'checkbox', 'checkboxes', 'radio', 'select', 'hidden', 'content', 'callback', 'colorpicker', 'colorpicker2', 'imageupload', 'fileupload', 'raw');       
        ?>
            <?php
                foreach($content as $itemvals) {
                    $itemval = $itemvals['content'];
                    if (in_array($itemval['type'], $valid_form_elements)) {
                        $form_item = 'form_' . $itemval['type'];
                        $item_display = isset($itemval['display']) ? $itemval['display'] : false;
                        $item_help = isset($itemval['help']) ? $itemval['help'] : false;
                        if($item_display == 'clean' || $itemval['type'] == 'hidden' || $itemval['type'] == 'raw') {
                            echo $this->$form_item($itemval['name'], $itemval);
                        } else {
                        ?>
                                <div class="admin_tab_element">
                                    <?php if($item_display == 'inline') {
                                        ?>
                                            <table width="100%">
                                                <tr>
                                                    <td class="admin-inline-label" valign="top"><?php echo $itemval['title']; ?>:</td>
                                                    <td class="admin-inline-content" valign="top"><?php echo $this->$form_item($itemval['name'], $itemval); if($item_help) {?><div class="admin-inline-help"><?php echo $item_help; ?></div><?php } ?></td>
                                                </tr>
                                            </table>
                                        <?php
                                    } elseif($item_display == 'block') {
                                        ?>
                                            <div class="admin-form-label"><?php echo $itemval['title'];?></div>
                                            <table width="100%">
                                                <tr>
                                                    <td class="admin-inline-content2" valign="top"><?php echo $this->$form_item($itemval['name'], $itemval); ?></td>
                                                    <td class="admin-inline-label2" valign="top"><?php if($item_help) {?><div class="admin-inline-help2"><?php echo $item_help; ?></div><?php } ?></td>
                                                </tr>
                                            </table>
                                        <?php
                                    } elseif($item_display == 'extended') { ?>
                                        <div class="admin-form-label"><?php echo $itemval['title']; ?></div>
                                        <?php echo $this->$form_item($itemval['name'], $itemval); 
                                        if($item_help) {?><div class="admin-extended-help"><?php echo $item_help; ?></div><?php }
                                        
                                    } elseif($item_display == 'extended-top') { ?>
                                        <div class="admin-form-label"><?php echo $itemval['title']; ?></div>
                                        <?php if($item_help) {?><div class="admin-extended-top-help"><?php echo $item_help; ?></div><?php } ?>
                                        <?php echo $this->$form_item($itemval['name'], $itemval);
                                        
                                    } else {
                                        if($item_help) { ?>
                                                <a href="javascript:admincore_showHide('<?php echo $itemval['name'] . '_help'; ?>');"><img src="<?php echo ADMINCORE_ADMIN_URL;?>/images/help.gif" class="admin-help" title="Click for Help" /></a>
                                        <?php }?>
                                        <div class="admin-form-label"><?php echo $itemval['title']; if($item_help) { ?>
                                                <div class="admin-show-help" id="<?php echo $itemval['name'] . '_help'; ?>" ><?php echo $item_help; ?></div>
                                        <?php }?>
                                        </div>
                                        <?php echo $this->$form_item($itemval['name'], $itemval);
                                    }
                                    ?>
                                </div>
                            <?php
                        }
                    }
                }
            ?>
        <?php
    }
}
?>