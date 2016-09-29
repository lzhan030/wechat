<?php
//added by Harvey for customer upload dir by user
global $current_user;

require_once (ABSPATH . WPINC . '/pluggable.php');
get_currentuserinfo();

if( isset($current_user->user_login)&& !empty($current_user->user_login)&& !isset($_GET['admin']) )
{
		add_filter( 'upload_dir', 'upload_dir_filter' );

		function upload_dir_filter( $upload ) {
			global $current_user;
			
			$upload_dir = $upload['basedir'] . '/'.$current_user->user_login;
			$upload_url = $upload['baseurl'] . '/'.$current_user->user_login;
			$subdir='';
			if ( get_option( 'uploads_use_yearmonth_folders' ) ) {
				// Generate the yearly and monthly dirs
				if ( !$time )
					$time = current_time( 'mysql' );
				$y = substr( $time, 0, 4 );
				$m = substr( $time, 5, 2 );
				$subdir = "/$y/$m";
			}

			wp_mkdir_p( $upload_dir );  //WordPress will check if the dir exists and can write to it.
			$upload['path'] = $upload_dir.$subdir;
			$upload['url']  = $upload_url.$subdir;

			return $upload;
			}
}
add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() ) {
 // add your extension to the array
 $existing_mimes['pem'] = 'application/x-x509-ca-cert';
 // add as many as you like
 // removing existing file types
 //unset( $existing_mimes['exe'] );
 // add as many as you like
 // and return the new full result
 return $existing_mimes;
}
?>