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
	<meta name="Noticemanage" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<!--<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js" type="text/javascript"></script>-->
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<!--<script src="../../js/bootstrap.min.js"></script>-->
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<style type="text/css">
		.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	</style>
	</head>
	<body>
	<div class="main_auto">
		<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('searchnotice',array('GWEID' => ''));?>" method="post" enctype="multipart/form-data">		
		<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
			<div class="panel-heading">公告列表</div>
			<table class="table table-striped" width="800" border="0" align="center" >
			<tbody>
				<tr>
					<td colspan=8 scope="col" width="100" align="left" >
						<select id="range" name="range">
							<option value="">请选择
							<option value="all">全部</option>
							<option value="notice_title">公告标题</option>
							<option value="notice_rights">公告查看权限</option>
							<option value="notice_date">发布时间</option>
							<option value="notice_publisher">发布人</option>
						</select>
						<input id="indata" name="indata" value="" />
						<input id="search" class="btn btn-info btn-sm" style="margin-right:0px;margin-bottom:0px" type="submit" value="查询"/>
						<input type="button" class="btn btn-warning btn-sm" onclick="ExportNoticeExcel()" value="导出" />
					</td>
				</tr>
				<tr>
					<td scope="col" width="100" align="center" style="font-weight:bold">公告编号</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">公告标题</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">公告内容</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">是否允许评论</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">公告查看权限</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">发布时间</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">发布人</td>
					<td scope="col" width="250" align="center" style="font-weight:bold">操作</td>
				</tr>
				<?php
				if($f==1){
					
					$pagesize=5; //设定每一页显示的记录数
					
					foreach($noticesCount as $hk){
						 $countnumber=$hk['noticeCount'];						 
					}
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GET['noticepage'])){ 
						$page=intval($_GET['noticepage']); 
					}else{ 
						$page=1; 
					}//否则，设置为第一页
				   
					//↓计算记录偏移量
					$offset=$pagesize*($page - 1);

					//↓读取指定记录数
					//20140624 janeen update
					//$rs=$this -> doWebCountNoticeSearchPage($_SESSION['WEID'],$in,$r,$offset,$pagesize);//取得—当前页—记录集！
					$rs=$this -> doWebCountNoticeSearchPage($_SESSION['GWEID'],$in,$r,$offset,$pagesize);//取得—当前页—记录集！
					//end 
					if($rs!==false){
					foreach($rs as $notice) {
					
				?>
				<tr>
					<td align="center"><?php echo $notice['notice_id']; ?></td>
					<?php if(mb_strlen($notice['notice_title'])>5){ ?>
							<td align="center" title=<?php echo $notice['notice_title'];?>><?php echo mb_substr($notice['notice_title'],0,5,'UTF-8').'......'; ?>  </td>
					<?php	}else{ ?>
							<td align="center" ><?php echo $notice['notice_title']; ?>  </td>
					<?php	} 	?>
					<?php if(mb_strlen($notice['notice_content'])>5){ ?>
							<td align="center" title=<?php echo $notice['notice_content'];?>><?php echo mb_substr($notice['notice_content'],0,5,'UTF-8').'......'; ?>  </td>
					<?php	}else{ ?>
							<td align="center" ><?php echo $notice['notice_content']; ?>  </td>
					<?php	} 	?>
					<td align="center"><?php echo $notice['notice_allowcomments']?'是':'否'; ?> </td>
					<td align="center">
					<?php 
					  if(strpos($notice['notice_rights'],'t*')!==false){
							echo substr($notice['notice_rights'],0,6)."班所有老师";
					   }else if(strpos($notice['notice_rights'],'t')!==false){
					        
							//2014-07-12新增修改，截取字符串有问题
						    $id=substr($notice['notice_rights'],7);
						    //$id=substr($notice['notice_rights'],8);				
							$teacher_id=$this -> doWebSelecttea($id);
		
							foreach($teacher_id as $td){
								echo substr($notice['notice_rights'],0,6).$td['tea_name']."老师";
							}
						}else if(strpos($notice['notice_rights'],'*')!==false){
							echo substr($notice['notice_rights'],0,4)."级所有班";
						}else{
							echo $notice['notice_rights'];} 
					?> 
					</td>
					<td align="center"><?php echo $notice['notice_date']; ?> </td>
					<td align="center">
					<?php 
						if((substr($notice['notice_publisher'],0,1))=='t'){
							$teid=substr($notice['notice_publisher'],1);
							$te_id=$this -> doWebSelectteacher($teid);
							foreach($te_id as $e_id){
								
								echo $e_id['tea_name'];
							}
						}else{
							$stid=substr($notice['notice_publisher'],1);
							$st_id=$this -> doWebSelectstudent($stid);
							foreach($st_id as $s_id){
								
								echo $s_id['stu_name'];
							}
						}
					?> </td>
					<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick='delNotice("<?php echo $notice['notice_id']?>")' name="del" id="buttondel" value="删除"> 
					<!--2014-07-13新增修改，先去掉更新-->
					<!--<input type="button" class="btn btn-sm btn-info" onclick='editNotice("<?php echo $notice['notice_id']?>")' name="upd" id="buttonupd" value="更新">--> 
					<input type="button" class="btn btn-sm btn-success" onclick='manReply("<?php echo $notice['notice_id']?>")' name="man" id="buttonupd" value="评论管理"> 
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
						echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $first, 'f' => $f, 'in'=>$in, 'r' => $r))}'>首页</a>  ";
						echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $prev, 'f' => $f, 'in'=>$in, 'r' => $r))}'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $next, 'f' => $f, 'in'=>$in, 'r' => $r))}'>下一页</a>  ";
						echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $last, 'f' => $f, 'in'=>$in, 'r' => $r))}'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){
				
					echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r))}'>[".$i ."]</a>  ";
				}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){
					echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r))}'>[".$i ."]</a>  ";
				}// 3-接着输出当前页之后
				
				echo "</p>";
				}if($rs!==False && empty($rs)&&!isset($_REQUEST['deleteflag'])){ 
					    echo "<script language='javascript'>alert('没有符合该条件的查询结果');</script>";
					}
				}else{			
					
					$pagesize=5; //设定每一页显示的记录数						
					
					foreach($noticeCount as $nt){
						 $countnumber=$nt['noticeCount'];						 
					}
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GET['noticepage'])){ $page=intval($_GET['noticepage']); }else{ $page=1; }//否则，设置为第一页
				   
					//↓计算记录偏移量
						$offset=$pagesize*($page - 1);

					//↓读取指定记录数
					//20140624 janeen update
					//$rs=$this -> doWebCountNoticePage($_SESSION['WEID'],$offset,$pagesize);
					$rs=$this -> doWebCountNoticePage($_SESSION['GWEID'],$offset,$pagesize);
					//end
				?> 
				
				<?php
		
					foreach ($rs as $notice) {
					//if(strpos($notice['notice_rights'],'t*')!==false){echo substr($notice['notice_rights'],0,6)."所有老师";}else{echo $notice['notice_rights'];}
				?>
				<tr>
					<td align="center"><?php echo $notice['notice_id']; ?></td>
					<?php if(mb_strlen($notice['notice_title'])>5){ ?>
							<td align="center" title=<?php echo $notice['notice_title'];?>><?php echo mb_substr($notice['notice_title'],0,5,'UTF-8').'......'; ?>  </td>
					<?php	}else{ ?>
							<td align="center" ><?php echo $notice['notice_title']; ?>  </td>
					<?php	} 	?>
					<?php if(mb_strlen($notice['notice_content'])>5){ ?>
							<td align="center" title=<?php echo $notice['notice_content'];?>><?php echo mb_substr($notice['notice_content'],0,5,'UTF-8').'......'; ?>  </td>
					<?php	}else{ ?>
							<td align="center" ><?php echo $notice['notice_content']; ?>  </td>
					<?php	} 	?>
					<td align="center"><?php echo $notice['notice_allowcomments']?'是':'否'; ?> </td>
					<td align="center">
					<?php  
						   if($notice['notice_rights'] == '*'){
								echo "所有年级";
						   }else if(strpos($notice['notice_rights'],'t*')!==false){
								echo substr($notice['notice_rights'],0,6)."班所有老师";
						   }else if(strpos($notice['notice_rights'],'t')!==false){
						        //2014-07-12新增修改，截取字符串有问题
						        $id=substr($notice['notice_rights'],7);
								//$id=substr($notice['notice_rights'],8);
								$teacher_id=$this -> doWebSelecttea($id); 
								foreach($teacher_id as $td){
									echo substr($notice['notice_rights'],0,6).$td['tea_name']."老师";
								}
							}
							//2014-07-12新增修改，之前下面的判断条件不足
							else if((strpos($notice['notice_rights'],'*')!==false) && ($notice['notice_rights'] != '*') ){
								echo substr($notice['notice_rights'],0,4)."级所有班";
							}else if(strpos($notice['notice_rights'],'*') == false ){
								echo $notice['notice_rights'];
							}
							/* else{
								//echo $notice['notice_rights'];
								echo "所有年级";
								}  */?> 
					</td>
					<td align="center"><?php echo $notice['notice_date']; ?> </td>
					<td align="center">
					<?php if((substr($notice['notice_publisher'],0,1))=='t'){
							$teid=substr($notice['notice_publisher'],1);
							$te_id=$this -> doWebSelectteacher($teid);
							foreach($te_id as $e_id){
								echo $e_id['tea_name'];
							}
						}else{
							$stid=substr($notice['notice_publisher'],1);
							$st_id=$this -> doWebSelectstudent($stid);
							foreach($st_id as $s_id){
								echo $s_id['stu_name'];
							}
						} 
					?> 
					</td>
					<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick='delNotice("<?php echo $notice['notice_id']?>")' name="del" id="buttondel" value="删除"> 
					<!--2014-07-13新增修改，先去掉更新-->
					<!--<input type="button" class="btn btn-sm btn-info" onclick='editNotice("<?php echo $notice['notice_id']?>")' name="upd" id="buttonupd" value="更新"> -->
					<input type="button" class="btn btn-sm btn-success" onclick='manReply("<?php echo $notice['notice_id']?>")' name="man" id="buttonupd" value="评论管理"> 
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
					echo "<p style='margin-left:30px;'>";  //  align=center
					$first=1;
					$prev=$page-1;   
					$next=$page+1;
					$last=$pages;

				if ($page > 1)
					{
						echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $first))}'>首页</a>  ";
						echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $prev))}'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $next))}'>下一页</a>  ";
						echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $last))}'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){
					echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $i))}'>[".$i ."]</a>  ";
				}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){
					echo "<a href='{$this->createWebUrl('noticemanage',array('noticepage' => $i))}'>[".$i ."]</a>  ";
				}// 3-接着输出当前页之后
					echo "</p>";

			 } ?>
