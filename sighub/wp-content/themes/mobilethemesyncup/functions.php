<?php
function mtheme_thumb($postid=0, $size='thumbnail', $attributes='') {
	if ($postid<1) $postid = get_the_ID();
	if ($images = get_children(array(
		'post_parent' => $postid,
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image', )))
		foreach($images as $image) {
			$thumbnail=wp_get_attachment_image_src($image->ID, $size);
			?>
<img src="<?php echo $thumbnail[0]; ?>" <?php echo $attributes; ?> />
<?php
		}
	else {
		echo '<img src=' . get_bloginfo ( 'stylesheet_directory' );
		echo '/images/no-attachment.gif>';
	}
	
}

function mtheme_thumb_v2($postid=0) {
	$upload =wp_upload_dir();
	$baseurl=$upload['baseurl'];
	if ($postid<1) $postid = get_the_ID();
	$content = get_post_field('post_content', $postid);
	
	$imgTag=strstr($content, "<img");
	if($imgTag != null) {
		$imgTag=strstr($imgTag, "src=");
	}
	
	if($imgTag == null) {
	}
	else {
		$arr = explode("\"", $imgTag);
		if(count($arr) > 3) {
			
			if(stristr($arr[1],"http")===false){
				$post_picurl=$baseurl.$arr[1];
				$imgarr=str_ireplace($arr[1],$post_picurl,$arr[1]);
			}else{
				$imgarr=$arr[1];
			}
			echo '<img src=' . $imgarr.'>';
		}
	}
}

if ( function_exists('register_sidebars') )
    register_sidebars(1);

$themename = "Mobile Theme";
$shortname = "mob";
$options = array (

array(
"name" => "Mobile Theme Options",
"type" => "title"),

array(
"type" => "open"),

array(
"name" => "Theme Color",
"desc" => "Choose the base color for your mobile theme!",
"id" => $shortname."_color",
"std" => "C00000",
"type" => "colorjs"),

array(
"name" => "Theme Width",
"desc" => "Choose a width for the theme. Enter 100% for full width or define the width in pixels like 320px",
"id" => $shortname."_width",
"std" => "360px",
"type" => "text"),

array(
"type" => "close")

);

/*Add a Theme Options Page*/
function mytheme_add_admin() {

    global $themename, $shortname, $options;

    if ( $_GET['page'] == basename(__FILE__) ) {

        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                header("Location: themes.php?page=functions.php&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($options as $value) {
                delete_option( $value['id'] ); }

            header("Location: themes.php?page=functions.php&reset=true");
            die;

        }
    }

    add_theme_page($themename." Options", "".$themename." Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');

}

/*Adding scripts*/
function admin_js(){
	if ( $_GET['page'] == basename(__FILE__) ) {
?>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory') ?>/js/jscolor.js"></script>
<?php }
}

function mytheme_admin() {

    global $themename, $shortname, $options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';

?>
<div class="wrap" style="margin:0 auto; padding:20px 0px 0px;">

<form method="post">

<?php foreach ($options as $value) {
switch ( $value['type'] ) {

case "open":
?>
<div style="width:808px; background:#eee; border:1px solid #ddd; padding:20px; overflow:hidden; display: block; margin: 0px 0px 30px;">

<?php break;

case "close":
?>

</div>

<?php break;

case "misc":
?>
<div style="width:808px; background:#fffde2; border:1px solid #ddd; padding:20px; overflow:hidden; display: block; margin: 0px 0px 30px;">
	<?php echo $value['name']; ?>
</div>
<?php break;

case "title":
?>

<div style="width:810px; height:22px; background:#555; padding:9px 20px; overflow:hidden; margin:0px; font-family:Verdana, sans-serif; font-size:18px; font-weight:normal; color:#EEE;">
	<?php echo $value['name']; ?>
</div>

<?php break;

case 'text':
?>

<div style="width:808px; padding:0px 0px 10px; margin:0px 0px 10px; border-bottom:1px solid #ddd; overflow:hidden;">
	<span style="font-family:Arial, sans-serif; font-size:16px; font-weight:bold; color:#444; display:block; padding:5px 0px;">
		<?php echo $value['name']; ?>
	</span>
	<?php if ($value['image'] != "") {?>
		<div style="width:808px; padding:10px 0px; overflow:hidden;">
			<img style="padding:5px; background:#FFF; border:1px solid #ddd;" src="<?php bloginfo('template_url');?>/images/<?php echo $value['image'];?>" alt="image" />
		</div>
	<?php } ?>
	<input style="width:200px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] )); } else { echo stripslashes($value['std']); } ?>" />
	<br/>
	<span style="font-family:Arial, sans-serif; font-size:11px; font-weight:bold; color:#444; display:block; padding:5px 0px;">
		<?php echo $value['desc']; ?>
	</span>
</div>

<?php break;

case 'colorjs':
?>

<div style="width:808px; padding:0px 0px 10px; margin:0px 0px 10px; border-bottom:1px solid #ddd; overflow:hidden;">
	<span style="font-family:Arial, sans-serif; font-size:16px; font-weight:bold; color:#444; display:block; padding:5px 0px;"><?php echo $value['name']; ?></span>

	<input style="width:200px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] )); } else { echo stripslashes($value['std']); } ?>" class="color" />
	<br/>
    
	<span style="font-family:Arial, sans-serif; font-size:11px; font-weight:bold; color:#444; display:block; padding:5px 0px;">
		<?php echo $value['desc']; ?>
	</span>
</div>

<?php
break;

case 'textarea':
?>

<div style="width:808px; padding:0px 0px 10px; margin:0px 0px 10px; border-bottom:1px solid #ddd; overflow:hidden;">
	<span style="font-family:Arial, sans-serif; font-size:16px; font-weight:bold; color:#444; display:block; padding:5px 0px;">
		<?php echo $value['name']; ?>
	</span>
	<?php if ($value['image'] != "") {?>
		<div style="width:808px; padding:10px 0px; overflow:hidden;">
			<img style="padding:5px; background:#FFF; border:1px solid #ddd;" src="<?php bloginfo('template_url');?>/images/<?php echo $value['image'];?>" alt="image" />
		</div>
	<?php } ?>
	<textarea name="<?php echo $value['id']; ?>" style="width:400px; height:200px;" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] )); } else { echo stripslashes($value['std']); } ?></textarea>
	<br/>
	<span style="font-family:Arial, sans-serif; font-size:11px; font-weight:bold; color:#444; display:block; padding:5px 0px;">
		<?php echo $value['desc']; ?>
	</span>
</div>

<?php
break;
/*Ralph Damiano*/
case 'select':
?>

<div style="width:808px; padding:0px 0px 10px; margin:0px 0px 10px; border-bottom:1px solid #ddd; overflow:hidden;">
	<span style="font-family:Arial, sans-serif; font-size:16px; font-weight:bold; color:#444; display:block; padding:5px 0px;">
		<?php echo $value['name']; ?>
	</span>
	<?php if ($value['image'] != "") {?>
		<div style="width:808px; padding:10px 0px; overflow:hidden;">
			<img style="padding:5px; background:#FFF; border:1px solid #ddd;" src="<?php bloginfo('template_url');?>/images/<?php echo $value['image'];?>" alt="image" />
		</div>
	<?php } ?>
	<select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $option) { ?><option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?></select>
	<br/>
	<span style="font-family:Arial, sans-serif; font-size:11px; font-weight:bold; color:#444; display:block; padding:5px 0px;">
		<?php echo $value['desc']; ?>
	</span>
</div>

<?php
break;

case "checkbox":
?>

<div style="width:808px; padding:0px 0px 10px; margin:0px 0px 10px; border-bottom:1px solid #ddd; overflow:hidden;">
	<span style="font-family:Arial, sans-serif; font-size:16px; font-weight:bold; color:#444; display:block; padding:5px 0px;">
		<?php echo $value['name']; ?>
	</span>
	<?php if ($value['image'] != "") {?>
		<div style="width:808px; padding:10px 0px; overflow:hidden;">
			<img style="padding:5px; background:#FFF; border:1px solid #ddd;" src="<?php bloginfo('template_url');?>/images/<?php echo $value['image'];?>" alt="image" />
		</div>
	<?php } ?>
	<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
	<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
	<br/>
	<span style="font-family:Arial, sans-serif; font-size:11px; font-weight:bold; color:#444; display:block; padding:5px 0px;">
		<?php echo $value['desc']; ?>
	</span>
</div>


<?php
break;

case "submit":
?>

<p class="submit">
<input name="save" type="submit" value="Save changes" />
<input type="hidden" name="action" value="save" />
</p>

<?php break;
}
}
?>

<p class="submit">
<input name="save" type="submit" value="Save changes" />
<input type="hidden" name="action" value="save" />
</p>
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>

<?php
}
function mytheme_wp_head() { ?>
<link href="<?php bloginfo('template_directory'); ?>/style.php" rel="stylesheet" type="text/css" />
<?php }
add_action('wp_head', 'mytheme_wp_head');
add_action('admin_head', 'admin_js');
add_action('admin_menu', 'mytheme_add_admin'); ?>