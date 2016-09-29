<?php

/**
 * 微商城模块微站定义
 *
 * @author WeEngine Team
 * @url
 */
defined('IN_IA') or exit('Access Denied');

session_start();
include 'model.php';

class Ewei_shoppingModuleSite extends WeModuleSite {

    
	public function doWebOrder() {
        global $_W, $_GPC;
        load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $status = $_GPC['status'];
            $sendtype = !isset($_GPC['sendtype']) ? 0 : $_GPC['sendtype'];
            $starttime = empty($_GPC['starttime']) ? strtotime('-1 month') : strtotime($_GPC['starttime']);
            $endtime = empty($_GPC['endtime']) ? TIMESTAMP : strtotime($_GPC['endtime']) + 86399;
           $paras = array(
            ':weid' => $_W['uniacid'],
            ':starttime' => $starttime,
            ':endtime' => $endtime
        );
        
            $condition = " o.weid = :weid AND o.createtime >= :starttime AND o.createtime <= :endtime ";
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND o.ordersn LIKE '%{$_GPC['keyword']}%'";
            }
           if (!empty($_GPC['member'])) {
                $condition .= " AND (a.realname LIKE '%{$_GPC['member']}%' or a.mobile LIKE '%{$_GPC['member']}%')";
            }
            if ($status != '') {
                $condition .= " AND o.status = '" . intval($status) . "'";
            }

            if (!empty($sendtype)) {
                $condition .= " AND o.sendtype = '" . intval($sendtype) . "' AND status != '3'";
            }

            $sql = "select o.* , a.realname,a.mobile from ".tablename('shopping_order')." o"
                    ." left join ".tablename('shopping_address')." a on o.addressid = a.id "
                    . " where $condition ORDER BY o.status ASC, o.createtime DESC "
                    . "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
            $list = pdo_fetchall($sql,$paras);
 
 
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('shopping_order') . " o  "
                          ." left join ".tablename('shopping_address')." a on o.addressid = a.id "
                      ." WHERE $condition",$paras);
            $pager = pagination($total, $pindex, $psize);
            if (!empty($list)) {
                foreach ($list as &$row) {
                   // !empty($row['addressid']) && $addressids[$row['addressid']] = $row['addressid'];
                    $row['dispatch'] = pdo_fetch("SELECT * FROM " . tablename('shopping_dispatch') . " WHERE id = :id", array(':id' => $row['dispatch']));
                }
                unset($row);
            }
