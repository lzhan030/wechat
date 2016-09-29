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
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/orderstatistic_click.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/getorderstatistic_click.js"></script>
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
<?php //require_once 'wp-content/themes/ReeooV3/js/orderstatistic_click.js'; ?>
<body scroll="no" height="3000px">
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
	<div class="main_auto"><!--主体-->
	<!--主体-标题-->
		<div class="main-title">
			<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <a href="<?php echo $this->createWebUrl('indexstatistic',array('gweid' => $gweid));?>">统计图表</a> > <font class="fontpurple">流量统计 </font></div>
		</div>
		<div class="submenu"></div>
		<div>
		<table  width="750" height="200" border="0" cellpadding="5px" style=" margin-left:30px; margin-top:30px;">
			<tr>
				<td colspan="3"><label for="name"><b>查询条件: </b></label></td>	
			</tr>
			<tr>
				<td colspan="3"><label for="name" style="margin-left:45px;font-weight:bold;">请选择查询商品: </label></td>
			</tr>
			<tr>
				<td width="100"></td>
				<td width="150"><label>商品分类： </label></td>
				<td width="250">
					<select class="form-control" style="margin-right:15px;" id="goods_select" name="goods_select">
						<option value="0" selected>全部支付链接及原生商品</option>
						<?php  if(is_array($goodsindexarray)) { foreach($goodsindexarray as $goodsindex) { 
									if($goodsindex['type']=='JSAPI'){
										$pay_type="[网页支付]";
									}else if($goodsindex['type']=='NATIVE'){
										$pay_type="[原生支付]";
									}
						?>					
							<option value="<?php  echo $goodsindex['id'];?>" ><?php  echo $pay_type.$goodsindex['goodsindex_name'];?></option>						
						<?php  } } ?>
						<!--微支付原生商品-->
						<?php  if(is_array($wepaynativearray)) { foreach($wepaynativearray as $native) { ?>
							<option value="<?php  echo "wepaynative".$native['product_id'];?>" ><?php  echo "[原生商品]".$native['product_name'];?></option>
						<?php  } } ?>
					</select>
				</td>
				
			</tr>
			<tr>
				<td colspan="3"><label for="name"><div class="help-block" style="margin-left:45px;">注:对网页支付中的类型，由于都是同一网页，则不进行流量的分别统计</div></label></td>
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
				<td><input type="button" class="btn btn-primary" style="vertical-align: middle; width:70px;" value="查询" onClick="javascript:showByDays('<?php echo $this->createWebUrl('orderstatisticclick');?>');"/>
				<input type="button" class="btn btn-warning" style="vertical-align: middle; width:120px;" value="下载统计数据" onClick="javascript:downloadByDays('<?php echo $this->createWebUrl('downloadstatisticclick');?>');"/></td>
			</tr>
		</table>
		<!--statistic new add-->
		<div id="div7" style="margin-left:370px;margin-top:45px; margin-bottom:-30px; display:none;">点击量</div>
		<div id="chart9" style="margin-top:30px; margin-left:0px; width:100%; height:300px;"></div>
		<div id="chart10" style="margin-top:30px; margin-left:0px; width:100%; height:300px; display:none;"></div>
		<div id="div6" style="margin-left:370px;margin-top:45px; margin-bottom:-30px; display:none;">综合分析点击量</div>
		<div id="chart7" style="margin-top:30px; margin-left:0px; width:100%; height:300px;"></div>
		<div id="chart8" style="margin-top:30px; margin-left:0px; width:100%; height:300px; display:none;"></div>
		<!--statistic new add END-->
		</div>
	</div><!--主体结束-->

</body>
</html>