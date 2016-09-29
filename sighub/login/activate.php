<?php
/**
 * Confirms that the activation key that is sent in an email after a user signs
 * up for a new blog matches the key for that user and then displays confirmation.
 *
 * @package WordPress
 */

define( 'WP_INSTALLING', true );

/** Sets up the WordPress Environment. */
require( dirname(dirname(__FILE__)) . '/wp-load.php' );

require( dirname(dirname( __FILE__ )) . '/wp-blog-header.php' );

if ( !is_multisite() ) {
	wp_redirect( site_url( '/login/?action=register' ) );
	die();
}

if ( is_object( $wp_object_cache ) )
	$wp_object_cache->cache_enabled = false;

// Fix for page title
$wp_query->is_404 = false;

/**
 * Fires before the Site Activation page is loaded.
 *
 * @since 3.0
 */
do_action( 'activate_header' );

/**
 * Adds an action hook specific to this page that fires on wp_head
 *
 * @since MU
 */
function do_activate_header() {
    /**
     * Fires before the Site Activation page is loaded, but on the wp_head action.
     *
     * @since 3.0
     */
    do_action( 'activate_wp_head' );
}
add_action( 'wp_head', 'do_activate_header' );

/**
 * Loads styles specific to this page.
 *
 * @since MU
 */
function wpmu_activate_stylesheet() {
	?>
	<style type="text/css">
		form { margin-top: 2em; }
		#submit, #key { width: 90%; font-size: 24px; }
		#language { margin-top: .5em; }
		.error { background: #f66; }
		span.h3 { padding: 0 8px; font-size: 1.3em; font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif; font-weight: bold; color: #333; }
	</style>
	<?php
}
add_action( 'wp_head', 'wpmu_activate_stylesheet' );

//get_header();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <title><?php bloginfo('name'); ?> &rsaquo; <?php echo $title; ?></title>
    <?php

    wp_admin_css( 'wp-admin', true );
    wp_admin_css( 'colors-fresh', true );

    if ( wp_is_mobile() ) { ?>
        <meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" /><?php
    } ?>
</head>
<body class="login<?php if ( wp_is_mobile() ) echo ' mobile'; ?>">
<div id="login" style="width: 35%">
    <h1><?php bloginfo( 'name' ); ?></h1>

    <div id="content" class="widecolumn">
	<?php if ( empty($_GET['key']) && empty($_POST['key']) ) { ?>

		<h2><?php _e('Activation Key Required') ?></h2>
		<form name="activateform" id="activateform" method="post" action="<?php echo network_site_url('wp-activate.php'); ?>">
			<p>
			    <label for="key"><?php _e('Activation Key:') ?></label>
			    <br /><input type="text" name="key" id="key" value="" size="50" />
			</p>
			<p class="submit">
			    <input id="submit" type="submit" name="Submit" class="submit" value="<?php esc_attr_e('Activate') ?>" />
			</p>
		</form>

	<?php } else {

		$key = !empty($_GET['key']) ? $_GET['key'] : $_POST['key'];
		$result = wpmu_activate_signup($key);
		if ( is_wp_error($result) ) {
			if ( 'already_active' == $result->get_error_code() || 'blog_taken' == $result->get_error_code() ) {
			    $signup = $result->get_error_data();
				?>
				<h2><?php _e('链接已过时！'); ?></h2>
				<?php
			} else {
				?>
				<h2><?php _e('An error occurred during the activation'); ?></h2>
				<?php
			    echo '<p>'.$result->get_error_message().'</p>';
			}
		} else {
			extract($result);
			$url = get_blogaddress_by_id( (int) $blog_id);
			$user = get_userdata( (int) $user_id);
			?>
			<h2><?php _e('Your account is now active!'); ?></h2>

			<div id="signup-welcome">
				<p><span class="h3"><?php _e('Username:'); ?></span> <?php echo $user->user_login ?></p>
				<p><span class="h3"><?php _e('Password:'); ?></span> <?php echo $password; ?></p>
			</div>
            <p>若需要，请登录修改您的密码！</p>
			<?php if ( $url != network_home_url('', 'http') ) : ?>
				<p class="view"><?php printf( __('Your account is now activated. <a href="%1$s">View your site</a> or <a href="%2$s">Log in</a>'), $url, $url . 'wp-login.php' ); ?></p>
			<?php else: ?>
				<p class="view"><?php printf( __('Your account is now activated. <a href="%1$s">Log in</a> or go back to the <a href="%2$s">homepage</a>.' ), network_site_url('wp-login.php', 'login'), network_home_url() ); ?></p>
			<?php endif;
		}
	}
	?>
</div>
<script type="text/javascript">
	var key_input = document.getElementById('key');
	key_input && key_input.focus();
</script>
</div>
</body>
</html>
