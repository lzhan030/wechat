<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include 'wechat_constant.php';


function get_table_name($name) 
{	
	return "wp_".$name;
}

//get the text content as the auto reply message
//΢���߼�����
function wechat_text_get($nid) 
{
	global $wpdb;
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_material_text')." where text_id=".intval($nid));
	
	return $myrows;
	 	
}

//�޸��Զ��ظ��ı�����
function wechat_autrplay_text_insert($type,$sendContent,$user_id,$WEID) {
	
	
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_material_text")."(text_title,text_content,text_user,WEID)VALUES (%s,%s, %d, %d)",$type,$sendContent, $user_id,$WEID));
	return $wpdb->insert_id;
	
}
//20140623 janeen add
function wechat_autrplay_text_insert_group($type,$sendContent,$user_id,$GWEID) {
	
	
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_material_text")."(text_title,text_content,text_user,GWEID)VALUES (%s,%s, %d, %d)",$type,$sendContent, $user_id,$GWEID));
	return $wpdb->insert_id;
	
}

function wechat_autrplay_text_update($sendContent,$textId) {
	global $wpdb;
	$update = $wpdb -> update(get_table_name("wechat_material_text"),array('text_content'=>$sendContent),array('text_id'=>$textId),array("%s"),array("%s"));

	return $update;
	
}

function wechat_autrplay_text_get($type,$WEID) {
	
	
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".get_table_name('wechat_material_text')." where text_title=%s and WEID=%s",$type,$WEID));
	
	return $myrows;
	
}
//20140623 janeen add
function wechat_autrplay_text_get_group($type,$GWEID) {
	
	
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".get_table_name('wechat_material_text')." where text_title=%s and GWEID=".$GWEID,$type));
	
	return $myrows;
	
}

//�����Զ��ظ�������
function wechat_autrplay_acty($nid,$autype,$id) 
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_autoreply"),array('arply_type'=>$autype,'arplymesg_id'=>$nid),array('arply_id'=>$id),array("%s","%s"),array("%d"));
	
	return $update;
	 	
}

//����΢���߼�����
function wechat_news_get($nid) 
{
	global $wpdb;
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_material_news')." where news_item_id=".intval($nid)." order by news_id");
	
	return $myrows;
	 	
}

function wechat_users_get() 
{
	global $wpdb;
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_user_reg'));
	
	return $myrows;
	 	
}


function wechat_message_get($openId) 
{	
	global $wpdb;
	
	//����˼·���û�֮�����Ϣ��¼�Ƿ��ı���ͼ�ı����ڲ�ͬ�ı?��ʱ����	
	//$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_message_text')." where OpenId=".$openId);
	
	//����˼·���û�֮�����Ϣ��¼������һ�Ŵ�ı���(�˴�ʡ�Ե���Ż����)
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_messages')." where FromUserName ='".$openId."' or ToUserName ='".$openId."'");
	
	return $myrows;
	 	
}

function material_news_getmax()
{
	global $wpdb;
	$myrows = $wpdb->get_results("SELECT Max(news_item_id) as maxnid FROM ".get_table_name('wechat_material_news'));
	return $myrows;

}

function material_item_get($itemId)
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_material_news')." where news_id=".intval($itemId));

	return !empty($myrows);

}
function material_item_get_info($itemId)
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_material_news')." where news_id=".intval($itemId));

	return $myrows;

}
function material_news_get($nId)
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_material_news')." where news_item_id=".intval($nId));

	return $myrows;

}

function material_news_getlist($WEID) 
{	
	global $wpdb;
	
	$myrows = $wpdb->get_results("select news_item_id, news_name FROM ".get_table_name('wechat_material_news')." where WEID=".intval($WEID)." GROUP BY news_item_id");
	
	return $myrows;
	 	
}
//20140623 janeen add
function material_news_getlist_group($GWEID) 
{	
	global $wpdb;
	
	$myrows = $wpdb->get_results("select news_item_id, news_name FROM ".get_table_name('wechat_material_news')." where GWEID=".$GWEID." GROUP BY news_item_id");
	
	return $myrows;
	 	
}
function material_news_add($title,$itemUrl,$itemPicUrl,$desc,$newsItemId,$user_id,$news_name,$WEID)
{
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_material_news")."(news_item_title,news_item_url,news_item_picurl,news_item_description,news_item_id,news_user,news_name,WEID)VALUES (%s, %s ,%s,%s, %s, %d, %s ,%d)",$title, $itemUrl,$itemPicUrl,$desc,$newsItemId,$user_id,$news_name,$WEID));
	return $insert;

}
//20140623 janeen add
function material_news_add_group($title,$itemUrl,$itemPicUrl,$abstract,$desc,$newsItemId,$user_id,$news_name,$GWEID)
{
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_material_news")."(news_item_title,news_item_url,news_item_picurl,news_item_abstract,news_item_description,news_item_id,news_user,news_name,GWEID)VALUES (%s,%s,%s,%s,%s,%s,%d,%s,%d)",$title,$itemUrl,$itemPicUrl,$abstract,$desc,$newsItemId,$user_id,$news_name,$GWEID));
	return $insert;

}
function material_news_update($title,$itemUrl,$itemPicUrl,$abstract,$desc,$itemId,$news_name)
{
	global $wpdb;
	if($itemPicUrl==null){
		$update = $wpdb -> update(get_table_name("wechat_material_news"),array('news_item_title'=>$title,'news_item_url'=>$itemUrl,'news_item_abstract'=>$abstract,'news_item_description'=>$desc,'news_name'=>$news_name),array('news_id'=>$itemId),array("%s","%s","%s","%s","%s"),array("%d"));
		return $update;
	}else{
		$pic = material_item_get($itemId);
		$old_desc = $pic['news_item_description'];
		$pic = $pic['news_item_picurl'];
		if($pic != $itemPicUrl)
			file_unlink($pic);
		file_unlink_from_xml_update(str_replace('\"', '"', str_ireplace('../', '', str_ireplace('../uploads', '', $old_desc))),str_replace('\"', '"', str_ireplace('../', '', str_ireplace('../uploads', '', $desc))));
		$update = $wpdb -> update(get_table_name("wechat_material_news"),array('news_item_title'=>$title,'news_item_url'=>$itemUrl,'news_item_picurl'=>$itemPicUrl,'news_item_abstract'=>$abstract,'news_item_description'=>$desc,'news_name'=>$news_name),array('news_id'=>$itemId),array("%s","%s","%s","%s","%s"),array("%d"));
		return $update;
	}
}
			
function material_news_delete($newsItemId,$newsId)
{
	global $wpdb;	
	if($newsItemId!=null){
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_material_news")." WHERE news_item_id=%d", $newsItemId));
		//删除该多图文之后，要将关键词回复、菜单的地方用到该多图文的地方都清空或删除，否则，再接着新建一个多图文变回接着使用了
		//关键词等
		$deletenews=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_autoreply")." WHERE arply_type=%s and arplymesg_id=%d" , "weChat_news",$newsItemId));
		//个人菜单
		$update = $wpdb -> update(get_table_name("wechat_menu"),array('menu_type'=>'','menu_key'=>''),array('menu_key'=>'s'.$newsItemId),array("%s","%s"),array("%s"));
		//公共菜单
		$update = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>'','menu_key'=>''),array('menu_key'=>'s'.$newsItemId),array("%s","%s"),array("%s"));
		return $delete;
	}else{
		$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_material_news")." WHERE news_id=%d", $newsId));
		return $delete;
	}

}	
function web_admin_delete_vmember($vid)
{
	global $wpdb;	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_member")." WHERE mid=%s", $vid));
	return $delete;

	

}	
function web_admin_delete_vmember_group($vid)
{
	global $wpdb;	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_member_group")." WHERE mid=%s", $vid));
	return $delete;

	

}
function web_admin_delete_vmember_all($GWEID)
{
	global $wpdb;	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_member")." WHERE GWEID=%d", $GWEID));
	return $delete;

	

}
function web_admin_delete_vmember_all_group($GWEID)
{
	global $wpdb;	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_member_group")." WHERE GWEID=%d", $GWEID));
	return $delete;	

}
//根据会员id获取要更新会员的信息
function web_admin_get_vipmember($vipmemberId)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_member')." WHERE mid=".intval($vipmemberId));	 
	return $myrows;
}

function web_admin_get_memberopenid($vipmemberId)
{
	global $wpdb;
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_member_group')." WHERE mid=".intval($vipmemberId));
	return $myrows;
}

