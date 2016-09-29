<?php
if(!function_exists('md_install')){
	/**
	 * Initialize the plugin.
	 */
	function md_install()
	{
		global $table_prefix, $wpdb;
		$table_mobilemeta =$table_prefix.'md_mobilemeta';
		$sql2 = "CREATE TABLE IF NOT EXISTS `$table_mobilemeta` (
		  `mobile_id` int(11) NOT NULL AUTO_INCREMENT,
		  `subLink` varchar(255) NOT NULL,
		  `theme_template` varchar(255) NOT NULL,
		  PRIMARY KEY  (mobile_id)
		);";
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql2);
        $tableName =  $table_prefix.'orangesitemeta';
        $sql2 = "CREATE TABLE IF NOT EXISTS `$tableName` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `site_id` int(11) NOT NULL ,
		  `site_key` varchar(255) NOT NULL,
		  `site_value` varchar(255) NOT NULL,
		  PRIMARY KEY  (id)
		);";
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($sql2);
	}
}

if(!function_exists('md_uninstall')) {
	/**
	 * Uninstallation of plugin.
	 */
	function md_uninstall()
	{
		global $table_prefix, $wpdb;
		$table_mobilemeta = $table_prefix."md_mobilemeta";
		$sql2 = "DROP TABLE `$table_mobilemeta`";
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql2);
	}
}

if(!function_exists('md_pluginversion')) {
	/**
	 * The plugin version.
	 * @return string Plugin version
	 */
	function md_pluginversion(){
		$md_plugin_data = implode('', file(dirname(dirname(__FILE__)).'/ThemesDetector.php'));
		if (preg_match("|Version:(.*)|i", $md_plugin_data, $version)) {
				$version = $version[1];
		}
		return $version;
	}
}

if(!function_exists('md_pluginname')) {
	/**
	 * The plugin name.
	 * @return string Plugin name
	 */
	function md_pluginname()
	{
		$md_plugin_data = implode('', file(dirname(dirname(__FILE__)).'/ThemesDetector.php'));
		if (preg_match("|Plugin\sName:(.*)|i", $md_plugin_data, $pluginname)) {
				$pluginname = $pluginname[1];
		}
		return $pluginname;
	}
}

if(!function_exists('get_mobile_themes')) {
	/**
	 * Retrieve list of mobile themes with theme data in theme directory.
	 * @global array $wptap_mobile_themes Stores the working mobile themes.
	 * @return array Mobile Theme list with theme data.
	 */
	 function get_mobile_themes()
	 {
		if(!function_exists('get_themes'))
			return null;
		return $wp_themes = get_themes();
	 }
}

/**
 * Detect user agent.
 */
function mobileDetect($defaultTheme)
{
	global $wpdb,$table_prefix;
   $siteId ='';
    if((isset($_GET['page_id']) && !empty($_GET['page_id']))||(isset($_GET['p']) && !empty($_GET['p'])))
    {
        //通过页面的ID到数据库里拿出siteId
        $table = $table_prefix.'posts';
        $ID = isset($_GET['page_id'])?$_GET['page_id']:$_GET['p'];
        $sql = "select post_content_filtered from ".$table." where ID ='".$ID."'";
        $row = $wpdb->get_row($sql);
        $siteId = $row->post_content_filtered;
        //var_dump($sql);
    }
    elseif(isset($_GET['site']) && !empty($_GET['site']))
    {
        $siteId = $_GET['site'];
    }
    elseif(isset($_GET['admin']) )
    {
		return 'admin';
	}
    else
    {
        //当GET参数中不包含上述参数时，返回默认的主题
        return $defaultTheme;
    }
    //将siteId存放到session中
    session_start();
    $_SESSION['orangeSite']=$siteId;
	if(isset($_GET['openid']) && !empty($_GET['openid'])) {
		$_SESSION['openid']=$_GET['openid'];
	}
	//取出并返回主题名称
    $tableName = $table_prefix.'orangesite';
    $sql = "SELECT `themes_key` FROM `".$tableName."` WHERE `id`='".$siteId."'";
    $sitemeta = $wpdb->get_row($sql);
    $themeId = $sitemeta->themes_key;
	$mobilemeta = $wpdb->get_row("SELECT `theme_template` FROM `".TABLE_MOBILEMETA."` WHERE `mobile_id`='".$themeId."'");
    //var_dump($mobilemeta->theme_template);
    if($mobilemeta->theme_template)
    {
        return $mobilemeta->theme_template;
    }
	else
        return $defaultTheme;
}
?>