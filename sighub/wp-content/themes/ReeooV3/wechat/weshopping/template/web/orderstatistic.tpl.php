<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

global  $wpdb;
?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.10.2.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
    <title>查看报表</title>
	<script src="<?php bloginfo('template_directory'); ?>/jquery/jquery.json-2.4.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/scripts/common.js"></script>
		
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/excanvas.min.js"></script> 
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/jquery.jqplot.min.js"></script> 
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/jquery/jqplot/jquery.jqplot.min.css" />
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.BezierCurveRenderer.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.pointLabels.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.logAxisRenderer.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.highlighter.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jquery/jqplot/plugins/jqplot.cursor.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/orderstatistic.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/getorderstatistic.js"></script>
	<style>
	#SeSelect
	{
	    margin: -5px 0 0;
        line-height: normal;
	}
	#OSelect
	{
	    margin: -5px 0 0;
        line-height: normal;
	}
	tr{font-size:14px;}
	label{font-weight:100;}
	body {
		font-family: "微软雅黑",Arial,"宋体";
		font-size: 14px;
		line-height: 1.42857;
		color: #222;
	}
	</style>
   
</head>
<?php //require_once 'wp-content/themes/ReeooV3/js/orderstatistic.js'; ?>
<body scroll="no" height="3000px">
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
	<div class="main_auto"><!--主体-->
	<!--主体-标题-->
		<div class="main-title">
			<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> >
			<a href="<?php echo $this->createWebUrl('indexstatistic',array('gweid' => $gweid));?>">统计图表</a> > <font class="fontpurple">订单统计 </font></div>
		</div>
		<div class="submenu"></div>
		<div>
		<table  width="750" height="200" border="0" cellpadding="5px" style=" margin-left:30px; margin-top:30px;">
			<tr>
				<td colspan="3"><label for="name"><b>查询条件: </b></label></td>	
			</tr>
			<tr>
				<td colspan="3"><label for="name" style="margin-left:45px;font-weight:bold;">请选择查询商品分类/商品: </label></td>
			</tr>
			<tr>
				<td width="100"></td>
				<td width="150"><label>商品分类： </label></td>
				<td width="250">
					<select class="form-control" style="margin-right:15px;" id="cate_1" name="cate_1" onchange="fetchChildCategory(this.options[this.selectedIndex].value);">
						<option value="0" selected>全部分类及商品</option>
						<!--微商城下商品类型-->
						<?php  if(is_array($category)) { foreach($category as $row) { ?>
						<?php  if($row['parentid'] == 0) { ?>
						<option value="<?php  echo $row['id'];?>" ><?php  echo $row['name'];?></option>
						<?php  } ?>
						<?php  } } ?>
					</select>
				</td>
				<td width="250">
					<select style="display:none;"  class="form-control input-medium" id="cate_2" name="cate_2" onchange="fetchGood(this.options[this.selectedIndex].value)">
						<option value="0" selected>全部二级分类</option>
					</select>
				</td>
			</tr>
			<tr class="goodsitem" style="display:none;">
				<td></td>
				<td>商品名称：</td>
				<td>
					<select class="form-control" id="goods_select" name="goods_select" autocomplete="off">
						<option value="0" selected>全部商品</option>
					</select>
				</td>
				<td></td>	
			</tr>
			<tr>
				<td colspan="3"><label for="name" style="margin-left:45px;font-weight:bold;">请选择查询时间段: </label></td>
			</tr>			
			<tr>
				<td><input id="SeSelect" valign="middle" align="center" type="radio" name="Selected" onclick="check(this.value)" align="center" value="0" style="margin-left:55px;" checked="true"></td>
				<td><label for="name">输入开始和结束时间: </label></td>
				<td></td>	
			</tr>
			<tr>			
				<td></td>
				<td>
				  <label for="name">开始时间: </label>
				 </td>
				<td><input name="startDate" type="text" class="form-control" id="start_date" size="10" maxlength="10" onclick="new Calendar().show(this);" value="<?php echo date("Y-m-d",strtotime("-6 day")) ?>" /></td>	
			</tr>
			<tr>
				<td></td>
				<td><label for="name">结束时间: </label></td>
				<td><input name="endDate" type="text" class="form-control" id="end_date" size="10" maxlength="10" onclick="new Calendar().show(this);" value="<?php echo date("Y-m-d"); ?>" /></td>
			</tr>
			<tr>
				<td><input id="OSelect" valign="middle" align="center" type="radio" name="Selected" onclick="check(this.value)" align="center" value="1" style="margin-left:55px;"></td>
				<td><label for="name">选择时间段: </label></td>
				<td>
					<select name="period" class="form-control" size="1" type="text;margin-left:500px;" id="periodsel" value="5" maxlength="20" >
						<option value="0">今天</option>
						<option value="1" selected="selected">最近一周</option>
						<option value="2">最近一个月</option>
						<option value="3">最近一个季度</option>
						<option value="4">最近一年</option>
					</select>
				</td>
				<td><input type="button" class="btn btn-primary" style="vertical-align: middle; width:70px;" value="查询" onClick="javascript:showByDays('<?php echo $this->createWebUrl('orderstatistic');?>');"/>
				<input type="button" class="btn btn-warning" style="vertical-align: middle; width:120px;" value="下载统计数据" onClick="javascript:downloadByDays('<?php echo $this->createWebUrl('downloadstatistic');?>');"/></td>
			</tr>
		</table>
		<div id="div1" style="margin-bottom:-60px; margin-left:435px;margin-top:30px; display:none;">每日下单量</div>
		<div id="div2" style="margin-bottom:-60px; margin-left:400px;margin-top:30px; display:none;">当日每时段下单量</div>
		<div id="chart4" style="margin-top:60px; margin-left:0px; width:100%; height:300px;"></div>
		<div id="chart2" style="margin-top:60px; margin-left:0px; width:100%; height:300px; display:none;"></div>
		<div id="div3" style="margin-left:370px;margin-top:45px; margin-bottom:-30px; display:none;">综合分析订单量</div>
		<div id="chart3" style="margin-top:30px; margin-left:0px; width:100%; height:300px;"></div>
		<div id="chart1" style="margin-top:30px; margin-left:0px; width:100%; height:300px; display:none;"></div>
		<div id="div5" style="margin-left:390px;margin-top:45px; margin-bottom:-30px; display:none;">综合分析订单金额</div>
		<div id="chart5" style="margin-top:30px; margin-left:0px; width:100%; height:300px;"></div>
		<div id="chart6" style="margin-top:30px; margin-left:0px; width:100%; height:300px; display:none;"></div>
		</div>
	</div><!--主体结束-->