//更新会员信息
// function web_admin_update_vipmember($vipmemberUser,$viPos,$nicName,$vipmemberId)
// {
	// global $wpdb;
	// $update = $wpdb -> update(get_table_name("wechat_member"),array('from_user'=>$vipmemberUser,'password'=>$viPos,'nickname'=>$nicName),array('mid'=>$vipmemberId),array("%s","%s","%s"),array("%d"));
	// return $update;
	// }
function web_admin_update_vipmember($realName,$nickName,$point,$level,$rtime,$mobilenumber,$email,$billingplan,$regtype,$apptype,$isaudit,$vipmemberId)
{
	global $wpdb;
	if(!isset($isaudit)){//用户不设置会员审批限制，则不更新会员审批状态
		$update = $wpdb -> update(get_table_name("wechat_member"),array('realname'=>$realName,'nickname'=>$nickName,'point'=>$point,'level'=>$level,'rtime'=>$rtime,'mobilenumber'=>$mobilenumber,'email'=>$email,'billing_plan'=>$billingplan,'reg_type'=>$regtype,'app_type'=>$apptype),array('mid'=>$vipmemberId),array("%s","%s","%d","%s","%s","%s","%s","%s","%s","%s"),array("%s"));	
	}else{
		$update = $wpdb -> update(get_table_name("wechat_member"),array('realname'=>$realName,'nickname'=>$nickName,'point'=>$point,'level'=>$level,'rtime'=>$rtime,'mobilenumber'=>$mobilenumber,'email'=>$email,'billing_plan'=>$billingplan,'reg_type'=>$regtype,'app_type'=>$apptype,'isaudit'=>$isaudit),array('mid'=>$vipmemberId),array("%s","%s","%d","%s","%s","%s","%s","%s","%s","%s","%d"),array("%s"));	
	}
	return $update;	
}
function wechat_message_all() 
{	
	global $wpdb;
	
	$myrows = $wpdb->get_results("select FromUserName, count(*) as count FROM ".get_table_name('wechat_messages')." GROUP BY FromUserName");
	
	return $myrows;
	 	
}

//�õ�ĳ����ͼ�ĵ�ͼ�ĸ���
function wechat_get_news_count($nid) 
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results("SELECT COUNT(*) as counts FROM " .get_table_name('wechat_material_news')." where news_item_id=".intval($nid));
	return $myrows;
}

//�õ���ͼ�ĵĸ���
function wechat_get_news_act($WEID) 
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_material_news')." where WEID=".intval($WEID)." group by news_item_id");

	return $myrows;
}
//20140623 janeen add
function wechat_get_news_act_group($GWEID) 
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_material_news')." where GWEID=".intval($GWEID)." group by news_item_id");

	return $myrows;
}


function wechat_message_user_count() 
{	
	global $wpdb;
	
	$myrows = $wpdb->get_results("select count(distinct(FromUserName)) as ucount FROM ".get_table_name('wechat_messages'));
	
	return $myrows;
	 	
}	
	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//΢�ź�̨�߼����?��
//��ݹؼ���õ�Ҫ�ظ������ݵ�id
function wechat_mess_kw_get($keyword,$WEID)
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM " .get_table_name('wechat_autoreply')." where WEID=".intval($WEID)." and arply_keyword=%s",$keyword));
	
	return $myrows;

}
//20140623 janeen add start
function wechat_mess_kw_get_group($keyword,$GWEID)
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM " .get_table_name('wechat_autoreply')." where GWEID=".intval($GWEID)." and arply_keyword=%s",$keyword));
	
	return $myrows;

}

function wechat_clearNews($keyword,$WEID,$type='weChat_news')
{
    global $wpdb;
    if($type != 'weChat_news')
    {
        $sql = "delete from " .get_table_name('wechat_material_text')." where text_title='".$keyword."' and WEID='".$WEID."'";
        if($wpdb->query($sql)==0)
            return 0;
    }
    $sql = "delete from " .get_table_name('wechat_autoreply')." where arply_type='".$type."' and WEID='".$WEID."' and arply_keyword='".$keyword."'";
    //var_dump($sql);
    return $wpdb->query($sql);
}
//20140623 janeen add start
function wechat_clearNews_group($keyword,$GWEID,$type='weChat_news')
{
    global $wpdb;
    if($type != 'weChat_news')
    {
        $sql = "delete from " .get_table_name('wechat_material_text')." where text_title='".$keyword."' and GWEID='".$GWEID."'";
        if($wpdb->query($sql)==0)
            return 0;
    }
    $sql = "delete from " .get_table_name('wechat_autoreply')." where arply_type='".$type."' and GWEID='".$GWEID."' and arply_keyword='".$keyword."'";
    //var_dump($sql);
    return $wpdb->query($sql);
}

//check whether keyword existed?
function wechat_keyword_exist($keyword,$WEID)
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM " .get_table_name('wechat_autoreply')." where WEID=".$WEID." and arply_keyword=%s",$keyword));

	return !empty($myrows);

}
//20140624 janeen add
function wechat_keyword_exist_group($keyword,$GWEID)
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM " .get_table_name('wechat_autoreply')." where GWEID=".$GWEID." and arply_keyword=%s",$keyword));

	return !empty($myrows);

}
//get keyword according to keyword id and user id
function wechat_keyword_get($keywordId)
{
	global $wpdb;
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_autoreply')." where arply_id=".intval($keywordId));
	
	return $myrows;
}

 //�õ����з�-1�ͷ�-2�Ĺؼ��
function wechat_mess_kw_list($WEID)
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_autoreply')." where WEID=".intval($WEID)." and arply_keyword!='subscribe' and arply_keyword!='nokey' ORDER BY arply_id");

	return $myrows;

}
//20140623 janeen add
function wechat_mess_kw_list_group($GWEID)
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_autoreply')." where GWEID=".intval($GWEID)." and arply_keyword!='subscribe' and arply_keyword!='nokey' ORDER BY arply_id desc");

	return $myrows;

}

//获取所有的keyword的个数
function webchat_mess_kw_count($WEID)
{
    global $wpdb;
    $myrows = $wpdb->get_results("SELECT COUNT(*) as kwCount FROM " .get_table_name('wechat_autoreply')." where WEID=".intval($WEID)." and arply_keyword!='subscribe' and arply_keyword!='nokey'");
	return $myrows;
}
//20140623 janeen add
function webchat_mess_kw_count_group($GWEID)
{
    global $wpdb;
    $myrows = $wpdb->get_results("SELECT COUNT(*) as kwCount FROM " .get_table_name('wechat_autoreply')." where GWEID=".intval($GWEID)." and arply_keyword!='subscribe' and arply_keyword!='nokey'");
	return $myrows;
}

 //获取当页的所有keyword数据集
function wechat_array_kw($offset,$pagesize,$WEID)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".get_table_name('wechat_autoreply'). " where WEID=".intval($WEID)." and arply_keyword!='subscribe' and arply_keyword!='nokey' ORDER BY arply_id limit ".$offset.",".$pagesize );
	return $myrows;
}
//20140623 janeen add
function wechat_array_kw_group($offset,$pagesize,$GWEID)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".get_table_name('wechat_autoreply'). " where GWEID=".intval($GWEID)." and arply_keyword!='subscribe' and arply_keyword!='nokey' ORDER BY arply_id desc limit ".$offset.",".$pagesize );
	return $myrows;
}

//获取当页数据集的个数
function wechat_array_kw_count($offset,$pagesize,$WEID)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as arrayCount FROM ".get_table_name('wechat_autoreply'). " where WEID=".intval($WEID)." and arply_keyword!='subscribe' and arply_keyword!='nokey' limit ".$offset.",".$pagesize);
	return $myrows;
}  
//20140623 janeen add
function wechat_array_kw_count_group($offset,$pagesize,$GWEID)
{
    global $wpdb;
	$myrows = $wpdb->get_results("SELECT COUNT(*) as arrayCount FROM ".get_table_name('wechat_autoreply'). " where GWEID=".intval($GWEID)." and arply_keyword!='subscribe' and arply_keyword!='nokey' limit ".$offset.",".$pagesize);
	return $myrows;
}  
function wechat_mess_kw_isExistInDB($keyword,$WEID)
{
    global $wpdb;
    $sql = "SELECT COUNT(*) as arrayCount FROM ".get_table_name('wechat_autoreply'). " where arply_keyword=%s and WEID=%d";
    $myrows = $wpdb->get_results($wpdb -> prepare($sql,$keyword,$WEID));
    return $myrows;
}
//20140623 janeen add
function wechat_mess_kw_isExistInDB_group($keyword,$GWEID)
{
    global $wpdb;
    $sql = "SELECT COUNT(*) as arrayCount FROM ".get_table_name('wechat_autoreply'). " where arply_keyword=%s and GWEID=%d";
    $myrows = $wpdb->get_results($wpdb -> prepare($sql,$keyword,$GWEID));
    return $myrows;
}
function wechat_mess_kw_add($autoType,$autoId,$keyword,$user_id,$WEID)
{
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_autoreply")."(arply_type,arplymesg_id,arply_keyword,arply_user,WEID)VALUES (%s, %d ,%s, %d, %d)",$autoType, $autoId,$keyword,$user_id,$WEID));
	$auid=$wpdb->insert_id;
	return $auid;
}
//20140623 janeen add
function wechat_mess_kw_add_group($autoType,$autoId,$keyword,$user_id,$GWEID)
{
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_autoreply")."(arply_type,arplymesg_id,arply_keyword,arply_user,GWEID)VALUES (%s, %d ,%s, %d, %d)",$autoType, $autoId,$keyword,$user_id,$GWEID));
	$auid=$wpdb->insert_id;
	return $auid;
}
function wechat_mess_kw_update($autoType,$autoId,$keyword,$kid)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_autoreply"),array('arply_type'=>$autoType,'arplymesg_id'=>$autoId,'arply_keyword'=>$keyword),array('arply_id'=>$kid),array("%s","%s","%s"),array("%d"));

	return $update;
}

