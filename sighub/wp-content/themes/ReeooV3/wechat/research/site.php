<?php
/**
 * 预约与调查模块微站定义
 *
 * @author WeEngine Team
 * @url http://bbs.we7.cc
 */
defined('IN_IA') or exit('Access Denied');

class ResearchModuleSite extends ModuleSite {


	public function doWebQuery() {
		global $_W, $_GPC;
		$kwd = $_GPC['keyword'];
		//$sql = 'SELECT * FROM ' . tablename('research') . ' WHERE `gweid`='.$_W['gweid'].' AND `title` LIKE "'."%{$kwd}%".'"';
		$sql = 'SELECT * FROM ' . tablename('research') . ' WHERE `gweid`='.$_W['gweid'].' AND `title` LIKE "'."%{$kwd}%".'"';
		$ds = pdo_fetchall($sql);
		foreach($ds as &$row) {
			$r = array();
			$r['title'] = $row['title'];
			$r['description'] = $row['description'];
			$r['postscript'] = $row['postscript'];
			$r['thumb'] = $row['thumb'];
			$r['reid'] = $row['reid'];
			$row['entry'] = $r;
		}
		include $this->template('query');
	}

	public function doMobileDetail() {
		global $_W, $_GPC;
		$rerid = intval($_GPC['id']);

		$sql = 'SELECT * FROM ' . tablename('research_rows') . " WHERE `rerid`='{$rerid}'";
		$row = pdo_fetch($sql);
		if(empty($row)) {
			message('访问非法.');
		}
		$sql = 'SELECT * FROM ' . tablename('research') . " WHERE `gweid`='{$_W['gweid']}' AND `reid`='{$row['reid']}'";
		$activity = pdo_fetch($sql);
		if(empty($activity)) {
			message('非法访问.');
		}
		$sql = 'SELECT * FROM ' . tablename('research_fields') . " WHERE `reid`='{$row['reid']}' ORDER BY `refid`";

		$fields = pdo_fetchall($sql);
		if(empty($fields)) {
			message('非法访问.');
		}
		$ds = array();
		$fids = array();
		foreach($fields as $f) {
			$ds[$f['refid']]['fid'] = $f['title'];
			$ds[$f['refid']]['type']= $f['type'];
			$fids[] = $f['refid'];
		}

		$fids = implode(',', $fids);
		$row['fields'] = array();
		$sql = 'SELECT * FROM ' . tablename('research_data') . " WHERE `reid`='{$row['reid']}' AND `rerid`='{$row['rerid']}' AND `refid` IN ({$fids})";
		$fdatas = pdo_fetchall($sql);
		foreach($fdatas as $fd) {
			$row['fields'][$fd['refid']] = $fd['data'];
		}

		include $this->template('detail');
	}

	public function doMobileManage() {
		global $_W, $_GPC, $wpdb;
		$reid = intval($_GPC['id']);
		$sql = 'SELECT * FROM ' . tablename('research') . " WHERE `gweid`='{$_W['gweid']}' AND `reid`='{$reid}'";
		$activity = pdo_fetch($sql);
		if(empty($activity)) {
			message('非法访问.');
		}
		$sql = 'SELECT * FROM ' . tablename('research_fields') . " WHERE `reid`='{$reid}' ORDER BY `refid`";
		$fields = pdo_fetchall($sql);
		
		if(empty($fields)) {
			message('非法访问.');
		}
		$ds = array();
		foreach($fields as $f) {
			$ds[$f['refid']] = $f['title'];
		}

/* 		$starttime = empty($_GPC['start']) ? strtotime('-1 month') : strtotime($_GPC['start']);
		$endtime = empty($_GPC['end']) ? TIMESTAMP : strtotime($_GPC['end']) + 86399;
		$select = array();
		if (!empty($_GPC['select'])) {
			foreach ($_GPC['select'] as $field) {
				if (isset($ds[$field])) {
					$select[] = $field;
				}
			}
		} */
		$select = array();
		if(array_key_exists(0,$fields))
			$select[]=$fields[0]['refid'];
		if(array_key_exists(1,$fields))
			$select[]=$fields[1]['refid'];
		$fids = implode(',', $select);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 5;
		$sql = 'SELECT * FROM ' . tablename('research_rows') . " WHERE `reid`={$reid} ORDER BY `createtime` DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

		$total = $wpdb -> get_var("SELECT COUNT(*) FROM " . tablename('research_rows') . " WHERE `reid`={$reid}");
		$pager = pagination($total, $pindex, $psize);

		$list = pdo_fetchall($sql);
		foreach($list as &$r) {
			$r['fields'] = array();
			//$sql = 'SELECT * FROM ' . tablename('research_data') . " WHERE `reid`={$reid} AND `rerid`='{$r['rerid']}' AND `refid` IN ({$fids})";
			$sql = 'SELECT * FROM ' . tablename('research_data') . " WHERE `reid`={$reid} AND `rerid`='{$r['rerid']}' AND `refid` IN ({$fids})";
			$fdatas = pdo_fetchall($sql);
			foreach($fdatas as $fd) {
				$r['fields'][$fd['refid']] = $fd['data'];
			}
		}

		include $this->template('manage');
	}

