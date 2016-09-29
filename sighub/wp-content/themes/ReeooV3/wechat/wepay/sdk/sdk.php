<?php

/**
 * 
 * 微信api基础类
 *
 */
 
class WeixinPay{
	
	public $app_id='';
    public $app_secret='';
	public $appsecret='';
    public $mch_id = '';
	public $apiclient_cert = '';
    public $apiclient_key = '';
	
	public function __construct($gweid)
	{
		global $wpdb;
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		
		$global_vars = $wpdb -> get_row($wpdb -> prepare("SELECT * FROM {$wpdb -> prefix}shopping_global WHERE gweid=%s",$gweid),ARRAY_A);
        $this -> app_id = $global_vars['appid'];
		$this -> app_secret = $global_vars['appkey'];
		$this -> appsecret = $global_vars['appsecret'];
		$this -> mch_id = $global_vars['mch_id'];
		$this -> apiclient_cert = $upload_dir.$global_vars['certificate_url'];
		$this -> apiclient_key = $upload_dir.$global_vars['certificate1_url'];
				
	}
	public function isConfigAvailable(){
		if( !empty($this -> app_id) && !empty($this -> app_secret) && !empty($this -> appsecret) && !empty($this -> mch_id))
			return true;
		return false;
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
		
		if($cert){
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT,$this -> apiclient_cert);
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY,$this -> apiclient_key);
		}
		
		$response_text = curl_exec($ch);//运行curl
		$error_code = curl_errno($ch);
		//error_log("Request:\n".$data."\nResponse:\n".$response_text."\nError_Code:\n".$error_code);
		curl_close($ch);
		if($error_code>0)
			return FALSE;
		global $wpdb;
		$wpdb -> insert("wp_test",array('text' => $data));
		$wpdb -> insert("wp_test",array('text' => $response_text));
		$wpdb -> insert("wp_test",array('text' => $error_code));
		return $response_text;
    }
    
    
    
   
    /**
 	* 除去数组中的空值和签名参数
 	* @param $para 签名参数组
 	* return 去掉空值与签名参数后的新签名参数组
 	*/
	
	public	function parafilter($para) {
		$para_filter = array();
		foreach ($para as $key => $val ) {
			if($key == "sign_method" || $key == "sign" ||$val == "")continue;
			else	$para_filter[$key] = $para[$key];
		}
		return $para_filter;
	}
	
	/**
	 * 对数组排序
 	* @param $para 排序前的数组
 	* return 排序后的数组
 	*/
	public function argsort($para) {
		ksort($para);
		reset($para);
		return $para;
	}
	
	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	 * @param $para 需要拼接的数组
	 * return 拼接完成以后的字符串
	 */
	public function createlinkstring($para) {
		$arg  = "";
		foreach ($para as $key => $val ) {
			$arg.= $key."=".$val."&";
		}
		//去掉最后一个&字符
		$arg = substr($arg,0,count($arg)-2);
		
		//如果存在转义字符，那么去掉转义
		if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
		
		return $arg;	

	}
	public function createlinkstring_strtolower($para) {
        $arg  = "";
        foreach ($para as $key => $val ) {
            $arg.=strtolower($key)."=".$val."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

        return $arg;    

    }
   
    /**
     * 创建sign
     * @return string
     */
    public function create_sign( $arr ){
        $para = $this->parafilter($arr);
		$para = $this->argsort($para);
		$signValue = $this->createlinkstring($para);
		$signValue = $signValue."&key=".$this->app_secret;
		$signValue = strtoupper(md5($signValue));	
        
		return $signValue;
    }
	function create_sha1sign($arr){
		$para = $this->parafilter($arr);
		$para = $this->argsort($para);
		$signValue = $this->createlinkstring_strtolower($para);
		$signValue = sha1($signValue);	
        return $signValue;
	}
	
	/*
    * 生成随机数
    */
    function create_noncestr( $length = 16 ) {  
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
        $str ="";  
        for ( $i = 0; $i < $length; $i++ )  {  
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
            //$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
        }  
        return $str;  
    }
    
	/**
     * 获取用户基本信息
     * @return array
     */
    public function user_info($openid){
    	$ret = $this->open("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token()."&openid=$openid&lang=zh_CN");
    	if ( in_array($ret['errcode'],array(40001,40002,42001)) ){
        	$this->access_token(false);
         	return $this->user_info($openid);
        }
        return $ret;
    }
	
    
    /**
     * 标记客户的投诉处理状态
     * @return bool
     */
    public function payfeedback_update($openid,$feedbackid){
    	 $url = "https://api.weixin.qq.com/payfeedback/update?access_token=".$this->access_token()."&openid=".$openid."&feedbackid=".$feedbackid;
         $ret = $this->open($url);
         if ( in_array($ret['errcode'],array(40001,40002,42001)) ){
         	$this->access_token(false);
         	return $this->payfeedback_update($openid,$feedbackid);
         }
         return $ret;
    }
    
    /**
     * 发货通知
     *  
     * openid					购买用户的 OpenId，这个已经放在最终支付结果通知的 PostData 里了 
     * transid					交易单号
     * out_trade_no				第三方订单号
     * deliver_timestamp		发货时间戳
     * deliver_status			发货状态	1:成功 0:失败
     * deliver_msg				发货状态信息	
     *
     */
    public function delivernotify($openid,$transid,$out_trade_no,$deliver_status=1,$deliver_msg='ok'){
    	$post = array();
    	$post['appid'] = $this->app_id;
    	$post['appkey'] = $this->paySignKey;
    	$post['openid'] = $openid;
    	$post['transid'] = $transid;
    	$post['out_trade_no'] = $out_trade_no;
    	$post['deliver_timestamp'] = time();
    	$post['deliver_status'] = $deliver_status;
    	$post['deliver_msg'] = $deliver_msg;
    	
    	$post['app_signature'] = $this->create_app_signature($post);
    	$post['sign_method'] = "SHA1";
    	
    	$data = json_encode($post);
    	
    	$url = 'https://api.weixin.qq.com/pay/delivernotify?access_token=' . $this->access_token();
	    $ret = $this->post($url,$data);
	    if ( in_array($ret['errcode'],array(40001,40002,42001)) ){
         	$this->access_token(false);
         	return $this->delivernotify($openid,$transid,$out_trade_no,$deliver_status,$deliver_msg);
        }
	    return $ret;
    }
    
    
    /**
     * 从xml中获取数组
     * @return array
     */
    public function getXmlArray($xml = FALSE) {
		if(!$xml)
			$xml = @file_get_contents('php://input');
		
		if ($xml) {
			$xmlObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (! is_object($xmlObj)) {
                return false;
            }
            $array = json_decode(json_encode($xmlObj), true); // xml对象转数组
            return array_change_key_case($array, CASE_LOWER); // 所有键小写
        } else {
            return false;
        }        
    }
	
	/**
     * 数组转化为Xml
     * @return string
     */
	function arrayToXml($arr)
    {
        $xml = '<xml>';
        foreach ($arr as $key=>$val)
        {
        	 if (is_numeric($val))
        	 {
        	 	$xml.="<".$key.">".$val."</".$key.">"; 

        	 }
        	 else
        	 	$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.="</xml>";
        return $xml; 
    }
    
    
    /**
	 * 验证服务器通知
	 * @param array $data
	 * @return array
	 */
	public function verifyNotify($post) {
		if(!is_array($post))
			$postArray = $this -> getXmlArray($post);
        $sign = $postArray['sign'];
        $para = $this->parafilter($post);
		$para = $this->argsort($para); 
		$signValue = $this->createlinkstring($para);
		$signValue = $signValue."&key=".$this->partnerKey;
		$signValue = strtoupper(md5($signValue));
		if ( $sign == $signValue ){
			return true;	
		}else{
			return false;
		}
		
	}
	
	
	 /**
	 * 是否支持微信支付
	 * @return bool
	 */
	public function is_show_pay($agent) {
		$ag1  = strstr($agent,"MicroMessenger");
		$ag2 = explode("/",$ag1);
		$ver = floatval($ag2[1]);
		if ( $ver < 5.0 || empty($aid) ){
			return false;
    	}else{
    		return true;
    	}
	}


	/**
    * 生成原生支付（带Product_ID）URL
    * @return string
    */
	
    public function create_native_product_url($product_id){
        $parameter = array();
        $parameter['appid'] = $this -> app_id;
        $parameter['mch_id'] = $this -> mch_id;
        $parameter['time_stamp'] = time();
        $parameter['nonce_str'] = $this -> create_noncestr();
        $parameter['product_id'] = $product_id;
        $parameter['sign'] = $this -> create_sign($parameter);
        $query_str = http_build_query($parameter);
        return 'weixin://wxpay/bizpayurl?'.$query_str;

    }


	/**
	* 统一下单
	* @return array('prepay_id','code_url')
	*/
	
	public function create_order($order, $jsapi = true){
		
		$order['appid'] = $this->app_id;
        $order['mch_id'] = $this->mch_id;
        $order['nonce_str'] = $this -> create_noncestr();
        if(!isset($order['trade_type'] ))
            $order['trade_type'] = $jsapi?'JSAPI':'NATIVE';
		$order['sign'] = $this -> create_sign($order);
        $data = $this -> arrayToXml($order);
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$ret = $this->post($url,$data);
		if($ret===FALSE){
			return false;
		}else{
			$returnArray = $this -> getXmlArray($ret);
			return $returnArray;
		}
	}


	 /**
     * 订单查询
     * @return array
     */
    public function order_query($out_trade_no){
    	$post = array();
    	$post['appid'] = $this->app_id;
        $post['mch_id'] = $this->mch_id;
        $post['out_trade_no'] = $out_trade_no;
        $post['nonce_str'] = $this -> create_noncestr();
    	$post['sign'] = $this -> create_sign($post);
		$data = $this -> arrayToXml($post);
		
    	$url = 'https://api.mch.weixin.qq.com/pay/orderquery';
	    $ret = $this->post($url,$data);
		if($ret===FALSE){
			return false;
		}else{
			$returnArray = $this -> getXmlArray($ret);
			return $returnArray;
		}
		
    }
    
    /**
    * 创建退款
    * @return array
    */
    public function create_refund($refund){
		global $wpdb;
        $refund['appid'] = $this -> app_id;
        $refund['mch_id'] = $this -> mch_id;
        $refund['nonce_str'] = $this -> create_noncestr();
        $refund['op_user_id'] = $this -> mch_id;
        $refund['sign'] = $this->create_sign($refund);;
        $reqBody = $this -> arrayToXml($refund);
        $response = $this -> post("https://api.mch.weixin.qq.com/secapi/pay/refund",$reqBody,true);
		$wpdb->insert('wp_wechat_website_statistics',array(
			'site_id'=>00001,
			'site_link'=>$response,
			'time'=>date('Y-m-d H:i:s'),
			'site_ip'=>00001,
		));
		$response = $this -> getXmlArray($response);
        return $response;
    }

     /**
     * 退款查询
     * @return array
     */
    public function refund_query($out_trade_no){
        $post = array();
        $post['appid'] = $this->app_id;
        $post['mch_id'] = $this->mch_id;
        $post['out_trade_no'] = $out_trade_no;
        $post['nonce_str'] = $this -> create_noncestr();
        $post['sign'] = $this -> create_sign($post);

        $data = $this -> arrayToXml($post);
        $url = 'https://api.mch.weixin.qq.com/pay/refundquery';
        $ret = $this->post($url,$data);
        return $this -> getXmlArray($ret);
    }

    /**
     * 对账单
     * @return str
     */
    public function download_bill($date, $type = 'ALL'){
	    global $wpdb;
        if(!in_array($type,array('ALL','SUCCESS','REFUND')))
            $type = 'ALL';
        $post = array();
        $post['appid'] = $this->app_id;
        $post['mch_id'] = $this->mch_id;
        $post['nonce_str'] = $this -> create_noncestr();
        $post['bill_date'] = $date;
        $post['bill_type'] = $type;

        $post['sign'] = $this -> create_sign($post);
        $data = $this -> arrayToXml($post);
		
		/* 测试微信返回
		$testpost = array();
        $testpost['appid'] = 'wx0ab58dbf38931a6b';
        $testpost['mch_id'] = 10013548;
		$testpost['nonce_str'] = 'apm6EHQ1uhLW0P5C';
        $testpost['bill_date'] = '20141105';
		//$testpost['bill_type'] = 'ALL';
        $testpost['bill_type'] = '1';
		$testpost['sign'] = $this -> create_sign($testpost);
		$data = $this -> arrayToXml($testpost); */
		/* $data = '<xml>
			<appid><![CDATA[wx0ab58dbf38931a6b]]></appid>
			<mch_id>10013548</mch_id>
			<nonce_str><![CDATA[apm6EHQ1uhLW0P5C]]></nonce_str>
			<bill_date>19880101</bill_date>
			<bill_type><![CDATA[ALL]]></bill_type>
			<sign><![CDATA[87135E2ABAB95225130BEB5758A0520F]]></sign>
			</xml>
			'; */
		
        //$wpdb->insert( 'wp_test', array('text' => $data), array('%s') ) ;
        $url = 'https://api.mch.weixin.qq.com/pay/downloadbill';
        $ret = $this->post($url,$data);
        return $ret;

    }
	function https_request($url){	
		$ch = curl_init();	
		curl_setopt($ch,CURLOPT_URL,$url);	
		curl_setopt($ch,CURLOPT_HEADER,0);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );	
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);	
		$res = curl_exec($ch);	
		if (curl_errno($ch)) {
			return 'ERROR '.curl_error($ch);
		}	
		curl_close($ch);	
		return $res;
	}
	public function isoauth2_base($gweid){
	    //return true;
		//通过gweid拿到appid和secret
		$reurl=urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$appid=$this -> app_id;
		$secret=$this -> appsecret;
		$rurl=home_url().'/wp-content/themes/ReeooV3/wechat/wepay/sdk/oauth2/oauth_userinfo_base.php?appid='.$appid.'&secret='.$secret.'&gweid='.$gweid.'&reurl='.$reurl;
		$redriect_url=urlencode($rurl);
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redriect_url.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
		header("Location:".$url);
	}
	//通过openid获取用户信息接口
	public function userinfo($openid){
		//$access_token=$this->access_token_get();
		$appid=$this -> app_id;
		$appsecret=$this -> appsecret;
		$access_token_function = new Access_token();
		$access_token = $access_token_function -> get_access_token($appid,'appid',$appsecret);
	
    	$get_user_info_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid;
		$res=$this->https_request($get_user_info_url);
		$user_obj = json_decode($res,true);
		return $user_obj;
	}
    //获取token
	public function access_token_get(){
		$appid=$this -> app_id;
		$appsecret=$this -> appsecret;
		$get_token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
		$res=$this->https_request($get_token_url);
		$json_obj = json_decode($res,true);
		$access_token = $json_obj['access_token'];
		return $access_token;
	}
	//网页支付
    public function JSAPI($prepay_id){
        $post = array();
        $post['appId'] = $this->app_id;
		$timeStamp = time();
	    $post['timeStamp'] = "$timeStamp";
		$post['nonceStr'] = $this -> create_noncestr();
		$post['package'] =  "prepay_id={$prepay_id}";
	    $post['signType'] = "MD5";
		$post['paySign'] = $this -> create_sign($post);
		return $post;
		//return json_encode($post);
		
	}
	//共享收货地址
	public function addrsign($accesstoken){
		$timeStamp = (string)time();
		$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		//$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		$post = array();
        $post['appid'] = $this->app_id;
		$post['url'] = $url;
        $post['timestamp'] = $timeStamp;
        $post['noncestr'] = $this -> create_noncestr();
		$post['accesstoken'] = $accesstoken;
		$post['addrsign'] = $this -> create_sha1sign($post);
		return $post;
	}

    public function parse_rights($post){
        $postArray = $this -> getXmlArray($post);
        return $postArray;
    }
	 public function paid_notify($post){
        $postArray = $this -> getXmlArray($post);
        return $postArray;
    }
	
	
	public function build_native_response($prepay_id,$err_code_des = ""){
		$resp = array();
		$resp['return_code'] = "SUCCESS";
        $resp['appid'] = $this->app_id;
		$resp['mch_id'] = $this->mch_id;
		$resp['nonce_str'] = $this -> create_noncestr();
		$resp['prepay_id'] = $prepay_id;
		$resp['result_code'] = (empty($prepay_id)?'FAIL':'SUCCESS');
		if(empty($prepay_id))
			$resp['err_code_des'] = $err_code_des;
		$resp['sign'] = $this -> create_sign($resp);
		return $this -> arrayToXml($resp);
		
	}
}