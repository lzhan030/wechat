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
$winfo=wechat_info_gets($_GET["hash"]);
	foreach($winfo as $wi){
		$TOK=$wi->token;
		$wid=$wi->wid;		
	}	
	
	define("TOKEN",$TOK); //TOKEN值	
	$wechatObj = new wechat();
	$wechatObj->valid($wid);
	
	class wechat {
	
		public function valid($wid) {
			$echoStr= $_GET["echostr"];
			if($this->checkSignature()){
				echo $echoStr;
				$this->responseMsg($wid);
				exit;
			}
		}
 
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
		
		public function responseMsg($wid){
			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];   
			if (!empty($postStr)){ 
				include 'webManagConn.php';
                include 'wechat_dbaccessor.php';
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA); 
                
				//判断接收到的是什么类型的消息
				$RX_TYPE = trim($postObj->MsgType);
				//table:usechat
				$usechatinfo=wechat_usechat_get($wid);
				$isCon=false;
				//判断该用户是否已经关注商家
				foreach($usechatinfo as $uc){
					$WEIDs=$uc->WEID;
					$GWEIDs=$uc->GWEID;
					//table:wp_wechat_subscribe
					$exist_user_weid=wechat_exist_user_weid_gweid_group($WEIDs,$GWEIDs,$postObj->FromUserName);
					if($exist_user_weid!=null){
						$isCon=true;
						$WEID=$WEIDs;
						$GWEID=$GWEIDs;	
						
						//get initfunc status						
						$result = web_user_display_index_groupnew_forsel($GWEID);
						$selCheck=array();
						foreach($result as $initfunc){
							if($selCheck[$initfunc->func_name] == 0)
							$selCheck[$initfunc->func_name] = $initfunc->status;
						}
						break;//如果找到这个用户，就无须循环了
					}
				}
				//如果该用户还没有关注某个商家
				if(!$isCon){
					if ($RX_TYPE=="text") { // 文本类型
						$resultStr=$this->handleRegisText($postObj,$wid,$selCheck);//此时$selCheck无值
						echo $resultStr;  
					}else if ($RX_TYPE=="event") {// 事件类型
						$resultStr=$this->handleEvent($postObj,"",$GWEID,$wid,$selCheck,false);//此时$selCheck无值
						echo $resultStr;
					}					
				}else{//如果该用户已经关注了某个商家
					switch($RX_TYPE){
						case "text":
							$resultStr=$this->handleText($postObj,$WEID,$GWEID,$wid,$selCheck);
							break;
						case "event":
							$resultStr=$this->handleEvent($postObj,$WEID,$GWEID,$wid,$selCheck,true);
							break;
						case "image":
							$resultStr=$this->handleImage($postObj,$WEID,$GWEID,$selCheck);
							break;
						case "location":
							$resultStr=$this->handleLocation($postObj,$WEID,$GWEID,$selCheck);
							break;	
						case "link":
							$resultStr=$this->handleLink($postObj,$WEID,$GWEID,$selCheck);
							break;		
						default:
							$resultStr="Unknow msg type: ".$RX_TYPE;
							break;							
					}
					echo $resultStr;  						
				}
								 
			}else {
				return; 
			} 
		}
		//处理文本类型的回复
		public function handleText($poObj,$WEID,$GWEID,$wid,$selCheck){		
			$fromUsername = $poObj->FromUserName;
			$toUsername = $poObj->ToUserName;
			$keyword = trim($poObj->Content);
			$time = time();
			
			if($keyword!=null){				
				//获取退出商家关注需输入的退出码 table:usechat
				$exitce=wechat_info_get($WEID);
				foreach($exitce as $ec){
					$buexcode=$ec->busi_exit;
					$exreplay=$ec->prompt_content;
				}
				//如果用户输入的等于该公用号的退出码
				if($buexcode == $keyword){
					$subreturn = '';
					$subreturn=$this->responseExitMessage("text",$poObj,$exreplay,$wid,$excode);
					echo $subreturn;
					//从数据库subscribe中删除关注记录
					//table:wechat_subscribe
					$delsub=wechat_del_user_weid_group($poObj->FromUserName,$GWEID);
					wechat_update_fans("",$poObj->FromUserName,"","",$wid);
				}else{			
					
					
					/**
					function:get the gweid(shared)
					*/
					$info=getWechatGroupInfo_gweid_all($GWEID);
					foreach($info as $weidinfo){
						$shared_flag=$weidinfo->shared_flag;
						$user_id=$weidinfo->user_id;
						if($shared_flag==1){
							$weinfo=getWechatGroupActiveInfo_all($user_id,2);
							foreach($weinfo as $gweids){
								$GWEID=$gweids->GWEID;
							}
						}
					}
					
					
					$editable_keyword_exists = editable_reply_exists($fromUsername,$GWEID,$WEID,'keyword',$keyword);
					$editable_nokeyword_exists = editable_reply_exists($fromUsername,$GWEID,$WEID,'nokeyword');
					if($editable_keyword_exists){
						$resultStr = $this->responseText($poObj, editable_reply_content($poObj->FromUserName,$GWEID,$WEID,$editable_keyword_exists,'keyword'));
					}elseif($editable_nokeyword_exists && !wechat_keyword_exist_group($keyword,$GWEID)){
						$resultStr = $this->responseText($poObj, editable_reply_content($poObj->FromUserName,$GWEID,$WEID,$editable_nokeyword_exists,'nokeyword'));
					}else{
						$keyword_exist=wechat_keyword_exist_group($keyword,$GWEID);//20140624 janeen update

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
								$reply=wechat_mess_kw_get_group('nokey',$GWEID);
							}
						}else{
							if($selCheck['wechatfunckeywordsreply']!=1){
								$isOpen=false;
								$isKey=false;
								$reply=null;
							}else{
								$isOpen=true;
								$isKey=true;
								$reply=wechat_mess_kw_get_group($keyword,$GWEID);
							}
						}

						//判断回复类型以及回复的内容id	
						
						if($isOpen){
							foreach($reply as $ryinfo){
								$contentStrType = $ryinfo->arply_type;
								$autoId=$ryinfo->arplymesg_id;
							}					
							$resultStr=$this->responseMessage($contentStrType,$autoId,$poObj,$WEID,$GWEID);
						}else{
							if(!$isNokey){
								$resultStr = $this->responseText($poObj, "对不起，无匹配回复权限没有开启");
							}
							if(!$isKey){
								$resultStr = $this->responseText($poObj, "对不起，关键词回复权限没有开启");
							}
						}
					}
					
					$tmp = '';
					$tmp .= $resultStr;						
					echo $tmp;
					//echo $resultStr.$resultStr;					
				}
			}else{
				echo "Input something...";
			}
		}

		//处理关注事件的回复								
		public function handleEvent($obj,$WEID,$GWEID,$wid,$selCheck,$isP){
			$resultStr = "kaishi";
			$contentStr = "kaishi";
			switch ($obj->Event){
				case "subscribe":					
					if(!$isP){						
						$subreturn = '';
						$subreturn=$this->responseExitMessage("business",$obj,"",$wid,"");
						wechat_insert_fans($wid,$obj->FromUserName,"","","");
						echo $subreturn;
						break;					
					}else{
						
						
						/**
						function:get the gweid(shared)
						*/
						$info=getWechatGroupInfo_gweid_all($GWEID);
						foreach($info as $weidinfo){
							$shared_flag=$weidinfo->shared_flag;
							$user_id=$weidinfo->user_id;
							if($shared_flag==1){
								$weinfo=getWechatGroupActiveInfo_all($user_id,2);
								foreach($weinfo as $gweids){
									$GWEID=$gweids->GWEID;
								}
							}
						}
						
						
						
						
						if($selCheck['wechatfuncfirstconcern']!=1){
							$isOpen=false;
							$reply=null;
						}else{
							$isOpen=true;						
							$reply=wechat_mess_kw_get_group('subscribe',$GWEID);//20140624 janeen update
						}
						if($isOpen){
							$editable_reply_exists_id = editable_reply_exists($obj->FromUserName,$GWEID,$WEID,'autorep');
							if($editable_reply_exists_id)
								$resultStr = $this->responseText($obj, editable_reply_content($obj->FromUserName,$GWEID,$WEID,$editable_reply_exists_id,'autorep'));
							else{
								foreach($reply as $ryinfo){
									$contentStrType = $ryinfo->arply_type;
									$autoId=$ryinfo->arplymesg_id;
								}						
								$resultStr=$this->responseMessage($contentStrType,$autoId,$obj,$WEID,$GWEID);
							}
						}else{
							$resultStr = $this->responseText($obj, "对不起，首次关注回复权限没有开启");
						}
						echo $resultStr;						
						
						break;						
					}					
				case "unsubscribe":
					//公众号删除关注记录
					$delsub=wechat_del_user_weid_group($obj->FromUserName,$GWEID);//20140624 janeen update
					wechat_delete_fans_wid($obj->FromUserName,$wid);
					if($delsub===false){
						echo "失败";
					}
					break;
				case "CLICK":
					if(!$isP){
						$contentStr = "没注册";
						$resultStr = $this->responseText($obj, $contentStr);
						echo $resultStr;
						break;					
					}else{

						/**
						function:get the gweid(shared)
						*/
						$info=getWechatGroupInfo_gweid_all($GWEID);
						foreach($info as $weidinfo){
							$shared_flag=$weidinfo->shared_flag;
							$user_id=$weidinfo->user_id;
							if($shared_flag==1){
								$weinfo=getWechatGroupActiveInfo_all($user_id,2);
								foreach($weinfo as $gweids){
									$GWEID=$gweids->GWEID;
								}
							}
						}


					
						if($selCheck['wechatfuncmenumanage']!=1){
							$isOpen=false;
							$meinf=null;
						}else{
							$isOpen=true;
							$eventKey=$obj->EventKey;
							$meinf=wechat_menu_public_gets($eventKey,$WEID);
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
						
						break;
					}
				default :
					$contentStr = "Unknow Event: ".$obj->Event;
					$resultStr = $this->responseText($obj, $contentStr);
					break;
			}
			
		}
		public function handleRegisText($obj,$wid,$selCheck){
			$fromUsername = $obj->FromUserName;
			$toUsername = $obj->ToUserName;
			$content = trim($obj->Content);
			if($content == null) {
				$resultStr = $this->responseText($obj, "您输入的为空");
				echo $resultStr;
			}else{
				$winfo=wechat_usechat_get($wid);
				$ucinfo=null;
				foreach($winfo as $info){
					$WEID=$info->WEID;
					$GWEID=$info->GWEID;
					
					/**
					function:get the gweid(shared)
					*//*
					$info=getWechatGroupInfo_gweid_all($GWEID);
					foreach($info as $weidinfo){
						$shared_flag=$weidinfo->shared_flag;
						$user_id=$weidinfo->user_id;
						if($shared_flag==1){
							$weinfo=getWechatGroupActiveInfo_all($user_id,2);
							foreach($weinfo as $gweids){
								$GWEID=$gweids->GWEID;
							}
						}
					}*/
					
					
					$ucinfo=wechat_usechat_exist_ver_group($content,$WEID,$GWEID);
					if(!empty($ucinfo))
					break;
				}
				
				if($ucinfo==null){
						$resultStr = $this->responseText($obj, "您输入的验证码有误，请重新输入");
						echo $resultStr;
				}else{
						wechat_insert_user_weid_gweid_group($fromUsername,$WEID,$GWEID);					
						
						
						/**
						function:get the gweid(shared)
						*/
						$info=getWechatGroupInfo_gweid_all($GWEID);
						foreach($info as $weidinfo){
							$shared_flag=$weidinfo->shared_flag;
							$user_id=$weidinfo->user_id;
							if($shared_flag==1){
								$weinfo=getWechatGroupActiveInfo_all($user_id,2);
								foreach($weinfo as $gweids){
									$GWEID=$gweids->GWEID;
								}
							}
						}
						if(!empty($WEID)){
							$usechats = wechat_info_get($WEID);
							foreach($usechats as $usechat){
								$wechat_fanscount=$usechat->wechat_fanscount;
							}
							$newcount=$wechat_fanscount+1;
							wechat_update_usechat($newcount,$WEID);
							wechat_update_fans($newcount,$fromUsername,$WEID,$GWEID,$wid);
						}
						
						
						
						//获取首次关注
						//$reply=wechat_mess_kw_get('subscribe',$WEID);
						$result = web_user_display_index_groupnew_forsel($GWEID);
						$selCheck=array();
						foreach($result as $initfunc){
							if($selCheck[$initfunc->func_name] == 0)
							$selCheck[$initfunc->func_name] = $initfunc->status;
						}	
						if($selCheck['wechatfuncfirstconcern']!=1){
							$isOpen=false;
							$reply=null;
						}else{
							$isOpen=true;
							//$reply=wechat_mess_kw_get('subscribe',$WEID);
							$reply=wechat_mess_kw_get_group('subscribe',$GWEID);//20140624 janeen update
						}
						if($isOpen){
							$editable_reply_exists_id = editable_reply_exists($obj->FromUserName,$GWEID,$WEID,'autorep');
							if($editable_reply_exists_id)
								$resultStr = $this->responseText($obj, editable_reply_content($obj->FromUserName,$GWEID,$WEID,$editable_reply_exists_id,'autorep'));
							else{
								foreach($reply as $ryinfo){
									$contentStrType = $ryinfo->arply_type;
									$autoId=$ryinfo->arplymesg_id;
								}
								$resultStr=$this->responseMessage($contentStrType,$autoId,$obj,$WEID,$GWEID);
							}
						}else{
							$resultStr = $this->responseText($obj, "对不起，首次关注回复权限没有开启");
						}												
						//获取首次关注结束
						echo $resultStr;						
				}						
			}
		}						
	
		public function responseExitMessage($prep,$poObj,$exreplay,$wid,$excode){
			//要提示的内容如果是文本
			$subreturn='';
			if($prep == "text"){						
				$resultStr = $this->responseText($poObj,$exreplay);
				$subreturn .= $resultStr;
			}else if($prep == "business"){
				$i=1;
				$hint="";
				$replay = "请输入您要关注的商家验证码：\n";						
				
				$usechatinfo=wechat_usechat_getflg($wid);
				
				foreach($usechatinfo as $uc){
					$vericode=$uc->vericode;
					$exitc=$uc->busi_exit;
					$hint.="商家".$i++."验证码是".$vericode.",退出请输入'".$exitc."'\n";
				}	
				
				$replay .=$hint;
				$resultStr = $this->responseText($poObj,$replay);
				$subreturn .=$resultStr;
			}else{
				$resultStr = $this->responseText($poObj,"我们正在维护，请稍后重试");
				$subreturn .=$resultStr;
			}
			return $subreturn;
		}
		public function responseMessage($contentStrType,$autoId,$obj,$WEID,$GWEID){
			//自定义回复的是文本					
			if($contentStrType=="weChat_text"){						
				$msgType="text";
				$me_text=wechat_text_get($autoId);
				//回复触发
				foreach($me_text as $text){
					$contentStr = $text->text_content;
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