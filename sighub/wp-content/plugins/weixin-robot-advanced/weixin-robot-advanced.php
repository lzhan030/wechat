<?php
/*
Plugin Name: 微信机器人高级版
Plugin URI: http://wpjam.net/item/weixin-robot-advanced/
Description: 微信机器人的主要功能就是能够将你的公众账号和你的 WordPress 博客联系起来，搜索和用户发送信息匹配的日志，并自动回复用户，让你使用微信进行营销事半功倍。
Version: 3.2
Author: Denis
Author URI: http://blog.wpjam.com/
*/

define('WEIXIN_ROBOT_PLUGIN_URL', plugins_url('', __FILE__));
define('WEIXIN_ROBOT_PLUGIN_DIR', WP_PLUGIN_DIR.'/'. dirname(plugin_basename(__FILE__)));
define('WEIXIN_ROBOT_PLUGIN_FILE',  __FILE__);

add_action('parse_request', 'wpjam_weixin_robot_redirect', 4);
function wpjam_weixin_robot_redirect($wp){
	if(isset($_GET['weixin']) ){
		global $wechatObj;
		if(!isset($wechatObj)){
			$wechatObj = new wechatCallback();
			$wechatObj->valid();
			exit;
		}
	}
}

class wechatCallback {
	private $postObj		= '';
	private $fromUsername	= '';
	private $toUsername		= '';
	private $response		= '';

	public function valid(){

		if(isset($_GET['debug'])){
			$this->checkSignature();
			$this->responseMsg();
		}else{
			if($this->checkSignature()){
				if(isset($_GET["echostr"])){
					$echoStr = $_GET["echostr"];
					echo $echoStr;					
				}
				$this->responseMsg();
				exit;
			}
		}
	}

	public function responseMsg(){
		//get post data, May be due to the different environments
		$postStr = (isset($GLOBALS["HTTP_RAW_POST_DATA"]))?$GLOBALS["HTTP_RAW_POST_DATA"]:'';

		if (isset($_GET['debug']) || !empty($postStr)){	
			if(isset($_GET['debug'])){
				$this->fromUsername = $this->toUsername = '';
				$keyword = strtolower(trim($_GET['t']));
			}else{
				$postObj		= simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

				$this->postObj		= $postObj;

				$this->fromUsername	= (string)$postObj->FromUserName;
				$this->toUsername	= (string)$postObj->ToUserName;

				$msgType = strtolower(trim($postObj->MsgType));

				if($msgType == 'event'){
					$event = strtolower(trim($postObj->Event));

					if($event == 'subscribe' || $event == 'unsubscribe'){ // 订阅和取消订阅时间
						$keyword = $event;
					}elseif($event == 'click'){	//点击事件
						$keyword = strtolower(trim($postObj->EventKey));
					}elseif($event == 'view'){	//查看网页事件，估计也进不来。
						exit;
					}
				}elseif($msgType == 'text'){
					$keyword = strtolower(trim($postObj->Content));
				}else{
					$keyword = '['.$msgType.']';
				}
			}

			if(empty( $keyword ) || strpos($keyword, '#') !== false ) {
				echo "";
				exit;
			}

			$pre = apply_filters('weixin_custom_keyword', false, $keyword);

			if($pre == false){ // 如果不是自定义关键字，就直接搜索回复，其他各种情况都移到 hook.php 保持简洁和简单
				$this->query($keyword);
			}

			do_action('weixin_robot',$postObj,$this->response);
		}else {
			echo "";
		}
		exit;
	}

