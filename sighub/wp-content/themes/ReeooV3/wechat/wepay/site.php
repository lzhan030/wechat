<?php

defined('IN_IA') or exit('Access Denied');
require_once ABSPATH.'wp-content/themes/ReeooV3/wechat/wepay/sdk/sdk.php';
class WepayModuleSite extends ModuleSite {

	public $REFUND_REASON = array('SHIPPING_FAILED' =>'发货失败',
					'WRONG_GOODS' => '商品错误',
					'CONSENSUS' => '协商一致',
					'WRONG_SHIPPING_FEE' =>'运费错误',
					'OTHER' => '其他原因');

	public $REFUND_STATUS = array(
					'SUCCESS' => '退款成功',
					'FAIL' => '退款失败',
					'PROCESSING' => '退款处理中',
					'NOTSURE' => '未确定',
					'CHANGE' => '转入代发',
					'CREATEFAIL' => '退款创建失败'
		);
	public $DISCOUNT_TYPE = array('' => '',
					'MANUAL' => '人工优惠',
					'SCRATCHCARD' => '刮刮卡',
					'OTHER' => '其他原因');
	public $SEND_TYPE = array('' => '',
					'DELIVERY' => '快递',
					'YOURSELF' => '自取',
					'NOTDELIVERY' => '无需配送');
	public $TRADE_STATE = array(
					'SUCCESS' => '支付成功',
					'REFUND' => '转入退款',
					'NOTPAY' => '未支付',
					'CLOSED' => '已关闭',
					'REVOKED' => '已撤销',
					'USERPAYING' => '用户支付中',
					'NOPAY' => '支付中断',
					'PAYERROR' => '支付失败',
					'PAYING' => '未付款',
					'SELFDELIVERY' => '自提',
					'CASHONDELIVERY' => '货到付款',
					'SELFDELIVERY_CLOSED' => '自提_订单关闭',
					'CASHONDELIVERY_CLOSED' => '货到付款_订单关闭',
					'ERROR_WARNING' => '未知');
	public $BANK_TYPE = array('' => '',
					'CFT' => '财付通',
					'ABC_DEBIT' => '中国农业银行借记卡'
					);
	public $TRADE_STATE_REVERSE = array('' => '',
					'支付成功' => 'SUCCESS',
					'转入退款' => 'REFUND',
					'未支付' => 'NOTPAY',
					'已关闭' => 'CLOSED',
					'已撤销' => 'REVOKED',
					'用户支付中' => 'USERPAYING',
					'支付失败' => 'PAYERROR',
					'未付款' => 'PAYING',
					'支付中断' => 'NOPAY',
					'自提' => 'SELFDELIVERY',
					'货到付款' => 'CASHONDELIVERY',
					'自提_订单关闭' => 'SELFDELIVERY_CLOSED',
					'货到付款_订单关闭' => 'CASHONDELIVERY_CLOSED',
					'未知' => 'ERROR_WARNING');
	public $RIGHT_MSGTYPE = array(
					'confirm' => '用户同意维权结果',
					'reject' => '用户不同意维权结果');
	public $RIGHT_REASON = array(
			'1' =>'商品质量有问题',
			'2' => '商品与实际购买不符',
			'3' => '商品发货延迟',
			'4' => '其他原因'
			);
	public $RIGHT_SOLUTION = array(
			'1' =>'退款退货',
			'2' => '退款不退货');
	public $RIGHT_STATUS = array(
			'1' =>'未处理',
			'2' => '处理中',
			'3' => '已解决');
					
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

