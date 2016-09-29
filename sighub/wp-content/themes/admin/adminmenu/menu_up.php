<?php
	@session_start();

	$path = explode ( 'wp-content', __FILE__ );
	$wp_root_path = $path [0];
	require_once ($wp_root_path . '/wp-load.php');
	//get_header();
    global $current_user;
?>

<?php
	include './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
	$menuId=$_GET["menuId"];
	$menuName=$_POST["menuname"];
	$M_id=$_GET["M_id"];
	if(empty($menuId) || empty($menuName)){
       echo "不能为空";
    }/*else{
    $arr = wechat_public_menu_isExistInDB($menuName,$M_id);
    foreach($arr as $arraynumber){
        $count_number=$arraynumber->arrayCount;
    }
    if($count_number >0) {
        echo "添加失败，已有此菜单名称";
    }*/
    else{
        $updaterlt=wechat_public_menu_name_update($menuName, $menuId);

        if($updaterlt===false){
            echo "更新失败！";
        }else{
            echo "更新成功！";
        }
    }

?>

<body onload="closeit()">
</body>

<script language='javascript'>

	function closeit() {
		top.resizeTo(300, 200); 		
		setTimeout("self.close()", 2000); 
		opener.location.reload();  
		//opener.menuPage();
	}
</script>