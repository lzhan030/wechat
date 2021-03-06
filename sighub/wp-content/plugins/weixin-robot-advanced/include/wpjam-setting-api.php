<?php
/** WPJAM OPTIONS */

function wpjam_option_page($labels, $title='', $type='default', $icon='options-general'){
	extract($labels);
	?>
	<div class="wrap">
	<?php settings_errors();?>
	<?php if($icon){?>
		<div id="icon-<?php echo $icon;?>" class="icon32"><br></div>
	<?php } ?>
	<?php if($type == 'tab'){ ?>
		<h2 class="nav-tab-wrapper">
	        <?php foreach ( $sections as $section_name => $section) { ?>
	            <a class="nav-tab" href='#' id="tab-title-<?php echo $section_name; ?>"><?php echo $section['title']; ?></a>
	        <?php } ?>    
	    </h2>
		<form action="options.php" method="POST">
			<?php settings_fields( $option_group ); ?>
			<?php foreach ( $sections as $section_name => $section ) { ?>
	            <div id="tab-<?php echo $section_name; ?>" class="div-tab hidden">
	                <?php wpjam_do_settings_section($option_page, $section_name); ?>
	            </div>                      
	        <?php } ?>
			<?php submit_button(); ?>
		</form>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				//设置第一个显示
				jQuery('a.nav-tab').first().addClass('nav-tab-active');
				jQuery('div.div-tab').first().show();

				jQuery('a.nav-tab').on('click',function(){
			        jQuery('a.nav-tab').removeClass('nav-tab-active');
			        jQuery(this).addClass('nav-tab-active');
			        jQuery('div.div-tab').hide();
			        jQuery('#'+jQuery(this)[0].id.replace('title-','')).show();
			    });
			});
		</script>
	<?php }else{ ?>
		<?php if($title){?>
		<h2><?php echo $title; ?></h2>
		<?php }?>
		<form action="options.php" method="POST">
			<?php settings_fields( $option_group ); ?>
			<?php do_settings_sections( $option_page ); ?>
			<?php submit_button(); ?>
		</form>
	<?php } ?>
	</div>
	<?php
}

function wpjam_add_settings($labels,$defaults){
	extract($labels);
	register_setting( $option_group, $option_name, $field_validate );
	$field_callback = 'wpjam_field_callback';
	foreach ($sections as $section_name => $section) {
		add_settings_section( $section_name, $section['title'], $section['callback'], $option_page );
		foreach ($section['fileds'] as $field_name=>$field) {
			$field['option']	= $option_name;
			$field['field']		= $field_name;

			$field['default'] 	= isset($defaults[$field_name])?$defaults[$field_name]:'';
			add_settings_field( 
				$field_name,
				$field['title'],		
				$field_callback,	
				$option_page, 
				$section_name,	
				$field
			);	
		}
	}
}

function wpjam_field_callback($args) {

	$option_name	= $args['option'];
	$field_name		= $args['field'];

	$wpjam_option	= get_option( $option_name );

	$value			= (isset($wpjam_option[$field_name]))?$wpjam_option[$field_name]:$args['default'];
	$filed 			= $option_name.'['.$field_name.']';
	$type			= $args['type'];
	$description	= (isset($args['description']))?($type == 'checkbox')?' '.$args['description']:'<br />'.$args['description']:'';

	if($type == 'text'){
		echo '<input type="text" name="'.$filed.'" value="'.$value.'" class="regular-text" />';
	}elseif($type == 'textarea'){
		echo '<textarea name="'.$filed.'" rows="4" cols="50" class="regular-text code">'.$value.'</textarea>';
	}elseif($type == 'checkbox'){
		$checked = $value?'checked':'';
		echo '<input type="checkbox" name="'.$filed.'" value="1" '.$checked.' />';
	}
	echo $description;
}

// 拷贝自 do_settings_sections 函数，用于 tab 显示选项。
function wpjam_do_settings_section($option_page, $section_name){
	global $wp_settings_sections, $wp_settings_fields;

	if ( ! isset( $wp_settings_sections[$option_page] ) )
		return;

	$section = $wp_settings_sections[$option_page][$section_name];

	if ( $section['title'] )
		echo "<h3>{$section['title']}</h3>\n";

	if ( $section['callback'] )
		call_user_func( $section['callback'], $section );

	if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$option_page] ) || !isset( $wp_settings_fields[$option_page][$section['id']] ) )
		continue;
	echo '<table class="form-table">';
	do_settings_fields( $option_page, $section['id'] );
	echo '</table>';
}


function wpjam_get_setting($option, $setting_name){
	if(isset($option[$setting_name])){
		return str_replace("\r\n", "\n", $option[$setting_name]);
	}else{
		return '';
	}
}

function wpjam_get_option($option_name,$defaults){
	$option = get_option( $option_name );
	return wp_parse_args($option, $defaults);
}