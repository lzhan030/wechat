<?php 
	
		//����һ���˵�
		$menu = new MenuDefine();

		$menu->menuStart();  //�˵���ʼ

		$menu_one=wechat_menu_parget_group(-1,$GWEID);

		foreach($menu_one as $menuo){
			
			$menu_id=$menuo->menu_id;
			$menu_two=wechat_menu_parget_group($menu_id,$GWEID);
			if(!empty($menu_two)){
				$menu->addMenu($menuo->menu_name);
				foreach($menu_two as $menudis){
					if($menudis->menu_type=='view'){
						$mtype="url";
						/*���û��http��֤��Ϊ����������home_url��ʾ������ʱ���ж����ٽ�ȡ���*/
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
				/*���û��http��֤��Ϊ����������home_url��ʾ������ʱ���ж����ٽ�ȡ���*/
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
		$menu->menuEnd(); //�˵�����, ���ʱ$menu->str���в˵���������(JSON��ʽ) 

				
		foreach($weidinfo as $winfo){
			$APPID=$winfo->menu_appId;
			$APPSECRET=$winfo->menu_appSc;
			$wid=$winfo->wid;
			$ACC_TOKEN=re_Token($APPID,$APPSECRET);	
			$update=wechat_info_update($ACC_TOKEN,$wid);	
			if($menu->str!= '{ "button": [  ] }'){
				$result=wechat_menu_create($ACC_TOKEN,$menu->str);
			}else{
				$resultdel=wechat_menu_delete($ACC_TOKEN);
			}		
		}
?>