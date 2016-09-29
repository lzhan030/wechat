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
	function validateform(){
	    //var myreg = /^[a-zA-Z0-9_]\w\/+[a-zA-Z0-9_]$/;  //url只能以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线或者反斜杠组成
		var myreg = /^(?!\/)(\/{0,1}[a-zA-Z0-9_])+$/;//没有判断有两个连续的/的情况
		var myregpayment = /^[a-zA-Z0-9_]+$/;
		var emailreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;					
		var validatestr = /^[a-zA-Z0-9]{32}$/; //key32位的数字和大小写字母组合
		//var myreg = /^(?!\/)(?!.*?\/$)[a-zA-Z0-9_\/{0,1}]+$/;
	    //如果支付url下面的三个url有一个不为空，需要先输入支付url
		if(document.getElementById('payment_url').value != "")
		{
		    if(!myreg.test($("#payment_url").val())){
				alert("支付授权URL必须以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线(_)或者反斜杠(/)组成且不能出现连续相同的反斜杠，请重新输入！");
				return false;
			}else{
			    var yes = true;
				 //判断支付授权url和数据库中的值是否有重复
				$.ajax({
						url: '<?php echo $this->createWebUrl('urlcheck',array());?>', 
						type: "POST",
						data:{'urlcheck_submit':'urlcheck','urlstring':$("#payment_url").val(),'value':''},
						cache: false, 
						async: false,             //这个属性指的是，执行完 $.ajax() 中的所有内容之后，再往下继续执行
						success: function(data){
							if (data.status == 'error'){
							    yes = false;
								alert(data.message);
							}else if (data.status == 'success'){
								if(document.getElementById('alarm_url').value != "" && $('#payment_url').val() == $('#alarm_url').val() ){
									yes = false;
									alert("支付授权URL和告警URL不能重复");
									//return false;
								}else if(document.getElementById('right_url').value != "" && $('#payment_url').val() == $('#right_url').val() )
								{
									yes = false;
									alert("支付授权URL和维权URL不能重复");
									//return false;
								}else if(document.getElementById('nativepay_url').value != "" && $('#payment_url').val() == $('#nativepay_url').val())
								{
									yes = false;
									alert("支付授权URL和原生支付URL不能重复");
									//return false;
								}
							}			
						},
						 error: function(data){
						    yes = false;
							alert("出现错误");
						},
						dataType: 'json'
					});
					if(!yes)
					{
						return false;
					}	
			} 
		}
		if(document.getElementById('alarm_url').value != "")
		{
		    if(!myreg.test($("#alarm_url").val())){
				alert("告警URL必须以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线(_)或者反斜杠(/)组成且不能出现连续相同的反斜杠，请重新输入！");
				return false;
			}else{
				
				var yes = true;
				 //判断支付授权url和数据库中的值是否有重复
				$.ajax({
						url: '<?php echo $this->createWebUrl('urlcheck',array());?>', 
						type: "POST",
						data:{'urlcheck_submit':'urlcheck','urlstring':$("#alarm_url").val(),'value':'AlarmNotify'},
						cache: false, 
						async: false,             //这个属性指的是，执行完 $.ajax() 中的所有内容之后，再往下继续执行
						success: function(data){
							if (data.status == 'error'){
							    yes = false;
								alert(data.message);
							}else if (data.status == 'success'){
								
								if(document.getElementById('payment_url').value != "" && $('#payment_url').val() == $('#alarm_url').val() ){
									yes = false;
									alert("告警URL和支付授权URL不能重复");
									
								}else if(document.getElementById('right_url').value != "" && $('#alarm_url').val() == $('#right_url').val() )
								{
								    yes = false;
									alert("告警URL和维权URL不能重复");
								}else if(document.getElementById('nativepay_url').value != "" && $('#alarm_url').val() == $('#nativepay_url').val())
								{
								    yes = false;
									alert("告警URL和原生支付URL不能重复");
								}
							}			
						},
						 error: function(data){
						    yes = false;
							alert("出现错误");
						},
						dataType: 'json'
					});
					if(!yes)
					{
						return false;
					}	
			} 
		}
		if(document.getElementById('right_url').value != "")
		{
		    if(!myreg.test($("#right_url").val())){
				alert("维权URL必须以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线(_)或者反斜杠(/)组成且不能出现连续相同的反斜杠，请重新输入！");
				return false;
			}else{
				
				var yes = true;
				 //判断支付授权url和数据库中的值是否有重复
				$.ajax({
						url: '<?php echo $this->createWebUrl('urlcheck',array());?>', 
						type: "POST",
						data:{'urlcheck_submit':'urlcheck','urlstring':$("#right_url").val(),'value':'RightsNotify'},
						cache: false, 
						async: false,             //这个属性指的是，执行完 $.ajax() 中的所有内容之后，再往下继续执行
						success: function(data){
							if (data.status == 'error'){
							    yes = false;
								alert(data.message);
							}else if (data.status == 'success'){
								
								if(document.getElementById('payment_url').value != "" && $('#payment_url').val() == $('#right_url').val() ){
									yes = false;
									alert("维权URL和支付授权URL不能重复");
									
								}else if(document.getElementById('alarm_url').value != "" && $('#alarm_url').val() == $('#right_url').val() )
								{
								    yes = false;
									alert("维权URL和告警URL不能重复");
								}else if(document.getElementById('nativepay_url').value != "" && $('#right_url').val() == $('#nativepay_url').val())
								{
								    yes = false;
									alert("维权URL和原生支付URL不能重复");
								}
								
							}			
						},
						 error: function(data){
						    yes = false;
							alert("出现错误");
						},
						dataType: 'json'
					});
					if(!yes)
					{
						return false;
					}	
				
			}
		}
		if(document.getElementById('nativepay_url').value != "")
		{
		    if(!myreg.test($("#nativepay_url").val())){
				alert("原生支付URL必须以字母、数字或者下划线开头或结尾，中间部分也只能是字母、数字、下划线(_)或者反斜杠(/)组成且不能出现连续相同的反斜杠，请重新输入！");
				return false;
			}else{
				
			    var yes = true;
				 //判断支付授权url和数据库中的值是否有重复
				$.ajax({
						url: '<?php echo $this->createWebUrl('urlcheck',array());?>', 
						type: "POST",
						data:{'urlcheck_submit':'urlcheck','urlstring':$("#nativepay_url").val(),'value':'NativePayNotify'},
						cache: false, 
						async: false,             //这个属性指的是，执行完 $.ajax() 中的所有内容之后，再往下继续执行
						success: function(data){
							if (data.status == 'error'){
							    yes = false;
								alert(data.message);
							}else if (data.status == 'success'){
								
								if(document.getElementById('payment_url').value != "" && $('#payment_url').val() == $('#nativepay_url').val() ){
									yes = false;
									alert("原生支付URL和支付授权URL不能重复");
								}else if(document.getElementById('alarm_url').value != "" && $('#alarm_url').val() == $('#nativepay_url').val() )
								{
								    yes = false;
									alert("原生支付URL和告警URL不能重复");
								}else if(document.getElementById('right_url').value != "" && $('#right_url').val() == $('#nativepay_url').val())
								{
								    yes = false;
									alert("原生支付URL和维权URL不能重复");
								}
							}			
						},
						 error: function(data){
						    yes = false;
							alert("出现错误");
						},
						dataType: 'json'
					});
					if(!yes)
					{
						return false;
					}		
			
			}
		}
		
		//check appkey
		
		if(document.getElementById('appkey').value != "")
		{
		    if(!validatestr.test($("#appkey").val())){
				alert("支付密钥key只能是字母、数字的组合,且满足位数为32位！");
				return false;
			} 
			else{
				 
				var yes = true;
				 //判断支付授权url和数据库中的值是否有重复
				$.ajax({
						url: '<?php echo $this->createWebUrl('appKeycheck',array());?>', 
						type: "POST",
						data:{'appkey_submit':'appkeycheck','keystring':$("#appkey").val()},
						cache: false, 
						async: false,             //这个属性指的是，执行完 $.ajax() 中的所有内容之后，再往下继续执行
						success: function(data){
							if (data.status == 'error'){
							    yes = false;
								alert(data.message);
							}else if (data.status == 'success'){
							    //支付密钥没有问题的情况下，判断再次输入的值
								if(document.getElementById('appkeyagin').value == "")
								{
								    yes = false;
									alert("请重新输入支付密钥APPKEY");
								}
								else{
								    if(document.getElementById('appkeyagin').value != document.getElementById('appkey').value)
									{
									    yes = false;
										alert("两次输入的支付密钥APPKEY不一致，请重新输入");
									}
								
								}
								
							}			
						},
						 error: function(data){
						    yes = false;
							alert("出现错误");
						},
						dataType: 'json'
					});
					if(!yes)
					{
						return false;
					}		
			}
			
		}
		if(document.getElementById('appkeyagin').value != "")
		{
		    if(document.getElementById('appkey').value == "")
			{
			    alert("请先输入支付密钥APPKEY");
				return false;
			}else{
				if(document.getElementById('appkeyagin').value != document.getElementById('appkey').value)
				{
				    alert("两次输入的支付密钥APPKEY不一致，请重新输入");
				    return false;
				}else{
				    if(!validatestr.test($("#appkey").val())){
						alert("支付密钥key只能是字母、数字的组合,且满足位数为32位！");
						return false;
					}else{
					
						var yes = true;
						 //判断支付授权url和数据库中的值是否有重复
						$.ajax({
								url: '<?php echo $this->createWebUrl('appKeycheck',array());?>', 
								type: "POST",
								data:{'appkey_submit':'appkeycheck','keystring':$("#appkey").val()},
								cache: false, 
								async: false,             //这个属性指的是，执行完 $.ajax() 中的所有内容之后，再往下继续执行
								success: function(data){
									if (data.status == 'error'){
									    yes = false;
										alert(data.message);
									}			
								},
								 error: function(data){
								    yes = false;
									alert("出现错误");
								},
								dataType: 'json'
							});
							if(!yes)
							{
								return false;
							}		
					}
				    
				}
			}
		}
		
		if($("#contactemail").val() !="" && !emailreg.test($("#contactemail").val())){
			alert("您的邮箱格式不正确，请重新输入！");
			return false;
		}
		return true;
		
		
	}
	
	$(function(){
	    $('#paymentbtn').zclip({
		    path:'<?php bloginfo('template_directory'); ?>/js/ZeroClipboard.swf',
			copy:function(){
				var str1 = "<?php echo home_url()?>/";
				return str1 + $('#payment_url').val();
				
			},
		});
		$('#alarmbtn').zclip({
		    path:'<?php bloginfo('template_directory'); ?>/js/ZeroClipboard.swf',
			copy:function(){
			    //alert("herer");
				var str1 = "<?php echo home_url()?>/";
				return str1 + $('#alarm_url').val(); 
				
			},
		});
		$('#rightsbtn').zclip({
		    path:'<?php bloginfo('template_directory'); ?>/js/ZeroClipboard.swf',
			copy:function(){
				var str1 = "<?php echo home_url()?>/";
				return str1 + $('#right_url').val();

			},
		});
		
		$('#nativebtn').zclip({
		    path:'<?php bloginfo('template_directory'); ?>/js/ZeroClipboard.swf',
			copy:function(){
				var str1 = "<?php echo home_url()?>/";
				return str1 + $('#nativepay_url').val();
				
			},
		});
		
		//上传证书pem文件
		$("#fileupload").wrap("<form id='myupload' action='<?php echo $this->createWebUrl('uploadcertificate',array('gweid' => $gweid, 'type' => 'certificate1'));?>' method='post' enctype='multipart/form-data'></form>");
		var bar = $('.bar');
		var percent = $('.percent');
		var showimg = $('#showimg');
		var progress = $(".progress");
		var files = $(".files");
		var btn = $(".btnupload span");
		
		$("#fileupload").change(function(){
			$("#myupload").ajaxSubmit({
				//dataType:  'json',
				dataType:  'text',
				beforeSend: function() {
					showimg.empty();
					progress.show();
					var percentVal = '0%';
					bar.width(percentVal);
					percent.html(percentVal);
					btn.html("正在上传证书");
				},
				uploadProgress: function(event, position, total, percentComplete) {
					var percentVal = percentComplete + '%';
					bar.width(percentVal);
					percent.html(percentVal);
				},
				success: function(data) {
				//dataType改成text，再对json字符串解析成js对象，就不直接用json的了
					var obj = jQuery.parseJSON(data);				
					var returnstatus = obj.status;
					//alert(returnstatus);
					if(returnstatus == "上传成功")
					{
						btn.html("上传证书成功");
					}else{
						btn.html("上传证书失败");
					}
					
					$("#fileupload").attr("value","");
				},
				error:function(xhr){
					btn.html("上传证书失败");
					bar.width('0')
					files.html(xhr.responseText);
					$("#fileupload").attr("value","");
				}
			});return false;
		});
		//上传证书密钥pem文件
		$("#fileupload1").wrap("<form id='myupload1' action='<?php echo $this->createWebUrl('uploadcertificate',array('gweid' => $gweid, 'type' => 'certificate2'));?>' method='post' enctype='multipart/form-data'></form>");
		var bar1 = $('.bar1');
		var percent1 = $('.percent1');
		var showimg1 = $('#showimg1');
		var progress1 = $(".progress1");
		var files1 = $(".files1");
		var btn1 = $(".btnupload1 span");
		
		$("#fileupload1").change(function(){
			$("#myupload1").ajaxSubmit({
				//dataType:  'json',
				dataType:  'text',
				beforeSend: function() {
					showimg1.empty();
					progress1.show();
					var percentVal = '0%';
					bar.width(percentVal);
					percent1.html(percentVal);
					btn1.html("正在上传证书密钥");
				},
				uploadProgress: function(event, position, total, percentComplete) {
					var percentVal = percentComplete + '%';
					bar1.width(percentVal);
					percent1.html(percentVal);
				},
				success: function(data) {
				//dataType改成text，再对json字符串解析成js对象，就不直接用json的了
					var obj = jQuery.parseJSON(data);				
					var returnstatus = obj.status;
					if(returnstatus == "上传成功")
					{
						btn1.html("上传证书密钥成功");
					}else{
						btn1.html("上传证书密钥失败");
					}
					
					$("#fileupload1").attr("value","");
				},
				error:function(xhr){
					btn1.html("上传证书密钥失败");
					bar1.width('0')
					files1.html(xhr.responseText);
					$("#fileupload1").attr("value","");
				}
			});return false;
		});
	});

	
