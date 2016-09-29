
	var radiovalue = 0;
	function showByDays(urlShowDay)
	{
	    var zt = document.getElementsByName("Selected");
		$("#div1").html("");
		$("#div2").html("");
		$("#chart4").html("");
		$("#chart3").html("");
		$("#chart2").html("");
		$("#chart1").html("");
		$("#chart5").html("");
		$("#chart6").html("");
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

							$("#chart4").html("");
							$("#chart3").html("");
							$("#chart2").html("");
							$("#chart1").html("");
							$("#chart5").html("");
							$("#chart6").html("");
							//时间间隔超过31天的就按每个月来算
							//alert(interval(document.getElementById("start_date").value,document.getElementById("end_date").value));
							if(interval(document.getElementById("start_date").value,document.getElementById("end_date").value) <= 31)
							{
								document.getElementById("div1").style.display="block";
								$("#div1").html("每日下单量");
						    	document.getElementById("div2").style.display="none";
							}else{
								document.getElementById("div2").style.display="block";
								$("#div1").html("最近一段时间每月下单量");
								$("#div1").css("marginLeft","400px");
								document.getElementById("div1").style.display="none";
							} 
								
							document.getElementById("div3").style.display="block";
							document.getElementById("div5").style.display="block";
							document.getElementById("chart3").style.display="block";
							document.getElementById("chart4").style.display="block";
							document.getElementById("chart5").style.display="block";
							document.getElementById("chart6").style.display="none";
							document.getElementById("chart2").style.display="none";
							document.getElementById("chart1").style.display="none";
							document.getElementById("div1").style.display="block";
							document.getElementById("div2").style.display="none";
							var urlstr = urlShowDay+"statistic=yes&siteid=0&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							var urlstr1 = urlShowDay+"statistic=yes&siteid=-1&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							var urlstr5 = urlShowDay+"statistic=yes&siteid=-2&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							var commonstr = urlShowDay+"statistic=yes&siteid=1&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							var commonstr1 = urlShowDay+"statistic=yes&siteid=-3&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							
							openPage(urlstr, commonstr1, urlstr1, commonstr, urlstr5);	

						}
						else
						{
							
							$("#chart4").html("");
							$("#chart3").html("");
							$("#chart2").html("");
							$("#chart1").html("");
							$("#chart5").html("");
							$("#chart6").html("");
							$("#div2").html("当日每时段下单量");
							document.getElementById("div3").style.display="block";
							document.getElementById("div5").style.display="block";
							document.getElementById("chart3").style.display="none";
							document.getElementById("chart4").style.display="none";
							document.getElementById("chart5").style.display="none";
							document.getElementById("chart2").style.display="block";
							document.getElementById("chart1").style.display="block";
							document.getElementById("chart6").style.display="block";
							document.getElementById("div1").style.display="none";
							document.getElementById("div2").style.display="block";
								
							var urlstr = urlShowDay+"statistic=yes&siteid=0&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							var urlstr1 = urlShowDay+"statistic=yes&siteid=-1&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							var urlstr5 = urlShowDay+"statistic=yes&siteid=-2&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							var commonstr = urlShowDay+"statistic=yes&siteid=1&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							var commonstr1 = urlShowDay+"statistic=yes&siteid=-3&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
							
							openPage3(urlstr, commonstr1, urlstr1, commonstr, urlstr5);

						}
					}
			   }
			   else
			   {
			        radiovalue = zt[i].value;
					//如果选择的是今天就调用24小时的那个chart，如果不是今天就调用日期的那个chart
					if($("#periodsel option:selected").val() == 0)
					{				
						$("#chart4").html("");
						$("#chart3").html("");
						$("#chart2").html("");
						$("#chart1").html("");
						$("#chart5").html("");
						$("#chart6").html("");
						$("#div2").html("当日每时段下单量");
						document.getElementById("div3").style.display="block";
						document.getElementById("div5").style.display="block";
						document.getElementById("chart3").style.display="none";
						document.getElementById("chart4").style.display="none";
						document.getElementById("chart5").style.display="none";
						document.getElementById("chart2").style.display="block";
						document.getElementById("chart1").style.display="block";
						document.getElementById("chart6").style.display="block";
						document.getElementById("div1").style.display="none";
						document.getElementById("div2").style.display="block";
						
						var urlstr = urlShowDay+"statistic=yes&siteid=0&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						var urlstr1 = urlShowDay+"statistic=yes&siteid=-1&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						var urlstr5 = urlShowDay+"statistic=yes&siteid=-2&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						var commonstr = urlShowDay+"statistic=yes&siteid=1&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						var commonstr1 = urlShowDay+"statistic=yes&siteid=-3&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						
						openPage3(urlstr, commonstr1, urlstr1, commonstr, urlstr5);
						
					}
					else
					{
					    if($("#periodsel option:selected").val() == 1)
						{
						    $("#div1").html("每日下单量");
						    document.getElementById("div1").style.display="block";
						    document.getElementById("div2").style.display="none";
						}else if($("#periodsel option:selected").val() == 2){
						    $("#div1").html("每日下单量");
						    document.getElementById("div1").style.display="block";
							document.getElementById("div2").style.display="none";
						}else if($("#periodsel option:selected").val() == 3){
						    $("#div2").html("最近一个季度每月下单量");
							document.getElementById("div2").style.display="block";
							document.getElementById("div1").style.display="none";
							
						}else if($("#periodsel option:selected").val() == 4){
							$("#div1").html("最近一年每月下单量");
							document.getElementById("div1").style.display="block";
							document.getElementById("div2").style.display="none";
							
						}
						$("#chart4").html("");
						$("#chart3").html("");
						$("#chart2").html("");
						$("#chart1").html("");
						$("#chart5").html("");
						$("#chart6").html("");
						
						document.getElementById("div3").style.display="block";
						document.getElementById("div5").style.display="block";
						document.getElementById("chart3").style.display="block";
						document.getElementById("chart4").style.display="block";
						document.getElementById("chart5").style.display="block";
						document.getElementById("chart2").style.display="none";
						document.getElementById("chart1").style.display="none";
						document.getElementById("chart6").style.display="none";
						
						
						var urlstr = urlShowDay+"statistic=yes&siteid=0&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						var urlstr1 = urlShowDay+"statistic=yes&siteid=-1&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						var urlstr5 = urlShowDay+"statistic=yes&siteid=-2&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						var commonstr = urlShowDay+"statistic=yes&siteid=1&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						var commonstr1 = urlShowDay+"statistic=yes&siteid=-3&startdate="+document.getElementById("start_date").value+"&enddate="+document.getElementById("end_date").value+"&Selected="+radiovalue+"&period="+$("#periodsel option:selected").val()+"&cate_1="+$("#cate_1 option:selected").val()+"&cate_2="+$("#cate_2 option:selected").val()+"&goods_select="+$("#goods_select option:selected").val();
						
						openPage(urlstr, commonstr1, urlstr1, commonstr, urlstr5);
						
					}
					
			   }
			}
		}
	}
	
	//download orderstatistic data 
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
        document.getElementById("div3").style.display="none";
		document.getElementById("div5").style.display="none";  
    }); 

