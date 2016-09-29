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

get_header();
 ?>
 
<?php

    include '../../wesite/common/dbaccessor.php';
    include '../common/wechat_dbaccessor.php';
    //判断是否是分组管理员的公众号,分组管理员不需要进行此功能的check
	$groupadmincount = is_superadmin($_SESSION['GWEID']);
	if($groupadmincount == 0) 
	   include 'vmember_permission_check.php';
    include '../../wesite/common/web_constant.php';
    $flag=$_REQUEST['flag'];
    $f=$_REQUEST["f"];
    if($f==null){$f=$flag;}
	$indata=$_REQUEST['Ipad'];
    $in=$_REQUEST["in"];
    if($in==null){$in=$indata;}
    $indata=$in;
    $rg=$_REQUEST['range'];
	$r=$_REQUEST["r"];
	if($r==null){$r=$rg;}
	/**
	*@function:判断会员是否审批
	*/
	$gweid = $_SESSION['GWEID'];

	//如果该gweid处于分组管理员的虚拟号下，并且该虚拟号是共享的状态，则列出的是虚拟号下的会员,否则还是自身的会员
	$gweid = virtualgweid_open($gweid);

	$vipauditinfo=web_admin_usechat_info_group($gweid);
	foreach($vipauditinfo as $vaudit){
		$vipaudit=$vaudit->wechat_vipaudit;
	}

	