	public function doWebDisplay() {
		global $_W, $_GPC,$wpdb;
		/*
		if($_W['ispost']) {
			$reid = intval($_GPC['reid']);
			$switch = intval($_GPC['switch']);
			$sql = 'UPDATE ' . tablename('research') . " SET `status`='{$switch}' WHERE `reid`='{$reid}'";
			pdo_query($sql);
			exit();
		}
		*/
		$pindex = max(1, intval($_GPC['page']));
		$psize = 7;

		$total = $wpdb -> get_var("SELECT COUNT(*) FROM " . tablename('research') . " WHERE `gweid`= '{$_W['gweid']}'");
		$pager = pagination($total, $pindex, $psize);
		
		$sql = "SELECT * FROM " . tablename('research') . " WHERE `gweid`= '{$_W['gweid']}' ORDER BY `reid` desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize;;
		$ds = pdo_fetchall($sql);
		foreach($ds as &$item) {
			$item['isstart'] = $item['starttime'] > 0;
			$item['switch'] = $item['status'];
			$item['link'] =  $this->createMobileUrl('research', array('id' => $item['reid']));
		}
		
		$sql = 'SELECT * FROM ' . tablename('research_category') . ' WHERE `gweid`="'.$_W['gweid'].'"';
		$categories = pdo_fetchall($sql);

		include $this->template('display');
	}

	public function doWebDelete() {
		global $_W, $_GPC,$wpdb;
		$reid = intval($_GET['id']);
		if($reid) {
			$sql = 'SELECT * FROM ' . tablename('research') . " WHERE `reid`='{$reid}'";
			$activity = pdo_fetch($sql);
			file_unlink($activity['thumb']);
		 	file_unlink_from_xml(str_ireplace('\"', '"', $activity['description']));
		}
		if($reid > 0) {
			$sql = 'DELETE FROM ' . tablename('research') . ' WHERE `reid`='.$reid;
			pdo_query($sql);
			$sql = 'DELETE FROM ' . tablename('research_rows') . ' WHERE `reid`='.$reid;
			pdo_query($sql);
			$sql = 'DELETE FROM ' . tablename('research_fields') . ' WHERE `reid`='.$reid;
			pdo_query($sql);
			$sql = 'DELETE FROM ' . tablename('research_data') . ' WHERE `reid`='.$reid;
			pdo_query($sql);
			message('操作成功.', $_SERVER['HTTP_REFERER']);
		}
		message('非法访问.');
	}