function wechat_mess_content_update($autoType,$autoId,$kid)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_autoreply"),array('arply_type'=>$autoType,'arplymesg_id'=>$autoId),array('arply_id'=>$kid),array("%s","%s"),array("%d"));

	return $update;
}


function wechat_mess_kw_name_update($keyword,$kid)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_autoreply"),array('arply_keyword'=>$keyword),array('arply_id'=>$kid),array("%s"),array("%d"));

	return $update;
}

function wechat_mess_kw_delete($kid)
{
	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_autoreply")." WHERE arply_id=%d", $kid));
	return $delete;

}
 
 
 
 

//������Ϣ��¼--�ı�

function wechat_mess_insert($content,$FromUserName,$ToUserName,$msgType)
{

	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_messages")."(Content,FromUserName,ToUserName,MsgType,Time)VALUES (%s, %s ,%s, %s, %s)",$content, $FromUserName,$ToUserName,$msgType,date('Y-m-d H:i:s',time())));
	return $insert;

}

//΢�Ź�ע
function wechat_user_reg($openId, $companyId)
{
// Generate the userID automatically

	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_user_reg")."(OpenId, CompanyId)VALUES (%s, %d)",$openId, $companyId));
	
	return $insert;

}

function wechat_user_unreg($openId)
{

	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_user_reg")." WHERE OpenId=%s", $openId));
	return $delete;


}

//��Ϊurl��https ������������file_get_contents,��curl����json ���  
function http_request_json($url){    
	$ch = curl_init();  
	curl_setopt($ch, CURLOPT_URL,$url);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	$result = curl_exec($ch);  
	curl_close($ch);  
	return $result;    
} 



function re_Token($APPID,$APPSECRET){
	
	$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;

	//$json=file_get_contents($TOKEN_URL);
	$json=http_request_json($TOKEN_URL);//����ط�������file_get_contents
	$result=json_decode($json,true);

	if($result['access_token']){  
        return $result['access_token'];  
    }else{  
        return FALSE;
    }         

}

//��ɲ˵�
function wechat_menu_create($ACC_TOKEN, $jsonData){
    $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$ACC_TOKEN;
	$ch = curl_init() ;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
		
	if(curl_errno($ch)){
		return curl_errno($ch);
	}
	$res = json_decode($result);
	curl_close($ch) ;
	//return $result;
	return $res->errcode;
}

//ɾ��˵�
function wechat_menu_delete($ACC_TOKEN){
	$MENU_URL="https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$ACC_TOKEN;
	
	$ch = curl_init();  
	curl_setopt($ch, CURLOPT_URL,$MENU_URL);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	$result = curl_exec($ch); 
	$res = json_decode($result);	
	curl_close($ch);
	
	return $res->errcode; 
	//return $res->errmsg;
	

}	

///////////////////////////////////////�Զ���˵�����

function wechat_menu_isExistInDB($menuname,$WEID)
{
    global $wpdb;
    $sql = "SELECT COUNT(*) as arrayCount FROM ".get_table_name('wechat_menu'). " where menu_name=%s and WEID=%d";
    $myrows = $wpdb->get_results($wpdb -> prepare($sql,$menuname,$WEID));
    return $myrows;
}
//20140623 janeen add
function wechat_menu_isExistInDB_group($menuname,$GWEID)
{
    global $wpdb;
    $sql = "SELECT COUNT(*) as arrayCount FROM ".get_table_name('wechat_menu'). " where menu_name='".$menuname."' and GWEID='".$GWEID."'";
    $myrows = $wpdb->get_results($sql);
    return $myrows;
}


//���ĳ���Զ���˵�
function wechat_menu_add($parentid,$menuname,$menutype,$menukey,$user_id,$WEID)
{
	global $wpdb;
	$insert=$wpdb->query($wpdb->prepare("INSERT INTO ".get_table_name("wechat_menu")."(parent_id,menu_name,menu_type,menu_key,menu_user,WEID) VALUES (%d, %s ,%s, %s, %s ,%s)",$parentid, $menuname,$menutype,$menukey,$user_id,$WEID));
	return $insert;
}
//20140623 janeen update
function wechat_menu_add_group($parentid,$menuname,$menutype,$menukey,$user_id,$GWEID)
{
	global $wpdb;
	$insert=$wpdb->query($wpdb->prepare("INSERT INTO ".get_table_name("wechat_menu")."(parent_id,menu_name,menu_type,menu_key,menu_user,GWEID) VALUES (%d, %s ,%s, %s, %s ,%s)",$parentid, $menuname,$menutype,$menukey,$user_id,$GWEID));
	return $insert;
}


//����ĳ���Զ���˵�
function wechat_menu_update($menuid,$parentid,$menuname,$menutype,$menukey)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_menu"),array('parent_id'=>$parentid,'menu_name'=>$menuname,'menu_type'=>$menutype,'menu_key'=>$menukey),array('menu_id'=>$menuid),array("%d","%s","%s","%s"),array("%d"));

	return $update;
}
function wechat_menu_updateforchid($menuid,$parentid,$menutype,$menukey)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_menu"),array('parent_id'=>$parentid,'menu_type'=>$menutype,'menu_key'=>$menukey),array('menu_id'=>$menuid),array("%d","%s","%s"),array("%d"));

	return $update;
}

function wechat_menu_name_update($mename,$mid)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_menu"),array('menu_name'=>$mename),array('menu_id'=>$mid),array("%s"),array("%d"));

	return $update;
}
//ɾ��ĳ���Զ���˵�
function wechat_menu_del($menuid)
{
	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_menu")." WHERE menu_id=%d", $menuid));
	return $delete;

}
function wechat_menu_del_all($GWEID)
{
	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_menu")." WHERE GWEID=%d", $GWEID));
	return $delete;

}
function wechat_menupar_del($menuid)
{
	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_menu")." WHERE parent_id=%d", $menuid));
	return $delete;

}

//ȡ���Զ���˵�
function wechat_menu_list($WEID)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_menu')." where WEID=".intval($WEID)." order by menu_id");

	return $myrows;

}
//20140623 janeen add
function wechat_menu_list_group($GWEID)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_menu')." where GWEID=".$GWEID." order by menu_id");

	return $myrows;

}

function wechat_get_menu_count($parentid) 
{
	global $wpdb;
	                                                             
	$myrows = $wpdb->get_results("SELECT COUNT(*) as counts FROM " .get_table_name('wechat_menu')." where parent_id=".intval($parentid));
	return $myrows;
}





//ͨ��id�õ��Ӳ˵�
function wechat_menu_parget($parentid,$WEID)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_menu')." where WEID=".$WEID." and parent_id=".intval($parentid)." order by menu_id");

	return $myrows;

}	
//20140623 janeen add
function wechat_menu_parget_group($parentid,$GWEID)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_menu')." where GWEID=".$GWEID." and parent_id=".intval($parentid)." order by menu_id");

	return $myrows;

}	

//�õ��ض��Ӳ˵�
function wechat_menu_get($menuid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_menu')." where menu_id=".intval($menuid));

	return $myrows;

}

//��ȡ������
function getTree(){
	global $wpdb;
	$data=array();
	$myrows = $wpdb->get_results("SELECT menu_id,parent_id,menu_name,menu_type,menu_key FROM ".get_table_name('wechat_menu')." order by menu_id");
	
	foreach($myrows as $row){
		$data[]=$row;	
	}
	return $data;
}

