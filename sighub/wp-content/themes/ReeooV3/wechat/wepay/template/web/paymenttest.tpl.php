<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<script src="<?php bloginfo('template_directory'); ?>/js/checkurl.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.zclip.js"></script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadfile.css">
<script language="javascript">
    var xmlHttp; //定义一个全局对象 
	function createXMLHttpRequest(){
		if(window.ActiveXObject)
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		else if(window.XMLHttpRequest)
			xmlHttp = new XMLHttpRequest();
	}
	
	function check(value)
	{
	    if(value == 0)
		{
			$('#testpayment_url').attr("readonly",false);//去除input元素的readonly属性
			$('#submitpaytest').attr("disabled",false);//将button设置为可点击
		}else if(value == 1){
			$('#submitpaytest').attr("disabled",true);//将button设置为不可点击
			$('#testpayment_url').attr("readonly",true);//去除input元素的readonly属性
			document.getElementById('checkresult').innerHTML = ""; //清空check result
		}else if(value == 2){
			$('#testpayment_url').attr("readonly",true);//将input元素设置为readonly
			document.getElementById('checkresult').innerHTML = ""; //清空check result
			$('#submitpaytest').attr("disabled",false);//将button设置为可点击
		}else if(value == 3){
			$('#submitpaytest').attr("disabled",true);//将button设置为不可点击
			$('#testpayment_url').attr("readonly",true);//去除input元素的readonly属性
			document.getElementById('checkresult').innerHTML = ""; //清空check result
		}
	}

	function submittest()
	{
	    var myreg = /^(?!\/)(\/{0,1}[a-zA-Z0-9_])+$/;//没有判断有两个连续的/的情况
        var testurl = document.getElementById('testpayment_url').value;
	    var selectedradio = $("input[type='radio']:checked").val();
        if(selectedradio == 0)
        {
			if(testurl != "")
			{
				if(!myreg.test(testurl)){
					alert("测试目录URL必须以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线(_)或者反斜杠(/)组成且不能出现连续相同的反斜杠，请重新输入！");
					return false;
				} 
				else{
					createXMLHttpRequest(); 
					xmlHttp.open("GET",'module.php?module=wepay&do=urlcheck&urlstring='+testurl,true);
					xmlHttp.onreadystatechange=function(){
						if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
							if(xmlHttp.responseText.indexOf("该URL已存在")>0)
								alert("该URL已存在,请重新填写!");
							else{
								$.ajax({
									url:window.location.href, 
									type: "POST",
									data:{'submit_test':'submittest','flagvalue':$("input[name='paymenttest']:checked").val(),'testurl':$('#testpayment_url').val()},		
									success: function(data){
										if (data.status == 'error'){
											alert(data.message);
										}else if (data.status == 'success'){
											alert(data.message);
											//设置radio选中到第二步
											
											$("input[name=paymenttest]:eq(0)").attr("checked",false);
											$("input[name=paymenttest]:eq(2)").attr("checked",false);
											$("input[name=paymenttest]:eq(3)").attr("checked",false);
											//$('#testtwo').attr("checked",true);
											$("input[name=paymenttest]:eq(1)").prop("checked","checked"); 
											$('#testpayment_url').attr("readonly",true);//去除input元素的readonly属性
											$('#submitpaytest').attr("disabled",true);//将button设置为不可点击
											//将flag写入数据库
											$.ajax({
												url:window.location.href, 
												type: "POST",
												data:{'submit_flag':'submitflag','flagval':2},		
												success: function(data){
													if (data.status == 'error'){
														alert(data.message);
													}			
												},
												 error: function(data){
													alert("出现错误!");
												},
												dataType: 'json'
											});
										}			
									},
									 error: function(data){
										alert("出现错误!");
									},
									dataType: 'json'
								});
							}
						}
					}
					xmlHttp.send(); 
				}
			}else{
				alert("测试URL不能为空!");
			}
		}
		else if(selectedradio == 2){           //如果选中的是开始测试，不用判断测试目录是否为空
		    
			$.ajax({
					url:window.location.href, 
					type: "POST",
					data:{'submit_test':'submittest','flagvalue':$("input[name='paymenttest']:checked").val(),'testurl':$('#testpayment_url').val()},		
					success: function(data){
						if (data.status == 'error'){
							alert(data.message);
						}else if (data.status == 'success'){
							alert(data.message);
							//设置radio选中到第四步
							
							$("input[name=paymenttest]:eq(0)").attr("checked",false);
							$("input[name=paymenttest]:eq(1)").attr("checked",false);
							$("input[name=paymenttest]:eq(2)").attr("checked",false);
							//$("input[name=paymenttest]:eq(3)").prop("checked",true); 
							//$('#testfour').attr("checked",true);
							$("input[name=paymenttest]:eq(3)").prop("checked","checked"); 
							$('#testpayment_url').attr("readonly",true);//去除input元素的readonly属性
							$('#submitpaytest').attr("disabled",true);//将button设置为不可点击
							//将flag写入数据库
							$.ajax({
								url:window.location.href, 
								type: "POST",
								data:{'submit_flag':'submitflag','flagval':3},		
								success: function(data){
									if (data.status == 'error'){
										alert(data.message);
									}		
								},
								 error: function(data){
									alert("出现错误!");
								},
								dataType: 'json'
							});
						}			
					},
					 error: function(data){
						alert("出现错误!");
					},
					dataType: 'json'
				});
		}
	}
	
