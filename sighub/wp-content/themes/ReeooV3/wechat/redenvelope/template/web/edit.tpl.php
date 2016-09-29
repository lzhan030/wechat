<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadexcel.css">
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/font-awesome.css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap-datetimepicker.min.css">
<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/bootstrap-datetimepicker.js"></script>
<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/bootstrap-datetimepicker.zh-CN.js"></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/tinymce.min.js"></script>
<style>
	.btn-toolbar{margin-bottom: 5px;}
	#editor {overflow:auto; height:260px; border: 1px solid #ccc; width:680px;}
	div.progressbar{display:none;height:3px;background:#C4C4C4;padding:0px;margin-top: 5px; width:680px;}
	div.progressbar .scrollbar{display:block;width:50%;height:3px;background:red;}
</style>
<div class="main_auto">
	<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('list',array());?>">微红包</a> > <font class="fontpurple"><?php if(empty($id)){?>创建红包活动<?php }else{ ?>编辑红包活动<?php }?></font></div>
</div>
<form name ="mysetting" id="mysetting" onSubmit="return validateform();" action="<?php echo $this -> createWebUrl('edit',array( 'gweid' => $gweid, 'id' => $_GET['id']))?>" method="post" enctype="multipart/form-data">
	<div class="alert alert-block alert-new">
		<table>
			<tbody>
			<?php 
				if((!empty($id))&&(time()>=strtotime($reply['startdate']))){
				?>
				<div class="alert alert-block alert-warning" style="text-align:center">
					<p>该活动已经开始，不允许编辑</p>
				</div>
			<?php } ?>
				<tr>
					<th style="width: 130px;">活动名称（必填）</th>
					<td><input id="name" type="text" class="form-control" value="<?php echo $reply['name'];?>" class="form-control" name="name" style="margin-bottom: 10px;"></td>
				</tr>
				<tr>
					<th>活动LOGO</th>
					<td>
						<img id="pic" src="<?php echo $reppicture ?>" alt="图片预览" height='90' width='90'/>
						<a id='picurl' href='#' onclick='delImage(this)' style="display:none;">删除图片</a>
						<input type="file" class="form-control" name="file" id="file"  onchange="previewImage(this)" style="margin-top:5px;"/>
						<div class="help-block">图片建议尺寸：宽450像素 * 高100像素</div>
						<input name='delimgid' type='hidden' id='delimg_id' value='-1'/>
					</td>
				</tr>
				<tr>
					<th>活动背景图片</th>
					<td>
						<img id="bacpic" src="<?php echo $bacreppicture ?>" alt="图片预览" height='90' width='90'/>
						<a id='bacpicurl' href='#' onclick='delImage(this)' style="display:none;">删除图片</a>
						<input type="file" class="form-control" name="bacfile" id="bacfile" onchange="previewImage(this)" style="margin-top:5px;"/>
						<div class="help-block">图片建议尺寸：宽450像素 * 高700像素</div>
						<input name='bacdelimgid' type='hidden' id='bacdelimg_id' value='-1'/>
					</td>
				</tr>
				<tr>
					<th>活动规则</th>
					<td>
						<textarea id="rule" style="height:150px;" name="rule" class="form-control"><?php echo $reprule; ?></textarea>
						<div class="help-block">活动的相关说明和活动奖品介绍。</div>
					</td>
				</tr>
				<tr>
					<th>活动开始时间</th>
					<td>
						<div style="float:left;line-height:35px;margin-right:20px;" class='input-group date' id='datetimepicker2'>
							<input type='text'  style="background-color: #fff;height:38px;"  value="<?php echo !empty($reply['startdate'])?$reply['startdate']:date('Y-m-d')?>" class="form-control" id="startdate" name="startdate"  readonly />
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
						</div>
						<div style="float:left;line-height:35px;" class="help-block" style="line-height:35px;">活动开始后，该活动不允许编辑</div>						
					</td>
				</tr>
				<tr>
					<th>重复抽奖周期</th>
					<td>
						<div>
							<div style="line-height:35px;">
							   <div style="float:left;"><span class="uneditable-input span7">每</span></div>
							   <div style="float:left;width:10%;"><input type="text" value="<?php  echo $reply['periodlottery'];?>" class="form-control" id="periodlottery" name="periodlottery" placeholder="填天数" style="text-align: center;"></div>
							   <div style="float:left;"><span class="uneditable-input span7">天，抽奖</span></div>
							   <div style="float:left;width:10%;"><input type="text" value="<?php  echo $reply['maxlottery'];?>" class="form-control" id="maxlottery" name="maxlottery" placeholder="填次数" style="text-align: center;"></div>
							   <div style="float:left;margin-right:5%;"><span class="uneditable-input span7">次</span></div>
							</div>
							<div class="help-block" style="line-height:35px;">若天数为0，则永远只能抽N次（这里N为设置的次数）。</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>红包领取有效期</th>
					<td>
						<div style="margin-top:12px;">
							<div style="float:left;margin-right:8px;">
								<label  style="line-height:31px;"><input type="radio" name="expirestatus" onclick="setexpire($(this),value)" value="0" id="expire1"  <?php  if($reply['isrelative'] == 0) { ?>checked="true"<?php  } ?> />相对有效期</label>
							</div>
							<div style="line-height:35px;float:left;">
								<input type="text" value="<?php echo $reply['expire'];?>" class="form-control" id="expire" name="expire" <?php if($reply['isrelative'] == 1) echo 'readonly = "readonly "'?> style="float:left; width:60%"><span style="line-height:35px;margin-left: 5px;">天</span>
							</div>
							<div class="help-block" style="line-height:35px;">若天数为0，则表示永不过期。</div>
						</div>
						<div style="margin-bottom:56px;">
							<div style="float:left;margin-right:8px;">
								<label style="line-height:31px;"><input type="radio" name="expirestatus" onclick="setexpire($(this),value)" value="1" id="expire2"  <?php  if($reply['isrelative'] == 1) { ?>checked="true"<?php  } ?> />绝对有效期</label>
							</div>
							<div style="line-height:35px;float:left;">
								<div class='input-group date' id='datetimepicker1'>
									<input type='text' <?php if($reply['isrelative'] == 0){?>  disabled = "true " style="background-color: #eee;height:38px;" <?php }else{ ?> style="background-color: #fff;height:38px;" <?php } ?> value="<?php echo !empty($reply['absexpire'])?date('Y-m-d', $reply['absexpire']):date('Y-m-d')?>" class="form-control" id="absexpire" name="absexpire"  readonly />
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
								</div>								
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>中奖率</th>
					<td>
						<input type="text" value="<?php echo $reply['probalilty'];?>" class="form-control" id="probalilty" name="probalilty" style="float:left; width:30%"><span style="line-height:35px;margin-left: 5px;">%</span>
						<div class="help-block"></div>
					</td>
				</tr>
				<tr>
					<th>红包总(剩余)金额</th>
					<td>
						<div style="float:left;line-height:35px;margin-top:9px;">
							<input type="text" value="<?php echo $reply['amount'];?>" class="form-control" id="amount" name="amount" style="float:left; width:50%"><span style="line-height:35px;margin-left: 5px;">元</span>
						</div>
						<div class="help-block" style="float:left;line-height:35px;margin-top:9px;margin-left:-67px;">
							<?php if((!empty($id))&&(time()>=strtotime($reply['startdate']))){
							  echo "(红包初始总金额:".$reply['total_amount']."元,过期未领金额:".intval($samount)."元)";
							}?>
						</div>
					</td>
				</tr>
				<tr>
					<th>单个红包金额</th>
					<td>
						<div style="margin-top:20px;margin-bottom:66px;">
							<div style="float:left;margin-right:9px;">
								<label style="line-height:28px;"><input type="radio" name="fixstatus" onclick="setstatus($(this),value)" value="0" id="fix1"  <?php  if($reply['isfixamount'] == 0) { ?>checked="true"<?php  } ?> />随机金额红包</label>
							</div>
							<div style="float:left;">
							   <div style="float:left;"><span class="uneditable-input span7 help-block">最小值：</span></div>
							   <div style="float:left;width:15%;"><input type="text" value="<?php  echo $reply['min_amount'];?>" <?php if($reply['isfixamount'] == 1) echo 'readonly = "readonly "'?> class="form-control" id="min_amount" name="min_amount" placeholder="" style="text-align: center;"></div>
							   <div style="float:left;margin-left:5px"><span class="uneditable-input span7 help-block">元</span></div>
							   <div style="float:left; margin-left:20px"><span class="uneditable-input span7 help-block">最大值：</span></div>
							   <div style="float:left;width:15%;"><input type="text" value="<?php  echo $reply['max_amount'];?>" <?php if($reply['isfixamount'] == 1) echo 'readonly = "readonly "'?> class="form-control" id="max_amount" name="max_amount" placeholder="" style="text-align: center;"></div>
							   <div style="float:left;margin-right:5%;margin-left:5px"><span class="uneditable-input span7 help-block">元</span></div>
							</div>
							<div class="help-block" style="line-height:35px;"></div>
						</div>
						<div style="margin-bottom:110px;">
							<div style="float:left;margin-right:8px;">
								<label style="line-height:31px;"><input type="radio" name="fixstatus" onclick="setstatus($(this),value)" value="1" id="fix2"  <?php  if($reply['isfixamount'] == 1) { ?>checked="true"<?php  } ?> />固定金额红包</label>
							</div>
							<div style="line-height:35px;float:left;">
								<div style="float:left;"><input type="text" value="<?php  echo $reply['max_amount'];?>" <?php if($reply['isfixamount'] == 0) echo 'readonly = "readonly "'?> class="form-control" id="fix_max_amount" name="fix_max_amount" placeholder="" style="text-align: center;"></div>
								<div style="float:left;margin-left:5px"><span class="uneditable-input span7 help-block">元</span></div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>剩余红包数量</th>
					<td>
						<input type="text" value="<?php echo $reply['total'];?>" class="form-control" id="total" name="total" style="float:left;margin-bottom: 10px; width:30%" readonly="readonly">
						<div class="help-block"></div>
					</td>
				</tr>
				<tr>
					<th>中奖奖励积分</th>
					<td>
						<input type="text" value="<?php echo $reply['hitcredit'];?>" class="form-control" name="hitcredit">
						<div class="help-block">当用户中奖时，给予用户的积分。为0时表示不给。</div>
					</td>
				</tr>
				<tr>
					<th>未中奖奖励积分</th>
					<td>
						<input type="text" value="<?php echo $reply['misscredit'];?>" class="form-control" name="misscredit">
						<div class="help-block">当用户未中任何奖时，给予用户的积分。为0时表示不给。</div>
					</td>
				</tr>
				<!--<tr>
					<th>兑换码格式</th>
					<td id="code-type">
						<div class="radio-inline"><label for="code_type" class="radio inline"><input type="radio" name="code_type" id="code_type-0" value="0" <?php if($code['type'] == 0){ ?>checked="checked" <?php } ?>>手工输入</label></div>
						<div class="radio-inline"><label for="code_type" class="radio inline"><input type="radio" name="code_type" id="code_type-1" value="1" <?php if($code['type'] == 1){ ?>checked="checked" <?php } ?>>自动生成</label></div>
					</td>
				</tr>-->
				<tr>
					<th style="padding-bottom:130px;">兑换码</th>
					<td>
						<div class="btnupload" style="float:right;margin-top:11px;margin-bottom:5px;">
							<span id="span">导入Excel</span>
							<input id="fileupload" type="file" name="inputExcel" value="">
						</div>
						<div style="float:left;margin-top:11px;margin-bottom:5px;">
							<div style="line-height:30px;float:left;margin-left:7px;"><span>自动生成</span></div>
							<div style="float:left;margin-left:7px;width:15%"><input type="text" value="" class="form-control" id="autogencount" name="autogencount"></div>
							<div style="line-height:30px;float:left;margin-left:7px;"><span>个</span></div>
							<div style="float:left;margin-left:7px;width:15%"><input type="text" value="" class="form-control" id="autogencode" name="autogencode"></div>
							<div style="line-height:30px;float:left;margin-left:7px;"><span>位兑换码</span></div>
							<div style="float:left;margin-left:7px;"><button id="autogenbutton" name="autogenbutton" class="btn btn-success" style="border-radius:4px;height:30px;line-height:18px;" type="button" onclick="autogen()">点击生成</button></div>
						</div>
						<div style="float:left;">
							<textarea style="height:134px;width:753px;margin-bottom:10px" class="form-control" cols="70" placeholder="请一行填写一个兑换码（每个中奖码只能被使用一次）" id="codelist" name="codelist"><?php echo $code['codelist']; ?></textarea>
						</div>
					</td>
				</tr>
				<tr>
					<th>兑换码说明</th>
					<td>
						<input type="text" value="<?php echo $reply['code_description'];?>" class="form-control" name="code_description">
					</td>
				</tr>
		</tbody>
	</table>
</div>
<button class="btn btn-primary submit" type="button" onclick="javascript:submitform()" style="width:100px;margin-left: 300px;">提交</button>
<button class="btn btn-default"  type="button" style="margin-left:50px;width: 100px;" onclick="javascript:location.href='<?php echo $this->createWebUrl('list',array());?>'">返回</button>
	
</form>
<script type="text/html" id="scratchcard-form-html">
<?php unset($item); include $this->template('item');?>
</script>
<?php tinymce_js("#rule"); ?>
<script type="text/javascript">
function previewImage(file){  
	var bac;
	if(file.id=='file'){
		bac="";
	}else if(file.id=='bacfile'){
		bac="bac";
	}
	$("#"+bac+"picurl").show();
	document.getElementById(bac+"delimg_id").value="";
	var picsrc = document.getElementById(bac+'pic');
	
	if (file.files && file.files[0]) {//chrome   
		var reader = new FileReader();
		reader.readAsDataURL(file.files[0]);  
		reader.onload = function(ev){
			picsrc.src = ev.target.result;
			$("#"+bac+"pic").show();
		}   
	
	}  else{
		//IE下，使用滤镜 出现问题
		picsrc.style.maxwidth="50px";
		picsrc.style.maxheight = "12px";
		picsrc.style.overflow="hidden";
		var picUpload = document.getElementById(bac+'file'); 
		picUpload.select();
		var imgSrc = document.selection.createRange().text;  
		picsrc.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
		picsrc.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\""+imgSrc+"\")";
		
	}                         
} 

function delImage(del){	  
	var bac;
	if(del.id=='picurl'){
		bac="";
	}else if(del.id=='bacpicurl'){
		bac="bac";
	}
	$("#"+bac+"pic").attr('src',"");
	$("#"+bac+"picurl").hide();
	document.getElementById(bac+"delimg_id").value="";
	document.getElementById(bac+"file").value="";  //清空file input的内容
}

//奖品类型切换
$("#append-list").delegate("#award-inkind input", "click", function(){
	if($(this).val() == 0) {
		$(this).parents(".item").find(".num").css("display", "none");
		$(this).parents(".item").find("tr:eq(3),tr:eq(4)").show();
	} else {
		$(this).parents(".item").find(".num").css("display", "inline-block");
		$(this).parents(".item").find("tr:eq(3),tr:eq(4)").hide();
	}
});
Array.prototype.intersect = function(b) {
	var flip = {};
	var res = [];
	for(var i=0; i< b.length; i++) flip[b[i]] = i;
	for(i=0; i<this.length; i++)
		if(flip[this[i]] != undefined) res.push(this[i]);
	return res;
}
function submitform(){
	document.getElementById('codelist').value=document.getElementById('codelist').value.replace(/(\r\n|\n)\s*/g,'\n');
	document.getElementById('codelist').value=$.trim($('#codelist').val());
	$('#codelist').val($.trim($('#codelist').val()));
	var codeold=eval(<?php echo json_encode($codedata);?>);
	var codenew=$.trim($('#codelist').val()).split("\n");
	$('#rule').val(tinymce.get("rule").getContent());
	if($("#name").val() == ""){
		alert("请填写活动名称");
		return false; 
	}
	if((!(/^\+?(0|[1-9][0-9]*)$/.test($("#periodlottery").val()))&& $("#periodlottery").val()!='')||(!(/^\+?(0|[1-9][0-9]*)$/.test($("#maxlottery").val()))&& $("#maxlottery").val()!='')){
		alert("请输入正确的重复抽奖周期(整数)");
		return false;
	}
	if($("input[name='expirestatus']:checked").val()==0){
		if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#expire").val()))&& $("#expire").val()!=''){
			alert("请输入正确的相对有效期(整数)");
			return false;
		}
	}
	
	if ((!/^\d+[.]?\d*$/.test($("#probalilty").val())&&($("#probalilty").val()!="")) || $("#probalilty").val()> 100){
		alert("中奖率在0%和100%之间");
		return false;
	}
	if((!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test($("#probalilty").val()))&&($("#probalilty").val()!="")){
		alert("中奖率最多只能保留小数点后两位");
		return false;
	}	
	
	if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#amount").val()))&& $("#amount").val()!=''){
		alert("请输入正确的红包总(剩余)金额(整数)");
		return false;
	}
	if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#total").val())) || $("#total").val()==''){
		alert("请修改红包总(剩余)金额或单个红包金额，以生成红包数量");
		return false;
	}
	if($("input[name='fixstatus']:checked").val()==0){
		if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#min_amount").val()))|| $("#min_amount").val()==0){
			alert("请输入正确的随机金额最小值,不为0的整数");
			return false;
		}
		if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#max_amount").val()))&& $("#max_amount").val()!=''){
			alert("请输入正确的随机金额最大值(整数)");
			return false;
		}
		if(parseInt($("#max_amount").val())<parseInt($("#min_amount").val())){
			alert("红包随机最大金额不能小于红包随机最小金额");
			return false;
		}
		if(parseInt((parseInt($("#min_amount").val())*parseInt($("#total").val())))>parseInt($("#amount").val())){
			alert("剩余红包数量与红包随机金额最小值的乘积不能大于红包总(剩余)金额");
			return false;
		}
		if(parseInt((parseInt($("#max_amount").val())*parseInt($("#total").val())))<parseInt($("#amount").val())){
			alert("剩余红包数量与红包随机金额最大值的乘积不能小于红包总(剩余)金额");
			return false;
		}
	}
	if(isRepeat($.trim($('#codelist').val()).split("\n"))){
		alert("您输入的兑换码中有重复项，请去除重复项");
		return false;
	}
	if(codenew!=""){
		for(var i in codenew) {
			if(codenew[i]==""){
				alert("兑换码不能有空行，请删除");
				return false;
			}
		}
	}
    if(((codenew!="" && $("#total").val()!="")&&(codenew.length != $("#total").val())) || (codenew!="" && $("#total").val()=="")|| (codenew=="" && $("#total").val()!=""&& $("#total").val()!=0)){
		if(codenew==""){
			var codecount=0;
		}else{
			var codecount=codenew.length;
		}
		alert("兑换码个数与剩余红包个数不相等，兑换码个数为"+codecount+",请更新剩余红包个数或兑换码个数");
		return false; 
	}     
	if(codeold!="" && codeold!=null && codenew!=""){
		if(codenew.intersect(codeold)!=''){
			alert("以下兑换码在该活动中已被使用，请更换："+codenew.intersect(codeold));
			return false;
		}
	}
	
	
	//判断上传的文件是否符合图片的类型
	var val= $("#file").val();  
	var hasd = val.indexOf(".");		 //手机端上传有可能没有扩展名	
	var bacval= $("#bacfile").val();  
	var bachasd = bacval.indexOf(".");		 //手机端上传有可能没有扩展名
	
	if(hasd >=0 || bachasd>=0){				
		if(hasd >=0){
			var filext = (val.substr(hasd)).toLowerCase();     //获取文件的扩展名全转化为小写
			if((filext != ".gif") && (filext != ".jpg") && (filext != ".png") && (filext != ".jpeg")){
				alert("活动图片格式不正确，请重新上传图片!");
				return false; 
			}
		}
		if(bachasd >=0){
			var bacfilext = (bacval.substr(bachasd)).toLowerCase();     //获取文件的扩展名全转化为小写
			if((bacfilext != ".gif") && (bacfilext != ".jpg") && (bacfilext != ".png") && (bacfilext != ".jpeg")){
				alert("活动背景图片格式不正确，请重新上传图片!");
				return false; 
			}
		}
		
		$("#mysetting").ajaxSubmit({
			dataType:'json',               
			beforeSend: function() {                   
			//表单提交前做表单验证               
			},               
			success: function(data) {  
				alert(data.message);   
				window.location.href = data.url;
			}          
		}); 
		return true;
	}else{
		$("#mysetting").ajaxSubmit({
			dataType:'json',               
			beforeSend: function() {                   
			//表单提交前做表单验证               
			},               
			success: function(data) {  
				alert(data.message);   
				window.location.href = data.url;
			}          
		}); 
		return true;
	}
}
function isRepeat(arr){

     var hash = {};

     for(var i in arr) {

         if(hash[arr[i]])

              return true;

         hash[arr[i]] = true;

     }

     return false;

}
function autogen(){
	if(!(/^\+?([1-9][0-9]*)$/.test($("#autogencount").val()))){
		alert("请输入正确的要生成的兑换码个数");
	}else if(!(/^\+?([1-9][0-9]*)$/.test($("#autogencode").val()))){
		alert("请输入正确的要生成的兑换码位数");
	}else if($("#autogencode").val()<5){
		alert("兑换码位数不能小于5位");
	}else{
		$.ajax({
			url:"<?php  echo $this->createWebUrl('autogencode')?>", 
			type: "POST",
			data:{'count':$("#autogencount").val(),'length':$("#autogencode").val()},
			beforeSend: function() {
				$("#autogenbutton").text("正在生成...");
			},
			success: function(data){
				json = eval(data);
				if(json.length==0){
					alert("生成失败");
					$("#autogenbutton").text("点击生成");
				}else{
					codeinsertAtCursor(document.getElementById('codelist'),"\n");
					for(var i=0; i<json.length; i++) { 
						codeinsertAtCursor(document.getElementById('codelist'),data[i]+"\n");
					}
					document.getElementById('codelist').value=document.getElementById('codelist').value.replace(/(\r\n|\n)\s*/g,'\n');
					document.getElementById('codelist').value=$.trim($('#codelist').val());
					alert("生成成功!");
					$("#autogenbutton").text("点击生成");
				}
			},
			 error: function(data){
				alert("出现错误,请重试");
				$("#autogenbutton").text("点击生成");
			},
			dataType: 'json'
		});				
	
	}

}
function codeinsertAtCursor(myField, myValue) {
	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
		sel.select();
	}
	//MOZILLA/NETSCAPE support 
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var restoreTop = myField.scrollTop;
		myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
		if (restoreTop > 0) {
			myField.scrollTop = restoreTop;
		}
		myField.focus();
		myField.selectionStart = startPos + myValue.length;
		myField.selectionEnd = startPos + myValue.length;
	} else {
		myField.value += myValue;
		myField.focus();
	}
} 
var myDate = new Date();

