<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<script type="text/javascript">

	var idat="<?php echo $indata ?>";
	var rang="<?php echo $range ?>";
	var flag=1;
<?php switch($range){

case 'all':
?>	
	location.href='<?php echo $this->createWebUrl('homeworkmanage',array('Ipad' => $indata , 'range'=> $range));?>';
<?php
break;

case 'homework_gradeclass':
?>	
	location.href='<?php echo $this->createWebUrl('homeworkmanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range));?>';
<?php
break;
case 'homework_starttime':
?>	
	location.href='<?php echo $this->createWebUrl('homeworkmanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range));?>';
<?php
break;
case 'homework_endtime':
?>
	location.href='<?php echo $this->createWebUrl('homeworkmanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range));?>';
<?php
break;
default:
?>
location.href='<?php echo $this->createWebUrl('homeworkmanage',array('Ipad' => $indata));?>';
<?php
} 
?>
</script>