//һ������ 
function getFlone($parentid){
	global $wpdb;
	
	$data=array();
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_menu')." where parent_id=".intval($parentid)." order by menu_id");
	foreach($myrows as $row){
		$data[]=$row;	
	}
	return $data;
}
//���������
function getFltwo($parentid){
	global $wpdb;
	
	$data=array();
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_menu')." where parent_id=".intval($parentid)." order by menu_id");
	foreach($myrows as $row){
		$data[]=$row;	
	}
	return $data;
}

//ѭ����ȡ�˵�,����ݿⶥ��������idΪ 59 
function getFlmenu($parentid){
	global $wpdb;
	$arr=getFlone($parentid);
	foreach($arr as &$v){
		if(getFlone($v['parent_id'])){        //�ж�$v['parentid']�Ƿ�����Ŀ¼
            $arr.=getFlmenu($v['parent_id']);   //�������Ŀ¼�������ٴε���getFlmenu������лص�����ÿһ��Ŀ¼��$arr�����ʾ�ģ��á�.�����������������㵽ʱ����explode��������ָ�
			return $arr;
        }else{
            return $arr; //���$v['parentid']����û����Ŀ¼��������ʾЧ�����Լ����Զ���
        }
    } 
}

function wechat_info_get($WEID)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_usechat')." where WEID=".intval($WEID));

	return $myrows;

}
//20140623 janeen add
function wechat_info_get_group($GWEID)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_usechat')." where GWEID=".intval($GWEID));

	return $myrows;

}
//拿到私有的
function wechat_info_get_group_pri($GWEID)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT u1.wid as wid FROM " .get_table_name('wechat_usechat')." u1,".get_table_name('wechats')." u2 where ((u2.wechat_type='pri_svc') or (u2.wechat_type='pri_sub' and u2.wechat_auth='1')) and u1.wid=u2.wid and u1.WEID != 0 and u1.GWEID=".intval($GWEID));

	return $myrows;

}
function wechat_wechats_get($wid){

	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechats')." where wid=".intval($wid));

	return $myrows;

}
function wechat_info_update($menu_token,$wid)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechats"),array('menu_token'=>$menu_token),array('wid'=>$wid),array("%s"),array("%d"));
	return $update;

	
}
//mashan 有关个人服务号
//删除菜单对应的链接
function wechat_menu_prisvc_urldel($menuId,$menuType,$menuKey,$WEID)
{
	global $wpdb;
	$urldelete = $wpdb -> update(get_table_name("wechat_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'WEID'=>$WEID),array("%s","%s"),array("%d","%d"));
	return $urldelete;
}
//20140623 janeen add
function wechat_menu_prisvc_urldel_group($menuId,$menuType,$menuKey,$GWEID)
{
	global $wpdb;
	$urldelete = $wpdb -> update(get_table_name("wechat_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'GWEID'=>$GWEID),array("%s","%s"),array("%d","%d"));
	return $urldelete;
}
//删除菜单对应的图文以及在图文表wechat_material_news中删除所有内容
function wechat_menu_prisvc_newsdel($menuId,$menuType,$menukey,$WEID)
{
	global $wpdb;
	// $sql = "delete from " .get_table_name('wechat_material_news')." where news_item_id='".$menukey."' and WEID='".$WEID."'";
        // if($wpdb->query($sql)==0)
            // return 0;
	$newsdelete = $wpdb -> update(get_table_name("wechat_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'WEID'=>$WEID),array("%s","%s"),array("%d","%d"));
	return $newsdelete;
}
//20140623 janeen add
function wechat_menu_prisvc_newsdel_group($menuId,$menuType,$menukey,$GWEID)
{
	global $wpdb;
	$newsdelete = $wpdb -> update(get_table_name("wechat_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'GWEID'=>$GWEID),array("%s","%s"),array("%d","%d"));
	return $newsdelete;
}
//删除菜单对应的文本以及在文本表wechat_material_text中删除所有内容
function wechat_menu_prisvc_textdel($menuId,$menuType,$menukey,$WEID)
{
	global $wpdb;
	$sql = "delete from " .get_table_name('wechat_material_text')." where text_id='".$menukey."' and WEID='".$WEID."'";
        if($wpdb->query($sql)==0)
            return 0;
	$textdelete = $wpdb -> update(get_table_name("wechat_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'WEID'=>$WEID),array("%s","%s"),array("%d","%d"));
	return $textdelete;
}
//20140623 janeen add
function wechat_menu_prisvc_textdel_group($menuId,$menuType,$menukey,$GWEID)
{
	global $wpdb;
	$sql = "delete from " .get_table_name('wechat_material_text')." where text_id='".$menukey."' and GWEID='".$GWEID."'";
        if($wpdb->query($sql)==0)
            return 0;
	$textdelete = $wpdb -> update(get_table_name("wechat_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'GWEID'=>$GWEID),array("%s","%s"),array("%d","%d"));
	return $textdelete;
}

//插入公用公众号的信息
//针对公用公众服务号菜单类型以及内容的更新的信息
function wechat_menu_publicsvc_update($menuId,$menuType,$menuKey,$WEID)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>$menuType,'menu_key'=>$menuKey),array('menu_id'=>$menuId,'WEID'=>$WEID),array("%s","%s"),array("%d","%d"));
	//$update = $wpdb -> update(get_table_name("wechat_content_menu"),array('parent_id'=>$menuPad,'menu_name'=>$menuName),array('menu_id'=>$menuId),array("%d","%s"),array("%d"));
	return $update;
}
//20140624 janeen add
function wechat_menu_publicsvc_update_group($menuId,$menuType,$menuKey,$GWEID)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>$menuType,'menu_key'=>$menuKey),array('menu_id'=>$menuId,'GWEID'=>$GWEID),array("%s","%s"),array("%d","%d"));
	//$update = $wpdb -> update(get_table_name("wechat_content_menu"),array('parent_id'=>$menuPad,'menu_name'=>$menuName),array('menu_id'=>$menuId),array("%d","%s"),array("%d"));
	return $update;
}
//删除菜单对应的链接
function wechat_menu_publicsvc_urldel($menuId,$menuType,$menuKey,$WEID)
{
	global $wpdb;
	$urldelete = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'WEID'=>$WEID),array("%s","%s"),array("%d","%d"));
	return $urldelete;
}
//20140624 add
function wechat_menu_publicsvc_urldel_group($menuId,$menuType,$menuKey,$GWEID)
{
	global $wpdb;
	$urldelete = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'GWEID'=>$GWEID),array("%s","%s"),array("%d","%d"));
	return $urldelete;
}
//删除菜单对应的图文以及在图文表wechat_material_news中删除所有内容
function wechat_menu_publicsvc_newsdel($menuId,$menuType,$menukey,$WEID)
{
	global $wpdb;
	// $sql = "delete from " .get_table_name('wechat_material_news')." where news_item_id='".$menukey."' and WEID='".$WEID."'";
        // if($wpdb->query($sql)==0)
            // return 0;
	$newsdelete = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'WEID'=>$WEID),array("%s","%s"),array("%d","%d"));
	return $newsdelete;
}
//20140624 add
function wechat_menu_publicsvc_newsdel_group($menuId,$menuType,$menukey,$GWEID)
{
	global $wpdb;
	// $sql = "delete from " .get_table_name('wechat_material_news')." where news_item_id='".$menukey."' and WEID='".$WEID."'";
        // if($wpdb->query($sql)==0)
            // return 0;
	$newsdelete = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'GWEID'=>$GWEID),array("%s","%s"),array("%d","%d"));
	return $newsdelete;
}
//删除菜单对应的文本以及在文本表wechat_material_text中删除所有内容
function wechat_menu_publicsvc_textdel($menuId,$menuType,$menukey,$WEID)
{
	global $wpdb;
	$sql = "delete from " .get_table_name('wechat_material_text')." where text_id='".$menukey."' and WEID='".$WEID."'";
        if($wpdb->query($sql)==0)
            return 0;
	$textdelete = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'WEID'=>$WEID),array("%s","%s"),array("%d","%d"));
	return $textdelete;
}
function wechat_menu_publicsvc_textdel_group($menuId,$menuType,$menukey,$GWEID)
{
	global $wpdb;
	$sql = "delete from " .get_table_name('wechat_material_text')." where text_id='".$menukey."' and GWEID='".$GWEID."'";
        if($wpdb->query($sql)==0)
            return 0;
	$textdelete = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'GWEID'=>$GWEID),array("%s","%s"),array("%d","%d"));
	return $textdelete;
}
function wechat_menu_publicsvc_textdel_all_group($menuId,$menuType,$menukey,$WEID,$GWEID)
{
	global $wpdb;
	$sql = "delete from " .get_table_name('wechat_material_text')." where text_id='".$menukey."' and GWEID='".$GWEID."'";
        if($wpdb->query($sql)==0)
            return 0;
	$textdelete = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>" ",'menu_key'=>" "),array('menu_id'=>$menuId,'WEID'=>$WEID),array("%s","%s"),array("%d","%d"));
	return $textdelete;
}
//从两个表中获取结果，因为需要的字段不在同一个表中，所以通过联合查询
function wechat_menu_publicsvc_list($WEID)
{
	global $wpdb;	       
	$M_id = $wpdb -> get_var("SELECT DISTINCT M_id FROM wp_wechat_user_menu WHERE WEID = ".$WEID." AND !ISNULL(M_id) LIMIT 1");
	$myrows = $wpdb->get_results("SELECT u1.menu_id,menu_name,parent_id,menu_type,menu_key FROM ".get_table_name("wechat_content_menu")." u1 left join ".get_table_name("wechat_user_menu")." u2 on ( (u1.menu_id = u2.menu_id) AND u2.WEID=".$WEID." ) WHERE u1.M_id = ".intval($M_id)." order by u1.menu_id " );
	return $myrows;

}
//20140624 add
function wechat_menu_publicsvc_list_group($GWEID)
{
	global $wpdb;	       
	$M_id = $wpdb -> get_var("SELECT DISTINCT M_id FROM wp_wechat_user_menu WHERE GWEID = ".intval($GWEID)." AND !ISNULL(M_id) LIMIT 1");
	$myrows = $wpdb->get_results("SELECT u1.menu_id,menu_name,parent_id,menu_type,menu_key FROM ".get_table_name("wechat_content_menu")." u1 left join ".get_table_name("wechat_user_menu")." u2 on ( (u1.menu_id = u2.menu_id) AND u2.GWEID=".intval($GWEID)." ) WHERE u1.M_id = ".$M_id." order by u1.menu_id " );
	return $myrows;

}

