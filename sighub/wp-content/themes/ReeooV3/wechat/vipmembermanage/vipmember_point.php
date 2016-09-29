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
date_default_timezone_set('PRC');
include '../common/wechat_dbaccessor.php';
include '../../wesite/common/dbaccessor.php';
include '../common/wechat_constant.php';
//判断是否是分组管理员的公众号,分组管理员不需要进行此功能的check
$groupadmincount = is_superadmin($_SESSION['GWEID']);
if($groupadmincount == 0)
   include 'vmember_permission_check.php';

/*used for the 'return' viplist and the vip page selected*/
$pagefor=$_GET['pagefor'];
$f=$_GET['f'];
$in=$_GET['in'];
$r=$_GET['r'];

$vipmemberId=$_REQUEST["vipmemberId"];//获取会员的id

$vmember=web_admin_get_vipmember($vipmemberId);
foreach($vmember as $vmemberinfo){
	$realName=$vmemberinfo->realname;
	$mobilenumber=$vmemberinfo->mobilenumber;
	$point=$vmemberinfo->point;	
}

$search_condition = trim($_REQUEST['range']);
$search_content = trim($_REQUEST['indata']);

$searchscr = array(
	'all' => '',
	'status' => array(
		" AND a.status = '0'",
		" AND a.status != '0'",
		" AND a.status = '6'",
	),
	'award' => "AND a.award ='{$search_content}'"
);

$searchegg = array(
	'all' => '',
	'status' => array(
		" AND c.status = '0'",
		" AND c.status != '0'",
		" AND c.status = '6'",
	),
	'award' => "AND c.award ='{$search_content}'"
);

$searchred = array(
	'all' => '',
	'status' => array(
		" AND e.status = '0'",
		" AND e.status != '0'",
		" AND e.status = '6'",
	),
	'award' => "AND e.amount ='{$search_content}'"
);

