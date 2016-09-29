
<?php
	@session_start();
	include './wp-content/themes/ReeooV3/wechat/common/wechat_dbaccessor.php';
	
	$parId=$_GET["parId"];
	$M_id=$_GET["M_id"];
	$menuName=$_POST["menuname"];
	if(empty($parId) || empty($menuName)){
       echo "不能为空";exit;
    }else{
	
		//如果管理员不删除菜单模板，而将模板情况后再编辑菜单项就失败了&&公众账号使用了没有菜单项的菜单模板，商家添加了该公众账号后，模板菜单也没有。修改如下：
		$ishave=wechat_menu_public_get_mid($M_id);
		if(empty($ishave)){
			$wechats_info=wechat_select_public_wid($M_id);//该菜单模板在哪些公众账号上面
			foreach($wechats_info as $winfo){
				$wid=$winfo->wid;
				$weidsinfo=wechat_usechat_get($wid);//有哪些商家利用了这些公众账号(包括一个商家对某个公众号的多次添加)
				foreach($weidsinfo as $weids){
					$WEID=$weids->WEID;
					wechat_menu_publicsvc_insert("","","",$WEID,$wid,$M_id);
				}								
			}
		}

        $adderlt=wechat_menu_public_add($parId,$menuName,$M_id);
		$update=wechat_menu_public_updateforchid($parId,"","");
        
		if($adderlt===false){
            echo "添加失败！";
        }else{
            echo "添加成功！";
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
	}
</script>