	public function query($keyword=''){

		$weixin_count = weixin_robot_get_setting('weixin_count');

		$weixin_query_array = array(
			's' 					=> $keyword, 
			'posts_per_page'		=> $weixin_count , 
			'post_status' 			=> 'publish', 
			'ignore_sticky_posts'	=> 1 
		);

		$weixin_query_array = apply_filters('weixin_query',$weixin_query_array); 

		if(empty($this->response)){
			if(isset($weixin_query_array['s'])){
				$this->response = 'query';
			}elseif(isset($weixin_query_array['cat'])){
				$this->response = 'cat';
			}elseif(isset($weixin_query_array['tag_id'])){
				$this->response = 'tag';
			}
		}

		$weixin_robot_query = new WP_Query($weixin_query_array);

		$items = '';

		$counter = 0;

		if($weixin_robot_query->have_posts()){
			while ($weixin_robot_query->have_posts()) {
				$weixin_robot_query->the_post();

				global $post;

				$title	= apply_filters('weixin_title', get_the_title()); 
				$excerpt= apply_filters('weixin_description', get_post_excerpt( $post,apply_filters( 'weixin_description_length', 150 ) ) );
				$url	= apply_filters('weixin_url', get_permalink());

				if($counter == 0){
					$thumb = get_post_weixin_thumb($post, array(640,320));
				}else{
					$thumb = get_post_weixin_thumb($post, array(80,80));
				}

				$items = $items . $this->get_item($title, $excerpt, $thumb, $url);
				$counter ++;
			}
		}

		$articleCount = count($weixin_robot_query->posts);
		if($articleCount > $weixin_count) $articleCount = $weixin_count;

		if($articleCount){
			echo sprintf($this->get_picTpl(),$articleCount,$items);
		}else{
			$weixin_not_found = weixin_robot_get_setting('weixin_not_found');
			$weixin_not_found = str_replace('[keyword]', '【'.$keyword.'】', $weixin_not_found);
			if($weixin_not_found){
				echo sprintf($this->get_textTpl(), $weixin_not_found);
			}
			$this->response = 'not-found';
		}
	}

	public function get_item($title, $description, $picUrl, $url){
		if(!$description) $description = $title;

		return
		'
		<item>
			<Title><![CDATA['.html_entity_decode($title, ENT_QUOTES, "utf-8" ).']]></Title>
			<Description><![CDATA['.html_entity_decode($description, ENT_QUOTES, "utf-8" ).']]></Description>
			<PicUrl><![CDATA['.$picUrl.']]></PicUrl>
			<Url><![CDATA['.$url.']]></Url>
		</item>
		';
	}

	public function get_fromUsername(){ // 微信的 USER OpenID
		return $this->fromUsername;
	}

	public function get_response(){
		return $this->response;
	}

	public function get_textTpl(){
		return "<xml>
				<ToUserName><![CDATA[".$this->fromUsername."]]></ToUserName>
				<FromUserName><![CDATA[".$this->toUsername."]]></FromUserName>
				<CreateTime>".time()."</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
			</xml>
		";
	}

	public function get_picTpl(){
		return "
			<xml>
				<ToUserName><![CDATA[".$this->fromUsername."]]></ToUserName>
				<FromUserName><![CDATA[".$this->toUsername."]]></FromUserName>
				<CreateTime>".time()."</CreateTime>
				<MsgType><![CDATA[news]]></MsgType>
				<Content><![CDATA[]]></Content>
				<ArticleCount>%d</ArticleCount>
				<Articles>
				%s
				</Articles>
				<FuncFlag>1</FuncFlag>
			</xml>
		";
	}

	public function get_postObj(){
		return $this->postObj;
	}

	public function set_response($response){
		$this->response = $response;
	}

	private function checkSignature(){
		$signature	= isset($_GET["signature"])?$_GET["signature"]:'';
		$timestamp	= isset($_GET["timestamp"])?$_GET["timestamp"]:'';
		$nonce 		= isset($_GET["nonce"])?$_GET["nonce"]:'';	
				
		$weixin_token = weixin_robot_get_setting('weixin_token');
		if(isset($_GET['debug'])){
			echo 'WEIXIN_TOKEN：'.$weixin_token."\n";
		}
		$tmpArr = array($weixin_token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

include(WEIXIN_ROBOT_PLUGIN_DIR.'/weixin-robot-hook.php');			// 自定义接口
include(WEIXIN_ROBOT_PLUGIN_DIR.'/weixin-robot-functions.php');		// 常用函数
include(WEIXIN_ROBOT_PLUGIN_DIR.'/weixin-robot-options.php');		// 后台选项
include(WEIXIN_ROBOT_PLUGIN_DIR.'/weixin-robot-custom-reply.php');	// 自定义回复
include(WEIXIN_ROBOT_PLUGIN_DIR.'/weixin-robot-custom-menu.php');	// 自定义菜单	

if(!function_exists('wpjam_net_check_domain')){
	include(WEIXIN_ROBOT_PLUGIN_DIR.'/include/wpjam-net-api.php');	// WPJAM 应用商城接口
}

$weixin_disable_stats = weixin_robot_get_setting('weixin_disable_stats');
if(empty($weixin_disable_stats)){
	include(WEIXIN_ROBOT_PLUGIN_DIR.'/weixin-robot-stats.php');		// 数据统计
}