	public function doWebPost() {
		global $_W, $_GPC ,$wpdb;
		$reid = intval($_GPC['id']);
		$hasData = false;
		if($reid) {
			$sql = 'SELECT * FROM ' . tablename('research') . " WHERE `gweid`='{$_W['gweid']}' AND `reid`='{$reid}'";
			$activity = pdo_fetch($sql);

			$sql = 'SELECT COUNT(*) FROM ' . tablename('research_rows') . ' WHERE `reid`=' . $reid;
			if($wpdb -> get_var($sql) > 0) {
				$hasData = true;
			}
		}
		if(checksubmit()) {
			$recrod = array();
			$recrod['title'] = trim($_GPC['activity']);
			$recrod['category'] = trim($_GPC['category']);
			$recrod['gweid'] = $_W['gweid'];
			$recrod['description'] = $_POST['description'];
			$recrod['postscript'] = $_POST['postscript'];
			$recrod['information'] = trim($_GPC['information']);
			$recrod['startdate'] = trim($_GPC['start_date']);
			$recrod['enddate'] = trim($_GPC['end_date']);
			if($_FILES['thumb']['tmp_name']) {
				if ( ! function_exists( 'wp_handle_upload' ) ) 
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
				$ret = wp_handle_upload( $_FILES['thumb'], array( 'test_form' => false ));
				//$ret = file_upload($_FILES['thumb']);
				if(!$ret['url']) {
					message ('上传封面失败, 请稍后重试.');
				}
				$str = strstr($ret['url'], 'uploads');
				$returl=substr($str, 7);
				$recrod['thumb'] = trim($returl);
			}
			$recrod['pretotal'] = intval($_GPC['pretotal']);
			if(empty($reid)) {
				$recrod['createtime'] = TIMESTAMP;
				$recrod['starttime'] = 0;
				pdo_insert('research', $recrod);
				$reid = pdo_insertid();
				if(!$reid) {
					message('保存预约失败, 请稍后重试.');
				}
			} else {
				if($hasData) {
					message('已经存在预约记录, 不能修改预约.');
				}
				if(isset($recrod['thumb']) && $activity['thumb'] != $recrod['thumb'] )
					file_unlink($activity['thumb']);
				file_unlink_from_xml_update($activity['description'],$recrod['description']);
				if(pdo_update('research', $recrod, array('reid' => $reid)) === false) {
					message('保存预约失败, 请稍后重试.');
				}
			}

			if(!$hasData) {
				$sql = 'DELETE FROM ' . tablename('research_fields') . ' WHERE `reid`='.$reid;
				pdo_query($sql);
				foreach($_GPC['title'] as $k => $v) {
					$field = array();
					$field['reid'] = $reid;
					$field['title'] = trim($v);
					$field['type'] = $_GPC['type'][$k];
					$field['essential'] =  ($_GPC['essential'][$k] == 'true')?1:0;
					$field['bind'] = $_GPC['bind'][$k];
					$field['value'] = $_GPC['value'][$k];
					$field['value'] = urldecode($field['value']);
					$field['description'] = $_GPC['desc'][$k];
					pdo_insert('research_fields', $field);
				}
			}
			message('保存预约成功.', $this->createWebUrl('display',array('gweid' => $_W['gweid'])) );
		}

		$types = array();
		$types['number'] = '数字(number)';
		$types['text'] = '字串(text)';
		$types['textarea'] = '文本(textarea)';
		$types['radio'] = '单选(radio)';
		$types['checkbox'] = '多选(checkbox)';
		$types['select'] = '选择(select)';
		//$types['calendar'] = '日历(calendar)';
		//$types['email'] = '电子邮件(email)';
		//$types['image'] = '上传图片(image)';
		//$types['range'] = '日期范围(range)';
		$fields = null; //fans_fields();
		
		$fields = array(
			'realname' => '真实姓名',
			'nickname' => '昵称',
			'mobilenumber' => '联系电话',
			'email' => '电子邮箱',
		);
		if($reid) {
			$sql = 'SELECT * FROM ' . tablename('research') . " WHERE `gweid`='{$_W['gweid']}' AND `reid`='{$reid}'";
			$activity = pdo_fetch($sql);

			if($activity) {
				$sql = 'SELECT * FROM ' . tablename('research_fields') . " WHERE `reid`='{$reid}' ORDER BY `refid`";
				$ds = pdo_fetchall($sql);
			}
		}
		$categories = pdo_fetchall("SELECT `id`,`name` FROM ".tablename('research_category')." WHERE `gweid`='{$_W['gweid']}'");
		include $this->template('post');
	}
	
