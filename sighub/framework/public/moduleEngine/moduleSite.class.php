<?php
/**
 *    模块基类，所有模块均需要继承此类
 *
 *    $sn$
 */
 defined('IN_IA') or exit('Access Denied');

abstract class ModuleSite {
	public $module;
	public $weid;
	public $gweid;//20140625 janeen add
	public $inMobile;
	
	protected function createMobileUrl($do, $querystring = array()) {
		global $wpdb;
		$gweid = $_SESSION['GWEID'];
		if(!empty($querystring['gweid']))
			$gweid = $querystring['gweid'];
		$url_pattern = $wpdb -> get_var($wpdb -> prepare("SELECT `url_pattern` FROM {$wpdb -> prefix}url_pattern_mapping WHERE `GWEID`='{$gweid}' AND `module`='{$this -> module['name']}' AND `type`='action' AND value=%s AND valid=1",$do));
		if(empty($url_pattern))
		$url_pattern = $wpdb -> get_var("SELECT `url_pattern` FROM {$wpdb -> prefix}url_pattern_mapping WHERE `GWEID`='{$gweid}' AND `module`='{$this -> module['name']}' AND `type`='base' AND valid=1");
		return create_url("mobile/{$this->module['name']}/{$do}", $querystring,$url_pattern);
	}
	
	protected function createWebUrl($do, $querystring = array()) {
		if($this->weid)
			$querystring['weid'] = $this->weid;
		//$querystring['gweid'] = $this->gweid;//20140625 janeen add
		return create_url("module/{$this->module['name']}/{$do}", $querystring);
	}
	
	protected function template($filename, $public_template = False) {
		global $_W;
		$mn = $this->module['name'];
		if($this->inMobile) {
			$source = MODULES_DIR.$mn."/template/mobile/{$filename}.tpl.php";
		} else {
			$source = MODULES_DIR.$mn."/template/web/{$filename}.tpl.php";
		}
		return $source;

	}
    
    protected function model($name){
        $mn = $this->module['name'];
        $model = MODULES_DIR.$mn."/{$name}.mod.php";
        return $model;
    }
}