if($search_condition=='status'){
	if($search_content=='未领奖'){
		$search_content_value=0;
	}else if($search_content=='已领奖'){
		$search_content_value=1;
	}else{
		$search_content_value=2;
	}
	$searchscr[$search_condition]=$searchscr[$search_condition][$search_content_value];
	$searchegg[$search_condition]=$searchegg[$search_condition][$search_content_value];
	$searchred[$search_condition]=$searchred[$search_condition][$search_content_value];
}
if($search_condition=='type'){
	if($search_content=='刮刮卡'){
		$searchscr[$search_condition]="";
		$searchegg[$search_condition]="AND c.status = '6'";
		$searchred[$search_condition]="AND e.status = '6'";
	}else if($search_content=='砸蛋'){
		$searchscr[$search_condition]="AND a.status = '6'";
		$searchegg[$search_condition]="";
		$searchred[$search_condition]="AND e.status = '6'";
	}else if($search_content=='红包'){
		$searchred[$search_condition]="";
		$searchegg[$search_condition]="AND c.status = '6'";
		$searchscr[$search_condition]="AND a.status = '6'";
	}else{
		$searchscr[$search_condition]="AND a.status = '6'";
		$searchegg[$search_condition]="AND c.status = '6'";
		$searchegg[$search_condition]="AND e.status = '6'";
	}	
}
?>

 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/vipcommon.css">
	</head>
	<body>
		<div class="main_auto">
			<div class="main-title">
				<div class="title-1">当前位置：会员管理> <font class="fontpurple">获奖详情 </font>
				</div>
			</div>
			<div class="bgimg"></div>
			<div class="panel panel-default" style="width:500px;height:200px;margin-top:30px;float:left;">
				<div class="panel-heading">会员基本信息</div>
				<div class="newline">
					<div class="vip-label"><label for="type">编号: </label></div>
					<div class="vip-value"><?php echo $vipmemberId; ?></div>
				</div>
				<div class="newline">
					<div class="vip-label"><label for="type">姓名: </label></div>
					<div class="vip-value"><?php echo $realName; ?></div>
				</div>
				<div class="newline">
					<div class="vip-label"><label for="type">电话: </label></div>
					<div class="vip-value"><?php echo $mobilenumber; ?></div>
				</div>
				<div class="newline">
					<div class="vip-label"><label for="type">积分: </label></div>
					<div class="vip-value"><?php echo $point; ?></div>
				</div>
			</div>
			<div style="float:left;margin-top:198px;margin-left:20px;">
				<input class="btn btn-sm btn-default" type="button" onclick="location.href='<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vipmember_list.php?beIframe&page=<?php echo $pagefor; ?>&f=<?php echo $f; ?>&in=<?php echo $in; ?>&r=<?php echo $r; ?>'" value="返回"/>
			</div>
			<div style="margin-top:20px;clear:both;" >
			<form name ="content" onSubmit="return validateform()" action="" method="get" enctype="multipart/form-data">		
			<div style="margin-top:20px">
			<table class="table table-striped" width="800" border="0" align="center">
			<tbody>
				<tr>
					<td colspan=10 scope="col" width="100" align="left" >
						<select id="range" name="range" class="sltfield">
							<option value="">请选择
							<option value="all">全部</option>
							<option value="type">活动类型</option>
							<option value="award">奖品</option>
							<option value="status">领奖情况</option>
						</select>
						<input type="hidden" id="vipmemberId" name="vipmemberId" value="<?php echo $vipmemberId;?>" />
						<input type="hidden" id="pagefor" name="pagefor" value="<?php echo $_GET['pagefor'];?>" />
						<input type="hidden" id="f" name="f" value="<?php echo $_GET['f'];?>" />
						<input type="hidden" id="in" name="in" value="<?php echo $_GET['in'];?>" />
						<input type="hidden" id="r" name="r" value="<?php echo $_GET['r'];?>" />
						<input id="indata" class="sltfield" name="indata" value="<?php echo $_GET['indata'];?>" />
						<input type="hidden" name="beIframe" value="1">
						<input id="search" class="btn btn-info btn-sm" type="submit" value="查询"/>
					</td>
				</tr>
				<tr>
					<td scope="col" width="150" align="center" style="font-weight:bold">时间</td>
					<td scope="col" width="150" align="center" style="font-weight:bold">活动类型</td>
					<td scope="col" width="150" align="center" style="font-weight:bold">奖品</td>
					<td scope="col" width="150" align="center" style="font-weight:bold">积分</td>
					<td scope="col" width="150" align="center" style="font-weight:bold">领奖情况</td>
				</tr>
				
				<?php 
					$pagesize=7; //设定每一页显示的记录数						
					//-----------------------------------------------------------------------------------------------//
					//分页逻辑处理
					//-----------------------------------------------------------------------------------------------
					$countnumber = wechat_vip_point_count($vipmemberId,$searchscr[$search_condition],$searchegg[$search_condition],$searchred[$search_condition]);
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					if (isset($_GET['page'])){ $page=intval($_GET['page']); }else{ $page=1; }//否则，设置为第一页
				   
					$offset=$pagesize*($page - 1);

					$rs=wechat_vip_point_list($vipmemberId,$offset,$pagesize,$searchscr[$search_condition],$searchegg[$search_condition],$searchred[$search_condition]);//取得—当前页—记录集！
				?> 
				
				<?php
				
					foreach ($rs as $vippoint) {
				?>
				<tr>	
					<td align="center"><?php echo  date('Y-m-d H:i:s', $vippoint->ctime); ?> </td>
					<td align="center"><?php echo $vippoint->type; ?> </td>
					<td align="center"><?php echo $vippoint->paward; ?> </td>
					<td align="center"><?php echo intval($vippoint->pdescription); ?> </td>
					<td align="center"><?php if($vippoint->pstatus == 0) {echo "未领奖"; }else{echo "已领奖"; } ?> </td>
				</tr>
				<?php
				}
				?>
				
				</tr>
			</tbody>
			</table>
			
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
						echo "<a href='?beIframe&page=".$first."&vipmemberId=".$vipmemberId."&indata=".$search_content."&range=".$search_condition."&pagefor=".$pagefor."&f=".$f."&in=".$in."&r=".$r."'>首页</a>  ";
						echo "<a href='?beIframe&page=".$prev."&vipmemberId=".$vipmemberId."&indata=".$search_content."&range=".$search_condition."&pagefor=".$pagefor."&f=".$f."&in=".$in."&r=".$r."'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='?beIframe&page=".$next."&vipmemberId=".$vipmemberId."&indata=".$search_content."&range=".$search_condition."&pagefor=".$pagefor."&f=".$f."&in=".$in."&r=".$r."'>下一页</a>  ";
						echo "<a href='?beIframe&page=".$last."&vipmemberId=".$vipmemberId."&indata=".$search_content."&range=".$search_condition."&pagefor=".$pagefor."&f=".$f."&in=".$in."&r=".$r."'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&vipmemberId=".$vipmemberId."&indata=".$search_content."&range=".$search_condition."&pagefor=".$pagefor."&f=".$f."&in=".$in."&r=".$r."&page=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&vipmemberId=".$vipmemberId."&indata=".$search_content."&range=".$search_condition."&pagefor=".$pagefor."&f=".$f."&in=".$in."&r=".$r."&page=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

					echo "</p>";
			?>
		</div>
	</form>
	</div>
	</body>
</html>
<script language='javascript'>
$(function(){
		if( $('#range').val() == 'all')
			$("#indata").hide();
		$('#range').change( function(){
				if($(this).val() == 'all'){
					$("#indata").hide();//隐藏
					$("#indata").val("");  	
				}else{
					$("#indata").show();//显示				
				}
				
			})
		}
	);
	
	$('#range').val('<?php echo $_GET['range'];?>');
	
	function validateform()
	{
		var range = $('#range').val();
		if(range == "all")
			return true;
		if(range == ""){
			alert("请选择查询条件！");
			return false;
		}			
		if($('#indata').val() ==""){
			alert("请输入查询内容");
			return false;
		}
		return true;
	} 
</script>