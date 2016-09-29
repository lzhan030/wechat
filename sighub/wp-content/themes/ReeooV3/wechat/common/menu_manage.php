<?php

		//定义一个菜单
	$menu = new MenuDefine();
	$menu->menuStart();  //菜单开始

	$menu_one=wechat_menu_parget_group(-1,$MENUGWEID);

	foreach($menu_one as $menuo){
		
		$menu_id=$menuo->menu_id;
		$menu_two=wechat_menu_parget_group($menu_id,$MENUGWEID);
		
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
	
?>