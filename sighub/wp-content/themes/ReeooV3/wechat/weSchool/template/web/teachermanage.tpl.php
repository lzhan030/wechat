<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php
//20140624 janeen update
//$weid=$_SESSION['WEID'];
$gweid=$_SESSION['GWEID'];
//end
?>

<script>

    function selSearchType(value)
	{
	    if(value == "teachergrade")
	    {
	        document.getElementById('indata').style.display="none";
			document.getElementById('ygrade').style.display="block";
			document.getElementById('class').style.display="block";
	    }
		else
		{
		    document.getElementById('indata').style.display="block";
			document.getElementById('ygrade').style.display="none";
			document.getElementById('class').style.display="none";
		}
	}

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
		$("#fileupload").wrap("<form id='myupload' action='<?php echo $this->createWebUrl('uploadteacher',array());?>' method='post' enctype='multipart/form-data'></form>");
		
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
	
</script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadexcel.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
</style>
<!--<div class="bgimg"></div>-->
<input type="button" class="btn btn-sm btn-primary" onclick="location.href='<?php echo $this->createWebUrl('addteacher');?>'" name="del" id="buttondel" value="添加新教师" style="margin-left:30px; margin-top:20px;">	
<input type="button" class="btn btn-sm btn-default" onclick="location.href='<?php echo $this->createWebUrl('personmanage',array('id' => 3));?>'" name="del" id="adds" value="返回" style="margin-top:20px;">
<!--2014-07-07新增修改
<input type="button" class="personentry btn btn-sm btn-info"  title="<?php //echo $this->createWebUrl('addteacher');?>" name="del" id="buttondel" value="添加新教师">	-->
<div class="main_auto">
<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('searchteacher');?>" method="post" enctype="multipart/form-data">	
<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
	<div class="panel-heading">教师列表</div>
	<table class="table table-striped" width="800" border="0" align="center">
	<tbody>
	
	    <tr>
			 <td colspan=10 scope="col" width="100" align="left" >
				 <!--<select id="range" name="range" onchange="selSearchType(this.options[this.selectedIndex].value)">-->
				 <select id="range" name="range">
					<option value="">请选择
					<option value="all">全部</option>
					<option value="tea_name">教师姓名</option>
					<option value="tea_gradeclass">所在年级/班级</option>
				</select>
			    <input id="indata" name="indata" value="" />
				<input id="search1" class="btn btn-warning btn-sm" type="submit" value="查询" />
				<div class="btnupload">
					<span>导入Excel</span>
					<input id="fileupload" type="file" name="inputExcel">
				</div>
				
				

				
			</td>
		</tr>
	
		<tr>
			<!--<td scope="col" width="30" align="center">
			    <input type="checkbox" name="checkUser[]" value="check_user" style="20px 10px 0px 50px"></input>
			</td>-->
			<td scope="col" width="100" align="center" style="font-weight:bold">教师名</td>
			<td scope="col" width="150" align="center" style="font-weight:bold">性别</td>
			<td scope="col" width="200" align="center" style="font-weight:bold">年级/班级</td>
			<!--<td scope="col" width="100" align="center" style="font-weight:bold">所在班级</td>-->
			<td scope="col" width="150" align="center" style="font-weight:bold">验证码</td>
			<td scope="col" width="150" align="center" style="font-weight:bold">操作</td>
		</tr>
		
		
		<?php 
		
		if($f==1){
				
	
		$pagesize=6; //设定每一页显示的记录数						
		//-----------------------------------------------------------------------------------------------//
		//分页逻辑处理
		//-----------------------------------------------------------------------------------------------
		
		//$vmembersCount = doWebCountTeacher($in,$r);
		//echo $vmembersCount;//获取表的记录总数
		foreach($vmembersCount as $vmembersnumber){
			// $countnumber=$vmembersnumber->memberCount;
			$countnumber=$vmembersnumber['memberCount'];
		}
		
		$pages=intval($countnumber/$pagesize); //计算总页数

		if ($countnumber % $pagesize) $pages++;

		//设置缺省页码
		//↓判断“当前页码”是否赋值过
		if (isset($_GET['teacherpage'])){ $page=intval($_GET['teacherpage']); }else{ $page=1; }//否则，设置为第一页
	   
		//↓计算记录偏移量
			$offset=$pagesize*($page - 1);

		//↓读取指定记录数
		//20140624 janeen update
		//$rs=$this -> doWebCountTeacherSearchPage($in,$r,$offset,$pagesize,$weid);//取得—当前页—记录集！
		$rs=$this -> doWebCountTeacherSearchPage($in,$r,$offset,$pagesize,$gweid);//取得—当前页—记录集！
		//end
		if($rs!==false){

		foreach($rs as $teacher) {
		
	?>
	   <tr>
			<!--<td align="center"><input type="checkbox" name="checkUser[]" value="check_user" style="20px 10px 0px 50px"></input></td>-->
			<td align="center"><?php echo $teacher['tea_name']; ?> </td>
			<td align="center"><?php if($teacher['tea_sex'] == 0){ echo '女';}else {echo '男';} ?></td>
			<td align="center"><?php echo $teacher['tea_gradeclass'] ?></td>
			<!--<td align="center"><?php //echo $teacher->tea_ugrade ?></td>
			<td align="center"><?php //echo $teacher->tea_class; ?> </td>-->
			<td align="center"><?php echo $teacher['tea_vericode']; ?></td>
			<td class="row" align="center">
   			 <input type="button" class="btn btn-sm btn-warning" onclick="delteacher('<?php echo $teacher['tea_id'] ?>')" name="del" id="buttondel" value="删除"> 
			  <!--<input type="button" class="btn btn-sm btn-warning" onclick="location.href='<?php //echo $this->createWebUrl('teachermanage',array('del'=>$teacher->tea_id));?>'" name="del" id="buttondel" value="删除">-->
			  <input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('editteacher',array('id' => $teacher['tea_id']));?>'" name="upd" id="buttonupd" value="更新"> </td>
			
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
		    echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $first, 'f' => $f, 'in'=>$in, 'r' => $r))}'>首页</a>  ";
			echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $prev, 'f' => $f, 'in'=>$in, 'r' => $r))}'>上一页</a>  ";
			//echo "<a href='?admin&page=usermanage&userpage=".$first."&f=".$f."&in=".$in."&r=".$r."'>首页</a>  ";
			//echo "<a href='?admin&page=usermanage&userpage=".$prev."&f=".$f."&in=".$in."&r=".$r."'>上一页</a>  ";
		}

	if ($page < $pages)
		{
		     echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $next, 'f' => $f, 'in'=>$in, 'r' => $r))}'>下一页</a>  ";
			 echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $last, 'f' => $f, 'in'=>$in, 'r' => $r))}'>尾页</a>  ";
			//echo "<a href='?admin&page=usermanage&userpage=".$next."&f=".$f."&in=".$in."&r=".$r."'>下一页</a>  ";
			//echo "<a href='?admin&page=usermanage&userpage=".$last."&f=".$f."&in=".$in."&r=".$r."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
	echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

	for ($i=1;$i< $page;$i++)
	{
	   echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r))}'>[".$i ."]</a>  ";
	   //echo "<a href='?admin&page=usermanage&userpage=".$i."&f=".$f."&in=".$in."&r=".$r."'>[".$i ."]</a>  ";
	   
	   }  // 1-先输出当前页之前的

	if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

	for ($i=$page+1;$i<=$pages;$i++)
	{
	   echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r))}'>[".$i ."]</a>  ";
	   //echo "<a href='?admin&page=usermanage&userpage=".$i."&f=".$f."&in=".$in."&r=".$r."'>[".$i ."]</a>  ";
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
			//-----------------------------------------------------------------------------------------------
			//$tmpArr = mysql_fetch_array($rs);
	        //$numAL = mysql_num_rows($site_list);  //取得记录总数$rs
			
			//$websitesCount=web_admin_count_website($current_user->ID);//获取记录总数
			//$usersCount = web_admin_count_teacher();
			//echo $usersCount;//获取wp_users表的记录总数
			
			foreach($usersCount as $usersnumber){
				 //$countnumber=$usersnumber->userCount;
				 $countnumber=$usersnumber['userCount'];
			}
			
			$pages=intval($countnumber/$pagesize); //计算总页数

			if ($countnumber % $pagesize) $pages++;

			//设置缺省页码
			//↓判断“当前页码”是否赋值过
			if (isset($_GET['teacherpage'])){ $page=intval($_GET['teacherpage']); }else{ $page=1; }//否则，设置为第一页
           
			//↓计算记录偏移量
				$offset=$pagesize*($page - 1);

			//↓读取指定记录数
				//20140624 janeen update
				//$rspage=$this -> doWebCountTeacherPage($offset,$pagesize,$weid);//取得—当前页—记录集！
				$rspage=$this -> doWebCountTeacherPage($offset,$pagesize,$gweid);//取得—当前页—记录集！
				//end
				//一个function活的总个数
				//foreach (){$curNem=iii->as bianliang}
				//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
				
				//$arraysCount=web_admin_array_website_count($offset,$pagesize,$current_user->ID);
				
		?> 
		
		
		<?php
		
		foreach ($rspage as $teacher) {
		?>
		<tr>
			<!--<td align="center"><input type="checkbox" name="checkUser[]" value="check_user" style="20px 10px 0px 50px"></input></td>-->
			<td align="center"><?php echo $teacher['tea_name']; ?> </td>
			<td align="center"><?php if($teacher['tea_sex'] == 0){ echo '女';}else {echo '男';} ?></td>
			<td align="center"><?php echo $teacher['tea_gradeclass'] ?></td>
			<!--<td align="center"><?php //echo $teacher->tea_ugrade ?></td>
			<td align="center"><?php //echo $teacher->tea_class; ?> </td>-->
			<td align="center"><?php echo $teacher['tea_vericode']; ?></td>
			<td class="row" align="center">
   			  <input type="button" class="btn btn-sm btn-warning" onclick="delteacher('<?php echo $teacher['tea_id'] ?>')" name="del" id="buttondel" value="删除"> 
			  <!--<input type="button" class="btn btn-sm btn-warning" onclick="location.href='<?php //echo $this->createWebUrl('teachermanage',array('del'=>$teacher->tea_id));?>'" name="del" id="buttondel" value="删除">-->
			  <input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('editteacher',array('id' => $teacher['tea_id']));?>'" name="upd" id="buttonupd" value="更新"> 
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
		    echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $first))}'>首页</a>  ";
			echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $prev))}'>上一页</a>  ";
		    //echo "<a href='?admin&page=usermanage&userpage=".$first."'>首页</a>  ";
		    //echo "<a href='?admin&page=usermanage&userpage=".$prev."'>上一页</a>  ";
		}

		if ($page < $pages)
		{
		    echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $next))}'>下一页</a>  ";
			echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $last))}'>尾页</a>  ";
		    //echo "<a href='?admin&page=usermanage&userpage=".$next."'>下一页</a>  ";
		    //echo "<a href='?admin&page=usermanage&userpage=".$last."'>尾页</a>  ";
		}

		//============================//
		//  翻页显示 二               
		//============================//
		echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

		for ($i=1;$i< $page;$i++)
		{
		    echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $i))}'>[".$i ."]</a>  ";
		    //echo "<a href='?admin&page=usermanage&userpage=".$i."'>[".$i ."]</a>  ";
		}  // 1-先输出当前页之前的

		if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

		for ($i=$page+1;$i<=$pages;$i++)
		{
		    echo "<a href='{$this->createWebUrl('teachermanage',array('teacherpage' => $i))}'>[".$i ."]</a>  ";
		    //echo "<a href='?admin&page=usermanage&userpage=".$i."'>[".$i ."]</a>  ";
		}// 3-接着输出当前页之后

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
    function delteacher(id){
       if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			//xmlHttp.open("GET","?admin&page=usermanage&del="+id,true);
			xmlHttp.open("GET","<?php echo $this->createWebUrl('teachermanage',array());?>"+"&del="+id,true);
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