<?php

defined('IN_IA') or exit('Access Denied');
class VoteModuleSite extends ModuleSite {

    public function doWebList(){
        global $wpdb,$_W;
        
        $search_condition = trim($_GET['range']);
        $search_content = trim($_GET['indata']);
        $search = array(
            'all' => '',
            'vote_id' => "AND id LIKE '%%{$search_content}%%'",
            'vote_name' => "AND title LIKE '%%{$search_content}%%'",
        );
        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vote WHERE gweid='{$_W['gweid']}' {$search[$search_condition]}");
        $pindex = max(1, intval($_GET['page']));
        $psize = 5;
        $pindex = min(max(ceil($total/$psize),1),$pindex );
        $offset=($pindex - 1) * $psize;
        $pager = $this->pagination($total, $pindex, $psize);
        $list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}vote WHERE gweid='{$_W['gweid']}' {$search[$search_condition]} ORDER BY `id` DESC Limit {$offset},{$psize}",ARRAY_A);
        if(is_array($list))
            foreach($list as &$wxwall_element)
                $wxwall_element['count'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wxwall_message WHERE wxwall_id=%d",$wxwall_element['id']));
        include $this->template('list');
    }

    public function doWebEdit(){
        global $wpdb,$_W;
        $id = intval($_GET['id']);
        if (!empty($id)) {
            $reply = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}vote WHERE id = %d ORDER BY `id` DESC", $id),ARRAY_A);
            $options = $wpdb->get_results($wpdb->prepare("SELECT * from {$wpdb->prefix}vote_option where vote_id=%d order by id asc", $id),ARRAY_A);     
        }
        if (!$reply) {
            $now = time();
            $reply = array(
                "starttime" => $now,
                "endtime" => strtotime(date("Y-m-d H:i", $now + 7 * 24 * 3600)),
            );
        }
        $upload =wp_upload_dir();
        $baseurl=$upload['baseurl'];
        if($_W['ispost']){
            require_once 'wp-content/themes/ReeooV3/wesite/common/upload.php';
            $insert = array(
                'gweid' => $_W['gweid'],
                'title' => $_POST['title'],
                'description' => stripslashes($_POST['description']),
                'votetype' => $_POST['votetype'],
                'votelimit' => $_POST['votelimit'],
                'votetimes' => $_POST['votetimes'],
                'votetotal' => $_POST['votetotal'],
                'isimg' => $_POST['isimg'],
                'starttime' => strtotime($_POST['datelimit-start']),
                'endtime' => strtotime($_POST['datelimit-end'])
            );
            if(!$_FILES['thumb']['error']){
                $up=new upphoto();
                $insert['thumb'] = $up->up_photo($_FILES['thumb']);
            }

            if (empty($id)) {
                if ($insert['starttime'] <= time()) {
                    $insert['isshow'] = 1;
                } else {
                    $insert['isshow'] = 0;
                }
                $wpdb->insert($wpdb->prefix.'vote', $insert);
                $vote_id = $wpdb -> insert_id;
            } else {
                if(isset($insert['thumb']) && $reply['thumb']!= $insert['thumb'])
                    file_unlink($reply['thumb']);
                file_unlink_from_xml_update($reply['description'],stripslashes($_POST['description']));
                $wpdb->update($wpdb->prefix.'vote', $insert, array('id' => $id));
                $vote_id = $id;
            }  
            $options = array();
            $option_ids = $_POST['option_id'];
            $option_titles = $_POST['option_title'];
            $option_thumb_olds = $_POST['option_thumb_old'];
            $files =$_FILES;
            $len = count($option_ids);
            $ids = array();
            for ($i = 0; $i < $len; $i++) {
                 $item_id  = $option_ids[$i];
                 $a = array(
                     "title"=>$option_titles[$i],
                     "vote_id"=>$vote_id
                 );
                 
                 $f = 'option_thumb_'.$item_id;
                 $old = $_POST['option_thumb_'.$item_id];
                 $up=new upphoto();
                 if (!empty($files[$f]['tmp_name'])) {
                    if(!$files[$f]['error'])
                        $a['thumb'] = $up->up_photo($files[$f]);
                    else
                        $a['thumb'] = "";
                
                }else if(!empty($old)){
                    $a['thumb'] = $old;
                }
               if((int)$item_id==0){
                    $wpdb->insert($wpdb->prefix."vote_option", $a);
                    $item_id = $wpdb->insert_id;
                } else {
                    if(!empty($old) && $old!= $a['thumb'])
                        file_unlink($old);

                    $wpdb->update($wpdb->prefix."vote_option", $a, array('id' => $item_id));
                }  
                $ids[] = $item_id;
             }
             if(!empty($ids)){
                $wpdb->query("delete from ".tablename('vote_option')." where id not in ( ".implode(',',$ids).") AND vote_id='{$id}'");    
             }
             header("Location: {$this->createWebUrl('List',array())}");
        }
        