function wechat_menu_public_get($menuid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_user_menu')." where menu_id=".intval($menuid));

	return $myrows;
}
function wechat_menu_public_get_mid($M_id)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_user_menu')." where M_id=".intval($M_id));

	return $myrows;
}
//此功能在menupblicsvc/menu.php中,暂时没用到
/* function  wechat_messmenu_kw_get($menusecid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_user_menu')." where menu_id=".$menusecid);

	return $myrows;
} */
/* //此功能在menu/menu.php中，暂时没用到
function  wechat_messprimenu_kw_get($menusecid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_menu')." where menu_id=".$menusecid);

	return $myrows;
} 
 */
 //判断表wechat_user_menu中是否存在新纪录
 function wechat_menu_public_exist($menuId,$WEID)
 {
    global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_user_menu')." where menu_id=".intval($menuId)." and WEID=".intval($WEID));
	//echo "SELECT * FROM ".get_table_name('wechat_user_menu')." where menu_id=".$menuId." and WEID=".$WEID ;
	return $myrows;
}
//20140624 janeen add
 function wechat_menu_public_exist_group($menuId,$GWEID)
 {
    global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name('wechat_user_menu')." where menu_id=".intval($menuId)." and GWEID=".intval($GWEID));
	//echo "SELECT * FROM ".get_table_name('wechat_user_menu')." where menu_id=".$menuId." and WEID=".$WEID ;
	return $myrows;
}

//若是无该新纪录，则将新纪录插入到表wechat_user_menu中
 function wechat_menu_publicsvc_insert($menuId,$menuType,$menuKey,$weid,$wid,$mid)
 {
   global $wpdb;
	$insert=$wpdb->query($wpdb->prepare("INSERT INTO ".get_table_name("wechat_user_menu")."(menu_id,menu_type,menu_key,WEID,wid,M_id) VALUES (%d, %s ,%s, %d, %d ,%d)",$menuId, $menuType,$menuKey,$weid,$wid,$mid));
	return $insert;
}
//20140624 janeen add
 function wechat_menu_publicsvc_insert_group($menuId,$menuType,$menuKey,$gweid,$wid,$mid)
 {
   global $wpdb;
	$insert=$wpdb->query($wpdb->prepare("INSERT INTO ".get_table_name("wechat_user_menu")."(menu_id,menu_type,menu_key,GWEID,wid,M_id) VALUES (%d, %s ,%s, %d, %d ,%d)",$menuId, $menuType,$menuKey,$gweid,$wid,$mid));
	return $insert;
}
//通过联合查询获得wid和M_id
function wechat_menu_public_selectwid($menuId,$weid)
{
	global $wpdb;	                                                             
	//$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name("wechat_content_menu")." u1 left join ".get_table_name("wechats_info")." u2 on u1.M_id = u2.M_id where menu_id=".$menuId);
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name("wechat_content_menu")." u1 left join ".get_table_name("wechats_info")." u2 on u1.M_id = u2.M_id left join ".get_table_name("wechat_usechat")." u3 on u2.wid=u3.wid where menu_id=".intval($menuId)." and WEID=".intval($weid));
	return $myrows;
}
//20140624 janeen add
function wechat_menu_public_selectwid_group($menuId,$gweid)
{
	global $wpdb;	                                                             
	//$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name("wechat_content_menu")." u1 left join ".get_table_name("wechats_info")." u2 on u1.M_id = u2.M_id where menu_id=".$menuId);
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name("wechat_content_menu")." u1 left join ".get_table_name("wechats_info")." u2 on u1.M_id = u2.M_id left join ".get_table_name("wechat_usechat")." u3 on u2.wid=u3.wid where menu_id=".intval($menuId)." and GWEID=".$gweid);
	return $myrows;
}
//mashan
//�ҵ�wp_wechat_autoreply��keywordΪsubscribe��arplymesg_id
function wechat_get_aplymesgid($arplykey,$WEID)
{
    global $wpdb;	                                                             
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT arplymesg_id FROM ".get_table_name('wechat_autoreply')." where arply_keyword=%s and arply_type='weChat_news' and WEID=".$WEID,$arplykey));

	return $myrows;
}
//20140623 janeen add
function wechat_get_aplymesgid_group($arplykey,$GWEID)
{
    global $wpdb;	                                                             
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT arplymesg_id FROM ".get_table_name('wechat_autoreply')." where arply_keyword=%s and arply_type='weChat_news' and GWEID=".intval($GWEID),$arplykey));

	return $myrows;
}

//获取会员活动相关信息
if(!function_exists('web_admin_initfunc_info')){
	function web_admin_initfunc_info($user_id,$funcname)
	{
		global $wpdb;
		$myrows = $wpdb->get_results($wpdb->prepare( "SELECT func_flag FROM wp_wechat_initfunc_info where user_id='".$user_id."' and func_name=%s",$funcname));

		foreach($myrows as $func_info)	
		{
			return $func_info->func_flag;
		}
	}
}
if(!function_exists('web_admin_function_info')){
	function web_admin_function_info($weid,$funcname,$wid,$userid)
	{
		global $wpdb;
		//$myrows = $wpdb->get_results( "SELECT func_flag FROM wp_wechat_initfunc_info where WEID=".$WEID." and func_name='".$funcname."'");
	
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".get_table_name("wechat_func_info")." a WHERE a.func_name=%s AND NOT EXISTS(SELECT * FROM ".get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'WEID' AND value = ".$weid." AND func_flag = 0) AND EXISTS(SELECT * FROM ".get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) AND NOT EXISTS(SELECT * FROM ".get_table_name("wechat_initfunc_info")." b3 WHERE a.func_name=b3.func_name AND type = 'wid' AND value = ".intval($wid)." AND func_flag = 0) LIMIT 0, 100",$funcname));	
	
		foreach($myrows as $func_info)	
		{
			return $func_info->status;
		}
	}
}

//2014-06-27新增修改
function web_admin_function_info_group($gweid,$funcname,$wid,$userid)
{
	global $wpdb;
	//$myrows = $wpdb->get_results( "SELECT func_flag FROM wp_wechat_initfunc_info where WEID=".$WEID." and func_name='".$funcname."'");
	
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".get_table_name("wechat_func_info")." a WHERE a.func_name=%s AND NOT EXISTS(SELECT * FROM ".get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$gweid." AND func_flag = 0) AND EXISTS(SELECT * FROM ".get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) AND NOT EXISTS(SELECT * FROM ".get_table_name("wechat_initfunc_info")." b3 WHERE a.func_name=b3.func_name AND type = 'wid' AND value = ".$wid." AND func_flag = 0) LIMIT 0, 100",$funcname));	
	
	foreach($myrows as $func_info)	
	{
		return $func_info->status;
	}
}

