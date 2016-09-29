<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<title>投票结束</title>
<link type="text/css" rel="stylesheet" href="<?php echo home_url() ?>/wp-content/themes/ReeooV3/wechat/vote/style/vote.css?id=<?php echo TIMESTAMP;?>" />
</head>
<body>
<div class="wrapper" style="margin-top:-8px;">
	<img class="bg" src="<?php echo home_url() ?>/wp-content/themes/ReeooV3/wechat/vote/style/images/bg.jpg">
	<div class="top fn-clear">
		 
	<div class="title-cont">
                    <p class="title"><?php  echo $reply['title'];?></p>
                    <p class="timeout" style='padding-left:15px;'><img class="clock" src="<?php echo home_url() ?>/wp-content/themes/ReeooV3/wechat/vote/style/images/clock.png"><span class="text"><?php  echo $limits;?></span></p>
                    <p>&nbsp;</p>
                </div>
	</div>
	   <?php  if(!empty($reply['thumb'])) { ?>
            <div class="cover">
                <img class="line" src="<?php echo home_url() ?>/wp-content/themes/ReeooV3/wechat/vote/style/images/ctline.jpg">
                <img class="cimg" src="<?php  echo $baseurl.$reply['thumb'] ?>">
                <img class="line" src="<?php echo home_url() ?>/wp-content/themes/ReeooV3/wechat/vote/style/images/cbline.jpg">
            </div>
            <?php  } ?>
            <?php if ($reply['endtime'] < TIMESTAMP) {?>
                <div class="tip-cont">
                    <img class="icon" src="<?php echo home_url() ?>/wp-content/themes/ReeooV3/wechat/vote/style/images/tip_icon.png">该投票已过期！
               </div>            
            <?php } else if(!empty($reply['votetimes'])) {?>
                <div class="tip-cont">
                    <img class="icon" src="<?php echo home_url() ?>/wp-content/themes/ReeooV3/wechat/vote/style/images/tip_icon.png">您还可以投票 <?php  echo $canvotetimes;?> 次！
               </div>            
            <?php } else { ?>
                <div class="tip-cont">
                    <img class="icon" src="<?php echo home_url() ?>/wp-content/themes/ReeooV3/wechat/vote/style/images/tip_icon.png">您还可以继续投票！
               </div>
            <?php  } ?>            
			<div class="summary"><?php  echo $reply['description'];?></div>
			<div class="option-cont">
             <?php  if(is_array($list)) { foreach($list as $row) { ?>
			<div class="option fn-clear option-statis" data-value="0">
                     <?php  if(!empty($row['thumb']) && $reply['isimg']==1) { ?>
                    <div><image src="<?php  echo $baseurl.$row['thumb']?>" style="width:95%;margin:10px;" /></div>
                    <?php  } ?>
			<div><?php  echo $row['title'];?></div>
			<div class="progress"><div data-per="<?php  echo $row['percent'];?>" class="bar bar0" style="width: <?php  echo $row['percent'];?>%;"></div></div><span class="per" style="margin-left:15px;"><?php  echo $row['num'];?>(<?php  echo $row['percent'];?>%)</span>
		</div>
		<img class="sep" src="<?php echo home_url() ?>/wp-content/themes/ReeooV3/wechat/vote/style/images/option_sep.jpg">
                <?php  } } ?>
		 
	</div>
<!--	<div class="vote-cont">
		<div style="height: 10px;"></div>
		<img class="vote-btn" id="submit" src="./source/modules/vote/style/images/vote.png">
		<div style="height: 10px;"></div>
	</div>-->
 	<p class="page-url">
	</p>
</div>
<?php 
	$gweidname = get_bloginfo('name','display');
	share_page_in_wechat($gweid, array(
	'title' => $gweidname.'- 微投票 - '.$reply['title'],
	'desc' => "点击参与 {$gweidname} 的{$reply['title']}",
	'link' => 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&wxref=mp.weixin.qq.com' ));
?>
</body>
</html>
