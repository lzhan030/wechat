<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	

?>


<?php
require_once './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';	
//加上这个代码，从js传参数过来
@extract($_REQUEST);

$M_id=$_GET["Mid"];

if($M_id!="0"){
	$wechats_info=wechat_select_public_wid($M_id);
	if(!empty($wechats_info)){
		$name=":";
		$first = true;
		foreach($wechats_info as $winfo){
			$wid=$winfo->wid;			
			$weidsinfo=wechat_wechats_info($wid);			
			foreach($weidsinfo as $weinfo){
				$wechat_nikename=$weinfo->wechat_nikename;				
				if($first){
					$first = false; 
					$name=$name.$wechat_nikename;
				}else{
					$name=$name.",".$wechat_nikename;
				}				
			}
		}
		$candelarr = array("iscandel"=>"no","business"=>$name);
		echo json_encode($candelarr);
	}else{
		$candelarr = array("iscandel"=>"yes");
		echo json_encode($candelarr);
	
	}
}else{
		$candelarr = array("iscandel"=>"error");
		echo json_encode($candelarr);
}


	
?>