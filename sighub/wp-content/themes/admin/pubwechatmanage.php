<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

if(isset($_GET['del']) && !empty($_GET['del']) ){
	require_once './wp-admin/includes/user.php';
	wp_delete_user( $_GET['del'] );
	}
	
   $flag=$_REQUEST['flag'];
   $f=$_REQUEST["f"];
   if($f==null){$f=$flag;}
  // echo "flag的值".$flag;
  // echo "f的值".$f;
   $indata=$_REQUEST['Ipad'];
   $in=$_REQUEST["in"];
   if($in==null){$in=$indata;}
   $indata=$in;
   // echo $indata;
	//$weid=$_SESSION['WEID'];
	$rg=$_REQUEST['range'];
	$r=$_REQUEST["r"];
	if($r==null){$r=$rg;}
	//echo "这是我的值".$r;
?>

<script>
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
</script>
<div class="main-title">
	<div class="title-1">当前位置：公用公众号管理 > <font class="fontpurple">公用公众号列表 </font>
	</div>
</div>
<div class="bgimg"></div>
<input type="button" class="btn btn-primary" onclick="location.href='?admin&page=adminwechataccountadd'" name="del" id="buttondel" value="添加公用公众号" style="margin-top:10px;">

	<!--<div class="panel-heading">公用公众号列表</div>-->
