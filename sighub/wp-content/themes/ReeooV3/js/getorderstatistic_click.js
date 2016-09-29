
	var countclick = [];
	var countmoney = [];
	var county = [];
	var i = 0;
	var j = 0;
	var k = 0;
	function getArray1(data) {
		i = 0;
		var array = [];
		countclick.length = 0; //每次countclick这个数组应该清空
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
		var array = [];
		for (var idx=0; idx<data.length; idx++) {
		    var item = data[idx];
		    var result = [item.today, item.countclick];
			array[idx] = result;			
		}
		return array;
	}
	
	function getArray4(data) {
		i = 0; //reset
		countclick.length = 0; //每次countclick这个数组应该清空
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
	function getArray5(data) {
		j = 0; //reset
		countmoney.length = 0; //每次countmoney这个数组应该清空
		var array = [];
		for (var idx=0; idx<data.length; idx++) {
		    var item = data[idx];
		    var result = [item.today, item.countMoney];
			array[idx] = result;
			if(item.countMoney>=0)
			{
			   countmoney[j] = item.countMoney;
			   //alert(countmoney[j]);
			   j++;
			}
		}
		j = 0; //reset
		return array;
	}
	function getArray6(data) {
		k = 0; //reset
		county.length = 0; //每次county这个数组应该清空
		var array = [];
		for (var idx=0; idx<data.length; idx++) {
		    var item = data[idx];
		    var result = [item.today, item.county];
			array[idx] = result;
			if(item.county>=0)
			{
			   county[k] = item.county;
			   //alert(county[k]);
			   k++;
			}
		}
		k = 0; //reset
		return array;
	}
	
	var max;   //定义全局变量max，下面图表中的每一个站点的最值肯定小于所有站点点击数的总和
	var maxmoney;
	var maxy;
	function openPage_click(urlstr, commonstr1, urlstr1) {
			
		var ret_rate_today =null;
		//obtain the second data
		$.ajax({
			type: "GET",
			url: urlstr,
			timeout: 15000,
			cache: false,
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
				
				var ret1=getArray1(result);   //这个地方还需要对response进行上述处理才能用到下面的图表中去
				max = countclick[0];
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
				
				$.jqplot('chart9', [ret1], {
				title: '',
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
			
			//获取到max值后就可以调用下面的进行显示
			openPage1_click(commonstr1, urlstr1);
				
				
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
	
	function openPage1_click(commonstr1, urlstr1) {
		var ret_rate_today =null;
		
		//obtain y values
		$.ajax({
			type: "GET",
			url: commonstr1,
			timeout: 15000,
			cache: false,
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
				
				//reset maxy
				maxy = 0;
				county.length=0; //清空数组
				//display the chart using these data
				var ret1=getArray6(result);   //这个地方还需要对response进行上述处理才能用到下面的图表中去
				
				maxy = county[0];
				for(var i = 1; i< county.length; i++)
				{						
				    if(parseInt(maxy) < parseInt(county[i]))
					{				    
					    maxy = county[i];					
					} 
                    					
				} 
		        //alert("最大值:"+maxy);
		
					//obtain the second data
					$.ajax({
						type: "GET",
						url: urlstr1,
						timeout: 15000,
						cache: false,
						processData: false,
						dataType: "text",
						contentType: "application/json; charset=utf-8"
						}).done(function(response) {
						 
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
								//var testresult = getArray3(eval("result."+temp));  //eval把一个字符串当做一个javascript来执行
								var testresult = getArray3(eval("result['"+temp+"']"));  
								test.push(testresult);	
							}
							var yvalue1 = [];
							yvalue1[0] = 0;
							yvalue1[1] = parseInt(maxy/5);
							
							for(var j=2; j<=5;j++)
							{
							   yvalue1[j] = yvalue1[1] * j + 5;
							} 
							var plot1 = $.jqplot("chart7", test , {     		
													title: "",        
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
	
	function openPage3_click(urlstr, commonstr1, urlstr1){
			
		var ret_rate_today =null;
		//obtain the second data
		$.ajax({
			type: "GET",
			url: urlstr,
			timeout: 15000,
			cache: false,
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
				openPage4_click(commonstr1, urlstr1);
				
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

				$.jqplot('chart10', [xtimevalue], {
				title: '',
			    series:[{renderer:$.jqplot.BarRenderer}, {xaxis:'xaxis'}],
				axes: {
				  xaxis: {
					  label: '当日24小时时间段 ',  
                      renderer: $.jqplot.CategoryAxisRenderer,		  
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
	
	function openPage4_click(commonstr1, urlstr1) {
			
		var ret_rate_today =null;
		
		//obtain y values
		$.ajax({
			type: "GET",
			url: commonstr1,
			timeout: 15000,
			cache: false,
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
				
				//reset maxy
				maxy = 0;
				county.length=0; //清空数组
				//display the chart using these data
				var ret1=getArray6(result);   //这个地方还需要对response进行上述处理才能用到下面的图表中去
				
				maxy = county[0];
				for(var i = 1; i< county.length; i++)
				{						
				    if(parseInt(maxy) < parseInt(county[i]))
					{				    
					    maxy = county[i];					
					} 
                    					
				} 
		        //alert("最大值:"+maxy);
		
				//obtain the second data
				$.ajax({
					type: "GET",
					
					url: urlstr1,
					timeout: 15000,
					cache: false,
					processData: false,
					dataType: "text",
					contentType: "application/json; charset=utf-8"
					}).done(function(response) {
					 
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
						console.info("result:"+result);
						var size = i;
						var test = [];
						for(var j=0; j<size; j++)
						{
							var temp = name[j];
							//var testresult = getArray3(eval("result."+temp));  //eval把一个字符串当做一个javascript来执行
							var testresult = getArray3(eval("result['"+temp+"']")); //否则带-出错
							test.push(testresult);
						}
						
						var yvalue1 = [];
						yvalue1[0] = 0;
						yvalue1[1] = parseInt(maxy/5);
						
						for(var j=2; j<=5;j++)
						{
						   yvalue1[j] = yvalue1[1] * j + 5;
						}
						
						
						//按照之前的test的值，就是返回的时间+点击数，是没有问题的。		
						var plot1 = $.jqplot("chart8", test , { 	
												title: "",        
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
	