<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
include '../common/wechat_dbaccessor.php';
include 'sitedisplay_permission_check.php';
global  $wpdb;
//obtain the parameter id
$userid = $_GET['id'];
$gweid = $_SESSION['GWEID'];
//$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."orangesite where site_user='".$userid."'" );

//2014-07-15新增修改
$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."orangesite where site_user='".$userid."' AND GWEID = ".$gweid );

//var_dump($myrows); print the list
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../css/init.css">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
    <title>查看报表</title>
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
	</style>

<script>

	var countclick = [];
	var i = 0;
	function getArray1(data) {
		i = 0; //reset
		countclick.length = 0;  //清空数组
		var array = [];
		for (var idx=0; idx<data.length; idx++) {
		    var item = data[idx];
		    var result = [item.today, item.countClick];
			array[idx] = result;
			if(item.countClick>=0)
			{
			   countclick[i] = item.countClick;
			   i++;
			}
		}
		i = 0; //reset
		return array;
	}
	function getArray2(data) {
		var array = [];
		for (var idx=0; idx<data.length; idx++) {
		    var item = data[idx];
		    var result = [item.unit, item.value];
			array[idx] = result;
		}
		return array;
	}
	
	function getArray3(data) {
		console.info(data);
		console.info(data.length);
		var array = [];
		for (var idx=0; idx<data.length; idx++) {
		    var item = data[idx];
		    var result = [item.today, item.countclick];
			array[idx] = result;			
		}
		console.info(array);
		return array;
	}
	
	
	function getArray4(data) {
		i = 0; //reset
		countclick.length = 0;  //清空数组
		var array = [];
		for (var idx=0; idx<data.length; idx++) {
		    var item = data[idx];
		    var result = [item.today, item.countClick];
			array[idx] = result;
			//alert(item.countClick);
			if(item.countClick>=0)
			{
			   countclick[i] = item.countClick;
			   i++;
			}
		}
		i = 0; //reset
		return array;
	}
	
	var max;   //定义全局变量max，下面图表中的每一个站点的最值肯定小于所有站点点击数的总和
	
	function openPage() {
			
		var ret_rate_today =null;
		
		//obtain the second data
		$.ajax({
			type: "GET",
			url: "<?php bloginfo('template_directory'); ?>/wechat/sitecount/sitecount.php?userid="+<?php echo $userid;?>+"&siteid="+$('#sitesel option:selected').val()+"&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val(),
			timeout: 15000,
			cache: false,
			//data: $.toJSON(data),
			processData: false,
			dataType: "text",
			contentType: "application/json; charset=utf-8"
		    }).done(function(response) {
			    
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
				
				max = countclick[0];
				//获取到max值后就可以调用下面的进行显示
				openPage1();
				
				for(var i = 1; i< countclick.length; i++)
				{				   
				    if(parseInt(max) < parseInt(countclick[i]))
					{				    
					    max = countclick[i];					
					} 	
				}
				
				var yvalue = [];
				yvalue[0] = 0;
				yvalue[1] = parseInt(max/5);
				
				for(var j=2; j<=5;j++)
				{
				   yvalue[j] = parseInt(yvalue[1] * j + 5);
				}
				
				$.jqplot('chart4', [ret1], {
				title: '点击数',
			    series:[{renderer:$.jqplot.BarRenderer}, {xaxis:'xaxis'}],
			
				axes: {
				  xaxis: {
					  label: '日期 ',   
					  labelRenderer: $.jqplot.CanvasAxisLabelRendere,
					  renderer: $.jqplot.CategoryAxisRenderer,
					  tickRenderer: $.jqplot.CanvasAxisTickRenderer,
					  tickOptions: {
						  angle: -30
						}
				    },
				 	
				yaxis: {
					  label: '数量',
					  labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
					  min:0, 
					  max:null, 
					  ticks:yvalue,
                      tickOptions: { formatString: '%d'},		 //整数显示			  
                      numberTicks:5,						  
					  autoscale:true,
					  decimal:0
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
	
	function openPage1() {
			
		var ret_rate_today =null;

		//obtain the second data
		$.ajax({
			type: "GET",
			url: "<?php bloginfo('template_directory'); ?>/wechat/sitecount/sitecount.php?userid="+<?php echo $userid;?>+"&siteid=-1&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val(),
			timeout: 60000,
			cache: false,
			//data: $.toJSON(data),
			processData: false,
			dataType: "text",
			contentType: "application/json; charset=utf-8"
		    }).done(function(response) {
			 
			 	console.info("response:"+response);
				var seriestest=[];  //js定义数组
				var name=[];
				var i = 0;
				for(var key in JSON.parse(response))
				{
				    var test = {label:key};	
                    seriestest.push(test);		  //js给数组添加元素	
					name.push(key);
                    i++;					
				}
               
			    var result = undefined;
				if (response != null && response.length > 0) {
				    try {
					   result = $.secureEvalJSON(response);
					   
					}
					catch(err) {
					  
					   result = response;
					}
				}	
				
		var test1 = {"codes":[{"unit":6,"value":33},{"unit":7,"value":11},{"unit":8,"value":53},{"unit":9,"value":35},{"unit":10,"value":0},{"unit":11,"value":0},{"unit":12,"value":0},{"unit":13,"value":0},{"unit":14,"value":0},{"unit":15,"value":0}]};
		
		var size = i;
		var test = [];
		for(var j=0; j<size; j++)
		{
		    var temp = name[j];
		    var testresult = getArray3(eval("result."+temp));  //eval把一个字符串当做一个javascript来执行
			test.push(testresult);
			
		}
		console.info("test:"+test);
		var yvalue1 = [];
		yvalue1[0] = 0;
		yvalue1[1] = parseInt(max/5);
		
		for(var j=2; j<=5;j++)
		{
		   yvalue1[j] = yvalue1[1] * j + 5;
		}
         
        var plot1 = $.jqplot("chart3", test , {     		
										title: "所有站点综合分析点击数",        
										axesDefaults: { 
												pad: 1.05        
											}, 
										axes: {            
											xaxis: {                
												pad: 0,                
												renderer: $.jqplot.CategoryAxisRenderer,            
												label: '日期',
												tickRenderer: $.jqplot.CanvasAxisTickRenderer,
												tickOptions: {                    
													angle: -30             
												}            
											},            
											yaxis: {               
												min: 0,               
												max: null,
												ticks:yvalue1,
												pad:1,
												label: '数量',  
												labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
												autoscale:true,
												tickOptions: { formatString: '%d'},
												decimal:0
										    }     										
										},
										legend: {
											    show: true,
											    placement: 'outsideGrid'
										},
										seriesDefaults: {           
												rendererOptions: {             
													 smooth: true            
											    }        
										},
										highlighter: {   
											show: true,             
											showLabel: true,             
											tooltipAxes: 'y',            
											sizeAdjust: 7.5 , 
											tooltipLocation : 'ne'       
										},
										//series:[{label:'站点一'}, {label:'站点二'},{label:'站点三'}],
										series: seriestest,
										
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
	
	function openPage3() {
			
		var ret_rate_today =null;
		
		//obtain the second data
		$.ajax({
			type: "GET",
			url: "<?php bloginfo('template_directory'); ?>/wechat/sitecount/sitecount.php?userid="+<?php echo $userid;?>+"&siteid="+$('#sitesel option:selected').val()+"&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val(),
			timeout: 15000,
			cache: false,
			//data: $.toJSON(data),
			processData: false,
			dataType: "text",
			contentType: "application/json; charset=utf-8"
		    }).done(function(response) {
			   
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
				var ret=getArray4(response);  //这是从后台直接返回的
				var ret1=getArray4(result);   //这个地方还需要对response进行上述处理才能用到下面的图表中去
				
				var xtime = [];
				var x = [];
				var xtimevalue = [];
				for(var t=0; t<ret1.length; t++)
				{
					xtime[t] = ret1[t][0];
					xtimevalue[t] = ret1[t][1];
					x[t] = t;
				}
				
				max = xtimevalue[0];
				//获取到max值后就可以调用下面的进行显示
				openPage4();
				
				for(var i = 1; i< xtimevalue.length; i++)
				{				   
				    if(parseInt(max) < parseInt(xtimevalue[i]))
					{				    
					    max = xtimevalue[i];					
					} 	
				}
				
				var yvalue = [];
				yvalue[0] = 0;
				yvalue[1] = parseInt(max/5);
				
				for(var j=2; j<=5;j++)
				{
				   yvalue[j] = yvalue[1] * j + 5;
				}
				
				
				$.jqplot('chart2', [xtimevalue], {
				title: '当日点击的统计曲线',
			    series:[{renderer:$.jqplot.BarRenderer}, {xaxis:'xaxis'}],
			
				axes: {
				  xaxis: {
					  label: '当日24小时时间段 ',  
                      renderer: $.jqplot.CategoryAxisRenderer,		  
					  //ticks:xtime,
					  ticks:x,
					  tickOptions: {                    
									angle: -30             
									}    				
					  
				    },
				 	
				yaxis: {
					  label: '数量',
					  labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
					  min:0, 
					  max:null, 
					  ticks:yvalue,
                      numberTicks:5,						  
					  autoscale:true,
					  tickOptions: { formatString: '%d'},
					  decimal:0
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
	
	function openPage4() {
			
		var ret_rate_today =null;
		
		//obtain the second data
		$.ajax({
			type: "GET",
			url: "<?php bloginfo('template_directory'); ?>/wechat/sitecount/sitecount.php?userid="+<?php echo $userid;?>+"&siteid=-1&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val(),
			timeout: 15000,
			cache: false,
			//data: $.toJSON(data),
			processData: false,
			dataType: "text",
			contentType: "application/json; charset=utf-8"
		    }).done(function(response) {
			 
				var seriestest=[];  //js定义数组
				var name=[];
				var i = 0;
				for(var key in JSON.parse(response))
				{
				    //alert("Key是:" + key);  
                    var test = {label:key};	
                    seriestest.push(test);		  //js给数组添加元素	
					name.push(key);
                    i++;					
				}
               
			    var result = undefined;
				if (response != null && response.length > 0) {
				    try {
					   result = $.secureEvalJSON(response);
					   
					}
					catch(err) {
					  
					   result = response;
					}
				}	
				
		var test1 = {"codes":[{"unit":6,"value":33},{"unit":7,"value":11},{"unit":8,"value":53},{"unit":9,"value":35},{"unit":10,"value":0},{"unit":11,"value":0},{"unit":12,"value":0},{"unit":13,"value":0},{"unit":14,"value":0},{"unit":15,"value":0}]};
		
		var size = i;
		var test = [];
		
		for(var j=0; j<size; j++)
		{
		    var temp = name[j];
		    var testresult = getArray3(eval("result."+temp));  //eval把一个字符串当做一个javascript来执行
			
			test.push(testresult);
			
		}
		
		var yvalue1 = [];
		yvalue1[0] = 0;
		yvalue1[1] = parseInt(max/5);
		
		for(var j=2; j<=5;j++)
		{
		   yvalue1[j] = yvalue1[1] * j + 5;
		}
        
		
        //按照之前的test的值，就是返回的时间+点击数，是没有问题的。		
        // var plot1 = $.jqplot("chart3", [test1, test2, test3], {   
		var plot1 = $.jqplot("chart1", test , { 
        //var plot1 = $.jqplot("chart1", testyv , { 		
										title: "所有站点综合分析点击数",        
										axesDefaults: { 
												pad: 1.05        
											}, 
										axes: {            
											xaxis: {                
												pad: 0,                
												renderer: $.jqplot.CategoryAxisRenderer,            
												label: '当日24小时时间段',
												tickRenderer: $.jqplot.CanvasAxisTickRenderer,
											
												  												
											},            
											yaxis: {               
												min: 0,               
												max: null,
												ticks:yvalue1,
												pad:1,
												label: '数量',  
												labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
												autoscale:true,
												tickOptions: { formatString: '%d'},
												decimal:0
										    }     										
										},
										legend: {
											    show: true,
											    placement: 'outsideGrid'
										},
										seriesDefaults: {           
												rendererOptions: {             
													 smooth: true            
											    }        
										},
										highlighter: {   
											show: true,             
											showLabel: true,             
											tooltipAxes: 'y',            
											sizeAdjust: 7.5 , 
											tooltipLocation : 'ne'       
										},
										//series:[{label:'站点一'}, {label:'站点二'},{label:'站点三'}],
										series: seriestest,
										
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

    function showByDays1()
    {
	    $("#startToEnd").show();
	    $("#divchange").hide();
    }
	var radiovalue = 0;
	function showByDays()
	{
	    var zt = document.getElementsByName("Selected");
		
		for(var i=0;i<zt.length;i++){
			if(zt[i].checked) {
			
			   if(zt[i].value == 0)
			   {
			        radiovalue = zt[i].value;
			       if((document.getElementById("start_date").value=="")||(document.getElementById("end_date").value==""))
					{
						alert("开始时间和结束时间是必选项 ");
					}
					else
					{
						if(document.getElementById("start_date").value <= document.getElementById("end_date").value)
						{
							$("#chart4").html("");
							$("#chart3").html("");
							$("#chart2").html("");
							$("#chart1").html("");
						
							if($('#sitesel option:selected').val()==0)
						    {   
								document.getElementById("chart3").style.display="block";
								document.getElementById("chart4").style.display="block";
								document.getElementById("chart2").style.display="none";
								document.getElementById("chart1").style.display="none";
								openPage();	
								
							}
							else
							{
								document.getElementById("chart3").style.display="none";
								document.getElementById("chart4").style.display="block";
								document.getElementById("chart2").style.display="none";
								document.getElementById("chart1").style.display="none";
								openPage();
						
							}

						}
						else
						{
							alert('开始时间不能晚于结束时间！');
							
							$("#chart4").html("");
							$("#chart3").html("");
							$("#chart2").html("");
							$("#chart1").html("");
						
						
						    if($('#sitesel option:selected').val()==0)
				            {   
				   
								document.getElementById("chart3").style.display="none";
								document.getElementById("chart4").style.display="none";
								document.getElementById("chart2").style.display="block";
								document.getElementById("chart1").style.display="block";
								openPage3();	
								
				            }
				            else
				            {
								document.getElementById("chart3").style.display="none";
								document.getElementById("chart4").style.display="none";
								document.getElementById("chart2").style.display="block";
								document.getElementById("chart1").style.display="none";
								openPage3();
					
				            }

						}
					}
			   }
			   else
			   {
			        radiovalue = zt[i].value;
					if($("#sitesel option:selected").val() == 0)
					{
					    
						//如果选择的是今天就调用24小时的那个chart，如果不是今天就调用日期的那个chart
						if($("#periodsel option:selected").val() == 0)
						{				
                            $("#chart4").html("");
							$("#chart3").html("");
							$("#chart2").html("");
							$("#chart1").html("");	
							
							document.getElementById("chart3").style.display="none";
							document.getElementById("chart4").style.display="none";
							document.getElementById("chart2").style.display="block";
							document.getElementById("chart1").style.display="block";
							openPage3();
						}
						else
						{
						    $("#chart4").html("");
							$("#chart3").html("");
							$("#chart2").html("");
							$("#chart1").html("");
							
						    document.getElementById("chart3").style.display="block";
							document.getElementById("chart4").style.display="block";
							document.getElementById("chart2").style.display="none";
							document.getElementById("chart1").style.display="none";
							openPage();	 
						}
					}
					else
					{
					    //如果选择的是今天就调用24小时的那个chart，如果不是今天就调用日期的那个chart
						if($("#periodsel option:selected").val() == 0)
						{	
							$("#chart4").html("");
							$("#chart3").html("");
							$("#chart2").html("");
							$("#chart1").html("");	
							
							document.getElementById("chart3").style.display="none";
							document.getElementById("chart4").style.display="none";
							document.getElementById("chart2").style.display="block";
							document.getElementById("chart1").style.display="none";
							openPage3();
						}
						else
						{
						    $("#chart4").html("");
							$("#chart3").html("");
							$("#chart2").html("");
							$("#chart1").html("");
							document.getElementById("chart3").style.display="none";
							document.getElementById("chart4").style.display="block";
							document.getElementById("chart2").style.display="none";
							document.getElementById("chart1").style.display="none";
							openPage();
						}
							
							
							
					}

			   
			   }
			}
		}

	    
		
		
	}
	
	//获取上个月在昨天这一天的日期
	function getLastMonthYestdy(date){      
		var daysInMonth = new Array([0],[31],[28],[31],[30],[31],[30],[31],[31],[30],[31],[30],[31]);      
		var strYear = date.getFullYear();        
		var strDay = date.getDate();        
		var strMonth = date.getMonth()+1;      
		if(strYear%4 == 0 && strYear%100 != 0){      
		   daysInMonth[2] = 29;      
		}      
		if(strMonth - 1 == 0)      
		{      
		   strYear -= 1;      
		   strMonth = 12;      
		}      
		else     
		{      
		   strMonth -= 1;      
		}      
		strDay = daysInMonth[strMonth] >= strDay ? strDay : daysInMonth[strMonth];      
		if(strMonth<10)        
		{        
		   strMonth="0"+strMonth;        
		}      
		if(strDay<10)        
		{        
		   strDay="0"+strDay;        
		}      
		datastr = strYear+"-"+strMonth+"-"+strDay;      
		return datastr;      
    }     
	
	
	function clearInput(){
		$("#start_date").val("");
		$("#end_date").val("");
	}
</script>
</head>

<body scroll="no" height="3000px">
<script type="text/javascript" src="../../js/calendar.js"></script>
<form action="" method="POST">
<div class="main_auto"><!--主体-->
<!--主体-标题-->
    <div class="main-title">
	    <div class="title-1">当前位置：统计 > <font class="fontpurple">统计图表 </font></div>
    </div>
    <!--主体-标题结束-->
    <!--二级导航-->
    <div class="submenu"></div>
    <!--二级导航结束-->
    <!--内容开始-->
    <div><!--表单-->
    <table width="350" height="50" border="0" cellpadding="10px" style=" margin-left:80px; margin-top:15px;">
		<tr>
			<td><label for="name">选择站点: </label></td>
			<td>
				<select name="siteselect" class="form-control" size="1" type="text;margin-left:500px;" id="sitesel" value="5" maxlength="20">
					<option value="0" selected="selected">     所有</option>
					<?php 
					 foreach($myrows as $site){
					 ?>
					  <option value="<?php echo $site->id?>" ><?php echo '['.$site->id.']  '.$site->site_name?></option>
					<?php
					}
					?>
				</select>
			</td>		
            <td></td>			
		</tr>
	</table>
	<table  width="550" height="200" border="0" cellpadding="10px" style=" margin-left:80px; margin-top:15px;">
		<tr>
		    <td><label for="name">查询条件: </label></td>
			
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
			<!--<td>
			<input type="button" class="btn btn-primary" style="vertical-align: middle; width:70px;" value="查询" onClick="javascript:showByDays();"/></td>-->
		</tr>
		<tr>
		    <td><input id="OSelect" valign="middle" align="center" type="radio" name="Selected" onclick="check(this.value)" align="center" value="1" style="margin-left:55px;"></td>
		    <td><label for="name">选择时间段: </label></td>
			<td>
				<select name="period" class="form-control" size="1" type="text;margin-left:500px;" id="periodsel" value="5" maxlength="20" onchange="selTime(this.options[this.selectedIndex].value)">
					<option value="0">今天</option>
					<option value="1" selected="selected">最近一周</option>
					<option value="2">最近一个月</option>
					<option value="3">最近一个季度</option>
					<option value="4">最近一年</option>
				</select>
			</td>
			<td><input type="button" class="btn btn-primary" style="vertical-align: middle; width:70px;" value="查询" onClick="javascript:showByDays();"/></td>
		</tr>
	</table>
 
    <table width="100%" height="350px">
			<tr>				
				<td align="center">
					<div id="chart4" style="margin-top:60px; margin-left:0px; width:100%; height:300px;"></div>
					<div id="chart2" style="margin-top:60px; margin-left:0px; width:100%; height:300px; display:none;"></div>
				</td>
			</tr>
	</table>
	
	 <table width="100%" height="1000px" style="margin-top:-330px;">
			<tr>				
				<td align="center">
					<div id="chart3" style="margin-top:30px; margin-left:0px; width:100%; height:300px;"></div>
					<div id="chart1" style="margin-top:30px; margin-left:0px; width:100%; height:300px; display:none;"></div>
				</td>
			</tr>
	</table>

    </div>
</div><!--主体结束-->
</form>
</body>
</html>