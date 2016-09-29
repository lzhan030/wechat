<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
require_once ('../wesite/common/dbaccessor.php');
require_once ('../wesite/common/upload.php');
require_once ('../wechat/common/wechat_dbaccessor.php');
require_once ('../wechat/common/jostudio.wechatmenu.php');
require_once ('../wesite/common/random.php');

//2014-07-15新增修改,获取最开始创建用户时的gweid在initfunc_info中的信息
//obtain userId
global $current_user;
$userId= $current_user->ID;

$funcinfos = getUseridFuncinfo($userId);
foreach($funcinfos as $funcinfo){
	$selCheck[$funcinfo->func_name] = $funcinfo->func_flag;
}

//第三方客服设置入库
if(isset($_POST['cuservicethird_url'])){
	$cuservicepost=trim($_POST['cuservicethird_url']);
}else{
	$cuservicepost="";
}
	

//Get the post value from previous page
$wechat_fans = $_POST['wechatfans']; //新添加获取页面上的用户填入的微信名称
//增加公众号图片上传功能20141204
/*上传图片*/
	$type =strtolower(strstr($_FILES['file']['name'], '.'));
	if($type == false){
		$_FILES['file']['name'] = $_FILES['file']['name'].".jpg";
		$type = ".jpg";
	}
	$picname = $_FILES['file']['name'];
	$picsize = $_FILES['file']['size'];

	if ($picname != "") {
		if ($picsize > 1024000) {
			$hint = array("status"=>"success","message"=>"图片大小不能超过1M!");
			echo json_encode($hint);
			exit;
		}
		
		if ($type != ".gif" && $type != ".jpg"&& $type != ".png" && $type != ".jpeg") {
			$hint = array("status"=>"success","message"=>"图片格式不对!");
			echo json_encode($hint);
			exit;
		} 
		
		$up=new upphoto();	
		$picUrl=$up->save();
		$path=substr( $picUrl,1);
	}
	$size = round($picsize/1024,2);
	$upload =wp_upload_dir();
	if((empty($picUrl))||(stristr($picUrl,"http")!==false)){
		$echopicurl=$picUrl;
	}else{
		$echopicurl=$upload['baseurl'].$picUrl;
	}
	/*上传图片END*/

if($_POST['wechattype']=='pri_sub')
{
   if( isset($_POST['site_name']) ){

        $wechattype=$_POST['wechattype'];
	   
		$token=generate_password(10);
		
		//2014-07-14新增修改
		$shared_flag = $_POST['share'];  //获取用户添加公众号时的flag
		$active_flag = $_POST['active'];
		
		$wechat_name = $_POST['wechatname']; //新添加获取页面上的用户填入的微信名称
		$wechat_auth = $_POST['auth']; //新添加获取页面上的微信公众号的认证情况
		
		//2014-07-07新增修改，如果是已认证过的公众号，需要填写menuid和menusc
		if($wechat_auth == 1)
		{
		    $menuappid=trim($_POST['menu_appId1']);
			$menuappsc=trim($_POST['menu_appSc1']);
			$sitename=$_POST['site_namem'];
		
		}
		else
		{
		    $sitename=$_POST['site_name'];
		}
		
		//obtain userId
		global $current_user;
		$userId= $current_user->ID;
		//生成随机数
		$hash = random(5);
		$weid = weid();
		$_SESSION['WEID']=$weid;
		
		$vericode=generate_password(8);
		
		//2014-6-23最新修改增加函数
		//通过session获取gweid的值
        

		//start-janeen对wechat_group表进行数据插入，用来判断共享与否等
		$GWEID = gweid();
		if(($shared_flag=='1')&&(!empty($active_flag))){
			$insert=getWechatGroup_insert($GWEID,$userId,$weid,$active_flag);
		}else{			
			$insert=getWechatGroup_insert($GWEID,$userId,$weid,$shared_flag);	
		}
				
		//end-janeen
		//2014-07-15新增修改，初始化新创建的gweid
		foreach($selCheck as $func_name => $func_flag){
			$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$GWEID, $func_name, $func_flag));
		} 

		
		if($wechat_auth == 0)
		{
			$wid=web_admin_add_wechat_prisub($hash, $sitename, $wechat_name, $wechattype, $wechat_auth, $token, $weid, $userId, $vericode, $GWEID, $wechat_fans, $picUrl,$cuservicepost);  
		}		
		else
		{
			$wid=web_admin_add_wechat_prisubrenzheng($hash, $sitename, $wechat_name, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc, $weid, $userId, $vericode, $GWEID, $wechat_fans, $picUrl,$cuservicepost);

			//0715
			if(($wechat_auth == 1)&&($shared_flag==1)&&(empty($active_flag))){
				
				$weinfo=getWechatGroupActiveInfo($userId,2);
				foreach($weinfo as $gweids){
					$GWEIDZERO=$gweids->GWEID;//虚拟号的GWEID
				}
				require_once ('wechat_config_menu.php');
				
			}else if(($wechat_auth == 1)){//去掉shared_flag的为0条件，active_flag也可以执行菜单生成2014-08-18
				$GWEIDZERO=$GWEID;//真实的GWEID
				require_once ('wechat_config_menu.php');
			}
			
		}
		//初始化公众号功能列表(有可能有多个公众号)
		web_admin_initfunction($weid);
		
	    $isSubmit = TRUE;
	    $url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoin.php?hash='.$hash;
		$url=preg_replace('|^https://|', 'http://', $url);
    }
}

