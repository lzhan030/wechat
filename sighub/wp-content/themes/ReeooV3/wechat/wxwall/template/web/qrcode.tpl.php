<?php defined('IN_IA') or exit('Access Denied');?><?php include template('common/header', TEMPLATE_INCLUDEPATH);?>
<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/common.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/common.css?t=<?php echo TIMESTAMP;?>" />
<style>
body{background-image:url('<?php if(!empty($wall['background'])){ ?><?php echo $baseurl.$wall['background']?><?php } else { ?><?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/image/5.jpg<?php }?>'); overflow:hidden;  background-size: cover;}
.wxwall-logo {background: url('<?php echo $baseurl.$wall['logo']?>') no-repeat 0 15px;}
.topbox_l {background: url('<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/image/message_box.png') no-repeat 0 15px;}
</style>
<div id="wallMain">
	<div id="topbox" class="topbox">
		<div class="wxwall-logo"></div>
		<div class="topbox_l">			
			<div class="topic">
				<h1 class="msg_tit">搜索公众号 <strong class="red"><?php echo $wechat_name;?></strong></h1>
				<span class="addCnt"><?php echo $wall['entry_tips'];?></span>
			</div>
		</div>
	</div>
	<div id="msg_list">
		<div class="talkList" id="msg_<?php echo $row['id'];?>" style="text-align:center;">
			<img src="<?php echo $baseurl.$wall['qrcode'];?>" style="width:430px;" />
		</div>
	</div>
</div>

<div class="side_div">
	<div class="side_item"><a href="<?php echo $this->createWebUrl('detail', array('id' => $wall['id']))?>">微信墙</a></div>
	<!--<div class="side_item"><a href="<?php echo $this->createWebUrl('lottery', array('id' => $wall['rid']))?>">抽奖</a></div>-->
</div>
</body>
</html>
