<?php

class JSSDK {
	  private $appId;
	  private $appSecret;
	  private $access_token;

	  public function __construct($appId, $appSecret, $access_token) {
	    $this->appId = $appId;
	    $this->appSecret = $appSecret;
	    $this->access_token = $access_token;
	  }

	  public function getSignPackage() {
	    $jsapiTicket = $this->getJsApiTicket();
	    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	    $timestamp = time();
	    $nonceStr = $this->createNonceStr();

	    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
	    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
	    $signature = sha1($string);
	    $signPackage = array(
	      "appId"     => $this->appId,
	      "nonceStr"  => $nonceStr,
	      "timestamp" => $timestamp,
	      "url"       => $url,
	      "signature" => $signature,
	      "rawString" => $string
	    );
	    return $signPackage; 
	  }

	  private function createNonceStr($length = 16) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $str = "";
	    for ($i = 0; $i < $length; $i++) {
	      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	    }
	    return $str;
	  }

	  private function getJsApiTicket() {
	  	global $wpdb;
	  	$token_row = $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}token WHERE `id`=%s AND `token_type`='JsApiTicket' AND `id_type`='appid'",$this -> appId),ARRAY_A);
		if($token_row && time() < strtotime($token_row['expire']) - 600 && !empty($token_row['token']))
			return $token_row['token'];
	    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token={$this -> access_token}";
		$res = json_decode($this->httpGet($url));
		$ticket = $res->ticket;
		$wpdb -> replace($wpdb->prefix.'token', array(
			'id' => $this->appId,
			'id_type' => 'appid',
			'token_type' => 'JsApiTicket',
			'token' => $ticket,
			'expire' => date("Y-m-d H:i:s", time() + 7150)
			));
	    return $ticket;
	  }

	  private function getAccessToken() {
	    return $this -> access_token;
	  }

	  private function httpGet($url) {
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	    curl_setopt($curl, CURLOPT_URL, $url);

	    $res = curl_exec($curl);
	    curl_close($curl);

	    return $res;
	  }
}


class Access_token{

	function get_access_token($id, $type = "gweid",$id2 = ""){
		global $wpdb;
		//get $app_id & $app_secret from DB
		$token_row = $wpdb -> get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}token WHERE `id`=%s AND `id_type`=%s AND `token_type`='access_token'",$id,$type),ARRAY_A);
		if($token_row && time() < strtotime($token_row['expire']) - 600 && !empty($token_row['token']))
			return $token_row['token'];
		switch ($type) {
			case 'gweid':
				$gweid_app_id_secret = $this->get_app_id_secret($gweid);
				$app_id = $gweid_app_id_secret['app_id'];
				$app_secret = $gweid_app_id_secret['app_secret'];
				break;
			case 'appid':
				$app_id = $id;
				$app_secret = $id2;
				break;
			default:
				break;
		}
			//{"access_token":"ACCESS_TOKEN","expires_in":7200}
		$access_token_and_expire = $this->get_access_token_from_wechat($app_id,$app_secret);

		$wpdb -> replace($wpdb->prefix.'token', array(
			'id' => $id,
			'id_type' => $type,
			'token_type' => 'access_token',
			'token' => $access_token_and_expire['access_token'],
			'expire' => date("Y-m-d H:i:s", time() + $access_token_and_expire['expires_in'])
			));
		return $access_token_and_expire['access_token'];

	}

	function get_access_token_from_wechat($APPID,$APPSECRET){
	
		$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
		$json = $this->https_request($TOKEN_URL);
		if($json===FALSE){
			return false;
		}else{
			$result=json_decode($json,true);
			return $result;
		}
	}

	function https_request($url){	
		$ch = curl_init();	
		curl_setopt($ch,CURLOPT_URL,$url);	
		curl_setopt($ch,CURLOPT_HEADER,0);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );	
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);	
		$res = curl_exec($ch);	
		if (curl_errno($ch)) {
			return FALSE;
		}	
		curl_close($ch);	
		return $res;
	}

	function get_app_id_secret($gweid){
		global $wpdb;
		$winfo= $wpdb->get_row( $wpdb -> prepare("SELECT u2.wechat_nikename,u2.wid,u2.menu_appId,u2.menu_appSc FROM {$wpdb->prefix}wechat_usechat u1,{$wpdb->prefix}wechats u2 where u1.wid=u2.wid and ((u2.wechat_type='pri_svc') or (u2.wechat_type='pri_sub' and u2.wechat_auth='1')) and u1.WEID !=0 and u1.GWEID=%s",intval($gweid)) ,ARRAY_A);
		if(empty($winfo))
			return FALSE;
		$app_id = $winfo['menu_appId'];
		$app_secret = $winfo['menu_appSc'];
		$wechat_nikename = $winfo['wechat_nikename'];
		$wid = $winfo['wid'];
		return array('app_id' => $winfo['menu_appId'] , 'app_secret' => $winfo['menu_appSc']);
	}

}

/*
array:
    title: $arr['title']
    desc: $arr['desc']
    link: $arr['link']
    imgUrl: $arr['imgUrl']
*/
function share_page_in_wechat($gweid,$arr){
	global $wpdb;
	//if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false)
	//	return;
	$access_token = new Access_token();
	$app_id = $access_token -> get_app_id_secret($gweid);
	if(empty($app_id))
		return;
	$app_secret = $app_id['app_secret'];
	$app_id = $app_id['app_id'];
	if(empty($app_id) || empty($app_secret))
		return;
	$access_token = new Access_token();
	$access_token = $access_token -> get_access_token($app_id,"appid",$app_secret);
	if(empty($access_token))
		return;
	$wechat_imgUrl = $wpdb->get_var( $wpdb -> prepare("SELECT u1.wechat_imgurl FROM {$wpdb->prefix}wechat_usechat u1,{$wpdb->prefix}wechats u2 where u1.wid=u2.wid and ((u2.wechat_type='pri_svc') or (u2.wechat_type='pri_sub' and u2.wechat_auth='1')) and u1.WEID !=0 and u1.GWEID=%s",intval($gweid)));
	$upload = wp_upload_dir();
	$base_url = $upload['baseurl'];
	$arr['imgUrl'] = $base_url.$wechat_imgUrl;
	$jssdk = new JSSDK($app_id, $app_secret, $access_token);
	$signPackage = $jssdk -> getSignPackage();
	if(empty($signPackage))
		return;
	include dirname(__FILE__) . '/share_js.php';

}