<?php 

global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	

//get_header(); 

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
require_once './wp-content/themes/ReeooV3/wesite/common/web_constant.php';

	$range=$_POST['range'];	
	$indata=$_POST['indata'];
?>
<script type="text/javascript">

	var idat="<?php echo $indata ?>";
	var rang="<?php echo $range ?>";
	var flag=1;
<?php switch($range){
case 'all':
?>	
	location.href='?admin&page=pubwechatmanage&Ipad='+idat+'&range='+rang;
<?php
break;
case 'wechat_nikename':
?>	
	location.href='?admin&page=pubwechatmanage&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;
default:
?>
    location.href='?admin&page=pubwechatmanage&Ipad='+idat;
<?php
} 
?>
</script>