//2014-07-08新增修改
function web_admin_function_info_groupnew($gweid,$funcname,$userid)
{
	global $wpdb;
	
	$myrows = $wpdb->get_results($wpdb -> prepare("SELECT * FROM ".get_table_name("wechat_func_info")." a WHERE a.func_name=%s AND NOT EXISTS(SELECT * FROM ".get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".intval($gweid)." AND func_flag = 0) AND EXISTS(SELECT * FROM ".get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".intval($userid)." AND func_flag = 1) LIMIT 0, 100",$funcname));

	
	foreach($myrows as $func_info)	
	{
		return $func_info->status;
	}
}

function getswid($weid,$userid){
		global $wpdb;
		
		$myrows = $wpdb->get_results(  "SELECT * FROM wp_wechat_usechat WHERE WEID = ".intval($weid)."  AND user_id = ".intval($userid));	
		
		return $myrows;

}
//20140623 janeen update
function getswid_group($gweid,$userid){
		global $wpdb;
		
		$myrows = $wpdb->get_results(  "SELECT * FROM wp_wechat_usechat WHERE GWEID = ".intval($gweid)."  AND user_id = ".intval($userid));	
		
		return $myrows;

}
//获取是公有账号还是私有账号
function wechat_wechats_info($wid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM wp_wechats where wid='".intval($wid)."'");
	return $myrows;
}
function wechat_usechat_get($wid)
{
	global $wpdb;	                                                             	
	$myrows = $wpdb->get_results("SELECT * FROM wp_wechat_usechat where wid=".intval($wid));
	return $myrows;

}
function wechat_usechat_get_disuid($wid)
{
	global $wpdb;	                                                             	
	$myrows = $wpdb->get_results("SELECT distinct  user_id FROM wp_wechat_usechat where wid=".intval($wid));
	return $myrows;

}
function wechat_usechat_getflg($wid)
{
	global $wpdb;	                                                             	
	$myrows = $wpdb->get_results("SELECT * FROM wp_wechat_usechat where wid=".intval($wid)." and flgopen=1");
	return $myrows;

}
//验证表wechat_subscribe中是否存在from_user和WEID
function wechat_exist_user_weid($WEID,$fromUsername)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare( "SELECT * FROM wp_wechat_subscribe where WEID=%d and from_user=%s",$WEID,$fromUsername));
	return $myrows;
}
function wechat_exist_user_weid_group($GWEID,$fromUsername)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM wp_wechat_subscribe where GWEID=%d and from_user=%s",$GWEID,$fromUsername));
	return $myrows;
}
function wechat_exist_user_weid_gweid_group($WEID,$GWEID,$fromUsername)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb -> prepare( "SELECT * FROM wp_wechat_subscribe where WEID=%d and GWEID=%d and from_user=%s",$WEID,$GWEID,$fromUsername));
	return $myrows;
}
//取出表wechat_userchat的验证码
function wechat_usechat_ver($wid){
	global $wpdb;
	$myrows = $wpdb->get_results( $wpdb -> prepare("SELECT Vericode FROM wp_wechat_usechat where wid=%s",$wid));
	return $myrows;
}
//验证是否存在该验证码
function wechat_usechat_exist_ver($keyword,$wid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM wp_wechat_usechat where vericode='".$keyword."' and wid=%s",$wid));
	return $myrows;
}
function wechat_usechat_exist_ver_group($keyword,$WEID,$GWEID)
{
	global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM wp_wechat_usechat where vericode=%s and WEID=%d and GWEID=",$keyword,$GWEID));
	return $myrows;
}
//插入subscribe表from_user和WEID
function wechat_insert_user_weid($fromUsername,$WEID)
{
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_subscribe")."(from_user, WEID)VALUES (%s, %d)",$fromUsername, $WEID));
	
	return $insert;
}
function wechat_insert_user_weid_group($fromUsername,$GWEID)
{
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_subscribe")."(from_user, GWEID)VALUES (%s, %d)",$fromUsername, $GWEID));
	
	return $insert;
}
function wechat_insert_user_weid_gweid_group($fromUsername,$WEID,$GWEID)
{
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_subscribe")."(from_user, WEID,GWEID)VALUES (%s, %d, %d)",$fromUsername, $WEID, $GWEID));
	
	return $insert;
}
function wechat_del_user_weid($fromUsername,$WEID)
{

	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_subscribe")." WHERE from_user=%s and WEID=%d",$fromUsername,$WEID));
	return $delete;


}
function wechat_del_user_weid_group($fromUsername,$GWEID)
{

	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_subscribe")." WHERE from_user=%s and GWEID=%d",$fromUsername,$GWEID));
	return $delete;


}
function get_wechats_info($wid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( $wpdb -> prepare("SELECT * FROM ".get_table_name("wechats_info")." where wid=%s",$wid));
	return $myrows;
}
function get_wechats_info_group($WEID)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".get_table_name("wechats_info")." where WEID=".intval($WEID)."");
	return $myrows;
}
function wechats_info_set_mid($M_id)
{
	global $wpdb;
	$num=0;
	$update = $wpdb -> update(get_table_name("wechats_info"),array('M_id'=>$num),array('M_id'=>$M_id),array("%d"),array("%d"));

	return $update;
}
//用于php针对js使用escape函数进行编码后的解码函数
function unescape($str){ 
	$ret = ''; 
	$len = strlen($str); 
	for ($i = 0; $i < $len; $i++){ 
	    if ($str[$i] == '%' && $str[$i+1] == 'u'){ 
	        $val = hexdec(substr($str, $i+2, 4)); 
	    if ($val < 0x7f) $ret .= chr($val); 
	    else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
	    else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 
	        $i += 5; 
	    } 
	    else if ($str[$i] == '%'){ 
	        $ret .= urldecode(substr($str, $i, 3)); 
	        $i += 2; 
	    } 
	   else $ret .= $str[$i]; 
	} 
	return $ret; 
} 
//用于删除素材管理中存在storage中的图片
function material_news_url_delete($newsItemId,$newsId)
{
	global $wpdb;
	$filesize = 0;
	
	if($newsItemId!=null){
		$urls=$wpdb->get_results($wpdb -> prepare( "SELECT news_item_picurl, news_user, news_item_description FROM wp_wechat_material_news where news_item_id=%s",$newsItemId));
		//查找对应的用户
		foreach($urls as $user){
		    $userid = $user->news_user;
		}
		
		foreach($urls as $url){
			//$del_file=substr( $url->news_item_picurl,strripos($url->news_item_picurl,'/uploads/')+9);
			$del_file=$url->news_item_picurl;
			file_unlink($del_file);
			file_unlink_from_xml(str_replace('\"', '"', str_ireplace('../', '', str_ireplace('../uploads', '', $url->news_item_description))));
			/* if(!empty($url->news_item_picurl))
			{
				//@$filesize += filesize( path_join($uploadpath['basedir'], $del_file) );
				$headers = get_headers(path_join($uploadpath['basedir'], $del_file));
				print_r($headers);
				//@$filesize += $headers['Content-Length']/1024;
			} */
		}
		//$wpdb -> query("update {$wpdb -> prefix}wesite_space set used_space = used_space - '".$filesize."' where userid = ".$userid);
		
/* 		$select=$wpdb->get_results("SELECT COUNT(*) as counts FROM " .get_table_name('wechat_material_news')." where news_item_id=".$newsItemId);
		foreach($select as $number){
			$countnumber=$number->counts;
		}
		for($i=1;$i<=$countnumber;$i++){
			$url=$wpdb->get_results( "SELECT news_item_picurl FROM wp_wechat_material_news where news_item_id='".$newsItemId."'");
			if($url===false) {
			continue;}else{
				foreach($url as $ur){
					$u=$ur->news_item_picurl;
				}
				$del_file=substr( $u,strripos($u,'/uploads/')+9);
				$uploadpath = wp_upload_dir();
				@unlink(path_join($uploadpath['basedir'], $del_file) );
			}
		} */
	}else{
		$urls=$wpdb->get_results( $wpdb -> prepare("SELECT news_item_picurl, news_user FROM wp_wechat_material_news where news_id=%s",$newsId));
		foreach($urls as $user){
		    $userid = $user->news_user;
		}
		
		foreach($urls as $url){
			//$del_file=substr( $url->news_item_picurl,strripos($url->news_item_picurl,'/uploads/')+9);
			$del_file=$url->news_item_picurl;
			file_unlink($del_file);
			/* if(!empty($url->news_item_picurl))
			{
				//@$filesize += filesize( path_join($uploadpath['basedir'], $del_file) );
				$headers = get_headers(path_join($uploadpath['basedir'], $del_file));
				print_r($headers);
				//@$filesize += $headers['Content-Length']/1024;
			} */
		}
		
		//$wpdb -> query("update {$wpdb -> prefix}wesite_space set used_space = used_space - '".$filesize."' where userid = ".$userid);
		
	}
	

}
function wechats_insert_log($content)
{
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechats_log")."(content)VALUES (%s)",$content));

}

