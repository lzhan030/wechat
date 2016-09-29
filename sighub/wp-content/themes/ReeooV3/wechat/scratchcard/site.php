<?php
/**
 * 刮刮卡模块
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
class ScratchcardModuleSite extends ModuleSite {
	
	
	/*
	*PC
	/
	/*刮刮卡规则列表*/
	public function doWebScratchcardList(){
		global $_W, $wpdb;
		global $current_user;	
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;		
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$gweid=$_SESSION['GWEID'];
	    
		$search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		
		$search = array(
			'all' => '',
			'id' => "AND id = '{$search_content}'",
			'name' => "AND name = '{$search_content}'"
		);
		
		$sql=$wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}scratchcard where gweid=%s {$search[$search_condition]} ORDER BY id desc",$gweid);
			
		$total = $wpdb->get_var($sql);
		$pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}scratchcard where gweid=%s {$search[$search_condition]} ORDER BY id desc limit {$offset},{$psize}",$gweid);
		$list = $wpdb->get_results($sql,ARRAY_A);
		
		
		//删除刮刮卡活动
		 if(isset($_POST['scratchcard_del']) && !empty($_POST['scratchcard_del']) ){							
			
			$sql = $wpdb -> prepare("SELECT id, picture,rule FROM {$wpdb->prefix}scratchcard where id=%d",$_POST['scradelid']);
			$replies = $wpdb->get_results($sql,ARRAY_A);
			$deleteid = array();
			$upload = wp_upload_dir();
			
			if (!empty($replies)) {
				foreach ($replies as $index => $row) {
					//删除图片
					file_unlink($row['picture']);
					file_unlink_from_xml($row['rule']);
					$deleteid[] = $row['id'];
				}
			}
			
			$how_many = count($deleteid);
			$placeholders = array_fill(0, $how_many, '%d');
			$deleteidarray=implode("','", $deleteid);
			$delete= $wpdb->query("DELETE FROM {$wpdb->prefix}scratchcard WHERE id IN ('{$deleteidarray}')");
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$hint = array("status"=>"success","message"=>"删除成功");
			}
			echo json_encode($hint);
			exit;	
		}
		
		include $this->template('scratchcardlist');
	}
	
	/*创建或编辑某个刮刮卡规则*/
	public function doWebScratchcardEdit() {
		global $_W,$wpdb;
		
		include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
		$gweid = $_W['gweid'];
		
		$id = intval($_GET['reply_id']);
		//编辑
		
		if($_W['ispost']){
			$id = intval($_POST['reply_id']);
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}scratchcard where id=%d ORDER BY id DESC",$id);
			$reply = $wpdb->get_row($sql,ARRAY_A);
			$delimgid=$_POST['delimgid'];//是否更新图片
			if($delimgid!=-1){
				/*上传图片*/
				$type =strtolower(strstr($_FILES['file']['name'], '.'));
				if($type == false){
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
				/*上传图片END*/
			}
			/*处理入DB的文章图片*/
			$upload =wp_upload_dir();
			$baseurl=$upload['baseurl'];
			//$rule=htmlspecialchars_decode($_POST['rule'])	
			$rule=stripslashes($_POST['rule']);
			$rule =preg_replace('#(?<=src=(\'|"))(('.home_url().'|(?!\w+://))[\w%\/.\-_]+uploads(?=/))|'.$baseurl.'(?=[\w%\/.\-_]+\")#i','',$rule);
			/*处理入DB的文章图片END*/
	
			$insert = array(
				'name' => $_POST['name'],
				'gweid' => $_W['gweid'],
				'picture' => $picUrl,
				//'description' => $_POST['description'],删除用于图文的活动简介
				'periodlottery' => intval($_POST['periodlottery']),
				'maxlottery' => intval($_POST['maxlottery']),
				'rule' => $rule,
				'hitcredit' => intval($_POST['hitcredit']),
				'misscredit' => intval($_POST['misscredit']),
			);
			if($delimgid!=-1){
				
			}else{
				unset($insert['picture']);
			}
			if (empty($id)) {
				$status=$wpdb -> insert("{$wpdb->prefix}scratchcard",$insert);
				$id = $wpdb->insert_id;//创建新刮刮卡活动时创建奖品
			}else{
				/*if (!empty($_POST['picture'])) {
					file_delete($_POST['picture-old']);??
				} else {
					unset($insert['picture']);
				}*/

				if(isset($insert['picture']) && $reply['picture'] != $insert['picture'])
					file_unlink($reply['picture']);
				file_unlink_from_xml_update($reply['rule'],$insert['rule']);
				$status=$wpdb->update("{$wpdb -> prefix}scratchcard", $insert, array('id' => $id));
			}
			if($status!==false){
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
						$wpdb->update("{$wpdb -> prefix}scratchcard_award", $update, array('id' => $index));
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
							$aindex=trim($_POST['award-activation-code-new'][$index]);
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
						$wpdb -> insert("{$wpdb->prefix}scratchcard_award",$insert);
					}
				}
				$hint = array("status"=>"success","message"=>"提交成功","url"=>$this->createWebUrl('scratchcardList',array()));
				echo json_encode($hint);
				exit; 
			}else{
				$hint = array("status"=>"error","message"=>"提交失败");
				echo json_encode($hint);
				exit; 			
			}
		}
		
		if (!empty($id)) {
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}scratchcard where id=%d ORDER BY id DESC",$id);
			$reply = $wpdb->get_row($sql,ARRAY_A);
			$upload =wp_upload_dir();
			
			/*处理活动图片的显示*/
			if((empty($reply['picture']))||(stristr($reply['picture'],"http")!==false)){
				$reppicture=$reply['picture'];
			}else{
				$reppicture=$upload['baseurl'].$reply['picture'];
			}
			/*处理文章图片显示*/
			$reprule=$this->rule_content($reply['rule']);
			
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}scratchcard_award where rid=%d ORDER BY id ASC",$id);
			$award = $wpdb->get_results($sql,ARRAY_A);
			if (!empty($award)) {
				foreach ($award as &$pointer) {
					if (!empty($pointer['activation_code'])) {
						$pointer['activation_code'] = implode("\n", iunserializer($pointer['activation_code']));
					}
				}
			}
		}else{//新建
			$reply = array(
				'periodlottery' => 1,
				'maxlottery' => 1,
			);
		}
		include $this->template('scratchcardedit');
	}
	
	
	
	/*刮刮卡对应的中奖记录*/
	public function doWebAwardlist() {
		global $_W, $wpdb;
		$id = intval($_GET['id']);
		if (checksubmit('delete')) {
			$deleteidarray=implode("','", $_POST['select']);
			$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}scratchcard_winner WHERE id IN ('{$deleteidarray}')") );
			message('删除成功！', $this->createWebUrl('awardlist', array('id' => $id, 'page' => $_GET['page'])));
		}
		if (!empty($_GET['wid'])) {
			$wid = intval($_GET['wid']);
			$update=$wpdb->update( $wpdb->prefix.'scratchcard_winner', array('status'=>intval($_GET['status'])),array('id'=>$wid),array('%d'),array('%s'));
			
			/*确认取消领奖，更新库存*/
			//读奖品id
			$awardid= $wpdb->get_var($wpdb -> prepare("SELECT aid FROM {$wpdb->prefix}scratchcard_winner where id=%s",$wid));
			//读total_status
			$sql = $wpdb -> prepare("SELECT aid_status FROM {$wpdb->prefix}scratchcard_winner where id=%s",$wid);
			$aid_status = $wpdb->get_var($sql);
			$message="确认领奖成功!";
			if($aid_status==1){//是否更新库存标识
				//加库存
				if(intval($_GET['status'])==0){
					$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}scratchcard_award set total=total+1 WHERE id = %s",$awardid));
					$message="取消领奖成功!";
				}
				//减库存
				if(intval($_GET['status'])==1){
					$totalzero=$wpdb->get_var($wpdb -> prepare("SELECT total FROM {$wpdb->prefix}scratchcard_award where id=%s",$awardid));
					if(intval($totalzero)>0){
						$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}scratchcard_award set total=total-1 WHERE id = %s",$awardid));
					}
					$message="确认领奖成功!";
				}
			}else{
				$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}scratchcard_winner set aid_status=1 WHERE id = %s",$wid));
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
			//$_GET['isregister'] = 1;
			$_GET['isregister'] = "";
		}
		$where .= $condition['isregister'][$_GET['isregister']];
		if (!isset($_GET['isaward'])) {
			//$_GET['isaward'] = 1;
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
		$list = $wpdb -> get_results($wpdb -> prepare("SELECT a.id, a.award, a.description, a.status, a.createtime, b.realname, b.mobilenumber FROM {$wpdb->prefix}scratchcard_winner AS a
				LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid WHERE a.rid = %s AND a.award <> '' {$where} ORDER BY a.createtime DESC, a.status ASC LIMIT {$offset},{$psize}",$id),ARRAY_A);
		if (!empty($list)) {
			$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}scratchcard_winner  AS a LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid WHERE a.rid = %s AND a.award <> '' {$where}",$id);
			$total = $wpdb->get_var($sql);
			$pager = pagination($total, $pindex, $psize);
		}
		include $this->template('awardlist');
	}
	
	
	public function doWebfieldsFormValidate($rid = 0) {
		return true;
	}
	
	public function getHomeTiles() {
		global $_W, $wpdb;
		//GET信息
		$gweid = $_GET['gweid'];
		$urls = array();
		$list = $wpdb -> get_results($wpdb -> prepare("SELECT name, id FROM {$wpdb->prefix}rule WHERE gweid = %s AND module = 'scratchcard'",$gweid),ARRAY_A);
		if (!empty($list)) {
			foreach ($list as $row) {
				$urls[] = array('title'=>$row['name'], 'url'=> $this->createMobileUrl('lottery', array('id' => $row['id'])));
			}
		}
		return $urls;
	}

	public function doWebFormDisplay() {
		global $_W, $wpdb;
		$result = array('error' => 0, 'message' => '', 'content' => '');
		$result['content']['id'] = $GLOBALS['id'] = 'add-row-news-'.$_W['timestamp'];
		$result['content']['html'] = $this->template('item', TEMPLATE_FETCH);
		exit(json_encode($result));
	}

	
	/*删除奖品*/
	public function doWebDelete() {
		global $_W, $wpdb;
		$id = intval($_GET['id']);
		$sql = $wpdb -> prepare("SELECT id FROM {$wpdb->prefix}scratchcard_award WHERE  id=%d",$id);
		$row = $wpdb->get_row($sql,ARRAY_A);
		
		if (empty($row)) {
			message('抱歉，奖品不存在或是已经被删除！', '', 'error');
		}
		$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}scratchcard_award WHERE id =%d",$id ) );
		if ($delete!==FALSE) {
			message('删除奖品成功', '', 'success');
		}
	}

	
	/*
	*Mobile
	/
	
	/*手机端抽奖页面*/
	public function doMobileLottery() {
		global $_W, $wpdb;
		$title = '刮刮卡';
		
		/*from_user拿mid?*/
		
		$gweid=$_W['gweid'];
		$mid=$_W['fans']['mid'];
		$id = intval($_GET['id']);
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		
		/*判断功能是否可用*/
		if(!$this->has_module($gweid,'wechatactivity_scratch')){
			message('刮刮卡功能已经关闭！');
		}else{

			if(!$this->has_module($gweidv,'wechatvip')){
				message('没有开启会员权限,无法使用该功能！');
			}
			if($this->has_module($gweidv,'wechatvip') && empty($mid)){
				header("Location: ".home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$gweidv.'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]).'&alert='.urlencode('登录后才能使用刮刮卡，请先登录。'));
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
		
		$sql = $wpdb -> prepare("SELECT id, picture, periodlottery, maxlottery, rule, hitcredit, misscredit FROM {$wpdb->prefix}scratchcard WHERE id =%d LIMIT 1",$id);
		$scratchcard = $wpdb->get_row($sql,ARRAY_A);
		if (empty($scratchcard)) {
			message('页面已经失效！');
		}
		/*判断功能是否可用END*/
		
		
		/*处理活动图片的显示*/
		$upload =wp_upload_dir();
		if((empty($scratchcard['picture']))||(stristr($scratchcard['picture'],"http")!==false)){
			$scrpicture=$scratchcard['picture'];
		}else{
			$scrpicture=$upload['baseurl'].$scratchcard['picture'];
		}
		/*处理活动图片的显示END*/
		
		/*处理文章图片的显示*/
		$scrrule=$this->rule_content($scratchcard['rule']);
		
		$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}scratchcard_winner  WHERE createtime > %s AND mid = %s AND status <> 3 AND award <> '' AND rid=%d",strtotime(date('Y-m-d')),$mid,$id);
		$total = $wpdb->get_var($sql);
		/*$member = fans_search($fromuser);*/
		/*winner中取出用户某个刮刮卡的中奖情况*/
		$myaward = $wpdb -> get_results($wpdb -> prepare("SELECT id, award, description, status FROM {$wpdb->prefix}scratchcard_winner WHERE mid = %s AND aid != '0' AND award <> '' AND rid = %s ORDER BY createtime DESC",$mid,$id),ARRAY_A);
		$mycredit = $wpdb -> get_results($wpdb -> prepare("SELECT description FROM {$wpdb->prefix}scratchcard_winner  WHERE mid = %s AND aid = '0' AND award <> '' AND rid = %s ORDER BY createtime DESC",$mid,$id),ARRAY_A);
		foreach($mycredit as $v => $k) {
			$_mycredit[] = $k['description'];
		}
		if(!empty($_mycredit)){
			$mycredit = array_sum($_mycredit);
		}
		$mycredit = (!empty($mycredit)) ? $mycredit : '0';
		$allaward = $wpdb -> get_results($wpdb -> prepare("SELECT id, title, probalilty, description, inkind FROM {$wpdb->prefix}scratchcard_award WHERE rid = %s ORDER BY id ASC",$id),ARRAY_A);
		
		//过期
		if (!empty($scratchcard['periodlottery'])) {
			$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}scratchcard_winner  WHERE createtime > %s AND mid = %s AND status <> 3 AND rid=%d",strtotime(date('Y-m-d')),$mid,$id);
			$total = $wpdb->get_var($sql);
			$sql = $wpdb -> prepare("SELECT createtime FROM {$wpdb->prefix}scratchcard_winner WHERE mid = %s AND status <> 3 AND rid=%d ORDER BY createtime DESC ",$mid,$id);
			$lastdate = $wpdb->get_var($sql);
			if (($total >= intval($scratchcard['maxlottery'])) && strtotime(date('Y-m-d')) < strtotime(date('Y-m-d', $lastdate)) + $scratchcard['periodlottery'] * 86400) {
				$message = '您还未到达可以再次抽奖的时间<br>下次可抽奖时间为：'.date('Y-m-d', strtotime(date('Y-m-d', $lastdate)) + $scratchcard['periodlottery'] * 86400);
			}
		} else {
			$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}scratchcard_winner WHERE createtime > %s AND mid = %s AND status <> 3 AND rid=%d",strtotime(date('Y-m-d')),$mid,$id);
			$total = $wpdb->get_var($sql);
			if (!empty($scratchcard['maxlottery']) && $total >= $scratchcard['maxlottery']) {
				$message = $scratchcard['periodlottery'] ? '您已经超过当日抽奖次数' : '您已经超过最大抽奖次数';
			}
		}
		include $this->template('lottery');
	}

	public function doMobileGetAward() {
		global $_W, $wpdb;
		
		$gweid=$_W['gweid'];
		$mid=$_W['fans']['mid'];
		$id = intval($_GET['id']);
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		
		/*进入抽奖页面后，管理员禁止了应用或禁止了会员处理*/
		if($this->has_module($gweid,'wechatactivity_scratch')){

			if($this->has_module($gweidv,'wechatvip') && empty($mid)){
				$url=home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$gweidv.'&redirect_url='.urlencode($this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid))).'&alert='.urlencode('登录后才能使用刮刮卡，请先登录。');
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

			$sql = $wpdb -> prepare("SELECT id, periodlottery, maxlottery, misscredit, hitcredit FROM {$wpdb->prefix}scratchcard WHERE id =%d LIMIT 1",$id);
			$scratchcard = $wpdb->get_row($sql,ARRAY_A);
			if (empty($scratchcard)) {
				$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
				$result = array('status' => -2, 'message' => '该页面已经失效！','url'=>$url);
				message($result, '', 'ajax');
			}
		}else{
			$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
			$result = array('status' => -2, 'message' => '刮刮卡活动已经关闭！','url'=>$url);
			message($result, '', 'ajax');
		}
		/*进入抽奖页面后，管理员禁止了应用或禁止了会员处理END*/
		
		$result = array('status' => -1, 'message' => '');
		if (!empty($scratchcard['periodlottery'])) {
			$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}scratchcard_winner  WHERE createtime > %s AND mid = %s AND status <> 3 AND rid=%d",strtotime(date('Y-m-d')),$mid,$id);
			$total = $wpdb->get_var($sql);
			$sql = $wpdb -> prepare("SELECT createtime FROM {$wpdb->prefix}scratchcard_winner WHERE mid = %s AND status <> 3 AND rid=%d ORDER BY createtime DESC",$mid,$id);
			$lastdate = $wpdb->get_var($sql);
			
			if (($total >= intval($scratchcard['maxlottery'])) && strtotime(date('Y-m-d')) < strtotime(date('Y-m-d', $lastdate)) + $scratchcard['periodlottery'] * 86400) {
				$result['message'] = '您还未到达可以再次抽奖的时间。下次可抽奖时间为'.date('Y-m-d', strtotime(date('Y-m-d', $lastdate)) + $scratchcard['periodlottery'] * 86400);
				message($result, '', 'ajax');
			}
		} else {
			$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}scratchcard_winner WHERE createtime > %s AND mid = %s AND status <> 3 AND rid=%d",strtotime(date('Y-m-d')),$mid,$id);
			$total = $wpdb->get_var($sql);
			if (!empty($scratchcard['maxlottery']) && $total >= $scratchcard['maxlottery']) {
				$result['message'] = $scratchcard['periodlottery'] ? '您已经超过当日抽奖次数' : '您已经超过最大抽奖次数';
				message($result, '', 'ajax');
			}
		}
		$gifts = $wpdb -> get_results($wpdb -> prepare("SELECT id, probalilty, inkind FROM {$wpdb->prefix}scratchcard_award   WHERE rid = %s ORDER BY probalilty ASC",$id),ARRAY_A);
		
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
		$result['message'] = '很遗憾，您没能中奖！';
		
		$data = array(
			'rid' => $id,
			'from_user' => $fromuser,
			'mid' => $mid,
			'status' => empty($gift['inkind']) ? 2 : 0,
			'createtime' => TIMESTAMP,
		);
		$credit = array(
			'rid' => $id,
			'award' =>  (empty($awardid) ? '未' : ''). '中奖积分',
			'from_user' => $fromuser,
			'mid' => $mid,
			'status' => 3,
			'description' => (empty($awardid) ? $scratchcard['misscredit'] : $scratchcard['hitcredit']),
			'createtime' => TIMESTAMP,
		);
		if (!empty($awardid)) {
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}scratchcard_award WHERE rid = %s AND id = %s",$id,$awardid);
			$gift = $wpdb->get_row($sql,ARRAY_A);
			if ($gift['total'] > 0) {
				$data['award'] = $gift['title'];
				if (!empty($gift['inkind'])) {
					$data['description'] = $gift['description'];
					$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}scratchcard_award set total=total-1 WHERE rid = %s AND id = %s",$id,$awardid));
				} else {
					$gift['activation_code'] = iunserializer($gift['activation_code']);
					$code = array_pop($gift['activation_code']);
					$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}scratchcard_award set total=total-1, activation_code=%s WHERE rid = %s AND id = %s",iserializer($gift['activation_code']),$id,$awardid));
					$data['description'] = '兑换码：' . $code . (empty($gift['activation_url']) ? '' : '<br>兑换方式：' . $gift['activation_url']);
				}
				$result['message'] = '恭喜您，得到“'.$data['award'].'”！' ;
				$result['status'] = 0;
			} else {
				$credit['description'] = $scratchcard['misscredit'];
				$credit['award'] = '未中奖积分';
			}
		}
		!empty($credit['description']) && $result['message'] .= '<br />' . $credit['award'] . '：'. $credit['description'];
		$data['aid'] = $gift['id'];
		if (!empty($credit['description'])) {
			$wpdb -> insert("{$wpdb -> prefix}scratchcard_winner",$credit);
			$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}wechat_member set point=point+{$credit['description']} WHERE mid = %s",$mid));
		}
		$wpdb -> insert("{$wpdb -> prefix}scratchcard_winner",$data);
		
		$myawardarray = $wpdb -> get_results($wpdb -> prepare("SELECT id, award, description, status FROM {$wpdb->prefix}scratchcard_winner WHERE mid = %s AND aid != '0' AND award <> '' AND rid = %s ORDER BY createtime DESC",$mid,$id),ARRAY_A);
		$result['myaward'] = $myawardarray ;
		$mycredit = $wpdb -> get_results($wpdb -> prepare("SELECT description FROM {$wpdb->prefix}scratchcard_winner WHERE mid = %s AND aid = '0' AND award <> '' AND rid = %s ORDER BY createtime DESC",$mid,$id),ARRAY_A);
		
		foreach($mycredit as $v => $k) {
			$_mycredit[] = $k['description'];
		}
		if(!empty($_mycredit)){
			$result['credit'] = array_sum($_mycredit);
		}
		$result['credit'] = (!empty($result['credit'])) ? $result['credit'] : '0';
		message($result, '', 'ajax');
	}

	public function doMobileSetStatus() {
		global $_W, $wpdb;
		/*if (empty($_W['fans']['from_user'])) {
			message('非法访问，请重新发送消息进入抽奖页面！');
		}
		$fromuser = $_W['fans']['from_user'];*/
		
		$gweid=$_W['gweid'];
		$mid=$_W['fans']['mid'];
		$id = intval($_GET['id']);
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		
		/*进入抽奖页面后，管理员禁止了应用或禁止了会员处理*/
		if($this->has_module($gweid,'wechatactivity_scratch')){

			if($this->has_module($gweidv,'wechatvip') && empty($mid)){
				$url=home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$gweidv.'&redirect_url='.urlencode($this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid))).'&alert='.urlencode('登录后才能使用刮刮卡，请先登录。');
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
			
			$sql = $wpdb -> prepare("SELECT id, periodlottery, maxlottery, misscredit, hitcredit FROM {$wpdb->prefix}scratchcard WHERE id =%d LIMIT 1",$id);
			$scratchcard = $wpdb->get_row($sql,ARRAY_A);
			if (empty($scratchcard)) {
				$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
				$result = array('status' => -2, 'message' => '该页面已经失效！','url'=>$url);
				message($result, '', 'ajax');
			}
		}else{
			$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
			$result = array('status' => -2, 'message' => '刮刮卡活动已经关闭！','url'=>$url);
			message($result, '', 'ajax');
		}
		/*进入抽奖页面后，管理员禁止了应用或禁止了会员处理END*/
		
		$data = array(
			'status' => 2,
		);
		$wpdb->update("{$wpdb -> prefix}scratchcard_winner", $data, array('id' => $_GET['awardid']));
		message($data, '', 'ajax');
	}
	
	/*
	*公共function
	*/
	function filter($var){
        if($var == ''){
            return false;
        }
		return true;
	}
	
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
			if($selCheck['wechatactivity_scratch']!=1){
				return false;
			}else{
				return true;
			}
		}
	}

	/*该公众号是否处于开启共享状态的分组管理员对应的虚拟号下*/
	function virtualgweid_open($gweid){
		global $_W,$wpdb;
		//20150417 sara new added
		//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
		$getgroupids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_group w left join {$wpdb->prefix}user_group u on w.user_id = u.user_id where w.GWEID=".$gweid,ARRAY_A);
		//obtain the groupid
		if(!empty($getgroupids)){
			foreach ($getgroupids as $getgroupid) {
				$gid = $getgroupid['group_id'];
			}
			if(!empty($gid)){
				$getflags = $wpdb->get_results("SELECT count(*) as flagcount FROM {$wpdb->prefix}user_group u where u.group_id=".$gid." and u.flag = 1",ARRAY_A);
				//obtain the groupid
				foreach ($getflags as $getflag) {
					$flagcount = $getflag['flagcount'];
				}
				//如果有分组管理员,查看分组中的虚拟号是否是开启状态
				if($flagcount != 0){
					$getvgweids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_group u left join {$wpdb->prefix}wechat_group w on u.user_id=w.user_id where u.flag = 1 and w.adminshare_flag = 1",ARRAY_A);
					//obtain the groupid
					if(!empty($getvgweids)){
						foreach ($getvgweids as $getvgweid) {
							$vgweid = $getvgweid['GWEID'];
						}
						$gweid = $vgweid;   //将虚拟号的gweid赋过来
					}
				}
			}
		}
		return $gweid;
		//20150417new end
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

	//当前用户有可能是分组管理员下的，如果分组管理员下的切换，需要找到对应的session中的值
	function site_issuperadmin($currentuserid){
	   	global $_W,$wpdb;
		$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$currentuserid);
		if(!empty($getgroupuserids)){
			foreach($getgroupuserids as $getgroupinfo)
			{
			    $usergroupid = $getgroupinfo -> group_id;
			    $usergroupflag = $getgroupinfo -> flag;
			}
		}else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
			$usergroupid = 0;
			$usergroupflag = 0;
		}
		//如果是分组管理员
		if($usergroupid !=0 && $usergroupflag == 1){
			$groupadminflag = 1;
		}else{
			$groupadminflag = 0;
		}

		return $groupadminflag;
	}

	function onWechatAccountDelete($gweid){
		global $wpdb;
		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}scratchcard WHERE gweid='{$gweid}'",ARRAY_A);
		if(is_array($list))
			foreach($list as $element){
				file_unlink($element['picture']);
				file_unlink_from_xml($element['rule']);

			}
				
	}

}
