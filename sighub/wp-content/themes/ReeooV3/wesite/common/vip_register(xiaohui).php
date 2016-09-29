<?php
session_start();
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

include_once 'dbaccessor.php';

//global $wpdb;
//$userid = $_GET['id'];

//$user = get_userdata( $userid ); 

$uAgent = $_SERVER['HTTP_USER_AGENT']; 
//echo $uAgent;
$osPat = "android|UCWEB|iPhone|iPad|BlackBerry|Symbian|Windows Phone|hpwOS"; 
if(preg_match("/($osPat)/i", $uAgent ))  
{ 
    //echo "来者手机终端"; 
	$regtype = 'Mobile';
} 
else 
{ 
    //echo "来者pc端"; 
	$regtype = 'Web';
}


global $wpdb;

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


if( isset($_POST['user_displayname']) ){

	$realname = $_POST['user_realname'];
	$nickname = $_POST['user_displayname'];
	$mobilenumber = $_POST['user_mobile'];	
	$email = $_POST['user_email'];	
	
	$countnumber = web_admin_member_count($weid, $fromuser);

	foreach($countnumber as $memberNumber){
		$countmember=$memberNumber->memberCount;
	}

	if(!empty($fromuser))
    {
		if($countmember == 0)
		{
		   if(web_admin_create_member($weid, $fromuser, $realname, $nickname, $mobilenumber, $email, $regtype)) 
		   {
			  $flag = true;
			  $countmember = false;
			}
		}
		else
			$flag1 = true;
		
    }
	else
	{
	   ?>
	   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<script>
				alert("注册失败，请从微信公众号登陆");
			</script>
		</head>
		</html>
		
		<?php
	}
}	

$countnumber = web_admin_member_count($weid, $fromuser);
foreach($countnumber as $memberNumber){
		$countmember=$memberNumber->memberCount;
	}

 
?>

<?php

if(($fromuser!=null)&&(!$countmember))
{ 
?>


<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
		<link rel="stylesheet" href="../../css/wsite.css" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css">
		<script src="../../js/jquery.min.js"></script>
		<script src="../../js/bootstrap.min.js"></script>
		<title>会员申请</title>
		
		
		<?php 
		if(0&&$flag1)
		{?>
		   <script>
	         location.href="vip_member.php?weid=<?php echo $weid;?>&fromuser=<?php echo $fromuser;?>";
	       </script>
		 <?php
		}
		?>
		<script>
	   function vipregister()
	   {
	        
			   if($("#user_realname").val()=="")
				{
				   alert("真实姓名是必填项");
				}	
				else if($("#user_displayname").val()=="")
				{
				   alert("昵称是必填项");
				}
				else if($("#user_mobile").val()=="")
				{
					alert("手机号是必填项");
						
				}
				else if($("#user_email").val()=="")
				{
					alert("Email是必填项");
				}
				else if($("#user_mobile").val()!="" && $("#user_email").val()!="")
				{
					var reg =/^0{0,1}(13[0-9]|15[0-9])[0-9]{8}$/; 
					var myreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
					if(!reg.test($("#user_mobile").val()))
					{
						 alert("您的手机号码不正确，请重新输入！");
					}
					else if(!myreg.test($("#user_email").val()))
					{
					     alert("您的邮箱格式不正确，请重新输入！");
					}
					else
				    {
				        $("#vipregister").submit();
				    }
			
					
				}
				

	   }
		</script>
	</head>
<div>
	<form id="vipregister" action="" method="post">
	
	<div class="main-title">
		<div class="title-1">会员申请 > <font class="fontpurple">会员信息填写 </font>
		</div>
	</div>
	<div class="bgimg"></div>
	<?php
		//if( isset($_POST['user_nicename']) ){
		?>
		<!--<div style="background-color:#ffffe0; border-color:#e6db55; width:320px; font-size:18px; margin-left:180px;"><p style="padding-left:10%;"><?php //if(isset($info)) echo $info;?><br>
		</p></div>-->
		<?php
		//} ?>
	<table width="95%" height="150" border="0" cellpadding="10px" style="margin-left: 5%; margin-top:15px;" id="table2">
		<tbody>
			<tr>
				<td><label for="user_realname">真实姓名: </label></td>
				<td width="65%"><input type="text" value="" class="form-control" id="user_realname" name="user_realname" ></td>
				<td></td>
			</tr>
			<tr>
				<td><label for="user_nikename">昵称: </label></td>
				<td width="65%"><input type="text" value="" class="form-control" id="user_displayname" name="user_displayname"></td>
				<td></td>
			</tr>
			
			<tr>
				<td><label for="user_mobile">手机号: </label></td>
				<td><input type="text" value="" class="form-control" id="user_mobile" name="user_mobile"></td>
			</tr>
			
			<tr>
				<td><label for="user_email">Email: </label></td>
				<td><input type="text" value="" class="form-control" id="user_email" name="user_email"></td>
			</tr>
			
		</tbody>
	</table>
	
	
	<div style="margin-top:3%; margin-left:35%;">
	    <input type="button" onclick="vipregister();" class="btn btn-primary" value="申请" id="checkaccount" style="width:70px">
	    <input type="button" onclick="location.href='?admin&page=usermanage'" class="btn btn-default" value="取消" id="sub3" style="width:70px; margin-left:20px;">
	</div>
	</form>
</div>
</html>
<?php
    }
	else if(($fromuser!=null)&&($countmember)){
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<?php 
	if($_SERVER['REQUEST_METHOD']=="GET")
	{?>
    <script>
        alert("您已经是会员,请点击确定查看个人信息");
	</script>
	 <?php
	}
	?>
	<?php 
	if($flag&&$_SERVER['REQUEST_METHOD']=="POST")
	{?>
	   <script>
		 alert("会员申请成功");
		 location.href="vip_member.php?weid=<?php echo $weid;?>&fromuser=<?php echo $fromuser;?>";
	   </script>
	 <?php
	}
	?>

<?php
	//include 'vip_register.php';
?>
		
		<script>
		    location.href="vip_member.php?weid=<?php echo $weid;?>&fromuser=<?php echo $fromuser;?>";
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
        alert("请从微信公众号处进行访问并注册为会员");
	</script>

<?php

?>	
	<script>
		//location.href="http://www.xiaohuivip.com";
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
?>