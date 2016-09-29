<?php



//定义一个菜单
$menu = new MenuDefine();
$menu->menuStart();  //菜单开始

$menu_one=wechat_menu_parget_group(-1,$GWEIDZERO);

foreach($menu_one as $menuo){
	
	$menu_id=$menuo->menu_id;
	$menu_two=wechat_menu_parget_group($menu_id,$GWEIDZERO);
	
	if(!empty($menu_two)){
		$menu->addMenu($menuo->menu_name);
		foreach($menu_two as $menudis){
			if($menudis->menu_type=='view'){
				$mtype="url";
				/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
				$tmp = stristr($menudis->menu_key,"http");
				if(($tmp===false)&&(!empty($menudis->menu_key))){
					$menukeylink=home_url().$menudis->menu_key;
				}else{				
					$menukeylink=$menudis->menu_key;
				}
				$menu->addMenuItem($menudis->menu_name,$mtype,"view",$menukeylink);
			}else{
				$mtype="key";
				$menu->addMenuItem($menudis->menu_name,$mtype,"click",$menudis->menu_id);
			}		
		}
	}
	if(empty($menu_two)){
		/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
		$tmp = stristr($menuo->menu_key,"http");
		if(($tmp===false)&&(!empty($menuo->menu_key))){
			$menukeylink=home_url().$menuo->menu_key;
		}else{				
			$menukeylink=$menuo->menu_key;
		}
		$menu->addMenu(array(
		  'type' => $menuo->menu_type=='view'?"view":"click",
		  'name' => $menuo->menu_name,
		  $menuo->menu_type=='view'?'url':'key'=> $menuo->menu_type=='view'?$menukeylink:$menuo->menu_id,
		),true); 
		
	}
}

$menu->menuEnd(); //菜单结束, 则此时$menu->str中有菜单定义数据(JSON格式) 

//自身就是微信公众号(认证的个人订阅号和个人服务号)的menuappid和menuappsc
$APPID=$menuappid;
$APPSECRET=$menuappsc;
if((!empty($APPID))&&(!empty($APPSECRET)))
{
	$ACC_TOKEN=re_Token($APPID,$APPSECRET);	
	$update=wechat_info_update($ACC_TOKEN,$wid);
	
	if($menu->str!= '{ "button": [  ] }'){
		$result=wechat_menu_create($ACC_TOKEN,$menu->str);
	}else{
		$result=wechat_menu_delete($ACC_TOKEN);
	}
	
	//echo "result:".$result;
	if($result != '0')
	{
	   $menuflag = false;
	   ?>
	    <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
	    <html>
	    <head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	    <script>
		var result='<?php echo $result;?>';
		var obj =eval(<?php echo json_encode($WECHAT_RESPONSE);?>);
		if(obj[result]!=undefined){
			alert(obj[result]);
		}else{
			alert("菜单上传出现错误，请重试")
		}
		</script>
		</head>
		</html>
		<?php
	}
}
//原来是未认证的个人订阅号，现在改为已认证后新输入了menuappid和menuappsc
$APPID1=$wechatmenuappid1;
$APPSECRET1=$wechatmenuappsc1;
if((!empty($APPID1))&&(!empty($APPSECRET1)))
{
	$ACC_TOKEN1=re_Token($APPID1,$APPSECRET1);	
	$update1=wechat_info_update($ACC_TOKEN1,$wid);
	if($menu->str!= '{ "button": [  ] }'){	
		$result1=wechat_menu_create($ACC_TOKEN1,$menu->str);
	}else{
		$result1=wechat_menu_delete($ACC_TOKEN1);
	}
	//echo "result1:".$result;
	if($result1 != '0')
	{
	   $menuflag = false;
	   ?>
	   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
	    <html>
	    <head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	   <script>
	    var result1='<?php echo $result1;?>';
		var obj =eval(<?php echo json_encode($WECHAT_RESPONSE);?>);
		if(obj[result1]!=undefined){
			alert(obj[result1]);
		}else{
			alert("菜单上传出现错误，请重试")
		}
		</script>
		</head>
		</html>
		<?php
	}
}
?>