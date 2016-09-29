<?php
/**
 * Created by PhpStorm.
 * User: shaoqinc
 * Date: 13-12-18
 * Time: 下午2:15
 */
require( dirname(dirname(__FILE__)) . '/wp-load.php' );
add_action( 'wp_head', 'wp_no_robots' );
if ( is_array( get_site_option( 'illegal_names' )) && isset( $_GET[ 'new' ] ) && in_array( $_GET[ 'new' ], get_site_option( 'illegal_names' ) ) == true ) {
    wp_redirect( network_home_url() );
    die();
}
if ( !is_multisite() ) {
    echo 'sorry, 没开启wp多站点，不能使用此功能 ！';
   die();
}
global $current_site;
// Fix for page title
$wp_query->is_404 = false;
/**
 * Prints styles for front-end Multisite signup pages
 *
 * @since MU
 */
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

<?php
/**
 * Setup the new user signup process
 *
 * @since MU
 *
 * @uses apply_filters() filter $filtered_results
 * @uses show_user_form() to display the user registration form
 * @param string $user_name The username
 * @param string $user_email The user's email
 * @param array $errors
 */
function registerPage($user_name = '', $user_email = '', $blogname='', $blog_title='', $errors = '') {
    global $current_site, $active_signup;
    if ( !is_wp_error($errors) )
        $errors = new WP_Error();
    $signup_user_defaults = array(
        'user_name'  => $user_name,
        'user_email' => $user_email,
        'errors'     => $errors,
    );
    $filtered_results = apply_filters( 'signup_user_init', $signup_user_defaults );
    $user_name = $filtered_results['user_name'];
    $user_email = $filtered_results['user_email'];
    $errors = $filtered_results['errors'];
    ?>
    <form name="registerform" id="registerform" action="<?php echo esc_url( site_url('login/register.php', 'login_post') ); ?>" method="post">
        <p>
            <label for="user_login"><?php _e('Username') ?><br/>
              <?php
              if ( $errmsg = $errors->get_error_message('user_name') ) {
                  echo '<p class="error">'.$errmsg.'</p>';
              }
              ?>
                <input type="text" name="user_name" id="user_name" class="input" value="<?php echo esc_attr(stripslashes($user_name)); ?>" size="20" tabindex="10" /></label>
        </p>
        <p>
            <label for="user_email"><?php _e('E-mail') ?><br />
             <?php if ( $errmsg = $errors->get_error_message('user_email') ) { ?>
               <p class="error"><?php echo $errmsg ?></p>
             <?php } ?>
                <input type="email" name="user_email" id="user_email" class="input" value="<?php echo esc_attr(stripslashes($user_email)); ?>" size="25" tabindex="20" /></label>
            <?php _e('We send your registration email to this address. (Double-check your email address before continuing.)') ?>
        </p><br>
        <p>
            <label for="blogname"><?php echo  __('Site Name:')?></label>
           <?php if ( $errmsg = $errors->get_error_message('blogname') ) {
                echo "<p class='error'>".$errmsg ."</p>";
         }?>
            <span class="prefix_address"><?php echo home_url(); ?></span><input name="blogname" type="text" id="blogname" value="<?php esc_attr($blogname);?>" maxlength="5" /><br />
            (<strong><?php $site = $current_site->domain . $current_site->path . __( 'sitename' ); echo sprintf( __('Your address will be %s.'), $site );?></strong>)<?php echo __( 'Must be at least 4 characters, letters and numbers only. It cannot be changed, so choose carefully!' ); ?>
        </p><br/>
        <p>
            <label for="blog_title"><?php _e('Site Title:');?></label>
            <?php if ( $errmsg = $errors->get_error_message('blog_title') ) {
                echo "<p class='error'>".$errmsg ."</p>";
            }?>
            <input name="blog_title" type="text" id="blog_title" value="<?php echo esc_attr($blog_title);?>" />
        </p>
        <?php do_action('register_form'); ?>
        <p id="reg_passmail"><?php _e('A password will be e-mailed to you.') ?></p>
        <br class="clear" />
        <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
        <input type="hidden" name="stage" value="validate-blog-signup" />
        <input id="signupblog" type="hidden" name="signup_for" value="blog" />
        <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Register'); ?>" tabindex="100" /></p>
    </form>
<?php
}
/**
 * Validate new site signup
 *
 * @since MU
 *
 * @uses wpmu_validate_user_signup() to retrieve an array of the new user data and errors
 * @uses wpmu_validate_blog_signup() to retrieve an array of the new site data and errors
 * @uses apply_filters() to make signup $meta filterable
 * @uses signup_user() to signup a new user
 * @uses signup_blog() to signup a the new user to a new site
 * @return bool True if the site signup was validated, false if error
 */
