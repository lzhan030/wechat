<?php 

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	

//get_header(); 

   include '../../wesite/common/dbaccessor.php';
   include '../../wesite/common/web_constant.php';
	$range=$_POST['range'];
	//echo "$range";
	$indata=$_POST['indata'];
	//echo "$indata";
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
	location.href=url+'/wechat/noticemanage/notice_list.php?beIframe&Ipad='+idat+'&range='+rang;
<?php
break;
case 'notice_title':
?>	
	location.href=url+'/wechat/noticemanage/notice_list.php?beIframe&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;
case 'notice_rights':
?>	
	location.href=url+'/wechat/noticemanage/notice_list.php?beIframe&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;
case 'notice_date':
?>	
	location.href=url+'/wechat/noticemanage/notice_list.php?beIframe&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;
case 'notice_publisher':
?>
	location.href=url+'/wechat/noticemanage/notice_list.php?beIframe&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;
default:
?>
location.href=url+'/wechat/noticemanage/notice_list.php?beIframe&Ipad='+idat;
<?php
} 
?>
</script>