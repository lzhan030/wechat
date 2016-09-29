<?php

defined('IN_IA') or exit('Access Denied');
require_once ABSPATH.'wp-content/themes/ReeooV3/wechat/webchat/DES.php';
class WebchatModuleSite extends ModuleSite {

	public function doMobileChat(){
		global $_W, $wpdb;
		
		$gweid=$_W['gweid'];
		$mid=$_W['fans']['serviceid'];
		$fromuser = $_W['fans']['from_user'];
		$gweidv=$_W['gweidv'];  //获取虚拟号gweid
		$weid=$_W['weid'];
		
		if(!$this->has_module($gweid,'wechatcuservice')){
			message('没有开启第三方客服权限，无法使用该功能！');
		}
		
		//如果是虚拟的号，取出的链接还是共享或非共享的，仅会员是虚拟的
		$url=$wpdb->get_var( $wpdb -> prepare("SELECT wechat_cuservice FROM {$wpdb->prefix}wechat_usechat where GWEID=%d",$gweid));
		if(!empty($mid)){
			$vipinfo=$wpdb->get_row( $wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_member_thirdservice where id=%d and GWEID=%d",$mid,$gweidv),ARRAY_A);
			$tokenValue="userPhone=".$vipinfo['mobilenumber']."&accessName="."&position="."&habit="."&hobby=";
		}else if(empty($fromuser)){	//不是会员并且没有fromuser则直接不带phone进行token处理并跳转
		    $tokenValue="userPhone="."&accessName="."&position="."&habit="."&hobby=";
			if(!empty($url)){
				$url=str_replace("channel=pzhwx","",$url);
			}
		}
		
		if(!empty($mid)||empty($fromuser)){
			if(!empty($url)){
				$des = new DES3();//echo $des->decrypt($ret);
				$encTokenValue=$des->encrypt($tokenValue);//加密
				//$encodeUrl=urlencode($encTokenValue);//编码//http_build_query会进行urlencode处理
				
				$paramurl=$this->redirectUrl($url,$encTokenValue);
				header('location: ' .$paramurl."#wechat_redirect");
			}else{/*如果多图文没有url则跳转到图文内容显示页面*/
				message('链接无效！');
			}
		}
		
		if(isset($_POST['user_phone'])&&!empty($_POST['user_phone'])){
			$user_phone = $_POST['user_phone'];
			
			$mobilenumber=$wpdb->get_var( $wpdb -> prepare("SELECT mobilenumber FROM {$wpdb->prefix}wechat_member_thirdservice where mobilenumber=%s and WEID=%d and GWEID=%d",$user_phone,$weid,$gweidv));
			if(!empty($mobilenumber)){
				$registerary = array("status"=>"error","message"=>"该手机号已经注册");
				echo json_encode($registerary);
				exit;
			}
			
			$insert = array(
				'WEID' => intval($weid),
				'GWEID' => intval($gweidv),
				'from_user' => $fromuser,
				'mobilenumber' => $user_phone,
				'rtime'=> date('Y-m-d H:i:s')
			);
			$status=$wpdb -> insert("{$wpdb->prefix}wechat_member_thirdservice",$insert);
			if($status!==false){
				$registerary = array("status"=>"success","message"=>"申请成功");
				echo json_encode($registerary);
				exit;				
			}else{
				$registerary = array("status"=>"error","message"=>"系统出现问题，请重试");
				echo json_encode($registerary);
				exit;
			}
		}
	
		include $this->template('vipreg');
	}
	
	public function doWebExportData(){
		global $wpdb;

		$gweid=intval($_GET["gweid"]);
		$gweidv=intval($_GET["gweid"]);


		$getgroupids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_group w left join {$wpdb->prefix}user_group u on w.user_id = u.user_id where w.GWEID=".$gweid,ARRAY_A);

		//obtain the groupid
		if(!empty($getgroupids)){
			foreach ($getgroupids as $getgroupid) {
				$gid = $getgroupid['group_id'];
			}
			
			if(!empty($gid)){
				$getflags = $wpdb->get_results("SELECT count(*) as flagcount FROM {$wpdb->prefix}user_group u where u.group_id=".$gid." and u.flag = 1",ARRAY_A);
				foreach ($getflags as $getflag) {
					$flagcount = $getflag['flagcount'];
				}
				
				//如果有分组管理员,查看分组中的虚拟号是否是开启状态
				if($flagcount != 0){
					$getadminusers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_group u where u.group_id=".$gid." and u.flag = 1",ARRAY_A);
					//obtain the groupid
					foreach ($getadminusers as $getadminuser) {
						$adminuserid = $getadminuser['user_id'];
					}
					
					$getvgweids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}user_group u left join {$wpdb->prefix}wechat_group w on u.user_id=w.user_id where u.flag = 1 and w.adminshare_flag = 1 and u.group_id=".$gid,ARRAY_A);
					
					//obtain the groupid
					if(!empty($getvgweids)){
						foreach ($getvgweids as $getvgweid) {
							$vgweid = $getvgweid['GWEID'];
						}
						$gweidv =  $vgweid;   //将虚拟号的gweid赋过来
					}
				}
			}
		}



		$winfo= $wpdb->get_row( "SELECT u1.WEID,u2.wid,u2.menu_appId,u2.menu_appSc,u2.wechat_nikename FROM {$wpdb->prefix}wechat_usechat u1,{$wpdb->prefix}wechats u2 where u1.wid=u2.wid and u1.WEID != 0 and u1.GWEID='".intval($gweid)."'" ,ARRAY_A);

		$weid=$winfo['WEID'];

		$filename=$winfo['wechat_nikename']."第三方客服用户手机统计.csv";//先定义一个excel文件

		header("Content-Type: application/vnd.ms-execl"); 
		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename"); 
		header("Pragma: no-cache"); 
		header("Expires: 0");

		//我们先在excel输出表头，当然这不是必须的
		echo iconv("utf-8", "gb2312", "绑定时间").",";
		echo iconv("utf-8", "gb2312", "手机号码")."\n";


		$sql=$wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_member_thirdservice where WEID=%d and GWEID=%d",$weid,$gweidv);
		$results = $wpdb->get_results($sql,ARRAY_A);

		if(is_array($results) && !empty($results)){

			foreach($results as $data){
			
				echo iconv("utf-8", "gb2312", $data['rtime']).",";
				echo iconv("utf-8", "gb2312", $data['mobilenumber'])."\n";
				
			}
		}
    }
	
	
	
