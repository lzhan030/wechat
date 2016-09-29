<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include_once 'dbaccessor.php';
//global $wpdb;
//$userid = $_GET['id'];

//$user = get_userdata( $userid ); 

if(!isset($_GET['weid'])||!isset($_GET['fromuser']))
{
    $weid = $_SESSION['WECID'];
	$fromuser = $_SESSION['fromuser'];
}
else
{
    $weid =  $_GET['weid'];
    $fromuser = $_GET['fromuser'];
}


$countnumber = web_admin_member_count($weid, $fromuser);
$memberinfo =  web_admin_member($weid, $fromuser);
foreach($countnumber as $memberNumber){
		$countmember=$memberNumber->memberCount;
	}
//echo $countmember;查看该用户是否是会员
foreach($memberinfo as $member){
		$realname = $member->realname;
		$nickname = $member->nickname;
		$point = $member->point;
		$level = $member->level;
		$rtime = $member->rtime;
		$mobilenumber = $meber->mobilenumber;
		$billingplan = $member->billing_plan;
		$regtype = $member->reg_type;
	}
?>
<?php

if($countmember)
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
		<title>个人中心</title>
		
		<style>
		
		</style>
	</head>
<body>
<div>
	<div class="banner">
		<div class="user">
			<span class="username"><?php echo $nickname;?></span>
			<span class="usergroup"><?php echo $level;?></span>
			<div class="credit user-list"><span><font color="yellow">积分</font>:</span><?php echo $point;?></div>
			<!--<div class="money user-list"><span>金额</span>元</div>-->
		</div>
		<div class="avatar"><img src="../../images/noavatar_middle.gif"></div>
		<div class="banner_footer">
			<div class="vipbutton">
				<a class="btn btn-warning" href="vip_detail.php?weid=<?php echo $weid;?>&fromuser=<?php echo $fromuser;?>">我的资料</a>
				<!--<a class="btn btn-success" href="mobile.php?act=module&amp;do=charge&amp;name=member&amp;weid=1#qq.com#wechat_redirect">充值</a>-->
			</div>
		</div>
    </div>
	
	<!--<div id="footer">©默认公众号</div>-->
	
	<div class="modal-footer"><a href="#" target="preview" class="vipbtn">首页</a><a href="#" target="preview" class="vipbtn">个人中心</a><a href="#" class="vipbtn" data-dismiss="modal" aria-hidden="true">关闭</a></div>
	
</div>
</body>
</html>
<?php
    }
	
else if(($fromuser==null)&&(!$countmember)){

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script>
        alert("请从微信公众号处进行访问并注册为会员");
	</script>

<?php
	//include 'vip_register.php';
?>
		
		<script>
		  <?php if(isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER']))
		{?>
		   location.href="<?php echo $_SERVER['HTTP_REFERER']; ?>";
		<?php }else
		{?>
		   location.href="http://www.xiaohuivip.com";
		<?php }?>
		</script>
			</head>
<body>
</body>
</html>
<?php
	} 
	
else{
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script>
        alert("您还不是会员,请点击确定进行会员注册");
	</script>

<?php
	//include 'vip_register.php';
?>
		
		<script>
		    location.href="vip_register.php?weid=<?php echo $weid;?>&fromuser=<?php echo $fromuser;?>";
		</script>
			</head>
<body>
</body>
</html>
<?php
	} 
?>
