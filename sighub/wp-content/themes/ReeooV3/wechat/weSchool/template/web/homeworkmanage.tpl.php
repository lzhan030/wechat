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
	<meta name="Homeworkmanage" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
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
		<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('searchhomework',array('GWEID' => ''));?>" method="post" enctype="multipart/form-data">			
		<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
			<div class="panel-heading">作业列表</div>
			<table class="table table-striped" width="800" border="0" align="center" >
			<tbody>
				<tr>
					<td colspan=8 scope="col" width="100" align="left" >
						<select id="range" name="range">
							<option value="">请选择</option>
							<option value="all">全部</option>
							<option value="homework_gradeclass">年级班级</option>
							<option value="homework_starttime">开始时间</option>
							<option value="homework_endtime">结束时间</option>
						</select>
						<input id="indata" name="indata" value="" />
						<input id="search" class="btn btn-default btn-sm" type="submit" style="margin-right:0px;margin-bottom:0px" value="查询"/>
						<input type="button" class="btn btn-info btn-sm" onclick="ExportHomeworkExcel()" value="导出" />
						<input type="button" class="btn btn-warning btn-sm" onclick="insertHomework()" name="add" id="buttonadd" value="新建" />
					</td>
				</tr>
				<tr>
					<td scope="col" width="100" align="center" style="font-weight:bold">作业编号</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">作业标题</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">作业内容</td>
					<!--<td scope="col" width="100" align="center" style="font-weight:bold">年级</td>-->
					<!--<td scope="col" width="100" align="center" style="font-weight:bold">班级</td>-->
					<td scope="col" width="100" align="center" style="font-weight:bold">年级/班级</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">开始时间</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">结束时间</td>
					<td scope="col" width="100" align="center" style="font-weight:bold">操作</td>
				</tr>
				<?php
				if($f==1){
					
					$pagesize=5; //设定每一页显示的记录数
					
					foreach($homeworksCount as $hk){
						 $countnumber=$hk['homeworkCount'];						 
					}
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GET['homeworkpage'])){ $page=intval($_GET['homeworkpage']); }else{ $page=1; }//否则，设置为第一页
				   
					//↓计算记录偏移量
					$offset=$pagesize*($page - 1);

					//↓读取指定记录数
					//20140624 janeen update
					//$rs=$this -> doWebCountHomeworkSearchPage($_SESSION['WEID'],$in,$r,$offset,$pagesize);
					$rs=$this -> doWebCountHomeworkSearchPage($_SESSION['GWEID'],$in,$r,$offset,$pagesize);
					//end
					if($rs!==false){
					
					foreach($rs as $homework) {
				?>
				<tr>
					<td align="center"><?php echo $homework['homework_id']; ?></td>
					<?php if(mb_strlen($homework['homework_title'])>5){ ?>
							<td align="center" title=<?php echo $homework['homework_title'];?>><?php echo mb_substr($homework['homework_title'],0,5,'UTF-8').'......'; ?>  </td>
					<?php	}else{ ?>
							<td align="center" ><?php echo $homework['homework_title']; ?>  </td>
					<?php	} 	?>
					<?php if(mb_strlen($homework['homework_content'])>5){ ?>
							<td align="center" title=<?php echo $homework['homework_content'];?>><?php echo mb_substr($homework['homework_content'],0,5,'UTF-8').'......'; ?>  </td>
					<?php	}else{ ?>
							<td align="center" ><?php echo $homework['homework_content']; ?>  </td>
					<?php	} 	?>
					<!--<td align="center"><?php echo $homework['homework_ugrade']; ?> </td>-->
					<!--<td align="center"><?php echo $homework['homework_class']; ?> </td>-->
					<td align="center">
					<?php //echo $homework['homework_gradeclass']; ?> 
					<?php 
						if(strpos($homework['homework_gradeclass'],'*')===false)
							echo $homework['homework_gradeclass'];
						else if((strpos($homework['homework_gradeclass'],'*')!==false) && ($homework['homework_gradeclass'] != '*') )
							echo substr($homework['homework_gradeclass'],0,4)."年级";
						else if($homework['homework_gradeclass'] == '*')
							echo "所有年级";  ?>

					</td>
					<td align="center"><?php echo $homework['homework_starttime']; ?> </td>
					<td align="center"><?php echo $homework['homework_endtime']; ?> </td>
					<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick='delHomework("<?php echo $homework['homework_id']?>")' name="del" id="buttondel" value="删除"> 
					<input type="button" class="btn btn-sm btn-info" onclick='editHomework("<?php echo $homework['homework_id']?>")' name="upd" id="buttonupd" value="更新"> 
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
					
						echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $first, 'f' => $f, 'in'=>$in, 'r' => $r))}'>首页</a>  ";
						echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $prev, 'f' => $f, 'in'=>$in, 'r' => $r))}'>上一页</a>  ";
										
					}

				if ($page < $pages)
					{
						echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $next, 'f' => $f, 'in'=>$in, 'r' => $r))}'>下一页</a>  ";
						echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $last, 'f' => $f, 'in'=>$in, 'r' => $r))}'>尾页</a>  ";
					
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){
					echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r))}'>[".$i ."]</a>  ";
				
				}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){
					echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r))}'>[".$i ."]</a>  ";
				}// 3-接着输出当前页之后
				
				echo "</p>";
				}if($rs!==False && empty($rs)&&!isset($_REQUEST['deleteflag'])){ 
					    echo "<script language='javascript'>alert('没有符合该条件的查询结果');</script>";
					}
				}else{			
					
					$pagesize=5; //设定每一页显示的记录数						
					
					foreach($homeworkCount as $hw){
						 $countnumber=$hw['homeworkCount'];
						 
					}
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GET['homeworkpage'])){ $page=intval($_GET['homeworkpage']); }else{ $page=1; }//否则，设置为第一页
				   
					//↓计算记录偏移量
					$offset=$pagesize*($page - 1);

					//↓读取指定记录数
					//20140624 janeen update
					//$rs=$this -> doWebCountHomeworkPage($_SESSION['WEID'],$offset,$pagesize);//取得—当前页—记录集！
					$rs=$this -> doWebCountHomeworkPage($_SESSION['GWEID'],$offset,$pagesize);//取得—当前页—记录集！
					//end
				?> 
				
				<?php
		
					foreach ($rs as $homework) {
				?>
				<tr>

						<td align="center"><?php echo $homework['homework_id']; ?></td>
						<?php if(mb_strlen($homework['homework_title'])>5){ ?>
							<td align="center" title=<?php echo $homework['homework_title'];?>><?php echo mb_substr($homework['homework_title'],0,5,'UTF-8').'......'; ?>  </td>
						<?php	}else{ ?>
								<td align="center" ><?php echo $homework['homework_title']; ?>  </td>
						<?php	} 	?>
						<?php if(mb_strlen($homework['homework_content'])>5){ ?>
								<td align="center" title=<?php echo $homework['homework_content'];?>><?php echo mb_substr($homework['homework_content'],0,5,'UTF-8').'......'; ?>  </td>
						<?php	}else{ ?>
								<td align="center" ><?php echo $homework['homework_content']; ?>  </td>
						<?php	} 	?>
						<!--<td align="center"><?php echo $homework['homework_ugrade']; ?> </td>-->
						<!--<td align="center"><?php echo $homework['homework_class']; ?> </td>-->
						<td align="center"><?php //echo strlen($homework['homework_gradeclass'])==5?substr($homework['homework_gradeclass'],0,4):$homework['homework_gradeclass']; ?> 
						<?php 
						//2014-07-12新增修改
						/* echo $homework['homework_gradeclass']."对应的班级";
						echo strlen($homework['homework_gradeclass'])."对应的长度";	 */
						if(strpos($homework['homework_gradeclass'],'*')===false)
							echo $homework['homework_gradeclass'];
						else if((strpos($homework['homework_gradeclass'],'*')!==false) && ($homework['homework_gradeclass'] != '*') )
							echo substr($homework['homework_gradeclass'],0,4)."年级";
						else if($homework['homework_gradeclass'] == '*')
							echo "所有年级";  ?>
						</td>
						<td align="center"><?php echo $homework['homework_starttime']; ?> </td>
						<td align="center"><?php echo $homework['homework_endtime']; ?> </td>
					<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick='delHomework("<?php echo $homework['homework_id']?>")' name="del" id="buttondel" value="删除"> 
					<input type="button" class="btn btn-sm btn-info" onclick='editHomework("<?php echo $homework['homework_id']?>")' name="upd" id="buttonupd" value="更新"> 
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
						echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $first))}'>首页</a>  ";
						echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $prev))}'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $next))}'>下一页</a>  ";
						echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $last))}'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){
					echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $i))}'>[".$i ."]</a>  ";
				
				}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){
					echo "<a href='{$this->createWebUrl('homeworkmanage',array('homeworkpage' => $i))}'>[".$i ."]</a>  ";
				
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
	function delHomework(id){  	
		if(confirm("确定删除吗？")){	
			createXMLHttpRequest();
			xmlHttp.open("GET","<?php echo $this->createWebUrl('homeworkmanage',array());?>"+"&del="+id,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
					//alert("删除成功！");
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
	
	function ExportHomeworkExcel(){
		var selectone = document.getElementById("range"); 
	    var index = selectone.selectedIndex;
	    var value = selectone.options[index].value;
		var indata=document.getElementById("indata").value; 
		window.location.href='<?php echo $this->createWebUrl('exporthomework',array());?>'+'&range='+value+'&indata='+indata;		
	}
	function insertHomework(){
		window.open('<?php echo $this->createWebUrl('addhomework',array());?>','_blank','height=620,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}
	}
	function editHomework(id){  	
		window.open('<?php echo $this->createWebUrl('edithomework',array());?>'+'&homeworkId='+id,'_blank','height=620,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}	
	}
</script>
</html>