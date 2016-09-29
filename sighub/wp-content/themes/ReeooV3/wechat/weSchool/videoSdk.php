<?php
define('KU6VMS_KEY','4VW4DVTC9X3BDZ7WI87KJCLSA');
define('KU6VMS_SECRET','ZWI4NDhjODAtMDZmOC00Y2NiLWI2MzItZmVlNjY4MTM5M2Rl');
class Ku6VmsSdk{
	public $host = 'api.ku6vms.com';
	public $requesUri;
	public $AccessKey;
	public $query_str = '';
	public $AccessSecret;
	public $headers = array();
	public $params = array();
	public $method = 'POST';
	public $postBody = array();

	
	public function Ku6VmsSdk( $AccessKey = KU6VMS_KEY , $AccessSecret = KU6VMS_SECRET ){
		$this -> AccessKey = $AccessKey;
		$this -> AccessSecret = $AccessSecret;
		$this -> params['SndaAccessKeyId'] = $AccessKey;
		//TODO should modify when move out of Wordpress
		$this -> params['Timestamp'] = date('Y-m-d H:i:s');
		$this -> params['Expires'] = date("Y-m-d H:i:s",strtotime("+7 day"));
	}
	public function add_param($data){
		$this -> params = array_merge($this -> params, $data);
	}
	
	public function sign(){
		$this -> params['SignatureMethod'] = 'HmacSHA1';
		$this -> params['SignatureVersion'] = '1';
		
		$params_str = '';
		if(!empty($this -> params)) {
			ksort($this -> params, SORT_STRING);
			$params_str = http_build_query($this -> params);
			$this -> query_str .= $params_str;
		}
		$auth = "{$this -> method}\n"
		."{$this -> host}\n"
		."{$this -> requesUri}\n"
		.urldecode($params_str);
		return base64_encode(hash_hmac('sha1', $auth, $this->AccessSecret, true));
	}
	public function execRequest($target = false,$sign = true,$header = array()){
		if($sign)
			$this -> params['Signature'] = $this -> sign();
			
			$params_str = '';
			$this -> query_str = '';
			if(!empty($this -> params)) {
				ksort($this -> params, SORT_STRING);
				$params_str = http_build_query($this -> params);
				if(false === strstr($this -> requesUri, '?')) {
					$this -> query_str .= "?";
				} else {
					$this -> query_str .= "&";
				}
			   $this -> query_str .= $params_str;
			}
		$conn = curl_init();
		if ($conn) {
			$url =  $target?$target:"http://{$this->host}{$this -> requesUri}{$this -> query_str}";
            //echo "\n***".$url."\n***\n";
			$this->set_header('Date', date('Y-m-d H:i:s'));
			$this->set_header('Accept', 'application/xml');
			$this->set_header('Expect', '');
			if($header)
				foreach($header as $key => $value)
					$this->set_header($key , $value );
			
			curl_setopt_array($conn, array(
				CURLOPT_URL             => $url,
				CURLOPT_CUSTOMREQUEST   => $this -> method,
				CURLOPT_CONNECTTIMEOUT  => 50,
				CURLOPT_FOLLOWLOCATION  => true,
				CURLOPT_HEADER          => true,
				CURLOPT_NOBODY          => 'HEAD' === $this -> method,
				CURLOPT_RETURNTRANSFER  => true,
				CURLOPT_BINARYTRANSFER  => true,
				CURLOPT_HTTPHEADER      => $this->headers
				));
			if( $this -> method == 'POST' && $this -> postBody)
				curl_setopt($conn, CURLOPT_POSTFIELDS, $this -> postBody); 
			//print_r($this->headers);
			$response = curl_exec($conn);
			$response_code = curl_getinfo($conn, CURLINFO_HTTP_CODE);
			$response_length = curl_getinfo($conn, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            //var_dump($response_code);
            //var_dump($response_length);
            if(FALSE === $response){
				var_dump(curl_error($conn).curl_errno($conn));
                return false;
            }
            else{
                list($header, $body) = explode("\r\n\r\n", $response, 2);
				//echo $response;
                return $body;
            }
                
			// Check for errors and display the error message
        }
	}
	public function createvideo($video){
		$this -> requesUri = '/video';
		$this -> add_param($video);
        //var_dump($this -> execRequest());
        $response = simplexml_load_string($this -> execRequest());
        return (array)$response ;
        
	}
    public function createVideoUploadUrl($url,$sid,$filesize,$ext,$cfrom){
       	$this -> params = array();
        
        $this -> params['cfrom'] = $cfrom;
        $this -> params['ext'] = $ext;
        $this -> params['filesize'] = $filesize;
        $this -> params['sid'] = $sid; 
        $this -> params['SignatureMethod'] = 'HmacSHA1';
        $this -> params['SndaAccessKeyId'] = $this->AccessKey;
        
        $parseurl = parse_url($url);
		$host = $parseurl['host'] ;
        $requesUri = $parseurl['path'] ;
		$params_str = '';
		if(!empty($this -> params)) {
            //print_r($this -> params);
            ksort($this -> params, SORT_STRING);
            //print_r($this -> params);
			$params_str = http_build_query($this -> params);
		}
        //echo urldecode($params_str);
        $auth = "POST\n"
		."{$host}\n"
		."{$requesUri}\n"
		.urldecode($params_str);
		$sign = base64_encode(hash_hmac('sha1', $auth, $this->AccessSecret, true));
        $this -> params['Signature'] = $sign;
        $params_str = http_build_query($this -> params);
        if(false === strstr($url, '?')) {
            $url .= "?";
        } else {
            $url .= "&";
        }
        $url .= $params_str;
        return $url;
		
    }
	public function getVideoPlayCode($vid,$programId){
		$this -> host = 'api.ku6vms.com';
		$this -> requesUri = "/video/{$vid}/publication";
		$this -> method = 'GET';
		$this -> add_param(array('ProgramId' => $programId ));
		$response = simplexml_load_string($this -> execRequest());
        return $response ;
		
	}
	public function VideoUrlUpload($filepath,$url){
		$this -> params = array();
		$this -> postBody['file'] = '@'.$filepath;
		$response = json_decode($this -> execRequest($url,false, array('Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8')),true);
        return $response ;
		
	}
	
	public function set_header($field, $value=null) {
		$field = trim($field);
		$value = trim($value);

		if (empty($field)) {
			return $this;
		}

		if (strpos($field, ':')) {  //$field can be like "key1:value1\nkey2:value2\n..",$value will unused in this situation 
			foreach (explode("\n", $field) as $item) {
				$key = substr($item, 0, strpos($item, ':'));

				$this->headers[$key] = $item;
			}
		} else {
			$this->headers[$field] = "{$field}: {$value}";
		}

		return $this;
	}
}
