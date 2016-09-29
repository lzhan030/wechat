<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once ('./wp-content/themes/ReeooV3/wesite/common/random.php');

//当添加公众号时需要执行。。。。此处还不明确，需要最终修改
//2014-07-11新增修改，添加公众未认证订阅号
if($_POST['wechattype']=='pub_subnrz')
{
   if( !empty($_POST['site_name']) ){

        //$wechattype=$_POST['wechattype'];
		//2014-07-11新增修改
		$wechattype="pub_sub";
	    $sitename=$_POST['site_name'];
	
		//$wechat_auth = $_POST['auth']; //新添加获取页面上的微信公众号的认证情况
		//2014-07-11新增修改
		$wechat_auth = 0; //未认证对应的认证情况为0
		//2014-07-09新增修改
		//$wechat_name = $_POST['dispname'];
		//2014-07-10新增修改	
		//$demomenu=$_POST['demomenu1'];
		
		//2014-07-08新增修改如果是认证的订阅号就有菜单功能，就可以填入manuappid和menuappsc
		//2014-07-11新增修改
		/* if($wechat_auth == 1)  
		{
		    $menuappid=$_POST['menu_appId1'];
			$menuappsc=$_POST['menu_appSc1'];
		} */
		
		/*$busexit=$_POST['busexit'];
		if($_POST["exireply"]==0){						
			$prompt_type="business";
			$prompt_content="";
		}else{
			$prompt_type="text";
			$prompt_content=$_POST['exireply_content'];
		}	*/

		$token=generate_password(10);
		
		//obtain userId
		global $current_user;
		//echo $current_user->ID;
		$userId=$current_user->ID;
		//生成随机数
		$hash = random(5);
		//echo "这是hash的值".$hash;
		$weid = weid();
		//echo "这是weid".$weid;
		//$_SESSION['WEID']=$weid;

		$vericode=generate_password(8);
		//echo "这".$vericode;
	    //web_admin_add_wechatD($userId, $wechattype, $sitename, $token);
	    //2014-2-13最新修改增加函数
		//$wid=web_admin_add_wechat_pubsub1($hash, $sitename, $wechattype, $token, $weid, $userId, $vericode);
		//2014-6-23最新修改增加函数
		
		//$wid=web_admin_add_wechat_pubsub1($hash, $sitename, $wechattype, $wechat_auth, $token, $weid, $userId, $vericode);
		//2014-07-09新增修改
		$wid=web_admin_add_wechat_pubsub1($hash, $sitename, $wechattype, $wechat_auth, $token, $weid, $userId, $vericode);
		//2014-07-10新增修改
		//web_admin_add_wechats_info($wid,$busexit,$prompt_type,$prompt_content,0); 		
		web_admin_add_wechats_info($wid,"","","",0); 
		//初始化admin公众号功能列表
		//web_admin_initfunction($weid);
		web_admin_pub_initfunction($wid); //wechat_initfunc_info这张表结构改了
		
	    $isSubmit = TRUE;
	    $url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoinPublic.php?hash='.$hash;
		$url=preg_replace('|^https://|', 'http://', $url);
	    //$token = 'wexinCourse';
		
	    //web_admin_add_wechatF($userId, $wechattype, $sitename, $menuappid, $menuappsc, $token);
    }
	
	
}

