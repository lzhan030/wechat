<?php

/*
Plugin Name: OrangeWeChat
Plugin URI: http://1.wpforsae.sinaapp.com/
Description: OrangeWeChat平台微信插件测试
Version: 1.0
Author: Saxon
*/
define('WECHAT_PATH', dirname(__FILE__) . '/');
add_action('parse_request', 'Orange_wechat_redirect', 4);
if(is_admin()) {
    //微信插件admin界面
    require_once(WECHAT_PATH . 'wechat_admin/admin_page.php');
    //多模板admin界面
    //require_once(WECHAT_PATH . 'detector_admin/admin_page.php');
}
function Orange_wechat_redirect(){
    if(isset($_GET['weixin'])){
        //define your token
        define("TOKEN", "weixin");
        $WeChatDebug = new debugLogForWeChat();
        $WeChatDebug->Log("调试信息：");
        $wechatObj = new OrangeWeChatCallback();
        $wechatObj->valid();
        exit;
    }
}


 class debugLogForWeChat
{
    private $isDebug ;
    public function __construct(){
        $this->isDebug =false;
    }
    public function getDebug()
    {
        return $this->isDebug;
    }
    public function setDebug($bool)
    {
        $this->isDebug = $bool;
    }
    public function Log($text)
    {
        if($this->isDebug)
        {
            $this->LogToDb($text);
        }
    }
     public function LogToDb($text)
     {
             $text = get_option('Orange_WeChat_DebugLog').'<br>'.$text;
             update_option('Orange_WeChat_DebugLog',$text);
     }
     public function LogToResponse($text)
     {
         echo '<br>'.$text;
     }
}

class WeChatSendData
{
    private $textTpl;
    private $newsTpl;
    private $musicTpl;
    private $postObj;
    function __construct($postObj)
    {
        $this->postObj = $postObj;
        $this->valid();
    }
    private function  valid()
    {
        $time = time();
        //文本消息
        $this->textTpl = "<xml>
							<ToUserName><![CDATA[".$this->postObj->FromUserName."]]></ToUserName>
							<FromUserName><![CDATA[".$this->postObj->ToUserName."]]></FromUserName>
							<CreateTime>".$time."</CreateTime>
							<MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
        //图文消息
        $this->newsTpl = "<xml>
                             <ToUserName><![CDATA[".$this->postObj->FromUserName."]]></ToUserName>
                             <FromUserName><![CDATA[".$this->postObj->ToUserName."]]></FromUserName>
                             <CreateTime>".$time."</CreateTime>
                             <MsgType><![CDATA[news]]></MsgType>
                             <Content><![CDATA[]]></Content>
                             <ArticleCount>%d</ArticleCount>
                             <Articles>
                             %s
                             </Articles>
                             <FuncFlag>1</FuncFlag>
                            </xml>";
        //音乐消息
        $this->musicTpl = '';
    }
    public function sendTextMsg($text)
    {
        echo sprintf($this->textTpl,$text);
    }
    public function sendNewsMsgFromXml($count,$xml)
    {
        $WeChatDebug = new debugLogForWeChat();
        $str = sprintf($this->newsTpl,$count,$xml);
        $WeChatDebug->Log($str);
        echo $str;
    }
    public function sendNewsMsgFromUrl($count,$str)
    {
        $xml = '';
        foreach($str as $item)
        {
            $temp = '<item>
                    <Title><![CDATA['.$item[0].']]></Title>
                    <Discription><![CDATA['.$item[1].']]></Discription>
                    <PicUrl><![CDATA['.$item[2].']]></PicUrl>
                    <Url><![CDATA['.$item[3].']]></Url>
                    </item> ';
            $xml =  $xml.$temp;
        }
        echo sprintf($this->newsTpl,$count,$xml);
    }
    public function sendMusicMsg(){}
}

