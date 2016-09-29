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
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
	<style type="text/css">
		.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	</style>

<div class="main_auto">
<form name ="content" onSubmit="return validateform()" action="<?php echo $this->createWebUrl('searchvideo');?>" method="post" enctype="multipart/form-data">	
<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
	<div class="panel-heading">视频照片列表</div>
	<table class="table table-striped" width="900" border="0" align="center">
			<tbody>
				<tr>
					<td colspan=10 scope="col" width="100" align="left" >
						<select id="range" name="range">
							<option value="">请选择
							<option value="all">全部</option>
							<!--<option value="video_title">视频标题</option>-->
							<option value="video_title">标题</option>
							<option value="video_time">上传时间</option>
							<option value="video_gradeclass">年级/班级</option>
							<option value="video_publisher">发布者</option>
						</select>
						<input id="indata" name="indata" value="" style="margin-left:3px;" />
						<input id="search1" class="btn btn-info btn-sm" type="submit" value="查询" />
					</td>
				</tr>
				<tr>
						<!--<td scope="col" width="150" align="center" style="font-weight:bold">视频标题</td>-->
						<td scope="col" width="150" align="center" style="font-weight:bold">标题</td>
						<td scope="col" width="100" align="center" style="font-weight:bold">类型</td>
						<!--<td scope="col" width="100" align="center" style="font-weight:bold">视频url</td>-->
						<td scope="col" width="150" align="center" style="font-weight:bold">上传时间</td>
						<!--<td scope="col" width="100" align="center" style="font-weight:bold">学生年级</td>
						<td scope="col" width="100" align="center" style="font-weight:bold">学生班级</td>-->
						<td scope="col" width="100" align="center" style="font-weight:bold">年级/班级</td>
						<td scope="col" width="100" align="center" style="font-weight:bold">发布者</td>
						<td scope="col" width="150" align="center" style="font-weight:bold">操作</td>
				</tr>
					<?php
					if($f==1){
					$pagesize=6; //设定每一页显示的记录数						
					//-----------------------------------------------------------------------------------------------//
					//分页逻辑处理
					//-----------------------------------------------------------------------------------------------
					
					foreach($vidCount as $vidnumber){
				 //$countnumber=$usersnumber->userCount;
					$countnumber=$vidnumber['videoCount'];
						 
					}
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GPC['videopage'])){ $page=intval($_GPC['videopage']); }else{ $page=1; }//否则，设置为第一页
				   
					//↓计算记录偏移量
						$offset=$pagesize*($page - 1);

					//↓读取指定记录数
					//20140624 janeen update
					//$rs=$this -> doWebCountVideoSearchPage($in,$r,$offset,$pagesize,$weid);//取得—当前页—记录集！
					$rs=$this -> doWebCountVideoSearchPage($in,$r,$offset,$pagesize,$gweid);//取得—当前页—记录集！
					//end
					if($rs!==false){
					foreach($rs as $video) {
				?>
				<tr>
						<?php 
						$vt=mb_substr($video['video_title'],0,8,'UTF-8');
						if(mb_strlen($video['video_title'])>8){
						//echo "<td title=\"{$video['video_title']}\">{$vt}...</td>";}//这种写法也可以
						echo "<td align='center' title='{$video['video_title']}'>{$vt}...</td>";}
						else 
						{ echo " <td align='center'>$vt</td> ";}
						?>
						<td align="center"><?php if($video['type'] == "video")echo "视频"; else echo "图片"; ?> </td>
						<!--<td align="center"><?php //echo $video['video_url']; ?> </td>-->
						<td align="center"><?php echo $video['video_time']; ?> </td>
						<!--<td align="center"><?php echo $video['video_ugrade']; ?> </td>
						<td align="center"><?php echo $video['video_class']; ?> </td>-->
						<td align="center">
						    <?php 
							//2014-07-12新增修改
							
							if(strlen($video['video_gradeclass']) === 4 )
                                echo $video['video_gradeclass']."年级";
							else if(strlen($video['video_gradeclass']) >= 6)
							     echo $video['video_gradeclass'];
							else if($video['video_gradeclass'] == '*')
							    echo "所有年级";  ?>

						</td>
						<td align="center"><?php echo $video['tea_name']; ?></td>
						<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick="delvideo('<?php echo $video['video_id'] ?>')" name="del" id="buttondel" value="删除"> 
					<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('editvideo',array('id' => $video['video_id']));?>'" name="upd" id="buttonupd" value="更新"> 
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
		    echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $first, 'f' => $f, 'in'=>$in, 'r' => $r))}'>首页</a>  ";
			echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $prev, 'f' => $f, 'in'=>$in, 'r' => $r))}'>上一页</a>  ";
		}

		if ($page < $pages)
		{
		     echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $next, 'f' => $f, 'in'=>$in, 'r' => $r))}'>下一页</a>  ";
			 echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $last, 'f' => $f, 'in'=>$in, 'r' => $r))}'>尾页</a>  ";
		}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++)
				{
					echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r))}'>[".$i ."]</a>  ";
	   
				}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页
				for ($i=$page+1;$i<=$pages;$i++)
				{
				echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $i, 'f' => $f, 'in'=>$in, 'r' => $r))}'>[".$i ."]</a>  ";
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
					foreach($vsCount as $vidnumber){
				    //$countnumber=$usersnumber->userCount;
					$countnumber=$vidnumber['videoCount'];
					}
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GPC['videopage'])){ $page=intval($_GPC['videopage']); }else{ $page=1; }//否则，设置为第一页
				   
					//↓计算记录偏移量
						$offset=$pagesize*($page - 1);

					//↓读取指定记录数
					//20140624 janeen update
					//$rspage=$this -> doWebCountvideopage($offset,$pagesize,$weid);//取得—当前页—记录集！
					$rspage=$this -> doWebCountvideopage($offset,$pagesize,$gweid);//取得—当前页—记录集！
					//end
				?> 
				
				<?php
		
					foreach ($rspage as $video) {
				?>
						<tr>
						<?php 
						$vt=mb_substr($video['video_title'],0,8,'UTF-8');
						if(mb_strlen($video['video_title'])>8){
						//echo "<td title=\"{$video['video_title']}\">{$vt}...</td>";}//这种写法也可以
						echo "<td align='center' title='{$video['video_title']}'>{$vt}...</td>";}
						else 
						{ echo " <td align='center'>$vt</td> ";}
						?>
						<td align="center"><?php if($video['type'] == "video")echo "视频"; else echo "图片"; ?> </td>
						<!--<td align="center"><?php //echo $video['video_url']; ?> </td>-->
						<td align="center"><?php echo $video['video_time']; ?> </td>
						<!--<td align="center"><?php echo $video['video_ugrade']; ?> </td>
						<td align="center"><?php echo $video['video_class']; ?> </td>-->
						<td align="center"> 
						 <?php 
							//2014-07-12新增修改
							
							if(strlen($video['video_gradeclass']) === 4 )
                                echo $video['video_gradeclass']."年级";
                            else if(strlen($video['video_gradeclass']) >= 6)
							    echo $video['video_gradeclass'];
							else if($video['video_gradeclass'] == '*')
							    echo "所有年级";  ?>

						<td align="center"><?php echo $video['tea_name']; ?></td>
						<td class="row" align="center"><input type="hidden"  value="308" maxlength="100"> 
					<input type="button" class="btn btn-sm btn-warning" onclick="delvideo('<?php echo $video['video_id'] ?>')" name="del" id="buttondel" value="删除"> 
					<input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php echo $this->createWebUrl('editvideo',array('id' => $video['video_id']));?>'" name="upd" id="buttonupd" value="更新"> 
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
						echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $first))}'>首页</a>  ";
						echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $prev))}'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $next))}'>下一页</a>  ";
						echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $last))}'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";
			for ($i=1;$i< $page;$i++)
			{
				echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $i))}'>[".$i ."]</a>  ";
			}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

					for ($i=$page+1;$i<=$pages;$i++)
			{
				echo "<a href='{$this->createWebUrl('videomanage',array('videopage' => $i))}'>[".$i ."]</a>  ";
		   
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
    function delvideo(id){
       if(confirm("确定删除吗？")){
			createXMLHttpRequest();
			//xmlHttp.open("GET","?admin&page=usermanage&del="+id,true);
			xmlHttp.open("GET","<?php echo $this->createWebUrl('videomanage',array());?>"+"&del="+id,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
					//alert(window.location.href);
					if((window.location.href).indexOf("deleteflag") == -1)
					{
					    alert("删除成功");
					    window.location.href = window.location.href + "&deleteflag";
					}
					else
					{
					   	alert("删除成功");
					    window.location.reload();
					}
					
				}
			}
			xmlHttp.send(null);
		}
	} 
</script>