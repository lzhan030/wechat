<?php
//added by Harvey for add login hook 
function validateExpireDate( $user, $username, $password ) {
	if(is_wp_error($user)||$user===null)
		return $user;
	$startdate = get_user_meta($user->ID, "startdate", true);
	$enddate = get_user_meta($user->ID, "enddate", true);
	if($startdate==false||$enddate==false)
		return $user;
	if(strtotime($startdate)<= strtotime(date("Y-m-d")) && strtotime(date("Y-m-d"))<=strtotime($enddate))
		return $user;
    $wp_error = new WP_Error('error',"用户不在有效登录时间内!");
	return $wp_error;
}
add_filter( 'authenticate', 'validateExpireDate', 30, 3 );


function login_redirect_url( $redirect_to, $request, $user ) {
	//is there a user to check?
	global $user;
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for admins
		if ( in_array( 'administrator', $user->roles ) ) {
			// redirect them to the default place
			return home_url().'?admin';
		} else {
			return home_url();
		}
	} else {
		return $redirect_to;
	}
}

add_filter( 'login_redirect', 'login_redirect_url', 10, 3 );

function login_success($user_login, $user) {
    if(is_super_admin( $user->ID ))
		setcookie ( 'aname' , 'usermanage', 0,'/'); //登陆成功即设置cookie值

	if(!empty( $_POST['rememberme'] )||!isset( $_POST['log'] ))
		setcookie ( 'displayname' ,$user->user_login , time()+30*3600*24,'/'); 
	else
		setcookie ( 'displayname' ,'' , time()+30*3600*24,'/');
	
	unset($_SESSION['GWEID_matched_userid']);
	unset($_SESSION['exact_GWEID']);
	$_SESSION['GWEID'] = 0 ;
	setcookie ( 'cookiename' , 'pubmanageheader', 0,'/');
	setcookie ( 'cookieindexname' , 'first', 0,'/');
	//setcookie ( 'testcookie' , 'pubmanageheader', 0, '/', home_url());
}
add_action('wp_login', 'login_success', 10, 2);


function autoFillUserLogin(){
	if(empty($user_login)&&isset($_COOKIE['displayname'])&&!empty($_COOKIE['displayname'])){
		$user_login=$_COOKIE['displayname'];
	?>
		<script type="text/javascript" language="javascript">
		document.getElementById('user_login').value="<?php echo $user_login ?>";
		</script>
	<?php
	}
}
add_action( 'login_footer', 'autoFillUserLogin', 20);

function wechat_login_footer(){
	?>
	<script type="text/javascript" language="javascript">
		document.getElementById("backtoblog").remove();
		document.getElementById('captcha_code').removeAttribute("tabIndex");
	</script>
	<?php
}
add_action( 'login_footer', 'wechat_login_footer', 10);

function wechat_login_header(){
	?>
	<style type="text/css">
		.login h1 a {
		  background-image: none,url(wp-admin/images/wordpress-logo.png?ver=20131107);
		  background-size: 274px 63px;
		  width: 326px;
		  height: 67px;
		  margin-bottom: 15px;
		}
	</style>
	<?php
}
add_action( 'login_head', 'wechat_login_header', 10);