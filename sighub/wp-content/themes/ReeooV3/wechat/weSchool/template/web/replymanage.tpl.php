<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
 

 <script language="javascript">
	function checknull(obj, warning){
		if (obj.value == "") {
			alert(warning);
			obj.focus();
			return true;
		}
		return false;
	}

	function validateform(){
		var selectone = document.getElementById("range"); 
		var index = selectone.selectedIndex;
		var value = selectone.options[index].value; 
		if(value != "all"){
			if (checknull(document.content.indata, "请输入查询内容!") == true) {
				return false;
			}
			return true; 
		}else
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
 
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Replymanage" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	</head>
	<body>
		<div class="main_auto">
			<div class="main-title">
				<div class="title-1">当前位置：评论管理 ><font class="fontpurple">评论列表</font>
				</div>
			</div>
			<div class="bgimg"></div>
		<div style="padding:0 20px">
		<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('searchreply',array('GWEID' => '','notice_id'=>$noc_id));?>" method="post" enctype="multipart/form-data">		
			<table class="table table-striped" width="800" border="1" align="center" style="margin-top:40px;">
			<tbody>
				<tr>
					<td colspan=5 scope="col" width="100" align="left" >
						<select id="range" name="range">
							<option value="">请选择
							<option value="all">全部</option>
							<option value="reply_content">评论内容</option>
							<option value="reply_time">评论时间</option>
							<option value="reply_author">评论者</option>
						</select>
						<input id="indata" name="indata" value="" />
						<input id="search" class="btn btn-info" style="margin-bottom:0px" type="submit" value="查询"/>
					</td>
				</tr>
				<tr>
					<td scope="col" width="100" align="center" style="font-weight:bold">评论编号</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">评论内容</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">评论时间</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">评论者</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">操作</td>
				</tr>
				<?php
				if($f==1){
						
					$pagesize=5; //设定每一页显示的记录数
					
					foreach($replysCount as $rp){
						 $countnumber=$rp['replyCount'];
						 
					}
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GET['replypage'])){ $page=intval($_GET['replypage']); }else{ $page=1; }//否则，设置为第一页
				   
					//↓计算记录偏移量
					$offset=$pagesize*($page - 1);

					//↓读取指定记录数
					//20140624 janeen update
					//$rs=$this -> doWebCountReplySearchPage($_SESSION['WEID'],$in,$r,$offset,$pagesize,$notice_id);//取得—当前页—记录集！
					$rs=$this -> doWebCountReplySearchPage($_SESSION['GWEID'],$in,$r,$offset,$pagesize,$notice_id);//取得—当前页—记录集！
					//end
					if($rs!==false){
					foreach($rs as $reply) {
				?>
				<tr>
					<td align="center"><?php echo $reply['reply_id']; ?></td>
					<td align="center" title=<?php echo $reply['reply_content'];?>><?php echo mb_substr($reply['reply_content'],0,5,'UTF-8'); ?> </td>
					<td align="center"><?php echo $reply['reply_time']; ?> </td>
					<td align="center">
					<?php if((substr($reply['reply_author'],0,1))=='t'){
							$teid=substr($reply['reply_author'],1);
							$te_id=$this -> doWebSelectReplyteacher($teid);
							foreach($te_id as $e_id){
								echo $e_id['tea_name'];
							}
						}else{
							$stid=substr($reply['reply_author'],1);
							$st_id=$this -> doWebSelectReplystudent($stid);
							foreach($st_id as $s_id){
								echo $s_id['stu_name'];
							}
						} 
					?>
					</td>
					<!--<td align="center"><?php echo $reply['reply_author']; ?> </td>-->
					<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick='delReply("<?php echo $reply['reply_id']?>")' name="del" id="buttondel" value="删除"> 
					<!--<input type="button" class="btn btn-sm btn-info" onclick='updateReply("<?php echo $reply['reply_id']?>")' name="upd" id="buttonupd" value="更新"> -->
					</td>
					
				</tr>
				<?php
				}
				?>
			</tbody>
			</table>
			
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
						echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $first, 'f' => $f, 'in'=>$in, 'r' => $r,'notice_id'=>$notice_id))}'>首页</a>  ";
						echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $prev, 'f' => $f, 'in'=>$in, 'r' => $r,'notice_id'=>$notice_id))}'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $next, 'f' => $f, 'in'=>$in, 'r' => $r,'notice_id'=>$notice_id))}'>下一页</a>  ";
						echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $last, 'f' => $f, 'in'=>$in, 'r' => $r,'notice_id'=>$notice_id))}'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){
				
					echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r,'notice_id'=>$notice_id))}'>[".$i ."]</a>  ";
				}  // 1-先输出当前页之前的


				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){
					echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r,'notice_id'=>$notice_id))}'>[".$i ."]</a>  ";
					
				}// 3-接着输出当前页之后
				
				echo "</p>";
				}if($rs!==False && empty($rs)&&!isset($_REQUEST['deleteflag'])){ 
					    echo "<script language='javascript'>alert('没有符合该条件的查询结果');</script>";
					}
				}else{			
					
					$pagesize=5; //设定每一页显示的记录数
					
					foreach($replyCount as $rp){
						 $countnumber=$rp['replyCount'];
						 
					}
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GET['replypage'])){ $page=intval($_GET['replypage']); }else{ $page=1; }//否则，设置为第一页
				   
					//↓计算记录偏移量
						$offset=$pagesize*($page - 1);

					//↓读取指定记录数
					//20140624 janeen update
					//$rs=$this -> doWebCountReplyPage($_SESSION['WEID'],$offset,$pagesize,$notice_id);//取得—当前页—记录集！
					$rs=$this -> doWebCountReplyPage($_SESSION['GWEID'],$offset,$pagesize,$notice_id);//取得—当前页—记录集！
					//end
					//var_dump($rs);
				?> 
				
				<?php
					
					foreach ($rs as $reply) {
					
				?>
				<tr>
					<td align="center"><?php echo $reply['reply_id']; ?></td>
					<td align="center" title=<?php echo $reply['reply_content'];?>><?php echo mb_substr($reply['reply_content'],0,5,'UTF-8'); ?> </td>
					<td align="center"><?php echo $reply['reply_time']; ?> </td>
					<td align="center">
					<?php if((substr($reply['reply_author'],0,1))=='t'){
							$teid=substr($reply['reply_author'],1);
							$te_id=$this -> doWebSelectReplyteacher($teid);
							foreach($te_id as $e_id){
								echo $e_id['tea_name'];
							}
						}else{
							$stid=substr($reply['reply_author'],1);
							$st_id=$this -> doWebSelectReplystudent($stid);
							foreach($st_id as $s_id){
								echo $s_id['stu_name'];
							}
						} 
					?>
					</td>
					<!--<td align="center"><?php //echo $reply['reply_author']; ?> </td>-->
					<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick='delReply("<?php echo $reply['reply_id']?>")' name="del" id="buttondel" value="删除"> 
					<!--<input type="button" class="btn btn-sm btn-info" onclick='updateReply("<?php echo $reply['reply_id']?>")' name="upd" id="buttonupd" value="更新"> -->
					</td>
					
				</tr>
				<?php
				}
				?>
			</tbody>
			</table>
			</form>
			
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
						echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $first,'notice_id'=>$notice_id))}'>首页</a>  ";
						echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $prev,'notice_id'=>$notice_id))}'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $next,'notice_id'=>$notice_id))}'>下一页</a>  ";
						echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $last,'notice_id'=>$notice_id))}'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){
					echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $i,'notice_id'=>$notice_id))}'>[".$i ."]</a>  ";
					
				}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){
					echo "<a href='{$this->createWebUrl('replymanage',array('replypage' => $i,'notice_id'=>$notice_id))}'>[".$i ."]</a>  ";
				}// 3-接着输出当前页之后
					echo "</p>";

			 } ?>
			 </div>
			</div>
</body>
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
	function delReply(id){  	
		if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			xmlHttp.open("GET","<?php echo $this->createWebUrl('replymanage',array());?>"+"&del="+id,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
				alert("删除成功");
				if((window.location.href).indexOf("deleteflag") == -1){
				   window.location.href = window.location.href + "&deleteflag";			  
				}else{
				  window.location.reload();
				}
			}
			xmlHttp.send(null);
		}
	}
	
</script>
</html>