</body>
<script language='javascript'>
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
	function delNotice(id){  	
			if(confirm("确定删除吗？")){
				createXMLHttpRequest();
				xmlHttp.open("GET","<?php echo $this->createWebUrl('noticemanage',array());?>"+"&del="+id,true);
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
					
					//window.location.reload();
					if((window.location.href).indexOf("deleteflag") == -1)
					{
						alert("删除成功！");
					   window.location.href = window.location.href + "&deleteflag";
					  
					}
					else
					{
						alert("删除成功！");
					  window.location.reload();
					}
				}
				xmlHttp.send(null);
			}
	   }
	
	
	function manReply(id){  
	   window.param=id;
	   window.open('<?php echo $this->createWebUrl('replymanage',array());?>'+'&refreshOpener=yes&noticeId='+id,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
	   		
	 xmlHttp.onreadystatechange = function(){
			window.location.reload();
			}
		
	}
	
	function ExportNoticeExcel(){
		var selectone = document.getElementById("range"); 
	    var index = selectone.selectedIndex;
	    var value = selectone.options[index].value;
		var indata=document.getElementById("indata").value; 
		window.location.href='<?php echo $this->createWebUrl('exportnotice',array());?>'+'&range='+value+'&indata='+indata;		
	}
	
	function editNotice(id){  	
		window.open('<?php echo $this->createWebUrl('editnotice',array());?>'+'&noticeId='+id,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}	
	}
</script>
</html>