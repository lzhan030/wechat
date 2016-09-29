<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<style>
	.panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	.sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
	th{text-align:center;}
</style>
<div class="main_auto">
	<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('list',array());?>">微投票</a> > <font class="fontpurple">票数统计</font></div>
</div>
<div class="panel panel-default" style="margin-right:50px; margin-top:20px">
	<table class="table table-striped" width="800" border="0" align="center">
		<tbody>
			<tr>
				<th class="coltitle">编号</tdh>
				<th>选项名称</th>
				<th>得票数</th>
				<th style="width: 50%;">得票比例</th>
				<th>查看详情</th> 
			</tr>
			<?php
				if(is_array($list) && !empty($list)){
					$i=0;
					$states = array('success','info','warning','danger');
					foreach($list as $element){
						$i++;
				 ?>
			<tr data-wxwall-id="<?php echo $element['id']; ?>">
				<td align="center"><?php echo $element['id']; ?> </td>
				<td align="center"><?php echo $element['title']; ?></td>
				<td align="center"><?php echo $element['vote_num']; ?></td>
				<td align="center">
					<div class="progress">
					  <div class="progress-bar progress-bar-<?php echo $states[$i%4]; ?> " role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $element['percent'] ?>%">
					  </div>
					</div>
				</td>
				<td align="center"><a class="btn btn-sm btn-info" href="<?php echo $this -> createWebUrl('history',array('id' => $_GET['id'],'option' => $element['id'])) ?>">详情</a></td>
			</tr>
			<?php }}?>
		</tbody>
	</table>
</div>
<a class="btn btn-primary" style="margin-left: 300px;width: 120px;" href="<?php echo $this-> createWebUrl('List',array()); ?>">返回</a>

