<?php defined('IN_IA') or exit('Access Denied');?>
<?php $upload =wp_upload_dir();?>
<div class="list_aera">
	<div class="box">
		<a href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id'], 'gweid' => $gweid))?>">
			<img src="<?php if(empty($item['thumb'])|| (stristr($item['thumb'],"http")!==false)){ echo $item['thumb'];}else{echo $upload['baseurl'].$item['thumb'];} ?>">
		</a>
	</div>
	<h1><a href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id'], 'gweid' => $gweid))?>"><?php  echo $item['title'];?></a><?php  if($item['type'] == '2') { ?>(虚拟)<?php  } ?></h1>
	<h2><?php  echo $item['content'];?></h2>
	<p>
		<?php if($item['ismanual']=='1'){ ?>
		<span class="big_b">自定义金额</span>
		<?php }else{ ?>
		<span class="big_b" >价格:<span style="color: #C00;">￥<?php  echo $item['market_price'];?></span></span>
		<?php } ?>
	</p>
</div>