if($_POST['wechattype']=='pri_svc')
{
   if( isset($_POST['site_name1']) ){

        $wechattype=$_POST['wechattype'];
	    $sitename=$_POST['site_name1'];
		$menuappid=trim($_POST['menu_appId']);
		$menuappsc=trim($_POST['menu_appSc']);
		$token=generate_password(10);
		
		//2014-07-14新增修改
		$shared_flag = $_POST['share'];  //获取用户添加公众号时的flag
		$active_flag = $_POST['active'];
		$wechat_name = $_POST['wechatname']; //新添加获取页面上的用户填入的微信名称
		$wechat_auth = $_POST['authfw']; //新添加获取页面上的微信公众号的认证情况
		
		//obtain userId
		global $current_user;
		$userId= $current_user->ID;
		//生成随机数
		$hash = random(5);
		$weid = weid();
		$_SESSION['WEID']=$weid;
		$vericode=generate_password(8);
	   
		//2014-6-23最新修改增加函数
		//通过session获取gweid的值
		
		//start-janeen对wechat_group表进行数据插入，用来判断共享与否等
		$GWEID = gweid();		
		if(($shared_flag=='1')&&(!empty($active_flag))){
			$insert=getWechatGroup_insert($GWEID,$userId,$weid,$active_flag);
		}else{
			$insert=getWechatGroup_insert($GWEID,$userId,$weid,$shared_flag);
		}
		
		//end-janeen
		//2014-07-15新增修改，初始化新创建的gweid
		foreach($selCheck as $func_name => $func_flag){
			$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$GWEID, $func_name, $func_flag));
		} 
		
		$wid=web_admin_add_wechat_prisvc($hash, $sitename, $wechat_name, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc, $weid, $userId, $vericode, $GWEID, $wechat_fans,$picUrl,$cuservicepost);
		
		
		//0715
		if(($shared_flag==1)&&(empty($active_flag))){
			
			$weinfo=getWechatGroupActiveInfo($userId,2);
			foreach($weinfo as $gweids){
				$GWEIDZERO=$gweids->GWEID;//虚拟号的GWEID
			}
			require_once ('wechat_config_menu.php');
		
		}else{
			$GWEIDZERO=$GWEID;//真实的GWEID
			require_once ('wechat_config_menu.php');
		
		}
		
				
		//初始化公众号功能列表(有可能有多个公众号)
		web_admin_initfunction($weid);
		
		
	    $isSubmit = TRUE;
	    $url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoin.php?hash='.$hash;
		$url=preg_replace('|^https://|', 'http://', $url);
	    //$token = 'wexinCourse';
		if(($menuappid=='')&&($menuappsc==''))
		{
		    $menuinfo="您没有输入菜单信息，没有自定义菜单功能";
		}
    }
}