</script>
<style>
	label{font-weight:normal;}
</style>
<div class="main_auto">  

	<form name ="paymenttestset" id="paymenttestset" action="" method="post" >  
		<div class="main-title">
			<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <font class="fontpurple">支付测试</font></div>
		</div>
		
		<div style="margin-left:10%;margin-top:50px;width:650px;">
			<div>
				<label style="font-weight:bold;">支付测试流程：</label>
			<div>
			<div style="padding-left:50px;">
			<div style="float:left;padding-bottom:20px;padding-top:30px;">
				<div>
					<label for="name" style="font-weight:bold;">第一步：
						<input id="teststart" valign="middle" align="center" type="radio" name="paymenttest" onclick="check(this.value)" value="0" style="margin-right:5px;" <?php if($testflag == 0) echo 'checked = "checked"'?>>
						开启微支付测试
					</label>
				</div>
				<div style="line-height:35px;margin-top: -5px;margin-left: 83px;">
					<div style="margin-right:10px;float:left;"><label for="notice">测试目录:</label></div>
					<div style="float:left;"><span><?php echo home_url()?>/</span></div>
					<div style="float:left;">
					    
						<input type="text" value="<?php echo $testurl;?>" onchange="checktestpaymenturl()" style="margin-right:5px;" class="form-control" id="testpayment_url" name="testpayment_url" <?php if($testflag == 1 || $testflag == 2 || $testflag == 3) {echo 'readonly="readonly"';}?>/>
					</div>
					<div style="float:left;">
						<span id="checkresult" style="font-size:12px;font-family:'微软雅黑';"></span>
					</div>
				</div>
			</div>
			<div style="clear:both;padding-bottom:35px; border-bottom:1px solid #ccc;" >
				<div>
					<!--<label style="font-weight:bold;">第二步：请到微信公众号平台，设置微支付测试白名单<label>-->
					<label style="font-weight:bold;">第二步：
						<input id="testtwo" valign="middle" align="center" type="radio" name="paymenttest" onclick="check(this.value)" value="1" style="margin-right:5px;" <?php if($testflag == 2) echo 'checked = "checked"'?>>
						请到微信公众号平台，设置微支付测试白名单
					<label>
				</div>
				<div style="padding-top:10px;">
					<p>注：完成微支付测试后，请选择点击第三步并提交</p>
				</div>
			</div>
			<div style="clear:both;padding-top:35px;">
				<label for="name" style="font-weight:bold;">第三步：
					<input id="testclose" valign="middle" align="center" type="radio" name="paymenttest" style="margin-right:5px;" onclick="check(this.value)" value="2" <?php if($testflag == 1) echo 'checked = "checked"'?>>
					微支付测试完成，正式发布微支付模块
				</label>
			</div>
			<div style="clear:both;padding-top:35px;">
				<label for="name" style="font-weight:bold;">第四步：
					<input id="testfour" valign="middle" align="center" type="radio" name="paymenttest" style="margin-right:5px;" onclick="check(this.value)" value="3" <?php if($testflag == 3) echo 'checked = "checked"'?>>
					微支付可以正式使用
				</label>
			</div>
			</div>
		</div>
		
		
		<div style="padding-top: 50px;padding-left: 200px;" >
			<input class="newsadd btn btn-primary" type="button" id="submitpaytest" onclick="submittest()" value="提交" style="width:100px;"  <?php if($testflag == 2 || $testflag == 3 ) echo 'disabled = "disabled"'?>/>
			<input onclick="location.href='<?php echo $this->createWebUrl('index',array());?>'" class="btn btn-default" type="button"  value="返回"style="width:100px;;margin-left:40px;"/>	
		</div>
		
	</form>
	
</div>	

