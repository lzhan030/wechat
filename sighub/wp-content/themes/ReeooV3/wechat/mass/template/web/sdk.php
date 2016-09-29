<?php

/**
 * 
 * 微信api基础类
 *
 */
 
class WeixinMass{
	
	public function __construct(){
		
	}
	
	public function post($api_url,$data,$cert = false){
		$ch = curl_init();//初始化curl
		curl_setopt($ch, CURLOPT_URL,$api_url);
		curl_setopt($ch, CURLOPT_TIMEOUT,15);
		curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		
		
		$response_text = curl_exec($ch);//运行curl
		$error_code = curl_errno($ch);
		//error_log("Request:\n".$data."\nResponse:\n".$response_text."\nError_Code:\n".$error_code);
		curl_close($ch);
		if($error_code>0)
			return FALSE;
		return $response_text;
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
	
	
	/*获取accesstoken
	*{"access_token":"ACCESS_TOKEN","expires_in":7200}
	*{"errcode":40013,"errmsg":"invalid appid"}
	*/
	function re_Token($APPID,$APPSECRET){
	
		$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
		$json=$this->https_request($TOKEN_URL);
		if($json===FALSE){
			return false;
		}else{
			$result=json_decode($json,true);
			return $result;
		}
	}
	
	/*获取关注者列表
	*{"total":2,"count":2,"data":{"openid":["","OPENID1","OPENID2"]},"next_openid":"NEXT_OPENID"}
	*{"errcode":40013,"errmsg":"invalid appid"}
	*/
	function userget($access_token,$next_openid){
		$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$access_token."&next_openid=".$next_openid;
		$ret = $this->https_request($url);
		if($ret===FALSE){
			return false;
		}else{
			$result=json_decode($ret,true);
			return $result;
		}
	}
	
	/*上传缩略图
	*{"type":"TYPE","media_id":"MEDIA_ID","created_at":123456789}
	*{"errcode":40004,"errmsg":"invalid media type"}
	*/
	function upload_thumb($access_token,$filepath){
		
		$type="thumb";
		$varname = 'update_file'; 
		//$name = '14289890151154966045.jpg';      
		//$type = 'image/jpeg'; 
		$name =end(explode('/',$filepath));//文件名
		$type = 'image'; //文件类型
		$key = "$varname\"; filename=\"$name\r\nContent-Type: $type\r\n";  
		$fields[$key] = file_get_contents($filepath);
	
		$url="http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=".$type;
		$ret=$this->post($url,$fields);
		if($ret===FALSE){
			return false;
		}else{
			$result=json_decode($ret,true);
			return $result;
		}
		
	}
	
	/*上传图文
	*{"type":"news","media_id":"CsEf3ldqkAYJAU6EJeIkStVDSvffUJ54vqbThMgplD-VJXXof6ctX5fI6-aYyUiQ","created_at":1391857799}
	*/
	function upload_news($access_token,$data){
		$url="https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=".$access_token;
		$ret = $this->post($url,$data);
		if($ret===FALSE){
			return false;
		}else{
			$result=json_decode($ret,true);
			return $result;
		}
	}
	
	
	
	/*群发
	*{"errcode":0,"errmsg":"send job submission success","msg_id":34182}
	*/
	function mass($access_token,$data){
		$url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=".$access_token;
		$ret = $this->post($url,$data);
		if($ret===FALSE){
			return false;
		}else{
			$result=json_decode($ret,true);
			return $result;
		}
	}
	
	
	/*查询群发状态
	*{"msg_id":201053012,"msg_status":"SEND_SUCCESS"}
	*/
	function mass_status($access_token,$msg_id){
		$url = "https://api.weixin.qq.com/cgi-bin/message/mass/get?access_token=".$access_token;
		$ret = $this->post($url,$msg_id);
		if($ret===FALSE){
			return false;
		}else{
			$result=json_decode($ret,true);
			return $result;
		}
	}
	
	/*预览接口
	*
	*/
	function mass_preview($access_token,$data){
		$url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=".$access_token;
		$ret = $this->post($url,$data);
		if($ret===FALSE){
			return false;
		}else{
			$result=json_decode($ret,true);
			return $result;
		}
	}
	
	

}