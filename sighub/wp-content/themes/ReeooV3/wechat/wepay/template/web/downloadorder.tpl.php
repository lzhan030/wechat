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
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>	
    <title>下载对账单</title>
    <script>
    function ExportOrderExcel(){
		var type = $('#typesel option:selected').val();
		var zt = document.getElementsByName("Selected");
		
		/* var myDate = new Date();
		var year = myDate.getFullYear();    //获取完整的年份(4位,1970-????)
		var month = myDate.getMonth() + 1;       //获取当前月份(0-11,0代表1月)
		var date = myDate.getDate();        //获取当前日(1-31)
		var currentdate =year+"-"+month+"-"+date;//输出时间 */
		var currentdate = '<?php echo date("Y-m-d"); ?>';  //获取当天时间
		for(var i=0;i<zt.length;i++){
			if(zt[i].checked) {
				var selectedvalue = $('input[name="Selected"]:checked').val();
		
				if(selectedvalue == 0)
				{
					var startdate = $("#start_date").attr("value"); //2014-09-14
					var enddate = $("#end_date").attr("value");
					if(startdate > enddate)
					{
					    alert("开始时间不能晚于结束时间");
					}else{
					    if(startdate >= currentdate || enddate >= currentdate)
						{
						    alert("开始时间和结束时间不能是今天或者晚于今天");
						
						} else{
							window.location.href='<?php echo $this->createWebUrl('exportorder',array());?>'+'&type='+type+'&sevalue='+selectedvalue+'&startdate='+startdate+'&enddate='+enddate;
						}
					}
					
				}else{
				
					if(selectedvalue == 1)
					{
						var yesterday = '<?php echo date("Y-m-d",strtotime("-1 day")); ?>';
						window.location.href='<?php echo $this->createWebUrl('exportorder',array());?>'+'&type='+type+'&sevalue='+selectedvalue+'&startdate='+yesterday;	
					}else if(selectedvalue == 2)
					{
						var startdate = '<?php echo date("Y-m-d",strtotime("-7 day")); ?>'; //2014-09-14
						var enddate = '<?php echo date("Y-m-d",strtotime("-1 day")); ?>';
                        window.location.href='<?php echo $this->createWebUrl('exportorder',array());?>'+'&type='+type+'&sevalue='+selectedvalue+'&startdate='+startdate+'&enddate='+enddate;								
					}else if(selectedvalue == 3)
					{
					    var startdate = '<?php echo  date('Y-m-01', strtotime(date("Y-m-d"))); ?>'; //2014-09-14
						var enddate = '<?php echo date("Y-m-d",strtotime("-1 day")); ?>';
					    window.location.href='<?php echo $this->createWebUrl('exportorder',array());?>'+'&type='+type+'&sevalue='+selectedvalue+'&startdate='+startdate+'&enddate='+enddate;		
					}
				}
			}
		}
	} 
	</script>
	<style>
		tr{font-size:14px;}
		.radio-mid{margin-bottom:15px;} 
		.input-time{float:left;width:200px;}
		.label-time{float:left;line-height:35px;font-weight: normal;}
	</style>
</head>

<body scroll="no" height="3000px">
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/calendar.js"></script>
	<form action="" method="POST">
	<div class="main_auto">
		<div class="main-title">
			<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <font class="fontpurple">对账单下载 </font></div>
		</div>
		<div style="margin:40px 130px 0 130px;">
			<div style="height:50px;">
				<div>
					<label for="name" style="float:left;line-height:35px; text-align:left;width:100px;">选择订单类型: </label>
					<select name="siteselect" class="form-control" size="1" type="text;" id="typesel" value="5" maxlength="20" style="width:460px;float:left;">
						<option value="ALL" selected="selected">     全部订单</option>
						<option value="SUCCESS">     成功订单</option>
						<option value="REFUND">     退款订单</option>
					</select>
				</div>
			</div>
			<div style="clear:both;margin-top:30px;">
				<div>
					<label for="name">下载条件: </label>
				</div>
				<div>
					<div>
						<div class="radio">
							<label for="name">
								<input id="SeSelect" valign="middle" align="center" type="radio" name="Selected" onclick="check(this.value)" value="0" style="float:left" checked="true">
								自定义: 
							</label>
						</div>
						<label for="name" class="label-time" style="margin: 0 5px 20px 20px;">开始时间: </label>
						<input name="startDate" type="text" class="form-control input-time" style="margin-right:10px;" id="start_date" size="10" maxlength="10" onclick="new Calendar().show(this);" value="<?php echo date("Y-m-d",strtotime("-6 day")) ?>">
						<label for="name" class="label-time" style="margin-right: 5px;">结束时间: </label>
						<input name="endDate" type="text" class="form-control input-time" id="end_date" size="10" maxlength="10" onclick="new Calendar().show(this);" value="<?php echo date("Y-m-d",strtotime("-1 day")); ?>">
					</div>
					<div style="clear:both">
						<div class="radio radio-mid">
							<label for="name">
								<input id="SeSelect" valign="middle" align="center" type="radio" name="Selected" onclick="check(this.value)" align="center" value="1" style="" >
								昨天 
							</label>
						</div>
						<div class="radio radio-mid">
							<label for="name">
								<input id="SeSelect" valign="middle" align="center" type="radio" name="Selected" onclick="check(this.value)" align="center" value="2" style="" >
								最近七天
							</label>
						</div>
						<div class="radio">
							<label for="name">
								<input id="SeSelect" valign="middle" align="center" type="radio" name="Selected" onclick="check(this.value)" align="center" value="3" style="" >
								本月
							</label>
						</div>
					</div>
				</div>
			</div>
			<div style="margin-top:30px;"><span>注意:由于微信在次日9点启动生成前一天的对账单,建议您9点半以后再下载查看昨天的对账单。</span></div>
			<div style="margin-top:50px;margin-right: 100px;float: right;">
				<input type="button" class="btn btn-primary" onclick="ExportOrderExcel()" value="下载" id="sub3" style="width:100px">
				<input type="button" onclick="location.href='<?php echo $this->createWebUrl('index',array());?>'" class="btn btn-default" value="返回" id="sub3" style="width:100px; margin-left:20px;">
			</div>
		</div>
	</div>
	</form>
</body>
</html>