$('#absexpire').datetimepicker({	
		format: 'yyyy-mm-dd',
		startDate: myDate.toLocaleDateString(),
		language: 'zh-CN',
		todayBtn:true,
		minView: "month",
		autoclose: true
});
$('#startdate').datetimepicker({	
		format: 'yyyy-mm-dd',
		startDate: myDate.toLocaleDateString(),
		language: 'zh-CN',
		todayBtn:true,
		minView: "month",
		autoclose: true
});
$(function () {
	var btn = $(".btnupload span");
	$("#fileupload").wrap("<form id='myupload' action='<?php echo $this->createWebUrl('uploadcode',array());?>' method='post' enctype='multipart/form-data'></form>");
	$("#fileupload").change(function(e){
		$("#myupload").ajaxSubmit({
			dataType:  'json',
			beforeSend: function() {
				btn.html("正在导入...");
			},
			success: function(data) {
				json = eval(data);
				if(json.length==1&&data[0]==null){
					alert("没有找到符合条件的数据");
					btn.html("导入Excel");
				}else{
					codeinsertAtCursor(document.getElementById('codelist'),"\n");
					for(var i=0; i<json.length; i++) { 
						codeinsertAtCursor(document.getElementById('codelist'),data[i]+"\n");
					}
					//document.getElementById('codelist').value=document.getElementById('codelist').value.replace(/(\r\n\s*){2,}/gi,'\r\n');
					//document.getElementById('codelist').value=document.getElementById('codelist').value.replace(/\r\n\s*/gi,'\r\n');
					document.getElementById('codelist').value=document.getElementById('codelist').value.replace(/(\r\n|\n)\s*/g,'\n');
					document.getElementById('codelist').value=$.trim($('#codelist').val());
					alert("导入成功!");
					btn.html("导入Excel");
					
				}
			},
			error:function(xhr){
				alert("导入失败");
				btn.html("导入Excel");
			}
		});
		$("#fileupload").val("");
		return false;
	});
});
function setstatus(e,value){
	if(value==0){
		$("#fix_max_amount").val("");
		$("#fix_max_amount").attr("readonly",true);
		$("#max_amount").attr("readonly",false);
		$("#min_amount").attr("readonly",false);
	}else{
		$("#max_amount").val("");
		$("#min_amount").val("");
		$("#max_amount").attr("readonly",true);
		$("#min_amount").attr("readonly",true);
		$("#fix_max_amount").attr("readonly",false);
	}
}
function setexpire(e,value){
	if(value==0){		
		$("#expire").attr("readonly",false);
		$("#absexpire").attr("disabled",true);
		$("#absexpire").css("background-color","#eee");		
	}else{
		$("#expire").attr("readonly",true);
		$("#absexpire").attr("disabled",false);
		$("#absexpire").css("background-color","#fff");
	}
}
$(function(){ 
    <?php 
	if(!empty($reppicture)){
	?>
		$("#picurl").show();
	<?php }?> 
	<?php 
	if(!empty($bacreppicture)){
	?>
		$("#bacpicurl").show();
	<?php }?>
	if($("input[name='fixstatus']:checked").val()==0){
		$("#fix_max_amount").val("");
	}else{
		$("#max_amount").val("");
		$("#min_amount").val("");
	}
	
	<?php 
	if((!empty($id))&&(time()>=strtotime($reply['startdate']))){
	?>
		$('input,textarea').attr('readonly','readonly'); 
		$('input[type="file"],input[type="radio"],#datetimepicker1 input,#datetimepicker2 input,.submit,#autogenbutton').attr('disabled','disabled'); 

	<?php }?>

	$('#amount,input[name="fixstatus"],#min_amount,#max_amount,#fix_max_amount').change(function(){
		if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#amount").val()))&& $("#amount").val()!=''){
			alert("请输入正确的红包总(剩余)金额(整数)");
			return false;
		}

		if(isNaN(parseInt($('#amount').val())) || $('#amount').val() == 0){
			$('#total').val("");
			return false;
		}
			
		if($('input[name="fixstatus"]:checked').val() == 1){
			if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#fix_max_amount").val()))&& $("#fix_max_amount").val() !=""){
				alert("请输入正确的红包固定金额,不为0的整数");
				return false;
			}
			if( isNaN(parseInt($('#fix_max_amount').val())) || $("#fix_max_amount").val() == 0){
				$('#total').val("");
				return false;
			}
				
			if( $("#amount").val() % $("#fix_max_amount").val() != 0){
				alert("红包总金额无法被单个红包金额整除，请修改红包总(剩余)金额或单个红包金额");
				$('#total').val("");
				return false;
			}
				
			$('#total').val( $("#amount").val() / $("#fix_max_amount").val());
			$('#autogencount').val($("#amount").val() / $("#fix_max_amount").val());
			if((Math.ceil(Math.log2($("#amount").val() / $("#fix_max_amount").val())/Math.log2(62))+2)<5){
				$('#autogencode').val(5);
			}else{
				$('#autogencode').val(Math.ceil(Math.log2($("#amount").val() / $("#fix_max_amount").val())/Math.log2(62))+2);
			}
			return false;
		}
		if($('input[name="fixstatus"]:checked').val() == 0){
			if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#min_amount").val()))&& $("#min_amount").val()!=""){
				$('#total').val("");
				alert("请输入正确的随机金额最小值,不为0的整数");
				return false;
			}
			if(!(/^\+?(0|[1-9][0-9]*)$/.test($("#max_amount").val()))&& $("#max_amount").val()!=''){
				alert("请输入正确的随机金额最大值(整数)");
				$('#total').val("");
				return false;
			}
			if(isNaN(parseInt($('#min_amount').val())) || isNaN(parseInt($('#max_amount').val()))){
				$('#total').val("");
				return false;
			}
			if(parseInt($("#min_amount").val()) == 0 ){
				alert("红包金额最小值不能为0");
				$('#total').val("");
				return false;
			}	
			if(parseInt($("#max_amount").val())<parseInt($("#min_amount").val())){
				alert("红包随机最大金额不能小于红包随机最小金额");
				$('#total').val("");
				return false;
			}				
			total = Math.floor(Math.random()*(Math.floor($('#amount').val()/$('#min_amount').val()) - Math.ceil($('#amount').val()/$('#max_amount').val())));
			if(total < 0){
				alert("无法生成红包数量，请调整红包总金额或单个红包金额");
				$('#total').val("");
				return false;
			}
			total += Math.ceil($('#amount').val()/$('#max_amount').val());
			$('#total').val(total);
			$('#autogencount').val(total);
			if((Math.ceil(Math.log2(total)/Math.log2(62))+2)<5){
				$('#autogencode').val(5);
			}else{
				$('#autogencode').val(Math.ceil(Math.log2(total)/Math.log2(62))+2);
			}

		}
	});
	
}); 
</script>
