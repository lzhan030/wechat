<?php

//obtain the parameter id
$userid = $_GET['id'];
include '../common/wechat_dbaccessor.php';
include 'sitedisplay_permission_check.php';	
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">    
	<meta http-equiv="keywords" content="advertise,analysis">
	<meta http-equiv="description" content="statistic page">
    <!--<link href="../css/style.css" rel="stylesheet" type="text/css"/>-->
    <title>无标题文档</title>
    <!--<script type="text/javascript" src="../../js/jquery-1.8.3.js"></script>-->
	<script type="text/javascript" src="../../js/jquery-1.10.2.js"></script>
    <script type="text/javascript" src="../../jquery/jquery-1.9.0.min.js"></script>
	<script type="text/javascript" src="../../jquery/jquery.json-2.4.min.js"></script>
	<script type="text/javascript" src="../../scripts/common.js"></script>
		
	<script type="text/javascript" src="../../jquery/jqplot/excanvas.min.js"></script> 
	<script type="text/javascript" src="../../jquery/jqplot/jquery.jqplot.min.js"></script> 
	<link rel="stylesheet" type="text/css" href="../../jquery/jqplot/jquery.jqplot.min.css" />
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.BezierCurveRenderer.min.js"></script>
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.pointLabels.min.js"></script>
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.logAxisRenderer.min.js"></script>
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.highlighter.min.js"></script>
	<script type="text/javascript" src="../../jquery/jqplot/plugins/jqplot.cursor.min.js"></script>

    <script language="javascript" type="text/javascript">
	
	function getArray1(data) {
		var array = [];
		for (var idx=0; idx<data.length; idx++) {
		    var item = data[idx];
		    var result = [item.today, item.countClick];
			array[idx] = result;
		}
		return array;
	}
	function getArray2(data) {
		var array = [];
		for (var idx=0; idx<data.length; idx++) {
		    var item = data[idx];
		    var result = [item.today, item.rate];
			array[idx] = result;
		}
		return array;
	}
	
	function openPage() {
			
		var ret_rate_today =null;
			
		//obtain the second data
		$.ajax({
			type: "GET",
			url: "<?php bloginfo('template_directory'); ?>/wechat/sitecount/sitecount.php?userid="+<?php echo $userid;?>+"&siteid=0&startdate=2013-12-22&enddate=2013-12-29",
			timeout: 15000,
			cache: false,
			//data: $.toJSON(data),
			processData: false,
			dataType: "text",
			contentType: "application/json; charset=utf-8"
		    }).done(function(response) {
			    //alert(response);
			    var result = undefined;
			    if (response != null && response.length > 0) {
				    try {
					   result = $.secureEvalJSON(response);
					}
					catch(err) {
					   result = response;
					}
				}	
				
				//display the chart using these data
				var ret=getArray1(response);  //这是从后台直接返回的
				var ret1=getArray1(result);   //这个地方还需要对response进行上述处理才能用到下面的图表中去
				
				$.jqplot('chart4', [ret1], {
				title: '当日点击的统计曲线',
			    series:[{renderer:$.jqplot.BarRenderer}, {xaxis:'xaxis'}],
			
				axes: {
				  xaxis: {
					  label: '时间段 ',   
					  labelRenderer: $.jqplot.CanvasAxisLabelRendere,
					  renderer: $.jqplot.CategoryAxisRenderer,
					  tickRenderer: $.jqplot.CanvasAxisTickRenderer,
					  tickOptions: {
						  angle: -30
						}
				    },
			
				yaxis: {
					  label: '点击数',
					  labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
					  min:0, 
					  max:null, 
					  numberTicks:11,				  
					  autoscale:true
				    },
				},
				highlighter: {   
					show: true,             
					showLabel: true,             
					tooltipAxes: 'y',            
					sizeAdjust: 7.5 , 
					tooltipLocation : 'ne'       
				}
			});
				
				
			}).fail(function(jqXHR) {
			 var errMsg = undefined;
			 if (jqXHR.readyState === 4) {
				try {
					errMsg = $.secureEvalJSON(jqXHR.responseText);
				}
				catch(err) {
					errMsg = {code:"SERVER_FAILURE"};
				}
		
			}
		   else {
			errMsg = {code:"SERVER_UNREACHABLE"};
		}
		});
		
		
	}	
	
	$(function() {
		openPage();
	});
</script>
        
        
</head>

    <div class="main-title">
	    <div class="title-1">当前位置：统计 > <font class="fontpurple">统计图表 </font></div>
    </div>
    <div class="bgimg"></div>
	
    <table width="100%">
			<tr>				
				<td align="center">
					<div id="chart4" style="margin-top:20px; margin-left:20px; width:600px; height:300px;"></div>
				</td>
			</tr>
	</table>
 					
</html>					