//2014-3-12 添加公众服务号的数据库信息

//判断是否存在此菜单名称
function wechat_menu_public_isExistInDB($menuname,$M_id)
{
    global $wpdb;
    $sql = $wpdb -> prepare("SELECT COUNT(*) as arrayCount FROM ".get_table_name('wechat_content_menu'). " where menu_name=%s and M_id=%s",$menuname,$M_id);
    $myrows = $wpdb->get_results($sql);
    return $myrows;
}
//插入菜单名字
function wechat_menu_public_add($parentid,$menuname,$M_id)
{
	global $wpdb;
	$insert=$wpdb->query($wpdb->prepare("INSERT INTO ".get_table_name("wechat_content_menu")."(parent_id,menu_name,M_id) VALUES (%d, %s ,%d)",$parentid, $menuname,$M_id));
	return $insert;
}
function wechat_menu_public_updateforchid($menuid,$menutype,$menukey)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>$menutype,'menu_key'=>$menukey),array('menu_id'=>$menuid),array("%s","%s"),array("%d"));

	return $update;
}
function wechat_menu_public_updatenull($menutype,$menukey,$WEID)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_user_menu"),array('menu_type'=>$menutype,'menu_key'=>$menukey),array('WEID'=>$WEID),array("%s","%s"),array("%d"));

	return $update;
}

//拿到菜单树的结构样式
function wechat_public_menu_list($mid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_content_menu')." where M_id=".intval($mid)." order by menu_id");

	return $myrows;
}
//更新菜单树的结构样式
function wechat_public_menu_get($menuid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_content_menu')." where menu_id=".intval($menuid));

	return $myrows;

}
//判断是否存在重名
function wechat_public_menu_isExistInDB($menuname,$M_id)
{
    global $wpdb;
    $sql = $wpdb -> prepare("SELECT COUNT(*) as arrayCount FROM ".get_table_name('wechat_content_menu'). " where menu_name=%s and M_id=%s",$menuname,$M_id);
    $myrows = $wpdb->get_results($sql);
    return $myrows;
}
//更新菜单名称
function wechat_public_menu_name_update($mename,$mid)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_content_menu"),array('menu_name'=>$mename),array('menu_id'=>$mid),array("%s"),array("%d"));

	return $update;
}
//找到菜单的menuid的数据
function wechat_public_menu_gets($menuid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_content_menu')." where menu_id=".intval($menuid));

	return $myrows;

}
function wechat_public_menu_gets_bypar($menuPid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_content_menu')." where parent_id=".intval($menuPid));

	return $myrows;

}
function wechat_public_menu_del($menuid)
{
	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_content_menu")." WHERE menu_id=%d", $menuid));
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_user_menu")." WHERE menu_id=%d", $menuid));
	return $delete;

}
function wechat_public_usermenu_del($menuid)
{
	global $wpdb;	

	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_user_menu")." WHERE menu_id=%d", $menuid));
	return $delete;

}

function wechat_public_menupar_del($menuid)
{
	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_content_menu")." WHERE parent_id=%d", $menuid));
	//$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_user_menu")." WHERE menu_id=%d", $menuid));
	return $delete;

}

function wechat_public_menu_count_del($mid)
{
	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_content_menu")." WHERE M_id=%d", $mid));
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_user_menu")." WHERE M_id=%d", $mid));
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_add_menu")." WHERE M_id=%d", $mid));
	return $delete;

}
function wechat_public_menu_countsm_del($mid)
{
	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_content_menu")." WHERE M_id=%d", $mid));
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_user_menu")." WHERE M_id=%d", $mid));
	return $delete;

}
function wechat_public_user_menu_del($wid)
{
	global $wpdb;	

	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_user_menu")." WHERE wid=%d", $wid));
	
	return $delete;

}
//判断菜单模板名是否存在重名
function wechat_public_menu_demo_isExistInDB($menuname,$M_id)
{
    global $wpdb;
    $sql = $wpdb -> prepare("SELECT COUNT(*) as arrayCount FROM ".get_table_name('wechat_add_menu'). " where M_name=%s and M_id=%s",$menuname,$M_id);
    $myrows = $wpdb->get_results($sql);
    return $myrows;
}
function wechat_public_menu_name_demoupdate($mename,$M_id)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_add_menu"),array('M_name'=>$mename),array('M_id'=>$M_id),array("%s"),array("%d"));

	return $update;
}
//用于封装菜单模板
function wechat_menu_public_parget($parentid,$m_id)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_content_menu')." where M_id=".intval($m_id)." and parent_id=".intval($parentid)." order by menu_id ");

	return $myrows;

}
//修改的微信的publicjoin中的内容
function wechat_menu_public_gets($menuid,$weid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_user_menu')." where menu_id=".intval($menuid)." and WEID=".intval($weid));
	//$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_content_menu')." where M_id=".$m_id." and parent_id=".$parentid);
	return $myrows;

}
function wechat_menu_public_gets_group($menuid,$gweid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_user_menu')." where menu_id=".intval($menuid)." and GWEID=".$gweid);
	//$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_content_menu')." where M_id=".$m_id." and parent_id=".$parentid);
	return $myrows;

}
//找到已经使用M_id的公众号
function wechat_select_public_wid($mid){
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name('wechats_info')." where M_id=".intval($mid));

	return $myrows;
}
//用于商户预览每个公众号对应的菜单模板
function wechat_view_select_menu()
{
	global $wpdb;
	//$myrows = $wpdb->get_results("SELECT count(wechat_nikename) as m_count FROM ".get_table_name("wechats")." u1 left join ".get_table_name("wechats_info")." u2 on ( (u1.wid = u2.wid) AND u1.wechat_type=".$wechat_type." ) left join ".get_table_name(wechat_add_menu)." u3 on u2.M_id=u3.M_id ");
	$myrows = $wpdb->get_results("SELECT count(*) as m_count From ".get_table_name("wechats")." u1 left join ".get_table_name("wechats_info")." u2 on u1.wid = u2.wid left join ".get_table_name("wechat_add_menu")." u3 on u2.M_id=u3.M_id where u1.wechat_type='pub_svc' OR ( u1.wechat_type='pub_sub' && u1.wechat_auth= 1 )");
	
	return $myrows;
}
function web_admin_array_view_menu($offset,$pagesize)
{
	global $wpdb;
	$wechat_type="pub_svc";
	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name("wechats")." u1 left join ".get_table_name("wechats_info")." u2 on u1.wid = u2.wid left join ".get_table_name("wechat_add_menu")." u3 on u2.M_id=u3.M_id where u1.wechat_type='pub_svc' OR ( u1.wechat_type='pub_sub' && u1.wechat_auth= 1 ) limit ".$offset.",".$pagesize);
	//$myrows = $wpdb->get_results("SELECT count(wechat_nikename) as m_count FROM ".get_table_name("wechats")." u1 left join ".get_table_name("wechats_info")." u2 on ( (u1.wid = u2.wid) AND u1.wechat_type=".$wechat_type." ) left join ".get_table_name(wechat_add_menu)." u3 on u2.M_id=u3.M_id ");
	
	return $myrows;
}
//判断该mid是否存在该mid
function wechat_select_demo_exist($wid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results("SELECT M_id FROM " .get_table_name('wechats_info')." where wid=".intval($wid));

	return $myrows;
}
/*共用公众号权限设置*/
function wechat_usechat_getwids_forsel($GWEID)
{
	global $wpdb;	                                                             
	$getwids = $wpdb->get_results("SELECT * FROM " .get_table_name('wechat_usechat')." WHERE GWEID = ".intval($GWEID));
	return $myrows;

}
function web_user_display_index_group_forsel($GWEID, $userid, $wid)
{
    global $wpdb;

	$myrows = $wpdb->get_results("SELECT * FROM " .get_table_name("wechat_func_info")." a WHERE NOT EXISTS(SELECT * FROM " .get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".intval($GWEID)." AND func_flag = 0) AND EXISTS(SELECT * FROM " .get_table_name("wechat_initfunc_info")." b2 WHERE a.func_name=b2.func_name AND type = 'userid' AND value = ".$userid." AND func_flag = 1) AND NOT EXISTS(SELECT * FROM " .get_table_name("wechat_initfunc_info")." b3 WHERE a.func_name=b3.func_name AND type = 'wid' AND value = ".intval($wid)." AND func_flag = 0) LIMIT 0, 100");
	
	return $myrows;
}
function wechat_group($GWEID)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_var("SELECT user_id FROM " .get_table_name('wechat_group')." where GWEID=".$GWEID);
	return $myrows;

}
function getWechatGroupInfo_gweid_all($GWEID)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".get_table_name("wechat_group")." where GWEID=".$GWEID );
	return $myrows;
}
function getWechatGroupInfo_pubgweid_all($userid,$weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".get_table_name("wechat_group")." where user_id=".$userid." and WEID=".intval($weid) );
	return $myrows;
}
function getWechatGroup_count($userid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT count(*) as wechatCount FROM ".get_table_name("wechat_group")." where user_id=".$userid );
	return $myrows;
}
function web_user_display_index_groupnew_forsel($GWEID)
{
    global $wpdb;

	$myrows = $wpdb->get_results("SELECT * FROM ".get_table_name("wechat_func_info")." a WHERE NOT EXISTS(SELECT * FROM ".get_table_name("wechat_initfunc_info")." b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$GWEID." AND func_flag = 0) LIMIT 0, 100");
	
	return $myrows;
}
/*共用公众号权限设置end*/