//当添加公众号时需要执行。。。。此处还不明确，需要最终修改
//2014-07-09新增修改，如果点击的是公共未认证订阅号
if($_POST['wechattype']=='pub_subnrz')
{
  
	if( !empty($_POST['wechatpubsub']) )
	{

        $wechattype="pub_sub";
	    $pubsubname=$_POST['wechatpubsub'];
		//拿到微信公众号的wid
		$wid=$_POST['wechatpubsub'];
		
	    //2014-07-14新增修改
		$shared_flag = $_POST['share'];  //获取用户添加公众号时的flag	//2014-07-09新增修改，前台用户不能自己填写公用的公众号的微信名称，应该由管理员进行填写
		$active_flag = $_POST['active'];
		//2014-07-11新增修改
		$wechat_name = $_POST['wechatname']; //新添加获取页面上的用户填入的微信名称
		$busexit = $_POST['busexit'];
		$exireply_content = $_POST['exireply_content'];

		//obtain userId
		global $current_user;
		$userId= $current_user->ID;
		//生成随机数
		$weid = weid();
		$_SESSION['WEID']=$weid;

		$vericode=generate_password(8);

	    //2014-2-13最新修改增加函数
		//通过昵称获得微信公众号的wid,此处不合理,因为根据昵称，昵称不是唯一的
		$count = web_admin_get_wechat_pubsubcount($userId, $wid);
			$GWEID = gweid();			
			
			$insert=getWechatGroup_insert($GWEID,$userId,$weid,$shared_flag);
					
			//end-janeen
			//2014-07-15新增修改，初始化新创建的gweid
			foreach($selCheck as $func_name => $func_flag){
				$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$GWEID, $func_name, $func_flag));
			} 
			
			//2014-07-09新增修改
			web_admin_add_wechat_pubsubper_all($weid, $userId, $wid, $wechat_name, $vericode,$busexit,$exireply_content,$GWEID, $wechat_fans,$picUrl,$cuservicepost);
			//初始化公众号功能列表(有可能有多个公众号)
		    web_admin_initfunction($weid);

			//2014-07-07新增修改
			header("Location: ".constant("CONF_THEME_DIR")."/init/apply_success.php?beIframe&weid=".$weid);
			exit;
    }
}

//2014-07-09新增修改,如果点击的是公共认证订阅号，
if($_POST['wechattype']=='pub_subrz')
{
	if( !empty($_POST['wechatpubsubrz']) )
	{
        
        $wechattype="pub_sub";
	    $pubsubname=$_POST['wechatpubsubrz'];
		//拿到微信公众号的wid
		$wid=$_POST['wechatpubsubrz'];
	
	    //2014-07-14新增修改
		$shared_flag = $_POST['share'];  //获取用户添加公众号时的flag
		$active_flag = $_POST['active'];
		//2014-07-11新增修改
		$wechat_name = $_POST['wechatname']; //新添加获取页面上的用户填入的微信名称
		$busexit = $_POST['busexit'];
		$exireply_content = $_POST['exireply_content'];
		global $current_user;
		$userId= $current_user->ID;

		$weid = weid();
		$_SESSION['WEID']=$weid;
		$vericode=generate_password(8);
		$count = web_admin_get_wechat_pubsubcount($userId, $wid);

		//2014-06-20新修改函数
		//通过session获取gweid的值
		
		//start-janeen对wechat_group表进行数据插入，用来判断共享与否等
		$GWEID = gweid();		
		$insert=getWechatGroup_insert($GWEID,$userId,$weid,$shared_flag);		
		//end-janeen
		//2014-07-15新增修改，初始化新创建的gweid
		foreach($selCheck as $func_name => $func_flag){
			$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$GWEID, $func_name, $func_flag));
		} 
		
		//2014-07-09新增修改
		web_admin_add_wechat_pubsubper_all($weid, $userId, $wid, $wechat_name, $vericode,$busexit,$exireply_content,$GWEID, $wechat_fans,$picUrl,$cuservicepost);
		
		//20140313janeen添加用户对应公众号的菜单
		//2014-07-09公共认证订阅号和服务号的效果是一样的
		$wechats_info=web_admin_get_wechats_info($wid);
		foreach($wechats_info as $winfo){
			$M_id=$winfo->M_id;
		}
		
		$wechats_user_content=web_user_wechat_menu_content_get($M_id);
		foreach($wechats_user_content as $wconinfo){
			$menu_id=$wconinfo->menu_id;
			$insert=web_user_wechat_menu_add($menu_id,"","",$userId,$weid,$wid,$M_id);
		}
		
		//初始化公众号功能列表(有可能有多个公众号)
		web_admin_initfunction($weid);
		
		//2014-07-07新增修改
		header("Location: ".constant("CONF_THEME_DIR")."/init/apply_success.php?beIframe&weid=".$weid);
		exit;
    }
}