	/**
		transaction error
	*/
	public function wechatOrderSearch(){
		global $wpdb;
		$gweidList = $wpdb -> get_results("SELECT distinct gweid FROM {$wpdb->prefix}shopping_order order by gweid");
		if(is_array($gweidList) && !empty($gweidList)){
			foreach($gweidList as $gweids){
				$gweid=$gweids->gweid;
				$weixin = new WeixinPay($gweid);
				$time=date('Y-m-d H:i:s',strtotime('now')-0.5*60*60);
				//查询数据库中订单状态为转入退款、未支付、用户支付中、支付失败的订单，再次通过微信接口查询订单状态并更新DB
				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order where trade_state in ('NOTPAY','USERPAYING','NOPAY','PAYERROR','ERROR_WARNING') and gweid= %s and time_start < %s",$gweid,$time);
				$orders = $wpdb->get_results($sql);
				if(is_array($orders) && !empty($orders)){	
					foreach($orders as $order){
						$out_trade_no=$order->out_trade_no;				
						//支付成功页面查询订单并更新数据库
						$orderquery=$weixin->order_query($out_trade_no);
						if($orderquery!=false){	
							if(($orderquery['return_code']=="SUCCESS")&&($orderquery['result_code']=="SUCCESS")){
								if($orderquery['trade_state']=="SUCCESS"){
									$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>$orderquery['trade_state'],'time_end'=>empty($orderquery['time_end'])?NULL:$orderquery['time_end'],'iserror'=>0,'error_description'=>''),array('out_trade_no'=>$out_trade_no),array('%s','%s'),array('%s'));
								}else{
									$update=$wpdb->query( $wpdb->prepare("update {$wpdb->prefix}shopping_order set trade_state=%s,time_end=null,iserror=0,error_description='' where out_trade_no=%s",$orderquery['trade_state'],$out_trade_no));
								}
								$update=$wpdb->update( $wpdb->prefix.'shopping_order_wepay', array('bank_type'=>$orderquery['bank_type'],'fee_type'=>$orderquery['fee_type']),array('out_trade_no'=>$out_trade_no), array('%s','%s'),array('%s'));
							}else if($orderquery['return_code']!="SUCCESS"){
								$errorhint=$orderquery['return_msg'];
								$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>"ERROR_WARNING",'iserror'=>1,'error_description'=>$errorhint),array('out_trade_no'=>$out_trade_no),array('%s'),array('%s'));
							}
						}else{
							$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>"ERROR_WARNING",'iserror'=>1,'error_description'=>"订单状态查询失败"),array('out_trade_no'=>$out_trade_no),array('%s'));
						}
					}
				}
			}
		}
		
	}
	/**
		delivery_confirmed
	*/
	public function wechatDeliveryConfirmed(){
		global $_W, $_GPC ,$wpdb;
		$time=date('Y-m-d H:i:s',strtotime('now')-DELIVERY_CONFIRMED);
		$sql  = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_delivery where delivery_timestamp < %s and delivery_status=1",$time);
		$deliveryinfos = $wpdb->get_results($sql);
		if(is_array($deliveryinfos) && !empty($deliveryinfos)){
			foreach($deliveryinfos as $deliveryinfo){
				$orderid=$deliveryinfo->out_trade_no;
				$update=$wpdb->update( $wpdb->prefix.'shopping_delivery', array('delivery_status'=>2),array('out_trade_no'=>$orderid), array('%s'));
			}
		}
	}
	
	
	//后台管理--网页支付商品定制页面管理页面
	public function doWebGoodsindexmanage(){
		global $_W, $wpdb;
	    $gweid =  $_SESSION['GWEID'];
		$this->Perdenied($gweid);
		$search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		
		$search = array(
			'all' => '',
			'id' => "AND id ='{$search_content}'",
			'goodsindex_name' => "AND goodsindex_name LIKE '%%{$search_content}%%'"
		);
		
	    $sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_goodsindex WHERE type='JSAPI' and gweid=%s {$search[$search_condition]} ",$gweid);
		$total = $wpdb->get_var($sql);
		
	    $pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goodsindex where gweid=%s and type='JSAPI' {$search[$search_condition]} ORDER BY id DESC Limit {$offset},{$psize}",$_SESSION['GWEID']);
		$list = $wpdb->get_results($sql);
		
        if(isset($_POST['goodsindex_del']) && !empty($_POST['goodsindex_del']) ){							
			$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_goodsindex WHERE id=%s", $_POST['goodsindexid']));			
			//不删除商品以免订单对应数据看不到商品信息,商品设置为下架
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$update=$wpdb->update( $wpdb->prefix.'shopping_goods', array('status'=>1),array('groupid'=>$_POST['goodsindexid']), array('%s'));
				if($update===false){
					$hint = array("status"=>"error","message"=>"删除失败");
				}else{
					$hint = array("status"=>"success","message"=>"删除成功");
				}
			}
			echo json_encode($hint);
			exit;	
		}
		
        include $this -> template('goodsindexlist');
    }
	//查看二维码
	public function doWebShowpayqr(){
		global $_W, $_GPC ,$wpdb;
		$gweid = $_SESSION['GWEID'];
		$goodsgid = $_GET['goodsgid'];
		$pictureurl=$this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsgid));
		
		include 'phpqrcode.php'; 
		if(isset($_GET['download'])){
			header('Content-Disposition: attachment; filename="qrcode-'.$goodsgid.'.png"');  
			QRcode::png($pictureurl,false,8);	
			
		}else{
			QRcode::png($pictureurl);	
		}
    }
	//后台管理--网页支付商品定制添加或更新
	public function doWebgoodsindexhandle(){
		include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
	    global $_W, $_GPC ,$wpdb;
		global $current_user;	
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;			
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$gweid=$_SESSION['GWEID'];
		$native=$_REQUEST['native'];
		$this->Perdenied($gweid);
		if($native){
			$paytype="NATIVE";
		}else{
			$paytype="JSAPI";
		}
		$uploaderror="isfalse";
		$goodsindexid=$_REQUEST['goodsindexid'];
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goodsindex where id=%s",$goodsindexid);
		$goodsindexs = $wpdb->get_results($sql);
		if(is_array($goodsindexs) && !empty($goodsindexs)){
			foreach($goodsindexs as $goodsindex){
				$gname=$goodsindex->goodsindex_name;
				$gurl=$goodsindex->goodsindex_url;
			}
		}
		//该支付页面的商品信息
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods where groupid=%s and status=0 order by createtime",$goodsindexid);
		$goods = $wpdb->get_results($sql);
		
		//删除某一项
		if(isset($_POST['goods_del']) && !empty($_POST['goods_del']) ){							
			//商品并没有真正删除，状态改为下架
			//$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_goods WHERE id=%s", $_GPC['goodsid']));			
			$update=$wpdb->update( $wpdb->prefix.'shopping_goods', array('status'=>1),array('id'=>$_POST['goodsid']), array('%s'));
			
			if($update===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$hint = array("status"=>"success","message"=>"删除成功");
			}
			echo json_encode($hint);
			exit;	
			
		}		
		
		//更新支付页面中的某个商品
		if((isset($_POST['goodsindex_id']) && !empty($_POST['goodsindex_id']) )&&(isset($_POST['goodsid']) && !empty($_POST['goodsid']) )){
			$goodsindexid=$_POST['goodsindex_id'];
			$goodsid=$_POST['goodsid'];
			$title=$_POST['title'];
			$total=$_POST['total'];
			$type=$_POST['type'];
			$isdelivery=$_POST['isdelivery'];
			$ismanual=$_POST['ismanual'];
			$market_price=$_POST['market_price'];
			$goodssn=$_POST['goodssn'];
			$description=$_POST['description'];
			$gname=$_POST['gname'];
			$delimgid=$_POST['delimgid'];
			
			//上传图片
			if($_FILES["file"]["error"] > 0){
				if($delimgid!=-1){
					$wpdb->update( $wpdb->prefix.'shopping_goods', array('type'=>$type,'isdelivery'=>$isdelivery,'ismanual'=>$ismanual,'title'=>$title,'description'=>$description,'goodssn'=>$goodssn,'market_price'=>$market_price,'total'=>$total),array('id'=>$goodsid), array('%s','%d','%d','%s','%s','%s','%f','%s'),array('%s'));
				}else{
					$thumb = $wpdb->get_var($wpdb->prepare("SELECT thumb FROM {$wpdb->prefix}shopping_goods where id=%s",$goodsid));
					file_unlink($thumb);
					$wpdb->update( $wpdb->prefix.'shopping_goods', array('type'=>$type,'isdelivery'=>$isdelivery,'ismanual'=>$ismanual,'title'=>$title,'thumb'=>"",'description'=>$description,'goodssn'=>$goodssn,'market_price'=>$market_price,'total'=>$total),array('id'=>$goodsid), array('%s','%d','%d','%s','%s','%s','%f','%s'),array('%s'));
				}
			}else{
				$up=new upphoto();
				$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
				$up->get_ph_type($_FILES["file"]["type"]);
				$up->get_ph_size($_FILES["file"]["size"]);
				$up->get_ph_name($_FILES["file"]["name"]);
				$picUrl=$up->save();
				if($picUrl!=false){
					$thumb = $wpdb->get_var($wpdb->prepare("SELECT thumb FROM {$wpdb->prefix}shopping_goods where id=%s",$goodsid));
					if($thumb != $picUrl)
						file_unlink($thumb);
					$wpdb->update( $wpdb->prefix.'shopping_goods', array('type'=>$type,'isdelivery'=>$isdelivery,'ismanual'=>$ismanual,'title'=>$title,'thumb'=>$picUrl,'description'=>$description,'goodssn'=>$goodssn,'market_price'=>$market_price,'total'=>$total),array('id'=>$goodsid), array('%s','%d','%d','%s','%s','%s','%s','%f','%s'),array('%s'));
				}else{
					$uploaderror="istrue";
				}
			}
			$update=$wpdb->update( $wpdb->prefix.'shopping_goodsindex', array('goodsindex_name'=>$gname),array('id'=>$goodsindexid), array('%s'),array('%s'));
		
			
		?>	
		<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<script>
				var uploaderror='<?= $uploaderror ?>';
				if(uploaderror=='istrue'){
					alert('图片上传错误，可能是空间不足，请检查后重试');
				}else{
					alert('提交成功');
					location.href="<?php echo $this->createWebUrl('goodsindexhandle',array('goodsindexid' => $goodsindexid,'isone'=>$goodsid,'native'=>$native));?>";
				}
			</script>
			</head>
			</html>
		<?php	
				exit;
		}else if((!isset($_POST['goodsindex_id']) || empty($_POST['goodsindex_id']) )&&(!isset($_POST['goodsid']) && empty($_POST['goodsid']) )&&(isset($_POST['title']) && !empty($_POST['title']))){//添加新的支付页面以及某个商品
			$goodsid=time().rand(11,99);
			$goodsindexid=time().rand(111,999);
			$title=$_POST['title'];
			$total=$_POST['total'];
			$type=$_POST['type'];
			$isdelivery=$_POST['isdelivery'];
			$ismanual=$_POST['ismanual'];
			$market_price=$_POST['market_price'];
			$goodssn=$_POST['goodssn'];
			$description=$_POST['description'];
			$gname=$_POST['gname'];		
			
			//上传图片
			if($_FILES["file"]["error"] > 0){
					//echo "<h3>保存图片失败！</h3>";
			}else{
				$up=new upphoto();
				$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
				$up->get_ph_type($_FILES["file"]["type"]);
				$up->get_ph_size($_FILES["file"]["size"]);
				$up->get_ph_name($_FILES["file"]["name"]);
				$picUrl=$up->save();
				
				if($picUrl==false){
					$uploaderror="istrue";
				}
			}
			$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_goodsindex(id,goodsindex_name,gweid,type)VALUES (%s,%s,%s,%s)",$goodsindexid,$gname,$gweid,$paytype));
			$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_goods(id,groupid,gweid,type,isdelivery,ismanual,title,thumb,description,goodssn,market_price,total)VALUES (%s,%s,%s,%s,%d,%d,%s,%s,%s,%s,%f,%s)",$goodsid,$goodsindexid,$gweid,$type,$isdelivery,$ismanual,$title,$picUrl,$description,$goodssn,$market_price,$total));
		
		?>	<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<script>
				var uploaderror = '<?= $uploaderror?>';
				if(uploaderror=='istrue'){
					alert('图片上传错误，可能是空间不足，请检查后重试');
				}else{
					alert('提交成功');
					location.href="<?php echo $this->createWebUrl('goodsindexhandle',array('goodsindexid' => $goodsindexid,'isone'=>$goodsid,'native'=>$native));?>";
				}
			</script>
			</head>
			</html>
		<?php	
		
		exit;
		}else if((isset($_POST['goodsindex_id']) && !empty($_POST['goodsindex_id']) )&&(!isset($_POST['goodsid']) && empty($_POST['goodsid']) )){//存在的支付页面中添加新商品
			$goodsid=time().rand(11,99);
			$goodsindexid=$_POST['goodsindex_id'];
			$title=$_POST['title'];
			$total=$_POST['total'];
			$type=$_POST['type'];
			$isdelivery=$_POST['isdelivery'];
			$ismanual=$_POST['ismanual'];
			$market_price=$_POST['market_price'];
			$goodssn=$_POST['goodssn'];
			$description=$_POST['description'];
			$gname=$_POST['gname'];
			
					
			//上传图片
			if($_FILES["file"]["error"] > 0){
					//echo "<h3>保存图片失败！</h3>";
			}else{
				$up=new upphoto();
				$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
				$up->get_ph_type($_FILES["file"]["type"]);
				$up->get_ph_size($_FILES["file"]["size"]);
				$up->get_ph_name($_FILES["file"]["name"]);
				$picUrl=$up->save();
				if($picUrl==false){
					$uploaderror="istrue";
				}
			}
			$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_goods(id,groupid,gweid,type,isdelivery,ismanual,title,thumb,description,goodssn,market_price,total)VALUES (%s,%s,%s,%s,%d,%d,%s,%s,%s,%s,%f,%s)",$goodsid,$goodsindexid,$gweid,$type,$isdelivery,$ismanual,$title,$picUrl,$description,$goodssn,$market_price,$total));
			$update=$wpdb->update( $wpdb->prefix.'shopping_goodsindex', array('goodsindex_name'=>$gname),array('id'=>$goodsindexid), array('%s'),array('%s'));
		
		
		?>	<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<script>
				var uploaderror = '<?= $uploaderror?>';
				if(uploaderror=='istrue'){
					alert('图片上传错误，可能是空间不足，请检查后重试');
				}else{
					alert('提交成功');
					location.href="<?php echo $this->createWebUrl('goodsindexhandle',array('goodsindexid' => $goodsindexid,'isone'=>$goodsid,'native'=>$native));?>";
				}
			</script>
			</head>
			</html>
		<?php	
		
		exit;
		}
		
		include $this -> template('goodsindexhandle');	
	}
	
	//后台管理--物流公司管理
	public function doWebCouriersmanage(){
	
	    global $_W, $_GPC ,$wpdb;
		global $current_user;	
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;			
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$gweid=$_SESSION['GWEID'];
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_courier where gweid=%s",$gweid);
		$couriers = $wpdb->get_results($sql);
		$sql = $wpdb -> prepare("SELECT COUNT(*) as couriersCount FROM {$wpdb->prefix}shopping_courier WHERE gweid =%s",$gweid);
		$couriersCount = $wpdb->get_results($sql);
		
		if(isset($_POST['courier_del']) && !empty($_POST['courier_del']) ){							
			$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_courier WHERE id=%s", $_POST['courierid']));			
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$hint = array("status"=>"success","message"=>"");
			}
			echo json_encode($hint);
			exit;	
			
		}		
		include $this -> template('courierslist');	
	}
	//后台管理--获取物流公司当页的所有数据集
	public function doWebCountcourierspage($offset,$pagesize,$gweid){		
		global $_W, $_GPC ,$wpdb;
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_courier WHERE GWEID = %s ORDER BY id limit %d,%d",$gweid,$offset,$pagesize);
		$myrows = $wpdb->get_results($sql);	
		return $myrows;
	}
	public function doWebCouriershandle(){
	
	    global $_W, $_GPC ,$wpdb;
		global $current_user;
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;				
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$gweid=$_SESSION['GWEID'];
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_courier where id=%s",$_POST['courierid']);
		$couriers = $wpdb->get_results($sql);
		
		//添加或更新物流公司
		if(isset($_POST['courier_id']) && !empty($_POST['courier_id']) ){	
			$wpdb->update( $wpdb->prefix.'shopping_courier', array('courier_name'=>$_POST['courier_name']),array('id'=>$_POST['courier_id']), array('%s'),array('%s'));
		?>	
		<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<script>
				alert('提交成功');	
				setTimeout('self.close()',0);	
				opener.location.reload();
			</script>
			</head>
			</html>
		<?php	exit;
		}else if(isset($_POST['courier_name']) && !empty($_POST['courier_name'])){
			$courierid=time().rand(111,999);
			$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_courier(id,courier_name,gweid)VALUES (%s,%s,%s)",$courierid,$_POST['courier_name'],$gweid));
		?>	<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<script>
				alert('提交成功');	
				setTimeout('self.close()',0);	
				opener.location.reload();
			</script>
			</head>
			</html>
		<?php	exit;
		}
		
		include $this -> template('couriersmanage');	
	}
	
	//微信交互--向微信发送发货通知
	public function doWebDeliveryWePay($orderid,$gweid,$upstatus){
		/*检查是否已经给微信发送发货通知*/
		/*发货*/
		global $_W, $_GPC ,$wpdb;
			
		$weixin = new WeixinPay($gweid);
		
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$orderid);
		$orders = $wpdb->get_results($sql);

		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_wepay WHERE out_trade_no = %s",$orderid);
		$orders_wepay = $wpdb->get_results($sql);
		if(is_array($orders) && !empty($orders)){
			foreach($orders as $order ){	
				$order_openid = $order->openid;
				$order_mid = $order->mid;		
			}
		}
		if(is_array($orders_wepay) && !empty($orders_wepay)){
			foreach($orders_wepay as $order_wepay ){	
				$order_transaction_id = $order_wepay->transaction_id;		
			}
		}
		$delivery_array=array();
		$delivery_array['openid']=$order_openid;
		$delivery_array['transid']=$order_transaction_id;
		$delivery_array['out_trade_no']=$orderid;
		$delivery_array['deliver_status']=$upstatus;
		if($upstatus=='0'){	
			$delivery_array['deliver_msg']="";
		}else{
			$delivery_array['deliver_msg']="发货出错";
		}
		$delivernotify = $weixin->delivernotify($order_openid,$order_transaction_id,$orderid);
	}
	//后台管理--获取订单用户信息
	public function doWebBuyer($mid,$openid,$gweid){	
		global $_W, $_GPC ,$wpdb;
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_member_group where from_user=%s and GWEID=%s",$openid,$gweid);
		$membergroupsinfo = $wpdb->get_results($sql);
		if(!empty($mid)){
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_member where mid=%s and GWEID=%s",$mid,$gweid);
			$myrows = $wpdb->get_results($sql);
		}else if(!empty($membergroupsinfo)){
			foreach($membergroupsinfo as $membergroupinfo){
				$mid=$membergroupinfo->mid;
			}
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_member where mid=%s and GWEID=%s",$mid,$gweid);
			$myrows = $wpdb->get_results($sql);
		}else{
			$myrows=array();
		}		
		return $myrows;
	}
	//检查是否有新订单
	public function doWebOrderCheck(){
		global $wpdb;
		$gweid=$_SESSION['GWEID'];
		$list = $wpdb -> get_results($wpdb -> prepare("SELECT  `out_trade_no` FROM {$wpdb -> prefix}shopping_order WHERE `gweid` = %s AND `read`=0 AND isshopping='0' and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') ORDER BY `time_start` DESC", $gweid),ARRAY_A);
		echo json_encode(array('new_status' => empty($list)?FALSE:TRUE));
	} 
	
	//后台管理--订单管理页面
	public function doWebOrdermanage(){
	
		global $_W, $wpdb;
		global $current_user;	
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;			
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$gweid=$_SESSION['GWEID'];
	    $this->Perdenied($gweid);
		$search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		
		if($search_condition=="delivery_status"){
			
			if($search_content=="未发货"){
				$sql=$wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') and  out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='1' or delivery_status='2') and out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and  out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order_goods WHERE isdelivery=0))",$gweid,$gweid);
			}else if($search_content=="已发货"){
				$sql=$wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  and out_trade_no in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='1')",$gweid);		
			}else if($search_content=="无需发货"){
				$sql=$wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') and out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order_goods WHERE isdelivery=0)",$gweid);		
			}else if($search_content=="收货已确认"){
				$sql=$wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') and out_trade_no in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='2')",$gweid);		
			}
			
			$total = $wpdb->get_var($sql);
			$pindex = max(1, intval($_GET['page']));
			$psize = 5;
			$pindex = min(max(ceil($total/$psize),1),$pindex );
			$offset=($pindex - 1) * $psize;
			$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
			
			if($search_content=="未发货"){
				$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') and  out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='1' or delivery_status='2') and out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order_goods WHERE isdelivery=0)) ORDER BY out_trade_no desc limit %d,%d",$gweid,$gweid,$offset,$psize);
			}else if($search_content=="已发货"){
				$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') and  out_trade_no in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='1') ORDER BY out_trade_no desc limit %d,%d",$gweid,$offset,$psize);		
			}else if($search_content=="无需发货"){//SELECT out_trade_no FROM {$wpdb->prefix}shopping_order_goods 排除native支付方式不在order_goods表
				$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') and  out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order_goods WHERE isdelivery=0) ORDER BY out_trade_no desc limit %d,%d",$gweid,$offset,$psize);		
			}else if($search_content=="收货已确认"){
				$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') and  out_trade_no in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='2') ORDER BY out_trade_no desc limit %d,%d",$gweid,$offset,$psize);		
			}
			$list = $wpdb->get_results($sql);
			
		}else{		
			if($search_condition=="payment_type"){
				if($search_content=="货到付款"){
					$search_content="0";
				}else if($search_content=="微信支付"){
					$search_content="1";
				}
			}			
			
			if($search_condition == 'trade_state'){
				$search_content = trim($_GET['indata_state']);
			}
			$search = array(
				'all' => '',
				'out_trade_no' => "AND out_trade_no LIKE '%%{$search_content}%%'",
				'openid' => "AND openid = '{$search_content}'",
				'openid_name' => "AND openid_name LIKE '%%{$search_content}%%'",
				'address_name' => "AND address_name LIKE '%%{$search_content}%%'",
				'trade_state' => "AND trade_state = '{$search_content}'",
			);
			
			$sql=$wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}shopping_order where gweid=%s and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search[$search_condition]} ORDER BY out_trade_no desc",$gweid);
			
			$total = $wpdb->get_var($sql);
			$pindex = max(1, intval($_GET['page']));
			$psize = 5;
			$pindex = min(max(ceil($total/$psize),1),$pindex );
			$offset=($pindex - 1) * $psize;
			$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
			
			$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order where gweid=%s and  trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  {$search[$search_condition]} ORDER BY out_trade_no desc limit {$offset},{$psize}",$gweid);
			$list = $wpdb->get_results($sql);
			
		}
		$orderarray=array();
		if(is_array($list) && !empty($list)){
			foreach($list as $order){
				$buyersinfo = $this ->doWebBuyer($order->mid,$order->openid,$gweid);
				$orderarray[$order->out_trade_no]['buyersinfo']=$buyersinfo;
				
				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_goods where out_trade_no=%s",$order->out_trade_no);
				$ordergoodsinfos = $wpdb->get_results($sql);
				$orderarray[$order->out_trade_no]['ordgoodsinfo']=$ordergoodsinfos;	

				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_delivery WHERE out_trade_no = %s",$order->out_trade_no);
				$deliverys = $wpdb->get_results($sql);
				$orderarray[$order->out_trade_no]['deliverys']=$deliverys;						
				
			}
		}
		
		include $this -> template('orderlist');
    }
	//后台管理--订单页面
	public function doWebOrderinfo(){
	
	    global $_W, $_GPC ,$wpdb;

	    $gweid = $_SESSION['GWEID'];
		$orderid = $_GET['orderid'];
		$this->Perdenied($gweid);
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$orderid);
		$orders = $wpdb->get_results($sql);
		
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_wepay WHERE out_trade_no = %s",$orderid);
		$orders_wepay = $wpdb->get_results($sql);
		
		
		
		//订单信息
		if(is_array($orders) && !empty($orders)){	
			foreach($orders as $order ){	
				$order_openid = $order->openid;
				$order_openid_name = $order->openid_name;
				$order_mid = $order->mid;
				$order_payment_type = $order->payment_type;
				$order_fee = $order->fee;
				$order_time_start = $order->time_start;
				$order_time_expire = $order->time_expire;
				$order_time_end = $order->time_end;
				$order_address = $order->address;
				$order_address = json_decode($order_address,true);
				$order_trade_state = $order->trade_state;
				$order_read = $order->read;
				$order_iserror=$order->iserror;
				$order_error_description=$order->error_description;
				$order_description=$order->description;
			}
		}
		if(is_array($orders_wepay) && !empty($orders_wepay)){
			foreach($orders_wepay as $order_wepay ){	
				$order_product_id = $order_wepay->product_id;
				$order_body = $order_wepay->body;
				$order_goods_tag = $order_wepay->goods_tag;
				$order_goods_type = $order_wepay->goods_type;
				$order_trade_type = $order_wepay->trade_type;
				$order_fee_type = $order_wepay->fee_type;
				$order_bank_type = $order_wepay->bank_type;
				$order_coupon_fee = $order_wepay->coupon_fee;
				$order_send_type = $order_wepay->send_type;				
			}
		}
		/*买家信息*/		
		$buyersinfo = $this ->doWebBuyer($order_mid,$order_openid,$gweid);
		//$trade_states=$this ->doWebTradeState();
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_goods where out_trade_no=%s",$orderid);
		$ordergoodsinfos = $wpdb->get_results($sql);
		
		
		$paytype_display="0";//网页支付
		foreach($ordergoodsinfos as $ordergoodsinfo){
			$gid=$ordergoodsinfo->goods_id;
			
			$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_goods WHERE id=%s",$gid);
			$good_display = $wpdb->get_row($sql,ARRAY_A);
			
			$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_goodsindex WHERE id=%s",$good_display['groupid']);
			$paytype_dis = $wpdb->get_row($sql,ARRAY_A);
			$goodsindexid_display=$paytype_dis['id'];
			if($paytype_dis['type']=='NATIVE'){
				$paytype_display="1";//原生支付
				break;
			}
		}
		
		
		
		
		$needdelivery=1;//如果购买的商品中有需要发货的，则显示发货信息和收货信息
		$needdiscount=0;//是否显示优惠信息(如果购买的商品全是自己手动输入金额的，则不显示优惠信息)
		foreach($ordergoodsinfos as $ordergoodsinfo){
			$isdelivery=$ordergoodsinfo->isdelivery;
			$ismanual=$ordergoodsinfo->ismanual;
			if($isdelivery==0){
				$needdelivery=0;
			}
			if($ismanual==0){//只要有不是人工输入金额的就显示优惠信息
				$needdiscount=1;
			}
		}
		
		//发货信息
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_delivery WHERE out_trade_no = %s",$orderid);
		$deliverys = $wpdb->get_results($sql);
		foreach($deliverys as $delivery ){	
			$delivery_compid = $delivery->delivery_compid;
			$delivery_sn = $delivery->delivery_sn;
			$delivery_timestamp = $delivery->delivery_timestamp;
			$delivery_status = $delivery->delivery_status;
			$delivery_msg = $delivery->delivery_msg;
		}
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_courier where gweid=%s",$gweid);
		$couriers = $wpdb->get_results($sql);
		//发货信息end
		
		
		//优惠信息
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_discount where out_trade_no=%s",$orderid);
		$discount_list = $wpdb->get_results($sql);
		
		$discount_fee = max($wpdb -> get_var( $wpdb -> prepare("SELECT sum(discount_price) FROM  {$wpdb->prefix}shopping_order_discount WHERE  out_trade_no=%s",$orderid) ) , 0);
		$discount_fee = max($discount_fee , 0);
		$discount_fee = number_format($discount_fee,2,'.','');//优惠信息end
		
		//删除优惠信息
		if(isset($_POST['discountdel']) && !empty($_POST['discountdel']) ){							
			$discountid=$_POST['discountid'];
			$orderid=$_POST['orderid'];
			$sql = $wpdb -> prepare("SELECT discount_price FROM  {$wpdb->prefix}shopping_order_discount WHERE  id=%d",$discountid);
			$discount_price = $wpdb->get_var($sql);
			$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_order_discount WHERE id=%d",$discountid));			
			
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$sql = $wpdb -> prepare("SELECT fee FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$orderid);
				$fee = $wpdb->get_var($sql);
				$totalfee=$fee+$discount_price;
				$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('fee'=>$totalfee),array('out_trade_no'=>$orderid), array('%f'), array('%s'));
				if($update===false){
					$hint = array("status"=>"error","message"=>"删除失败");
				}else{
					$hint = array("status"=>"success","message"=>"删除成功");
				}
			}
			echo json_encode($hint);
			exit;	
		}
		
		$sql = $sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_refund WHERE  out_trade_no=%s ORDER BY out_refund_no",$orderid);
		$refund_list = $wpdb->get_results($sql,ARRAY_A);
		if(!$order_read)
			$wpdb -> update("{$wpdb -> prefix}shopping_order",array('read' => 1),array('out_trade_no' => $orderid));
        include $this -> template('orderinfo');
    }
	//后台管理--发货管理页面
	public function doWebDelivery(){
	
	    global $_W, $_GPC ,$wpdb;
		//获取session中的gweid
	    $gweid = $_SESSION['GWEID'];
		$orderid = $_GET['orderid'];
				
		if( isset($_POST['delivery_status'])){
			$orderid = $_POST['orderid'];
			$delivery_compid = $_POST['delivery_compid'];
			$delivery_sn = $_POST['delivery_sn'];
			$delivery_status = $_POST['delivery_status'];
			$delivery_msg = $_POST['delivery_msg'];
			
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_delivery where out_trade_no=%s",$orderid);
			$deliversinfo = $wpdb->get_results($sql);
			if((!empty($deliversinfo))&&($delivery_status=='0')){			
				$upstatus=$wpdb->update( $wpdb->prefix.'shopping_delivery', array('delivery_compid'=>$delivery_compid,'delivery_sn'=>$delivery_sn,'delivery_status'=>$delivery_status,'delivery_msg'=>$delivery_msg,'delivery_timestamp'=> date('Y-m-d H:i:s')),array('out_trade_no'=>$orderid), array('%s','%s','%s','%s'),array('%s'));
			
			}else if((!empty($deliversinfo))&&($delivery_status!='0')){
				$upstatus=$wpdb->update( $wpdb->prefix.'shopping_delivery', array('delivery_compid'=>$delivery_compid,'delivery_sn'=>$delivery_sn,'delivery_status'=>$delivery_status,'delivery_msg'=>$delivery_msg,'delivery_timestamp'=> date('Y-m-d H:i:s')),array('out_trade_no'=>$orderid), array('%s','%s','%s','%s'),array('%s'));
				//调用发货通知给微信--待测
				//$this -> doWebDeliveryWePay($orderid,$gweid,$upstatus);
				
			}else{ 
				$upstatus=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_delivery(out_trade_no,delivery_compid,delivery_sn,delivery_status,delivery_msg)VALUES (%s,%s, %s, %s, %s)",$orderid,$delivery_compid,$delivery_sn,$delivery_status,$delivery_msg));
			}
			
			?>
			<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
				<html>
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<script language='JavaScript'>
			<?php if($upstatus===false){?>
						alert("提交失败");
			<?php }else{?>
					alert('提交成功');	
					setTimeout('self.close()',0);	
					opener.location.reload();			
			<?php }?>
				</script>	
			</head>
				</html>
		<?php 
			/*<script>
				location.href='<?php echo $this->createWebUrl('ordermanage',array());?>';
			</script>*/
		
		}
		//用于未付款但发货的提醒
		$sql = $wpdb -> prepare("SELECT trade_state FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$orderid);
		$trade_state = $wpdb->get_var($sql);
		
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_delivery WHERE out_trade_no = %s",$orderid);
		$deliverys = $wpdb->get_results($sql);
		foreach($deliverys as $delivery ){	
			$delivery_compid = $delivery->delivery_compid;
			$delivery_sn = $delivery->delivery_sn;
			$delivery_timestamp = $delivery->delivery_timestamp;
			$delivery_status = $delivery->delivery_status;
			$delivery_msg = $delivery->delivery_msg;
		}
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_courier where gweid=%s",$gweid);
		$couriers = $wpdb->get_results($sql);
		
        include $this -> template('deliverymanage');
    }
	//后台管理--修改发货地址
	public function doWebAddressupdate(){		
		global $_W, $_GPC ,$wpdb;
		//获取session中的gweid
	    $gweid = $_SESSION['GWEID'];
		$orderid = $_GET['orderid'];
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$orderid);
		$orders = $wpdb->get_results($sql);
		
		foreach($orders as $order ){
			$order_address = $order->address;
			$order_address = json_decode($order_address,true);
		}
		if( isset($_POST['username']) &&!empty($_POST['username'])){
			$orderid = $_POST['orderid'];
			$username = $_POST['username'];
			$telnumber = $_POST['telnumber'];
			$postalcode = $_POST['postalcode'];
			$stagename = $_POST['stagename'];
			$detailinfo = $_POST['detailinfo'];
			
			$orderaddress=array();
			$orderaddress['username']=$username;
			$orderaddress['telnumber']=$telnumber;
			$orderaddress['postalcode']=$postalcode;
			$orderaddress['stagename']=$stagename;
			$orderaddress['detailinfo']=$detailinfo;
			$orderaddress=json_encode($orderaddress);			
			$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('address'=>$orderaddress,'address_name'=>$username),array('out_trade_no'=>$orderid), array('%s','%s'),array('%s'));
			

			?>
			<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
				<html>
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<script language='JavaScript'>
			<?php if($update===false){?>
						alert("提交失败");
			<?php }else{?>
					alert('提交成功');	
					setTimeout('self.close()',0);	
					opener.location.reload();			
			<?php }?>
				</script>	
			</head>
				</html>
		<?php 
		}
		include $this -> template('addressupdate');
	}
	//后台管理--优惠信息
	public function doWebCreateDiscount(){
    	global $_W,$wpdb;
		$gweid = $_SESSION['GWEID'];
    	$DISCOUNT_TYPE = array('' => '',
					'MANUAL' => '人工优惠',
					'SCRATCHCARD' => '刮刮卡',
					'OTHER' => '刮刮卡');
    	$orderid = $_GET['orderid'];
		$discountid = $_GET['discountid'];
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$orderid);
		$order = $wpdb->get_row($sql,ARRAY_A);
		$max_discount_fee = $order['fee'] - max($wpdb -> get_var( $wpdb -> prepare("SELECT sum(discount_price) FROM  {$wpdb->prefix}shopping_order_discount WHERE  out_trade_no=%s ORDER BY out_refund_no",$orderid) ) , 0);
		$max_discount_fee = max($max_discount_fee , 0);
		$max_discount_fee = number_format($max_discount_fee,2,'.','');
		
		//当前订单金额
		$sql = $wpdb -> prepare("SELECT fee FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$orderid);
		$fee = $wpdb->get_var($sql);
		//优惠信息
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_discount WHERE id=%d",$discountid);
		$discountinfo = $wpdb->get_row($sql);
			
    	if($_W['ispost']){
    		//创建优惠
			if(empty($discountid)){
				$wpdb -> insert($wpdb->prefix.'shopping_order_discount',array(
					'out_trade_no' => $orderid,
					'discount_type' => $_POST['discount_type'],
					'discount_price' => $_POST['discount_price'],
					'time_start' => date('Y-m-d H:i:s')
					));
				$totalfee=$fee-$_POST['discount_price'];
			}else{//更新优惠
				$totalfee=$fee-($_POST['discount_price']-$discountinfo->discount_price);
				$update=$wpdb->update( $wpdb->prefix.'shopping_order_discount', array('discount_type'=>$_POST['discount_type'],'discount_price'=>$_POST['discount_price']),array('id'=>$discountid), array('%s','%f'),array('%d'));
			}
			$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('fee'=>$totalfee),array('out_trade_no'=>$orderid), array('%f'),array('%s'));
		
			?>
			<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
				<html>
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<script language='JavaScript'>
			<?php if($update===false){?>
						alert("提交失败");
			<?php }else{?>
					alert('提交成功');	
					setTimeout('self.close()',0);	
					opener.location.reload();			
			<?php }?>
				</script>	
			</head>
				</html>
		<?php 
		
		}
		
    	include $this -> template('creatediscount');
    }
	/**
	 function：订单部分mobile端
	*/
	
	//微信交互--通知回调--待测
	public function doMobilePaidNotify(){
		
		global $_W, $_GPC ,$wpdb;
	    $gweid = $_GET['gweid'];		
		$weixin = new WeixinPay($gweid);
		//获取微信异步通知
		$xml = $weixin->paid_notify(@file_get_contents('php://input'));
		$out_trade_no = $xml['out_trade_no'];
		$trade_status = "SUCCESS";
		
		//首先检查对应业务数据的状态，判断该通知是否已经处理过，如果没有处理过再进行处理，如果处理过直接返回结果成功
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order where out_trade_no= %s",$out_trade_no);
		$orderinfo = $wpdb->get_row($sql);
		if($orderinfo->trade_state=='SUCCESS'){
			echo $weixin->arrayToXml(array('return_code' => 'SUCCESS'));
		}else{
			//$isweixin = $weixin->verifyNotify($xml);
			$isweixin = true;
			if($isweixin){	
				//做一次订单查询
				$orderquery=$weixin->order_query($out_trade_no);
				if($orderquery!=false){	
					if($orderquery['return_code']=="SUCCESS"){	
						if($orderquery['result_code']=="SUCCESS"){
							if($orderquery['trade_state']=="SUCCESS"){
								$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>$orderquery['trade_state'],'time_end'=>empty($orderquery['time_end'])?NULL:$orderquery['time_end'],'iserror'=>0,'error_description'=>''),array('out_trade_no'=>$out_trade_no),array('%s','%s'),array('%s'));
							}else{
								$update=$wpdb->query( $wpdb->prepare("update {$wpdb->prefix}shopping_order set trade_state=%s,time_end=null,iserror=0,error_description='' where out_trade_no=%s",$orderquery['trade_state'],$out_trade_no));
							}
							$update=$wpdb->update( $wpdb->prefix.'shopping_order_wepay', array('bank_type'=>$orderquery['bank_type'],'fee_type'=>$orderquery['fee_type']),array('out_trade_no'=>$out_trade_no), array('%s','%s'),array('%s'));
							if($orderquery['trade_state']=="SUCCESS"){
								echo $weixin->arrayToXml(array('return_code' => 'SUCCESS'));
							}else{
								echo $weixin->arrayToXml(array('return_code' => 'FAIL'));
							}
						}else{
							echo $weixin->arrayToXml(array('return_code' => 'FAIL'));
						}
					}else{
						$errordes=$orderquery['return_msg'];//不展示给微信用户
						$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>"ERROR_WARNING",'iserror'=>1,'error_description'=>$errordes),array('out_trade_no'=>$out_trade_no),array('%s'),array('%s'));
						echo $weixin->arrayToXml(array('return_code' => 'FAIL'));
					}
				}else{//订单查询没有获取到任何微信返回
					$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>"ERROR_WARNING",'iserror'=>1,'error_description'=>"订单状态查询失败"),array('out_trade_no'=>$out_trade_no),array('%s'));
					echo $weixin->arrayToXml(array('return_code' => 'FAIL'));
				}
			}else{
				echo $weixin->arrayToXml(array('return_code' => 'FAIL'));
			}
		}
	}
	
	//手机--商品页面
	public function doMobileGoodsinfo(){
		global $_W, $_GPC ,$wpdb;
	    $shoppingtitle='项目详情';
		$gweid = $_GET['gweid'];
		$this->Perdenied($gweid);
		
		//商品组标识
		$goodsgid=$_GET['goodsgid'];
		//获取商品组未下架商品信息
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods where groupid= %d and status=0 order by createtime",$goodsgid);
		$goodsgs = $wpdb->get_results($sql);
		$disgoodsid=$_GET['disgoodsid'];
		$goodsarray=array();
		if(is_array($goodsgs) && !empty($goodsgs)){
			foreach($goodsgs as $goodsg){
				$goodsid=$goodsg->id;
				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods where id= %d",$goodsid);
				$goods = $wpdb->get_results($sql);		
				foreach($goods as $goodsinfo){
					$goodsarray[$goodsid]['id']=$goodsinfo->id;
					$goodsarray[$goodsid]['title']=$goodsinfo->title;
					$goodsarray[$goodsid]['market_price']=$goodsinfo->market_price;//市场价格
					$goodsarray[$goodsid]['vip_price']=$goodsinfo->vip_price;//会员价格
					$goodsarray[$goodsid]['isvipprice']=$goodsinfo->isvipprice;
					$goodsarray[$goodsid]['ismanual']=$goodsinfo->ismanual;//是否手动输入价格
					$goodsarray[$goodsid]['description']=$goodsinfo->description;//商品描述
					$goodsarray[$goodsid]['goodssn']=$goodsinfo->goodssn;
					$goodsarray[$goodsid]['total']=$goodsinfo->total;
					
					$upload =wp_upload_dir();
					if((empty($goodsinfo->thumb))||(stristr($goodsinfo->thumb,"http")!==false)){
						$goodsthumb=$goodsinfo->thumb;
					}else{
						$goodsthumb=$upload['baseurl'].$goodsinfo->thumb;
					}
				
					$goodsarray[$goodsid]['thumb']=$goodsthumb;
					$goodsarray[$goodsid]['type']=$goodsinfo->type;
				}
				if(empty($disgoodsid)){
					$disgoodsid=$goodsid;
				}
			}
		}
		//点击立即支付时执行，如果没有商品任何商品信息，同样不会执行，不会进行页面跳转
		if(!empty($_POST['forgoodsid'])){
			$manual_price=base64_encode($_POST['manual_price']);
			
		?><script>
			location.href='<?php echo $this->createMobileUrl('goodspay',array('gweid' => $gweid,'goodsgid'=>$goodsgid,'goodsid' => $_POST['forgoodsid'],'p' => $manual_price));?>';
		</script>
		<?php }
		
		include $this -> template('goodsinfo');
	}
	//手机--商品提交订单页面
	public function doMobileGoodspay(){
		global $_W, $_GPC ,$wpdb;
	    $shoppingtitle='下订单';
		//GET基本信息
		$gweid = $_GET['gweid'];
		//$this->Perdenied($gweid);
		$goodsgid=$_GET['goodsgid'];
		$goodsid=$_GET['goodsid'];
		$manual_price = base64_decode($_GET['p']);//用户输入的价格
		$weixin = new WeixinPay($gweid);
		//获取fromuser
		$fromuser=$_SESSION['oauth_openid']['openid'];
		if(!empty($_GET['errorcode'])){
			include $this -> template('oauth_error');
			exit;
		}
		if(empty($fromuser)||($_SESSION['oauth_openid']['gweid']!=$gweid)||empty($_SESSION['addaccesstoken'])||(time()-60>=$_SESSION['expires_time'])){
			$weixin->isoauth2_base($gweid);
			$fromuser=$_SESSION['oauth_openid']['openid'];
		}
		//get userinfo
		$user=$weixin->userinfo($fromuser);
		$wechatname=$user['nickname'];
		
		//判断是否会员，从而显示会员价格
		$mid = $_W['fans']['mid'];
		$buyer=$this->doWebBuyer($mid,$fromuser,$gweid);
		
		//共享收货地址
		$data = array();
		$addr=$weixin->addrsign($_SESSION['addaccesstoken']);
		$data['appId'] = $addr['appid'];
		$data['timeStamp'] = $addr['timestamp'];
		$data['nonceStr'] = $addr['noncestr'];
		$data['addrSign'] = $addr['addrsign'];
		
		//默认显示购买的商品数量
		$goodstotal=1;
		
		//获取购买商品的信息，不限定是否下架。如果下架，商品同样显示只是提示下架并不可购买
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods where id= %d",$goodsid);
		$goods = $wpdb->get_results($sql);		
		if(is_array($goods) && !empty($goods)){
			foreach($goods as $goodsinfo){
				$title=$goodsinfo->title;
				$isdelivery=$goodsinfo->isdelivery;
				$ismanual=$goodsinfo->ismanual;
				$total=$goodsinfo->total;
				$price=$goodsinfo->market_price;//市场价格
				$vip_price=$goodsinfo->vip_price;//会员价格
				$isvipprice=$goodsinfo->isvipprice;
				if(!empty($buyer)&&($isvipprice=='1')){
					$price=$goodsinfo->vip_price;
					$marketprice=$goodsinfo->market_price;
				}
				$description=$goodsinfo->description;//商品描述
				$goodssn=$goodsinfo->goodssn;//
				$thumb=$goodsinfo->thumb;//商品描述
				$goodstatus=$goodsinfo->status;//是否下架
			}
		}
		$totalfee=$price*intval($goodstotal);//总价格
		
		//显示默认收货地址
		if($isdelivery!=1){
			$address = $wpdb -> get_var( $wpdb -> prepare("SELECT address FROM  {$wpdb->prefix}shopping_order WHERE  openid=%s and time_start in (select max(time_start) from {$wpdb->prefix}shopping_order WHERE  openid=%s and address_name !='' )",$fromuser,$fromuser));
			$order_address = json_decode($address,true);
		}
		
		//订单生成入库
		if(!empty($_POST['order_add'])){
			//入库时生成订单号，非页面加载时生成
			$out_trade_no=time().rand(111111,999999);
			//获取要生成订单的信息
			$gweid=$_POST['gweid'];
			$goodsid=$_POST['goodsid'];
			$goodsprice=$_POST['goods_price'];
			$goodstotal=$_POST['goodstotal'];
			$totalfee=$_POST['totalfee'];
			$address=$_POST['address'];
			if($isdelivery==1){
				$address="";
			}
			$orderaddress=array();
			$orderaddress['username']=$address['0'];
			$orderaddress['telnumber']=$address['1'];
			$orderaddress['postalcode']=$address['2'];
			$orderaddress['stagename']=$address['3'];
			$orderaddress['detailinfo']=$address['4'];
			$orderaddress=json_encode($orderaddress);
			$payment_type="1";
			$trade_state="PAYING";
			$fee_type="1";
			
			//Native or JSAPI
			$sql = $wpdb -> prepare("SELECT type FROM  {$wpdb->prefix}shopping_goodsindex WHERE  GWEID=%s and id = %s",$gweid,$goodsgid);
			$trade_type = $wpdb->get_var($sql);
			$trade_type = ($trade_type == 'JSAPI'?'JSAPI':'NATIVE_ORDER');
			
			//一些其他信息
			$goods = $wpdb -> get_row($wpdb -> prepare("SELECT  * FROM {$wpdb -> prefix}shopping_goods WHERE id = %s", $goodsid));
			if($goods->status==1){//删除了商品或删除了网页支付链接
				$hint = array("status"=>"error","message"=>"该商品已经下架");
				echo json_encode($hint);
				exit;
			}else{
				$uptotal=$total-$goodstotal;
				if(($total!='-1')&&($goods->ismanual!='1')&&($uptotal<0)){//设置了最大库存并且不是手动输入才对库存做操作
					$hint = array("status"=>"error","message"=>"库存不足，请重新选择购买数量");
					echo json_encode($hint);
					exit;
				}else{//先插入order_goods表
					if($goods->ismanual=='1'){
						$send_type="NOTDELIVERY";
						$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_order_goods(out_trade_no,goods_id,goods_title,goods_thumb,goods_price,total,total_price,isdelivery,ismanual,isvipprice)VALUES (%s, %s,%s,%s, %f,%s,%f,%d,%d,%d)",$out_trade_no,$goodsid,$goods->title,$goods->thumb,"","",$totalfee,$goods->isdelivery,$goods->ismanual,$goods->isvipprice));
					}else{
						$send_type="DELIVERY";
						$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_order_goods(out_trade_no,goods_id,goods_title,goods_thumb,goods_price,total,total_price,isdelivery,ismanual,isvipprice)VALUES (%s, %s,%s,%s, %f,%s,%f,%d,%d,%d)",$out_trade_no,$goodsid,$goods->title,$goods->thumb,$goodsprice,$goodstotal,$totalfee,$goods->isdelivery,$goods->ismanual,$goods->isvipprice));
					}
					if($insert===false){//插入order goods表失败	
						$hint = array("status"=>"error","message"=>"出现错误");
						echo json_encode($hint);
						exit;
					}else{//插入order_wepay
						$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_order_wepay(out_trade_no,fee_type,send_type,trade_type)VALUES (%s,%s,%s,%s)",$out_trade_no,$fee_type,$send_type,$trade_type));
						if($insert===false){
							$hint = array("status"=>"error","message"=>"出现错误");
							echo json_encode($hint);
							exit;
						}else{
							$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_order(out_trade_no,gweid,openid,openid_name,mid,payment_type,fee,trade_state,address,address_name)VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",$out_trade_no,$gweid,$fromuser,$wechatname,$mid,$payment_type,$totalfee,$trade_state,$orderaddress,$address['0']));
							if($insert===false){
								$hint = array("status"=>"error","message"=>"出现错误");
								echo json_encode($hint);
								exit;
							}else{
								if(($total!='-1')&&($goods->ismanual!='1')){//设置了最大库存并且不是手动输入才对库存做操作
									$uptotal=$total-$goodstotal;
									$update=$wpdb->update( $wpdb->prefix.'shopping_goods', array('total'=>$uptotal),array('id'=>$goodsid),array('%s'));
									if($update===false){//订单生成了，但库存更新失败	
										//插入告警表
										$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_alarm(gweid,description,errortype,alarmcontent)VALUES (%s,%s,%s,%s)",$gweid,"订单库存更新失败",'1010',"请为商品(编号".$goodsid.")减少库存个数".$goodstotal));
									}
									$hint = array("status"=>"success","message"=>"","url"=>$this->createMobileUrl('goodspayConfirm',array('gweid' => $gweid,'orderid' => $out_trade_no,'goodsgid'=>$goodsgid)));
									echo json_encode($hint);
									exit;
									
								}else{
									$hint = array("status"=>"success","message"=>"","url"=>$this->createMobileUrl('goodspayConfirm',array('gweid' => $gweid,'orderid' => $out_trade_no,'goodsgid'=>$goodsgid)));
									echo json_encode($hint);
									exit;
								}
							}				
						}
					}
				}
			}
		}
		include $this -> template('goodspay');
	}
	
	public function doMobileSdkTestCreateOrder(){
		$gweid=$_GET['gweid'];
		//$this->Perdenied($gweid);
		$weixin = new WeixinPay($gweid);
		/*订单查询测试ok
		$out_trade_no="1414637348923048";
		$data = $weixin -> order_query($out_trade_no);
		var_dump($data);
		
		$weixin = new WeixinPay($gweid);		
		$data = array();
		$addr=$weixin->addrsign();
		
		var_dump($addr);*/
		$this->wechatOrderSearch();
	}

	//手机--商品确认订单支付页面
	public function doMobileGoodspayConfirm(){
		global $_W, $_GPC ,$wpdb;
		$shoppingtitle='支付';
		//GET信息
		$gweid = $_GET['gweid'];
		//$this->Perdenied($gweid);
		$goodsgid=$_GET['goodsgid'];
		$out_trade_no=$_GET['orderid'];
		$weixin = new WeixinPay($gweid);
		//获取fromuser
		$fromuser=$_SESSION['oauth_openid']['openid'];
		if(!empty($_GET['errorcode'])){
			include $this -> template('oauth_error');
			exit;
		}
		if(empty($fromuser)||($_SESSION['oauth_openid']['gweid']!=$gweid)){
			$weixin->isoauth2_base($gweid);
			$fromuser=$_SESSION['oauth_openid']['openid'];
		}
		
		//判断是否会员，从而显示会员价格
		$mid = $_W['fans']['mid'];
		$buyer=$this->doWebBuyer($mid,$fromuser,$gweid);
		
				
		//判断支付类型
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_wepay WHERE  out_trade_no=%s",$out_trade_no);
		$tradetype = $wpdb->get_row($sql);

		//对订单的优惠信息（不是针对商品）
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_discount where out_trade_no=%s",$out_trade_no);
		$discount_list = $wpdb->get_results($sql);		
		$discount_fee = max($wpdb -> get_var( $wpdb -> prepare("SELECT sum(discount_price) FROM  {$wpdb->prefix}shopping_order_discount WHERE  out_trade_no=%s",$out_trade_no) ) , 0);
		$discount_fee = max($discount_fee , 0);
		$discount_fee = number_format($discount_fee,2,'.','');
		//优惠信息end	
			
		//订单信息
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$out_trade_no);
		$orders = $wpdb->get_results($sql);
		if(is_array($orders) && !empty($orders)){
			foreach($orders as $order){
				$totalfee=$order->fee;
				$order_address = $order->address;
				$order_address = json_decode($order_address,true);
				$description = $order->description;//订单描述
				$orderfromuser= $order->openid;
			}	
		}
		//订单商品信息
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_goods where out_trade_no=%s",$out_trade_no);
		$ordergoodsinfos = $wpdb->get_results($sql);
		
		$needdelivery=1;//默认不发货
		$needdiscount=0;//默认不需要优惠
		$goodsarray=array();
		$description=" ";
		foreach($ordergoodsinfos as $ordergoodsinfo){
			
			$ordergoodsid=$ordergoodsinfo->goods_id;
			$ordergoodstotal=$ordergoodsinfo->total;
			$ordergoodstotal_price=$ordergoodsinfo->total_price;
			$description=$description.$ordergoodsinfo->goods_title;
			
			
			$isdelivery=$ordergoodsinfo->isdelivery;
			$ismanual=$ordergoodsinfo->ismanual;
			if($isdelivery==0){//如果购买的商品中包含需要发货的商品则显示
				$needdelivery=0;
			}
			if($ismanual==0){//只要有不是人工输入金额的就显示优惠信息
				$needdiscount=1;
			}
			$goodsid=$ordergoodsinfo->goods_id;
			$goodsarray[$goodsid]['id']=$ordergoodsinfo->goods_id;
			$goodsarray[$goodsid]['title']=$ordergoodsinfo->goods_title;
			$goodsarray[$goodsid]['market_price']=$ordergoodsinfo->goods_price;//市场价格
			$goodsarray[$goodsid]['ismanual']=$ordergoodsinfo->ismanual;//是否手动输入价格
			
			
			$upload =wp_upload_dir();
			if((empty($ordergoodsinfo->goods_thumb))||(stristr($ordergoodsinfo->goods_thumb,"http")!==false)){
				$goodsthumb=$ordergoodsinfo->goods_thumb;
			}else{
				$goodsthumb=$upload['baseurl'].$ordergoodsinfo->goods_thumb;
			}
			$goodsarray[$goodsid]['thumb']=$goodsthumb;
			
		}
		
		//微信支付
		$data = array(
			'body' => $description,//订单描述(不能为空，否则微支付出错)
			//'openid' => "oZNvzjoc536CwKn5jNT7Hf1P-o3s",
			'openid' => $orderfromuser,
			'out_trade_no' => $out_trade_no,//订单号，需保证该字段对于本商户的唯一性
			'total_fee' =>  (string)($totalfee*100), //支付金额 单位：分
			'notify_url'=>$this->createMobileUrl('paidNotify',array('gweid' =>$gweid)),//支付成功后将通知该地址
			'spbill_create_ip' => $_SERVER['REMOTE_ADDR']
			
		);
		
		if(!empty($_POST['order_pay'])){
			if($tradetype->trade_type == 'JSAPI'){
				//待测
				$creorder=$weixin->create_order($data, true);
				$data['resultUrl']=$this->createMobileUrl('goodspay_result',array('gweid' => $gweid,'out_trade_no' => $out_trade_no));
				if($creorder!=false){	
					if($creorder['return_code']=="SUCCESS"){
						if($creorder['result_code']=="SUCCESS"){
						
							$data['appid']=$creorder['appid'];
							$data['package']="prepay_id=".$creorder['prepay_id'];
							$data['sign']=$creorder['sign'];
							$data['nonce_str']=$creorder['nonce_str'];
							$data['trade_type']=$creorder['trade_type'];
							$data['return_code']=$creorder['return_code'];
							$data['result_code']=$creorder['result_code'];
							$jsapi_data=$weixin->JSAPI($creorder['prepay_id']);
							$hint = array("status"=>"success","message"=>"提交成功","data"=>$data,"jsapi_data"=>$jsapi_data);
							
							echo json_encode($hint);
							exit;
						}else{
							$hint = array("status"=>"errordec","message"=>$creorder['err_code_des']);
							echo json_encode($hint);
							exit;
						}
					}else{
						$hint = array("status"=>"error","message"=>$creorder['return_msg']);
						echo json_encode($hint);
						exit;
					}
				}else{
					$hint = array("status"=>"error","message"=>"出现问题，请稍后重试");
					echo json_encode($hint);
					exit;
				}
			}
			if($tradetype->trade_type == 'NATIVE_ORDER'){
				unset($data['openid']);
				$creorder=$weixin->create_order($data, false);
				$data['trade_type']='NATIVE_ORDER';
				if($creorder!=false){	
					if($creorder['return_code']=="SUCCESS"){
						if($creorder['result_code']=="SUCCESS"){
							$data['code_url'] = $creorder['code_url'];
							$wpdb -> update("{$wpdb->prefix}shopping_order_wepay",array('code_url' => $data['code_url']),array('out_trade_no'=>$out_trade_no));
							$hint = array("status"=>"success","message"=>"提交成功","data"=>$data);
							echo json_encode($hint);
							exit;
						}else{
							$hint = array("status"=>"errordec","message"=>"提交失败，错误原因：".$creorder['err_code_des']."。","paytype"=>$tradetype->trade_type);
							echo json_encode($hint);
							exit;
						}
					}else{
						$hint = array("status"=>"error","message"=>$creorder['return_msg']);
						echo json_encode($hint);
						exit;
					}
				//$data['code_url'] = $creorder['code_url'];
				}else{
					$hint = array("status"=>"error","message"=>"出现问题，请稍后重试");
					echo json_encode($hint);
					exit;
				}
				
			}
		}
		include $this -> template('goodspay_confirm');
	}
	
	//手机--商品支付成功页面
	public function doMobileGoodspay_result(){
		global $_W, $_GPC ,$wpdb;
	    $gweid = $_GET['gweid'];
		//$this->Perdenied($gweid);
		$out_trade_no = $_GET['out_trade_no'];
		$weixin = new WeixinPay($gweid);
		//首先检查对应业务数据的状态，判断该通知是否已经处理过，如果没有处理过再进行处理，如果处理过直接返回结果成功
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order where out_trade_no= %s",$out_trade_no);
		$orderinfo = $wpdb->get_row($sql);
		if($orderinfo->trade_state=='SUCCESS'){
			include $this -> template('goodspay_success');
		}else{
			//向微信订单查询状态，并进行页面展示,真正更新数据库的支付结果
			$orderquery=$weixin->order_query($out_trade_no);
			if($orderquery!=false){	
				if($orderquery['return_code']=="SUCCESS"){	
					if($orderquery['result_code']=="SUCCESS"){
						if($orderquery['trade_state']=="SUCCESS"){
							$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>$orderquery['trade_state'],'time_end'=>empty($orderquery['time_end'])?NULL:$orderquery['time_end'],'iserror'=>0,'error_description'=>''),array('out_trade_no'=>$out_trade_no),array('%s','%s'),array('%s'));
						}else{
							$update=$wpdb->query( $wpdb->prepare("update {$wpdb->prefix}shopping_order set trade_state=%s,time_end=null,iserror=0,error_description='' where out_trade_no=%s",$orderquery['trade_state'],$out_trade_no));
						}
						$update=$wpdb->update( $wpdb->prefix.'shopping_order_wepay', array('bank_type'=>$orderquery['bank_type'],'fee_type'=>$orderquery['fee_type']),array('out_trade_no'=>$out_trade_no), array('%s','%s'),array('%s'));
						if($orderquery['trade_state']=="SUCCESS"){
							include $this -> template('goodspay_success');
						}else{
							$errorhint=$this -> TRADE_STATE[$orderquery['trade_state']];
							include $this -> template('goodspay_error');
						}
					}else{
						$errorhint=$orderquery['err_code_des'];
						include $this -> template('goodspay_error');
					}
				}else{
					$errordes=$orderquery['return_msg'];//不展示给微信用户
					$errorhint="状态未正确获取";
					$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>"ERROR_WARNING",'iserror'=>1,'error_description'=>$errordes),array('out_trade_no'=>$out_trade_no),array('%s'),array('%s'));
					include $this -> template('goodspay_error');
				}
			}else{//订单查询没有获取到任何微信返回
				$update=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>"ERROR_WARNING",'iserror'=>1,'error_description'=>"订单状态查询失败"),array('out_trade_no'=>$out_trade_no),array('%s'));
				$errorhint="状态未正确获取";
				include $this -> template('goodspay_error');
			}
		}		
	}
	//手机--我的订单列表
	public function doMobileMyorderlist(){
		global $_W, $_GPC ,$wpdb;
		$shoppingtitle='我的订单';	    
		$gweid = $_GET['gweid'];
		//$this->Perdenied($gweid);
		$goodsgid=$_GET['goodsgid'];
		$weixin = new WeixinPay($gweid);		
		
		//获取fromuser
		$fromuser=$_SESSION['oauth_openid']['openid'];
		if(!empty($_GET['errorcode'])){
			include $this -> template('oauth_error');
			exit;
		}
		if(empty($fromuser)||($_SESSION['oauth_openid']['gweid']!=$gweid)){
			$weixin->isoauth2_base($gweid);
			$fromuser=$_SESSION['oauth_openid']['openid'];
		}
		
		
		//判断是否会员
		$mid = $_W['fans']['mid'];
		$buyer=$this->doWebBuyer($mid,$fromuser,$gweid);
		
		//所有未关闭的订单信息
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and openid = %s and trade_state!='CLOSED' and trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  order by out_trade_no desc",$gweid,$fromuser);
		$orders = $wpdb->get_results($sql);
		$orderarray=array();
		if(is_array($orders) && !empty($orders)){
			foreach($orders as $order){
				$orderid=$order->out_trade_no;
				$orderarray[$orderid]['wepay']=$wpdb->get_row($wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_wepay WHERE out_trade_no = %s",$orderid),ARRAY_A);
				$orderarray[$orderid]['out_trade_no']=$order->out_trade_no;
				$orderarray[$orderid]['fee']=$order->fee;//总金额
				$order_address = json_decode($order->address,true);//收货地址
				$orderarray[$orderid]['address']=$order_address;//收货地址
				$orderarray[$orderid]['trade_state_display']=$this -> TRADE_STATE[$order->trade_state];//交易状态
				$orderarray[$orderid]['trade_state']=$order->trade_state;//交易状态
				$orderarray[$orderid]['time_end']=$order->time_end;
				$orderarray[$orderid]['description']=$order->description;
				//获某条订单是否提交过维权信息,add 20141104
				$order_rightscounts=$wpdb -> get_var($wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}shopping_rights where (rights_status=1 OR rights_status=2) AND out_trade_no=%s",$order->out_trade_no));
				$orderarray[$orderid]['rightscounts']=$order_rightscounts;
				//对订单的优惠信息（不是针对商品）
				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_discount where out_trade_no=%s",$order->out_trade_no);
				$discount_list = $wpdb->get_results($sql);		
				$discount_fee = max($wpdb -> get_var( $wpdb -> prepare("SELECT sum(discount_price) FROM  {$wpdb->prefix}shopping_order_discount WHERE  out_trade_no=%s",$order->out_trade_no) ) , 0);
				$discount_fee = max($discount_fee , 0);
				$discount_fee = number_format($discount_fee,2,'.','');
				$orderarray[$orderid]['discount']=$discount_fee;
				//优惠信息end
				$sql = $wpdb -> prepare("SELECT * ,g1.total,g1.isdelivery,g1.ismanual,g1.isvipprice,g1.createtime FROM {$wpdb->prefix}shopping_order_goods g1 left join {$wpdb->prefix}shopping_goods g2 on g1.goods_id=g2.id where g1.out_trade_no=%s",$orderid);
				$ordergoodsinfos = $wpdb->get_results($sql);
				$orderarray[$orderid]['ordergoods']=$ordergoodsinfos;
				
				$deliveryinfo=$wpdb -> get_row($wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_delivery where out_trade_no=%s",$orderid));
				$orderarray[$orderid]['deliveryinfo']=$deliveryinfo;
			}
		}
		
		
		//取消订单
		if(!empty($_POST['order_del'])){
			$status=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>'CLOSED'),array('out_trade_no'=>$_POST['out_trade_no']),array('%s'));
			if($status===false){
				$hint = array("status"=>"error","message"=>"出现错误");
				echo json_encode($hint);
				exit;
			}else{
				
				//取消订单将库存更新
				$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_goods WHERE  out_trade_no = %s",$_POST['out_trade_no']);
				$ordergoodsinfos = $wpdb->get_results($sql);
				foreach($ordergoodsinfos as $ordergoodsinfo){
					$canceltotal=$ordergoodsinfo->total;
					$goods = $wpdb -> get_row($wpdb -> prepare("SELECT  * FROM {$wpdb -> prefix}shopping_goods WHERE id=%s", $ordergoodsinfo->goods_id));
					if(($goods->total!=-1)&&($goods->status!=1)){//如果库存不是-1,并且没有下架则更新库存
						$uptotal=$goods->total+$canceltotal;
						$update=$wpdb->update( $wpdb->prefix.'shopping_goods', array('total'=>$uptotal),array('id'=>$ordergoodsinfo->goods_id),array('%s'));
					}else{
						$update=true;
					}
				}
				if($update===false){
					//插入告警表
					$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_alarm(gweid,description,errortype,alarmcontent)VALUES (%s,%s,%s,%s)",$gweid,"订单库存更新失败",'1010',"请为商品(编号".$ordergoodsinfo->goods_id.")增加库存个数".$canceltotal));
				}
				$hint = array("status"=>"success","message"=>"取消成功");
				echo json_encode($hint);
				exit;
			}		
		}
		//确认收货
		if(!empty($_POST['order_confirmed'])){
			$status=$wpdb->update( $wpdb->prefix.'shopping_delivery', array('delivery_status'=>'2'),array('out_trade_no'=>$_POST['out_trade_no']),array('%s'));
			if($status===false){
				$hint = array("status"=>"error","message"=>"出现错误");
				echo json_encode($hint);
				exit;
			}else{
				$hint = array("status"=>"success","message"=>"确认成功");
				echo json_encode($hint);
				exit;
			}		
		}
		//支付
		if(!empty($_POST['order_pay'])){
			
			$out_trade_no=$_POST['orderid'];
			//订单信息
			$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$out_trade_no);
			$orders = $wpdb->get_results($sql);
			foreach($orders as $order){
				$totalfee=$order->fee;
			}
			
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_goods where out_trade_no=%s",$out_trade_no);
			$ordergoodsinfos = $wpdb->get_results($sql);
			$description=" ";
			foreach($ordergoodsinfos as $ordergoodsinfo){
				$ordergoodsid=$ordergoodsinfo->goods_id;
				$description=$description.$ordergoodsinfo->goods_title;
				
			}
			
			$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_wepay WHERE  out_trade_no=%s",$out_trade_no);
			$tradetype = $wpdb->get_row($sql);

			//微信支付
			$data = array(
				'body' => $description,//订单描述(不能为空，否则微支付出错)
				//'openid' => "oZNvzjoc536CwKn5jNT7Hf1P-o3s",
				'openid' => $fromuser,
				'out_trade_no' => $out_trade_no,//订单号，需保证该字段对于本商户的唯一性
				'total_fee' =>  (string)($totalfee*100), //支付金额 单位：分
				'notify_url'=>$this->createMobileUrl('paidNotify',array('gweid' =>$gweid)),//支付成功后将通知该地址
				'spbill_create_ip' => $_SERVER['REMOTE_ADDR']
				
			);
			
			if($tradetype->trade_type == 'JSAPI'){
				
				$creorder=$weixin->create_order($data, true);
				$data['resultUrl']=$this->createMobileUrl('goodspay_result',array('gweid' => $gweid,'out_trade_no' => $out_trade_no));
				if($creorder!=false){	
					if($creorder['return_code']=="SUCCESS"){
						if($creorder['result_code']=="SUCCESS"){
						
							$data['appid']=$creorder['appid'];
							$data['package']="prepay_id=".$creorder['prepay_id'];
							$data['sign']=$creorder['sign'];
							$data['nonce_str']=$creorder['nonce_str'];
							$data['trade_type']=$creorder['trade_type'];
							$data['return_code']=$creorder['return_code'];
							$data['result_code']=$creorder['result_code'];
							$jsapi_data=$weixin->JSAPI($creorder['prepay_id']);
							$hint = array("status"=>"success","message"=>"提交成功","data"=>$data,"jsapi_data"=>$jsapi_data);
							
							echo json_encode($hint);
							exit;
						}else{
							$hint = array("status"=>"errordec","message"=>$creorder['err_code_des']);
							echo json_encode($hint);
							exit;
						}
					}else{
						$hint = array("status"=>"error","message"=>$creorder['return_msg']);
						echo json_encode($hint);
						exit;
					}
				}else{
					$hint = array("status"=>"error","message"=>"出现问题，请稍后重试");
					echo json_encode($hint);
					exit;
				}
			}			
			if($tradetype->trade_type == 'NATIVE_ORDER'){
/* 				$code_url = $wpdb->get_var($wpdb -> prepare("SELECT code_url FROM  {$wpdb->prefix}shopping_order_wepay WHERE out_trade_no = %s",$out_trade_no));
				if(!empty($code_url)){
					$hint = array("status"=>"success","message"=>"提交成功","data"=>array('trade_type'=>'NATIVE_ORDER','code_url'=>$code_url));
					echo json_encode($hint);
					exit;
				} */
				unset($data['openid']);
				$creorder=$weixin->create_order($data, false);
				$data['trade_type']='NATIVE_ORDER';
				if($creorder!=false){	
					if($creorder['return_code']=="SUCCESS"){
						if($creorder['result_code']=="SUCCESS"){
							$data['code_url'] = $creorder['code_url'];
							$wpdb -> update("{$wpdb->prefix}shopping_order_wepay",array('code_url' => $data['code_url']),array('out_trade_no'=>$out_trade_no));
							$hint = array("status"=>"success","message"=>"提交成功","data"=>$data);
							echo json_encode($hint);
							exit;
						}else{
							$hint = array("status"=>"errordec","message"=>"提交失败，错误原因：".$creorder['err_code_des']."。","paytype"=>$tradetype->trade_type);
							echo json_encode($hint);
							exit;
						}
					}else{
						$hint = array("status"=>"error","message"=>$creorder['return_msg']);
						echo json_encode($hint);
						exit;
					}
				//$data['code_url'] = $creorder['code_url'];
				}else{
					$hint = array("status"=>"error","message"=>"出现问题，请稍后重试");
					echo json_encode($hint);
					exit;
				}
			}
		}
		$currentime = date("Y-m-d H:i:s");
		$currentdate = strtotime($currentime);
		include $this -> template('myorderlist');	
	}
	
	
	//手机--我的某条订单
	public function doMobileOrderdetail(){
		global $_W, $_GPC ,$wpdb;
		$shoppingtitle='订单详情';
		$gweid = $_GET['gweid'];
		//$this->Perdenied($gweid);
		$orderid = $_GET['orderid'];
		$goodsgid=$_GET['goodsgid'];
		$weixin = new WeixinPay($gweid);
		$fromuser=$_SESSION['oauth_openid']['openid'];
		if(!empty($_GET['errorcode'])){
			include $this -> template('oauth_error');
			exit;
		}
		if(empty($fromuser)||($_SESSION['oauth_openid']['gweid']!=$gweid)){
			$weixin->isoauth2_base($gweid);
			$fromuser=$_SESSION['oauth_openid']['openid'];
		}
		
		$mid = $_W['fans']['mid'];
		$buyer=$this->doWebBuyer($mid,$fromuser,$gweid);
		
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$orderid);
		$orders = $wpdb->get_results($sql);
		//获取某条订单是否有维权提交过,add 20141104
		$order_rightscounts=$wpdb -> get_var($wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}shopping_rights where (rights_status=1 OR rights_status=1) and out_trade_no=%s",$orderid));
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_wepay WHERE out_trade_no = %s",$orderid);
		$orders_wepay = $wpdb->get_results($sql);
		if(is_array($orders) && !empty($orders)){
			foreach($orders as $order ){	
				$order_openid = $order->openid;
				$order_mid = $order->mid;
				$order_payment_type = $order->payment_type;
				$order_fee = $order->fee;
				$order_time_start = $order->time_start;
				$order_time_expire = $order->time_expire;
				$order_time_end = $order->time_end;
				$order_address = $order->address;
				$order_address = json_decode($order_address,true);
				$order_trade_state = $order->trade_state;
				$order_description = $order->description;
				$orderfromuser= $order->openid;				
			}
		}
		if(is_array($orders_wepay) && !empty($orders_wepay)){
			foreach($orders_wepay as $order_wepay ){	
				$order_product_id = $order_wepay->product_id;
				$order_body = $order_wepay->body;
				$order_goods_tag = $order_wepay->goods_tag;
				$order_goods_type = $order_wepay->goods_type;
				$order_trade_type = $order_wepay->trade_type;
				$order_fee_type = $order_wepay->fee_type;
				$order_bank_type = $order_wepay->bank_type;
				$order_coupon_fee = $order_wepay->coupon_fee;
				$order_send_type = $order_wepay->send_type;
				$order_code_url = $order_wepay->code_url;
			}
		}
		//$trade_states=$this ->doWebTradeState();
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_goods where out_trade_no=%s",$orderid);
		$ordergoodsinfos = $wpdb->get_results($sql);
		
		//如果购买的商品中有需要发货的，则显示发货信息和收货信息
		$needdelivery=1;
		if(is_array($ordergoodsinfos) && !empty($ordergoodsinfos)){	
			foreach($ordergoodsinfos as $ordergoodsinfo){
				$isdelivery=$ordergoodsinfo->isdelivery;
				if($isdelivery==0){
					$needdelivery=0;
				}
			}
		}
		//发货信息
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_delivery WHERE out_trade_no = %s",$orderid);
		$deliverys = $wpdb->get_results($sql);
		if(is_array($deliverys) && !empty($deliverys)){	
			foreach($deliverys as $delivery ){	
				$delivery_compid = $delivery->delivery_compid;
				$delivery_sn = $delivery->delivery_sn;
				$delivery_timestamp = $delivery->delivery_timestamp;
				$delivery_status = $delivery->delivery_status;
				$delivery_msg = $delivery->delivery_msg;
				
			}
		}
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_courier where gweid=%s",$gweid);
		$couriers = $wpdb->get_results($sql);
		//发货信息end
		
		//优惠信息
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_discount where out_trade_no=%s",$orderid);
		$discount_list = $wpdb->get_results($sql);
		
		$discount_fee = max($wpdb -> get_var( $wpdb -> prepare("SELECT sum(discount_price) FROM  {$wpdb->prefix}shopping_order_discount WHERE  out_trade_no=%s",$orderid) ) , 0);
		$discount_fee = max($discount_fee , 0);
		$discount_fee = number_format($discount_fee,2,'.','');//优惠信息end
		
		
		$sql = $sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_refund WHERE  out_trade_no=%s ORDER BY out_refund_no",$orderid);
		$refund_list = $wpdb->get_results($sql,ARRAY_A);
       

		//取消订单
		if(!empty($_POST['order_del'])){
			$status=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>'CLOSED'),array('out_trade_no'=>$_POST['out_trade_no']),array('%s'));
			if($status===false){
				$hint = array("status"=>"error","message"=>"出现错误");
				echo json_encode($hint);
				exit;
			}else{
				//取消订单将库存更新
				$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_goods WHERE  out_trade_no = %s",$_POST['out_trade_no']);
				$ordergoodsinfos = $wpdb->get_results($sql);
				if(is_array($ordergoodsinfos) && !empty($ordergoodsinfos)){	
					foreach($ordergoodsinfos as $ordergoodsinfo){
						$canceltotal=$ordergoodsinfo->total;
						$goods = $wpdb -> get_row($wpdb -> prepare("SELECT  * FROM {$wpdb -> prefix}shopping_goods WHERE id=%s", $ordergoodsinfo->goods_id));
						if(($goods->total!=-1)&&($goods->status!=1)){///如果库存不是-1并且没有下架则更新库存
							$uptotal=$goods->total+$canceltotal;
							$update=$wpdb->update( $wpdb->prefix.'shopping_goods', array('total'=>$uptotal),array('id'=>$ordergoodsinfo->goods_id),array('%s'));
						}else{
							$update=true;
						}
					}
					if($update===false){
						//插入告警表
						$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_alarm(gweid,description,errortype,alarmcontent)VALUES (%s,%s,%s,%s)",$gweid,"订单库存更新失败",'1010',"请为商品(编号".$ordergoodsinfo->goods_id.")增加库存个数".$canceltotal));
					}
				}
				
				$hint = array("status"=>"success","message"=>"取消成功","url"=>$this->createMobileUrl('myorderlist',array('gweid' => $gweid,'goodsgid' => $goodsgid)));
				echo json_encode($hint);
				exit;
				
			}		
		}
		//确认收货
		if(!empty($_POST['order_confirmed'])){
			$status=$wpdb->update( $wpdb->prefix.'shopping_delivery', array('delivery_status'=>'2'),array('out_trade_no'=>$_POST['out_trade_no']),array('%s'));
			if($status===false){
				$hint = array("status"=>"error","message"=>"出现错误");
				echo json_encode($hint);
				exit;
			}else{
				$hint = array("status"=>"success","message"=>"确认成功");
				echo json_encode($hint);
				exit;
			}		
		}
		//支付
		if(!empty($_POST['order_pay'])){
			
			$out_trade_no=$_POST['orderid'];
			//订单信息
			$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$out_trade_no);
			$orders = $wpdb->get_results($sql);
			foreach($orders as $order){
				$totalfee=$order->fee;
			}
			
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_goods where out_trade_no=%s",$out_trade_no);
			$ordergoodsinfos = $wpdb->get_results($sql);
			$description=" ";
			if(is_array($ordergoodsinfos) && !empty($ordergoodsinfos)){
				foreach($ordergoodsinfos as $ordergoodsinfo){
					$ordergoodsid=$ordergoodsinfo->goods_id;
					$description=$description.$ordergoodsinfo->goods_title;
					
				}
			}
			$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_wepay WHERE  out_trade_no=%s",$out_trade_no);
			$tradetype = $wpdb->get_row($sql);

			//微信支付
			$data = array(
				'body' => $description,//订单描述(不能为空，否则微支付出错)
				//'openid' => "oZNvzjoc536CwKn5jNT7Hf1P-o3s",
				'openid' => $orderfromuser,
				'out_trade_no' => $out_trade_no,//订单号，需保证该字段对于本商户的唯一性
				'total_fee' =>  (string)($totalfee*100), //支付金额 单位：分
				'notify_url'=>$this->createMobileUrl('paidNotify',array('gweid' =>$gweid)),//支付成功后将通知该地址
				'spbill_create_ip' => $_SERVER['REMOTE_ADDR']
				
			);
		
			if($tradetype->trade_type == 'JSAPI'){
				
				$creorder=$weixin->create_order($data, true);
				$data['resultUrl']=$this->createMobileUrl('goodspay_result',array('gweid' => $gweid,'out_trade_no' => $out_trade_no));
				if($creorder!=false){	
					if($creorder['return_code']=="SUCCESS"){
						if($creorder['result_code']=="SUCCESS"){
						
							$data['appid']=$creorder['appid'];
							$data['package']="prepay_id=".$creorder['prepay_id'];
							$data['sign']=$creorder['sign'];
							$data['nonce_str']=$creorder['nonce_str'];
							$data['trade_type']=$creorder['trade_type'];
							$data['return_code']=$creorder['return_code'];
							$data['result_code']=$creorder['result_code'];
							$jsapi_data=$weixin->JSAPI($creorder['prepay_id']);
							$hint = array("status"=>"success","message"=>"提交成功","data"=>$data,"jsapi_data"=>$jsapi_data);
							
							echo json_encode($hint);
							exit;
						}else{
							$hint = array("status"=>"errordec","message"=>$creorder['err_code_des']);
							echo json_encode($hint);
							exit;
						}
					}else{
						$hint = array("status"=>"error","message"=>$creorder['return_msg']);
						echo json_encode($hint);
						exit;
					}
				}else{
					$hint = array("status"=>"error","message"=>"出现问题，请稍后重试");
					echo json_encode($hint);
					exit;
				}
			}
			if($tradetype->trade_type == 'NATIVE_ORDER'){
/* 				$code_url = $wpdb->get_var($wpdb -> prepare("SELECT code_url FROM  {$wpdb->prefix}shopping_order_wepay WHERE out_trade_no = %s",$out_trade_no));
				if(!empty($code_url)){
					$hint = array("status"=>"success","message"=>"提交成功","data"=>array('trade_type'=>'NATIVE_ORDER','code_url'=>$code_url));
					echo json_encode($hint);
					exit;
				} */
				unset($data['openid']);
				$creorder=$weixin->create_order($data, false);
				$data['trade_type']='NATIVE_ORDER';
				if($creorder!=false){	
					if($creorder['return_code']=="SUCCESS"){
						if($creorder['result_code']=="SUCCESS"){
							$data['code_url'] = $creorder['code_url'];
							$wpdb -> update("{$wpdb->prefix}shopping_order_wepay",array('code_url' => $data['code_url']),array('out_trade_no'=>$out_trade_no));
							$hint = array("status"=>"success","message"=>"提交成功","data"=>$data);
							echo json_encode($hint);
							exit;
						}else{
							$hint = array("status"=>"errordec","message"=>"提交失败，错误原因：".$creorder['err_code_des']."。","paytype"=>$tradetype->trade_type);
							echo json_encode($hint);
							exit;
						}
					}else{
						$hint = array("status"=>"error","message"=>$creorder['return_msg']);
						echo json_encode($hint);
						exit;
					}
				//$data['code_url'] = $creorder['code_url'];
				}else{
					$hint = array("status"=>"error","message"=>"出现问题，请稍后重试");
					echo json_encode($hint);
					exit;
				}
			}
			
		}
		//获取现在时间
		$currentime = date("Y-m-d H:i:s");
		$currentdate = strtotime($currentime);
	   include $this -> template('orderdetail');
	}
    //click weschool button
	public function doWebIndex(){
	    global $_W;
	    $gweid =  $_W['gweid'];
        include $this -> template('index');
    } 
	
	public function doWebIndexstatistic(){
	    global $_W;
	    $gweid =  $_W['gweid'];
        include $this -> template('index_statistic');
    } 
	
	//告警管理
	public function doWebAlarmManage(){
	
	    global $_GPC,$wpdb;
	    //obtain userId
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
			'alarm_id' => "AND id LIKE '%%{$search_content}%%'",
			'error_type' => "AND description LIKE '%%{$search_content}%%'"
		);
		
		$total = $wpdb -> get_var($wpdb -> prepare("SELECT  COUNT(*) FROM {$wpdb -> prefix}shopping_alarm WHERE `gweid` = %d {$search[$search_condition]} ORDER BY id DESC", $gweid));
	    $pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		

		$list = $wpdb -> get_results($wpdb -> prepare("SELECT  * FROM {$wpdb -> prefix}shopping_alarm WHERE `gweid` = %d {$search[$search_condition]} ORDER BY id DESC Limit {$offset},{$psize}", $gweid),ARRAY_A);
		//echo $wpdb -> prepare("SELECT  * FROM {$wpdb -> prefix}shopping_alarm WHERE `gweid` = %d", $gweid);
		include $this -> template('alarmlist');
    }
    //告警详情
	public function doWebAlarmDetail(){
	
	    global $_GPC,$wpdb;
	    //obtain userId
		global $current_user;
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;				
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$gweid=$_SESSION['GWEID'];
		$alarm = $wpdb -> get_row($wpdb -> prepare("SELECT  * FROM {$wpdb -> prefix}shopping_alarm WHERE `gweid` = %d AND `id` = %d ORDER BY id DESC", $gweid ,$_GET['id']),ARRAY_A);
		//echo $wpdb -> prepare("SELECT  * FROM {$wpdb -> prefix}shopping_alarm WHERE `gweid` = %d", $gweid);
		if(!$alarm['read'])
			$wpdb -> update("{$wpdb -> prefix}shopping_alarm",array('read' => 1),array('id' => $_GET['id']));
		include $this -> template('alarmdetail');
    }

    public function doWebAlarmCheck(){
		global $wpdb;
		$gweid=$_SESSION['GWEID'];
		$list = $wpdb -> get_results($wpdb -> prepare("SELECT  `id` FROM {$wpdb -> prefix}shopping_alarm WHERE `gweid` = %d AND `read`=0 ORDER BY `id` DESC", $gweid),ARRAY_A);
		echo json_encode(array('new_status' => empty($list)?FALSE:TRUE));
	}

    
	//click weschool button
	public function doWebRefund(){
	    global $_W, $wpdb;
	    $gweid =  $_W['gweid'];
		$search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		if($search_condition == 'refund_state')
			$search_content = trim($_GET['indata_state']);
		if($search_condition == 'refund_reason')
			$search_content = trim($_GET['indata_reason']);
		$search = array(
			'all' => '',
			'refund_id' => "AND out_refund_no LIKE '%%{$search_content}%%'",
			'order_id' => "AND out_trade_no LIKE '%%{$search_content}%%'",
			'refund_reason' => "AND reason ='{$search_content}'",
			'refund_state' => "AND refund_status = '{$search_content}'",
		);
		
	    $sql = $wpdb -> prepare("SELECT count(*) FROM  {$wpdb->prefix}shopping_refund WHERE  out_trade_no IN (SELECT out_trade_no FROM {$wpdb -> prefix}shopping_order WHERE trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  and `gweid` = %s) {$search[$search_condition]} ORDER BY out_refund_no DESC",$_SESSION['GWEID']);
		
		$total = $wpdb->get_var($sql);
	    $pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		

	    $sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_refund WHERE  out_trade_no IN (SELECT out_trade_no FROM {$wpdb -> prefix}shopping_order WHERE trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  and `gweid` = %s) {$search[$search_condition]} ORDER BY out_refund_no DESC Limit {$offset},{$psize}",$_SESSION['GWEID']);
		$list = $wpdb->get_results($sql,ARRAY_A);
        include $this -> template('refund');
    }

    public function doWebCreateRefund(){
    	global $_W,$wpdb;
		
    	$orderid = $_GET['orderid'];
		
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$_SESSION['GWEID'],$orderid);
		$order = $wpdb->get_row($sql,ARRAY_A);

		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_wepay WHERE out_trade_no = %s",$orderid);
		$order_wepay = $wpdb->get_row($sql,ARRAY_A);
		
    	if($_W['ispost']){
			$wpdb -> insert($wpdb->prefix.'shopping_refund',array(
    			'out_trade_no' => $orderid,
    			'reason' => $_POST['reason'],
    			'refund_fee' => $_POST['refund_fee'],
    			'time_start' => date('Y-m-d H:i:s'),
    			'refund_status' => 'CREATEFAIL'
    			));
			$refund_id = $wpdb -> insert_id;
			if(!empty($refund_id)){
				$weixin = new WeixinPay($_SESSION['GWEID']);	
				$data = array(
					'out_trade_no' => $orderid,
					'out_refund_no' => $refund_id,
					'total_fee' => intval($order['fee']*100),
					'refund_fee' => intval($_POST['refund_fee']*100),
				);
				$result = $weixin -> create_refund($data);
				if($result['result_code']=='SUCCESS')
					$wpdb -> update($wpdb->prefix.'shopping_refund',array('refund_status' => 'PROCESSING'),array('out_refund_no' => $refund_id));
			}
			header("Location: {$this->createWebUrl('createRefund',array('gweid'=>$_GET['gweid'],'orderid'=>$_GET['orderid'],'norefresh'=>$_GET['norefresh']))}");
		}
		
    	$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_refund WHERE  out_trade_no=%s ORDER BY out_refund_no",$orderid);
		$list = $wpdb->get_results($sql,ARRAY_A);

		$max_refund_fee = $order['fee'] - max($wpdb -> get_var( $wpdb -> prepare("SELECT sum(refund_fee) FROM  {$wpdb->prefix}shopping_refund WHERE  out_trade_no=%s ORDER BY out_refund_no",$orderid) ) , 0);
		$max_refund_fee = max($max_refund_fee , 0);
		$max_refund_fee = number_format($max_refund_fee,2,'.','');

    	include $this -> template('createrefund');
    }
	
	
	public function doWebRetryCreateRefund(){
    	global $_W,$wpdb;
		$return_msg = '';
		if($_W['ispost']){
			$refund_id = $_POST['out_refund_no'];
			$refund = $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}shopping_refund WHERE `out_refund_no`=%s",$refund_id),ARRAY_A);
			$order =  $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE `out_trade_no`=%s",$refund['out_trade_no']),ARRAY_A);
			$weixin = new WeixinPay($_SESSION['GWEID']);	
			if(!empty($refund_id)){
				$data = array(
				'out_trade_no' => $refund['out_trade_no'],
				'out_refund_no' => $refund_id,
				'total_fee' => intval($order['fee']*100),
				'refund_fee' => intval($refund['refund_fee']*100),
				);
				$result = $weixin -> create_refund($data);
				if($result['result_code']=='SUCCESS'){
					$wpdb -> update($wpdb->prefix.'shopping_refund',array('refund_status' => 'PROCESSING'),array('out_refund_no' => $refund_id));
					echo json_encode(array('status'=>'SUCCESS'));
					exit;
				}else{
					$return_msg = $result['err_code_des'];
				}
			}
		}
		echo json_encode(array('status'=>'FAIL','error_msg'=>$return_msg));
    }
	
	
	
	//click weschool button
	public function doWebDownloadorder(){
	    global $_W;
	    $gweid =  $_W['gweid'];
        include $this -> template('downloadorder');
    }

	//对账单下载页面
	public function doWebexportorder(){
	    //调用微信对账单下载接口
	    global $_W, $_GPC, $wpdb;
		$gweid=$_SESSION['GWEID'];
			
		//require_once 'pclzip/pclzip.lib.php'; //用于压缩csv文件
		$weixin = new WeixinPay($gweid);
        
		$billtype = $_GET['type'];
		$sevalue = $_GET['sevalue'];
		
		//used to zip several csv files and download the zipfile
		$file = tempnam("tmp", "zip"); 
		$zip = new ZipArchive();  
        //var_dump($zip);
		$res = $zip->open($file, ZipArchive::OVERWRITE); 
		if ($res === TRUE) {
			
			if($sevalue == 0){       //一段时间内的
				$startdate = $_GET['startdate'];
				$enddate = $_GET['enddate'];
				$current_date = $startdate;
				while($current_date <= $enddate) {
					$date = date( 'Ymd', strtotime( $current_date ) );  
					$exportdata = $weixin -> download_bill($date, $billtype);
					$wpdb->insert( 'wp_test', array('text' => $exportdata), array('%s') ) ;
					if(strpos($exportdata, 'xml') === false )
					{
						$zip->addFromString($date.'.csv', $exportdata);
					}else{
						$getxmlarray = $weixin -> getXmlArray($exportdata);   //如果返回的是xml，则解析下对应的返回消息
						if($getxmlarray["return_code"] == 'FAIL')
						{
						    
							if(!empty($getxmlarray["return_msg"]))
							{
							    if($getxmlarray["return_msg"] == "No Bill Exist")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "当日没有相关的对账单信息。");
								}else if($getxmlarray["return_msg"] == "invalid appid" )
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:无效的公众号AppId，请联系平台管理员。");
								}else if($getxmlarray["return_msg"] == "missing parameter")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:参数缺失，请联系平台管理员。");
								}else if( $getxmlarray["return_msg"] == "invalid bill_date")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:日期不在有效期范围内，无法生成对账单信息");
								}else if($getxmlarray["return_msg"] == "mch_id不存在")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:mch_id不存在，请联系平台管理员。");
								}else if($getxmlarray["return_msg"] == "签名错误")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:签名错误，请联系平台管理员。");
								}else{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:".$getxmlarray["return_msg"]."，请联系平台管理员。");
							    } 
							}else{
								$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，请重试。");
							}
							$zip->addFromString($date.'.csv', $exportchangedata);
						}   
					} 
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				
			}else if($sevalue == 1){    //昨天
				$startdate = $_GET['startdate'];
				$current_date = $startdate;
				$date = date( 'Ymd', strtotime( $current_date ) );  
				$exportdata = $weixin -> download_bill($date, $billtype);
				
				if(strpos($exportdata, 'xml') === false )
				{
					$zip->addFromString($date.'.csv', $exportdata);
				}else{
					$getxmlarray = $weixin -> getXmlArray($exportdata);   //如果返回的是xml，则解析下对应的返回消息
					if($getxmlarray["return_code"] == 'FAIL')
					{
						if(!empty($getxmlarray["return_msg"]))
						{
							if($getxmlarray["return_msg"] == "No Bill Exist")
							{
								$exportchangedata = iconv("utf-8", "gb2312", "当日没有相关的对账单信息。");
							}else if($getxmlarray["return_msg"] == "invalid appid" )
							{
								$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:无效的公众号AppId，请联系平台管理员。");
							}else if($getxmlarray["return_msg"] == "missing parameter")
							{
								$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:参数缺失，请联系平台管理员。");
							}else if( $getxmlarray["return_msg"] == "invalid bill_date")
							{
								$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:日期不在有效期范围内，无法生成对账单信息");
							}else if($getxmlarray["return_msg"] == "mch_id不存在")
							{
								$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:mch_id不存在，请联系平台管理员。");
							}else if($getxmlarray["return_msg"] == "签名错误")
							{
								$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:签名错误，请联系平台管理员。");
							}else{
								$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:".$getxmlarray["return_msg"]."，请联系平台管理员。");
							} 
						}else{
							$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，请重试。");
						}
						$zip->addFromString($date.'.csv', $exportchangedata);
					}   
				}
			}else if($sevalue == 2){    //最近七天
				$startdate = $_GET['startdate'];
				$enddate = $_GET['enddate'];
				$current_date = $startdate;
				while($current_date <= $enddate) {
					$date = date( 'Ymd', strtotime( $current_date ) );  
					$exportdata = $weixin -> download_bill($date, $billtype);
					
					if(strpos($exportdata, 'xml') === false )
					{
						$zip->addFromString($date.'.csv', $exportdata);
					}else{
						$getxmlarray = $weixin -> getXmlArray($exportdata);   //如果返回的是xml，则解析下对应的返回消息
						if($getxmlarray["return_code"] == 'FAIL')
						{
							if(!empty($getxmlarray["return_msg"]))
							{
								if($getxmlarray["return_msg"] == "No Bill Exist")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "当日没有相关的对账单信息。");
								}else if($getxmlarray["return_msg"] == "invalid appid" )
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:无效的公众号AppId，请联系平台管理员。");
								}else if($getxmlarray["return_msg"] == "missing parameter")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:参数缺失，请联系平台管理员。");
								}else if( $getxmlarray["return_msg"] == "invalid bill_date")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:日期不在有效期范围内，无法生成对账单信息");
								}else if($getxmlarray["return_msg"] == "mch_id不存在")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:mch_id不存在，请联系平台管理员。");
								}else if($getxmlarray["return_msg"] == "签名错误")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:签名错误，请联系平台管理员。");
								}else{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:".$getxmlarray["return_msg"]."，请联系平台管理员。");
							    } 
							}else{
								$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，请重试。");
							}
							$zip->addFromString($date.'.csv', $exportchangedata);
						}   
					}
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
			}else if($sevalue == 3){   //本月
				$startdate = $_GET['startdate'];
				$enddate = $_GET['enddate'];
				$current_date = $startdate;
				while($current_date <= $enddate) {
					$date = date( 'Ymd', strtotime( $current_date ) );  
					$exportdata = $weixin -> download_bill($date, $billtype);
					
					if(strpos($exportdata, 'xml') === false )
					{
						$zip->addFromString($date.'.csv', $exportdata);
					}else{
						$getxmlarray = $weixin -> getXmlArray($exportdata);   //如果返回的是xml，则解析下对应的返回消息
						if($getxmlarray["return_code"] == 'FAIL')
						{
							if(!empty($getxmlarray["return_msg"]))
							{
								if($getxmlarray["return_msg"] == "No Bill Exist")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "当日没有相关的对账单信息。");
								}else if($getxmlarray["return_msg"] == "invalid appid" )
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:无效的公众号AppId，请联系平台管理员。");
								}else if($getxmlarray["return_msg"] == "missing parameter")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:参数缺失，请联系平台管理员。");
								}else if( $getxmlarray["return_msg"] == "invalid bill_date")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:日期不在有效期范围内，无法生成对账单信息");
								}else if($getxmlarray["return_msg"] == "mch_id不存在")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:mch_id不存在，请联系平台管理员。");
								}else if($getxmlarray["return_msg"] == "签名错误")
								{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:签名错误，请联系平台管理员。");
								}else{
									$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，原因是:".$getxmlarray["return_msg"]."，请联系平台管理员。");
							    } 
							}else{
								$exportchangedata = iconv("utf-8", "gb2312", "没有相关的对账单信息，请重试。");
							}
							$zip->addFromString($date.'.csv', $exportchangedata);
						}   
					}
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
			} 
			
		} 

		$zip->close(); 

		// Stream the file to the client 
		header("Content-Type: application/zip"); 
		header("Content-Length: " . filesize($file)); 
		header("Content-Disposition: attachment; filename=\"对账单.zip\""); 
		readfile($file);
		unlink($file); 
	}
	//订单统计图表页面
	public function doWebOrderstatistic(){
	    global $_W, $wpdb;
	    $gweid=$_SESSION['GWEID'];
		
		
		/*new add(查询条件加入商品的选择)*/
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_category where gweid=%s ORDER BY parentid ASC, displayorder DESC",$gweid);
		$category = $wpdb->get_results($sql,ARRAY_A);
		
		//微商城一级分类
		$rs = array();
		if (!empty($category)) {
			foreach ($category as $key => &$row) {
				if (isset($row['id'])) {
					$rs[$row['id']] = $row;
				} else {
					$rs[] = $row;
				}
			}
			$category=$rs;
		}
		
		//微商城二级分类
		if (!empty($category)) {
            $children = '';
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }
		
		
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE gweid = %s and isshopping='1' and deleted=0  ORDER BY createtime DESC ,status DESC, displayorder DESC, id DESC",$gweid);
		$goodsarray=$wpdb->get_results($sql,ARRAY_A);
		
		//微商城一级分类下商品
		$goodsfetch_parent = '';
		if(is_array($goodsarray) && !empty($goodsarray)){
			foreach ($goodsarray as $goods) {
				if (!empty($goods['pcate'])) {
					$goodsfetch_parent[$goods['pcate']][$goods['id']] = array($goods['id'], $goods['title']);
				}
			}
		}
		
		
		//微商城二级分类下商品
		$goodsfetch = '';
		if(is_array($goodsarray) && !empty($goodsarray)){
			foreach ($goodsarray as $goods) {
				if (!empty($goods['pcate'])) {
					$goodsfetch[$goods['pcate']][$goods['ccate']][$goods['id']] = array($goods['id'], $goods['title']);
				}
			}
		}
		
		
		//支付链接
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goodsindex WHERE gweid = %s and isshopping='0'",$gweid);
		$wepaygoodsindexarray=$wpdb->get_results($sql,ARRAY_A);
		
		//微支付分类下商品
		$sql = $wpdb -> prepare("SELECT s1.id,s1.title,s2.id as indexid,s2.goodsindex_name FROM {$wpdb->prefix}shopping_goods s1 ,{$wpdb->prefix}shopping_goodsindex s2 WHERE s1.groupid=s2.id and s1.gweid = %s and s1.isshopping!='1' and s1.deleted=0 and s1.status!=1 ORDER BY s1.createtime DESC ,s1.status DESC, s1.displayorder DESC, s1.id DESC",$gweid);
		
		$goodswepayarray=$wpdb->get_results($sql,ARRAY_A);
		if(is_array($goodswepayarray) && !empty($goodswepayarray)){
			foreach ($goodswepayarray as $goods) {
				$goodsfetch_parent["wepay".$goods['indexid']][$goods['id']] = array($goods['id'], $goods['title']);
			}
		}
		
		/*new add*/
		//原生商品
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_native_qrcode WHERE gweid = %s",$gweid);
		$wepaynativearray=$wpdb->get_results($sql,ARRAY_A);
		
		
		
		/*new add end*/
		
		$cate_1 = $_GET['cate_1'];
		$cate_2 = $_GET['cate_2'];
		$goods_select = $_GET['goods_select'];
		
		if(!isset($cate_1)){
			$cate_1 = 0;
			$cate_2 = 0;
			$goods_select = 0;
		}
		
		if($goods_select!="0"){
			$search_condition='search1';//某个特定的商品
		}else if($cate_2!="0"){
			$search_condition='search2';//某二级分类下所有商品
		}else if($cate_1!="0"){
			if(is_numeric($cate_1)){
				$search_condition='search3';//微商城某一级分类下所有商品
			}else{
				if(strstr($cate_1,"wepaynative")){
					$search_condition='search5';//某个原生商品
				}else{
					$search_condition='search4';//微支付某一index下所有商品
				}
				$number=preg_match_all('/\d+/',$cate_1,$arr);
				$cate_1=$arr[0][0];
				
			}
		}else{
			$search_condition = 'all';//所有商品
		}
		$search = array(
			'all' => '',
			'search1' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id={$goods_select})",
			'search2' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where ccate={$cate_2} and deleted=0 and gweid={$gweid}))",
			'search3' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where pcate={$cate_1} and deleted=0 and gweid={$gweid}))",
			'search4' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where status=0 and groupid='{$cate_1}'))",
			'search5' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_wepay where trade_type='NATIVE_PRODUCT' and product_id ='{$cate_1}')"
		);
		
		
		/*new add end*/
		
		
		$statistic = $_GET['statistic'];
		$siteid = $_GET['siteid'];
		$startdate = $_GET['startdate'];
		$enddate = $_GET['enddate'];
        
		if(isset($statistic))
		{
			if ($_GET['Selected']!=1) {   //选择输入开始和结束时间单选按钮
				$startdate = $_GET['startdate'];
				$enddate = $_GET['enddate'];
			} else {  //选择时间段单选按钮
				$enddate=date("Y-m-d");
				switch($_GET['period']) {
					case 0 :
						$startdate=date("Y-m-d");
						break;
					case 1 :
						$startdate=date("Y-m-d",strtotime("-1 week +1 day"));
						break;
					case 2 :
						$startdate=date("Y-m-d",strtotime("-1 month +1 day"));
						break;
					case 3 :
						$startdate=date("Y-m-d",strtotime("-3 month +1 day"));
						break;
					case 4 :
						$startdate=date("Y-m-d",strtotime("-1 year +1 day"));
						break;
				}
			}
			//echo "开始日期".$startdate."结束日期".$enddate;
			$jsonresult=array();
			$day1 = 3600 * 24;
			$month1 = 31 * $day1;
			$year1 = 365 * $day1;
			//每日下单量
			if(strtotime($enddate) - strtotime($startdate) <= $month1 && strtotime($enddate) - strtotime($startdate) >=$day1){
				$current_date = $startdate;

				while($current_date <= $enddate) {
				
					if($siteid == 0){   
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE  s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')   {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
						$element=array();
						$element['today']=$current_date;
						$element['countClick']=$clicktimes;
						$jsonresult[]=$element;
						
						}
					elseif($siteid>0){    //count sum fee from shopping_order, the sum fee may less than the refund fee 
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
						$element=array();
						$element['today']=$current_date;
						if(empty($clicktimes))
						{
						    $element['countMoney']='0';
						}else{
						    $element['countMoney']=$clicktimes;
						}
						
						$sql1 = $wpdb -> prepare("SELECT sum(s1.refund_fee) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$refundcounts = $wpdb->get_var($sql1);
						if(empty($refundcounts))
						{
							$refundmoney = '0';
						}else{
							$refundmoney = $refundcounts;
						}
						
						if(intval($clicktimes) < intval($refundmoney))
						{
						    $element['countMoney']= $refundmoney;
						}
						
						$jsonresult[]=$element;
						}
					elseif($siteid == -1){   //全部订单、成功订单、退款订单以及进行中的订单对应的数量
						
						$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as $successcount){
						    if( !array_key_exists('成功的订单',$jsonresult) )
								$jsonresult['成功的订单'] = array();
							$jsonresult['成功的订单'][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
						}
		
						$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$refundcounts = $wpdb->get_results($sql);
						foreach($refundcounts as $refundcount){
							if( !array_key_exists('退款的订单',$jsonresult) )
								$jsonresult['退款的订单'] = array();
							$jsonresult['退款的订单'][] = array("today" => str_replace('-','/',$current_date), "countclick" => $refundcount->counts);
						}
						
						$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')   {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$incounts = $wpdb->get_results($sql);
						foreach($incounts as $incount){
							if( !array_key_exists('进行中的订单',$jsonresult) )
								$jsonresult['进行中的订单'] = array();
							$jsonresult['进行中的订单'][] = array("today" => str_replace('-','/',$current_date), "countclick" => $incount->counts);
						}
						
					}elseif($siteid == -2){   //全部订单、成功订单、退款订单以及进行中的订单对应的金额
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as $successcount){
						    if( !array_key_exists('成功的订单金额',$jsonresult) )
								$jsonresult['成功的订单金额'] = array();
							if(empty($successcount->counts))
							{
								$jsonresult['成功的订单金额'][] = array("today" => str_replace('-','/',$current_date), "countclick" => '0');
							}else{
							    $jsonresult['成功的订单金额'][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
							}
						}
						
						$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$refundcounts = $wpdb->get_results($sql);
						foreach($refundcounts as $refundcount){
							if( !array_key_exists('退款的订单金额',$jsonresult) )
								$jsonresult['退款的订单金额'] = array();
							if(empty($refundcount->counts))
							{
								$jsonresult['退款的订单金额'][] = array("today" => str_replace('-','/',$current_date), "countclick" => '0');
							}else{
							    $jsonresult['退款的订单金额'][] = array("today" => str_replace('-','/',$current_date), "countclick" => $refundcount->counts);
							}
							
						}
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$incounts = $wpdb->get_results($sql);
						foreach($incounts as $incount){
							if( !array_key_exists('进行中的订单金额',$jsonresult) )
								$jsonresult['进行中的订单金额'] = array();
							
							if(empty($incount->counts))
							{
								$jsonresult['进行中的订单金额'][] = array("today" => str_replace('-','/',$current_date), "countclick" => '0');
							}else{
							    $jsonresult['进行中的订单金额'][] = array("today" => str_replace('-','/',$current_date), "countclick" => $incount->counts);
							}
						}
						
					}elseif($siteid == -3){    //获取成功的订单量、退款的订单量以及进行中的订单量的纵坐标最大值
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
						$sql1 = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$refundcounts = $wpdb->get_var($sql1);
						$element=array();
						$element['today']=$current_date;
						if(intval($clicktimes) < intval($refundcounts)){
							$element['county']=$refundcounts;
						}else{
							$element['county']=$clicktimes;
						}
						$jsonresult[]=$element;
						
					}	
				$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
			}
			//当日24小时时间段下单量
			if(strtotime($enddate) - strtotime($startdate) == 0)
			{   
				$current_date = $startdate;
				if($siteid == 0){
					for($i =0; $i<24; $i++)
					{
						$element=array();
						if($i < 10){
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							$element['countClick']=$clicktimes;
							$jsonresult[]=$element;
							
						}else{
							if($i == 23){  //注意，数据库中的24整点是不会出现的，比较到最后的23点到24点之间，需要单独处理
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state not  in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state not  in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);	
							}
							$clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							$element['countClick']=$clicktimes;
							$jsonresult[]=$element;
							
							
						}
					}
						
				}elseif($siteid>0){
					for($i =0; $i<24; $i++)
					{
						$element=array();
						if($i < 10){
							
							$sql = $wpdb -> prepare("SELECT sum(s1.fee) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							if(empty($clicktimes))
							{
							    $element['countMoney']='0';
							}else{
							    $element['countMoney']=$clicktimes;
							}
							
							$sql1 = $wpdb -> prepare("SELECT sum(s1.refund_fee) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $refundcounts = $wpdb->get_var($sql1);
							if(empty($refundcounts))
							{
								$refundmoney = '0';
							}else{
								$refundmoney = $refundcounts;
							}
							
							if(intval($element['countMoney']) < intval($refundmoney))
							{
							    $element['countMoney'] = $refundmoney;
							}
								
							$jsonresult[]=$element;
						}else{
							if($i == 23){  
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
							$clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							if(empty($clicktimes))
							{
							    $element['countMoney']='0';
							}else{
							    $element['countMoney']=$clicktimes;
							}
							
							if($i == 23){ 
								$sql1 = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql1 = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
							$refundcounts = $wpdb->get_var($sql1);
							if(empty($refundcounts))
							{
								$refundmoney = '0';
							}else{
							    $refundmoney = $refundcounts;
							}
							if(intval($element['countMoney']) < intval($refundmoney))
							{
							    $element['countMoney'] = $refundmoney;
							}
							
							$jsonresult[]=$element;
						}
					}
				
				}elseif($siteid == -1){
					for($i =0; $i<24; $i++)
					{
						if($i < 10)
						{
							
						    $sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
							$successcounts = $wpdb->get_results($sql);
							
							foreach($successcounts as $successcount){
								if( !array_key_exists('成功的订单',$jsonresult) )
									$jsonresult['成功的订单'] = array();
								$jsonresult['成功的订单'][] = array("today" => $i, "countclick" => $successcount->counts);
							}
							
							$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $refundcounts = $wpdb->get_results($sql);
							
							foreach($refundcounts as $refundcount){
								if( !array_key_exists('退款的订单',$jsonresult) )
									$jsonresult['退款的订单'] = array();
								$jsonresult['退款的订单'][] = array("today" => $i, "countclick" => $refundcount->counts);
							}
							
							
						    $sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
							$incounts = $wpdb->get_results($sql);
							
							foreach($incounts as $incount){
								if( !array_key_exists('进行中的订单',$jsonresult) )
									$jsonresult['进行中的订单'] = array();
								$jsonresult['进行中的订单'][] = array("today" => $i, "countclick" => $incount->counts);
							}
							
						}else{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
						    $successcounts = $wpdb->get_results($sql);
							
							foreach($successcounts as $successcount){
								if( !array_key_exists('成功的订单',$jsonresult) )
									$jsonresult['成功的订单'] = array();
								$jsonresult['成功的订单'][] = array("today" => $i, "countclick" => $successcount->counts);
							}

							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
							$refundcounts = $wpdb->get_results($sql);
							
							foreach($refundcounts as $refundcount){
								if( !array_key_exists('退款的订单',$jsonresult) )
									$jsonresult['退款的订单'] = array();
								$jsonresult['退款的订单'][] = array("today" => $i, "countclick" => $refundcount->counts);
							}
							
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
							$incounts = $wpdb->get_results($sql);
							
							foreach($incounts as $incount){
								if( !array_key_exists('进行中的订单',$jsonresult) )
									$jsonresult['进行中的订单'] = array();
								$jsonresult['进行中的订单'][] = array("today" => $i, "countclick" => $incount->counts);
							}
							
						}
					}
				}elseif($siteid == -2){
					for($i =0; $i<24; $i++)
					{
						if($i < 10)
						{
							
						    $sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
							$successcounts = $wpdb->get_results($sql);
							
							foreach($successcounts as $successcount){
								if( !array_key_exists('成功的订单金额',$jsonresult) )
									$jsonresult['成功的订单金额'] = array();
								if(empty($successcount->counts))
								{
									$jsonresult['成功的订单金额'][] = array("today" => $i, "countclick" => '0');
								}else{
									$jsonresult['成功的订单金额'][] = array("today" => $i, "countclick" => $successcount->counts);
								}
							}
							
							$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $refundcounts = $wpdb->get_results($sql);
							
							foreach($refundcounts as $refundcount){
								if( !array_key_exists('退款的订单金额',$jsonresult) )
									$jsonresult['退款的订单金额'] = array();
								if(empty($refundcount->counts))
								{
									$jsonresult['退款的订单金额'][] = array("today" => $i, "countclick" => '0');
								}else{
									$jsonresult['退款的订单金额'][] = array("today" => $i, "countclick" => $refundcount->counts);
								}
								
							}
							
							
							$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    //echo "sql stensce:".$sql."\n";
							$incounts = $wpdb->get_results($sql);
							
							foreach($incounts as $incount){
								if( !array_key_exists('进行中的订单金额',$jsonresult) )
									$jsonresult['进行中的订单金额'] = array();
								
								if(empty($incount->counts))
								{
									$jsonresult['进行中的订单金额'][] = array("today" => $i, "countclick" => '0');
								}else{
									$jsonresult['进行中的订单金额'][] = array("today" => $i, "countclick" => $incount->counts);
								}
							}
							
							
						}else{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
						    $successcounts = $wpdb->get_results($sql);
							
							foreach($successcounts as $successcount){
								if( !array_key_exists('成功的订单金额',$jsonresult) )
									$jsonresult['成功的订单金额'] = array();
								if(empty($successcount->counts))
								{
									$jsonresult['成功的订单金额'][] = array("today" => $i, "countclick" => '0');
								}else{
									$jsonresult['成功的订单金额'][] = array("today" => $i, "countclick" => $successcount->counts);
								}
							}

							if($i == 23){
								$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
							$refundcounts = $wpdb->get_results($sql);
							
							foreach($refundcounts as $refundcount){
								if( !array_key_exists('退款的订单金额',$jsonresult) )
									$jsonresult['退款的订单金额'] = array();
								if(empty($refundcount->counts))
								{
									$jsonresult['退款的订单金额'][] = array("today" => $i, "countclick" => '0');
								}else{
									$jsonresult['退款的订单金额'][] = array("today" => $i, "countclick" => $refundcount->counts);
								}
								
							}

							if($i == 23){
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
							
							$incounts = $wpdb->get_results($sql);
							
							foreach($incounts as $incount){
								if( !array_key_exists('进行中的订单金额',$jsonresult) )
									$jsonresult['进行中的订单金额'] = array();
								
								if(empty($incount->counts))
								{
									$jsonresult['进行中的订单金额'][] = array("today" => $i, "countclick" => '0');
								}else{
									$jsonresult['进行中的订单金额'][] = array("today" => $i, "countclick" => $incount->counts);
								}
							}
						
							
						}
					}
				}elseif($siteid == -3){    //获取成功的订单量、退款的订单量以及进行中的订单量的纵坐标最大值
					for($i =0; $i<24; $i++)
					{
						$element=array();
						if($i < 10)
						{
							
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $clicktimes = $wpdb->get_var($sql);
							$sql1 = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $refundcounts = $wpdb->get_var($sql1);
							$element=array();
							$element['today']=$i;
							if(intval($clicktimes) < intval($refundcounts))
							{
								$element['county']=$refundcounts;
							}else{
								$element['county']=$clicktimes;
							}
							$jsonresult[]=$element;
							
							
						}else{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
							$clicktimes = $wpdb->get_var($sql);
							if($i == 23){
								$sql1 = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql1 = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
							$refundcounts = $wpdb->get_var($sql1);
							
							$element=array();
							$element['today']=$i;
							if(intval($clicktimes) < intval($refundcounts))
							{
								$element['county']=$refundcounts;
							}else{
								$element['county']=$clicktimes;
							}
							$jsonresult[]=$element;
							
							
						}
					}
				}
			}
			//每月下单量
			if (strtotime($enddate) - strtotime($startdate) > $month1) {

				$start_month = date("Y-m", strtotime($startdate));
				$end_month = date("Y-m", strtotime($enddate));
				$current_month = $start_month;

				while(strtotime($current_month) <= strtotime($end_month)) {
				
					if($siteid == 0){
						
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1  WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
							
						$element=array();
						$element['today']=$current_month;
						$element['countClick']=$clicktimes;
						$jsonresult[]=$element;
						
						
						}
					elseif($siteid>0){
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
							
						$element=array();
						$element['today']=$current_month;
						if(empty($clicktimes))
						{
							$element['countMoney']='0';
						}else{
							$element['countMoney']=$clicktimes;
						}
						
						$sql1 = $wpdb -> prepare("SELECT sum(s1.refund_fee) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$refundcounts = $wpdb->get_var($sql1);
						if(empty($refundcounts))
						{
							$refundmoney = '0';
						}else{
							$refundmoney = $refundcounts;
						}
						if(intval($element['countMoney'])< intval($refundmoney))
						{
						    $element['countMoney'] = $refundmoney;
						}
						
						$jsonresult[]=$element;
						}
					elseif($siteid == -1){
						
						$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$successcounts = $wpdb->get_results($sql);
						
						foreach($successcounts as $successcount){
							if( !array_key_exists('成功的订单',$jsonresult) )
								$jsonresult['成功的订单'] = array();
							$jsonresult['成功的订单'][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
						}
						$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$refundcounts = $wpdb->get_results($sql);
						
						foreach($refundcounts as $refundcount){
							if( !array_key_exists('退款的订单',$jsonresult) )
								$jsonresult['退款的订单'] = array();
							$jsonresult['退款的订单'][] = array("today" => str_replace('-','/',$current_month), "countclick" => $refundcount->counts);
						}
						
						
						$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$incounts = $wpdb->get_results($sql);
						
						foreach($incounts as $incount){
							if( !array_key_exists('进行中的订单',$jsonresult) )
								$jsonresult['进行中的订单'] = array();
							$jsonresult['进行中的订单'][] = array("today" => str_replace('-','/',$current_month), "countclick" => $incount->counts);
						}
							
					
						
					}elseif($siteid == -2){   //全部订单、成功订单、退款订单以及进行中的订单对应的金额
						
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$successcounts = $wpdb->get_results($sql);
						
						foreach($successcounts as $successcount){
						    if( !array_key_exists('成功的订单金额',$jsonresult) )
								$jsonresult['成功的订单金额'] = array();
							if(empty($successcount->counts))
							{
								$jsonresult['成功的订单金额'][] = array("today" => str_replace('-','/',$current_month), "countclick" => '0');
							}else{
							    $jsonresult['成功的订单金额'][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
							}
						}
						
						$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$refundcounts = $wpdb->get_results($sql);
						
						foreach($refundcounts as $refundcount){
							if( !array_key_exists('退款的订单金额',$jsonresult) )
								$jsonresult['退款的订单金额'] = array();
							if(empty($refundcount->counts))
							{
								$jsonresult['退款的订单金额'][] = array("today" => str_replace('-','/',$current_month), "countclick" => '0');
							}else{
							    $jsonresult['退款的订单金额'][] = array("today" => str_replace('-','/',$current_month), "countclick" => $refundcount->counts);
							}
							
						}
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$incounts = $wpdb->get_results($sql);
						
						foreach($incounts as $incount){
							if( !array_key_exists('进行中的订单金额',$jsonresult) )
								$jsonresult['进行中的订单金额'] = array();
							
							if(empty($incount->counts))
							{
								$jsonresult['进行中的订单金额'][] = array("today" => str_replace('-','/',$current_month), "countclick" => '0');
							}else{
							    $jsonresult['进行中的订单金额'][] = array("today" => str_replace('-','/',$current_month), "countclick" => $incount->counts);
							}
						}
				
					}elseif($siteid == -3){     //计算成功的订单量、退款的订单量以及进行中的订单量的纵坐标最大值
						
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
						$sql1 = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$refundcounts = $wpdb->get_var($sql1);
							
						$element=array();
						$element['today']=$current_month;
						if(intval($clicktimes) < intval($refundcounts))
						{
							$element['county']=$refundcounts;
						}else{
							$element['county']=$clicktimes;
						}
						
						$jsonresult[]=$element;
					
						}
					
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}

			}
			echo json_encode($jsonresult);
			exit;
		}
        include $this -> template('orderstatistic');
    }
	
	//订单统计图表页面
	public function doWebDownloadstatistic(){
	    global $_W, $wpdb;
	    $gweid=$_SESSION['GWEID'];
		
		$downloads = $_GET['downloads'];
		$siteid = $_GET['siteid'];
		$startdate = $_GET['startdate'];
		$enddate = $_GET['enddate'];
		
		$cate_1 = $_GET['cate_1'];
		$cate_2 = $_GET['cate_2'];
		$goods_select = $_GET['goods_select'];
		
		
		if(!isset($cate_1)){
			$cate_1 = 0;
			$cate_2 = 0;
			$goods_select = 0;
		}
		
		if($goods_select!="0"){
			$search_condition='search1';//某个特定的商品
		}else if($cate_2!="0"){
			$search_condition='search2';//某二级分类下所有商品
		}else if($cate_1!="0"){
			if(is_numeric($cate_1)){
				$search_condition='search3';//微商城某一级分类下所有商品
			}else{
				if(strstr($cate_1,"wepaynative")){
					$search_condition='search5';//原生商品
				}else{
					$search_condition='search4';//微支付某一级分类下所有商品
				}
				$number=preg_match_all('/\d+/',$cate_1,$arr);
				$cate_1=$arr[0][0];
			}
		}else{
			$search_condition = 'all';//所有商品
		}
		
		
		$search = array(
			'all' => '',
			'search1' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id={$goods_select})",
			'search2' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where ccate={$cate_2} and deleted=0 and gweid={$gweid}))",
			'search3' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where pcate={$cate_1} and deleted=0 and gweid={$gweid}))",
			'search4' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where status=0 and groupid='{$cate_1}'))",
			'search5' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_wepay where trade_type='NATIVE_PRODUCT' and product_id ='{$cate_1}')"
		);
		
		if(isset($downloads))
		{
			if ($_GET['Selected']!=1) {   //选择输入开始和结束时间单选按钮
				$startdate = $_GET['startdate'];
				$enddate = $_GET['enddate'];
			} else {  //选择时间段单选按钮
				$enddate=date("Y-m-d");
				switch($_GET['period']) {
					case 0 :
						$startdate=date("Y-m-d");
						break;
					case 1 :
						$startdate=date("Y-m-d",strtotime("-1 week +1 day"));
						break;
					case 2 :
						$startdate=date("Y-m-d",strtotime("-1 month +1 day"));
						break;
					case 3 :
						$startdate=date("Y-m-d",strtotime("-3 month +1 day"));
						break;
					case 4 :
						$startdate=date("Y-m-d",strtotime("-1 year +1 day"));
						break;
				}
			}
			$jsonresult=array();
			$day1 = 3600 * 24;
			$month1 = 31 * $day1;
			$year1 = 365 * $day1;
			if(strtotime($enddate) - strtotime($startdate) <= $month1 && strtotime($enddate) - strtotime($startdate) >= $day1){
				$current_date = $startdate;
				
				$filename="订单统计表".$startdate."_".$enddate.".csv";//先定义一个excel文件

				header("Content-Type: application/vnd.ms-execl"); 
				header("Content-Type: application/vnd.ms-excel; charset=utf-8");
				header("Content-Disposition: attachment; filename=$filename"); 
				header("Pragma: no-cache"); 
				header("Expires: 0");
				
				//先输出表头那一行
				echo " ".",";
                while($current_date <= $enddate) {
				
				    echo iconv("utf-8", "gb2312", $current_date."").",";
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				//接下来是每天的下单量
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "全部下单量(个)").",";
				while($current_date <= $enddate) {
				   
					$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
				    $countallorders = $wpdb->get_var($sql);
						
					echo iconv("utf-8", "gb2312", $countallorders).",";
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				
				//接下来是成功的订单量
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "成功的订单量(个)").",";
				while($current_date <= $enddate) {
				
				    $sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
					$successcounts = $wpdb->get_results($sql);
						
					foreach($successcounts as $successcount){
						echo iconv("utf-8", "gb2312", $successcount->counts).",";
					}
				   
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				
				//接下来是退款的订单量
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "退款的订单量(个)").",";
				while($current_date <= $enddate) {
				
					$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
				    $refundcounts = $wpdb->get_results($sql);
						
					foreach($refundcounts as $refundcount){
						echo iconv("utf-8", "gb2312", $refundcount->counts).",";
					}
				   
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				
				//接下来是进行中的订单量
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "进行中的订单量(个)").",";
				while($current_date <= $enddate) {
				
					$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
				    $incounts = $wpdb->get_results($sql);
						
					foreach($incounts as $incount){
						echo iconv("utf-8", "gb2312", $incount->counts).",";
					}
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				
				//接下来是成功的订单金额
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "成功的订单金额(￥)").",";
				while($current_date <= $enddate) {
				
				    $sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
					$successcounts = $wpdb->get_results($sql);
						
					foreach($successcounts as $successcount){
						if(empty($successcount->counts))
						{
						    echo iconv("utf-8", "gb2312", 0).",";
						}else{
						    echo iconv("utf-8", "gb2312", $successcount->counts).",";
						}
					}
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				
				//接下来是退款的订单金额
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "退款的订单金额(￥)").",";
				while($current_date <= $enddate) {
				
					$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
				    $refundcounts = $wpdb->get_results($sql);
						
					foreach($refundcounts as $refundcount){
						if(empty($refundcount->counts))
						{
						    echo iconv("utf-8", "gb2312", 0).",";
						}else{
						    echo iconv("utf-8", "gb2312", $refundcount->counts).",";
						}
					}
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				
				//接下来是进行中的订单金额
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "进行中的订单金额(￥)").",";
				while($current_date <= $enddate) {
				
					$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
				    //echo "sql stensce:".$sql."\n";
					$incounts = $wpdb->get_results($sql);
						
					foreach($incounts as $incount){
						if(empty($incount->counts))
						{
							echo iconv("utf-8", "gb2312", 0).",";
						}else{
							echo iconv("utf-8", "gb2312", $incount->counts).",";
						}
					}
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				
			
			}
			if(strtotime($enddate) - strtotime($startdate) == 0)
			{
				$current_date = $startdate;
				
				$filename="订单统计表".$current_date.".csv";//先定义一个excel文件

				header("Content-Type: application/vnd.ms-execl"); 
				header("Content-Type: application/vnd.ms-excel; charset=utf-8");
				header("Content-Disposition: attachment; filename=$filename"); 
				header("Pragma: no-cache"); 
				header("Expires: 0");
				
				//先输出表头那一行
				echo " ".",";
				
				for($i =0; $i<24; $i++)
				{
				    if($i == 0)
				        echo iconv("utf-8", "gb2312", "24点--".($i+1)."点").",";
					else  
					    echo iconv("utf-8", "gb2312", $i."点--".($i+1)."点").",";
				}
				echo "\n";
				//输出当日每时段下单量
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "全部下单量(个)").",";
				for($i =0; $i<24; $i++)
				{
				    if($i < 10)
					{
						
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
				        $countallcounts = $wpdb->get_var($sql);
						
						echo iconv("utf-8", "gb2312", $countallcounts).",";
					}
					else
					{
						if($i == 23){
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
						}
						$countallcounts = $wpdb->get_var($sql);
						
						echo iconv("utf-8", "gb2312", $countallcounts).",";
					}
				}
				echo "\n";
				//输出当日每时段成功订单量
				echo iconv("utf-8", "gb2312", "成功的订单量(个)").",";
				$current_date = $startdate;
				for($i =0; $i<24; $i++)
				{
				    if($i < 10)
					{
						
				        $sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						$successcounts = $wpdb->get_results($sql);
						
						foreach($successcounts as $successcount){
							echo iconv("utf-8", "gb2312",  $successcount->counts).",";
						}
					}
					else
					{
						if($i == 23){
							$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
						}
				        $successcounts = $wpdb->get_results($sql);
						
						foreach($successcounts as $successcount){
							echo iconv("utf-8", "gb2312",  $successcount->counts).",";
						}
					}
				}
				echo "\n";
				
				//输出当日每时段退款订单量
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "退款的订单量(个)").",";
				for($i =0; $i<24; $i++)
				{
				    if($i < 10)
					{
						
						$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
				        $refundcounts = $wpdb->get_results($sql);
						
						foreach($refundcounts as $refundcount){
							echo iconv("utf-8", "gb2312",  $refundcount->counts).",";
						}
					}
					else
					{
					    if($i == 23){
					    	$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
					    }else{
					    	$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
					    }
						$refundcounts = $wpdb->get_results($sql);
						
						foreach($refundcounts as $refundcount){
							echo iconv("utf-8", "gb2312",  $refundcount->counts).",";
						}
						
					}
				}
				echo "\n";
				
				//输出当日每时段进行中的订单量
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "进行中的订单量(个)").",";
				for($i =0; $i<24; $i++)
				{
				    if($i < 10)
					{
					   
						$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
				        $incounts = $wpdb->get_results($sql);
						
						foreach($incounts as $incount){
							echo iconv("utf-8", "gb2312",  $incount->counts).",";
						}
						
					}
					else
					{
					    if($i == 23){
					    	$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
				        
					    }else{
					    	$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
				        
					    }
						$incounts = $wpdb->get_results($sql);
						
						foreach($incounts as $incount){
							echo iconv("utf-8", "gb2312",  $incount->counts).",";
						}
						
					}
				}
				echo "\n";
				
				//输出当日每时段成功的订单金额
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "成功的订单金额(￥)").",";
				for($i =0; $i<24; $i++)
				{
				    if($i < 10)
					{
						
				        $sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						$successcounts = $wpdb->get_results($sql);
						
						foreach($successcounts as $successcount){
							if(empty($successcount->counts))
							{
								echo iconv("utf-8", "gb2312",  '0').",";
							}else{
								echo iconv("utf-8", "gb2312",  $successcount->counts).",";
							}
						}
						
					}
					else
					{
					    if($i == 23){
					    	$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
					    }else{
					    	$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
					    }
				        $successcounts = $wpdb->get_results($sql);
						
						foreach($successcounts as $successcount){
							if(empty($successcount->counts))
							{
								echo iconv("utf-8", "gb2312",  '0').",";
							}else{
								echo iconv("utf-8", "gb2312",   $successcount->counts).",";
							}
						}
					}
				}
				echo "\n";
				
				//输出当日每时段退款的订单金额
				echo iconv("utf-8", "gb2312", "退款的订单金额(￥)").",";
				$current_date = $startdate;
				for($i =0; $i<24; $i++)
				{
				    if($i < 10)
					{
						
						$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
				        $refundcounts = $wpdb->get_results($sql);
						
						foreach($refundcounts as $refundcount){
							if(empty($refundcount->counts))
							{
								echo iconv("utf-8", "gb2312",  '0').",";
							}else{
								echo iconv("utf-8", "gb2312",  $refundcount->counts).",";
							}
							
						}
						
					}
					else
					{
					    if($i == 23){
					    	$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
				        
					    }else{
					    	$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
				        
					    }
						$refundcounts = $wpdb->get_results($sql);
						
						foreach($refundcounts as $refundcount){
							if(empty($refundcount->counts))
							{
								echo iconv("utf-8", "gb2312",  '0').",";
							}else{
								echo iconv("utf-8", "gb2312",  $refundcount->counts).",";
							}
							
						}
					}
				}
				echo "\n";
				
				//输出当日每时段进行中的订单金额
				echo iconv("utf-8", "gb2312", "进行中的订单金额(￥)").",";
				$current_date = $startdate;
				for($i =0; $i<24; $i++)
				{
				    if($i < 10)
					{
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
				        //echo "sql stensce:".$sql."\n";
					    $incounts = $wpdb->get_results($sql);
						
						foreach($incounts as $incount){
							
							if(empty($incount->counts))
							{
								echo iconv("utf-8", "gb2312",  '0').",";
							}else{
								echo iconv("utf-8", "gb2312",  $incount->counts).",";
							}
						}
							
						
					}
					else
					{
					   	if($i == 23){
					   		$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
					   	}else{
					   		$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
					   	}
						//echo "sql stensce:".$sql."\n";
						$incounts = $wpdb->get_results($sql);
						
						foreach($incounts as $incount){
							
							if(empty($incount->counts))
							{
								echo iconv("utf-8", "gb2312",  '0').",";
							}else{
								echo iconv("utf-8", "gb2312",  $incount->counts).",";
							}
						}
					}
				}
				echo "\n";
				
			}

			if (strtotime($enddate) - strtotime($startdate) > $month1) {

				$start_month = date("Y-m", strtotime($startdate));
				$end_month = date("Y-m", strtotime($enddate));
				$current_month = $start_month;
				
				$filename="订单统计表".$start_month."月份-".$end_month."月份.csv";//先定义一个excel文件
				
				header("Content-Type: application/vnd.ms-execl"); 
				header("Content-Type: application/vnd.ms-excel; charset=utf-8");
				header("Content-Disposition: attachment; filename=$filename"); 
				header("Pragma: no-cache"); 
				header("Expires: 0");
				
				//先输出表头那一行
				echo " ".",";
                while(strtotime($current_month) <= strtotime($end_month)) {
				
				    echo iconv("utf-8", "gb2312", $current_month."月份").",";
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				echo "\n";
				
				//输出每月下单量
				$current_month = $start_month;
				echo iconv("utf-8", "gb2312", "全部下单量(个)").",";
				while(strtotime($current_month) <= strtotime($end_month)) {
				
					$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
				    $allcounts = $wpdb->get_var($sql);
						
					echo iconv("utf-8", "gb2312", $allcounts).",";
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				
				echo "\n";
				
				//输出每月成功的订单量
				$current_month = $start_month;
				echo iconv("utf-8", "gb2312", "成功的订单量(个)").",";
				while(strtotime($current_month) <= strtotime($end_month)) {
				
					
				    $sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
					$successcounts = $wpdb->get_results($sql);
						
					foreach($successcounts as $successcount){
						echo iconv("utf-8", "gb2312", $successcount->counts).",";
					}
					
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				
				echo "\n";
				
				//输出每月退款的订单量
				$current_month = $start_month;
				echo iconv("utf-8", "gb2312", "退款的订单量(个)").",";
				while(strtotime($current_month) <= strtotime($end_month)) {
				
					
					$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
				    $refundcounts = $wpdb->get_results($sql);
					
					foreach($refundcounts as $refundcount){
						echo iconv("utf-8", "gb2312", $refundcount->counts).",";
					}
					
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				
				echo "\n";
				
				//输出每月进行中的订单量
				$current_month = $start_month;
				echo iconv("utf-8", "gb2312", "进行中的订单量(个)").",";
				while(strtotime($current_month) <= strtotime($end_month)) {
				
					
					$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
				    $incounts = $wpdb->get_results($sql);
					
					foreach($incounts as $incount){
						echo iconv("utf-8", "gb2312", $incount->counts).",";
					}
					
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				
				echo "\n";
				
				//输出每月成功的订单金额
				$current_month = $start_month;
				echo iconv("utf-8", "gb2312", "成功的订单金额(￥)").",";
				while(strtotime($current_month) <= strtotime($end_month)) {
				
					
				    $sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
					$successcounts = $wpdb->get_results($sql);
					
					foreach($successcounts as $successcount){
						
						if(empty($successcount->counts))
						{
							echo iconv("utf-8", "gb2312", '0').",";
						}else{
							echo iconv("utf-8", "gb2312", $successcount->counts).",";
						}
					}
					
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				
				echo "\n";
				
				//输出每月退款的订单金额
				$current_month = $start_month;
				echo iconv("utf-8", "gb2312", "退款的订单金额(￥)").",";
				while(strtotime($current_month) <= strtotime($end_month)) {
				
					$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
				    $refundcounts = $wpdb->get_results($sql);
					
					foreach($refundcounts as $refundcount){
						
						if(empty($refundcount->counts))
						{
							echo iconv("utf-8", "gb2312", '0').",";
						}else{
							echo iconv("utf-8", "gb2312", $refundcount->counts).",";
						}
						
					}
					
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				
				echo "\n";
				
				//输出每月进行中的订单金额
				$current_month = $start_month;
				echo iconv("utf-8", "gb2312", "进行中的订单金额(￥)").",";
				while(strtotime($current_month) <= strtotime($end_month)) {
				
					$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
				    $incounts = $wpdb->get_results($sql);
					
					foreach($incounts as $incount){
						
						if(empty($incount->counts))
						{
							echo iconv("utf-8", "gb2312", '0').",";
						}else{
							echo iconv("utf-8", "gb2312", $incount->counts).",";
						}
					}
					
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}			
				echo "\n";			
			}	
			exit;
		}
		
	}	



	//订单统计图表页面
	public function doWebOrderstatisticclick(){
	    global $_W, $wpdb;
	    $gweid=$_SESSION['GWEID'];
		
		//支付链接
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goodsindex WHERE gweid = %s and isshopping='0'",$gweid);
		$goodsindexarray=$wpdb->get_results($sql,ARRAY_A);
		
		//原生商品
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_native_qrcode WHERE gweid = %s",$gweid);
		$wepaynativearray=$wpdb->get_results($sql,ARRAY_A);
		
		$goods_select = $_GET['goods_select'];
		
		
		if(!isset($goods_select)){
			$goods_select = 0;
		}
		
		if($goods_select!="0"){
		
			if(is_numeric($goods_select)){
				
				$select=$goods_select;
				/*click statistic*/
				$sql = $wpdb -> prepare("SELECT goodsindex_name,type from ".$wpdb->prefix."shopping_goodsindex WHERE id=%s",$select);
				$info = $wpdb->get_row($sql);
				if($info->type=="JSAPI"){
					$pay_type="[网页支付]";
				}else{
					$pay_type="[原生支付]";
				}
				
				
				$type='wepaygoodsinfo';
				/*click statistic END*/
				
				$clickstatistic = array(
					'type_id' => $select,
					'type' => $type,
					'displayname'=>$pay_type.$info->goodsindex_name."_".$select
				);
			
			}else{				
				$number=preg_match_all('/\d+/',$goods_select,$arr);
				$nativeid=$arr[0][0];
				$sql = $wpdb -> prepare("SELECT product_name from ".$wpdb->prefix."shopping_native_qrcode WHERE product_id=%s",$nativeid);
				$info = $wpdb->get_row($sql);
				
				$type='native_qrcode';
				/*click statistic END*/
				
				$clickstatistic = array(
					'type_id' => $nativeid,
					'type' => $type,
					'displayname'=>"[原生商品]".$info->product_name."_".$nativeid
				);
			}
			
		}
		
		/*new add end*/
		
		
		$statistic = $_GET['statistic'];
		$siteid = $_GET['siteid'];
		$startdate = $_GET['startdate'];
		$enddate = $_GET['enddate'];
        
		if(isset($statistic))
		{
			if ($_GET['Selected']!=1) {   //选择输入开始和结束时间单选按钮
				$startdate = $_GET['startdate'];
				$enddate = $_GET['enddate'];
			} else {  //选择时间段单选按钮
				$enddate=date("Y-m-d");
				switch($_GET['period']) {
					case 0 :
						$startdate=date("Y-m-d");
						break;
					case 1 :
						$startdate=date("Y-m-d",strtotime("-1 week +1 day"));
						break;
					case 2 :
						$startdate=date("Y-m-d",strtotime("-1 month +1 day"));
						break;
					case 3 :
						$startdate=date("Y-m-d",strtotime("-3 month +1 day"));
						break;
					case 4 :
						$startdate=date("Y-m-d",strtotime("-1 year +1 day"));
						break;
				}
			}
			$jsonresult=array();
			$day1 = 3600 * 24;
			$month1 = 31 * $day1;
			$year1 = 365 * $day1;
			if(strtotime($enddate) - strtotime($startdate) <= $month1 && strtotime($enddate) - strtotime($startdate) >=$day1){
				$current_date = $startdate;

				while($current_date <= $enddate) {
				
					
					if($siteid == -4){   /*statistic new add*/

						if($goods_select=="0" ){//全部显示出来
							
							//所有支付链接
							$sql = $wpdb -> prepare("SELECT goodsindex_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'wepaygoodsinfo' AND type_id = ".$wpdb->prefix."shopping_goodsindex.id ) as counts,type,id from ".$wpdb->prefix."shopping_goodsindex where isshopping='0' and gweid=%s group by id", $current_date." 00:00:00", $current_date." 23:59:59",$gweid);
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as &$successcount){
								$successcount->title  = str_replace ( " ", "_" , $successcount->title );
								if($successcount->type=='JSAPI'){
									$pay_type="[网页支付]";
								}else{
									$pay_type="[原生支付]";
								}
								if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->id,$jsonresult) )
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id] = array();
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
							}
							
							//所有原生商品
							$sql = $wpdb -> prepare("SELECT product_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'native_qrcode' AND type_id = ".$wpdb->prefix."shopping_native_qrcode.product_id ) as counts,product_id from ".$wpdb->prefix."shopping_native_qrcode where gweid=%s", $current_date." 00:00:00", $current_date." 23:59:59",$gweid);
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as &$successcount){
								$successcount->title  = str_replace ( " ", "_" , $successcount->title );
								$pay_type="[原生商品]";
								if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->product_id,$jsonresult) )
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id] = array();
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
							}
						}else{
							$sql = $wpdb -> prepare("SELECT count(*) as counts FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s", $current_date." 00:00:00", $current_date." 23:59:59", $clickstatistic['type_id'],$clickstatistic['type']);
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as $successcount){
								if( !array_key_exists($clickstatistic['displayname'],$jsonresult) )
									$jsonresult[$clickstatistic['displayname']] = array();
									$jsonresult[$clickstatistic['displayname']][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
							}
						}
					}else if($siteid == -5){   
						if($goods_select=="0" ){
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE  s1.time >=%s AND s1.time <=%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
							
						}else{
							$sql = $wpdb -> prepare("SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s", $current_date." 00:00:00", $current_date." 23:59:59", $clickstatistic['type_id'],$clickstatistic['type']);
						}
						$clicktimes = $wpdb->get_var($sql);
						$element=array();
						$element['today']=$current_date;
						$element['countClick']=$clicktimes;
						$jsonresult[]=$element;
						
					}elseif($siteid == -6){   
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
						
						$element=array();
						$element['today']=$current_date;
						$element['county']=$clicktimes;
						$jsonresult[]=$element;
						
					}/*statistic new add end*/
					
				$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
			}
			if(strtotime($enddate) - strtotime($startdate) == 0)
			{
			   
				$current_date = $startdate;
				if($siteid == -4){   /*statistic new add*/
					for($i =0; $i<24; $i++)
					{
						if($i < 10)
						{
					
							if($goods_select=="0" ){//全部显示出来
								
								//所有支付链接
								$sql = $wpdb -> prepare("SELECT goodsindex_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'wepaygoodsinfo' AND type_id = ".$wpdb->prefix."shopping_goodsindex.id ) as counts,type,id from ".$wpdb->prefix."shopping_goodsindex where isshopping='0' and gweid=%s group by id",$current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00",$gweid);
								$successcounts = $wpdb->get_results($sql);
								foreach($successcounts as &$successcount){
									$successcount->title  = str_replace ( " ", "_" , $successcount->title );
									if($successcount->type=='JSAPI'){
										$pay_type="[网页支付]";
									}else{
										$pay_type="[原生支付]";
									}
									if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->id,$jsonresult) )
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id] = array();
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id][] = array("today" => $i, "countclick" => $successcount->counts);
								}
								
								//所有原生商品
								$sql = $wpdb -> prepare("SELECT product_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'native_qrcode' AND type_id = ".$wpdb->prefix."shopping_native_qrcode.product_id ) as counts,product_id from ".$wpdb->prefix."shopping_native_qrcode where gweid=%s",$current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00",$gweid);
								$successcounts = $wpdb->get_results($sql);
								foreach($successcounts as &$successcount){
									$successcount->title  = str_replace ( " ", "_" , $successcount->title );
									$pay_type="[原生商品]";
									if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->product_id,$jsonresult) )
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id] = array();
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id][] = array("today" => $i, "countclick" => $successcount->counts);
								}
								
							}else{
							
								$sql = $wpdb -> prepare("SELECT count(*) as counts FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <%s AND s1.type_id = %s AND s1.type = %s", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $clickstatistic['type_id'],$clickstatistic['type']);
										
								$successcounts = $wpdb->get_results($sql);
								foreach($successcounts as $successcount){
									if( !array_key_exists($clickstatistic['displayname'],$jsonresult) )
										$jsonresult[$clickstatistic['displayname']] = array();
										$jsonresult[$clickstatistic['displayname']][] = array("today" => $i, "countclick" => $successcount->counts);
								}
							}
						
						}else{/*statistic new add end*/
							if($goods_select=="0" ){//全部显示出来
								
								//所有支付链接
								if($i == 23){
									$sql = $wpdb -> prepare("SELECT goodsindex_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'wepaygoodsinfo' AND type_id = ".$wpdb->prefix."shopping_goodsindex.id ) as counts,type,id from ".$wpdb->prefix."shopping_goodsindex where isshopping='0' and gweid=%s group by id", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59",$gweid);
								}else{
									$sql = $wpdb -> prepare("SELECT goodsindex_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'wepaygoodsinfo' AND type_id = ".$wpdb->prefix."shopping_goodsindex.id ) as counts,type,id from ".$wpdb->prefix."shopping_goodsindex where isshopping='0' and gweid=%s group by id", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00",$gweid);
								}
								$successcounts = $wpdb->get_results($sql);
								foreach($successcounts as &$successcount){
									$successcount->title  = str_replace ( " ", "_" , $successcount->title );
									if($successcount->type=='JSAPI'){
										$pay_type="[网页支付]";
									}else{
										$pay_type="[原生支付]";
									}
									if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->id,$jsonresult) )
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id] = array();
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id][] = array("today" => $i, "countclick" => $successcount->counts);
								}
								
								//所有原生商品
								if($i == 23){
									$sql = $wpdb -> prepare("SELECT product_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'native_qrcode' AND type_id = ".$wpdb->prefix."shopping_native_qrcode.product_id ) as counts,product_id from ".$wpdb->prefix."shopping_native_qrcode where gweid=%s", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59",$gweid);
								}else{
									$sql = $wpdb -> prepare("SELECT product_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'native_qrcode' AND type_id = ".$wpdb->prefix."shopping_native_qrcode.product_id ) as counts,product_id from ".$wpdb->prefix."shopping_native_qrcode where gweid=%s", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00",$gweid);
								}
								$successcounts = $wpdb->get_results($sql);
								foreach($successcounts as &$successcount){
									$successcount->title  = str_replace ( " ", "_" , $successcount->title );
									$pay_type="[原生商品]";
									if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->product_id,$jsonresult) )
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id] = array();
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id][] = array("today" => $i, "countclick" => $successcount->counts);
								}
								
							}else{
								if($i == 23){
									$sql = $wpdb -> prepare("SELECT count(*) as counts FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $clickstatistic['type_id'],$clickstatistic['type']);
								}else{
									$sql = $wpdb -> prepare("SELECT count(*) as counts FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <%s AND s1.type_id = %s AND s1.type = %s", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $clickstatistic['type_id'],$clickstatistic['type']);
								
								}
										
								$successcounts = $wpdb->get_results($sql);
								foreach($successcounts as $successcount){
									if( !array_key_exists($clickstatistic['displayname'],$jsonresult) )
										$jsonresult[$clickstatistic['displayname']] = array();
										$jsonresult[$clickstatistic['displayname']][] = array("today" => $i, "countclick" => $successcount->counts);
								}
							}
								
				
						}
					}
				}else if($siteid == -5){
					for($i =0; $i<24; $i++)
					{
						$element=array();
						if($i < 10)
						{
							if($goods_select=="0" ){
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <%s AND s1.type_id = %s AND s1.type = %s", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $clickstatistic['type_id'],$clickstatistic['type']);							
							}
						    $clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							$element['countClick']=$clicktimes;
							$jsonresult[]=$element;
							
							
						}
						else
						{
							if($goods_select=="0" ){
								if($i == 23){
									$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
								}else{
									$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
								
								
								}
							}else{
								if($i == 23){
									$sql = $wpdb -> prepare("SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $clickstatistic['type_id'],$clickstatistic['type']);
									
								}else{
									$sql = $wpdb -> prepare("SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <%s AND s1.type_id = %s AND s1.type = %s", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $clickstatistic['type_id'],$clickstatistic['type']);
								}
								
							}
							$clicktimes = $wpdb->get_var($sql);
							$element=array();
							$element['today']=$i;
							$element['countClick']=$clicktimes;
							$jsonresult[]=$element;
							
							
						}
					}
						
				}else if($siteid == -6){   
					for($i =0; $i<24; $i++)
					{
						$element=array();
						if($i < 10)
						{
							
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							$element['county']=$clicktimes;
							$jsonresult[]=$element;
							
							
						}
						else
						{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
								
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
								
							}
							$clicktimes = $wpdb->get_var($sql);
							$element=array();
							$element['today']=$i;
							$element['county']=$clicktimes;
							$jsonresult[]=$element;
							
							
						}
					}
				}/*statistic new add end*/
			}
			if (strtotime($enddate) - strtotime($startdate) > $month1) {

				$start_month = date("Y-m", strtotime($startdate));
				$end_month = date("Y-m", strtotime($enddate));
				$current_month = $start_month;

				while(strtotime($current_month) <= strtotime($end_month)) {
				
					if($siteid == -4){   /*statistic new add*/
							if($goods_select=="0" ){//全部显示出来
								
								//所有支付链接
								$sql = $wpdb -> prepare("SELECT goodsindex_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'wepaygoodsinfo' AND type_id = ".$wpdb->prefix."shopping_goodsindex.id ) as counts,type,id from ".$wpdb->prefix."shopping_goodsindex where isshopping='0' and gweid=%s group by id", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59",$gweid);
								$successcounts = $wpdb->get_results($sql);
								foreach($successcounts as &$successcount){
									$successcount->title  = str_replace ( " ", "_" , $successcount->title );
									if($successcount->type=='JSAPI'){
										$pay_type="[网页支付]";
									}else{
										$pay_type="[原生支付]";
									}
									if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->id,$jsonresult) )
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id] = array();
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
								}
								
								//所有原生商品
								$sql = $wpdb -> prepare("SELECT product_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'native_qrcode' AND type_id = ".$wpdb->prefix."shopping_native_qrcode.product_id ) as counts,product_id from ".$wpdb->prefix."shopping_native_qrcode where gweid=%s", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59",$gweid);
								$successcounts = $wpdb->get_results($sql);
								foreach($successcounts as &$successcount){
									$successcount->title  = str_replace ( " ", "_" , $successcount->title );
									$pay_type="[原生商品]";
									if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->product_id,$jsonresult) )
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id] = array();
										$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
								}
								
							}else{
					
					
								$sql = $wpdb -> prepare("SELECT count(*) as counts FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s",  ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $clickstatistic['type_id'],$clickstatistic['type']);
								
								$successcounts = $wpdb->get_results($sql);
								foreach($successcounts as $successcount){
									if( !array_key_exists($clickstatistic['displayname'],$jsonresult) )
										$jsonresult[$clickstatistic['displayname']] = array();
										$jsonresult[$clickstatistic['displayname']][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
								}
						
							}
						}else if($siteid == -5){
							if($goods_select=="0" ){
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1  WHERE s1.time >=%s AND s1.time <=%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
							}else{							
								$sql = $wpdb -> prepare("SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s",  ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $clickstatistic['type_id'],$clickstatistic['type']);
							}
							
							$clicktimes = $wpdb->get_var($sql);
								
							$element=array();
							$element['today']=$current_month;
							$element['countClick']=$clicktimes;
							$jsonresult[]=$element;
					
						
						}elseif($siteid == -6){     //计算成功的订单量、退款的订单量以及进行中的订单量的纵坐标最大值
						
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
							$clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$current_month;
							$element['county']=$clicktimes;
							
							$jsonresult[]=$element;
					
						
						}/*statistic new add end*/
					
				$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}

			}
			echo json_encode($jsonresult);
			exit;
		}
        include $this -> template('orderstatistic_click');
    }
	
	//订单统计图表页面
	public function doWebDownloadstatisticclick(){
	    global $_W, $wpdb;
	    $gweid=$_SESSION['GWEID'];
		
		$downloads = $_GET['downloads'];
		$siteid = $_GET['siteid'];
		$startdate = $_GET['startdate'];
		$enddate = $_GET['enddate'];
		
		
		$goods_select = $_GET['goods_select'];
		
		
		if(!isset($goods_select)){
			$goods_select = 0;
		}
		
		if($goods_select!="0"){
			
			if(is_numeric($goods_select)){
				
				$select=$goods_select;
				/*click statistic*/
				$sql = $wpdb -> prepare("SELECT goodsindex_name,type from ".$wpdb->prefix."shopping_goodsindex WHERE id=%s",$select);
				$info = $wpdb->get_row($sql);
				if($info->type=="JSAPI"){
					$pay_type="[网页支付]";
				}else{
					$pay_type="[原生支付]";
				}
				
				$type='wepaygoodsinfo';
				/*click statistic END*/
				
				$clickstatistic = array(
					'type_id' => $select,
					'type' => $type,
					'displayname'=>$pay_type.$info->goodsindex_name."_".$select
				);
			}else{				
				$number=preg_match_all('/\d+/',$goods_select,$arr);
				$nativeid=$arr[0][0];
				$sql = $wpdb -> prepare("SELECT product_name from ".$wpdb->prefix."shopping_native_qrcode WHERE product_id=%s",$nativeid);
				$info = $wpdb->get_row($sql);
				
				$type='native_qrcode';
				/*click statistic END*/
				
				$clickstatistic = array(
					'type_id' => $nativeid,
					'type' => $type,
					'displayname'=>"[原生商品]".$info->product_name."_".$nativeid
				);
			}
		}
		
		
		
		if(isset($downloads))
		{
			if ($_GET['Selected']!=1) {   //选择输入开始和结束时间单选按钮
						$startdate = $_GET['startdate'];
						$enddate = $_GET['enddate'];
			} else {  //选择时间段单选按钮
				$enddate=date("Y-m-d");
				switch($_GET['period']) {
					case 0 :
						$startdate=date("Y-m-d");
						break;
					case 1 :
						$startdate=date("Y-m-d",strtotime("-1 week +1 day"));
						break;
					case 2 :
						$startdate=date("Y-m-d",strtotime("-1 month +1 day"));
						break;
					case 3 :
						$startdate=date("Y-m-d",strtotime("-3 month +1 day"));
						break;
					case 4 :
						$startdate=date("Y-m-d",strtotime("-1 year +1 day"));
						break;
				}
			}
			$jsonresult=array();
			$day1 = 3600 * 24;
			$month1 = 31 * $day1;
			$year1 = 365 * $day1;
			if(strtotime($enddate) - strtotime($startdate) <= $month1 && strtotime($enddate) - strtotime($startdate) >= $day1){
				$current_date = $startdate;
				
				$filename="流量统计表".$startdate."_".$enddate.".csv";//先定义一个excel文件

				header("Content-Type: application/vnd.ms-execl"); 
				header("Content-Type: application/vnd.ms-excel; charset=utf-8");
				header("Content-Disposition: attachment; filename=$filename"); 
				header("Pragma: no-cache"); 
				header("Expires: 0");
				
				//先输出表头那一行
				echo " ".",";
                while($current_date <= $enddate) {
				
				    echo iconv("utf-8", "gb2312", $current_date."").",";
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				
				/*shopping count*/
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "点击量").",";
				while($current_date <= $enddate) {
					if($goods_select=="0" ){
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
					}else{
						$sql = $wpdb -> prepare("SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s", $current_date." 00:00:00", $current_date." 23:59:59", $clickstatistic['type_id'],$clickstatistic['type']);					
					}
					$countallorders = $wpdb->get_var($sql);
						
					echo iconv("utf-8", "gb2312", $countallorders).",";
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				/*shopping count END*/
				
				/*shopping count*/
				$current_date = $startdate;
				while($current_date <= $enddate) {
					if($goods_select=="0" ){//全部显示出来
						
						//所有支付链接
						$sql = $wpdb -> prepare("SELECT goodsindex_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'wepaygoodsinfo' AND type_id = ".$wpdb->prefix."shopping_goodsindex.id ) as counts,type,id from ".$wpdb->prefix."shopping_goodsindex where isshopping='0' and gweid=%s group by id", $current_date." 00:00:00", $current_date." 23:59:59",$gweid);
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as &$successcount){
							$successcount->title  = str_replace ( " ", "_" , $successcount->title );
							if($successcount->type=='JSAPI'){
								$pay_type="[网页支付]";
							}else{
								$pay_type="[原生支付]";
							}
							if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->id,$jsonresult) )
								$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id] = array();
								$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
						}
						//所有原生商品
						$sql = $wpdb -> prepare("SELECT product_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'native_qrcode' AND type_id = ".$wpdb->prefix."shopping_native_qrcode.product_id ) as counts,product_id from ".$wpdb->prefix."shopping_native_qrcode where gweid=%s", $current_date." 00:00:00", $current_date." 23:59:59",$gweid);
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as &$successcount){
							$successcount->title  = str_replace ( " ", "_" , $successcount->title );
							$pay_type="[原生商品]";
							if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->product_id,$jsonresult) )
								$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id] = array();
								$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
						}
					}else{
					
						$sql = $wpdb -> prepare("SELECT count(*) as counts FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s", $current_date." 00:00:00", $current_date." 23:59:59", $clickstatistic['type_id'],$clickstatistic['type']);
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as $successcount){
							if( !array_key_exists($clickstatistic['displayname'],$jsonresult) )
								$jsonresult[$clickstatistic['displayname']] = array();
								$jsonresult[$clickstatistic['displayname']][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
						}
					}
						
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				//下载商品+一级分类+二级分类
				foreach($jsonresult as  $k=>$val){ 
					echo iconv("utf-8", "gb2312", "{$k}").",";
					foreach($val as  $v=>$va){ 
						echo iconv("utf-8", "gb2312", "{$va['countclick']}").",";
					}
					echo "\n";
				}
				/*shopping count END*/
				
			
			}
			if(strtotime($enddate) - strtotime($startdate) == 0)
			{
				$current_date = $startdate;
				
				$filename="流量统计表".$current_date.".csv";//先定义一个excel文件

				header("Content-Type: application/vnd.ms-execl"); 
				header("Content-Type: application/vnd.ms-excel; charset=utf-8");
				header("Content-Disposition: attachment; filename=$filename"); 
				header("Pragma: no-cache"); 
				header("Expires: 0");
				
				//先输出表头那一行
				echo " ".",";
				
				for($i =0; $i<24; $i++)
				{
				    if($i == 0)
				        echo iconv("utf-8", "gb2312", "24点--".($i+1)."点").",";
					else  
					    echo iconv("utf-8", "gb2312", $i."点--".($i+1)."点").",";
				}
				echo "\n";
				
				//输出当日商城点击量
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "点击量").",";
				for($i =0; $i<24; $i++)
				{
				    if($i < 10)
					{
						if($goods_select=="0" ){
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <%s AND s1.type_id = %s AND s1.type = %s", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $clickstatistic['type_id'],$clickstatistic['type']);
						
						}
				        $countallcounts = $wpdb->get_var($sql);
						
						echo iconv("utf-8", "gb2312", $countallcounts).",";
					}
					else
					{
						if($goods_select=="0" ){
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							
							}
						}else{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $clickstatistic['type_id'],$clickstatistic['type']);
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <%s AND s1.type_id = %s AND s1.type = %s", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $clickstatistic['type_id'],$clickstatistic['type']);
							}
						
						}
				        $countallcounts = $wpdb->get_var($sql);
						
						echo iconv("utf-8", "gb2312", $countallcounts).",";
					}
				}
				echo "\n";
				
				/*商城统计*/
				$current_date = $startdate;
				
				for($i =0; $i<24; $i++)
				{
					if($i < 10)
					{
						
						if($goods_select=="0" ){//全部显示出来
							
							//所有支付链接
							$sql = $wpdb -> prepare("SELECT goodsindex_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'wepaygoodsinfo' AND type_id = ".$wpdb->prefix."shopping_goodsindex.id ) as counts,type,id from ".$wpdb->prefix."shopping_goodsindex where isshopping='0' and gweid=%s group by id",$current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00",$gweid);
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as &$successcount){
								$successcount->title  = str_replace ( " ", "_" , $successcount->title );
								if($successcount->type=='JSAPI'){
									$pay_type="[网页支付]";
								}else{
									$pay_type="[原生支付]";
								}
								if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->id,$jsonresult) )
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id] = array();
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id][] = array("today" => $i, "countclick" => $successcount->counts);
							}
							//所有原生商品
							$sql = $wpdb -> prepare("SELECT product_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'native_qrcode' AND type_id = ".$wpdb->prefix."shopping_native_qrcode.product_id ) as counts,product_id from ".$wpdb->prefix."shopping_native_qrcode where gweid=%s",$current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00",$gweid);
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as &$successcount){
								$successcount->title  = str_replace ( " ", "_" , $successcount->title );
								$pay_type="[原生商品]";
								if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->product_id,$jsonresult) )
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id] = array();
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id][] = array("today" => $i, "countclick" => $successcount->counts);
							}
						
						}else{
						
							$sql = $wpdb -> prepare("SELECT count(*) as counts FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <%s AND s1.type_id = %s AND s1.type = %s", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $clickstatistic['type_id'],$clickstatistic['type']);
									
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as $successcount){
								if( !array_key_exists($clickstatistic['displayname'],$jsonresult) )
									$jsonresult[$clickstatistic['displayname']] = array();
									$jsonresult[$clickstatistic['displayname']][] = array("today" => $i, "countclick" => $successcount->counts);
							}
						}
							
					}
					else
					{
						
							
						if($goods_select=="0" ){//全部显示出来
									
												
							//所有支付链接
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT goodsindex_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'wepaygoodsinfo' AND type_id = ".$wpdb->prefix."shopping_goodsindex.id ) as counts,type,id from ".$wpdb->prefix."shopping_goodsindex where isshopping='0' and gweid=%s group by id", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59",$gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT goodsindex_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'wepaygoodsinfo' AND type_id = ".$wpdb->prefix."shopping_goodsindex.id ) as counts,type,id from ".$wpdb->prefix."shopping_goodsindex where isshopping='0' and gweid=%s group by id", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00",$gweid);
							}
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as &$successcount){
								$successcount->title  = str_replace ( " ", "_" , $successcount->title );
								if($successcount->type=='JSAPI'){
									$pay_type="[网页支付]";
								}else{
									$pay_type="[原生支付]";
								}
								if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->id,$jsonresult) )
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id] = array();
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id][] = array("today" => $i, "countclick" => $successcount->counts);
							}
							//所有原生商品
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT product_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'native_qrcode' AND type_id = ".$wpdb->prefix."shopping_native_qrcode.product_id ) as counts,product_id from ".$wpdb->prefix."shopping_native_qrcode where gweid=%s", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59",$gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT product_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'native_qrcode' AND type_id = ".$wpdb->prefix."shopping_native_qrcode.product_id ) as counts,product_id from ".$wpdb->prefix."shopping_native_qrcode where gweid=%s", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00",$gweid);
							}
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as &$successcount){
								$successcount->title  = str_replace ( " ", "_" , $successcount->title );
								$pay_type="[原生商品]";
								if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->product_id,$jsonresult) )
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id] = array();
									$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id][] = array("today" => $i, "countclick" => $successcount->counts);
							}
						}else{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(*) as counts FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $clickstatistic['type_id'],$clickstatistic['type']);
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) as counts FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <%s AND s1.type_id = %s AND s1.type = %s", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $clickstatistic['type_id'],$clickstatistic['type']);
							}
									
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as $successcount){
								if( !array_key_exists($clickstatistic['displayname'],$jsonresult) )
									$jsonresult[$clickstatistic['displayname']] = array();
									$jsonresult[$clickstatistic['displayname']][] = array("today" => $i, "countclick" => $successcount->counts);
							}
						}
						
					}
				}
				
				//下载商品+一级分类+二级分类
				foreach($jsonresult as  $k=>$val){ 
					echo iconv("utf-8", "gb2312", "{$k}").",";
					foreach($val as  $v=>$va){ 
						echo iconv("utf-8", "gb2312", "{$va['countclick']}").",";
					}
					echo "\n";
				}
				/*商城统计END*/
				
			}

			if (strtotime($enddate) - strtotime($startdate) > $month1) {

				$start_month = date("Y-m", strtotime($startdate));
				$end_month = date("Y-m", strtotime($enddate));
				$current_month = $start_month;
				
				$filename="流量统计表".$start_month."月份-".$end_month."月份.csv";//先定义一个excel文件
				
				header("Content-Type: application/vnd.ms-execl"); 
				header("Content-Type: application/vnd.ms-excel; charset=utf-8");
				header("Content-Disposition: attachment; filename=$filename"); 
				header("Pragma: no-cache"); 
				header("Expires: 0");
				
				//先输出表头那一行
				echo " ".",";
                while(strtotime($current_month) <= strtotime($end_month)) {
				
				    echo iconv("utf-8", "gb2312", $current_month."月份").",";
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				echo "\n";
				
				
				//输出每月商城点击量
				$current_month = $start_month;
				echo iconv("utf-8", "gb2312", "点击量").",";
				while(strtotime($current_month) <= strtotime($end_month)) {
					if($goods_select=="0" ){
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND (s1.type='wepaygoodsinfo' or s1.type='native_qrcode') AND s1.gweid = %s", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
					}else{
						$sql = $wpdb -> prepare("SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s",  ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $clickstatistic['type_id'],$clickstatistic['type']);
					
					}
				    $allcounts = $wpdb->get_var($sql);
						
					echo iconv("utf-8", "gb2312", $allcounts).",";
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				
				echo "\n";
				
				/*商城统计*/
				$current_month = $start_month;
				while(strtotime($current_month) <= strtotime($end_month)) {
					if($goods_select=="0" ){//全部显示出来
						
						//所有支付链接
						$sql = $wpdb -> prepare("SELECT goodsindex_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'wepaygoodsinfo' AND type_id = ".$wpdb->prefix."shopping_goodsindex.id ) as counts,type,id from ".$wpdb->prefix."shopping_goodsindex where isshopping='0' and gweid=%s group by id", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59",$gweid);
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as &$successcount){
							$successcount->title  = str_replace ( " ", "_" , $successcount->title );
							if($successcount->type=='JSAPI'){
								$pay_type="[网页支付]";
							}else{
								$pay_type="[原生支付]";
							}
							if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->id,$jsonresult) )
								$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id] = array();
								$jsonresult[$pay_type.$successcount->title.'_'.$successcount->id][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
						}
						//所有原生商品
						$sql = $wpdb -> prepare("SELECT product_name as title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'native_qrcode' AND type_id = ".$wpdb->prefix."shopping_native_qrcode.product_id ) as counts,product_id from ".$wpdb->prefix."shopping_native_qrcode where gweid=%s", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59",$gweid);
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as &$successcount){
							$successcount->title  = str_replace ( " ", "_" , $successcount->title );
							$pay_type="[原生商品]";
							if( !array_key_exists($pay_type.$successcount->title.'_'.$successcount->product_id,$jsonresult) )
								$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id] = array();
								$jsonresult[$pay_type.$successcount->title.'_'.$successcount->product_id][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
						}
					}else{
			
			
						$sql = $wpdb -> prepare("SELECT count(*) as counts FROM ".$wpdb->prefix."shopping_statistics s1 where s1.time >=%s AND s1.time <=%s AND s1.type_id = %s AND s1.type = %s",  ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $clickstatistic['type_id'],$clickstatistic['type']);
						
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as $successcount){
							if( !array_key_exists($clickstatistic['displayname'],$jsonresult) )
								$jsonresult[$clickstatistic['displayname']] = array();
								$jsonresult[$clickstatistic['displayname']][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
						}
					}	
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				//下载商品+一级分类+二级分类
				foreach($jsonresult as  $k=>$val){ 
					echo iconv("utf-8", "gb2312", "{$k}").",";
					foreach($val as  $v=>$va){ 
						echo iconv("utf-8", "gb2312", "{$va['countclick']}").",";
					}
					echo "\n";
				}
				/*商城统计END*/
				
			}	
			exit;
		}
		
	}		
	
	//维权管理页面
	public function doWebRightmanage(){
	
	    global $_GPC,$wpdb;
		$gweid=$_SESSION['GWEID'];
		$search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		if($search_condition == 'rights_status')
			$search_content = trim($_GET['rights_status']);
		if($search_condition == 'msgtype')
			$search_content = trim($_GET['msgtype']);
		$search = array(
			'all' => '',
			'id' => "AND id LIKE '%%{$search_content}%%'",
			'feedbackid' => "AND feedbackid LIKE '%%{$search_content}%%'",
			'out_trade_no' => "AND u1.out_trade_no LIKE '%%{$search_content}%%'",
			'rights_status' => "AND rights_status = '{$search_content}'",
			'msgtype' => "AND msgtype = '{$search_content}'"
		);
		$total = $wpdb -> get_var($wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_rights u1,{$wpdb->prefix}shopping_order u2 where u1.out_trade_no = u2.out_trade_no and u2.gweid=%s {$search[$search_condition]} ORDER BY id DESC", $gweid));
	    $pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		$rslist = $wpdb -> get_results($wpdb -> prepare("SELECT  *,u1.read as reds FROM {$wpdb -> prefix}shopping_rights u1,{$wpdb->prefix}shopping_order u2 where u1.out_trade_no = u2.out_trade_no and u2.gweid=%s  {$search[$search_condition]} ORDER BY id DESC Limit {$offset},{$psize}", $gweid),ARRAY_A);
		//删除某个维权
		if(isset($_POST['rihgtsindex_del']) && !empty($_POST['rihgtsindex_del']) ){
		$rightid=$_POST['rihgtsindexid'];
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_rights WHERE id = %d", $rightid ) );
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$hint = array("status"=>"success","message"=>"删除成功");
			}
			echo json_encode($hint);
			exit;	
		}
        include $this -> template('rightmanage');
    }
	//维权详细信息页面
	public function doWebCheckrightdetail(){
		global $_W,$_GPC,$wpdb;
		$RIGHT_MSGTYPE = array(
					'confirm' => '用户同意维权结果',
					'reject' => '用户不同意维权结果');
		$RIGHT_REASON = array(
			'1' =>'商品质量有问题',
			'2' => '商品与实际购买不符',
			'3' => '商品发货延迟',
			'4' => '其他原因');
		$RIGHT_SOLUTION = array(
			'1' =>'退款退货',
			'2' => '退款不退货',
			);
		$RIGHT_STATUS = array(
			'1' =>'未处理',
			'2' => '处理中',
			'3' => '已解决');
			$rid=$_GET['id'];
			if( isset($_POST['right_id']) ){
			$timestamp = time();
			$right_id = $_POST['right_id'];
			$out_trade_no = $_POST['right_out_trade_no'];
			$right_feedbackid = $_POST['right_feedbackid'];
			$right_extinfo = $_POST['right_extinfo'];
			$right_picurl = $_POST['right_picurl'];
			$right_create_time = $_POST['right_create_time'];
			$rights_result = $_POST['rights_result'];
			$rights_notes = $_POST['rights_notes'];
			$rights_status = $_POST['rights_status'];
			$end_time = date('Y-m-d H:i:s');
			$wpdb->update("{$wpdb -> prefix}shopping_rights", array( 'out_trade_no' => $out_trade_no,'feedbackid' => $right_feedbackid,'extinfo' => $right_extinfo,'create_time' => $right_create_time,'rights_result' => $rights_result,'rights_notes' => $rights_notes,'rights_status' => $rights_status,'end_time' => $end_time),array('id' => $rid ), array('%s','%s','%s','%s','%d','%s','%s','%s'),array('%d') );   
			}
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_rights WHERE id =%d",$rid);
			$rightslist = $wpdb->get_row($sql,ARRAY_A);
			if(!$rightslist['read'])
			$wpdb -> update("{$wpdb -> prefix}shopping_rights",array('read' => 1),array('id' => $rid));
			include $this -> template('checkrightdetail');
    }
	 public function doWebRightsCheck(){
		global $wpdb;
		$gweid=$_SESSION['GWEID'];
		$list = $wpdb -> get_results($wpdb -> prepare("SELECT  id FROM {$wpdb->prefix}shopping_rights u1,{$wpdb->prefix}shopping_order u2 where u1.out_trade_no = u2.out_trade_no and u2.gweid=%s AND u1.read=0 ORDER BY id DESC", $gweid),ARRAY_A);
		echo json_encode(array('new_status' => empty($list)?FALSE:TRUE));
	}
	/**
	 function：goodsindex management
	*/
	//后台管理--获取根据查询条件得到的网页支付商品定制页面个数
		function doWebCountSelectedGoodsindex($gweid,$indata,$rg,$type){
		global $wpdb;
		$sql  = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_goodsindex where type=%s and gweid=%s and ".$rg." like '%%".$indata."%%'",$type,$gweid);
		$myrows = $wpdb->get_var($sql);
		return $myrows;
	}
	//后台管理--获取根据查询条件得到的支付商品定制页面
	function doWebCountSelectedGoodsindexsPage($gweid,$indata,$rg,$offset,$psize,$type){
		global $wpdb;
		$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goodsindex where type=%s and gweid=%s and ".$rg." like '%%".$indata."%%' ORDER BY id desc limit %d,%d",$type,$gweid,$offset,$psize);
		$myrows = $wpdb->get_results($sql);
		return $myrows;	
	}
	//后台管理--获取网页商品定制页面个数
	public function doWebCountGoodsindex($gweid,$type){
		global $wpdb;
		$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_goodsindex WHERE type=%s and gweid=%s",$type,$gweid);
		$myrows = $wpdb->get_var($sql);
		return $myrows;
	}
	//后台管理--获取支付商品定制页面
	public function doWebCountGoodsindexsPage($offset,$psize,$gweid,$type){
		global $wpdb;
		$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_goodsindex WHERE type=%s and gweid=%s ORDER BY id DESC limit %d,%d",$type,$gweid,$offset,$psize);
		$myrows = $wpdb->get_results($sql);
		return $myrows;
	} 
	//商品列表管理
	public function doWebQrcodemanage(){
	    global $_W,$_GPC,$wpdb;
	   $gweid = $_SESSION['GWEID'];
	   $search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		$search = array(
			'all' => '',
			'product_id' => "AND product_id LIKE '%%{$search_content}%%'",
			'product_name' => "AND product_name LIKE '%%{$search_content}%%'"
		);
		$total = $wpdb -> get_var($wpdb -> prepare("SELECT  COUNT(*) FROM {$wpdb -> prefix}shopping_native_qrcode WHERE gweid=%s {$search[$search_condition]} ORDER BY product_id DESC", $gweid));
	    $pindex = max(1, intval($_GET['page']));
		$psize = 5;
		$pindex = min(max(ceil($total/$psize),1),$pindex );
		$offset=($pindex - 1) * $psize;
		$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		$productslist = $wpdb -> get_results($wpdb -> prepare("SELECT  * FROM {$wpdb -> prefix}shopping_native_qrcode WHERE gweid=%s {$search[$search_condition]} ORDER BY product_id DESC Limit {$offset},{$psize}", $gweid),ARRAY_A);
		//删除商品信息
		if(isset($_POST['productindex_del']) && !empty($_POST['productindex_del']) ){
		$proid=$_POST['productindexid'];
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_native_qrcode WHERE product_id = %d", $proid ) );
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$hint = array("status"=>"success","message"=>"删除成功");
			}
			echo json_encode($hint);
			exit;		
		}
		
	    if(isset($_POST['goodsindex_del']) && !empty($_POST['goodsindex_del']) ){							
			$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_goodsindex WHERE id=%s", $_POST['goodsindexid']));			
			//不删除商品以免订单对应数据看不到商品信息,商品设置为下架
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$update=$wpdb->update( $wpdb->prefix.'shopping_goods', array('status'=>1),array('groupid'=>$_POST['goodsindexid']), array('%s'));
				if($update===false){
					$hint = array("status"=>"error","message"=>"删除失败");
				}else{
					$hint = array("status"=>"success","message"=>"删除成功");
				}
			}
			echo json_encode($hint);
			exit;	
			
		}
		include $this -> template('qrcodemanage');
    }
	//生成原生支付二维码
	public function doWebCreateproductqrinfo(){
	    global $_W, $_GPC ,$wpdb;
	    $gweid = $_SESSION['GWEID'];	
		if( isset($_POST['product_name']) ){
			$product_name = $_POST['product_name'];
			$product_price = number_format($_POST['product_price'],2,".","");
			$product_description = $_POST['product_description'];
			$product_notes = $_POST['product_notes'];	
			$wpdb->insert( 'wp_shopping_native_qrcode', array('gweid' => $gweid,'product_name' => $product_name, 'product_price' => $product_price,'product_description' => $product_description,'product_notes' => $product_notes,'qr_code' => 'url'), array('%s','%s','%f','%s','%s','%s') ) ;
			$productid=$wpdb->insert_id;
			$weixin = new WeixinPay($gweid);
			$pictureurl=$weixin->create_native_product_url('p'.$productid);
			$wpdb->update( 'wp_shopping_native_qrcode', array( 'qr_code' => $pictureurl),array('product_id' => $productid ), array('%s'),array('%d') );
			//添加完商品信息之后，跳转到商品详情信息页面，查看新生成的商品信息及二维码（或下载图片），还可以更新商品信息。
			?>
			<script>
				location.href="<?php echo $this->createWebUrl('productinformation',array('id' => $productid));?>";
			</script>
				<?php
				exit;
	} 
	 include $this -> template('createproductqrinfo');
    }

	//商品详情页面
	public function doWebProductinformation(){
	    global $wpdb;
	    $gweid = $_SESSION['GWEID'];
		$proid=$_GET['id'];
		if( isset($_POST['product_name'])&&!empty($_POST['product_name'])){
		   $product_name = $_POST['product_name'];
		   $product_price = number_format($_POST['product_price'],2,".","");
		   $product_description = $_POST['product_description'];
		   $product_notes = $_POST['product_notes'];
		   $qr_code = $_POST['qr_code'];
		   $wpdb->update( 'wp_shopping_native_qrcode', array( 'product_name' => $product_name,'product_price' => $product_price,'product_description' => $product_description,'product_notes' => $product_notes,'qr_code' => $qr_code),array('product_id' => $proid ), array('%s','%f','%s','%s','%s'),array('%d') );   
			}
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_native_qrcode WHERE gweid=%s and product_id =%d",$gweid,$proid );
			$productlist = $wpdb->get_row($sql,ARRAY_A);
		include $this -> template('productinformation');
    }
	//查看商品详细信息时查看支付二维码
	public function doWebShowproductqr(){
		global $_W, $_GPC ,$wpdb;
		$gweid = $_SESSION['GWEID'];
		$productid = $_GET['product_id'];
		$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_native_qrcode WHERE gweid =%s and product_id =%d",$gweid,$productid );
		$myrows = $wpdb->get_results($sql,ARRAY_A);
			foreach($myrows as $productqrinfo)	
			{
				$pictureurl=$productqrinfo['qr_code'];
			}
		include 'phpqrcode.php'; 
		if(isset($_GET['download'])){
			header('Content-Disposition: attachment; filename="qrcode-'.$productid.'.png"');  
			QRcode::png($pictureurl,false,8);	
			
		}else{
			QRcode::png($pictureurl);	
		}
    }

	//全局变量设置页面
	public function doWebGlobalsetting(){
	    global $_W, $_GPC, $wpdb;
		$gweid = $_SESSION['GWEID'];
		 
		//change to https in globalsetting page
		if (!is_ssl() ) {
			if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
				wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
				exit();
			} else {
				//wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				wp_redirect(preg_replace('|^http://|', 'https://', home_url().'/module.php?module=wepay&do=globalSettinghttps&gweid='.$gweid));
				exit();
			}
		}  
    }
	
	//change globalsetting page to https
	public function doWebGlobalSettinghttps(){
	    global $_W, $_GPC, $wpdb;
		$gweid = $_SESSION['GWEID'];
		
		//提交form表单
		if( $_SERVER['REQUEST_METHOD'] === 'POST'){
			
            $payment_url = $_POST['payment_url'];
			$alarm_url = $_POST['alarm_url'];
			$nativepay_url = $_POST['nativepay_url'];	   
			$appid = $_POST['appid'];
		    $appkey = $_POST['appkey'];
			$appsecret = $_POST['appsecret'];
			$mchid = $_POST['mchid'];	   
			$contactemergency = $_POST['contactemergency'];	   
			$contactnumber = $_POST['contactnumber'];
			$contactemail = $_POST['contactemail'];
			$sqlgeturl = $wpdb -> prepare("SELECT * FROM wp_shopping_global WHERE gweid = %s", $gweid);
			$urlsettings = $wpdb->get_results($sqlgeturl);

			foreach($urlsettings as $urlsetting)
			{
				$certificate_url = $urlsetting -> certificate_url;  //先查找该字段中有没有值
				$certificate1_url = $urlsetting -> 	certificate1_url;  //先查找该字段中有没有值
			}
			
			//先添加到globalsetting这张表，再添加到url_pattern_mapping
			$globalsetting = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."shopping_global"."(gweid, payment_url, alarm_url, nativepay_url, appid, appkey, appsecret, mch_id, contact_emergency, contact_phone, contact_email, certificate_url, certificate1_url)VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",$gweid, $payment_url, $alarm_url, $nativepay_url, $appid, $appkey, $appsecret, $mchid, $contactemergency, $contactnumber,$contactemail, $certificate_url, $certificate1_url));
			//只有三个url全不为空才允许insert到wp_url_pattern_mapping这张表
			if( !empty($payment_url) &&!empty($alarm_url) && !empty($nativepay_url)){
				$urlpatternalarm = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."url_pattern_mapping"."(GWEID, module, type, value, url_pattern)VALUES (%s, %s, %s, %s, %s)",$gweid, 'wepay','action', 'AlarmNotify', $alarm_url));
				$urlpatternnative = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."url_pattern_mapping"."(GWEID, module, type, value,  url_pattern)VALUES (%s, %s, %s, %s, %s)",$gweid, 'wepay','action', 'NativePayNotify', $nativepay_url));
				$urlpatternpayment = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."url_pattern_mapping"."(GWEID, module, type, value,  url_pattern)VALUES (%s, %s, %s, %s, %s)",$gweid, 'wepay','base', '', $payment_url));
			}
			//付款通知路径
			$urlpatternpay = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."url_pattern_mapping"."(GWEID, module, type, value, url_pattern)VALUES (%s, %s, %s, %s, %s)",$gweid, 'wepay','action', 'PaidNotify', $gweid.'PaidNotify'));
			
			//change to ajax submit
			if($globalsetting===false || $urlpatternalarm===false || $urlpatternnative===false || $urlpatternpayment===false || $urlpatternpay===false){
			    $hint = array("status"=>"error","message"=>"提交失败，请重新提交") ;
				echo json_encode($hint);
				exit;
			}else{
				$hint = array("status"=>"success","message"=>"提交成功");
				echo json_encode($hint);
				exit;
			}
			
		}

		$sql = $wpdb -> prepare("SELECT * FROM wp_shopping_global WHERE gweid = %s", $gweid);
		$globalsettings = $wpdb->get_results($sql);

		foreach($globalsettings as $globalsetting)
		{
		    $payment_url = $globalsetting -> payment_url; 
		    $alarm_url = $globalsetting -> alarm_url;
			$nativepay_url = $globalsetting -> nativepay_url;	   
			$appid = $globalsetting -> appid;
			$appkey = $globalsetting -> appkey;
			$appsecret = $globalsetting -> appsecret;
			$mchid = $globalsetting -> mch_id;	   
			$contactemergency = $globalsetting -> contact_emergency;	   
			$contactnumber = $globalsetting -> contact_phone;
			$contactemail = $globalsetting -> contact_email;
		}
		include $this -> template('globalsettinghttps');  
	
	}
	
	//check url in globalsetting page
	public function doWebUrlcheck(){
	
	    global $_W, $_GPC, $wpdb;
		$gweid = $_SESSION['GWEID'];
		
		//check url before submit the form
		if( isset($_POST['urlcheck_submit']) ){
			
			$urlstring = $_POST['urlstring'];
			$value = $_POST['value'];
			
			$sql = $wpdb -> prepare("SELECT count(*) as urlcount FROM wp_url_pattern_mapping WHERE url_pattern = %s and value != %s and GWEID != %s", $urlstring, $value, $gweid);
			$counturls = $wpdb->get_results($sql);
			
			foreach($counturls as $counturl)
			{
				if(!empty($counturl)) 
				{
					$urlcount = $counturl -> urlcount;
				}
			}
			if ($urlcount == 0) 
			{
				$hint = array("status"=>"success","message"=>"可以使用");
				echo json_encode($hint);
				exit;
			}
			else
			{
			    $hint = array("status"=>"error","message"=>"支付授权url重复，请重新输入") ;
				echo json_encode($hint);
				exit;
			} 
				
		}
		
		$urlstr = $_GET['urlstring'];
		$value = $_GET['value'];
		//在整个表中查找是否有重复
		$sql = $wpdb -> prepare("SELECT count(*) as urlcount FROM wp_url_pattern_mapping WHERE url_pattern = %s and value != %s and GWEID != %s", $urlstr, $value, $gweid);
		$counturls = $wpdb->get_results($sql);
		
		foreach($counturls as $counturl)
		{
		    if(!empty($counturl)) 
			{
				$urlcount = $counturl -> urlcount;
			}
		}
		if ($urlcount == 0) 
		{
			echo "<font color=#008FFF>&nbsp;可以使用!</font>";
		}
		else
		{
			echo "<font color=red>&nbsp;该url已存在!</font>";
		} 
		
	}
	//check appkey in globalsetting page
	public function doWebAppKeycheck(){
	
	    global $_W, $_GPC, $wpdb;
		$gweid = $_SESSION['GWEID'];
		
		//check url before submit the form
		if( isset($_POST['appkey_submit']) ){
			
			$appkeystr = $_POST['keystring'];
			
			$sql = $wpdb -> prepare("SELECT count(*) as urlcount FROM wp_shopping_global WHERE appkey = %s AND gweid != %s", $appkeystr, $gweid);
			$counturls = $wpdb->get_results($sql);
			
			foreach($counturls as $counturl)
			{
				if(!empty($counturl)) 
				{
					$urlcount = $counturl -> urlcount;
				}
			}
			if ($urlcount == 0) 
			{
				$hint = array("status"=>"success","message"=>"可以使用");
				echo json_encode($hint);
				exit;
			}
			else
			{
				$hint = array("status"=>"error","message"=>"支付密钥key重复，请重新输入") ;
				echo json_encode($hint);
				exit;
			} 
			
		}
		
		
		$keystring = $_GET['keystring'];
		
		$sql = $wpdb -> prepare("SELECT count(*) as urlcount FROM wp_shopping_global WHERE appkey = %s AND gweid != %s", $keystring, $gweid);
		$counturls = $wpdb->get_results($sql);
		
		foreach($counturls as $counturl)
		{
		    if(!empty($counturl)) 
			{
				$urlcount = $counturl -> urlcount;
			}
		}
		if ($urlcount == 0) 
		{
			echo "<font color=#008FFF>&nbsp;可以使用!</font>";
		}
		else
		{
			echo "<font color=red>&nbsp;该key已存在,请重新填写!</font>";
		} 
		
	}
	//check url in globalsetting page
	public function doWebUploadcertificate(){
		global $_W, $_GPC ,$wpdb;
		$gweid = $_GET['gweid'];
		$certificatetype = $_GET['type'];
	    include 'upload/uploadcertificate.php';
		
		if($certificatetype == "certificate1")
		{
		
			$filename = $_FILES['file']['name'];
			$filesize = $_FILES['file']['size'];
			if ($filename != "") {
				$type = strstr($filename, '.');
				if ($type != ".pem" && $type != ".PEM") {
					$arr = array(
						'status'=>"文件格式不对"
						
					);
					echo json_encode($arr);
					exit;
				}
				
				$up=new upfile();
				$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
				$up->get_ph_type($_FILES["file"]["type"]);
				$up->get_ph_size($_FILES["file"]["size"]);
				$up->get_ph_name($_FILES["file"]["name"]);		
				$fileUrl=$up->save();  //这个是相对路径
				//$path=substr( $fileUrl,strripos($fileUrl,'uploads/')+8 );
				if($fileUrl!=false){
					$path=substr( $fileUrl,strripos($fileUrl,'uploads/')+8 );
				}else{
					$arr = array(
						'status'=>"文件上传错误,可能是空间不足,请检查后重试"
						
					);
					echo json_encode($arr);
					exit;
				}
				
			}
			$size = round($filesize/1024,2);
			$upload =wp_upload_dir();
			
			if((empty($fileUrl))||(stristr($fileUrl,"http")!==false)){
				$echofileurl=$fileUrl;  //这个是相对路径
			}else{
				$echofileurl=$upload['baseurl'].$fileUrl;   //这个是完整的url
			}
			//更新数据库之前需要先查出该数据表中相应gweid对应的其他字段值
			$sql = $wpdb -> prepare("SELECT * FROM wp_shopping_global WHERE gweid = %s", $gweid);
			$globalsettings = $wpdb->get_results($sql);

			foreach($globalsettings as $globalsetting)
			{
				$payment_url = $globalsetting -> payment_url;
				$alarm_url = $globalsetting -> alarm_url;
				$right_url = $globalsetting -> right_url;
				$nativepay_url = $globalsetting -> nativepay_url;	   
				$appid = $globalsetting -> appid;
				$appkey = $globalsetting -> appkey;
				$appsecret = $globalsetting -> appsecret;
				$mchid = $globalsetting -> mch_id;	   
				$contactemergency = $globalsetting -> contact_emergency;	   
				$contactnumber = $globalsetting -> contact_phone;
				$contactemail = $globalsetting -> contact_email;
				$certificate_url = $globalsetting -> certificate_url;  //这条字段是需要更新的
				$certificate1_url = $globalsetting -> certificate1_url;  //这条字段是需要更新的
			}
			//将路径写进数据库
			$globalcertificateurl = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."shopping_global"."(gweid, payment_url, alarm_url, right_url, nativepay_url, appid, appkey, appsecret, mch_id, contact_emergency, contact_phone, contact_email, certificate_url, certificate1_url)VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",$gweid, $payment_url, $alarm_url, $right_url, $nativepay_url, $appid, $appkey, $appsecret, $mchid, $contactemergency, $contactnumber, $contactemail, $fileUrl, $certificate1_url));
			
			if($globalcertificateurl===false){
				$arr = array(
					'status'=>"上传失败"
					
				);
				echo json_encode($arr);
			
			}else{
				$arr = array(
					'name'=>$filename,
					'file'=>$echofileurl,
					'size'=>$size,
					'status'=>"上传成功"
				);
				echo json_encode($arr);
			
			}
		}
		if($certificatetype == "certificate2")
		{
		
			$filename = $_FILES['file1']['name'];
			$filesize = $_FILES['file1']['size'];
			if ($filename != "") {
				$type = strstr($filename, '.');
				
				if ($type != ".pem" && $type != ".PEM") {
					$arr = array(
						'status'=>"文件格式不对"
						
					);
					echo json_encode($arr);
					exit;
				}  
				
				$up1=new upfile();
				$up1->get_ph_tmpname($_FILES["file1"]["tmp_name"]);
				$up1->get_ph_type($_FILES["file1"]["type"]);
				$up1->get_ph_size($_FILES["file1"]["size"]);
				$up1->get_ph_name($_FILES["file1"]["name"]);		
				$fileUrl=$up1->save1();  //这个是相对路径
				//$path=substr( $fileUrl,strripos($fileUrl,'uploads/')+8 );
				if($fileUrl!=false){
					$path=substr( $fileUrl,strripos($fileUrl,'uploads/')+8 );
				}else{
					$arr = array(
						'status'=>"文件上传错误,可能是空间不足,请检查后重试"
						
					);
					echo json_encode($arr);
					exit;
				} 
			}
			$size = round($filesize/1024,2);
			$upload =wp_upload_dir();
			
			if((empty($fileUrl))||(stristr($fileUrl,"http")!==false)){
				$echofileurl=$fileUrl;  //这个是相对路径
			}else{
				$echofileurl=$upload['baseurl'].$fileUrl;   //这个是完整的url
			}
			//更新数据库之前需要先查出该数据表中相应gweid对应的其他字段值
			$sql = $wpdb -> prepare("SELECT * FROM wp_shopping_global WHERE gweid = %s", $gweid);
			$globalsettings = $wpdb->get_results($sql);

			foreach($globalsettings as $globalsetting)
			{
			    $payment_url = $globalsetting -> payment_url;
				$alarm_url = $globalsetting -> alarm_url;
				$right_url = $globalsetting -> right_url;
				$nativepay_url = $globalsetting -> nativepay_url;	   
				$appid = $globalsetting -> appid;
				$appkey = $globalsetting -> appkey;
				$appsecret = $globalsetting -> appsecret;
				$mchid = $globalsetting -> mch_id;	   
				$contactemergency = $globalsetting -> contact_emergency;	   
				$contactnumber = $globalsetting -> contact_phone;
				$contactemail = $globalsetting -> contact_email;
				$certificate_url = $globalsetting -> certificate_url;  //这条字段是需要更新的
				$certificate1_url = $globalsetting -> certificate1_url;  //这条字段是需要更新的
			}
			//将路径写进数据库
			$globalcertificateurl = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."shopping_global"."(gweid, payment_url, alarm_url, right_url, nativepay_url, appid, appkey, appsecret, mch_id, contact_emergency, contact_phone, contact_email, certificate_url, certificate1_url)VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",$gweid, $payment_url, $alarm_url, $right_url, $nativepay_url, $appid, $appkey, $appsecret, $mchid, $contactemergency, $contactnumber, $contactemail, $certificate_url, $fileUrl));
			
			if($globalcertificateurl===false){
				$arr = array(
					'status'=>"上传失败"
					
				);
				echo json_encode($arr);
			
			}else{
				$arr = array(
					'name'=>$filename,
					'file'=>$echofileurl,
					'size'=>$size,
					'status'=>"上传成功"
				);
				echo json_encode($arr);
			
			}
		}
	}
	
	
	//支付测试首页
	public function doWebPaymenttest(){
	    global $_W, $_GPC, $wpdb;
		$gweid = $_SESSION['GWEID'];
		
		if( isset($_POST['submit_test']) ){
			
			$testurl = $_POST['testurl'];
			$flagvalue = $_POST['flagvalue'];
			
			//更新数据库之前需要先查出该数据表中相应gweid对应的其他字段值
			$sql = $wpdb -> prepare("SELECT * FROM wp_shopping_global WHERE gweid = %s", $gweid);
			$testsettings = $wpdb->get_results($sql);

			foreach($testsettings as $testsetting)
			{
			    $payment_url = $testsetting -> payment_url;
				$alarm_url = $testsetting -> alarm_url;
				$right_url = $testsetting -> right_url;
				$nativepay_url = $testsetting -> nativepay_url;	   
				$appid = $testsetting -> appid;
				$appkey = $testsetting -> appkey;
				$appsecret = $testsetting -> appsecret;
				$mchid = $testsetting -> mch_id;	   
				$contactemergency = $testsetting -> contact_emergency;	   
				$contactnumber = $testsetting -> contact_phone;
				$contactemail = $testsetting -> contact_email;
				$certificate_url = $testsetting -> certificate_url;  //这条字段是需要更新的
				$certificate1_url = $testsetting -> certificate1_url;  //这条字段是需要更新的
				$test_url = $testsetting -> test_url;
				$flag = $testsetting -> flag;
			}
			//现在是测试状态
			if($flagvalue == 0)
			{
			   
				//将路径写进数据库
				$status = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."shopping_global"."(gweid, payment_url, alarm_url, right_url, nativepay_url, appid, appkey, appsecret, mch_id, contact_emergency, contact_phone, contact_email, certificate_url, certificate1_url,test_url,flag)VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d)",$gweid, $payment_url, $alarm_url, $right_url, $nativepay_url, $appid, $appkey, $appsecret, $mchid, $contactemergency, $contactnumber, $contactemail, $certificate_url, $certificate1_url, $testurl, $flagvalue));
			    //并将testurl存入url_patten表中
				$patternstatus = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."url_pattern_mapping"."(GWEID, module, type, value, url_pattern)VALUES (%s, %s, %s, %s, %s)",$gweid, 'wepay','base', 'Test', $testurl));
				//更新url_pattern表中正式支付目录的valid为0
				$upstatus = $wpdb->update( $wpdb->prefix.'url_pattern_mapping', array('valid'=>0),array('gweid'=>$gweid,'module'=>'wepay','type'=>'base', 'value'=>'' ), array('%d'),array('%s','%s','%s','%s'));
				//echo $wpdb -> last_query;
				if($status===false || $patternstatus===false || $upstatus===false){
					$hint = array("status"=>"error","message"=>"出现问题，请稍后重试");
						
				}else{
					$hint = array("status"=>"success","message"=>"可以开始测试");
				}
				echo json_encode($hint);
				exit;	
			}else{            //现在是正常支付状态
			    //将路径写进数据库
				$status = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."shopping_global"."(gweid, payment_url, alarm_url, right_url, nativepay_url, appid, appkey, appsecret, mch_id, contact_emergency, contact_phone, contact_email, certificate_url, certificate1_url,test_url,flag)VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d)",$gweid, $payment_url, $alarm_url, $right_url, $nativepay_url, $appid, $appkey, $appsecret, $mchid, $contactemergency, $contactnumber, $contactemail, $certificate_url, $certificate1_url, $test_url, $flagvalue));
			    //并将testurl存入url_patten表中
				$patternstatus = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."url_pattern_mapping"."(GWEID, module, type, value, url_pattern, valid)VALUES (%s, %s, %s, %s, %s, %d)",$gweid, 'wepay','base', 'Test', $testurl, 0));
				//更新url_pattern表中正式支付目录的valid为1, test目录的为0
				$upstatus=$wpdb->update( $wpdb->prefix.'url_pattern_mapping', array('valid'=>1),array('gweid'=>$gweid,'module'=>'wepay','type'=>'base', 'value'=>'' ), array('%d'),array('%s','%s','%s','%s'));
				//echo $wpdb -> last_query;
				if($status===false || $patternstatus===false || $upstatus===false){
					$hint = array("status"=>"error","message"=>"出现问题，请稍后重试");
						
				}else{
					$hint = array("status"=>"success","message"=>"测试完成，可以发布微支付模块");
				}
				echo json_encode($hint);
				exit;	
			}
		}
		if( isset($_POST['submit_flag']) ){
			
			$flagval = $_POST['flagval'];
			
			//更新数据库之前需要先查出该数据表中相应gweid对应的其他字段值
			$sql = $wpdb -> prepare("SELECT * FROM wp_shopping_global WHERE gweid = %s", $gweid);
			$testsettings = $wpdb->get_results($sql);

			foreach($testsettings as $testsetting)
			{
			    $payment_url = $testsetting -> payment_url;
				$alarm_url = $testsetting -> alarm_url;
				$right_url = $testsetting -> right_url;
				$nativepay_url = $testsetting -> nativepay_url;	   
				$appid = $testsetting -> appid;
				$appkey = $testsetting -> appkey;
				$appsecret = $testsetting -> appsecret;
				$mchid = $testsetting -> mch_id;	   
				$contactemergency = $testsetting -> contact_emergency;	   
				$contactnumber = $testsetting -> contact_phone;
				$contactemail = $testsetting -> contact_email;
				$certificate_url = $testsetting -> certificate_url;  //这条字段是需要更新的
				$certificate1_url = $testsetting -> certificate1_url;  //这条字段是需要更新的
				$test_url = $testsetting -> test_url;
			}
			//将flag标志写进数据库
			$flagstatus = $wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."shopping_global"."(gweid, payment_url, alarm_url, right_url, nativepay_url, appid, appkey, appsecret, mch_id, contact_emergency, contact_phone, contact_email, certificate_url, certificate1_url,test_url,flag)VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d)",$gweid, $payment_url, $alarm_url, $right_url, $nativepay_url, $appid, $appkey, $appsecret, $mchid, $contactemergency, $contactnumber, $contactemail, $certificate_url, $certificate1_url, $test_url, $flagval));
			//echo $wpdb -> last_query;
			if($flagstatus===false){
				$hint = array("status"=>"error","message"=>"出现问题，请重新提交");
					
			}else{
				$hint = array("status"=>"success","message"=>"状态更新成功");
			} 
			echo json_encode($hint);
			exit;	
			
		}
		
		//更新数据库之前需要先查出该数据表中相应gweid对应的其他字段值
		$sql = $wpdb -> prepare("SELECT * FROM wp_shopping_global WHERE gweid = %s", $gweid);
		$testsettings = $wpdb->get_results($sql);
		foreach($testsettings as $testsetting)
		{
		    $testflag = $testsetting -> flag;
		    $testurl = $testsetting -> test_url;
		}
		
	    include $this -> template('paymenttest');
	}

	//手机--微信用户手机端维权列表页面
	public function doMobileRightsLists(){
		require_once 'wp-content/themes/ReeooV3/wesite/common/upload.php';
		$shoppingtitle='我的维权';
		global $_W,$wpdb;
		$gweid = $_GET['gweid'];
		//$this->Perdenied($gweid);
		$goodsgid=$_GET['goodsgid'];
		//$fromuser=oZNvzjpG8qQedvsq1RHds2q0LQIE;
		$fromuser=$_SESSION['oauth_openid']['openid'];
		$weixin = new WeixinPay($gweid);
		if(!empty($_GET['errorcode'])){
			include $this -> template('oauth_error');
			exit;
		}
		if(empty($fromuser)||($_SESSION['oauth_openid']['gweid']!=$gweid)){
			$weixin->isoauth2_base($gweid);
			$fromuser=$_SESSION['oauth_openid']['openid'];
		}
		if( isset($_POST['reason'])&&!empty($_POST['reason'])){
			$out_trade_no = $_GET['out_trade_no'];
			$right_picurl = '';
			$upload = new upphoto($gweid);
			if ( is_array($_FILES['uploadinput']['name'])){
				foreach($_FILES['uploadinput']['name'] as $k => $v){
					$file_url = $upload -> up_photo(array(
					'name' => $_FILES['uploadinput']['name'][$k],
					'type' => $_FILES['uploadinput']['type'][$k],
					'tmp_name' => $_FILES['uploadinput']['tmp_name'][$k],
					'error' => $_FILES['uploadinput']['error'][$k],
					'size' => $_FILES['uploadinput']['size'][$k]
					));
					$right_picurl .= (empty($right_picurl)?'':';').$file_url;
				 }
			}
			$right_feedbackid = intval( (time()-1400000000)/10 ).rand(111,999);
			$reason = $_POST['reason'];
			$right_solution = $_POST['right_solution'];
			$rights_notes = $_POST['rights_notes'];
			$right_create_time = date('Y-m-d H:i:s');
			$wpdb->insert("{$wpdb->prefix}shopping_rights", array('out_trade_no' => $out_trade_no,'feedbackid' => $right_feedbackid, 'reason' => $reason,'solution' => $right_solution,'extinfo' => $rights_notes,'picurl'=>$right_picurl,'create_time' => $right_create_time,'rights_status' => '1'),array('%s','%s','%s','%s','%s','%s','%s','%s'));
			$id=$wpdb->insert_id;
		}
		$sql = $wpdb -> prepare( "SELECT count(*) FROM {$wpdb->prefix}shopping_order u1,{$wpdb->prefix}shopping_rights u2 where u1.out_trade_no = u2.out_trade_no and u1.gweid=%s and u1.openid= %s",$gweid,$fromuser);
		$rightcounts= $wpdb->get_var($sql);
		$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order u1,{$wpdb->prefix}shopping_rights u2 	where u1.out_trade_no = u2.out_trade_no and u1.gweid=%s and u1.openid= %s ORDER BY create_time DESC",$gweid,$fromuser);
		$rightsinfo = $wpdb->get_results($sql,ARRAY_A);
		//删除维权信息
		if( isset($_POST['rightdel']) ){
		    $rightid=$_POST['id'];
			$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_rights WHERE id = %d", $rightid ) );
			if($delete===false){
				$hint = array("status"=>"error","message"=>"删除失败");
			}else{
				$hint = array("status"=>"success","message"=>"删除成功");
			}
			echo json_encode($hint);
			exit;
		
		}
		include $this -> template('rightslists');
	
	}
		
	//手机--维权详情页面
	public function doMobileRightsDetails(){
		global $_W, $_GPC ,$wpdb;
		$shoppingtitle='维权详情';
		$gweid = $_GET['gweid'];
		//$this->Perdenied($gweid);
		$goodsgid=$_GET['goodsgid'];	
		$fromuser=$_SESSION['oauth_openid']['openid'];
		$weixin = new WeixinPay($gweid);
		if(!empty($_GET['errorcode'])){
			include $this -> template('oauth_error');
			exit;
		}
		if(empty($fromuser)||($_SESSION['oauth_openid']['gweid']!=$gweid)){
			$weixin->isoauth2_base($gweid);
			$fromuser=$_SESSION['oauth_openid']['openid'];
		}
		/*if(empty($fromuser)){
			$this->isoauth2($gweid);
			$fromuser=$_SESSION['oauthuser']['openid'];
		}*/
		$out_trade_no = $_GET['out_trade_no'];
		$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order_goods u2,{$wpdb->prefix}shopping_goods u3 where u3.id=u2.goods_id and u2.out_trade_no=%s",$out_trade_no);
		$goodsinfos = $wpdb->get_results($sql,ARRAY_A);
		$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order where out_trade_no=%s and gweid=%s and openid=%s",$out_trade_no,$gweid,$fromuser );
		$rsdetails = $wpdb->get_row($sql,ARRAY_A);
		include $this -> template('rightsdetails');
	}
	//手机--维权状态页面
	public function doMobileRightsOrderStatus(){
			global $_W,$wpdb;
			$shoppingtitle='维权单详情';
			$gweid = $_GET['gweid'];
			//$this->Perdenied($gweid);
			$goodsgid=$_GET['goodsgid'];	
			$fromuser=$_SESSION['oauth_openid']['openid'];
			$weixin = new WeixinPay($gweid);
			if(!empty($_GET['errorcode'])){
				include $this -> template('oauth_error');
				exit;
			}
			if(empty($fromuser)||($_SESSION['oauth_openid']['gweid']!=$gweid)){
				$weixin->isoauth2_base($gweid);
				$fromuser=$_SESSION['oauth_openid']['openid'];
			}
			
			$out_trade_no = $_GET['out_trade_no'];
			$feedbackid = $_GET['feedbackid'];
			if( isset($_POST['msgtype'])&&!Empty($_POST['msgtype']) ){
				$msgtype = $_POST['msgtype'];
				$fid = $_GET['feedbackid'];
				$wpdb->update( "{$wpdb->prefix}shopping_rights", array( 'msgtype' => $msgtype),array('feedbackid' => $fid ), array('%s'),array('%s') );
			}
			$RIGHT_REASON = array(
				'1' =>'商品质量有问题',
				'2' => '商品与实际购买不符',
				'3' => '商品发货延迟',
				'4' => '其他原因');
			$RIGHT_SOLUTION = array(
				'1' =>'退款退货',
				'2' => '退款不退货',
				'3' => '暂不处理');
			$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order u1,{$wpdb->prefix}shopping_rights u2 where u1.out_trade_no = u2.out_trade_no and u1.out_trade_no=%s and u1.gweid=%s and u1.openid=%s and u2.feedbackid= %s" ,$out_trade_no,$gweid,$fromuser,$feedbackid);
			$right = $wpdb->get_row($sql,ARRAY_A);
			$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order_goods u2,{$wpdb->prefix}shopping_goods u3 where u3.id=u2.goods_id and u2.out_trade_no=%s",$out_trade_no);
			$goodsinfos = $wpdb->get_results($sql,ARRAY_A);
			$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order where out_trade_no=%s and gweid=%s and openid=%s",$out_trade_no,$gweid,$fromuser );
			$ordersinfo = $wpdb->get_row($sql,ARRAY_A);
			include $this -> template('rightsorderstatus');
	
	}

	public function doMobileAlarmNotify(){
		global $wpdb;
		$weixin = new WeixinPay($_GET['gweid']);
		$ALARM_TYPE = array(
			'1001' => '发货超时',
			'1010' => '库存更新失败'
		);
 		$alarm = $weixin -> getXmlArray();
		$wpdb -> insert("{$wpdb->prefix}shopping_alarm",array(
			'appid' => $alarm['appid'],
			'gweid' => $_GET['gweid'],
			'description' => $alarm['description'],
			'errortype' => $alarm['errortype'],
			'timestamp' => date('Y-m-d H:i:s',$alarm['timestamp']),
			'alarmcontent' => $alarm['alarmcontent'],
			'read' =>0			
		));
		if($wpdb->insert_id)
			echo 'success';
		
	}
	
	function doMobileNativePayNotify(){
		global $wpdb;
		$weixin = new WeixinPay($_GET['gweid']);
 		$qrNotifyArray = $weixin -> getXmlArray();
		$openid = $qrNotifyArray['openid'];
		$product_id = $qrNotifyArray['product_id']; 
		if(substr($product_id,0,1) == 'p'){
			$product_id = substr($product_id,1);
			$product = $wpdb -> get_row($wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_native_qrcode WHERE product_id=%d",$product_id),ARRAY_A);
			if(empty($product)){
				echo $weixin-> build_native_response(NULL,"商品不存在，请重试。");
				exit;
			}
			$wpdb -> insert($wpdb -> prefix.'shopping_statistics', array(
				'type_id' => $product_id,
				'type' => 'native_qrcode',
				'link' => '',
				'time' => date('Y-m-d H:i:s'),
				'ip' => '',
				'gweid' => $_GET['gweid']
				));
			$out_trade_no = time().rand(111111,999999);
			$user=$weixin->userinfo($openid);
			$wechatname=$user['nickname'];
			$wpdb -> insert("{$wpdb -> prefix}shopping_order",array(
				"out_trade_no" => $out_trade_no,
				"gweid" => $_GET['gweid'],
				"openid" => $openid,
				"openid_name" => $wechatname,
				"payment_type" => 1,
				"fee" => $product['product_price'],
				"time_start" => date('Y-m-d H:i:s'),
				"address" => json_encode(array()),
				"trade_state" => "NOTPAY",
				"description" => "[{$product['product_name']}]{$product['product_description']}"
			));
			$wpdb -> insert("{$wpdb->prefix}shopping_order_wepay",array(
				"out_trade_no" => $out_trade_no,
				"fee_type" => $fee_type,
				"send_type" => $send_type,
				"trade_type" => 'NATIVE_PRODUCT',
				"product_id" => $product['product_id']
				));
			
			$data = array(
				   'body' => "[{$product['product_name']}]{$product['product_description']}",
				   'out_trade_no' => $out_trade_no ,
				   'total_fee' => intval($product['product_price']*100),
				   'notify_url'=>$this->createMobileUrl('PaidNotify',array('gweid' =>$_GET['gweid'])),
				   'spbill_create_ip' => $_SERVER['REMOTE_ADDR']
			 );

			$result = $weixin->create_order($data,false);
			if(!$result){
				echo $weixin-> build_native_response(NULL,"创建订单失败，请扫码重试。");
			}else{
				$prepay_id = $result['prepay_id'];
				echo $weixin-> build_native_response($prepay_id);
			}
		}else
			echo $weixin-> build_native_response(NULL,"商品不存在，请重试。");
	}
	
	function refundStatusUpdate(){
		global $wpdb;
		$gweidList = $wpdb -> get_results("SELECT DISTINCT `{$wpdb->prefix}shopping_order`.`gweid` FROM `{$wpdb->prefix}shopping_refund` LEFT JOIN `{$wpdb->prefix}shopping_order` on `{$wpdb->prefix}shopping_refund`.`out_trade_no`=`{$wpdb->prefix}shopping_order`.`out_trade_no`  WHERE `refund_status`='PROCESSING' ORDER BY `refund_status` ASC ",ARRAY_A);
		foreach($gweidList as $gweid){
			$weixin = new WeixinPay($gweid);
			$orderIdList = $wpdb -> get_col("SELECT DISTINCT `{$wpdb->prefix}shopping_order`.`out_trade_no` FROM `{$wpdb->prefix}shopping_refund` LEFT JOIN `{$wpdb->prefix}shopping_order` on `{$wpdb->prefix}shopping_refund`.`out_trade_no`=`{$wpdb->prefix}shopping_order`.`out_trade_no`  WHERE `refund_status`='PROCESSING' ORDER BY `refund_status` ASC ");
			if(is_array($orderIdList)){
				foreach($orderIdList as $orderId){
					$orderRefund = $weixin -> refund_query($orderId);
					$refundCount = $orderRefund['refund_count'];
					for($i=0;$i<$refundCount;$i++){
						$wpdb -> update("{$wpdb->prefix}shopping_refund",
							array('refund_status' => $orderRefund["refund_status_$i"]),
							array('out_refund_no' => $orderRefund["out_refund_no_$i"])
						);
						echo $orderRefund["refund_status_$i"].$orderRefund["out_refund_no_$i"];
					}
				}
			}
		}
	}
	
	
	function doMobileRefundStatusUpdate(){
		$this -> refundStatusUpdate();
	}
	
	function doMobilebatchRefund(){
		return false;
		var_dump($_SERVER);
		$weixin = new WeixinPay($_GET['gweid']);
		$order_list = explode("\n",str_replace('`','',@file_get_contents('php://input')));
		foreach($order_list as $order){
			$data = array(
				'out_trade_no' => $order,
				'out_refund_no' => rand(111111,9999999),
				'total_fee' => 1,
				'refund_fee' => 1,
			);
			$result = $weixin -> create_refund($data);
			var_dump($result);
		}
	}

	function doMobileWeShoppingJSAPIPayOrder(){
		global $wpdb;
		$out_trade_no = $_GET['orderid'];
		$gweid = $_GET['gweid'];
		$weixin = new WeixinPay($gweid);
		$resultUrl=$this->createMobileUrl('goodspay_result',array('gweid' => $gweid,'out_trade_no' => $out_trade_no, 'return_url' => $this->createModuleMobileUrl('weshopping','myorder',array('gweid' => $gweid))));
		$jsapi_data=$weixin->JSAPI($_GET['prepay_id']);
		$access_token = new Access_token();
		$access_token = $access_token -> get_access_token($weixin -> app_id,"appid",$weixin -> appsecret);
		if(empty($access_token))
			return;
		$jssdk = new JSSDK($weixin -> app_id, $weixin -> appsecret, $access_token);
		$signPackage = $jssdk -> getSignPackage();
		if(empty($signPackage))
			return;
			include $this -> template('weShoppingJSAPIPayOrder');

	}
	function doMobileJSAPIPayOrder(){
		global $wpdb, $_W;
		if(!empty($_POST['order_pay'])){
			
			$out_trade_no = $_POST['orderid'];
			$gweid = $_GET['gweid'];
			//订单信息
			$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$out_trade_no);
			$orders = $wpdb->get_results($sql);
			foreach($orders as $order){
				$totalfee=$order->fee;
			}
			
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_goods where out_trade_no=%s",$out_trade_no);
			$ordergoodsinfos = $wpdb->get_results($sql);
			$description=" ";
			foreach($ordergoodsinfos as $ordergoodsinfo){
				$ordergoodsid=$ordergoodsinfo->goods_id;
				$description=$description.$ordergoodsinfo->goods_title;
				
			}
			
			$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_wepay WHERE  out_trade_no=%s",$out_trade_no);
			$tradetype = $wpdb->get_row($sql);

			//微信支付
			$data = array(
				'body' => $description,//订单描述(不能为空，否则微支付出错)
				//'openid' => "oMjb0sgxNRXi3S92AAsE9OxqIbxA",
				'openid' => $_POST['openid'],
				'out_trade_no' => $out_trade_no,//订单号，需保证该字段对于本商户的唯一性
				'total_fee' =>  (string)($totalfee*100), //支付金额 单位：分
				'notify_url'=>$this->createMobileUrl('paidNotify',array('gweid' =>$gweid)),//支付成功后将通知该地址
				'spbill_create_ip' => $_SERVER['REMOTE_ADDR']
				
			);
			
			if($tradetype->trade_type == 'JSAPI'){
				$weixin = new WeixinPay($gweid);
				$creorder=$weixin->create_order($data, true);
				$data['resultUrl']=$this->createMobileUrl('goodspay_result',array('gweid' => $gweid,'out_trade_no' => $out_trade_no));
				if($creorder!=false){	
					if($creorder['return_code']=="SUCCESS"){
						if($creorder['result_code']=="SUCCESS"){
							$hint = array("status"=>"success","message"=>"提交成功","prepay_id"=>$creorder['prepay_id']);
							
							echo json_encode($hint);
							exit;
						}else{
							$hint = array("status"=>"errordec","message"=>$creorder['err_code_des']);
							echo json_encode($hint);
							exit;
						}
					}else{
						$hint = array("status"=>"error","message"=>$creorder['return_msg']);
						echo json_encode($hint);
						exit;
					}
				}else{
					$hint = array("status"=>"error","message"=>"出现问题，请稍后重试");
					echo json_encode($hint);
					exit;
				}
			}			
		}
	}
	public function createModuleWebUrl($module_name, $do, $querystring = array()){
		$module_site = WeUtility::createModuleSite($module_name);
		$module_site -> inMobile = true;
		$module_site -> module['dir'] = MODULES_DIR.$module_name.'/';
		$module_site -> module['name'] = $module_name;
		return $module_site -> createWebUrl($do, $querystring);
	}

	public function createModuleMobileUrl($module_name, $do, $querystring = array()){
		$module_site = WeUtility::createModuleSite($module_name);
		$module_site -> inMobile = true;
		$module_site -> module['dir'] = MODULES_DIR.$module_name.'/';
		$module_site -> module['name'] = $module_name;
		return $module_site -> createMobileUrl($do, $querystring);
	}

	public function Perdenied($gweid){
		if(!$this->has_module($gweid)){
			include $this -> template('perdenied');
			exit;
		}
	}
	/*是否开启功能权限*/
	public function has_module($gweid){
		global $_W,$wpdb;
		$result = $wpdb -> get_results($wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_func_info a WHERE NOT EXISTS(SELECT * FROM wp_wechat_initfunc_info b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = %s AND func_flag = 0) LIMIT 0, 100",$gweid),ARRAY_A);
		foreach($result as $initfunc){
			if($selCheck[$initfunc['func_name']] == 0)
				$selCheck[$initfunc['func_name']] = $initfunc['status'];
		}
		if($selCheck['wepay']!=1){
			return false;
		}else{
			return true;
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
		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE gweid='{$gweid}' AND groupid >0",ARRAY_A);
		if(is_array($list))
			foreach($list as $element){
				file_unlink($element['thumb']);
			}
				
	}
}