//            if (!empty($addressids)) {
//                $address = pdo_fetchall("SELECT * FROM " . tablename('shopping_address') . " WHERE id IN ('" . implode("','", $addressids) . "')", array(), 'id');
//            }
            
     
        
        } elseif ($operation == 'detail') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT * FROM " . tablename('shopping_order') . " WHERE id = :id", array(':id' => $id));
            if (empty($item)) {
                message("抱歉，订单不存在!", referer(), "error");
            }
            if (checksubmit('confirmsend')) {
                if (!empty($_GPC['isexpress']) && empty($_GPC['expresssn'])) {
                    message('请输入快递单号！');
                }
                $item = pdo_fetch("SELECT transid FROM " . tablename('shopping_order') . " WHERE id = :id", array(':id' => $id));
                if (!empty($item['transid'])) {
                    $this->changeWechatSend($id, 1);
                }
                pdo_update('shopping_order', array(
                    'status' => 2,
                    'remark' => $_GPC['remark'],
                    'express' => $_GPC['express'],
                    'expresscom' => $_GPC['expresscom'],
                    'expresssn' => $_GPC['expresssn'],
                        ), array('id' => $id));
                message('发货操作成功！', referer(), 'success');
            }
            if (checksubmit('cancelsend')) {
                $item = pdo_fetch("SELECT transid FROM " . tablename('shopping_order') . " WHERE id = :id", array(':id' => $id));
                if (!empty($item['transid'])) {
                    $this->changeWechatSend($id, 0, $_GPC['cancelreson']);
                }
                pdo_update('shopping_order', array(
                    'status' => 1,
                    'remark' => $_GPC['remark'],
                        ), array('id' => $id));
                message('取消发货操作成功！', referer(), 'success');
            }
            if (checksubmit('finish')) {
                pdo_update('shopping_order', array('status' => 3, 'remark' => $_GPC['remark']), array('id' => $id));
                message('订单操作成功！', referer(), 'success');
            }
            if (checksubmit('cancel')) {
                pdo_update('shopping_order', array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
                message('取消完成订单操作成功！', referer(), 'success');
            }
            if (checksubmit('cancelpay')) {
                pdo_update('shopping_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
                //设置库存
                $this->setOrderStock($id, false);
                //减少积分
                $this->setOrderCredit($id, false);
                message('取消订单付款操作成功！', referer(), 'success');
            }
            if (checksubmit('confrimpay')) {
                pdo_update('shopping_order', array('status' => 1, 'paytype' => 2, 'remark' => $_GPC['remark']), array('id' => $id));
                //设置库存
                $this->setOrderStock($id);
                //增加积分
                $this->setOrderCredit($id);
                message('确认订单付款操作成功！', referer(), 'success');
            }
            if (checksubmit('close')) {
                $item = pdo_fetch("SELECT transid FROM " . tablename('shopping_order') . " WHERE id = :id", array(':id' => $id));
                if (!empty($item['transid'])) {
                    $this->changeWechatSend($id, 0, $_GPC['reson']);
                }
                pdo_update('shopping_order', array('status' => -1, 'remark' => $_GPC['remark']), array('id' => $id));
                message('订单关闭操作成功！', referer(), 'success');
            }
            if (checksubmit('open')) {
                pdo_update('shopping_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
                message('开启订单操作成功！', referer(), 'success');
            }
            $dispatch = pdo_fetch("SELECT * FROM " . tablename('shopping_dispatch') . " WHERE id = :id", array(':id' => $item['dispatch']));
            if (!empty($dispatch) && !empty($dispatch['express'])) {
                $express = pdo_fetch("select * from " . tablename('shopping_express') . " WHERE id=:id limit 1", array(":id" => $dispatch['express']));
            }
            $item['user'] = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE id = {$item['addressid']}");
            $goods = pdo_fetchall("SELECT g.id, g.title, g.status,g.thumb, g.unit, g.marketprice,o.total,g.type,o.optionname,o.optionid,o.price as orderprice FROM " . tablename('shopping_order_goods') . " o left join " . tablename('shopping_goods') . " g on o.goodsid=g.id "
                    . " WHERE o.orderid='{$id}'");
            $item['goods'] = $goods;
        } elseif ($operation == 'delete') {
        	/*订单删除*/
        	$orderid = intval($_GPC['id']);
        	if (pdo_delete('shopping_order', array('id' => $orderid))) {
        		message('订单删除成功', $this->createWebUrl('order', array('op' => 'display')), 'success');
        	} else {
        		message('订单不存在或已被删除', $this->createWebUrl('order', array('op' => 'display')), 'error');
        	}
        }
        include $this->template('order');
    }

    //设置订单商品的库存 minus  true 减少  false 增加
    private function setOrderStock($id = '', $minus = true) {

        $goods = pdo_fetchall("SELECT g.id, g.title, g.thumb, g.unit, g.marketprice,g.total as goodstotal,o.total,o.optionid,g.sales FROM " . tablename('shopping_order_goods') . " o left join " . tablename('shopping_goods') . " g on o.goodsid=g.id "
                . " WHERE o.orderid='{$id}'");
        foreach ($goods as $item) {
            if ($minus) {
                //属性
                if (!empty($item['optionid'])) {
                    pdo_query("update " . tablename('shopping_goods_option') . " set stock=stock-:stock where id=:id", array(":stock" => $item['total'], ":id" => $item['optionid']));
                }
                $data = array();
                if (!empty($item['goodstotal']) && $item['goodstotal'] != -1) {
                    $data['total'] = $item['goodstotal'] - $item['total'];
                }
                $data['sales'] = $item['sales'] + $item['total'];
                pdo_update('shopping_goods', $data, array('id' => $item['id']));
            } else {
                //属性
                if (!empty($item['optionid'])) {
                    pdo_query("update " . tablename('shopping_goods_option') . " set stock=stock+:stock where id=:id", array(":stock" => $item['total'], ":id" => $item['optionid']));
                }
                $data = array();
                if (!empty($item['goodstotal']) && $item['goodstotal'] != -1) {
                    $data['total'] = $item['goodstotal'] + $item['total'];
                }
                $data['sales'] = $item['sales'] - $item['total'];
                pdo_update('shopping_goods', $data, array('id' => $item['id']));
            }
        }
    }

    public function doWebNotice() {
        global $_GPC, $_W;
		load()->func('tpl');
        $operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
        $operation = in_array($operation, array('display')) ? $operation : 'display';

        $pindex = max(1, intval($_GPC['page']));
        $psize = 50;

        $starttime = empty($_GPC['starttime']) ? strtotime('-1 month') : strtotime($_GPC['starttime']);
        $endtime = empty($_GPC['endtime']) ? TIMESTAMP : strtotime($_GPC['endtime']) + 86399;

        $where .= " WHERE `weid` = :weid AND `createtime` >= :starttime AND `createtime` < :endtime";
        $paras = array(
            ':weid' => $_W['uniacid'],
            ':starttime' => $starttime,
            ':endtime' => $endtime
        );
        $keyword = $_GPC['keyword'];
        if (!empty($keyword)) {
            $where .= " AND `feedbackid`=:feedbackid";
            $paras[':feedbackid'] = $keyword;
        }

        $type = empty($_GPC['type']) ? 0 : $_GPC['type'];
        $type = intval($type);
        if ($type != 0) {
            $where .= " AND `type`=:type";
            $paras[':type'] = $type;
        }
        $status = empty($_GPC['status']) ? 0 : intval($_GPC['status']);
        $status = intval($status);
        if ($status != -1) {
            $where .= " AND `status` = :status";
            $paras[':status'] = $status;
        }

        $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('shopping_feedback') . $where, $paras);
        $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_feedback') . $where . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $paras);
        $pager = pagination($total, $pindex, $psize);

        $transids = array();
        foreach ($list as $row) {
            $transids[] = $row['transid'];
        }
        if (!empty($transids)) {
            $sql = "SELECT * FROM " . tablename('shopping_order') . " WHERE weid='{$_W['uniacid']}' AND transid IN ( '" . implode("','", $transids) . "' )";
            $orders = pdo_fetchall($sql, array(), 'transid');
        }
        $addressids = array();
        if(is_array($orders)){
            foreach ($orders as $transid => $order) {
            $addressids[] = $order['addressid'];
        }
        }
        
        $addresses = array();
        if (!empty($addressids)) {
            $sql = "SELECT * FROM " . tablename('shopping_address') . " WHERE weid='{$_W['uniacid']}' AND id IN ( '" . implode("','", $addressids) . "' )";
            $addresses = pdo_fetchall($sql, array(), 'id');
        }

        foreach ($list as &$feedback) {
            $transid = $feedback['transid'];
            $order = $orders[$transid];
            $feedback['order'] = $order;
            $addressid = $order['addressid'];
            $feedback['address'] = $addresses[$addressid];
        }

        include $this->template('notice');
    }

    public function getCartTotal() {
        global $_W;
        $cartotal = pdo_fetchcolumn("select sum(total) from " . tablename('shopping_cart') . " where weid = '{$_W['uniacid']}' and from_user='{$_W['fans']['from_user']}'");
        return empty($cartotal) ? 0 : $cartotal;
    }

    private function getFeedbackType($type) {
        $types = array(1 => '维权', 2 => '告警');
        return $types[intval($type)];
    }

    private function getFeedbackStatus($status) {
        $statuses = array('未解决', '用户同意', '用户拒绝');
        return $statuses[intval($status)];
    }

    public function doMobilelist() {
        global $_GPC, $_W;
        // $cart = $this->getCartGoods();
 
        

        $pindex = max(1, intval($_GPC['page']));
        $psize = 4;
        $condition = '';
        if (!empty($_GPC['ccate'])) {
            $cid = intval($_GPC['ccate']);
            $condition .= " AND ccate = '{$cid}'";
            $_GPC['pcate'] = pdo_fetchcolumn("SELECT parentid FROM " . tablename('shopping_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
        } elseif (!empty($_GPC['pcate'])) {
            $cid = intval($_GPC['pcate']);
            $condition .= " AND pcate = '{$cid}'";
        }
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        $children = array();
        $category = pdo_fetchall("SELECT * FROM " . tablename('shopping_category') . " WHERE weid = '{$_W['uniacid']}' and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][$row['id']] = $row;
                unset($category[$index]);
            }
        }
        $recommandcategory = array();
        foreach ($category as &$c) {
            if ($c['isrecommand'] == 1) {
                $c['list'] = pdo_fetchall("SELECT * FROM " . tablename('shopping_goods') . " WHERE weid = '{$_W['uniacid']}' and deleted=0 AND status = '1'  and pcate='{$c['id']}'  ORDER BY displayorder DESC, sales DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
                $c['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('shopping_goods') . " WHERE weid = '{$_W['uniacid']}'  and deleted=0  AND status = '1' and pcate='{$c['id']}'");
                $c['pager'] = pagination($c['total'], $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
                $recommandcategory[] = $c;
            }
            if (!empty($children[$c['id']])) {
                foreach ($children[$c['id']] as &$child) {
                    if ($child['isrecommand'] == 1) {
                        $child['list'] = pdo_fetchall("SELECT * FROM " . tablename('shopping_goods') . " WHERE weid = '{$_W['uniacid']}'  and deleted=0 AND status = '1'  and pcate='{$c['id']}' and ccate='{$child['id']}'  ORDER BY displayorder DESC, sales DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
                        $child['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('shopping_goods') . " WHERE weid = '{$_W['uniacid']}'  and deleted=0  AND status = '1' and pcate='{$c['id']}' and ccate='{$child['id']}' ");
                        $child['pager'] = pagination($child['total'], $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
                        $recommandcategory[] = $child;
                    }
                }
                unset($child);
            }
        }
        unset($c);
        $carttotal = $this->getCartTotal();

        //幻灯片
        $advs = pdo_fetchall("select * from " . tablename('shopping_adv') . " where enabled=1 and weid= '{$_W['uniacid']}'  order by displayorder asc");
        foreach ($advs as &$adv) {
            if (substr($adv['link'], 0, 5) != 'http:') {
                $adv['link'] = "http://" . $adv['link'];
            }
        }
        unset($adv);

        //首页推荐
        $rpindex = max(1, intval($_GPC['rpage']));
        $rpsize = 6;
        $condition = ' and isrecommand=1';
        $rlist = pdo_fetchall("SELECT * FROM " . tablename('shopping_goods') . " WHERE weid = '{$_W['uniacid']}'  and deleted=0 AND status = '1' $condition ORDER BY displayorder DESC, sales DESC LIMIT " . ($rpindex - 1) * $rpsize . ',' . $rpsize);

        include $this->template('list');
    }

    public function doMobilelistmore_rec() {
        global $_GPC, $_W;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 6;
        $condition = ' and isrecommand=1 ';
        $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_goods') . " WHERE weid = '{$_W['uniacid']}'  and deleted=0 AND status = '1' $condition ORDER BY displayorder DESC, sales DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        include $this->template('list_more');
    }

    public function doMobilelistmore() {
        global $_GPC, $_W;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 6;
        $condition = '';
        if (!empty($_GPC['ccate'])) {
            $cid = intval($_GPC['ccate']);
           // $condition .= " AND ccate = '{$cid}'";
            $_GPC['pcate'] = pdo_fetchcolumn("SELECT parentid FROM " . tablename('shopping_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
            $cate = pdo_fetchcolumn("SELECT parentid FROM " . tablename('shopping_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
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
        $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_goods') . " WHERE weid = '{$_W['weid']}' AND deleted=0 and status = '1' $condition ORDER BY displayorder DESC, sales DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        include $this->template('list_more');
    }

    public function doMobilelist2() {

        global $_GPC, $_W;
        $pindex = max(1, intval($_GPC["page"]));
        $psize = 10;
        $condition = '';
        if (!empty($_GPC['ccate'])) {
            $cid = intval($_GPC['ccate']);
            $condition .= " AND ccate = '{$cid}'";
            $_GPC['pcate'] = pdo_fetchcolumn("SELECT parentid FROM " . tablename('shopping_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
        } elseif (!empty($_GPC['pcate'])) {
            $cid = intval($_GPC['pcate']);
            $condition .= " AND pcate = '{$cid}'";
        }
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        $sort = empty($_GPC['sort']) ? 0 : $_GPC['sort'];
        $sortfield = "displayorder asc";

        $sortb0 = empty($_GPC['sortb0']) ? "desc" : $_GPC['sortb0'];
        $sortb1 = empty($_GPC['sortb1']) ? "desc" : $_GPC['sortb1'];
        $sortb2 = empty($_GPC['sortb2']) ? "desc" : $_GPC['sortb2'];
        $sortb3 = empty($_GPC['sortb3']) ? "asc" : $_GPC['sortb3'];

        if ($sort == 0) {
            $sortb00 = $sortb0 == "desc" ? "asc" : "desc";
            $sortfield = "createtime " . $sortb0;
            $sortb11 = "desc";
            $sortb22 = "desc";
            $sortb33 = "asc";
        } else if ($sort == 1) {
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
        } else if ($sort == 3) {
            $sortb33 = $sortb3 == "asc" ? "desc" : "asc";
            $sortfield = "marketprice " . $sortb3;
            $sortb00 = "desc";
            $sortb11 = "desc";
            $sortb22 = "desc";
        }



        $sorturl = $this->createMobileUrl('list2', array("keyword" => $_GPC['keyword'], "pcate" => $_GPC['pcate'], "ccate" => $_GPC['ccate']), true);
        if (!empty($_GPC['isnew'])) {
            $condition .= " AND isnew = 1";
            $sorturl.="&isnew=1";
        }


        if (!empty($_GPC['ishot'])) {
            $condition .= " AND ishot = 1";
            $sorturl.="&ishot=1";
        }
        if (!empty($_GPC['isdiscount'])) {
            $condition .= " AND isdiscount = 1";
            $sorturl.="&isdiscount=1";
        }
        if (!empty($_GPC['istime'])) {
            $condition .= " AND istime = 1 and " . time() . ">=timestart and " . time() . "<=timeend";
            $sorturl.="&istime=1";
        }

        $children = array();



        $category = pdo_fetchall("SELECT * FROM " . tablename('shopping_category') . " WHERE weid = '{$_W['uniacid']}' and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][$row['id']] = $row;
                unset($category[$index]);
            }
        }
        $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_goods') . " WHERE weid = '{$_W['uniacid']}'  and deleted=0 AND status = '1' $condition ORDER BY $sortfield LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        foreach ($list as &$r) {
            if ($r['istime'] == 1) {
                $arr = $this->time_tran($r['timeend']);
                $r['timelaststr'] = $arr[0];
                $r['timelast'] = $arr[1];
            }
        }
        unset($r);


        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('shopping_goods') . " WHERE weid = '{$_W['uniacid']}'  and deleted=0  AND status = '1' $condition");
        $pager = pagination($total, $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
        $carttotal = $this->getCartTotal();
        include $this->template('list2');
    }

    function time_tran($the_time) {

        $timediff = $the_time - time();
        $days = intval($timediff / 86400);
        if (strlen($days) <= 1) {
            $days = "0" . $days;
        }
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        ;
        if (strlen($hours) <= 1) {
            $hours = "0" . $hours;
        }
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        if (strlen($mins) <= 1) {
            $mins = "0" . $mins;
        }
        $secs = $remain % 60;
        if (strlen($secs) <= 1) {
            $secs = "0" . $secs;
        }
        $ret = "";
        if ($days > 0) {
            $ret.=$days . " 天 ";
        }
        if ($hours > 0) {
            $ret.=$hours . ":";
        }
        if ($mins > 0) {
            $ret.=$mins . ":";
        }

        $ret.=$secs;

        return array("倒计时 " . $ret, $timediff);
    }

    public function doMobileMyCart() {
        global $_W, $_GPC;
        $this->checkAuth();
        $op = $_GPC['op'];
        if ($op == 'add') {
            $goodsid = intval($_GPC['id']);
            $total = intval($_GPC['total']);
            $total = empty($total) ? 1 : $total;
            $optionid = intval($_GPC['optionid']);
            $goods = pdo_fetch("SELECT id, type, total,marketprice,maxbuy FROM " . tablename('shopping_goods') . " WHERE id = :id", array(':id' => $goodsid));
            if (empty($goods)) {
                $result['message'] = '抱歉，该商品不存在或是已经被删除！';
                message($result, '', 'ajax');
            }
            $marketprice = $goods['marketprice'];
            if (!empty($optionid)) {
                $option = pdo_fetch("select marketprice from " . tablename('shopping_goods_option') . " where id=:id limit 1", array(":id" => $optionid));
                if (!empty($option)) {
                    $marketprice = $option['marketprice'];
                }
            }

            $row = pdo_fetch("SELECT id, total FROM " . tablename('shopping_cart') . " WHERE from_user = :from_user AND weid = '{$_W['uniacid']}' AND goodsid = :goodsid  and optionid=:optionid", array(':from_user' => $_W['fans']['from_user'], ':goodsid' => $goodsid,':optionid'=>$optionid));
            if ($row == false) {
                //不存在
                $data = array(
                    'weid' => $_W['uniacid'],
                    'goodsid' => $goodsid,
                    'goodstype' => $goods['type'],
                    'marketprice' => $marketprice,
                    'from_user' => $_W['fans']['from_user'],
                    'total' => $total,
                    'optionid' => $optionid
                );
                pdo_insert('shopping_cart', $data);
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
                    'marketprice' => $marketprice,
                    'total' => $t,
                    'optionid' => $optionid
                );
                pdo_update('shopping_cart', $data, array('id' => $row['id']));
            }

            //返回数据
            $carttotal = $this->getCartTotal();

            $result = array(
                'result' => 1,
                'total' => $carttotal
            );
            die(json_encode($result));
        } else if ($op == 'clear') {
            pdo_delete('shopping_cart', array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid']));
            die(json_encode(array("result" => 1)));
        } else if ($op == 'remove') {
            $id = intval($_GPC['id']);
            pdo_delete('shopping_cart', array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid'], 'id' => $id));
            die(json_encode(array("result" => 1, "cartid" => $id)));
        } else if ($op == 'update') {
            $id = intval($_GPC['id']);
            $num = intval($_GPC['num']);
            $sql = "update " . tablename('shopping_cart') . " set total=$num where id=:id";
            pdo_query($sql, array(":id" => $id));
            die(json_encode(array("result" => 1)));
        } else {
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_cart') . " WHERE  weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'");
            $totalprice = 0;
            if (!empty($list)) {
                foreach ($list as &$item) {
                    $goods = pdo_fetch("SELECT  title, thumb, marketprice, unit, total,maxbuy FROM " . tablename('shopping_goods') . " WHERE id=:id limit 1", array(":id" => $item['goodsid']));
                    //属性
                    $option = pdo_fetch("select title,marketprice,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $item['optionid']));
                    if ($option) {
                        $goods['title'] = $goods['title'];
                        $goods['optionname'] = $option['title'];
                        $goods['marketprice'] = $option['marketprice'];
                        $goods['total'] = $option['stock'];
                    }
                    $item['goods'] = $goods;
                    $item['totalprice'] = (floatval($goods['marketprice']) * intval($item['total']));
                    $totalprice += $item['totalprice'];
                }
                unset($item);
            }
            include $this->template('cart');
        }
    }

    public function doMobileConfirm() {
        global $_W, $_GPC;
        //$this->checkAuth();
        checkauth();


        $totalprice = 0;
        $allgoods = array();

        $id = intval($_GPC['id']);
        $optionid = intval($_GPC['optionid']);
        $total = intval($_GPC['total']);
        if (empty($total)) {
            $total = 1;
        }
        $direct = false; //是否是直接购买
        $returnurl = ""; //当前连接

        if (!empty($id)) {
            $item = pdo_fetch("select id,thumb,title,weight,marketprice,total,type,totalcnf,sales,unit,istime,timeend from " . tablename("shopping_goods") . " where id=:id limit 1", array(":id" => $id));

            if ($item['istime'] == 1) {
                if (time() > $item['timeend']) {
                    message('抱歉，商品限购时间已到，无法购买了！', referer(), "error");
                }
            }

            if (!empty($optionid)) {
                $option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $optionid));
              
                if ($option) {
                    $item['optionid'] = $optionid;
                    $item['title'] = $item['title'];
                    $item['optionname'] = $option['title'];
                    $item['marketprice'] = $option['marketprice'];
                    $item['weight'] = $option['weight'];
                }
            }
            $item['stock'] = $item['total'];
            $item['total'] = $total;
            $item['totalprice'] = $total * $item['marketprice'];
            $allgoods[] = $item;
            $totalprice+= $item['totalprice'];
            if ($item['type'] == 1) {
                $needdispatch = true;
            }
            $direct = true;
            $returnurl = $this->createMobileUrl("confirm", array("id" => $id, "optionid" => $optionid, "total" => $total));
        }
        if (!$direct) {
            //如果不是直接购买（从购物车购买）
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_cart') . " WHERE  weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'");
            if (!empty($list)) {
                foreach ($list as &$g) {
                    $item = pdo_fetch("select id,thumb,title,weight,marketprice,total,type,totalcnf,sales,unit from " . tablename("shopping_goods") . " where id=:id limit 1", array(":id" => $g['goodsid']));
                    //属性
                    $option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $g['optionid']));
                    if ($option) {
                        $item['optionid'] = $g['optionid'];
                        $item['title'] = $item['title'];
                        $item['optionname'] = $option['title'];
                        $item['marketprice'] = $option['marketprice'];
                        $item['weight'] = $option['weight'];
                    }
                    $item['stock'] = $item['total'];
                    $item['total'] = $g['total'];
                    $item['totalprice'] = $g['total'] * $item['marketprice'];
                    $allgoods[] = $item;
                    $totalprice+= $item['totalprice'];
                    if ($item['type'] == 1) {
                        $needdispatch = true;
                    }
                }
                unset($g);
            }
            $returnurl = $this->createMobileUrl("confirm");
        }

        if (count($allgoods) <= 0) {
            header("location: " . $this->createMobileUrl('myorder'));
            exit();
        }
    
        
        //配送方式
        $dispatch = pdo_fetchall("select id,dispatchname,dispatchtype,firstprice,firstweight,secondprice,secondweight from " . tablename("shopping_dispatch") . " WHERE weid = {$_W['uniacid']} order by displayorder desc");
        foreach ($dispatch as &$d) {

            $weight = 0;

            foreach ($allgoods as $g) {
                $weight+=$g['weight'] * $g['total'];
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

        if (checksubmit('submit')) {
            
               //是否自提
        $sendtype=1;
        
            $address = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE id = :id", array(':id' => intval($_GPC['address'])));
            if (empty($address)) {
                message('抱歉，请您填写收货地址！');
            }
            //商品价格
            $goodsprice = 0;
            foreach ($allgoods as $row) {
                if ($item['stock'] != -1 && $row['total'] > $item['stock']) {
                    message('抱歉，“' . $row['title'] . '”此商品库存不足！', $this->createMobileUrl('confirm'), 'error');
                }
                $goodsprice+= $row['totalprice'];
            }
            //运费
            $dispatchid = intval($_GPC['dispatch']);
            $dispatchprice = 0;
            foreach ($dispatch as $d) {
                if ($d['id'] == $dispatchid) {
                    $dispatchprice = $d['price'];
                    $sendtype = $d['dispatchtype'];
                }
            }
            $data = array(
                'weid' => $_W['uniacid'],
                'from_user' => $_W['fans']['from_user'],
                'ordersn' => date('md') . random(4, 1),
                'price' => $goodsprice + $dispatchprice,
                'dispatchprice' => $dispatchprice,
                'goodsprice' => $goodsprice,
                'status' => 0,
                'sendtype' =>intval($sendtype),
                'dispatch' => $dispatchid,
                'paytype' => '2',
                'goodstype' => intval($cart['type']),
                'remark' => $_GPC['remark'],
                'addressid' => $address['id'],
                'createtime' => TIMESTAMP,
            );
            pdo_insert('shopping_order', $data);
            $orderid = pdo_insertid();
            //插入订单商品
            foreach ($allgoods as $row) {
                if (empty($row)) {
                    continue;
                }
                $d = array(
                    'weid' => $_W['uniacid'],
                    'goodsid' => $row['id'],
                    'orderid' => $orderid,
                    'total' => $row['total'],
                    'price' => $row['marketprice'],
                    'createtime' => TIMESTAMP,
                    'optionid' => $row['optionid']
                );
                $o = pdo_fetch("select title from ".tablename('shopping_goods_option')." where id=:id limit 1",array(":id"=>$row['optionid']));
                if(!empty($o)){
                    $d['optionname'] = $o['title'];
                }
                pdo_insert('shopping_order_goods', $d);
            }
            //清空购物车
            if (!$direct) {
                pdo_delete("shopping_cart", array("weid" => $_W['uniacid'], "from_user" => $_W['fans']['from_user']));
            }
            //$this->setCartGoods(array());
            //变更商品库存
            $this->setOrderStock($orderid);

            //
            //  message('提交订单成功，现在跳转至付款页面...', $this->createMobileUrl('pay', array('orderid' => $orderid)), 'success');

            message('提交订单成功,现在跳转到付款页面...',$this->createMobileUrl('pay', array('orderid' => $orderid)),'success');
            //die("<script>alert('提交订单成功,现在跳转到付款页面...');location.href='" . $this->createMobileUrl('pay', array('orderid' => $orderid)) . "';</script>");
        }
        $carttotal = $this->getCartTotal();
        $profile = fans_search($_W['fans']['from_user'], array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
        $row = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE isdefault = 1 and openid = :openid limit 1", array(':openid' => $_W['fans']['from_user']));
        include $this->template('confirm');
 
    }

    //设置订单积分
    public function setOrderCredit($orderid, $add = true) {
        $order = pdo_fetch("SELECT * FROM " . tablename('shopping_order') . " WHERE id = :id limit 1", array(':id' => $orderid));
        if (empty($order)) {
            return;
        }
        $ordergoods = pdo_fetchall("SELECT goodsid, total FROM " . tablename('shopping_order_goods') . " WHERE orderid = '{$orderid}'", array(), 'goodsid');
        if (!empty($ordergoods)) {
            $goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total,credit FROM " . tablename('shopping_goods') . " WHERE id IN ('" . implode("','", array_keys($ordergoods)) . "')");
        }

        //增加积分
        if (!empty($goods)) {

            $credits = 0;
            foreach ($goods as $g) {
                $credits+=$g['credit'];
            }
            $fans = fans_search($order['from_user'], array("credit1"));
            if (!empty($fans)) {
                if ($add) {
                    $new_credit = $credits + $fans['credit1'];
                } else {
                    $new_credit = $fans['credit1'] - $credits;
                    if ($new_credit <= 0) {
                        $new_credit = 0;
                    }
                }
                //fans_update($order['from_user'], array("credit1" => $new_credit));
                pdo_update('mc_members', array("credit1" => $new_credit), array('uid' => $uid));
            }
        }
    }

    public function doMobilePay() {
        global $_W, $_GPC;
        $this->checkAuth();
        $orderid = intval($_GPC['orderid']);
        $order = pdo_fetch("SELECT * FROM " . tablename('shopping_order') . " WHERE id = :id", array(':id' => $orderid));
        if ($order['status'] != '0') {
            message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('myorder'), 'error');
        }
        if (checksubmit('codsubmit')) {
            $ordergoods = pdo_fetchall("SELECT goodsid, total,optionid FROM " . tablename('shopping_order_goods') . " WHERE orderid = '{$orderid}'", array(), 'goodsid');
            if (!empty($ordergoods)) {
                $goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total,credit FROM " . tablename('shopping_goods') . " WHERE id IN ('" . implode("','", array_keys($ordergoods)) . "')");
            }



            //邮件提醒
            if (!empty($this->module['config']['noticeemail'])) {

                $address = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE id = :id", array(':id' => $order['addressid']));

                $body = "<h3>购买商品清单</h3> <br />";

                if (!empty($goods)) {
                    foreach ($goods as $row) {

                        //属性
                        $option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $ordergoods[$row['id']]['optionid']));
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
            pdo_update('shopping_order', array('status' => '1', 'paytype' => '3'), array('id' => $orderid));


            //增加积分
            $this->setOrderCredit($orderid);

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
        $params['virtual'] = $order['goodstype'] == 2 ? true : false;
        include $this->template('pay');
    }

    public function doMobileContactUs() {
        global $_W;
        $cfg = $this->module['config'];

        include $this->template('contactus');
    }

    public function doMobileMyOrder() {
        global $_W, $_GPC;
        $this->checkAuth();
        $op = $_GPC['op'];
        if ($op == 'confirm') {
            $orderid = intval($_GPC['orderid']);
            $order = pdo_fetch("SELECT * FROM " . tablename('shopping_order') . " WHERE id = :id AND from_user = :from_user", array(':id' => $orderid, ':from_user' => $_W['fans']['from_user']));
            if (empty($order)) {
                message('抱歉，您的订单不存或是已经被取消！', $this->createMobileUrl('myorder'), 'error');
            }
            pdo_update('shopping_order', array('status' => 3), array('id' => $orderid, 'from_user' => $_W['fans']['from_user']));
            message('确认收货完成！', $this->createMobileUrl('myorder'), 'success');
        } else if ($op == 'detail') {

            $orderid = intval($_GPC['orderid']);
            $item = pdo_fetch("SELECT * FROM " . tablename('shopping_order') . " WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}' and id='{$orderid}' limit 1");
            if (empty($item)) {
                message('抱歉，您的订单不存或是已经被取消！', $this->createMobileUrl('myorder'), 'error');
            }
            $goodsid = pdo_fetchall("SELECT goodsid,total FROM " . tablename('shopping_order_goods') . " WHERE orderid = '{$orderid}'", array(), 'goodsid');
            $goods = pdo_fetchall("SELECT g.id, g.title, g.thumb, g.unit, g.marketprice, o.total,o.optionid FROM " . tablename('shopping_order_goods') . " o left join " . tablename('shopping_goods') . " g on o.goodsid=g.id "
                    . " WHERE o.orderid='{$orderid}'");
            foreach ($goods as &$g) {
                //属性
                $option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $g['optionid']));
                if ($option) {
                    $g['title'] = "[" . $option['title'] . "]" . $g['title'];
                    $g['marketprice'] = $option['marketprice'];
                }
            }
            unset($g);


            $dispatch = pdo_fetch("select id,dispatchname from " . tablename('shopping_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
            include $this->template('order_detail');
        } else {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;

            $status = intval($_GPC['status']);
            $where = " weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'";

            if ($status == 2) {
                $where.=" and ( status=1 or status=2 )";
            } else {
                $where.=" and status=$status";
            }
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_order') . " WHERE $where ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('shopping_order') . " WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'");
            $pager = pagination($total, $pindex, $psize);

            if (!empty($list)) {
                foreach ($list as &$row) {
                	$goodsid = pdo_fetchall("SELECT goodsid,total FROM " . tablename('shopping_order_goods') . " WHERE orderid = '{$row['id']}'", array(), 'goodsid');
                    $goods = pdo_fetchall("SELECT g.id, g.title, g.thumb, g.unit, g.marketprice,o.total,o.optionid FROM " . tablename('shopping_order_goods') . " o left join " . tablename('shopping_goods') . " g on o.goodsid=g.id "
                            . " WHERE o.orderid='{$row['id']}'");
                    foreach ($goods as &$item) {
                        //属性
                        $option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $item['optionid']));
                        if ($option) {
                            $item['title'] = "[" . $option['title'] . "]" . $item['title'];
                            $item['marketprice'] = $option['marketprice'];
                        }
                    }
                    unset($item);
                    $row['goods'] = $goods;
                    $row['total'] = $goodsid;
                    $row['dispatch'] = pdo_fetch("select id,dispatchname from " . tablename('shopping_dispatch') . " where id=:id limit 1", array(":id" => $row['dispatch']));
                }
            }
            include $this->template('order');
        }
    }

    public function doMobileDetail() {
        global $_W, $_GPC;
        $goodsid = intval($_GPC['id']);
        $goods = pdo_fetch("SELECT * FROM " . tablename('shopping_goods') . " WHERE id = :id", array(':id' => $goodsid));

        if (empty($goods)) {
            message('抱歉，商品不存在或是已经被删除！');
        }
        if ($goods['istime'] == 1) {
            if (time() < $goods['timestart']) {
                message('抱歉，还未到购买时间, 暂时无法购物哦~', referer(), "error");
            }
            if (time() > $goods['timeend']) {
                message('抱歉，商品限购时间已到，不能购买了哦~', referer(), "error");
            }
        }
        //浏览量
        pdo_query("update " . tablename('shopping_goods') . " set viewcount=viewcount+1 where id=:id and weid='{$_W['uniacid']}' ", array(":id" => $goodsid));
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

        $marketprice = $goods['marketprice'];
        $productprice= $goods['productprice'];
        $stock = $goods['total'];

      
        //规格及规格项
           $allspecs = pdo_fetchall("select * from " . tablename('shopping_spec') . " where goodsid=:id order by displayorder asc", array(':id' => $goodsid));
           foreach ($allspecs as &$s) {
                 $s['items'] = pdo_fetchall("select * from " . tablename('shopping_spec_item') . " where  `show`=1 and specid=:specid order by displayorder asc", array(":specid" => $s['id']));
           }
           unset($s);
          
           //处理规格项
           $options = pdo_fetchall("select id,title,thumb,marketprice,productprice,costprice,point, stock,weight,specs from " . tablename('shopping_goods_option') . " where goodsid=:id order by id asc", array(':id' => $goodsid));

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
        $params = pdo_fetchall("SELECT * FROM " . tablename('shopping_goods_param') . " WHERE goodsid=:goodsid order by displayorder asc", array(":goodsid" => $goods['id']));
        $carttotal = $this->getCartTotal();
        include $this->template('detail');
    }

    public function doMobileAddress() {
        global $_W, $_GPC;
        $from = $_GPC['from'];
        $returnurl = urldecode($_GPC['returnurl']);
        $this->checkAuth();
        // $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'post';
        $operation = $_GPC['op'];

        if ($operation == 'post') {
            $id = intval($_GPC['id']);
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
                pdo_update('shopping_address', $data, array('id' => $id));
                message($id, '', 'ajax');
            } else {
                pdo_update('shopping_address', array('isdefault' => 0), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
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
            $address = pdo_fetch("select isdefault from " . tablename('shopping_address') . " where id='{$id}' and weid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");
            if(!empty($address) && empty($address['isdefault'])){
                pdo_update('shopping_address', array('isdefault' => 0), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                pdo_update('shopping_address', array('isdefault' => 1), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user'], 'id' => $id));
            }
            message(1, '', 'ajax');
        } elseif ($operation == 'detail') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id, realname, mobile, province, city, area, address FROM " . tablename('shopping_address') . " WHERE id = :id", array(':id' => $id));
            message($row, '', 'ajax');
        } elseif ($operation == 'remove') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $address = pdo_fetch("select isdefault from " . tablename('shopping_address') . " where id='{$id}' and weid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");

                if (!empty($address)) {
                    //pdo_delete("shopping_address",  array('id'=>$id, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                    //修改成不直接删除，而设置deleted=1
                    pdo_update("shopping_address", array("deleted" => 1, "isdefault" => 0), array('id' => $id, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));

                    if ($address['isdefault'] == 1) {
                        //如果删除的是默认地址，则设置是新的为默认地址
                        $maxid = pdo_fetchcolumn("select max(id) as maxid from " . tablename('shopping_address') . " where weid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");
                        if (!empty($maxid)) {
                            pdo_update('shopping_address', array('isdefault' => 1), array('id' => $maxid, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                            die(json_encode(array("result" => 1, "maxid" => $maxid)));
                        }
                    }
                }
            }
            die(json_encode(array("result" => 1, "maxid" => 0)));
        } else {
            $profile = fans_search($_W['fans']['from_user'], array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
            $address = pdo_fetchall("SELECT * FROM " . tablename('shopping_address') . " WHERE deleted=0 and openid = :openid", array(':openid' => $_W['fans']['from_user']));
            $carttotal = $this->getCartTotal();
            include $this->template('address');
        }
    }

    private function checkAuth() {
        global $_W;
        checkauth();
    }

    private function changeWechatSend($id, $status, $msg = '') {
		global $_W;
		$paylog = pdo_fetch("SELECT plid, openid, tag FROM " . tablename('core_paylog') . " WHERE tid = '{$id}' AND status = 1 AND type = 'wechat'");
		if (!empty($paylog['openid'])) {
			$paylog['tag'] = iunserializer($paylog['tag']);
			$send = array(
					'appid' => $_W['account']['payment']['wechat']['appid'],
					'openid' => $paylog['openid'],
					'transid' => $paylog['tag']['transaction_id'],
					'out_trade_no' => $paylog['plid'],
					'deliver_timestamp' => TIMESTAMP,
					'deliver_status' => $status,
					'deliver_msg' => $msg,
			);
			$sign = $send;
			$sign['appkey'] = $_W['account']['payment']['wechat']['signkey'];
			ksort($sign);
			foreach ($sign as $key => $v) {
				$key = strtolower($key);
				$string .= "{$key}={$v}&";
			}
			$send['app_signature'] = sha1(rtrim($string, '&'));
			$send['sign_method'] = 'sha1';
			$acid = $paylog['tag']['acid'];
			$account = WeAccount::create($acid);
			$response = $account->changeOrderStatus($send);
			if (is_error($response)) {
				message($response['message']);
			}
		}
	}

    public function payResult($params) {
        $fee = intval($params['fee']);
        $data = array('status' => $params['result'] == 'success' ? 1 : 0);
        if ($params['type'] == 'wechat') {
            $data['transid'] = $params['tag']['transaction_id'];
        }
        pdo_update('shopping_order', $data, array('id' => $params['tid']));
        if ($params['from'] == 'return') {
            //邮件提醒
            if (!empty($this->module['config']['noticeemail'])) {
                $order = pdo_fetch("SELECT price, from_user FROM " . tablename('shopping_order') . " WHERE id = '{$params['tid']}'");
                $ordergoods = pdo_fetchall("SELECT goodsid, total FROM " . tablename('shopping_order_goods') . " WHERE orderid = '{$params['tid']}'", array(), 'goodsid');
                $goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total FROM " . tablename('shopping_goods') . " WHERE id IN ('" . implode("','", array_keys($ordergoods)) . "')");
                $address = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE id = :id", array(':id' => $order['addressid']));
                $body = "<h3>购买商品清单</h3> <br />";
                if (!empty($goods)) {
                    foreach ($goods as $row) {
                        $body .= "名称：{$row['title']} ，数量：{$ordergoods[$row['id']]['total']} <br />";
                    }
                }
                $body .= "<br />总金额：{$order['price']}元 （已付款）<br />";
                $body .= "<h3>购买用户详情</h3> <br />";
                $body .= "真实姓名：{$address['realname']} <br />";
                $body .= "地区：{$address['province']} - {$address['city']} - {$address['area']}<br />";
                $body .= "详细地址：{$address['address']} <br />";
                $body .= "手机：{$address['mobile']} <br />";
                ihttp_email($this->module['config']['noticeemail'], '微商城订单提醒', $body);
            }
            $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
            $credit = $setting['creditbehaviors']['currency'];
            if ($params['type'] == $credit) {
                message('支付成功！', $this->createMobileUrl('myorder'), 'success');
            } else {
                message('支付成功！', '../../app/' . $this->createMobileUrl('myorder'), 'success');
            }
        }
    }

   
   

    public function doWebExpress() {
        global $_W, $_GPC;
        // pdo_query('DROP TABLE ims_shopping_express');
        //pdo_query("CREATE TABLE IF NOT EXISTS `ims_shopping_express` (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',  `express_name` varchar(50) NOT NULL COMMENT '分类名称',  `express_price` varchar(10) NOT NULL DEFAULT '0',  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',  `express_area` varchar(50) NOT NULL COMMENT '配送区域',  `enabled` tinyint(1) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ");
        //pdo_query("ALTER TABLE  `ims_shopping_order` ADD  `expressprice` VARCHAR( 10 ) NOT NULL AFTER  `totalnum` ;");
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_express') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
                if (empty($_GPC['express_name'])) {
                    message('抱歉，请输入物流名称！');
                }
                $data = array(
                    'weid' => $_W['uniacid'],
                    'displayorder' => intval($_GPC['express_name']),
                    'express_name' => $_GPC['express_name'],
                    'express_url' => $_GPC['express_url'],
                    'express_area' => $_GPC['express_area'],
                );
                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update('shopping_express', $data, array('id' => $id));
                } else {
                    pdo_insert('shopping_express', $data);
                    $id = pdo_insertid();
                }
                message('更新物流成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
            }
            //修改
            $express = pdo_fetch("SELECT * FROM " . tablename('shopping_express') . " WHERE id = '$id' and weid = '{$_W['uniacid']}'");
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $express = pdo_fetch("SELECT id  FROM " . tablename('shopping_express') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
            if (empty($express)) {
                message('抱歉，物流方式不存在或是已经被删除！', $this->createWebUrl('express', array('op' => 'display')), 'error');
            }
            pdo_delete('shopping_express', array('id' => $id));
            message('物流方式删除成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('express', TEMPLATE_INCLUDEPATH, true);
    }

    public function doWebDispatch() {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {

            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_dispatch') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
        } elseif ($operation == 'post') {

            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
                $data = array(
                    'weid' => $_W['uniacid'],
                    'displayorder' => intval($_GPC['dispatch_name']),
                    'dispatchtype' => intval($_GPC['dispatchtype']),
                    'dispatchname' => $_GPC['dispatchname'],
                    'express' => $_GPC['express'],
                    'firstprice' => $_GPC['firstprice'],
                    'firstweight' => $_GPC['firstweight'],
                    'secondprice' => $_GPC['secondprice'],
                    'secondweight' => $_GPC['secondweight'],
                    'description' => $_GPC['description']
                );
                if (!empty($id)) {
                    pdo_update('shopping_dispatch', $data, array('id' => $id));
                } else {
                    pdo_insert('shopping_dispatch', $data);
                    $id = pdo_insertid();
                }
                message('更新配送方式成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
            }
            //修改
            $dispatch = pdo_fetch("SELECT * FROM " . tablename('shopping_dispatch') . " WHERE id = '$id' and weid = '{$_W['uniacid']}'");
            $express = pdo_fetchall("select * from " . tablename('shopping_express') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $dispatch = pdo_fetch("SELECT id  FROM " . tablename('shopping_dispatch') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
            if (empty($dispatch)) {
                message('抱歉，配送方式不存在或是已经被删除！', $this->createWebUrl('dispatch', array('op' => 'display')), 'error');
            }
            pdo_delete('shopping_dispatch', array('id' => $id));
            message('配送方式删除成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('dispatch', TEMPLATE_INCLUDEPATH, true);
    }

    public function doWebAdv() {
        global $_W, $_GPC;
            load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_adv') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
        } elseif ($operation == 'post') {

            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
                $data = array(
                    'weid' => $_W['uniacid'],
                    'advname' => $_GPC['advname'],
                    'link' => $_GPC['link'],
                    'enabled' => intval($_GPC['enabled']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'thumb'=>$_GPC['thumb']
                );
           

                if (!empty($id)) {
                    pdo_update('shopping_adv', $data, array('id' => $id));
                } else {
                    pdo_insert('shopping_adv', $data);
                    $id = pdo_insertid();
                }
                message('更新幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
            }
            $adv = pdo_fetch("select * from " . tablename('shopping_adv') . " where id=:id and weid=:weid limit 1", array(":id" => $id, ":weid" => $_W['uniacid']));
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $adv = pdo_fetch("SELECT id  FROM " . tablename('shopping_adv') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
            if (empty($adv)) {
                message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('adv', array('op' => 'display')), 'error');
            }
            pdo_delete('shopping_adv', array('id' => $id));
            message('幻灯片删除成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('adv', TEMPLATE_INCLUDEPATH, true);
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
}