function validate_blog_signup() {
    // Re-validate user info.
    $result = wpmu_validate_user_signup($_POST['user_name'], $_POST['user_email']);
    extract($result);
    //var_dump($result);
    if ( $errors->get_error_code() ) {
        registerPage($user_name, $user_email,$_POST['blogname'],$_POST['blog_title'], $errors);
        return false;
    }

    $result = wpmu_validate_blog_signup($_POST['blogname'], $_POST['blog_title']);
    extract($result);

    if ( $errors->get_error_code() ) {
        registerPage($user_name, $user_email, $blogname, $blog_title, $errors);
        return false;
    }

    $public = (int) $_POST['blog_public'];
    $meta = array ('lang_id' => 1, 'public' => $public);

    /** This filter is documented in wp-signup.php */
    $meta = apply_filters( 'add_signup_meta', $meta );

    wpmu_signup_blog($domain, $path, $blog_title, $user_name, $user_email, $meta);
    confirm_blog_signup($domain, $path, $blog_title, $user_name, $user_email, $meta);
    return true;
}
/**
 * New site signup confirmation
 *
 * @since MU
 *
 * @param string $domain The domain URL
 * @param string $path The site root path
 * @param string $blog_title The new site title
 * @param string $user_name The user's username
 * @param string $user_email The user's email address
 * @param array $meta Any additional meta from the 'add_signup_meta' filter in validate_blog_signup()
 */
function confirm_blog_signup( $domain, $path, $blog_title, $user_name = '', $user_email = '', $meta = array() ) {
    ?>
    <h2><?php printf( __( 'Congratulations! Your new site, %s, is almost ready.' ), "<a href='http://{$domain}{$path}'>{$blog_title}</a>" ) ?></h2>

    <p><?php _e( 'But, before you can start using your site, <strong>you must activate it</strong>.' ) ?></p>
    <p><?php printf( __( 'Check your inbox at <strong>%s</strong> and click the link given.' ),  $user_email) ?></p>
    <p><?php _e( 'If you do not activate your site within two days, you will have to sign up again.' ); ?></p>
    <?php
    /** This action is documented in wp-signup.php */
    do_action( 'signup_finished' );
}

// Main
$active_signup = get_site_option( 'registration' );
if ( !$active_signup )
    $active_signup = 'all';
$active_signup = apply_filters( 'wpmu_active_signup', $active_signup ); // return "all", "none", "blog" or "user"

// Make the signup type translatable.
$i18n_signup['all'] = _x('all', 'Multisite active signup type');
$i18n_signup['none'] = _x('none', 'Multisite active signup type');
$i18n_signup['blog'] = _x('blog', 'Multisite active signup type');
$i18n_signup['user'] = _x('user', 'Multisite active signup type');

if ( is_super_admin() )
    echo '<div class="mu_alert">' . sprintf( __( 'Greetings Site Administrator! You are currently allowing &#8220;%s&#8221; registrations. To change or disable registration go to your <a href="%s">Options page</a>.' ), $i18n_signup[$active_signup], esc_url( network_admin_url( 'settings.php' ) ) ) . '</div>';

$newblogname = isset($_GET['new']) ? strtolower(preg_replace('/^-|-$|[^-a-zA-Z0-9]/', '', $_GET['new'])) : null;

$current_user = wp_get_current_user();
if ( $active_signup == 'none' ) {
    _e( 'Registration has been disabled.' );
} elseif ( $active_signup == 'blog' && !is_user_logged_in() ) {
    if ( is_ssl() )
        $proto = 'https://';
    else
        $proto = 'http://';
    $login_url = site_url( 'wp-login.php?redirect_to=' . urlencode($proto . $_SERVER['HTTP_HOST'] . '/wp-signup.php' ));
    echo sprintf( __( 'You must first <a href="%s">log in</a>, and then you can create a new site.' ), $login_url );
} else {
    $stage = isset( $_POST['stage'] ) ?  $_POST['stage'] : 'default';
    switch ( $stage ) {
        case 'validate-user-signup' :
            if ( $active_signup == 'all' || $_POST[ 'signup_for' ] == 'blog' && $active_signup == 'blog' || $_POST[ 'signup_for' ] == 'user' && $active_signup == 'user' )
                validate_user_signup();
            else
                _e( 'User registration has been disabled.' );
            break;
        case 'validate-blog-signup':
            if ( $active_signup == 'all' || $active_signup == 'blog' )
                validate_blog_signup();
            else
                _e( 'Site registration has been disabled.' );
            break;
        case 'gimmeanotherblog':
            validate_another_blog_signup();
            break;
        case 'default':
        default :
            $user_email = isset( $_POST[ 'user_email' ] ) ? $_POST[ 'user_email' ] : '';
            do_action( 'preprocess_signup_form' ); // populate the form from invites, elsewhere?
            if ( is_user_logged_in() && ( $active_signup == 'all' || $active_signup == 'blog' ) )
                echo '您已经登录，不能再注册了';//signup_another_blog($newblogname);
            elseif ( is_user_logged_in() == false && ( $active_signup == 'all' || $active_signup == 'user' ) )
                registerPage();
            elseif ( is_user_logged_in() == false && ( $active_signup == 'blog' ) )
                _e( 'Sorry, new registrations are not allowed at this time.' );
            else
                _e( 'You are logged in already. No need to register again!' );
            if ( $newblogname ) {
                $newblog = get_blogaddress_by_name( $newblogname );

                if ( $active_signup == 'blog' || $active_signup == 'all' )
                    printf( __( '<p><em>The site you were looking for, <strong>%s</strong> does not exist, but you can create it now!</em></p>' ), $newblog );
                else
                    printf( __( '<p><em>The site you were looking for, <strong>%s</strong>, does not exist.</em></p>' ), $newblog );
            }
            break;
    }
}
?>
</div>
   <!-- </div>-->
<?php do_action( 'after_signup_form' );

