<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php
//20140624 janeen update
//$weid=$_SESSION['WEID'];
$gweid=$_SESSION['GWEID'];
//end
?>
 <script language="javascript">
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
	//上传excel文件
	$(function () {
		$("#fileupload").wrap("<form id='myupload' action='<?php echo $this->createWebUrl('uploadstudent',array());?>' method='post' enctype='multipart/form-data'></form>");
		
		$("#fileupload").change(function(){
			$("#myupload").ajaxSubmit({
				//dataType:  'json',
				dataType:  'text',
				
				success: function(data) {
				    alert(data);
					window.location.reload();
				},
				error:function(xhr){
					alert("导入失败");
				}
			});return false;
		});
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
		
		
		//再测试下吧
		/* $(".personentry").live("click",function(e){
		
				e=e||window.event
				e.preventDefault();
				$("#personmanage").load("<?php echo $this->createWebUrl('addstudent');?>",function(){
					console.log($(".personentry").attr("onlick").split("=")[1]);
				});
				return false;
				

			}); */
	
    });
	
</script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadexcel.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
</style>

<input type="button" class="personentry btn btn-sm btn-primary" onclick="location.href='<?php echo $this->createWebUrl('addstudent');?>'" name="del" id="adds" value="添加新学生" style="margin-left:30px; margin-top:20px;">
<input type="button" class="btn btn-sm btn-default" onclick="location.href='<?php echo $this->createWebUrl('personmanage',array('id' => 3));?>'" name="del" id="adds" value="返回" style="margin-top:20px;">
<div class="main_auto">
<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('searchstudent');?>" method="post" enctype="multipart/form-data">	
<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
	<div class="panel-heading">学生列表</div>
	<table class="table table-striped" width="800" border="0" align="center">
			<tbody>
				<tr>
					<td colspan=10 scope="col" width="100" align="left" >
						 <select id="range" name="range">
							<option value="">请选择
							<option value="all">全部</option>
							<option value="stu_number">学生学号</option>
							<option value="stu_name">学生姓名</option>
							<option value="stu_gradeclass">所在年级/班级</option>				
					<input id="indata" name="indata" value="" />
					<input id="search1" class="btn btn-sm btn-warning" type="submit" value="查询" />
					<div class="btnupload">
					<span>导入Excel</span>
					<input id="fileupload" type="file" name="inputExcel">
					</div>
					</td>
				</tr>
				<tr>
						<td scope="col" width="100" align="center" style="font-weight:bold">学生学号</td>
						<td scope="col" width="100" align="center" style="font-weight:bold">学生姓名</td>
						<td scope="col" width="100" align="center" style="font-weight:bold">学生性别</td>
						<td scope="col" width="100" align="center" style="font-weight:bold">学生年龄</td>
						<td scope="col" width="100" align="center" style="font-weight:bold">年级/班级</td>
						<td scope="col" width="100" align="center" style="font-weight:bold">验证码</td>
						<td scope="col" width="100" align="center" style="font-weight:bold">操作</td>
				</tr>
					<?php
					if($f==1){
					$pagesize=6; //设定每一页显示的记录数						
					//-----------------------------------------------------------------------------------------------//
					//分页逻辑处理
					//-----------------------------------------------------------------------------------------------
					
					foreach($smembersCount as $vmembersnumber){
						$countnumber=$vmembersnumber['memberCount'];
						 
					}		
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GPC['studentpage'])){ $page=intval($_GPC['studentpage']); }else{ $page=1; }//否则，设置为第一页
					//↓计算记录偏移量
						$offset=$pagesize*($page - 1);

					//↓读取指定记录数
					//20140624 janeen update
					//$rs=$this -> doWebCountStudentSearchPage($in,$r,$offset,$pagesize,$weid);//取得—当前页—记录集！
					$rs=$this -> doWebCountStudentSearchPage($in,$r,$offset,$pagesize,$gweid);//取得—当前页—记录集！
					//end
					if($rs!==false){
					foreach($rs as $student) {
				?>
				<tr>
						<td align="center"><?php echo $student['stu_number']; ?></td>
						<td align="center"><?php echo $student['stu_name']; ?> </td>
						<td align="center">
						<?php
							$sex=$student['stu_sex'];
							//echo "这是".$sex;
							if ($sex==0)
							echo '男';
							else  echo '女';
						?></td>
						<td align="center">
						<?php
							$birth=$student['stu_birth'];
							list($by,$bm,$bd)=explode('-',$birth);
							$cm=date('n');
							$cd=date('j');
							$age=date('Y')-$by-1;
							if ($cm>$bm || $cm==$bm && $cd>$bd) $age++;
							echo "$age";
						?></td>
						<!--<td align="center"><?php echo $student['stu_ugrade']; ?> </td>
						<td align="center"><?php echo $student['stu_class']; ?> </td>-->
						<td align="center"><?php echo $student['stu_gradeclass']; ?> </td>
						<td align="center"><?php echo $student['stu_vericode']; ?></td>
					<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick="delstudent('<?php echo $student['stu_id'] ?>')" name="del" id="buttondel" value="删除"> 
					<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('editstudent',array('id' => $student['stu_id']));?>'" name="upd" id="buttonupd" value="更新"> 
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
		    echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $first, 'f' => $f, 'in'=>$in, 'r' => $r))}'>首页</a>  ";
			echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $prev, 'f' => $f, 'in'=>$in, 'r' => $r))}'>上一页</a>  ";
		}

		if ($page < $pages)
		{
		     echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $next, 'f' => $f, 'in'=>$in, 'r' => $r))}'>下一页</a>  ";
			 echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $last, 'f' => $f, 'in'=>$in, 'r' => $r))}'>尾页</a>  ";
		}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++)
				{
					echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r))}'>[".$i ."]</a>  ";
	   
				}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页
				for ($i=$page+1;$i<=$pages;$i++)
				{
				echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r))}'>[".$i ."]</a>  ";
				}// 3-接着输出当前页之后
				echo "</p>";
					}
					if($rs!==False && empty($rs)&&!isset($_GPC['deleteflag']))
					{ 
					    echo "<script language='javascript'>alert('没有符合该条件的查询结果');</script>";
					}
					}else{			
					$pagesize=6; //设定每一页显示的记录数						
					//-----------------------------------------------------------------------------------------------//
					//分页逻辑处理
						foreach($usersCount as $usersnumber){
				 //$countnumber=$usersnumber->userCount;
				 $countnumber=$usersnumber['userCount'];
					}
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GPC['studentpage'])){ $page=intval($_GPC['studentpage']); }else{ $page=1; }//否则，设置为第一页
					//↓计算记录偏移量
						$offset=$pagesize*($page - 1);

					//↓读取指定记录数
					//20140624 janeen update
					//$rspage=$this -> doWebCountStudentPage($offset,$pagesize,$weid);//取得—当前页—记录集！
					$rspage=$this -> doWebCountStudentPage($offset,$pagesize,$gweid);
					//end
				?> 
				
				<?php
		
					foreach ($rspage as $student) {
				?>
					<tr>
						<td align="center"><?php echo $student['stu_number']; ?></td>
						<td align="center"><?php echo $student['stu_name']; ?> </td>
						<td align="center">
						<?php
							$sex=$student['stu_sex'];
							//echo "这是".$sex;
							if ($sex==0)
							echo '男';
							else  echo '女';
						?></td>
						<td align="center">
						<?php
							$birth=$student['stu_birth'];
							list($by,$bm,$bd)=explode('-',$birth);
							$cm=date('n');
							$cd=date('j');
							$age=date('Y')-$by-1;
							if ($cm>$bm || $cm==$bm && $cd>$bd) $age++;
							echo "$age";
						?></td>
						<!--<td align="center"><?php echo $student['stu_ugrade']; ?> </td>
						<td align="center"><?php echo $student['stu_class']; ?> </td>-->
						<td align="center"><?php echo $student['stu_gradeclass']; ?> </td>
						<td align="center"><?php echo $student['stu_vericode']; ?></td>
					<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick="delstudent('<?php echo $student['stu_id'] ?>')" name="del" id="buttondel" value="删除"> 
					<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('editstudent',array('id' => $student['stu_id']));?>'" name="upd" id="buttonupd" value="更新"> 
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
						echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $first))}'>首页</a>  ";
						echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $prev))}'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $next))}'>下一页</a>  ";
						echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $last))}'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";
			for ($i=1;$i< $page;$i++)
			{
				echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $i))}'>[".$i ."]</a>  ";
			}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]"; // 2-再输出当前页

					for ($i=$page+1;$i<=$pages;$i++)
			{
				echo "<a href='{$this->createWebUrl('studentmanage',array('studentpage' => $i))}'>[".$i ."]</a>  ";
		   
			}// 3-接着输出当前页之后
			echo "</p>";

			 } ?>
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
    function delstudent(id){
       if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			//xmlHttp.open("GET","?admin&page=usermanage&del="+id,true);
			xmlHttp.open("GET","<?php echo $this->createWebUrl('studentmanage',array());?>"+"&del="+id,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
					alert("删除成功");
					//alert(window.location.href);
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
</script>