//会员分页设置mashan
function wechat_get_vipmember_count($gweid)
{
    global $wpdb;
    $myrows = $wpdb->get_var( "SELECT COUNT(*) FROM ".get_table_name("wechat_member")." where GWEID=".intval($gweid));
	return $myrows;
}
function wechat_get_vipmember_offset_list($gweid,$offset,$psize)
{
    global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".get_table_name("wechat_member")." where GWEID=".$gweid." limit ".$offset.",".$psize );
	return $myrows;
	
}
function paginationa_page($tcount, $pindex, $psize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => '')) {
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
		if($url) {
			$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
			$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
			$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
			$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
		} else {
			$_GET['page'] = $pdata['findex'];
			$pdata['faa'] = 'href="' .'?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['pindex'];
			$pdata['paa'] = 'href="' . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['nindex'];
			$pdata['naa'] = 'href="' . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['lindex'];
			$pdata['laa'] = 'href="' .'?' . http_build_query($_GET) . '"';
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
					$_GET['page'] = $i;
					$aa = 'href="?' . http_build_query($_GET) . '"';
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
function wechat_get_count_selectvmember_group($gweid,$indata,$rg)
{
    global $wpdb;
    $myrows = $wpdb->get_results($wpdb->prepare( "SELECT COUNT(*) FROM ".get_table_name("wechat_member")." where GWEID='".$gweid."' and ".$rg." like '%%%s%%'",$indata));
	
	return $myrows;
}
function wechat_get_selectvmember_group($gweid,$indata,$rg,$offset,$psize)
{
    global $wpdb;
	$myrows = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".get_table_name("wechat_member")." where GWEID=".$gweid." and ".$rg." like '%%%s%%' limit ".$offset.",".$psize,$indata) );

	return $myrows;
	
}
/* Fans Function
   2014-08-14
*/
function wechat_insert_fans($wid,$fromUsername,$number,$WEID,$GWEID)
{
	global $wpdb;
	$insert=$wpdb->query( $wpdb->prepare("INSERT INTO ".get_table_name("wechat_fans")."(wid, from_user, number, WEID, GWEID)VALUES (%d, %s, %d, %d ,%d)",$wid, $fromUsername, $number, $WEID, $GWEID));	
	return $insert;
}
function wechat_delete_fans_weid($fromUsername,$WEID)
{

	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_fans")." WHERE from_user=%s and WEID=%d",$fromUsername,$WEID));
	return $delete;
}

function wechat_delete_fans_wid($fromUsername,$wid)
{
	global $wpdb;	
	
	$delete=$wpdb->query( $wpdb->prepare( "DELETE FROM ".get_table_name("wechat_fans")." WHERE from_user=%s and wid=%d",$fromUsername,$wid));
	return $delete;

}
function wechat_update_fans($number,$fromUsername,$WEID,$GWEID,$wid)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_fans"),array('number'=>$number,'WEID'=>$WEID,'GWEID'=>$GWEID),array('from_user'=>$fromUsername,'wid'=>$wid),array("%d","%d","%d"),array("%s","%d"));

	return $update;
}
function wechat_update_usechat($fanscount,$WEID)
{
	global $wpdb;
	
	$update = $wpdb -> update(get_table_name("wechat_usechat"),array('wechat_fanscount'=>$fanscount),array('WEID'=>$WEID),array("%d"),array("%d"));

	return $update;
}
//Get the count of the fans for each userid
function wechat_get_count_fans($userid)
{
    global $wpdb;
    $myrows = $wpdb->get_results("SELECT COUNT(*) as fans_count FROM ".get_table_name("wechat_fans")." f WHERE EXISTS (SELECT * FROM " .get_table_name("wechat_group"). " g WHERE g.user_id = " . $userid ." AND g.weid=f.weid AND weid!=0)");
	return $myrows;
}
//Get the count of the fans for each weid
function wechat_get_count_weid_fans($weid)
{
    global $wpdb;
    $myrows = $wpdb->get_results("SELECT COUNT(*) as fans_weid_count FROM ".get_table_name("wechat_fans"). " WHERE weid = " . intval($weid));
	return $myrows;
}
/*End*/
function getWechatGroupActiveInfo_all($userid,$shared_flag)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM ".get_table_name("wechat_group")." where WEID != 0 and user_id=".$userid." and shared_flag=".intval($shared_flag) );
	return $myrows;
}

//get vip point
function wechat_vip_point_count($mid,$wherescr,$whereegg,$wherered)
{
    global $wpdb;
    $totalscratchcard = $wpdb->get_var($wpdb -> prepare("SELECT COUNT(*) FROM {$wpdb->prefix}scratchcard_winner  AS a LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid WHERE a.mid = %s AND a.award <> '' {$wherescr}",$mid));
	 
	$totalegg = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}egg_winner AS c
				LEFT JOIN {$wpdb->prefix}wechat_member AS d ON c.mid = d.mid WHERE c.mid = %d AND c.award <> '' {$whereegg}", $mid));
	
	$totalred = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}redenvelope_winner AS e
				LEFT JOIN {$wpdb->prefix}wechat_member AS f ON e.mid = f.mid WHERE e.mid = %d {$wherered}", $mid));
	
	return $totalscratchcard+$totalegg+$totalred;
}
function wechat_vip_point_list($mid,$offset,$pagesize,$wherescr,$whereegg,$wherered)
{
    global $wpdb;
	$list = $wpdb -> get_results($wpdb -> prepare("( SELECT '刮刮卡' as type ,a.id, a.award as paward, a.description as pdescription, a.status as pstatus, a.createtime as ctime, b.realname, b.mobilenumber FROM {$wpdb->prefix}scratchcard_winner AS a
				LEFT JOIN {$wpdb->prefix}wechat_member AS b ON a.mid = b.mid WHERE a.mid = %s AND a.award <> ''  {$wherescr}) union (SELECT '砸蛋' as type , c.id, c.award as paward, c.description as pdescription, c.status as pstatus , c.createtime as ctime, d.realname, d.mobilenumber FROM {$wpdb->prefix}egg_winner AS c
				LEFT JOIN {$wpdb->prefix}wechat_member AS d ON c.mid = d.mid WHERE c.mid = %s AND c.award <> ''  {$whereegg}) union (SELECT '红包' as type , e.id, e.amount as paward, e.credit as pdescription, e.status as pstatus , e.createtime as ctime, f.realname, f.mobilenumber FROM {$wpdb->prefix}redenvelope_winner AS e
				LEFT JOIN {$wpdb->prefix}wechat_member AS f ON e.mid = f.mid WHERE e.mid = %s  {$wherered}) ORDER BY ctime DESC, pstatus ASC LIMIT {$offset},{$pagesize}",$mid,$mid,$mid));
	return $list;
}
/* Mass Function
   2015-04-13
*/
function wechat_update_mass_status($wid,$MsgID,$Status,$TotalCount,$FilterCount,$SentCount,$ErrorCount)
{

	global $wpdb;	
	
	$update = $wpdb -> update(get_table_name("wechat_mass_statistics"),array('status'=>$Status,'totalcount'=>$TotalCount,'sentcount'=>$SentCount,'filtercount'=>$FilterCount,'errorcount'=>$ErrorCount),array('wid'=>$wid,'msgid'=>$MsgID));
}

//判断gweid是否为虚拟号的gweid
function web_admin_function_virtual_gweid($GWEID){
	global $_W,$wpdb;
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$getgweids = $wpdb->get_results("SELECT count(*) as acount FROM {$wpdb->prefix}wechat_group w where w.WEID = 0 and w.adminshare_flag = 1 and w.GWEID=".$GWEID,ARRAY_A);
	foreach ($getgweids as $getgweid) {
		return $getgweid['acount'];
	}

}

?>