	public function doMobileExport(){
		global $_W,$_GPC;
		$reid = intval($_GPC['id']);
		$sql = 'SELECT * FROM ' . tablename('research') . " WHERE `gweid`='{$_W['gweid']}' AND `reid`='{$reid}'";
		$activity = pdo_fetch($sql);
		if(empty($activity)) {
			message('非法访问.');
		}
		$sql = 'SELECT * FROM ' . tablename('research_fields') . " WHERE `reid`='{$reid}' ORDER BY `refid`";
		$fields = pdo_fetchall($sql);
		
		if(empty($fields)) {
			message('非法访问.');
		}
		$ds = array();
		foreach($fields as $f) {
			$ds[$f['refid']] = $f['title'];
		}
		$sql = 'SELECT * FROM ' . tablename('research_rows') . " WHERE `reid`={$reid} ORDER BY `createtime` DESC";
		$list = pdo_fetchall($sql);
		foreach($list as &$r) {
			$r['fields'] = array();
			$sql = 'SELECT * FROM ' . tablename('research_data') . " WHERE `reid`={$reid} AND `rerid`='{$r['rerid']}'";
			$fdatas = pdo_fetchall($sql);
			foreach($fdatas as $fd) {
				$r['fields'][$fd['refid']] = $fd['data'];
			}
		}
		header("Content-type:text/csv"); 
		header("Content-Disposition:attachment;filename=".$activity[title].date('Y-m-d').'.csv'); 
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0'); 
		header('Expires:0'); 
		header('Pragma:public'); 
		echo "\xEF\xBB\xBF";
		if(is_array($fields)) {
			echo '用户标识,';
			foreach($fields as $fid) {
				echo $fid['title'].',';
			}
			echo "提交时间\n";
		}
		
		if(is_array($list)) { 
			foreach($list as $row) {
				if(is_array($fields)) {
					echo $row['openid'].',';
					foreach($fields as $fid) {
						echo $row['fields'][(int)$fid['refid']].',';
					}
					echo date('Y-m-d H:i:s', $row['createtime'])."\n";
				}
			}
		}

		
	}

	public function doMobileResearch() {
		global $_W, $_GPC, $wpdb;
		$reid = intval($_GPC['id']);
		$sql = 'SELECT * FROM ' . tablename('research') . " WHERE `gweid`='{$_W['gweid']}' AND `reid`='{$reid}'";
		$activity = pdo_fetch($sql);
		if(empty($activity))
			message('预约活动不存在。');
		if( strtotime(date('Y-m-d')) < strtotime($activity['startdate']) || strtotime(date('Y-m-d')) > strtotime($activity['enddate']) ) {
			message('当前预约活动不在有效期.');
		}
		if(!$activity) {
			message('非法访问.');
		}
		$sql = 'SELECT * FROM ' . tablename('research_fields') . " WHERE `reid`='{$reid}' ORDER BY `refid`";
		$ds = pdo_fetchall($sql);
		if(!$ds) {
			message('非法访问.');
		}
		if(checksubmit()) {
			$pretotal = 0;
			if(!empty($_W['fans']['from_user']))
				$pretotal = $wpdb -> get_var("SELECT COUNT(*) FROM ".tablename('research_rows')." WHERE reid = '{$reid}' AND openid = '{$_W['fans']['from_user']}'");
			if(!empty($_SESSION['mid']))
				$pretotal += $wpdb -> get_var("SELECT COUNT(*) FROM ".tablename('research_rows')." WHERE reid = '{$reid}' AND openid = '{$_SESSION['mid']}'");
			if ($pretotal >= $activity['pretotal']) {
				message('抱歉!每人只能提交'.$activity['pretotal']."次！", $_SERVER['HTTP_REFERER'], 'error');
			}
			$row = array();
			$row['reid'] = $reid;
			$row['openid'] = !empty($_W['fans']['from_user'])?$_W['fans']['from_user']:$_SESSION['mid'];
			$row['createtime'] = TIMESTAMP;
			$datas = array();
			$fields = array();
			foreach($ds as $r) {
				$fields[$r['refid']] = $r;
			}
			foreach($_GPC as $key => $value) {
				if(strexists($key, 'field_')) {
					$refid = intval(str_replace('field_', '', $key));
					$field = $fields[$refid];
					if($refid && $field) {
						$entry = array();
						$entry['reid'] = $reid;
						$entry['rerid'] = 0;
						$entry['refid'] = $refid;
						if(in_array($field['type'], array('number', 'text', 'calendar', 'email', 'textarea', 'radio', 'range', 'select'))) {
							$entry['data'] = strval($value);
						}
						if(in_array($field['type'], array('checkbox'))) {
							if(!is_array($value))
								continue;
							$entry['data'] = implode(';', $value);
						}
						$datas[] = $entry;
					}
				}
			}
			if($_FILES) {
				foreach($_FILES as $key => $file) {
					if(strexists($key, 'field_')) {
						$refid = intval(str_replace('field_', '', $key));
						$field = $fields[$refid];
						if($refid && $field && $file['name'] && $field['type'] == 'image') {
							$entry = array();
							$entry['reid'] = $reid;
							$entry['rerid'] = 0;
							$entry['refid'] = $refid;
							$ret = file_upload($file);
							if(!$ret['success']) {
								message('上传图片失败, 请稍后重试.');
							}
							$entry['data'] = trim($ret['path']);
							$datas[] = $entry;
						}
					}
				}
			}
			if(empty($datas)) {
				message('非法访问.', '', 'error');
			}
			if(pdo_insert('research_rows', $row) != 1) {
				message('保存失败.');
			}
			$rerid = pdo_insertid();
			if(empty($rerid)) {
				message('保存失败.');
			}
			foreach($datas as &$r) {
				$r['rerid'] = $rerid;
				pdo_insert('research_data', $r);
			}
			if(empty($activity['starttime'])) {
				$record = array();
				$record['starttime'] = TIMESTAMP;
				pdo_update('research', $record, array('reid' => $reid));
			}
			message($activity['information'], $this -> createMobileUrl('MyResearch',array('gweid' => $_W['gweid'],'from_user' => $_W['fans']['from_user'],'id' => $rerid)) );
		}
		$initRange = false;
		$initCalendar = false;
		$binds = array();
		foreach($ds as &$r) {
			if($r['type'] == 'range') {
				$initRange = true;
			}
			if($r['type'] == 'calendar') {
				$initCalendar = true;
			}
			if($r['value']) {
				$r['options'] = explode(',', $r['value']);
			}
			if($r['bind']) {
				$binds[] = $r['bind'];
			}
		}

		if($binds && !empty($_W['fans']['mid'])) {
		
			//$profile = pdo_fetch("SELECT * FROM ".tablename('wechat_member_group')." WHERE `from_user`='{$_W['fans']['from_user']}' AND `gweid` ='{$_W['gweid']}' ");
			$profile = pdo_fetch("SELECT * FROM ".tablename('wechat_member')." WHERE `mid`='{$_W['fans']['mid']}' AND `gweid` ='{$_W['gweid']}' ");
			if($profile['gender']) {
				if($profile['gender'] == '0') $profile['gender'] = '保密';
				if($profile['gender'] == '1') $profile['gender'] = '男';
				if($profile['gender'] == '2') $profile['gender'] = '女';
			}
			foreach($ds as &$r) {
				if($profile[$r['bind']]) {
					$r['default'] = $profile[$r['bind']];
				}
			}
		}
		include $this->template('submit');
	}

