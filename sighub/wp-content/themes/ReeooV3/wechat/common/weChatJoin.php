<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

require_once $wp_root_path.'/wp-content/themes/ReeooV3/wechat/editrep/editreply.func.php';


function wechat_info_gets($hash)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_wechats where hash=%s",$hash));
	return $myrows;

}
function wechat_usechat_gets($wid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_var("SELECT WEID FROM wp_wechat_usechat where wid=".intval($wid));
	return $myrows;

}
function wechat_usechat_gets_gweid($wid)
{
	global $wpdb;	                                                             
	$myrows = $wpdb->get_var("SELECT GWEID FROM wp_wechat_usechat where wid=".intval($wid));
	return $myrows;

}

function web_user_display_index_groupnew($GWEID)
{
    global $wpdb;

	$myrows = $wpdb->get_results("SELECT * FROM wp_wechat_func_info a WHERE NOT EXISTS(SELECT * FROM wp_wechat_initfunc_info b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = ".$GWEID." AND func_flag = 0) LIMIT 0, 100");
	
	return $myrows;
}

function getWechatGroupInfo_wechat($GWEID)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM wp_wechat_group where GWEID=".intval($GWEID) );
	return $myrows;
}
function getWechatGroupInfo_wechat_pubgweid($userid,$weid)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM wp_wechat_group where user_id=".intval($userid)." and WEID=".intval($weid) );
	return $myrows;
}
function getWechatGroupInfoActive_wechat_pubgweid($userid,$shared_flag)
{
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM wp_wechat_group where WEID != 0 and user_id=".$userid." and shared_flag=".$shared_flag );
	return $myrows;
}

$winfo=wechat_info_gets($_GET["hash"]);
	foreach($winfo as $wi){
		$TOK=$wi->token;
		//echo 'csq:'.$wi->token;
		//update_option('wechat_debug','CSQ:'.$wi->token);
		//$TOK="weixinCourse";
		$wid=$wi->wid;		
	}	
$WEID=wechat_usechat_gets($wid);
$GWEID=wechat_usechat_gets_gweid($wid);//20140624 janeen add

/**
function:get the gweid(shared)
*/
$info=getWechatGroupInfo_wechat($GWEID);
foreach($info as $weidinfo){
	$shared_flag=$weidinfo->shared_flag;
	$user_id=$weidinfo->user_id;
	if($shared_flag==1){
		$weinfo=getWechatGroupInfoActive_wechat_pubgweid($user_id,2);
		foreach($weinfo as $gweids){
			$GWEID=$gweids->GWEID;
		}
	}
}


/*add for sel*/
$result = web_user_display_index_groupnew($GWEID);
$selCheck=array();
foreach($result as $initfunc){
	if($selCheck[$initfunc->func_name] == 0)
	$selCheck[$initfunc->func_name] = $initfunc->status;
}

