<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
</style>

<div class="main_auto">
	<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('list',array());?>">微投票</a> > <a href="<?php echo $this->createWebUrl('display',array('id' => $_GET['id']));?>">票数统计</a> > <font class="fontpurple">投票记录</font></div>
</div>
<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
	<div class="panel-heading">
		 活动名称： <?php echo $vote_name; ?> （选项：  <?php echo $option_array[$vote_option_id]; ?>）
	</div>
	<table class="table table-striped" width="800" border="0" align="center">
		<tbody>
			<tr>
				<td scope="col" align="center" style="font-weight:bold">用户OpenID</td>
				<td scope="col" align="center" style="font-weight:bold">投票时间</td>
				<td scope="col" align="center" style="font-weight:bold">本次投票的所有选项</td>
			</tr>
			<?php
				if(is_array($list) && !empty($list)){
					foreach($list as $element){
				 ?>
			<tr data-vote-id="<?php echo $element['id']; ?>">
				<td align="center"><?php if(!empty($element['mid'])) {?><a href="<?php bloginfo('template_directory'); ?>/wechat/vipmembermanage/vipmember_update_dialog.php?beIframe&vipmemberId=<?php echo $element['mid']; ?>"><?php echo $element['from_user']; ?></a><?php } else { echo $element['from_user'];}?>  </td>
				<td align="center"><?php echo date('Y-m-d H:i:s',$element['votetime']); ?></td>
				<td align="center"><?php echo $element['votes']; ?></td>
			</tr>
			<?php }}?>
		</tbody>
	</table>
	
</div>
<?php echo $pager;?>