class OrangeWeChatCallback
{
   private $toBeSend ;
    private $postObj;
	public function valid()
    {
        $WeChatDebug = new debugLogForWeChat();
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature() || $WeChatDebug->getDebug()){
            echo $echoStr;
            $this->responseMsg();
            exit;
        }
        $WeChatDebug->Log("验证失败");

    }
    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
      	//extract post data
        $WeChatDebug = new debugLogForWeChat();
		if (!empty($postStr) || $WeChatDebug->getDebug()){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->postObj = $postObj;
            $WeChatDebug->Log("$postStr:".$postStr);
            $this->toBeSend =   new WeChatSendData(clone($postObj));

            $msgType = strtolower(trim($postObj->MsgType));
            $WeChatDebug->Log('$msgType: '.$msgType);
            if($msgType == 'event')
            {
                $event = strtolower(trim($postObj->Event));
                if($event == 'subscribe')
                { // 订阅
                    $this->subscribeEvent();
                }
                elseif($event == 'unsubscribe')
                {// 取消订阅
                    $this->unSubscribeEvent();
                }
                elseif($event == 'click')
                {	//点击事件
                    $this->menuClick(strtolower(trim($postObj->EventKey)));
                }
                else
                {
                    $this->otherMsg();
                }
            }
            elseif($msgType == 'text')
            {
                $this->textMsg();
            }
            elseif($msgType == 'image')
            {
               $this->imgMsg();
            }
            elseif($msgType == 'location')
            {
                $this->locationMsg();
            }
            elseif($msgType == 'voice')
            {
                $this->voiceMsg();
            }
            elseif($msgType == 'link')
            {
                $this->linkMsg();
            }
            else
            {
                $this->otherMsg();
            }
           // $this->search();
        }
        else
        {
            $this->otherMsg();
        }
    }
    private function subscribeEvent(){
        $this->toBeSend->sendNewsMsgFromXml(get_option('OrangeNewsAccount'),$this->allToBeDone());
    }
    private function unSubscribeEvent(){
        $this->toBeSend->sendNewsMsgFromXml(get_option('OrangeNewsAccount'),$this->allToBeDone());
    }
    private function menuClick($eventKey){
        $this->toBeSend->sendNewsMsgFromXml(get_option('OrangeNewsAccount'),$this->allToBeDone());
    }
    private function textMsg(){
        $text = strtolower(trim($this->postObj->Content));
        if($text =='1')
            $this->toBeSend->sendNewsMsgFromXml(1,$this->get_item('首页','描述','http://wpforsae-wordpress.stor.sinaapp.com/uploads/2013/11/0104432.jpg','http://4.wpforsae.sinaapp.com/'));
        elseif($text == '2')
            $this->toBeSend->sendNewsMsgFromXml(1,$this->get_item('博客','描述','http://wpforsae-wordpress.stor.sinaapp.com/uploads/2013/11/8ad4b31c8701a18b6ea3016c9e2f070829381f30e924de75.jpg','http://1.wpforsae.sinaapp.com/'));
        elseif($text == '3')
            $this->toBeSend->sendNewsMsgFromXml(1,$this->get_item('主题','描述','http://wpforsae-wordpress.stor.sinaapp.com/uploads/2013/11/0104434.jpg','http://4.wordpressforsina.sinaapp.com/'));
        elseif($text == '4')
            $this->toBeSend->sendNewsMsgFromXml(1,$this->get_item('会员天地','描述','http://wpforsae-wordpress.stor.sinaapp.com/uploads/2013/11/0104432.jpg','http://3.wordpressforsina.sinaapp.com/'));
        else
            $this->toBeSend->sendTextMsg("other");
    }
    private function imgMsg(){
        $this->toBeSend->sendNewsMsgFromXml(get_option('OrangeNewsAccount'),$this->allToBeDone());
    }
    private function voiceMsg(){
        $this->toBeSend->sendNewsMsgFromXml(get_option('OrangeNewsAccount'),$this->allToBeDone());
    }
    private function locationMsg(){
        $this->toBeSend->sendNewsMsgFromXml(get_option('OrangeNewsAccount'),$this->allToBeDone());
    }
    private function linkMsg(){
        $this->toBeSend->sendNewsMsgFromXml(get_option('OrangeNewsAccount'),$this->allToBeDone());
    }
    private function otherMsg(){
        $this->toBeSend->sendTextMsg("error");
    }
    //获取图片
    private function get_post_first_image($post_content){
        preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post_content, $matches);
        if($matches){
            return $matches[1][0];
        }else{
            return false;
        }
    }
    private function get_post_excerpt($post){
        $post_excerpt = strip_tags($post->post_excerpt);
        if(!$post_excerpt){
            $post_excerpt = mb_substr(trim(strip_tags($post->post_content)),0,120);
        }
        return $post_excerpt;
    }

    private function allToBeDone(){

		$items = '';
        for($i = 1;$i<=get_option('OrangeNewsAccount');$i++)
        {
            $title = esc_attr(get_option('OrangeTitle_'.$i));
            $desc = esc_attr(get_option('OrangeDescription_'.$i));
            $picUrl = esc_attr(get_option('OrangePicUrl_'.$i));
            $url = esc_attr(get_option('OrangeUrl_'.$i));
            $openId = $this->postObj->FromUserName;     //动态URL，确认身份
            if($url != NULL && $url != '')
                $url .= strstr($url,'?') ? '&token='.$openId : '?token='.$openId;
            $items .= $this->get_item($title,$desc,$picUrl,$url);
        }
        return $items;
    }

    private function get_item($title, $description, $picUrl, $url){
        if(!$description) $description = $title;
        return
        '
        <item>
            <Title><![CDATA['.$title.']]></Title>
            <Discription><![CDATA['.$description.']]></Discription>
            <PicUrl><![CDATA['.$picUrl.']]></PicUrl>
            <Url><![CDATA['.$url.']]></Url>
        </item>
        ';
    }
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];    
                
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
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