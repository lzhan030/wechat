<?php
/**
 * 砸蛋抽奖模块
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
require_once ABSPATH.'wp-content/themes/ReeooV3/wechat/common/fans.mod.php';

class EggModuleSite extends ModuleSite {

    public $tablename = 'egg_reply';
	
	/*手机端砸蛋抽奖页面*/
	public function doMobileLottery() {
		global $_GPC, $_W, $wpdb;
		$title = '砸蛋抽奖';
		$gweid=$_W['gweid'];
		$mid=$_W['fans']['mid'];
		$id = intval($_GET['id']);
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid


		
		/*判断功能是否可用*/
		if(!$this->has_module($gweid,'wechatactivity_egg')){
			message('砸蛋功能已经关闭！');
		}else{
			
			if(!$this->has_module($gweidv,'wechatvip')){
				message('没有开启会员权限,无法使用该功能！');
			}
			if($this->has_module($gweidv,'wechatvip') && empty($mid)){
				header("Location: ".home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$gweidv.'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]).'&alert='.urlencode('登录后才能使用砸蛋，请先登录。'));
				exit;
			}
			if($this->has_module($gweidv,'wechatvip') && !empty($mid)){
				$vipaudit=$wpdb->get_var( $wpdb -> prepare("SELECT wechat_vipaudit FROM {$wpdb->prefix}wechat_usechat where GWEID=%d",$gweidv));
				$isaudit=$wpdb->get_var( $wpdb -> prepare("SELECT isaudit FROM {$wpdb->prefix}wechat_member where mid=%s and GWEID=%d",$mid,$gweidv));
				if(($vipaudit=='1')&&($isaudit=='0')){
					message('您的会员申请已经被拒绝！');
				}else if(($vipaudit=='1')&&($isaudit=='2')){
					message('您的会员权限正在审批中...！');
				}
			}
		}

		
		$sql = $wpdb -> prepare("SELECT id, picture, periodlottery, maxlottery, rule, hitcredit, misscredit FROM {$wpdb->prefix}egg_reply WHERE id =%d LIMIT 1",$id);
		$egg = $wpdb->get_row($sql,ARRAY_A);
		if (empty($egg)) {
			message('页面已经失效！');
		}
		/*判断功能是否可用END*/

		$egg = $wpdb -> get_row($wpdb->prepare("SELECT id, picture, maxlottery, default_tips, rule FROM {$wpdb->prefix}egg_reply WHERE id = %d LIMIT 1",$id),ARRAY_A);
		if (empty($egg)) {
			message('非法访问，请重新发送消息进入砸蛋页面！');
		}
		
		/*处理活动图片的显示*/
		$upload =wp_upload_dir();
		if((empty($egg['picture']))||(stristr($egg['picture'],"http")!==false)){
			$scrpicture=$egg['picture'];
		}else{
			$scrpicture=$upload['baseurl'].$egg['picture'];
		}
		/*处理活动图片的显示END*/
		
		/*处理文章图片的显示*/
		$eggrule=$this->rule_content($egg['rule']);
		
		//获取该rid下中奖的个数
		$total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}egg_winner WHERE rid = %d AND createtime > %s AND mid = %s AND status <> 3 AND award <> ''", $id, strtotime(date('Y-m-d')), $fromuser));
			
		//获取会员的相关信息
		$member = $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wechat_member WHERE mid = %s LIMIT 1",$mid),ARRAY_A);;
		/*winner中取出用户某个砸蛋的中奖情况*/
		$myaward = $wpdb->get_results($wpdb->prepare("SELECT award, description, status FROM {$wpdb->prefix}egg_winner WHERE mid = %s AND award <> '' AND rid = %d ORDER BY createtime DESC", $mid, $id),ARRAY_A);
		//查看其它人的中奖情况
		$sql = "SELECT a.award, b.realname FROM {$wpdb->prefix}egg_winner AS a
				LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid WHERE b.mobilenumber <> '' AND b.realname <> '' AND a.award <> '' AND a.rid = %d ORDER BY a.createtime DESC LIMIT 20";
		$otheraward = $wpdb->get_results($wpdb->prepare($sql,$id),ARRAY_A);
		include $this->template('lottery');
	}
    
	/*手机端砸蛋相关操作*/
	public function doMobileGetAward() {
		global $_GPC, $_W, $wpdb;
		$gweid=$_W['gweid'];
		$mid=$_W['fans']['mid'];
		$id = intval($_GET['id']);
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid


		

		/*进入抽奖页面后，管理员禁止了应用或禁止了会员处理*/
		if($this->has_module($gweid,'wechatactivity_egg')){

			if($this->has_module($gweidv,'wechatvip') && empty($mid)){
				$url=home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$gweidv.'&redirect_url='.urlencode($this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid))).'&alert='.urlencode('登录后才能使用砸蛋，请先登录。');
				$result = array('status' => -2, 'message' => '请重新登录','url'=>$url);
				message($result, '', 'ajax');
			}
			if($this->has_module($gweidv,'wechatvip') && !empty($mid)){

				$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
				$vipaudit=$wpdb->get_var( $wpdb -> prepare("SELECT wechat_vipaudit FROM {$wpdb->prefix}wechat_usechat where GWEID=%d",$gweidv));
				$isaudit=$wpdb->get_var( $wpdb -> prepare("SELECT isaudit FROM {$wpdb->prefix}wechat_member where mid=%s and GWEID=%d",$mid,$gweidv));
				if(($vipaudit=='1')&&($isaudit=='0')){
					$hint="您的会员申请已经被拒绝";
					$result = array('status' => -2, 'message' => $hint,'url'=>$url);
					message($result, '', 'ajax');
				}else if(($vipaudit=='1')&&($isaudit=='2')){
					$hint="您的会员权限正在审批中...！";
					$result = array('status' => -2, 'message' => $hint,'url'=>$url);
					message($result, '', 'ajax');
				}
			}
			if(!$this->has_module($gweidv,'wechatvip')){
				$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
			
				$result = array('status' => -2, 'message' => '没有开启会员权限,无法使用该功能！','url'=>$url);
				message($result, '', 'ajax');
			}

			$sql = $wpdb -> prepare("SELECT id, periodlottery, maxlottery, misscredit, hitcredit FROM {$wpdb->prefix}egg_reply WHERE id =%d LIMIT 1",$id);
			$egg = $wpdb->get_row($sql,ARRAY_A);
			if (empty($egg)) {
				$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
				$result = array('status' => -2, 'message' => '该页面已经失效！','url'=>$url);
				message($result, '', 'ajax');
			}
		}else{
			$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
			$result = array('status' => -2, 'message' => '砸蛋活动已经关闭！','url'=>$url);
			message($result, '', 'ajax');
		}
		/*进入抽奖页面后，管理员禁止了应用或禁止了会员处理END*/
		
		$egg = $wpdb -> get_row($wpdb->prepare("SELECT id, periodlottery, maxlottery, default_tips, misscredit, hitcredit FROM {$wpdb->prefix}egg_reply WHERE id = %d LIMIT 1",$id),ARRAY_A);
		if (empty($egg)) {
			message('非法访问，请重新发送消息进入砸蛋页面！');
		}
		$result = array('status' => -1, 'message' => '');
		if (!empty($egg['periodlottery'])) {
			 
			$total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}egg_winner WHERE rid = %d AND createtime > %s AND mid = %s AND status <> 3", $id, strtotime(date('Y-m-d')), $mid));
			$lastdate = $wpdb -> get_var($wpdb -> prepare("SELECT createtime FROM {$wpdb->prefix}egg_winner WHERE rid = %d AND mid = %s AND status <> 3 ORDER BY createtime DESC", $id, $mid));
			if (($total >= intval($egg['maxlottery'])) && strtotime(date('Y-m-d')) < strtotime(date('Y-m-d', $lastdate)) + $egg['periodlottery'] * 86400) {
				$result['message'] = '您还未到达可以再次砸蛋的时间<br>下次可砸时间为'.date('Y-m-d', strtotime(date('Y-m-d', $lastdate)) + $egg['periodlottery'] * 86400);
				message($result, '', 'ajax');
			}
		} else {
			$total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}egg_winner WHERE rid = %d AND createtime > %s AND mid = %s AND status <> 3", $id, strtotime(date('Y-m-d')), $mid));
			if (!empty($egg['maxlottery']) && $total >= $egg['maxlottery']) {
				$result['message'] = $egg['periodlottery'] ? '您已经超过当日砸蛋次数' : '您已经超过最大砸蛋次数';
				message($result, '', 'ajax');
			}
		}
		
		$gifts = $wpdb->get_results($wpdb->prepare("SELECT id, probalilty, inkind FROM {$wpdb->prefix}egg_award WHERE rid = %d ORDER BY probalilty ASC", $id),ARRAY_A);
		
		//计算每个礼物的概率
		$probability = 0;
		$rate = 1;
		$award = array();
		foreach ($gifts as $name => $gift){
			if (empty($gift['probalilty'])) {
				continue;
			}
			if ($gift['probalilty'] < 1) {
				$temp = explode('.', $gift['probalilty']);
				$temp = pow(10, strlen($temp[1]));
				$rate = $temp < $rate ? $rate : $temp;
			}
			$probability = $probability + $gift['probalilty'] * $rate;
			$award[] = array('id' => $gift['id'], 'probalilty' => $probability, 'inkind' => $gift['inkind']);
		}
		$all = 100 * $rate;
		if($probability < $all){
			$award[] = array('title' => '','probalilty' => $all);
		}
		mt_srand((double) microtime()*1000000);
		$rand = mt_rand(1, $all);
		foreach ($award as $key => $gift){
			if(isset($award[$key - 1])){
				if($rand > $award[$key -1]['probalilty'] && $rand <= $gift['probalilty']){
					$awardid = $gift['id'];
					break;
				}
			}else{
				if($rand > 0 && $rand <= $gift['probalilty']){
					$awardid = $gift['id'];
					break;
				}
			}
		}
		$title = '';
		$result['message'] = empty($egg['default_tips']) ? '很遗憾,您没能中奖！' : $egg['default_tips'];
		
		$data = array(
			'rid' => $id,
			'from_user' => $fromuser,
			'mid' => $mid,
			'status' => empty($gift['inkind']) ? 2 : 0,   //只有实物的status会被置为0(未领取)，其他的情况都是2
			'createtime' => TIMESTAMP,
		);

		$credit = array(
			'rid' => $id,
			'award' => (empty($awardid) ? '未中' : '中') . '奖励积分',
			'from_user' => $fromuser,
			'mid' => $mid,
			'status' => 3,
			'description' => (empty($awardid) ? $egg['misscredit'] : $egg['hitcredit']),
			'createtime' => TIMESTAMP,
		);
		if (!empty($awardid)) {
			$gift = $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}egg_award WHERE rid = %d AND id = %d", $id, $awardid),ARRAY_A);
			if ($gift['total'] > 0) {
				$data['award'] = $gift['title'];
				if (!empty($gift['inkind'])) {
					$data['description'] = $gift['description'];
					//更新数量  
					$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}egg_award set total=total-1 WHERE rid = %d AND id = %s",$id,$awardid));
				} else {
					$gift['activation_code'] = iunserializer($gift['activation_code']);
					$code = array_pop($gift['activation_code']);
					//更新数量     
					$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}egg_award set total=total-1, activation_code=%s WHERE rid = %d AND id = %s",iserializer($gift['activation_code']),$id,$awardid));
					$data['description'] = '兑换码：' . $code . '<br /> 兑换地址：' . $gift['activation_url'];
				}
				$result['message'] = '恭喜您，得到“'.$data['award'].'”！' ;
				$result['status'] = 0;
			} else {
				$credit['description'] = $egg['misscredit'];
				$credit['award'] = '未中奖励积分';
			}
		}

		!empty($credit['description']) && $result['message'] .= '<br />' . $credit['award'] . '：'. $credit['description'];
		$data['aid'] = $gift['id'];
		if (!empty($credit['description'])) {
			$wpdb->insert($wpdb->prefix.'egg_winner', $credit);
			$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}wechat_member set point=point+{$credit['description']} WHERE mid = %s",$mid)); //将积分写进数据库
		}
		$wpdb->insert($wpdb->prefix.'egg_winner', $data); 
		$result['myaward'] = $wpdb->get_results($wpdb->prepare("SELECT e1.award, e1.description, e1.status, w1.realname FROM {$wpdb->prefix}egg_winner e1 LEFT JOIN {$wpdb->prefix}wechat_member w1 ON e1.mid = w1.mid WHERE e1.mid = %s AND e1.award <> '' AND e1.rid = %d ORDER BY e1.createtime DESC", $mid, $id),ARRAY_A);
		message($result, '', 'ajax');
	}
	
	/*获取中奖名单列表*/
	public function doMobileGetOtherAward(){
		global $_GPC, $_W, $wpdb;
		
		
		$gweid=$_GET['gweid'];
		$id = intval($_GET['id']);
	
		//"中奖名单" also need update after getaward 
		$result['otheraward'] = $wpdb->get_results($wpdb->prepare("SELECT e1.award, e1.description, w1.realname FROM {$wpdb->prefix}egg_winner e1 LEFT JOIN {$wpdb->prefix}wechat_member w1 ON e1.mid = w1.mid WHERE e1.award <> '' AND e1.rid = %d ORDER BY e1.createtime DESC", $id),ARRAY_A);
		message($result, '', 'ajax');
	}

	/*not be used*/
	public function doMobileRegister() {
		global $_GPC, $_W;
		$title = '砸蛋领奖登记个人信息';
		if(!$this->has_module($gweid,'wechatvip')){
			message('没有开启会员权限,无法使用该功能！');
		}
		if($this->has_module($gweid,'wechatvip') && empty($_W['fans']['mid'])){
			header("Location: ".home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?weid='.$_W['weid'].'&gweid='.$_W['gweidv'].'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]).'&alert='.urlencode('登录后才能使用砸蛋，请先登录。'));
		}
		$mid=$_W['fans']['mid'];
		
		if (!empty($_POST['submit'])) {
			if (empty($_W['fans']['from_user'])) {
				message('非法访问，请重新发送消息进入砸蛋页面！');
			}
			$data = array(
				'realname' => $_POST['realname'],
				'mobilenumber' => $_POST['mobile'],
				'qq' => $_POST['qq'],
				'gweid' => $_POST['gweid']
			);
			if (empty($data['realname'])) {
				die('<script>alert("请填写您的真实姓名！");location.reload();</script>');
			}
			if (empty($data['mobile'])) {
				die('<script>alert("请填写您的手机号码！");location.reload();</script>');
			}

			$wpdb->update($wpdb->prefix.'wechat_member', $data, array('mid' => $mid));	
			die('<script>alert("登记成功！");location.href = "'.$this->createMobileUrl('lottery', array('id' => $_GET['id'],'gweid' => $_W['gweid'])).'";</script>');
		}
		include $this->template('register');
	}

	public function doWebFormDisplay() {
		global $_W, $_GPC;
		$result = array('error' => 0, 'message' => '', 'content' => '');
		$result['content']['id'] = $GLOBALS['id'] = 'add-row-news-'.$_W['timestamp'];
		$result['content']['html'] = $this->template('egg/item', TEMPLATE_FETCH);
		exit(json_encode($result));
	}
    /*中奖详情页面*/
	public function doWebAwardlist() {
		global $_GPC, $_W, $wpdb;
		//checklogin();
		$id = intval($_GET['id']);
		if (checksubmit('delete')) {
			$deleteidarray=implode("','", $_POST['select']);
			$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}egg_winner WHERE id IN ('{$deleteidarray}')") );
			message('删除成功！',$this->createWebUrl('awardlist',array('id' => $id, 'page' => $_GET['page'])));
		}
		if (!empty($_GET['wid'])) {
			$wid = intval($_GET['wid']);
			$update=$wpdb->update( $wpdb->prefix.'egg_winner', array('status'=>intval($_GET['status'])),array('id'=>$wid),array('%d'),array('%s'));
			/*确认取消领奖，更新库存*/
			//读奖品id
			$awardid= $wpdb->get_var($wpdb -> prepare("SELECT aid FROM {$wpdb->prefix}egg_winner where id=%s",$wid));
			//读total_status
			$sql = $wpdb -> prepare("SELECT aid_status FROM {$wpdb->prefix}egg_winner where id=%s",$wid);
			$aid_status = $wpdb->get_var($sql);
			$message="确认领奖成功!";
			if($aid_status==1){//是否更新库存标识
				//加库存
				if(intval($_GET['status'])==0){
					$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}egg_award set total=total+1 WHERE id = %s",$awardid));
					$message="取消领奖成功!";
				}
				//减库存
				if(intval($_GET['status'])==1){
				    $totalzero=$wpdb->get_var($wpdb -> prepare("SELECT total FROM {$wpdb->prefix}egg_award where id=%s",$awardid));
					if(intval($totalzero)>0){   //防止数量出现负数
						$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}egg_award set total=total-1 WHERE id = %s",$awardid));
					}
					$message="确认领奖成功!";
				}
			}else{
				$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}egg_winner set aid_status=1 WHERE id = %s",$wid));
			}
			/*确认取消领奖，更新库存END*/
			
			message($message, $this->createWebUrl('awardlist', array('id' => $id, 'page' => $_GET['page'])));
		}
		$pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$where = '';
		$starttime = !empty($_GET['start']) ? strtotime($_GET['start']) : strtotime(date("Y-m-d", TIMESTAMP));
		$endtime = !empty($_GET['end']) ? strtotime($_GET['end'])+ 86400 - 1 : strtotime(date("Y-m-d", TIMESTAMP));
		if (!empty($starttime) && $starttime == $endtime) {
			$endtime = $endtime + 86400 - 1;
		}
		$condition = array(
			'isregister' => array(
				'',
				" AND b.realname <> ''",
				" AND b.realname = ''",
			),
			'isaward' => array(
				'',
				" AND a.aid != '0'",
				" AND a.aid = '0'",
			),
			'isstatus' => array(
				" AND a.status = '0'",
				" AND a.status != '0'",
				'',
				'',
				'',
			),
			'mobilenumber' => " AND b.mobilenumber ='{$_GET['profilevalue']}'",
			'realname' => " AND b.realname ='{$_GET['profilevalue']}'",
			'title' => " AND a.award = '{$_GET['awardvalue']}'",
			'description' => " AND a.description = '{$_GET['awardvalue']}'",
			'starttime' => " AND a.createtime >= '$starttime'",
			'endtime' => " AND a.createtime <= '$endtime'",
		);
		if (!isset($_GET['isregister'])) {
			$_GET['isregister'] = "";
		}
		$where .= $condition['isregister'][$_GET['isregister']];
		if (!isset($_GET['isaward'])) {
			$_GET['isaward'] = "";
		}
		$where .= $condition['isaward'][$_GET['isaward']];
		if (!empty($_GET['profile'])) {
			$where .= $condition[$_GET['profile']];
		}
		if (!empty($_GET['award'])) {
			$where .= $condition[$_GET['award']];
		}
		if (!isset($_GET['isstatus'])) {
			$_GET['isstatus'] = 4;
		}
		$where .= $condition['isstatus'][$_GET['isstatus']];
		if (!empty($starttime)) {
			$where .= $condition['starttime'];
		}
		if (!empty($endtime)) {
			$where .= $condition['endtime'];
		}
		
		$offset=($pindex - 1) * $psize;
		$list = $wpdb -> get_results($wpdb -> prepare("SELECT a.id, a.award, a.description, a.status, a.createtime, b.realname, b.mobilenumber FROM {$wpdb->prefix}egg_winner AS a
				LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid WHERE a.rid = %d AND a.award <> '' {$where} ORDER BY a.createtime DESC, a.status ASC LIMIT {$offset},{$psize}",$id),ARRAY_A);
		
		if (!empty($list)) { 	
		    $total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}egg_winner AS a
				LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid WHERE a.rid = %d AND a.award <> '' {$where}", $id));
			$pager = pagination($total, $pindex, $psize);
		}
		include $this->template('awardlist');
	}

	/*删除奖品*/
	public function doWebDelete() {
		global $_W,$_GPC, $wpdb;
		$id = intval($_GET['id']);
		$row = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$wpdb->prefix}egg_award WHERE `id`=%d",$id),ARRAY_A);
		if (empty($row)) {
			message('抱歉，奖品不存在或是已经被删除！', '', 'error');
		}
		$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}egg_award WHERE id =%d",$id ) );
		if ($delete!==FALSE) {
			message('删除奖品成功', '', 'success');
		}
	}
	
	/*砸蛋活动列表页面 */
	public function doWebEggList(){
		global $wpdb,$_W;
		$gweid = $_W['gweid'];
		$search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		
		$search = array(
			'all' => '',
			'id' => "AND id = '{$search_content}'",
			'name' => "AND name = '{$search_content}'"
		);
		$sql=$wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}egg_reply where gweid=%s {$search[$search_condition]} ORDER BY id desc",$gweid);	
		$total = $wpdb->get_var($sql);
		$pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}egg_reply where gweid=%s {$search[$search_condition]} ORDER BY id desc limit {$offset},{$psize}",$gweid);
		$list = $wpdb->get_results($sql,ARRAY_A);
		
		//删除砸蛋活动
		 if(isset($_POST['egg_del']) && !empty($_POST['egg_del']) ){							
			
			$sql = $wpdb -> prepare("SELECT id, picture,rule FROM {$wpdb->prefix}egg_reply where id=%d",$_POST['eggid']);
			$replies = $wpdb->get_results($sql,ARRAY_A);
			$deleteid = array();
			$upload = wp_upload_dir();
			
			if (!empty($replies)) {
				foreach ($replies as $index => $row) {
					file_unlink($row['picture']);
					file_unlink_from_xml($row['rule']);
					$deleteid[] = $row['id'];
				}
			}
			$how_many = count($deleteid);
			$placeholders = array_fill(0, $how_many, '%d');
			$deleteidarray=implode("','", $deleteid);
			
			$delete= $wpdb->query( "DELETE FROM {$wpdb->prefix}egg_reply WHERE id IN ('{$deleteidarray}')" );
			//活动删除，奖品是不是也要删除
			/* $deleteaward= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}egg_award WHERE rid IN ('{$deleteidarray}')") ); */
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$hint = array("status"=>"success","message"=>"删除成功");
			}
			echo json_encode($hint);
			exit;	
		}
		
		include $this->template('list');
	}
	
	/*创建或编辑砸蛋活动页面*/
	public function doWebFieldsFormDisplay($rid = 0) {
		
		global $_GPC, $_W, $wpdb;
		$rid = $_GET['id'];
		if (!empty($rid)) {
			$reply = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}egg_reply WHERE id = %d ORDER BY `id` DESC",$rid),ARRAY_A);
			$upload =wp_upload_dir();
			if((empty($reply['picture']))||(stristr($reply['picture'],"http")!==false)){
				$reppicture=$reply['picture'];
			}else{
				$reppicture=$upload['baseurl'].$reply['picture'];
			}
			
			/*处理文章图片显示*/
			$reprule=$this->rule_content($reply['rule']);
			$award = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}egg_award WHERE rid = %d ORDER BY `id` ASC", $rid),ARRAY_A);
			if (!empty($award)) {
				foreach ($award as &$pointer) {
					if (!empty($pointer['activation_code'])) {
						$pointer['activation_code'] = implode("\n", iunserializer($pointer['activation_code']));
					}
				}
			}
		} else {
			$reply = array(
				'periodlottery' => 1,
				'maxlottery' => 1,
			);
		}
		include $this->template('form');
	}
	/*创建或编辑砸蛋活动页面的相关操作*/
	public function doWebEggformsubmit(){
		global $_GPC, $_W, $wpdb;
		include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
		
		$gweid = $_W['gweid'];
		if( isset($_POST['hitcredit']) ){
			
			$id = intval($_POST['reply_id']);
			$delimgid = $_POST['delimgid'];//该值为-1，表示删除图片
			if($delimgid!=-1){
				/*上传图片*/
				//有些手机中的图片没有扩展名导致上传不成功，这里加上jpg类型
				$type =strtolower(strstr($_FILES['file']['name'], '.'));
				if($type == false)
				{
					$_FILES['file']['name'] = $_FILES['file']['name'].".jpg";
					$type = ".jpg";
				}
				$picname = $_FILES['file']['name'];
				$picsize = $_FILES['file']['size'];
				
				if ($picname != "") {
					if ($picsize > 10240000) {
						
						$hint = array("status"=>"success","message"=>"图片大小不能超过10M!");
						echo json_encode($hint);
						exit;
					}
					if ($type != ".gif" && $type != ".jpg"&& $type != ".png" && $type != ".jpeg") {
						
						$hint = array("status"=>"success","message"=>"图片格式不对!");
						echo json_encode($hint);
						exit;	
					} 
					$up=new upphoto();	
					$picUrl=$up->save();
					$path=substr( $picUrl,1);
				}
				$size = round($picsize/1024,2);
				$upload =wp_upload_dir();
				if((empty($picUrl))||(stristr($picUrl,"http")!==false)){
					$echopicurl=$picUrl;
				}else{
					$echopicurl=$upload['baseurl'].$picUrl;
				}
				$arr = array(
					'name'=>$picname,
					'pic'=>$echopicurl,
					'size'=>$size
				);
				//上传图片结束
			}
			
			$rule=stripslashes($_POST['rule']);
			$rule =preg_replace('#(?<=src=(\'|"))(('.home_url().'|(?!\w+://))[\w%\/.\-_]+uploads(?=/))|'.$baseurl.'(?=[\w%\/.\-_]+\")#i','',$rule);
			/*处理入DB的文章图片END*/
			
			$insert = array(
				'gweid' => $_W['gweid'],
				'name' => $_POST['name'],
				'picture' => $picUrl,
				'periodlottery' => intval($_POST['periodlottery']),
				'maxlottery' => intval($_POST['maxlottery']),
				'rule' => $rule,
				'default_tips' => $_POST['default_tips'],
				'hitcredit' => intval($_POST['hitcredit']),
				'misscredit' => intval($_POST['misscredit']),
			);
			if($delimgid!=-1){
				
			}else{    //如果删除了图片
				unset($insert['picture']);
			}
			if (empty($id)) {
				$status = $wpdb->insert($wpdb->prefix.'egg_reply', $insert);
				$id = $wpdb->insert_id; //获取新插入的记录id	
			} else {       //编辑再调试
				$replies = $wpdb->get_row($wpdb->prepare("SELECT id, picture,rule FROM {$wpdb->prefix}egg_reply WHERE id = %d", $id),ARRAY_A);
				if(isset($replies['picture']) && $replies['picture'] != $insert['picture'])
					file_unlink($replies['picture']);
				file_unlink_from_xml_update($replies['rule'],$rule);
				$status = $wpdb->update($wpdb->prefix.'egg_reply', $insert, array('id' => $id));
			}	
			if($status !== false)  //添加或者更新都会执行奖品的更新
			{
				if (!empty($_POST['award-title'])) {
					foreach ($_POST['award-title'] as $index => $title) {
						if (empty($title)) {
							continue;
						}
						$update = array(
							'title' => $title,
							'description' => $_POST['award-description'][$index],
							'probalilty' => $_POST['award-probalilty'][$index],
							'total' => $_POST['award-total'][$index],
							'activation_code' => '',
							'activation_url' => '',
						);
						if (empty($update['inkind']) && !empty($_POST['award-activation-code'][$index])) {
						
						    $aindex=trim($_POST['award-activation-code'][$index]);
							$activationcode = explode("\n", $aindex);
							foreach( $activationcode as $k=>$v){   
								$tv = str_replace(array("\r","\n"),"",$v);
								if(empty($tv)){  
									unset( $activationcode[$k] ); 
								}
							}
							$update['activation_code'] = iserializer($activationcode);
							$update['total'] = count($activationcode);
							$update['activation_url'] = $_POST['award-activation-url'][$index];
						}
						$wpdb->update($wpdb->prefix.'egg_award', $update, array('id' => $index));
					}
				}
				//处理添加
				if (!empty($_POST['award-title-new'])) {
					foreach ($_POST['award-title-new'] as $index => $title) {
						if (empty($title)) {
							continue;
						}
						$insert = array(
							'rid' => $id,
							'title' => $title,
							'description' => $_POST['award-description-new'][$index],
							'probalilty' => $_POST['award-probalilty-new'][$index],
							'inkind' => intval($_POST['award-inkind-new'][$index]),
							'total' => intval($_POST['award-total-new'][$index]),
							'activation_code' => '',
							'activation_url' => '',
						);

						if (empty($insert['inkind'])) {
							$aindex = trim($_POST['award-activation-code-new'][$index]);  //trim去除开头和结尾的空字符或者换行等
							$activationcode = explode("\n", $aindex);
							foreach( $activationcode as $k=>$v){   
								$tv = str_replace(array("\r","\n"),"",$v);
								if(empty($tv)){  
									unset( $activationcode[$k] ); 
								}
							}
							
							$insert['activation_code'] = iserializer($activationcode);
							$insert['total'] = count($activationcode);
							$insert['activation_url'] = $_POST['award-activation-url-new'][$index];
						}
						$wpdb->insert($wpdb->prefix.'egg_award', $insert);
					}
				}

				$hint = array("status"=>"success","message"=>"提交成功","url"=>$this->createWebUrl('eggList',array()));
				echo json_encode($hint);
				exit; 
			}		
			else{
				$hint = array("status"=>"error","message"=>"提交失败");
				echo json_encode($hint);
				exit; 
			}
		}
		include $this->template('');
	}

	public function doWebFieldsFormValidate($rid = 0) {
		return true;
	}

	public function doWebRuleDeleted($rid = 0) {
		global $_W;
		$replies = $wpdb->get_results($wpdb->prepare("SELECT id, picture,rule FROM {$wpdb->prefix}egg_reply WHERE rid = %d", $rid),ARRAY_A);
		$deleteid = array();
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				file_unlink($row['picture']);
				file_unlink_from_xml($row['rule']);
				$deleteid[] = $row['id'];
			}
		}
		$wpdb->delete($wpdb->prefix.'egg_reply', "id IN ('".implode("','", $deleteid)."')");
		return true;
	}
	
	/*
	*公共function
	/
	/*处理文章内容图片显示*/
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
	
	/*是否开启功能权限*/
	function has_module($gweid,$type){
		global $_W,$wpdb;
		$result = $wpdb -> get_results($wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_func_info a WHERE NOT EXISTS(SELECT * FROM wp_wechat_initfunc_info b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = %s AND func_flag = 0) LIMIT 0, 100",$gweid),ARRAY_A);
		foreach($result as $initfunc){
			if($selCheck[$initfunc['func_name']] == 0)
				$selCheck[$initfunc['func_name']] = $initfunc['status'];
		}
		if($type=='wechatvip'){
			if($selCheck['wechatvip']!=1){
				return false;
			}else{
				return true;
			}
		}else{
			if($selCheck['wechatactivity_egg']!=1){
				return false;
			}else{
				return true;
			}
		}
	}


	/*分页设置*/
	function doWebpaginationa_page($tcount, $pindex, $psize = 15, $url = '', $context = array('before' => 5, 'after' => 4),$attach = array(),$remove = array()) {
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
		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}egg WHERE gweid='{$gweid}'",ARRAY_A);
		if(is_array($list))
			foreach($list as $element){
				file_unlink($element['picture']);
				file_unlink_from_xml($element['rule']);
			}
				
	}

}