<form name ="content" onSubmit="return validateform()" action="?admin&page=adminwechataccountsearch" method="post" enctype="multipart/form-data">	
<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
	<table class="table table-striped" width="800" border="1" align="center">
	<tbody>
	
		<tr>
			 <td colspan=5 scope="col" width="100" align="left" >
				 <select id="range" name="range">
					<option value="">请选择
					<option value="all">全部</option>
					<option value="wechat_nikename">微信昵称</option>
				</select>
			    <input id="indata" name="indata" value="" />
				<input id="search1" class="btn btn-default" type="submit" value="查询" />
			</td>
		</tr>
		<tr>
			<td scope="col" width="120" align="center" style="font-weight:bold">微信昵称</td>
			<td scope="col" width="160" align="center" style="font-weight:bold">公众号类型</td>
			<td scope="col" align="center" style="font-weight:bold">URL</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">Token</td>
			<td scope="col" width="180" align="center" style="font-weight:bold">操作</td>
		</tr>
		
		
		<?php 
		   
	if($f==1){
				
	
		$pagesize=5; //设定每一页显示的记录数						
		//-----------------------------------------------------------------------------------------------//
		//分页逻辑处理
		//-----------------------------------------------------------------------------------------------
		//$vmembersCount = web_admin_count_vmember($_SESSION['WEID']);
		$vmembersCount = web_admin_count_selectpub($in,$r);
		//echo $vmembersCount;//获取表的记录总数
		
		foreach($vmembersCount as $vmembersnumber){
			 $countnumber=$vmembersnumber->memberCount;
			 
		}
		
		$pages=intval($countnumber/$pagesize); //计算总页数

		if ($countnumber % $pagesize) $pages++;

		//设置缺省页码
		//↓判断“当前页码”是否赋值过
		if (isset($_GET['pubwechatpage'])){ $page=intval($_GET['pubwechatpage']); }else{ $page=1; }//否则，设置为第一页
	   
		//↓计算记录偏移量
			$offset=$pagesize*($page - 1);

		//↓读取指定记录数
		//$rs=web_admin_array_vmember($_SESSION['WEID'],$offset,$pagesize);//取得—当前页—记录集！
		$rs=web_admin_array_selectpub($in,$r,$offset,$pagesize);//取得—当前页—记录集！
		if($rs!==false){

		foreach($rs as $account) {
		
		//显示的url链接
		$url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoinPublic.php?hash='.$account->hash;
		$url=preg_replace('|^https://|', 'http://', $url);

	?>
	    <tr>
			<td align="center"><?php echo $account->wechat_nikename; ?></td>
			<td align="center">
				<?php if(($account->wechat_type == "pub_sub") && ($account->wechat_auth == 0)) echo "公用微信未认证订阅号"; ?>
				<?php if(($account->wechat_type == "pub_sub") && ($account->wechat_auth == 1)) echo "公用微信认证订阅号"; ?>
				<?php if(($account->wechat_type == "pub_svc") && ($account->wechat_auth == 0)) echo "公用微信未认证服务号"; ?>
				<?php if(($account->wechat_type == "pub_svc") && ($account->wechat_auth == 1)) echo "公用微信认证服务号"; ?>
			</td>
			<td align="center"><input type="text" class="form-control" value="<?php echo $url; ?>" readonly=enable /></td>
			<td align="center"><?php echo $account->token; ?></td>
			<td class="row" align="center">
				<input type="button" class="btn btn-sm btn-warning" style="margin-right:5px;" onclick="delwAccount('<?php echo $account->wid;?>')" name="del" id="buttondel" value="删除"> 
				<input type="button" class="btn btn-sm btn-info" onclick="location.href='?admin&page=pubwechataccountedit&mid=<?php echo $account->M_id ?>&wid=<?php echo $account->wid ?>'" name="upd" id="buttonupd" value="编辑">
			</td>
		</tr>
	<?php
	}
	?>
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
			echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$first."&f=".$f."&in=".$in."&r=".$r."'>首页</a>  ";
			echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$prev."&f=".$f."&in=".$in."&r=".$r."'>上一页</a>  ";
		}

	if ($page < $pages)
		{
			echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$next."&f=".$f."&in=".$in."&r=".$r."'>下一页</a>  ";
			echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$last."&f=".$f."&in=".$in."&r=".$r."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
	echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

	for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$i."&f=".$f."&in=".$in."&r=".$r."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

	if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

	for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$i."&f=".$f."&in=".$in."&r=".$r."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后
		echo "</p>";
		}
		if($rs!==False && empty($rs)&&!isset($_REQUEST['deleteflag']))
		{
			echo "<script language='javascript'>alert('没有符合该条件的查询结果');</script>";
		}
		}else{
		   
			$pagesize=5; //设定每一页显示的记录数						
			//-----------------------------------------------------------------------------------------------//
			//分页逻辑处理
			//-----------------------------------------------------------------------------------------------
			//$tmpArr = mysql_fetch_array($rs);
	        //$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
			
			//$websitesCount=web_admin_count_website($current_user->ID);//获取记录总数
			$accountsCount = web_admin_count_adminaccount();
			//echo $usersCount;//获取wp_users表的记录总数
			
			foreach($accountsCount as $accountsnumber){
				 $countnumber=$accountsnumber->accountCount;
				 
			}
			//echo $countnumber;
			
			$pages=intval($countnumber/$pagesize); //计算总页数

			if ($countnumber % $pagesize) $pages++;

			//设置缺省页码
			//↓判断“当前页码”是否赋值过
			if (isset($_GET['pubwechatpage'])){ $page=intval($_GET['pubwechatpage']); }else{ $page=1; }//否则，设置为第一页
           
			//↓计算记录偏移量
				$offset=$pagesize*($page - 1);

			//↓读取指定记录数
				//$rs=web_admin_array_website($offset,$pagesize,$current_user->ID);//取得—当前页—记录集！
				$rs=web_admin_array_adminaccount($offset,$pagesize);//取得—当前页—记录集！
				
				//一个function活的总个数
				//foreach (){$curNem=iii->as bianliang}
				//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
				
		?> 
		
		
		<?php
		//$blogusers = get_users('meta_key=wp_user_level&meta_value=2&orderby=ID');
		//foreach ($blogusers as $user) {
		foreach ($rs as $account) {
		   //显示的url链接
		$url = constant("CONF_THEME_DIR").'/wechat/common/weChatJoinPublic.php?hash='.$account->hash;
		$url=preg_replace('|^https://|', 'http://', $url);
		
		?>
		<tr>
			<td align="center"><?php echo $account->wechat_nikename; ?></td>
			<td align="center">
				<?php if(($account->wechat_type == "pub_sub") && ($account->wechat_auth == 0)) echo "公用微信未认证订阅号"; ?>
				<?php if(($account->wechat_type == "pub_sub") && ($account->wechat_auth == 1)) echo "公用微信认证订阅号"; ?>
				<?php if(($account->wechat_type == "pub_svc") && ($account->wechat_auth == 0)) echo "公用微信未认证服务号"; ?>
				<?php if(($account->wechat_type == "pub_svc") && ($account->wechat_auth == 1)) echo "公用微信认证服务号"; ?>
			</td>			
			<td align="center"><input type="text" class="form-control" value="<?php echo $url; ?>" readonly=enable /></td>
			<td align="center"><?php echo $account->token; ?></td>
			<td class="row" align="center"><table><td><tr><input type="button" class="btn btn-sm btn-warning" style="margin-right:5px;" onclick="delwAccount('<?php echo $account->wid;?>')" name="del" id="buttondel" value="删除"> </tr><tr><input type="button" class="btn btn-sm btn-info" onclick="location.href='?admin&page=pubwechataccountedit&mid=<?php echo $account->M_id ?>&mname=<?php echo $account->M_name ?>&wid=<?php echo $account->wid ?>'" name="upd" id="buttonupd" value="编辑"></tr></td></table>
			</td>
		</tr>
		<?php
		}
        ?>
		</tr>
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
		echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$first."'>首页</a>  ";
		echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$prev."'>上一页</a>  ";
		}

		if ($page < $pages)
		{
		echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$next."'>下一页</a>  ";
		echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$last."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
		echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

		for ($i=1;$i< $page;$i++){echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

		if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

		for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?admin&page=pubwechatmanage&pubwechatpage=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

		//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
		echo "</p>";

		}?>
<script language='javascript'>
	var i="<?php echo $indata ?>";
	//$("#indata").attr("value",i);
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
	    function delAccount(id){
			
		   if (confirm("确认要删除？")) {
							
				createXMLHttpRequest();
				//请求回来的内容去掉header和footer, 即加上&header=0&footer=0
				xmlHttp.open("GET","?admin&page=pubwechataccountdelete&header=0&footer=0&id="+id,true);
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
						if(xmlHttp.responseText)
						
						alert(xmlHttp.responseText);
						//window.location.reload();
						if((window.location.href).indexOf("deleteflag") == -1)
						{
						   window.location.href = window.location.href + "&deleteflag";
						  
						}
						else
						{
						  
						window.location.reload();
						}
					}

				}
				xmlHttp.send(null);
			}
			
	   }
	function delwAccount(id){
			
		if(confirm("确定删除该公用公众号吗？")){	
			$.ajax({
				type:'get',
				dataType: 'json',
				url:"?admin&page=pubwechataccountdeletecheck&header=0&footer=0&wid="+id,
				success: function(data){
					if(data.iscandel=="yes"){
						$.ajax({
							type:'get',
							url:"?admin&page=pubwechataccountdelete&header=0&footer=0&id="+id,
							success: function(data){
								//console.info(data);
								if((window.location.href).indexOf("deleteflag") == -1){
									window.location.href = window.location.href + "&deleteflag";
							  
								}
								else{
								  window.location.reload();
								}
							},
				        	error: function(data){
								alert("出现错误");
							}			
						});
					}else{
						var bness="已有商家"+data.business+" 正在使用该公众号，如果删除，则商家的所有该公众号信息将被删除，是否仍旧强行删除该公众号";
						if (confirm(bness)) {
							$.ajax({
								type:'get',
								url:"?admin&page=pubwechataccountdelete&header=0&footer=0&id="+id,
								success: function(data){
									if((window.location.href).indexOf("deleteflag") == -1){
										window.location.href = window.location.href + "&deleteflag";
								  
									}
									else{
									  window.location.reload();
									}
								},
					        	error: function(data){
									alert("出现错误");
								}			
							});						
						}					
					}
				},
		        error: function(data){
					alert("出现错误");
				}			
			});
		}
	}
	</script>
					
