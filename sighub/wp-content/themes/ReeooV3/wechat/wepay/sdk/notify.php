<?php

	include 'weixin.php';
	
	$weixin = new Weixin();
	
	$xml = $this->weixin->getXmlArray();
	$openid = $xml['openid'];
	
	$out_trade_no = $_GET['out_trade_no'];
    $trade_no = $_GET['transaction_id'];
    $trade_status = $_GET['trade_state'];
    
    $isweixin = $weixin->verifyNotify(@file_get_contents('php://input'));
	if (  $isweixin ){
		if( $trade_status == 0 ) { //支付成功
			//业务处理
			echo "success";
		}else{			
    		echo "fail";
    	}
	}else{
		echo "不是微信回调";
	}
	