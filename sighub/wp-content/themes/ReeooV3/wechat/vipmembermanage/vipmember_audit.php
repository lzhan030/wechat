<?php
    global  $current_user;
    include '../../wesite/common/dbaccessor.php';
    include '../common/wechat_dbaccessor.php';
    //判断是否是分组管理员的公众号,分组管理员不需要进行此功能的check
	$groupadmincount = is_superadmin($_SESSION['GWEID']);
	if($groupadmincount == 0) 
	   include 'vmember_permission_check.php';
    include '../../wesite/common/web_constant.php';
  
	
	/**
	*@function:判断会员是否审批
	*/
	$vipauditinfo=web_admin_usechat_info_group($_SESSION['GWEID']);
	foreach($vipauditinfo as $vaudit){
		$vipaudit=$vaudit->wechat_vipaudit;
	}
	/*会员审批全局设置*/
	if(isset($_POST['setAudit'])){
		$auditStatus=$_POST['setAudit'];
		$status=web_admin_update_usechat_audit($auditStatus,$_SESSION['GWEID']);
		if($status==false){
			$hint = array("status"=>"error","message"=>"设置失败");
		}else{
			$hint = array("status"=>"success","message"=>"设置成功");
		}
		echo json_encode($hint);
	}
	/*设置会员审批状态*/
	if(isset($_POST['setVmAudit'])){
		$vmauditStatus=$_POST['setVmAudit'];
		$vmmid=$_POST['mid'];
		$status=web_admin_update_member_audit($vmmid,$vmauditStatus);
		if($status==false){
			$hint = array("status"=>"error","message"=>"设置失败");
		}else{
			$hint = array("status"=>"success","message"=>"设置成功");
		}
		echo json_encode($hint);
	}
	
?>