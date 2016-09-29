<?php
/**
 *    模块操作类
 *
 *    $sn$
 */
defined('IN_IA') or exit('Access Denied');

class WeUtility {

	public static function createModuleSite($name) {
		$classname = "{$name}ModuleSite";
		if(!class_exists($classname)) {
			$file = MODULES_DIR. "{$name}/site.php";
			if(!is_file($file)) {
				trigger_error('ModuleSite Definition File Not Found '.$file, E_USER_WARNING);
				return null;
			}
			require $file;
		}
		if(!class_exists($classname)) {
			trigger_error('ModuleSite Definition Class Not Found', E_USER_WARNING);
			return null;
		}
		$o = new $classname();
		$o->module = $GLOBALS['_W']['account']['modules'][$GLOBALS['_W']['modules'][$name]['mid']];
		$o->weid = $GLOBALS['_W']['weid'];
		$o->gweid = $GLOBALS['_W']['gweid'];//20140625 janeen add
		$o->inMobile = defined('IN_MOBILE');
		if($o instanceof ModuleSite) {
			return $o;
		} else {
			trigger_error('ModuleSite Class Definition Error', E_USER_WARNING);
			return null;
		}
	}
}    