<script language="javascript">
var category = <?php  echo json_encode($children)?>; 
var good = <?php  echo json_encode($goodsfetch)?>; 
var goodsfetch_parent = <?php  echo json_encode($goodsfetch_parent)?>; 
var htmlgoods = '<option value="0">全部商品</option>';
var nonehtml = '<option value="0">无添加商品</option>';
var html = '<option value="0">全部二级分类</option>';

function fetchChildCategory(cid) {
	//initiate the display
	$('.goodsitem').css('display', 'none');
	$('#cate_2').css('display', 'none');

	pid=$("#cate_1").val();
	if (!category || !category[cid]) {
		$('#cate_2').html();
		$('#cate_2').html(html);
		$('#cate_2').css('display', 'none');
		if(pid=='0'){
			$('.goodsitem').css('display', 'none');
			htmlgoods = '<option value="0">全部商品</option>';
			$('#goods_select').html(htmlgoods);
		}else{
			if (!goodsfetch_parent || !goodsfetch_parent[pid]) {
				$('#goods_select').html();
				$('#goods_select').html(nonehtml);
				$('.goodsitem').css('display', 'table-row');
			}else{
				$('.goodsitem').css('display', 'table-row');
				htmlgoods = '<option value="0">全部商品</option>';//必须加，否则会+=重复
				for (i in goodsfetch_parent[pid]) {
					htmlgoods += '<option value="'+goodsfetch_parent[pid][i][0]+'">'+goodsfetch_parent[pid][i][1]+'</option>';
				}
				$('#goods_select').html();
				$('#goods_select').html(htmlgoods);
			}
		}
	}else{
		$('#cate_2').css('display', 'block');
		html = '<option value="0">全部二级分类</option>';
		for (i in category[cid]) {
			html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
		}
		$('#cate_2').html();
		$('#cate_2').html(html);
	}
}

function fetchGood(cid) {

	//initiate the display
	$('.goodsitem').css('display', 'none');

	pid=$("#cate_1").val();
	if(cid=='0'){
		$('.goodsitem').css('display', 'none');
		if (!goodsfetch_parent || !goodsfetch_parent[pid]) {
			$('#goods_select').html();
			htmlgoods = '<option value="0">无添加商品</option>';
			$('#goods_select').html(htmlgoods);
			return false;
		}
		htmlgoods = '<option value="0">全部商品</option>';			
		for (i in goodsfetch_parent[pid]) {
			htmlgoods += '<option value="'+goodsfetch_parent[pid][i][0]+'">'+goodsfetch_parent[pid][i][1]+'</option>';
		}
		$('#goods_select').html();
		$('#goods_select').html(htmlgoods);
	}else{
		$('.goodsitem').css('display', 'table-row');
		if (!good) {
			$('#goods_select').html();
			$('#goods_select').html(nonehtml);
			return false;
		}
		htmlgoods = '<option value="0">全部商品</option>';
		if(good[pid])
			for (i in good[pid][cid]) {
				htmlgoods += '<option value="'+good[pid][cid][i][0]+'">'+good[pid][cid][i][1]+'</option>';
			}
		$('#goods_select').html();
		$('#goods_select').html(htmlgoods);
	}
}
</script>
</body>
</html>