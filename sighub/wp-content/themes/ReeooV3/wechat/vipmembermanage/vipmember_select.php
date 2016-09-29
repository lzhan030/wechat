<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	

	include '../../wesite/common/dbaccessor.php';
   include '../../wesite/common/web_constant.php';
	$range=$_POST['range'];
	$indata=$_POST['indata'];
	$next_url=constant("CONF_THEME_DIR");
?>
<script type="text/javascript">

	var idat="<?php echo $indata ?>";
	var url="<?php echo $next_url ?>";
	var rang="<?php echo $range ?>";
	var flag=1;
<?php switch($range){
case 'all':
?>	
	location.href=url+'/wechat/vipmembermanage/vipmember_list.php?beIframe&Ipad='+idat+'&range='+rang;
<?php
break;
case 'realname':
?>	
	location.href=url+'/wechat/vipmembermanage/vipmember_list.php?beIframe&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;
case 'nickname':
?>	
	location.href=url+'/wechat/vipmembermanage/vipmember_list.php?beIframe&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;
case 'mobilenumber':
?>
	location.href=url+'/wechat/vipmembermanage/vipmember_list.php?beIframe&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;
case 'isaudit':
?>
	location.href=url+'/wechat/vipmembermanage/vipmember_list.php?beIframe&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;

default:
?>
location.href=url+'/wechat/vipmembermanage/vipmember_list.php?beIframe&Ipad='+idat;
<?php
} 
?>
</script>