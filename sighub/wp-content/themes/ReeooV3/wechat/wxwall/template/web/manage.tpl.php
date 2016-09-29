<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
	tr{text-align:center;}
	.reply_content{text-align:left;}
</style>

<div class="main_auto">
	<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('list',array());?>">微信墙</a> > <font class="fontpurple"><?php echo $isshow?'内容管理':'内容审核';?></font></div>
</div>
<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
	<div class="panel-heading">
		<h4 class="sub-title">消息列表</h4>
	</div>
	<form id="message_list" action="" method="post">
		<table class="table table-striped" width="800" border="0" align="center" style="margin-bottom:0px;">
			<tbody>
				<tr>
					<td width="60px" class="row-first">选择</td>
					<td width="90px">用户</td>
					<td class="row-hover">消息</td>
					<td width="90px">时间</td>
					<td width="<?php echo $isshow?'90px':'150px';?>">操作</td>
				</tr>
				<?php if(is_array($list)) { foreach($list as $row) { ?>
				<tr>
					<td class="row-first"><input type="checkbox" name="select[]" value="<?php echo $row['id'];?>" /></td>
					<td class="row-hover">
						<img width="50" src="<?php echo $row['avatar'];?>" class="avatar" />
						<p><?php echo $row['nickname'];?></p>
					</td>
					<td class="reply_content"><?php echo $row['content'];?></td>
					<td style="font-size:12px; color:#666;">
						<div style="margin-bottom:10px;"><?php echo date('Y-m-d', $row['createtime']);?></div>
						<div><?php echo date('H:i:s', $row['createtime']);?></div>
					</td>
					<td>
						<?php if(!$isshow) { ?>
						<a type="button" name="pass" data-id="<?php echo $row['id'];?>" class="btn btn-sm btn-info" href="#">审核通过</a>
						<?php } ?>
						<a type="button" name="delete" data-id="<?php echo $row['id'];?>" class="btn btn-sm btn-warning" href="#">删除</a>
					</td>
				</tr>
				<?php } } ?>
			</tbody>
		</table>
	</div>
	<table>
		<tr>
			<td width="60px" class="row-first"><input type="checkbox" id="checkall"/></td>
			<td colspan="4" class="reply_content">
				<?php if(!$isshow) { ?>
				<input type="submit" name="pass" data-id="0" value="批量审核通过" class="btn btn-info" />
				<?php } ?>
				<input type="submit" name="delete" data-id="0" value="批量删除" class="btn btn-warning" />
			</td>
		</tr>
	</table>
</form>
	<?php echo $pager;?>



<script>
$(function(){
	$('#checkall').click(function(){
		$('table:first').find('input[type="checkbox"]').prop('checked',$('#checkall').is(':checked'));
	})
	$('[name="pass"]').click(function(){
		if($(this).data('id')>=1){
			passlist={'select[]':$(this).data('id')};
			passobjs = $(this);
		}else{
			if($('#message_list').find(':checked').length==0){
				alert("请选择要批量审核通过的微信墙消息。")
				return false;
			}
			passlist=$('#message_list').serialize();
			passobjs = $('#message_list').find(':checked').parent().parent().find('a[name="pass"]');
		}

		jQuery.post(
			'<?php echo $this->createWebUrl('manage',array('id'=>$_GET['id'],'isshow'=>$_GET['isshow'],'action'=>'pass'))?>',
			passlist,
			function(data){
				if(data.status == 'success'){
					alert("操作成功");
					passobjs.text("已通过").addClass('disabled');
					console.info(passobjs);
				}else{
					alert("网络异常，请重试");
					window.location.reload();
				}
			},
			'json'
		).fail(function(){
			alert("网络异常，请重试");
			window.location.reload();
		});
	});
	$('[name="delete"]').click(function(){
		if($(this).data('id')>=1){
			if(!confirm("确定要删除这条微信墙消息吗？"))
				return false;
			deletelist={'select[]':$(this).data('id')};
		}else{
			if($('#message_list').find(':checked').length==0){
				alert("请选择要批量删除的微信墙消息。")
				return false;
			}
			if(!confirm("确定要删除这些微信墙消息吗？"))
				return false;
			deletelist=$('#message_list').serialize();
		}
		deletelist.action="delete";
		console.info(deletelist);
		jQuery.post(
			'<?php echo $this->createWebUrl('manage',array('id'=>$_GET['id'],'isshow'=>$_GET['isshow'],'action'=>'delete'))?>',
			deletelist,
			function(data){
				if(data.status == 'success'){
					alert("操作成功");
				}else{
					alert("网络异常，请重试");
				}
				window.location.reload();
			},
			'json'
		).fail(function(){
			alert("网络异常，请重试");
			window.location.reload();
		});
	});
	$('form').submit(function(){
		return false;
	});
});
</script>
