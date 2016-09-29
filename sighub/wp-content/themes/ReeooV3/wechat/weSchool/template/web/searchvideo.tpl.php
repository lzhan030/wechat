<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<script type="text/javascript">

	var idat="<?php echo $indata ?>";
	var rang="<?php echo $range ?>";
	var flag=1;
<?php switch($range){
case 'all':
?>	
	location.href='<?php echo $this->createWebUrl('videomanage',array('Ipad' => $indata , 'range'=> $range));?>';
<?php
break;
case 'video_title':
?>	
	location.href='<?php echo $this->createWebUrl('videomanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range));?>';
<?php
break;
case 'video_time':
?>	
	location.href='<?php echo $this->createWebUrl('videomanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range));?>';
<?php
break;
case 'video_gradeclass':
?>	
	location.href='<?php echo $this->createWebUrl('videomanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range));?>';
<?php
break;
case 'video_publisher':		
?>
	location.href='<?php echo $this->createWebUrl('videomanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range));?>';
<?php
break;
default:
?>
	location.href='<?php echo $this->createWebUrl('videomanage',array('Ipad' => $indata));?>';
<?php
} 
?>
</script>