	/*url处理*/
	function redirectUrl($url,$encodeUrl){
		
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
			$tmp['token'] = $encodeUrl;
			$queryString = http_build_query($tmp);					
			$las=explode("#",$url);					
			$paramurl=substr($url,0,$firloc)."?".$queryString.($las[1]?"#".$las[1]:'');
		}else{
			$tmp['token'] = $encodeUrl;
			$queryString = http_build_query($tmp);
			$las=explode("#",$url);
			$paramurl=$url."?".$queryString.($las[1]?"#".$las[1]:'');
		}
		return $paramurl;
	}
	
	/*是否开启功能权限*/
	function has_module($gweid,$type){
		global $_W,$wpdb;
		$result = $wpdb -> get_results($wpdb -> prepare("SELECT * FROM {$wpdb->prefix}wechat_func_info a WHERE NOT EXISTS(SELECT * FROM wp_wechat_initfunc_info b1 WHERE a.func_name=b1.func_name AND type = 'GWEID' AND value = %s AND func_flag = 0) LIMIT 0, 100",$gweid),ARRAY_A);
		foreach($result as $initfunc){
			if($selCheck[$initfunc['func_name']] == 0)
				$selCheck[$initfunc['func_name']] = $initfunc['status'];
		}
		if($type=='wechatvip'){
			if($selCheck['wechatvip']!=1){
				return false;
			}else{
				return true;
			}
		}else{
			if($selCheck[$type]!=1){
				return false;
			}else{
				return true;
			}
		}
	}
}