</script>
<style>
	label{font-weight:normal;}
</style>
<div class="main_auto">    
	<div class="main-title">
		<div class="title-1">当前位置：<a href="<?php echo str_replace("https", "http", $this->createWebUrl('index',array()));?>">微支付</a> > <font class="fontpurple">全局变量设置</font></div>
	</div>
	<?php
		if( $_SERVER['REQUEST_METHOD'] === 'POST' && $submitflag ){
		?>
		<div style="position:absolute; top:50px; left:400px;">
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 90%;font-size: 18px;margin-left:10px;">提交成功<br>
		</p>
		</div>
		<?php
	} ?>
	<?php
		if( $_SERVER['REQUEST_METHOD'] === 'POST' && !($submitflag) ){
		?>
		<div style="position:absolute; top:50px; left:400px;">
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 95%;font-size: 18px;margin-left: 10px;">	提交失败,请重新提交!<br>
		</p>
		</div>
		<?php
	} ?>
	<form name ="mysetting" onSubmit="return validateform();" action="" method="post" enctype="multipart/form-data">
	<div> 
		<table width="850" height="700" border="0" cellpadding="10px" style="margin-left:50px; margin-top:50px;" id="table2">		
            <tr>
				<td width="140"><label for="notice">支付授权URLa:</label></td>
				<td width="600">
					<div style="float:left;margin-top:5px;"><span><?php echo str_replace("https", "http", home_url());?>/</span></div>
					<div style="float:right;width:320px;">
					    <input type="text" value="<?php echo $payment_url;?>" onchange="checkpaymenturl()" class="form-control" id="payment_url" name="payment_url" />
					</div>
				</td>		
				<td width="70"></td>
				<td width="80"><span id="checkresult3" style="font-size:12px;font-family:'微软雅黑';"></span></td>
			</tr>
			 <tr>
				<td width="140"><label for="notice">告警URL:</label></td>
				<td width="600">
					<div style="float:left;margin-top:5px;"><span><?php echo str_replace("https", "http", home_url());?>/</span></div>
					<div style="float:right;width:320px;">
					    <input type="text" value="<?php echo $alarm_url;?>" onchange="checkalarmurl()" class="form-control" id="alarm_url" name="alarm_url">
					</div>
				</td>		
				<td width="70"></td>
				<td width="80"><span id="checkresult" style="font-size:12px;font-family:'微软雅黑';"></span></td>
			</tr>
			 <tr>
				<td width="140"><label for="notice">维权URL:</label></td>
				<td width="600">
					<div style="float:left;margin-top:5px;"><span><?php echo str_replace("https", "http", home_url());?>/</span></div>
					<div style="float:right;width:320px;">
					    <input type="text" value="<?php echo $right_url;?>" onchange="checkrightsurl()" class="form-control" id="right_url" name="right_url" />
					</div>
				</td>		
				<td width="70"></td>
				<td width="80"><span id="checkresult1" style="font-size:12px;font-family:'微软雅黑';"></span></td>
			</tr>
			 <tr>
				<td width="140"><label for="notice">原生支付URL:</label></td>
				<td width="600">
					<div style="float:left;margin-top:5px;"><span><?php echo str_replace("https", "http", home_url());?>/</span></div>
					<div style="float:right;width:320px;">
					    <input type="text" value="<?php echo $nativepay_url;?>" onchange="checknativeurl()" class="form-control" id="nativepay_url" name="nativepay_url" />
					</div>
				</td>		
				<td width="70"></td>
				<td width="80"><span id="checkresult2" style="font-size:12px;font-family:'微软雅黑';"></span></td>
			</tr>
			<tr>
				<td><label for="notice">公众号APPID:</label></td>
				<td><input type="text" value="<?php echo $appid; ?>" class="form-control" id="appid" name="appid" /></td>					
			</tr>
			<tr>
				<td><label for="notice">公众号APPSECRET:</label></td>
				<td><input type="text" value="<?php echo $appsecret; ?>" class="form-control" id="appsecret" name="appsecret" /></td>					
			</tr>
			<tr>
				<td><label for="notice">支付密钥APPKEY:</label></td>
				<td><input type="password" value="<?php echo $appkey; ?>" onchange="checkkey()" class="form-control" id="appkey" name="appkey" /></td>					
			</tr>
			<tr>
				<td><label for="notice">重新输入APPKEY:</label></td>
				<td><input type="password" value="<?php echo $appkey; ?>" onchange="checkkeyagain()" class="form-control" id="appkeyagin" name="appkeyagin" /></td>					
			</tr>
			<tr>
				<td><label for="notice">商户号MCHID:</label></td>
				<td><input type="text" value="<?php echo $mchid; ?>" class="form-control" id="mchid" name="mchid" /></td>					
			</tr>
			<tr>
				<td><label for="notice">紧急联系人:</label></td>
				<td><input type="text" value="<?php echo $contactemergency; ?>" class="form-control" id="contactemergency" name="contactemergency" /></td>					
			</tr>
			<tr>
				<td><label for="notice">联系人电话:</label></td>
				<td><input type="text" value="<?php echo $contactnumber; ?>" class="form-control" id="contactnumber" name="contactnumber" /></td>					
			</tr>
			<tr>
				<td><label for="notice">联系人邮箱:</label></td>
				<td><input type="text" value="<?php echo $contactemail; ?>"  onchange="checkemail()" class="form-control" id="contactemail" name="contactemail" /></td>					
			</tr>
			<tr>
				<td></td>
				<td>
					<div class="upload" style="margin-top:10px;"> 
							<div class="btnupload">
								<span>上传证书pem</span>
								<input id="fileupload" type="file" name="file">
							</div>
							<div class="progress">
								<span class="bar"></span><span class="percent">0%</span >
							</div>
							<div class="files"></div>
							<div id="showimg"></div>							
						</div>	
				</td>					
			</tr>
			<tr>
				<td></td>
				<td>
					<div class="upload" style="margin-top:-30px;"> 
							<div class="btnupload1">
								<span>上传证书密钥pem</span>
								<input id="fileupload1" type="file" name="file1">
							</div>
							<div class="progress1">
								<span class="bar1"></span><span class="percent1">0%</span >
							</div>
							<div class="files1"></div>
							<div id="showimg1"></div>							
						</div>	
				</td>					
			</tr>
			
		</table>
	 
	</div>
	<div style="margin-top:0px;margin-left:330px;" >
		<input class="newsadd btn btn-primary" type="submit"  value="确定" style="width:100px;"/>		
		<input onclick="location.href='<?php echo str_replace("https", "http", $this->createWebUrl('index',array()));?>'" class="btn btn-default" type="button"  value="返回"style="width:100px;;margin-left:40px;"/>	
	</div>
	<div>
		<div style="position:absolute;right:100px;top:96px;"><input type="button" value="复制" class="btn btn-sm btn-info" name="copy" id="paymentbtn"/></div>
		<div style="position:absolute;right:100px;top:140px;"><input type="button" value="复制" class="btn btn-sm btn-info" name="copy" id="alarmbtn"/></div>
		<div style="position:absolute;right:100px;top:184px;"><input type="button" value="复制" class="btn btn-sm btn-info" name="copy" id="rightsbtn"/></div>
		<div style="position:absolute;right:100px;top:228px;"><input type="button" value="复制" class="btn btn-sm btn-info" name="copy" id="nativebtn"/></div>
	</div>
</div>	

