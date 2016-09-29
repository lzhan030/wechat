
	var radiovalue = 0;
	function showByDays(urlShowDay)
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
					else if(document.getElementById("start_date").value > document.getElementById("end_date").value)
					{
					    alert('开始时间不能晚于结束时间！');
					}
					else
					{
						if(document.getElementById("start_date").value < document.getElementById("end_date").value)
						{

							
							$("#chart7").html("");//statistic update
							$("#chart9").html("");//statistic update
							$("#chart8").html("");//statistic update
							$("#chart10").html("");//statistic update
							document.getElementById("div6").style.display="block";//statistic update
							document.getElementById("div7").style.display="block";//statistic update
							document.getElementById("chart7").style.display="block";//statistic update
							document.getElementById("chart9").style.display="block";//statistic update
							document.getElementById("chart8").style.display="none";//statistic update
							document.getElementById("chart10").style.display="none";//statistic update
							
							
							/*click statistic*/
							var urlstrcount = urlShowDay+"statistic=yes&siteid=-5&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();//全部点击量
							
							var commonstr1click = urlShowDay+"statistic=yes&siteid=-6&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							
							var urlstrclick = urlShowDay+"statistic=yes&siteid=-4&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();//各个商品分析的点击量
							/*click statistic END*/
							
							
							openPage_click(urlstrcount, commonstr1click, urlstrclick);/*click statistic*/

						}
						else
						{
							
							$("#chart7").html("");//statistic update
							$("#chart9").html("");//statistic update
							$("#chart8").html("");//statistic update
							$("#chart10").html("");//statistic update
							
							document.getElementById("div6").style.display="block";//statistic update
							document.getElementById("div7").style.display="block";//statistic update
							
							document.getElementById("chart7").style.display="none";//statistic update
							document.getElementById("chart9").style.display="none";//statistic update
							document.getElementById("chart8").style.display="block";//statistic update
							document.getElementById("chart10").style.display="block";//statistic update

								
							
							/*click statistic*/
							var urlstrcount = urlShowDay+"statistic=yes&siteid=-5&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							
							var commonstr1click = urlShowDay+"statistic=yes&siteid=-6&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							
							var urlstrclick = urlShowDay+"statistic=yes&siteid=-4&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							/*click statistic END*/
							
							openPage3_click(urlstrcount, commonstr1click, urlstrclick);/*click statistic*/

						}
					}
			   }
			   else
			   {
			        radiovalue = zt[i].value;
					//如果选择的是今天就调用24小时的那个chart，如果不是今天就调用日期的那个chart
					if($("#periodsel option:selected").val() == 0)
					{				
						$("#chart7").html("");//statistic update
						$("#chart9").html("");//statistic update
						$("#chart8").html("");//statistic update
						$("#chart10").html("");//statistic update							
						
						document.getElementById("div6").style.display="block";//statistic update
						document.getElementById("div7").style.display="block";//statistic update
						
						document.getElementById("chart7").style.display="none";//statistic update
						document.getElementById("chart9").style.display="none";//statistic update
						
						document.getElementById("chart8").style.display="block";//statistic update
						document.getElementById("chart10").style.display="block";//statistic update
						
						
						
						
						/*click statistic*/
						var urlstrcount = urlShowDay+"statistic=yes&siteid=-5&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();//商城点击量
						var commonstr1click = urlShowDay+"statistic=yes&siteid=-6&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();//最大值
						var urlstrclick = urlShowDay+"statistic=yes&siteid=-4&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();//商品统计
						/*click statistic end*/
						
						
						openPage3_click(urlstrcount, commonstr1click, urlstrclick);/*click statistic*/
					}
					else
					{
					   
						$("#chart7").html("");//statistic update
						$("#chart9").html("");//statistic update
						$("#chart8").html("");//statistic update
						$("#chart10").html("");//statistic update

						document.getElementById("div6").style.display="block";//statistic update
						document.getElementById("div7").style.display="block";//statistic update
						
						document.getElementById("chart7").style.display="block";//statistic update
						document.getElementById("chart9").style.display="block";//statistic update
						
						document.getElementById("chart8").style.display="none";//statistic update
						document.getElementById("chart10").style.display="none";//statistic update
						
						
						
						
						
						/*click statistic*/
						var urlstrcount = urlShowDay+"statistic=yes&siteid=-5&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						var commonstr1click = urlShowDay+"statistic=yes&siteid=-6&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						var urlstrclick = urlShowDay+"statistic=yes&siteid=-4&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						
						
						openPage_click(urlstrcount, commonstr1click, urlstrclick);/*click statistic*/
					}
					
			   }
			}
		}
	}
	
	//download orderstatisticclick data 
	function downloadByDays(urlDownloadDay)
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
					else if(document.getElementById("start_date").value > document.getElementById("end_date").value)
					{
					    alert('开始时间不能晚于结束时间！');
					}
					else
					{
						if(document.getElementById("start_date").value < document.getElementById("end_date").value)
						{
							window.location.href=urlDownloadDay+"downloads=yes&siteid=0&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						}
						else
						{
							window.location.href=urlDownloadDay+"downloads=yes&siteid=0&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						}
					}
			   }
			   else
			   {
			        radiovalue = zt[i].value;
					//如果选择的是今天就调用24小时的那个chart，如果不是今天就调用日期的那个chart
					if($("#periodsel option:selected").val() == 0)
					{				
						window.location.href=urlDownloadDay+"downloads=yes&siteid=0&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
					}
					else
					{
					    window.location.href=urlDownloadDay+"downloads=yes&siteid=0&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
					}
			   }
			}
		}

	}
 	
 	//比较两个日期之间的间隔
	function interval(startDate, endDate){ 
	    var d1 = new Date(startDate.replace(/-/g, "/")); 
	    var d2 = new Date(endDate.replace(/-/g, "/")); 
	    var time = d2.getTime() - d1.getTime(); 
	    return parseInt(time / (1000 * 60 * 60 * 24));
	}
	
	
	//获取上个月在昨天这一天的日期 
	function clearInput(){
		$("#start_date").val("");
		$("#end_date").val("");
	}
	$(function(){
      
		document.getElementById("div6").style.display="none";//statistic update
		document.getElementById("div7").style.display="none";//statistic update
		
    }); 
	
 