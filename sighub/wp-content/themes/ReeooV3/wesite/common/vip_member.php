<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include_once 'dbaccessor.php';

/**
*@author: janeen
*@version: add by janeen 20140430
*/
$weid =  $_GET['WEID'];
$mid = $_SESSION['mid'];
$auth = $_SESSION['auth'];
if(empty($_GET['fromuser'])){   
	$fromuser = $_SESSION['fromuser'];
}else{ //从多图文进
    $fromuser = $_GET['fromuser'];
	$_SESSION['fromuser']=$fromuser;
}//end


/**
*@description: login check
*@author: janeen
*@version: add by janeen 20140430
*/
$memberinfo=null;
if(!empty($fromuser)){//通过fromuser拿到会员信息	
	$memberinfo =  web_admin_member($weid, $fromuser);
}
 if(empty($memberinfo)&&($mid!=null)){//通过mid拿到会员信息
	$memberinfo =  web_admin_member_mid($mid,$weid);
	foreach($memberinfo as $minfo){
	   $au_password=$minfo->password;
	}
	if($auth!= md5($mid.$au_password."weauth3647668")){
		$memberinfo=null;
	}	
} //end

if(!empty($memberinfo))	{
	foreach($memberinfo as $member){
		$realname = $member->realname;
		$nickname = $member->nickname;
		$point = $member->point;
		$level = $member->level;
		$rtime = $member->rtime;
		$mobilenumber = $meber->mobilenumber;
		$billingplan = $member->billing_plan;
		$regtype = $member->reg_type;
		$memid=$member->mid;
		//退出登录提交到本页面，memid拿不到，若此时不是通过login进的这个页面mid是没有的，所以有如下处理
		$_SESSION['mid']=$memid;
		$mid = $_SESSION['mid'];
	}
}

/**
*@description: logout
*@author: janeen
*@version: add by janeen 20140430
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	$exit=false;
	
    //如果现有的fromuser不为空则清空现有的fromuser
	if(!empty($fromuser)){	
		unset($_SESSION['fromuser']);
		web_admin_update_fromuser($mid,"");
	}
	unset($_SESSION['mid']);
	unset($_SESSION['auth']);
	$exit=true;
	$weid=$_POST['WEID'];
}
?>
<?php if($exit){?>
	<script>
		location.href="vip_login.php?WEID=<?php echo $weid;?>";			
	</script>
<?php } ?>
		 
<?php
//如果拿到会员信息则显示
if(!empty($memberinfo))
{ ?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
		<link rel="stylesheet" href="../../css/wsite.css" />
		<link rel="stylesheet" href="../../css/vip.css" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css">
		<script src="../../js/jquery.min.js"></script>
		<script src="../../js/bootstrap.min.js"></script>
		<title><?php bloginfo('name'); ?></title>
	</head>
		
	<script>
		function vipexit(){			
			if(confirm("确认退出?")){
			   $("#vipexit").submit();
			}					   
		}
	</script>
<body>
<div>
	<div class="banner">
		<div class="user">
			<span class="username"><?php echo $nickname;?></span>
			<span class="usergroup"><?php echo $level;?></span>
			<div class="credit user-list">
				<span><font color="yellow">积分</font>:</span><?php echo $point;?>
			</div>
		</div>
		<div class="avatar"><img src="../../images/noavatar_middle.gif"></div>
		<div class="banner_footer">
			<div class="vipbutton">
				<a class="btn btn-warning" href="vip_detail.php?WEID=<?php echo $weid;?>">我的资料</a>
			</div>
		</div>
    </div>
	<form id="vipexit" action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post">
		<div class="modal-footer">
			<input type="hidden" name="WEID" value="<?php echo $weid; ?>"> 
			<a href="#"  onclick="vipexit();"   class="vipbtn" data-dismiss="modal" aria-hidden="true">退出登录</a>
		</div>
	</form>
</div>
</body>
</html>
<?php
 }else{ ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script>
			alert("请登录");
			location.href="vip_login.php?WEID=<?php echo $weid;?>&redirect_url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>";
		</script>
	</head>
</html>
<?php } ?>
