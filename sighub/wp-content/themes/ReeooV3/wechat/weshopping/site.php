<?php

defined('IN_IA') or exit('Access Denied');
require_once ABSPATH.'wp-content/themes/ReeooV3/wechat/wepay/sdk/sdk.php';

class WeshoppingModuleSite extends ModuleSite {
	private $fromuser = NULL;
	private $get_from_info_sql = NULL;
	private $wechatname = NULL;
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
	public $REFUND_REASON = array(
		'SHIPPING_FAILED' =>'发货失败',
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
		'CREATEFAIL' => '退款创建失败');
			
	function __construct() {
		global $_W;
		$upload =wp_upload_dir();
		$_W['attachurl'] = $upload['baseurl'];
			
	}
	
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

		$html = '<ul class="pagination pagination-centered" style="margin-top:1px;margin-bottom:1px;">';
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
		return "<hr style='border-top:2px solid #dddddd;margin-top:3px;'>".$html;
	}
	//微商城
	public function doWebweshoptemselect(){
		global $wpdb;
		$gweid =  $_SESSION['GWEID'];
		$sql = "SELECT * FROM {$wpdb->prefix}shopping_template where activate=1 ";
		$template_list = $wpdb->get_results($sql,ARRAY_A);
		$list = $wpdb -> get_results($wpdb -> prepare("SELECT *,u1.id as uid FROM {$wpdb->prefix}shopping_template u1,{$wpdb->prefix}shopping_goodsindex u2 where u1.id = u2.template and u2.gweid=%s AND u2.isshopping=1 ", $gweid),ARRAY_A);
		include $this->template('weshopping_template_select');
	}
	public function doWebweshoppingsiteset(){
		global $wpdb;
		$gweid =  $_SESSION['GWEID'];
		$slides = intval($_GET['slid']);
		$shoppingcount = $wpdb->get_var($wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}shopping_goodsindex where gweid=%s and isshopping=%d ",$gweid,1));
		$templateSelected = $_POST['templateSelected'];
		$slide= $wpdb->get_var($wpdb -> prepare("SELECT slide FROM {$wpdb->prefix}shopping_template where id=%d ",$templateSelected ));
		if( isset($_POST['shoppingname'])){
			$shoppingname = $_POST['shoppingname'];
			$templateSelected = $_POST['templateSelected'];
			if ($shoppingcount ==0){
			$goodsindexid=time().rand(111,999);
			$shoppingdata = $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_goodsindex(id ,gweid,goodsindex_name,isshopping ,template )VALUES (%s,%s, %s, %d, %d)",$goodsindexid,$gweid, $shoppingname, 1 ,$templateSelected));
			}else{
			$shoppingdata = $wpdb->update( $wpdb->prefix.'shopping_goodsindex', array('goodsindex_name'=>$shoppingname,'template'=>$templateSelected),array('gweid'=>$gweid,'isshopping'=>1),array('%s','%d'),array('%s','%d'));
			}
		}
		include $this->template('weshopping_site_setting');
	}
	//幻灯片
	    public function doWebAdv() {
        include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
        global $_W, $_GPC,$wpdb;
		$gweid =  $_SESSION['GWEID'];
		$slide= $wpdb->get_var($wpdb -> prepare("SELECT slide FROM {$wpdb->prefix}shopping_template u1,{$wpdb->prefix}shopping_goodsindex u2 where u1.id = u2.template and u2.gweid=%s", $gweid));
		$operation = !empty($_GET['op']) ? $_GET['op'] : 'display';
        if ($operation == 'display') {
			$pindex = max(1, intval($_GET['page']));
			$psize = 5;
			$offset=($pindex - 1) * $psize;
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_adv WHERE gweid = %s ORDER BY displayorder DESC limit {$offset},{$psize}", $gweid);
			$list = $wpdb->get_results($sql, ARRAY_A);
			$total= $wpdb->get_var($wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_adv where gweid = %s", $gweid));
			$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
        } elseif ($operation == 'post') {

            $id = intval($_GET['id']);
            if (checksubmit('submit')) {
              $data = array(
                    'gweid' => $gweid,
                    'advname' => $_POST['advname'],
                    'link' => $_POST['link'],
                    'enabled' => intval($_POST['enabled']),
                    'displayorder' => intval($_POST['displayorder'])
                );
				$delimgid = $_POST['delimgid'];//该值为-1，表示删除图片
				if($delimgid!=-1&&$delimgid!=-2){
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
							
							message('图片大小不能超过10M！');
						}
						if ($type != ".gif" && $type != ".jpg"&& $type != ".png" && $type != ".jpeg") {
							message('图片格式不对!');
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
					$data['thumb']=$picUrl;
				}else if($delimgid==-1){
					unset($data['thumb']);
				}else if($delimgid==-2){
					$data['thumb']='';
				}
                if (!empty($id)) {
                	$sql = $wpdb -> prepare("SELECT thumb FROM {$wpdb->prefix}shopping_adv WHERE id = %d and gweid = %s ",$id,$gweid);
		            $thumb = $wpdb->get_var($sql);
		            if(isset($insert['thumb']) && $thumb != $insert['thumb'])
                    	file_unlink($thumb);
                    $wpdb->update("{$wpdb -> prefix}shopping_adv", $data, array('id' => $id));
					 message('更新幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
                } else {
                    $wpdb -> insert("{$wpdb->prefix}shopping_adv",$data);
					$id = $wpdb->insert_id;
					 message('添加幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
                }
            }
			//Reload the page
            $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_adv WHERE id = %d and gweid = %s limit 1",$id,$gweid);
			$adv = $wpdb->get_row($sql,ARRAY_A);
			/*处理图片显示*/
			$upload =wp_upload_dir();
			  if((empty($adv['thumb']))||(stristr($adv['thumb'],"http")!==false)){
					$advthumb=$adv['thumb'];
			 }else{
					$advthumb=$upload['baseurl'].$adv['thumb'];
				}
        } elseif ($operation == 'delete') {
            $id = intval($_GET['id']);
			$sql = $wpdb -> prepare("SELECT id,thumb FROM {$wpdb->prefix}shopping_adv WHERE id = %d and gweid = %s ",$id,$gweid);
            $adv = $wpdb->get_row($sql,ARRAY_A);
            $thumb = $adv['thumb'];
            $adv = $adv['id'];
            if (empty($adv)) {
                message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('adv', array('op' => 'display')), 'error');
            }
            file_unlink($thumb);
            $delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_adv WHERE id =%d ",$id ) );
            message('幻灯片删除成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('adv');
    }
	//小店介绍
	public function doWebShopdescription() {
	    include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
        global $_W, $_GPC,$wpdb;
		$gweid =  $_SESSION['GWEID'];
		$slide= $wpdb->get_var($wpdb -> prepare("SELECT slide FROM {$wpdb->prefix}shopping_template u1,{$wpdb->prefix}shopping_goodsindex u2 where u1.id = u2.template and u2.gweid=%s", $gweid));
		$sql = $wpdb -> prepare("SELECT id  FROM {$wpdb->prefix}shopping_shop WHERE gweid = %s ",$gweid);
		$id =$wpdb->get_var($sql);
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_shop WHERE id = %d and gweid = %s ",$id,$gweid);
		$shop = $wpdb->get_row($sql,ARRAY_A);
			/*处理编辑器中文章图片显示*/
			$shopdescription=$this->rule_content($shop['description']);
			/*处理图片显示*/
			$upload =wp_upload_dir();
			  if((empty($shop['image']))||(stristr($shop['image'],"http")!==false)){
					$shopimg=$shop['image'];
			 }else{
					$shopimg=$upload['baseurl'].$shop['image'];
				}
	     if (checksubmit('submit')) {
			$id = intval($_POST['id']);
			$description=stripslashes($_POST['description']);
			$description =preg_replace('#(?<=src=(\'|"))(('.home_url().'|(?!\w+://))[\w%\/.\-_]+uploads(?=/))|'.$baseurl.'(?=[\w%\/.\-_]+\")#i','',$description);
			/*处理入DB的文章图片END*/
              $data = array(
                    'gweid' => $gweid,
					'email' => $_POST['noticeemail'],
					'name' => $_POST['shopname'],
                    'site' => $_POST['site'],
					'phone' => $_POST['phone'],
					'address' => $_POST['address'],
					'description' => $description,
                );
				$delimgid = $_POST['delimgid'];//该值为-1，表示删除图片
				if($delimgid!=-1&&$delimgid!=-2){
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
							message('图片大小不能超过10M！');
						}
						if ($type != ".gif" && $type != ".jpg"&& $type != ".png" && $type != ".jpeg") {
							message('图片格式不对!');	
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
					$data['image']=$picUrl;
				}else if($delimgid==-1){
					unset($data['image']);
				}else if($delimgid==-2){
					$data['image']='';
				}
                if (!empty($id)) {
                	if(isset($data['image']) && $shop['image'] != $data['image'])
                		file_unlink($shop['image']);
                	file_unlink_from_xml_update( $shop['description'] , $data['description'] );
                    $wpdb->update("{$wpdb -> prefix}shopping_shop", $data, array('id' => $id));
					message('更新小店信息成功！', $this->createWebUrl('Shopdescription'), 'success');
                } else {
                    $wpdb -> insert("{$wpdb->prefix}shopping_shop",$data);
					$id = $wpdb->insert_id;
					message('添加小店信息成功！', $this->createWebUrl('Shopdescription'), 'success');
                }
            }
			include $this->template('shopdescription');
	
	}
	//分类管理
	public function doWebCategory() {
		include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
		global $_W, $_GPC ,$wpdb;
		$gweid =  $_SESSION['GWEID'];
		
		$operation = !empty($_GET['op']) ? $_GET['op'] : 'display';
        if ($operation == 'display') {
			
			//for page
			$pindex = max(1, intval($_GET['page']));
			$psize = 5;
			$offset=($pindex - 1) * $psize;
			//for page end
			
            if (!empty($_POST['displayorder'])) {
                foreach ($_POST['displayorder'] as $id => $displayorder) {
                    $wpdb->update("{$wpdb -> prefix}shopping_category", array('displayorder' => $displayorder), array('id' => $id));
				}
                message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
            }
            $children = array();
			
			//$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_category where gweid=%s ORDER BY parentid ASC, displayorder DESC limit {$offset},{$psize}",$gweid);
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_category where gweid=%s ORDER BY parentid ASC, displayorder DESC",$gweid);
			$category = $wpdb->get_results($sql,ARRAY_A);
			
			foreach ($category as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($category[$index]);
                }
            }
			
			//for page
			$total= $wpdb->get_var($wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_category where gweid=%s",$gweid));
			$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
			//for page end
			
			include $this->template('category');
        } elseif ($operation == 'post') {
            $parentid = intval($_GET['parentid']);
            $id = intval($_GET['id']);
            if (!empty($id)) {
                $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_category WHERE  id=%d",$id);
				$category = $wpdb->get_row($sql,ARRAY_A);
				
				/*处理图片显示*/
				$upload =wp_upload_dir();
				if((empty($category['thumb']))||(stristr($category['thumb'],"http")!==false)){
					$cathumb=$category['thumb'];
				}else{
					$cathumb=$upload['baseurl'].$category['thumb'];
				}
			} else {
                $category = array(
                    'displayorder' => 0,
                );
            }
            if (!empty($parentid)) {
				$sql = $wpdb -> prepare("SELECT id, name  FROM {$wpdb->prefix}shopping_category WHERE  id=%d",$parentid);
				$parent = $wpdb->get_row($sql,ARRAY_A);
                if (empty($parent)) {
                    message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_POST['catename'])) {
                    message('抱歉，请输入分类名称！');
                }
                $data = array(
                    'gweid' => $gweid,
                    'name' => $_POST['catename'],
                    'enabled' => intval($_POST['enabled']),
					'indexenabled' => intval($_POST['indexenabled']),
                    'displayorder' => intval($_POST['displayorder']),
                    'isrecommend' => intval($_POST['isrecommend']),
					'isrecommendorder' => intval($_POST['isrecommendorder']),
                    'description' => $_POST['description'],
                    'parentid' => intval($parentid),
                );
                /*if (!empty($_FILES['thumb']['tmp_name'])) {
                    file_delete($_POST['thumb_old']);
                    $upload = file_upload($_FILES['thumb']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['thumb'] = $upload['path'];
                }*/
				$delimgid = $_POST['delimgid'];//该值为-1，表示删除图片
				
				if($delimgid!=-1&&$delimgid!=-2){
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
							message('图片大小不能超过10M！');
						}
						if ($type != ".gif" && $type != ".jpg"&& $type != ".png" && $type != ".jpeg") {
							message('图片格式不对!');
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
					$data['thumb']=$picUrl;
				}else if($delimgid==-1){
					unset($data['thumb']);
				}else if($delimgid==-2){
					$data['thumb']='';
				}
				
				if (!empty($id)) {
                    unset($data['parentid']);
					$catemessage="分类更新成功！";
					$catemessageerror="分类更新失败！";
					if(isset($category['thumb']) && $category['thumb'] != $data['thumb'])
						file_unlink($category['thumb']);
                    $isupdate=$wpdb->update("{$wpdb -> prefix}shopping_category", $data, array('id' => $id));
				} else {
					$catemessage="分类添加成功！";
					$catemessageerror="分类添加失败！";
					$isupdate= $wpdb -> insert("{$wpdb->prefix}shopping_category",$data);
					$id = $wpdb->insert_id;
                }
				if($isupdate===false){
					message($catemessageerror, $this->createWebUrl('category', array('op' => 'display')), 'error');
				}else{
					message($catemessage, $this->createWebUrl('category', array('op' => 'display')), 'success');				
				}
				// message($catemessage, $this->createWebUrl('category', array('op' => 'post', 'id' => $id, 'parentid' => $parentid)), 'success');
				
            }
            include $this->template('category');
        } elseif ($operation == 'delete') {
            $id = intval($_GET['id']);
            $sql = $wpdb -> prepare("SELECT id, parentid,thumb FROM {$wpdb->prefix}shopping_category WHERE  id=%d",$id);
			$category = $wpdb->get_row($sql,ARRAY_A);

			if (empty($category)) {
                message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
            }
           // pdo_delete('shopping_category', array('id' => $id, 'parentid' => $id), 'OR');
			file_unlink($category['thumb']);
			$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_category WHERE id =%d or parentid=%d",$id,$id ) );
			if($delete===false){
				message('分类删除失败！', $this->createWebUrl('category', array('op' => 'display')), 'error');
			}else{
				message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
			}
        }
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
	public function doWebParam() {
        $tag = $this->random(32);
        global $_GPC;
        include $this->template('param');
    }
	
	//管理商品首页对商品属性的修改
	public function doWebSetGoodsProperty() {
		global $_W, $wpdb;
		$gweid =  $_SESSION['GWEID'];
		
        $id = intval($_POST['id']);
        $type = $_POST['type'];
        $data = intval($_POST['data']);
       
        if (in_array($type, array('new', 'hot', 'recommend', 'discount'))) {
            
			$data = ($data==1?'0':'1');
            $status=$wpdb->update("{$wpdb -> prefix}shopping_goods", array("is" . $type => $data), array('id' => $id,'gweid' => $gweid));
			die(json_encode(array("result" => 1, "data" => $data)));
        }
        if (in_array($type, array('status'))) {
            if($data==1){
				$data=0;
			}else if($data==0){
				$data=2;
			}else{
				$data=1;
			}
			$wpdb->update("{$wpdb -> prefix}shopping_goods", array($type => $data), array('id' => $id,'gweid' => $gweid));
			die(json_encode(array("result" => 1, "data" => $data)));
        }
         if (in_array($type, array('type'))) {
            $data = ($data==1?'2':'1');
			$wpdb->update("{$wpdb -> prefix}shopping_goods", array($type => $data), array('id' => $id,'gweid' => $gweid));
			die(json_encode(array("result" => 1, "data" => $data)));
        }
        die(json_encode(array("result" => 0)));
        
    }

    public function doWebGoods() {
        include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
		global $_W, $_GPC ,$wpdb;
		$gweid =  $_SESSION['GWEID'];
		//$category = pdo_fetchall("SELECT * FROM " . tablename('shopping_category') . " WHERE gweid = {$gweid} ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_category where gweid=%s ORDER BY parentid ASC, displayorder DESC",$gweid);
		$category = $wpdb->get_results($sql,ARRAY_A);
		
		/*new add 将返回的数据结果将以id的值为键名呈现为关联数组*/
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
		
		
		if (!empty($category)) {
            $children = '';
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }

        $operation = !empty($_GET['op']) ? $_GET['op'] : 'display';
        
        if ($operation == 'post') {//添加或更新商品


            $id = intval($_GET['id']);
            if (!empty($id)) {
				$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_goods WHERE  id=%s",$id);
				$item = $wpdb->get_row($sql,ARRAY_A);
				
				/*处理商品简介图片显示*/
				$content=$this->rule_content($item['content']);
				
				/*处理商品图片显示*/
				$upload =wp_upload_dir();
				if((empty($item['thumb']))||(stristr($item['thumb'],"http")!==false)){
					$goodsthumb=$item['thumb'];
				}else{
					$goodsthumb=$upload['baseurl'].$item['thumb'];
				}
				
				/*处理显示时间*/
				$timestart = !empty($item['timestart']) ? strtotime($item['timestart']) : strtotime(date("Y-m-d H:i", TIMESTAMP));
				$timeend = !empty($item['timeend']) ? strtotime($item['timeend']) : strtotime(date("Y-m-d H:i", TIMESTAMP));
				
				
				if (empty($item)) {
                    message('抱歉，商品不存在或是已经删除！', '', 'error');
                }
				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_spec where goodsid=%s order by displayorder asc",$id);
				$allspecs = $wpdb->get_results($sql,ARRAY_A);
		
				foreach ($allspecs as &$s) {
					$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_spec_item where specid=%s order by displayorder asc",$s['id']);
					$s['items'] =$wpdb->get_results($sql,ARRAY_A);
				}
                unset($s);

				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods_param where goodsid=%s order by displayorder asc",$id);
				$params =$wpdb->get_results($sql,ARRAY_A);
				
				$piclist1 = unserialize($item['thumb_url']);
				$piclist = array();
				if(is_array($piclist1)){
					foreach($piclist1 as $p){
						$piclist[]  = is_array($p)?$p['attachment']:$p;
					}
				}
				
             
                //处理规格项
                $html = "";
				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods_option where goodsid=%s order by id asc",$id);
				$options =$wpdb->get_results($sql,ARRAY_A);
				
                //排序好的specs
                $specs = array();
                //找出数据库存储的排列顺序
                if (count($options) > 0) {
                    $specitemids = explode("_", $options[0]['specs'] );
                    foreach($specitemids as $itemid){
                        foreach($allspecs as $ss){
                             $items=  $ss['items'];
                             foreach($items as $it){
                                 if($it['id']==$itemid){
                                     $specs[] = $ss;
                                     break;
                                 }
                             }
                        }
                    }
                    
                    $html = '<table class="table table-bordered table-condensed" style="min-width:870px;"><thead><tr class="active">';

                    $len = count($specs);
                    $newlen = 1; //多少种组合
                    $h = array(); //显示表格二维数组
                    $rowspans = array(); //每个列的rowspan


                    for ($i = 0; $i < $len; $i++) {
                        //表头
                        $html.="<th style='width:80px;'>" . $specs[$i]['title'] . "</th>";

                        //计算多种组合
                        $itemlen = count($specs[$i]['items']);
                        if ($itemlen <= 0) {
                            $itemlen = 1;
                        }
                        $newlen*=$itemlen;

                        //初始化 二维数组
                        $h = array();
                        for ($j = 0; $j < $newlen; $j++) {
                            $h[$i][$j] = array();
                        }
                        //计算rowspan
                        $l = count($specs[$i]['items']);
                        $rowspans[$i] = 1;
                        for ($j = $i + 1; $j < $len; $j++) {
                            $rowspans[$i]*= count($specs[$j]['items']);
                        }
                    }

                    $html .= '<th class="info" style="width:130px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">库存</div><div class="input-group"><input type="text" class="form-control option_stock_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
					$html.= '<th class="success" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;"><span style="color:red">*</span>销售价格</div><div class="input-group"><input type="text" class="form-control option_marketprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
					$html.='<th class="warning" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">市场价格</div><div class="input-group"><input type="text" class="form-control option_productprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
					$html.='<th class="danger" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">成本价格</div><div class="input-group"><input type="text" class="form-control option_costprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
					$html.='<th class="info" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">使用积分</div><div class="input-group"><input type="text" class="form-control option_point_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_point\');"></a></span></div></div></th>';
					$html.='<th class="success" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">重量（克）</div><div class="input-group"><input type="text" class="form-control option_weight_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
					$html.='</tr></thead>';
                    for($m=0;$m<$len;$m++){
                        $k = 0;$kid = 0;$n=0;
                             for($j=0;$j<$newlen;$j++){
                                   $rowspan = $rowspans[$m];
                                   if( $j % $rowspan==0){
                                        $h[$m][$j]=array("html"=> "<td rowspan='".$rowspan."'>".$specs[$m]['items'][$kid]['title']."</td>","id"=>$specs[$m]['items'][$kid]['id']);
                                   }
                                   else{
                                       $h[$m][$j]=array("html"=> "","id"=>$specs[$m]['items'][$kid]['id']);
                                   }
                                   $n++;
                                   if($n==$rowspan){
                                     $kid++; if($kid>count($specs[$m]['items'])-1) { $kid=0; }
                                      $n=0;
                                   }
                        }
                     }
         
                    $hh = "";
                    for ($i = 0; $i < $newlen; $i++) {
                        $hh.="<tr>";
                        $ids = array();
                        for ($j = 0; $j < $len; $j++) {
                            $hh.=$h[$j][$i]['html'];
                            $ids[] = $h[$j][$i]['id'];
                        }
                        $ids = implode("_", $ids);

                        $val = array("id" => "","title"=>"", "stock" => "", "costprice" => "", "point" => "","productprice" => "", "marketprice" => "", "weight" => "");
                        foreach ($options as $o) {
                            if ($ids === $o['specs']) {
                                $val = array("id" => $o['id'],
                                    "title"=>$o['title'],
                                    "stock" => $o['stock'],
                                    "costprice" => $o['costprice'],
									"point" => $o['point'],
                                    "productprice" => $o['productprice'],
                                    "marketprice" => $o['marketprice'],
                                    "weight" => $o['weight']);
                                break;
                            }
                        }

                        $hh .= '<td class="info">';
                        $hh .= '<input name="option_stock_' . $ids . '[]"  type="text" class="form-control option_stock option_stock_' . $ids . '" value="' . $val['stock'] . '"/></td>';
                        $hh .= '<input name="option_id_' . $ids . '[]"  type="hidden" class="form-control option_id option_id_' . $ids . '" value="' . $val['id'] . '"/>';
                        $hh .= '<input name="option_ids[]"  type="hidden" class="form-control option_ids option_ids_' . $ids . '" value="' . $ids . '"/>';
                        $hh .= '<input name="option_title_' . $ids . '[]"  type="hidden" class="form-control option_title option_title_' . $ids . '" value="' . $val['title'] . '"/>';
                        $hh .= '</td>';
                        $hh .= '<td class="success"><input name="option_marketprice_' . $ids . '[]" type="text" class="form-control option_marketprice option_marketprice_' . $ids . '" value="' . $val['marketprice'] . '"/></td>';
                        $hh .= '<td class="warning"><input name="option_productprice_' . $ids . '[]" type="text" class="form-control option_productprice option_productprice_' . $ids . '" " value="' . $val['productprice'] . '"/></td>';
                        $hh .= '<td class="danger"><input name="option_costprice_' . $ids . '[]" type="text" class="form-control option_costprice option_costprice_' . $ids . '" " value="' . $val['costprice'] . '"/></td>';
						$hh .= '<td class="info"><input name="option_point_' . $ids . '[]" type="text" class="form-control option_point option_point_' . $ids . '" " value="' . $val['point'] . '"/></td>';
                        $hh .= '<td class="success"><input name="option_weight_' . $ids . '[]" type="text" class="form-control option_weight option_weight_' . $ids . '" " value="' . $val['weight'] . '"/></td>';
                        $hh .="</tr>";
                    }
                    $html.=$hh;
                    $html.="</table>";
                }
            }
            if (empty($category)) {
                message('抱歉，请您先添加商品分类！', $this->createWebUrl('category', array('op' => 'post')), 'error');
            }
            if (checksubmit('submit')) {
            	$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_goods WHERE  id=%s",$id);
				$item = $wpdb->get_row($sql,ARRAY_A);
                if (empty($_POST['goodsname'])) {
                    message('请输入商品名称！');
                }
                if (empty($_POST['pcate'])) {
                    message('请选择商品分类！');
                }
				if($_POST['ismanual']==0){
					if($_POST['marketprice']>WEPAY_MAX_TOTAL_FEE){
						$pricemessage="销售金额超出范围，请重新输入".WEPAY_MAX_TOTAL_FEE."以内金额";
						message($pricemessage);
					}
				}
				
				/*处理入DB的文章图片*/
				$upload =wp_upload_dir();
				$baseurl=$upload['baseurl'];
				$content=stripslashes($_POST['content']);
				$content =preg_replace('#(?<=src=(\'|"))(('.home_url().'|(?!\w+://))[\w%\/.\-_]+uploads(?=/))|'.$baseurl.'(?=[\w%\/.\-_]+\")#i','',$content);
				/*处理入DB的文章图片END*/
	
				/*处理商品图*/
				$delimgid = $_POST['delimgid'];//该值为-1，表示删除图片
				
				if($delimgid!=-1&&$delimgid!=-2){
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
							message('图片大小不能超过10M!');
						}
						if ($type != ".gif" && $type != ".jpg"&& $type != ".png" && $type != ".jpeg") {
							message('图片格式不对!');
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
					$data['thumb']=$picUrl;
				}else if($delimgid==-2){
					$data['thumb']='';
				}
				
				
				/*处理商品图END*/
	
	
				$data = array(
					
                    'gweid' => $gweid,
                    'displayorder' => intval($_POST['displayorder']),
                    'title' => $_POST['goodsname'],
                    'pcate' => intval($_POST['pcate']),
                    'ccate' => intval($_POST['ccate']),
                    'thumb'=>$data['thumb'],
                    'type' => intval($_POST['type']),
					'ismanual'=>intval($_POST['ismanual']),//1买家输入，0商家输入金额
					'isdelivery'=>intval($_POST['isdelivery']),//0发货 1不发货
					'isfreedelivery'=>$_POST['isfreedelivery'],//0不包邮 1包邮
					'freedeliverycount'=>intval($_POST['freedeliverycount']),//n件包邮
					'isrecommend' => intval($_POST['isrecommend']),
                    'description' => $_POST['description'],
                    'content' => $content,
                    'goodssn' => $_POST['goodssn'],
                    'unit' => $_POST['unit'],
                    //'createtime' => time(),
                    'total' => intval($_POST['total']),
                    'totalcnf' => intval($_POST['totalcnf']),
                    'market_price' => floatval($_POST['marketprice']),
                    'weight' => floatval($_POST['weight']),
                    'cost_price' => floatval($_POST['costprice']),
                    'product_price' => floatval($_POST['productprice']),
                    'productsn' => $_POST['productsn'],
					'point' => intval($_POST['point']),
                    'maxbuy' => intval($_POST['maxbuy']),
                    'hasoption' => intval($_POST['hasoption']),
                    'sales' => intval($_POST['sales']),
                    'status' => intval($_POST['status']),
                    'timestart' => $_POST['timestart'],
					'timeend' => $_POST['timeend'],
					'isshopping' => 1,
                );
                
				if($delimgid==-1){
					unset($data['thumb']);
				}
				
				/*处理批量上传图片*/
				$upload =wp_upload_dir();
				
				if(is_array($_POST['thumbs'])){
					$thumbsarray=$_POST['thumbs'];
					foreach ($thumbsarray as $key => $value){
						$tmp = stristr($value,$upload['baseurl']);
						if($tmp===false){
							$insertPicUrl=$value;
						}else{
							$str = stristr($value, $upload['baseurl']);
							$postion=intval($str)+intval(strlen($upload['baseurl']));
							$insertPicUrl=substr($value, $postion);		
						}
						$thumbsarray[$key]=$insertPicUrl;
					}
					
					$data['thumb_url'] = serialize($thumbsarray);
                }else{
					$data['thumb_url'] = "";
				}	
               


                if (empty($id)) {
                    $data['id']=time().rand(11,99);
					//$id = $wpdb->insert_id;
					$id = $data['id'];
					$hintmessage="商品添加成功！";
					$wpdb -> insert("{$wpdb->prefix}shopping_goods",$data);
					//echo $wpdb -> last_query;
					//exit;
					
                } else {
					$hintmessage="商品更新成功！";
                    unset($data['createtime']);
                    if($item['thumb'] != $data['thumb']&&isset($data['thumb']))
                    	file_unlink($item['thumb']);
                    file_unlink_from_xml_update($item['content'] , $data['content']);
                    if(is_array($item['thumb_url'])){
						foreach(array_diff(unserialize($item['thumb_url']), unserialize($data['thumb_url'])) as $pic)
                    	file_unlink($pic);
					}
                    $wpdb->update("{$wpdb -> prefix}shopping_goods", $data, array('id' => $id));
                }


                $totalstocks = 0;

                //处理自定义参数    

                $param_ids = $_POST['param_id'];
                $param_titles = $_POST['param_title'];
                $param_values = $_POST['param_value'];
                $param_displayorders = $_POST['param_displayorder'];
                $len = count($param_ids);
                $paramids = array();
				
				for ($k = 0; $k < $len; $k++) {
                    $param_id = "";
                    $get_param_id = $param_ids[$k];
                    $a = array(
                        "title" => $param_titles[$k],
                        "value" => $param_values[$k],
                        "displayorder" => $k,
                        "goodsid" => $id,
                    );
                    if (!is_numeric($get_param_id)) {
                        $wpdb -> insert("{$wpdb->prefix}shopping_goods_param",$a);
						$param_id = $wpdb->insert_id;
                    } else {
                        $wpdb->update("{$wpdb -> prefix}shopping_goods_param", $a, array('id' => $get_param_id));
                        $param_id = $get_param_id;
                    }
                    $paramids[] = $param_id;
                }
                if (count($paramids) > 0) {
                    $deleteidarray=implode(',', $paramids);
					$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_goods_param WHERE goodsid=%s and id not IN ({$deleteidarray})",$id) );
				}
                else{
                    $delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_goods_param WHERE goodsid =%s",$id ) );
                }

                //处理商品规格
                //new add
			if($_POST['hasoption']==1){//add if hasoption check
				
				$files = $_FILES;
                $spec_ids = $_POST['spec_id'];
                $spec_titles = $_POST['spec_title'];

                $specids = array();
                $len = count($spec_ids);
                $specids = array();
                $spec_items = array();
                for ($k = 0; $k < $len; $k++) {
                    $spec_id = "";
                    $get_spec_id = $spec_ids[$k];
                    $a = array(
                        "gweid" => $gweid,
                        "goodsid" => $id,
                        "displayorder" => $k,
                        "title" => $spec_titles[$get_spec_id]
                    );
                    if (is_numeric($get_spec_id)) {

                        $wpdb->update("{$wpdb -> prefix}shopping_spec", $a, array('id' => $get_spec_id));
                        $spec_id = $get_spec_id;
                    } else {
                        $status=$wpdb -> insert("{$wpdb->prefix}shopping_spec",$a);
						$spec_id = $wpdb->insert_id;
                    }
					
					
                    //子项
                    $spec_item_ids = $_POST["spec_item_id_".$get_spec_id];
                    $spec_item_titles = $_POST["spec_item_title_".$get_spec_id];
                    $spec_item_shows = $_POST["spec_item_show_".$get_spec_id];
                    $spec_item_thumbs = $_POST["spec_item_thumb_".$get_spec_id];
                    $spec_item_oldthumbs = $_POST["spec_item_oldthumb_".$get_spec_id];
					
					
					
                    $itemlen = count($spec_item_ids);
                    $itemids = array();
                    
					$upload =wp_upload_dir();
                    for ($n = 0; $n < $itemlen; $n++) {
                    
						/*处理入库的图片*/
						$specitthumb=$spec_item_thumbs[$n];
						
						$tmp = stristr($specitthumb,$upload['baseurl']);
						if($tmp===false){
							$insertthumb=$specitthumb;
							
							//new add
							$tmpthumb = stristr($insertthumb,"wp-content/uploads");
							if($tmpthumb!==false){
								$postion=intval(strlen("wp-content/uploads"));
								$insertthumb=substr($tmpthumb, $postion);
							}
							//new add end

						}else{
							$str = stristr($specitthumb, $upload['baseurl']);
							$postion=intval($str)+intval(strlen($upload['baseurl']));
							$insertthumb=substr($specitthumb, $postion);
						}
						
						
						$item_id = "";
                        $get_item_id = $spec_item_ids[$n];
                        $d = array(
                            "gweid" => $gweid,
                            "specid" => $spec_id,
                            "displayorder" => $n,
                            "title" => $spec_item_titles[$n],
                            "show" => $spec_item_shows[$n],
                            "thumb"=>$insertthumb
                        );
                        $f = "spec_item_thumb_" . $get_item_id;
                        
                        if (is_numeric($get_item_id)) {
                            $wpdb->update("{$wpdb -> prefix}shopping_spec_item", $d, array('id' => $get_item_id));
                            $item_id = $get_item_id;
                        } else {
                            $status=$wpdb -> insert("{$wpdb->prefix}shopping_spec_item",$d);
							$item_id = $wpdb->insert_id;
                        }
                        $itemids[] = $item_id;

                        //临时记录，用于保存规格项
                        $d['get_id'] = $get_item_id;
                        $d['id']= $item_id;
                        $spec_items[] = $d;
                    }
                    //删除其他的
                    if(count($itemids)>0){
                        $deleteidarray=implode(",", $itemids);
						$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_spec_item WHERE gweid=%s and specid=%s and id not in ({$deleteidarray})",$gweid,$spec_id) );
					}
                    else{
                        $delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_spec_item WHERE gweid=%s and specid=%s",$gweid,$spec_id ) );
					}
                    
                    //更新规格项id
                    $wpdb->update("{$wpdb -> prefix}shopping_spec", array("content" => serialize($itemids)), array('id' => $spec_id));
                    $specids[] = $spec_id;
                }

                //删除其他的
                if( count($specids)>0){
                	$deleteidarray=implode(",", $specids);
					$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_spec WHERE gweid=%s and goodsid=%s and id not in ({$deleteidarray})",$gweid,$id) );
				}
                else{
                    $delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_spec WHERE gweid=%s and goodsid=%s",$gweid,$id ) );
				}


                //保存规格
           
                $option_idss = $_POST['option_ids'];
                $option_productprices = $_POST['option_productprice'];
                $option_marketprices = $_POST['option_marketprice'];
                $option_costprices = $_POST['option_costprice'];
				$option_points = $_POST['option_point'];
                $option_stocks = $_POST['option_stock'];
                $option_weights = $_POST['option_weight'];
                $len = count($option_idss);
                $optionids = array();
                for ($k = 0; $k < $len; $k++) {
                    $option_id = "";
					//fix weqing bug  两者调换顺序
					$ids = $option_idss[$k];
                    $get_option_id = $_POST['option_id_' . $ids][0];
					
					$idsarr = explode("_",$ids);
                    $newids = array();
                    foreach($idsarr as $key=>$ida){
                        foreach($spec_items as $it){
                            if($it['get_id']==$ida){
                                $newids[] = $it['id'];
                                break;
                            }
                        }
                    }
                    $newids = implode("_",$newids);
                     
					 
                    $a = array(
                        "title" => $_POST['option_title_' . $ids][0],
                        "productprice" => floatval($_POST['option_productprice_' . $ids][0]),
                        "costprice" => floatval($_POST['option_costprice_' . $ids][0]),
						"point" => intval($_POST['option_point_' . $ids][0]),
                        "marketprice" => floatval($_POST['option_marketprice_' . $ids][0]>WEPAY_MAX_TOTAL_FEE?0:$_POST['option_marketprice_' . $ids][0]),//new update
                        "stock" => intval($_POST['option_stock_' . $ids][0]),
                        "weight" => floatval($_POST['option_weight_' . $ids][0]),
                        "goodsid" => $id,
                        "specs" => $newids
                    );
                   
                    $totalstocks+=$a['stock'];

                    if (empty($get_option_id)) {
                        $wpdb -> insert("{$wpdb->prefix}shopping_goods_option",$a);
						$option_id = $wpdb->insert_id;
					} else {
                        $wpdb->update("{$wpdb -> prefix}shopping_goods_option", $a, array('id' => $get_option_id));
                        $option_id = $get_option_id;
					}
                    $optionids[] = $option_id;
                }
				if (count($optionids) > 0) {
					$deleteidarray=implode(',', $optionids);
					$delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_goods_option WHERE goodsid=%s and id not in ({$deleteidarray})",$id) );
				}
                else{
                   $delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_goods_option WHERE goodsid=%s",$id ) );
				}
                

                //总库存
                if ($totalstocks > 0) {
                    $wpdb->update("{$wpdb -> prefix}shopping_goods", array("total" => $totalstocks), array("id" => $id));
                }
				
				//new add
				$option_idss = $_POST['option_ids'];
				$option_marketprices = $_POST['option_marketprice'];
				$len = count($option_idss);
				for ($k = 0; $k < $len; $k++) {
					$ids = $option_idss[$k];
					if($_POST['option_marketprice_' . $ids][0]>WEPAY_MAX_TOTAL_FEE){
						$pricemessage="销售金额超出范围，请重新输入".WEPAY_MAX_TOTAL_FEE."以内金额";
						message($pricemessage);
					}
				}
				//new add end
			
			}    
				message($hintmessage, $this->createWebUrl('goods', array('op' => 'display')), 'success');
				//message($hintmessage, $this->createWebUrl('goods', array('op' => 'post', 'id' => $id)), 'success');
            }
        } elseif ($operation == 'display') {
            $pindex = max(1, intval($_GET['page']));
            $psize = 5;
            $condition = '';
			if (!empty($_GET['keyword'])) {
                $condition .= " AND title LIKE '%%{$_GET['keyword']}%%'";
            }

            if (!empty($_GET['cate_2'])) {
                $cid = intval($_GET['cate_2']);
                $condition .= " AND ccate = '{$cid}'";
            } elseif (!empty($_GET['cate_1'])) {
                $cid = intval($_GET['cate_1']);
                $condition .= " AND pcate = '{$cid}'";
            }

            if (isset($_GET['status']) && $_GET['status']!=-1) {
                $condition .= " AND status = '" . intval($_GET['status']) . "'";
            }
			$offset=($pindex - 1) * $psize;
            $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE gweid = %s and isshopping='1' and deleted=0 {$condition} ORDER BY createtime DESC ,status DESC, displayorder DESC, id DESC limit {$offset},{$psize}",$gweid);
			$list = $wpdb->get_results($sql,ARRAY_A);
			
			$total= $wpdb->get_var($wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_goods where gweid =%s and isshopping='1' and deleted=0 {$condition}",$gweid));


		   $pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
        } elseif ($operation == 'delete') {
            $id = intval($_GET['id']);
			$sql = $wpdb -> prepare("SELECT id, thumb FROM {$wpdb->prefix}shopping_goods WHERE  id=%s",$id);
			$row = $wpdb->get_row($sql,ARRAY_A);
			
			if (empty($row)) {
                message('抱歉，商品不存在或是已经被删除！');
            }
            //修改成不直接删除，而设置deleted=1
            $wpdb->update("{$wpdb -> prefix}shopping_goods", array("deleted" => 1), array('id' => $id));
			message('删除成功！', '', 'success');
 
        } elseif ($operation == 'productdelete') {
            $id = intval($_GET['id']);
            $delete= $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_product WHERE id =%d",$id ) );
            message('删除成功！', '', 'success');
        }
        include $this->template('goods');
    }
	
	
	
	public function doWebOption() {
        $tag = $this->random(32);
        global $_GPC;
        include $this->template('option');
    }

    public function doWebSpec() {

        global $_GPC;
        $spec = array(
            "id" => $this->random(32),
            "title" => $_POST['title']
        );
        include $this->template('spec');
    }

    public function doWebSpecItem() {
        
        global $_GPC;
        $spec = array(
            "id" => $_GET['specid']
        );
        $specitem = array(
            "id" => $this->random(32),
            "title" => $_POST['title'],
            "show" => 1
        );
        include $this->template('spec_item');
    }

	function tpl_form_field_date($name, $value = '', $withtime = false) {
		$s = '';
		if (!defined('TPL_INIT_DATA')) {
			
			$format = '';
			if ($withtime) {
				$format = 'format : "yyyy-mm-dd hh:ii",
								minView : 0,';
			}
			
			$s = '
	<script type="text/javascript">
		require(["datetimepicker"], function($){
			$(function(){
				$(".datetimepicker").each(function(){
					var withtime = $(this).attr("data-withtime");
					var opt = {
						language: "zh-CN",
						format: "yyyy-mm-dd",
						minView: 2,
						autoclose: true,
						'.$format.'
					};
					$(this).datetimepicker(opt);
				});
			});
		});
	</script>';
			define('TPL_INIT_DATA', true);
		}
		$withtime = empty($withtime) ? 'false' : 'true';
		$value = !empty($value) ? $value : ($withtime ? date('Y-m-d H:i') : date('Y-m-d')); 
		$s .= '<input type="text" name="' . $name . '" value="'.$value.'" data-withtime="'.$withtime.'" placeholder="请选择日期时间"  readonly="readonly" class="datetimepicker form-control" />';
		return $s;
	}
	
	

	function array_elements($keys, $src, $default = FALSE) {
		$return = array();
		if(!is_array($keys)) {
			$keys = array($keys);
		}
		foreach($keys as $key) {
			if(isset($src[$key])) {
				$return[$key] = $src[$key];
			} else {
				$return[$key] = $default;
			}
		}
		return $return;
	}
	
	//Express management
    public function doWebExpress() {
	    global $_W, $_GPC ,$wpdb;
	    $gweid = $_SESSION['GWEID'];	
        $operation = !empty($_GET['op']) ? $_GET['op'] : 'display';
        if ($operation == 'display') {
			$pindex = max(1, intval($_GET['page']));
			$psize = 5;
			$offset=($pindex - 1) * $psize;	
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_express WHERE gweid = %s ORDER BY id ASC limit {$offset},{$psize}", $gweid);
			$list = $wpdb->get_results($sql, ARRAY_A);
			$total= $wpdb->get_var($wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_express where gweid = %s", $gweid));
			$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);	
		} elseif ($operation == 'post') {
            $id = intval($_GET['id']);
            if (checksubmit('submit')) {
                if (empty($_POST['express_name'])) {
                    message('抱歉，请输入物流名称！');
                }
                $data = array(
                    'gweid' => $gweid,
                    'express_name' => $_POST['express_name'],
                    'express_url' => $_POST['express_url'],
                    'express_des' => $_POST['express_des'],
                );
                if (!empty($id)) {
					$rlt = $wpdb->update($wpdb->prefix.'shopping_express', $data, array('id' => $id), array('%s', '%s', '%s', '%s'));
                } else {
                    $rlt = $wpdb->insert($wpdb->prefix.'shopping_express', $data);
                }
				if ($rlt == false && $rlt!=0) {
					message('添加/更新物流失败！', $this->createWebUrl('express', array('op' => 'display')), 'error');
				} else {
					message('添加/更新物流成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
				}
            }
            //reload the page with updates
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_express WHERE id = %d", $id);
            $express = $wpdb->get_row($sql, ARRAY_A);
        } elseif ($operation == 'delete') {
            $id = intval($_GET['id']);
			//check whether this express is exist
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_express WHERE id = %d", $id);
            $express = $wpdb->get_row($sql, ARRAY_A);			
            if (empty($express)) {
                message('抱歉，该物流公司不存在或是已经被删除！', $this->createWebUrl('express', array('op' => 'display')), 'error');
            }
			//check whethere there are dispatches under this express
			$dispatch_count = $wpdb->get_var($wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}shopping_dispatch WHERE express = %d", $id));
			if($dispatch_count != 0) {
				message('该物流公司下有配送方式，请先到配送管理里删除其下的配送方式！', $this->createWebUrl('express', array('op' => 'display')), 'error');
			}
			//deletion action
			$rlt = $wpdb->delete($wpdb->prefix.'shopping_express', array('id' => $id), array('%d'));
			if ($rlt==false) {
				message('物流方式删除失败！', $this->createWebUrl('express', array('op' => 'display')), 'error');
			} else {
				message('物流方式删除成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
			}
        } else {
            message('请求方式不存在');
        }
        include $this->template('express');
    }
	
	//Dispatch management
	public function doWebDispatch() {
	    global $_W, $_GPC ,$wpdb;
	    $gweid = $_SESSION['GWEID'];	
		$allregions = array("北京", "天津", "上海", "重庆", "河北", "山西", "内蒙古", "辽宁", "吉林", "黑龙江", "江苏", "浙江", "安徽", "福建", "江西", "山东", "河南", "湖北", "湖南", "广东", "广西", "海南", "四川", "贵州", "云南", "西藏", "陕西", "甘肃", "青海", "宁夏", "新疆", "台湾", "香港", "澳门", "海外");		
        $operation = !empty($_GET['op']) ? $_GET['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GET['page']));
			$psize = 5;
			$offset=($pindex - 1) * $psize;		/*point update*/	
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_dispatch WHERE gweid = %s and dispid!='-1' and dispid!='-2' ORDER BY id ASC limit {$offset},{$psize}", $gweid);
			$list = $wpdb->get_results($sql, ARRAY_A);
			//add express_name into the array $list
			$i = 0;
			foreach($list as $l) {
				$dispatch = $wpdb -> get_var($wpdb -> prepare("SELECT express_name FROM {$wpdb->prefix}shopping_express WHERE id = %d", $l['express']));
				if (!empty($dispatch)) {
					$list[$i]['express_name'] = $dispatch;
				} else {
					$list[$i]['express_name'] = "";
				}
				$i++;
			}/*point update*/
			$total= $wpdb->get_var($wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_dispatch where gweid = %s and dispid!='-1' and dispid!='-2' ", $gweid));
			$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
		} elseif ($operation == 'post') {
            $id = intval($_GET['id']);
			
			if (checksubmit('submit')) {
				
				
				
				/*point update*/
				
				//判断是否选择使用积分
				if (!empty($_POST['isdispathpoint'][0])) {
					//如果选择使用积分
					$isdispathpoint=-2;
				}else{
					$isdispathpoint=-1;
				}
				
				if($_POST['dispatchpricetype_point'] == 0) {
					$dispatchprice_point = $_POST['dispatchprice_point'];
					
					$region_point = '';
					$firstprice_point = 0;
					$firstweight_point = 0;
					$secondprice_point = 0;
					$secondweight_point = 0;
				} else {
					$arr = array();
					$j = 0;
					
					for($i=0;$i<count($allregions);$i++) {
						if (!empty($_POST['regions_point'][$i])) {
							$arr[$j] = $_POST['regions_point'][$i];
							$j++;
						}
					}
					
					$region_point = implode(",", $arr);
					$dispatchprice_point = 0;
					$firstprice_point = $_POST['firstprice_point'];
					$secondprice_point = $_POST['secondprice_point'];
					
					$firstweight_point = $_POST['firstweight_point'];
					$secondweight_point = $_POST['secondweight_point'];					
				}
				
				$data = array(
                    'gweid' => $gweid,
                    'dispatchname' => $_POST['dispatchname'],
					'dispatchtype' => intval($_POST['dispatchtype']),
					'dispatchpricetype' => intval($_POST['dispatchpricetype_point']),
					'dispatchprice' => floatval($dispatchprice_point),
					'region' =>  $region_point,
                    'express' => intval($_POST['expressname']),
                    'firstprice' => floatval($firstprice_point),
                    'firstweight' => intval($firstweight_point),
                    'secondprice' => floatval($secondprice_point),
                    'secondweight' => intval($secondweight_point),
                    'description' => $_POST['description'],
					'dispid' => $isdispathpoint//是否表示操作积分
					
                );
				
				
                if (!empty($id)) {
					$rlt = $wpdb->update($wpdb->prefix.'shopping_dispatch', $data, array('id' => $_POST['id_point']), array('%s', '%s', '%d', '%d', '%f', '%s', '%d', '%f', '%d', '%f', '%d', '%s', '%s'));
					$uppointid=$_POST['id_point'];
                } else {
                    $rlt = $wpdb->insert($wpdb->prefix.'shopping_dispatch', $data);
					$uppointid=$wpdb->insert_id;
                }
				
				/*point update END*/
				
				
				if($_POST['dispatchpricetype'] == 0) {
					$dispatchprice = $_POST['dispatchprice'];
					
					$region = '';
					$firstprice = 0;
					$firstweight = 0;
					$secondprice = 0;
					$secondweight = 0;
				} else {
					$arr = array();
					$j = 0;
					
					for($i=0;$i<count($allregions);$i++) {
						if (!empty($_POST['regions'][$i])) {
							$arr[$j] = $_POST['regions'][$i];
							$j++;
						}
					}
					
					$region = implode(",", $arr);
					$dispatchprice = 0;
					$firstprice = $_POST['firstprice'];
					$secondprice = $_POST['secondprice'];
					
					$firstweight = $_POST['firstweight'];
					$secondweight = $_POST['secondweight'];					
				}
				
				
				
				
				$data = array(
                    'gweid' => $gweid,
                    'dispatchname' => $_POST['dispatchname'],
					'dispatchtype' => intval($_POST['dispatchtype']),
					'dispatchpricetype' => intval($_POST['dispatchpricetype']),
					'dispatchprice' => floatval($dispatchprice),
					'region' =>  $region,
                    'express' => intval($_POST['expressname']),
                    'firstprice' => floatval($firstprice),
                    'firstweight' => intval($firstweight),
                    'secondprice' => floatval($secondprice),
                    'secondweight' => intval($secondweight),
                    'description' => $_POST['description'],
					'dispid' => $uppointid
                );
				
                if (!empty($id)) {
					$rlt = $wpdb->update($wpdb->prefix.'shopping_dispatch', $data, array('id' => $id), array('%s', '%s', '%d', '%d', '%f', '%s', '%d', '%f', '%d', '%f', '%d', '%s', '%s'));
                } else {
                    $rlt = $wpdb->insert($wpdb->prefix.'shopping_dispatch', $data);
                }
				
				
				if ($rlt == false && $rlt != 0) {
					message('添加/更新配送失败！', $this->createWebUrl('dispatch', array('op' => 'display')), 'error');
				} else {
					message('添加/更新配送成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
				}
            }
            //Reload the page
            $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_dispatch WHERE id = %d", $id);
			$dispatch = $wpdb->get_row($sql, ARRAY_A);
			$r = explode(",", $dispatch['region']);
			
			
			/*point update*/
			if($dispatch['dispid']!=-1 || $dispatch['dispid']!=0){
				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_dispatch WHERE id = %s", $dispatch['dispid']);
				$dispatch_point = $wpdb->get_row($sql, ARRAY_A);
				$r_point = explode(",", $dispatch_point['region']);			
			}
			/*point update end*/
			
			
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_express WHERE gweid = %s", $gweid);
			$express = $wpdb->get_results($sql, ARRAY_A);
        } else if ($operation == 'delete') {
            //Delete one dispatch
			$id = intval($_GET['id']);
            $sql = $wpdb -> prepare("SELECT id,dispid FROM {$wpdb->prefix}shopping_dispatch WHERE id = %d AND gweid = %s", $id, $gweid);
			$dispatch = $wpdb->get_row($sql,ARRAY_A);
            if (empty($dispatch)) {
                message('抱歉，配送方式不存在或是已经被删除！', $this->createWebUrl('dispatch', array('op' => 'display')), 'error');
            }
			//point update
			$rlt = $wpdb->delete($wpdb->prefix.'shopping_dispatch', array('id' => $dispatch['dispid']), array('%d'));
            $rlt = $wpdb->delete($wpdb->prefix.'shopping_dispatch', array('id' => $id), array('%d'));
			if ($rlt==false) {
				message('配送方式删除失败！', $this->createWebUrl('dispatch', array('op' => 'display')), 'error');
			} else {
				message('配送方式删除成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
			}
        } else {
            message('请求方式不存在');
        }
        include $this->template('dispatch', TEMPLATE_INCLUDEPATH, true);
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
	//检查是否有新订单
	public function doWebOrderCheck(){
		global $wpdb;
		$gweid=$_SESSION['GWEID'];
		$list = $wpdb -> get_results($wpdb -> prepare("SELECT  `out_trade_no` FROM {$wpdb -> prefix}shopping_order WHERE `gweid` = %s AND `read`=0 AND isshopping='1' ORDER BY `time_start` DESC", $gweid),ARRAY_A);
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
		$gweid =  $_SESSION['GWEID'];
	    
		$search_condition = trim($_GET['range']);
		$search_content = trim($_GET['indata']);
		
		if($search_condition=="delivery_status"){
			
			if($search_content=="未发货"){
				$sql=$wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and isshopping='1' and  out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='1' or delivery_status='2') and out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and  out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order_goods WHERE isdelivery=0))",$gweid,$gweid);
			}else if($search_content=="已发货"){
				$sql=$wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and isshopping='1' and  out_trade_no in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='1')",$gweid);		
			}else if($search_content=="无需发货"){
				$sql=$wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and isshopping='1' and out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order_goods WHERE isdelivery=0)",$gweid);		
			}else if($search_content=="收货已确认"){
				$sql=$wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and isshopping='1' and  out_trade_no in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='2')",$gweid);		
			}
			
			$total = $wpdb->get_var($sql);
			$pindex = max(1, intval($_GET['page']));
			$psize = 5;
			$pindex = min(max(ceil($total/$psize),1),$pindex );
			$offset=($pindex - 1) * $psize;
			$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
			
			if($search_content=="未发货"){
				$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and isshopping='1' and  out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='1' or delivery_status='2') and out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order_goods WHERE isdelivery=0)) ORDER BY out_trade_no desc limit %d,%d",$gweid,$gweid,$offset,$psize);
			}else if($search_content=="已发货"){
				$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and isshopping='1' and  out_trade_no in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='1') ORDER BY out_trade_no desc limit %d,%d",$gweid,$offset,$psize);		
			}else if($search_content=="无需发货"){//SELECT out_trade_no FROM {$wpdb->prefix}shopping_order_goods 排除native支付方式不在order_goods表
				$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and isshopping='1' and  out_trade_no not in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_order_goods WHERE isdelivery=0) ORDER BY out_trade_no desc limit %d,%d",$gweid,$offset,$psize);		
			}else if($search_content=="收货已确认"){
				$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE  gweid = %s and isshopping='1' and  out_trade_no in (SELECT out_trade_no FROM {$wpdb->prefix}shopping_delivery WHERE delivery_status='2') ORDER BY out_trade_no desc limit %d,%d",$gweid,$offset,$psize);		
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
			
			$sql=$wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}shopping_order where gweid=%s and isshopping='1' {$search[$search_condition]} ORDER BY out_trade_no desc",$gweid);
			
			$total = $wpdb->get_var($sql);
			$pindex = max(1, intval($_GET['page']));
			$psize = 5;
			$pindex = min(max(ceil($total/$psize),1),$pindex );
			$offset=($pindex - 1) * $psize;
			$pager = $this -> doWebpaginationa_page($total, $pindex, $psize);
			
			$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order where gweid=%s and isshopping='1' {$search[$search_condition]} ORDER BY out_trade_no desc limit {$offset},{$psize}",$gweid);
			$list = $wpdb->get_results($sql);
		}
		$orderarray=array();
		if(is_array($list) && !empty($list)){
			foreach($list as $order){

				//20150420 sara new added 
				$gweidv=$_W['gweidv'];  //获取虚拟号gweid
				$buyersinfo = $this ->doWebBuyer($order->mid,$order->openid,$gweidv);
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
		
		/* $sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$orderid);
		$orders = $wpdb->get_results($sql); */
		//获取快递相关信息weshopping部分需要
		$sql = $wpdb -> prepare("SELECT *, o.dispatchprice as dispatchprice FROM  {$wpdb->prefix}shopping_order o LEFT JOIN {$wpdb->prefix}shopping_dispatch d on o.dispatch = d.id WHERE  o.GWEID=%s and o.out_trade_no = %s",$gweid,$orderid);
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
				$order_pointall = $order->pointall;//point update
				$order_isdispatchpoint = $order->isdispatchpoint;//point update
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
				//new column needed for weshopping
				$order_isshopping = $order->isshopping;     //是否是weshopping中的订单
				if($order_isshopping == 1){
					$order_dispatchname = $order->dispatchname;
					$order_dispatchprice = $order->dispatchprice;
					$order_remark = $order->remark;
				}
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
		//20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid	
		$buyersinfo = $this ->doWebBuyer($order_mid,$order_openid,$gweidv);
		
		//$trade_states=$this ->doWebTradeState();
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_goods where out_trade_no=%s",$orderid);
		$ordergoodsinfos = $wpdb->get_results($sql);
		
		
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
			$remark = $delivery->remark;
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
			/* if($_POST['delivery_compid'] == -1){   //如果选择的是下拉列表中的“其他”，则将新输入的input作为此字段值
				$delivery_compid = $_POST['new_dispatch'];
				//对应order表中的dispatch字段置为-1
				$wpdb->update("{$wpdb -> prefix}shopping_order", array('dispatch' => -1), array('out_trade_no' => $orderid));
			}else{
				$dispatchid = $_POST['dispatch_newid'];
				//对应order表中的dispatch字段更新为相应的dispatchid
				$wpdb->update("{$wpdb -> prefix}shopping_order", array('dispatch' => $dispatchid), array('out_trade_no' => $orderid));
			} */
			$delivery_sn = $_POST['delivery_sn'];
			$delivery_status = $_POST['delivery_status'];
			$delivery_msg = $_POST['delivery_msg'];
			$remark = $_POST['remark'];
			
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_delivery where out_trade_no=%s",$orderid);
			$deliversinfo = $wpdb->get_results($sql);
			if((!empty($deliversinfo))&&($delivery_status=='0')){			
				$upstatus=$wpdb->update( $wpdb->prefix.'shopping_delivery', array('delivery_compid'=>$delivery_compid,'delivery_sn'=>$delivery_sn,'delivery_status'=>$delivery_status,'delivery_msg'=>$delivery_msg,'remark'=>$remark,'delivery_timestamp'=> date('Y-m-d H:i:s')),array('out_trade_no'=>$orderid), array('%s','%s','%s','%s','%s'),array('%s'));
			
			}else if((!empty($deliversinfo))&&($delivery_status!='0')){
				$upstatus=$wpdb->update( $wpdb->prefix.'shopping_delivery', array('delivery_compid'=>$delivery_compid,'delivery_sn'=>$delivery_sn,'delivery_status'=>$delivery_status,'delivery_msg'=>$delivery_msg,'remark'=>$remark,'delivery_timestamp'=> date('Y-m-d H:i:s')),array('out_trade_no'=>$orderid), array('%s','%s','%s','%s','%s'),array('%s'));
				//调用发货通知给微信--待测
				//$this -> doWebDeliveryWePay($orderid,$gweid,$upstatus);
				
			}else{ 
				$upstatus=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_delivery(out_trade_no,delivery_compid,delivery_sn,delivery_status,delivery_msg,remark)VALUES (%s,%s, %s, %s, %s, %s)",$orderid,$delivery_compid,$delivery_sn,$delivery_status,$delivery_msg,$remark));
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
			$remark =  $delivery->remark;
		}
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_courier where gweid=%s",$gweid);
		$couriers = $wpdb->get_results($sql);
		
		//如果是weshopping中的订单，则需要下拉出对应的配送方式
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order WHERE  GWEID=%s and out_trade_no = %s",$gweid,$orderid);
		//$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order o LEFT JOIN {$wpdb->prefix}shopping_dispatch d on o.dispatch = d.id WHERE  o.gweid=%s and o.out_trade_no = %s",$gweid,$orderid);
		$orderinfos = $wpdb->get_results($sql);
		foreach($orderinfos as $orderinfo ){	
			$order_isshopping = $orderinfo->isshopping;
			$order_dispatchid = $orderinfo->dispatch;
		}
		if($order_isshopping == 1){
			$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_dispatch WHERE gweid=%s AND dispid > 0",$gweid);
			$dispatchs = $wpdb->get_results($sql);	
		}
		
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
			$search_condition='search3';//微商城某一级分类下所有商品			
		}else{			
			$search_condition = 'all';//所有商品
		}
		
		
		$search = array(
			'all' => '',
			'search1' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id={$goods_select})",
			'search2' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where ccate={$cate_2} and deleted=0 and isshopping=1 and gweid={$gweid}))",
			'search3' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where pcate={$cate_1} and deleted=0 and isshopping=1 and gweid={$gweid}))",
			'search4'=>"AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where isshopping=1 and deleted=0 and gweid={$gweid}))"
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
			$jsonresult=array();
			$day1 = 3600 * 24;
			$month1 = 31 * $day1;
			$year1 = 365 * $day1;
			if(strtotime($enddate) - strtotime($startdate) <= $month1 && strtotime($enddate) - strtotime($startdate) >=$day1){
				$current_date = $startdate;

				while($current_date <= $enddate) {
				
					if($siteid == 0){   
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE  s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')   {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
						$element=array();
						$element['today']=$current_date;
						$element['countClick']=$clicktimes;
						$jsonresult[]=$element;
						
						}
					elseif($siteid>0){    //count sum fee from shopping_order, the sum fee may less than the refund fee 
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
						$element=array();
						$element['today']=$current_date;
						if(empty($clicktimes))
						{
						    $element['countMoney']='0';
						}else{
						    $element['countMoney']=$clicktimes;
						}
						
						$sql1 = $wpdb -> prepare("SELECT sum(s1.refund_fee) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
						
						$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as $successcount){
						    if( !array_key_exists('成功的订单',$jsonresult) )
								$jsonresult['成功的订单'] = array();
							$jsonresult['成功的订单'][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
						}
		
						$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$refundcounts = $wpdb->get_results($sql);
						foreach($refundcounts as $refundcount){
							if( !array_key_exists('退款的订单',$jsonresult) )
								$jsonresult['退款的订单'] = array();
							$jsonresult['退款的订单'][] = array("today" => str_replace('-','/',$current_date), "countclick" => $refundcount->counts);
						}
						
						$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')   {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$incounts = $wpdb->get_results($sql);
						foreach($incounts as $incount){
							if( !array_key_exists('进行中的订单',$jsonresult) )
								$jsonresult['进行中的订单'] = array();
							$jsonresult['进行中的订单'][] = array("today" => str_replace('-','/',$current_date), "countclick" => $incount->counts);
						}
						
					}elseif($siteid == -2){   //全部订单、成功订单、退款订单以及进行中的订单对应的金额
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
						
						$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						//echo "对应的sql语句".$sql."\n";
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
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
						$sql1 = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
			if(strtotime($enddate) - strtotime($startdate) == 0)
			{
			   
				$current_date = $startdate;
				if($siteid == 0){
					for($i =0; $i<24; $i++)
					{
						$element=array();
						if($i < 10)
						{
							
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							$element['countClick']=$clicktimes;
							$jsonresult[]=$element;
							
							
						}
						else
						{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state not  in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state not  in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
						    $clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							$element['countClick']=$clicktimes;
							$jsonresult[]=$element;
							
							
						}
					}
						
				}
				elseif($siteid>0){
					 for($i =0; $i<24; $i++)
					{
						$element=array();
						if($i < 10)
						{
							
							$sql = $wpdb -> prepare("SELECT sum(s1.fee) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							if(empty($clicktimes))
							{
							    $element['countMoney']='0';
							}else{
							    $element['countMoney']=$clicktimes;
							}
							
							$sql1 = $wpdb -> prepare("SELECT sum(s1.refund_fee) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s  {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
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
						else
						{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED')  {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
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
								$sql1 = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql1 = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
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
				
				}
				elseif($siteid == -1){
					for($i =0; $i<24; $i++)
					{
						if($i < 10)
						{
							
						    $sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
							$successcounts = $wpdb->get_results($sql);
							
							foreach($successcounts as $successcount){
								if( !array_key_exists('成功的订单',$jsonresult) )
									$jsonresult['成功的订单'] = array();
								$jsonresult['成功的订单'][] = array("today" => $i, "countclick" => $successcount->counts);
							}
							
							$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $refundcounts = $wpdb->get_results($sql);
							
							foreach($refundcounts as $refundcount){
								if( !array_key_exists('退款的订单',$jsonresult) )
									$jsonresult['退款的订单'] = array();
								$jsonresult['退款的订单'][] = array("today" => $i, "countclick" => $refundcount->counts);
							}
							
							
						    $sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
							$incounts = $wpdb->get_results($sql);
							
							foreach($incounts as $incount){
								if( !array_key_exists('进行中的订单',$jsonresult) )
									$jsonresult['进行中的订单'] = array();
								$jsonresult['进行中的订单'][] = array("today" => $i, "countclick" => $incount->counts);
							}
							
						}
						else
						{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as $successcount){
								if( !array_key_exists('成功的订单',$jsonresult) )
									$jsonresult['成功的订单'] = array();
								$jsonresult['成功的订单'][] = array("today" => $i, "countclick" => $successcount->counts);
							}
							
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
						    $refundcounts = $wpdb->get_results($sql);
							
							foreach($refundcounts as $refundcount){
								if( !array_key_exists('退款的订单',$jsonresult) )
									$jsonresult['退款的订单'] = array();
								$jsonresult['退款的订单'][] = array("today" => $i, "countclick" => $refundcount->counts);
							}
							
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
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
							
						    $sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
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
							
							$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
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
							
							
							$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
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
							
							
						}
						else
						{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
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
								$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							
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
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							
							}
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
						
							
						}
					}
				}elseif($siteid == -3){    //获取成功的订单量、退款的订单量以及进行中的订单量的纵坐标最大值
					for($i =0; $i<24; $i++)
					{
						$element=array();
						if($i < 10)
						{
							
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $clicktimes = $wpdb->get_var($sql);
							$sql1 = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
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
						else
						{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
						    $clicktimes = $wpdb->get_var($sql);
							
							if($i == 23){
								$sql1 = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql1 = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
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
			if (strtotime($enddate) - strtotime($startdate) > $month1) {

				$start_month = date("Y-m", strtotime($startdate));
				$end_month = date("Y-m", strtotime($enddate));
				$current_month = $start_month;

				while(strtotime($current_month) <= strtotime($end_month)) {
				
					if($siteid == 0){
						
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1  WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
							
						$element=array();
						$element['today']=$current_month;
						$element['countClick']=$clicktimes;
						$jsonresult[]=$element;
					
						
						}
					elseif($siteid>0){
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
							
						$element=array();
						$element['today']=$current_month;
						if(empty($clicktimes))
						{
							$element['countMoney']='0';
						}else{
							$element['countMoney']=$clicktimes;
						}
						
						$sql1 = $wpdb -> prepare("SELECT sum(s1.refund_fee) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
						
						
						$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$successcounts = $wpdb->get_results($sql);
						
						foreach($successcounts as $successcount){
							if( !array_key_exists('成功的订单',$jsonresult) )
								$jsonresult['成功的订单'] = array();
							$jsonresult['成功的订单'][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
						}
						$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$refundcounts = $wpdb->get_results($sql);
						
						foreach($refundcounts as $refundcount){
							if( !array_key_exists('退款的订单',$jsonresult) )
								$jsonresult['退款的订单'] = array();
							$jsonresult['退款的订单'][] = array("today" => str_replace('-','/',$current_month), "countclick" => $refundcount->counts);
						}
						
						
						$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$incounts = $wpdb->get_results($sql);
						
						foreach($incounts as $incount){
							if( !array_key_exists('进行中的订单',$jsonresult) )
								$jsonresult['进行中的订单'] = array();
							$jsonresult['进行中的订单'][] = array("today" => str_replace('-','/',$current_month), "countclick" => $incount->counts);
						}
							
					
						
					}elseif($siteid == -2){   //全部订单、成功订单、退款订单以及进行中的订单对应的金额
						
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
						
						$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
						
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
						$sql1 = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
			$search_condition='search3';//微商城某一级分类下所有商品

			
		}else{
			$search_condition = 'all';//所有商品
			
		}
		
		
		$search = array(
			'all' => '',
			'search1' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id={$goods_select})",
			'search2' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where ccate={$cate_2} and isshopping=1 and deleted=0 and gweid={$gweid}))",
			'search3' => "AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where pcate={$cate_1} and isshopping=1 and deleted=0 and gweid={$gweid}))",
			'search4'=>"AND s1.out_trade_no IN (select distinct out_trade_no from {$wpdb->prefix}shopping_order_goods where goods_id IN (select id from {$wpdb->prefix}shopping_goods where isshopping=1 and deleted=0 and gweid={$gweid}))"
		
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
				   
					$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
				    $countallorders = $wpdb->get_var($sql);
						
					echo iconv("utf-8", "gb2312", $countallorders).",";
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				
				//接下来是成功的订单量
				$current_date = $startdate;
				echo iconv("utf-8", "gb2312", "成功的订单量(个)").",";
				while($current_date <= $enddate) {
				
				    $sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
				
					$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
				
					$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
				
				    $sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
				
					$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
				
					$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
						
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
				        $countallcounts = $wpdb->get_var($sql);
						
						echo iconv("utf-8", "gb2312", $countallcounts).",";
					}
					else
					{
						if($i == 23){
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
						
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
						
				        $sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						$successcounts = $wpdb->get_results($sql);
						
						foreach($successcounts as $successcount){
							echo iconv("utf-8", "gb2312",  $successcount->counts).",";
						}
					}
					else
					{
						if($i == 23){
							$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
						
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
						
						$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
				        $refundcounts = $wpdb->get_results($sql);
						
						foreach($refundcounts as $refundcount){
							echo iconv("utf-8", "gb2312",  $refundcount->counts).",";
						}
					}
					else
					{
						if($i == 23){
							$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
						
						
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
					   
						$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
				        $incounts = $wpdb->get_results($sql);
						
						foreach($incounts as $incount){
							echo iconv("utf-8", "gb2312",  $incount->counts).",";
						}
						
					}
					else
					{
					    if($i == 23){
							$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}",$current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
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
						
				        $sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
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
							$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
						
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
						
						$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
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
							$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
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
						
						$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
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
							$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
						
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
				
					$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_order s1 WHERE s1.time_start >=%s AND s1.time_start <=%s AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
				    $allcounts = $wpdb->get_var($sql);
						
					echo iconv("utf-8", "gb2312", $allcounts).",";
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				
				echo "\n";
				
				//输出每月成功的订单量
				$current_month = $start_month;
				echo iconv("utf-8", "gb2312", "成功的订单量(个)").",";
				while(strtotime($current_month) <= strtotime($end_month)) {
				
					
				    $sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
				
					
					$sql = $wpdb -> prepare("SELECT count(distinct s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
				
					
					$sql = $wpdb -> prepare("SELECT count(s1.out_trade_no) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
				
					
				    $sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND s1.gweid = %s AND s1.trade_state = 'SUCCESS' {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
				
					$sql = $wpdb -> prepare("SELECT sum(s1.refund_fee) as counts FROM ".$wpdb->prefix."shopping_refund s1, ".$wpdb->prefix."shopping_order s2 where s1.out_trade_no = s2.out_trade_no AND s1.time_start >=%s AND s1.time_start <=%s AND s2.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
				
					$sql = $wpdb -> prepare("SELECT sum(s1.fee) as counts FROM ".$wpdb->prefix."shopping_order s1 where s1.time_start >=%s AND s1.time_start <=%s AND ((s1.trade_state != 'SUCCESS') AND (s1.trade_state != 'REFUND') AND (s1.trade_state != 'CLOSED')) AND s1.trade_state not in('SELFDELIVERY','CASHONDELIVERY','SELFDELIVERY_CLOSED','CASHONDELIVERY_CLOSED') AND s1.gweid = %s {$search['search4']}  {$search[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
			$search_condition='search3';//微商城某一级分类下所有商品
		
		}else{
			$search_condition = 'all';//所有商品
		}
		
		
		$search = array(
			'all' => '',
			'search1' => "AND id={$goods_select}",
			'search2' => "AND ccate={$cate_2}",
			'search3' => "AND pcate={$cate_1}"
		);
		
		$searchall = array(
			'all' => "AND s1.type_id IN (select id from {$wpdb->prefix}shopping_goods where isshopping=1 and deleted=0 and gweid={$gweid})",
			'search1' => "AND s1.type_id={$goods_select}",
			'search2' => "AND s1.type_id IN (select id from {$wpdb->prefix}shopping_goods where isshopping=1 and ccate={$cate_2} and deleted=0 and gweid={$gweid})",
			'search3' => "AND s1.type_id IN (select id from {$wpdb->prefix}shopping_goods where isshopping=1 and pcate={$cate_1} and deleted=0 and gweid={$gweid})"
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
			$jsonresult=array();
			$day1 = 3600 * 24;
			$month1 = 31 * $day1;
			$year1 = 365 * $day1;
			if(strtotime($enddate) - strtotime($startdate) <= $month1 && strtotime($enddate) - strtotime($startdate) >=$day1){
				$current_date = $startdate;

				while($current_date <= $enddate) {
				
					
					if($siteid == -4){   /*statistic new add*/

						//所有商品
						$sql = $wpdb -> prepare("SELECT title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'goodsid' AND type_id = ".$wpdb->prefix."shopping_goods.id ) as counts,id from ".$wpdb->prefix."shopping_goods where isshopping='1' and deleted='0' and gweid=%s {$search[$search_condition]} group by id", $current_date." 00:00:00", $current_date." 23:59:59",$gweid);
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as &$successcount){
							$successcount->title  = str_replace ( " ", "_" , $successcount->title );
							if( !array_key_exists($successcount->title.'_'.$successcount->id,$jsonresult) )
								$jsonresult[$successcount->title.'_'.$successcount->id] = array();
								$jsonresult[$successcount->title.'_'.$successcount->id][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
						}
						
					}else if($siteid == -5){   
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE  s1.time >=%s AND s1.time <=%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
						$clicktimes = $wpdb->get_var($sql);
						$element=array();
						$element['today']=$current_date;
						$element['countClick']=$clicktimes;
						$jsonresult[]=$element;
						
					}else if($siteid == -6){   
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
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
					
							//所有商品
							$sql = $wpdb -> prepare("SELECT title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'goodsid' AND type_id = ".$wpdb->prefix."shopping_goods.id ) as counts,id from ".$wpdb->prefix."shopping_goods where isshopping='1' and deleted='0' and gweid=%s {$search[$search_condition]} group by id",$current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00",$gweid);
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as &$successcount){
								$successcount->title  = str_replace ( " ", "_" , $successcount->title );
								if( !array_key_exists($successcount->title.'_'.$successcount->id,$jsonresult) )
									$jsonresult[$successcount->title.'_'.$successcount->id] = array();
									$jsonresult[$successcount->title.'_'.$successcount->id][] = array("today" => $i, "countclick" => $successcount->counts);
							}
						
						}else{/*statistic new add end*/
							//所有商品
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'goodsid' AND type_id = ".$wpdb->prefix."shopping_goods.id ) as counts,id from ".$wpdb->prefix."shopping_goods where isshopping='1' and deleted='0' and gweid=%s {$search[$search_condition]} group by id", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59",$gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'goodsid' AND type_id = ".$wpdb->prefix."shopping_goods.id ) as counts,id from ".$wpdb->prefix."shopping_goods where isshopping='1' and deleted='0' and gweid=%s {$search[$search_condition]} group by id", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00",$gweid);
							}
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as &$successcount){
								$successcount->title  = str_replace ( " ", "_" , $successcount->title );
								if( !array_key_exists($successcount->title.'_'.$successcount->id,$jsonresult) )
									$jsonresult[$successcount->title.'_'.$successcount->id] = array();
									$jsonresult[$successcount->title.'_'.$successcount->id][] = array("today" => $i, "countclick" => $successcount->counts);
							}
				
						}
					}
				}else if($siteid == -5){
					for($i =0; $i<24; $i++)
					{
						$element=array();
						if($i < 10)
						{
							
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							$element['countClick']=$clicktimes;
							$jsonresult[]=$element;
							
							
						}
						else
						{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							}
						    $clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							$element['countClick']=$clicktimes;
							$jsonresult[]=$element;
							
							
						}
					}
						
				}elseif($siteid == -6){   
					for($i =0; $i<24; $i++)
					{
						$element=array();
						if($i < 10)
						{
							
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
						    $clicktimes = $wpdb->get_var($sql);
							
							$element=array();
							$element['today']=$i;
							$element['county']=$clicktimes;
							$jsonresult[]=$element;
							
							
						}
						else
						{
							if($i == 23){
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
							}else{
								$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
							
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
							//所有商品
							$sql = $wpdb -> prepare("SELECT title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'goodsid' AND type_id = ".$wpdb->prefix."shopping_goods.id ) as counts,id from ".$wpdb->prefix."shopping_goods where isshopping='1' and deleted='0' and gweid=%s {$search[$search_condition]} group by id", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59",$gweid);
							$successcounts = $wpdb->get_results($sql);
							foreach($successcounts as &$successcount){
								$successcount->title  = str_replace ( " ", "_" , $successcount->title );
								if( !array_key_exists($successcount->title.'_'.$successcount->id,$jsonresult) )
									$jsonresult[$successcount->title.'_'.$successcount->id] = array();
									$jsonresult[$successcount->title.'_'.$successcount->id][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
							}
					}else if($siteid == -5){
						
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1  WHERE s1.time >=%s AND s1.time <=%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
							$clicktimes = $wpdb->get_var($sql);
								
							$element=array();
							$element['today']=$current_month;
							$element['countClick']=$clicktimes;
							$jsonresult[]=$element;
					
						
						}elseif($siteid == -6){     //计算成功的订单量、退款的订单量以及进行中的订单量的纵坐标最大值
						
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
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
			$search_condition='search3';//微商城某一级分类下所有商品
		
		}else{
			$search_condition = 'all';//所有商品
		}
		
		
		$search = array(
			'all' => '',
			'search1' => "AND id={$goods_select}",
			'search2' => "AND ccate={$cate_2}",
			'search3' => "AND pcate={$cate_1}"
		);
		
		$searchall = array(
			'all' => "AND s1.type_id IN (select id from {$wpdb->prefix}shopping_goods where isshopping=1 and deleted=0 and gweid={$gweid})",
			'search1' => "AND s1.type_id={$goods_select}",
			'search2' => "AND s1.type_id IN (select id from {$wpdb->prefix}shopping_goods where isshopping=1 and deleted=0 and ccate={$cate_2} and gweid={$gweid})",
			'search3' => "AND s1.type_id IN (select id from {$wpdb->prefix}shopping_goods where isshopping=1 and deleted=0 and pcate={$cate_1} and gweid={$gweid})"
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
				echo iconv("utf-8", "gb2312", "商品总点击单量").",";
				while($current_date <= $enddate) {
				   
					$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", $current_date." 00:00:00", $current_date." 23:59:59", $gweid);
					$countallorders = $wpdb->get_var($sql);
						
					echo iconv("utf-8", "gb2312", $countallorders).",";
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				echo "\n";
				/*shopping count END*/
				
				/*shopping count*/
				$current_date = $startdate;
				while($current_date <= $enddate) {
					//所有商品
					$sql = $wpdb -> prepare("SELECT title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'goodsid' AND type_id = ".$wpdb->prefix."shopping_goods.id ) as counts,id from ".$wpdb->prefix."shopping_goods where isshopping='1' and deleted='0' and gweid=%s {$search[$search_condition]} group by id", $current_date." 00:00:00", $current_date." 23:59:59",$gweid);
					$successcounts = $wpdb->get_results($sql);
					foreach($successcounts as &$successcount){
						$successcount->title  = str_replace ( " ", "_" , $successcount->title );
						if( !array_key_exists($successcount->title.'_'.$successcount->id,$jsonresult) )
							$jsonresult[$successcount->title.'_'.$successcount->id] = array();
							$jsonresult[$successcount->title.'_'.$successcount->id][] = array("today" => str_replace('-','/',$current_date), "countclick" => $successcount->counts);
					}
						
					$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
				}
				//下载商品
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
				echo iconv("utf-8", "gb2312", "商品总点击量").",";
				for($i =0; $i<24; $i++)
				{
				    if($i < 10)
					{
						
						$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", $current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00", $gweid);
				        $countallcounts = $wpdb->get_var($sql);
						
						echo iconv("utf-8", "gb2312", $countallcounts).",";
					}
					else
					{
						if($i == 23){
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND s1.type = 'goodsid' AND s1.gweid = %s  {$searchall[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59", $gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <%s AND s1.type = 'goodsid' AND s1.gweid = %s  {$searchall[$search_condition]}", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00", $gweid);
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
						
						//所有商品
						$sql = $wpdb -> prepare("SELECT title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'goodsid' AND type_id = ".$wpdb->prefix."shopping_goods.id ) as counts,id from ".$wpdb->prefix."shopping_goods where isshopping='1' and deleted='0' and gweid=%s {$search[$search_condition]} group by id",$current_date." 0".$i.":00:00", $current_date." 0".($i+1).":00:00",$gweid);
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as &$successcount){
							$successcount->title  = str_replace ( " ", "_" , $successcount->title );
							if( !array_key_exists($successcount->title.'_'.$successcount->id,$jsonresult) )
								$jsonresult[$successcount->title.'_'.$successcount->id] = array();
								$jsonresult[$successcount->title.'_'.$successcount->id][] = array("today" => $i, "countclick" => $successcount->counts);
						}
							
					}
					else
					{
						
						//所有商品
						if($i == 23){
							$sql = $wpdb -> prepare("SELECT title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'goodsid' AND type_id = ".$wpdb->prefix."shopping_goods.id ) as counts,id from ".$wpdb->prefix."shopping_goods where isshopping='1' and deleted='0' and gweid=%s {$search[$search_condition]} group by id", $current_date." ".$i.":00:00", $current_date." ".$i.":59:59",$gweid);
						}else{
							$sql = $wpdb -> prepare("SELECT title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <%s AND type = 'goodsid' AND type_id = ".$wpdb->prefix."shopping_goods.id ) as counts,id from ".$wpdb->prefix."shopping_goods where isshopping='1' and deleted='0' and gweid=%s {$search[$search_condition]} group by id", $current_date." ".$i.":00:00", $current_date." ".($i+1).":00:00",$gweid);
						}
						$successcounts = $wpdb->get_results($sql);
						foreach($successcounts as &$successcount){
							$successcount->title  = str_replace ( " ", "_" , $successcount->title );
							if( !array_key_exists($successcount->title.'_'.$successcount->id,$jsonresult) )
								$jsonresult[$successcount->title.'_'.$successcount->id] = array();
								$jsonresult[$successcount->title.'_'.$successcount->id][] = array("today" => $i, "countclick" => $successcount->counts);
						}
							
					}
				}
				
				//下载商品
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
				echo iconv("utf-8", "gb2312", "商品总点击量").",";
				while(strtotime($current_month) <= strtotime($end_month)) {
				
					$sql = $wpdb -> prepare("SELECT count(*) from ".$wpdb->prefix."shopping_statistics s1 WHERE s1.time >=%s AND s1.time <=%s AND s1.type = 'goodsid' AND s1.gweid = %s {$searchall[$search_condition]}", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59", $gweid);
				    $allcounts = $wpdb->get_var($sql);
						
					echo iconv("utf-8", "gb2312", $allcounts).",";
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				
				echo "\n";
				
				/*商城统计*/
				$current_month = $start_month;
				while(strtotime($current_month) <= strtotime($end_month)) {
					//所有商品
					$sql = $wpdb -> prepare("SELECT title, (SELECT count(*) FROM ".$wpdb->prefix."shopping_statistics where time >=%s AND time <=%s AND type = 'goodsid' AND type_id = ".$wpdb->prefix."shopping_goods.id ) as counts,id from ".$wpdb->prefix."shopping_goods where isshopping='1' and deleted='0' and gweid=%s {$search[$search_condition]} group by id", ($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00", ($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59",$gweid);
					$successcounts = $wpdb->get_results($sql);
					foreach($successcounts as &$successcount){
						$successcount->title  = str_replace ( " ", "_" , $successcount->title );
						if( !array_key_exists($successcount->title.'_'.$successcount->id,$jsonresult) )
							$jsonresult[$successcount->title.'_'.$successcount->id] = array();
							$jsonresult[$successcount->title.'_'.$successcount->id][] = array("today" => str_replace('-','/',$current_month), "countclick" => $successcount->counts);
					}	
					$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
				}
				//下载商品
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
	
	//mobile first page display
	public function doMobilelist() {
        global $_GPC, $_W, $wpdb;
		$gweid = $_GET['gweid'];
		$mid = $_W['fans']['mid'];
		//20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo = $this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				$buyer = $buyer -> nickname;
			}
		}

        $pindex = max(1, intval($_GPC['page']));
        $psize = 4;
		$offset=($pindex - 1) * $psize;
        $condition = '';
        if (!empty($_GPC['ccate'])) {
            $cid = intval($_GPC['ccate']);
            $condition .= " AND ccate = '{$cid}'";
			$sql = $wpdb -> prepare("SELECT parentid FROM {$wpdb->prefix}shopping_category WHERE id = %d",intval($_GPC['ccate']));
			$_GPC['pcate'] = $wpdb->get_var($sql);
		} elseif (!empty($_GPC['pcate'])) {
            $cid = intval($_GPC['pcate']);
            $condition .= " AND pcate = '{$cid}'";
        }
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%%{$_GPC['keyword']}%%'";
        }
        $children = array();
		
		//模板一首页显示
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_category WHERE gweid = %s and enabled=1 and indexenabled=1 ORDER BY parentid ASC, displayorder DESC", $gweid);
		$categoryindex = $wpdb->get_results($sql, ARRAY_A);
		foreach ($categoryindex as $index => $row) {
            if (!empty($row['parentid'])) {
                unset($categoryindex[$index]);
            }
        }
		
	    $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_category WHERE gweid = %s and enabled=1 ORDER BY parentid ASC, displayorder DESC", $gweid);
		$category = $wpdb->get_results($sql, ARRAY_A);
		
		foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][$row['id']] = $row;
                unset($category[$index]);
            }
        }
        $recommandcategory = array();
		//this column is named "isrecommend" not "isrecommand"
        foreach ($category as &$c) {
            if ($c['isrecommend'] == 1) {
				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0 AND (status = 0 OR (`status`=2 AND NOW()>timestart AND NOW()<timeend)) and pcate='{$c['id']}' AND NOT EXISTS(SELECT * FROM {$wpdb->prefix}shopping_category c WHERE c.id=ccate AND enabled = 0) ORDER BY displayorder DESC, sales DESC LIMIT {$offset},{$psize}", $gweid);
				$c['list'] = $wpdb->get_results($sql, ARRAY_A);
                $sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0 AND (status = 0 OR (`status`=2 AND NOW()>timestart AND NOW()<timeend)) and pcate='{$c['id']}' AND NOT EXISTS(SELECT * FROM {$wpdb->prefix}shopping_category c WHERE c.id=ccate AND enabled = 0)",$gweid);
				$c['total'] = $wpdb->get_var($sql);
				$c['pager'] = pagination($c['total'], $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
                $recommandcategory[] = $c;
            }
            if (!empty($children[$c['id']])) {
                foreach ($children[$c['id']] as &$child) {
                    if ($child['isrecommend'] == 1) {
                        $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0 AND (status = 0 OR (`status`=2 AND NOW()>timestart AND NOW()<timeend)) and pcate='{$c['id']}'and ccate='{$child['id']}' AND NOT EXISTS(SELECT * FROM {$wpdb->prefix}shopping_category c WHERE c.id=ccate AND enabled = 0) ORDER BY displayorder DESC, sales DESC LIMIT {$offset},{$psize}", $gweid);
                       	$child['list'] = $wpdb->get_results($sql, ARRAY_A);					
                        $sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0 AND (status = 0 OR (`status`=2 AND NOW()>timestart AND NOW()<timeend))  and pcate='{$c['id']}' and ccate='{$child['id']}' AND NOT EXISTS(SELECT * FROM {$wpdb->prefix}shopping_category c WHERE c.id=ccate AND enabled = 0) ",$gweid);
						$child['total'] = $wpdb->get_var($sql);
						$child['pager'] = pagination($child['total'], $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
                        $recommandcategory[] = $child;
                    }
                }
                unset($child);
            }
        }
        unset($c);
        function category_index_cmp($cate_a, $cate_b) {
        	return $cate_a['isrecommendorder'] == $cate_b['isrecommendorder']?0:($cate_a['isrecommendorder'] > $cate_b['isrecommendorder'] ? -1 : 1);
		}
		usort($recommandcategory, 'category_index_cmp');
        $carttotal = $this->getCartTotal();
        $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_adv WHERE gweid = %s and enabled=1 ORDER BY displayorder asc", $gweid);
		$advs = $wpdb->get_results($sql, ARRAY_A);
		
		foreach ($advs as &$adv) {
            if (substr($adv['link'], 0, 5) != 'http:') {
            	if(!empty($adv['link']))
                	$adv['link'] = "http://" . $adv['link'];
            }
        }
        unset($adv);

        //首页推荐
        $rpindex = max(1, intval($_GPC['rpage']));
        $rpsize = 6;
		$roffset=($rpindex - 1) * $rpsize;
        $condition = ' and isrecommend=1';   //this column is named "isrecommend" not "isrecommand"
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0 AND (status = 0 OR (`status`=2 AND NOW()>timestart AND NOW()<timeend)) {$condition} ORDER BY displayorder DESC, sales DESC LIMIT {$roffset},{$rpsize} ", $gweid);
		$rlist = $wpdb->get_results($sql, ARRAY_A);
		$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0 AND (status = 0 OR (`status`=2 AND NOW()>timestart AND NOW()<timeend)) {$condition} ORDER BY displayorder DESC, sales DESC", $gweid);
		$rlistcount = $wpdb->get_var($sql);
		
		//模板2没有推荐商品的个数限制
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0 AND (status = 0 OR (`status`=2 AND NOW()>timestart AND NOW()<timeend)) {$condition} ORDER BY displayorder DESC, sales DESC", $gweid);
		$rlist2 = $wpdb->get_results($sql, ARRAY_A);
		
		if(isset($_POST['sou']))
		{
		    $searchcontent = $_POST['sou'];
			header("location: " . $this->createMobileUrl('list2',  array('gweid' => $gweid, 'keyword' => $searchcontent)));
            exit();
		}
		$shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
		$_share = array(
			'title' => get_bloginfo('name','display') .' - '.$shopname,
			'desc' => "点击访问".$shopname.'!');
        include $this->template('list');
    }
	
	//detail page
	public function doMobileDetail() {
        global $_W, $_GPC, $wpdb;
		$gweid = $_GET['gweid'];
		$mid = $_W['fans']['mid'];
		//20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				/*point*/
				$allpoint = $buyer -> point;
				$costpoint = $buyer -> point_cost;
				$point=intval($allpoint);
				/*point end*/
				$buyer = $buyer -> nickname;
			}
		}

		$goodsid = $_GET['id'];
		//先判断在不同浏览器上的用户身份
		$this -> verifyUser($gweid);
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_goods WHERE  id = %s",$goodsid);
		$goods = $wpdb->get_row($sql, ARRAY_A);
		/*处理文章图片的显示*/
		$goods['content']=$this->rule_content($goods['content']);
		
		if (empty($goods)) {
			message('抱歉，商品不存在或是已经被删除！');
		}
		if ($goods['status'] == 2) {
			if (time() < strtotime($goods['timestart'])) {
				message('抱歉，还未到购买时间, 暂时无法购物哦~', $this->createMobileUrl('list'), "error");
			}
			if (time() > strtotime($goods['timeend'])) {
				message('抱歉，商品限购时间已到，不能购买了哦~', $this->createMobileUrl('list'), "error");
			}
		}
		//浏览量
		//$wpdb -> update("{$wpdb->prefix}shopping_goods",array('viewcount' => 'viewcount+1'),array('id' => $goodsid, 'gweid' => $gweid));
		
		$piclist1 = array(array("attachment" => $goods['thumb']));
		$piclist = array();
		if (is_array($piclist1)) {
			foreach($piclist1 as $p){
				$piclist[] = is_array($p)?$p['attachment']:$p;
			}
		}
		if ($goods['thumb_url'] != 'N;') {
		  $urls = unserialize($goods['thumb_url']);
		   if (is_array($urls)) {
				foreach($urls as $p){
					$piclist[]  = is_array($p)?$p['attachment']:$p;
				}
			}
		}

		$marketprice = $goods['market_price'];
		$productprice= $goods['product_price'];
		$stock = $goods['total'];

		if($goods['hasoption']){
			//规格及规格项
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_spec WHERE goodsid = %s order by displayorder asc", $goodsid);
			$allspecs = $wpdb->get_results($sql, ARRAY_A);
			
			foreach ($allspecs as &$s) {
				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_spec_item WHERE `show`=1 and specid = %d order by displayorder asc", $s['id']);
				$s['items'] = $wpdb->get_results($sql, ARRAY_A);	
			}
			unset($s); 
		  
			//处理规格项
			$sql = $wpdb -> prepare("SELECT id,title,thumb,marketprice,productprice,costprice, point,stock,weight,specs FROM {$wpdb->prefix}shopping_goods_option WHERE goodsid = %s order by id asc", $goodsid);
			$options = $wpdb->get_results($sql, ARRAY_A);
			
			//排序好的specs
			$specs = array();
				//找出数据库存储的排列顺序
			if (count($options) > 0) {
				$specitemids = explode("_", $options[0]['specs'] );
				foreach($specitemids as $itemid){
					foreach($allspecs as $ss){
						 $items=  $ss['items'];
						 foreach($items as $it){
							 if($it['id']==$itemid){
								 $specs[] = $ss;
								 break;
							 }
						 }
					}
				}
			}
		}
		//修改成只显示默认价格，
		//        if (!empty($goods['hasoption'])) {
		//            $options = pdo_fetchall("SELECT * FROM " . tablename('shopping_goods_option') . " WHERE goodsid=:goodsid order by thumb asc,displayorder asc", array(":goodsid" => $goods['id']));
		//            foreach ($options as $o) {
		//                if ($marketprice >= $o['marketprice']) {
		//                    $marketprice = $o['marketprice'];
		//                }
		//                if ($productprice >= $o['productprice']) {
		//                    $productprice = $o['productprice'];
		//                }
		//                if ($stock <= $o['stock']) {
		//                    $stock = $o['stock'];
		//                }
		//            }
		//        }
		$sql = $wpdb -> prepare("SELECT `title`,`value` FROM {$wpdb->prefix}shopping_goods_param WHERE goodsid = %s order by displayorder asc", $goods['id']);
		$params = $wpdb->get_results($sql, ARRAY_A);
		if(!empty($goods['goodssn']))
			$params[] = array('title' => '商品编号', 'value' => $goods['goodssn']);
		if(!empty($goods['productsn']))
			$params[] = array('title' => '商品条码', 'value' => $goods['productsn']);

		$carttotal = $this->getCartTotal();
		$shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
		$_share = array(
			'title' => get_bloginfo('name','display') .' - '.$shopname.' - '.$goods['title'],
			'desc' => "点击查看".$goods['title'].'!');
		include $this->template('detail');
    }
	
	//product list page
	public function doMobilelist2() {

        global $_GPC, $_W, $wpdb;
		$gweid = $_GET['gweid'];
		$mid = $_W['fans']['mid'];
		//20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				$buyer = $buyer -> nickname;
			}
		}
		
        $pindex = max(1, intval($_GET["page"]));
        $psize = 10;
		$offset=($pindex - 1) * $psize;
        $condition = '';
        if (!empty($_GET['ccate'])) {
            $cid = intval($_GET['ccate']);
            $condition .= " AND ccate = '{$cid}'";
			$sql = $wpdb -> prepare("SELECT parentid FROM {$wpdb->prefix}shopping_category WHERE id = %d",intval($_GET['ccate']));
			$_GET['pcate'] = $wpdb->get_var($sql);
		} elseif (!empty($_GET['pcate'])) {
            $cid = intval($_GET['pcate']);
            $condition .= " AND pcate = '{$cid}'";
        }
        if (!empty($_GET['keyword']) && !isset($_POST['sou'])) {
            $condition .= " AND title LIKE '%%{$_GET['keyword']}%%' OR id = '{$_GET['keyword']}'";
        }
		
		//如果是post提交查询
		if(isset($_POST['sou']))
		{
		    $searchcontent = $_POST['sou'];
			$condition .= " AND title LIKE '%%{$searchcontent}%%' OR id = '{$searchcontent}'";
		}
		
        $sort = empty($_GET['sort']) ? 0 : $_GET['sort'];
        $sortfield = "displayorder asc";

        $sortb0 = empty($_GET['sortb0']) ? "desc" : $_GET['sortb0'];
        $sortb1 = empty($_GET['sortb1']) ? "desc" : $_GET['sortb1'];
        $sortb2 = empty($_GET['sortb2']) ? "desc" : $_GET['sortb2'];
        $sortb3 = empty($_GET['sortb3']) ? "asc" : $_GET['sortb3'];

        if ($sort == 0) {   //按时间
            $sortb00 = $sortb0 == "desc" ? "asc" : "desc";
            $sortfield = "createtime " . $sortb0;
            $sortb11 = "desc";
            $sortb22 = "desc";
            $sortb33 = "asc";
        } else if ($sort == 1) { //销量
            $sortb11 = $sortb1 == "desc" ? "asc" : "desc";
            $sortfield = "sales " . $sortb1;
            $sortb00 = "desc";
            $sortb22 = "desc";
            $sortb33 = "asc";
        } else if ($sort == 2) {
            $sortb22 = $sortb2 == "desc" ? "asc" : "desc";
            $sortfield = "viewcount " . $sortb2;
            $sortb00 = "desc";
            $sortb11 = "desc";
            $sortb33 = "asc";
        } else if ($sort == 3) { //价格
            $sortb33 = $sortb3 == "asc" ? "desc" : "asc";
            //自定义价格的始终排在最前面,ismanual=1表示手动输入金额
			$sortfield = "ismanual desc, market_price " . $sortb3;
            $sortb00 = "desc";
            $sortb11 = "desc";
            $sortb22 = "desc";
        }

        //搜索框内一直显示对应搜索的关键词,仅模板二使用此种方式
        if(isset($_POST['sou']))
		{
		    $soucontent = $_POST['sou'];
		}else{
			if(!empty($_GET['keyword'])){
				$soucontent = $_GET['keyword'];
	        }
		}
        
        $sorturl = $this->createMobileUrl('list2', array("gweid" => $gweid,"keyword" => $_GET['keyword'], "pcate" => $_GET['pcate'], "ccate" => $_GET['ccate']), true);
        if (!empty($_GET['isnew'])) {
            $condition .= " AND isnew = 1";
            $sorturl.="&isnew=1";
        }


        if (!empty($_GET['ishot'])) {
            $condition .= " AND ishot = 1";
            $sorturl.="&ishot=1";
        }
        if (!empty($_GET['isdiscount'])) {
            $condition .= " AND isdiscount = 1";
            $sorturl.="&isdiscount=1";
        }
        if (!empty($_GET['istime'])) {
            $condition .= " AND istime = 1 and " . time() . ">=timestart and " . time() . "<=timeend";
            $sorturl.="&istime=1";
        }

        $children = array();
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_category WHERE gweid = %s and enabled=1 ORDER BY parentid ASC, displayorder DESC", $gweid);
		$category = $wpdb->get_results($sql, ARRAY_A);
		$category_tmp = array();
		foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][$row['id']] = $row;
                unset($category[$index]);
            }
        }
        foreach ($category as $c_row) {
			$category_tmp[$c_row['id']] = $c_row;
        }

        $category = $category_tmp;
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0 AND (status = 0 OR (`status`=2 AND NOW()>timestart AND NOW()<timeend)) {$condition} AND NOT EXISTS(SELECT * FROM {$wpdb->prefix}shopping_category c WHERE c.id=ccate AND enabled = 0) ORDER BY {$sortfield} LIMIT {$offset},{$psize}", $gweid);
		$list = $wpdb -> get_results($sql, ARRAY_A);		
		foreach ($list as &$r) {
            if ($r['istime'] == 1) {
                $arr = $this->time_tran($r['timeend']);
                $r['timelaststr'] = $arr[0];
                $r['timelast'] = $arr[1];
            }
			//处理编辑器中的内容显示
			$r['content'] = $this->rule_content($r['content']);
        }
        unset($r);

		$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0  AND (status = 0 OR (`status`=2 AND NOW()>timestart AND NOW()<timeend)) {$condition} AND NOT EXISTS(SELECT * FROM {$wpdb->prefix}shopping_category c WHERE c.id=ccate AND enabled = 0)", $gweid);
		$total = $wpdb->get_var($sql);	
		$pager = pagination($total, $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
        $carttotal = $this->getCartTotal();
        $shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
		if($_GPC['pcate'])
			$categoryname = $category[$_GPC['pcate']]['name'];
		if($_GPC['ccate'])
			$categoryname = $category[$_GPC['ccate']]['name'];
		$_share = array(
			'title' => get_bloginfo('name','display') .' - '.$shopname .' - '.$categoryname ,
			'desc' => "点击访问".$shopname.'!');
        include $this->template('list2');
    }
	
	 public function doMobilelistmore_rec() {
        global $_GPC, $_W, $wpdb;
		$gweid = $_GET['gweid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 6;
		$offset = ($pindex - 1) * $psize;
        $condition = ' and isrecommend=1 ';
        $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0 AND (status = 0 OR (`status`=2 AND NOW()>timestart AND NOW()<timeend)) {$condition}  ORDER BY displayorder DESC, sales DESC LIMIT {$offset},{$psize}", $gweid);
		$list = $wpdb->get_results($sql, ARRAY_A);
		include $this->template('list_more');
    }

	//get more goods
    public function doMobilelistmore() {
        global $_GPC, $_W, $wpdb;
		$gweid = $_GET['gweid'];
		$mid = $_W['fans']['mid'];
		//20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				$buyer = $buyer -> nickname;
			}
		}
		
        $pindex = max(1, intval($_GPC['page']));
        $psize = 6;
		$offset = ($pindex - 1) * $psize;
        $condition = '';
        if (!empty($_GPC['ccate'])) {
            $cid = intval($_GPC['ccate']);
            $sql = $wpdb -> prepare("SELECT parentid FROM {$wpdb->prefix}shopping_category WHERE id = %d", intval($_GPC['ccate']));
			$_GPC['pcate'] = $wpdb->get_var($sql);
            $sql = $wpdb -> prepare("SELECT parentid FROM {$wpdb->prefix}shopping_category WHERE id = %d", intval($_GPC['ccate']));
			$cate = $wpdb->get_var($sql);
			if(!empty($cate['parentid'])){
                $condition.=" and ccate={$cid}";
            }
            else{
                 $condition.=" and pcate={$cid}";
            }
        } elseif (!empty($_GPC['pcate'])) {
            $cid = intval($_GPC['pcate']);
            $condition .= " AND pcate = '{$cid}'";
        }
        $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and gweid = %s and deleted=0 AND status = 0 {$condition}  ORDER BY displayorder DESC, sales DESC LIMIT {$offset},{$psize}", $gweid);
		$list = $wpdb->get_results($sql, ARRAY_A);	
		include $this->template('list_more');
    }
	
	
	//list all categories
	public function doMobileallcategories() {
        global $_GPC, $_W, $wpdb;
		$gweid = $_GET['gweid'];
		$mid = $_W['fans']['mid'];
		//20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				$buyer = $buyer -> nickname;
			}
		}
		
		//get all categories
        $children = array();
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_category where gweid=%s ORDER BY parentid ASC, displayorder DESC",$gweid);
		$category = $wpdb->get_results($sql,ARRAY_A);
		
		foreach ($category as $index => $row) {
			if (!empty($row['parentid'])) {
				$children[$row['parentid']][] = $row;
				unset($category[$index]);
			}
		}
		$shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));

		$_share = array(
			'title' => get_bloginfo('name','display') .' - '.$shopname .' - 全部商品' ,
			'desc' => "点击访问".$shopname.'!');
        include $this->template('allcategories');
    }
	//cart function
	public function doMobileMyCart() {
        global $_W, $_GPC, $wpdb;
		//先判断在不同浏览器上的用户身份
		$gweid =$_GET['gweid'];
		$fromuser = $_W['fans']['from_user'];
		$this -> verifyUser($gweid);
		$mid = $_W['fans']['mid'];
		//20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				/*point*/
				$allpoint = $buyer -> point;
				$costpoint = $buyer -> point_cost;
				$point=intval($allpoint);
				/*point end*/
				$buyer = $buyer -> nickname;
			}
		}

		//如果购物车中的商品规格发生变化后，购物车中需要相应的删除
    	$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_cart WHERE gweid = %s AND {$this -> get_from_info_sql}", $gweid);
		$list = $wpdb->get_results($sql, ARRAY_A);
		if (!empty($list)) {
            foreach ($list as $litem) {
            	$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE gweid = %s AND id = %s", $gweid,$litem['goodsid']);
				$goodsinfo = $wpdb->get_results($sql, ARRAY_A);
				
				if (!empty($goodsinfo)) {
        			foreach ($goodsinfo as $info) {
        				$status = $info['status'];
        			}
    				if($status == 1){  //如果商品下架了

						$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
					}
					if($status == 2){ //如果是限时的商品
						$currenttime = time();  //当前时间戳
						if(strtotime($litem['timeend']) < $currenttime){
							$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
						}
					}
        		}else{  //如果购物车中有的商品在goods表中不存在，即该商品被删除了，则该条记录也应该被删除
        			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
        		}

				
            	if($litem['optionid'] == 0){  //如果购物车中该条记录对应的是没有使用商品规格的,后来改为使用商品规格	
					if (!empty($goodsinfo)) {
            			foreach ($goodsinfo as $info) {
            				$hasoption = $info['hasoption'];
            			}
            			if($hasoption == 1){  //现在该商品改为使用商品规格，则该条记录需要被删除
            				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
            			}
            		}else{  //如果购物车中有的商品在goods表中不存在，即该商品被删除了，则该条记录也应该被删除
            			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
            		}
            	}else{  //如果一开始商品有规格，后来改为没有规格，则该条记录也删除
            		
            		if (!empty($goodsinfo)) {
            			foreach ($goodsinfo as $info) {
            				$hasoption = $info['hasoption'];
            			}
            			if($hasoption == 0){  //现在该商品改为不再使用商品规格，则该条记录需要被删除
            				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
            			}else{   //如果原有的商品使用了规格，现在商品仍然有规格，需要比较下规格是否有大的变化
            				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_cart g1, {$wpdb->prefix}shopping_goods_option g2 WHERE g1.optionid = g2.id and {$this -> getfrominfo('g1.')} and g1.gweid = %s AND g1.goodsid = %s and g1.optionid = %s ", $gweid,$litem['goodsid'],$litem['optionid']);
							$goodsninfo = $wpdb->get_results($sql, ARRAY_A);
							//如果规格前后变化比较大，则原有的规格会被删除(比如原来只有颜色这一种规格，后来加上了大小，原来的规格在goods_option表中就删除了)
							//多个规格的gweid,mid,openid,goodsid等都是一样的
							if (empty($goodsninfo)) {
								$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s and optionid = %s",$gweid, $litem['goodsid'], $litem['optionid']) );
							}
            			}
            		}else{  //如果购物车中有的商品在goods表中不存在，即该商品被删除了，则该条记录也应该被删除
            			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
            		}
            	}
            }
        }
		
        $op = $_GET['op'];
        if ($op == 'add') {
            $goodsid = $_GET['id'];
			$total = intval($_GET['total']);
            $total = empty($total) ? 1 : $total;
            $optionid = intval($_GET['optionid']);
			$pointcheck = intval($_GET['pointcheck']);/*point update*/	
			$sql = $wpdb -> prepare("SELECT ismanual FROM {$wpdb->prefix}shopping_goods WHERE isshopping = 1 and id = %s",$goodsid);
			$goodsmanual = $wpdb->get_row($sql,ARRAY_A);
			$sql = $wpdb -> prepare("SELECT id, type, total,market_price,ismanual, maxbuy,point FROM {$wpdb->prefix}shopping_goods WHERE id = %s",$goodsid);/*point update*/
			$goods = $wpdb->get_row($sql,ARRAY_A);
			if (empty($goods)) {
                $result['message'] = '抱歉，该商品不存在或是已经被删除！';
                message($result, '', 'ajax');
            }
            $marketprice = $goods['market_price'];
			$point=$goods['point'];/*point update*/
            if (!empty($optionid)) {
                $sql = $wpdb -> prepare("SELECT marketprice,point FROM {$wpdb->prefix}shopping_goods_option WHERE id = %d",$optionid);/*point update*/
				$option = $wpdb->get_row($sql,ARRAY_A);
				if (!empty($option)) {
                    $marketprice = $option['marketprice'];
					$point=$option['point'];/*point update*/
                }
            }
			if($goodsmanual['ismanual'] == 1)   //如果是买家输入，则获取对应的输入金额
			{
				$marketprice = $_GET['manual_price'];
				$point=0;/*point update*/
			}
			
			/*point update 0表示添加购物车时不使用积分*/
			if($pointcheck==0){
				$point_insert=0;
			}else{
				$point_insert=intval($point);
			}
			/*point END*/
			
			$sql = $wpdb -> prepare("SELECT id, total, marketprice,point,ispoint FROM {$wpdb->prefix}shopping_cart WHERE gweid = %s and {$this -> get_from_info_sql} and goodsid = %d  and optionid= %d ",$gweid, $goodsid, $optionid);/*point update*/
			$row = $wpdb->get_row($sql,ARRAY_A);
			
			if ($row == false) {
                //不存在
                $data = array(
					'gweid' => $gweid,
					'mid' => $mid,
					'openid' => $fromuser,
                    'goodsid' => $goodsid,
                    'goodstype' => $goods['type'], 
                    'total' => intval($total),
                    'optionid' => intval($optionid),
					'point' => intval($point),/*point update*/
					'ispoint' => intval($point_insert),/*point update*/
					'marketprice' => floatval($marketprice)
                );
				$wpdb -> insert("{$wpdb->prefix}shopping_cart",$data);
            } else {
                //累加最多限制购买数量
                $t = $total + $row['total'];
                if (!empty($goods['maxbuy'])) {
                    if ($t > $goods['maxbuy']) {
                        $t = $goods['maxbuy'];
                    }
                }
                //存在
                $data = array(  
                    'total' => intval($t),
                    'optionid' => intval($optionid),
					'point' => intval($point),/*point update*/
					'ispoint' => intval($point_insert),/*point update*/
					'marketprice' => floatval($marketprice)
                );
				
				if($goodsmanual['ismanual'] == 1)   //如果是买家输入,相当于数量不变，价格相加
				{
				    $price = $_GET['manual_price'];
					$data = array(  
						'total' => 1,
						'optionid' => intval($optionid),
						'point' => 0,/*point update*/
						'ispoint' => 0,/*point update*/
						'marketprice' => floatval($row['marketprice'] + $price)
					);
				}
				$wpdb->update("{$wpdb -> prefix}shopping_cart", $data, array('id' => $row['id']));
            }

            //返回数据
            $carttotal = $this->getCartTotal();

            $result = array(
                'result' => 1,
                'total' => $carttotal
            );
            die(json_encode($result));
        } else if ($op == 'clear') {
            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s ",$gweid ) );
			die(json_encode(array("result" => 1)));
        } else if ($op == 'remove') {
            $id = intval($_GPC['id']);
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and id = %d", $gweid, $id ) );
			die(json_encode(array("result" => 1, "cartid" => $id)));
        } else if ($op == 'update') {
            $id = intval($_GET['id']);
            $num = intval($_GET['num']);
			$ispoint = intval($_GET['ispoint']);
			$wpdb->update("{$wpdb -> prefix}shopping_cart", array('total' => $num,'ispoint'=>$ispoint), array('id' => $id));//point update
            die(json_encode(array("result" => 1)));
        } else if ($op == 'updateprice') {
            $id = intval($_GPC['id']);
            $marketprice = $_GET['marketprice'];
			$wpdb->update("{$wpdb -> prefix}shopping_cart", array('marketprice' => $marketprice,'ispoint'=>0,'point'=>0), array('id' => $id));//point update
            die(json_encode(array("result" => 1)));
        } else {

        	//如果购物车中的商品规格发生变化后，购物车中需要相应的删除
        	$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_cart WHERE gweid = %s AND {$this -> get_from_info_sql}", $gweid);
			$list = $wpdb->get_results($sql, ARRAY_A);
			
			//需要判断下相应购物车中的商品是不是手动输入金额的
			$totalprice = 0;
			$totalpoint = 0;//point update
            if (!empty($list)) {
                foreach ($list as &$item) {
                    $sql = $wpdb -> prepare("SELECT title, thumb, market_price, unit, ismanual, total, maxbuy,point FROM {$wpdb->prefix}shopping_goods WHERE id = %d limit 1", $item['goodsid']);/*point update*/
					$goods = $wpdb->get_row($sql,ARRAY_A);
                    $sql = $wpdb -> prepare("SELECT title,marketprice,stock,point FROM {$wpdb->prefix}shopping_goods_option WHERE id = %d limit 1", $item['optionid']);/*point update*/
					$option = $wpdb->get_row($sql,ARRAY_A);
					if ($option) {
                        $goods['title'] = $goods['title'];
                        $goods['optionname'] = $option['title'];
                        $goods['market_price'] = $option['marketprice'];
                        $goods['total'] = $option['stock'];
						$goods['point'] = $option['point'];/*point update*/
                    }
					/*point update*/
					if($goods['ismanual'] == 1){
						$goods['point'] = 0;
						$goods['ispoint'] = 0;
					}else{
						$goods['ispoint'] =$item['ispoint'];
					}
					/*point END*/
                    $item['goods'] = $goods;
                    $item['totalprice'] = (floatval($goods['market_price']) * intval($item['total']));
					$item['totalpoint'] = (intval($goods['point']) * intval($item['total']));//point update
					if($goods['ismanual'] == 1){    //如果该商品是买家输入,该商品的总额即是marketprice中存的值
						$item['totalprice'] = $item['marketprice'];
					}
                    $totalprice += $item['totalprice'];//没起作用
					$totalpoint += $item['totalpoint'];//point update
                }
                unset($item);
            }
            $shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
			$_share = array(
				'title' => get_bloginfo('name','display') .' - '.$shopname,
				'desc' => "点击访问".$shopname.'!',
				'link' => $this -> createMobileUrl('list', array())
				);
            include $this->template('cart');
        }
    }
	
	//结算
	public function doMobileConfirm() {
        global $_W, $_GPC, $wpdb;
        //先判断在不同浏览器上的用户身份
        $gweid = $_GET['gweid'];
		$this -> verifyUser($gweid);
		
		$weixin = new WeixinPay($gweid);
		$mid = $_W['fans']['mid'];
		//20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				/*point*/
				$allpoint = $buyer -> point;
				$costpoint = $buyer -> point_cost;
				$point=intval($allpoint);
				/*point end*/
				$buyer = $buyer -> nickname;
			}
		}
		
		//共享收货地址
		$data = array();
		$addr=$weixin->addrsign($_SESSION['addaccesstoken']);
		$data['appId'] = $addr['appid'];
		$data['timeStamp'] = $addr['timestamp'];
		$data['nonceStr'] = $addr['noncestr'];
		$data['addrSign'] = $addr['addrsign'];
		
        $totalprice = 0;
		$pointtotal=0;//point update
        $allgoods = array();

        //$id = intval($_GET['id']);   id在shopping_goods表中是字符串类型的
		$id = $_GET['id'];
        $optionid = intval($_GET['optionid']);
        $total = intval($_GET['total']);
        if (empty($total)) {
            $total = 1;
        }
		
        $direct = false; //是否是直接购买
        $returnurl = ""; //当前连接

        if (!empty($id)) {			
        	if(!empty($_GET['modifiedtotal'])){
        		$total = $_GET['modifiedtotal'];
        	}
        		
			$sql = $wpdb -> prepare("SELECT id,isdelivery, thumb,title,weight,market_price,ismanual,total,type,totalcnf,sales,unit,timestart,timeend,isfreedelivery,freedeliverycount,maxbuy,point FROM {$wpdb->prefix}shopping_goods WHERE id = %s limit 1", $id);/*point update*/
			$item = $wpdb->get_row($sql,ARRAY_A);
            if ($item['istime'] == 1) {
                if (time() > $item['timeend']) {
                    message('抱歉，商品限购时间已到，无法购买了！', referer(), "error");
                }
            } 
			//如果是自定义金额，则获取传递过来的金额
            if (!empty($optionid)) {
				$sql = $wpdb -> prepare("SELECT title,marketprice,weight,stock,point FROM {$wpdb->prefix}shopping_goods_option WHERE id = %d limit 1", $optionid);/*point update*/
				$option = $wpdb->get_row($sql,ARRAY_A);
                if ($option) {
                    $item['optionid'] = $optionid;
                    $item['title'] = $item['title'];
                    $item['optionname'] = $option['title'];
                    $item['market_price'] = $option['marketprice'];
                    $item['weight'] = $option['weight'];
					$item['point'] = $option['point'];/*point update*/
                }
            }
			/*point update*/
			$pointcheck = intval($_GET['pointcheck']);/*point update*/
			
			if($_GET['ismanual'] == 1){
				$item['point'] = 0;
				$item['ispoint'] = 0;
			}else{
				$item['ispoint'] =intval($pointcheck);
			}
			/*point END*/
					
            if($_GET['ismanual'] == 1){
				if(!empty($_POST['order_add'])){
					$_POST['total'] = 1;
					$item['market_price'] = $_POST['manual_price'];
				}else
					$item['market_price'] = $_GET['manual_price'];
			}
            $item['stock'] = $option?$option['stock']:$item['total'];
            $item['total'] = !empty($_POST['order_add'])?$_POST['total']:$total;
            $item['totalprice'] = $item['total'] * $item['market_price'];
			$item['totalpoint'] = $item['total'] * $item['point'];/*point update*/
            $allgoods[] = $item;
			
			/*point update*/
			if(intval($item['ispoint'])==0 || intval($item['point'])==0 || $_GET['ismanual'] == 1){
				 $totalprice+= $item['totalprice'];
			}else{
				 $pointtotal+= $item['totalpoint'];
			}
           
			if ($item['type'] == 1) {
                $needdispatch = true;
            }
            $good_total_array = array($id => $item['total']);
            $direct = true;
            $returnurl = $this->createMobileUrl("confirm", array("id" => $id, "gweid" => $gweid, "optionid" => $optionid, "total" => $total));
		}
        if (!$direct) {
            //如果不是直接购买（从购物车购买）
        	//将购物车中的商品，如果有已经删除的商品或者下架的或者限时购买超时的，在购物车中提前删除，则直接在结算的时候不再显示
        	//如果购物车中的商品规格发生变化的，购物车中需要相应的删除
	    	$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_cart WHERE gweid = %s AND {$this -> get_from_info_sql}", $gweid);
			$listn = $wpdb->get_results($sql, ARRAY_A);
			if (!empty($listn)) {
	            foreach ($listn as $litem) {
	            	$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE gweid = %s AND id = %s", $gweid,$litem['goodsid']);
					$goodsinfo = $wpdb->get_results($sql, ARRAY_A);
					if (!empty($goodsinfo)) {
	        			foreach ($goodsinfo as $info) {
	        				$status = $info['status'];
	        			}
	    				if($status == 1){  //如果商品下架了
							$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
						}
						if($status == 2){ //如果是限时的商品
							$currenttime = time();  //当前时间戳
							if(strtotime($litem['timeend']) < $currenttime){
								$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
							}
						}
	        		}else{  //如果购物车中有的商品在goods表中不存在，即该商品被删除了，则该条记录也应该被删除
	        			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
	        		}

					//如果是商品的规格发生变化
					if($litem['optionid'] == 0){  //如果购物车中该条记录对应的是没有使用商品规格的,后来改为使用商品规格	
						if (!empty($goodsinfo)) {
	            			foreach ($goodsinfo as $info) {
	            				$hasoption = $info['hasoption'];
	            			}
	            			if($hasoption == 1){  //现在该商品改为使用商品规格，则该条记录需要被删除
	            				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
	            			}
	            		}else{  //如果购物车中有的商品在goods表中不存在，即该商品被删除了，则该条记录也应该被删除
	            			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
	            		}
	            	}else{  //如果一开始商品有规格，后来改为没有规格，则该条记录也删除
	            		
	            		if (!empty($goodsinfo)) {
	            			foreach ($goodsinfo as $info) {
	            				$hasoption = $info['hasoption'];
	            			}
	            			if($hasoption == 0){  //现在该商品改为不再使用商品规格，则该条记录需要被删除
	            				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
	            			}else{   //如果原有的商品使用了规格，现在商品仍然有规格，需要比较下规格是否有大的变化
	            				$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_cart g1, {$wpdb->prefix}shopping_goods_option g2 WHERE g1.optionid = g2.id and {$this -> getfrominfo('g1.')} and g1.gweid = %s AND g1.goodsid = %s and g1.optionid = %s ", $gweid,$litem['goodsid'],$litem['optionid']);
								$goodsninfo = $wpdb->get_results($sql, ARRAY_A);
								//如果规格前后变化比较大，则原有的规格会被删除(比如原来只有颜色这一种规格，后来加上了大小，原来的规格在goods_option表中就删除了)
								//多个规格的gweid,mid,openid,goodsid等都是一样的
								if (empty($goodsninfo)) {
									$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s and optionid = %s",$gweid, $litem['goodsid'], $litem['optionid']) );
								}
	            			}
	            		}else{  //如果购物车中有的商品在goods表中不存在，即该商品被删除了，则该条记录也应该被删除
	            			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s and goodsid = %s",$gweid, $litem['goodsid']) );
	            		}
	            	}
	            }
	        }

        	//下面是处理最后在购物车中存在的商品
			$sql = $wpdb -> prepare("SELECT *, id as cartid FROM {$wpdb->prefix}shopping_cart WHERE gweid = %s and {$this -> get_from_info_sql}", $gweid);
			$list = $wpdb->get_results($sql, ARRAY_A);
			if (!empty($list)) {
                foreach ($list as &$g) {
                    $sql = $wpdb -> prepare("SELECT id,thumb,title,weight,market_price,total,type,totalcnf,ismanual,sales,unit,isdelivery,isvipprice, vip_price,isfreedelivery,freedeliverycount,maxbuy,point FROM {$wpdb->prefix}shopping_goods WHERE id = %s limit 1", $g['goodsid']);/*point update*/
					$item = $wpdb->get_row($sql,ARRAY_A);

                    $sql = $wpdb -> prepare("SELECT title,marketprice,weight,stock,point FROM {$wpdb->prefix}shopping_goods_option WHERE id = %d limit 1", $g['optionid']);/*point update*/
					$option = $wpdb->get_row($sql,ARRAY_A);
					
					//??做什么用
					$sql = $wpdb -> prepare("SELECT `goodsid`,sum(total) as total,ispoint FROM {$wpdb->prefix}shopping_cart WHERE gweid = %s and {$this -> get_from_info_sql} GROUP BY `goodsid`", $gweid);/*point update*/
                    $good_total = $wpdb->get_results($sql,ARRAY_A);

                    $good_total_array = array();
                    foreach($good_total as $good_total_item){
                    	$good_total_array[$good_total_item['goodsid']] = $good_total_item['total'];
                    }
                    unset($good_total);

                    if ($option) {
                        $item['optionid'] = $g['optionid'];
                        $item['title'] = $item['title'];
                        $item['optionname'] = $option['title'];
                        $item['market_price'] = $option['marketprice'];
                        $item['weight'] = $option['weight'];
						$item['point'] = $option['point'];/*point update*/
                    }
					
                    $item['stock'] = $option?$option['stock']:$item['total'];
                    $item['total'] = $g['total'];
                   
 				    $item['totalprice'] = $g['total'] * $item['market_price'];
					/*point update*/
					$item['totalpoint'] = $g['total'] * $item['point'];
					
					/*point update*/
					if($item['ismanual'] == 1){
						$item['total'] = 1;//point update
						$item['point'] = 0;
						$item['ispoint'] = 0;
					}else{
						$item['ispoint'] =intval($g['ispoint']);//购物车中是否选中积分兑换
					}
					/*point END*/
					if($item['ismanual'] == 1){   //如果是买家输入的金额
						$item['totalprice'] = $g['marketprice'];
					}
					//获取cartid
					$item['cartid'] = $g['cartid'];
                    $allgoods[] = $item;
					
					/*point update*/
					if(intval($item['ispoint'])==0 || intval($item['point'])==0 || $item['ismanual'] == 1){
						$totalprice+= $item['totalprice'];
					}else{
						$pointtotal+= $item['totalpoint'];
					}
					
					
                    if ($item['type'] == 1) {
                        $needdispatch = true;
                    }
                }
                unset($g);
            }
			$returnurl = $this->createMobileUrl("confirm", array('gweid' => $gweid));
        }

        if (count($allgoods) <= 0) {
			header("location: " . $this->createMobileUrl('myorder',  array('gweid' => $gweid)));
            exit();
        }

        //配送方式
		//如果不是微信浏览器，则不出现微支付的相关配送方式
		$weight = 0;
		$needdispatchpay = FALSE;
		$needdispatch = FALSE;
	    foreach ($allgoods as $g) {
	    	if($g['type'] == 1 && $g['isdelivery'] != 1)
        		$needdispatch = TRUE;
        	if($g['type'] == 2 || $g['isdelivery'] == 1 || ($g['isfreedelivery'] == 1 && $g['freedeliverycount'] <= $good_total_array[$g['id']]))
        		continue;
        	$weight+=$g['weight'] * $g['total'];
        	$needdispatchpay = TRUE;

        }
        //这些查询都不会出现dispid=-1的
        if($this -> is_weixin() && $weixin -> isConfigAvailable() && $needdispatch)
			$dispatch_sql_where = "AND dispid != -1";//point update 不使用积分设置，不显示
		if(!($this -> is_weixin() && $weixin -> isConfigAvailable()) && $needdispatch)
			$dispatch_sql_where = "AND (dispatchtype = 1 OR dispatchtype = 2 OR dispid = -2) AND dispid !=-1";//point update fix bug add "and dispid !=-1"
		if(($this -> is_weixin() && $weixin -> isConfigAvailable()) && !$needdispatch)
			$dispatch_sql_where = "AND dispatchtype = 0";
		if(!($this -> is_weixin() && $weixin -> isConfigAvailable()) && !$needdispatch)
			$dispatch_sql_where = "AND 1=0";
		
		$sql = $wpdb -> prepare("SELECT id,dispatchname,dispatchpricetype,dispatchprice,dispatchtype,firstprice,firstweight,secondprice,secondweight,region,dispid FROM {$wpdb->prefix}shopping_dispatch WHERE gweid = %s {$dispatch_sql_where}", $gweid);//point update

		$dispatch = $wpdb->get_results($sql, ARRAY_A);

		foreach ($dispatch as &$d) {
			if(!$needdispatchpay){
				$d['price'] = 0;
				continue;
			}
			if(!$d['dispatchpricetype']){
				$d['price'] = $d['dispatchprice'];
				continue;
			}
            $price = 0;
            if ($weight <= $d['firstweight']) {
                $price = $d['firstprice'];
            } else {
                $price = $d['firstprice'];
                $secondweight = $weight - $d['firstweight'];
                if ($secondweight % $d['secondweight'] == 0) {
                    $price+= (int) ( $secondweight / $d['secondweight'] ) * $d['secondprice'];
                } else {
                    $price+= (int) ( $secondweight / $d['secondweight'] + 1 ) * $d['secondprice'];
                }
            }
            $d['price'] = $price;
        }
        unset($d);
        if(isset($_GET['action']) && $_GET['action'] == 'orderdispatch'){
        	$orderdispatch = array();
        	if(is_array($dispatch)) { foreach($dispatch as $dispatch_index => $d) {
				if(!empty($_GET['province']) && $d['dispatchpricetype']){
        			$_GET['province'] = str_replace(array('特区','区','省','市'), array('','','','') , $_GET['province']);
        			if(stripos($d['region'], $_GET['province']) === FALSE){
        				continue;
        			}
	       		}
        		$orderdispatch[] = array(
        			'id' => $d['id'],
        			'price' => $d['price'],
					'dispatchtype' => $d['dispatchtype'],//point update
					'dispid' => $d['dispid'],//point update
        			'dispatchname' => $d['dispatchname']
        			);
        	}}
        	echo json_encode($orderdispatch);
        	exit;
        }

        $carttotal = $this->getCartTotal();
        //获取会员的相关信息
		$profile = $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wechat_member WHERE mid = %s LIMIT 1",$mid),ARRAY_A);
		
		if (!empty($id)) {
            
			$sql = $wpdb -> prepare("SELECT id,isdelivery FROM {$wpdb->prefix}shopping_goods WHERE id = %s limit 1", $id);
			$isdelivery = $wpdb->get_row($sql,ARRAY_A);

		}
		if($needdispatch){ //如果需要发货
				$address = $wpdb -> get_var( "SELECT address FROM  {$wpdb->prefix}shopping_order WHERE {$this -> get_from_info_sql} and time_start in (select max(time_start) from {$wpdb->prefix}shopping_order WHERE {$this -> get_from_info_sql} and address_name !='' )");
				$order_address = json_decode($address,true);
			}
		//通过ajax提交
		if(!empty($_POST['order_add'])){
		    $address=$_POST['address'];
			if($needdispatch== FALSE){
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
			$trade_type = "JSAPI";   //weshopping对应的都是JSAPI
			
			//是否自提
			$sendtype=1;
        
            //商品价格
            $goodsprice = 0;
			$goodspoint = 0;
            foreach ($allgoods as $row) {
                if ($row['stock'] != -1 && $row['total'] > $row['stock'] && $row['ismanual'] != 1) {//point update(增加ismanual验证)
                    message('抱歉，“' . $row['title'] . '”库存不足！还剩'.$row['stock'], $this->createMobileUrl('confirm'), 'error');
                }
                /*point update*/
				//非购物车购买，从详情页面跳转时选中积分，但提交订单时未选中积分
				if ($direct && $row['ismanual'] != 1){
					$row['ispoint']=$_POST['selectpoint'];
				}
				
				if(intval($row['ispoint'])==0 || intval($row['point'])==0 || $row['ismanual'] == 1){
					$goodsprice+= $row['totalprice'];//old
				}else{
					$goodspoint+= $row['totalpoint'];
				}
			}
            //运费
            $dispatchid = intval($_POST['dispatch']);
            $dispatchprice = 0;
			$dispid=0;//point update 是否积分-1 不是积分 -2 是积分 其余不是积分
            foreach ($dispatch as $d) {
                if ($d['id'] == $dispatchid) {
                    $dispatchprice = $d['price'];//point update 有可能是积分
                    $sendtype = $d['dispatchtype'];
					$dispid = $d['dispid'];//point update 是否积分-1 不是积分 -2 是积分 其余不是积分
                }
            }
			
			//如果选择自提或者货到付款的方式，则对应的trade_state状态为“NEEDNOTPAY”
			if($sendtype == 1){
				$trade_state = "CASHONDELIVERY";
			}
			if($sendtype == 2){
				$trade_state = "SELFDELIVERY";
			}
			//payment_type=1表示微信支付，2为自提，0为货到付款
			$sql = $wpdb -> prepare("SELECT dispatchtype FROM {$wpdb->prefix}shopping_dispatch WHERE id = %d limit 1", $dispatchid);
			$dispatch_type = $wpdb->get_row($sql,ARRAY_A);
			if(!empty($dispatch_type)){
				$dtype = $dispatch_type['dispatchtype'];
				if($dtype == 1)  //dispatchtype为1表示货到付款，2表示自提
					$payment_type="0";
				if($dtype == 2)
					$payment_type="2";
			}
			
			/*point update*/
			$point_all=0;
			if($dispid==-2){//则物流使用了积分			
				$point_all=intval($goodspoint)+intval($dispatchprice);
				$fee=floatval($goodsprice);
			}else{
				$point_all=intval($goodspoint);
				$fee=floatval($goodsprice + $dispatchprice);//old
			}
			/*point update end*/
			
			//入库时生成订单号，非页面加载时生成
			$out_trade_no=time().rand(111111,999999);
            $data = array(
				'out_trade_no' => $out_trade_no,
				'gweid' => $gweid,
				'openid' => $this -> fromuser,
				'openid_name' => $this -> wechatname,
                'mid' => $mid,
                'isshopping' => 1, //微商城的订单此字段设置为1，微支付默认为0
				'payment_type' => $payment_type,  //此字段代表?
                'fee' =>$fee ,  //商品总金额+运费(可能)
				'pointall' =>$point_all ,  //商品花费总积分+运费(可能)+会员需要减的积分和point update 
                'goodsprice' => floatval($goodsprice),//商品总金额（不包括运费）
				'goodspoint' => intval($goodspoint),//商品总积分（不包括运费）
				'dispatchprice' => floatval($dispatchprice),//point update 物流费用或物流积分
				'isdispatchpoint' => $dispid,//point update 物流是否积分-1 不是积分 -2 是积分 其余不是积分
				'address' => $orderaddress,
				'address_name' => $address['0'],
                'dispatch' => intval($dispatchid),
				'trade_state' => $trade_state,
                'remark' => $_POST['remark']
            );
			/* 
			$orderid = $wpdb->insert_id; 先插入order_goods表再插入order表*/
            //插入订单商品
			$flag = true;
            foreach ($allgoods as $row) {
                if (empty($row)) {
                    continue;
                }
				//如果是手动输入金额，需要更新order_goods表中的goods_price字段
				if($row['ismanual']=='1'){
					$row['market_price'] = $row['totalprice'];
				}
				if ($direct && $row['ismanual'] != 1){
					$row['ispoint']=$_POST['selectpoint'];
				}
				$d = array(
					'out_trade_no' => $out_trade_no,
					'goods_id' => $row['id'],
					'goods_title' => $row['title'],
					'goods_thumb' => $row['thumb'],
					'goods_price' => floatval($row['market_price']),
					'goods_vipprice' => floatval($row['vip_price']),
                    'total' => $row['total'],
					'total_price' => floatval($row['totalprice']),
					'total_point' => $row['totalpoint'],//point update 积分总数
					'ispoint' => $row['ispoint'],//point update 用户是否选择积分购买
					'point' => $row['point'],//point update 商品是否积分购买（以及设置的积分数,非0则表示商品所用积分）
					'isdelivery' => intval($row['isdelivery']),
					'ismanual' => intval($row['ismanual']),
					'isvipprice' => intval($row['isvipprice']),
                    'optionid' => intval($row['optionid']),
					'optionname' => $row['optionname']
                );
                $sql = $wpdb -> prepare("SELECT title FROM {$wpdb->prefix}shopping_goods_option WHERE id = %d limit 1", $row['optionid']);
				$o = $wpdb->get_row($sql,ARRAY_A);
				if(!empty($o)){
                    $d['optionname'] = $o['title'];
                }
                //先插入order_goods表
				if($row['ismanual']=='1'){
					$send_type="NOTDELIVERY";
					$insert=$wpdb -> insert("{$wpdb->prefix}shopping_order_goods",$d);
				}else{
					$send_type="DELIVERY";
					$insert=$wpdb -> insert("{$wpdb->prefix}shopping_order_goods",$d);
				}
				if($insert===false){//插入order goods表失败	
				    $flag = false;  //只要商品有插入失败的，就不能插入order_wepay表中
					$hint = array("status"=>"error","message"=>"出现错误");
					echo json_encode($hint);
					exit;
				}
				//插入order goods表失败	
            }
			//order_goods表中可能插入多条记录
			//插入order_wepay
			if($flag){
				$insert=$wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->prefix}shopping_order_wepay(out_trade_no,fee_type,send_type,trade_type)VALUES (%s,%s,%s,%s)",$out_trade_no,$fee_type,$send_type,$trade_type));
				if($insert===false){
					$hint = array("status"=>"error","message"=>"出现错误");
					echo json_encode($hint);
					exit;
				}else{
					$insert = $wpdb -> insert("{$wpdb->prefix}shopping_order",$data);
					
					if($insert===false){
						$hint = array("status"=>"error","message"=>"出现错误");
						echo json_encode($hint);
						exit;
					}else{
					    //数据正确插入到数据库后，才可以清空购物车以及变更商品库存
						//清空购物车
						if (!$direct) {
							$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopping_cart WHERE {$this -> get_from_info_sql} and gweid = %s ",$gweid ) );
						}
						//变更商品库存
						$this->setOrderStock($out_trade_no,'place');  //购物车中的商品具有相同的订单号
						
						/*point update*/
						if($allpoint-$point_all<0){
							$upoint=0;
						}else{
							$upoint=intval($allpoint)-intval($point_all);
						}
						$wpdb -> update("{$wpdb -> prefix}wechat_member",array('point' => $upoint,'point_cost' => $costpoint+$point_all),array('mid' => $mid));
						/*point update end*/
						
						$hint = array("status"=>"success","message"=>"订单提交成功","url"=>$this->createMobileUrl('pay',array('gweid' => $gweid,'orderid' => $out_trade_no)));
						echo json_encode($hint);
						exit;
					}
				
				}
			} 
		}
	    $shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
		$_share = array(
			'title' => get_bloginfo('name','display') .' - '.$shopname,
			'desc' => "点击访问".$shopname.'!',
			'link' => $this -> createMobileUrl('list', array())
			);
		include $this->template('confirm', array('gweid' => $gweid));
 
    }
	//订单
	public function doMobileMyOrder() {
        global $_W, $_GPC, $wpdb;
        //先判断在不同浏览器上的用户身份
        $gweid = $_GET['gweid'];
		$this -> verifyUser($gweid);
		
		$mid = $_W['fans']['mid'];
		//20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				/*point*/
				$allpoint = $buyer -> point;
				$costpoint = $buyer -> point_cost;
				$point=intval($allpoint);
				/*point end*/
				$buyer = $buyer -> nickname;
			}
		}
		
        $op = $_GET['op'];
		
        if ($op == 'confirm') {
			$orderid = $_GET['orderid'];
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE gweid = %s AND {$this -> get_from_info_sql} and out_trade_no = %s", $gweid, $orderid);
			$order = $wpdb->get_row($sql,ARRAY_A);
			if (empty($order)) {
                message('抱歉，您的订单不存或是已经被取消！', $this->createMobileUrl('myorder'), 'error');
            }
			$delivery_status = $wpdb->update( $wpdb->prefix.'shopping_delivery', array('delivery_status'=>'2'),array('out_trade_no' => $orderid),array('%s'));
			
			if($delivery_status!=false){
				//还需要更新order表中的状态
				if($order['trade_state'] == "SELFDELIVERY"){
					$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>'SELFDELIVERY_CLOSED'),array('gweid' => $gweid,'out_trade_no' => $orderid),array('%s', '%s'));
				}
				if($order['trade_state'] == "CASHONDELIVERY"){
					$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>'CASHONDELIVERY_CLOSED'),array('gweid' => $gweid,'out_trade_no' => $orderid),array('%s', '%s'));
				}
				message('确认收货完成！', $this->createMobileUrl('myorder'), 'success');
			}else{
				message('确认收货失败！', $this->createMobileUrl('myorder'), 'error');
			}	
        } else if ($op == 'detail') {

			$orderid = $_GET['orderid'];
           
			$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE gweid = %s AND {$this -> get_from_info_sql} and out_trade_no = %s", $gweid, $orderid);
			$item = $wpdb->get_row($sql,ARRAY_A);
			//获某条订单是否提交过维权信息
			$order_rightscounts=$wpdb -> get_var($wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}shopping_rights where (rights_status=1 OR rights_status=2) AND out_trade_no=%s",$orderid));
			if (empty($item)) {
                message('抱歉，您的订单不存或是已经被取消！', $this->createMobileUrl('myorder'), 'error');
            }
           
            $sql = $wpdb -> prepare("SELECT goods_id,total,ismanual FROM {$wpdb->prefix}shopping_order_goods WHERE out_trade_no = %s ", $orderid);
			$goodsid = $wpdb->get_results($sql, ARRAY_A);
			
            $sql = $wpdb -> prepare("SELECT g.id, g.title, g.thumb, g.unit, o.goods_price as market_price,g.ismanual, o.total,o.optionid,o.ispoint,o.point,o.total_point FROM {$wpdb->prefix}shopping_order_goods  o left join {$wpdb->prefix}shopping_goods g on o.goods_id=g.id WHERE o.out_trade_no = %s", $orderid);//point update
			$goods = $wpdb->get_results($sql, ARRAY_A);
			foreach ($goods as &$g) {
                //属性
                $sql = $wpdb -> prepare("SELECT title,marketprice,weight,stock FROM {$wpdb->prefix}shopping_goods_option WHERE id = %d", $g['optionid']);
				$option = $wpdb->get_row($sql,ARRAY_A);
				if ($option) {
                    $g['title'] = "[" . $option['title'] . "]" . $g['title'];
                    $g['market_price'] = $option['marketprice'];
                }
            }
            unset($g);

            $sql = $wpdb -> prepare("SELECT id,dispatchname FROM {$wpdb->prefix}shopping_dispatch WHERE id = %d", $item['dispatch']);
			$dispatch = $wpdb->get_row($sql,ARRAY_A);
			
			//优惠信息
			$needdiscount=0;//是否显示优惠信息(如果购买的商品是自己手动输入金额的，则不显示优惠信息)
			foreach ($goodsid as &$gid) {
				$ismanual=$gid['ismanual'];
				if($ismanual==0){//只要有不是人工输入金额的就显示优惠信息
					$needdiscount=1;
				}
			}
			
			//退款信息
			$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_refund WHERE out_trade_no=%s ORDER BY out_refund_no",$orderid);
			$refund_list = $wpdb->get_results($sql,ARRAY_A);
			
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
					$remark = $delivery->remark;
					
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
			
			
			//取消订单
			if(!empty($_POST['order_del'])){
				
				$item = $wpdb->get_var($wpdb -> prepare("SELECT trade_state FROM {$wpdb->prefix}shopping_order WHERE gweid = %s AND {$this -> get_from_info_sql} and out_trade_no = %s", $gweid, $_POST['out_trade_no']));
				$update_state =  (in_array($item, array('SELFDELIVERY','CASHONDELIVERY'))?($item.'_'):'').'CLOSED';
				$status=$wpdb->update( $wpdb->prefix.'shopping_order', array('trade_state'=>$update_state),array('out_trade_no'=>$_POST['out_trade_no']),array('%s'));
				if($status===false){
					$hint = array("status"=>"error","message"=>"出现错误");
					echo json_encode($hint);
					exit;
				}
				//取消订单将库存更新
				$this -> setOrderStock($_POST['out_trade_no'],'cancel');
				
				/*point update积分还回去?????bug 取到的是null fixed*/
				$point_all = $wpdb->get_var($wpdb -> prepare("SELECT pointall FROM {$wpdb->prefix}shopping_order WHERE gweid = %s AND {$this -> get_from_info_sql} and out_trade_no = %s", $gweid, $_POST['out_trade_no']));
				
				
				
				if(intval($costpoint)-intval($point_all)<0){
					$upoint=0;
				}else{
					$upoint=intval($costpoint)-intval($point_all);
				}
				
				$wpdb -> update("{$wpdb -> prefix}wechat_member",array('point' => intval($allpoint)+intval($point_all),'point_cost' => $upoint),array('mid' => $mid));
				/*point update end*/
				
				$hint = array("status"=>"success","message"=>"取消成功","url"=>$this->createMobileUrl('myorder',array('gweid' => $gweid,'goodsgid' => $goodsgid)));
				echo json_encode($hint);
				exit;	
			}
						
			include $this->template('order_detail');
        } else {
            $pindex = max(1, intval($_GET['page']));
            $psize = 20;
			$offset = ($pindex - 1) * $psize;
			$status = $_GET['status'];   //获取订单的交易状态
			$delivery_status = $_GET['delivery_status'];   //获取订单的发货状态
			if(empty($status)){
				$status = "PAYING";	
			}
			$where = " o.gweid = '{$gweid}' AND {$this -> getfrominfo('o.')}";

			if ($status == "PAYING") {
                $where.=" and ( o.trade_state = 'PAYING' or o.trade_state = 'NOTPAY') and ( d.delivery_status = '1' or d.delivery_status IS NULL)";
            }else if ($status == "SUCCESS" && $delivery_status == "1") {
                //$where.=" and ( o.trade_state = 'SUCCESS' or o.trade_state = 'SELFDELIVERY' or o.trade_state = 'CASHONDELIVERY' ) and ( d.delivery_status = '1' or d.delivery_status IS NULL)";  //未发货的也应该放在待收货列表中
				$where.=" and ( o.trade_state = 'SUCCESS' or o.trade_state = 'SELFDELIVERY' or o.trade_state = 'CASHONDELIVERY' ) and ( d.delivery_status = '1' or d.delivery_status = '0' or d.delivery_status IS NULL)"; 
            }else if ($status == "SUCCESS" && $delivery_status == "2") {
                $where.=" and ((( o.trade_state = 'SUCCESS' or o.trade_state = 'SELFDELIVERY_CLOSED' or o.trade_state = 'CASHONDELIVERY_CLOSED' ) and d.delivery_status = '2') or (( o.trade_state = 'PAYING' or o.trade_state = 'NOTPAY') and d.delivery_status = 2 )) ";
            } 
			$sql = "SELECT *, o.out_trade_no as out_trade_no FROM {$wpdb->prefix}shopping_order o left join {$wpdb->prefix}shopping_delivery d on o.out_trade_no = d.out_trade_no WHERE {$where} ORDER BY o.out_trade_no DESC LIMIT {$offset}, {$psize}";
			$list = $wpdb->get_results($sql, ARRAY_A);
			$sql = $wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}shopping_cart WHERE gweid = %s and {$this->get_from_info_sql} ",$gweid);
			$total = $wpdb->get_var($sql);
			$pager = pagination($total, $pindex, $psize);

            if (!empty($list)) {
                foreach ($list as &$row) {
                    $sql = $wpdb -> prepare("SELECT goods_id,total FROM {$wpdb->prefix}shopping_order_goods WHERE out_trade_no = %s", $row['out_trade_no']);
					$goodsid = $wpdb->get_results($sql, ARRAY_A);
					$sql = $wpdb -> prepare("SELECT g.id, g.title, g.thumb, g.unit, o.goods_price as market_price, g.ismanual, o.total,o.optionid,o.ispoint,o.point,o.total_point FROM {$wpdb->prefix}shopping_order_goods  o left join {$wpdb->prefix}shopping_goods  g on o.goods_id=g.id WHERE o.out_trade_no = %s", $row['out_trade_no']);//point update
					$goods = $wpdb->get_results($sql, ARRAY_A);
					
                    foreach ($goods as &$item) {
                        //属性
                        $sql = $wpdb -> prepare("SELECT title,marketprice,weight,stock FROM {$wpdb->prefix}shopping_goods_option WHERE id = %d", $item['optionid']);
						$option = $wpdb->get_row($sql,ARRAY_A);
						if ($option) {
                            $item['title'] = "[" . $option['title'] . "]" . $item['title'];
                            $item['market_price'] = $option['marketprice'];
                        }
                    }
                    unset($item);
                    $row['goods'] = $goods;
                    $row['total'] = $goodsid;
					//获某条订单是否提交过维权信息
					$order_rightscounts=$wpdb -> get_var($wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}shopping_rights where (rights_status=1 OR rights_status=2) AND out_trade_no=%s",$row['out_trade_no']));
					$row['rightscounts'] = $order_rightscounts;
					$sql = $wpdb -> prepare("SELECT id,dispatchname FROM {$wpdb->prefix}shopping_dispatch WHERE id = %d", $row['dispatch']);
					$row['dispatch'] = $wpdb->get_row($sql,ARRAY_A);
			    }
            }
            $shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
			$_share = array(
				'title' => get_bloginfo('name','display') .' - '.$shopname,
				'desc' => "点击访问".$shopname.'!',
				'link' => $this -> createMobileUrl('list', array())
				);
            include $this->template('order');
        }
    }
	//管理收货地址
	public function doMobileAddress() {
        global $_W, $_GPC, $wpdb;
		$gweid = $_GET['gweid'];
		$mid = $_W['fans']['mid'];
		//20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				$buyer = $buyer -> nickname;
			}
		}
		
        $from = $_GPC['from'];
        $returnurl = urldecode($_GPC['returnurl']);
        //先判断在不同浏览器上的用户身份
		$this -> verifyUser($gweid);
        // $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'post';
        $operation = $_GPC['op'];

        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            /* $data = array(
                'weid' => $_W['uniacid'],
                'openid' => $_W['fans']['from_user'],
                'realname' => $_GPC['realname'],
                'mobile' => $_GPC['mobile'],
                'province' => $_GPC['province'],
                'city' => $_GPC['city'],
                'area' => $_GPC['area'],
                'address' => $_GPC['address'],
            ); */
			 $data = array(
                'weid' => $_W['uniacid'],
                'openid' => $_W['fans']['from_user'],
                'realname' => $_GPC['realname'],
                'mobile' => $_GPC['mobile'],
                'province' => $_GPC['province'],
                'city' => $_GPC['city'],
                'area' => $_GPC['area'],
                'address' => $_GPC['address'],
            );
            if (empty($_GPC['realname']) || empty($_GPC['mobile']) || empty($_GPC['address'])) {
                message('请输完善您的资料！');
            }
            if (!empty($id)) {
                unset($data['weid']);
                unset($data['openid']);
                //pdo_update('shopping_address', $data, array('id' => $id));
				$wpdb->update("{$wpdb -> prefix}shopping_address", $data, array('id' => $id));
                message($id, '', 'ajax');
            } else {
                //pdo_update('shopping_address', array('isdefault' => 0), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                $wpdb->update("{$wpdb -> prefix}shopping_address", array('isdefault' => 0), array('gweid' => $gweid, 'mid' => $mid));
				$data['isdefault'] = 1;
                pdo_insert('shopping_address', $data);
                $id = pdo_insertid();
                if (!empty($id)) {
                    message($id, '', 'ajax');
                } else {
                    message(0, '', 'ajax');
                }
            }
        } elseif ($operation == 'default') {
            $id = intval($_GPC['id']);
            //$address = pdo_fetch("select isdefault from " . tablename('shopping_address') . " where id='{$id}' and weid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");
            $sql = $wpdb -> prepare("SELECT isdefault FROM {$wpdb->prefix}shopping_address WHERE id = %d and gweid = %s and mid = %s limit 1", $id, $gweid, $mid);
			$address = $wpdb->get_row($sql,ARRAY_A);
			if(!empty($address) && empty($address['isdefault'])){
                //pdo_update('shopping_address', array('isdefault' => 0), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                $wpdb->update("{$wpdb -> prefix}shopping_address", array('isdefault' => 0), array('gweid' => $gweid, 'mid' => $mid));
				//pdo_update('shopping_address', array('isdefault' => 1), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user'], 'id' => $id));
				$wpdb->update("{$wpdb -> prefix}shopping_address", array('isdefault' => 0), array('gweid' => $gweid, 'mid' => $mid, 'id' => $id));
			}
            message(1, '', 'ajax');
        } elseif ($operation == 'detail') {
            $id = intval($_GPC['id']);
            //$row = pdo_fetch("SELECT id, realname, mobile, province, city, area, address FROM " . tablename('shopping_address') . " WHERE id = :id", array(':id' => $id));
            $sql = $wpdb -> prepare("SELECT id, realname, mobile, province, city, area, address FROM {$wpdb->prefix}shopping_address WHERE id = %d", $id);
			$row = $wpdb->get_row($sql,ARRAY_A);
			message($row, '', 'ajax');
        } elseif ($operation == 'remove') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                //$address = pdo_fetch("select isdefault from " . tablename('shopping_address') . " where id='{$id}' and weid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");
				$sql = $wpdb -> prepare("SELECT isdefault FROM {$wpdb->prefix}shopping_address WHERE id = %d and gweid = %s and mid = %s limit 1", $id, $gweid, $mid);
				$address = $wpdb->get_row($sql,ARRAY_A);
                if (!empty($address)) {
                    //pdo_delete("shopping_address",  array('id'=>$id, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                    //修改成不直接删除，而设置deleted=1
                    //pdo_update("shopping_address", array("deleted" => 1, "isdefault" => 0), array('id' => $id, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
					$wpdb->update("{$wpdb -> prefix}shopping_address", array("deleted" => 1, "isdefault" => 0), array('id' => $id, 'gweid' => $gweid, 'mid' => $mid));
                    if ($address['isdefault'] == 1) {
                        //如果删除的是默认地址，则设置是新的为默认地址
                        //$maxid = pdo_fetchcolumn("select max(id) as maxid from " . tablename('shopping_address') . " where weid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");
                        $sql = $wpdb -> prepare("SELECT  max(id) as maxid FROM {$wpdb->prefix}shopping_address WHERE gweid = %s and mid=%s limit 1",$gweid, $mid);
						$maxid = $wpdb->get_var($sql);
						if (!empty($maxid)) {
                            //pdo_update('shopping_address', array('isdefault' => 1), array('id' => $maxid, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                            $wpdb->update("{$wpdb -> prefix}shopping_address", array( "isdefault" => 0), array('id' => $maxid, 'gweid' => $gweid, 'mid' => $mid));
							die(json_encode(array("result" => 1, "maxid" => $maxid)));
                        }
                    }
                }
            }
            die(json_encode(array("result" => 1, "maxid" => 0)));
        } else {
            //$profile = fans_search($_W['fans']['from_user'], array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
            //获取会员的相关信息
			$profile = $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wechat_member WHERE mid = %s LIMIT 1",$mid),ARRAY_A);;
			//$address = pdo_fetchall("SELECT * FROM " . tablename('shopping_address') . " WHERE deleted=0 and openid = :openid", array(':openid' => $_W['fans']['from_user']));
            $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_address WHERE  deleted=0 and mid = %s", $mid);
			$address = $wpdb->get_results($sql, ARRAY_A);
			$carttotal = $this->getCartTotal();
            include $this->template('address');
        }
    }
	
	public function doMobilePay() {
        global $_W, $_GPC, $wpdb;
		$gweid = $_GET['gweid'];
		$mid = $_W['fans']['mid'];
		//20150420 Sara new added
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				$buyer = $buyer -> nickname;
			}
		}
		
        //先判断在不同浏览器上的用户身份
		$this -> verifyUser($gweid);
        //$orderid = intval($_GPC['orderid']);
		$orderid = $_GET['orderid'];
		$weixin = new WeixinPay($gweid);
        //$order = pdo_fetch("SELECT * FROM " . tablename('shopping_order') . " WHERE id = :id", array(':id' => $orderid));
        $sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order WHERE out_trade_no = %s ", $orderid);
		$order = $wpdb->get_row($sql,ARRAY_A);
		/* if ($order['status'] != '0') {
            message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('myorder'), 'error');
        } */
        /* if (checksubmit('codsubmit')) {
            //$ordergoods = pdo_fetchall("SELECT goodsid, total,optionid FROM " . tablename('shopping_order_goods') . " WHERE orderid = '{$orderid}'", array(), 'goodsid');
            $sql = $wpdb -> prepare("SELECT goods_id, total,optionid FROM {$wpdb->prefix}shopping_order_goods WHERE out_trade_no = %s", $orderid);
			$ordergoods = $wpdb->get_results($sql, ARRAY_A);
			if (!empty($ordergoods)) {
                //$goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total,credit FROM " . tablename('shopping_goods') . " WHERE id IN ('" . implode("','", array_keys($ordergoods)) . "')");
				$sql = $wpdb -> prepare("SELECT id, title, thumb, marketprice, unit, total FROM {$wpdb->prefix}shopping_goods WHERE id = %s", implode("','", array_keys($ordergoods)));
				$goods = $wpdb->get_results($sql, ARRAY_A); 
			}



            //邮件提醒
            if (!empty($this->module['config']['noticeemail'])) {

                $address = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE id = :id", array(':id' => $order['addressid']));
				
                $body = "<h3>购买商品清单</h3> <br />";

                if (!empty($goods)) {
                    foreach ($goods as $row) {

                        //属性
                        //$option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $ordergoods[$row['id']]['optionid']));
                        $sql = $wpdb -> prepare("SELECT title,marketprice,weight,stock FROM {$wpdb->prefix}shopping_goods_option WHERE id = %d limit 1", $ordergoods[$row['id']]['optionid']);
						$option = $wpdb->get_row($sql,ARRAY_A);
						if ($option) {
                            $row['title'] = "[" . $option['title'] . "]" . $row['title'];
                        }
                        $body .= "名称：{$row['title']} ，数量：{$ordergoods[$row['id']]['total']} <br />";
                    }
                }
                $paytype = $order['paytype']=='3'?'货到付款':'已付款';
                $body .= "<br />总金额：{$order['price']}元 （{$paytype}）<br />";
                $body .= "<h3>购买用户详情</h3> <br />";
                $body .= "真实姓名：$address[realname] <br />";
                $body .= "地区：$address[province] - $address[city] - $address[area]<br />";
                $body .= "详细地址：$address[address] <br />";
                $body .= "手机：$address[mobile] <br />";

                ihttp_email($this->module['config']['noticeemail'], '微商城订单提醒', $body);
            }
            //pdo_update('shopping_order', array('status' => '1', 'paytype' => '3'), array('id' => $orderid));
			$wpdb->update("{$wpdb -> prefix}shopping_order", array('status' => '1', 'payment_type' => '3'),  array('out_trade_no' => $orderid));

            //增加积分
            //$this->setOrderCredit($orderid);

            message('订单提交成功，请您收到货时付款！', $this->createMobileUrl('myorder'), 'success');
        }

        if (checksubmit()) {
            if ($order['paytype'] == 1 && $_W['fans']['credit2'] < $order['price']) {
                message('抱歉，您帐户的余额不够支付该订单，请充值！', create_url('mobile/module/charge', array('name' => 'member', 'weid' => $_W['uniacid'])), 'error');
            }
            if ($order['price'] == '0') {
                $this->payResult(array('tid' => $orderid, 'from' => 'return', 'type' => 'credit2'));
                exit;
            }
        }
        $params['tid'] = $orderid;
        $params['user'] = $_W['fans']['from_user'];
        $params['fee'] = $order['price'];
        $params['title'] = $_W['account']['name'];
        $params['ordersn'] = $order['ordersn'];
        $params['virtual'] = $order['goodstype'] == 2 ? true : false; */
		
		
        //判断是否会员，从而显示会员价格
		$mid = $_W['fans']['mid'];
		//20150420 Sara new added
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				$buyer = $buyer -> nickname;
			}
		}
				
		//判断支付类型
		$sql = $wpdb -> prepare("SELECT * FROM  {$wpdb->prefix}shopping_order_wepay WHERE  out_trade_no=%s",$out_trade_no);
		$tradetype = $wpdb->get_row($sql);

		//对订单的优惠信息（不是针对商品）
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_discount where out_trade_no=%s",$out_trade_no);
		$discount_list = $wpdb->get_results($sql);		
		$discount_fee = max($wpdb -> get_var( $wpdb -> prepare("SELECT sum(discount_price) FROM  {$wpdb->prefix}shopping_order_discount WHERE  out_trade_no=%s",$out_trade_no) ) , 0);
		$discount_fee = max($discount_fee , 0);
		$discount_fee = number_format($discount_fee,2,'.','');
		
		//订单信息(如果使用的快递方式是自提或者货到付款，则不出现微信支付)
		$sql = $wpdb -> prepare("SELECT o.fee,o.pointall, o.address, o.description, o.openid, d.dispatchtype FROM  {$wpdb->prefix}shopping_order o LEFT JOIN {$wpdb->prefix}shopping_dispatch d on o.dispatch = d.id WHERE  o.gweid=%s and o.out_trade_no = %s",$gweid,$orderid);//point update
		$orders = $wpdb->get_results($sql);
		if(is_array($orders) && !empty($orders)){
			foreach($orders as $order){
				$totalfee=$order-> fee;
				$totalpoint=intval($order-> pointall);//point update
				$order_address = $order-> address;
				$order_address = json_decode($order_address,true);
				$description = $order-> description;//订单描述
				$orderfromuser = $order-> openid;
				$orderdispatchtype = $order-> dispatchtype;
			}	
		}
		//订单商品信息
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_order_goods where out_trade_no=%s",$orderid);
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
			'out_trade_no' => $orderid,//订单号，需保证该字段对于本商户的唯一性
			'total_fee' =>  (string)($totalfee*100), //支付金额 单位：分
			'notify_url'=>$this->createMobileUrl('paidNotify',array('gweid' =>$gweid)),//支付成功后将通知该地址
			'spbill_create_ip' => $_SERVER['REMOTE_ADDR']
			
		);
	    $shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
		$_share = array(
			'title' => get_bloginfo('name','display') .' - '.$shopname,
			'desc' => "点击访问".$shopname.'!',
			'link' => $this -> createMobileUrl('list', array())
			);
        include $this->template('pay');
    }
			
	public function getCartTotal() {
        global $_W, $wpdb;
        if($this -> getfrominfo() === FALSE)
        	return 0;
		$gweid = $_GET['gweid'];
        $sql = $wpdb -> prepare("SELECT count(*) FROM {$wpdb->prefix}shopping_cart WHERE gweid = %s and {$this -> getfrominfo()}", $gweid);
		$cartotal = $wpdb->get_var($sql);
		return empty($cartotal) ? 0 : $cartotal;
    }
	
	  //设置订单商品的库存 minus  true 减少  false 增加
      //$action place 下单 pay 付款 cancel 取消
    private function setOrderStock($id = '', $action) {
        global $_W, $wpdb;
		$sql = $wpdb -> prepare("SELECT g.id, g.title, g.thumb, g.unit, g.ismanual, g.market_price,g.total as goodstotal,o.total,o.optionid,g.totalcnf,g.sales FROM {$wpdb->prefix}shopping_order_goods o left join {$wpdb->prefix}shopping_goods g on o.goods_id=g.id  WHERE o.out_trade_no = %s", $id);
		$goods = $wpdb->get_results($sql, ARRAY_A);
		$order_state = $wpdb -> get_var($wpdb->prepare("SELECT trade_state FROM {$wpdb->prefix}shopping_order WHERE out_trade_no=%s",$id));
		if($action == 'cancel')
			$minus = false;
		else
			$minus = true;
		if($action == 'place')
			$cnf = array(0);
		elseif($action == 'pay')
			$cnf = array(1);
		elseif($action == 'cancel' && $order_state =='SUCCESS')
			$cnf = array(0,1);
		else
			$cnf = array(0);
		foreach ($goods as &$item) {
			if(in_array($item['totalcnf'], $cnf)){   //如果其减库存的方式是0(拍下减库存)或者1(付款减库存)，则进行下面的操作，否则(2表示永不减库存)不进行库存的任何操作
				if ($minus) {
					//属性
					if (!empty($item['optionid'])) {
						$stock = $wpdb -> get_var( $wpdb -> prepare("SElECT stock FROM {$wpdb->prefix}shopping_goods_option where id = %d",$item['optionid']));
						if($stock != -1)
							$update=$wpdb->query( $wpdb->prepare("update {$wpdb->prefix}shopping_goods_option set stock = stock - %d where id = %d",$item['total'],$item['optionid']));
					}
					$data = array();
					//库存不为-1且不是买家输入金额
					if ((!empty($item['goodstotal']) && $item['goodstotal'] != -1) && ($item['ismanual'] != 1)) {
						$data['total'] = $item['goodstotal'] - $item['total'];
					}
					$data['sales'] = $item['sales'] + $item['total'];
					$wpdb -> update("{$wpdb -> prefix}shopping_goods",$data,array('id' => $item['id']));
					//$wpdb -> query($wpdb -> prepare("UPDATE {$wpdb -> prefix}shopping_goods SET `sales`=`sales`+{$item['total']},`total`=`total`-{$item['total']} WHERE `id`={$item['id']}"));///??????bug 这里就算-1也给减库存了
				} else {
					//属性
					if (!empty($item['optionid'])) {
						$stock = $wpdb -> get_var( $wpdb -> prepare("SElECT stock FROM {$wpdb->prefix}shopping_goods_option where id = %d",$item['optionid']));
						if($stock != -1)
							$update=$wpdb->query( $wpdb->prepare("update {$wpdb->prefix}shopping_goods_option set stock = stock + %d where id = %d",$item['total'],$item['optionid']));
					}
					$data = array();
					if ((!empty($item['goodstotal']) && $item['goodstotal'] != -1) && ($item['ismanual'] != 1)) {
						$data['total'] = $item['goodstotal'] + $item['total'];
					}
					$data['sales'] = $item['sales'] - $item['total'];
					$wpdb->update("{$wpdb -> prefix}shopping_goods", $data,  array('id' => $item['id']));
				}
			}else{ //永不减库存的方式，库存不发生变化，但是已出售数仍然需要增加
				if ($minus) {
					//属性
					$data = array();
					$data['sales'] = $item['sales'] + $item['total'];
					$wpdb -> query($wpdb -> prepare("UPDATE {$wpdb -> prefix}shopping_goods SET `sales`=`sales`+{$item['total']} WHERE `id`={$item['id']}"));
				} else {
					
					$data = array();
					$data['sales'] = $item['sales'] - $item['total'];
					$wpdb->update("{$wpdb -> prefix}shopping_goods", $data,  array('id' => $item['id']));
				}
			}
		}
    }
	
	//品牌介绍
	public function doMobileContactUs() {
        global $_W, $wpdb;
		$gweid = $_GET['gweid'];
        $mid = $_W['fans']['mid'];
        //20150420 sara new added 
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$buyerinfo=$this->doWebBuyer($mid,$fromuser,$gweidv);
		if(!empty($buyerinfo)){
			foreach($buyerinfo as $buyer){
				$buyer = $buyer -> nickname;
			}
		}
		
		$sql = $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}shopping_shop where gweid =%s",$gweid);
		$contactus = $wpdb->get_results($sql);
		if(is_array($contactus) && !empty($contactus)){
			foreach($contactus as $contact){
			    $name = $contact -> name;
				$imgurl = $contact -> image;
				$img = $this -> upload_url($imgurl);
				$phone = $contact -> phone;
				$address = $contact -> address;
				$description = $this->rule_content($contact -> description);
				$email = $contact -> email;
				$site = $contact -> site;
			}	
		}
		$shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
		$_share = array(
			'title' => get_bloginfo('name','display') .' - '.$shopname .' - 联系我们',
			'desc' => "点击访问".$shopname.'!',
			);
        include $this->template('contactus');
    }

    public function doMobileRightsLists(){
		require_once 'wp-content/themes/ReeooV3/wesite/common/upload.php';
		$shoppingtitle='我的维权';
		global $_W,$wpdb;
		$gweid = $_GET['gweid'];
		$this -> verifyUser($gweid);
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
		$sql = $wpdb -> prepare( "SELECT count(*) FROM {$wpdb->prefix}shopping_order u1,{$wpdb->prefix}shopping_rights u2 where u1.out_trade_no = u2.out_trade_no and u1.gweid=%s and {$this -> getfrominfo('u1.')}",$gweid);
		$rightcounts= $wpdb->get_var($sql);
		$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order u1,{$wpdb->prefix}shopping_rights u2 	where u1.out_trade_no = u2.out_trade_no and u1.gweid=%s and {$this -> getfrominfo('u1.')} ORDER BY create_time DESC",$gweid);
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
		$shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
		$_share = array(
			'title' => get_bloginfo('name','display') .' - '.$shopname,
			'desc' => "点击访问".$shopname.'!',
			'link' => $this -> createMobileUrl('list', array())
			);
		include $this -> template('rightslists');
	
	}
		
	//手机--维权详情页面
	public function doMobileRightsDetails(){
		global $_W, $_GPC ,$wpdb;
		$shoppingtitle='维权详情';
		$gweid = $_GET['gweid'];
		$this -> verifyUser($gweid);
		$out_trade_no = $_GET['out_trade_no'];
		$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order_goods u2,{$wpdb->prefix}shopping_goods u3 where u3.id=u2.goods_id and u2.out_trade_no=%s",$out_trade_no);
		$goodsinfos = $wpdb->get_results($sql,ARRAY_A);
		$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order where out_trade_no=%s and gweid=%s and {$this -> getfrominfo()}",$out_trade_no,$gweid );
		$rsdetails = $wpdb->get_row($sql,ARRAY_A);

		$shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
		$_share = array(
			'title' => get_bloginfo('name','display') .' - '.$shopname,
			'desc' => "点击访问".$shopname.'!',
			'link' => $this -> createMobileUrl('list', array())
			);
		include $this -> template('rightsdetails');
	}
	//手机--维权状态页面
	public function doMobileRightsOrderStatus(){
			global $_W,$wpdb;
			$shoppingtitle='维权单详情';
			$gweid = $_GET['gweid'];
			$this -> verifyUser($gweid);
			
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
			$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order u1,{$wpdb->prefix}shopping_rights u2 where u1.out_trade_no = u2.out_trade_no and u1.out_trade_no=%s and u1.gweid=%s and {$this -> getfrominfo('u1.')} and u2.feedbackid= %s" ,$out_trade_no,$gweid,$feedbackid);
			$right = $wpdb->get_row($sql,ARRAY_A);
			$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order_goods u2,{$wpdb->prefix}shopping_goods u3 where u3.id=u2.goods_id and u2.out_trade_no=%s",$out_trade_no);
			$goodsinfos = $wpdb->get_results($sql,ARRAY_A);
			$sql = $wpdb -> prepare( "SELECT * FROM {$wpdb->prefix}shopping_order where out_trade_no=%s and gweid=%s and {$this -> getfrominfo()}",$out_trade_no,$gweid );
			$ordersinfo = $wpdb->get_row($sql,ARRAY_A);

			$shopname = $wpdb->get_var($wpdb -> prepare("SELECT goodsindex_name FROM {$wpdb->prefix}shopping_goodsindex where gweid =%s AND isshopping=1",$gweid));
			$_share = array(
				'title' => get_bloginfo('name','display') .' - '.$shopname,
				'desc' => "点击访问".$shopname.'!',
				'link' => $this -> createMobileUrl('list', array())
				);
			include $this -> template('rightsorderstatus');
	
	}

	protected function template($filename, $public_template = False){
		if($this->inMobile){
			global $wpdb;
			$templateId = $wpdb->get_var($wpdb->prepare("SELECT `template` FROM `wp_shopping_goodsindex` WHERE `isshopping`=1 AND `gweid`=%d",$_GET['gweid']));
			if(!empty($templateId))
				$filename = 'template'.$templateId.'/'.$filename;
		}
			    
		return parent::template($filename, $public_template);
	}
	protected function createMobileUrl($do, $querystring = array()) {
		if($this->inMobile){
			$querystring['gweid'] = $_GET['gweid'];
		}
		return parent::createMobileUrl($do, $querystring);
	}
	private function memberLimitation($remind_text = "登陆后才能继续，请登录或注册。"){
		if(empty($_W['fans']['mid'])){
			header("Location: ".home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$_W['gweidv'].'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]).'&alert='.urlencode($remind_text));
		}
	}

	private function upload_url($url = ""){
		$upload =wp_upload_dir();
		if(empty($url))
			return '';
		if(stristr($url,"http")!==false)
			return $url;
		return $upload['baseurl'].$url;	
	}
	
	public function doMobileAjaxdelete() {
        global $_GPC;
        $delurl = $_GPC['pic'];
        if (file_delete($delurl)) {
            echo 1;
        } else {
            echo 0;
        }
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
	
	//判断浏览器是否是微信内置浏览器
	public function is_weixin(){  
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) { 
			return true; 
		} 
		return false; 
	}
	//验证用户身份
	public function verifyUser($gweid){
	    global $_W, $wpdb;
	    $this -> get_from_info_sql = $this -> getfrominfo();
		$weixin = new WeixinPay($gweid);
		/*
		if($this -> is_weixin() && $weixin -> isConfigAvailable()){
			$fromuser=$_SESSION['oauth_openid']['openid'];
			if(!empty($_GET['errorcode'])){
				include $this -> moduleTemplate('wepay','oauth_error');
				exit;
			}
			if(empty($fromuser)||($_SESSION['oauth_openid']['gweid']!=$gweid)||empty($_SESSION['addaccesstoken'])||(time()-60>=$_SESSION['expires_time'])){
				$weixin->isoauth2_base($gweid);
				$fromuser=$_SESSION['oauth_openid']['openid'];
			}
			//get userinfo
			$user=$weixin->userinfo($fromuser);
			$this-> wechatname=$user['nickname']; 
			
		}else{  //如果是从普通浏览器浏览,先判断是否是会员
		
			
			$mid=$_W['fans']['mid'];   //通过mid取会员昵称
			if(empty($mid)){
				header("Location: ".home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$_W['gweidv'].'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]).'&alert='.urlencode('登录后才能继续，请先登录。'));
				exit;
			}
		}*/
		if(empty($_W['fans']['from_user'])) {
			$mid=$_W['fans']['mid'];   //通过mid取会员昵称
			if(empty($mid)){
				header("Location: ".home_url().'/wp-content/themes/ReeooV3/wesite/common/vip_login.php?gweid='.$_W['gweidv'].'&redirect_url='.urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]).'&alert='.urlencode('登录后才能继续，请先登录。'));
				exit;
			}
		}else{
			$this -> fromuser = $_W['fans']['from_user'];
		}
	}
	//判断用户的fromuser和mid的情况
	public function getfrominfo($prefix = ""){
		global $_W, $wpdb;
		$fromuser = $_W['fans']['from_user'];
		$mid = $_W['fans']['mid'];
		if(!empty($fromuser) && empty($mid)){
			$fromuser = $wpdb->escape($fromuser);
		    $fromuser = "{$prefix}`openid` = '{$fromuser}'";
		    
			return $fromuser;
		}else if(empty($fromuser) && !empty($mid)){
			$mid = $wpdb->escape($mid);
			$mid = "{$prefix}`mid` = '{$mid}'";
			return $mid;
		}else if(!empty($fromuser) && !empty($mid)){
			$fromuser = $wpdb->escape($fromuser);
			$mid = $wpdb->escape($mid);
			$fromusermid = "({$prefix}`openid` = '{$fromuser}' OR {$prefix}`mid` = '{$mid}')";
			return $fromusermid;
		}
		return FALSE;
	}

	private function createModuleMobileUrl($module_name, $do, $querystring = array()){
		$module_site = WeUtility::createModuleSite($module_name);
		$module_site -> inMobile = true;
		$module_site -> module['dir'] = MODULES_DIR.$module_name.'/';
		$module_site -> module['name'] = $module_name;
		return $module_site -> createMobileUrl($do, $querystring);
	}
	public function createModuleWebUrl($module_name, $do, $querystring = array()){
		$module_site = WeUtility::createModuleSite($module_name);
		$module_site -> inMobile = true;
		$module_site -> module['dir'] = MODULES_DIR.$module_name.'/';
		$module_site -> module['name'] = $module_name;
		return $module_site -> createWebUrl($do, $querystring);
	}
	private function moduleTemplate($module_name, $filename, $public_template = False){
		$module_site = WeUtility::createModuleSite($module_name);
		$module_site -> inMobile = true;
		$module_site -> module['dir'] = MODULES_DIR.$module_name.'/';
		$module_site -> module['name'] = $module_name;
		return $module_site -> template($filename, $public_template);
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
		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}shopping_goods WHERE gweid='{$gweid}' AND groupid IS NULL",ARRAY_A);
		if(is_array($list))
			foreach($list as $element){
				file_unlink($element['thumb']);
				file_unlink_from_xml($element['content']);
			}

		$list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}wp_shopping_shop WHERE gweid='{$gweid}'",ARRAY_A);
		if(is_array($list))
			foreach($list as $element){
				file_unlink($element['image']);
				file_unlink_from_xml($element['description']);
			}
				
	}

}