	public function doMobileMyResearch() {
		global $_W, $_GPC, $wpdb;
		$rerid = intval($_GPC['id']);

		$sql = 'SELECT * FROM ' . tablename('research_rows') . " WHERE `rerid`='{$rerid}'";
		$row = pdo_fetch($sql);
		if(empty($row)) {
			message('访问非法.');
		}
		$reid = $row['reid'];
		$rstatus = $row['status'];
		$rreason = $row['reason'];
		$sql = 'SELECT * FROM ' . tablename('research') . " WHERE `gweid`='{$_W['gweid']}' AND `reid`='{$reid}'";
		$activity = pdo_fetch($sql);
		if(!$activity) {
			message('非法访问.');
		}
		$sql = 'SELECT * FROM ' . tablename('research_fields') . " WHERE `reid`='{$reid}' ORDER BY `refid`";
		$ds = pdo_fetchall($sql);
		if(!$ds) {
			message('非法访问.');
		}
		foreach($ds as &$r) {
			if($r['value']) {
				$r['options'] = explode(',', $r['value']);
			}
		}
		$fids = array();
		foreach($ds as $f) {
			$fids[] = $f['refid'];
		}
		$fids = implode(',', $fids);
		$userData = array();
		$sql = 'SELECT * FROM ' . tablename('research_data') . " WHERE `reid`='{$reid}' AND `rerid`='{$rerid}' AND `refid` IN ({$fids})";
		$fdatas = pdo_fetchall($sql);
		if(is_array($fdatas))
			foreach($fdatas as $fd) {
				$userData[$fd['refid']] = $fd['data'];
			}
		include $this->template('myresearch');
	}
	public function doMobileApplyStatus(){
		global $_W,$_GPC,$wpdb;
		$rerid = intval($_GPC['id']);
		$status = ( intval($_GPC['status']) == 1 ) ? 1 : 0;
		pdo_update('research_rows' , array('status' => $status,'reason'=>'' ) , array('rerid' => $rerid));
		echo json_encode(array('code' => 'success'));
	}
	public function doMobileRejectReason(){
		global $_W,$_GPC,$wpdb;
		$rerid = intval($_POST['id']);
		$reason = $_POST['reason'];
		$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}research_rows set reason = %s WHERE rerid=%d",$reason, $rerid));
		if ($update!==FALSE) {
			$hint = array("status"=>"success","message"=>"提交成功");
			echo json_encode($hint);
			exit;
		}else{
			$hint = array("status"=>"error","message"=>"提交失败");
			echo json_encode($hint);
			exit;
		}
	}

	public function doMobileResearchList(){
		global $_W,$_GPC;
		if($this -> has_member_module(true) && empty($_W['fans']['mid'])){
			header("Location: ".home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$_W['gweidv'].'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]).'&alert='.urlencode('登录后才能查看预约记录，请先登录。'));
		}
		if(!empty($_W['fans']['from_user'])){
			$from_user = $_W['fans']['from_user'];
			$sql = "SELECT *,rows.createtime as time FROM `".tablename('research_rows')."` rows,`".tablename('research')."` research WHERE rows.`reid` = research.`reid` AND `gweid` = '{$_W['gweid']}' AND (`openid`= '{$from_user}'";
			if(!empty($_W['fans']['mid']))
				$sql .= " OR (`openid` IN(SELECT from_user FROM ".tablename('wechat_member_group')." WHERE mid='{$_W['fans']['mid']}' ) OR `openid`='{$_SESSION['mid']}')";
			$sql .= ") ORDER BY `rerid` DESC";
		}elseif(!empty($_W['fans']['mid']))
			$sql = "SELECT *,rows.createtime as time FROM `".tablename('research_rows')."` rows,`".tablename('research')."` research WHERE rows.`reid` = research.`reid` AND `gweid` = '{$_W['gweid']}' AND (`openid` IN(SELECT from_user FROM ".tablename('wechat_member_group')." WHERE mid='{$_W['fans']['mid']}' ) OR `openid`='{$_SESSION['mid']}') ORDER BY `rerid` DESC";
		$list = pdo_fetchall($sql);
        $raw_count = 0;
        $reject_count = 0;
		if(is_array($list))
			foreach($list as $item){
				if($item['status'] === NULL)
					$raw_count ++;
				if($item['status'] === '0')
					$reject_count ++;
			}
		include $this->template('researchlist');
	}
	public function doMobileActiveResearch(){
		global $_W,$_GPC;
		$category_id = isset($_GPC['category']) ? $_GPC['category'] : NULL;
		$category = $category_id > 0 ? pdo_fetch("SELECT `id`,`name` FROM ".tablename('research_category')." WHERE `gweid`='{$_W['gweid']}' AND `id`='{$category_id}'") : array('id' => 0,'name' => NULL);
		if(empty($category))
			$category = array('id' => 0,'name' => NULL);
		$today = date('Y-m-d');
		$sql = "SELECT * FROM `".tablename('research')."` WHERE `gweid`='{$_W['gweid']}' AND `startdate` > '{$today}'".($category_id !== NULL?" AND `category` = '{$category['id']}'":"")." ORDER BY `startdate` ASC";
		$notStartResearch = pdo_fetchall($sql);
		foreach($notStartResearch as $key => &$research){
			$research['restday'] = (strtotime($research['startdate']) - strtotime($today))/(3600*24);
		}
		
		
		$sql = "SELECT * FROM `".tablename('research')."` WHERE `gweid`='{$_W['gweid']}' AND `startdate` <= '{$today}' AND `enddate` >= '{$today}'".($category_id !== NULL?" AND `category` = '{$category['id']}'":"")." ORDER BY `enddate` ASC";
		$inProcess = pdo_fetchall($sql);
		foreach($inProcess as $key => &$research){
			$research['restday'] = (strtotime($research['enddate']) - strtotime($today))/(3600*24);
		}
		include $this->template('activeResearch');
	}
	public function doMobileEndedResearch(){
		global $_W,$_GPC;
		$category_id = isset($_GPC['category']) ? $_GPC['category'] : 0;
		$category = $category_id > 0 ? pdo_fetch("SELECT `id`,`name` FROM ".tablename('research_category')." WHERE `gweid`='{$_W['gweid']}' AND `id`='{$category_id}'") : array('id' => 0,'name' => NULL);
		if(empty($category))
			$category = array('id' => 0,'name' => NULL);
		$today = date('Y-m-d');
		$sql = "SELECT * FROM `".tablename('research')."` WHERE `gweid`='{$_W['gweid']}' AND `enddate` < '{$today}'".($category_id !== NULL?" AND `category` = '{$category['id']}'":"")." ORDER BY `enddate` DESC";
		$endedResearch = pdo_fetchall($sql);
		include $this->template('endedResearch');
	}
	public function doWebCategory(){
		global $_W,$_GPC;
		if($_GPC['action'] == 'delete' && $_GPC['id']){
			pdo_update('research',array('category' => 0), array('category' => $_GPC['id'], 'gweid' => $_W['gweid']));
			$status = pdo_delete('research_category',array('id' => $_GPC['id']));
			echo json_encode(array('code' => 'success'));
		}
		if($_GPC['action'] == 'update' && !empty($_GPC['category_id'])){
			foreach($_GPC['category_id'] as $key => $categoryId ){
				if( empty($_GPC['name'][$key]))
					continue;
				if($categoryId == '-1') {
					pdo_insert('research_category', array('gweid' => $_W['gweid'], 'name'=> $_GPC['name'][$key]));
				}
				if($categoryId > '0')
					pdo_update('research_category', array('name'=> $_GPC['name'][$key]), array('id' => $categoryId, 'gweid' => $_W['gweid']));
			}
		}
		if(in_array($_GPC['action'],array('update','all'))){
			$list = pdo_fetchall("SELECT `id`,`name` FROM ".tablename('research_category')." WHERE `gweid`='{$_W['gweid']}'");
			foreach($list as &$item){
				$item['activeUrl'] = $this -> createMobileUrl('ActiveResearch', array('gweid' => $_W['gweid'],'category' => $item['id'] ));
				$item['endedUrl'] = $this -> createMobileUrl('EndedResearch', array('gweid' => $_W['gweid'], 'category' => $item['id']));
			}
			echo json_encode($list);
		}
	}
	
	public function verify_user(){
		global $_W;
		if(empty($_SESSION['mid']) && empty($_W['fans']['from_user']))
			header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_login.php?weid={$_W['weid']}&gweid={$_W['gweidv']}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");		
	}
	
	public function rule_content($postContent){
	
		$upload =wp_upload_dir();
		$baseurl=$upload['baseurl']; 
		$sp='~<img [^\>]*\ ?/?>~';
		preg_match_all( $sp, $postContent, $aPics );  
		$np = count($aPics[0]); 
		$SoImgAddress="/\<img.*?src\=\"(.*?)\"[^>]*>/i";  //正则表达式语句
		
		if ( $np > 0 ) {   
			for ( $i=0; $i < $np ; $i++ ) {  			
				$ImgUrl = $aPics[0][$i];
				preg_match($SoImgAddress,$ImgUrl,$imagesurl);
				$post_picurl=$baseurl.$imagesurl[1];
				if((stristr($imagesurl[1],"http")===false) && (stristr($imagesurl[1],'file://')===false)&&(stristr($imagesurl[1],'data:')===false)){
					$postContent=str_ireplace($imagesurl[1],$post_picurl,$postContent);
				}
			}
		}
		return $postContent;
	}
	public function has_member_module($include_user_option = false){
		global $_W;
		$result=pdo_fetchall("SELECT * FROM wp_wechat_func_info a WHERE NOT EXISTS(SELECT * FROM wp_wechat_initfunc_info b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$_W['gweid']." AND func_flag = 0) LIMIT 0, 100");
		foreach($result as $initfunc){
			if($selCheck[$initfunc['func_name']] == 0)
				$selCheck[$initfunc['func_name']] = $initfunc['status'];
		}

		if(!$include_user_option)
			if($selCheck['wechatvip']!=1)
				return false;
			else
				return true;
		else
			if($selCheck['wechatvip']!=1 || !get_option('allow_research_use_wechatvip','1'))
				return false;
			else
				return true;

	}

	public function doWebWechatvipSwitch(){
		global $_GPC;
		if(isset($_GPC['status']) )
			update_option('allow_research_use_wechatvip',$_GPC['status']);
		echo json_encode(array('status' => 'success'));
	}

	function onWechatAccountDelete($gweid){
		global $wpdb;
		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}research WHERE gweid='{$gweid}'",ARRAY_A);
		if(is_array($list))
			foreach($list as $element){
				file_unlink($element['thumb']);
				file_unlink_from_xml($element['description']);
		}
	}
}