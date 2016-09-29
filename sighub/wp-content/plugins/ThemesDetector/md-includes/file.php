<?php

function file_unlink($file){
	if(empty($file))
		return;
	if(stripos($file, '://'))
		return;
	global $current_user,$wpdb; //TODO: admin delete 

	$id = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($usergroupid !=0 && $usergroupflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		//$id = (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
	$user = get_user_by('id',$id);

	$user_name = $user->user_login;
	if( substr(trim($file, "\\\/"), 0, strpos(trim($file, "\\\/"), '/')) != $user_name){
		if(!is_super_admin( $current_user->ID ))
			return;
		$id = $current_user->ID ;
		$user = get_user_by('id',$id);
		$user_name = $user->user_login;
		if(substr(trim($file, "\\\/"), 0, strpos(trim($file, "\\\/"), '/')) != $user_name)
			return 0;
	}
		
	$uploadpath = wp_upload_dir();

	$file = $uploadpath['basedir'].$file;
	if(!file_exists($file))
		return 1;
	$size = @filesize($file)/1048576;
	if(!$size)
		return 2;	
	$unlink_result=@unlink($file);

	if($unlink_result){
		$size=number_format($size,3,'.','');
		$user_space = $wpdb -> get_row("SELECT * FROM {$wpdb->prefix}wesite_space WHERE userid = {$id}",ARRAY_A);
		$size = min($size,$user_space['used_space']);
		if ( $size ) {
			$wpdb->query(
				"
				UPDATE {$wpdb->prefix}wesite_space 
				SET used_space = used_space-{$size}
				WHERE userid = {$id}
				"
			);	
		}
	}
	return $unlink_result;
}

function get_img_file_list($xml){

	$img_file_list = array();

	$sp='~<img [^\>]*\ ?/?>~';
	preg_match_all( $sp, $xml, $aPics );  
	$np = count($aPics[0]); 
	$SoImgAddress="/\<img.*?src\=\"(.*?)\"[^>]*>/i";  //正则表达式语句

	if ( $np > 0 ) {   
		for ( $i=0; $i < $np ; $i++ ) {  			
			$ImgUrl = $aPics[0][$i];
			preg_match($SoImgAddress,$ImgUrl,$imagesurl);
			if((stristr($imagesurl[1],"http")===false) && (stristr($imagesurl[1],'file://')===false)&&(stristr($imagesurl[1],'data:')===false)){
				$img_file_list[] = $imagesurl[1];
			}
		}
	}
	return $img_file_list;
}

function diff_img_file_text($old, $new){

	$old = get_img_file_list($old);
	$new = get_img_file_list($new);

	return array_diff($old, $new);
}

function file_unlink_from_xml($xml){
	$file_list = get_img_file_list($xml);
	if(is_array($file_list) && !empty($file_list))
		foreach ($file_list as $url) {
			$url = str_replace('wp-content/uploads', '', $url);
			$result = file_unlink($url);
		}
}

function file_unlink_from_xml_update($old, $new){
	$file_list = diff_img_file_text($old, $new);
	if(is_array($file_list) && !empty($file_list))
		foreach ($file_list as $url) {
			$url = str_replace('wp-content/uploads', '', $url);
			$result = file_unlink($url);
		}
}