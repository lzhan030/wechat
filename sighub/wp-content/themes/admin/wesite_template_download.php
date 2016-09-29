<?php
	require_once './wp-content/themes/admin/cgi-bin/zipfile_download.php';
	global $wpdb;
	// defile file path & file name	
	$wesite_amount = 17;
	$id = $_GET['id'] - 1;
	$filepath = './wp-content/themes/mobilepagewe7/template/'; 
	if($id == 0) {
		//if id =0, default template
		$filepath = $filepath . 'default/';
		$filename = 'default.zip';
	}
	if($id>0 && $id< $wesite_amount){
		$filepath = $filepath . 'style'. (strval($id)) . '/';
		$filename = 'style'. (strval($id)) . '.zip';
	}
	if($id>= $wesite_amount){
		$domain = parse_url(home_url());
		$d = $domain["host"];
		$filepath = $filepath . $d . (strval($id)) . '/';
		$filename = $d . (strval($id)) . '.zip';		
	}
	$file= $filepath . $filename;

	//zip the files
	zip_files($filepath, $file);
	//download the zip file and remove it
	download_zip($file);
?>