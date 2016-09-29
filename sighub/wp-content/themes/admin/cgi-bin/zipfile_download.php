<?php
	function zip_files($filepath, $file){
		$zip = new ZipArchive();
		if($zip->open($file, ZIPARCHIVE::CREATE) != TRUE) {
			exit("can not open zip file");
		}
		$files = new RecursiveIteratorIterator (new RecursiveDirectoryIterator($filepath), RecursiveIteratorIterator::SELF_FIRST);
		foreach ($files as $f) {
			$f = str_replace('\\', '/', $f);
			if( in_array(substr($f, strrpos($f, '/')+1), array('.', '..')) )
	            continue;

	        $start = strlen($filepath);
	        if(is_dir($f) === true) {
	        	$folder = substr($f, $start);
	        	$zip->addEmptyDir($folder);
	        } else if(is_file($f) === true) {
		        $f = substr($f, $start);
				$rlt = $zip->addFile($filepath.$f, $f);      
	        }

		}
		$zip->close();
	}

	function download_zip($file){
	   	header("Content-Description: File Transfer"); 	
	    header("Content-Type: application/zip");
	    header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($file));
		header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
		ob_clean();
		flush();
		readfile($file);
		unlink($file);
		exit;		
	}

	function upload_upzip($filename, $source, $type, $wesit_new_url){
		$filetype = explode(".", $filename);
		$continue = strtolower($filetype[1]) == 'zip' ? true:false;
		if(!$continue) {
			return false;
	    }

	    if(!file_exists($wesit_new_url)) {
	    	mkdir($wesit_new_url, 0775, true);
	    }
	    $target_path = $wesit_new_url . $filename;
	    if(move_uploaded_file($source, $target_path)) {
	    	$zip = new ZipArchive;
	    	$x = $zip->open($target_path);
	    	if($x === true) {
	    		$zip->extractTo($wesit_new_url);
	    		$zip->close();
	    		unlink($target_path);
				return true;
	    	} else {
	    		return false;
	    	}
	    } else {
	    	return false;
	    }	

	}

	function DeleteDir($dir){
		if(is_dir($dir)) {
			$files = scandir($dir);
			foreach ($files as $file) {
				if('.' === $file || '..' === $file) continue;
				if(is_dir("$dir/$file")) 
					DeleteDir("$dir/$file");
				else 
					unlink("$dir/$file");
			}
			reset($files);
			rmdir($dir);
		}
	}	
?>
