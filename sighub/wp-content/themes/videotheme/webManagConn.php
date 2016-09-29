
<?php

	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');

    $con=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
    if(!$con) {
        $_SESSION['connMeg']  = "Can't connect datacase!";
    }else{
        $_SESSION['connMeg']  = "Success connect!";
    }
    mysql_select_db(SAE_MYSQL_DB);
 
?>