//当添加公众号时需要执行。。。。此处还不明确，需要最终修改
//2014-07-09新增修改,如果点击的是公共未认证服务号
if($_POST['wechattype']=='pub_svcnrz')
{
   
	if( !empty($_POST['wechatpubsvc']) ){

        $wechattype="pub_svc";
	    $pubsvcname=$_POST['wechatpubsvc'];
		$wid=$_POST['wechatpubsvc'];
		
		//2014-07-14新增修改
		$shared_flag = $_POST['share'];  //获取用户添加公众号时的flag	//2014-07-09新增修改，前台用户不能自己填写公用的公众号的微信名称，应该由管理员进行填写
		$active_flag = $_POST['active'];
		//2014-07-11新增修改
		$wechat_name = $_POST['wechatname']; //新添加获取页面上的用户填入的微信名称
		$busexit = $_POST['busexit'];
		$exireply_content = $_POST['exireply_content'];
		//obtain userId
		global $current_user;
		$userId= $current_user->ID;
		$weid = weid();
		$_SESSION['WEID']=$weid;
		$vericode=generate_password(8);

	    //2014-2-13最新修改增加函数
		$count = web_admin_get_wechat_pubsubcount($userId, $wid);
		//2014-07-04注释掉只添加一个的判断
		   //2014-06-20新修改函数
		   //通过session获取gweid的值
			
			//start-janeen对wechat_group表进行数据插入，用来判断共享与否等
			$GWEID = gweid();		
			
			$insert=getWechatGroup_insert($GWEID,$userId,$weid,$shared_flag);	
			
			//end-janeen
			//2014-07-15新增修改，初始化新创建的gweid
			foreach($selCheck as $func_name => $func_flag){
				$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$GWEID, $func_name, $func_flag));
			} 
		
			//2014-07-09新增修改
			web_admin_add_wechat_pubsubper_all($weid, $userId, $wid, $wechat_name, $vericode,$busexit,$exireply_content,$GWEID, $wechat_fans,$picUrl,$cuservicepost);
		   
		   //20140313janeen添加用户对应公众号的菜单
			$wechats_info=web_admin_get_wechats_info($wid);
			foreach($wechats_info as $winfo){
				$M_id=$winfo->M_id;
			}
			
			$wechats_user_content=web_user_wechat_menu_content_get($M_id);
			foreach($wechats_user_content as $wconinfo){
				$menu_id=$wconinfo->menu_id;
				$insert=web_user_wechat_menu_add($menu_id,"","",$userId,$weid,$wid,$M_id);
			}			
			
		   
			//初始化公众号功能列表(有可能有多个公众号)
		    web_admin_initfunction($weid);
			
			//2014-07-07新增修改
			header("Location: ".constant("CONF_THEME_DIR")."/init/apply_success.php?beIframe&weid=".$weid);
			exit;
    }
}

