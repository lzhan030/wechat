<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
require_once '../../wesite/common/dbaccessor.php';
/*get weid,gweid,fromuser from news*/
$gweid=$_GET['GWEID'];
unset($_SESSION['fromuser']);
unset($_SESSION['WEID']);
unset($_SESSION['gopenid'][intval($_GET['GWEID'])]);
unset($_SESSION['weid'][intval($_GET['GWEID'])]);
$fromuser_get=$_GET['fromuser'];

//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
$gweid = virtualgweid_open($gweid);

//if(isset($_GET['time']) && isset($_GET['signature'])){
//	$time=time();
//	$signature = md5($_GET['time'].NONCE_SALT);
//	if(($time - intval($_GET['time'])) <= OPENID_EXPIRE_TIME && $_GET['signature'] == $signature)
		if(!empty($fromuser_get)){
			$_SESSION['gopenid'][$gweid] = $fromuser_get;
		}
		if(!empty($_GET['WEID'])){
			$_SESSION['weid'][$gweid] = $_GET['WEID'];
		}
		
//}
/*多图文里添加的url,封装gweid和weid*/
$url=$_GET['redirect_url'];
if(!empty($url)){
	if(!strstr($url,"42.96.185.189")){
		$tmp=array();
		$noinfo=false;
		$ifhaveone=stristr($url,"?");
		$ifhavetwo=stristr($url,"#");
		$firloc=strpos($url,"?");
		$endloc=strpos($url,"#");	
		if(($ifhaveone)&&($ifhavetwo)){				
			$query=substr($url,$firloc+1,$endloc-$firloc-1);
		}else if(($ifhaveone)&&(!$ifhavetwo)){/*有问号无井号*/
			$query=substr($url,$firloc+1);
		}else{/*无问号有井号+无问号无井号*/
			$noinfo=true;				
		}	
		if(!$noinfo){
			$kvs=explode("&",$query);
			foreach($kvs as $k=>$v){
				$tmpkv = explode("=",$v);
				$tmp= array_merge ( $tmp, array($tmpkv[0] => $tmpkv[1] ) );
			}			
			if(empty($tmp['gweid'])){/*如果存在gweid则不覆盖，防止gweid并不一样而被修改*/
				$tmp['gweid'] = $gweid;
			}
			$queryString = http_build_query($tmp);					
			$las=explode("#",$url);					
			$paramurl=substr($url,0,$firloc)."?".$queryString.($las[1]?"#".$las[1]:'');
		}else{
			if(empty($tmp['gweid'])){	
				$tmp['gweid'] = $gweid;
			}
			$queryString = http_build_query($tmp);
			$las=explode("#",$url);
			$paramurl=$url."?".$queryString.($las[1]?"#".$las[1]:'');
		}
	}else{
		$paramurl="http://42.96.185.189/maibo/index.php/openidcommon/creaturl?openid=".$fromuser_get."&redirect_url=".urlencode($url);
	}
	header('location: ' .$paramurl."#wechat_redirect");
}else{/*如果多图文没有url则跳转到图文内容显示页面*/
	$newsid=$_GET['newsid'];
	header('Location: '."wechat_content.php?newsid={$newsid}"."#wechat_redirect");
}
?>