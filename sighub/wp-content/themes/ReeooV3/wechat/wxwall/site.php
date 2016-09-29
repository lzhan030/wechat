<?php
/**
 * 微信墙模块
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class WxwallModuleSite extends ModuleSite {

	public function doWebList(){
		global $wpdb,$_W;
		
		$search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		$search = array(
			'all' => '',
			'wall_id' => "AND id LIKE '%%{$search_content}%%'",
			'wall_name' => "AND name LIKE '%%{$search_content}%%'",
		);
		$total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wxwall WHERE gweid='{$_W['gweid']}' {$search[$search_condition]}");
	    $pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this->pagination($total, $pindex, $psize);
		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}wxwall WHERE gweid='{$_W['gweid']}' {$search[$search_condition]} ORDER BY `id` DESC Limit {$offset},{$psize}",ARRAY_A);
		if(is_array($list))
			foreach($list as &$wxwall_element)
				$wxwall_element['count'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wxwall_message WHERE wxwall_id=%d",$wxwall_element['id']));
		include $this->template('list');
	}
	
	public function doWebEdit() {
		global $_W,$wpdb;
		$id = intval($_GET['id']);

		if (!empty($id)) {
			$wxwall = $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wxwall WHERE id = %d ORDER BY `id` DESC",$id),ARRAY_A);
			$wxwall['syncwall'] = unserialize($wxwall['syncwall']);
		} else {
			$wxwall = array(
				'isshow' => 0,
				'timeout' => 0,
			);
		}
		$upload =wp_upload_dir();
		$baseurl=$upload['baseurl'];
		if($_W['ispost']){
			require_once 'wp-content/themes/ReeooV3/wesite/common/upload.php';
			$insert = array(
				'name' => $_POST['name'],
				'gweid' => $_W['gweid'],
				'entry_tips'=> $_POST['entry-tips'],
				'send_tips' => $_POST['send-tips'],
				'isshow' => intval($_POST['isshow']),
				'pagesize' => intval($_POST['pagesize']),
				'syncwall' => array(
					'tx' => array(
						'status' => intval($_POST['walls']['tx']['status']),
						'subject' => $_POST['walls']['tx']['subject'],
					),
				),
			);
			if(!$_FILES['logo']['error']){
				$up=new upphoto();
				$picUrl=$up->up_photo($_FILES['logo']);
				$insert['logo'] = $picUrl;
			}
			if(!$_FILES['qrcode']['error']){
				$up=new upphoto();	
				$picUrl=$up->up_photo($_FILES['qrcode']);
				$insert['qrcode'] = $picUrl;
			}
			if(!$_FILES['background']['error']){
				$up=new upphoto();	
				$picUrl=$up->up_photo($_FILES['background']);
				$insert['background'] = $picUrl;
			}
			
			
			$insert['syncwall'] = serialize($insert['syncwall']);
			if (empty($id)) {
				$wpdb->insert($wpdb->prefix.'wxwall', $insert);
			} else {
				if(isset($insert['logo']) && $wxwall['logo'] != $insert['logo'])
					file_unlink($wxwall['logo'] );
				if(isset($insert['qrcode']) && $wxwall['qrcode'] != $insert['qrcode'])
					file_unlink($wxwall['qrcode'] );
				if(isset($insert['background']) && $wxwall['background'] != $insert['background'])
					file_unlink($wxwall['background'] );
				$wpdb->update($wpdb->prefix.'wxwall', $insert, array('id' => $id));
			}
			header("Location: {$this->createWebUrl('list', array())}");
		}
		include $this->template('edit');
	}

	public function doWebDetail() {
		global $_W ,$wpdb;

		$id = intval($_GET['id']);
		$wall = $this->getWall($id);
		
		$gweid = $wall['gweid'];
		$wechat_name = $wpdb->get_var($wpdb->prepare("SELECT wechat_name from {$wpdb->prefix}wechat_usechat WHERE GWEID = %d",$gweid));
		
		
		$wall['onlinemember'] = $wpdb -> get_col($wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wxwall_members WHERE wxwall_id = %d",$wall['id']));
		$list = $wpdb->get_results($wpdb->prepare("SELECT id, content, from_user, type, createtime FROM {$wpdb->prefix}wxwall_message WHERE wxwall_id = %d AND isshow = '2' ORDER BY createtime DESC",$wall['id']),ARRAY_A);
		$this->formatMsg($list);
		
		$upload =wp_upload_dir();
		$baseurl=$upload['baseurl'];
		include $this->template('detail');
	}

	/*
	 * 内容管理
	 */
	public function doWebManage() {
		global $_W ,$wpdb;
		$id = intval($_GET['id']);
		$wall = $this->getWall($id);
		if ($_GET['action']=='pass' && !empty($_POST['select'])) {
			$format = implode(', ', array_fill(0, count($_POST['select']), '%d'));
			$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}wxwall_message SET `isshow`=1,`createtime`=".TIMESTAMP." WHERE `id` IN({$format})",$_POST['select']));
			echo json_encode(array('status'=>'success'));
			exit;
		}
		if ( $_GET['action']=='delete' && !empty($_POST['select'])) {
			$format = implode(', ', array_fill(0, count($_POST['select']), '%d'));
			$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}wxwall_message WHERE `id` IN({$format})",$_POST['select']));

			echo json_encode(array('status'=>'success'));
			exit;
		}
		$isshow = isset($_GET['isshow']) ? intval($_GET['isshow']) : 0;
		$condition = '';
		if($isshow == 0) {
			$condition .= 'AND isshow = '.$isshow;
		} else {
			$condition .= 'AND isshow > 0';
		}
		
		$pindex = max(1, intval($_GET['page']));
		$total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wxwall_message WHERE wxwall_id = '{$wall['id']}' {$condition}");

		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;

		
		$list = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wxwall_message WHERE wxwall_id = %d {$condition} ORDER BY createtime DESC limit {$offset},{$psize}",$wall['id']),ARRAY_A);
		
		$pager = $this->pagination($total, $pindex, $psize);

		if (!empty($list)) {
			foreach ($list as &$row) {
				$row['content'] = iunserializer($row['content']);
				$row['avatar'] = $row['content']['avatar'];
				if(stripos($row['avatar'],'http')===FALSE){
					if(!empty($row['avatar']) || $row['avatar']===0 ||$row['avatar']==='0')
						$row['avatar'] = home_url()."/wp-content/themes/ReeooV3/wechat/wxwall/template/avatar/{$row['avatar']}.png";
					else
						$row['avatar'] = home_url()."/wp-content/themes/ReeooV3/wechat/wxwall/template/avatar/avatar_".rand(0,12).".jpg";
					}
				$row['nickname'] = $row['content']['nickname'];
				$row['content'] = emotion($row['content']['content']);
			}
			unset($row);
		}
		include $this->template('manage');
	}

	/*
	 * 增量数据调用
	 */
	public function doWebIncoming() {
		global $_GPC, $_W ,$wpdb;
		$id = intval($_GET['id']);
		$lastmsgtime = intval($_GET['lastmsgtime']);
		$sql = "SELECT id, content, from_user, type, createtime FROM {$wpdb->prefix}wxwall_message WHERE wxwall_id = %d";
		$page = max(1, intval($_GET['page']));
		if (!empty($lastmsgtime)) {
			$sql .= " AND isshow = '1' ORDER BY isshow DESC,id ASC LIMIT 1";
		} else {
			$sql .= " AND isshow = '1' ORDER BY createtime ASC  LIMIT 1";
		}
		$list = $wpdb->get_results($wpdb->prepare($sql,$id),ARRAY_A);
		if (!empty($list)) {
			$this->formatMsg($list);
			$row = $list[0];
			$wpdb->update($wpdb->prefix.'wxwall_message', array('isshow' => '2'), array('id' => $row['id']));
			$row['content'] = emotion($row['content'], '48px');
			message($row, '', 'ajax');
		}
	}

	public function doWebQrcode() {
		global $_GPC, $_W, $wpdb;
		$gweid = $_W['gweid'];
		$wechat_name = $wpdb->get_var($wpdb->prepare("SELECT wechat_name from {$wpdb->prefix}wechat_usechat WHERE GWEID = %d",$gweid));
		$id = intval($_GET['id']);
		$wall = $this->getWall($id);
		$upload =wp_upload_dir();
		$baseurl=$upload['baseurl'];
		include $this->template('qrcode');
	}

	private function getWall($id) {
		global $wpdb;
		$wall = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wxwall WHERE id = %d LIMIT 1",$id),ARRAY_A);
		$wall['syncwall'] = unserialize($wall['syncwall']);
		if(!intval($wall['pagesize']))
			$wall['pagesize'] = 7;
		return $wall;
	}

	private function formatMsg(&$list) {
		global $_W,$wpdb;
		if (empty($list)) {
			return false;
		}
		$uids = $members = array();
		foreach ($list as &$row) {
			$uids[$row['from_user']] = $row['from_user'];
			$content = unserialize($row['content']);
			$row['content'] = $content['content'];
			$row['avatar'] = $content['avatar'];
			$row['nickname'] = $content['nickname'];
			$row['content'] = emotion($row['content'], '48px');
		}
		unset($row);
		/*
		if (!empty($uids)) {
			$members = array();//fans_search($uids, array('nickname', 'avatar'));
		}
		if (!empty($members)) {
			foreach ($list as $index => &$row) {
				if ($row['type'] == 'txwall') {
					continue;
				}
				$row['nickname'] = $members[$row['from_user']]['nickname'];
				$row['avatar'] = $members[$row['from_user']]['avatar'];
				unset($list[$index]['from_user']);
			}
			unset($row);
		}*/
	}

	public function doWebIncomingTxWall() {
		global $_W, $_GPC, $wpdb;
		
		
		$result = array('status' => 0);
		$id = intval($_GPC['id']);
		$lastmsgtime = intval($_GPC['lastmsgtime']);
		$lastuser = '';

		$wall = $wpdb->get_var($wpdb->prepare("SELECT syncwall FROM {$wpdb->prefix}wxwall WHERE id = %d LIMIT 1",$id));
		if (empty($wall)) {
			message($result, '', 'ajax');
		}
		$wall = unserialize($wall);
		if (empty($wall['tx']['status'])) {
			message($result, '', 'ajax');
		}
		$response = ihttp_request('http://wall.v.t.qq.com/index.php?c=wall&a=topic&ak=801424380&t='.$wall['tx']['subject'].'&fk=&fn=&rnd='.TIMESTAMP);
		if (empty($response['content'])) {
			$result['status'] = -1;
			message($result, '', 'ajax');
		}
		$last = $wpdb->get_row($wpdb->prepare("SELECT createtime, from_user FROM {$wpdb->prefix}wxwall_message WHERE createtime >= %s AND type = 'txwall' AND wxwall_id = %d ORDER BY createtime DESC LIMIT 1",$lastmsgtime,$id),ARRAY_A);
		if (!empty($last)) {
			$lastmsgtime = $last['createtime'];
			$lastuser = $last['from_user'];
		}
		$list = json_decode($response['content'], true);
		if (!empty($list['data']['info'])) {
			$insert = array();
			foreach ($list['data']['info'] as $row) {
				if ($row['timestamp'] < $lastmsgtime || ($lastmsgtime == $row['timestamp'] && !empty($lastuser) && $lastuser == $row['name'])) {
					break;
				}
				$content = array('nickname' => $row['nick'], 'avatar' => !empty($row['head']) ? $row['head'] . '/120' : '', 'content' => $row['text']);
				$insert[] = array(
					'wxwall_id' => $id,
					'content' => serialize($content),
					'from_user' => $row['name'],
					'type' => 'txwall',
					'isshow' => 1,
					'createtime' => $row['timestamp'],
				);
			}
			unset($row);
			$insert = array_reverse($insert);
			foreach ($insert as $row) {
				$wpdb->insert($wpdb->prefix.'wxwall_message', $row);
			}
			$lastmsgtime = $row['timestamp'];
			$result = array(
				'status' => 1,
				'lastmsgtime' => $lastmsgtime,
			);
			message($result, '', 'ajax');
		} else {
			message($result, '', 'ajax');
		}
	}

	public function doMobileUserMessage(){
		global $wpdb,$_W;
		$wall = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".tablename('wxwall')." WHERE id = %d LIMIT 1",$_POST['id']),ARRAY_A);
		$content = array('nickname' => $_POST['nickname'], 'avatar' => $_POST['avatar'], 'content' => $_POST['content']);
		$data = array(
			'wxwall_id' => $_POST['id'],
			'from_user' => $_W['fans']['from_user'],
			'type' => 'text',
			'content' => serialize($content),
			'createtime' => TIMESTAMP,
		);
		if($wall['isshow'])
			$data['isshow'] = '0';
		else
			$data['isshow'] = '1';
		$wpdb->insert($wpdb->prefix.'wxwall_message', $data);
		//pdo_update($wpdb->prefix.'wxwall_members', array('lastupdate' => TIMESTAMP), array('from_user' => $this->message['from']));
		
		$content = $wall['send_tips'];
		echo json_encode(array('status'=>'success','message'=>$content));
	}
	public function doMobileChat(){
		global $_W;
		include $this->template('chat');
	}
	public function doWebWxwallDelete(){
		global $wpdb;
		$id = intval($_POST['wxwall_id']);
		$wxwall = $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wxwall WHERE id = %d",$id),ARRAY_A);
		
		file_unlink($wxwall['logo'] );
		file_unlink($wxwall['qrcode'] );
		file_unlink($wxwall['background'] );
		
		$wpdb->delete($wpdb->prefix.'wxwall',array('id'=>$id));
		echo json_encode(array('status'=>'success'));
		
	}
	/*分页设置*/
	function pagination($tcount, $pindex, $psize = 15, $url = '', $context = array('before' => 5, 'after' => 4),$attach = array(),$remove = array()) {
		$pdata = array(
			'tcount' => 0,
			'tpage' => 0,
			'cindex' => 0,
			'findex' => 0,
			'pindex' => 0,
			'nindex' => 0,
			'lindex' => 0,
			'options' => ''
		);

		$pdata['tcount'] = $tcount;
		$pdata['tpage'] = ceil($tcount / $psize);
		if($pdata['tpage'] <= 1) {
			return '';
		}
		$cindex = $pindex;
		$cindex = min($cindex, $pdata['tpage']);
		$cindex = max($cindex, 1);
		$pdata['cindex'] = $cindex;
		$pdata['findex'] = 1;
		$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
		$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
		$pdata['lindex'] = $pdata['tpage'];
		$GET = $_GET;
		if(!empty($attach))
			$GET = array_merge($GET,$attach);
		if(!empty($remove))
			$GET = array_diff_key($GET,$remove);
		if(in_array('beIframe',$_GET))
		$GET['beIframe'] ='1';
		//var_dump($_GET);
			if($url) {
				$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
				$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
				$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
				$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
			} else {
				$GET['page'] = $pdata['findex'];
				$pdata['faa'] = 'href="' .'?' . http_build_query($GET) . '"';
				$GET['page'] = $pdata['pindex'];
				$pdata['paa'] = 'href="' . '?' . http_build_query($GET) . '"';
				$GET['page'] = $pdata['nindex'];
				$pdata['naa'] = 'href="' . '?' . http_build_query($GET) . '"';
				$GET['page'] = $pdata['lindex'];
				$pdata['laa'] = 'href="' .'?' . http_build_query($GET) . '"';
			}

		$html = '<ul class="pagination pagination-centered">';
		if($pdata['cindex'] > 1) {
			$html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
			$html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
		}
		//页码算法：前5后4，不足10位补齐
		if(!$context['before'] && $context['before'] != 0) {
			$context['before'] = 5;
		}
		if(!$context['after'] && $context['after'] != 0) {
			$context['after'] = 4;
		}

		if($context['after'] != 0 && $context['before'] != 0) {
			$range = array();
			$range['start'] = max(1, $pdata['cindex'] - $context['before']);
			$range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
			if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
				$range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
				$range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
			}
			for ($i = $range['start']; $i <= $range['end']; $i++) {
					if($url) {
						$aa = 'href="?' . str_replace('*', $i, $url) . '"';
					} else {
						$GET['page'] = $i;
						$aa = 'href="?' . http_build_query($GET) . '"';
					}
				$html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
			}
		}

		if($pdata['cindex'] < $pdata['tpage']) {
			$html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
			$html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
		}
		$html .= '</ul>';
		return $html;
	}	

	function onWechatAccountDelete($gweid){
		global $wpdb;
		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}wxwall WHERE gweid='{$gweid}'",ARRAY_A);
		if(is_array($list))
			foreach($list as $wxwall_element){
				file_unlink($wxwall_element['logo'] );
				file_unlink($wxwall_element['qrcode'] );
				file_unlink($wxwall_element['background'] );
			}
				
	}
}
