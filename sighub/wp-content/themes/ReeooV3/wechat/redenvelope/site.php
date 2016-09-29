<?php
/**
 * 红包模块
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
class RedenvelopeModuleSite extends ModuleSite {
	
	
	/*红包规则列表*/
	public function doWebList(){
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
			'name' => "AND name LIKE '%%{$search_content}%%'"
		);
		
		$sql=$wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}redenvelope where gweid=%s {$search[$search_condition]} ORDER BY id desc",$gweid);
			
		$total = $wpdb->get_var($sql);
		$pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}redenvelope where gweid=%s {$search[$search_condition]} ORDER BY id desc limit {$offset},{$psize}",$gweid);
		$list = $wpdb->get_results($sql,ARRAY_A);
		
		
		//删除红包活动
		 if(isset($_POST['redenvelope_del']) && !empty($_POST['redenvelope_del']) ){							
			
			$sql = $wpdb -> prepare("SELECT id, redenvelope_image,redenvelope_bacimage,rule FROM {$wpdb->prefix}redenvelope where id=%d",$_POST['scradelid']);
			$replies = $wpdb->get_row($sql,ARRAY_A);

			file_unlink($replies['redenvelope_image']);
			file_unlink($replies['redenvelope_bacimage']);
            file_unlink_from_xml($replies['rule']);
            $deleteid = array($row['id']);
			$upload = wp_upload_dir();
			$how_many = count($deleteid);
			$placeholders = array_fill(0, $how_many, '%d');
			$deleteidarray=implode("','", $deleteid);
			$delete= $wpdb->query( "DELETE FROM {$wpdb->prefix}redenvelope WHERE id IN ('{$deleteidarray}')");
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
	
	/*创建或编辑某个红包规则*/
	public function doWebEdit() {
		global $_W,$wpdb;
		
		include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
		$gweid = $_W['gweid'];
		if (!empty($id)) {
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}redenvelope where id=%d ORDER BY id DESC",$id);
			$reply = $wpdb->get_row($sql,ARRAY_A);
		}
		$id = intval($_GET['id']);
		if($_W['ispost']){
			$delimgid=$_POST['delimgid'];//是否更新图片
			$bacdelimgid=$_POST['bacdelimgid'];//是否更新图片
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
					$picUrl=$up->save($_FILES['file']);
				}
				/*上传图片END*/
			}
			if($bacdelimgid!=-1){
				/*上传图片*/
				$type =strtolower(strstr($_FILES['bacfile']['name'], '.'));
				if($type == false){
					$_FILES['bacfile']['name'] = $_FILES['bacfile']['name'].".jpg";
					$type = ".jpg";
				}
				$picname = $_FILES['bacfile']['name'];
				$picsize = $_FILES['bacfile']['size'];
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
					$bacpicUrl=$up->save($_FILES['bacfile']);
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
			$code = array();
			if($_POST['code_type'] == 1){
				$code['type'] = 1;
			}else{
				$code['type'] = 0;
				$codelistarray = explode("\n", trim($_POST['codelist']));
				foreach( $codelistarray as $k=>$v){   
					$tv = str_replace(array("\r","\n"),"",$v);
					if($tv==""){  
						unset( $codelistarray[$k] ); 
					}
				}
				$code['codelist']=$codelistarray;
			}
			if($_POST['expirestatus']==0){
				$reexpire=$_POST['expire'];
				$absreexpire="";
			}else{
				$reexpire=0;
				$absreexpire=$_POST['absexpire'];
				
			}
			if($_POST['fixstatus']==0){
				$remin_maount=intval($_POST['min_amount']);
				$remax_maount= intval($_POST['max_amount']);
			}else{
				$remin_maount=1;
				$remax_maount= intval($_POST['fix_max_amount']);
			}
			
			$insert = array(
				'name' => $_POST['name'],
				'gweid' => $_W['gweid'],
				'redenvelope_image' => $picUrl,
				'redenvelope_bacimage' => $bacpicUrl,
				'description' => $_POST['description'],
				'isrelative' => $_POST['expirestatus'],
				'startdate' => $_POST['startdate'],
				'expire' => intval($reexpire),
				'absexpire' => intval(strtotime($absreexpire)),
				'isfixamount' => $_POST['fixstatus'],
				'periodlottery' => intval($_POST['periodlottery']),
				'maxlottery' => intval($_POST['maxlottery']),
				'rule' => $rule,
				'hitcredit' => intval($_POST['hitcredit']),
				'misscredit' => intval($_POST['misscredit']),
				'amount' => intval($_POST['amount']),
				'total_amount' => intval($_POST['amount']),
				'total' => intval($_POST['total']),
				'min_amount' => $remin_maount,
				'max_amount' =>$remax_maount,
				'probalilty' => $_POST['probalilty'],
				'code_description' => $_POST['code_description'],
				'code' => serialize($code)
				
			);
			if($delimgid==-1)
				unset($insert['redenvelope_image']);
			if($bacdelimgid==-1)
				unset($insert['redenvelope_bacimage']);
			if (empty($id)) {
				$status=$wpdb -> insert("{$wpdb->prefix}redenvelope",$insert);
				$id = $wpdb->insert_id;//创建新红包活动时创建奖品
			}else{
				if(isset($insert['redenvelope_image']) && $reply['redenvelope_image']!= $insert['redenvelope_image'])
                    file_unlink($reply['redenvelope_image']);
                if(isset($insert['redenvelope_bacimage']) && $reply['redenvelope_bacimage']!= $insert['redenvelope_bacimage'])
                    file_unlink($reply['redenvelope_bacimage']);
                file_unlink_from_xml_update($reply['rule'],$rule);
                $status=$wpdb->update("{$wpdb -> prefix}redenvelope", $insert, array('id' => $id));
			}
				
			
			if($status!==false){
				$hint = array("status"=>"success","message"=>"提交成功","url"=>$this->createWebUrl('list',array()));
				echo json_encode($hint);
				exit; 
			}else{
				$hint = array("status"=>"error","message"=>"提交失败");
				echo json_encode($hint);
				exit; 			
			}
		}
		//编辑
		if (!empty($id)) {
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}redenvelope where id=%d ORDER BY id DESC",$id);
			$reply = $wpdb->get_row($sql,ARRAY_A);
			$upload =wp_upload_dir();
			
			$code = unserialize($reply['code']);
			
			if(is_array($code) && !$code['type'] && isset($code['codelist']))
				$code['codelist'] = implode("\n", $code['codelist']);
			
			/*处理已经中奖的验证码与商家再次输入时不能有重复*/
			$sql = $wpdb -> prepare("SELECT code FROM {$wpdb->prefix}redenvelope_winner where rid=%d and code <> ''",$id);
			$scodearray = $wpdb->get_results($sql,ARRAY_A);	
			$codedata = array();
			if(is_array($scodearray) && !empty($scodearray)){
				foreach($scodearray as $element){
					$codedata[]= $element['code'];
				}
			}
			//过期未领金额
			$sql = $wpdb -> prepare("SELECT SUM(amount) as samount FROM {$wpdb->prefix}redenvelope_winner where rid=%d and status=0 and winexpire!=0 and winexpire!='' and code!='' and ".strtotime(date('Y-m-d'))." > winexpire",$id);
			$samount = $wpdb->get_var($sql);
			
			/*处理活动图片的显示*/
			if((empty($reply['redenvelope_image']))||(stristr($reply['redenvelope_image'],"http")!==false)){
				$reppicture=$reply['redenvelope_image'];
			}else{
				$reppicture=$upload['baseurl'].$reply['redenvelope_image'];
			}
			if((empty($reply['redenvelope_bacimage']))||(stristr($reply['redenvelope_bacimage'],"http")!==false)){
				$bacreppicture=$reply['redenvelope_bacimage'];
			}else{
				$bacreppicture=$upload['baseurl'].$reply['redenvelope_bacimage'];
			}
			/*处理文章图片显示*/
			$reprule=$this->rule_content($reply['rule']);

		}else{//新建
			$reply = array(
				'periodlottery' => 1,
				'maxlottery' => 1,
				'amount'=>0,
				'total'=>'',
				'probalilty'=>0,
			);
		}
		
		include $this->template('edit');
	}
	
	//导入兑换码
	public function doWebUploadcode(){
		error_reporting(E_ERROR);
	
		require_once MODULES_DIR.$this -> module['name'].'/upload/PHPExcel.php';
		require_once MODULES_DIR.$this -> module['name'].'/upload/PHPExcel/IOFactory.php';
		require_once MODULES_DIR.$this -> module['name'].'/upload/PHPExcel/Reader/Excel5.php';

		$filename = $_FILES['inputExcel']['name'];
		$tmp_name = $_FILES['inputExcel']['tmp_name'];
		
		//判断上传文件的后缀名
		$extstring = substr($filename, strrpos($filename, ".")+1, strlen($filename)-strrpos($filename, "."));
		
		if($extstring === "xls"){
		   $objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format 
		}elseif($extstring === "xlsx"){
		   $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format 
		}else{
			echo "导入失败，格式有误";
			exit;
		}
		
		$objPHPExcel = $objReader->load($tmp_name); //$filename可以是上传的文件，或者是指定的文件
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		
		$codeexcel = array();
		for($j=1;$j<=$highestRow;$j++){
			$codeexcel[] = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取A列的值
		}
		echo json_encode($codeexcel);
	}
	
	public function doWebAutoGenCode() {
		$count=intval($_POST['count']);
		$length=intval($_POST['length']);
		
		$codearray = array();
		if($count!=0&&$length!=0){
			for($i=0;$i<$count;$i++){
				$codearray[]=$this->random($length);
			}
		}
		echo json_encode($codearray);
	}

	function random($length, $numeric = FALSE) {
		$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
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

	/*红包对应的中奖记录*/
	public function doWebAwardlist() {
		global $_W, $wpdb;
		$id = intval($_GET['id']);
		
		//显示剩余红包总金额
		$sql = $wpdb -> prepare("SELECT amount FROM {$wpdb->prefix}redenvelope where id=%d ORDER BY id DESC",$id);
		$amount = $wpdb->get_var($sql);
		//显示过期未兑奖金额
		$sql = $wpdb -> prepare("SELECT SUM(amount) as samount FROM {$wpdb->prefix}redenvelope_winner where rid=%d and status=0 and winexpire!=0 and winexpire!='' and code!='' and ".strtotime(date('Y-m-d'))." > winexpire",$id);
		$samount = $wpdb->get_var($sql);
		

		if (checksubmit('delete')) {
			$deleteidarray=implode("','", $_POST['select']);
			$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}redenvelope_winner WHERE id IN ('{$deleteidarray}')") );
			message('删除成功！', $this->createWebUrl('awardlist', array('id' => $id, 'page' => $_GET['page'])));
		}
		
		$pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$where = '';
		$starttime = !empty($_GET['start']) ? strtotime($_GET['start']) : strtotime(date("Y-m-d", TIMESTAMP));
		$endtime = !empty($_GET['end']) ? strtotime($_GET['end'])+86400 - 1 : strtotime(date("Y-m-d", TIMESTAMP));
		if (!empty($starttime) && $starttime == $endtime) {
			$endtime = $endtime + 86400 - 1;
		}
		$awardvalue=$_GET['awardvalue'];
		if($_GET['awardvalue']=='未中奖'){
			$awardvalue="";
		}
		$condition = array(
			'isregister' => array(
				'',
				" AND b.realname <> ''",
				" AND b.realname = ''",
			),
			'isstatus' => array(
				" AND a.status = '0'",
				" AND a.status = '1'",
				'',
			),
			/*'isexpire' => array(
				" AND a.winexpire='' or a.winexpire=0 or a.code=''",
				" AND ".strtotime(date('Y-m-d'))." < a.winexpire and a.winexpire!='' and a.winexpire!=0 and a.code!=''",
				" AND ".strtotime(date('Y-m-d'))." > a.winexpire",
				'',
			),*/
			'mobilenumber' => " AND b.mobilenumber ='{$_GET['profilevalue']}'",
			'realname' => " AND b.realname ='{$_GET['profilevalue']}'",
			'code' => " AND a.code = '{$awardvalue}'",
			'starttime' => " AND a.createtime >= '$starttime'",
			'endtime' => " AND a.createtime <= '$endtime'",
		);
		//isregister
		if (!isset($_GET['isregister'])) {
			$_GET['isregister'] = "";
		}
		$where .= $condition['isregister'][$_GET['isregister']];
		
		//isaward
		if (!isset($_GET['isaward'])) {
			$_GET['isaward'] = "";
		}
		$where .= $condition['isaward'][$_GET['isaward']];
		
		//profile
		if (!empty($_GET['profile'])) {
			$where .= $condition[$_GET['profile']];
		}
		
		//award
		if (!empty($_GET['award'])) {
			$where .= $condition[$_GET['award']];
		}
		
		//isstatus
		if (!isset($_GET['isstatus'])) {
			$_GET['isstatus'] = 3;
		}
		$where .= $condition['isstatus'][$_GET['isstatus']];
		
		/*//isexpire
		if (!isset($_GET['isexpire'])) {
			$_GET['isexpire'] = 3;
		}
		$where .= $condition['isexpire'][$_GET['isexpire']];
		*/
		if (!empty($starttime)) {
			$where .= $condition['starttime'];
		}
		if (!empty($endtime)) {
			$where .= $condition['endtime'];
		}
		$offset=($pindex - 1) * $psize;
		
		
		
		$list = $wpdb -> get_results($wpdb -> prepare("SELECT a.id, a.code, a.amount, a.credit, a.winexpire, a.description, a.status, a.createtime,b.realname, b.mobilenumber FROM {$wpdb->prefix}redenvelope_winner AS a
				LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid WHERE a.rid = %s {$where} ORDER BY a.createtime DESC, a.status ASC LIMIT {$offset},{$psize}",$id),ARRAY_A);
		if (!empty($list)) {
			$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}redenvelope_winner  AS a LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid WHERE a.rid = %s {$where}",$id);
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
		$list = $wpdb -> get_results($wpdb -> prepare("SELECT name, id FROM {$wpdb->prefix}rule WHERE gweid = %s AND module = 'redenvelope'",$gweid),ARRAY_A);
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
		$sql = $wpdb -> prepare("SELECT id FROM {$wpdb->prefix}redenvelope_award WHERE  id=%d",$id);
		$row = $wpdb->get_row($sql,ARRAY_A);
		
		if (empty($row)) {
			message('抱歉，奖品不存在或是已经被删除！', '', 'error');
		}
		$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}redenvelope_award WHERE id =%d",$id ) );
		if ($delete!==FALSE) {
			message('删除奖品成功', '', 'success');
		}
	}
	
	
	//商家查看兑换码兑换的结果
	public function doMobileExchangecode() {   //去掉session
		global $_W, $wpdb;
		$id = intval($_GET['id']);
		
		//提交兑换码
		if( isset($_POST['exchangecode_submit']) ){	
			$exchangecode = $_POST['exchangecode'];
			$id = $_POST['id'];	
			
			//判断该兑换码是否被领取
			//$sql = $wpdb -> prepare("SELECT count(*) as redcount FROM {$wpdb->prefix}redenvelope_winner WHERE rid = %d and codevalue = %s", $id, $exchangecode);
			$sql = $wpdb -> prepare("SELECT count(*) as redcount FROM {$wpdb->prefix}redenvelope_winner WHERE rid = %d and code = binary %s", $id, $exchangecode);
			//echo "SELECT * FROM {$wpdb->prefix}redenvelope_winner WHERE rid = {$id} and codevalue = '{$exchangecode}'";
			$countredenvlopes = $wpdb->get_results($sql);
			foreach($countredenvlopes as $countredenvlope)
			{
				$redcount = $countredenvlope -> redcount;
			}
		
			if($redcount == 0){   //没有该兑换码的信息
				$hint = array("status"=>"errorcode","message"=>"您输入的的兑换码有误，请重新输入");
				echo json_encode($hint);
				exit;
			}else{     //有该兑换码的信息，先判断是否已领取过
				$sql = $wpdb -> prepare("SELECT  *, b.nickname FROM {$wpdb->prefix}redenvelope_winner AS a
				LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid where a.rid = %d AND a.code = binary %s AND a.status = 0 and (a.winexpire = 0 OR a.winexpire = '' OR ".strtotime(date('Y-m-d'))." <= a.winexpire)", $id, $exchangecode);
				$redenvelopearray1s = $wpdb->get_row($sql,ARRAY_A); //未领未过期
				//$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}redenvelope_winner where status=1 and code!='' ");
				$sql = $wpdb -> prepare("SELECT  *, b.nickname FROM {$wpdb->prefix}redenvelope_winner AS a
				LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid where a.rid = %d AND a.code = binary %s AND a.status = 1", $id, $exchangecode);
				$redenvelopearray2s = $wpdb->get_row($sql,ARRAY_A);//已领
				$sql = $wpdb -> prepare("SELECT  *, b.nickname FROM {$wpdb->prefix}redenvelope_winner AS a
				LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid where a.rid = %d AND a.code = binary %s AND a.status = 0 and (a.winexpire = 0 OR a.winexpire = '' OR ".strtotime(date('Y-m-d'))." > a.winexpire)", $id, $exchangecode);
				$redenvelopearray3s = $wpdb->get_row($sql,ARRAY_A);//未领已过期
				
				if(!empty($redenvelopearray1s)){  //可领未过期
					$amount = $redenvelopearray1s['amount'];
					$awardstatus = $redenvelopearray1s['status'];
					$membername = $redenvelopearray1s['nickname'];
					$sendid = $redenvelopearray1s['id'];
					$hint = array("status"=>"success","message"=>"您获得的红包金额:","amount"=>$amount,"awardstatus"=>$awardstatus, "sendid"=>$sendid,"membername"=>$membername);
					echo json_encode($hint);
					exit;	
				}else if(!empty($redenvelopearray2s)){  //已领
					$amount = $redenvelopearray2s['amount'];
					$membername = $redenvelopearray2s['nickname'];
					$hint = array("status"=>"error","message"=>"您已经领取过该兑换码", "amount"=>$amount, "membername"=>$membername);
					echo json_encode($hint);
					exit;	
				}else if(!empty($redenvelopearray3s)){ //未领已过期，可能有多个 
					$membername = $redenvelopearray3s['nickname'];
					$hint = array("status"=>"expire","message"=>"很抱歉，您的兑换码已过期,<br>不能领取,谢谢参与!", "membername"=>$membername);
					echo json_encode($hint);
					exit;
				}
			}
		}
		//确认领奖
		if( isset($_POST['confirm_award']) ){	
			$exchangecode = $_POST['exchangecode'];
			$id = $_POST['id'];	
			$sendid = $_POST['sendid'];	
			$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}redenvelope_winner set status = 1 WHERE id=%d AND rid = %d AND code = %s and (winexpire = 0 OR winexpire = '' OR ".strtotime(date('Y-m-d'))." <= winexpire)",$sendid, $id, $exchangecode));
			//$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}redenvelope_winner set status = 1 WHERE rid = %d AND codevalue = %s",$id,$exchangecode));
			//echo $wpdb -> last_query;
			if ($update!==FALSE) {
				$hint = array("status"=>"success","message"=>"确认领奖成功");
				echo json_encode($hint);
				exit;
			}else{
				$hint = array("status"=>"error","message"=>"确认领奖失败");
				echo json_encode($hint);
				exit;
			}
		}
		
		//取消领奖
		if( isset($_POST['cancel_award']) ){	
			$exchangecode = $_POST['exchangecode'];
			$id = $_POST['id'];	
			$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}redenvelope_winner WHERE rid =%d and code = %s",$id, $exchangecode ) );
			//$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}redenvelope_winner WHERE rid =%d and codevalue = %s",$id, $exchangecode ) );
			if ($delete!==FALSE) {
				$hint = array("status"=>"success","message"=>"取消领奖成功");
				echo json_encode($hint);
				exit;
			}else{
				$hint = array("status"=>"error","message"=>"取消领奖失败");
				echo json_encode($hint);
				exit;
			}
		}
		
		include $this->template('exchangecode');
	}
	
	/*
	*Mobile
	/
	
	/*手机端抽奖页面*/
	public function doMobileLottery() {
		global $_W, $wpdb;
		$title = '微红包';
		
		/*from_user拿mid?*/
		
		$gweid=$_W['gweid'];
		$mid=$_W['fans']['mid'];
		$id = intval($_GET['id']);
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		
		/*判断功能是否可用*/
		if(!$this->has_module($gweid,'wechatactivity_redenvelope')){
			message('微红包功能已经关闭！');
		}else{
		
			if(!$this->has_module($gweidv,'wechatvip')){
				message('没有开启会员权限,无法使用该功能！');
			}
			if($this->has_module($gweidv,'wechatvip') && empty($mid)){
				header("Location: ".home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$gweidv.'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]).'&alert='.urlencode('登录后才能使用微红包，请先登录。'));
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

		$sql = $wpdb -> prepare("SELECT id, redenvelope_image, redenvelope_bacimage, startdate, periodlottery, maxlottery, rule, hitcredit, misscredit FROM {$wpdb->prefix}redenvelope WHERE id =%d LIMIT 1",$id);
		$redenvelope = $wpdb->get_row($sql,ARRAY_A);
		if (empty($redenvelope)) {
			message('页面已经失效！');
		}
		/*判断功能是否可用END*/
		
		
		/*处理活动图片的显示*/
		$upload =wp_upload_dir();
		if((empty($redenvelope['redenvelope_image']))||(stristr($redenvelope['redenvelope_image'],"http")!==false)){
			$redenvelopepicture=$redenvelope['redenvelope_image'];
		}else{
			$redenvelopepicture=$upload['baseurl'].$redenvelope['redenvelope_image'];
		}
		/*处理活动图片的显示END*/
		
		/*处理活动背景图片的显示*/
		$upload =wp_upload_dir();
		if((empty($redenvelope['redenvelope_bacimage']))||(stristr($redenvelope['redenvelope_bacimage'],"http")!==false)){
			$redenvelopebacpicture=$redenvelope['redenvelope_bacimage'];
		}else{
			$redenvelopebacpicture=$upload['baseurl'].$redenvelope['redenvelope_bacimage'];
		}
		/*处理活动图片的显示END*/
		
		
		/*处理文章图片的显示*/
		$redenveloperule=$this->rule_content($redenvelope['rule']);
		
		$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}redenvelope_winner  WHERE createtime > %s AND mid = %s AND status <> 3 AND rid=%d",strtotime(date('Y-m-d')),$mid,$id);
		$total = $wpdb->get_var($sql);
		/*$member = fans_search($fromuser);*/
		/*winner中取出用户某个微红包的中奖情况*/
		//$myaward = $wpdb -> get_results($wpdb -> prepare("SELECT id, award, description, status FROM {$wpdb->prefix}redenvelope_winner WHERE mid = %s  AND rid = %s ORDER BY createtime DESC",$mid,$id),ARRAY_A);
		$mycredit = $wpdb -> get_results($wpdb -> prepare("SELECT description FROM {$wpdb->prefix}redenvelope_winner  WHERE mid = %s AND rid = %s ORDER BY createtime DESC",$mid,$id),ARRAY_A);
		foreach($mycredit as $v => $k) {
			$_mycredit[] = $k['description'];
		}
		if(!empty($_mycredit)){
			$mycredit = array_sum($_mycredit);
		}
		$mycredit = (!empty($mycredit)) ? $mycredit : '0';
		$allaward = $wpdb -> get_results($wpdb -> prepare("SELECT id, title, probalilty, description, inkind FROM {$wpdb->prefix}redenvelope_award WHERE rid = %s ORDER BY id ASC",$id),ARRAY_A);
		
		/* if(strtotime($redenvelope['startdate']) >=  strtotime(date('Y-m-d', TIMESTAMP))){
			$returnmessage = '活动还未开始，开始时间为：'.$redenvelope['startdate'].'，敬请期待!';
			//echo $message;
		}else{ */
			
			//过期
			if (!empty($redenvelope['periodlottery'])) {
				$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}redenvelope_winner  WHERE createtime > %s AND mid = %s AND status <> 3 AND rid=%d",strtotime(date('Y-m-d')),$mid,$id);
				$total = $wpdb->get_var($sql);
				$sql = $wpdb -> prepare("SELECT createtime FROM {$wpdb->prefix}redenvelope_winner WHERE mid = %s AND status <> 3 AND rid=%d ORDER BY createtime DESC ",$mid,$id);
				$lastdate = $wpdb->get_var($sql);
				if (($total >= intval($redenvelope['maxlottery'])) && strtotime(date('Y-m-d')) < strtotime(date('Y-m-d', $lastdate)) + $redenvelope['periodlottery'] * 86400) {
					$message = '您还未到达可以再次抽奖的时间<br>下次可抽奖时间为：'.date('Y-m-d', strtotime(date('Y-m-d', $lastdate)) + $redenvelope['periodlottery'] * 86400);
				}
			} else {
				$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}redenvelope_winner WHERE createtime > %s AND mid = %s AND status <> 3 AND rid=%d",strtotime(date('Y-m-d')),$mid,$id);
				$total = $wpdb->get_var($sql);
				if (!empty($redenvelope['maxlottery']) && $total >= $redenvelope['maxlottery']) {
					$message = $redenvelope['periodlottery'] ? '您已经超过当日抽奖次数' : '您已经超过最大抽奖次数';
				}
			}
		/* } */
		
		//获取会员的相关信息
		$member = $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wechat_member WHERE mid = %s LIMIT 1",$mid),ARRAY_A);;
		/*winner中取出用户某个砸蛋的中奖情况*/
		//$myaward = $wpdb->get_results($wpdb->prepare("SELECT code, amount, credit, description, status, winexpire, createtime FROM {$wpdb->prefix}redenvelope_winner WHERE mid = %s AND rid = %d ORDER BY createtime DESC", $mid, $id),ARRAY_A);
		$myaward = $wpdb->get_results($wpdb->prepare("SELECT a.code, a.amount, a.credit, a.description, a.status, a.winexpire,a.createtime, b.code_description as des FROM {$wpdb->prefix}redenvelope_winner AS a LEFT JOIN {$wpdb->prefix}redenvelope AS b ON a.rid = b.id WHERE a.mid = %s AND a.rid = %d ORDER BY a.createtime DESC", $mid, $id),ARRAY_A);
		//echo "SELECT a.code, a.amount, a.credit, a.description, a.status, a.winexpire,a.createtime, b.description as des FROM {$wpdb->prefix}redenvelope_winner AS a LEFT JOIN {$wpdb->prefix}redenvelope AS b ON a.rid = b.id WHERE a.mid = {$mid} AND a.rid = {$id} ORDER BY a.createtime DESC";
		//查看其它人的中奖情况
		$sql = "SELECT a.code, a.amount, a.credit, b.realname FROM {$wpdb->prefix}redenvelope_winner AS a
				LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid WHERE b.mobilenumber <> '' AND b.realname <> '' AND a.rid = %d ORDER BY a.createtime DESC";
		$otheraward = $wpdb->get_results($wpdb->prepare($sql,$id),ARRAY_A);
		
		include $this->template('lottery');
	}

	public function doMobileGetAward() {
		global $_W, $wpdb;
		
		$gweid=$_W['gweid'];
		$mid=$_W['fans']['mid'];
		$id = intval($_GET['id']);
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		
		/*进入抽奖页面后，管理员禁止了应用或禁止了会员处理*/
		if($this->has_module($gweid,'wechatactivity_redenvelope')){

			if($this->has_module($gweidv,'wechatvip') && empty($mid)){
				$url=home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$gweidv.'&redirect_url='.urlencode($this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid))).'&alert='.urlencode('登录后才能使用微红包，请先登录。');
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

			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}redenvelope WHERE id =%d LIMIT 1",$id);
			$redenvelope = $wpdb->get_row($sql,ARRAY_A);
			if (empty($redenvelope)) {
				$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
				$result = array('status' => -2, 'message' => '该页面已经失效！','url'=>$url);
				message($result, '', 'ajax');
			}
		}else{
			$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
			$result = array('status' => -2, 'message' => '微红包活动已经关闭！','url'=>$url);
			message($result, '', 'ajax');
		}
		/*进入抽奖页面后，管理员禁止了应用或禁止了会员处理END*/
		
		$result = array('status' => -1, 'message' => '');
		if (!empty($redenvelope['periodlottery'])) {
			$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}redenvelope_winner  WHERE createtime > %s AND mid = %s AND status <> 3 AND rid=%d",strtotime(date('Y-m-d')),$mid,$id);
			$total = $wpdb->get_var($sql);
			$sql = $wpdb -> prepare("SELECT createtime FROM {$wpdb->prefix}redenvelope_winner WHERE mid = %s AND status <> 3 AND rid=%d ORDER BY createtime DESC",$mid,$id);
			$lastdate = $wpdb->get_var($sql);
			
			if (($total >= intval($redenvelope['maxlottery'])) && strtotime(date('Y-m-d')) < strtotime(date('Y-m-d', $lastdate)) + $redenvelope['periodlottery'] * 86400) {
				$result['message'] = '您还未到达可以再次抽奖的时间。下次可抽奖时间为'.date('Y-m-d', strtotime(date('Y-m-d', $lastdate)) + $redenvelope['periodlottery'] * 86400);
				message($result, '', 'ajax');
			}
		} else {
			$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}redenvelope_winner WHERE createtime > %s AND mid = %s AND status <> 3 AND rid=%d",strtotime(date('Y-m-d')),$mid,$id);
			$total = $wpdb->get_var($sql);
			if (!empty($redenvelope['maxlottery']) && $total >= $redenvelope['maxlottery']) {
				$result['message'] = $redenvelope['periodlottery'] ? '您已经超过当日抽奖次数' : '您已经超过最大抽奖次数';
				message($result, '', 'ajax');
			}
		}
		
		//取得活动的截止日期存入winner表
		if($redenvelope['isrelative'] == 1){  //取绝对有效期	
			$winexpire = $redenvelope['absexpire'];
		}else{
			//$winexpire = date('Y-m-d', strtotime(date('Y-m-d', $lastdate)) + $redenvelope['expire'] * 86400);
			if($redenvelope['expire'] == 0){
				$winexpire ="";    //winexpire为空，表示永不过期
			}else{
				$winexpire = strtotime(date('Y-m-d', TIMESTAMP)) + $redenvelope['expire'] * 86400;
			}
		}
		
		//当总金额为0的时候，$award_amount的值是当时输入的金额的最小值，而不是0
		//$award_amount = $this -> getAward($redenvelope['probalilty'], $redenvelope['total'], $redenvelope['amount'], $redenvelope['min_amount'], $redenvelope['max_amount']);
		if(!empty($redenvelope['total'])){
			$award_amount = $this -> getAward($redenvelope['probalilty'], $redenvelope['total'], $redenvelope['amount'], $redenvelope['min_amount'], $redenvelope['max_amount']);
		}else{
			/* //如果红包数为0，并且没有已过期未领奖的数据，则显示红包抢光了；否则按未中奖处理
			$sql = $wpdb -> prepare("SELECT count(*) as rcount FROM {$wpdb->prefix}redenvelope_winner where rid = %d and isupdate!=1 and status=0 and winexpire!=0 and winexpire!='' and code!='' and ".strtotime(date('Y-m-d'))." > winexpire", $id);
			$expirearrays = $wpdb->get_row($sql,ARRAY_A);
			
			$rcount = intval($expirearrays['rcount']);
			
			if($rcount == 0){
				$result['message'] = "兑换码库存不足！";
				message($result, '', 'ajax');
			}else{     //按未中奖处理,还会继续执行下面的
			
			} */
			$result['message'] = "兑换码库存不足！";
			message($result, '', 'ajax');
		}
		
		$title = '';
		$result['message'] = '很遗憾，您没能中奖！';
		
		$data = array(
			'rid' => $id,
			'openid' => $fromuser,
			'mid' => $mid,
			'status' => 0,
			'winexpire' => intval($winexpire),    //取得该活动的领奖过期天数
			'createtime' => TIMESTAMP,
		);
		if (!empty($award_amount)) {
			$data['description'] = $gift['code_description'];
			$code = $this -> getCode($redenvelope);
			if(!$code){
				$result['message'] = "兑换码库存不足！";
				message($result, '', 'ajax');
			}
			$db_code_field = $code['db_code_field'];
			$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}redenvelope set total=total-1, amount=amount - %d, code=%s WHERE id = %s",$award_amount,$db_code_field,$id));
			
			$code = $code['code'];
			$data['code'] = rtrim($code);  //去掉字符串末尾的空格，\0,\t,\n,\X0B,\r
			$data['amount'] = $award_amount;
			$result['message'] = "恭喜您，得到{$award_amount}元红包，兑换码为“{$code}”！" ;
			$result['codedes'] = $redenvelope['code_description'];  //兑换码说明
			$result['status'] = 0;
		}else{
			$data['code'] = "";   //在数据库中该字段没有默认值
			$data['status'] = 1;   //如果是未中奖，对应的未中奖积分的status为1
		}
		$data['credit'] = (empty($award_amount) ? $redenvelope['misscredit'] : $redenvelope['hitcredit']);
		
		//$data['aid'] = $gift['id']; 当前数据库已经去掉此字段
		if (!empty($data['credit'])) 
			$update = $wpdb->query($wpdb -> prepare("update {$wpdb->prefix}wechat_member set point=point+{$data['credit']} WHERE mid = %s",$mid));
		$wpdb -> insert("{$wpdb -> prefix}redenvelope_winner",$data);
		//$myawardarray = $wpdb -> get_results($wpdb -> prepare("SELECT id, description, code, amount, credit, status, winexpire, createtime FROM {$wpdb->prefix}redenvelope_winner WHERE mid = %s AND rid = %s ORDER BY createtime DESC",$mid,$id),ARRAY_A);
		$myawardarray = $wpdb->get_results($wpdb->prepare("SELECT a.id, a.description, a.code, a.amount, a.credit, a.status, a.winexpire, a.createtime, b.code_description as des FROM {$wpdb->prefix}redenvelope_winner AS a LEFT JOIN {$wpdb->prefix}redenvelope AS b ON a.rid = b.id WHERE a.mid = %s AND a.rid = %d ORDER BY a.createtime DESC", $mid, $id),ARRAY_A);
		if(!empty($myawardarray)){
			foreach($myawardarray as &$myaward){      //对myaward重新赋值需要加&
			    if($myaward['winexpire'] == 0){
					$myaward['winexpire'] = 0;
					$myaward['codeexpire'] = "";
				}else{	
					
					if($myaward['winexpire'] != 0 && (strtotime(date('Y-m-d', $myaward['winexpire'])." 23:59:59") < time())  && ($myaward['status'] == 0)){  //只有未领取且过期的才显示过期的提示
						$myaward['codeexpire'] = "已过期";
					}else{
						$myaward['codeexpire'] = "";
					}
					$myaward['winexpire'] = date('Y-m-d', $myaward['winexpire']);
				}
			}
		}
		
		$result['myaward'] = $myawardarray ;
		$result['credit']= $wpdb -> get_var($wpdb -> prepare("SELECT sum(credit) FROM {$wpdb->prefix}redenvelope_winner WHERE mid = %s AND credit <> 0 AND rid = %s ORDER BY createtime DESC",$mid,$id));

		$result['credit'] = (!empty($result['credit'])) ? $result['credit'] : '0';
		message($result, '', 'ajax');
	}
	
	/*获取中奖名单列表*/
	public function doMobileGetOtherAward(){
		global $_GPC, $_W, $wpdb;
		
		$gweid=$_GET['gweid'];
		$id = intval($_GET['id']);
	
		//"中奖名单" also need update after getaward 
		$result['otheraward'] = $wpdb->get_results($wpdb->prepare("SELECT e1.code, e1.amount, e1.credit, w1.realname FROM {$wpdb->prefix}redenvelope_winner e1 LEFT JOIN {$wpdb->prefix}wechat_member w1 ON e1.mid = w1.mid WHERE e1.rid = %d ORDER BY e1.createtime DESC", $id),ARRAY_A);
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
		if($this->has_module($gweid,'wechatactivity_redenvelope')){
			
			if($this->has_module($gweidv,'wechatvip') && empty($mid)){
				$url=home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$gweidv.'&redirect_url='.urlencode($this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid))).'&alert='.urlencode('登录后才能使用红包，请先登录。');
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

			$sql = $wpdb -> prepare("SELECT id, periodlottery, maxlottery, misscredit, hitcredit FROM {$wpdb->prefix}redenvelope WHERE id =%d LIMIT 1",$id);
			$redenvelope = $wpdb->get_row($sql,ARRAY_A);
			if (empty($redenvelope)) {
				$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
				$result = array('status' => -2, 'message' => '该页面已经失效！','url'=>$url);
				message($result, '', 'ajax');
			}
		}else{
			$url=$this->createMobileUrl('Lottery',array('id' => $id,'gweid' => $gweid));
			$result = array('status' => -2, 'message' => '红包活动已经关闭！','url'=>$url);
			message($result, '', 'ajax');
		}
		/*进入抽奖页面后，管理员禁止了应用或禁止了会员处理END*/
		
		$data = array(
			'status' => 2,
		);
		$wpdb->update("{$wpdb -> prefix}redenvelope_winner", $data, array('id' => $_GET['awardid']));
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
	/*
	probalilty: 中奖率
	total: 剩余红包个数
	total_amount: 剩余红包总金额
	min: 红包金额最小值
	max: 红包金额最大值

	*/
	private function getAward($probalilty, $total, $total_amount, $min, $max){
		if(mt_rand(1,10000) > $probalilty*100)
			return 0;
		
		//剩余红包数量为1，此时红包总金额大于红包最大值，则返回红包最大值，否则返回红包总金额
		if($total == 1){
			if($total_amount > $max)
				return $max;
			return $total_amount;
		}

		$ta = ($total - 1) * $max;
		$ti = ($total - 1) * $min;

		//为避免本次抽奖结束后 剩余红包总额大于红包数*红包最大值 或 剩余红包总额小于红包数*红包最小值
		$rand_max = min($total_amount-$ti , $max);
		$rand_min = max($total_amount-$ta , $min);

		//避免计算出的最大值小于最小值
		if($rand_max < $rand_min)
			return $rand_max;

		return mt_rand($rand_min,$rand_max);
	}

	private function getCode($redenvelope){
		global $wpdb;
		$code = unserialize($redenvelope['code']);
		if($code['type'] == 0){
			if(empty($code['codelist']))
				return FALSE;
			$code_value = array_pop($code['codelist']);
			return array('code' => $code_value, 'db_code_field' => serialize($code));
		}
			
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
		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}redenvelope WHERE gweid='{$gweid}'",ARRAY_A);
		if(is_array($list))
			foreach($list as $element){
				file_unlink($element['redenvelope_image']);
				file_unlink($element['redenvelope_bacimage']);
           		file_unlink_from_xml($element['rule']);
			}
				
	}

}