/*
	if($useinfo!=null){
		foreach($useinfo as $ui){
			$WEID=$ui->WEID;		
		}
	}
	*/
	define("TOKEN",$TOK); //TOKEN值	
	//echo "";
	$wechatObj = new wechat();
	$wechatObj->valid($WEID,$GWEID,$selCheck,$wid);
	
	class wechat {
	
		//微信验证
		public function valid($WEID,$GWEID,$selCheck,$wid) {
			$echoStr= $_GET["echostr"];
			if($this->checkSignature()){
				echo $echoStr;
				$this->responseMsg(intval($WEID),intval($GWEID),$selCheck,$wid);//20140624 janeen update
				exit;
			}
		}
		//微信验证方法
	    private function checkSignature() {
	        $signature= $_GET["signature"];
	        $timestamp= $_GET["timestamp"];
	        $nonce= $_GET["nonce"];
	        $token= TOKEN;
			$tmpArr= array($token,$timestamp, $nonce);
	        sort($tmpArr,SORT_STRING);
	        $tmpStr= implode( $tmpArr );
	        $tmpStr= sha1( $tmpStr );
	        if($tmpStr == $signature) {
	            return true;
	        } else {
	            return false;
	        }
	    }
		
		//处理回复
		public function responseMsg($WEID,$GWEID,$selCheck,$wid){//20140624 janeen update
			//get post data, May be due to the different environments
			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];   
			//extract post data 
			if (!empty($postStr)){ 
				include 'webManagConn.php';
                include 'wechat_dbaccessor.php';
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA); 
                
				//判断接收到的是什么类型的消息
				$RX_TYPE = trim($postObj->MsgType);
				
				switch($RX_TYPE){
					case "text":
						$resultStr=$this->handleText($postObj,$WEID,$GWEID,$selCheck);//20140624 janeen update
						break;
					case "event":
						$resultStr=$this->handleEvent($postObj,$WEID,$GWEID,$selCheck,$wid);//20140624 janeen update
						break;
					case "image":
						$resultStr=$this->handleImage($postObj,$WEID,$GWEID,$selCheck);//20140624 janeen update
						break;
					case "location":
						$resultStr=$this->handleLocation($postObj,$WEID,$GWEID,$selCheck);//20140624 janeen update
						break;	
					case "link":
						$resultStr=$this->handleLink($postObj,$WEID,$GWEID,$selCheck);//20140624 janeen update
						break;		
					default:
						$resultStr="Unknow msg type: ".$RX_TYPE;
						break;
					
				}
				echo $resultStr;
						
			}else {
				return; 
			} 
		}
		//处理文本类型的回复
		public function handleText($obj,$WEID,$GWEID,$selCheck){	//20140624 janeen update	
			//获取用户发送的内容
			$fromUsername = $obj->FromUserName;
			$toUsername = $obj->ToUserName;
			$keyword = trim($obj->Content);
			
			$editable_keyword_exists = editable_reply_exists($fromUsername,$GWEID,$WEID,'keyword',$keyword);
			$editable_nokeyword_exists = editable_reply_exists($fromUsername,$GWEID,$WEID,'nokeyword');
			if($keyword!=null && $editable_keyword_exists){
				$resultStr = $this->responseText($obj, editable_reply_content($obj->FromUserName,$GWEID,$WEID,$editable_keyword_exists,'keyword'));
			}elseif($keyword!=null && $editable_nokeyword_exists && !wechat_keyword_exist_group($keyword,$GWEID)){
				$resultStr = $this->responseText($obj, editable_reply_content($obj->FromUserName,$GWEID,$WEID,$editable_nokeyword_exists,'nokeyword'));
			}elseif($keyword!=null){
				//$keyword_exist=wechat_keyword_exist($keyword,$WEID);
				$keyword_exist=wechat_keyword_exist_group($keyword,$GWEID);	//20140624 janeen update
				//如果没有关键词就选择没有关键词的回复内容
				$isNokey=true;
				$isKey=true;
				if($keyword_exist==false){
					if($selCheck['wechatfuncnokeywordsreply']!=1){
						$isOpen=false;
						$isNokey=false;
						$reply=null;
					}else{
						$isOpen=true;
						$isNokey=true;
						//$reply=wechat_mess_kw_get('nokey',$WEID);
						$reply=wechat_mess_kw_get_group('nokey',$GWEID);//20140624 janeen update
					}
				}else{
					if($selCheck['wechatfunckeywordsreply']!=1){
						$isOpen=false;
						$isKey=false;
						$reply=null;
					}else{
						$isOpen=true;
						$isKey=true;
						//$reply=wechat_mess_kw_get($keyword,$WEID);
						$reply=wechat_mess_kw_get_group($keyword,$GWEID);//20140624 janeen update
					}
				}
				
				//判断回复类型以及回复的内容id	
				
				if($isOpen){
					foreach($reply as $ryinfo){
						$contentStrType = $ryinfo->arply_type;
						$autoId=$ryinfo->arplymesg_id;
					}
					$resultStr=$this->responseMessage($contentStrType,$autoId,$obj,$WEID,$GWEID);
				}else{
					if(!$isNokey){
						$resultStr = $this->responseText($obj, "对不起，无匹配回复权限没有开启");
					}
					if(!$isKey){
						$resultStr = $this->responseText($obj, "对不起，关键词回复权限没有开启");
					}
				}
						
			}
			$tmp = '';
			$tmp .= $resultStr;
			
			echo $tmp;
		}

		//处理关注事件的回复								
		public function handleEvent($obj,$WEID,$GWEID,$selCheck,$wid){
			$resultStr = "kaishi";
			$contentStr = "kaishi";
			switch ($obj->Event){
				case "subscribe":										
					if($selCheck['wechatfuncfirstconcern']!=1){
						$isOpen=false;
						$reply=null;
					}else{
						$isOpen=true;
						//$reply=wechat_mess_kw_get('subscribe',$WEID);
						$reply=wechat_mess_kw_get_group('subscribe',$GWEID);//20140624 janeen update
					}
					if(!empty($WEID)){
						$usechats = wechat_info_get($WEID);
						foreach($usechats as $usechat){
							$wechat_fanscount=$usechat->wechat_fanscount;
						}
						$newcount=$wechat_fanscount+1;
						wechat_update_usechat($newcount,$WEID);
						wechat_insert_fans("",$obj->FromUserName,$newcount,$WEID,$GWEID);
					}
					
					
					$editable_reply_exists_id = editable_reply_exists($obj->FromUserName,$GWEID,$WEID,'autorep');
					if($editable_reply_exists_id)
						$resultStr = $this->responseText($obj, editable_reply_content($obj->FromUserName,$GWEID,$WEID,$editable_reply_exists_id,'autorep'));
					else
						if($isOpen){
							foreach($reply as $ryinfo){
								$contentStrType = $ryinfo->arply_type;
								$autoId=$ryinfo->arplymesg_id;
							}					
							$resultStr=$this->responseMessage($contentStrType,$autoId,$obj,$WEID,$GWEID);
						}else{
							$resultStr = $this->responseText($obj, "对不起，首次关注回复权限没有开启");
						}
						
					//if($reg===false){
					//	echo "关注失败";
					//}
					echo $resultStr;
					//这一条不能删
					//return $resultStr;
					break;
				case "unsubscribe":
					//$ureg=wechat_user_unreg($obj->FromUserName);
					//if($ureg===false){
						//echo "失败";
					//}
					wechat_delete_fans_weid($obj->FromUserName,$WEID);
					break;
				case "CLICK":					
					if($selCheck['wechatfuncmenumanage']!=1){
						$isOpen=false;
						$meinf=null;
					}else{
						$isOpen=true;
						$eventKey=$obj->EventKey;
						$meinf=wechat_menu_get($eventKey);
					}
					if($isOpen){
						foreach($meinf as $minfo){
							$mekey=$minfo->menu_key;
						}
						$mtype=substr($mekey,0,1);
						$mk=substr($mekey,1);
						$resultStr=$this->responseMenu($mtype,$mk,$obj,$WEID,$GWEID);
					}else{
						$resultStr = $this->responseText($obj, "对不起，菜单权限没有开启");
					}
					echo $resultStr;
					//update_option("a",$resultStr);
					break;
				case "MASSSENDJOBFINISH":
					$content = "消息ID：".$obj->MsgID.
							   "\n结果：".$obj->Status.
							   "\n粉丝数：".$obj->TotalCount.
							   "\n过滤：".$obj->FilterCount.
							   "\n发送成功：".$obj->SentCount.
							   "\n发送失败：".$obj->ErrorCount;
					wechat_update_mass_status($wid,$obj->MsgID,$obj->Status,$obj->TotalCount,$obj->FilterCount,$obj->SentCount,$obj->ErrorCount);
					break;
				default :
					$contentStr = "Unknow Event: ".$obj->Event;
					$resultStr = $this->responseText($obj, $contentStr);
					break;
			}
			
		}
		
		public function responseMessage($contentStrType,$autoId,$obj,$WEID,$GWEID){
			//自定义回复的是文本					
			if($contentStrType=="weChat_text"){						
				$msgType="text";
				$me_text=wechat_text_get($autoId);
				//回复消息插入消息记录
				//$insertSql="insert into wp_wechat_messages (Content,FromUserName,ToUserName,MsgType,Time) values ('".."','".."','".."','".."','".date('Y-m-d H:i:s',time())."')";										
				//$me_insert=wechat_mess_insert($object->Content,$object->FromUserName,$object->ToUserName,$msgType);
				//if($me_insert===false){
				//	echo "添加失败";
				//}
				//回复触发
				foreach($me_text as $text){
					$contentStr = $text->text_content;
					//$contentStr = $contentStrType;
					$resultStr = $this->responseText($obj, $contentStr);
					
				}
				
			}			
			//自定义回复的是多图文					
			if($contentStrType=="weChat_news"){
				$result=wechat_news_get($autoId);						
				$resultc=wechat_get_news_count($autoId);						
				foreach($resultc as $nc){
					$count=$nc->counts;
				}
										
				$i=0;
				$upload =wp_upload_dir();
				
				foreach($result as $ns){
					/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
					$tmp = stristr($ns->news_item_url,"http");
					if(($tmp===false)&&(!empty($ns->news_item_url))){
						$newsitemurl=home_url().$ns->news_item_url;
					}else{				
						$newsitemurl=$ns->news_item_url;
					}
					if((empty($ns->news_item_picurl))||(stristr($ns->news_item_picurl,"http")!==false)){
						$itempicurl=$ns->news_item_picurl;
					}else{
						$itempicurl=$upload['baseurl'].$ns->news_item_picurl;
					}
					$data[$i]['id']=$ns->news_id;
					$data[$i]['title']=$ns->news_item_title;
					if(count($result)==1){
						$data[$i]['description']=strip_tags($ns->news_item_abstract);
					}else{
						$data[$i]['description']=strip_tags($ns->news_item_description);
					}
					
					$data[$i]['picurl']=$itempicurl;
					$data[$i]['url']=$newsitemurl;
					$i=$i+1;						
				}										
				$resultStr=$this->responseNews($obj,$data,$WEID,$GWEID);				
			}
			return $resultStr;
		}
		public function responseMenu($mtype,$mk,$obj,$WEID,$GWEID){
			if($mtype=='t'){
				$me_text=wechat_text_get($mk);
				foreach($me_text as $text){
					$contentStr = $text->text_content;
					//$contentStr = $contentStrType;
					$resultStr = $this->responseText($obj, $contentStr);
				}
			}
			if($mtype=='s'){
				$result=wechat_news_get($mk);						
				$resultc=wechat_get_news_count($mk);						
				foreach($resultc as $nc){
					$count=$nc->counts;
				}											
				$i=0;
				$upload =wp_upload_dir();
				foreach($result as $ns){						
					/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
					$tmp = stristr($ns->news_item_url,"http");
					if(($tmp===false)&&(!empty($ns->news_item_url))){
						$newsitemurl=home_url().$ns->news_item_url;
					}else{				
						$newsitemurl=$ns->news_item_url;
					}
					if((empty($ns->news_item_picurl))||(stristr($ns->news_item_picurl,"http")!==false)){
						$itempicurl=$ns->news_item_picurl;
					}else{
						$itempicurl=$upload['baseurl'].$ns->news_item_picurl;
					}
					$data[$i]['id']=$ns->news_id;
					$data[$i]['title']=$ns->news_item_title;
					
					if(count($result)==1){
						$data[$i]['description']=strip_tags($ns->news_item_abstract);
					}else{
						$data[$i]['description']=strip_tags($ns->news_item_description);
					}
					
					$data[$i]['picurl']=$itempicurl;
					$data[$i]['url']=$newsitemurl;
					$i=$i+1;						
				}										
				$resultStr=$this->responseNews($obj,$data,$WEID,$GWEID);
			}
			return $resultStr;
		}
		//text format
		public function responseText($object, $content, $flag=0){
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>%d</FuncFlag>
						</xml>";
			
			$content = htmlspecialchars_decode($content);
				//过滤HTML
			$content = str_replace(array('<br>', '&nbsp;', "<p>\n\t", "</p>\n"), array("\n", ' ','',''), $content);
			$content = str_replace(array("<p>\n", "</p>"), array('',''), $content);
			$content = str_replace(array("<p>", "</p>"), array('',''), $content);
			$content = strip_tags($content, '<a>');

			$resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
			return $resultStr;
			
		}
				
		//news format  
		public function responseNews($object,$data,$WEID,$GWEID){//$title,$description,$picUrl,$url
			$content='';
			$time=time();
			$signature = md5($time.NONCE_SALT);
			$count=count($data);
			//$myOpenId="&openid=".$object->FromUserName;
			$myOpenInfo="WEID=".$WEID."&GWEID=".$GWEID."&fromuser=".$object->FromUserName;
			
			$header="
			<xml>
			<ToUserName><![CDATA[".$object->FromUserName."]]></ToUserName>
			<FromUserName><![CDATA[".$object->ToUserName."]]></FromUserName>
			<CreateTime>".$time."</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>".$count."</ArticleCount>
			<Articles>";
			foreach($data as $value){
		
			$tmp="
			<item>
			<Title><![CDATA[".$value['title']."]]></Title> 
			<Description><![CDATA[".$value['description']."]]></Description>
			<PicUrl><![CDATA[".$value['picurl']."]]></PicUrl>
			<Url><![CDATA[".get_template_directory_uri()."/wechat/common/createurl.php?".$myOpenInfo."&newsid=".$value['id']."&time={$time}&signature={$signature}&redirect_url=".urlencode($value['url'])."]]></Url>	
			</item>";
				$content=$content.$tmp;
					
			}		
			$footer="
			</Articles>
			<FuncFlag>1</FuncFlag>
			</xml>";
			
			return $header.$content.$footer;
						

		}
				
		//music format
		public function responseMusic($fromUsername,$toUsername,$title,$description,$musicUrl,$HQMusicUrl){		
			$time=time();
			$tmpStr= "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[music]]></MsgType>
					<Music>
					<Title><![CDATA[TITLE]]></Title>
					<Description><![CDATA[DESCRIPTION]]></Description>
					<MusicUrl><![CDATA[MUSIC_Url]]></MusicUrl>
					<HQMusicUrl><![CDATA[HQ_MUSIC_Url]]></HQMusicUrl>
					</Music>
					<FuncFlag>1</FuncFlag>
					</xml>";
			$tmpStr=sprintf($tmpStr,$fromUsername,$toUsername,$time,$title,$description,$musicUrl,$HQMusicUrl);
			return $tmpStr;
		}
	}		
	
?>