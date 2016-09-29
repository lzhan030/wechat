<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php $gweid=$_SESSION['GWEID'];?>
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	.search{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
</style>
<div class="main_auto">
<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <font class="fontpurple">物流公司管理</font></div>
</div>
<input type="button" class="btn btn-primary" onclick="couriersadd()" name="add" id="buttonadd" value="创建新物流公司" style="margin-top:20px;"/>
<form name ="content" method="post" enctype="multipart/form-data">	
<div class="panel panel-default" style="margin-right:30px; margin-top:10px">
	<div class="panel-heading">物流公司列表</div>
	<table class="table table-striped" width="500" border="0" align="center">
		<tbody>
			<tr>
				<td scope="col" width="100" align="center" style="font-weight:bold">物流公司编号</td>
				<td scope="col" width="90" align="center" style="font-weight:bold">名称</td>
				<td scope="col" width="90" align="center" style="font-weight:bold">操作</td>
			</tr>
			<?php			
			$pagesize=6; //设定每一页显示的记录数						
			//分页逻辑处理
			foreach($couriersCount as $couriersnumber){
				 $countnumber=$couriersnumber->couriersCount;
			}
			$pages=intval($countnumber/$pagesize); //计算总页数
			if ($countnumber % $pagesize) $pages++;
				//判断“当前页码”是否赋值过，否则，设置为第一页
				if (isset($_GPC['courierspage'])){ $page=intval($_GPC['courierspage']); }else{ $page=1; }
				//计算记录偏移量
				$offset=$pagesize*($page - 1);
				//读取指定记录数
				$rspage=$this -> doWebCountcourierspage($offset,$pagesize,$gweid);
				foreach ($rspage as $courier) {
				?>
				<tr>
					<td align="center"><?php echo $courier->id; ?></td>
					<td align="center"><?php echo $courier->courier_name; ?></td>
					<td class="row" align="center">
						<input type="button" class="btn btn-sm btn-info" onclick="couriersupdate('<?php echo $courier->id; ?>')" name="courierupdate" id="courierupdate" value="更新">
						<input type="button" class="btn btn-sm btn-warning" onclick="couriersdel('<?php echo $courier->id; ?>')" value="删除"></button>
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
	echo "<p>";  //  align=center
	$first=1;
	$prev=$page-1;   
	$next=$page+1;
	$last=$pages;

	if ($page > 1){
		echo "<a href='{$this->createWebUrl('couriersmanage',array('courierspage' => $first))}'>首页</a>  ";
		echo "<a href='{$this->createWebUrl('couriersmanage',array('courierspage' => $prev))}'>上一页</a>  ";
	}

	if ($page < $pages){
		echo "<a href='{$this->createWebUrl('couriersmanage',array('courierspage' => $next))}'>下一页</a>  ";
		echo "<a href='{$this->createWebUrl('couriersmanage',array('courierspage' => $last))}'>尾页</a>  ";
	}
	//  翻页显示 二	
	echo " | 共有 ".$pages." 页(".$page."/".$pages.")";
	for ($i=1;$i< $page;$i++){
		echo "<a href='{$this->createWebUrl('couriersmanage',array('courierspage' => $i))}'>[".$i ."]</a>  ";
	}
	if ($page > 0) echo " [".$page."]"; // 2-再输出当前页
	for ($i=$page+1;$i<=$pages;$i++){
		echo "<a href='{$this->createWebUrl('couriersmanage',array('courierspage' => $i))}'>[".$i ."]</a>  ";
	}// 3-接着输出当前页之后
		echo "</p>";
 
 ?>
 </body>
 <script language="javascript" type="text/javascript">
	function couriersdel(id){
		$.ajax({
			url:window.location.href, 
			type: "POST",
			data:{'courier_del':'isDel','courierid':id},
			success: function(data){
				if (data.status == 'error'){
					alert(data.message);
				}else if (data.status == 'success'){
					window.location.reload();
				}
			},
			 error: function(data){
				alert("出现错误,请重试");
			},
			dataType: 'json'
		});				
	}
	function couriersadd(){
		window.open('<?php echo $this->createWebUrl('couriershandle',array());?>','_blank','height=450,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}
	}
	function couriersupdate(id){  	
		window.open('<?php echo $this->createWebUrl('couriershandle',array());?>'+'&courierid='+id,'_blank','height=450,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}	
	}
	</script>
</html>