//2014-07-09新增修改,如果点击的是公共认证服务号
if($_POST['wechattype']=='pub_svcrz')
{
   
	if( !empty($_POST['wechatpubsvcrz']) ){
        $wechattype="pub_svc";
	    $pubsvcname=$_POST['wechatpubsvcrz'];
		$wid=$_POST['wechatpubsvcrz'];
		
		//2014-07-14新增修改
		$shared_flag = $_POST['share'];  //获取用户添加公众号时的flag	//2014-07-09新增修改，前台用户不能自己填写公用的公众号的微信名称，应该由管理员进行填写
		$active_flag = $_POST['active'];
		//2014-07-11新增修改
		$wechat_name = $_POST['wechatname']; //新添加获取页面上的用户填入的微信名称
		$busexit = $_POST['busexit'];
		$exireply_content = $_POST['exireply_content'];
		//obtain userId
		global $current_user;
		$userId= $current_user->ID;
		$weid = weid();
		$_SESSION['WEID']=$weid;
		$vericode=generate_password(8);

	    //2014-2-13最新修改增加函数
		$count = web_admin_get_wechat_pubsubcount($userId, $wid);
		//2014-07-04注释掉只添加一个的判断
		   //2014-06-20新修改函数
		   //通过session获取gweid的值
			
			//start-janeen对wechat_group表进行数据插入，用来判断共享与否等
			$GWEID = gweid();
			
			$insert=getWechatGroup_insert($GWEID,$userId,$weid,$shared_flag);	
			
			//end-janeen
			//2014-07-15新增修改，初始化新创建的gweid
			foreach($selCheck as $func_name => $func_flag){
				$wpdb->query( $wpdb->prepare("REPLACE INTO ".$wpdb->prefix."wechat_initfunc_info"."(type, value, func_name, func_flag)VALUES (%s, %d, %s, %d)",'GWEID',$GWEID, $func_name, $func_flag));
			} 
		
			//2014-07-09新增修改
			web_admin_add_wechat_pubsubper_all($weid, $userId, $wid, $wechat_name, $vericode,$busexit,$exireply_content,$GWEID,$wechat_fans,$picUrl,$cuservicepost);
		   
		   //20140313janeen添加用户对应公众号的菜单
			$wechats_info=web_admin_get_wechats_info($wid);
			foreach($wechats_info as $winfo){
				$M_id=$winfo->M_id;
			}
			
			$wechats_user_content=web_user_wechat_menu_content_get($M_id);
			foreach($wechats_user_content as $wconinfo){
				$menu_id=$wconinfo->menu_id;
				$insert=web_user_wechat_menu_add($menu_id,"","",$userId,$weid,$wid,$M_id);
			}			
		   
			//初始化公众号功能列表(有可能有多个公众号)
		    web_admin_initfunction($weid);
			
			//2014-07-07新增修改
			header("Location: ".constant("CONF_THEME_DIR")."/init/apply_success.php?beIframe&weid=".$weid);
			exit;
    }
}
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>初始化</title>
		<style type="text/css">
            body,ul,li
			{margin: 0;padding: 0;font: 14px normal "微软雅黑", "宋体", Arial, Helvetica, sans-serif;list-style: none;}
            a{text-decoration: none;color: #000;font-size: 14px;}
			a:hover{text-decoration:none}

            #tabbox{ width:100%; overflow:hidden; /*margin:0 auto;*/}
            .tab_conbox{/*border: 1px solid #999;*/ border-top: none;}
            .tab_con{ display:none;}

            .tabs{height: 32px;border-bottom:1px solid #999;border-left: 1px solid #999;width: 100%;}
            .tabs li{height:31px;line-height:31px;float:left;border:1px solid #999;border-left:none;margin-bottom: -1px;background: #e0e0e0;overflow: hidden;position: relative;}
            .tabs li a {display: block;padding: 0 20px;border: 1px solid #fff;outline: none;}
            .tabs li a:hover {background: #ccc;}	
            .tabs .thistab,.tabs .thistab a:hover{background: #fff;border-bottom: 1px solid #fff;}

            .tab_con {padding:12px;font-size: 14px; line-height:175%;}
			
			.box-h3{ padding:20px 0 0; font-size:16px; font-weight:bold; text-indent:100px; color:#f00;}
            .formbox dt{ font-size:14px;}
            .load-step{ background:#eaeaea; border-top:1px dashed #ccc; padding-left:50px; }
            .load-step dl{ float:left; width:680px; margin-top:20px;}
            .load-step dd{ float:left; width:680px; line-height:25px; font-size:14px; margin:10px 0 10px 0; }
            .load-step dd a{ text-decoration:underline; color:#0069be;}
            .load-step dt{ float:left; width:680px; margin-top:15px;}
        </style>
        <script type="text/javascript">
			top.refersh_account_list();
		<?php if($isTrue){?>
		    alert("该公众号已经添加过，请重新添加");
			window.location.href = 'javascript:history.go(-1)';
        <?php } ?>		
        $(document).ready(function() {
	        jQuery.jqtab = function(tabtit,tab_conbox,shijian) {
		    $(tab_conbox).find("li").hide();
		    $(tabtit).find("li:first").addClass("thistab").show(); 
		    $(tab_conbox).find("li:first").show();
	
		    $(tabtit).find("li").bind(shijian,function(){
		        $(this).addClass("thistab").siblings("li").removeClass("thistab"); 
			    var activeindex = $(tabtit).find("li").index(this);
			    $(tab_conbox).children().eq(activeindex).show().siblings().hide();
			    return false;
		    });
	    };
	    /*调用方法如下：*/
	    $.jqtab("#tabs","#tab_conbox","click");
	    $.jqtab("#tabs2","#tab_conbox2","mouseenter");
});
</script>
</head>

	<body style="height:1500px;">	
		<div id="primary" class="site-content">
			<div id="content" role="main">
				<!--2014-07-17新增修改-->
				<form action="<?php echo constant("CONF_THEME_DIR"); ?>/init/apply_success.php?beIframe&wid=<?php echo $wid;?>&weid=<?php echo $weid;?>" method="post" >
					<div style="font-size:16px; font-weight:bold; margin-top:15px; margin-left:0px;">请将此URL及Token填到微信公众平台中，以完成绑定</div>
					<div>
						<table width="700" height="150" border="0" cellpadding="20px" style=" margin-left:0px;" id="table2">
							<tr>
							    <td><label for="name">URL: </label></td>
								<td width="480"><input type="text" value="<?php echo $url; ?>"class="form-control" id="name" name="site_name1" /></td>
								<td></td>
							</tr>
							<tr>
								<td><label for="name">Token: </label></td>
								<td><input type="text" value="<?php echo $token; ?>" class="form-control" id="name" name="menu_appId" /></td>
								<td><input type="submit" class="btn btn-primary" value="下一步" id="sub3" style="width:70px" /></td>
							</tr>
						</table>
					</div>
					<h3 style="font-size:16px; font-weight:bold; line-height:30px;margin-top:5px;">绑定步聚</h3>
                    <div class="load-step fn-clear">
                        <dl>
							<dd><b>第一步：</b>点击链接，打开并登录公众平台   <a target="_blank" href="http://mp.weixin.qq.com/">http://mp.weixin.qq.com</a></dd>
                        </dl>
                        <dl>
                            <dd><b>第二步：</b>左侧导航栏中选择 <font style="font-weight:bold; color:#f00;">“开发者中心”</font></dd>
                            <dt><img src="../images/wx1.png"></dt>
                        </dl>
                        <dl>
                            <dd><b>第三步：</b>先点击<font style="color:#f00;">“修改配置”</font>进入。</dd>
                            <dt><img src="../images/wx2.png"></dt>
                        </dl>
                        <dl>
                            <dd><b>第四步：</b>输入<font style="color:#f00;">“URL”</font>及<font style="color:#f00;">“Token”</font> 并<font style="color:#f00;">“提交”</font>，完成配置。</dd>
                            <dt><img src="../images/wx3.png"></dt>
                        </dl>
                    </div>
				</form>
			</div>
		</div>
	</body>
</html>

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>