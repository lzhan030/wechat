<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/wsite.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/gradeclass.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/uploadfile.css">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/button.css" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery1.83.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap-tab.js"></script>
		<title>网页支付页面</title>
	</head>
	<style>
		tr{height: 45px;}
	</style>
	<div class="main_auto">
		<div class="main-title">
		<?php if($native){ ?>	
			<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <a href="<?php echo $this->createWebUrl('qrcodemanage',array('id' => 3,'nativeOrder'=>true));?>">原生支付管理</a> > <font class="fontpurple">详情</font></div>
		<?php }else{ ?>
			<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微支付</a> > <a href="<?php echo $this->createWebUrl('Goodsindexmanage',array('id' => 3));?>">网页支付管理</a> > <font class="fontpurple">详情</font></div>
		<?php } ?>
		</div>
		<!--显示二维码-->
		<div class="panel panel-default" style="<?php if(empty($goodsindexid)){?> display:none; <?php }else{ ?>margin-right:30px; margin-top:25px;float:left;width:96.5%; <?php }?>">
			<div class="panel-heading">商品二维码</div>
			<div id="productqr" style="float:left;">
				<div style="float:left;">
					<img src='<?php echo $this->createWebUrl('showpayqr',array('gweid'=>$gweid,'goodsgid' => $goodsindexid));?>' width='250' height='250'>
				</div>
				<div style="float:right;padding-top:195px;">
					<a href="<?php echo $this->createWebUrl('showpayqr',array('gweid'=>$gweid,'goodsgid' => $goodsindexid,'download'=>1));?>"><input type="button" class="btn btn-primary"  value="下载图片"></a>
				</div>
			</div>
		</div>
		<!--显示二维码END-->
		<div class="panel panel-default" style="margin-right:30px; margin-top:25px;clear:both;">
			<div class="panel-heading">详情</div>
				<div style="<?php if(empty($goodsindexid)){?> display:none;width:95%;  <?php }?>">
					<table class="table table-striped" width="800" border="0" align="center">
						<tr>
							<td colspan=2 scope="col" width="60" align="center" >
								<span class="control-label" style="font-size: 14px;line-height:33px;" for="inputInfo">网页支付链接</span>
							</td>
							<td colspan="2" scope="col" width="400"><input type="text" class="form-control" readonly="readonly"  value="<?php echo $this->createMobileUrl('goodsinfo',array('gweid'=>$gweid,'goodsgid' => $goodsindexid)); ?>"></td>
						</tr>
					</table>
				</div>
				<!--tab-->
				<div id="nav-main">
					<ul class="nav nav-tabs" id="tabselect">
						<?php	
							$isone=$_GPC['isone'];
							if(!empty($goods)){
							foreach($goods as $good){
								if(empty($isone)){
									$isone=$good->id;
								}
						?>
						<li <?php if($isone==$good->id){ ?> class="selected active" <?php } ?> id="li<?php echo $good->id; ?>" name="liname"><a onclick="switab('<?php echo $good->id; ?>')" data-toggle="tab" ><?php echo "类型编号:".$good->id; ?></a></li>
						<?php }} ?>
						<li id="linew" class="active selected" <?php if((!empty($goodsindexid))&&(!empty($goods))){?> style="display:none" <?php } ?>><a onclick="switab('new')" data-toggle="tab" ><?php echo "新建:" ?></a></li>
					<img  onclick="newadd()" style="height:14ps;margin-left:1%;margin-top:12px;" width="20ps" src="<?php bloginfo('template_directory'); ?>/images/addpng.png"  value=">"  style="width:70px">	
					</ul>
				</div>
				<!--tab end-->
				<!--form add-->
				<form id="formnew" name ="formnew" <?php if((!empty($goodsindexid))&&(!empty($goods))){?> style="display:none;padding:1" <?php } ?> onSubmit="return validateform('new')" action="" method="post" enctype="multipart/form-data">
				<div style="width:500px;margin-left:25%;margin-top:30px;">
					<div id="gnameformnew" style="<?php if((!empty($goodsindexid))&&(!empty($goods))){?> display:none; <?php } ?>" >
						<div class="goods-label"><label for="title">链接名称: </label></div>
						<div class="goods-input"><input type="text"  class="form-control" id="gnamenew" name="gname" value="<?php echo $gname; ?>" > </div>
					</div>
					<div class="newline">
						<div class="goods-label"><label for="title">名称: </label></div>
						<div class="goods-input"><input type="text" id="titlenew" class="form-control" name="title" value="" > </div>
					</div>
					<div class="newline">
						<div class="goods-label"><label for="thumb">图片: </label></div>
						<div class="goods-input" style=" padding-bottom: 15px; ">
							<div>
								<div><img href="javascript:void(0)" id="picnew" name="pic" src="" height='150' width='150'/></div>
								<div style="margin-left:168px;margin-top:-24px"><a style="display:none" id="delimagenew" onclick="delimage('new')">删除图片</a></div>
							</div>
							<div class="upload" style="margin-top:33px;">
								<div class="btnupload">
									<span>上传图片</span>
									<input id="fileuploadnew" type="file" name="file" onchange="previewImage(this,'new')">
								</div>	
								(建议上传图片大小为428*300)									
							</div>
							<input name='delimgid' type='hidden' id='delimg_idnew' value=''/>
						</div>
					</div>
					<div class="newline">
						<div class="goods-label"><label for="type">类型: </label></div>
						<div class="goods-input"><input type="text" id="typenew" class="form-control" name="type" value="" > </div>
					</div>
					
					<div class="newline">
						<div class="goods-label"><label for="market_price">金额: </label></div>
						<div class="goods-input">
							<div style="float:left;">
								<div style="float:left;margin-top:7px;margin-right:5px">
									<input onclick="setmanual('new',$(this),value)" id="isnotmanualnew" checked = "checked" valign="middle" align="center" type="radio" name="ismanual" value="0">
								</div>
								<div style="float:right;">
									<input type="text" id="market_pricenew" class="form-control"  name="market_price" value="" >
								</div>
							</div>
							<div style="float:right;margin-right: 8px;margin-top:7px">
								<label for="name" style="font-weight:normal;">
									<input onclick="setmanual('new',$(this),value)" id="ismanualnew" valign="middle" align="center" type="radio" name="ismanual" value="1">
									买家输入
								</label>
							</div>
						</div>
					</div>
					<div class="newline">
						<div class="goods-label" style="padding-bottom: 30px;"><label for="total">库存: </label></div>
						<div class="goods-input"><input type="text" id="totalnew" class="form-control" name="total" value="-1" >(当前的库存数量，-1则表示不限制)</div>
					</div>
					<div class="newline">
						<div class="goods-label" style="line-height:25px;"><label for="isdelivery">是否需要发货: </label></div>
						<div class="goods-input">
							<div style="float:left;">
								<label for="name">
									<input id="isdeliverynew" checked = "checked" valign="middle" align="center" type="radio" name="isdelivery" value="0">
									是
								</label>
							</div>
							<div style="float:left;margin-left: 30px;">
								<label for="name">
									<input id="isnotdeliverynew" valign="middle" align="center" type="radio" name="isdelivery" value="1">
									否
								</label>
							</div>
						</div>
					</div>
					<div class="newline">
						<div class="goods-label"><label for="goodssn">条形码: </label></div>
						<div class="goods-input"><input type="text" id="goodssnnew" class="form-control" name="goodssn" value="" > </div>
					</div>
					<div class="newline">
						<div class="goods-label"><label for="description">描述: </label></div>
						<div class="goods-input">
							<textarea type="text" id="descriptionnew" class="form-control" placeholder="" name="description"></textarea>							
						</div>
					</div>	
				</div>
				<div style="padding-top:10px;padding-bottom:50px;margin-left:36.5%; clear:both;">
					<input id="new_goodsindex_id" type="hidden" name="goodsindex_id" value="<?php echo $goodsindexid ?>"/>
					<input type="hidden" name="native" value='<?php echo $native ?>'>
					<input type="submit" class="btn btn-primary" value="提交" id="checkinfo" style="width:100px">
				<?php if($native){ ?>	
					<input type="button" class="btn btn-default" onclick="location.href='<?php echo $this->createWebUrl('qrcodemanage',array('id' => 3,'nativeOrder'=>true));?>'" value="返回" id="sub3" style="width:100px; margin-left:20px;">
				<?php }else{ ?>
					<input type="button" class="btn btn-default" onclick="location.href='<?php echo $this->createWebUrl('Goodsindexmanage',array('id' => 3));?>'" value="返回" id="sub3" style="width:100px; margin-left:20px;">
				<?php } ?>
				</div>
				</form>
				<!--form add end-->
				<!--display-->
				<?php	
					if(!empty($goods)){
					
					$isone=$_GPC['isone'];//更新后的默认选中
					$isdisplayname='0';//第一个显示链接名称
					foreach($goods as $good){
					if(empty($isone)){
						$isone=$good->id;
					}
					$isdisplayname=$isdisplayname+1;
				?>
				<form id="form<?php echo $good->id; ?>" style="<?php if($isone!=$good->id){ echo 'display:none';} ?>" name ="formname" onSubmit="return validateform('<?php echo $good->id; ?>')" action="" method="post" enctype="multipart/form-data">
				<div style="width:500px;margin-left:25%;margin-top:30px;">
					<div id="gnameform<?php echo $good->id; ?>" style="<?php if($isdisplayname!='1'){?> display:none; <?php } ?>" >
						<div class="goods-label"><label for="title">链接名称: </label></div>
						<div class="goods-input"><input id="gname<?php echo $good->id; ?>" type="text" class="form-control" name="gname" value="<?php echo $gname; ?>" > </div>
					</div>
					<div class="newline">
						<div class="goods-label"><label for="title">名称: </label></div>
						<div class="goods-input"><input type="text" id="title<?php echo $good->id; ?>" class="form-control" name="title" value="<?php echo $good->title?>" > </div>
					</div>
					<div class="newline">
						<div class="goods-label"><label for="thumb">图片: </label></div>
						<?php 
							$upload =wp_upload_dir();
							if((empty($good->thumb))||(stristr($good->thumb,"http")!==false)){
								$goodsthumb=$good->thumb;
							}else{
								$goodsthumb=$upload['baseurl'].$good->thumb;
							}
							?>
						<div class="goods-input" style=" padding-bottom: 15px; ">
							<div>
								<div><img id="pic<?php echo $good->id; ?>" name="pic" src='<?php echo $goodsthumb ?>' height='150' width='150'/></div>
								<div style="margin-left:168px;margin-top:-24px"><a href="javascript:void(0)" <?php if(empty($goodsthumb)){?> style="display:none" <?php } ?> id="delimage<?php echo $good->id; ?>" onclick="delimage('<?php echo $good->id; ?>')">删除图片</a></div>
							</div>
							<div class="upload" style="margin-top:33px;">
								<div class="btnupload">
									<span>上传图片</span>
									<input id="fileupload<?php echo $good->id; ?>" type="file" name="file" onchange="previewImage(this,<?php echo $good->id; ?>)">
								</div>
								(建议上传图片大小为428*300)								
							</div>
							<input name='delimgid' type='hidden' id='delimg_id<?php echo $good->id; ?>' value=''/>
						</div>
					</div>
					<div class="newline">
						<div class="goods-label"><label for="type">类型: </label></div>
						<div class="goods-input"><input type="text" id="type<?php echo $good->id; ?>" class="form-control" name="type" value="<?php echo $good->type?>" ></div>
					</div>
					
					<div class="newline">
						<div class="goods-label"><label for="market_price">金额: </label></div>
						<div class="goods-input">
							<div style="float:left;margin-left:5px;">
								<div style="float:left;margin-top:7px;margin-right:5px">
									<input onclick="setmanual(<?php echo $good->id; ?>,$(this),value)" id="isnotmanual<?php echo $good->id; ?>" <?php if($good->ismanual == 0) echo 'checked = "checked"'?> valign="middle" align="center" type="radio" name="ismanual" value="0">
								</div>
								<div style="float:right;">
									<input width="59%" type="text" id="market_price<?php echo $good->id; ?>"  <?php if($good->ismanual == 1) echo 'readonly = "readonly "'?> class="form-control" name="market_price" value="<?php echo $good->market_price?>" >
								</div>
							</div>
							<div style="float:right;margin-right: 8px;margin-top:7px">
								<label for="name" style="font-weight:normal;">
									<input onclick="setmanual(<?php echo $good->id; ?>,$(this),value)" id="ismanual<?php echo $good->id; ?>" <?php if($good->ismanual == 1) echo 'checked = "checked"'?> valign="middle" align="center" type="radio" name="ismanual" value="1">
									买家输入
								</label>
							</div>
						</div>
					</div>
					<div class="newline">
						<div class="goods-label" style="padding-bottom: 30px;"><label for="total">库存: </label></div>
						<div class="goods-input"><input type="text" id="total<?php echo $good->id; ?>"  class="form-control" name="total" <?php if($good->ismanual == 1) echo 'readonly = "readonly "'?> value="<?php echo $good->total?>" >(当前的库存数量，-1则表示不限制)</div>
					</div>
					<div class="newline">
						<div class="goods-label" style="line-height:25px;"><label for="isdelivery">是否需要发货: </label></div>
						<div class="goods-input">
							<div style="float:left;">
								<label for="name">
									<input id="isdelivery<?php echo $good->id; ?>" <?php if($good->isdelivery == 0) echo 'checked = "checked"'?> valign="middle" align="center" type="radio" name="isdelivery" value="0">
									是
								</label>
							</div>
							<div style="float:left;margin-left: 30px;">
								<label for="name">
									<input id="isnotdelivery<?php echo $good->id; ?>" <?php if($good->isdelivery == 1) echo 'checked = "checked"'?> valign="middle" align="center" type="radio" name="isdelivery" value="1">
									否
								</label>
							</div>
						</div>
					</div>
					<div class="newline">
						<div class="goods-label"><label for="goodssn">条形码: </label></div>
						<div class="goods-input"><input type="text" id="goodssn<?php echo $good->id; ?>" class="form-control" name="goodssn" value="<?php echo $good->goodssn?>" ></div>
					</div>
					<div class="newline">
						<div class="goods-label"><label for="description">描述: </label></div>
						<div class="goods-input">
							<textarea type="text" id="description<?php echo $good->id; ?>" class="form-control" placeholder="" name="description"><?php echo $good->description; ?></textarea>
						</div>
					</div>
					<input type="hidden" name="goodsid" value="<?php echo $good->id ?>"/>
				</div>
				<div style="padding-top:10px;padding-bottom:50px;margin-left:36.5%; clear:both;">
					<input type="hidden" name="goodsindex_id" value="<?php echo $goodsindexid ?>"/>
					<input type="submit" class="btn btn-primary" value="提交" id="checkinfo" style="width:100px">
					<input type="button" class="btn btn-default" onclick="goodsdel('<?php echo $good->id ?>')" value="删除" id="sub3" style="width:100px; margin-left:20px;">
				</div>
				</form>
				<? }} ?>
				<!--display end-->
		</div>
		
	</div>
	<script language='javascript'>
		 $(function(){
			//$('.nav-tabs a:first').tab('show');
			$('.nav-tabs a:last').click(function (e) { 
			}) 
			
		});
		
		function switab(id){
			var objs=document.getElementsByName("formname");
			for(var i=0;i<objs.length;i++){
			   document.getElementById(objs[i].id).style.display = "none";			   
			}
			document.getElementById('formnew').style.display = "none";
			document.getElementById('form'+id).style.display = "";			
		}
		
		function newadd(){
			var objs=document.getElementsByName("formname");
			var liobjs=document.getElementsByName("liname");
			for(var i=0;i<objs.length;i++){
				var obj=document.getElementById(objs[i].id);
				obj.style.display = "none";
			}
			
			$("#nav-main ul li.active").removeClass("active");
			$("#linew").addClass("active");
			
			document.getElementById('formnew').style.display = "";	
			document.getElementById('linew').style.display = "";
			
		}
		var r = /^(0|([1-9]\d*))$/ ;　　//正整数       
        function validateform(id){
			if(document.getElementById('gname'+id).value == ""){
				alert("链接名称不能为空");
				return false;
			}else if(document.getElementById('title'+id).value == ""){
				alert("名称不能为空");
				return false;
			}else if(document.getElementById('total'+id).value == ""){
				alert("库存不能为空");
				return false;
			}else if(!(r.test(document.getElementById('total'+id).value)||parseInt(document.getElementById('total'+id).value)==-1)){
            	alert("库存必须为-1或其他正整数");
				return false;
			}
			var flfee=parseFloat(document.getElementById('market_price'+id).value);
			if(document.getElementById('market_price'+id).readOnly==false){
				if(document.getElementById('market_price'+id).value==''){
					alert("请输入金额");
					return false;
				}
				if( parseFloat((document.getElementById('market_price'+id)).value)==0){
					alert("金额不能等于0");
					return false;
				}
				if (!/^\d+[.]?\d*$/.test(document.getElementById('market_price'+id).value)){
					alert("请填写正确的金额");
					return false;
				}
				if(flfee><?php echo WEPAY_MAX_TOTAL_FEE;?>){
					alert("金额超出范围，请重新输入<?php echo WEPAY_MAX_TOTAL_FEE;?>以内金额");
					return false;
				}
				if(!(/^([1-9]\d+|\d)(\.\d{1,2})?$/).test(document.getElementById('market_price'+id).value)){
					alert("金额最多只能保留小数点后两位");
					return false;
				}
			}
			return true;
		}
		function closew(){
			setTimeout('self.close()',0);			
		}
		
		isSubmitting=false;
		function goodsdel(id){		 
			
			if(isSubmitting)
				return false;
			isSubmitting = true;
			if(confirm("确定删除吗？")){
				$.ajax({
					url:window.location.href, 
					type: "POST",
					data:{'goods_del':'isDel','goodsid':id},
					success: function(data){
						if (data.status == 'error'){
							alert(data.message);
						}else if (data.status == 'success'){
							alert(data.message);						
							//document.getElementById('form'+id).style.display = "none";//form消失
							if(document.getElementById('gnameform'+id).style.display == ""){//标明要删除的商品是否显示了链接名称
								isdisplay=true;
							}else{
								isdisplay=false;
							}
							$('#form'+id).remove();
							//右边那个默认被选中
							var selfid=$("#nav-main ul li.active").attr("id");
							var nextid=$("#nav-main ul li.active").next().attr("id");
							
							var formid=nextid.substring(2);	
							document.getElementById(nextid).style.display = "";
							$("#"+nextid).addClass("active");
							document.getElementById('form'+formid).style.display = "";
							if(isdisplay==true){//如果删除的带有链接名称，则被选中的要显示链接名称
								document.getElementById('gnameform'+formid).style.display = "";
							}
							
							$("#"+selfid).removeClass("active");
							$('#li'+id).remove();
							//document.getElementById('li'+id).style.display = "none";
						}
						isSubmitting = false;
					},
					 error: function(data){
						alert("出现错误");
						isSubmitting = false;
					},
					dataType: 'json'
				});	
			}else{
				isSubmitting = false;
			}
		}
		function delimage(goodsid){
			document.getElementById('pic'+goodsid).src="";//即使这样还是插入图片了，bug
			document.getElementById('delimage'+goodsid).style.display = "none";
			document.getElementById('fileupload'+goodsid).value = "";	
			document.getElementById("delimg_id"+goodsid).value=-1;			
			
		}
		function previewImage(file,id){  
	
			var picsrc = document.getElementById('pic'+id);
			document.getElementById('delimage'+id).style.display = "";			
			document.getElementById("delimg_id"+id).value="";
			if (file.files && file.files[0]) {//chrome   
				var reader = new FileReader();
				reader.readAsDataURL(file.files[0]);  
				reader.onload = function(ev){
					picsrc.src = ev.target.result;
				}   
			
			} else{
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
		function setmanual(id,e,value){
			var obj='market_price'+id;
			var goodstotal='total'+id;
			if(value==0){
				document.getElementById(obj).readOnly=false;
				document.getElementById(goodstotal).readOnly=false;
			}else{
				document.getElementById(obj).readOnly=true;
				document.getElementById(goodstotal).value="-1";
				document.getElementById(goodstotal).readOnly=true;
			}
			
			//document.getElementById('market_price'+id).readOnly=true;
		}		
		
	</script>
</html>