<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}

include '../../wesite/common/dbaccessor.php';
include '../../wesite/common/web_constant.php';
$range=$_GET['range'];
//echo "$range";
$indata=$_GET['indata'];
/**
*@function:判断会员是否审批
*/
$vipauditinfo=web_admin_usechat_info_group($_SESSION['GWEID']);
foreach($vipauditinfo as $vaudit){
	$vipaudit=$vaudit->wechat_vipaudit;
}

$filename="会员.csv";//先定义一个excel文件

header("Content-Type: application/vnd.ms-execl"); 
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename"); 
header("Pragma: no-cache"); 
header("Expires: 0");

//我们先在excel输出表头，当然这不是必须的
echo iconv("utf-8", "gb2312", "会员编号").",";
echo iconv("utf-8", "gb2312", "微信昵称").",";
echo iconv("utf-8", "gb2312", "真实姓名").",";
echo iconv("utf-8", "gb2312", "联系方式").",";//注意这个要换行
echo iconv("utf-8", "gb2312", "邮箱地址").",";
echo iconv("utf-8", "gb2312", "审批状态").",";
echo iconv("utf-8", "gb2312", "注册时间")."\n";
//如果输入为空或者选择全部，则导出全部会员信息
if(empty($indata) || $range=="all" || empty($range)){
	$vips=web_admin_list_vmember_group($_SESSION['GWEID']);
}else{
	$vips=web_admin_list_selectvmember_group($_SESSION['GWEID'],$indata,$range);
}
foreach($vips as $vip){

echo iconv("utf-8", "gb2312", $vip->mid).",";
echo iconv("utf-8", "gb2312", $vip->nickname).",";
echo iconv("utf-8", "gb2312", $vip->realname).",";
echo iconv("utf-8", "gb2312", $vip->mobilenumber).",";
echo iconv("utf-8", "gb2312", $vip->email).",";
if($vipaudit!='0'){
	if($vip->isaudit=='0'){
		echo iconv("utf-8", "gb2312", "拒绝").",";
	}else if($vip->isaudit=='1'){
		echo iconv("utf-8", "gb2312", "审批通过").",";
	}else if($vip->isaudit=='2'){
		echo iconv("utf-8", "gb2312", "审批中").",";
	}
}else{
	echo iconv("utf-8", "gb2312", "审批通过").",";
}
echo iconv("utf-8", "gb2312", $vip->rtime)."\n";

}
?>
