<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

$variable_blacklist = array('tmp_path','template_path','path','wp_root_path','_COOKIE','_GET','_POST','_ENV','_FILES','_SERVER','_SESSION','wpdb');
$funtion_whitelist = array('while','if','switch','date','time','addcslashes','addslashes','bin2hex','chop','chr','chunk_split','convert_cyr_string','convert_uudecode','convert_uuencode','count_chars','crc32','crypt','echo','explode','fprintf','get_html_translation_table','hebrev','hebrevc','hex2bin','html_entity_decode','htmlentities','htmlspecialchars_decode','htmlspecialchars','implode','join','lcfirst','levenshtein','localeconv','ltrim','md5_file','md5','metaphone','money_format','nl_langinfo','nl2br','number_format','ord','parse_str','print','printf','quoted_printable_decode','quoted_printable_encode','quotemeta','rtrim','setlocale','sha1_file','sha1','similar_text','soundex','sprintf','sscanf','str_getcsv','str_ireplace','str_pad','str_repeat','str_replace','str_rot13','str_shuffle','str_split','str_word_count','strcasecmp','strchr','strcmp','strcoll','strcspn','strip_tags','stripcslashes','stripos','stripslashes','stristr','strlen','strnatcasecmp','strnatcmp','strncasecmp','strncmp','strpbrk','strpos','strrchr','strrev','strripos','strrpos','strspn','strstr','strtok','strtolower','strtoupper','strtr','substr_compare','substr_count','substr_replace','substr','trim','ucfirst','ucwords','vfprintf','vprintf','vsprintf','wordwrap','rand','abs','base_convert','bindec','ceil','decbin','dechex','decoct','exp','floor','fmod','hexdec','log10','log1p','log','max','min','mt_getrandmax','mt_rand','mt_srand','octdec','pi','pow','rand','round','sin','sinh','sqrt','boolval','doubleval','empty','floatval','get_defined_vars','get_resource_type','gettype','intval','is_array','is_bool','is_callable','is_double','is_float','is_int','is_integer','is_long','is_null','is_numeric','is_object','is_real','is_resource','is_scalar','is_string','isset','serialize','settype','strval','unserialize','unset','var_dump','var_export');
$keyword_blacklist = array('global','exit','define');

/*
$code = <<<'EOT'

$i = 333;
if($i>5)
	echo $i;
echo concat($i.$abcdsokp.$234sss);
EOT;
*/
$code = $_POST['code'];
$variables_count = preg_match_all("/(?<=[\$])\w+(?=[\W])/",$code,$variables);
$functions_count = preg_match_all("/(?<=[\W])\w+(?=[(])/",$code,$functions);

$variables = array_unique($variables[0]);
$functions = array_unique($functions[0]);

$variable_blacklist_test = array_intersect($variables, $variable_blacklist);
$function_whitelist_test = array_diff($functions, $funtion_whitelist);

$variable_test = empty($variable_blacklist_test);
$function_test = empty($function_whitelist_test);

/*
var_dump($variables);
var_dump($functions);

var_dump($variables_test);
var_dump($functions_test);

var_dump($variable_test);
var_dump($function_test);
*/

$result = array();
if( $variable_test && $function_test){
	$result['status'] = 'success';
	$result['message'] = '';
}else{
	$result['status'] = 'error';
	$result['message'] = '';
	if(!$variable_test)
		$result['message'] .= '使用了非法变量';
	if(!$function_test)
		$result['message'] .= '使用了非法函数或方法';
	}
echo json_encode($result);

	
	
	
	