        include $this->template('edit');
    }

    public function doWebitem(){
        $tag = $this -> random(32);
        global $_GPC;
        $type = $_GPC['type'];
        include $this->template('item');
    }

    public function doWebVoteDelete() {
        global $wpdb;
        $rid = $_POST['vote_id'];
        $reply = $wpdb->get_row($wpdb->prepare("SELECT `thumb`,`description` FROM {$wpdb->prefix}vote WHERE id = %d ORDER BY `id` DESC", $rid),ARRAY_A);
        file_unlink($reply['thumb']);
        file_unlink_from_xml($reply['description']);
        $options = $wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}vote_option where vote_id=%d order by id asc", $rid),ARRAY_A);
        $wpdb->delete($wpdb->prefix.'vote', array('id' => $rid));
        $wpdb->delete($wpdb->prefix.'vote_fans', array('vote_id' => $rid));
        $wpdb->delete($wpdb->prefix.'vote_option', array('vote_id' => $rid));
        echo json_encode(array('status'=>'success'));
    }

    public function random($length, $numeric = 0) {
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
    public function doMobileindex() {
        global $_GPC, $_W, $wpdb;

        $upload =wp_upload_dir();
        $baseurl=$upload['baseurl'];

        $vote_id = $_GPC['id'];
        $gweid = $_W['gweid'];

        if (empty($vote_id)) {
            message('抱歉，参数错误！', '', 'error');
        }
        $from_user = $_W['fans']['from_user'];
        $reply = $wpdb -> get_row( $wpdb -> prepare( "SELECT * FROM `{$wpdb->prefix}vote` WHERE `id`=%d LIMIT 1", $vote_id),ARRAY_A);
        if ($reply == false) {
            message('活动已经取消了！', '', 'error');
        }
        $nowtime = time();
        $endtime = $reply['endtime'];

        if ($reply['votelimit'] == 1) {
            if ($reply['votenum'] >= $reply['votetotal']) {

               header("Location: {$this->createMobileUrl('result', array('id' => $vote_id,'gweid' => $_GET['gweid']))}");
            }
        } else {
            if ($reply['starttime'] > $nowtime) {
                message('投票未开始！', '', 'error');
            } elseif ($endtime < $nowtime) {
                header("Location: {$this->createMobileUrl('result', array('id' => $vote_id,'gweid' => $_GET['gweid']))}");
            }
        }

        $limits = "";
        if ($reply['votelimit'] == 1) {
            $limits = "参与人数 " . $reply['votenum'] . " /  允许总数 " . $reply['votetotal'];
        } else {
            $limits = "投票期限: " . date('Y-m-d H:i', $reply['starttime']) . " 至 " . date('Y-m-d H:i', $endtime);
        }
        $selects = "";
        if ($reply['votetype'] == 0) {
            $selects = "（单选）";
        } else {
            $selects = "（多选）";
        }
        //判断有没有投票过
        $votetimes = 0;
        if($_W['fans']['from_user'])    
            $votetimes += $wpdb -> get_var( $wpdb -> prepare( "SELECT count(*) as cnt FROM `{$wpdb->prefix}vote_fans` where vote_id= %d and from_user=%s",$vote_id,$_W['fans']['from_user']));
        if(isset($_COOKIE['vote_'.$vote_id]))
            $votetimes += $_COOKIE['vote_'.$vote_id] ;
       
        $isvote = $votetimes>0;
       
        $list = $wpdb -> get_results( $wpdb -> prepare("SELECT * FROM `{$wpdb->prefix}vote_option` WHERE vote_id = %d ORDER by `id` ASC", $vote_id), ARRAY_A);
        $sumnum = $wpdb -> get_row( $wpdb -> prepare("SELECT sum(vote_num) FROM `{$wpdb->prefix}vote_option` WHERE vote_id = %d", $vote_id),ARRAY_A);
        $sumnum = $sumnum["sum(vote_num)"];
         foreach ($list as &$r) {
            if ($sumnum == 0) {
                $r['percent'] = 0;
            } else {
                $r['percent'] = floor($r['vote_num']  / $sumnum * 100);
            }
        }
        unset($r);
    
        //判断粉丝是否要继续投票
        $can =true;
        if($reply['votetimes']>0){
            if($votetimes>=$reply['votetimes']){
               $can =false;    
            }
        }
        
        $canvotetimes =intval( $reply['votetimes'] - $votetimes);
        if($reply['votelimit'] && $reply['votetotal'] - $reply['votenum'] < $canvotetimes)
            $canvotetimes = $reply['votetotal'] - $reply['votenum'];

        if( $can )  {
            $wpdb -> query($wpdb->prepare("UPDATE `{$wpdb->prefix}vote` SET viewnum = (viewnum + 1) WHERE vote_id = %d AND weid = %d", $vote_id, $weid));
            include $this->template('vote-content');
        }
        else{
             include $this->template('vote-end');
        }
       
    }

    function doMobilesubmit() {
        global $_GPC, $_W, $wpdb;
        //判断用户是否存在
        $vote_id = $_GPC['id'];

        if (empty($vote_id)) 
            die("参数错误!");
        $reply = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}vote` WHERE id = %d ORDER BY `id` DESC", $vote_id),ARRAY_A);
        if (!$reply) 
            die("参数错误!");

        $nowtime = time();
        $endtime = $reply['endtime'];

        if ($reply['votelimit'] == 1) {
            if ($reply['votenum'] >= $reply['votetotal']) {
                die("投票人数已满!");
            }

        } else {
            if ($reply['starttime'] > $nowtime)
                die("投票未开始!");
            elseif ($endtime < $nowtime)
                die("投票已经结束!");
        }

            //判断用户投票次数
            $vc = 0;
            if($_W['fans']['from_user'])
                $vc +=  $wpdb->get_var($wpdb->prepare("select count(*) as cnt from `{$wpdb->prefix}vote_fans` where from_user=%s and vote_id=%d",$_W['fans']['from_user'],$vote_id));
            if(isset($_COOKIE['vote_'.$vote_id]))
                $vc += $_COOKIE['vote_'.$vote_id] ;
            if($reply['votetimes']>0 && $vc>=$reply['votetimes'])
                die('您已经超过投票次数了!');
            else {

                $ids = ','.$_GPC['ids'].',';
                if(empty($ids)){
                       die("参数错误!");
                }
                //粉丝投票次数
                $wpdb->insert($wpdb->prefix.'vote_fans', array('from_user'=>$_W['fans']['from_user'],'vote_id'=>$vote_id, 'votes' => $ids,'votetime'=>time()));
                //参与人数
                $wpdb->update($wpdb->prefix.'vote', array('votenum' => ($reply['votenum'] + 1)), array('id' =>$vote_id));
                if(!$_W['fans']['from_user'])
                    setcookie('vote_'.$vote_id , (isset($_COOKIE['vote_'.$vote_id] )?$_COOKIE['vote_'.$vote_id]:0)+1 ,4102415999) ;
                //投票记录
                $item_ids = explode(",",$ids);
                foreach($item_ids as $item_id){
                     //查找投票项是否存在
                    $vote = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}vote_option` WHERE vote_id = %d and id= %d ORDER by `id` ASC", $vote_id, $item_id),ARRAY_A);
                    if($vote){
                        $wpdb->update($wpdb->prefix.'vote_option', array('vote_num' => ($vote['vote_num'] + 1)), array('id' =>$item_id));
                    }
                }
                die('');
            }
        
    }
    
    
    public function doMobileResult() {
        global $_GPC, $_W, $wpdb;

        $vote_id = $_GPC['id'];
        if (empty($vote_id)) {
            message('抱歉，参数错误！', '', 'error');
        }

        $upload =wp_upload_dir();
        $baseurl=$upload['baseurl'];

        $from_user = $_W['fans']['from_user'];
        $reply = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `{$wpdb->prefix}vote` WHERE `id`=%d LIMIT 1", $vote_id),ARRAY_A);
        if ($reply == false) {
            message('活动已经取消了！', '', 'error');
        }
       
        $limits = "";
        if ($reply['votelimit'] == 1) {
            $limits = "参与人数 " . $reply['votenum'] . " /  允许总数 " . $reply['votetotal'];
        } else {
            $endtime = $reply['endtime'];
            $limits = "投票期限: " . date('Y-m-d H:i', $reply['starttime']) . " 至 " . date('Y-m-d H:i', $endtime);
        }
        $selects = "";
        if ($reply['votetype'] == 0) {
            $selects = "（单选）";
        } else {
            $selects = "（多选）";
        }
      
        $list = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}vote_option` WHERE vote_id = %d ORDER by `id` ASC", $vote_id), ARRAY_A);
        $sumnum = $wpdb->get_var($wpdb->prepare("SELECT sum(vote_num) FROM `{$wpdb->prefix}vote_option` WHERE vote_id = %d ", $vote_id));

        foreach ($list as &$r) {
                  
        
            if ($sumnum == 0) {
                $r['percent'] = 0;
            } else {
                $r['percent'] = floor($r['vote_num'] * 100 / $sumnum);
            }
        }
        unset($r);

        $votetimes = 0;
        if($_W['fans']['from_user'])    
            $votetimes += $wpdb -> get_var( $wpdb -> prepare( "SELECT count(*) as cnt FROM `{$wpdb->prefix}vote_fans` where vote_id= %d and from_user=%s",$vote_id,$_W['fans']['from_user']));
        if(isset($_COOKIE['vote_'.$vote_id]))
            $votetimes += $_COOKIE['vote_'.$vote_id] ;        
        $canvotetimes =intval( $reply['votetimes'] - $votetimes);
        if($reply['votelimit'] && $reply['votetotal'] - $reply['votenum'] < $canvotetimes)
            $canvotetimes = $reply['votetotal'] - $reply['votenum'];

        include $this->template('vote-end');
    }

    public function doWebDisplay() {
        global $_GPC, $_W, $wpdb;

        $vote_id = $_GPC['id'];
        if (empty($vote_id)) {
            message('抱歉，参数错误！ ', '', 'error');
        }

        $upload =wp_upload_dir();
        $baseurl=$upload['baseurl'];

      
        $list = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}vote_option` WHERE vote_id = %d ORDER by `id` ASC", $vote_id), ARRAY_A);
        $sumnum = $wpdb->get_var($wpdb->prepare("SELECT sum(vote_num) FROM `{$wpdb->prefix}vote_option` WHERE vote_id = %d ", $vote_id));

        foreach ($list as &$r) {
                  
        
            if ($sumnum == 0) {
                $r['percent'] = 0;
            } else {
                $r['percent'] = floor($r['vote_num'] * 100 / $sumnum);
            }
        }
        unset($r);
        include $this->template('display');
    }

    public function doWebHistory(){
        global $wpdb,$_W;
        
        $vote_id = $_GET['id'];
        $vote_option_id = intval($_GET['option']);
        $vote_name = $wpdb -> get_var( $wpdb -> prepare("SELECT `title` FROM {$wpdb->prefix}vote WHERE id=%d",$vote_id));
        $options_list = $wpdb -> get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}vote_option WHERE vote_id=%d",$vote_id),ARRAY_A);
        $option_array = array();
        foreach($options_list as $option)
            $option_array[$option['id']] = $option['title'];
        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vote_fans WHERE vote_id='{$vote_id}' AND votes LIKE '%,{$vote_option_id},%'");
        $pindex = max(1, intval($_GET['page']));
        $psize = 5;
        $pindex = min(max(ceil($total/$psize),1),$pindex );
        $offset=($pindex - 1) * $psize;
        $pager = $this->pagination($total, $pindex, $psize);
        $list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}vote_fans WHERE vote_id='{$vote_id}' AND votes LIKE '%,{$vote_option_id},%' ORDER BY `id` DESC Limit {$offset},{$psize}",ARRAY_A);
        foreach($list as &$vote_element){
            $vote_element['votes'] = trim(trim($vote_element['votes'],','));
            $vote_element['votes'] = explode(",", $vote_element['votes']);
            if(is_array($vote_element['votes']) && !empty($vote_element['votes'])){
                foreach($vote_element['votes'] as &$vote_element_item){
                    $vote_element_item = $option_array[$vote_element_item];
                }
                $vote_element['votes'] = implode(',', $vote_element['votes']);
            }
            $getMID = $wpdb->get_var("SELECT mid FROM {$wpdb->prefix}wechat_member_group WHERE from_user='{$vote_element['from_user']}' and GWEID = '{$_W['gweid']}'");
            if(!empty($getMID)) {
                $vote_element['mid'] = $getMID;
            }
        }
        include $this->template('history');
    }

    function img_url($img = '') {
        global $_W;
        if (empty($img)) 
            return "";
        if (substr($img, 0, 6) == 'avatar') 
            return $_W['siteroot'] . "resource/image/avatar/" . $img;
        if (substr($img, 0, 8) == './themes')
            return $_W['siteroot'] . $img;
        if (substr($img, 0, 1) == '.')
            return $_W['siteroot'] . substr($img, 2);
        if (substr($img, 0, 5) == 'http:')
            return $img;
        return $_W['attachurl'] . $img;
    }

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
        $list = $wpdb -> get_results("SELECT * FROM {$wpdb->prefix}vote WHERE gweid='{$_W['gweid']}'",ARRAY_A);
        if(is_array($list))
            foreach($list as $element){
                file_unlink($element['thumb']);
                file_unlink_from_xml($element['description']);
            }
                
    }
}
