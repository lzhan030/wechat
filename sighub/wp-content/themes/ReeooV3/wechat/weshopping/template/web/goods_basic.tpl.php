<?php defined('IN_IA') or exit('Access Denied');?>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>商品名称</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="goodsname" id="goodsname" class="form-control" value="<?php  echo $item['title'];?>" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品单位</label>
	<div class="col-sm-6 col-xs-6">
		<input type="text" name="unit" id="unit" class="form-control" value="<?php  echo $item['unit'];?>" />
	</div>
	<span class="help-inline">如: 个/件/包</span>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品属性</label>
	<div class="col-sm-9 col-xs-12" >
		<label for="isrecommend" class="checkbox-inline">
			<input type="checkbox" name="isrecommend" value="1" id="isrecommend" <?php  if($item['isrecommend'] == 1) { ?>checked="true"<?php  } ?> /> 首页推荐
		</label>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>分类</label>
	<div class="col-sm-4 col-xs-6">
		<select class="form-control" style="margin-right:15px;" id="pcate" name="pcate" onchange="fetchChildCategory(this.options[this.selectedIndex].value)"  autocomplete="off">
			<option value="0">请选择一级分类</option>
			<?php  if(is_array($category)) { foreach($category as $row) { ?>
			<?php  if($row['parentid'] == 0) { ?>
			<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['pcate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
			<?php  } ?>
			<?php  } } ?>
		</select>
	</div>
	<div class="col-sm-4 col-xs-6">
		<select class="form-control" id="cate_2" name="ccate" autocomplete="off">
			<option value="0">请选择二级分类</option>
			<?php  if(!empty($item['ccate']) && !empty($children[$item['pcate']])) { ?>
			<?php  if(is_array($children[$item['pcate']])) { foreach($children[$item['pcate']] as $row) { ?>
			<option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $item['ccate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
			<?php  } } ?>
			<?php  } ?>
		</select>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品图</label>
	<div class="col-sm-9 col-xs-12">
		<div>
			<img id="pic" style="max-width: 150px; max-height: 150px;" src="<?php echo $goodsthumb ?>" onerror="this.src='<?php bloginfo('template_directory'); ?>/images/nopic.jpg'; this.title='图片未找到'" class="img-responsive img-thumbnail">
			<em id='picurl' class="close" style="position:absolute; top: 0px; margin-left:9px;display:none;" title="删除这张图片" onclick="delImage()">×</em>
		</div>
		<input name='delimgid' type='hidden' id='delimg_id' value='-1'/>
		<input type="file" class="form-control" name="file" id="file" onchange="previewImage(this)" style="margin-top:5px;"/>
		<div class="help-block">图片建议尺寸：宽530像素 * 高340像素 </div>	
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">其他图片</label>
	<div class="col-sm-9 col-xs-12">
		<div class="input-group">
			<input type="text" class="form-control" readonly="readonly" value="" placeholder="批量上传图片" autocomplete="off">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="my_picktrue(this);">选择图片</button>
			</span>
		</div>
		<div id="fileList" class="input-group multi-img-details" style="margin-top:.5em;">
			<?php 
				if (is_array($piclist) && count($piclist)>0) {
					foreach ($piclist as $row) {
						$upload =wp_upload_dir();
						if((empty($row))||(stristr($row,"http")!==false)){
							$picurl=$row;
						}else{
							$picurl=$upload['baseurl'].$row;
						}	
			?>	 
				<div class="multi-item" style="height: 150px; position:relative; float: left; margin-right: 18px;margin-top:6px;">
					<img style="max-width: 150px; max-height: 150px;" src="<?php echo $picurl ?>" onerror="this.src='./resource/images/nopic.jpg'; this.title='图片未找到'" class="img-responsive img-thumbnail">
					<input type="hidden" name="thumbs[]" value="<?php echo $picurl ?>" >
					<em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deletepic(this)">×</em>
				</div>
			<?php 	}
		}?>
		</div>
		<div class="help-block">图片建议尺寸：宽530像素 * 高340像素 </div>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品编号</label>
	<div class="col-sm-4 col-xs-12">
		<input type="text" name="goodssn" id="goodssn" class="form-control" value="<?php  echo $item['goodssn'];?>" />
	</div>
</div>
<div class="form-group">
	<label class=" col-sm-3 col-md-2 control-label">商品条码</label>
	<div class="col-sm-4 col-xs-12">
		<input type="text" name="productsn" id="productsn" class="form-control" value="<?php  echo $item['productsn'];?>" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">
		<input onclick="setmanual($(this),value)" <?php if($item['ismanual'] == 1) echo 'checked = "checked"'?> id="isnotmanual" checked = "checked" valign="middle" align="center" type="radio" name="ismanual" value="0">
		商品价格
	</label>
	<div class="col-sm-9 col-xs-12">
		<div class="input-group">
			<span class="input-group-addon"><span style='color:red'>*</span>销售价</span>
			<input type="text" name="marketprice" id="marketprice" class="form-control" <?php if($item['ismanual'] == 1) echo 'readonly = "readonly "'?> value="<?php  echo $item['market_price'];?>" />
			<span class="input-group-addon">元</span>
		</div>
		<br>
		<div class="input-group">
			<span class="input-group-addon">市场价</span>
			<input type="text" name="productprice" id="productprice" class="form-control" <?php if($item['ismanual'] == 1) echo 'readonly = "readonly "'?> value="<?php  echo $item['product_price'];?>" />
			<span class="input-group-addon">元</span>
		</div>
		<br>
		<div class="input-group">
			<span class="input-group-addon">成本价</span>
			<input type="text" name="costprice" id="costprice" class="form-control" <?php if($item['ismanual'] == 1) echo 'readonly = "readonly "'?> value="<?php  echo $item['cost_price'];?>" />
			<span class="input-group-addon">元</span>
		</div>
		<br>
		<div class="input-group">
			<span class="input-group-addon">使用积分</span>
			<input type="text" name="point" id="point" class="form-control" <?php if($item['ismanual'] == 1) echo 'readonly = "readonly "'?> value="<?php  echo empty($item['point'])?0:$item['point'];?>" />
			<span class="input-group-addon">分</span>
		</div>
		<div style="margin-top:10px;" class="help-block">积分为0或空则表示不使用积分购买商品 </div>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">
		<input onclick="setmanual($(this),value)" <?php if($item['ismanual'] == 1) echo 'checked = "checked"'?>  id="ismanual" valign="middle" align="center" type="radio" name="ismanual" value="1">
		买家输入
	</label>						
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否需要发货</label>
	<div class="col-sm-9 col-xs-12">
		<label class="radio-inline"><input type="radio" name="isdelivery" value="0" id="isdelivery" <?php if($item['isdelivery'] == 0) echo 'checked = "checked"'?> /> 是</label>
		&nbsp;&nbsp;&nbsp;
		<label class="radio-inline"><input type="radio" name="isdelivery" value="1" id="isnotdelivery"  <?php if($item['isdelivery'] == 1) echo 'checked = "checked"'?> /> 否</label>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否包邮</label>
	<div class="col-sm-3 col-xs-12">
		<div>
			<label class="radio-inline" style="float:left;">
				<input type="radio" name="isfreedelivery" onclick="setfreedelivery($(this),value)" value="0" id="isnotfreedelivery" <?php if($item['isfreedelivery'] == 0) echo 'checked = "checked"'?> /> 不包邮
			</label>
			&nbsp;&nbsp;&nbsp;
			<label class="radio-inline" style="float:right;margin-right:-90px;">
				<input type="radio" name="isfreedelivery" onclick="setfreedelivery($(this),value)" value="1" id="isfreedelivery"  <?php if($item['isfreedelivery'] == 1) echo 'checked = "checked"'?> /> 
				<div class="input-group" style="margin-top:-7px;">
					<input type="text" name="freedeliverycount" id="freedeliverycount"  class="form-control" <?php if($item['isfreedelivery'] == 0) echo 'readonly = "readonly "'?> value="<?php  echo $item['freedeliverycount'];?>" />
					<span class="input-group-addon">件包邮</span>
				</div>			
			</label>
		</div>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">重量</label>
	<div class="col-sm-6 col-xs-12">
		<div class="input-group">
			<input type="text" name="weight" id="weight" class="form-control" value="<?php  echo $item['weight'];?>" />
			<span class="input-group-addon">克</span>
		</div>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">库存</label>
	<div class="col-sm-6 col-xs-12">
		<div class="input-group">
			<input type="text" name="total" id="total" class="form-control" <?php if($item['ismanual'] == 1) echo 'readonly = "readonly "'?> value="<?php  echo $item['total'];?>" />
			<span class="input-group-addon">件</span>
		</div>
	</div>
	<span class="help-block">当前商品的库存数量，-1则表示不限制。</span>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">减库存方式</label>
	<div class="col-sm-9 col-xs-12">
		<label for="totalcnf1" class="radio-inline"><input type="radio" name="totalcnf" value="0" id="totalcnf1" <?php  if(empty($item) || $item['totalcnf'] == 0) { ?>checked="true"<?php  } ?> /> 拍下减库存</label>
		&nbsp;&nbsp;&nbsp;
		<label for="totalcnf2" class="radio-inline"><input type="radio" name="totalcnf" value="1" id="totalcnf2"  <?php  if(!empty($item) && $item['totalcnf'] == 1) { ?>checked="true"<?php  } ?> /> 付款减库存</label>
		&nbsp;&nbsp;&nbsp;
		<label for="totalcnf3" class="radio-inline"><input type="radio" name="totalcnf" value="2" id="totalcnf3"  <?php  if(!empty($item) && $item['totalcnf'] == 2) { ?>checked="true"<?php  } ?> /> 永不减库存</label>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">最多购买量</label>
	<div class="col-sm-6 col-xs-12">
		<div class="input-group">
			<input type="text" name="maxbuy" id="maxbuy" class="form-control" <?php if($item['ismanual'] == 1) echo 'readonly = "readonly "'?> value="<?php  echo empty($item['maxbuy'])?0:$item['maxbuy'];?>" />
			<span class="input-group-addon">件</span>
		</div>
	</div>
	<span class="help-block">当前商品的最多购买数量，0则表示在库存范围内不限制。</span>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">已出售数</label>
	<div class="col-sm-6 col-xs-12">
		<div class="input-group">
			<input type="text" name="sales" id="sales" class="form-control" value="<?php  echo $item['sales'];?>" />
			<span class="input-group-addon">件</span>
		</div>
	</div>
</div>


<script language="javascript">
$(function(){ 
	fetchChildCategory($('#pcate').val());
	<?php if(!empty($item['ccate'])) {?>
	$('#cate_2').val('<?php echo $item['ccate'];?>');
	<?php } ?>
    <?php 
	if(!empty($goodsthumb)){
	?>
		$("#picurl").show();
	<?php }?> 
}); 

function deletepic(obj){
	if (confirm("确认要删除？")) {
		var $thisob=$(obj);
		var $liobj=$thisob.parent();
		var picurl=$liobj.children('input').val();
		$liobj.remove();
		/*真正删除图片的操作
		$.post('<?php  echo $this->createMobileUrl('ajaxdelete',array())?>',{ pic:picurl},function(m){
			if(m=='1') {
				$liobj.remove();
			} else {
				alert("删除失败");
			}
		},"html");*/
	}
}
$('#date-range').daterangepicker({
		format: 'YYYY-MM-DD',
		startDate: $(':hidden[name=start]').val(),
		endDate: $(':hidden[name=end]').val(),
		locale: {
			applyLabel: '确定',
			cancelLabel: '取消',
			fromLabel: '从',
			toLabel: '至',
			weekLabel: '周',
			customRangeLabel: '日期范围',
			daysOfWeek: moment()._lang._weekdaysMin.slice(),
			monthNames: moment()._lang._monthsShort.slice(),
			firstDay: 0
		}
	}, function(start, end){
		$('#date-range .date-title').html(start.format('YYYY-MM-DD') + ' 至 ' + end.format('YYYY-MM-DD'));
		$(':hidden[name=start]').val(start.format('YYYY-MM-DD'));
		$(':hidden[name=end]').val(end.format('YYYY-MM-DD'));
	});

/*是否买家输入金额*/
function setmanual(e,value){
	var mprice='marketprice';
	var pprice='productprice';
	var cprice='costprice';
	var point='point';
	var goodstotal='total';
	var maxbuytotal='maxbuy';
	if(value==0){
		$("#"+mprice).attr("readonly",false);
		$("#"+pprice).attr("readonly",false);
		$("#"+cprice).attr("readonly",false);
		$("#"+point).attr("readonly",false);
		$("#"+goodstotal).attr("readonly",false);
		$("#"+maxbuytotal).attr("readonly",false);
	}else{
		$("#"+mprice).attr("readonly",true);
		$("#"+pprice).attr("readonly",true);
		$("#"+cprice).attr("readonly",true);
		$("#"+point).attr("readonly",true);
		$("#"+point).val("0");
		$("#"+goodstotal).val("-1");
		$("#"+goodstotal).attr("readonly",true);
		$("#"+maxbuytotal).val("0");
		$("#"+maxbuytotal).attr("readonly",true);
	}
}

/*是否免运费*/
function setfreedelivery(e,value){
	if(value==0){
		$("#freedeliverycount").attr("readonly",true);
	}else{
		$("#freedeliverycount").attr("readonly",false);
	}
}

/*图片预览*/
function previewImage(file){  
    $("#picurl").show();
    document.getElementById("delimg_id").value="";   //是否更新图片
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

/*删除图片*/ 
function delImage(){	  
	$("#picurl").css('display','none');//$("#picurl").hide();
	$("#pic").attr('src',""); 
	document.getElementById("delimg_id").value=-2;
	document.getElementById("file").value="";  //清空file input的内容
}

/*批量上传*/ 
function my_picktrue(obj) { 
	var editor = KindEditor.editor({
			allowFileManager : false,
			imageSizeLimit : '30MB',
			cssPath : '<?php bloginfo('template_directory'); ?>/js/editor/plugins/code/prettify.css',
			uploadJson : '<?php bloginfo('template_directory'); ?>/js/editor/php/sae_upload_json.php',
			fileManagerJson : '<?php bloginfo('template_directory'); ?>/js/editor/php/sae_file_manager_json.php',
		});
		editor.loadPlugin('multiimage', function() {
			editor.plugin.multiImageDialog({
				clickFn : function(list) {
					if (list && list.length > 0) {
						for (i in list) {
							if (list[i]) {
								html =	'<div class="multi-item" style="height: 150px; position:relative; float: left; margin-right: 18px;margin-top:6px;">'+
								'<img style="max-width: 150px; max-height: 150px;" src="'+list[i]['url']+'" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">'+
								'<input type="hidden" name="thumbs[]" value="'+list[i]['url']+'" >'+
								'<em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deletepic(this)">×</em>'+
								'</div>';
		
								$('#fileList').append(html);
								i++;
							}
						}
						editor.hideDialog();
					} else {
						alert('请先选择要上传的图片！');
					}
				}
			});
		});
		setTimeout(function () {  
			$('.ke-dialog').css({'top':jQuery(obj).offset().top-100});	 
		}, 50);			
}

</script>