<?php 
	$tmp_path = explode ( 'ReeooV3', __FILE__ );
	$template_path=$tmp_path[0];
	require_once $template_path.'ReeooV3/wechat/common/session.php';

	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');
	global $current_user;
?>

<?php
	include '../common/wechat_dbaccessor.php';
	include 'keyword_permission_check.php';
	$keywordId=$_GET["keywordId"];
	$keycontent=$_POST["key"];
	if(empty($keywordId) || $keycontent==""||$keycontent==null){
       echo "不能为空";exit;
    }
	$arr = wechat_mess_kw_isExistInDB_group($keycontent,$_SESSION['GWEID']);
	foreach($arr as $arraynumber){
        $count_number=$arraynumber->arrayCount;
    }
    if($count_number >0) {
        echo "添加失败，已有此关键字";
    }
    else{
        $updaterlt=wechat_mess_kw_name_update($keycontent, $keywordId);

        if($updaterlt===false){
            echo "更新失败！";
        }else{
            echo "更新成功！";
        }
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body onload="closeit()">
	</body>

	<script language='javascript'>

		function closeit() {
			top.resizeTo(300, 200); 		
			setTimeout("self.close()", 2000); 
			opener.location.reload();  
		}
	</script>
</html>