?>

 
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Membermanage" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<style>
		.panel{border-radius: 0px;-webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05);box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
		.alert{border-radius: 0px;}
		.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
	</style>
	</head>
	<body>
	   
		<div class="main_auto">
			<div class="main-title">
				<div class="title-1">当前位置：会员管理 ><font class="fontpurple">会员列表</font>
				</div>
			</div>
			<div class="bgimg"></div>
			<div class="panel panel-default" style="width:100%;">
				<div class="panel-heading">会员管理链接</div>
				<table class="table table-striped" width="800" border="0" align="center">
				<tbody>
					<tr>
						<td colspan=2 scope="col" width="100" align="left" >
						<span class="control-label" style="font-size: 14px;line-height:30px" for="inputInfo">会员注册URL</span>
						<td colspan="2" scope="col" width="400"><input class="sltfield" size="100" readOnly="true" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_register.php?gweid=<?php echo $gweid;?>
						"></input></td>
					</tr>
					<tr>
						<td colspan=2 scope="col" width="100" align="left" >
						<span class="control-label" style="font-size: 14px;line-height:30px" for="inputInfo">会员登陆URL</span>
						<td colspan="2" scope="col" width="400"><input class="sltfield" size="100" readOnly="true" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_login.php?gweid=<?php echo $gweid;?>
						"></input></td>
					</tr>
					<tr>
						<td colspan=2 scope="col" width="100" align="left" >
						<span class="control-label" style="font-size: 14px;line-height:30px" for="inputInfo">会员中心URL</span>
						<td colspan="2" scope="col" width="400"><input class="sltfield" size="100" readOnly="true" value="<?php bloginfo('template_directory'); ?>/wesite/common/vip_detail.php?gweid=<?php echo $gweid;?>
						"></input></td>
					</tr>
				</table>
			</div>
			<div class="panel panel-default" style="width:100%;">
				<table class="table table-striped" width="800" border="1" align="center">
				<tbody>
					<tr>
						<td colspan=2 scope="col" width="100" align="left" >
						<span class="control-label" style="font-size: 14px;" for="inputInfo">会员审批设置</span>
						<td colspan="2" scope="col" width="400">
							<input type="radio" name="vipaudit" id="isvipaudit"  onclick="set_vipaudit('1')" style="vertical-align:middle;  margin-bottom:5px;margin-right:2px;" <?php if($vipaudit=='1') echo 'checked' ?>>会员审批</input>
							<input type="radio" name="vipaudit" id="isnovipaudit" onclick="set_vipaudit('0')" style="vertical-align:middle;  margin-bottom:5px;margin-left:27px;margin-right:2px;" <?php if($vipaudit=='0') echo 'checked' ?>>会员不审批</input>					
						</td>
					</tr>
				</table>
			</div>
		<form name ="content" onSubmit="return validateform()" action="<?php echo constant("CONF_THEME_DIR"); ?>/wechat/vipmembermanage/vipmember_select.php?beIframe" method="post" enctype="multipart/form-data">		
			<div class="panel panel-default"style="width: 100%;">
				<div class="panel-heading">会员列表</div>
				<table class="table table-striped" width="800" border="1" align="center">
				<tbody>
					<tr>
						<td colspan=6 scope="col" width="100" align="left" >
							<select id="range" name="range" class="sltfield">
								<option value="">请选择
								<option value="all">全部</option>
								<option value="realname">真实姓名</option>
								<option value="nickname">微信昵称</option>
								<option value="mobilenumber">联系方式</option>
								<?php if($vipaudit!='0'){?><option value="isaudit">审批状态</option><?php } ?>
							</select>
							<input id="indata" name="indata" value="" class="sltfield" />
							<input id="search" class="btn btn-info btn-sm" type="submit" value="查询"/>
							<input type="button" class="btn btn-success btn-sm" onclick="ExportLogExcel()" value="导出" />
						</td>
					</tr>
					<tr>
						<td scope="col" width="90" align="center" style="font-weight:bold">会员编号</td>
						<td scope="col" width="60" align="center" style="font-weight:bold">微信昵称</td>
						<td scope="col" width="60" align="center" style="font-weight:bold">会员真实姓名</td>
						<td scope="col" width="100" align="center" style="font-weight:bold">会员联系方式</td>
						<td scope="col" width="200" align="center" style="font-weight:bold">会员审批状态</td>
						<td scope="col" width="200" align="center" style="font-weight:bold">操作</td>
					</tr>
						<?php
						if($f==1){
						//获取当前用户下所有的会员
						$vmember_list=web_admin_list_selectvmember_group($gweid,$in,$r);
						$pagesize=5; //设定每一页显示的记录数						
						//-----------------------------------------------------------------------------------------------//
						//分页逻辑处理
						$vmembersCount = web_admin_count_selectvmember_group($gweid,$in,$r);
						foreach($vmembersCount as $vmembersnumber){
							 $countnumber=$vmembersnumber->memberCount;
							 
						}
						$pages=intval($countnumber/$pagesize); //计算总页数

						if ($countnumber % $pagesize) $pages++;

						if (isset($_GET['page'])){ $page=intval($_GET['page']); }else{ $page=1; }//否则，设置为第一页
					   
						$offset=$pagesize*($page - 1);

						$rs=web_admin_array_selectvmember_group($gweid,$in,$r,$offset,$pagesize);//取得—当前页—记录集！
						if($rs!==false){
						foreach($rs as $vmbs) {
					?>
					<tr>
							<td align="center"><?php echo $vmbs->mid; ?></td>
							<td align="center"><?php echo $vmbs->nickname; ?> </td>
							<td align="center"><?php echo $vmbs->realname; ?> </td>
							<td align="center"><?php echo $vmbs->mobilenumber; ?> </td>
						<?php if($vipaudit!='0'){?>
							<td align="center">
								<input type="radio" name="vmbsaudit<?php echo $vmbs->mid;?>"   onclick="set_vmbsaudit('1','<?php echo $vmbs->mid;?>')" style="vertical-align:middle;  margin-bottom:5px;margin-left:-11px;margin-right:2px;" <?php if($vmbs->isaudit=='1') echo 'checked' ?>>审批通过</input>
								<input type="radio" name="vmbsaudit<?php echo $vmbs->mid;?>"  onclick="set_vmbsaudit('2','<?php echo $vmbs->mid;?>')" style="vertical-align:middle;  margin-bottom:5px;margin-left:10px;margin-right:2px;" <?php if($vmbs->isaudit=='2') echo 'checked' ?>>审批中</input>
								<input type="radio" name="vmbsaudit<?php echo $vmbs->mid;?>"  onclick="set_vmbsaudit('0','<?php echo $vmbs->mid;?>')" style="vertical-align:middle;  margin-bottom:5px;margin-left:11px;margin-right:2px;" <?php if($vmbs->isaudit=='0') echo 'checked' ?>>拒绝</input>				
							</td>
						<?php }else{?>
							<td align="center">审批通过</td>
						<?php }?>
						<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
						<input type="button" class="btn btn-sm btn-warning" onclick='delVmember("<?php echo $vmbs->mid?>")' name="del" id="buttondel" value="删除"> 
						<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vipmember_update_dialog.php?beIframe&vipmemberId=<?php echo $vmbs->mid; ?>'" name="upd" id="buttonupd" value="更新"> 
						<input type="button" class="btn btn-sm btn-default" onclick="location.href='<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vipmember_point.php?beIframe&vipmemberId=<?php echo $vmbs->mid; ?>&pagefor=<?php echo $page; ?>&f=<?php echo $f; ?>&in=<?php echo $in; ?>&r=<?php echo $r; ?>'" name="upd" id="buttonupd" value="获奖详情"> 
						</td>
					</tr>
					<?php
					}
					?>
				</tbody>
				</table>
			</div>	
		</div>
			<?php
				//============================//
				//  翻页显示 一               
				//============================//
					echo "<p style='margin-left:30px;'>";  //  align=center
					$first=1;
					$prev=$page-1;   
					$next=$page+1;
					$last=$pages;

				if ($page > 1)
					{
						echo "<a href='?beIframe&page=".$first."&f=".$f."&in=".$in."&r=".$r."'>首页</a>  ";
						echo "<a href='?beIframe&page=".$prev."&f=".$f."&in=".$in."&r=".$r."'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='?beIframe&page=".$next."&f=".$f."&in=".$in."&r=".$r."'>下一页</a>  ";
						echo "<a href='?beIframe&page=".$last."&f=".$f."&in=".$in."&r=".$r."'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&page=".$i."&f=".$f."&in=".$in."&r=".$r."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&page=".$i."&f=".$f."&in=".$in."&r=".$r."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后
					echo "</p>";
					}if($rs!==False && empty($rs)&&!isset($_REQUEST['deleteflag']))
					{ 
					    echo "<script language='javascript'>alert('没有符合该条件的查询结果');</script>";
					}
					}else{			
				
					//获取当前用户下所有的会员
					$vmember_list=web_admin_list_vmember_group($gweid);	
					$pagesize=5; //设定每一页显示的记录数						
					//-----------------------------------------------------------------------------------------------//
					//分页逻辑处理
					$vmembersCount = web_admin_count_vmember_group($gweid);
					foreach($vmembersCount as $vmembersnumber){
						 $countnumber=$vmembersnumber->memberCount;
						 
					}
					$pages=intval($countnumber/$pagesize); //计算总页数
					if ($countnumber % $pagesize) $pages++;
					if (isset($_GET['page'])){ $page=intval($_GET['page']); }else{ $page=1; }//否则，设置为第一页
					$offset=$pagesize*($page - 1);
					$rs=web_admin_array_vmember_group($gweid,$offset,$pagesize);//取得—当前页—记录集！
				?> 
				
				<?php
		
					foreach ($rs as $vmbs) {
				?>
				<tr>
						<td align="center"><?php echo $vmbs->mid; ?></td>
						<td align="center"><?php echo $vmbs->nickname; ?> </td>
						<td align="center"><?php echo $vmbs->realname; ?> </td>
						<td align="center"><?php echo $vmbs->mobilenumber; ?> </td>
						<?php if($vipaudit!='0'){?>
							<td align="center">
								<input type="radio" name="vmbsaudit<?php echo $vmbs->mid;?>"   onclick="set_vmbsaudit('1','<?php echo $vmbs->mid;?>')" style="vertical-align:middle;  margin-bottom:5px;margin-left:-11px;margin-right:2px;" <?php if($vmbs->isaudit=='1') echo 'checked' ?>/>审批通过</input>
								<input type="radio" name="vmbsaudit<?php echo $vmbs->mid;?>"  onclick="set_vmbsaudit('2','<?php echo $vmbs->mid;?>')" style="vertical-align:middle;  margin-bottom:5px;margin-left:10px;margin-right:2px;" <?php if($vmbs->isaudit=='2') echo 'checked' ?>/>审批中</input>
								<input type="radio" name="vmbsaudit<?php echo $vmbs->mid;?>"  onclick="set_vmbsaudit('0','<?php echo $vmbs->mid;?>')" style="vertical-align:middle;  margin-bottom:5px;margin-left:11px;margin-right:2px;" <?php if($vmbs->isaudit=='0') echo 'checked' ?>/>拒绝</input>				
							</td>
						<?php }else{?>
							<td align="center">审批通过</td>
						<?php }?>
					<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick='delVmember("<?php echo $vmbs->mid?>")' name="del" id="buttondel" value="删除"> 
					<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vipmember_update_dialog.php?beIframe&vipmemberId=<?php echo $vmbs->mid; ?>'" name="upd" id="buttonupd" value="更新"> 
					<input type="button" class="btn btn-sm btn-default" onclick="location.href='<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vipmember_point.php?beIframe&vipmemberId=<?php echo $vmbs->mid; ?>&pagefor=<?php echo $page; ?>&f=<?php echo $f; ?>&in=<?php echo $in; ?>&r=<?php echo $r; ?>'" name="upd" id="buttonupd" value="获奖详情"> 
					</td>
					
				</tr>
				<?php
				}
				?>
			</tbody>
			</table>
			</form>	
			</div>
			<?php
				//============================//
				//  翻页显示 一               
				//============================//
					echo "<p>";  //  align=center
					$first=1;
					$prev=$page-1;   
					$next=$page+1;
					$last=$pages;

				if ($page > 1)
					{
						echo "<a href='?beIframe&page=".$first."'>首页</a>  ";
						echo "<a href='?beIframe&page=".$prev."'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='?beIframe&page=".$next."'>下一页</a>  ";
						echo "<a href='?beIframe&page=".$last."'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&page=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&page=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后
					echo "</p>";

			 } ?>
</body>
<script language='javascript'>
	function checknull(obj, warning)
	{
	  if (obj.value == "") {
		alert(warning);
		obj.focus();
		return true;
	  }
	return false;
	}

	function validateform()
	{
	  var selectone = document.getElementById("range"); 
	  var index = selectone.selectedIndex;
	  var value = selectone.options[index].value; 
	  //alert(value);
	  if(value != "all")
	  {
		  if (checknull(document.content.indata, "请输入查询内容!") == true) {
			return false;
		  }
		  return true; 
	  }
	  else
	     return true;
	} 
	function set_vipaudit(setaudit){
		$.ajax({
				url:"<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vipmember_audit.php?beIframe",				
				type: "POST",
				data:{'setAudit':setaudit},		
				success: function(data){
					if (data.status == 'error'){
						alert(data.message);
					}else if (data.status == 'success'){
						alert(data.message);
						location.reload();
					}
				},
				 error: function(data){
					alert("出现错误");
				},
				dataType: 'json'
			});	
	}
	function set_vmbsaudit(vmaudit,mid){
	$.ajax({
			url:"<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vipmember_audit.php?beIframe",
			type: "POST",
			data:{'setVmAudit':vmaudit,'mid':mid},		
			success: function(data){
				if (data.status == 'error'){
					alert(data.message);
				}else if (data.status == 'success'){
					alert(data.message);
					location.reload();
				}
			},
			 error: function(data){
				alert("出现错误");
			},
			dataType: 'json'
		});	
	}
		
	$(function(){
	
		if( $('#range').val() == 'all')
			$("#indata").hide();
		$('#range').change( function(){
				if($(this).val() == 'all'){
				$("#indata").hide();//隐藏
				}
				else 
				$("#indata").show();//显示
			})
		}
	);
	var i="<?php echo $indata ?>";
	document.getElementById("indata").value=i;
	var g="<?php echo $r ?>";
	document.getElementById("range").value=g;
	var xmlHttp;
    function createXMLHttpRequest(){
    if(window.ActiveXObject)
     xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    else if(window.XMLHttpRequest)
     xmlHttp = new XMLHttpRequest();
    }
	function delVmember(id){  	
		if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			xmlHttp.open("GET","<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vmember_delete.php?beIframe&vmId="+id,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				alert("" + xmlHttp.responseText);
				if((window.location.href).indexOf("deleteflag") == -1)
				{
				   window.location.href = window.location.href + "&deleteflag";
				  
				}
				else
				{
				  window.location.reload();
				}
			}
			xmlHttp.send(null);
		}
	}
	
	function ExportLogExcel(){
		var selectone = document.getElementById("range"); 
	    var index = selectone.selectedIndex;
	    var value = selectone.options[index].value;
		var indata=document.getElementById("indata").value; 
		window.location.href="<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vipmember_export.php?beIframe&range="+value+"&indata="+indata;		
	}
</script>
</html>