//当添加公众号时需要执行。。。。此处还不明确，需要最终修改
//2014-07-11新增修改，添加公众认证订阅号
if($_POST['wechattype']=='pub_subrz')
{
   if( !empty($_POST['site_name1']) ){
        
        //$wechattype=$_POST['wechattype'];
		//2014-07-11新增修改
		$wechattype="pub_sub";
	    $sitename=$_POST['site_name1'];
		
		//$wechat_auth = $_POST['authfw']; //新添加获取页面上的微信公众号的认证情况
		//2014-07-11新增修改
		$wechat_auth = 1; //认证对应的认证情况为1
		
		//2014-07-09新增修改
		//$wechat_name = $_POST['dispname1'];
		
		//$busexit=$_POST['busexit1'];
		$demomenu=$_POST['demomenu'];
		/*
		if($_POST["exireply1"]==0){						
			$prompt_type="business";
			$prompt_content="";
		}else{
			$prompt_type="text";
			$prompt_content=$_POST['exireply_content1'];
		}*/	
		$menuappid=trim($_POST['menu_appId']);
		$menuappsc=trim($_POST['menu_appSc']);
		$token=generate_password(10);
		
		//obtain userId
		global $current_user;
		//echo $current_user->ID;
		$userId=$current_user->ID;
		//生成随机数
		$hash = random(5);
		//echo "$hash";
		$weid = weid();
		//echo "$weid";
		$_SESSION['WEID']=$weid;
		$vericode=generate_password(8);
	   
	  
		$wid=web_admin_add_wechat_pubsvc1($hash, $sitename, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc, $weid, $userId, $vericode);
		//web_admin_add_wechats_info($wid,$busexit,$prompt_type,$prompt_content,$demomenu);
		web_admin_add_wechats_info($wid,"","","",$demomenu);
		//初始化admin公众号功能列表
		//web_admin_initfunction($weid);
		web_admin_pub_initfunction($wid);  //wechat_initfunc_info这张表结构改了
		
	    $isSubmit = TRUE;
	    $url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoinPublic.php?hash='.$hash;
		$url=preg_replace('|^https://|', 'http://', $url);
	    //$token = 'wexinCourse';
		if(($menuappid=='')&&($menuappsc==''))
		{
		    $menuinfo="您没有输入菜单信息，没有自定义菜单功能";
		}
    }
	
	
}

//当添加公众号时需要执行。。。。此处还不明确，需要最终修改
//2014-07-11新增修改，添加公众未认证服务号
if(($_POST['wechattype']=='pub_svcnrz') || ($_POST['wechattype']=='pub_svcrz'))
{
   if( !empty($_POST['site_name1']) ){
        
        //$wechattype=$_POST['wechattype'];
	    $sitename=$_POST['site_name1'];
		
		$wechat_auth = $_POST['authfw']; //新添加获取页面上的微信公众号的认证情况
		//2014-07-11新增修改
		if($_POST['wechattype']=='pub_svcnrz')
		{
		    //2014-07-11新增修改
		    $wechat_auth = 0;
			$wechattype="pub_svc";
		}
		else
		{
		    $wechat_auth = 1;
			$wechattype="pub_svc";
		}
		//2014-07-09新增修改
		//2014-07-11新增修改,去掉站点名称，商家在前台设置
		//$wechat_name = $_POST['dispname1'];
		
		//$busexit=$_POST['busexit1'];
		$demomenu=$_POST['demomenu'];

		/*if($_POST["exireply1"]==0){						
			$prompt_type="business";
			$prompt_content="";
		}else{
			$prompt_type="text";
			$prompt_content=$_POST['exireply_content1'];
		}*/	
		$menuappid=trim($_POST['menu_appId']);
		$menuappsc=trim($_POST['menu_appSc']);
		$token=generate_password(10);
		
		//obtain userId
		global $current_user;
		//echo $current_user->ID;
		$userId=$current_user->ID;
		//生成随机数
		$hash = random(5);
		//echo "$hash";
		$weid = weid();
		//echo "$weid";
		$_SESSION['WEID']=$weid;
		$vericode=generate_password(8);

	    //2014-2-13最新修改增加函数
		//$wid=web_admin_add_wechat_pubsvc1($hash, $sitename, $wechattype, $token, $menuappid, $menuappsc, $weid, $userId, $vericode);
		 //2014-6-23最新修改增加函数
		//$wid=web_admin_add_wechat_pubsvc1($hash, $sitename, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc, $weid, $userId, $vericode);
		//2014-07-09新增修改
		$wid=web_admin_add_wechat_pubsvc1($hash, $sitename, $wechattype, $wechat_auth, $token, $menuappid, $menuappsc, $weid, $userId, $vericode);
		//web_admin_add_wechats_info($wid,$busexit,$prompt_type,$prompt_content,$demomenu);
		web_admin_add_wechats_info($wid,"","","",$demomenu);
		//初始化admin公众号功能列表
		//web_admin_initfunction($weid);
		web_admin_pub_initfunction($wid);  //wechat_initfunc_info这张表结构改了
		
	    $isSubmit = TRUE;
	    $url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoinPublic.php?hash='.$hash;
		$url=preg_replace('|^https://|', 'http://', $url);
	    //$token = 'wexinCourse';
		if(($menuappid=='')&&($menuappsc==''))
		{
		    $menuinfo="您没有输入菜单信息，没有自定义菜单功能";
		}
    }
	
}



