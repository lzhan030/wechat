<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<script type="text/javascript">

	var idat="<?php echo $indata ?>";
	var rang="<?php echo $range ?>";
	var notice_id="<?php echo $notice_id ?>";
	var flag=1;
	
<?php switch($range){
case 'all':
?>	
	
	location.href='<?php echo $this->createWebUrl('replymanage',array('Ipad' => $indata , 'range'=> $range,'notice_id'=>$notice_id));?>';
<?php
break;
case 'reply_content':
?>	
	
	location.href='<?php echo $this->createWebUrl('replymanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range,'notice_id'=>$notice_id));?>';
<?php
break;
case 'reply_time':
?>	
	location.href='<?php echo $this->createWebUrl('replymanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range,'notice_id'=>$notice_id));?>';
<?php
break;
case 'reply_author':
?>	
	location.href='<?php echo $this->createWebUrl('replymanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range,'notice_id'=>$notice_id));?>';
<?php
break;
default:
?>
location.href='<?php echo $this->createWebUrl('replymanage',array('Ipad' => $indata,'notice_id'=>$notice_id));?>';
<?php
} 
?>
</script>