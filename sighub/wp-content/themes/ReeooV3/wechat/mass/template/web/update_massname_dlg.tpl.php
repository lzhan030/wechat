<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<body>
		<div id="primary" class="site-content">
			<div id="content" role="main" style="margin: 50px 0 0 80px; width:80%;">
				<form> 
					<div class="form-group">
						<label for='pic' style='font-size:16px;margin-bottom:30px;'>群发名称修改为：</label>
						<input type="text" class="form-control" id="mass_name" name="mass_name" value="<?php echo $mass['mass_name'];?>" style="margin-bottom:30px"/>
					</div>
					<div style="margin-top:45px; float:right">
						<input type="button" class="btn btn-sm btn-primary" style="width:120px" value="更新" onclick="massupdate('<?php echo $mass['id'];?>')"/>	
						<input type="button" class="btn btn-sm btn-default" style="width:120px" value="取消" onclick="Cancel()" />	
					</div>
				</form>
			</div>
		</div>
	</body>	
	<script language='javascript'>
	isSubmitting=false;
	function massupdate(id){
		if($('#mass_name').val()==""){
			alert("请输入群发名称");
		}else{		
			if(isSubmitting)
			return false;
			isSubmitting = true;
			$.ajax({
				url:window.location.href, 
				type: "POST",
				data:{'mass_update':'isupdate','massid':id,'massname':$('#mass_name').val()},
				success: function(data){
					if (data.status == 'error'){
						alert(data.message);
					}else if (data.status == 'success'){
						alert(data.message);
						window.close();
						window.opener.location.reload();
					}
					isSubmitting = false;
				},
				 error: function(data){
					alert("出现错误");
					isSubmitting = false;
				},
				dataType: 'json'
			});
		}		
	}
	function Cancel(){
		window.close();
	}
	</script>
</html>