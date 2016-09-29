<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/tinymce.min.js"></script>
<div class="main_auto">
	<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('scratchcardlist',array());?>">刮刮卡</a> > <font class="fontpurple"><?php if(empty($id)){?>创建刮刮卡<?php }else{ ?>编辑刮刮卡<?php }?></font></div>
</div>
<form name ="mysetting" id="mysetting" onSubmit="return validateform();" action="<?php echo $this -> createWebUrl('scratchcardEdit',array( 'gweid' => $gweid))?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="reply_id" value="<?php echo $reply['id'];?>" />
	<div class="alert alert-block alert-new">
		<table>
			<tbody>
				<tr>
					<th style="width: 130px;">刮刮卡名称（必填）</th>
					<td><input id="name" type="text" class="form-control" value="<?php echo $reply['name'];?>" class="form-control" name="name" style="margin-bottom: 10px;"></td>
				</tr>
				<tr>
					<th>活动图片</th>
					<td>
						<?php 
						 if(empty($reppicture)){
						?>
							<img id="pic" src="<?php bloginfo('template_directory'); ?>/images/bg.png" alt="图片预览" height='90' width='90'/>
						 <?php }else{ ?>
							<img id="pic" src="<?php echo $reppicture ?>" alt="图片预览" height='90' width='90'/>
						<?php } ?>
						<a id='picurl' href='#' onclick='delImage(this)' style="display:none;">删除图片</a>
						<input type="file" class="form-control" name="file" id="file" onchange="previewImage(this)" style="margin-top:5px;"/>
						<div class="help-block">建议图片大小长310像素，宽190像素。</div>
						<input name='delimgid' type='hidden' id='delimg_id' value='-1'/>
					</td>
				</tr>
				<tr>
					<th>活动规则</th>
					<td>
						<textarea id="rule" style="height:150px;" name="rule"><?php echo $reprule ?></textarea>
						<div class="help-block">活动的相关说明和活动奖品介绍。</div>
					</td>
				</tr>
				<tr>
					<th>重复抽奖周期</th>
					<td>
						<div>
							<div style="line-height:35px;">
							   <div style="float:left;"><span class="uneditable-input span7">每</span></div>
							   <div style="float:left;width:15%;"><input type="text" value="<?php  echo $reply['periodlottery'];?>" class="form-control" name="periodlottery" placeholder="填天数"></div>
							   <div style="float:left;"><span class="uneditable-input span7">天，抽奖</span></div>
							   <div style="float:left;width:15%;"><input type="text" value="<?php  echo $reply['maxlottery'];?>" class="form-control" name="maxlottery" placeholder="填次数"></div>
							   <div style="float:left;margin-right:5%;"><span class="uneditable-input span7">次</span></div>
							</div>
							<div class="help-block" style="line-height:35px;">若天数为0，则永远只能砸N次（这里N为设置的次数）。</div>
						</div>
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
		</tbody>
	</table>
	<div id="append-list" class="list">
	<?php if(!empty($award)) { ?>
		<?php $prize = 1;?>
		<?php if(is_array($award)) { foreach($award as $item) { ?>
		<div class="item" id="scratchcard-item-<?php echo $item['id'];?>">
		<?php include $this->template('item');?>
		</div>
		<?php $prize++;?>
		<?php } } ?>
	<?php } ?>
	</div>
	<div class="reply-news-edit-button"><a href="javascript:;" onclick="scratchcardHandler.buildAddForm('scratchcard-form-html', $('#append-list'))" class="btn"><i class="icon-plus"></i> 添加奖品</a></div>
	<div class="help-block" style="padding-left:12px;">如果填写了多种奖品，一次也只会送出一种奖品。</div>
</div>
<button class="btn btn-primary submit" type="button" onclick="javascript:submitform()" style="width:100px;margin-left: 300px;">提交</button>
<button class="btn btn-default submit"  type="button" style="margin-left:50px;width: 100px;" onclick="javascript:location.href='<?php echo $this->createWebUrl('scratchcardlist',array());?>'">返回</button>
	
</form>
<script type="text/html" id="scratchcard-form-html">
<?php unset($item); include $this->template('item');?>
</script>
<?php tinymce_js("#rule"); ?>
<script type="text/javascript">
function previewImage(file){
	$("#picurl").show();  
	document.getElementById("delimg_id").value="";
	var picsrc = document.getElementById('pic');  
  
	if (file.files && file.files[0]) {//chrome   
		var reader = new FileReader();
		reader.readAsDataURL(file.files[0]);  
		reader.onload = function(ev){
		picsrc.src = ev.target.result;
		$("#pic").show();
		
		}   
	
	}  else{
		//IE下，使用滤镜 出现问题
		picsrc.style.maxwidth="50px";
		picsrc.style.maxheight = "12px";
		picsrc.style.overflow="hidden";
		var picUpload = document.getElementById('file'); 
		picUpload.select();
		var imgSrc = document.selection.createRange().text;  
		picsrc.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
		picsrc.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\""+imgSrc+"\")";
		
	}                    
} 
function delImage(del){	  
	$("#pic").attr('src',"<?php bloginfo('template_directory'); ?>/images/bg.png"); 
	$("#picurl").hide();
	document.getElementById("delimg_id").value="";
	document.getElementById("file").value="";  //清空file input的内容
}
var scratchcardHandler = {
	'buildAddForm' : function(id, targetwrap) {
		var obj = buildAddForm(id, targetwrap);
		obj.html(obj.html().replace(/\(wrapitemid\)/gm, obj.attr('id')));
		top.iFrameHeight(); 
	}
};
function add_row() {
	$.getJSON('<?php echo $this->createWebUrl('site/module/formdisplay', array('name' => 'scratchcard'))?>', function(data){
		if (data.error === 0 && data.content.html != '') {
			$('#append-list').append(data.content.html);
			row = $('#'+data.content.id);
		}
	});
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

function submitform(){
	$("#rule").val(tinymce.get("rule").getContent());
	if($("#name").val() == ""){
		alert("请填写活动名称");
		return false; 
	}else{
		//判断上传的文件是否符合图片的类型
		var val= $("#file").val();  
		var hasd = val.indexOf(".");		 //手机端上传有可能没有扩展名	
		if(hasd >=0){				
			var filext = (val.substr(hasd)).toLowerCase();     //获取文件的扩展名全转化为小写
			
			if((filext != ".gif") && (filext != ".jpg") && (filext != ".png") && (filext != ".jpeg")){
				alert("图片格式不正确，请重新上传图片!");
				return false; 
			}
			else{  
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
}
$(function(){ 
    <?php 
	if(!empty($reppicture)){
	?>
		$("#picurl").show();
	<?php }?> 
}); 

</script>
