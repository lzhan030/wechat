<?php
	function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime()), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	if($numeric) {
		$hash = '';
	} else {
		$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		$length--;
	}
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}
	
	// function rad() {
	// $seriver = date('ymdHis');
    // $sjs = rand(1,9);
    // $rad=$seriver.$sjs;
	// return $rad;
// }

	//产生随机weid
function weid( $length = 7 ) { 
 // 密码字符集，可任意添加你需要的字符 
 $chars = '0123456789';  
 $password = $chars[ mt_rand(1, strlen($chars) - 1) ];  
 for ( $i = 0; $i < $length-1; $i++ )  
 {  
 // 这里提供两种字符获取方式  
 // 第一种是使用 substr 截取$chars中的任意一位字符；  
 // 第二种是取字符数组 $chars 的任意元素  
 // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);  
    $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];  }  
    return $password; 
}

	//产生10位随机gweid
function gweid( $length = 10 ) { 
 // 密码字符集，可任意添加你需要的字符 
 $chars = '0123456789';  
 $password = $chars[ mt_rand(1, strlen($chars) - 1) ];  
 for ( $i = 0; $i < $length-1; $i++ )  
 {  
    $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];  }  
    return $password; 
}

?>
