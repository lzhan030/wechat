<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
</style>

<div class="main_auto">
	<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('list',array());?>">微信墙</a> > <font class="fontpurple"><?php echo empty($id)?"创建微信墙":"修改活动";?></font></div>
</div>
<form id="edit" action="" method="POST" enctype="multipart/form-data">
	<div class="alert alert-block alert-new">
		<table>
			<tbody>
				<tr>
					<th style="width: 130px;">微信墙名称（必填）</th>
					<td><input type="text" class="form-control" value="<?php echo $wxwall['name'];?>" name="name" style="margin-bottom: 10px;"></td>
				</tr>
				<tr>
					<th>是否审核</th>
					<td>
						<div class="radio-inline"><input type="radio" name="isshow" value="1" id="isshow_1" <?php if($wxwall['isshow'] == '1') { ?>checked="true"<?php } ?>>是</div>
						<div class="radio-inline"><input type="radio" name="isshow" value="0" id="isshow_0"  <?php if($wxwall['isshow'] == '0') { ?>checked="true"<?php } ?>>否</div>
						<div class="help-block">当用户在话题中发表内容，是否需要审核，为否为则直接显示内容。</div></td>
				</tr>
				<tr>
					<th>活动Logo</th>
					<td>
						<img class="" src="<?php echo empty($wxwall['logo'])?'':($baseurl.$wxwall['logo']); ?>" height='100' width='300' <?php echo empty($wxwall['logo'])?'style="display:none;"':''; ?>/>
						<input type="file" class="form-control pic_upload" name="logo" style="margin-top:5px;"/>
						<div class="help-block">建议尺寸：355像素 * 125像素</div>	
					</td>
				</tr>
				<tr>
					<th>公众号二维码</th>
					<td>
						<img class="" src="<?php echo empty($wxwall['qrcode'])?'':($baseurl.$wxwall['qrcode']);?>" height='120' width='120' <?php echo empty($wxwall['qrcode'])?'style="display:none;"':''; ?>/>
						<input type="file" class="form-control pic_upload" name="qrcode" id="qrcode" style="margin-top:5px;"/>
						<div class="help-block">建议尺寸：430像素 * 430像素</div>	
					</td>
				</tr>
				<tr>
					<th>微信墙背景图片</th>
					<td>
						<img class="" src="<?php echo empty($wxwall['background'])?(home_url().'/wp-content/themes/ReeooV3/wechat/wxwall/template/image/5.jpg'):($baseurl.$wxwall['background']);?>" height='120' width='120'/>
						<input type="file" class="form-control pic_upload" name="background" id="background" style="margin-top:5px;"/>
						<div class="help-block">建议尺寸：1400像素 * 1000像素</div>	
					</td>
				</tr>
				<tr>
					<th>微信墙入口指引</th>
					<td><input type="text" class="form-control" value="<?php echo $wxwall['entry_tips'];?>" name="entry-tips" style="margin-bottom: 10px;">
					<div class="help-block">引导用户进入微信墙。如：发送微信墙（点击菜单）后发送内容，自动上墙。</div>
					</td>
				</tr>
				<tr>
					<th>发表提示（必填）</th>
					<td><textarea style="height:100px;" class="form-control" cols="70"  name="send-tips" id="send-tips"><?php echo $wxwall['send_tips'];?></textarea>
						<div class="help-block">当用户在话题发表内容成功时，返回用户的提示信息。</div></td>
				</tr>
				<tr>
					<th>每页显示条数</th>
					<td><input type="text" class="form-control" name="pagesize" id="pagesize" value="<?php echo empty($wxwall['pagesize'])?'3':$wxwall['pagesize'];?>" style="margin-bottom: 10px;">
						<div class="help-block">建议设置为3至7条。</div></td>
				</tr>
				<tr>
					<th>开启腾讯墙</th>
					<td>
						<div class="radio-inline"><input type="radio" name="walls[tx][status]" value="1" id="syncwall_tx_1" <?php if(!empty($wxwall['syncwall']['tx']['status'])) { ?>checked="true"<?php } ?> onclick="$('#syncwall_tx').show();">是</div>
						<div class="radio-inline"><input type="radio" name="walls[tx][status]" value="0" id="syncwall_tx_0"  <?php if(empty($wxwall['syncwall']['tx']['status'])) { ?>checked="true"<?php } ?> onclick="$('#syncwall_tx').hide();">否</div>
						<div class="help-block">开启此选项后，系统自动获取指定的腾讯墙内的数据显示到微擎墙内。</div>
					</td>
				</tr>
				<tbody id="syncwall_tx" <?php if(empty($wxwall['syncwall']['tx']['status'])) { ?>style="display:none;"<?php } ?>>
				<tr>
					<th>腾讯墙话题名</th>
					<td>
						<div class="input-append">
							<input type="text" value="<?php echo $wxwall['syncwall']['tx']['subject'];?>" class="form-control" name="walls[tx][subject]">
						</div>
						<div class="help-block"></div>
					</td>
				</tr>
				</tbody>
			</tbody>
		</table>
	</div>
	<button class="btn btn-primary submit" style="margin-left: 300px;width: 100px;">提交</button>
</form>
<script type="text/javascript">

function getFullPath(file) {    //得到图片的完整路径  
	var url = null ; 
	if (window.createObjectURL!=undefined) { // basic
		url = window.createObjectURL(file) ;
	} else if (window.URL!=undefined) { // mozilla(firefox)
		url = window.URL.createObjectURL(file) ;
	} else if (window.webkitURL!=undefined) { // webkit or chrome
		url = window.webkitURL.createObjectURL(file) ;
	}
	return url ;
} 

$(".pic_upload").change(function(){  
	objUrl = getFullPath(this.files[0]);
    if (objUrl) {
		$(this).prev().show();
		$(this).prev().attr("src", objUrl);
	}
});
$(function(){
	$('#edit').submit(function(){
		if($.trim($('input[name="name"]').val())==""){
			alert("请填写微信墙名称.");
			return false;
		}
		if($.trim($('#send-tips').val())==""){
			alert("请填写发表提示.");
			return false;
		}
		if(!(/^\+?([1-9][0-9]?)$/.test($("#pagesize").val()))){
			alert("请输入正确的每页显示条数,建议使用3至7间的整数");
			return false;
		}
		return true;
	});
});
</script>