get_header(); 
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
		<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
        <script type="text/javascript">
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
	<body style="height:3500px;">	
		<div id="primary" class="site-content">
			<!--<div id="content" role="main">-->
			<div>
				<form action="?admin&page=adminmenu/menu_public_create&wid=<?php echo $wid;?>&demomenu=<?php echo $demomenu?>" method="post" > 
					<div>
						<div class="main-title">
							<div class="title-1">当前位置：帐户信息初始化 > <font class="fontpurple">微信绑定 </font>
							</div>
						</div>
						<div class="bgimg"></div>
						
						<div id="tabbox">
	                        <!--<h1>简洁jQuery滑动门插件 单击切换演示</h1>-->
                            <ul class="nav nav-tabs" id="tabs" style="width:98%;margin-top:10px">
                                <li class="active"><a href="javascript:void(0);" data-toggle="tab">手动绑定</a></li>
                                <!--li><a href="javascript:void(0);" data-toggle="tab">自动绑定</a></li-->
                            </ul>
                            <ul class="tab_conbox" id="tab_conbox">
                                <li class="tab_con">
                                
								 <div style="font-size:16px; font-weight:bold; margin-top:15px; margin-left:50px;">请将此URL及Token填到微信公众平台中，以完成绑定</div>
						            <div>
							            <table width="650" height="150" border="0" cellpadding="20px" style=" margin-left:18px;" id="table2">
								
									        <tr>
										        <td><label for="name">URL: </label></td>
										        <td width="480"><input type="text" value="<?php echo $url; ?>"class="form-control" id="name" name="site_name1" /></td>
												<td>  </td>
											</tr>
									        <tr>
										        <td><label for="name">Token: </label></td>
										        <td><input type="text" value="<?php echo $token; ?>" class="form-control" id="name" name="menu_appId" /></td>
												<td><input type="submit" class="btn btn-primary" value="下一步" id="sub3"
										style="margin-left:20px; width:70px" /></td>
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
                                            <dt><img width="721" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/images/w1.jpg"></dt>
                                        </dl>
                                        <dl>
                                            <dd><b>第三步：</b>先点击<font style="color:#f00;">“修改配置”</font>进入。</dd>
                                            <dt><img width="750" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/images/w2.jpg"></dt>
                                        </dl>
                                        <dl>
                                             <dd><b>第四步：</b>输入<font style="color:#f00;">“URL”</font>及<font style="color:#f00;">“Token”</font> 并<font style="color:#f00;">“提交”</font>，完成配置。</dd>
                                             <dt><img src="<?php echo home_url();?>/wp-content/themes/ReeooV3/images/w3.jpg"></dt>
                                        </dl>
                                        <dl>
                                            <dd><b>第五步：</b>关注公众帐号，输入<font style="color:#f00;">“帮助”</font>。返回信息则表示绑定成功。</dd>
                                            <dt><!--img src="../images/s9.jpg"--></dt>
                                        </dl>
                                    </div>
                                </li>
            
                                <li class="tab_con">
        	                    
                                </li>
    
                            </ul>
   
                        </div>
					
			</form>
			
			</div>
		</div>
	</body>
</html>

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>