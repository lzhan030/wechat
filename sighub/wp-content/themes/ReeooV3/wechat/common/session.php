<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
/* session_start();
$request_path = explode ( 'wp-content', $_SERVER["REQUEST_URI"] );
$home_url = $request_path[0];
$home_url = explode ( '?', $home_url );
$home_url = $home_url[0]; */

$loginurl = wp_login_url();

//20140623 janeen update
//if( !isset($_SESSION['WEID']) ){
if( !isset($_SESSION['GWEID']) ){
	echo <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script>
		top.location.href="{$loginurl}";
	</script>
	</head>
<body>
</body>
</html>
EOT;

exit;
}
