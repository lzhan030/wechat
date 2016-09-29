<?php

defined('IN_IA') or exit('Access Denied');
require_once 'wp-content/themes/ReeooV3/wechat/wepay/sdk/sdk.php';
class WepaytestModuleSite extends ModuleSite {

	function doMobileRefundQueryTest(){
	
	
		echo <<<'EOT'
<xml>
	<return_code><![CDATA[SUCCESS]]></return_code>
	<return_msg><![CDATA[OK]]></return_msg>
	<appid><![CDATA[wx0ab58dbf38931a6b]]></appid>
	<mch_id><![CDATA[10013548]]></mch_id>
	<nonce_str><![CDATA[PUVhaE8kvnQjBxvr]]></nonce_str>
	<sign><![CDATA[EB3A7C840DAFFA0261C12F2A429938B3]]></sign>
	<result_code><![CDATA[SUCCESS]]></result_code>
	<prepay_id><![CDATA[wx20141105155939949b8238d50755508025]]></prepay_id>
	<trade_type><![CDATA[NATIVE]]></trade_type>
	<code_url><![CDATA[weixin://wxpay/bizpayurl?sr=EEEgdxE]]></code_url>
</xml>
EOT;
	}
	function doMobileCreateOrderTest(){
	
	
		echo <<<'EOT'
<xml>
    <return_code>
        <![CDATA[FAIL]]>
    </return_code>
    <return_msg>
        <![CDATA[签名错误]]>
    </return_msg>
</xml>

EOT;
	}
	
}