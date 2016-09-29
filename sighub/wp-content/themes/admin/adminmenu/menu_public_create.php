<?php

@header("Content-type: text/html; charset=utf-8"); 
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
?>


<?php

include './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
include './wp-content/themes/ReeooV3/wechat/common/jostudio.wechatmenu.php';


$wid=$_GET["wid"];
$weinfo=wechat_wechats_get(intval($wid));

foreach($weinfo as $win){
    $APPID=$win->menu_appId;
    $APPSECRET=$win->menu_appSc;
	$wechat_type=$win->wechat_type;
	$wechat_auth=$win->wechat_auth;
}

if((($wechat_type == "pub_sub")&&($wechat_auth == 1))||($wechat_type == "pub_svc")){
//获取token

	$ACC_TOKEN=re_Token($APPID,$APPSECRET);
	$update=wechat_info_update($ACC_TOKEN,$wid);
	if($update===false){
		echo "acctoken update  error";
	}

	$demomenu=$_GET["demomenu"];  //获取菜单编号M_id
	$MENUGWEID=$demomenu;
	include './wp-content/themes/ReeooV3/wechat/common/menu_public_manage.php';
	if($menu->str!= '{ "button": [  ] }'){
		$result=wechat_menu_create($ACC_TOKEN,$menu->str);
	}else{
		$result=wechat_menu_delete($ACC_TOKEN);
	}
}
else
{
    $result = "";
}
?>
    <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script>
	    var wid=<?php echo $wid ;?>;
		var result = '<?php echo $result;?>';
		var obj =eval(<?php echo json_encode($WECHAT_RESPONSE);?>);
		if((result!= '0')&&(result!= '')&&(result!= ' ')){
	   
		   if(obj[result]!=undefined){
				alert(obj[result]);
			}else{
				alert("菜单上传出现错误，请重试")
			}
	      location.href = "?admin&page=pubwechatmanage&wid="+wid;
		 
	    }else{
		   location.href = "?admin&page=pubwechatmanage&wid="+wid;
	    }
	</script>
	</head>
	</html>